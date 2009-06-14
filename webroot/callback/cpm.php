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
$_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH . $_POST['language'] ."/global.txt", sLANGUAGE_PATH . $_POST['language'] ."/custom.txt"), sSYSTEM_PATH, 0);

$obj_mPoint = new Callback($_OBJ_DB, $_OBJ_TXT, TxnInfo::produceInfo($_POST['mpointid'], $_OBJ_DB) );

// Success: Premium SMS accepted by GoMobile or Prepaid Account charged
if ($_POST['status'] == 200 || $_POST['status'] == 2000)
{
	$iStatus = Constants::iPAYMENT_ACCEPTED_STATE;
}
// Error: Premium SMS rejected by GoMobile
else { $iStatus = Constants::iPAYMENT_REJECTED_STATE; }
//
$obj_mPoint->completeTransaction(Constants::iCPM_PSP, $_POST['gomobileid'], $_POST['cardid'], $iStatus);

// Payment completed via Prepaid Account and Client has SMS Receipt enabled
if ($_POST['cardid'] == Constants::iEMONEY_CARD && $obj_mPoint->getTxnInfo()->getClientConfig()->smsReceiptEnabled() === true)
{
	$obj_mPoint->sendSMSReceipt(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO) );
}

// Callback URL has been defined for Client
if ($obj_mPoint->getTxnInfo()->getCallbackURL() != "")
{
	$obj_mPoint->notifyClient($iStatus, $_POST['gomobileid']);
	// Payment completed via Prepaid Account, notify client of automatic capture
	if ($_POST['cardid'] == Constants::iEMONEY_CARD) { $obj_mPoint->notifyClient(Constants::iPAYMENT_CAPTURED_STATE, $_POST['gomobileid']); }
}
?>