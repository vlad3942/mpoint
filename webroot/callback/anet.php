<?php
/**
 * This files contains the for the Callback component which handles transactions processed through Authorize.Net.
 * The component sends out SMS Receipts and performs a callback to the client.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage Authorize.Net
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
// Require specific Business logic for the Authorize.Net component
require_once(sCLASS_PATH ."/anet.php");

$_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH . $_POST['language'] ."/global.txt", sLANGUAGE_PATH . $_POST['language'] ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");

// Intialise Text Translation Object
$obj_TxnInfo = TxnInfo::produceInfo($_POST['mpoint-id'], $_OBJ_DB);

$obj_mPoint = new AuthorizeNet($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo);

switch($_POST['x_response_code'])
{
case (1):	// Payment Approved
	$obj_mPoint->completeTransaction(Constants::iANET_PSP, $_POST['x_trans_id'], $_POST['cardid'], Constants::iPAYMENT_ACCEPTED_STATE, $_POST);
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
		switch ($obj_TxnInfo->getTypeID() )
		{
		case (Constants::iPURCHASE_OF_EMONEY):
			$obj_mPoint->topup($iAccountID, Constants::iTOPUP_OF_EMONEY, $obj_TxnInfo->getID(), $obj_TxnInfo->getAmount() );
			break;
		case (Constants::iPURCHASE_OF_POINTS):
			$obj_mPoint->topup($iAccountID, Constants::iTOPUP_OF_POINTS, $obj_TxnInfo->getID(), $obj_TxnInfo->getPoints() );
			break;
		}
	}
	if ($obj_TxnInfo->getReward() > 0 && $obj_TxnInfo->getAccountID() > 0) { $obj_mPoint->topup($obj_TxnInfo->getAccountID(), Constants::iREWARD_OF_POINTS, $obj_TxnInfo->getID(), $obj_TxnInfo->getReward() ); }
	
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
		$obj_mPoint->notifyClient(Constants::iPAYMENT_ACCEPTED_STATE, $_POST['x_trans_id']);
	}
	// Auto-Capture enabled for Transaction
	if (strtolower($_POST['x_type']) == "auth_capture")
	{
		$obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_CAPTURED_STATE, "");
		// Callback URL has been defined for Client
		if ($obj_TxnInfo->getCallbackURL() != "")
		{
			$obj_mPoint->notifyClient(Constants::iPAYMENT_CAPTURED_STATE, $_POST['x_trans_id']);
		}
	}
	break;
default:			// Payment Rejected
	$iStatus = Constants::iPAYMENT_REJECTED_STATE;
	break;
}

$url = "http://". $_SERVER['HTTP_HOST'];
// Payment transaction approved
if ($_POST['x_response_code'] == 1)
{
	$url .= "/pay/accept.php";
}
// Payment transaction declined
else
{
	$url .= "/anet/dpm.php?cardid=". $_POST['cardid'];
}
?>
<html>
<head>
	<meta http-equiv="refresh" content="0; url=<?= $url;?>" />
</head>
<body>
	<div><br /></div>	
</body>
</html>