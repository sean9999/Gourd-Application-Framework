<?php
function IsAssoc($array) {
    return (is_array($array) && (count($array)==0 || 0 !== count(array_diff_key($array, array_keys(array_keys($array))) )));
}

function isSimpleArray($a) {
	//	if the array is simply a 1-d array of scalars, then true
	$r		= false;
	if (is_array($a)) {
		$scals	= array_map('is_scalar',array_values(array_filter($a)));
		if (!in_array(false,$scals)) {
			$r	= true; 
		}	
	}
	return $r;
}
 
?>