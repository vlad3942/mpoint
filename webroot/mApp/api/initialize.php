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
/*
$_SERVER['PHP_AUTH_USER'] = "CPMDemo";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<initialize-payment client-id="10007" account="100007">';
$HTTP_RAW_POST_DATA .= '<transaction order-no="1234abc">';
$HTTP_RAW_POST_DATA .= '<amount country-id="100">200</amount>';
$HTTP_RAW_POST_DATA .= '<callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url>';
$HTTP_RAW_POST_DATA .= '</transaction>';
$HTTP_RAW_POST_DATA .= '<client-info platform="iOS" version="5.1.1" language="da">';
$HTTP_RAW_POST_DATA .= '<mobile country-id="100" operator-id="10000">28882861</mobile>';
$HTTP_RAW_POST_DATA .= '<email>jona@oismail.com</email>';
$HTTP_RAW_POST_DATA .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
$HTTP_RAW_POST_DATA .= '</client-info>';
$HTTP_RAW_POST_DATA .= '</initialize-payment>';
$HTTP_RAW_POST_DATA .= '</root>';
*/
$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mpoint.xsd") === true && count($obj_DOM->{'initialize-payment'}) > 0)
	{	
		$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);
		
		for ($i=0; $i<count($obj_DOM->{'initialize-payment'}); $i++)
		{
			// Set Global Defaults
			if (empty($obj_DOM->{'initialize-payment'}[$i]["account"]) === true || intval($obj_DOM->{'initialize-payment'}[$i]["account"]) < 1) { $obj_DOM->{'initialize-payment'}[$i]["account"] = -1; }
		
			// Validate basic information
			if (Validate::valBasic($_OBJ_DB, (integer) $obj_DOM->{'initialize-payment'}[$i]["client-id"], (integer) $obj_DOM->{'initialize-payment'}[$i]["account"]) == 100)
			{
				$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'initialize-payment'}[$i]["client-id"], (integer) $obj_DOM->{'initialize-payment'}[$i]["account"]);
				
				if ($obj_ClientConfig->getUsername() == trim($_SERVER['PHP_AUTH_USER']) && $obj_ClientConfig->getPassword() == trim($_SERVER['PHP_AUTH_PW']) )
				{
					$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'initialize-payment'}[$i]->transaction->amount["country-id"]);
					if ( ($obj_CountryConfig instanceof CountryConfig) === false) { $obj_CountryConfig = $obj_ClientConfig->getCountryConfig(); }
					
					$obj_mPoint = new MobileWeb($_OBJ_DB, $_OBJ_TXT, $obj_ClientConfig);
					$iTxnID = $obj_mPoint->newTransaction(Constants::iAPP_PURCHASE_TYPE);
					try
					{
						// Update Transaction State
						$obj_mPoint->newMessage($iTxnID, Constants::iINPUT_VALID_STATE, $obj_DOM->asXML() );
			
						$data['typeid'] = Constants::iAPP_PURCHASE_TYPE;
						$data['amount'] = (integer) $obj_DOM->{'initialize-payment'}[$i]->transaction->amount;
						$data['gomobileid'] = -1;
						$data['orderid'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction["order-no"];
						$data['mobile'] = (float) $obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->mobile;
						$data['operator'] = (integer) $obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->mobile["operator-id"];
						$data['email'] = (string) $obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->email;
						$data['device-id'] = (string) $obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->{'device-id'};
						$data['logo-url'] = "";
						$data['css-url'] = "";
						$data['accept-url'] = "";
						$data['cancel-url'] = "";
						$data['callback-url'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->{'callback-url'};
						$data['icon-url'] = "";
						$data['language'] = $obj_DOM->{'initialize-payment'}[$i]->{'client-info'}["language"];
						$data['markup'] = "app";
						$obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $obj_ClientConfig, $data);
						// Associate End-User Account (if exists) with Transaction
						$iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_TxnInfo->getMobile(), false);
						if ($iAccountID == -1 && trim($obj_TxnInfo->getEMail() ) != "") { $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_TxnInfo->getEMail(), false); }
						$obj_TxnInfo->setAccountID($iAccountID);
						
						// Update Transaction Log
						$obj_mPoint->logTransaction($obj_TxnInfo);
						if (count($obj_DOM->{'client-variables'}) == 1 && count($obj_DOM->{'client-variables'}->children() ) > 0)
						{
							$aVars = array();
							foreach ($obj_DOM->{'client-variables'}->children() as $obj_Var)
							{
								if (substr($obj_Var->getName(), 0, 4) == "var_")
								{
									$aVars[$obj_Var->getName()] = (string) $obj_Var;
								}
								else { $aVars["var_". $obj_Var->getName()] = (string) $obj_Var; } 
							}
							// Log additional data
							$obj_mPoint->logClientVars($aVars);
						}
						$obj_mPoint = new CreditCard($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo);
						$xml = '<client-config id="'. $obj_ClientConfig->getID() .'" account="'. $obj_ClientConfig->getAccountConfig()->getID() .'" store-card="'. $obj_ClientConfig->getStoreCard() .'" auto-capture="'. General::bool2xml($obj_ClientConfig->useAutoCapture() ) .'" mode="'. $obj_ClientConfig->getMode() .'">';
						$xml .= '<name>'. htmlspecialchars($obj_ClientConfig->getName(), ENT_NOQUOTES) .'</name>';
						$xml .= '<callback-url>'. htmlspecialchars($obj_ClientConfig->getCallbackURL(), ENT_NOQUOTES) .'</callback-url>';
						$xml .= '</client-config>';
						$xml .= '<transaction id="'. $obj_TxnInfo->getID() .'" order-no="'. htmlspecialchars($obj_TxnInfo->getOrderID(), ENT_NOQUOTES) .'" type-id="'. $obj_TxnInfo->getTypeID() .'" eua-id="'. $obj_TxnInfo->getAccountID() .'" language="'. $obj_TxnInfo->getLanguage() .'" auto-capture="'. General::bool2xml($obj_TxnInfo->useAutoCapture() ) .'" mode="'. $obj_TxnInfo->getMode() .'">';
						$xml .= '<amount country-id="'. $obj_TxnInfo->getClientConfig()->getCountryConfig()->getID() .'" currency="'. $obj_TxnInfo->getClientConfig()->getCountryConfig()->getCurrency() .'" symbol="'. $obj_TxnInfo->getClientConfig()->getCountryConfig()->getSymbol() .'">'. intval($obj_TxnInfo->getAmount() ) .'</amount>';
						$xml .= '<mobile country-id="'. $obj_TxnInfo->getClientConfig()->getCountryConfig()->getID() .'" operator-id="'. $obj_TxnInfo->getOperator() .'">'. floatval($obj_TxnInfo->getMobile() ) .'</mobile>';
						$xml .= '<email>'. htmlspecialchars($obj_TxnInfo->getEMail(), ENT_NOQUOTES) .'</email>';
						$xml .= '<callback-url>'. htmlspecialchars($obj_TxnInfo->getCallbackURL(), ENT_NOQUOTES) .'</callback-url>';
						$xml .= '</transaction>';
						$obj_XML = simplexml_load_string($obj_mPoint->getCards($obj_TxnInfo->getAmount() ) );
						
						// End-User already has an account and payment with Account enabled
						if ($iAccountID > 0 && count($obj_XML->xpath("/cards/item[@type-id = 11]") ) == 1)
						{
							$obj_mPoint = new CreditCard($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo);
							$aObj_XML = simplexml_load_string($obj_mPoint->getStoredCards($iAccountID) );
							$aObj_XML = $aObj_XML->xpath("/stored-cards/card[client/@id = ". $obj_ClientConfig->getID() ."]");
						}
						else { $aObj_XML = array(); }
						$xml .= '<cards>';
						for ($j=0; $j<count($obj_XML->item); $j++)
						{
							// Card does not represent "My Account" or the End-User has an acccount with Stored Cards
							if ($obj_XML->item[$j]["type-id"] != 11 || ($iAccountID > 0 && count($aObj_XML) > 0) )
							{
								$xml .= '<card id="'. $obj_XML->item[$j]["id"] .'" type-id="'. $obj_XML->item[$j]["type-id"] .'" psp-id="'. $obj_XML->item[$j]["pspid"] .'">'. htmlspecialchars($obj_XML->item[$j]->name, ENT_NOQUOTES) .'</card>';
							}
						}
						$xml .= '</cards>';
						// End-User has Stored Cards available
						if (count($aObj_XML) > 0)
						{
							$xml .= '<stored-cards>';
							for ($j=0; $j<count($aObj_XML); $j++)
							{
								$xml .= '<card id="'. $aObj_XML[$j]["id"] .'" type-id="'. $aObj_XML[$j]->type["id"] .'" psp-id="'. $aObj_XML[$j]["pspid"] .'" preferred="'. $aObj_XML[$j]["preferred"] .'">';
								if (strlen($aObj_XML[$j]->name) > 0) { $xml .= $aObj_XML[$j]->name->asXML(); }
								$xml .= '<card-number-mask>'. $aObj_XML[$j]->mask .'</card-number-mask>';
								$xml .= $aObj_XML[$j]->expiry->asXML();
								$xml .= '</card>';
							}
							$xml .= '</stored-cards>';
						}
					}
					// Internal Error
					catch (mPointException $e)
					{
						$aMsgCds[$e->getCode()] = $e->getMessage();
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
	elseif (count($obj_DOM->{'initialize-payment'}) == 0)
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
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<root>';
echo $xml;
echo '</root>';
?>