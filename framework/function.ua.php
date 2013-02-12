<?php
function isFF($ua=NULL) {
	$r = false;
	if (is_null($ua)) $ua = $_SERVER['HTTP_USER_AGENT'];
	if ($b = strpos($ua,'Firefox')) {
		$reg = "/[0-9\.]+/";
		if(preg_match($reg, substr($ua, $b), $match)) {
			$r = (float) array_pop($match);
		}
	}
	return $r;
}
function isIE($ua=NULL) {
	$r = false;
	if (is_null($ua)) $ua = $_SERVER['HTTP_USER_AGENT'];
	if (strpos($ua,'MSIE')) {
		$bits = explode('; ', $ua);
		$important_bit = array_reduce($bits, create_function('$a,$b','if (strpos($a,"MSIE") !== false) return $a; if (strpos($b,"MSIE") !== false) return $b; return false;'));
		$reg = "/[0-9\.]+/";
		if(preg_match($reg, $important_bit, $match)) {
			$r = (float) array_pop($match);
		}
	}
	return $r;
}
function isChromeOnWindows($ua=NULL) {
	$r = false;
	if (is_null($ua)) $ua = $_SERVER['HTTP_USER_AGENT'];
	if (strpos($ua, 'Chrome') !== false && (preg_match('/windows|win32/i', $ua))) {
		$r = true;
	}
	return $r;
}

function isChrome($ua=NULL) {
	$r = false;
	if (is_null($ua)) $ua = $_SERVER['HTTP_USER_AGENT'];
	if ($b = strpos($ua,'Chrome')) {
		$reg = "/[0-9\.]+/";
		if(preg_match($reg, substr($ua, $b), $match)) {
			$r = (float) array_pop($match);
		}
	}
	return $r;
}

function vendorPrefix($ua=NULL) {
	$r = '';
	if (is_null($ua)) $ua = $_SERVER['HTTP_USER_AGENT'];
	if (stripos($ua,'webkit') !== false) {
		$r = '-webkit-';
	} elseif (stripos($ua,'opera') !== false) {
		$r = '-o-';
	} elseif (stripos($ua,'firefox') !== false) {
		$r = '-moz-';
	} elseif (strpos($ua,'MSIE') !== false) {
		$r = '-ms-';
	}
	return $r;
}

?>