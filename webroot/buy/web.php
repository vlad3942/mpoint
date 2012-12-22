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
 * @subpackage MobileWeb
 * @version 1.10
 */

// Require Global Include File
require_once("../inc/include.php");

// Require the PHP API for handling the connection to GoMobile
require_once(sAPI_CLASS_PATH ."/gomobile.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require Business logic for the Mobile Web module
require_once(sCLASS_PATH ."/mobile_web.php");
// Require Business logic for the Select Credit Card component
require_once(sCLASS_PATH ."/credit_card.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");

$aMsgCds = array();

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );

// Set Global Defaults
if (array_key_exists("account", $_REQUEST) === false || intval($_REQUEST['account']) <= 0) { $_REQUEST['account'] = -1; }
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
	if (array_key_exists("markup", $_REQUEST) === false) { $_REQUEST['markup'] = $obj_ClientConfig->getAccountConfig()->getMarkupLanguage(); }
	
	$obj_mPoint = new MobileWeb($_OBJ_DB, $_OBJ_TXT, $obj_ClientConfig);
	$iTxnID = $obj_mPoint->newTransaction(Constants::iPURCHASE_VIA_WEB);

	/* ========== Input Validation Start ========== */
	$obj_Validator = new Validate($obj_ClientConfig->getCountryConfig() );

	if ($obj_Validator->valMobile($_REQUEST['mobile']) != 10 && $obj_ClientConfig->smsReceiptEnabled() === true) { $aMsgCds[$obj_Validator->valMobile($_REQUEST['mobile']) + 30] = $_REQUEST['mobile']; }
	if ($obj_Validator->valOperator($_REQUEST['operator']) != 10) { $aMsgCds[$obj_Validator->valOperator($_REQUEST['operator']) + 40] = $_REQUEST['operator']; }
	if ($obj_Validator->valPrice($obj_ClientConfig->getMaxAmount(), $_REQUEST['amount']) != 10) { $aMsgCds[$obj_Validator->valPrice($obj_ClientConfig->getMaxAmount(), $_REQUEST['amount']) + 50] = $_REQUEST['amount']; }
	// Validate URLs
	if ($obj_Validator->valURL($_REQUEST['logo-url']) > 1 && $obj_Validator->valURL($_REQUEST['logo-url']) != 10) { $aMsgCds[$obj_Validator->valURL($_REQUEST['logo-url']) + 70] = $_REQUEST['logo-url']; }
	if ($obj_Validator->valURL($_REQUEST['css-url']) != 10) { $aMsgCds[$obj_Validator->valURL($_REQUEST['css-url']) + 80]= $_REQUEST['css-url']; }
	if ($obj_Validator->valURL($_REQUEST['accept-url']) > 1 && $obj_Validator->valURL($_REQUEST['accept-url']) != 10) { $aMsgCds[$obj_Validator->valURL($_REQUEST['accept-url']) + 90] = $_REQUEST['accept-url']; }
	if ($obj_Validator->valURL($_REQUEST['cancel-url']) > 1 && $obj_Validator->valURL($_REQUEST['cancel-url']) != 10) { $aMsgCds[$obj_Validator->valURL($_REQUEST['cancel-url']) + 100] = $_REQUEST['cancel-url']; }
	if ($obj_Validator->valURL($_REQUEST['callback-url']) != 10) { $aMsgCds[$obj_Validator->valURL($_REQUEST['callback-url']) + 110] = $_REQUEST['callback-url']; }
	if ($obj_Validator->valLanguage($_REQUEST['language']) != 10) { $aMsgCds[$obj_Validator->valLanguage($_REQUEST['language']) + 130] = $_REQUEST['language']; }
	if ($obj_Validator->valEMail($_REQUEST['email']) != 1 && $obj_Validator->valEMail($_REQUEST['email']) != 10) { $aMsgCds[$obj_Validator->valEMail($_REQUEST['email']) + 140] = $_REQUEST['email']; }
	if ($obj_Validator->valURL($_REQUEST['icon-url']) > 1 && $obj_Validator->valURL($_REQUEST['icon-url']) != 10) { $aMsgCds[$obj_Validator->valURL($_REQUEST['icon-url']) + 160] = $_REQUEST['icon-url']; }
	if ($obj_Validator->valMarkupLanguage($_REQUEST['markup']) != 10) { $aMsgCds[$obj_Validator->valMarkupLanguage($_REQUEST['markup']) + 190] = $_REQUEST['markup']; }
	/* ========== Input Validation End ========== */

	// Success: Input Valid
	if (count($aMsgCds) == 0)
	{
		try
		{
			// Update Transaction State
			$_REQUEST['typeid'] = Constants::iPURCHASE_VIA_WEB;
			$_REQUEST['gomobileid'] = -1;
			$obj_mPoint->newMessage($iTxnID, Constants::iINPUT_VALID_STATE, var_export($_REQUEST, true) );

			$_SESSION['obj_TxnInfo'] = TxnInfo::produceInfo($iTxnID, $obj_ClientConfig, $_REQUEST);
			// Associate End-User Account (if exists) with Transaction
			$iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $_SESSION['obj_TxnInfo']->getMobile() );
			if ($iAccountID == -1 && trim($_SESSION['obj_TxnInfo']->getEMail() ) != "") { $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $_SESSION['obj_TxnInfo']->getEMail() ); }
			// Client supports global storage of payment cards
			if ($iAccountID == -1 && $obj_ClientConfig->getStoreCard() > 3)
			{
				$iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_TxnInfo->getMobile(), false);
				if ($iAccountID == -1 && trim($obj_TxnInfo->getEMail() ) != "") { $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_TxnInfo->getEMail(), false); }
			}
			$_SESSION['obj_TxnInfo']->setAccountID($iAccountID);
			
			// Update Transaction Log
			$obj_mPoint->logTransaction($_SESSION['obj_TxnInfo']);
			// Log additional data
			$obj_mPoint->logClientVars($_REQUEST);

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
	$aMsgCds[Validate::valBasic($_OBJ_DB, $_REQUEST['clientid'], $_REQUEST['account'])+10] = "Client: ". $_REQUEST['clientid'] .", Account: ". $_REQUEST['account'];
}

// Instantiate data object with the User Agent Profile for the customer's mobile device.
$_SESSION['obj_UA'] = UAProfile::produceUAProfile(HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["iemendo"]) );

// Success: Construct "Select Credit Card" page
if (array_key_exists(1000, $aMsgCds) === true)
{
	unset($_SESSION['temp']);
	// Start Shop Flow
	if ($_SESSION['obj_TxnInfo']->getClientConfig()->getFlowID() == Constants::iPHYSICAL_FLOW)
	{
		$_SESSION['obj_Info']->setInfo("order_cost", $_SESSION['obj_TxnInfo']->getAmount() );

		header("Location: /shop/delivery.php?". session_name() ."=". session_id() );
	}
	// Start Payment Flow
	else
	{
		$iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $_SESSION['obj_TxnInfo']->getMobile() );
		if ($iAccountID == -1 && trim($_SESSION['obj_TxnInfo']->getEMail() ) != "") { $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $_SESSION['obj_TxnInfo']->getEMail() ); }
		
		// End-User already has an account that is linked to the Client
		if ($iAccountID > 0)
		{
			$obj_mPoint = new CreditCard($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo'], $_SESSION['obj_UA']);
			$obj_XML = simplexml_load_string($obj_mPoint->getCards($_SESSION['obj_TxnInfo']->getAmount() ) );
			$obj_CardsXML = simplexml_load_string($obj_mPoint->getStoredCards($_SESSION['obj_TxnInfo']->getAccountID() ) );
			
			/*
			 * Only prepaid account available or End-User already has an e-money based prepaid account or a stored card
			 * Go to step 2: My Account
			 */
			if (count($obj_XML->xpath("/cards[item/@id = 11]") ) > 0 && (count($obj_XML->item) == 1
				|| count($obj_CardsXML->xpath("/stored-cards/card[client/@id = ". $_SESSION['obj_TxnInfo']->getClientConfig()->getID() ."]") ) > 0) )
			{
				header("Location: /cpm/payment.php?". session_name() ."=". session_id() ."&cardtype=11");
			}
			// Go to step 1: Select payment method 
			else { header("Location: /pay/card.php?". session_name() ."=". session_id() ); }
		}
		// Go to step 1: Select payment method
		else { header("Location: /pay/card.php?". session_name() ."=". session_id() ); }
	}
}
// Error: Construct Status Page
else
{
	$s = date("Y-m-d H:i:s") ."\n";
	$s .= "REQUEST: " ."\n". var_export($_REQUEST) ."\n";
	$s .= "ERRORS: " ."\n". var_export($aMsgCds) ."\n";
	file_put_contents(sLOG_PATH ."/debug_". date("Y-m-d") .".log", $s);
	
	$_GET['msg'] = array_keys($aMsgCds);
	
	$xml = '<?xml version="1.0" encoding="UTF-8"?>';
	$xml .= '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/xhtml/status.xsl"?>';
	$xml .= '<root>';
	$xml .= $obj_mPoint->getMessages("Status");
	$xml .= '</root>';

	// Display page
	echo $xml;
}
?>