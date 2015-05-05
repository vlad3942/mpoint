<?php
/**
 * This files contains the for the Callback component which handles transactions processed through Cellpoint Mobile.
 * The file will update the Transaction status and send out receipts to the customer.
 * Additionally the client will be notified via the specified Callback URL.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage CellpointMobile
 * @version 1.0
 */

// Require Global Include File
require_once("../inc/include.php");

// Require the PHP API for handling the connection to GoMobile
require_once(sAPI_CLASS_PATH ."/gomobile.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");

// Intialise Text Translation Object
$_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH . $_POST['language'] ."/global.txt", sLANGUAGE_PATH . $_POST['language'] ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");

$obj_mPoint = new Callback($_OBJ_DB, $_OBJ_TXT, TxnInfo::produceInfo($_POST['mpointid'], $_OBJ_DB), $aCPM_CONN_INFO);

// Success: Premium SMS accepted by GoMobile or Stored Value Account charged
if ($_POST['status'] == 200 || $_POST['status'] == 2000)
{
	$iStateID = Constants::iPAYMENT_ACCEPTED_STATE;
}
// Error: Premium SMS rejected by GoMobile
else { $iStateID = Constants::iPAYMENT_REJECTED_STATE; }
//
$obj_mPoint->completeTransaction(Constants::iCPM_PSP, $_POST['gomobileid'], $_POST['cardid'], $iStateID);

// Premium SMS Purchase, associate transaction with End-User Account
if ($_POST['cardid'] == Constants::iPSMS_CARD && $obj_mPoint->getTxnInfo()->getAccountID() > 0)
{
	$obj_mPoint->associate($obj_mPoint->getTxnInfo()->getAccountID(), $obj_mPoint->getTxnInfo()->getID() );
}

// Payment completed via Prepaid Account and Client has SMS Receipt enabled
if ($_POST['cardid'] == Constants::iEMONEY_CARD && $obj_mPoint->getTxnInfo()->getClientConfig()->smsReceiptEnabled() === true)
{
	$obj_mPoint->sendSMSReceipt(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO) );
}

// Account Top-Up
if ($iStateID == Constants::iPAYMENT_ACCEPTED_STATE && $obj_mPoint->getTxnInfo()->getTypeID() >= 100 && $obj_mPoint->getTxnInfo()->getTypeID() <= 109)
{
	switch ($obj_mPoint->getTxnInfo()->getTypeID() )
	{
	case (Constants::iPURCHASE_OF_EMONEY):
		$obj_mPoint->topup($obj_mPoint->getTxnInfo()->getAccountID(), Constants::iTOPUP_OF_EMONEY, $obj_mPoint->getTxnInfo()->getID(), $obj_mPoint->getTxnInfo()->getAmount() );
		break;
	case (Constants::iPURCHASE_OF_POINTS):
		$obj_mPoint->topup($obj_mPoint->getTxnInfo()->getAccountID(), Constants::iTOPUP_OF_POINTS, $obj_mPoint->getTxnInfo()->getID(), $obj_mPoint->getTxnInfo()->getPoints() );
		break;
	}
}
if ($obj_mPoint->getTxnInfo()->getReward() > 0 && $obj_mPoint->getTxnInfo()->getAccountID() > 0) { $obj_mPoint->topup($obj_mPoint->getTxnInfo()->getAccountID(), Constants::iREWARD_OF_POINTS, $obj_mPoint->getTxnInfo()->getID(), $obj_mPoint->getTxnInfo()->getReward() ); }

// Callback URL has been defined for Client
if ($obj_mPoint->getTxnInfo()->getCallbackURL() != "")
{
	$obj_mPoint->notifyClient($iStateID, $_POST['gomobileid']);
	// Notify client of automatic capture
	$obj_mPoint->notifyClient(Constants::iPAYMENT_CAPTURED_STATE, $_POST['gomobileid']);
}
?>