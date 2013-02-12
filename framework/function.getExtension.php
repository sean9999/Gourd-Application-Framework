<?php
function getExtension($path) {
	$filebits = explode('.',$path);
	$r = array_pop($filebits);
	return $r;
}

function getMimeTypeFromExtenstion($ext) {
	
	$r = 'text/plain';

	switch (strtolower($ext)) {

		case 'css':
		case 'txt':
		case 'xml':
		case 'html':
		$r = 'text/' . strtolower($ext);
		break;
		
		case 'js':
		$r = 'application/x-javascript';
		break;
		
		case 'jpg':
		case 'jpeg':
		case 'png':
		case 'tif':
		case 'tiff':
		case 'gif':
		//$r = 'image/' . strtolower($ext);
		$r = 'Image';
		break;
			
		// WEB FONTS
		case 'ttf':
		$r = 'application/x-font-ttf';
		break;
	
		case 'woff':
		//$r = 'application/x-font-woff';
		$r = 'Font';
		break;
	
		case 'eot':
		$r = 'application/vnd.ms-fontobject';
		break;		
	
	}
	
	return $r;

}

?>