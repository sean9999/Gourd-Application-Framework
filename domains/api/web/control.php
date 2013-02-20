<?php
require_once 'vars.php';

error_reporting(-1);

switch ($_SERVER['HTTP_HOST']) {

	case 'api.canon.snappysmurf.ca':
	case 'api.canonlenses.ca':
	$gr = new CanonRestServer($_GET,$_POST);
	break;

	default:
	$gr = new RestServer($_GET,$_POST);	

}

// trap errors and include them in the JSON response
function userErrorHandler($errno, $errmsg, $filename, $linenum, $vars) {
	global $gr;
	// timestamp for the error entry
	$dt = date("Y-m-d H:i:s (T)");
	
	// define an assoc array of error string
	// in reality the only entries we should
	// consider are E_WARNING, E_NOTICE, E_USER_ERROR,
	// E_USER_WARNING and E_USER_NOTICE
	$errortype = array (
		E_ERROR              => 'Error',
		E_WARNING            => 'Warning',
		E_PARSE              => 'Parsing Error',
		E_NOTICE             => 'Notice',
		E_CORE_ERROR         => 'Core Error',
		E_CORE_WARNING       => 'Core Warning',
		E_COMPILE_ERROR      => 'Compile Error',
		E_COMPILE_WARNING    => 'Compile Warning',
		E_USER_ERROR         => 'User Error',
		E_USER_WARNING       => 'User Warning',
		E_USER_NOTICE        => 'User Notice',
		E_STRICT             => 'Runtime Notice',
		E_RECOVERABLE_ERROR  => 'Catchable Fatal Error'
	);
	// set of errors for which a var trace will be saved
	$user_errors = array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE);
	$e = array(
		'ztamp'		=> $dt,
		'errno'		=> $errno,
		'msg'		=> $errmsg,
		'file'		=> $filename,
		'line'		=> $linenum
	);
	if (in_array($errno, $user_errors)) {
	    $e['vars'] = $vars;
	}	
	$gr->pusherror($e,$errortype[$errno]);
}

$old_error_handler = set_error_handler("userErrorHandler");

$gr->spit();

?>