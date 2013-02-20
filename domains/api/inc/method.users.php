<?php

/*
if (!empty($this->request['UserID'])) {
	$this->setModule('Users');
}
*/

switch ($this->request['verb']) {

	case 'GET':
	if ($this->auth()) {
		if (isset($representation)) {
			if ($representation == 'user') {
				$G = $this->request['GET'];
				$r = $this->sql()->good_query_assoc("SELECT * FROM Users WHERE MD5(UserID) = '" . $G['UserToken'] . "' AND IsActive = 1 AND ClientID = ".$this->getClientID());
			} else {
				$r = $this->sql()->good_query_assoc("SELECT * FROM Users WHERE UserID = " . $representation . " AND IsActive = 1 AND ClientID = ".$this->getClientID());
			}
			$more_info = (array) $this->mong()->users->findOne(
				array(
					'_id'		=> (int) $r['UserID'],
					'ClientID'	=> (int) $r['ClientID']				
				)
			);
			$r = array_merge($more_info,$r);
		} else {
			$r = $this->sql()->good_query_table("SELECT * FROM Users WHERE IsActive = 1 AND ClientID = ".$this->getClientID());
		}
		$this->addToResponse($r);
	}
	break;

	case 'POST':
	if ($this->auth() && $this->userCan('write','Users') || (isset($representation) && $representation == $this->request['User']['UserID'])) {
		$P	= $this->request['POST'];
		if (isset($representation)) {
		$q	= 	"UPDATE Users SET
					Username	 = '$P[Username]',
					Email		 = '$P[Email]',
					FullName	 = '$P[FullName]'
				 WHERE	UserID	 = $representation
				 AND	ClientID = " . $this->getClientID();
		$usr = $P;
		$usr['_id'] = (int) $P['UserID'];
		$usr['ClientID'] = (int) $this->getClientID();
		unset($usr['UserID']);
		$upd2 = $this->mong()->users->save($usr);
		} else {
		$q	= "INSERT INTO Users (`ClientID`,`Username`,`Passwd`,`Email`,`FullName`)
			VALUES (".$this->getClientID().",'$P[Username]','".md5($P['Passwd'])."','$P[Email]','$P[FullName]')";		
		}
		$i	= $this->sql()->good_query($q);
		if ($this->sql()->error) {
			$r = array(
				'msg'		=> 'The user was not created/modified. There was an error.',
				'SQLerror'	=> $this->sql()->error
			);
			$this->addToResponse($r);
			$this->setStatus(400); 
		} else {
			$r = array(
				'msg'		=> 'The user was successfully created or modified.',
				'UserID'	=> $this->sql()->insert_id
			);
			$this->addToResponse($r);
			$this->setStatus(200);
		}	
	}
	break;
	
	case 'DELETE':
	if ($this->auth() && $this->userCan('alter','Users') ) {
		$G = $this->request['GET'];
		$kill = $this->sql()->good_query("DELETE FROM Users WHERE UserID = $G[UserID] AND ClientID =".$this->getClientID());
		$kill2 = $this->mong()->users->remove(array(
			'_id' => (int) $G['UserID'],
			'ClientID' => (int) $this->getClientID()
		));
		if ($this->sql()->error) {
			$r = array(
				'msg'		=> 'The user was not deleted. There was an error.',
				'SQLerror'	=> $this->sql()->error
			);
			$this->addToResponse($r);
			$this->setStatus(400);
		} else {
			$r = array(
				'msg'		=> 'The user was deleted.',
				'Affected_Rows'	=> $this->sql()->affected_rows
			);
			$this->addToResponse($r);
			$this->setStatus(200);
		}	
	}
	break;

}
?>