<?php

function http_digest_parse($txt) {
    $needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
    $data = array();
    $keys = implode('|', array_keys($needed_parts));
    preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);
    foreach ($matches as $m) {
        $data[$m[1]] = $m[3] ? $m[3] : $m[4];
        unset($needed_parts[$m[1]]);
    }
    return $needed_parts ? false : $data;
}

function userInTable($username,$table) {
	$r = false;
	foreach ($table as $row) {
		if (in_array($username,$row)) $r = true;
	}
	return $r;
}

function getSecret($username,$table) {
	$r = false;
	foreach ($table as $row) {
		if (in_array($username,$row)) $r = $row['Secretkey'];
	}
	return $r;
}

function lowercaseKeys($i) {
	$keys	= array_keys($i);
	$vals	= array_values($i);
	$lkeys	= array_map('strtolower',$keys);
	$r		= array_combine($lkeys,$vals);
	return $r;
}

function convertToMongo($criteria) {
	// takes an array and creates any necessary Mongo objects
	$r = array();
	foreach ($criteria as $k => $v) {
		if ( is_object($v) ) {
			if (isset($v->regex)) {
				$mr = new MongoRegex($v->regex);
				$r[$k] = $mr;
			} else {
				$r[$k] = convertToMongo($v);
			}
		} elseif (is_array($v)) {
			$r[$k] = convertToMongo($v);
		} else {
			$r[$k] = $v;
		}
	}
	return $r;
}

class RestServer extends Core {

	protected $errors 	 = NULL;	
	protected $client	 = NULL;
	protected $host		 = NULL;
	protected $request	 = NULL;
	protected $response	 = NULL;
	protected $hdrs		 = array();
	protected $module	 = array();
	protected $authLevel = 0;
	
	function __construct($GET,$POST) {
		$hdrs	= apache_request_headers();
		$client = array();
		
		foreach ($hdrs as $k => $v) {
			if ($k == 'X-Gourd-Debug') {
				$this->request['debug'] = $v;
			} elseif (strpos($k,'X-Gourd-') !== false) {
				$k = str_ireplace('X-Gourd-','',$k);
				$client[$k] = $v;
			}
		}
		
		//	GET can be json encoded, or no
		$this->request['GET'] = array();
		if (!empty($GET)) {
			if (isset($GET['jsonq'])) {
				$this->request['GET'] = (array) json_decode($GET['jsonq']);
			} else {
				$this->request['GET'] = $GET;
			}		
		}
		
		//	POST... same
		if (!empty($POST)) {
			if (sizeof($POST) == 1 && isset($POST['jsonpost'])) {
				$this->request['POST'] = json_decode($POST['jsonpost'],true);
			} else {
				$this->request['POST'] = $POST;
			}	
		}		
		
		$this->client = $client;
		if (isset($this->request['GET']['Apikey'])) {
			$this->client['Apikey'] = $this->request['GET']['Apikey'];
			unset($this->request['GET']['Apikey']);
		}
		if (isset($this->request['GET']['apikey'])) {
			$this->client['Apikey'] = $this->request['GET']['apikey'];
			unset($this->request['GET']['apikey']);
		}		
		if (isset($this->request['GET']['Debug'])) {
			$this->request['debug'] = $this->request['GET']['Debug'];
			unset($this->request['GET']['Debug']);
		}		
		if (isset($this->request['GET']['debug'])) {
			$this->request['debug'] = $this->request['GET']['debug'];
			unset($this->request['GET']['debug']);
		}
		if (isset($this->client['Userid'])) {
			$this->request['User']['UserID'] = $this->client['Userid'];
			unset($this->client['Userid']);
		}
		if (isset($this->request['GET']['UserID'])) {
			$this->request['User']['UserID'] = $this->request['GET']['UserID'];
			//unset($this->request['UserID']);
		}
		$this->client['addr']	= $_SERVER['REMOTE_ADDR'];
		$this->host['addr']		= $_SERVER['HTTP_HOST'];
		
		$jjjjj = explode('?',$_SERVER['REQUEST_URI']);
		
		$this->request['uri']	= $jjjjj[0];
		
		//	return type
		$this->request['returntype'] = 'json';
		if (strpos($this->request['uri'],'.')) {
			$uribits = explode('.',$this->request['uri']);
			$this->request['returntype'] = $uribits[1];
			$this->request['uri'] = $uribits[0];
		}
		
		//	to allow JSONP calls to have a greater vocabulary
		if ($this->request['returntype'] == 'jsonp' && isset($this->request['GET']['verb'])) {
			$this->request['verb']	= $this->request['GET']['verb'];
		} else {
			$this->request['verb']	= $_SERVER['REQUEST_METHOD'];
		}
		$verb = $this->request['verb'];
		
		//	dirty auth
		if ( !$this->auth(1) ) {
			$this->response = array('msg' => 'invalid API Key');
			$r = $this->setStatus(401);
			
		} else {

			if ( $this->auth(3) ) {
			
				$pathbits = explode('/',$this->request['uri']);
				array_shift($pathbits);
				$resource = $pathbits[0];
				if (isset($pathbits[1])) $representation = $pathbits[1];
			
				switch ($resource) {
					
					case 'ping':
					$this->ping();
					break;
					
					case 'modules':
					case 'permissions':
					case 'users':
					case 'validuser':
					case 'clients':
					case 'superchunkz':
					case 'radstackz':
					case 'textassets':
					case 'sql':
					case 'fulltextsearch':
					include 'method.'.$resource.'.php';
					break;
					
					case 'auth':
					$this->checkAuth();
					break;
					
					case 'test':
					$this->addToResponse(array('msg' => 'hello'));
					$this->addDebugInfo();
					$this->setStatus(200);
					break;
					
					default:
					//	this will usually be implemented in the child class
					$this->doit();	
				}
			} else {
				$this->addToResponse(array('msg' => 'Your request cannot be issued via ' . $this->request['verb']));
				$r = $this->setStatus(401);
			}
		}
	}
	
	public function asUser($UserID) {
		$UserID = (int) $UserID;
		if ($UserID) {
			$this->request['User']['UserID'] = $UserID;
			return true;
		} else {
			return false;
		}
	}
	
	protected function getUser() {
		$r = $this->sql()->good_query_assoc("SELECT UserID,Email,FullName FROM Users WHERE UserID = " . $this->request['User']['UserID']);
		settype($r['UserID'],'int');
		return $r;
	}	
	
	protected function executeMongoQuery($collection,$c,$f,$g=array()) {
		if (isset($g['criteria'])) 	$c = array_merge(convertToMongo((array)$g['criteria']),convertToMongo($c));
		if (isset($g['fields']))	$f = array_values(array_merge((array) $g['fields'],$f));
		$cursor = $this->mong()->selectCollection($collection)->find($c,$f);
		if (isset($g['sort'])) 		$cursor->sort( array_map('intval',(array) $g['sort']) );
		//if (isset($g['sort'])) 		$cursor->sort( $g['sort']) );
		if (isset($g['limit']))		$cursor->limit( (int) $g['limit']);
		if (isset($g['skip']))		$cursor->skip(  (int) $g['skip'] );
		return $cursor;
	}	
	
	public function setModule($Handle) {
		$m = $this->sql()->good_query_assoc("SELECT * FROM Modules WHERE Handle = '$Handle' AND ClientID = ".$this->getClientID());
		$this->request['Module'] = $m;
	}
	
	public function userCan($verb,$ModuleHandle) {
		if (!isset($this->request['User']['UserID'])) {
			$this->setStatus(401);
			$this->addToResponse(array('msg' => 'missing user credentials'));
			return false;
		}
		if (!isset($this->request['User']['perms'])) {
			$perms = $this->sql()->good_query_table("SELECT * FROM ModuleXUser WHERE UserID = ".$this->request['User']['UserID']);
			$this->request['User']['perms'] = $perms;
		}
		if (!isset($this->request['Module'])) {
			$this->setModule($ModuleHandle);
		}
		$permission_granted = false;
		$verb = strtolower($verb);
		foreach ($this->request['User']['perms'] as $perm) {
			if ($perm['UserID'] == $this->request['User']['UserID'] && $perm['ModuleID'] == $this->request['Module']['ModuleID']) {
				switch ($verb) {
					case 'read':
					if ($perm['CanRead'])	$permission_granted = true;
					case 'write':
					if ($perm['CanWrite'])	$permission_granted = true;
					case 'alter':
					if ($perm['CanAlter'])	$permission_granted = true;
				}
			}
		}
		if (!$permission_granted) {
			$this->setStatus(401);
			$this->addToResponse(array('msg' => 'User ' . $this->request['User']['UserID'] . ' does not have ' . $verb . ' access to ' . $this->request['Module']['Title']));
			$this->addToResponse(array('usrmsg' => 'Sorry, you do not have permission to perform that action'));
		}
		return $permission_granted;
	}
	
	private function isValidLookingApikey($h) {
		$n1			= 709;
		$n2			= 5;
		$isValid	= false;
		$y			= str_split($h,4);
		$checkdigit = array_pop($y);
		$z 			= array_map('hexdec',$y);
		$x 			= array_map('sqrt',$z);
		$i 			= abs( $x[0] * $x[1] - ( $x[2] + $x[3] + $x[4] + $x[5] + $x[6] ) ) + hexdec($checkdigit);
		$z 			= $i % $n1;
		if ($z == $n2) $isValid = true;
		return $isValid;
	}
	
	private function createApiKey() {
		$n1			= 709;
		$n2			= 5;
		$a			= md5(str_rot13(uniqid()));
		$y 			= str_split($a,4);
		array_pop($y);
		$z 			= array_map('hexdec',$y);
		$x 			= array_map('sqrt',$z);
		$i 			= abs( $x[0] * $x[1] - ( $x[2] + $x[3] + $x[4] + $x[5] + $x[6] ) );
		$z 			= $i % $n1;
		$correction = $n2 - $z;
		$min 		= ceil ( (4096 - $correction) / $n1);
		$max 		= floor ( (65535 - $correction) / $n1);
		$pad 		= ( rand($min,$max) * $n1 ) + $correction;
		$y[] 		= dechex($pad);
		$r 			= '';
		foreach ($y as $b) $r .= strtoupper($b);
		return $r;
	}
	
	private function createSecretKey() {
		$i			= rand(5,9);
		$chomp		= rand(1,31);
		$string		= '';
		for ($j = 0;$j < $i;$j++) {
			$string .= md5(str_rot13(uniqid()));
		}
		$string		= substr($string,0,$chomp*-1);
		$r			= $string;
		return $r;
	}
	
	public function getClientID() {
		if (isset($this->client['ClientID'])) {
			$r = $this->client['ClientID'];
		} else {
			$r = $this->sql()->good_query_value("SELECT ClientID FROM Clients WHERE Apikey = '".$this->client['Apikey']."'");
			$this->client['ClientID'] = $r;
		}
		return $r;
	}
	
	protected function apikeyExists($key) {
		$r = $this->sql()->good_query_value("SELECT ClientID FROM Clients WHERE Apikey = '" . $key ."' AND IsActive = 1");
		return $r;
	}
	
	private function getClientPermissions($refresh=false) {
		if ($refresh) unset($this->client['perms']);
		if (!isset($this->client['perms'])) {
			$perms = $this->sql()->good_query_table("SELECT * FROM ClientPermissions WHERE '".$this->host['addr']."' REGEXP DomainMatch AND ClientID = (SELECT ClientID FROM Clients WHERE Apikey = '".$this->client['Apikey']."')");
			$p = array();
			foreach ($perms as $perm) {
				$k = $perm['Verb'];
				$v = $perm['Authenticated'];
				$p[$k] = $v;
			}
			$this->client['perms'] = $p;
		} else {
			$p = $this->client['perms'];
		}
		return $p;
	}
 	
	protected function setStatus($code) {
		$sobj = new StatusCodes();
		$s = array(
			'code'	=> (int) $code,
			'msg' 	=>  $sobj->getMessageForCode($code),
			'iserr'	=>	$sobj->isError($code)
		);
		$this->addHeader($sobj->httpHeaderFor($code));
		return $this;
	}
	
	protected function addToResponse($stuff) {
		$r = array_merge((array)$this->response,(array)$stuff);
		$this->response = $r;
		return $this;
	}

	protected function addDebugInfo() {
		$dbg = array(
			'request_headers'	=> apache_request_headers(),
			'response_headers'	=> apache_response_headers(),
			'client'			=> $this->client,
			'host'				=> $this->host,
			'request'			=> $this->request
		);
		$this->addToResponse(array('dbg' => $dbg));
		return $this;
	}

	protected function addHeader($hdr) {
		array_push($this->hdrs,$hdr);
		return $this;
	}

	protected function noauth() {
		$this->addToResponse(array('msg' => 'authentication failed or was not attempted'));
		$r = $this->setStatus(401);
		return $this;
	}

	function __destruct() {
		//$this->spit();
	}

	public function spit($format='json') {
		
		$format = $this->request['returntype'];
		
		//	debug
		if (!empty($this->request['debug'])) {
			$this->addDebugInfo();
		}
		
		//	errors
		if (sizeof($this->errors)) {
			$this->addToResponse(array('errors' => $this->errors));
		}
		
		//	response
		switch ($format) {
			
			case 'jsonp':
			if (isset($this->request['GET']['jsonpcallback'])) {
				$r = $this->request['GET']['jsonpcallback'] . '(' . json_encode((array)$this->response) . ');';
			} else {
				$r = 'var r = ' . json_encode((array)$this->response) . ';';
				if (isset($this->request['GET']['inject'])) {
				$r .= "\n";
				$injectjs = json_encode($this->request['GET']['inject']);
				$r .= <<<JS
function listen(evnt, elem, func) {
    if (elem.addEventListener)		// W3C DOM
        elem.addEventListener(evnt,func,false);
    else if (elem.attachEvent) {	// IE DOM
         var r = elem.attachEvent("on"+evnt, func);
    return r;
    }
    else window.alert("This browser doesn`t support addEventListener() or attachEvent()");
}	
function gourdinject() {
	var inject = $injectjs,
		elem,
		rkey,
		strparts,
		c;
		
	for (var k in inject) {
		elem = k;
		rkey = inject[k];
		c	 = eval("r." + rkey);
		document.getElementById(elem).innerHTML = c;
	}			
}
listen("load", window, gourdinject);
JS;
				}			
			}
			$this->addHeader('Content-type: text/javascript');			
			break;
			
			default:
			$r = json_encode((array)$this->response);
			$this->addHeader('Content-type: application/json');
			break;
			
		}
		
		//$this->addHeader('Cache-Control: no-cache, must-revalidate');
		$hdrs = array_filter(array_unique($this->hdrs));
		foreach ($hdrs as $hdr) {
			header($hdr);
		}
		echo $r;
		exit;
	}

	public function doit() {
		switch ($this->request['uri']) {
			case '/ping':
			$this->ping();
			break;
			case '/checkauth':
			$this->checkAuth();
			break;
			default:
			$this->methodNotFound($this->request['uri']);
			break;
		}
	}

	public function ping() {
		$r = array(
			'client'	=> $this->client,
			'host'		=> $this->host,
			'request'	=> $this->request
		);
		$this->addToResponse($r);
		$this->setStatus(200);
	}
	
	public function checkauth() {
		$auth = $this->auth();
		$this->addToResponse(array('client' => $this->client))->addToResponse(array('auth' => $auth));
	}
	
	public function methodNotFound($path) {
		$r = array('status' => 'error', 'msg' => 'method not found', 'method' => $path);
		$this->addToResponse($r);
		$this->setStatus(404);		
	}
	
	public function pusherror($error,$type) {
		if (!isset($this->errors[$type])) $this->errors[$type] = array();
		$this->errors[$type][] = $error;
	}

	public function auth($level=5,$verb='GET') {
	
		if (!isset($this->client['Apikey'])) {
			$this->authLevel = 0;
			$this->pusherror('No API Key provided.','usr');
			return false;
		}
	
		if ($level <= $this->authLevel) return true;
	
		switch ($level) {
		
			case 0:
			return true;
			break;
			
			case 1:
			// check public key for checksum
			if ($this->isValidLookingApikey($this->client['Apikey'])) {
				$this->authLevel = 1;
				return true;
			} else {
				$this->authLevel = 0;
				return false;
			}
			break;
		
			case 2:
			//	check that api key exists
			if ($this->apikeyExists($this->client['Apikey'])) {
				$this->authLevel = 2;
				return true;
			} else {
				return false;
			}
			break;
		
			case 3:
			//	check ClientPermissions for unauthenticated permissions to $verb
			$perms = $this->getClientPermissions();
			$valid_methods = array_keys($perms);
			if (in_array($verb,$valid_methods)) {
				$this->authLevel = 3;
				return true;
			} else {
				return false;
			}
			break;
			
			case 4:
			//	check ClientPermissions for authenticated permissions to $verb
			$perms = $this->getClientPermissions();
			$valid_methods = array_keys(array_filter($perms));
			if (in_array($verb,$valid_methods)) {
				$this->authLevel = 4;
				return true;
			} else {
				return false;
			}			
			break;
		
			case 5:
			//	digest
			$realm = 'API Credentials Required';
			if (empty($_SERVER['PHP_AUTH_DIGEST'])) {
				header('HTTP/1.1 401 Unauthorized');
				header('WWW-Authenticate: Digest realm="'.$realm.'",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');
				$auth = false;
				$this->noauth();
			} else {			
				$userstable = $this->sql()->good_query_table("SELECT Apikey,Secretkey,Username,FullName FROM Clients WHERE IsActive = 1");
				if (!($data = http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])) || !userInTable($data['username'],$userstable)) {
					$r = array('msg' => 'bad api key');
					$this->addToResponse($r);
					$this->setStatus(401);
					$auth = false;
				} else {
					$A1 = md5($data['username'] . ':' . $realm . ':' . getSecret($data['username'],$userstable));
					$A2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
					$valid_response = md5($A1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$A2);
					if ($data['response'] != $valid_response) {
						$r = array('msg' => 'bad secret');
						$this->addToResponse($r);
						$this->setStatus(401);
						$auth = false;
					} else {
						$this->setStatus(202);
						$auth = true;
					}
				}
			}
			if ($auth) {
				$this->authLevel = 5;
			}
			return $auth;
			break;
		
		}
	}
}
?>