<?php
function file_force_contents($dir, $contents){
	//	create a file and any directories specified if they don't exist
	//	http://ca3.php.net/manual/en/function.file-put-contents.php#84180
    $parts	= explode('/', $dir);
    $file	= array_pop($parts);
    $dir	= '';
    foreach($parts as $part) if(!is_dir($dir .= "/$part")) mkdir($dir);
	return file_put_contents("$dir/$file", $contents);
}
?>