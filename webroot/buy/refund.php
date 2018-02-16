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
// Require specific Business logic for the CPM PSP component
require_once(sINTERFACE_PATH ."/cpm_psp.php");
// Require specific Business logic for the CPM ACQUIRER component
require_once(sINTERFACE_PATH ."/cpm_acquirer.php");
// Require specific Business logic for the DIBS component
require_once(sCLASS_PATH ."/dibs.php");
// Require specific Business logic for the WorldPay component
require_once(sCLASS_PATH ."/worldpay.php");
// Require specific Business logic for the NetAxept component
require_once(sCLASS_PATH ."/netaxept.php");
// Require specific Business logic for the DSB PSP component
require_once(sCLASS_PATH ."/dsb.php");
if (function_exists("json_encode") === true && function_exists("curl_init") === true)
{
	// Require specific Business logic for the Stripe component
	require_once(sCLASS_PATH ."/stripe.php");
}
// Require specific Business logic for the MobilePay component
require_once(sCLASS_PATH ."/mobilepay.php");
// Require specific Business logic for the Adyen component
require_once(sCLASS_PATH ."/adyen.php");
// Require specific Business logic for the VISA checkout component
require_once(sCLASS_PATH ."/visacheckout.php");
// Require specific Business logic for the Data Cash component
require_once(sCLASS_PATH ."/datacash.php");
// Require specific Business logic for the Master Pass component
require_once(sCLASS_PATH ."/masterpass.php");
// Require specific Business logic for the AMEX Express Checkout component
require_once(sCLASS_PATH ."/amexexpresscheckout.php");
// Require specific Business logic for the WireCard component
require_once(sCLASS_PATH ."/wirecard.php");
// Require specific Business logic for the Global Collect component
require_once(sCLASS_PATH ."/globalcollect.php");
// Require specific Business logic for the Secure Trading component
require_once(sCLASS_PATH ."/securetrading.php");
// Require specific Business logic for the PayFort component
require_once(sCLASS_PATH ."/payfort.php");
// Require specific Business logic for the CCAvenue component
require_once(sCLASS_PATH ."/ccavenue.php");
// Require specific Business logic for the PayPal component
require_once(sCLASS_PATH ."/paypal.php");
// Require specific Business logic for the 2C2P component
require_once(sCLASS_PATH ."/ccpp.php");
// Require specific Business logic for the MayBank component
require_once(sCLASS_PATH ."/maybank.php");
// Require specific Business logic for the PublicBank component
require_once(sCLASS_PATH ."/publicbank.php");
// Require specific Business logic for the Qiwi component
require_once(sCLASS_PATH ."/qiwi.php");
// Require specific Business logic for the Klarna component
require_once(sCLASS_PATH ."/klarna.php");
// Require specific Business logic for the Alipay component
require_once(sCLASS_PATH ."/alipay.php");
require_once(sCLASS_PATH ."/alipay_chinese.php");
// Require specific Business logic for the customerinfo component
require_once(sCLASS_PATH ."/customer_info.php");

// Require specific Business logic for the Nets component
require_once(sCLASS_PATH ."/nets.php");
// Require specific Business logic for the mVault component
require_once(sCLASS_PATH ."/mvault.php");

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
			if (strtolower($obj_ClientConfig->getUsername() ) == strtolower($_REQUEST['username']) && $obj_ClientConfig->getPassword() == $_REQUEST['password'])
			{	
				try
				{
					$obj_PSP = Callback::producePSP($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO);
					$obj_mPoint = new Refund($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $obj_PSP);

					// Refund operation succeeded
					$code = $obj_mPoint->refund($_REQUEST['amount']);
					if ($code == 1000 || $code == 1001)
					{
						header("HTTP/1.0 200 OK");
						
						$aMsgCds[$code] = "Success";
						// Perform callback to Client
						if (strlen($obj_TxnInfo->getCallbackURL() ) > 0)
						{
							$args = array("transact" => $obj_TxnInfo->getExternalID(),
										  "amount" => $_REQUEST['amount']);
							$obj_mPoint->getPSP()->notifyClient(Constants::iPAYMENT_REFUNDED_STATE, $args);
						}
					}
					else
					{
						header("HTTP/1.0 502 Bad Gateway");
						
						$aMsgCds[999] = "Declined";
					}						
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