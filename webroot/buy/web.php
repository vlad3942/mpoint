<?php
/**
 * This files contains the both Controller for mPoint's Mobile Web API.
 * The Controller will ensure that all input from a Mobile Internet Site or Mobile Application is validated and a new payment transaction is started.
 * Finally, assuming the Client Input is valid, the Controller will redirect the Customer to one of the following flow start pages:
 * 	- Payment Flow: /pay/card.php
 * 	- Shop Flow: /shop/delivery.php
 * If the input provided was determined to be invalid, an error page will be generated.
 * 
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Web
 * @subpackage Buy
 * @version 1.0
 */

// Require Global Include File
require_once("../inc/include.php");

// Require the PHP API for handling the connection to GoMobile
require_once(sAPI_CLASS_PATH ."/gomobile.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
// Require Business logic for the Mobile Web module
require_once(sCLASS_PATH ."/mobile_web.php");

$aMsgCds = array();

// Set Global Defaults
if (array_key_exists("account", $_POST) === false) { $_POST['account'] = -1; }
if (array_key_exists("orderid", $_POST) === false) { $_POST['orderid'] = null; }

// Validate basic information
if (Validate::valBasic($_OBJ_DB, $_POST['clientid'], $_POST['account']) == 100)
{
	$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, $_POST['clientid'], $_POST['account']);
	
	// Set Client Defaults
	if (array_key_exists("operator", $_POST) === false) { $_POST['operator'] = $obj_ClientConfig->getCountryConfig()->getID() * 1000; }
	if (array_key_exists("logo-url", $_POST) === false) { $_POST['logo-url'] = $obj_ClientConfig->getLogoURL(); }
	if (array_key_exists("css-url", $_POST) === false) { $_POST['css-url'] = $obj_ClientConfig->getCSSURL(); }
	if (array_key_exists("accept-url", $_POST) === false) { $_POST['accept-url'] = $obj_ClientConfig->getAcceptURL(); }
	if (array_key_exists("cancel-url", $_POST) === false) { $_POST['cancel-url'] = $obj_ClientConfig->getCancelURL(); }
	if (array_key_exists("callback-url", $_POST) === false) { $_POST['callback-url'] = $obj_ClientConfig->getCallbackURL(); }
	if (array_key_exists("language", $_POST) === false) { $_POST['language'] = $obj_ClientConfig->getLanguage(); }
	
	$obj_mPoint = new MobileWeb($_OBJ_DB, $_OBJ_TXT, $obj_ClientConfig);
	$iTxnID = $obj_mPoint->newTransaction(Constants::iWEB_PURCHASE_TYPE);
	
	/* ========== Input Validation Start ========== */
	$obj_Validator = new Validate($obj_ClientConfig);
	
	if ($obj_Validator->valAddress($_POST['recipient']) != 10) { $aMsgCds[$obj_Validator->valAddress($_POST['recipient']) + 30] = $_POST['recipient']; }
	if ($obj_Validator->valOperator($_POST['operator']) != 10) { $aMsgCds[$obj_Validator->valOperator($_POST['operator']) + 40] = $_POST['operator']; }
	if ($obj_Validator->valAmount($_POST['amount']) != 10) { $aMsgCds[$obj_Validator->valAmount($_POST['amount']) + 50] = $_POST['amount']; }
	// Validate URLs
	if ($obj_Validator->valURL($_POST['logo-url']) != 10) { $aMsgCds[$obj_Validator->valURL($_POST['logo-url']) + 70] = $_POST['logo-url']; }
	if ($obj_Validator->valURL($_POST['css-url']) != 10) { $aMsgCds[$obj_Validator->valURL($_POST['css-url']) + 80]= $_POST['css-url']; }
	if ($obj_Validator->valURL($_POST['accept-url']) != 10) { $aMsgCds[$obj_Validator->valURL($_POST['accept-url']) + 90] = $_POST['accept-url']; }
	if ($obj_Validator->valURL($_POST['cancel-url']) != 10) { $aMsgCds[$obj_Validator->valURL($_POST['cancel-url']) + 100] = $_POST['cancel-url']; }
	if ($obj_Validator->valURL($_POST['callback-url']) != 10) { $aMsgCds[$obj_Validator->valURL($_POST['callback-url']) + 110] = $_POST['callback-url']; }
	if ($obj_Validator->valLanguage($_POST['language']) != 10) { $aMsgCds[$obj_Validator->valLanguage($_POST['language']) + 130] = $_POST['language']; }
	/* ========== Input Validation End ========== */
	
	// Success: Input Valid
	if (count($aMsgCds) == 0)
	{
		try
		{
			// Update Transaction State
			$_POST['typeid'] = Constants::iWEB_PURCHASE_TYPE;
			$_POST['gomobileid'] = -1;
			$obj_mPoint->newMessage($iTxnID, Constants::iINPUT_VALID_STATE, var_export($_POST, true) );
			// Update Transaction Log
			$_SESSION['obj_TxnInfo'] = TxnInfo::produceInfo($iTxnID, $obj_ClientConfig, $_POST);
			$obj_mPoint->logTransaction($_SESSION['obj_TxnInfo']);
			// Log additional data
			$obj_mPoint->logClientVars($_POST);
			
			// Client is using the Physical Product Flow, ensure Shop has been Configured
			if ($_SESSION['obj_TxnInfo']->getClientConfig()->getFlowID() == Constants::iPHYSICAL_FLOW)
			{
				$_SESSION['obj_ShopConfig'] = ShopConfig::produceConfig($_OBJ_DB, $_SESSION['obj_TxnInfo']->getClientConfig() );
			}
			
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
	$aMsgCds[Validate::valBasic($_OBJ_DB, $_POST['clientid'], $_POST['account'])+10] = "Client: ". $_POST['clientid'] .", Account: ". $_POST['account'];
}

// Instantiate data object with the User Agent Profile for the customer's mobile device.
$_SESSION['obj_UA'] = UAProfile::produceUAProfile();

// Success: Construct "Select Credit Card" page
if (array_key_exists(1000, $aMsgCds) === true)
{
	// Start Shop Flow
	if ($_SESSION['obj_TxnInfo']->getClientConfig()->getFlowID() == Constants::iPHYSICAL_FLOW)
	{
		$_SESSION['obj_Info']->setInfo("order_cost", $_SESSION['obj_TxnInfo']->getAmount() );
		
		header("Location: http://". $_SERVER['HTTP_HOST'] ."/shop/delivery.php?". session_name() ."=". session_id() );
	}
	// Start Payment Flow
	else { header("Location: http://". $_SERVER['HTTP_HOST'] ."/pay/card.php?". session_name() ."=". session_id() ); }
}
// Error: Construct Status Page
else
{
	$_GET['msg'] = array_keys($aMsgCds);
	
	$xml = '<?xml version="1.0" encoding="ISO-8859-15"?>';
	$xml .= '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/'. General::getMarkupLanguage($_SESSION['obj_UA']) .'/status.xsl"?>';
	$xml .= '<root>';
	$xml .= $obj_mPoint->getMessages("Status");
	$xml .= '</root>';
	
	// Display page
	echo $xml;
}
?>