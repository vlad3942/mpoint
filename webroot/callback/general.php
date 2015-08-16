<?php
/**
 * This files contains the for the Callback component which handles transactions processed through Adyen.
 * The file will update the Transaction status and add the following data fields:
 *
 * Additionally the component sends out SMS Receipts and performs a callback to the client.
 */

// Require Global Include File
require_once("../inc/include.php");

// Require the PHP API for handling the connection to GoMobile
require_once(sAPI_CLASS_PATH ."/gomobile.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for Capture component (for use with auto-capture functionality)
require_once(sCLASS_PATH ."/capture.php");
// Require specific Business logic for the CPM PSP component
require_once(sINTERFACE_PATH ."/cpm_psp.php");
// Require specific Business logic for the Adyen component
require_once(sCLASS_PATH ."/adyen.php");
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");

/**
 * Input XML format
 *
<?xml version="1.0" encoding="UTF-8"?>
<root>
	<callback>
		<psp-config psp-id="12">
			<name>CellpointMobileCOM</name>
		</psp-config>
		<transaction id="1825317" order-no="970-253176" external-id="8814395474257619">
			<amount country-id="100" currency="DKK">10000</amount>
			<card type-id="8">
				<card-number>411111</card-number>
				<expiry>
					<month>6</month>
					<year>16</year>
				</expiry>
			</card>
		</transaction>
		<status code="2000">17103%3A1111%3A6%2F2016</status>
	</callback>
</root>
 */


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

	
$id = Callback::getTxnIDFromOrderNo($_OBJ_DB, $obj_XML->callback->transaction["order-no"], (integer) $obj_XML->callback->{'psp-config'}["psp-id"]);

try
{
	$obj_TxnInfo = TxnInfo::produceInfo($id, $_OBJ_DB);

	// Intialise Text Translation Object
	$_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH . $obj_TxnInfo->getLanguage() ."/global.txt", sLANGUAGE_PATH . $obj_TxnInfo->getLanguage() ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");

	$obj_mPoint = new Callback($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, array() );
	$iStateID = (integer) $obj_XML->callback->status["code"];
	
	
	// Save Ticket ID representing the End-User's stored Card Info
	if ($iStateID == Constants::iPAYMENT_ACCEPTED_STATE && count($obj_mPoint->getMessageData($obj_TxnInfo->getID(), Constants::iTICKET_CREATED_STATE, false) ) == 1)
	{
		$obj_mPoint->delMessage($obj_TxnInfo->getID(), Constants::iTICKET_CREATED_STATE);
		$obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iTICKET_CREATED_STATE, "Ticket: ". $obj_XML->callback->transaction->card->token);

		// Card Number preg_replace('/\s+/', '', $string)
		$sExpiry =  $obj_XML->callback->transaction->card->expiry->month ."/". $obj_XML->callback->transaction->card->expiry->year;
		
		$iStatus = $obj_mPoint->saveCard($obj_TxnInfo,
										 $obj_TxnInfo->getMobile(),
										 (integer) $obj_XML->callback->transaction->card["type-id"],
										 (integer) $obj_XML->callback->{'psp-config'}["psp-id"],
										 $obj_XML->callback->transaction->card->token,
										 (string) $sMask = $obj_XML->callback->transaction->card->{'card-number'}, 
										 (string) preg_replace('/\s+/', '', $sExpiry) );
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
	$obj_mPoint->completeTransaction( (integer) $obj_XML->callback->{'psp-config'}["psp-id"],
									  -1,
									  (integer) $obj_XML->callback->transaction->card["type-id"],
									  $iStateID,
									  $fee,
									  array($HTTP_RAW_POST_DATA) );
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
		$obj_mPoint->notifyClient($iStateID, $obj_XML);
	}
}
catch (TxnInfoException $e)
{
	// Database connection is active & healthy
	if ( ($_OBJ_DB instanceof RDB) === true && is_resource($_OBJ_DB->getDBConn() ) === true)
	{
	}
	// Internal Error
	else
	{
		header("HTTP/1.1 500 Internal Server Error");

	}
	trigger_error($e->getMessage() ."\n". $HTTP_RAW_POST_DATA, E_USER_WARNING);
}