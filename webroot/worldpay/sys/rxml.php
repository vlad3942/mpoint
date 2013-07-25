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

// Require Business logic for the Select Credit Card component
require_once(sCLASS_PATH ."/credit_card.php");

header("Content-Type: text/plain");

$obj_mPoint = new CreditCard($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo']);
$obj_XML = simplexml_load_string($obj_mPoint->getCards($_SESSION['obj_TxnInfo']->getAmount() ) );
$aCards = array();
foreach ($obj_XML->children() as $obj_Elem)
{
	if ($obj_Elem["pspid"] == Constants::iWORLDPAY_PSP) { $aCards[] = (integer) $obj_Elem["type-id"]; }
}

$obj_mPoint = new WorldPay($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo']);

// Stored Card enabled and end-user hasn't made a decision as to whether to store the card or not
if ($obj_mPoint->getTxnInfo()->getClientConfig()->getStoreCard() == 3 && array_key_exists("store-card", $_POST) === false)
{
	$_SESSION['obj_Info']->setInfo("psp-id", Constants::iWORLDPAY_PSP);
	$_SESSION['obj_Info']->setInfo("account", $_POST['merchant-code']);
	$_SESSION['obj_Info']->setInfo("sub-account", $_POST['installation-id']);
	$_SESSION['obj_Info']->setInfo("currency", $_POST['currency']);
	$_SESSION['obj_Info']->setInfo("card-id", $_POST['cardid']);
	$url = "/pay/store.php?". session_name() ."=". session_id();
}
else
{
	if ($_SESSION['obj_TxnInfo']->getMode() > 0) { $aHTTP_CONN_INFO["worldpay"]["host"] = str_replace("secure.", "secure-test.", $aHTTP_CONN_INFO["worldpay"]["host"]); }
	$aLogin = $obj_mPoint->getMerchantLogin($_SESSION['obj_TxnInfo']->getClientConfig()->getID(), Constants::iWORLDPAY_PSP);
	$aHTTP_CONN_INFO["worldpay"]["username"] = $aLogin["username"];
	$aHTTP_CONN_INFO["worldpay"]["password"] = $aLogin["password"];
	
	$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["worldpay"]);
	
	$url = $obj_mPoint->initialize($obj_ConnInfo, $_POST['merchant-code'], $_POST['installation-id'], $_POST['currency'], $aCards);
	$url .= "&preferredPaymentMethod=". $obj_mPoint->getCardName($_POST['cardid']) ."&language=". sLANG;
	$url .= "&successURL=". urlencode("https://". $_SERVER['HTTP_HOST'] ."/pay/accept.php?mpoint-id=". $_SESSION['obj_TxnInfo']->getID() ."&". session_name() ."=". session_id() );
	if (array_key_exists("store-card", $_POST) === true && General::xml2bool($_POST['store-card']) === true)
	{
		$obj_mPoint->newMessage($_SESSION['obj_TxnInfo']->getID(), Constants::iTICKET_CREATED_STATE);
	}
	$url .= "&failureURL=". urlencode("https://". $_SERVER['HTTP_HOST'] ."/pay/card.php?mpoint-id=". $_SESSION['obj_TxnInfo']->getID() ."&". session_name() ."=". session_id() ."&msg=99");
	
	/* ----- Construct Client HTTP Header Start ----- */
	$aHeaders = array();
	$aHeaders[] = "HTTP_CONTENT_LENGTH";
	$aHeaders[] = "HTTP_CONTENT_TYPE";
	$aHeaders[] = "HTTP_HOST";
	$h = "GET {PATH} ". $_SERVER['SERVER_PROTOCOL'] .HTTPClient::CRLF;
	$h .= "Host: {HOST}" .HTTPClient::CRLF;
	foreach ($_SERVER as $key => $val)
	{
		if (in_array($key, $aHeaders) === false && substr($key, 0, 5) == "HTTP_")
		{
			$k = strtolower(substr($key, 5) );
			$k = ucfirst(str_replace("_", "-", $k) );
			$h .= $k .": ". $val .HTTPClient::CRLF;
		}
	}
	/* ----- Construct Client HTTP Header End ----- */
/*
	 $obj_ConnInfo = HTTPConnInfo::produceConnInfo($url);
	$obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
	$obj_HTTP->connect();
	$code = $obj_HTTP->send($h);
	switch ($code)
	{
	case (200):
	$aMatches = array();
	preg_match('/<meta http-equiv="refresh" content="(.*)" \/>/', $obj_HTTP->getReplyBody(), $aMatches);
	$url = substr($aMatches[1], strpos(strtolower($aMatches[1]), "url=") + 4);
	break;
	case (302):
	$a = explode("\r\n", $obj_HTTP->getReplyHeader() );
	$aHeaders = array();
	foreach ($a as $header)
	{
	$pos = strpos($header, ":");
	$aHeaders[strtolower(trim(substr($header, 0, $pos) ) )] = trim(substr($header, $pos + 1) );
	}
	$url = $aHeaders["location"];
	break;
	default:	// Error
	break;
	}
	$obj_HTTP->disConnect();
*/
}
header("location: ". $url);
?>