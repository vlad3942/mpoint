<?php
/**
 * This files contains the for the Callback component which handles transactions processed through mPoints general PSP.
 * The file will update the Transaction status and add the following data fields:
 * 	- PSP Transaction ID
 * 	- ID of the card used for payment.
 * 
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
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");
// Require specific Business logic for the Adyen component
require_once(sCLASS_PATH ."/adyen.php");
// Require specific Business logic for the VISA checkout component
require_once(sCLASS_PATH ."/visacheckout.php");
// Require specific Business logic for the Apple Pay component
require_once(sCLASS_PATH ."/applepay.php");
// Require specific Business logic for the AMEX Express Checkout component
require_once(sCLASS_PATH ."/amexexpresscheckout.php");

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
				<card-number>411111*******4123</card-number>
				<expiry>
					<month>6</month>
					<year>16</year>
				</expiry>
				<token>31232121ddd</token>
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

	
$id = (integer)$obj_XML->callback->transaction["id"];

try
{
	$obj_TxnInfo = TxnInfo::produceInfo($id, $_OBJ_DB);

	// Intialise Text Translation Object
	$_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH . $obj_TxnInfo->getLanguage() ."/global.txt", sLANGUAGE_PATH . $obj_TxnInfo->getLanguage() ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");
	
	$obj_mPoint = Callback::producePSP($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO);
	
	$iStateID = (integer) $obj_XML->callback->status["code"];
	
	// Save Ticket ID representing the End-User's stored Card Info
	if ($iStateID == Constants::iPAYMENT_ACCEPTED_STATE && count($obj_mPoint->getMessageData($obj_TxnInfo->getID(), Constants::iTICKET_CREATED_STATE, false) ) == 1)
	{
		$obj_mPoint->delMessage($obj_TxnInfo->getID(), Constants::iTICKET_CREATED_STATE);
		$obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iTICKET_CREATED_STATE, "Ticket: ". $obj_XML->callback->transaction->card->token);

		
		$sExpiry =  $obj_XML->callback->transaction->card->expiry->month ."/". $obj_XML->callback->transaction->card->expiry->year;
		
		$iStatus = $obj_mPoint->saveCard($obj_TxnInfo,
										 $obj_TxnInfo->getMobile(),
										 (integer) $obj_XML->callback->transaction->card["type-id"],
										 (integer) $obj_XML->callback->{'psp-config'}["psp-id"],
										 $obj_XML->callback->transaction->card->token,
										 $obj_XML->callback->transaction->card->{'card-number'}, 
										 preg_replace('/\s+/', '', $sExpiry) ); // Remove all whitespaces from string.
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
	
	//request received from client appliction for notification ot the wallet instance.
	if($iStateID == Constants::iPAYMENT_ACCEPTED_STATE && count($obj_mPoint->getMessageData($obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE, false) ) == 1 )
	{
		if(isset($obj_XML->callback->{'psp-config'}["psp-id"]) === false )
		{
			$obj_XML->callback->{'psp-config'}->addAttribute('psp-id', $obj_TxnInfo->getPSPID());
		}
		
		switch (intval($obj_XML->callback->{'psp-config'}["psp-id"]) )
		{
		case (Constants::iAPPLE_PAY_PSP):			
			$obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), Constants::iAPPLE_PAY_PSP);
			break;
		case (Constants::iVISA_CHECKOUT_PSP):					
			$obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), Constants::iVISA_CHECKOUT_PSP);
			break;
		case (Constants::iAMEX_EXPRESS_CHECKOUT_PSP):					
			$obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), Constants::iAMEX_EXPRESS_CHECKOUT_PSP);
			break;
		default:	
			break;
		}
		$obj_mPoint->callback($obj_PSPConfig, $obj_XML->callback->transaction->card );
	}
		
	$fee = 0;	
	$obj_mPoint->completeTransaction( (integer) $obj_XML->callback->{'psp-config'}["psp-id"],
									  $obj_XML->callback->transaction["external-id"],
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
	// Transaction uses Auto Capture and Authorization was accepted
	if ($obj_TxnInfo->useAutoCapture() === true && $iStateID == Constants::iPAYMENT_ACCEPTED_STATE)
	{
		// Reload so we have the newest version of the TxnInfo
		$obj_TxnInfo = TxnInfo::produceInfo($id, $_OBJ_DB);
		$obj_mPoint = Callback::producePSP($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO);
		
		$aCallbackArgs = array("transact" => $obj_XML->callback->transaction["external-id"],
							   "amount" => $obj_TxnInfo->getAmount(),
							   "card-id" =>  $obj_XML->callback->transaction->card["type-id"]);
		
		$responseCode = $obj_mPoint->capture($obj_TxnInfo->getAmount());
		
		
		if ($responseCode == 1000)
		{				
			if ($obj_TxnInfo->getCallbackURL() != "") { $obj_mPoint->notifyClient(Constants::iPAYMENT_CAPTURED_STATE, $aCallbackArgs); }
			$obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_CAPTURED_STATE, "");
		}
		else
		{
			if ($obj_TxnInfo->getCallbackURL() != "") { $obj_mPoint->notifyClient(Constants::iPAYMENT_DECLINED_STATE, $aCallbackArgs); }
			$obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_DECLINED_STATE, "Payment Declined (2010)");
		}
	}
	// Callback URL has been defined for Client
	if ($obj_TxnInfo->getCallbackURL() != "")
	{
		$obj_mPoint->notifyClient($iStateID, $obj_XML);
	}
	$xml .= '<status code="1000">Callback Success</status>';
}
catch (TxnInfoException $e)
{
	header("HTTP/1.1 500 Internal Server Error");
	$xml .= '<status code="'. $e->getCode() .'">'. htmlspecialchars($e->getMessage(), ENT_NOQUOTES). '</status>';
	trigger_error($e->getMessage() ."\n". $HTTP_RAW_POST_DATA, E_USER_WARNING);
}
header("Content-Type: text/xml; charset=\"UTF-8\"");
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<root>';
echo $xml;
echo '</root>';
?>
