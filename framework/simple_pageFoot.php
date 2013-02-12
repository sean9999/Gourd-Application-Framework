<?php
$body_content = ob_get_contents();
ob_end_clean();
ob_start();
?>
	</body>
</html>

<?php
$footer_content = ob_get_contents();
ob_end_clean();
$html = $core->page()->head()->spill() . $header_content . $body_content . $footer_content;
if (!empty($tas)) {
	$html = unta($tas,$html);
}
echo $html;
?>