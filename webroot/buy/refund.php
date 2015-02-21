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

// Require data class for Payment Service Provider Configurations
require_once(sCLASS_PATH ."/pspconfig.php");
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
// Require specific Business logic for the NetAxept component
require_once(sCLASS_PATH ."/netaxept.php");
// Require specific Business logic for the NetAxept component
require_once(sCLASS_PATH ."/stripe.php");

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
	if ($code != 6 && $code != 10)
	{
		$aMsgCds[$code + 170] = $_REQUEST['mpointid'];
	}
	if ($obj_Validator->valOrderID($_OBJ_DB, $_REQUEST['orderid'], $_REQUEST['mpointid']) > 1 && $obj_Validator->valOrderID($_OBJ_DB, $_REQUEST['orderid'], $_REQUEST['mpointid']) < 10) { $aMsgCds[$obj_Validator->valOrderID($_OBJ_DB, $_REQUEST['orderid'], $_REQUEST['mpointid']) + 180] = $_REQUEST['orderid']; }
	/* ========== Input Validation End ========== */
	// Success: Input Valid
	if (count($aMsgCds) == 0)
	{
		$obj_TxnInfo = TxnInfo::produceInfo($_REQUEST['mpointid'], $_OBJ_DB);
		
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
					switch ($obj_TxnInfo->getPSPID() )
					{
					case (Constants::iSTRIPE_PSP):
					case (Constants::iDIBS_PSP):	// DIBS
						$obj_mPoint = Refund::produce($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo);
						break;
					case (Constants::iNETAXEPT_PSP):	// NetAxept
						$obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), Constants::iNETAXEPT_PSP);
						if ($obj_TxnInfo->getMode() > 0) { $aHTTP_CONN_INFO["netaxept"]["host"] = str_replace("epayment.", "epayment-test.", $aHTTP_CONN_INFO["netaxept"]["host"]); }
						$aHTTP_CONN_INFO["netaxept"]["username"] = $obj_PSPConfig->getUsername();
						$aHTTP_CONN_INFO["netaxept"]["password"] = $obj_PSPConfig->getPassword();
						$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["netaxept"]);
						
						$obj_mPoint = Refund::produce($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $obj_ConnInfo);
						break;
					default:	// Unkown Payment Service Provider
						break;
					}
					$aClientIDs = $obj_mPoint->getClientsForUser($iUserID);
					// User has access to Client
					if (in_array($obj_ClientConfig->getID(), $aClientIDs) === true)
					{									
						// Refund operation succeeded
						$refund = $obj_mPoint->refund($_REQUEST['amount']);
						if ($refund == 0)
						{
							header("HTTP/1.0 200 OK");
							
							$aMsgCds[1000] = "Success";
							$args = array("transact" => $obj_mPoint->getPSPID(),
										  "amount" => $_REQUEST['amount']);
							$obj_mPoint->getPSP()->notifyClient(Constants::iPAYMENT_REFUNDED_STATE, $args);
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
				/*
				 * Method: valmPointID has not returned one of the following states:
				 * 	 1. Undefined mPoint ID
				 * 	 2. Invalid mPoint ID
				 * 	 3. Transaction not found for mPoint ID
				 */
				if (array_key_exists(171, $aMsgCds) === false && array_key_exists(172, $aMsgCds) === false && array_key_exists(173, $aMsgCds) === false)
				{
					$obj_mPoint->newMessage($_REQUEST['mpointid'], $state, $debug);
				}
				else
				{
					// Transaction not found for mPoint ID
					if ($state == 173 && count($aMsgCds) == 1)
					{
						header("HTTP/1.0 404 Not Found");
					}
					trigger_error("Unable to log invalid input: ". $debug ." for state: ". $state .". No associated transaction found for mPoint ID: ". @$_REQUEST['mpointid'], E_USER_NOTICE);
				}
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
			/*
			 * Method: valmPointID has not returned one of the following states:
			 * 	 1. Undefined mPoint ID
			 * 	 2. Invalid mPoint ID
			 * 	 3. Transaction not found for mPoint ID
			 */
			if (array_key_exists(171, $aMsgCds) === false && array_key_exists(172, $aMsgCds) === false && array_key_exists(173, $aMsgCds) === false)
			{
				$obj_mPoint->newMessage($_REQUEST['mpointid'], $state, $debug);
			}
			else
			{
				// Transaction not found for mPoint ID
				if ($state == 173 && count($aMsgCds) == 1)
				{
					header("HTTP/1.0 404 Not Found");
				}
				trigger_error("Unable to log invalid input: ". $debug ." for state: ". $state .". No associated transaction found for mPoint ID: ". @$_REQUEST['mpointid'], E_USER_NOTICE);
			}
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