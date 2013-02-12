<?php
$body_content = ob_get_contents();
ob_end_clean();
ob_start();	// footer
?>	
	</body>
</html>

<?php

$footer_content = ob_get_contents();
ob_end_clean();
$html = $header->spill() . $header_content . $body_content . $footer_content;

if (!empty($tas)) {
	$html = unta($tas,$html);
}

echo $html;
?>