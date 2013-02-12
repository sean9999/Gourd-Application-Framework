<?php

function localize($var,$mode='wimpy') {	
	global $G;
	global $$var;
	$o = $$var;
	if (!isset($$var) || $mode == 'greedy') {
		// follow variables_order: "EGPCS"
		if (isset($_COOKIE[$var]))	{ $o = $_COOKIE[$var]; 	}
		if (isset($_POST[$var]))	{ $o = $_POST[$var]; 	}
		if (isset($_GET[$var]))		{ $o = $_GET[$var];  	}
	}
	return $o;
}

function getAddress() {
	// strip out real path. No args. also, not "/" at the beginning or end.
	// ex:	products/Kraft/KraftDinner
	$url=$_SERVER['REQUEST_URI'];
	if ($x = strpos($url,'?')) {
		$url = substr($url,0,$x);
	}
	if ($y = strpos($url,'#')) {
		$url = substr($url,0,$y);
	}
	return trim($url,'/');
	$address	= trim($address,'/');
	return $address;
}

function __autoload($classname) {
	$inc = 'class.'.$classname.'.php';	
	include $inc;
}

function load_header() {
	global $header;
	if (! ($header instanceof Header)) {
		$header  = new Header();
	}
	return $header;
}

function load_function($func_name) {
	require_once 'function.' . $func_name . '.php';
}

function load_core() {	
	global $core;
	if (! ($core instanceof Core)) $core = new Core;
}

function remove_accent($str)  {
	$a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ'); 
	$b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o'); 
	return str_replace($a, $b, $str); 
} 

function load_api() {
	global $api;
	if (! ($api instanceof RestClient)) {
		$api = new RestClient();
		if (defined('API_ENDPOINT'))	$api->endpoint(API_ENDPOINT);
		if (defined('API_KEY'))			$api->apikey(API_KEY);
		if (defined('API_USERID'))		$api->asUser(API_USERID);
	}
	return $api;
}

function SEOify($i){
	//	http://php.ca/manual/en/function.preg-replace.php#90316
	$o			= $i;
	$o			= htmlspecialchars_decode($o);
	$o			= remove_accent(trim($i));
	$o			= str_ireplace('/', ' ',$o);
	$o			= str_ireplace('\\',' ',$o);
	$o			= str_ireplace('(', ' ',$o);
	$o			= str_ireplace(')', ' ',$o);
	$o			= str_ireplace('[', ' ',$o);
	$o			= str_ireplace(']', ' ',$o);
	$o			= trim($o);
    $patterns 	= array( "([\40])" , "([^a-zA-Z0-9_-])", "(-{2,})" ); 
    $replacers	= array("-", "", "-"); 
    $o			= preg_replace($patterns, $replacers, $o);
    return $o;
}

function z($p) {
	$o = URLROOT_Z . '/' . trim($p,'/');
	return $o;
}

function gourd_file_exists($fyle) {
	
	if (isset($_SERVER['ZONE'])) {
		switch ($_SERVER['ZONE']) {
		
			case 'dev':
			if (file_exists(PATH_ROOT . '/dev/inc/' . $fyle)) {
				return PATH_ROOT . '/dev/inc/' . $fyle;
				break;
			} elseif (file_exists(PATH_ROOT . '/dev/web/' . $fyle)) {
				return PATH_ROOT . '/dev/web/' . $fyle;
				break;
			}
			
			case 'stage':
			if (file_exists(PATH_ROOT . '/stage/inc/' . $fyle)) {
				return PATH_ROOT . '/stage/inc/' . $fyle;
				break;
			} elseif (file_exists(PATH_ROOT . '/stage/web/' . $fyle)) {
				return PATH_ROOT . '/stage/web/' . $fyle;
				break;
			}
			
			case 'prod':
			if (file_exists(PATH_ROOT . '/prod/inc/' . $fyle)) {
				return PATH_ROOT . '/prod/inc/' . $fyle;
				break;
			} elseif (file_exists(PATH_ROOT . '/prod/web/' . $fyle)) {
				return PATH_ROOT . '/prod/web/' . $fyle;
				break;
			}	
		}	
	} else {
		if (file_exists(PATH_ROOT . '/' . $_SERVER['HTTP_HOST'] . '/' . $fyle)) {
			return PATH_ROOT . '/' . $_SERVER['HTTP_HOST'] . '/' . $fyle;
		}
	}
	return false;
}

function gourd_asset_exists($fyle) {
	//	note: this returns that actual blob. not just a path.
	switch ($_SERVER['ZONE']) {
		case 'dev':
		if ($r = file_get_contents(PATH_ROOT . '/dev/z/' . $fyle)) {
			return $r;
			//break;
		}
		case 'stage':
		if ($r = file_get_contents(PATH_ROOT . '/stage/z/' . $fyle)) {
			return $r;
			//break;
		}	
		case 'prod':
		if ($r = file_get_contents(PATH_ROOT . '/prod/z/' . $fyle)) {
			return $r;
			//break;
		}
	}
	if (defined('PATHROOT_CORE')) {
		if ($r = file_get_contents(PATHROOT_CORE . '/' . $fyle)) {
			return $r;
		} 
	} elseif (defined('URLROOT_CORE')) {
		if ($r = file_get_contents(URLROOT_CORE . '/' . $fyle)) {
			return $r;
		}
	}
	return false;
}


function unta($tas,$inhtml) {
	$html = $inhtml;
	if (!empty($tas)) {
		global $lang;
		global $api;
		load_api();
		if (empty($lang)) $lang = 'en';
		$q = array(
			'criteria' 	=> array(
				'meta.handle' => array('$in' => $tas),
				'meta.ModuleHandle' => TEXTASSETS_MODULEHANDLE
			),
			'fields'	=> array('content.' . $lang,'meta.handle')
		);
		$m = new Mongo('mongodb://hefty01');
		$textassets = $m->gourd01->textassets01->find(
			$q['criteria'],
			$q['fields']
		);
		$mapz				= array();
		$empty_tas			= array();
		foreach ($textassets as $t) {
			if (isset($t['content'][$lang])) {
				$k			= $t['meta']['handle'];
				$v			= $t['content'][$lang];
				$mapz[$k]	= base64_encode($v);
			} else {
				$empty_tas[]= $t['meta']['handle'];
			}
		}
		$pat			= '/<!-- #gourd:ta:(\S+) -->/';
		$html			= preg_replace_callback($pat,create_function('$matches','
			$mapz		= unserialize(\'' . serialize($mapz) . '\');
			$empty_tas 	= unserialize(\'' . serialize($empty_tas) . '\');
			$key		= $matches[1];
			$r			= \'<span class="missingtextasset">\' . $key . \'</span>\';
			if (isset($mapz[$key])) {
				$r 		= base64_decode($mapz[$key]);
			} elseif (!in_array($key,$empty_tas)) {
				$m 		= new Mongo("mongodb://hefty01");
				$ta = array(
					"label"	=> "Needs Content! " . $key,
					"meta"	=> array(
						"handle"		=> $key,
						"ClientID"		=> '.API_CLIENTID.',
						"ModuleHandle" 	=> "'.TEXTASSETS_MODULEHANDLE.'"
					)
				);
				$ins = $m->gourd01->textassets01->save($ta);
			}
			return $r;
		'),$html);
	}
	return $html;
}

function ta($handle) {
	if (!isset($GLOBALS['tas'])) $GLOBALS['tas'] = array();
	$GLOBALS['tas'][] = $handle;
	return '<!-- #gourd:ta:' . $handle . ' -->';
}

function swapLanguage() {
	global $lang;
	global $notlang;
	$goodlang	= $notlang;
	$badlang	= $lang;
	$lang		= $goodlang;
	$notlang	= $badlang;
	setcookie('lang',$lang,0,'/');
}

?>