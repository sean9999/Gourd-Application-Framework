<?php
require_once 'AWSSDKforPHP/sdk.class.php';
function gourdMail($from,$to,$subject,$body) {
	$ses = new AmazonSES();
    $message = array(
        'Subject.Data' 		=> $subject,
        'Body.Html.Data' 	=> $body,
        'Body.Text.Data'	=> strip_tags($body)
    );
	$r = $ses->send_email($from, array('ToAddresses' => array($to)), $message);
	spl_autoload_register('__autoload');
	return $r;
}
?>