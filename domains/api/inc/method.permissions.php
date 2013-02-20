<?php
switch ($this->request['verb']) {
	case 'GET':
	if (isset($representation)) {
		$r = $this->sql()->good_query_table("SELECT * FROM ModuleXUser WHERE UserID = " .$representation. " AND UserID IN (SELECT UserID FROM Users WHERE ClientID = ".$this->getClientID().")");
	} else {
		$r = $this->sql()->good_query_table("SELECT * FROM ModuleXUser WHERE UserID IN (SELECT UserID FROM Users WHERE ClientID = ".$this->getClientID().")");
	}
	$this->addToResponse($r);				
	break;

	case 'POST':
	if ($this->auth()) {
		$P	= $this->request['POST'];
		$matrix = array();
		if (isset($P['UserID'])) {
			//	we can assume it's a 1-d array
			$matrix[0] = $P;
		} else {
			//	must be 2-d
			$matrix = $P;
		}
		
		$default_permissions = array(
			'CanRead'	=> 0,
			'CanWrite'	=> 0,
			'CanAlter'	=> 0
		);
		foreach ($matrix as $m) {
			$mm = array_merge($default_permissions,$m);
			$q	= "INSERT INTO ModuleXUser (`ModuleID`,`UserID`,`CanRead`,`CanWrite`,`CanAlter`)
			VALUES ($mm[ModuleID],$mm[UserID],$mm[CanRead],$mm[CanWrite],$mm[CanAlter])
			ON DUPLICATE KEY UPDATE CanRead = $mm[CanRead], CanWrite = $mm[CanWrite], CanAlter = $mm[CanAlter]";
			$i = $this->sql()->good_query($q);	
		}
		if ($this->sql()->error) {
			$r = array(
				'msg'		=> 'The permission matrix was not created or updated. There was an error.',
				'SQLerror'	=> $this->sql()->error
			);
			$this->addToResponse($r);
			$this->setStatus(400);
		} else {
			$r = array(
				'msg'		=> 'The permission matrix was successfully created or modified.',
				'matrix'	=> $matrix
			);
			$this->addToResponse($r);
			$this->setStatus(200);
		}
	} else {
		$this->addToResponse(array('msg' => 'The permissions matrix was not created because authentication failed.'));
	}
	break;
	
	case 'DELETE':
	if ($this->auth()) {
		if (!empty($this->request['UserID']) && !empty($this->request['GET']['ModuleID'])) {
			
			$kill = $this->sql()->good_query("DELETE FROM ModuleXUser WHERE UserID = ".$this->request['UserID']." and ModuleID = ".$this->request['GET']['ModuleID']);
			
			if ($this->sql()->error) {
				$r = array(
					'msg'		=> 'The permission matrix was not deleted. There was an error.',
					'SQLerror'	=> $this->sql()->error
				);
				$this->addToResponse($r);
				$this->setStatus(400);
			} else {
				$r = array(
					'msg'		=> 'The permission matrix was successfully deleted.',
					'Affected_Rows' => $this->sql()->affected_rows
				);
				$this->addToResponse($r);
				$this->setStatus(200);
			}
		} else {
			$this->addToResponse(array('msg' => 'No permission matrix was deleted because of missing query parameters.'));
			$this->addToResponse($this->request);
			$this->setStatus(412);
		}
	}
	break;
	
}
?>