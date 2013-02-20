<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/../vars.php' );
include 'simple_header.php';
?>

<h1>Core</h1>

<p>This domain exists to be a common source for static assets (including images, css files, js files, etc). Sites, when attempting to load a particular resource, will first look in their own directories. If the resouce cannot be found, it will look here.</p>

<p>This mechanism is accomplished by sites having two constants set in the site in question:
	<ol>
		<li><code>PATHROOT_CORE</code>, whose value should point to <?= __DIR__ ?></li>
		<li><code>URLROOT_CORE</code>, whose value should be http://<?= $SERVER['HTTP_HOST']?></li>
	</ol>
</p>

<?php
include 'simple_footer.php';
?>
