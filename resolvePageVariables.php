<?php
if (isset($page_title) && !isset($page_id)) {
	$page_id 		= sanitize($page_title);
}

if (!isset($page_title) && isset($page_id)) {
	$page_title 	= glamourize($page_id);
}

if (isset($section_title) && !isset($section_id)) {
	$section_id 	= sanitize($section_title);
}

if (!isset($section_title) && isset($section_id)) {
	$section_title 	= glamourize($section_id);
}

if (isset($page_title) && !isset($section_title)) {
	$section_title	= $page_title;
	$section_id		= $page_id;
}

if (!isset($page_title) && isset($section_title)) {
	$page_title 	= $section_title;
	$page_id		= $section_id;
}

if (!isset($page_title)) {
	// if nothing at all was declared, create variables based on the name of the file
	$s				= $_SERVER["PHP_SELF"];
	$s				= trim($s,'/');
	$s				= str_replace('/',' • ',$s);
	$s				= str_replace('.php','',$s);
	$s				= str_replace('_•_','_',$s);
	$page_title		= glamourize($s);
	$page_id		= sanitize($s);
	$section_title 	= $page_title;
	$section_id		= $page_id;
}

function sanitize($i) {
	$o 				= trim($i);
	$o 				= strtolower($o);
	$o 				= SEOify($o);
	return $o;
}

function glamourize($i) {
	$o = $i;
	$o = str_replace('_',' ',$o);
	$o = ucwords($o);
	return $o;
}

?>