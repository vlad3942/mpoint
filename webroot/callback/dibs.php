<?php
/**
 * This files contains the for the Callback component which handles transactions processed through DIBS.
 * The file will update the Transaction status and add
 * 
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage DIBS
 * @version 1.0
 */

// Require Global Include File
require_once("../inc/include.php");

// Require the PHP API for handling the connection to GoMobile
require_once(sAPI_CLASS_PATH ."/gomobile.php");

// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for the DIBS component
require_once(sCLASS_PATH ."/dibs.php");

// Intialise Text Translation Object
$_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH . $_POST['language'] ."/global.txt", sLANGUAGE_PATH . $_POST['language'] ."/custom.txt"), sSYSTEM_PATH, 0);

$obj_mPoint = new DIBS($_OBJ_DB, $_OBJ_TXT, TxnInfo::produceInfo($_POST['mpointid'], $_OBJ_DB) );

// 
$obj_mPoint->completeTransaction(Constants::iDIBS_PSP, $_POST['transact'], $_POST['cardid'], Constants::iPAYMENT_ACCEPTED_STATE);

// Client has SMS Receipt enabled
if ($obj_mPoint->getTxnInfo()->getClientConfig()->smsReceiptEnabled() === true)
{
	$obj_mPoint->sendSMSReceipt(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO) );
}
// Client has E-Mail Receipt enabled
if ($obj_mPoint->getTxnInfo()->getClientConfig()->emailReceiptEnabled() === true)
{
	$obj_mPoint->sendEMailReceipt();
}

// Callback URL has been defined for Client
if ($obj_mPoint->getTxnInfo()->getCallbackURL() != "")
{
	$obj_mPoint->notifyClient(Constants::iPAYMENT_ACCEPTED_STATE, $_POST);
}
?>