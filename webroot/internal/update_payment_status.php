<?php

// Require Global Include File
require_once("../inc/include.php");

// Require specific Business logic for the Status component
require_once(sCLASS_PATH ."/status.php");
// Require specific Business logic for the Capture component
require_once(sCLASS_PATH ."/capture.php");
// Require specific Business logic for the Refund component
require_once(sCLASS_PATH ."/refund.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for the DIBS component
require_once(sCLASS_PATH ."/dibs.php");
// Require specific Business logic for the Netaxept component
require_once(sCLASS_PATH ."/netaxept.php");
// Require specific Business logic for the WannaFind component
require_once(sCLASS_PATH ."/wannafind.php");
// Require specific Business logic for the CPM PSP component
require_once(sINTERFACE_PATH ."/cpm_psp.php");
// Require specific Business logic for the MobilePay component
require_once(sCLASS_PATH ."/mobilepay.php");


set_time_limit(0);

$obj_Status = new Status($_OBJ_DB, $_OBJ_TXT);

$tOffset = isset($_GET['to']) === true ? intval($_GET['to']) : 3600*8;
$fOffset = isset($_GET['from']) === true ? intval($_GET['from']) : 3600*24*5;

$to = time() - $tOffset;
$from = time() - $fOffset;
$aTxns = $obj_Status->getActiveTransactions($from, $to, 0, true, 25);
$aSuccess = array();

echo date("r"). "\n";

foreach ($aTxns as $txn)
{
	try
	{
		$obj_TxnInfo = TxnInfo::produceInfo($txn["ID"], $_OBJ_DB);
		$obj_PSP = Callback::producePSP($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO);

		$iStatus = $obj_Status->getPSPStatus($obj_PSP, $obj_TxnInfo);

		switch ($iStatus)
		{
		case Constants::iPAYMENT_ACCEPTED_STATE:
			throw new RuntimeException("Transaction is already in the accepted state");
			break;
		case Constants::iPAYMENT_CAPTURED_STATE:
			$obj_Capture = new Capture($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $obj_PSP);
			$obj_Capture->updateCapturedAmount($obj_TxnInfo->getAmount() );
			$obj_PSP->newMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_CAPTURED_STATE, array("Auto transaction updater, PSP status: ". $iStatus) );
			break;
		case Constants::iPAYMENT_REFUNDED_STATE:
			$obj_Capture = new Refund($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $obj_PSP);
			$obj_Capture->updateRefundedAmount($obj_TxnInfo->getAmount() );
			$obj_PSP->newMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_REFUNDED_STATE, array("Auto transaction updater, PSP status: ". $iStatus) );
			break;
		case Constants::iPAYMENT_CANCELLED_STATE:
			$obj_PSP->newMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_CANCELLED_STATE, array("Auto transaction updater, PSP status: ". $iStatus) );
			break;

		default:
		throw new InvalidArgumentException("Got invalid PSP status: ". $iStatus ." from PSP: ". $obj_TxnInfo->getPSPID() );
		}

		// Notify retail system
		//TODO: Remove this switch case once notifyClient in mPoint has been normalized and centralized
		switch ($obj_TxnInfo->getPSPID() )
		{
		case Constants::iDIBS_PSP:
			$obj_PSP->notifyClient($iStatus, array('transact'=>$obj_TxnInfo->getExternalID(), 'amount'=>$obj_TxnInfo->getAmount() ) );
			break;
		case Constants::iNETAXEPT_PSP:
			$obj_PSP->notifyClient($iStatus, array('transact'=>$obj_TxnInfo->getExternalID(), 'amount'=>$obj_TxnInfo->getAmount(), 'fee'=>$obj_TxnInfo->getFee(), 'cardid'=>0, 'cardnomask'=>"" ) );
			break;
		case Constants::iMOBILEPAY_PSP:
			$obj_PSP->notifyClient($iStatus, array('transact'=>$obj_TxnInfo->getExternalID(), 'amount'=>$obj_TxnInfo->getAmount(), 'card-id'=>0 ) );
			break;
		default:
			trigger_error("Transaction ". $obj_TxnInfo->getID() ." updated with state ". $iStatus ." but PSP-id: ". $obj_TxnInfo->getPSPID() ." notify client protocol is not supported", E_USER_WARNING);
		}

		$aSuccess[] = intval($txn["ID"]);
	}
	catch (Exception $e)
	{
		echo "Transaction: ". $txn["ID"]. " ignored due to: ". $e->getMessage(). "\n";
	}

}

echo "Updated transaction status for mPoint id's (". implode(',', $aSuccess) .")\n\n\n";
