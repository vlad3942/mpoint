<?php
/**
 * This files contains the Controller for mPoint's Top-Up API.
 * The Controller will ensure that all input from the Client is validated and a new payment transaction is started.
 * Finally, assuming the Client Input is valid, the Controller will redirect the Customer to the: /pay/card.php page
 * to start the payment flow.
 * If the input provided was determined to be invalid, an error page will be generated.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package API
 * @subpackage TopUp
 * @version 1.10
 */

// Require Global Include File
require_once("../inc/include.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");

// Require Business logic for the Top-Up Component
require_once(sCLASS_PATH ."/topup.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");


$aMsgCds = array();

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );

// Set Global Defaults
if (array_key_exists("account", $_REQUEST) === false) { $_REQUEST['account'] = -1; }
if (array_key_exists("orderid", $_REQUEST) === false) { $_REQUEST['orderid'] = null; }
if (array_key_exists("email", $_REQUEST) === false) { $_REQUEST['email'] = ""; }

$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);

// Validate basic information
if (Validate::valBasic($_OBJ_DB, $_REQUEST['clientid'], $_REQUEST['account']) == 100)
{
	$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, $_REQUEST['clientid'], $_REQUEST['account']);

	// Set Client Defaults
	if (array_key_exists("operator", $_REQUEST) === false) { $_REQUEST['operator'] = $obj_ClientConfig->getCountryConfig()->getID() * 100; }
	if (array_key_exists("logo-url", $_REQUEST) === false) { $_REQUEST['logo-url'] = $obj_ClientConfig->getLogoURL(); }
	if (array_key_exists("css-url", $_REQUEST) === false) { $_REQUEST['css-url'] = $obj_ClientConfig->getCSSURL(); }
	if (array_key_exists("accept-url", $_REQUEST) === false) { $_REQUEST['accept-url'] = $obj_ClientConfig->getAcceptURL(); }
	if (array_key_exists("cancel-url", $_REQUEST) === false) { $_REQUEST['cancel-url'] = $obj_ClientConfig->getCancelURL(); }
	if (array_key_exists("callback-url", $_REQUEST) === false) { $_REQUEST['callback-url'] = $obj_ClientConfig->getCallbackURL(); }
	if (array_key_exists("icon-url", $_REQUEST) === false) { $_REQUEST['icon-url'] = $obj_ClientConfig->getIconURL(); }
	if (array_key_exists("language", $_REQUEST) === false) { $_REQUEST['language'] = $obj_ClientConfig->getLanguage(); }
	if (array_key_exists("auto-store-card", $_REQUEST) === false) { $_REQUEST['auto-store-card'] = "false"; }

	$obj_mPoint = new TopUp($_OBJ_DB, $_OBJ_TXT, $obj_ClientConfig->getCountryConfig() );
	$iTxnID = $obj_mPoint->newTransaction($obj_ClientConfig, Constants::iTOPUP_PURCHASE_TYPE);

	/* ========== Input Validation Start ========== */
	$obj_Validator = new Validate($obj_ClientConfig->getCountryConfig() );

	if ($obj_Validator->valMobile($_REQUEST['mobile']) != 10) { $aMsgCds[$obj_Validator->valMobile($_REQUEST['mobile']) + 30] = $_REQUEST['mobile']; }
	if ($obj_Validator->valOperator($_REQUEST['operator']) != 10) { $aMsgCds[$obj_Validator->valOperator($_REQUEST['operator']) + 40] = $_REQUEST['operator']; }
	if ($obj_Validator->valPrice($obj_ClientConfig->getMaxAmount(), $_REQUEST['amount']) != 10) { $aMsgCds[$obj_Validator->valPrice($obj_ClientConfig->getMaxAmount(), $_REQUEST['amount']) + 50] = $_REQUEST['amount']; }
	// Validate URLs
	if ($obj_Validator->valURL($_REQUEST['logo-url']) != 10) { $aMsgCds[$obj_Validator->valURL($_REQUEST['logo-url']) + 70] = $_REQUEST['logo-url']; }
	if ($obj_Validator->valURL($_REQUEST['css-url']) != 10) { $aMsgCds[$obj_Validator->valURL($_REQUEST['css-url']) + 80]= $_REQUEST['css-url']; }
	if ($obj_Validator->valURL($_REQUEST['accept-url']) > 1 && $obj_Validator->valURL($_REQUEST['accept-url']) != 10) { $aMsgCds[$obj_Validator->valURL($_REQUEST['accept-url']) + 90] = $_REQUEST['accept-url']; }
	if ($obj_Validator->valURL($_REQUEST['cancel-url']) > 1 && $obj_Validator->valURL($_REQUEST['cancel-url']) < 10) { $aMsgCds[$obj_Validator->valURL($_REQUEST['cancel-url']) + 100] = $_REQUEST['cancel-url']; }
	if ($obj_Validator->valURL($_REQUEST['callback-url']) > 1 && $obj_Validator->valURL($_REQUEST['callback-url']) < 10) { $aMsgCds[$obj_Validator->valURL($_REQUEST['callback-url']) + 110] = $_REQUEST['callback-url']; }
	if ($obj_Validator->valLanguage($_REQUEST['language']) != 10) { $aMsgCds[$obj_Validator->valLanguage($_REQUEST['language']) + 130] = $_REQUEST['language']; }
	if ($obj_Validator->valEMail($_REQUEST['email']) != 1 && $obj_Validator->valEMail($_REQUEST['email']) != 10) { $aMsgCds[$obj_Validator->valEMail($_REQUEST['email']) + 140] = $_REQUEST['email']; }
	if ($obj_Validator->valBoolean($_REQUEST['auto-store-card']) != 10) { $aMsgCds[$obj_Validator->valBoolean($_REQUEST['auto-store-card']) + 150] = $_REQUEST['auto-store-card']; }
	if ($obj_Validator->valURL($_REQUEST['icon-url']) > 1 && $obj_Validator->valURL($_REQUEST['icon-url']) != 10) { $aMsgCds[$obj_Validator->valURL($_REQUEST['icon-url']) + 160] = $_REQUEST['icon-url']; }
	/* ========== Input Validation End ========== */

	// Success: Input Valid
	if (count($aMsgCds) == 0)
	{
		try
		{
			$_REQUEST['auto-store-card'] = General::xml2bool($_REQUEST['auto-store-card']);
			// Update Transaction State
			$_REQUEST['typeid'] = Constants::iTOPUP_PURCHASE_TYPE;
			$_REQUEST['gomobileid'] = -1;
			$obj_mPoint->newMessage($iTxnID, Constants::iINPUT_VALID_STATE, var_export($_REQUEST, true) );

			$_SESSION['obj_TxnInfo'] = TxnInfo::produceInfo($iTxnID, $obj_ClientConfig, $_REQUEST);
			// Associate End-User Account (if exists) with Transaction
			$iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $_SESSION['obj_TxnInfo']->getMobile(), false);
			if ($iAccountID == -1 && trim($_SESSION['obj_TxnInfo']->getEMail() ) != "") { $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $_SESSION['obj_TxnInfo']->getEMail(), false); }
			$_SESSION['obj_TxnInfo']->setAccountID($iAccountID);
			
			// Update Transaction Log
			$obj_mPoint->logTransaction($_SESSION['obj_TxnInfo']);
			// Log additional data
			$obj_mPoint->logClientVars($iTxnID, $_REQUEST);
			
			$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, $obj_ClientConfig->getCountryConfig()->getID(), -1);
			$_SESSION['obj_TxnInfo'] = TxnInfo::produceInfo($iTxnID, $obj_ClientConfig, array_merge($_REQUEST, array("accountid" => $iAccountID) ) );

			$aMsgCds[1000] = "Success";
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
			$obj_mPoint->newMessage($iTxnID, $state, $debug);
		}
	}
}
// Error: Basic information is invalid
else
{
	$aMsgCds[Validate::valBasic($_OBJ_DB, $_REQUEST['clientid'], $_REQUEST['account'])+10] = "Client: ". $_REQUEST['clientid'] .", Account: ". $_REQUEST['account'];
}

// Instantiate data object with the User Agent Profile for the customer's mobile device.
$_SESSION['obj_UA'] = UAProfile::produceUAProfile();

// Success - Input Valid
if (array_key_exists(1000, $aMsgCds) === true)
{
	// Start Payment Flow with selecting the Credit Card (step 1)
	header("Location: http://". $_SERVER['HTTP_HOST'] ."/pay/card.php?". session_name() ."=". session_id() );
}
// Error: Construct Status Page
else
{
	$_GET['msg'] = array_keys($aMsgCds);
	
	$xml = '<?xml version="1.0" encoding="UTF-8"?>';
	$xml .= '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/'. General::getMarkupLanguage($_SESSION['obj_UA'], $_SESSION['obj_TxnInfo']) .'/status.xsl"?>';
	$xml .= '<root>';
	$xml .= $obj_mPoint->getMessages("Status");
	$xml .= '</root>';

	// Display page
	echo $xml;
}
?>