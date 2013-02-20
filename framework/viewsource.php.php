<?php

$phpsource = file_get_contents( $fyle );

include 'simple_header.php';
$header->addjs('http://core.gourdisgood.com/lib/prettify/prettify.js');
$header->addcss('http://core.gourdisgood.com/lib/prettify/gourd01.css');
$header->body_id = 'gourdprettyprint';
$header->addrawjs('
	window.onload = function() {
		prettyPrint();
		parent.$.colorbox.resize({
			width:	"95%",
			height:	"95%"	
		});
	};
');

echo '<pre class="prettyprint lang-php">';
echo htmlentities($phpsource);
echo '</pre>';

include 'simple_footer.php';
?>