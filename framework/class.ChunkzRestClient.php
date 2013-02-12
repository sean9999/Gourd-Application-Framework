<?php
function unfurl($obj) {
	$hash = $obj->content;
	foreach ($hash as $k => $v) {
		if (isset($v)) {
			$id = uniqid();
			if (is_array($v)) {
				$r[$id]['k'] = $k;
				$r[$id]['t'] = 'array';
				$r[$id]['v'] = array();
				foreach ($v as $tag) {
					$r[$id]['v'][] = $tag;
				}
			} elseif (is_object($v)) {
				$r[$id]['k'] = $k;
				$r[$id]['t'] = 'array';
				$r[$id]['v'] = array();
				foreach ($v as $kk => $vv) {
					$r[$id]['v'][$kk] = $vv;
				}
			} else {
				$t	= gettype($v);
				$r[$id]['k'] = $k;
				$r[$id]['v'] = (string) $v;
				$r[$id]['t'] = $t;		
			}		
		}
	}
	$meta = (array) $obj->meta;
	if (isset($meta['handle'])) $r['meta'] = array(
		'k'	=> 'meta:handle',
		'v' => $meta['handle'],
		't'	=> 'string'
	);
	return $r;
}

Class ChunkzRestClient extends RestClient {

	protected $hdrs			= array('User-Agent' => 'Gourd REST Client v.11');
	protected $path			= 'ping';
	protected $method		= 'GET';
	protected $queryparams	= array();
	public $postfields	= array();
	protected $endpoint		= 'http://api.canon.snappysmurf.ca';	// Added by Aleks 2012-11-16
//	protected $endpoint		= 'http://sjc.api.crazyhorsecoding.net';
	protected $auth			= array();
	protected $key			= '';
	private   $secret		= '';

	public function furl() {
		//	convert an Entity Relationship Map to a structure acceptable to chunkz storage
		$erm	= $this->postfields;
		$r		= array();
		unset($erm['meta:handle']);
		foreach ($erm as $row) {
			unset($v);
			unset($t);
			unset($k);
			if (isset($row['v']) && isset($row['k']) && isset($row['t'])) {	
				extract($row);
				if (isset($v)) {
					//	da fucking stupid hack
					if ($k == 'Tags_en' || $k == 'Tags_fr' || $k == 'TechTags_en' || $k == 'TechTags_fr') {
						$thislang = substr($k,-2,2);
						$thisk = substr($k,0,-3);
						$r[$thisk][$thislang] = array_values($v);
					} else {
						$r[$k] = $v;
						settype($r[$k],$t);	
					}
				}
			} elseif (isset($row['k']) && isset($row['t'])) {
				//	prolly empty tags. do nothing
			} else {
				//	assume we got a multi-dimensional array
				$keyname = key($row);
				foreach ($row as $subrow) {
					$subelement = array();
					foreach ($subrow as $subobj) {
						foreach ($subobj as $subkey => $suberm) {
							$subelement[$subkey] = $suberm['v'];
							settype($subelement[$subkey],$suberm['t']);
						}
						$r[$keyname][] = $subelement;
					}
				}
			}
		}
		$this->postfields = $r;
		return $this;
	}
	public function dbg5() {
		return $this->postfields;
	}
}
?>