<?php
$options = array(
    'hide-comments' => true,
    'tidy-mark' => false,
    'indent' => true,
    'indent-spaces' => 4,
    'new-blocklevel-tags' => 'article,header,footer,section,nav',
    'new-inline-tags' => 'video,audio,canvas,ruby,rt,rp',
    'doctype' => '<!DOCTYPE HTML>',
    'sort-attributes' => 'alpha',
    'vertical-space' => false,
    'output-xhtml' => true,
    'wrap' => 180,
    'wrap-attributes' => false,
    'break-before-br' => false,
    'indent-cdata' => true,
    'escape-cdata' => true
);
$tidy = new tidy;
$tidy->parseString(file_get_contents($_SERVER['HTTP_REFERER']), $options, 'utf8');
$tidy->cleanRepair();
$html = str_replace('<html lang="en" xmlns="http://www.w3.org/1999/xhtml">', '<!DOCTYPE HTML>', $tidy);
$html = preg_replace('/(\s*)\/[\*\/]\<\!\[CDATA\[(\*\/)?(\s*)/',"\n" . '        ',$html);
$html = preg_replace('/(\s*)\/[\/\*](\]){2}\>(\*\/)?(\s*)/',	"\n" . '        ',$html);
$html = str_replace(">\n</script>","></script>",$html);

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

echo '<pre class="prettyprint lang-html">';
echo htmlentities($html);
echo '</pre>';

include 'simple_footer.php';
?>