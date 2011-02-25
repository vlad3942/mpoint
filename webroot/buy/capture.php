<?php
/**
 * This files contains the Controller for mPoint's Mobile Web API.
 * The Controller will ensure that all input from a Mobile Internet Site or Mobile Application is validated and a new payment transaction is started.
 * Finally, assuming the Client Input is valid, the Controller will redirect the Customer to one of the following flow start pages:
 * 	- Payment Flow: /pay/card.php
 * 	- Shop Flow: /shop/delivery.php
 * If the input provided was determined to be invalid, an error page will be generated.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package API
 * @subpackage Capture
 * @version 1.00
 */

// Require Global Include File
require_once("../inc/include.php");

// Require specific Business logic for the Capture component
require_once(sCLASS_PATH ."/capture.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for the DIBS component
require_once(sCLASS_PATH ."/dibs.php");

header("Content-Type: text/plain");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");

$aMsgCds = array();

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );

// Set Global Defaults
if (array_key_exists("account", $_REQUEST) === false) { $_REQUEST['account'] = -1; }

$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);

// Validate basic information
if (Validate::valBasic($_OBJ_DB, $_REQUEST['clientid'], $_REQUEST['account']) == 100)
{
	$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, $_REQUEST['clientid'], $_REQUEST['account']);

	// Set Client Defaults
	
	/* ========== Input Validation Start ========== */
	$obj_Validator = new Validate($obj_ClientConfig->getCountryConfig() );
	
	if ($obj_Validator->valmPointID($_OBJ_DB, $_REQUEST['mpointid'], $obj_ClientConfig->getID() ) != 10) { $aMsgCds[$obj_Validator->valmPointID($_OBJ_DB, $_REQUEST['mpointid'], $obj_ClientConfig->getID() ) + 170] = $_REQUEST['mpointid']; }
	if ($obj_Validator->valOrderID($_OBJ_DB, $_REQUEST['orderid'], $_REQUEST['mpointid']) > 1 && $obj_Validator->valOrderID($_OBJ_DB, $_REQUEST['orderid'], $_REQUEST['mpointid']) < 10) { $aMsgCds[$obj_Validator->valOrderID($_OBJ_DB, $_REQUEST['orderid'], $_REQUEST['mpointid']) + 180] = $_REQUEST['orderid']; }
	/* ========== Input Validation End ========== */
	
	// Success: Input Valid
	if (count($aMsgCds) == 0)
	{
		$a = array($_REQUEST['orderid']);
		$obj_TxnInfo = TxnInfo::produceInfo($_REQUEST['mpointid'], $_OBJ_DB, $a);
		
		/* ========== Input Validation Start ========== */
		if ($obj_Validator->valPrice($obj_TxnInfo->getAmount(), $_REQUEST['amount']) != 10) { $aMsgCds[$obj_Validator->valPrice($obj_TxnInfo->getAmount(), $_REQUEST['amount']) + 50] = $_REQUEST['amount']; }
		/* ========== Input Validation End ========== */
		
		// Success: Input Valid
		if (count($aMsgCds) == 0)
		{
			try
			{
				$obj_mPoint = Capture::produce($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo);
				
				// Capture operation succeeded
				if ($obj_mPoint->capture() == 0)
				{
					$aMsgCds[1000] = "Success";
					$obj_mPoint->getPSP()->notifyClient(Constants::iPAYMENT_CAPTURED_STATE, array("transact" => $obj_mPoint->getPSPID() ) );
				}
				else { $aMsgCds[999] = "Declined"; }
			}
			// Internal Error
			catch (mPointException $e)
			{
				$aMsgCds[$e->getCode()] = $e->getMessage();
			}
		}
		// Error: Invalid Input
		else
		{
			// Log Errors
			foreach ($aMsgCds as $state => $debug)
			{
				$obj_mPoint->newMessage($_REQUEST['mpointid'], $state, $debug);
			}
		}
	}
	// Error: Invalid Input
	else
	{
		// Log Errors
		foreach ($aMsgCds as $state => $debug)
		{
			$obj_mPoint->newMessage($_REQUEST['mpointid'], $state, $debug);
		}
	}
}
// Error: Basic information is invalid
else
{
	$aMsgCds[Validate::valBasic($_OBJ_DB, $_REQUEST['clientid'], $_REQUEST['account'])+10] = "Client: ". $_REQUEST['clientid'] .", Account: ". $_REQUEST['account'];
}
$str = "";
foreach (array_keys($aMsgCds) as $code)
{
	$str .= "&msg=". $code;
}
echo substr($str, 1);
?>