<?php
	function isAssoc($array) {
		if (!is_array($array)) {
			return false;
		}
    	$array = array_keys($array);
    	return ($array !== array_keys($array));
	}
?>