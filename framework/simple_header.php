<?php
ob_start();
load_header();
include_once 'resolvePageVariables.php';
if (isset($_SERVER['ZONE']) && $_SERVER['ZONE'] != 'prod') $page_title = $_SERVER['ZONE'] . ' :: ' . $page_title;
if (empty($header->title))		$header->title 		= $page_title;
if (empty($header->body_id))	$header->body_id	= $section_id;
$header->addmeta('<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />');
$header->addrawcss(".message {font-weight: bold; color: red}");
if (isset($message)) echo '<div class="messagecontainer"><p class="message">'.$message.'</p></div>';
$header_content = ob_get_contents();
ob_end_clean();
ob_start();	//	begin body
?>
