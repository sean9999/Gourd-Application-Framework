<?php
switch ($this->request['verb']) {
	case 'GET':
	
	//if (isset($representation) && is_numeric($representation)) {
	
	if (isset($representation)) {
		
		$canread = true;
		if (!empty($this->request['User']['UserID'])) {
			if (is_numeric($representation)) {
				$canread = $this->sql()->good_query_value("SELECT CanRead FROM ModuleXUser WHERE UserID = " .$this->request['User']['UserID']. " AND ModuleID = $representation");
			} else {
				$canread = $this->sql()->good_query_value("SELECT CanRead FROM ModuleXUser WHERE UserID = " .$this->request['User']['UserID']. " AND ModuleID = (SELECT ModuleID FROM Modules WHERE ClientID = ".$this->getClientID()." AND Handle = '$representation')");
			}
		}
		
		if ($canread) {
			if (is_numeric($representation)) {
				$sql = "SELECT * FROM Modules WHERE ModuleID = $representation AND IsActive =1";
			} else {
				$sql = "SELECT * FROM Modules WHERE ClientID = ".$this->getClientID()." AND Handle = '$representation' AND IsActive = 1";
			}
			$module = $this->sql()->good_query_assoc($sql);
			if (sizeof($module)) {
				$this->addToResponse($module);
			} else {
				$this->setStatus(404);
				$this->addToResponse(array('msg' => 'The module was not found'));
			}
		} else {
			$this->setStatus(401);
			$this->addToResponse(array('msg' => 'That user does not have read access to that module'));
		}

	} elseif (isset($representation) && $representation == 'groups') {
	
		$rows = $this->sql()->good_query_table("SELECT `Group` FROM Modules WHERE ClientID = ".$this->getClientID());
		$r = array();
		foreach ($rows as $row) $r[] = $row['Group'];
		$r = array_unique($r);
		sort($r);
		$this->addToResponse($r);

	} else {
		if (!empty($this->request['User']['UserID'])) {
			$r = $this->sql()->good_query_table("
				SELECT * FROM Modules WHERE ModuleID IN (SELECT ModuleID FROM ModuleXUser WHERE UserID = ".$this->request['User']['UserID']." AND CanRead <> 0) AND IsActive = 1
				ORDER BY `Group` ASC, `Sort` ASC
			");
		} else {
			$r = $this->sql()->good_query_table("SELECT * FROM Modules WHERE ClientID = ".$this->getClientID()." ORDER BY `Group` ASC, `Sort` ASC");
		}
		$this->addToResponse($r);
	}	
	break;

	case 'POST':
	if ($this->auth()) {

		if (isset($representation)) {
			$P	= $this->request['POST'];
			if ($representation == 'resort') {
				
				$r = array();
				$mids = explode(',',$P['moduleids']);
				$count=0;
				foreach ($mids as $mid) {
					$count++;
					$u = $this->sql()->good_query("UPDATE Modules SET Sort = $count WHERE ModuleID = $mid");
				}
				$r['msg']	= 'The modules have been re-sorted';
				$this->addToResponse($r);
				
			} else {
				
				if (is_numeric($representation)) {
					$q	= "UPDATE Modules SET `Handle` = '$P[Handle]', `Title` = '$P[Title]', `Group` = '$P[Group]', `Target` = '$P[Target]', `IsActive` = $P[IsActive] WHERE `ModuleID` = $representation";
				} else {
					$q	= "UPDATE Modules SET Handle = '$P[Handle]', Title = '$P[Title]', Group = '$P[Group]', Target = '$P[Target]', IsActive = $P[IsActive] WHERE Handle = '$P[Handle]' AND ClientID =" . $this->getClientID();
				}
				$u = $this->sql()->good_query($q);
				if ($this->sql()->error) {
					$r 	= array(
						'msg'		=> 'The module was not modified. There was an error.',
						'SQLerror'	=> $this->sql()->error
					);
					$this->addToResponse($r);
					$this->addToResponse(array('q' => $q));
					$this->setStatus(400); 
				} else {
					$r = array(
						'msg'		=> 'The Module was successfully modified.',
						'ClientID'	=> $this->getClientID(),
						'ModuleID'	=> $representation
					);
					$this->addToResponse($r);
					$this->setStatus(200);
				}			
			}	
		} else {
			$P1	= $this->request['POST'];
			$defaults = array(
				'Group'		=> '',
				'Target'	=> '_self'
			);
			$P	= array_merge($defaults,$P1);
			$q	= "INSERT INTO Modules (`ClientID`,`Handle`,`Title`,`Group`,`Target`) VALUES (".$this->getClientID().",'$P[Handle]','$P[Title]','$P[Group]','$P[Target]')";
			$i	= $this->sql()->good_query($q);
			if ($this->sql()->error) {
				$r 	= array(
					'msg'		=> 'The module was not created. There was an error.',
					'SQLerror'	=> $this->sql()->error
				);
				$this->addToResponse($r);
				$this->setStatus(400); 
			} else {
				$r = array(
					'msg'		=> 'The Module was successfully created.',
					'ClientID'	=> $this->getClientID(),
					'ModuleID'	=> $this->sql()->insert_id
				);
				$this->addToResponse($r);
				$this->setStatus(200);
			}
		}
	}
	break;
	
	case 'DELETE':
	if (isset($representation)) {
		if (is_numeric($representation)) {
			$sql = "
				DELETE FROM Modules WHERE ModuleID = $representation AND ClientID = " . $this->getClientID . " ;
				DELETE FROM ModuleXUser WHERE ModuleID = $representation
		";
		} else {
			$sql = "
				DELETE FROM ModuleXUser WHERE ModuleID = (SELECT ModuleID FROM Modules WHERE Handle = '$representation' AND ClientID = ".$this->ClientID.");
				DELETE FROM Modules WHERE Handle = '" . $representation . " AND ClientID = " . $this->ClientID;
		}
		foreach (explode(';',$sql) as $this_sql) {
			$kill	= $this->sql()->query($this_sql);
		}
		if ($this->sql()->error) {
			$r 	= array(
				'msg'		=> 'The module was not deleted. There was an error.',
				'SQLerror'	=> $this->sql()->error
			);
			$this->addToResponse($r);
			$this->setStatus(400); 
		} else {
			$r = array(
				'msg'		=> 'The Module was successfully deleted, i think.',
				'ClientID'	=> $this->getClientID(),
				'Number Of Affected Rows'	=> $this->sql()->affected_rows
			);
			$this->addToResponse($r);
			$this->setStatus(200);
		}
	} else {
		$this->addToResponse(array('msg' => 'I can`t let you delete all modules at once. That would be insanity'));
		$this->setStatus(401);
	}
	
	break;
	
}
?>