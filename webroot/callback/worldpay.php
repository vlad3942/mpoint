<?php
/**
 * This files contains the for the Callback component which handles transactions processed through WorldPay.
 * The component sends out SMS Receipts and performs a callback to the client.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage WorldPay
 * @version 1.00
 */

// Require Global Include File
require_once("../inc/include.php");

// Require the PHP API for handling the connection to GoMobile
require_once(sAPI_CLASS_PATH ."/gomobile.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for the WorldPay component
require_once(sCLASS_PATH ."/worldpay.php");

header("Content-Type: text/plain");

$obj_XML = simplexml_load_string($HTTP_RAW_POST_DATA);

$obj_TxnInfo = TxnInfo::produceInfo($obj_XML->notify->orderStatusEvent["orderCode"], $_OBJ_DB);

// Intialise Text Translation Object
$_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH . $obj_TxnInfo->getLanguage() ."/global.txt", sLANGUAGE_PATH . $obj_TxnInfo->getLanguage() ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");

$obj_mPoint = new WorldPay($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo);

switch(strval($obj_XML->notify->orderStatusEvent->payment->lastEvent) )
{
case "AUTHORISED":	// Payment Authorised
	$iStatus = Constants::iPAYMENT_ACCEPTED_STATE;
	break;
case "CAPTURED":	// Payment Captured
	$iStatus = Constants::iPAYMENT_CAPTURED_STATE;
	break;
default:			// Payment Rejected
	$iStatus = Constants::iPAYMENT_REJECTED_STATE;
	break;
}

$obj_mPoint->completeTransaction(Constants::iWORLDPAY_PSP, -1, $obj_mPoint->getCardID( (string) $obj_XML->notify->orderStatusEvent->payment->paymentMethod), $iStatus, array($HTTP_RAW_POST_DATA) );
// Account Top-Up
if ($obj_TxnInfo->getTypeID() >= 100 && $obj_TxnInfo->getTypeID() <= 109)
{
	if ($obj_TxnInfo->getAccountID() > 0) { $iAccountID = $obj_TxnInfo->getAccountID(); }
	else
	{
		$obj_Home = new Home($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo->getClientConfig()->getCountryConfig() );
		$iAccountID = $obj_Home->getAccountID($obj_TxnInfo->getClientConfig()->getCountryConfig(), $obj_TxnInfo->getMobile() );
		if ($iAccountID == -1 && trim($obj_TxnInfo->getEMail() ) != "") { $iAccountID = $obj_Home->getAccountID($obj_TxnInfo->getClientConfig()->getCountryConfig(), $obj_TxnInfo->getEMail() ); }
		
		$obj_mPoint->link($iAccountID);
		$obj_TxnInfo->setAccountID($iAccountID);
	}
	$obj_mPoint->topup($iAccountID, $obj_TxnInfo->getID(), $obj_TxnInfo->getAmount() );
}

// Customer has an account
if ($obj_TxnInfo->getAccountID() > 0)
{
	$obj_mPoint->associate($obj_TxnInfo->getAccountID(), $obj_TxnInfo->getID() );
}

// Client has SMS Receipt enabled
if ($obj_TxnInfo->getClientConfig()->smsReceiptEnabled() === true)
{
	$obj_mPoint->sendSMSReceipt(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO) );
}

// Callback URL has been defined for Client
if ($obj_TxnInfo->getCallbackURL() != "")
{
	$obj_mPoint->notifyClient($iStatus, $obj_XML);
}
?>
[OK]