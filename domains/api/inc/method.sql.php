<?php

switch ($this->request['verb']) {

	case 'GET':
	if ($this->auth()) {
		$G		= $this->request['GET'];
		$sql	= $G['sql'];
		if (!isset($G['returnformat'])) $G['returnformat'] = 'table';		
		switch ($G['returnformat']) {
		
			case 'table':
			$result = $this->sql()->good_query_table($sql);	
			break;
			
			case 'assoc':
			$result = $this->sql()->good_query_assoc($sql);
			break;
			
			case 'value':
			$result = $this->sql()->good_query_value($sql);
			break;
		
		}		
		$r = array('result' => $result);
		$this->addToResponse($r);
	}
	break;

}

?>