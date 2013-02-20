<?php
if ($_SERVER['SCRIPT_NAME'] == '/vars.php') exit();

//	paths + URLs
define('PATH_FRAMEWORK',	'/var/www/frameworks/gourd07/');
define('PATH_INCLUDES',		'/var/www/domains/api/inc/');

//	database
define("SQL_DB",			"gourd");
define('SQL_HOST',			'hefty01');
define("SQL_USER",			'canondbusr');
define("SQL_PASSWD",		'B0BNwP@oa4"juF');

define('MONGODB_SERVER',	'hefty01');
define('MONGODB_DB',		'gourd01');
define('MONGODB_PORT',		27017);
define('MONGODB_USER',		NULL);
define('MONGODB_PASSWD',	NULL);

set_include_path(get_include_path() . PATH_SEPARATOR . PATH_INCLUDES . PATH_SEPARATOR . PATH_FRAMEWORK);

require_once 'functions.global.php';
?>