<?php

define('TA_COLLECTION_NAME', 'textassets01');

switch ($this->request['verb']) {

	case 'GET':
	$G = $this->request['GET'];
	if ( isset($representation) && $this->userCan('read',$representation)) {
		//	your module handle is $representation
		if (isset($pathbits[2])) {
		
			if (isset($G['key']) && $G['key'] != 'meta.handle') {
				$c1	= array($G['key'] => $pathbits[2]);
			} else {
				//$cid= new MongoId($pathbits[2]);
				$c1	= array('meta.handle' => $pathbits[2]);
			}
			$c2 	= array(
				'meta.ClientID'		=> (int) $this->getClientID(),
				'meta.ModuleHandle' => $representation
			);
			$c 		= array_merge($c1,$c2);
			$f		= array();
			$cursor	= $this->executeMongoQuery(TA_COLLECTION_NAME,$c,$f,$this->request['GET']);
			$r		= $cursor->getNext();
		} else {
			$c		= array(
				'meta.ModuleHandle' => (string) $representation,
				'meta.ClientID'		=> (int) $this->getClientID()
			);
			if (isset($G['handles'])) {
				$c['meta.handle'] = array('$in' => $G['handles']);
			}
			$f		= array();
			$cursor	= $this->executeMongoQuery(TA_COLLECTION_NAME,$c,$f,$this->request['GET']);
			$r		= array();
			while ($row = $cursor->getNext()) $r[] = $row;
		}
		$this->addToResponse($r);
	} else {
		$this->setStatus(401);
		$this->addToResponse(array('msg' => 'Your user does not have permission to access that namespace'));
	}
	break;

	case 'POST':
	if ( isset($representation) && $this->userCan('write',$representation)) {
		$P = $this->request['POST'];
		if (isset($P['sort'])) settype($P['sort'],'int'); else $P['sort'] = 0;
		$opts = array('safe' => true);
		
		$meta = $P['meta'];
		$meta['ClientID'] = (int) $this->getClientID();
		$meta['ModuleHandle'] = (string) $representation;
		
		if (!isset($meta['handle'])) {
			$meta['handle']	= uniqid();
		}
		
		ksort($meta);		
		
		if (isset($pathbits[2])) {
			//	this is an update
			$cid	 	= new MongoId($pathbits[2]);
			$obj		= $P;
			$obj['_id']	= $cid;
			$obj['meta']= $meta;
			$obj['LastUpdated'] = time();
			
			try {
				$upd = $this->mong()->selectCollection(TA_COLLECTION_NAME)->save($obj,$opts);
				$this->addToResponse(array('msg' => 'your ta has been updated.'));
			} catch (Exception $e) {
				$this->setStatus(401);
				$this->addToResponse(array('msg' => $e->getMessage()));			
			}
		} else {
			//	this is in an insert
			$obj = $P;
			$obj['meta'] = $meta;
			$obj['FirstCreated'] = time();
			try {
				$ins = $this->mong()->selectCollection(TA_COLLECTION_NAME)->save($obj,$opts);
				$this->addToResponse(array('msg' => 'your ta has been created.'));
			} catch (Exception $e) {
				$this->setStatus(401);
				$this->addToResponse(array('msg' => $e->getMessage()));
			}
		}
		
	} else {
		$this->setStatus(401);
	}
	break;

	case 'DELETE':
	if ( isset($representation) && $this->userCan('write',$representation) && isset($pathbits[2])) {
		$cid = new MongoId($pathbits[2]);
		$c = array('_id' => $cid);
		$this->mong()->selectCollection(TA_COLLECTION_NAME)->remove($c);
		$r = array('msg' => 'The chunk has been removed');
		$this->addToResponse($r);
	} else {
		$this->setStatus(401);
	}
	break;

}

?>