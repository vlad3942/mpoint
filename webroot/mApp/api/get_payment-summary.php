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
 * @subpackage MobileApp
 * @version 1.10
 */

// Require Global Include File
require_once("../../inc/include.php");

// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");

// Require data class for Payment Service Provider Configurations
require_once(sCLASS_PATH ."/pspconfig.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require Business logic for the Select Credit Card component
require_once(sCLASS_PATH ."/credit_card.php");
// Require data data class for Customer Information
require_once(sCLASS_PATH ."/customer_info.php");

// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for the CPM PSP component
require_once(sINTERFACE_PATH ."/cpm_psp.php");
// Require specific Business logic for the VISA checkout component
require_once(sCLASS_PATH ."/visacheckout.php");
// Require specific Business logic for the Apple Pay component
require_once(sCLASS_PATH ."/applepay.php");
// Require specific Business logic for the MasterPass component
require_once(sCLASS_PATH ."/masterpass.php");

ignore_user_abort(true);
set_time_limit(120);

$aMsgCds = array();

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );
/*
$_SERVER['PHP_AUTH_USER'] = "CPMDemoWallet";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<get-payment-summary client-id="10007" account = "100007" >';
$HTTP_RAW_POST_DATA .= '<transaction id="1810882">';
$HTTP_RAW_POST_DATA .= '<card type-id="16">';
$HTTP_RAW_POST_DATA .= '<token>7627314586497464001</token>';
$HTTP_RAW_POST_DATA .= '</card>';
$HTTP_RAW_POST_DATA .= '</transaction>';
$HTTP_RAW_POST_DATA .= '</get-payment-summary>';
$HTTP_RAW_POST_DATA .= '</root>';
*/

$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mpoint.xsd") === true && count($obj_DOM->{'get-payment-summary'}) > 0)
	{
		$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);

		$xml = '';
		for ($i=0; $i<count($obj_DOM->{'get-payment-summary'}); $i++)
		{
			// Set Global Defaults
			if (empty($obj_DOM->{'get-payment-summary'}[$i]["account"]) === true || intval($obj_DOM->{'get-payment-summary'}[$i]["account"]) < 1) { $obj_DOM->{'get-payment-summary'}[$i]["account"] = -1; }
			
			// Validate basic information
			$code = Validate::valBasic($_OBJ_DB, (integer) $obj_DOM->{'get-payment-summary'}[$i]["client-id"], (integer) $obj_DOM->{'get-payment-summary'}[$i]["account"]);
			if ($code == 100)
			{
				$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'get-payment-summary'}[$i]["client-id"], (integer) $obj_DOM->{'get-payment-summary'}[$i]["account"]);
				// Client successfully authenticated
 				if ($obj_ClientConfig->getUsername() == trim($_SERVER['PHP_AUTH_USER']) && $obj_ClientConfig->getPassword() == trim($_SERVER['PHP_AUTH_PW'])
 					&& $obj_ClientConfig->hasAccess($_SERVER['REMOTE_ADDR']) === true)
				{
					$obj_TxnInfo = TxnInfo::produceInfo( (integer) $obj_DOM->{'get-payment-summary'}[$i]->transaction["id"], $_OBJ_DB);
					// Re-Intialise Text Translation Object based on transaction
					$_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH . $obj_TxnInfo->getLanguage() ."/global.txt", sLANGUAGE_PATH . $obj_TxnInfo->getLanguage() ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");
					$obj_mPoint = new EndUserAccount($_OBJ_DB, $_OBJ_TXT, $obj_ClientConfig);
					for ($j=0; $j<count($obj_DOM->{'get-payment-summary'}[$i]->transaction->card); $j++)
					{
						if(count($obj_DOM->{'get-payment-summary'}[$i]->transaction->card[$j]->token) == 1  || count($obj_DOM->{'get-payment-summary'}[$i]->transaction->card[$j]->cryptogram) == 1 )		
						{
							switch (intval($obj_DOM->{'get-payment-summary'}[$i]->transaction->card[$j]["type-id"]) )
							{
							case (Constants::iAPPLE_PAY):
								$obj_Wallet = new ApplePay($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO["apple-pay"]);
								$obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_ClientConfig->getID(), $obj_ClientConfig->getAccountConfig()->getID(), Constants::iAPPLE_PAY_PSP);
								break;
							case (Constants::iVISA_CHECKOUT_WALLET):
								$obj_Wallet = new VisaCheckout($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO["visa-checkout"]);
								$obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_ClientConfig->getID(), $obj_ClientConfig->getAccountConfig()->getID(), Constants::iVISA_CHECKOUT_PSP);
								break;
							case (Constants::iMASTER_PASS_WALLET):
								$obj_Wallet = new MasterPass($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO["masterpass"]);
								$obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_ClientConfig->getID(), $obj_ClientConfig->getAccountConfig()->getID(), Constants::iMASTER_PASS_PSP);
								break;
							default:
								break;
							}
							$obj_XML = simpledom_load_string($obj_Wallet->getPaymentData($obj_PSPConfig, $obj_DOM->{'get-payment-summary'}[$i]->transaction->card[$j], Constants::sPAYMENT_DATA_SUMMARY) );
							
							if (count($obj_XML->{'payment-data'}) == 1)
							{
								$sXML = str_replace('<?xml version="1.0"?>', '', $obj_XML->{'payment-data'}->card->asXML() );								
							}
							else if(count($obj_XML->status) == 1)
							{
								$sXML - str_replace('<?xml version="1.0"?>', '', $obj_XML->status->asXML() );
							}
							 $xml .= $sXML;
						}
						else
						{
							header("HTTP/1.1 400 Bad Request");

							$xml = '<status code="400">Missing data in the request</status>';
						}
					}
				}
				else
				{
					header("HTTP/1.1 401 Unauthorized");
	
					$xml = '<status code="401">Username / Password doesn\'t match</status>';
				}
			}
			else
			{
				header("HTTP/1.1 400 Bad Request");

				$xml = '<status code="'. $code .'">Client ID / Account doesn\'t match</status>';
			}
		}
	}
	// Error: Invalid XML Document
	elseif ( ($obj_DOM instanceof SimpleDOMElement) === false)
	{
		header("HTTP/1.1 415 Unsupported Media Type");

		$xml = '<status code="415">Invalid XML Document</status>';
	}
	// Error: Wrong operation
	elseif (count($obj_DOM->{'get-payment-summary'}) == 0)
	{
		header("HTTP/1.1 400 Bad Request");

		$xml = '';
		foreach ($obj_DOM->children() as $obj_Elem)
		{
			$xml .= '<status code="400">Wrong operation: '. $obj_Elem->getName() .'</status>';
		}
	}
	// Error: Invalid Input
	else
	{
		header("HTTP/1.1 400 Bad Request");
		$aObj_Errs = libxml_get_errors();

		$xml = '';
		for ($i=0; $i<count($aObj_Errs); $i++)
		{
			$xml .= '<status code="400">'. htmlspecialchars($aObj_Errs[$i]->message, ENT_NOQUOTES) .'</status>';
		}
	}
}
else
{
	header("HTTP/1.1 401 Unauthorized");

	$xml = '<status code="401">Authorization required</status>';
}
header("Content-Type: text/xml; charset=\"UTF-8\"");

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<root>';
echo $xml;
echo '</root>';
?>