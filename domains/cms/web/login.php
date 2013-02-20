<?php
$header->addrawcss("
	div#msgs {
		position:	absolute;
		right:		0.5em;
		top:		26px;
		left: 		-80px;
		width:		100%;
}
");
$header->addjquery("
	var content1	= $('#login').html(),
		content2	= $('#frm_forgotpass').html();
	$('body').delegate('a#lnk_login','click',function() {			
		$('#login').html(content1);
		return false;
	});
	$('body').delegate('a#lnk_forgotpass','click',function() {			
		$('#login').html(content2);
		return false;
	});	
");	

$header->body_id = 'log';
?>

<div id="login" class="content hidden">

	<h1>Please Log In</h1>
	<p>You can request credentials from a CMS administrator.</p>

	<?php
	//	or can't we just use getAddress() ?
	if ($ModuleHandle == 'dashboard')				$goto_url = '/';
	elseif (isset($view) && $view != 'dashboard') 	$goto_url = '/' . $ModuleHandle . '/' . $view;
	else											$goto_url = '/' . $ModuleHandle;		
	?>
	
	<form name="login" action="?action=login" method="post" onsubmit="console.log(this); return true">
		<p><input type="text" name="usr" placeholder="Username" autofocus="autofocus" /></p>
		<p><input type="password" value="" name="passwd" /></p>
		<p><input type="checkbox" name="persist" id="check"/><label for="check">Remember Me Next Time</label></p>		
		<p class="right"><button type="submit">Submit</button></p>
	</form>
	<p class="passForgot">Silly me, I've gone forgot my password! <a href="#" id="lnk_forgotpass">Help!</a></p>		

</div>

<div class="content hidden" id="frm_forgotpass">
	
	<h1>I lost my keys!</h1>
	<p>This form will reset your password and then send it to you. Please type in your email</p>
	<form name="forgotpass" action="?action=resetpasswd" method="post">
		<p><input type="email" placeholder="someone@example.com" autofocus="autofocus" /></p>
		<p class="right"><button type="submit">do it</button></p>
	</form>
	<p class="passForgot"><a href="javascript:show_login()" id="lnk_login">back to login</a></p>
	</div>

</div>