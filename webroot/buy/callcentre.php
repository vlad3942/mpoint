<?php
/**
 * This files contains the Controller for mPoint's Call Centre API.
 * The Controller will ensure that all input from the Call Centre is validated and a WAP Link for the started transaction is sent to the Recipient.
 * 
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package CallCentre
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
// Require Business logic for the SMS Purchase module
require_once(sCLASS_PATH ."/sms_purchase.php");
// Require Business logic for the Call Centre module
require_once(sCLASS_PATH ."/callcentre.php");

$aMsgCds = array();

// Set Global Defaults
if (array_key_exists("account", $_POST) === false) { $_POST['account'] = -1; }
settype($_POST['prod-names'], "array");
settype($_POST['prod-quantities'], "array");
settype($_POST['prod-prices'], "array");
if (array_key_exists("prod-logos", $_POST) === false) { $_POST['prod-logos'] = array(); }
else { settype($_POST['prod-logos'], "array"); }
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
	
	$obj_mPoint = new CallCentre($_OBJ_DB, $_OBJ_TXT, $obj_ClientConfig);
	$iTxnID = $obj_mPoint->newTransaction(Constants::iCALL_CENTRE_PURCHASE_TYPE);
	
	/* ========== Input Validation Start ========== */
	$obj_Validator = new Validate($obj_ClientConfig);
	
	if ($obj_Validator->valAddress($_POST['recipient']) != 10) { $aMsgCds[$obj_Validator->valAddress($_POST['recipient']) + 30] = $_POST['recipient']; }
	if ($obj_Validator->valOperator($_POST['operator']) != 10) { $aMsgCds[$obj_Validator->valOperator($_POST['operator']) + 40] = $_POST['operator']; }
	// Calculate Total Amount from Product Prices
	$_POST['amount'] = 0;
	while (list(, $price) = each($_POST['prod-prices']) )
	{
		$_POST['amount'] += $price;
	}
	if ($obj_Validator->valAmount($_POST['amount']) != 10) { $aMsgCds[$obj_Validator->valAmount($_POST['amount']) + 50] = $_POST['amount']; }
	// Validate Product Data
	if ($obj_Validator->valProducts($_POST['prod-names'], $_POST['prod-quantities'], $_POST['prod-prices'], $_POST['prod-logos']) != 10)
	{
		$debug = "";
		$debug .= "prod-names: ". var_export($_POST['prod-names'], true) ."\n";
		$debug .= "prod-quantities: ". var_export($_POST['prod-quantities'], true) ."\n";
		$debug .= "prod-prices: ". var_export($_POST['prod-prices'], true) ."\n";
		$debug .= "prod-logos: ". var_export($_POST['prod-logos'], true);
		
		$aMsgCds[$obj_Validator->valProducts($_POST['prod-names'], $_POST['prod-quantities'], $_POST['prod-prices'], $_POST['prod-logos']) + 60] = $debug;
	}
	// Validate URLs
	if ($obj_Validator->valURL($_POST['logo-url']) != 10) { $aMsgCds[$obj_Validator->valURL($_POST['logo-url']) + 70] = $_POST['logo-url']; }
	if ($obj_Validator->valURL($_POST['css-url']) != 10) { $aMsgCds[$obj_Validator->valURL($_POST['css-url']) + 80]= $_POST['css-url']; }
	if ($obj_Validator->valURL($_POST['accept-url']) != 10) { $aMsgCds[$obj_Validator->valURL($_POST['accept-url']) + 90] = $_POST['accept-url']; }
	if ($obj_Validator->valURL($_POST['cancel-url']) != 10) { $aMsgCds[$obj_Validator->valURL($_POST['cancel-url']) + 100] = $_POST['cancel-url']; }
	if ($obj_Validator->valURL($_POST['callback-url']) != 10) { $aMsgCds[$obj_Validator->valURL($_POST['callback-url']) + 110] = $_POST['callback-url']; }
	if (array_key_exists("return-url", $_POST) === true && $obj_Validator->valURL($_POST['return-url']) != 10) { $aMsgCds[$obj_Validator->valURL($_POST['return-url']) + 120] = $_POST['return-url']; }
	if ($obj_Validator->valLanguage($_POST['language']) != 10) { $aMsgCds[$obj_Validator->valLanguage($_POST['language']) + 130] = $_POST['language']; }
	/* ========== Input Validation End ========== */
	
	// Success: Input Valid
	if (count($aMsgCds) == 0)
	{
		try
		{
			// Update Transaction State
			$_POST['typeid'] = Constants::iCALL_CENTRE_PURCHASE_TYPE;
			$_POST['gomobileid'] = -1;
			$obj_mPoint->newMessage($iTxnID, Constants::iINPUT_VALID_STATE, var_export($_POST, true) );
			// Update Transaction Log
			$obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $obj_ClientConfig, $_POST);
			$obj_mPoint->logTransaction($obj_TxnInfo);
			// Log additional data
			$obj_mPoint->logProducts($_POST['prod-names'], $_POST['prod-quantities'], $_POST['prod-prices'], $_POST['prod-logos']);
			$obj_mPoint->logClientVars($_POST);
			
			// Client is using the Physical Product Flow, ensure Shop has been Configured
			if ($obj_ClientConfig->getFlowID() == Constants::iPHYSICAL_FLOW)
			{
				$obj_ShopConfig = ShopConfig::produceConfig($_OBJ_DB, $obj_ClientConfig);
			}
			// Construct and send mPoint link for Payment Module
			$sURL = $obj_mPoint->constLink($_POST['operator'], "pay");
			$obj_mPoint->sendLink(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO), $obj_TxnInfo, $sURL);
			
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
	$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);
	$aMsgCds[Validate::valBasic($_OBJ_DB, $_POST['clientid'], $_POST['account'])+10] = "Client: ". $_POST['clientid'] .", Account: ". $_POST['account'];
}

// Redirect to Return URL
if (array_key_exists("return-url", $_POST) === true)
{
	$sQS = "";
	foreach ($aMsgCds as $state => $debug)
	{
		$sQS .= "&msg=". $state;
	}
	// Return URL already has Query String
	if (strstr($_POST['return-url'], "?") == false) { $sQS = "?". substr($sQS, 1); }
	// Return URL has an anchor part
	if (strstr($_POST['return-url'], "#") == true)
	{
		$sQS .= substr($_POST['return-url'], strpos($_POST['return-url'], "#")+1);
		$_POST['return-url'] = substr($_POST['return-url'], 0, strpos($_POST['return-url'], "#") );
	}
	
	header("location: ". $_POST['return-url'] . $sQS);
}
// Display Status Page
else
{
	$_GET['msg'] = array_keys($aMsgCds);
	
	$xml = '<?xml version="1.0" encoding="ISO-8859-15"?>';
	$xml .= '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/xhtml/status.xsl"?>';
	$xml .= '<root>';
	$xml .= $obj_mPoint->getSystemInfo();
	if (isset($obj_ClientConfig) === true)
	{
		$xml .= $obj_ClientConfig->toXML();
	}
	$xml .= $obj_mPoint->getMessages("Status");
	$xml .= '</root>';
	
	header("content-type: text/xml; charset=ISO-8859-15");
	header("content-length: ". strlen($xml) );
	
	echo $xml;
}
?>