<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/../' . $_SERVER['HTTP_HOST'] . '/vars.php';
session_start();
$bits = explode('/',getAddress());

function callToHeader($call,$header) {
	$raw = $call->getBody();
	$r = json_decode($raw);
	if (is_null($r) || !isset($r->msg)) {
		$msg = $raw;
	} else {
		$msg = $r->msg;
	}
	if (wasSuccessful($call)) {
		$header->addmsg($msg,'message');
	} else {
		$header->addmsg($msg,'error');
	}
}

empty($bits[0])?$ModuleHandle='':$ModuleHandle=$bits[0];
empty($bits[1])?$view='dashboard':$view=$bits[1];

$action = localize('action');
$view	= localize('view');

if (empty($ModuleHandle)) $ModuleHandle = 'dashboard';

$page_id	= $view;
$section_id	= $ModuleHandle;

$G = $_GET;
$P = $_POST;
$S = $_SESSION;

load_api();
load_header();
$header->body_id = $ModuleHandle;

$header->combine_css	= false;
$header->combine_js		= false;

if (isset($action)) {
	$msgs = array();
	global $header;
	global $api;
	$fa = gourd_file_exists($ModuleHandle . '/actions.php');
	
	if ($fa && $action != 'login') {
		include $fa;
	} else {
		include PATH_INCLUDES . 'actions.php';
	}
	
	//if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $ModuleHandle . '/actions.php')) include $_SERVER['DOCUMENT_ROOT'] . '/' . $ModuleHandle . '/actions.php';
	
	$msgs = array_filter(array_unique($msgs));
	foreach ($msgs as $msg) $header->addmsg($msg);
}

//	silently log user in if a persistent cookie is set and it is valid
if ( !isset($S['User']) && isset($_COOKIE['ut']) && ( !isset($action) || $action != 'logout' ) ) {
	$call = $api->path('/users/user')->queryparams(array('UserToken' => $_COOKIE['ut']))->auth()->get();
	if (wasSuccessful($call)) {
		$callbody = (array) json_decode($call->getBody());
		unset($callbody['ztamp']);
		unset($callbody['IsActive']);
		$callbody['Token'] = $_COOKIE['ut'];
		$S['User'] = $callbody;
	} else {
		$header->addmsg('You had a User-token, but since it was not valid, it was destroyed.','error');
		setcookie('ut',false);
	}
}


if (isset($S['User'])) {
	$module_call = $api->path("modules/$ModuleHandle")->asUser($S['User']['UserID'])->get();
} else {
	$module_call = $api->path("modules/$ModuleHandle")->get();
}

$module	= json_decode($module_call->getBody());

include 'header.php';

if (empty($S['User']['Token'])) {
	include 'login.php';
} else {
	if ($module_call->getStatus() == '200' || $module_call->getStatus() == '202') {
		load_function('subNav');
		//$f = $_SERVER['DOCUMENT_ROOT'] . '/' . $ModuleHandle . '/index.php';
		//if (file_exists($f)) include $f;
		//else include 'not-yet.php';
		
		$f = gourd_file_exists($ModuleHandle . '/index.php');
		if ($f) {
			include $f;	
		} else {
			include 'not-yet.php';
		}
			
	} else {
		include 'not-permitted.php';
	}
}

$_SESSION = $S;
include 'footer.php';
?>