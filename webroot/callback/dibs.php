<?php
/**
 * This files contains the for the Callback component which handles transactions processed through DIBS.
 * The file will update the Transaction status and add the following data fields:
 * 	- DIBS' Transaction ID
 * 	- ID of the selected Card
 * Additionally the component sends out SMS Receipts and performs a callback to the client.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage DIBS
 * @version 1.01
 */

// Require Global Include File
require_once("../inc/include.php");

// Require the PHP API for handling the connection to GoMobile
require_once(sAPI_CLASS_PATH ."/gomobile.php");

// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for the DIBS component
require_once(sCLASS_PATH ."/dibs.php");

try
{
	if (array_key_exists("language", $_POST) === false) { $sLang = $_POST['lang']; }
	else { $sLang = $_POST['language']; }
	// Intialise Text Translation Object
	$_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH . $sLang ."/global.txt", sLANGUAGE_PATH . $sLang ."/custom.txt"), sSYSTEM_PATH, 0);
}
catch (TranslateTextException $e)
{
	// Intialise Text Translation Object
	$_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH ."gb/global.txt", sLANGUAGE_PATH ."gb/custom.txt"), sSYSTEM_PATH, 0);
	trigger_error("Unknown Language received from DIBS. language: ". $_POST['language'] .", lang: ". $_POST['lang'], E_USER_WARNING);
}
try
{
	$obj_mPoint = new DIBS($_OBJ_DB, $_OBJ_TXT, TxnInfo::produceInfo($_POST['mpointid'], $_OBJ_DB) );

	//
	$obj_mPoint->completeTransaction(Constants::iDIBS_PSP, $_POST['transact'], $_POST['cardid'], Constants::iPAYMENT_ACCEPTED_STATE);

	// Client has SMS Receipt enabled
	if ($obj_mPoint->getTxnInfo()->getClientConfig()->smsReceiptEnabled() === true)
	{
		$obj_mPoint->sendSMSReceipt(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO) );
	}

	// Callback URL has been defined for Client
	if ($obj_mPoint->getTxnInfo()->getCallbackURL() != "")
	{
		$obj_mPoint->notifyClient(Constants::iPAYMENT_ACCEPTED_STATE, $_POST);
		// Transaction uses Auto Capture
		if ($obj_mPoint->getTxnInfo()->useAutoCapture() === true) {	$obj_mPoint->notifyClient(Constants::iPAYMENT_CAPTURED_STATE, $_POST); }
	}
}
catch (TxnInfoException $e)
{
	trigger_error($e->getMessage() ." (". $e->getCode() .")" ."\n". "HTTP POST Data: " ."\n". var_export($_POST, true), E_USER_ERROR);
}
?>