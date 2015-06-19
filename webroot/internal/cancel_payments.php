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

$to = time() - 3600*24*5; // NOW minus 6 days
$from = time() - 3600*24*30; // NOW minus 30 days
$aTxns = $obj_Status->getActiveTransactions($from, $to, true, 50);
$aSuccess = array();

echo date("r");

foreach ($aTxns as $txn)
{
	try
	{
		$obj_TxnInfo = TxnInfo::produceInfo($txn["ID"], $_OBJ_DB);
		$obj_PSP = Callback::producePSP($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO);

		// Notify retail system
		//TODO: Remove this switch case once notifyClient in mPoint has been normalized and centralized
		switch ($obj_TxnInfo->getPSPID() )
		{
		case Constants::iDIBS_PSP:
			$iStatus = $obj_PSP->refund($obj_TxnInfo->getAmount(), 2); //Second argument "2" orders a cancel ONLY refund behavior @see DIBS class
			$obj_PSP->notifyClient(Constants::iPAYMENT_CANCELLED_STATE, array('transact'=>$obj_TxnInfo->getExternalID(), 'amount'=>$obj_TxnInfo->getAmount() ) );
			break;
		case Constants::iNETAXEPT_PSP:
			$iStatus = $obj_PSP->refund($obj_TxnInfo->getAmount(), 2); //Second argument "2" orders a cancel ONLY refund behavior @see Netaxept class
			$obj_PSP->notifyClient(Constants::iPAYMENT_CANCELLED_STATE, array('transact'=>$obj_TxnInfo->getExternalID(), 'amount'=>$obj_TxnInfo->getAmount(), 'fee'=>$obj_TxnInfo->getFee(), 'cardid'=>0, 'cardnomask'=>"" ) );
			break;
		case Constants::iMOBILEPAY_PSP:
			$iStatus = $obj_PSP->cancel();
			break;
		default:
			throw new InvalidArgumentException("Transaction is active, but cancel for PSP: ". $obj_TxnInfo->getPSPID() ." is not supported", E_USER_WARNING);
		}

		if ($iStatus == 1001) { $aSuccess[] = intval($txn["ID"]); }
		else { throw new RuntimeException("Cancel failed with status code: ". $iStatus); }
	}
	catch (Exception $e)
	{
		echo "Transaction: ". $txn["ID"]. " ignored due to: ". $e->getMessage(). "\n";
	}

}

echo "Cancelled transactions for mPoint id's (". implode(',', $aSuccess) .")\n\n\n";
