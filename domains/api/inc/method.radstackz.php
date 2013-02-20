<?php
define('CHUNKZ_COLLECTION_NAME', 'radstackz');
switch ($this->request['verb']) {

	case 'GET':
	$G = $this->request['GET'];
	if ( isset($representation) && $this->userCan('read',$representation)) {
		
		if ( isset($pathbits[2]) && $pathbits[2]) {
			//	get a particular stack
			$c = array('index.StackID' => $pathbits[2]);
			$f = array('_meta' => 0);
			if (isset($G['sort'])) {
				$s = array_merge($s,$G['sort']);
			}
			if (isset($G['fields'])) {
				$f = $G['fields'];
			}
			if (!isset($pathbits[3])) {
				//	get all Planes
				$s = array('index.Plane' => 1);
				$r = array();
				$cursor = $this->mong()->selectCollection(CHUNKZ_COLLECTION_NAME)->find($c,$f)->sort($s);
				while($row = $cursor->getNext()) {
					$r[] = $row;
				}
			} elseif (is_numeric($pathbits[3])) {
				//	get one chunk matching the stackid and plane
				$c['index.Plane'] = (int) $pathbits[3];
				$r = $this->mong()->selectCollection(CHUNKZ_COLLECTION_NAME)->findOne($c,$f);
			} else {
				
				switch ($pathbits[3]) {
					
					case 'lowest':
					$s = array('index.Plane' => 1);
					$curs = $this->mong()->selectCollection(CHUNKZ_COLLECTION_NAME)->find($c,$f)->sort($s)->limit(1);
					$r = $curs->getNext();
					break;
					
					case 'highest':
					$s = array('index.Plane' => -1);
					$curs = $this->mong()->selectCollection(CHUNKZ_COLLECTION_NAME)->find($c,$f)->sort($s)->limit(1);
					$r = $curs->getNext();
					break;
					
					case 'prodiest':
					$curs = $this->mong()->selectCollection(CHUNKZ_COLLECTION_NAME)->find($c,$f);
					$lowestdiff = 9999;
					while ($row = $curs->getNext()) {
						$thisdiff = abs($row['index']['Plane']);
						if ($thisdiff < $lowestdiff) {
							$r = $row;
							$lowestdiff = $thisdiff;
						}
					}
					return $r;
					break;
					
					case 'stage':
					case 'dev':
					$c['index.Plane'] = array('$gte' => 0,'$lte' => 1);
					$s = array('index.Plane' => -1);
					$curs = $this->mong()->selectCollection(CHUNKZ_COLLECTION_NAME)->find($c,$f)->sort($s)->limit(1);
					$r = $curs->getNext();
					break;
					
					case 'prod':
					$c['index.Plane'] = 0;
					$r = $this->mong()->selectCollection(CHUNKZ_COLLECTION_NAME)->findOne($c,$f);
					break;	
				}
			}
		} else {
			//	get all stacks
			$c = array(
				'_meta.ModuleHandle'=> (string) $representation,
				'_meta.ClientID'	=> (int) $this->getClientID()
			);
			$f = array();
			
			/*
			$s = array('index.Plane' => 1);
			if (isset($G['sort'])) {
				$G['sort'] = array_merge($s,$G['sort']);
			} else {
				$G['sort'] = $s;
			}
			*/
			
			if (!isset($G['sort'])) {
				$G['sort'] = array('index.Plane' => 1);
			}
			
			
			if (isset($G['criteria'])) {
				$c = array_merge($G['criteria'],$c);
			}
			if (isset($G['fields'])) {
				$f = $G['fields'];
				//$f[] = 'index';
				$f = array_values(array_filter(array_unique($f)));
			}
			$cursor	= $this->executeMongoQuery(CHUNKZ_COLLECTION_NAME,$c,$f,$G);
			
			$r = array();
			while ($row = $cursor->getNext()) {
				$r[$row['index']['StackID']][] = $row;
			}

		}
		$this->addToResponse($r);
	} else {
		$this->setStatus(401);
		$this->addToResponse(array('msg' => 'Insufficient permissions.'));
		$this->addToResponse(json_encode($this->request));
	}
	break;

	case 'POST':
	$P = $this->request['POST'];
	if ( isset($representation) && $this->userCan('write',$representation) && isset($this->request['POST'])) {
		
		if (isset($pathbits[2])) {
			
			$StackID = $pathbits[2];
			
			//	this is an update
			if (isset($P['action'])) {
			
				switch($P['action']) {
				
					case 'promote':
					$c = array('index.StackID' => $StackID);
					$o = array('multiple' => true,'safe' => true);
					//	pad with tmp values to avoid increment collision
					$curs1 = $this->mong()->selectCollection(CHUNKZ_COLLECTION_NAME)->find(
						$c,
						array('index.Plane')
					)->sort(array('index.Plane' => 1));
					while($row = $curs1->getNext()) {
						$pad = array('$set' => array('index.tmp' => uniqid()));
						$up1 = $this->mong()->selectCollection(CHUNKZ_COLLECTION_NAME)->update(
							array('_id' => $row['_id']),
							$pad,
							$o
						);
					}
					//	now increment
					$u = array('$inc' => array('index.Plane' => -1));
					$upd = $this->mong()->selectCollection(CHUNKZ_COLLECTION_NAME)->update($c,$u,$o);
					//	now remove tmp values
					$kill = $this->mong()->selectCollection(CHUNKZ_COLLECTION_NAME)->update(
						$c,
						array('$unset' => array('index.tmp' => 1)),
						$o
					);
					if ($upd['ok']) {
						$r = array('msg' => 'The stack was promoted');
						$this->addToResponse(array('upd' => $upd));
					} else {
						$r = array('msg' => 'there was a problem. The stack was not promoted');
						$this->addToResponse(array('upd' => $upd));
					}
					break;
					
					case 'demote':
					$c = array('index.StackID' => $StackID);
					$o = array('multiple' => true,'safe' => true);
					//	pad with tmp values to avoid increment collision
					$curs1 = $this->mong()->selectCollection(CHUNKZ_COLLECTION_NAME)->find(
						$c,
						array('index.Plane')
					)->sort(array('index.Plane' => -1));
					while($row = $curs1->getNext()) {
						$pad = array('$set' => array('index.tmp' => uniqid()));
						$up1 = $this->mong()->selectCollection(CHUNKZ_COLLECTION_NAME)->update(
							array('_id' => $row['_id']),
							$pad,
							$o
						);
					}
					$u = array('$inc' => array('index.Plane' => 1));
					//	now decrement
					$upd = $this->mong()->selectCollection(CHUNKZ_COLLECTION_NAME)->update($c,$u,$o);
					//	now remove tmp values
					$kill = $this->mong()->selectCollection(CHUNKZ_COLLECTION_NAME)->update(
						$c,
						array('$unset' => array('index.tmp' => 1)),
						array('multiple' => true)
					);
					if ($upd['ok']) {
						$r = array('msg' => 'The stack was demoted');
						$this->addToResponse(array('upd' => $upd));
					} else {
						$r = array('msg' => 'there was a problem. The stack was not demoted');
						$this->addToResponse(array('upd' => $upd));
					}
					break;
				
					default:
					$this->setStatus(405);
					$r = array('msg' => 'unrecognized action');
				
				}
			} else {
			
				//	upsert
				$c = array(
					'index.StackID'	=> (string) $P['index']['StackID'],
					'index.Plane'	=> (int) $P['index']['Plane']
				);
				//	preserve meta
				$existing = $this->mong()->selectCollection(CHUNKZ_COLLECTION_NAME)->findOne($c,array('_meta'));
				$meta = array(
					'ModuleHandle'	=> (string) $representation,
					'User' 			=> (array) $this->getUser(),
					'ClientID'		=> (int) $this->getClientID(),
					'LastUpdated'	=> new MongoDate()
				);
				if (isset($P['_meta'])) {
					$meta = array_merge($P['_meta'],$meta);
				}
				if (isset($existing['_meta'])) {
					$meta = array_merge($existing['_meta'],$meta);
				}
				$P['_meta'] = $meta;

				//	type coersions and default values
				if (empty($P['index']['StackID'])) {
					$P['index']['StackID'] = uniqid();
				}
				if (empty($P['index']['Plane'])) {
					$P['index']['Plane'] = 1;
				}
				settype($P['index']['Plane'],'int');
				if (!empty($P['is'])) {
					$makebool 	= function($v) { return (boolean) $v; };
					$P['is'] 	= array_map($makebool,$P['is']);	
				}
				if (!isset($P['is']['Active'])) {
					$P['is']['Active'] 	= true;
				}
				if (!isset($P['is']['Featured'])) {
					$P['is']['Featured']= false;
				}
				if (!empty($P['axis'])) {
					$makeint	= function($v) { return (int) $v; };
					$P['axis']	= array_map($makeint,$P['axis']);
				}
				if (!isset($P['axis']['Visibility'])) {
					$P['axis']['Visibility'] = 1;
				}
				if (!isset($P['axis']['Sort'])) {
					$P['axis']['Sort'] = 0;
				}
				
				/*
				$better_tags = array();
				if (isset($P['tags'])) {
					foreach ($P['tags'] as $tag_group => $tags) {
						$better_tags[$tag_group] = array_values($tags);
					}
				}
				$P['tags'] = $better_tags;
				*/
				
				$u = $P;
				$o = array('upsert' => true,'safe' => true);
				$upd = $this->mong()->selectCollection(CHUNKZ_COLLECTION_NAME)->update($c,$u,$o);
				if ($upd['ok']) {
					$r = array('msg' => 'The chunk was saved.');
					$this->addToResponse(array('Plane' => (int) $P['index']['Plane']));
					$this->addToResponse(array('upd' => $upd));
				} else {
					$r = array('msg' => 'There was an error. The chunk was not saved.');
					$this->addToResponse(array('upd' => $upd));
				}
			}
		
		} else {
		
			//	this is an insert
			$meta = array(
				'ModuleHandle'	=> (string) $representation,
				'User' 			=> (array) $this->getUser(),
				'ClientID'		=> (int) $this->getClientID(),
				'LastUpdated'	=> new MongoDate()
			);
			if (isset($P['_meta'])) {
				$meta = array_merge($P['_meta'],$meta);
			}
			$P['_meta'] = $meta;
			
			//	type coersions and default values
			if (empty($P['index']['StackID'])) {
				$P['index']['StackID'] = uniqid();
			}
			if (empty($P['index']['Plane'])) {
				$P['index']['Plane'] = 1;
			}
			settype($P['index']['Plane'],'int');
			if (!empty($P['is'])) {
				$makebool 	= function($v) { return (boolean) $v; };
				$P['is'] 	= array_map($makebool,$P['is']);
			}
			if (!isset($P['is']['Active'])) {
				$P['is']['Active'] 	= true;
			}
			if (!isset($P['is']['Featured'])) {
				$P['is']['Featured']= false;
			}
			if (!empty($P['axis'])) {
				$makeint	= function($v) { return (int) $v; };
				$P['axis']	= array_map($makeint,$P['axis']);
			}
			if (!isset($P['axis']['Visibility'])) {
				$P['axis']['Visibility'] = 1;
			}
			if (!isset($P['axis']['Sort'])) {
				$P['axis']['Sort'] = 0;
			}
			
			/*
			$better_tags = array();
			if (isset($P['tags'])) {
				foreach ($P['tags'] as $tag_group => $tags) {
					$better_tags[$tag_group] = array_values($tags);
				}
			}
			$P['tags'] = $better_tags;
			*/
						
			$ins = $this->mong()->selectCollection(CHUNKZ_COLLECTION_NAME)->save($P,array('safe' => true));
			$r = array('msg' => 'The stack was created.');
			$this->addToResponse($ins);
		}
	} else {
		$this->setStatus(405);
		$r = array('msg2' => 'not enough info to perform operation');
	}
	$this->addToResponse($r);
	break;

	case 'DELETE':
	if ( isset($representation) && $this->userCan('write',$representation) && isset($pathbits[2])) {
		$StackID= (string) $pathbits[2];
		$c		= array(
			'index.StackID' 		=> (string) $pathbits[2],
			'_meta.ClientID'		=> (int) $this->getClientID(),
			'_meta.ModuleHandle'	=> (string) $representation
		);
		$o = array('safe' => true);
		if (isset($pathbits[3])) {
			$thing = 'chunk';
			$Plane = (int) $pathbits[3];
			$c['index.Plane'] = $Plane;
		} else {
			$thing = 'stack';
		}
		$kill = $this->mong()->selectCollection(CHUNKZ_COLLECTION_NAME)->remove($c,$o);
		if ($kill['ok']) {
			$r = array('msg' => 'The '.$thing.' has been removed.');
		} else {
			$r = array('msg' => 'There was an error. The '.$thing.' was not removed');
		}
		$this->addToResponse($kill);
		$this->addToResponse($r);
	} else {
		$this->setStatus(401);
	}
	break;
}
?>