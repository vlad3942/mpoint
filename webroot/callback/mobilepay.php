<?php
/**
 * This files contains the for the Callback component which handles transactions processed through MobilePay.
 * The file will update the Transaction status and add the following data fields:
 * 	- MobilePay' Transaction ID
 * 	- ID of the selected Card
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
// Require specific Business logic for the CPM PSP component
require_once(sINTERFACE_PATH ."/cpm_psp.php");
// Require specific Business logic for the MobilePay component
require_once(sCLASS_PATH ."/mobilepay.php");
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");

/**
 * Input XML format
 *
 *	<root>
 *		<callback client-id="">
 * 			<psp-config psp-id=""/>
 *				<transaction id="" order-no="" external-id="" is-captured="false">
 *					<amount country-id="" currency="" symbol="" format=""></amount>
 *					<status code="">message</status>
 *				</transaction>
 *		</callback>
 *	</root>
 */


set_time_limit(600);
$sRawInput = file_get_contents("php://input");

$xml = '';
try
{
	// Suppress simpledom errors propagation here, because we handle them elsewhere
	libxml_use_internal_errors(true);
	$obj_DOM = @simpledom_load_string($sRawInput);
	if ( ($obj_DOM instanceof SimpleDOMElement) === false) { throw new InvalidArgumentException("Failed to parse input XML", 400); }

	$i = 0;
	while ( ($_OBJ_DB instanceof RDB) === false && $i < 5)
	{
		// Instantiate connection to the Database
		$_OBJ_DB = RDB::produceDatabase($aDB_CONN_INFO["mpoint"]);
		$i++;
	}
	if ( ($_OBJ_DB instanceof RDB) === false) { throw new Exception("Failed to connect to database after ". $i ." attempts"); }


	$obj_TxnData = $obj_DOM->callback->transaction;
	//TODO: Improvement: validate incomming XML towards an XSD schema
	if ( ($obj_TxnData instanceof SimpleDOMElement) === false) { throw new InvalidArgumentException("Invalid input XML format", 400); }

	$obj_TxnInfo = TxnInfo::produceInfoFromOrderNo($_OBJ_DB, $obj_TxnData['order-no']);
	$obj_PSP = new MobilePay($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo);

	// According to MobilePay spec, a status call should be made here to ensure that the callback request is authentic
	$iPSPStatus = $obj_PSP->status();
	if ($iPSPStatus === Constants::iPAYMENT_ACCEPTED_STATE || $iPSPStatus === Constants::iPAYMENT_CAPTURED_STATE)
	{
		$iFee = 0; // MobilePay fee is hardcoded to zero for now
		$iAmount = $obj_TxnData->amount;

		$iStateID = $obj_PSP->completeTransaction(Constants::iMOBILEPAY_PSP, $obj_TxnData["external-id"], Constants::iMOBILE_PAY , ($obj_TxnData->status["code"] >= 1000 ? Constants::iPAYMENT_ACCEPTED_STATE : Constants::iPAYMENT_REJECTED_STATE), $iFee, array(var_export($obj_TxnData, true) ) );

		// Callback URL has been defined for Client and transaction hasn't been duplicated
		if ($obj_TxnInfo->getCallbackURL() != "" && $iStateID != Constants::iPAYMENT_DUPLICATED_STATE)
		{
			// Transaction uses Auto Capture and Authorization was accepted
			if ($obj_TxnInfo->useAutoCapture() === true && $iStateID == Constants::iPAYMENT_ACCEPTED_STATE)
			{
				// Capture automatically performed by MobilePay or invocation of capture operation with MobilePay succeeded
				$isAlreadyCaptured = isset($obj_TxnData["is-captured"]) === true && strtolower($obj_TxnData["is-captured"]) == "true";
				if ($isAlreadyCaptured || $obj_PSP->capture() == 1000)
				{
					$obj_PSP->notifyClient(Constants::iPAYMENT_ACCEPTED_STATE, $iAmount);
					$obj_PSP->notifyClient(Constants::iPAYMENT_CAPTURED_STATE, $iAmount);
					if ($isAlreadyCaptured) { $obj_PSP->newMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_CAPTURED_STATE, var_export($obj_TxnData, true) ); }
				}
				else { $obj_PSP->notifyClient(Constants::iPAYMENT_DECLINED_STATE, $iAmount); }
			}
			else { $obj_PSP->notifyClient($iStateID, $iAmount); }
		}

		$xml .= '<status code="'. $iStateID .'">Callback handled</status>';

		// Client has SMS Receipt enabled and payment has been authorized
		if ($obj_TxnInfo->getClientConfig()->smsReceiptEnabled() === true && $iStateID == Constants::iPAYMENT_ACCEPTED_STATE)
		{
			$obj_PSP->sendSMSReceipt(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO) );
		}
	}
	else
	{
		header("HTTP/1.0 403 Forbidden");
		$xml .= '<status code="403">Transaction not in a valid state, PSP state: '. $iPSPStatus .'</status>';
		trigger_error("Transaction not in valid state. Txn ID: ". $obj_TxnInfo->getID() .", State: ". $iPSPStatus ."\n". "Input Data: " ."\n". $sRawInput, E_USER_ERROR);
	}
}
catch (TxnInfoException $e)
{
	header("HTTP/1.0 404 Not Found");
	$xml .= '<status code="404">Transaction not found</status>';
	trigger_error($e->getMessage() ." (". $e->getCode() .")" ."\n". "Input Data: " ."\n". $sRawInput, E_USER_ERROR);
}
catch (UnexpectedValueException $e)
{
	header("HTTP/1.0 502 Bad Gateway");
	$xml .= '<status code="999">The PSP gateway gave an unexpected response</status>';
	trigger_error($e->getMessage() ." (". $e->getCode() .")" ."\n". "Input Data: " ."\n". $sRawInput, E_USER_ERROR);
}
catch (CallbackException $e) { /* ignore */ }
catch (InvalidArgumentException $e)
{
	header("HTTP/1.0 400 Bad Request");
	$xml .= '<status code="'. $e->getCode() .'">'. htmlspecialchars($e->getMessage(), ENT_NOQUOTES) .'</status>';
	$sErrors = '';
	foreach (libxml_get_errors() as $err)
	{
		$sErr = "Error ". $err->code .": ". $err->message ." on line ". $err->line .", column ". $err->column;
		$xml .= '<status code="'. $e->getCode() .'">'. htmlspecialchars($sErr, ENT_NOQUOTES). '</status>';
		$sErrors .= $sErr. "\n";
	}
	trigger_error($e->getMessage() ." (". $e->getCode() .")" ."\n". "Input Data: " ."\n". $sRawInput. "\nDebug:\n". $sErrors, E_USER_ERROR);
}
catch (Exception $e)
{
	header("HTTP/1.0 500 Internal Error");
	$xml .= '<status code="'. $e->getCode() .'">'. htmlspecialchars($e->getMessage(), ENT_NOQUOTES) .'</status>';
	trigger_error($e->getMessage() ." (". $e->getCode() .")" ."\n". "Input Data: " ."\n". $sRawInput, E_USER_ERROR);
}

header("Content-Type: text/xml; charset=\"UTF-8\"");
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<root>';
echo $xml;
echo '</root>';
