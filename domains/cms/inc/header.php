<?php
	ob_start();
	load_header();
	$header->setDocType('html5');
	
	//	theme
	if (isset($_COOKIE['theme'])) {
		$header->addtheme($_COOKIE['theme']);
	} else {
		$header->addtheme('gourd-fett');
	}
	//$header->addcss('/lib/jquery-ui/css/gourd-fett/jquery-ui.css');
	$header->jqueryui_theme_location = '/lib/jquery-ui/css/gourd-fett/jquery-ui.css';
	
	//$header->jqueryui_theme_location = 'http://code.jquery.com/ui/1.9.0/themes/base/jquery-ui.css';
	
	$header->jquery_location = 'http://code.jquery.com/jquery-1.7.1.min.js';
	
	$header->jqueryui_location = 'http://code.jquery.com/ui/1.9.0/jquery-ui.js';
	
	$header->addjqueryfile('/lib/nav/jquery.nav.js');
	$header->load_jquery();
	$header->load_jqueryui();
	$header->addjquery(' $("button, .button").button();');
	$header->addjquery('$( ".radioGroup" ).buttonset();');
	$header->addjquery('$( ".radioGroup2" ).buttonset();');
	$header->addjs('/lib/selectmenu/jquery.ui.selectmenu.js');
	$header->addjquery('$("select.select").selectmenu();');
	$header->addjquery(' $( "#tabs" ).tabs(); ');
	if (isset($page_title)) {
		$header->title = $page_title .' &bull; SJC CMS &bull;';
	} else {
		$header->title = 'SJC CMS';
	}
	if (isset($body_id)) {
		$header->body_id = $body_id;
	}
	//	jqui modal
	$header->addjquery('$(".dialog").dialog({ autoOpen: false })');
	$header->load_jqueryui()->addjquery('
		$("#dialog").dialog({
			autoOpen: 	false,
			title: 		"Module Not Available",
			modal: 		true,
			height: 	450,
			width: 		450,
			buttons: {
				Ok: function() {
					$( this ).dialog( "close" );
				}
			}
		});
		$(".modal-not-yet, .modal").click(function() {
			var url	= $(this).attr("href");
				t	= $(this).attr("title");
			$("#dialog").load(url, function() {
				if (t) $("#dialog").dialog({title: t});
				$("#dialog").dialog("open");
			});
			return false;
		});
	');
?>
<div id="wrapper">
	<header>
		<div id="box">
			<div id="logo">
				<p><a href="/">SJC CMS</a></p>
			</div>
			<?php 
			if (isset($S['User'])) include 'widget_navMain.php'; 
			?>
			<div id="accountLink">
				<?php
				include 'widget_navAccount.php';
				?>
			</div>
			
			<div class="clearer"></div>
		</div>
		<div class="clearer"></div>
	</header>
	
	<div id="msgs"></div>
	
	<div id="container">
		<div id="torso">

<?php
	$header_content = ob_get_contents();
	ob_end_clean();
	ob_start();	//	begin body
?>