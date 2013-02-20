<?php
switch ($this->request['verb']) {
	case 'GET':
	
	$G	 = $this->request['GET'];
	
	$usr = array();
	if (isset($G['usr']) && isset($G['passwd'])) {
		$usr = $this->sql()->good_query_assoc("SELECT * FROM Users WHERE MD5(Username) = '$G[usr]' AND Passwd = '$G[passwd]' AND IsActive = 1 AND ClientID = ".$this->getClientID());
	} else {
		$this->addToResponse(array('error' => 'Insufficient or invalid query parameters'));
	}
	
	
	if (sizeof($usr)) {
		$this->setStatus(200);
		$this->addToResponse(array($usr));
	} else {
		$this->setStatus(401);
		$this->addToResponse(array('msg' => 'bad credentials'));
	}
	
	break;
	
	/*
	case 'POST':
	$P	= $this->request['POST'];
	$q	= "INSERT INTO Users (`ClientID`,`Username`,`Passwd`,`Email`,`FullName`)
		VALUES (".$this->getClientID().",'$P[Username]','".md5($P['Passwd'])."','$P[Email]','$P[FullName]')";
	$i	= $this->sql()->good_query($q);
	if ($this->sql()->error) {
		$r = array(
			'msg'		=> 'The user was not created. There was an error.',
			'SQLerror'	=> $this->sql()->error
		);
		$this->addToResponse($r);
		$this->setStatus(400); 
	} else {
		$r = array(
			'msg'		=> 'The user was successfully created.',
			'UserID'	=> $this->sql()->insert_id
		);
		$this->addToResponse($r);
		$this->setStatus(200);
	}
	break;
	*/
}
?>