<?php

if ($action) {

	load_api();

	switch ($action) {
	
		case 'login':
		$P2 = array_map('md5',$P);
		$call = $api->path('/validuser')->queryparams($P2)->get();
		load_header();
		$raw_response = $call->getBody();
		$response = (array) json_decode($raw_response);
		if ($call->getStatus() == '200' || $call->getStatus() == '202') {
			$usr = (array) $response[0];
			unset($usr['Passwd']);
			unset($usr['IsActive']);
			unset($usr['ztamp']);
			$S['User']	= $usr;
			$S['User']['Token'] = md5($S['User']['UserID']);
			$msgs[]	= 'You have been successfully logged in as ' . $usr['FullName'];
			if (isset($P['persist'])) {
				setcookie('ut',$S['User']['Token'],time() + (60*60*24*50),'/');
			}
		} else {
			$header->addmsg('That was a bad username / password combination. Please try again.','error');
		}
		break;
	
		case 'logout':
		unset($S['User']);
		setcookie('ut',false);
		$msgs[] = 'You have been logged out.';
		break;
	
		case 'resetpasswd':
		//	do nuffin yet
		break;
	
		default:
		//	do nothing because actions.php in a particular module folder could be handling this action
		break;
	
	}
}
?>