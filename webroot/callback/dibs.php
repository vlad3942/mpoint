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

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for the DIBS component
require_once(sCLASS_PATH ."/dibs.php");

header("Content-Type: text/plain");

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

	// Save Ticket ID representing the End-User's stored Card Info
	if (@$_POST['preauth'] == "true")
	{
		$sMask = $_POST['cardprefix'] . substr($_POST['cardnomask'], strlen($_POST['cardprefix']) );
		$sExpiry = substr($_POST['cardexpdate'], 2) ."/". substr($_POST['cardexpdate'], 0, 2);

		$obj_mPoint->saveCard($obj_mPoint->getTxnInfo()->getMobile(), $_POST['cardid'], Constants::iDIBS_PSP, $_POST['transact'], str_replace("X", "*", $sMask), $sExpiry, $obj_mPoint->getTxnInfo()->getEMail() );
		if ($obj_mPoint->getTxnInfo()->getEMail() != "")
		{
			$obj_mPoint->saveEMail($obj_mPoint->getTxnInfo()->getMobile(), $obj_mPoint->getTxnInfo()->getEMail() );
		}
		$_POST['transact'] = $obj_mPoint->authTicket($_POST['transact']);
	}
	// Auto-Capture enabled for Client but not performed by DIBS because of "preauth" option
	elseif ($obj_mPoint->getTxnInfo()->getClientConfig()->useAutoCapture() === true && array_key_exists("capturenow", $_POST) === false)
	{
		$obj_mPoint->capture($_POST['transact']);
	}
	//
	$obj_mPoint->completeTransaction(Constants::iDIBS_PSP, $_POST['transact'], $_POST['cardid'], Constants::iPAYMENT_ACCEPTED_STATE);

	// Not an e-money based purchase
	if ($_POST['cardid'] != Constants::iEMONEY_CARD)
	{
		$iAccountID = $obj_mPoint->getAccountID($obj_mPoint->getTxnInfo()->getMobile() );
		if ($iAccountID == -1) { $iAccountID = $obj_mPoint->getAccountID($obj_mPoint->getTxnInfo()->getEMail() ); }
		if ($iAccountID > 0) { $obj_mPoint->associate($iAccountID, $obj_mPoint->getTxnInfo()->getID() ); }
	}

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