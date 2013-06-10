<?php
/**
 * This files contains the for the Callback component which handles transactions processed through PayEx.
 * The file will update the Transaction status and add the following data fields:
 * 	- PayEx' Transaction ID
 * 	- ID of the selected Card
 * Additionally the component sends out SMS Receipts and performs a callback to the client.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage PayEx
 * @version 1.03
 */

// Require Global Include File
require_once("../inc/include.php");

// Require the PHP API for handling the connection to GoMobile
require_once(sAPI_CLASS_PATH ."/gomobile.php");

// Require data class for Payment Service Provider Configurations
require_once(sCLASS_PATH ."/pspconfig.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for the PayEx component
require_once(sCLASS_PATH ."/payex.php");

header("Content-Type: text/plain");
$obj_TxnInfo = TxnInfo::produceInfo(PayEx::getIDFromOrderRef($_OBJ_DB, $_POST['orderRef']), $_OBJ_DB);
$obj_mPoint = new PayEx($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo);

$obj_mPointConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), Constants::iPAYEX_PSP);

if ($obj_TxnInfo->getMode() > 0) { $aHTTP_CONN_INFO["payex"]["host"] = str_replace("external.", "test-external.", $aHTTP_CONN_INFO["payex"]["host"]); }
$aHTTP_CONN_INFO["payex"]["username"] = $obj_mPointConfig->getUsername();
$aHTTP_CONN_INFO["payex"]["password"] = $obj_mPointConfig->getPassword();
$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["payex"]);
$obj_XML = $obj_mPoint->complete($obj_ConnInfo, $obj_mPointConfig->getMerchantAccount(), $_POST['orderRef']);

$iStateID = (integer) $obj_XML->status["code"];
try
{
	$_POST['transact'] = $_POST['transactionNumber'];
	// Callback URL has been defined for Client and transaction hasn't been duplicated
	if ($obj_TxnInfo->getCallbackURL() != "" && $iStateID != Constants::iPAYMENT_DUPLICATED_STATE)
	{
		// Transaction uses Auto Capture and Authorization was accepted
		if ($obj_TxnInfo->useAutoCapture() === true && $iStateID == Constants::iPAYMENT_ACCEPTED_STATE)
		{
			// Capture automatically performed by PayEx or invocation of capture operation with PayEx succeeded
			if (intval($obj_XML->transactionStatus) == 6 || $obj_mPoint->capture($obj_ConnInfo, $obj_mPointConfig->getMerchantAccount(), $_POST['transact']) == 0)
			{
				$obj_mPoint->notifyClient(Constants::iPAYMENT_ACCEPTED_STATE, $_POST);
				$obj_mPoint->notifyClient(Constants::iPAYMENT_CAPTURED_STATE, $_POST);
				if (intval($obj_XML->transactionStatus) == 6) { $obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_CAPTURED_STATE, ""); }
			}
			else { $obj_mPoint->notifyClient(Constants::iPAYMENT_DECLINED_STATE, $_REQUEST); }
		}
		elseif ($iStateID != Constants::iPAYMENT_CAPTURED_STATE) { $obj_mPoint->notifyClient($iStateID, $_POST); }
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