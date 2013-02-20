<?php
/**
 * This class is an External authenticator implementation.
 *
 * @package MCImageManager.Authenticators
 */
class Moxiecode_GourdAuthenticator extends Moxiecode_ManagerPlugin {

	function Moxiecode_GourdAuthenticator() { }

	function onAuthenticate(&$man) {
		$config =& $man->getConfig();
		include '/var/www/frameworks/gourd07/function.imageManager.php';
		$gourdspace1 = normalizeDomainForImageManager($_SERVER['HTTP_HOST']);
		$config['filesystem.rootpath'] 	= '../../content/' . $gourdspace1;
		$config['filesystem.path'] 		= '../../content/' . $gourdspace1;
		return true;
	}

}

// Add plugin to MCManager
$man->registerPlugin("GourdAuthenticator", new Moxiecode_GourdAuthenticator());
?>