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
 * @subpackage SMS
 * @version 1.10
 */

// Require Global Include File
require_once("/include.php");

// Require the PHP API for handling the connection to GoMobile
require_once(sAPI_CLASS_PATH ."/gomobile.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require Business logic for the Mobile Web module
require_once(sCLASS_PATH ."/mobile_web.php");
// Require Business logic for the SMS Purchase module
require_once(sCLASS_PATH ."/sms_purchase.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");

$aMsgCds = array();

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );

// Set Global Defaults
if (array_key_exists("account", $_REQUEST) === false) { $_REQUEST['account'] = -1; }
if (array_key_exists("orderid", $_REQUEST) === false) { $_REQUEST['orderid'] = null; }
if (array_key_exists("email", $_REQUEST) === false) { $_REQUEST['email'] = ""; }
$_REQUEST['logo-url'] = "N/A";
$_REQUEST['css-url'] = "N/A";
$_REQUEST['accept-url'] = "N/A";
$_REQUEST['cancel-url'] = "N/A";

$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);

// Validate basic information
if (Validate::valBasic($_OBJ_DB, $_REQUEST['clientid'], $_REQUEST['account']) == 100)
{
	$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, $_REQUEST['clientid'], $_REQUEST['account']);

	// Set Client Defaults
	if (array_key_exists("operator", $_REQUEST) === false) { $_REQUEST['operator'] = $obj_ClientConfig->getCountryConfig()->getID() * 100; }	
	if (array_key_exists("callback-url", $_REQUEST) === false) { $_REQUEST['callback-url'] = $obj_ClientConfig->getCallbackURL(); }
	if (array_key_exists("icon-url", $_REQUEST) === false) { $_REQUEST['icon-url'] = $obj_ClientConfig->getIconURL(); }
	if (array_key_exists("language", $_REQUEST) === false) { $_REQUEST['language'] = $obj_ClientConfig->getLanguage(); }
	
	$obj_mPoint = new SMS_Purchase($_OBJ_DB, $_OBJ_TXT, $obj_ClientConfig);
	$iTxnID = $obj_mPoint->newTransaction(Constants::iPURCHASE_VIA_SMS);

	/* ========== Input Validation Start ========== */
	$obj_Validator = new Validate($obj_ClientConfig->getCountryConfig() );

	if ($obj_Validator->valMobile($_REQUEST['mobile']) != 10) { $aMsgCds[$obj_Validator->valMobile($_REQUEST['mobile']) + 30] = $_REQUEST['mobile']; }
	if ($obj_Validator->valOperator($_REQUEST['operator']) != 10) { $aMsgCds[$obj_Validator->valOperator($_REQUEST['operator']) + 40] = $_REQUEST['operator']; }
	
	if ($obj_ClientConfig->getMaxAmount() < $obj_ClientConfig->getCountryConfig()->getMaxPSMSAmount() ) {  $iMaxAmount = $obj_ClientConfig->getMaxAmount(); }
	else { $iMaxAmount = $obj_ClientConfig->getCountryConfig()->getMaxPSMSAmount(); }
	$code = $obj_Validator->valPrice($iMaxAmount, $_REQUEST['amount']);
	if ($code != 10)
	{
		if ($obj_ClientConfig->getMaxAmount() > $obj_ClientConfig->getCountryConfig()->getMaxPSMSAmount() )
		{
			$aMsgCds[1011] = "Amount requires additional authentication, please invoke Call Centre API"; 
		}
		else { $aMsgCds[$code + 50] = $_REQUEST['amount']; }
	}
	// Validate URLs
	if ($obj_Validator->valURL($_REQUEST['callback-url']) != 10) { $aMsgCds[$obj_Validator->valURL($_REQUEST['callback-url']) + 110] = $_REQUEST['callback-url']; }
	if ($obj_Validator->valLanguage($_REQUEST['language']) != 10) { $aMsgCds[$obj_Validator->valLanguage($_REQUEST['language']) + 130] = $_REQUEST['language']; }
	if ($obj_Validator->valEMail($_REQUEST['email']) != 1 && $obj_Validator->valEMail($_REQUEST['email']) != 10) { $aMsgCds[$obj_Validator->valEMail($_REQUEST['email']) + 140] = $_REQUEST['email']; }
	
	// Verify whether Transaction is possible via SMS
	$iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, @$_REQUEST['mobile'], false);
	if ($iAccountID > 0)
	{
		$obj_AccountXML = simplexml_load_string($obj_mPoint->getAccountInfo($iAccountID) );
		// End-User's Account balance is too low to pay for Transaction
		if (intval($obj_AccountXML->balance) < $_REQUEST['amount'])
		{
			$obj_CardsXML = simplexml_load_string($obj_mPoint->getStoredCards($iAccountID, $obj_ClientConfig) );
			// End-User doesn't have any Stored Cards available for Client or System User
			if (count($obj_CardsXML->xpath("/stored-cards/card[client/@id = ". $obj_ClientConfig->getID() ." or client/@id = ". $obj_ClientConfig->getCountryConfig()->getID() ."]") ) == 0)
			{
				// Transaction cannot be charged through Premium SMS
				if ($obj_mPoint->psmsAvailable($_REQUEST['amount']) === false)
				{
					$aMsgCds[1013] = "Customer account balance too low, please invoke Call Centre API";
				}
			}
		}
	}
	// Transaction cannot be charged through Premium SMS
	elseif ($obj_mPoint->psmsAvailable($_REQUEST['amount']) === false)
	{
		$aMsgCds[1012] = "Customer doesn't have an account, please invoke Call Centre API";
	}
	/* ========== Input Validation End ========== */

	// Success: Input Valid
	if (count($aMsgCds) == 0)
	{
		try
		{
			// Update Transaction State
			$_REQUEST['typeid'] = Constants::iPURCHASE_VIA_SMS;
			$_REQUEST['gomobileid'] = -1;
			$obj_mPoint->newMessage($iTxnID, Constants::iINPUT_VALID_STATE, var_export($_REQUEST, true) );

			$obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $obj_ClientConfig, $_REQUEST);
			// Associate End-User Account (if exists) with Transaction
			$iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_TxnInfo->getMobile() );
			if ($iAccountID == -1 && trim($obj_TxnInfo->getEMail() ) != "") { $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_TxnInfo->getEMail() ); }
			$obj_TxnInfo->setAccountID($iAccountID);
			
			// Update Transaction Log
			$obj_mPoint->logTransaction($obj_TxnInfo);
			// Log additional data
			$obj_mPoint->logClientVars($_REQUEST);

			// Confirm to GoMobile that the MO-SMS has been received
			$obj_ConnInfo = GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO);
			
			$sBody = $_OBJ_TXT->_("SMS Purchase MT");
			$sBody = str_replace("{CLIENT}", $obj_ClientConfig->getName(), $sBody);
			$sBody = str_replace("{AMOUNT}", General::formatAmount($obj_ClientConfig->getCountryConfig(), $obj_TxnInfo->getAmount() ), $sBody);
			$obj_MsgInfo = GoMobileMessage::produceMessage(Constants::iMT_SMS_TYPE, $obj_ClientConfig->getCountryConfig()->getID(), $obj_TxnInfo->getOperator(), $obj_ClientConfig->getCountryConfig()->getChannel(), $obj_ClientConfig->getKeywordConfig()->getKeyword() ."_MPOINT", Constants::iMT_PRICE, $obj_TxnInfo->getMobile(), utf8_decode($sBody) );
			$obj_MsgInfo->setDescription("mPoint - SMS Purchase");
			$obj_mPoint->sendMT($obj_ConnInfo, $obj_MsgInfo, $obj_TxnInfo);

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
$str = "";
foreach (array_keys($aMsgCds) as $code)
{
	$str .= "&msg=". $code;
}
echo substr($str, 1);
?>