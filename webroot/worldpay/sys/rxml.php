<?php
/**
 * This files contains the Controller for initializing a payment through WorldPay and redirecting the customer
 * to WorldPay's payment pages.
 * The file will make the necesarry XML calls to WorldPay to initialize the payment transaction.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Payment
 * @subpackage WorldPay
 * @version 1.00
 */

// Require Global Include File
require_once("../../inc/include.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");

// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for the WorldPay component
require_once(sCLASS_PATH ."/worldpay.php");

header("Content-Type: text/plain");

$obj_mPoint = new WorldPay($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo']);

if ($_SESSION['obj_TxnInfo']->getMode() > 0) { $aHTTP_CONN_INFO["worldpay"]["host"] = str_replace("secure.", "secure-test.", $aHTTP_CONN_INFO["worldpay"]["host"]); }
$aHTTP_CONN_INFO["worldpay"]["username"] = $_POST['merchantcode']; 

$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["worldpay"]);
$obj_XML = $obj_mPoint->initialize($obj_ConnInfo, $_POST['merchantcode'], $_POST['currency'], $obj_mPoint->getCardName($_POST['cardid']) );

$url = $obj_XML->reply->orderStatus->reference ."&preferredPaymentMethod=". $obj_mPoint->getCardName($_POST['cardid']) ."&language=". sLANG;
$url .= "&successURL=". urlencode("http://". $_SERVER['HTTP_HOST'] ."/pay/accept.php?". session_name() ."=". session_id() );

$obj_ConnInfo = HTTPConnInfo::produceConnInfo($url);
$obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
$obj_HTTP->connect();
if ($obj_HTTP->send($obj_mPoint->constHTTPHeaders() ) == 200)
{
	$aMatches = array();
	preg_match('/<meta http-equiv="refresh" content="(.*)" \/>/', $obj_HTTP->getReplyBody(), $aMatches);
	$url = substr($aMatches[1], strpos(strtolower($aMatches[1]), "url=") + 4);
}
$obj_HTTP->disConnect();

header("location: ". $url);
?>