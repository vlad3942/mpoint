<?php
/**
 * This files contains the for the Callback component which handles transactions processed through WannaFind.
 * The component sends out SMS Receipts and performs a callback to the client.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage WannaFind
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
// Require specific Business logic for the WannaFind component
require_once(sCLASS_PATH ."/wannafind.php");

header("Content-Type: text/plain");

try
{
	// Intialise Text Translation Object
	$_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH . $_REQUEST['language'] ."/global.txt", sLANGUAGE_PATH . $_REQUEST['language'] ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");
}
catch (TranslateTextException $e)
{
	// Intialise Text Translation Object
	$_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH ."gb/global.txt", sLANGUAGE_PATH ."gb/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");
	trigger_error("Unknown Language received from WannaFind. language: ". $_REQUEST['language'], E_USER_WARNING);
}
try
{
	$obj_TxnInfo = TxnInfo::produceInfo($_REQUEST['mpoint-id'], $_OBJ_DB);
	// Account Top-Up
	if ($obj_TxnInfo->getTypeID() >= 100 && $obj_TxnInfo->getTypeID() <= 109 && $obj_TxnInfo->getClientConfig()->getCountryConfig()->getID() == $_REQUEST['clientid'])
	{
		$aTxnInfo = array("client_config" => ClientConfig::produceConfig($_OBJ_DB, $_REQUEST['clientid'], -1) );
		$obj_TxnInfo = TxnInfo::produceInfo($_REQUEST['mpoint-id'], $_OBJ_DB, $obj_TxnInfo, $aTxnInfo);
	}
	$obj_mPoint = new WannaFind($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO["wannafind"]);

	// Save Ticket ID representing the End-User's stored Card Info
	if ($_REQUEST['actioncode'] == 0 && $_REQUEST['authtype'] == "subscribe")
	{
		$obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iTICKET_CREATED_STATE, "Ticket: ". $_REQUEST['transact']);
		$iStatus = $obj_mPoint->saveCard($obj_TxnInfo, $obj_TxnInfo->getMobile(), $_REQUEST['cardid'], Constants::iWANNAFIND_PSP, $_REQUEST['transact'], str_replace("x", "*", $_REQUEST['cardnomask']), NULL);
		// The End-User's existing account was linked to the Client when the card was stored
		if ($iStatus == 1)
		{
			$obj_mPoint->sendLinkedInfo(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO), $obj_TxnInfo);
		}
		// New Account automatically created when Card was saved
		elseif ($iStatus == 2)
		{
			$iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_TxnInfo->getClientConfig(), $obj_TxnInfo->getMobile() );
			if ($iAccountID == -1 && trim($obj_TxnInfo->getEMail() ) != "") { $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_TxnInfo->getClientConfig(), $obj_TxnInfo->getEMail() ); }
			$obj_TxnInfo->setAccountID($iAccountID);
			$obj_mPoint->getTxnInfo()->setAccountID($iAccountID);
			// SMS communication enabled
			if ($obj_TxnInfo->getClientConfig()->smsReceiptEnabled() === true)
			{
				$obj_mPoint->sendAccountInfo(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO), $obj_TxnInfo);
			}
		}
		// E-Mail has been provided for the transaction
		if ($obj_TxnInfo->getEMail() != "") { $obj_mPoint->saveEMail($obj_TxnInfo->getMobile(), $obj_TxnInfo->getEMail() ); }
		$_REQUEST['transact'] = $obj_mPoint->authTicket($_REQUEST['transact']);
		if ($_REQUEST['transact'] == -1) { $_REQUEST['actioncode'] = 1; }
	}

	//
	$fee = 0;
	$iStateID = $obj_mPoint->completeTransaction(Constants::iWANNAFIND_PSP, $_REQUEST['transact'], $_REQUEST['cardid'], ($_REQUEST['actioncode'] == 0 ? Constants::iPAYMENT_ACCEPTED_STATE : Constants::iPAYMENT_REJECTED_STATE), $fee, $_REQUEST);
	// Account Top-Up
	if ($iStateID == Constants::iPAYMENT_ACCEPTED_STATE && $obj_TxnInfo->getTypeID() >= 100 && $obj_TxnInfo->getTypeID() <= 109)
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

	// Not an e-money based purchase
	if ($_REQUEST['cardid'] != Constants::iWALLET && $obj_TxnInfo->getAccountID() > 0)
	{
		$obj_mPoint->associate($obj_TxnInfo->getAccountID(), $obj_TxnInfo->getID() );
	}
	
	// Client has SMS Receipt enabled and payment has been authorized
	if ($obj_TxnInfo->getClientConfig()->smsReceiptEnabled() === true && $iStateID == Constants::iPAYMENT_ACCEPTED_STATE)
	{
		$obj_mPoint->sendSMSReceipt(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO) );
	}

	// Callback URL has been defined for Client and transaction hasn't been duplicated
	if ($obj_TxnInfo->getCallbackURL() != "" && $iStateID != Constants::iPAYMENT_DUPLICATED_STATE)
	{
		// Transaction uses Auto Capture and Authorization was accepted
		if ($obj_TxnInfo->useAutoCapture() === true && $iStateID == Constants::iPAYMENT_ACCEPTED_STATE)
		{
			// Capture automatically performed by WannaFind or invocation of capture operation with WannaFind succeeded
			if ($obj_mPoint->capture() == 0)
			{
				$obj_mPoint->notifyClient(Constants::iPAYMENT_ACCEPTED_STATE, $_REQUEST);
				$obj_mPoint->notifyClient(Constants::iPAYMENT_CAPTURED_STATE, $_REQUEST);
			}
			else { $obj_mPoint->notifyClient(Constants::iPAYMENT_DECLINED_STATE, $_REQUEST); }
		}
		else { $obj_mPoint->notifyClient($iStateID, $_REQUEST); }
	}
}
catch (TxnInfoException $e)
{
	trigger_error($e->getMessage() ." (". $e->getCode() .")" ."\n". "HTTP GET Data: " ."\n". var_export($_REQUEST, true), E_USER_ERROR);
}
