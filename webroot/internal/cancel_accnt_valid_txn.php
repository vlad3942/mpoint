<?php

/**
 * This is a cron to be run on scheduled time to cancell all Account Validation transactions.
 * It will get all transactions which were in 1998 i.e account validated state and cancel each of them
 * 
 * @author Deblina Das
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @version 2.00
 */




// Require Global Include File
require_once("../inc/include.php");

// Require specific Business logic for the Status component
require_once(sCLASS_PATH ."/status.php");
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
// Require specific Business logic for the DSB component
require_once(sCLASS_PATH ."/dsb.php");
// Require specific Business logic for the Adyen component
require_once(sCLASS_PATH ."/adyen.php");
// Require specific Business logic for the Apple Pay component
require_once(sCLASS_PATH ."/applepay.php");
// Require specific Business logic for the Data Cash component
require_once(sCLASS_PATH ."/datacash.php");
// Require specific Business logic for the Mada Mpgs component
require_once(sCLASS_PATH ."/mada_mpgs.php");
// Require specific Business logic for the Master Pass component
require_once(sCLASS_PATH ."/masterpass.php");
// Require specific Business logic for the AMEX Express Checkout component
require_once(sCLASS_PATH ."/amexexpresscheckout.php");
// Require specific Business logic for the WireCard component
require_once(sCLASS_PATH ."/wirecard.php");
// Require specific Business logic for the Global Collect component
require_once(sCLASS_PATH ."/globalcollect.php");
// Require specific Business logic for the Android Pay component
require_once(sCLASS_PATH ."/androidpay.php");
// Require specific Business logic for the Secure Trading component
require_once(sCLASS_PATH ."/securetrading.php");
// Require specific Business logic for the PayFort component
require_once(sCLASS_PATH ."/payfort.php");
// Require specific Business logic for the PayPal component
require_once(sCLASS_PATH ."/paypal.php");
// Require specific Business logic for the CCAvenue component
require_once(sCLASS_PATH ."/ccavenue.php");

set_time_limit(0);
$obj_Status = new Status($_OBJ_DB, $_OBJ_TXT);

$clients = isset($_GET['clients']) === true ? explode(",", $_GET['clients']) : array();

$aTxns = $obj_Status->getTransactionInStatus(Constants::iPAYMENT_ACCOUNT_VALIDATED);

$aSuccess = array();

echo date("r"). "\n";

foreach ($aTxns as $txn)
{
	try
	{
		$obj_TxnInfo = TxnInfo::produceInfo($txn["ID"], $_OBJ_DB);
		
		$obj_PSP = Callback::producePSP($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO);
		
		$obj_mPoint = new Refund($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $obj_PSP);
		
		$code = $obj_mPoint->refund($amount);
		
	    if($code = 1001)
	    {
	    	echo "Transaction :". $txn["ID"]." cancelled successfully"."\n" ;
	    }
	    else
	    {
	    	throw new Exception("Failed to cancel");
	    }
		
	}
	catch (Exception $e)
	{
		echo "Transaction: ". $txn["ID"]. " ignored due to: ". $e->getMessage(). "\n";
	}
}

