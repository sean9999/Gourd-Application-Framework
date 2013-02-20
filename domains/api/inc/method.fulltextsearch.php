<?php

define('CHUNKZ_COLLECTION_NAME', 'superchunkz');

switch ($this->request['verb']) {

	case 'GET':
	$G = $this->request['GET'];
	if ( isset($representation) ) {
	
		$c = array(
			'meta.ModuleHandle' => 'wikipages'
		);
		
		$conn		= new Mongo('mongodb://'.MONGODB_SERVER.':'.MONGODB_PORT);
		$db			= $conn->selectDB(MONGODB_DB);
		$mrs 		= new MongoSearch($db,$representation);
		$rr			= $mrs->execute('weird wiki nurse');
		
		//$cursor = $this->mong()->selectCollection('708e352750ddab5670e932ea80388882')->find();
		//$r = iterator_to_array($cursor);
		//$r = $cursor;
		$r = $rr;
	} else {
		$r = array('msg' => 'you need a representation');
	}
	$this->addToResponse($r);
	break;

	case 'POST':
	break;

	case 'DELETE':
	break;

}

?>