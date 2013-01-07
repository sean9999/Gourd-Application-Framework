<?php      
function autoHyperLink($i) {
	// not written yet. just copy-pasted this code.
   //$i = 'this is a test http://zamov.online.fr asda';
   $o = preg_replace( '/(http|ftp)+(s)?:(\/\/)((\w|\.)+)(\/)?(\S+)?/i', '<a href="\0">\4</a>', $i );  
   return $o;  
}     
     
function Linkify($i) {
	if (strlen($i)) {
		$o = '<a href="http://' . $i . '" target="externalwindow">' . $i . '</a>';
	} else {
		$o = NULL;
	}
	return $o;
}     
     
function niceURL($i) {
// currently, this function simply strips out the "http://"
	$o = $i;
	$o = str_replace('http://','',$o);
	$o = str_replace('https://','',$o);
	return $o;
}

function validURL($i) {
// currently, this function just adds the "http://"
	if (strlen($i)) {
		$o = $i;
		if (substr_count('https://',$o) + substr_count('https://',$o) == 0) $o = 'http://' . $o;
	} else {
		$o = NULL;
	}
	return $o;	
}

function niceTrunc($i,$numwords) {
	// take a large amount of text and truncuate it in a nice way,
	$iarr	= explode(' ',$i);
	$oarr	= array_slice($iarr,0,$numwords);
	$o		= implode(' ',$oarr);
	$o 		= trim($o,',');
	if ($numwords < sizeof($iarr)) {
		$o		.= '...';	
	}
	return $o;
}

function nameToSEOName($i) {
	//	built originally for Du Verre product names
	$o = $i;
	
	//$o str_replace();
	
	return $o;
}

function keephtml($string){
          $res = htmlentities($string);
          $res = str_replace("&lt;","<",$res);
          $res = str_replace("&gt;",">",$res);
          $res = str_replace("&quot;",'"',$res);
          $res = str_replace("&amp;",'&',$res);
          return $res;
}

function quoteify($i) {

	$o = '"' . addslashes($i) . '"';
	return $o;

}

?>