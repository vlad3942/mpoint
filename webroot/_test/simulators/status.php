<?php
/**
 * DIBS capture API simulator implemented to behave almost similar to:
 *Status connector for any PSP.
 */

// Require Global Include File
require_once '../../../webroot/inc/include.php';
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."/simpledom.php");

$obj_XML = simpledom_load_string(file_get_contents('php://input') );

$iTransact = $obj_XML->status->transactions->transaction['id'];
$iOrder_id = $obj_XML->status->transactions->transaction->orderid;


switch ($iTransact)
{
    //Authorised
    case 1001001:
        $status = '<status code="2000">Transaction is Authorised.</status>';
        break;
    //Captured
    case 1001002:
        $status = '<status code="2001">Transaction is Captured.</status>';
        break;
    //Rejected
    case 1001003:
        $status = '<status code="2010">Transaction is Rejected.</status>';
        break;
    //Cancelled
    case 1001004:
        $status = '<status code="2002">Transaction is Cancelled.</status>';
        break;
    //Refunded
    case 1001005:
        $status = '<status code="2003">Transaction is Refunded.</status>';
        break;
    default:
        $status = '<status code="999">Unknown Error.</status>';

};


$auth_req = '<?xml version="1.0" encoding="UTF-8"?>';
$auth_req .='<root>';
$auth_req .='<callback>';
$auth_req .='<psp-config id="4">';
$auth_req .='<name>$iTransact</name>';
$auth_req .='</psp-config>';
$auth_req .='<transaction id="'.$iTransact.'"';
$auth_req .=' order-no="'.$iOrder_id.'"';
$auth_req .=' external-id="'.$iTransact.''. $iOrder_id.'">';
$auth_req .='<amount country-id="100" currency="DKK">5000</amount>';
$auth_req .='<card type-id="7">';
$auth_req .='<card-number>3528********0000</card-number>';
$auth_req .='<expiry>';
$auth_req .='<month>01</month>';
$auth_req .='<year>20</year>';
$auth_req .='</expiry>';
$auth_req .='</card>';
$auth_req .='</transaction>';
$auth_req .=$status;
$auth_req .='<approval-code>838653</approval-code>';
$auth_req .='</callback>';
$auth_req .='</root>';

global $aMPOINT_CONN_INFO;
$aConnInfo = $aMPOINT_CONN_INFO;
$aConnInfo['path'] = "/callback/general.php";
$aConnInfo["contenttype"] = "text/xml";

$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aConnInfo);

$h = "{METHOD} {PATH} HTTP/1.0" .HTTPClient::CRLF;
$h .= "host: {HOST}" .HTTPClient::CRLF;
$h .= "referer: {REFERER}" .HTTPClient::CRLF;
$h .= "content-length: {CONTENTLENGTH}" .HTTPClient::CRLF;
$h .= "content-type: {CONTENTTYPE}" .HTTPClient::CRLF;
$h .= "user-agent: mPoint" .HTTPClient::CRLF;
$h .= "Authorization: Basic ". base64_encode("Tusername:Tpassword") .HTTPClient::CRLF;

$obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
$obj_HTTP->connect();
$code = $obj_HTTP->send($h, $auth_req);
$obj_HTTP->disconnect();

$xml = '<?xml version="1.0" encoding="UTF-8"?>';
$xml .='<root>';
$xml .='<transactions>';
$xml .= '<transaction id="'.$iTransact.'">';
$xml .= $status;
$xml .= '</transaction>';
$xml .= '</transactions>';
$xml .= '</root>';

echo $xml;
exit;

