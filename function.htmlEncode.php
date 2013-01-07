<?php
function htmlEncode($var) {
	return htmlentities($var, ENT_QUOTES, 'UTF-8') ;
}
?>