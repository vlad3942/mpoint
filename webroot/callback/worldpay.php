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
 * @version 1.02
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

/*
$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE paymentService PUBLIC "-//WorldPay//DTD WorldPay PaymentService v1//EN" "http://dtd.worldpay.com/paymentService_v1.dtd">
<paymentService version="1.4" merchantCode="CELLPOINT">
<notify>
	<orderStatusEvent orderCode="1768448">
		<payment>
			<paymentMethod>DANKORT-SSL</paymentMethod>
			<paymentMethodDetail>
				<card number="5019********3742" type="creditcard">
					<expiryDate>
						<date month="03" year="2014"/>
					</expiryDate>
				</card>
			</paymentMethodDetail>
			<amount value="100" currencyCode="DKK" exponent="2" debitCreditIndicator="credit"/>
			<lastEvent>AUTHORISED</lastEvent>
			<AuthorisationId id="666"/>
			<CVCResultCode description="C"/>
			<balance accountType="IN_PROCESS_AUTHORISED">
				<amount value="100" currencyCode="DKK" exponent="2" debitCreditIndicator="credit"/>
			</balance>
			<riskScore value="0"/>
		</payment>
		<journal journalType="AUTHORISED">
			<bookingDate>
				<date dayOfMonth="22" month="07" year="2013"/>
			</bookingDate>
			<accountTx accountType="IN_PROCESS_AUTHORISED" batchId="23">
				<amount value="100" currencyCode="DKK" exponent="2" debitCreditIndicator="credit"/>
			</accountTx>
		</journal>
	</orderStatusEvent>
</notify>
</paymentService>';
*/

header("Content-Type: text/plain");

set_time_limit(600);
// Standard retry strategy connecting to the database has proven inadequate
$i = 0;
while ( ($_OBJ_DB instanceof RDB) === false && $i < 5)
{
	// Instantiate connection to the Database
	$_OBJ_DB = RDB::produceDatabase($aDB_CONN_INFO["mpoint"]);
	$i++;
}
$obj_XML = simplexml_load_string($HTTP_RAW_POST_DATA);

$id = Callback::getTxnIDFromOrderNo($_OBJ_DB, $obj_XML->notify->orderStatusEvent["orderCode"], Constants::iWORLDPAY_PSP);
if ($id == -1) { $id = (integer) $obj_XML->notify->orderStatusEvent["orderCode"]; }

try
{
	$obj_TxnInfo = TxnInfo::produceInfo($id, $_OBJ_DB);

	// Intialise Text Translation Object
	$_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH . $obj_TxnInfo->getLanguage() ."/global.txt", sLANGUAGE_PATH . $obj_TxnInfo->getLanguage() ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");

	$obj_mPoint = new WorldPay($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo);

	switch(strval($obj_XML->notify->orderStatusEvent->payment->lastEvent) )
	{
	case "AUTHORISED":		// Payment Authorised
		$iStateID = Constants::iPAYMENT_ACCEPTED_STATE;
		break;
	case "CAPTURED":		// Payment Captured
		$iStateID = Constants::iPAYMENT_CAPTURED_STATE;
		break;
	case "CANCELLED":		// Payment Cancelled
		$iStateID = Constants::iPAYMENT_CANCELLED_STATE;
		break;
	case "SENT_FOR_REFUND":	// Payment Refunded
	case "REFUNDED":
		$iStateID = Constants::iPAYMENT_REFUNDED_STATE;
		break;
	default:				// Payment Rejected
		$iStateID = Constants::iPAYMENT_REJECTED_STATE;
		break;
	}
	// Save Ticket ID representing the End-User's stored Card Info
	if ($iStateID == Constants::iPAYMENT_ACCEPTED_STATE && count($obj_mPoint->getMessageData($obj_TxnInfo->getID(), Constants::iTICKET_CREATED_STATE, false) ) == 1)
	{
		$obj_mPoint->delMessage($obj_TxnInfo->getID(), Constants::iTICKET_CREATED_STATE);
		$obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iTICKET_CREATED_STATE, "Ticket: ". $obj_XML->notify->orderStatusEvent["orderCode"]);

		$sToken = $obj_XML->notify->orderStatusEvent["orderCode"] ." ### ". $obj_XML["merchantCode"] ." ### ". $obj_XML->notify->orderStatusEvent->payment->balance->amount["value"] ." ### ". $obj_XML->notify->orderStatusEvent->payment->balance->amount["currencyCode"];
		// Card Number
		if (count($obj_XML->notify->orderStatusEvent->payment->cardNumber) == 1) { $sMask = $obj_XML->notify->orderStatusEvent->payment->cardNumber; }
		else { $sMask = $obj_XML->notify->orderStatusEvent->payment->paymentMethodDetail->card["number"]; }
		$sExpiry = $obj_XML->notify->orderStatusEvent->payment->paymentMethodDetail->card->expiryDate->date["month"] ."/". substr($obj_XML->notify->orderStatusEvent->payment->paymentMethodDetail->card->expiryDate->date["year"], -2);

		$iStatus = $obj_mPoint->saveCard($obj_TxnInfo, $obj_TxnInfo->getMobile(), $obj_mPoint->getCardID( (string) $obj_XML->notify->orderStatusEvent->payment->paymentMethod), Constants::iWORLDPAY_PSP, $sToken, $sMask, $sExpiry);
		// The End-User's existing account was linked to the Client when the card was stored
		if ($iStatus == 1)
		{
			$obj_mPoint->sendLinkedInfo(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO), $obj_TxnInfo);
		}
		// New Account automatically created when Card was saved
		else if ($iStatus == 2)
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
	}
	$fee = 0;
	$obj_mPoint->completeTransaction(Constants::iWORLDPAY_PSP, -1, $obj_mPoint->getCardID( (string) $obj_XML->notify->orderStatusEvent->payment->paymentMethod), $iStateID, $fee, array($HTTP_RAW_POST_DATA) );
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
	if ($iStateID == Constants::iPAYMENT_ACCEPTED_STATE && $obj_TxnInfo->getReward() > 0 && $obj_TxnInfo->getAccountID() > 0)
	{
		$obj_mPoint->topup($obj_TxnInfo->getAccountID(), Constants::iREWARD_OF_POINTS, $obj_TxnInfo->getID(), $obj_TxnInfo->getReward() );
	}

	// Customer has an account
	if ($iStateID == Constants::iPAYMENT_ACCEPTED_STATE && $obj_TxnInfo->getAccountID() > 0)
	{
		$obj_mPoint->associate($obj_TxnInfo->getAccountID(), $obj_TxnInfo->getID() );
	}

	// Client has SMS Receipt enabled
	if ($iStateID == Constants::iPAYMENT_ACCEPTED_STATE && $obj_TxnInfo->getClientConfig()->smsReceiptEnabled() === true)
	{
		$obj_mPoint->sendSMSReceipt(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO) );
	}

	// Callback URL has been defined for Client
	if ($obj_TxnInfo->getCallbackURL() != "")
	{
		$obj_mPoint->notifyClient($iStateID, $obj_XML, new SurePayConfig(6, 5) );
	}
	echo "[OK]";
}
catch (TxnInfoException $e)
{
	header("HTTP/1.1 500 Internal Server Error");

	echo "[ERROR]";

	trigger_error($e->getMessage() ."\n". $HTTP_RAW_POST_DATA, E_USER_WARNING);
}
?>