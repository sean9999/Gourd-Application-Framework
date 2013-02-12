<?php

if (!defined('URLROOT_SIZEIFY')) define('URLROOT_SIZEIFY','http://sizeify.snappysmurf.ca');

function sizeify($imgurl,$resizeto) {
	$a = parse_url($imgurl);
	if (!isset($a['host'])) {
		//throw new Exception('That URL had no host');
		return URLROOT_SIZEIFY . '/404b.png';
	}
	if (!isset($a['path'])) {
		//throw new Exception('That URL had no path');
		return URLROOT_SIZEIFY . '/404b.png';
	}
	if (!isset($a['scheme'])) {
		//throw new Exception('That URL had no scheme');
		return URLROOT_SIZEIFY . '/404b.png';
	}
	if ($a['host'] ==  parse_url(URLROOT_SIZEIFY,PHP_URL_HOST)) {
		//throw new Exception('You cannot sizeify a siziefied URL');
		return URLROOT_SIZEIFY . '/404b.png';
	}
	/*
	if (!strpos(strtolower($a['path']),'.jpg') && !strpos(strtolower($a['path']),'.jpeg') && !strpos(strtolower($a['path']),'.gif') && !strpos(strtolower($a['path']),'.png') && !strpos(strtolower($a['path']),'.bmp')) {
		throw new Exception('That URL did not seem to be an image');
		return URLROOT_SIZEIFY . '/404b.png';
	}
	*/
	$rhost	= implode('.',array_reverse(explode('.',$a['host'])));
	$fltn	= str_replace('/','!',trim($a['path'],'/'));
	$r		= URLROOT_SIZEIFY . '/' . $a['scheme'] . '/' . $rhost . '/' . $resizeto . '/' . $fltn;
	return $r;
}

?>