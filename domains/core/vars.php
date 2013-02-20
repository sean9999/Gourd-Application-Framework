<?php
if ($_SERVER['SCRIPT_NAME'] == '/vars.php') exit();

define('PATH_FRAMEWORK',		'../../frameworks/Gourd10/');
set_include_path( get_include_path() . PATH_SEPARATOR . PATH_FRAMEWORK );

if (DEBUG == 'On') {	
	error_reporting(E_ALL);
	ini_set("display_errors", 1);
} else {
	error_reporting(0);
	ini_set("display_errors", -1);
}
?>
