<?php

load_function('htmlEncode');

Class LeafNode {
	public $val = '';
	function __construct($v) {
		$this->val = $v;
	}
	function __destruct() {
		return $this->val;
	}
}

function object_to_array($mixed) {
    if(is_object($mixed)) $mixed = (array) $mixed;
    if(is_array($mixed)) {
        $new = array();
        foreach($mixed as $key => $val) {
            $key = preg_replace("/^\\0(.*)\\0/","",$key);
            $new[$key] = object_to_array($val);
        }
    } 
    else $new = $mixed;
    return $new;        
}

function containsObject($iarr) {
	//	returns true if the input array contains an object, resource, or unknown data type
	//	returns false if the input var is simply a scalar
	$r = false;
	if (!is_scalar($iarr)) {
		if (is_object($iarr)) {
			$r = true;
		} elseif (is_resource($iarr)) {
			$r = true;
		} elseif (is_array($iarr)) {
			foreach ($iarr as $row) {
				$r = containsObject($row);
				if ($r) break;
			}
		} else {
			//	we are being cautious. it's not a known data type
			$r = true;
		}
	}
	return $r;
}

function normalizeString($i) {
	$o 			= $i;
	$o			= strip_tags($o);
	$o			= strtolower($o);
	$a 			= array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ'); 
	$b 			= array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
	$o			= htmlspecialchars_decode($o);
	$o 			= str_replace($a, $b, $o);
    $o			= preg_replace('/\W/', ' ', $o);
    return trim($o);
}

function reduceToStrings($obj) {
	$r = array();
	foreach ($obj as $k => $v) {
		if (is_string($v)) {
			$r[] = normalizeString($v);
		} elseif (is_object($v) || is_array($v)) {
			$r = array_merge($r,reduceToStrings($v));
		}
	}
	return $r;
}

function createSearchWords($obj) {
	$stopwords_english = 'a,able,about,across,after,all,almost,also,am,among,an,and,any,are,as,at,be,because,been,but,by,can,cannot,could,dear,did,do,does,either,else,ever,every,for,from,get,got,had,has,have,he,her,hers,him,his,how,however,i,if,in,into,is,it,its,just,least,let,like,likely,may,me,might,most,must,my,neither,no,nor,not,of,off,often,on,only,or,other,our,own,rather,said,say,says,she,should,since,so,some,than,that,the,their,them,then,there,these,they,this,tis,to,too,twas,us,wants,was,we,were,what,when,where,which,while,who,whom,why,will,with,would,yet,you,your';
	$strings = reduceToStrings($obj);
	$allwords= array();
	foreach ($strings as $string) {
		$allwords = array_merge($allwords,explode(' ',$string));
	}
	$allwords = array_filter($allwords);
	$goodwords = array_diff($allwords,explode(',',$stopwords_english));
	$r = array_values($goodwords);
	return $r;	
}

function MergeArrays($Arr1, $Arr2) {
	foreach($Arr2 as $key => $Value) {
		if(array_key_exists($key, $Arr1) && is_array($Value)) {
			$Arr1[$key] = MergeArrays($Arr1[$key], $Arr2[$key]);
		} else {
			$Arr1[$key] = $Value;
		}
	}
	return $Arr1;
}

Class GourdDataObject {

	public $requiredfields	= array();	//	save() borks if these fields not present
	public $defaultfields	= array();	//	on "new" view, these are shown
	public $types			= array();	//	coerce fields matching keys, to types matching values (ie: {"price":"float"}) 
	public $fields			= array();	//	on hydrate(), this gets populated with actual data
	public $forcefields		= array();	//	on save(), these fields and accompanying values are definitely saved
	public $alwaysfields	= array();	//	on save(), if these fields not present, they are added
	public $neverfields		= array();	//	on cleanse(), remove fields matching these keys
	public $dbg				= array();
	
	public function cleanse() {
		//	omit illegal fields
		$goodfields = array();
		foreach ($this->fields as $k => $v) {
			$ok2go = true;
			if (strpos($k,'$') === 0) {
				$ok2go = false;
			}
			if (in_array($k,$this->neverfields)) {
				$ok2go = false;
			}
			if ($ok2go) $goodfields[$k] = $v;
		}
		$this->fields = $goodfields;
		return $this;
	}
	
	public function push($i) {
		$this->fields = array_merge($this->fields,(array) $i);
		return $this;
	}
	
	public function fixObj($i) {
		$o = (array) $i;
		$o = array_merge($this->defaultfields,$o);
		foreach ($this->removefields as $x) {
			unset($o[$x]);
		}
		return (object) $o;
	}	
	
	public function __set($name,$val) {
		if (in_array($name,array(
			'fields',
			'types',
			'dbg',
			'defaultfields',
			'requiredfields',
			'alwaysfields',
			'forcefields',
			'neverfields'))) {
			$this->$name = (array) $val;
		} else {
			$this->$name = $val;
		}
	}

	public function upgrade() {
		//	classes that extend this class should implement this method
		//	we'll just pass through
		return $this;
	}
	
	public function prepare() {
		//	prepare for "new" view
		$this->defaultfields = MergeArrays($this->defaultfields,$this->forcefields);
		return $this;
	}
	
	public function hydrate($obj) {
		//	original data
		$this->push(object_to_array($obj));
		//	always fields
		if (!empty($this->alwaysfields)) {
			if (!function_exists('doNodes')) {
				function doNodes($i) {
					$o = array();
					foreach ($i as $k => $v) {
						if ($v instanceof LeafNode) {
							$o[$k] = $v->val;
						} elseif (is_scalar($v)) {
							$o[$k] = $v;
						} else {
							$o[$k] = doNodes($v);
						}
					}
					return $o;
				}
			}
			$alwaysfields = doNodes($this->alwaysfields);
			$this->fields = MergeArrays($alwaysfields,$this->fields);
		}
		$this->upgrade();
		//	force fields
		$this->fields = MergeArrays($this->fields,$this->forcefields);
		return $this->save();
	}
	
	public function validate() {
		$existingkeys	= array_keys($this->fields);
		$requiredfields	= $this->requiredfields;
		$diff			= array_diff($requiredfields,$existingkeys);
		if (sizeof($diff)) {
			throw new Exception('These required fields were not included: ' . json_encode($diff));
			return false;
		}
		return true;
	}
	
	public function getFormRowStructure2($group,$key,$lang=NULL,$new=false) {
		$flatkey = $group . '[' . $key . ']';
		$o = array(
			'type' 		=> 'text',
			'attributes'=> array('type' => 'text', 'name' => $group . '[' .$key . ']'),
			'label' 	=> $key,
			'more' 		=> ''
		);
		if ($lang) {
			$o['attributes']['name'] = $o['attributes']['name'] . '['.$lang.']';
		}
		switch ($group) {
			case 'is':
			$o['type']	= 'boolean';
			break;
			case 'axis':
			$o['type']	= 'number';
			break;
		}
		if (!empty($this->formfields)) {
			if (isset($this->formfields[$flatkey])) {
				$o = MergeArrays($o,$this->formfields[$flatkey]);
			}
		}
		if ($new) {
			if (isset($this->defaultfields[$group][$key])) {
				if ($lang) {
					$o['attributes']['value'] = $this->defaultfields[$group][$key]->val[$lang];
				} else {
					$o['attributes']['value'] = $this->defaultfields[$group][$key]->val;
				}
			} else {
				$o['attributes']['value'] = '';
			}
		} else {
			if (isset($this->fields[$group][$key])) {
				if ($lang) {
					$o['attributes']['value'] = $this->fields[$group][$key][$lang];
				} else {
					$o['attributes']['value'] = $this->fields[$group][$key];
				}
			} else {
				if (isset($this->alwaysfields[$group][$key])) {
					if ($lang) {
						$o['attributes']['value'] = $this->alwaysfields[$group][$key][$lang];
					} else {
						$o['attributes']['value'] = $this->alwaysfields[$group][$key];
					}
				} else {
					$o['attributes']['value'] = '';
				}
			}
		}
		return $o;
	}
	
	public function getFormRowStructure($key,$new=false) {
		$o = array(
			'type' 		=> 'text',
			'attributes'=> array('type' => 'text', 'name' => $key),
			'label' 	=> $key,
			'more' 		=> ''
		);
		if (isset($this->formfields)) {
			if (isset($this->formfields[$key])) {
				$o = MergeArrays($o,$this->formfields[$key]);
			}
		}
		if ($new) {
			if (isset($this->defaultfields[$key])) {
				$o['attributes']['value'] = $this->defaultfields[$key];
			} else {
				$o['attributes']['value'] = '';
			}
		} else {
			if (isset($this->fields[$key])) {
				$o['attributes']['value'] = $this->fields[$key];
			} else {
				if (isset($this->defaultfields[$key])) {
					$o['attributes']['value'] = $this->defaultfields[$key];
				} else {
					$o['attributes']['value'] = '';
				}				
			}
		}
		return $o;
	}
	
	public function formRowStructureToHTML($a) {
		$o = '';
		
		switch ($a['type']) {
			
			case 'boolean':
			if ($a['attributes']['value']) {
				$e1 = ' checked="checked"';
				$e2 = '';
			} else {
				$e1 = '';
				$e2 = ' checked="checked"';			
			}
			$o .= '<td class="label">'.$a['label'].'</td>
				<td class="input">
				<div class="radioGroup">
				<input type="radio" name="'.$a['attributes']['name'].'" value="1" id="'.$a['attributes']['name'].'_1"'.$e1.' /><label for="'.$a['attributes']['name'].'_1">yes</label>
				<input type="radio" name="'.$a['attributes']['name'].'" value="0" id="'.$a['attributes']['name'].'_0"'.$e2.' /><label for="'.$a['attributes']['name'].'_0">no</label>
				</div>
				</td>
				<td class="more">'.$a['more'].'</td>';
			break;
		
			case 'hidden':
			$o .= '<input type="hidden" name="'.$a['attributes']['name'].'" value="'.$a['attributes']['value'].'" />';
			break;			
		
			case 'textarea':
			if (isset($a['attributes']['type'])) {
				unset($a['attributes']['type']);
			}
			$vvv = $a['attributes']['value'];
			unset($a['attributes']['value']);
			$o .= '<td class="label">'.$a['label'].'</td>
			<td class="input"><textarea';
			foreach ($a['attributes'] as $attr_name => $attr_val) {
				$o .= ' ' . $attr_name . '="' . $attr_val . '"';
			}
			$o .= '>';
			$o .= htmlEncode($vvv);
			$o .= '</textarea></td>
			<td class="more">'.$a['more'].'</td>';
			break;			
		
			case 'richtext':
			if (isset($a['attributes']['type'])) {
				unset($a['attributes']['type']);
			}			
			$o .= '<td class="label">'.$a['label'].'</td>
			<td class="input"><textarea name="'.$a['attributes']['name'].'" rows="45" class="tinymce">'.$a['attributes']['value'].'</textarea></td>
			<td class="more">'.$a['more'].'</td>';
			break;
			
			case 'imagemanager':
			$a['attributes']['type'] = 'url';
			if (!isset($a['attributes']['id'])) {
				$a['attributes']['id'] = uniqid();
			}
			if (empty($a['more'])) {
				$a['more'] = '[ select file ]';
			}
			$o .= '<td class="label">'.$a['label'].'</td>
			<td class="input"><input';
			foreach ($a['attributes'] as $attr_name => $attr_val) {
				$o .= ' ' . $attr_name . '="' . $attr_val . '"';
			}	
			$o .=' /></td>
			<td class="more"><a href="#" class="imagemanager">'.$a['more'].'</a></td>';
			break;
		
			case 'tags':
			$o .= '<td class="label">'.$a['label'].'</td>
			<td class="input"><ul id="'.$a['attributes']['name'].'" class="tags">';
			foreach ((array) $a['attributes']['value'] as $t) {
				$o .= '<li>' . $t . '</li>';
			}
			$o .= '</ul></td>
			<td class="more">'.$a['more'].'</td>';
			break;			
			
			default:
			//	(text) and field-types that behave like text (like email, url, tel, etc)
			if (!isset($a['attributes']['type'])) {
				$a['attributes']['type'] = $a['type'];
			}
			$o .= '<td class="label">' . $a['label'] . '</td>';
			$o .= '<td class="input">';
			$o .= '<input';
			foreach ($a['attributes'] as $attr_name => $attr_val) {
				//$o .= ' ' . $attr_name . '="' . htmlEncode($attr_val) . '"';
				$o .= ' ' . $attr_name . '="' . $attr_val . '"';
			}
			$o .= ' />';
			$o .= '<td class="more">' . $a['more'] . '</td>';
		}
		return $o;
	}
	
	public function createSearchWords() {
		$this->searchwords = createSearchWords($this->fields);
		return $this;
	}	
	
	public function displayFormFields($new=false) {
		
		$o = array();
		if ($new) {
			$fff = $this->defaultfields;
		} else {
			$this->save();
			$fff = $this->fields;
		}
		foreach ($fff as $k => $v) {
			$thistype = 'text';
			$ff = $this->getFormRowStructure($k,$new);
			$o[] = $this->formRowStructureToHTML($ff);
		}
		return '<tr>' . implode('</tr><tr>',$o) . '</tr>';
	}	
	
	protected function fixTypes($arr,$types) {
		foreach ($types as $k => $t) {
			if (!is_scalar($t)) {
				$arr[$k] = $this->fixTypes($arr[$k],$types[$k]);
			} else {
				if (isset($arr[$k])) {
					settype($arr[$k],$t);
				}
			}
		}
		return $arr;
	}	
	
	public function save() {
		if ($this->validate()) {
			$this->cleanse();
			if (!empty($this->types)) {
				$this->fields = $this->fixTypes($this->fields,$this->types);
			}
			return $this->fields;
		} else {
			return 'Validation failed.';
		}
	}
	
	/*
	public function __sleep() {
		if ($this->validate()) {
			$this->cleanse();
			if (!empty($this->types)) {
				$this->fields = $this->fixTypes($this->fields,$this->types);
			}
			return (array) $this->fields;
		} else {
			$r = 'Validation failed.';
		}
	}
	*/
	
}
?>