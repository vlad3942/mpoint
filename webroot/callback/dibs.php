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
 * @version 1.03
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
	$_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH . $sLang ."/global.txt", sLANGUAGE_PATH . $sLang ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");
}
catch (TranslateTextException $e)
{
	// Intialise Text Translation Object
	$_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH ."gb/global.txt", sLANGUAGE_PATH ."gb/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");
	trigger_error("Unknown Language received from DIBS. language: ". $_POST['language'] .", lang: ". $_POST['lang'], E_USER_WARNING);
}
try
{
	$obj_TxnInfo = TxnInfo::produceInfo($_POST['mpointid'], $_OBJ_DB);
	// Account Top-Up
	if ($obj_TxnInfo->getTypeID() >= 100 && $obj_TxnInfo->getTypeID() <= 109 && $obj_TxnInfo->getClientConfig()->getCountryConfig()->getID() == $_POST['clientid'])
	{
		$aTxnInfo = array("client-config" => ClientConfig::produceConfig($_OBJ_DB, $_POST['clientid'], -1) );
		$obj_TxnInfo = TxnInfo::produceInfo($_POST['mpointid'], $obj_TxnInfo, $aTxnInfo);
	}
	$obj_mPoint = new DIBS($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo);

	// Save Ticket ID representing the End-User's stored Card Info
	if (array_key_exists("preauth", $_POST) === true && @$_POST['preauth'] == "true")
	{
		$sMask = $_POST['cardprefix'] . substr($_POST['cardnomask'], strlen($_POST['cardprefix']) );
		$sExpiry = substr($_POST['cardexpdate'], 2) ."/". substr($_POST['cardexpdate'], 0, 2);

		$iStatus = $obj_mPoint->saveCard($obj_TxnInfo->getMobile(), $_POST['cardid'], Constants::iDIBS_PSP, $_POST['transact'], str_replace("X", "*", $sMask), $sExpiry);
		// The End-User's existing account was linked to the Client when the card was stored
		if ($iStatus == 1)
		{
			$obj_mPoint->sendLinkedInfo(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO), $obj_TxnInfo);
		}
		// New Account automatically created when Card was saved and SMS communication enabled
		elseif ($iStatus == 2 && $obj_TxnInfo->getClientConfig()->smsReceiptEnabled() === true)
		{
			$obj_mPoint->sendAccountInfo(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO), $obj_TxnInfo);
		}
		// E-Mail has been provided for the transaction
		if ($obj_TxnInfo->getEMail() != "")
		{
			$obj_mPoint->saveEMail($obj_TxnInfo->getMobile(), $obj_TxnInfo->getEMail() );
		}
		$obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iTICKET_CREATED_STATE, "Ticket: ". $_POST['transact']);
		$_POST['transact'] = $obj_mPoint->authTicket($_POST['transact']);
	}

	//
	$iStateID = $obj_mPoint->completeTransaction(Constants::iDIBS_PSP, $_POST['transact'], $_POST['cardid'], ($_POST['transact'] > 0 ? Constants::iPAYMENT_ACCEPTED_STATE : Constants::iPAYMENT_REJECTED_STATE), $_POST);
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

	// Not an e-money based purchase
	if ($_POST['cardid'] != Constants::iEMONEY_CARD && $obj_TxnInfo->getAccountID() > 0)
	{
		$obj_mPoint->associate($obj_TxnInfo->getAccountID(), $obj_TxnInfo->getID() );
	}

	// Callback URL has been defined for Client and transaction hasn't been duplicated
	if ($obj_TxnInfo->getCallbackURL() != "" && $iStateID != Constants::iPAYMENT_DUPLICATED_STATE)
	{
		// Transaction uses Auto Capture and Authorization was accepted
		if ($obj_TxnInfo->useAutoCapture() === true && $iStateID == Constants::iPAYMENT_ACCEPTED_STATE)
		{
			// Capture automatically performed by DIBS or invocation of capture operation with DIBS succeeded
			if (array_key_exists("capturenow", $_POST) === true || $obj_mPoint->capture($_POST['transact']) == 0)
			{
				$obj_mPoint->notifyClient(Constants::iPAYMENT_ACCEPTED_STATE, $_POST);
				$obj_mPoint->notifyClient(Constants::iPAYMENT_CAPTURED_STATE, $_POST);
				if (array_key_exists("capturenow", $_POST) === true) { $obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_CAPTURED_STATE, ""); }
			}
			else { $obj_mPoint->notifyClient(Constants::iPAYMENT_DECLINED_STATE, $_REQUEST); }
		}
		else { $obj_mPoint->notifyClient($iStateID, $_POST); }
	}

	// Client has SMS Receipt enabled and payment has been authorized
	if ($obj_TxnInfo->getClientConfig()->smsReceiptEnabled() === true && $iStateID == Constants::iPAYMENT_ACCEPTED_STATE)
	{
		$obj_mPoint->sendSMSReceipt(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO) );
	}
}
catch (TxnInfoException $e)
{
	trigger_error($e->getMessage() ." (". $e->getCode() .")" ."\n". "HTTP POST Data: " ."\n". var_export($_POST, true), E_USER_ERROR);
}
?>