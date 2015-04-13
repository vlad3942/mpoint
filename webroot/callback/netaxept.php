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
// Require specific Business logic for the NetAxept component
require_once(sCLASS_PATH ."/netaxept.php");
// Require data class for Payment Service Provider Configurations
require_once(sCLASS_PATH ."/pspconfig.php");

header("Content-Type: text/plain");

// as netaxept is setting the wrong  post type this can be removed when when they fix it on their side.
$HTTP_RAW_POST_DATA = file_get_contents("php://input");
$obj_JSON = json_decode($HTTP_RAW_POST_DATA);

$imPointID = Callback::getTxnIDFromExtID($_OBJ_DB, $obj_JSON->TransactionId, Constants::iNETAXEPT_PSP);
try
{
	$obj_TxnInfo = TxnInfo::produceInfo($imPointID, $_OBJ_DB);

	$_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH . $obj_TxnInfo->getLanguage() ."/global.txt", sLANGUAGE_PATH . $obj_TxnInfo->getLanguage() ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");

	$obj_mPoint = new NetAxept($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo);

	$obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), Constants::iNETAXEPT_PSP);
	$aHTTP_CONN_INFO["netaxept"]["username"] = $obj_PSPConfig->getUsername();
	$aHTTP_CONN_INFO["netaxept"]["password"] = $obj_PSPConfig->getPassword();
	$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["netaxept"]);

	// query the server to get check if a stored card was saved at this transaction
	$queryResponse = $obj_mPoint->query($obj_ConnInfo, $obj_PSPConfig->getMerchantAccount(), $obj_JSON->TransactionId );

	if ($queryResponse->Summary->Authorized == "true")
	{
		$iStateID = Constants::iPAYMENT_ACCEPTED_STATE;
	}
	// Save Ticket ID representing the End-User's stored Card Info
	if ($queryResponse->Recurring->PanHash != null)
	{
		$ticket = $queryResponse->Recurring->PanHash;
		$obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iTICKET_CREATED_STATE, "Ticket: ". $ticket);
		$sMask = $queryResponse->CardInformation->MaskedPAN;
		$sExpiry = substr($queryResponse->CardInformation->ExpiryDate, -2) . "/" . substr($queryResponse->CardInformation->ExpiryDate, 0, 2);
		$iStatus = $obj_mPoint->saveCard($obj_TxnInfo, $obj_TxnInfo->getMobile(), $obj_mPoint->getCardID($queryResponse->CardInformation->PaymentMethod), Constants::iNETAXEPT_PSP, $ticket, $sMask, $sExpiry);

		if ($iStatus == 1)
		{
			$iMobileAccountID = -1;
			$iEMailAccountID = -1;
			if (strlen($obj_TxnInfo->getCustomerRef() ) == 0)
			{
				if (floatval($obj_TxnInfo->getMobile() ) > 0) { $iMobileAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_TxnInfo->getClientConfig(), $obj_TxnInfo->getMobile(), $obj_TxnInfo->getCountryConfig(), ($obj_TxnInfo->getClientConfig()->getStoreCard() <= 3) ); }
				if (trim($obj_TxnInfo->getEMail() ) != "") { $iEMailAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_TxnInfo->getClientConfig(), $obj_TxnInfo->getEMail(), $obj_TxnInfo->getCountryConfig(), ($obj_TxnInfo->getClientConfig()->getStoreCard() <= 3) ); }
				if ($iMobileAccountID != $iEMailAccountID && $iEMailAccountID > 0)
				{
					$obj_TxnInfo->setAccountID(-1);
					$obj_mPoint->getTxnInfo()->setAccountID(-1);
				}
			}
			$obj_mPoint->sendLinkedInfo(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO), $obj_TxnInfo);
		}
		// New Account automatically created when Card was saved
		else if ($iStatus == 2)
		{
			$iMobileAccountID = -1;
			$iEMailAccountID = -1;
			if (strlen($obj_TxnInfo->getCustomerRef() ) == 0)
			{
				if (floatval($obj_TxnInfo->getMobile() ) > 0) { $iMobileAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_TxnInfo->getClientConfig(), $obj_TxnInfo->getMobile(), $obj_TxnInfo->getCountryConfig(), false); }
				if (trim($obj_TxnInfo->getEMail() ) != "") { $iEMailAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_TxnInfo->getClientConfig(), $obj_TxnInfo->getEMail(), $obj_TxnInfo->getCountryConfig(), false); }
				if ($iMobileAccountID != $iEMailAccountID && $iEMailAccountID > 0)
				{
					$obj_TxnInfo->setAccountID(-1);
					$obj_mPoint->getTxnInfo()->setAccountID(-1);
				}
			}
			// SMS communication enabled
			if ($obj_TxnInfo->getClientConfig()->smsReceiptEnabled() === true)
			{
				$obj_mPoint->sendAccountInfo(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO), $obj_TxnInfo);
			}
		}

		if ($obj_TxnInfo->getEMail() != "") { $obj_mPoint->saveEMail($obj_TxnInfo->getMobile(), $obj_TxnInfo->getEMail() ); }
	}

	// Account Top-Up
	if ($obj_TxnInfo->getTypeID() >= 100 && $obj_TxnInfo->getTypeID() <= 109)
	{
		if ($obj_TxnInfo->getAccountID() > 0) { $iAccountID = $obj_TxnInfo->getAccountID(); }
		else
		{
			$obj_Home = new Home($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo->getClientConfig()->getCountryConfig() );
			$iAccountID = $obj_Home->getAccountID($obj_TxnInfo->getClientConfig()->getCountryConfig(), $obj_TxnInfo->getMobile() );

			if ($iAccountID == -1 && trim($obj_TxnInfo->getEMail() ) != "")
			{
				$iAccountID = $obj_Home->getAccountID($obj_TxnInfo->getClientConfig()->getCountryConfig(), $obj_TxnInfo->getEMail() );
			}

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
	/*
	// Not an e-money based purchase
	if ($queryResponse->Recurring->PanHash != null && $obj_TxnInfo->getAccountID() > 0)
	{
		$obj_mPoint->associate($obj_TxnInfo->getAccountID(), $obj_TxnInfo->getID() );
	}
	*/
	
	if ($obj_TxnInfo->getReward() > 0 && $obj_TxnInfo->getAccountID() > 0)
	{
		$obj_mPoint->topup($obj_TxnInfo->getAccountID(), Constants::iREWARD_OF_POINTS, $obj_TxnInfo->getID(), $obj_TxnInfo->getReward() );
	}	 
	$args = array("transact" => $obj_JSON->TransactionId,
			 	  "amount" => $queryResponse->OrderInformation->Amount,
			 	  "cardid" =>  $obj_mPoint->getCardID($queryResponse->CardInformation->Issuer),
			 	  "cardnomask" =>  str_replace("X", "*",$queryResponse->CardInformation->MaskedPAN),
			 	  "fee" => $queryResponse->OrderInformation->Fee);
	 
	// Callback URL has been defined for Client and transaction hasn't been duplicated
	if ($obj_TxnInfo->getCallbackURL() != "" && $iStateID != Constants::iPAYMENT_DUPLICATED_STATE)
	{
		// Transaction uses Auto Capture and Authorization was accepted
		if ($obj_TxnInfo->useAutoCapture() === true && $iStateID == Constants::iPAYMENT_ACCEPTED_STATE)
		{
			$responseCode = $obj_mPoint->capture();
			if ($responseCode == 1000)
			{
				//$obj_mPoint->notifyClient(Constants::iPAYMENT_ACCEPTED_STATE, json_decode($HTTP_RAW_POST_DATA, true) );
				$obj_mPoint->notifyClient(Constants::iPAYMENT_CAPTURED_STATE, $args);
				$obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_CAPTURED_STATE, "");
			}
			else
			{
				$obj_mPoint->notifyClient(Constants::iPAYMENT_DECLINED_STATE, $_REQUEST);
				$obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_DECLINED_STATE, "Payment Declined (2010) - Netaxept Error {$responseCode}");
			}
		}
		else { $obj_mPoint->notifyClient($iStateID, $args); }
	}

	// Client has SMS Receipt enabled and payment has been authorized
	if ($obj_TxnInfo->getClientConfig()->smsReceiptEnabled() === true && $iStateID == Constants::iPAYMENT_ACCEPTED_STATE)
	{
		$obj_mPoint->sendSMSReceipt(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO) );
	}
}
catch (TxnInfoException $e)
{
	trigger_error($e->getMessage() ." (". $e->getCode() .")" ."\n". "HTTP POST Data: " ."\n". var_export($HTTP_RAW_POST_DATA, true), E_USER_ERROR);
}
?>