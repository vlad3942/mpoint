<?php
/**
 * This files contains the for the Callback component which handles transactions processed through WireCard.
 * The file will update the Transaction status and add the following data fields:
 * 	- WireCard' Transaction ID
 * 	- ID of the selected Card
 * Additionally the component sends out SMS Receipts and performs a callback to the client.
 *
 * @author Abhishek Sawant
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage Wire Card
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
// Require specific Business logic for Capture component (for use with auto-capture functionality)
require_once(sCLASS_PATH ."/capture.php");
// Require specific Business logic for the CPM PSP component
require_once(sINTERFACE_PATH ."/cpm_psp.php");
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");
// Require specific Business logic for the wirecard component
require_once(sCLASS_PATH ."/wirecard.php");
// Require data class for Payment Service Provider Configurations
require_once(sCLASS_PATH ."/pspconfig.php");

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
	
	$obj_mPoint = new WireCard($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO["wire-card"]);
	
	$iStateID = (integer) $obj_XML->callback->status["code"];
	
	if($iStateID == Constants::iPAYMENT_ACCEPTED_STATE && $obj_XML->callback->transaction->card->token != "")
	{

		$obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iTICKET_CREATED_STATE, "Ticket: ". $obj_XML->callback->transaction->card->token);

		
		$sExpiry =  $obj_XML->callback->transaction->card->expiry->month ."/". substr($obj_XML->callback->transaction->card->expiry->year, -2);
		
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
	
	$fee = 0;	
	$obj_mPoint->completeTransaction( (integer) $obj_XML->callback->{'psp-config'}["psp-id"],
									  $obj_XML->callback->transaction["external-id"],
									  (integer) $obj_XML->callback->transaction->card["type-id"],
									  $iStateID,
									  $fee,
									  array($HTTP_RAW_POST_DATA) );
	
	// Customer has an account
	if ($iStateID == Constants::iPAYMENT_ACCEPTED_STATE && $obj_TxnInfo->getAccountID() > 0)
	{
		$obj_mPoint->associate($obj_TxnInfo->getAccountID(), $obj_TxnInfo->getID() );
	}
	
	// Transaction uses Auto Capture and Authorization was accepted
	if ($obj_TxnInfo->useAutoCapture() === true && $iStateID == Constants::iPAYMENT_ACCEPTED_STATE)
	{
		// Reload so we have the newest version of the TxnInfo
		$obj_TxnInfo = TxnInfo::produceInfo($id, $_OBJ_DB);
		
		$aCallbackArgs = array("transact" => $obj_XML->callback->transaction["external-id"],
							   "amount" => $obj_TxnInfo->getAmount(),
							   "card-id" =>  $obj_XML->callback->transaction->card["type-id"]);
		
		$responseCode = $obj_mPoint->capture($obj_TxnInfo->getAmount() );
		
		
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
	$xml = '<status code="1000">Callback Success</status>';
} 
catch (TxnInfoException $e)
{
	header("HTTP/1.1 500 Internal Server Error");
	$xml .= '<status code="'. $e->getCode() .'">'. htmlspecialchars($e->getMessage(), ENT_NOQUOTES). '</status>';
	trigger_error($e->getMessage() ."\n". $HTTP_RAW_POST_DATA, E_USER_WARNING);
}
catch (CallbackException $e)
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
