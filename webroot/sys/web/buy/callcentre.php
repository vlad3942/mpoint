<?php
/**
 * This files contains the Controller for mPoint's Call Centre API.
 * The Controller will ensure that all input from the Call Centre is validated and a WAP Link for the started transaction is sent to the Recipient.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package API
 * @subpackage CallCentre
 * @version 1.10
 */

// Require Global Include File
require_once("../include.php");

// Require the PHP API for handling the connection to GoMobile
require_once(sAPI_CLASS_PATH ."/gomobile.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require Business logic for the Mobile Web module
require_once(sCLASS_PATH ."/mobile_web.php");
// Require Business logic for the SMS Purchase module
require_once(sCLASS_PATH ."/sms_purchase.php");
// Require Business logic for the Call Centre module
require_once(sCLASS_PATH ."/callcentre.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");

$aMsgCds = array();

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );

// Set Global Defaults
if (array_key_exists("account", $_REQUEST) === false) { $_REQUEST['account'] = -1; }
if (array_key_exists("email", $_REQUEST) === false) { $_REQUEST['email'] = ""; }
settype($_REQUEST['prod-names'], "array");
settype($_REQUEST['prod-quantities'], "array");
settype($_REQUEST['prod-prices'], "array");
if (array_key_exists("prod-logos", $_REQUEST) === false) { $_REQUEST['prod-logos'] = array(); }
else { settype($_REQUEST['prod-logos'], "array"); }
if (array_key_exists("orderid", $_REQUEST) === false) { $_REQUEST['orderid'] = null; }

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

	$obj_mPoint = new CallCentre($_OBJ_DB, $_OBJ_TXT, $obj_ClientConfig);
	$iTxnID = $obj_mPoint->newTransaction(Constants::iPURCHASE_VIA_CALL_CENTRE);

	/* ========== Input Validation Start ========== */
	$obj_Validator = new Validate($obj_ClientConfig->getCountryConfig() );

	if ($obj_Validator->valMobile($_REQUEST['mobile']) != 10) { $aMsgCds[$obj_Validator->valMobile($_REQUEST['mobile']) + 30] = $_REQUEST['mobile']; }
	if ($obj_Validator->valOperator($_REQUEST['operator']) != 10) { $aMsgCds[$obj_Validator->valOperator($_REQUEST['operator']) + 40] = $_REQUEST['operator']; }
	// Calculate Total Amount from Product Prices
	$_REQUEST['amount'] = 0;
	while (list(, $price) = each($_REQUEST['prod-prices']) )
	{
		$_REQUEST['amount'] += $price;
	}
	if ($obj_Validator->valPrice($obj_ClientConfig->getMaxAmount(), $_REQUEST['amount']) != 10) { $aMsgCds[$obj_Validator->valPrice($obj_ClientConfig->getMaxAmount(), $_REQUEST['amount']) + 50] = $_REQUEST['amount']; }
	// Validate Product Data
	if ($obj_Validator->valProducts($_REQUEST['prod-names'], $_REQUEST['prod-quantities'], $_REQUEST['prod-prices'], $_REQUEST['prod-logos']) != 10)
	{
		$debug = "";
		$debug .= "prod-names: ". var_export($_REQUEST['prod-names'], true) ."\n";
		$debug .= "prod-quantities: ". var_export($_REQUEST['prod-quantities'], true) ."\n";
		$debug .= "prod-prices: ". var_export($_REQUEST['prod-prices'], true) ."\n";
		$debug .= "prod-logos: ". var_export($_REQUEST['prod-logos'], true);

		$aMsgCds[$obj_Validator->valProducts($_REQUEST['prod-names'], $_REQUEST['prod-quantities'], $_REQUEST['prod-prices'], $_REQUEST['prod-logos']) + 60] = $debug;
	}
	// Validate URLs
	if ($obj_Validator->valURL($_REQUEST['logo-url']) != 10) { $aMsgCds[$obj_Validator->valURL($_REQUEST['logo-url']) + 70] = $_REQUEST['logo-url']; }
	if ($obj_Validator->valURL($_REQUEST['css-url']) != 10) { $aMsgCds[$obj_Validator->valURL($_REQUEST['css-url']) + 80]= $_REQUEST['css-url']; }
	if ($obj_Validator->valURL($_REQUEST['accept-url']) > 1 && $obj_Validator->valURL($_REQUEST['accept-url']) != 10) { $aMsgCds[$obj_Validator->valURL($_REQUEST['accept-url']) + 90] = $_REQUEST['accept-url']; }
	if ($obj_Validator->valURL($_REQUEST['cancel-url']) > 1 && $obj_Validator->valURL($_REQUEST['cancel-url']) != 10) { $aMsgCds[$obj_Validator->valURL($_REQUEST['cancel-url']) + 100] = $_REQUEST['cancel-url']; }
	if ($obj_Validator->valURL($_REQUEST['callback-url']) != 10) { $aMsgCds[$obj_Validator->valURL($_REQUEST['callback-url']) + 110] = $_REQUEST['callback-url']; }
	if (array_key_exists("return-url", $_REQUEST) === true && $obj_Validator->valURL($_REQUEST['return-url']) != 10) { $aMsgCds[$obj_Validator->valURL($_REQUEST['return-url']) + 120] = $_REQUEST['return-url']; }
	if ($obj_Validator->valLanguage($_REQUEST['language']) != 10) { $aMsgCds[$obj_Validator->valLanguage($_REQUEST['language']) + 130] = $_REQUEST['language']; }
	if ($obj_Validator->valEMail($_REQUEST['email']) != 1 && $obj_Validator->valEMail($_REQUEST['email']) != 10) { $aMsgCds[$obj_Validator->valEMail($_REQUEST['email']) + 140] = $_REQUEST['email']; }
	/* ========== Input Validation End ========== */

	// Success: Input Valid
	if (count($aMsgCds) == 0)
	{
		try
		{
			// Update Transaction State
			$_REQUEST['typeid'] = Constants::iPURCHASE_VIA_CALL_CENTRE;
			$_REQUEST['gomobileid'] = -1;
			$obj_mPoint->newMessage($iTxnID, Constants::iINPUT_VALID_STATE, var_export($_REQUEST, true) );

			$obj_TxnInfo = TxnInfo::produceInfo($iTxnID,$_OBJ_DB, $obj_ClientConfig, $_REQUEST);
			// Associate End-User Account (if exists) with Transaction
			$iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_TxnInfo->getMobile(), false);
			if ($iAccountID == -1 && trim($_SESSION['obj_TxnInfo']->getEMail() ) != "") { $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_TxnInfo->getEMail(), false); }
			$obj_TxnInfo->setAccountID($iAccountID);
						
			// Update Transaction Log
			$obj_mPoint->logTransaction($obj_TxnInfo);
			// Log additional data
			$obj_mPoint->logProducts($_REQUEST['prod-names'], $_REQUEST['prod-quantities'], $_REQUEST['prod-prices'], $_REQUEST['prod-logos']);
			$obj_mPoint->logClientVars($_REQUEST);

			// Client is using the Physical Product Flow, ensure Shop has been Configured
			if ($obj_ClientConfig->getFlowID() == Constants::iPHYSICAL_FLOW)
			{
				$obj_ShopConfig = ShopConfig::produceConfig($_OBJ_DB, $obj_ClientConfig);
			}
			// Construct and send mPoint link for Payment Module
			$sURL = $obj_mPoint->constLink($obj_TxnInfo->getID(), $_REQUEST['operator'], "pay");
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
	$aMsgCds[Validate::valBasic($_OBJ_DB, $_REQUEST['clientid'], $_REQUEST['account'])+10] = "Client: ". $_REQUEST['clientid'] .", Account: ". $_REQUEST['account'];
}

// Redirect to Return URL
if (array_key_exists("return-url", $_REQUEST) === true)
{
	$sQS = "";
	foreach ($aMsgCds as $state => $debug)
	{
		$sQS .= "&msg=". $state;
	}
	// Return URL already has Query String
	if (strstr($_REQUEST['return-url'], "?") == false) { $sQS = "?". substr($sQS, 1); }
	// Return URL has an anchor part
	if (strstr($_REQUEST['return-url'], "#") == true)
	{
		$sQS .= substr($_REQUEST['return-url'], strpos($_REQUEST['return-url'], "#")+1);
		$_REQUEST['return-url'] = substr($_REQUEST['return-url'], 0, strpos($_REQUEST['return-url'], "#") );
	}

	header("location: ". $_REQUEST['return-url'] . $sQS);
}
// Display Status Page
else
{
	$_GET['msg'] = array_keys($aMsgCds);

	$xml = '<?xml version="1.0" encoding="UTF-8"?>';
	$xml .= '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/xhtml/status.xsl"?>';
	$xml .= '<root>';
	$xml .= $obj_mPoint->getSystemInfo($aHTTP_CONN_INFO["hpp"]["protocol"]);
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