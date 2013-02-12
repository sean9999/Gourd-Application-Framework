<?php
ob_start();
load_core();
include_once 'resolvePageVariables.php';
if (isset($_SERVER['ZONE']) && $_SERVER['ZONE'] != 'prod') $page_title = $_SERVER['ZONE'] . ' :: ' . $page_title;
if (empty($core->page()->head()->title)) {
	$core->page()->head()->title = $page_title;
}
if (empty($header->body_id)) {
	$core->page()->head()->body_id = $section_id;
}
$core->page()->head()->addmeta('<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />');
$header_content = ob_get_contents();
ob_end_clean();
ob_start();
?>
