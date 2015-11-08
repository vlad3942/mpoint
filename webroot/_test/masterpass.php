<?php
define("sAPI_CLASS_PATH", "/apps/php/php5api/classes/");

require_once(sAPI_CLASS_PATH ."template.php");
require_once(sAPI_CLASS_PATH ."http_client.php");
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");
/**
 * Connection info for sending error reports to a remote host
*/
$aHTTP_CONN_INFO["mesb"]["protocol"] = "http";
//$aHTTP_CONN_INFO["mesb"]["host"] = "10.150.242.42";
$aHTTP_CONN_INFO["mesb"]["host"] = $_SERVER['HTTP_HOST'];
$aHTTP_CONN_INFO["mesb"]["port"] = 80; // mPoint
//$aHTTP_CONN_INFO["mesb"]["port"] = 9000; // MESB
$aHTTP_CONN_INFO["mesb"]["timeout"] = 120;
$aHTTP_CONN_INFO["mesb"]["path"] = "/mApp/api/pay.php";
$aHTTP_CONN_INFO["mesb"]["method"] = "POST";
$aHTTP_CONN_INFO["mesb"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["mesb"]["username"] = "IBE";
$aHTTP_CONN_INFO["mesb"]["password"] = "kjsg5Ahf_1";

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
	<pay client-id="10007" account = "100007">
		<transaction id="1810883" store-card="false">
			<card type-id="23">
				<amount country-id="200">200</amount>
			</card>
		</transaction>
		<client-info platform="iOS" version="5.1.1" language="da">
			<mobile country-id="100" operator-id="10000">288828610</mobile>
			<email>jona@oismail.com</email>
			<device-id>23lkhfgjh24qsdfkjh</device-id>
		</client-info>
	</pay>
</root>';

try
{
	$obj_Client = new HTTPClient(new Template(), $obj_ConnInfo);
	$obj_Client->connect();
	$code = $obj_Client->send($h, $b);
	$obj_Client->disconnect();
	if ($code == 200 && strlen($obj_Client->getReplyBody() ) > 0)
	{
		$obj_XML = simplexml_load_string($obj_Client->getReplyBody() );
	}
	else
	{
		header("Content-Type: text/plain");
		var_dump($obj_Client);
		die();
	}
	
	$sHead = trim($obj_XML->{'psp-info'}->head);	
	?>
	<html>
	<head>
	<!-- Master Pass JavaScript function -->
	<?= $sHead; ?>
	</head>
	<body>
	<!-- Master Pass button img tag -->
	<?= $obj_XML->{'psp-info'}->body; ?>	
	</body>
	</html>
<?php
}
catch (Exception $e)
{
	header("Content-Type: text/plain");
	var_dump($e);
	var_dump($obj_Client);
	die();
}
?>