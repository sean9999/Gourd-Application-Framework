<?php
require_once 'HTTP/Request2.php';

function wasSuccessful($call) {
	$r = false;
	if ($call->getStatus() == 200 || $call->getStatus() == 202) {
		$r = true;
	}
	return $r;
}

function isActive($obj) {
	$r = true;
	$obj = (array) $obj;
 	if (isset($obj['IsActive'])) $r = (bool) $obj['IsActive'];
	return $r;
}

Class RestClient extends HTTP_Request2 {

	protected $hdrs			= array('User-Agent' => 'Gourd REST Client v.10');
	protected $path			= 'ping';
	protected $method		= 'GET';
	protected $queryparams	= array();
	protected $postfields	= array();
	protected $endpoint		= 'http://api.crazyhorsecoding.net';
	protected $auth			= array();
	protected $key			= '';
	private   $secret		= '';
	
	public function hdrs($i) {
		if (is_array($i)) {
			$this->hdrs		= array_merge($i,$this->hdrs);
		} else {
			$this->throwError('hdrs must be arrays');
		}
		return $this;
	}
	
	public function path($i) {
		$o = $i;
		$o = trim($o);
		$o = trim($o,'/');
		
		//$pattern = "|^[/]?([a-zA-ZÀ-ÿ0-9-!]+[/]?)+[a-zA-ZÀ-ÿ0-9-!\.]+$|";
		
		$pattern = "|(.*)|";
		
		//$pattern = "|^[/]?([\w-!]+[/]?)+[\w-\.]+$|";
		if (is_string($i) && preg_match($pattern,$o)) {
			$this->path = $o;
		} else {
			$this->throwError('The path looks invalid');
		}
		return $this;
	}
	
	public function method($i) {
		$acceptable_methods = array('GET','POST','PUT','DELETE');
		$o = strtoupper($i);
		if (in_array($o,$acceptable_methods)) {
			$this->method = $o;
		} else {
			$this->throwError('That method looks bad.');
		}
		return $this;
	}
	
	public function queryparams($i) {	
		if (is_array($i)) {
			//$o = filter_var_array($i, FILTER_SANITIZE_ENCODED, FILTER_FLAG_STRIP_LOW);
			//$o = $i;
			//$o = json_encode($i);
			$o = array('jsonq' => json_encode($i));
			//$o = array_map('json_encode',$o);
			$this->queryparams = $o;
		} else {
			$this->throwError('Query Params must be arrays.');
		}
		return $this;
	}
	
	public function postfields($i,$asjson=false) {
		if (is_array($i)) {
			if ($asjson) {
				$o = array('jsonpost' => json_encode($i));
			} else {
				$o = $i;
			}
			$this->postfields = $o;
		} else {
			$this->throwError('Post Fields must be arrays.');
		}
		return $this;
	}
	
	public function endpoint($i) {
		$o = $i;
		if (filter_var($o, FILTER_VALIDATE_URL)) {
			$this->endpoint = $o;
		} else {
			$this->throwError('That endpoint looks bad.');
		}
		return $this;
	}
	
	public function dbg($trueorfalse=true) {
		$flag = (int) $trueorfalse;
		$this->setHeader(array('X-Gourd-Debug' => $flag));
		return $this;
	}
	
	public function apikey($i) {
		$o = $i;
		if (ctype_xdigit($o)) {
			$this->setHeader(array('X-Gourd-Apikey' => $o));
		} else {
			//$this->throwError('That Api key looked real bad.');
		}
		return $this;
	}
	
	public function asUser($UserID) {
		$UserID = (int) $UserID;
		if ($UserID) {
			$this->setHeader(array('X-Gourd-UserID' => $UserID));
		}		
		return $this;
	}
	
	public function notAsUser() {
		$this->setHeader(array('X-Gourd-UserID' => NULL));
		return $this;
	}
	
	public function auth($u=API_KEY,$p=API_SECRET) {
		if ( strlen($u) && ctype_xdigit($u) && is_string($p) && strlen($p)) {
			$this->auth = array(
				'user'		=> $u,
				'password'	=> $p
			);
		}
		return $this;
	}
	
	private function throwError($msg) {
		throw new Exception($msg);
	}

	
	public function post() {
		$this->method('POST');
		return $this->doit();
	}

	public function get() {
		$this->method('GET');
		return $this->doit();
	}
	
	public function delete() {
		$this->method('DELETE');
		return $this->doit();
	}
	
	public function getPhp($as_assoc=false) {
		$r = json_decode($this->get()->getBody(),$as_assoc);
		return $r;
	}
	
	public function doit() {
		$this->setUrl($this->endpoint . '/' . $this->path);
		$this->setMethod($this->method);
		if (!empty($this->auth)) {
		
			if (isset($this->auth['type']) && $this->auth['type'] == 'basic') {
				$this->setAuth($this->auth['user'], $this->auth['password'], HTTP_Request2::AUTH_BASIC);
			} else {
				$this->setAuth($this->auth['user'], $this->auth['password'], HTTP_Request2::AUTH_DIGEST);
			}
		}
		if (!empty($this->hdrs)) $this->setHeader($this->hdrs);
		$url = $this->getUrl();
		switch ($this->method) {
			case 'POST':
			$this->addPostParameter((array) $this->postfields);
			case 'GET':
			case 'PUT':
			case 'DELETE':
			$url->setQueryVariables((array) $this->queryparams);
			break;
		}		
		return $this->send();
	}

}

?>