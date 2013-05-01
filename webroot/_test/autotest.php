<?php
define("sAPI_CLASS_PATH", "/opt/cpm/php5api/classes");
/**
 * Path to Log Files directory
 */
define("sLOG_PATH", "/var/log/cpm/mPoint/");

// Require the PHP API for handling the connection to the Mobile Enterprise Service Bus
require_once(sAPI_CLASS_PATH ."/template.php");
// Require the PHP API for handling the connection to Mobile Enterprise Service Bus
require_once(sAPI_CLASS_PATH ."/http_client.php");


/**
 * Connection info for sending error reports to a remote host
 */
$aHTTP_CONN_INFO["mesb"]["protocol"] = "http";
$aHTTP_CONN_INFO["mesb"]["host"] = "mpoint.test.cellpointmobile.com";
$aHTTP_CONN_INFO["mesb"]["port"] = 80;
$aHTTP_CONN_INFO["mesb"]["timeout"] = 120;
//$aHTTP_CONN_INFO["mesb"]["path"] = "/mticket/startup";
$aHTTP_CONN_INFO["mesb"]["method"] = "POST";
$aHTTP_CONN_INFO["mesb"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["mesb"]["username"] = "CPMDemo";
$aHTTP_CONN_INFO["mesb"]["password"] = "DEMOisNO_2";

$h = "{METHOD} {PATH} HTTP/1.0" .HTTPClient::CRLF;
$h .= "host: {HOST}" .HTTPClient::CRLF;
$h .= "referer: {REFERER}" .HTTPClient::CRLF;
$h .= "content-length: {CONTENTLENGTH}" .HTTPClient::CRLF;
$h .= "content-type: {CONTENTTYPE}; charset=iso-8859-1" .HTTPClient::CRLF;
$h .= "user-agent: mPoint" .HTTPClient::CRLF;
$h .= "authorization: Basic ". base64_encode($aHTTP_CONN_INFO["mesb"]["username"] .":". $aHTTP_CONN_INFO["mesb"]["password"]) .HTTPClient::CRLF;

header("Content-Type: text/xml; charset=\"utf-8\"");
//echo '<table>';

$aHTTP_CONN_INFO["mesb"]["path"] = "/mApp/api/initialize.php";
$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["mesb"]);
$obj_Client = new HTTPClient(new Template, $obj_ConnInfo);
		
$obj_Client->connect();

$b = '<?xml version="1.0" encoding="UTF-8"?>';
$b .= '<root>';
$b .= '<initialize-payment client-id="10007" account="100007">';
$b .= '<transaction order-no="1234abc">';
$b .= '<amount country-id="100">200</amount>';
$b .= '<callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url>';
$b .= '</transaction>';
$b .= '<client-info platform="iOS" version="5.1.1" language="da">';
$b .= '<mobile country-id="100" operator-id="10000">28882861</mobile>';
$b .= '<email>jona@oismail.com</email>';
$b .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
$b .= '</client-info>';
$b .= '</initialize-payment>';
$b .= '</root>';

$code = $obj_Client->send($h, $b);
if ($code == 200)
{
	$obj_XML = simplexml_load_string($obj_Client->getReplyBody() );
	echo $obj_XML->asXML();
}
else { var_dump($obj_Client); die(); }
$obj_Client->disconnect();

$aHTTP_CONN_INFO["mesb"]["path"] = "/mApp/api/pay.php";
$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["mesb"]);
$obj_Client = new HTTPClient(new Template, $obj_ConnInfo);

$obj_Client->connect();

$b = '<?xml version="1.0" encoding="UTF-8"?>';
$b .= '<root>';
$b .= '<pay client-id="10007" account="100007">';
$b .= '<transaction id="'. $obj_XML->transaction["id"] .'" store-card="false">';
$b .= '<card type-id="2">';
$b .= '<amount country-id="'. $obj_XML->transaction->amount["country-id"] .'">'. $obj_XML->transaction->amount .'</amount>';
$b .= '</card>';
$b .= '</transaction>';
$b .= '<client-info platform="iOS" version="1.00" language="da">';
$b .= '<mobile country-id="100" operator-id="10000">28882861</mobile>';
$b .= '<email>jona@oismail.com</email>';
$b .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
$b .= '</client-info>';
$b .= '</pay>';
$b .= '</root>';

$code = $obj_Client->send($h, $b);
if ($code == 200)
{
	var_dump($obj_Client);
	$obj_XML = simplexml_load_string($obj_Client->getReplyBody() );
	echo $obj_XML->asXML();
}
else { var_dump($obj_Client); die(); }
$obj_Client->disconnect();
/*
$aHTTP_CONN_INFO["mesb"]["path"] = "/mpoint/authorize-payment";
$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["mesb"]);
$obj_Client = new HTTPClient(new Template, $obj_ConnInfo);
		
$obj_Client->connect();

$b = '<?xml version="1.0" encoding="UTF-8"?>';
$b .= '<root>';
$b .= '<authorize-payment client-id="10007" account="100007">';
$b .= '<transaction id="'. $obj_XML->transaction["id"] .'">';
$b .= '<card id="'. $obj_XML->{'stored-cards'}->card["id"] .'" type-id="'. $obj_XML->{'stored-cards'}->card["type-id"] .'">';
$b .= '<amount country-id="'. $obj_XML->transaction->amount["country-id"] .'">'. $obj_XML->transaction->amount .'</amount>';
$b .= '</card>';
$b .= '</transaction>';
$b .= '<password>oisJona</password>';
$b .= '<client-info platform="iOS" version="1.00" language="da">';
$b .= '<mobile country-id="100" operator-id="10000">28882861</mobile>';
$b .= '<email>jona@oismail.com</email>';
$b .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
$b .= '</client-info>';
$b .= '</authorize-payment>';
$b .= '</root>';

$code = $obj_Client->send($h, $b);
if ($code == 200)
{
	var_dump($obj_Client);
	$obj_XML = simplexml_load_string($obj_Client->getReplyBody() );
	echo $obj_XML->asXML();
}
else { var_dump($obj_Client); die(); }
$obj_Client->disconnect();
*/
?>