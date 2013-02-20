<?php

define('CHUNKZ_COLLECTION_NAME', 'superchunkz');

switch ($this->request['verb']) {

	case 'GET':
	$G = $this->request['GET'];
	if ( isset($representation) && $this->userCan('read',$representation)) {
		//	your module handle is $representation
		if (isset($pathbits[2]) && $pathbits[2] != 'tags' && $pathbits[2] != 'laststamp' ) {
			if (isset($G['key']) && $G['key'] != '_id') {
				$c	= array($G['key'] => $pathbits[2]);
			} else {
				$cid= new MongoId($pathbits[2]);
				$c	= array('_id' => $cid);	
			}
			$f		= array();
			$cursor	= $this->executeMongoQuery(CHUNKZ_COLLECTION_NAME,$c,$f,$this->request['GET']);
			$r		= $cursor->getNext();
			
		} elseif (isset($pathbits[2]) && $pathbits[2] == 'tags') {
			
			$c		= array();
			$f		= array('content.tags');
			$cursor	= $this->executeMongoQuery(CHUNKZ_COLLECTION_NAME,$c,$f,$this->request['GET']);
			$r		= array();
			while ($row = $cursor->getNext()) {
				if (!empty($row['content']['tags'])) {
					foreach ((array)$row['content']['tags'] as $tag) {
						$r[] = $tag;
					}
				}
			}
			$r = array_filter(array_unique($r));
			sort($r);
		
		} elseif (isset($pathbits[2]) && $pathbits[2] == 'laststamp') {
			
			//	since this is just for LFP, here's our hack:
			$c		= array( 
				'meta.ModuleHandle' => array('$in' => array('glossary','printers'))
			);
			
			
			$f		= array('meta.LastUpdated');
			$cursor	= $this->executeMongoQuery(CHUNKZ_COLLECTION_NAME,$c,$f,$this->request['GET']);
			$cursor->sort(array('meta.LastUpdated' => -1))->limit(1);
			$lu		= $cursor->getNext();
			$r		= array('LastUpdated' => $lu['meta']['LastUpdated']->sec . '.' . $lu['meta']['LastUpdated']->usec);
			//$this->addToResponse(array('rx' => $rx));
			//$this->addToResponse(array('rx2' => 'foo'));
		
		} else {
			$c		= array(
				'meta.ModuleHandle' => (string) $representation,
				'meta.ClientID'		=> (int) $this->getClientID()
			);
			$f		= array();
			$cursor	= $this->executeMongoQuery(CHUNKZ_COLLECTION_NAME,$c,$f,$this->request['GET']);
			//$r		= iterator_to_array($cursor);
			$r = array();
			while ($row = $cursor->getNext()) $r[] = $row;
		}
		$this->addToResponse($r);
	} else {
		$this->setStatus(401);
		$this->addToResponse(array('msg' => 'Insufficient permissions.'));
		$this->addToResponse(json_encode($this->request));
	}
	break;

	case 'POST':
	if ( isset($representation) && $this->userCan('write',$representation)) {
		$P = $this->request['POST'];
		if (isset($P['sort'])) settype($P['sort'],'int'); else $P['sort'] = 0;
		$opts		= array('safe' => true);
		$meta		= array(
			'ClientID'		=> (int) 	$this->getClientID(),
			'ModuleHandle'	=> (string)	$representation,
			'LastUpdated'	=> new MongoDate()
		);
		foreach ($P as $k => $v) {
			if (strpos($k,'meta:') === 0) {
				$radkey = str_ireplace('meta:','',$k);
				unset($P[$k]);
				$meta[$radkey] = $v;
			}
		}
		/*
		if (isset($P['handle'])) {
			$meta['handle']	= $P['handle'];
			unset($P['handle']);
		} else {
			$meta['handle']	= uniqid();
		}
		*/
		
		if (isset($meta['werdz'])) {
			$meta['werdz'] = array_values($meta['werdz']);
		}
		
		if (isset($pathbits[2])) {
			//	this is an update
			$cid	 	= new MongoId($pathbits[2]);
			$obj		= array(
				'_id'		=> $cid,
				'content'	=> $P,
				'meta'		=> $meta
			);
			try {
				$upd = $this->mong()->selectCollection(CHUNKZ_COLLECTION_NAME)->save($obj,$opts);
				$this->addToResponse(array('msg' => 'your chunk has been updated.'));
			} catch (Exception $e) {
				$this->setStatus(401);
				$this->addToResponse(array('msg' => $e->getMessage()));			
			}
		} else {
			//	this is in an insert
			$obj = array(
				'meta'		=> $meta,
				'content'	=> $P
			);
			try {
				$ins = $this->mong()->selectCollection(CHUNKZ_COLLECTION_NAME)->save($obj,$opts);
				$this->addToResponse(array('msg' => 'your chunk has been created.'));
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
		$this->mong()->selectCollection(CHUNKZ_COLLECTION_NAME)->remove($c);
		$r = array('msg' => 'The chunk has been removed');
		$this->addToResponse($r);
	} else {
		$this->setStatus(401);
	}
	break;

}

?>