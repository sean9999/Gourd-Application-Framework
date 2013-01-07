<?php
function arrayToQuotedList($arr,$quot='"') {
	//	this could be improved by making it able to specify quote character and deliminator
	$o = '';
	$count = 2;
	foreach ($arr as $r) {
		$o .= $quot . trim($r) . $quot;
		if (sizeof($arr) >= $count) $o .= ', ';
		$count++;
	}
	return $o;
}
?>