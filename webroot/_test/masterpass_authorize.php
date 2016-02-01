<?php
define("sAPI_CLASS_PATH", "/apps/php/php5api/classes/");

require_once(sAPI_CLASS_PATH ."template.php");
require_once(sAPI_CLASS_PATH ."http_client.php");
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");

if(count($_POST) > 0 ) 
{	
	/**
	 * Connection info for sending error reports to a remote host
	*/
	$aHTTP_CONN_INFO["mesb"]["protocol"] = "http";
	//$aHTTP_CONN_INFO["mesb"]["host"] = "10.150.242.42";
	$aHTTP_CONN_INFO["mesb"]["host"] = $_SERVER['HTTP_HOST'];
	$aHTTP_CONN_INFO["mesb"]["port"] = 80; // mPoint
	//$aHTTP_CONN_INFO["mesb"]["port"] = 9000; // MESB
	$aHTTP_CONN_INFO["mesb"]["timeout"] = 120;
	$aHTTP_CONN_INFO["mesb"]["path"] = "/mApp/api/authorize.php";
	$aHTTP_CONN_INFO["mesb"]["method"] = "POST";
	$aHTTP_CONN_INFO["mesb"]["contenttype"] = "text/xml";
	$aHTTP_CONN_INFO["mesb"]["username"] = $_POST['client-username'];
	$aHTTP_CONN_INFO["mesb"]["password"] = $_POST['client-password'];
	
	$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["mesb"]);
	
	$h = "{METHOD} {PATH} HTTP/1.0" .HTTPClient::CRLF;
	$h .= "host: {HOST}" .HTTPClient::CRLF;
	$h .= "referer: {REFERER}" .HTTPClient::CRLF;
	$h .= "content-length: {CONTENTLENGTH}" .HTTPClient::CRLF;
	$h .= "content-type: {CONTENTTYPE}" .HTTPClient::CRLF;
	$h .= "user-agent: mPoint" .HTTPClient::CRLF;
	$h .= "Authorization: Basic ". base64_encode($aHTTP_CONN_INFO["mesb"]["username"] .":". $aHTTP_CONN_INFO["mesb"]["password"]) .HTTPClient::CRLF;
	
	$b = '<?xml version="1.0" encoding="UTF-8"?>
	<root>
		<authorize-payment client-id="'.$_POST['client-id'].'" account="'.$_POST['account-id'].'">
			<transaction id="'.$_POST['transaction-id'].'">
				<card type-id="23">
					<amount country-id="'.$_POST['country-id'].'">'.$_POST['amount'].'</amount>
					<token>'.$_POST['token'].'</token>
					<verifier>'.$_POST['verifier'].'</verifier>
					<checkout-url>'.$_POST['checkouturl'].'</checkout-url>
				</card>
			</transaction>			
			<password>oisJona</password>
			<client-info language="us" version="1.28" platform="iOS/8.1">
	            <mobile operator-id="10000" country-id="602">288828610</mobile>
	            <email>jona@oismail.com</email>
	            <device-id>23lkhfgjh24qsdfkjh</device-id>
	        </client-info>
		</authorize-payment>
	</root>';
	
	
	try
	{
		$obj_Client = new HTTPClient(new Template(), $obj_ConnInfo);
		$obj_Client->connect();		
		$code = $obj_Client->send($h, $b);		
		$obj_Client->disconnect();
		if ($code == 200 && strlen($obj_Client->getReplyBody() ) > 0)
		{
			echo '<pre>', htmlentities($obj_Client->getReplyBody()), '</pre>';
			//print_r($obj_Client->getReplyBody());die;
		}
		else
		{
			header("Content-Type: text/plain");
			print_r($obj_Client);
			die();
		}	
	}
	catch (Exception $e)
	{
		header("Content-Type: text/plain");
		var_dump($e);
		var_dump($obj_Client);
		die();
	}
}
?>