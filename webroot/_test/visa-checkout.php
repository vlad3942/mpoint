<?php
define("sAPI_CLASS_PATH", "D:/Development/php5api/classes/");

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
$aHTTP_CONN_INFO["mesb"]["username"] = "CPMDemo";
$aHTTP_CONN_INFO["mesb"]["password"] = "DEMOisNO_2";

$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["mesb"]);

$h = "{METHOD} {PATH} HTTP/1.0" .HTTPClient::CRLF;
$h .= "host: {HOST}" .HTTPClient::CRLF;
$h .= "referer: {REFERER}" .HTTPClient::CRLF;
$h .= "content-length: {CONTENTLENGTH}" .HTTPClient::CRLF;
$h .= "content-type: {CONTENTTYPE}" .HTTPClient::CRLF;
$h .= "user-agent: mPoint" .HTTPClient::CRLF;

$b = '<?xml version="1.0" encoding="UTF-8"?>
<root>
	<pay client-id="10001">
		<transaction id="1002469" store-card="false">
			<card type-id="16">
				<amount country-id="200">200</amount>
			</card>
		</transaction>
		<client-info platform="iOS" version="1.00" language="da">
			<mobile country-id="100" operator-id="10000">28882861</mobile>
			<email>jona@oismail.com</email>
			<device-id>23lkhfgjh24qsdfkjh</device-id>
		</client-info>
	</pay>
</root>';


$obj_Client = new HTTPClient(new Template(), $obj_ConnInfo);
$obj_Client->connect();
$code = $obj_Client->send($h, $b);
if ($code == 200)
{
	$obj_XML = simplexml_load_string($obj_Client->getReplyBody() );
}
else { $obj_XML = null; }
$obj_Client->disconnect();

$sHead = trim($obj_XML->{'psp-info'}->head);
$sHead = str_replace("{PAYMENT SUCCESS}", "document.getElementById('log').innerHTML = 'SUCCESS: '+ JSON.stringify(payment);", $sHead);
$sHead = str_replace("{PAYMENT CANCEL}", "document.getElementById('log').innerHTML = 'CANCEL: '+ JSON.stringify(payment);", $sHead);
$sHead = str_replace("{PAYMENT ERROR}", "document.getElementById('log').innerHTML = 'ERROR: '+ JSON.stringify(error);", $sHead);
?>
<html>
<head>
<!-- Visa Checkout JavaScript function -->
<?= $sHead; ?>
</head>
<body>
<!-- Visa Checkout button img tag -->
<?= $obj_XML->{'psp-info'}->body; ?>
<div id="log">
</div>
</body>
</html>