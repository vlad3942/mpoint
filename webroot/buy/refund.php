<?php
/**
 * This files contains the Controller for mPoint's Refund API.
 * The Controller will ensure that all input from the client is validated prior to performing the refund.
 * Finally, assuming the Client Input is valid, the Controller will contact the Payment Service Provider to perform the Refund.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package API
 * @subpackage Refund
 * @version 1.00
 */

// Require Global Include File
require_once("../inc/include.php");

// Require specific Business logic for the Refund component
require_once(sCLASS_PATH ."/refund.php");

// Require Business logic for Administrative functions
require_once(sCLASS_PATH ."/admin.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for the DIBS component
require_once(sCLASS_PATH ."/dibs.php");

header("Content-Type: application/x-www-form-urlencoded");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");

set_time_limit(120);

$aMsgCds = array();

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );

// Set Global Defaults
if (array_key_exists("account", $_REQUEST) === false) { $_REQUEST['account'] = -1; }

$obj_mPoint = new Admin($_OBJ_DB, $_OBJ_TXT);

// Validate basic information
if (Validate::valBasic($_OBJ_DB, $_REQUEST['clientid'], $_REQUEST['account']) == 100)
{
	$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, $_REQUEST['clientid'], $_REQUEST['account']);

	// Set Client Defaults
	
	/* ========== Input Validation Start ========== */
	$obj_Validator = new Validate($obj_ClientConfig->getCountryConfig() );
	
	// Validate input
	if ($obj_Validator->valUsername($_REQUEST['username']) != 10) { $aMsgCds[$obj_Validator->valUsername($_REQUEST['username']) + 20] = $_REQUEST['username']; }
	if ($obj_Validator->valPassword($_REQUEST['password']) != 10) { $aMsgCds[$obj_Validator->valPassword($_REQUEST['password']) + 30] = $_REQUEST['password']; }
	$code = $obj_Validator->valmPointID($_OBJ_DB, $_REQUEST['mpointid'], $obj_ClientConfig->getID() );
	if ($code != 6)
	{
		if ($code == 10) { $code = 9; }
		$aMsgCds[$code + 170] = $_REQUEST['mpointid'];
	}
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
			$iUserID = -1;
			if ($obj_mPoint->auth($_REQUEST['username'], $_REQUEST['password'], $iUserID) === 10)
			{
				try
				{
					$obj_mPoint = Refund::produce($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo);
					$aClientIDs = $obj_mPoint->getClientsForUser($iUserID);
					// User has access to Client
					if (in_array($obj_ClientConfig->getID(), $aClientIDs) === true)
					{
						// Refund operation succeeded
						if ($obj_mPoint->refund() == 0)
						{
							header("HTTP/1.0 200 OK");
							
							$aMsgCds[1000] = "Success";
							$obj_mPoint->getPSP()->notifyClient(Constants::iPAYMENT_REFUNDED_STATE, array("transact" => $obj_mPoint->getPSPID() ) );
						}
						else
						{
							header("HTTP/1.0 502 Bad Gateway");
							
							$aMsgCds[999] = "Declined";
						}						
					}
					// Error: Unauthorized access
					else { header("HTTP/1.0 403 Forbidden"); }
				}
				catch (HTTPException $e)
				{
					header("HTTP/1.0 502 Bad Gateway");
						
					$aMsgCds[998] = "Error while communicating with PSP";
				}
				// Internal Error
				catch (mPointException $e)
				{
					header("HTTP/1.0 500 Internal Error");
					
					$aMsgCds[$e->getCode()] = $e->getMessage();
				}
			}
			// Error: Unauthorized access
			else { header("HTTP/1.0 403 Forbidden"); }
		}
		// Error: Invalid Input
		else
		{
			header("HTTP/1.0 400 Bad Request");
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
		header("HTTP/1.0 400 Bad Request");
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
	header("HTTP/1.0 400 Bad Request");
	
	$aMsgCds[Validate::valBasic($_OBJ_DB, $_REQUEST['clientid'], $_REQUEST['account'])+10] = "Client: ". $_REQUEST['clientid'] .", Account: ". $_REQUEST['account'];
}
$str = "";
foreach (array_keys($aMsgCds) as $code)
{
	$str .= "&msg=". $code;
}
echo substr($str, 1);
?>