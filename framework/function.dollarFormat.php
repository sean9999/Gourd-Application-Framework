<?php
function dollarFormat_x($amount,$include_dollar_sign=true,$thousands_separator=',') {
	$o = '';
	if ($include_dollar_sign) $o .= '$';
	//$o .= sprintf("%.2f",$amount);
	//$o .= sprintf("%01.2f",$amount);
	$o .= number_format($amount,2,'.',$thousands_separator);
	return $o;
}

function dollarFormat($amt,$lang=NULL) {
	$r = false;
	if (is_null($lang)) {
		if (isset($GLOBALS['lang'])) {
			$lang = $GLOBALS['lang'];
		} elseif(isset($_COOKIE['lang'])) {
			$lang = $_COOKIE['lang'];
		} else {
			$lang = 'en';
		}
	}
	switch ($lang) {
		case 'fr':
		$r = number_format($amt,2,',',' ') . ' $';
		break;
		
		default:
		$r = '$' . number_format($amt,2,'.',',');
	}
	return $r;
}

?>