<?php

switch ($this->request['verb']) {

	case 'GET':
	if (isset($representation)) {
		$r = $this->sql()->good_query_assoc("SELECT ClientID,Username,Email,FullName,Apikey,ztamp,IsActive FROM Clients WHERE ClientID = " . $representation);
	} else {
		$r = $this->sql()->good_query_table("SELECT ClientID,Username,Email,FullName,Apikey,ztamp,IsActive FROM Clients");
	}
	$this->addToResponse($r);
	break;
	
	case 'POST':
	$P	= $this->request['POST'];
	if (isset($representation)) {
		
		if (!empty($P['Username']) && !empty($P['FullName']) && filter_var($P['Email'], FILTER_VALIDATE_EMAIL)) {
			$representation = (int) $representation;
			extract($P);
			$upd = $this->sql()->good_query("
				UPDATE Clients SET
					Username = '$Username',
					Email = '$Email',
					FullName = '$FullName',
					IsActive = $IsActive
				WHERE ClientID = $representation
			");
			$r = array(
				'msg' => 'The client was successfully updated'
			);
		} else {
			$r = array(
				'msg'	=> 'Invalid user information.'
			);
			$this->setStatus(400);		
		}
		$this->addToResponse($r);
		
	} elseif (!isset($P['Username']) || !isset($P['Password']) || !isset($P['Email']) || !filter_var($P['Email'], FILTER_VALIDATE_EMAIL)) {
		$r = array(
			'msg'	=> 'Invalid user information.'
		);
		$this->setStatus(400);
		$this->addToResponse($r);
	} else {
		$default_values = array('FullName' => '');
		$P['Apikey']		= $this->createApiKey();
		$P['Secretkey']		= $this->createSecretKey();
		if (!isset($P['Passwd'])) $P['Passwd'] = md5($P['Password']);
		$new_user = array_merge($default_values,$P);
		extract($new_user);
		$ins = $this->sql()->good_query("INSERT INTO Clients (Username,Password,Email,FullName,Apikey,Secretkey) VALUES ('$Username','$Passwd','$Email','$FullName','$Apikey','$Secretkey')");
		if ($this->sql()->error) {
			$r = array(
				'msg'		=> 'The client was not created/modified. There was an error.',
				'SQLerror'	=> $this->sql()->error
			);
			$this->addToResponse($r);
			$this->setStatus(400);
		} else {
			$usr = array(
				'UserID'	=> $this->sql()->insert_id,
				'Username'	=> $Username,
				'Password'	=> $P['Password'],
				'Api Key'	=> $Apikey,
				'Api Secret'=> $Secretkey
			);
			$r = array(
				'msg'		=> 'The client was successfully created or modified.',
				'user'		=> $usr
			);
			$this->addToResponse($r);
			$this->setStatus(200);
		}
		$this->addToResponse($r);
		$this->addToResponse(array('P' => $P));
	}
	break;
	
	case 'DELETE':
	
	if (!$this->auth()) {
		$this->setStatus(401);
		$r = array(
			'msg' => 'This client does not have sufficient permissions'
		);
	} elseif (!$this->userCan('alter','Clients')) {
		$this->setStatus(401);
		$r = array(
			'msg' => 'This user not have sufficient permissions'
		);	
	} elseif (!isset($representation)) {
		$r = array(
			'msg' => 'You did not specify a client to delete'
		);
		$this->setStatus(403);
	} else {
		$del1 = $this->sql()->good_query("DELETE FROM Clients WHERE ClientID = " . $representation);
		$del2 = $this->sql()->good_query("DELETE FROM ModuleXUser WHERE UserID IN (SELECT UserID FROM Users WHERE ClientID = $representation)");
		$del3 = $this->sql()->good_query("DELETE FROM Users WHERE ClientID = " . $representation);
		$del4 = $this->sql()->good_query("DELETE FROM Modules WHERE ClientID = " . $representation);
		$del5 = $this->sql()->good_query("DELETE FROM ClientPermissions WHERE ClientID = " . $representation);
		$r = array(
			'msg' => 'The client and all associated users and modules were deleted'
		);
	}
	$this->addToResponse($r);
	break;

}

?>