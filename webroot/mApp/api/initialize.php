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
$_SERVER['PHP_AUTH_USER'] = "1415";
$_SERVER['PHP_AUTH_PW'] = "Ghdy4_ah1G";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<initialize-payment client-id="10019" account="100026">';
$HTTP_RAW_POST_DATA .= '<transaction order-no="1234abc">';
$HTTP_RAW_POST_DATA .= '<amount country-id="100">200</amount>';
$HTTP_RAW_POST_DATA .= '<callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url>';
$HTTP_RAW_POST_DATA .= '</transaction>';
$HTTP_RAW_POST_DATA .= '<client-info platform="iOS" version="5.1.1" language="da">';
$HTTP_RAW_POST_DATA .= '<mobile country-id="100" operator-id="10000">288828610</mobile>';
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
			$code = Validate::valBasic($_OBJ_DB, (integer) $obj_DOM->{'initialize-payment'}[$i]["client-id"], (integer) $obj_DOM->{'initialize-payment'}[$i]["account"]);
			if ($code == 100)
			{
				$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'initialize-payment'}[$i]["client-id"], (integer) $obj_DOM->{'initialize-payment'}[$i]["account"]);
				if ($obj_ClientConfig->getUsername() == trim($_SERVER['PHP_AUTH_USER']) && $obj_ClientConfig->getPassword() == trim($_SERVER['PHP_AUTH_PW'])
					&& $obj_ClientConfig->hasAccess($_SERVER['REMOTE_ADDR']) === true)
				{
					$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'initialize-payment'}[$i]->transaction->amount["country-id"]);
					if ( ($obj_CountryConfig instanceof CountryConfig) === false) { $obj_CountryConfig = $obj_ClientConfig->getCountryConfig(); }
					
					$obj_mPoint = new MobileWeb($_OBJ_DB, $_OBJ_TXT, $obj_ClientConfig);
					$iTxnID = $obj_mPoint->newTransaction(Constants::iPURCHASE_VIA_APP);
					try
					{
						// Update Transaction State
						$obj_mPoint->newMessage($iTxnID, Constants::iINPUT_VALID_STATE, $obj_DOM->asXML() );
			
						$data['typeid'] = $obj_DOM->{'initialize-payment'}[$i]->transaction["type-id"];
						$data['amount'] = (integer) $obj_DOM->{'initialize-payment'}[$i]->transaction->amount;
						$data['country-config'] = $obj_CountryConfig;
						if (count($obj_DOM->{'initialize-payment'}[$i]->transaction->points) == 1)
						{
							$data['points'] = (integer) $obj_DOM->{'initialize-payment'}[$i]->transaction->points;
						}
						if (count($obj_DOM->{'initialize-payment'}[$i]->transaction->reward) == 1)
						{
							$data['reward'] = (integer) $obj_DOM->{'initialize-payment'}[$i]->transaction->reward;
							$data['reward-type'] = (integer) $obj_DOM->{'initialize-payment'}[$i]->transaction->reward["type-id"];
						}
						
						$data['description'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->description;
						$data['gomobileid'] = -1;
						$data['orderid'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction["order-no"];
						$data['customer-ref'] = (string) $obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->{'customer-ref'};
						$data['mobile'] = (float) $obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->mobile;
						$data['operator'] = (integer) $obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->mobile["operator-id"];
						if (intval($data['operator']) == 0) { $data['operator'] = $obj_CountryConfig->getID() * 100; }
						$data['email'] = (string) $obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->email;
						$data['device-id'] = (string) $obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->{'device-id'};
						if (count($obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->ip) == 1) { $data['ip'] = (string) $obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->ip; }
						elseif (array_key_exists("HTTP_X_FORWARDED_FOR", $_SERVER) === true) { $data['ip'] = $_SERVER['HTTP_X_FORWARDED_FOR']; }
						else { $data['ip'] = $_SERVER['REMOTE_ADDR']; }
						$data['logo-url'] = "";
						$data['css-url'] = "";
						$data['accept-url'] = $obj_ClientConfig->getAcceptURL();
						$data['cancel-url'] = "";
						if (count($obj_DOM->{'initialize-payment'}[$i]->transaction->{'callback-url'}) == 1)
						{
							$data['callback-url'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->{'callback-url'};
						}
						else { $data['callback-url'] = $obj_ClientConfig->getCallbackURL(); }
						if (count($obj_DOM->{'initialize-payment'}[$i]->transaction->{'auth-url'}) == 1)
						{
							$data['auth-url'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->{'auth-url'};
						}
						else { $data['auth-url'] = $obj_ClientConfig->getAuthenticationURL(); }
						$data['icon-url'] = "";
						$data['language'] = (string) $obj_DOM->{'initialize-payment'}[$i]->{'client-info'}["language"];
						$data['markup'] = $obj_ClientConfig->getAccountConfig()->getMarkupLanguage();
						
						$obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $obj_ClientConfig, $data);
						// Associate End-User Account (if exists) with Transaction
						$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->mobile["country-id"]);
						
						$iAccountID = -1;
						if (strlen($obj_TxnInfo->getCustomerRef() ) > 0) { $iAccountID = EndUserAccount::getAccountIDFromExternalID($_OBJ_DB, $obj_ClientConfig, $obj_TxnInfo->getCustomerRef() ); }
						if ($iAccountID == -1 && floatval($obj_TxnInfo->getMobile() ) > 0) { $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_TxnInfo->getMobile(), $obj_CountryConfig); }
						if ($iAccountID == -1 && trim($obj_TxnInfo->getEMail() ) != "") { $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_TxnInfo->getEMail(), $obj_CountryConfig); }
						// Client supports global storage of payment cards
						if ($iAccountID == -1 && $obj_ClientConfig->getStoreCard() > 3)
						{
							$iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_TxnInfo->getMobile(), false);
							if ($iAccountID == -1 && trim($obj_TxnInfo->getEMail() ) != "") { $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_TxnInfo->getEMail(), false); }
						}
						$obj_TxnInfo->setAccountID($iAccountID);
						// Update Transaction Log
						$obj_mPoint->logTransaction($obj_TxnInfo);
						if (count($obj_DOM->{'initialize-payment'}[$i]->transaction->{'custom-variables'}) == 1 && count($obj_DOM->{'initialize-payment'}[$i]->transaction->{'custom-variables'}->children() ) > 0)
						{
							$aVars = array();
							foreach ($obj_DOM->{'initialize-payment'}[$i]->transaction->{'custom-variables'}->children() as $obj_Var)
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
						$obj_XML = simplexml_load_string($obj_TxnInfo->toXML() );
						$xml = '<client-config id="'. $obj_ClientConfig->getID() .'" account="'. $obj_ClientConfig->getAccountConfig()->getID() .'" store-card="'. $obj_ClientConfig->getStoreCard() .'" auto-capture="'. General::bool2xml($obj_ClientConfig->useAutoCapture() ) .'" mode="'. $obj_ClientConfig->getMode() .'">';
						$xml .= '<name>'. htmlspecialchars($obj_ClientConfig->getName(), ENT_NOQUOTES) .'</name>';
						$xml .= '<callback-url>'. htmlspecialchars($obj_ClientConfig->getCallbackURL(), ENT_NOQUOTES) .'</callback-url>';
						$xml .= '<accept-url>'. htmlspecialchars($obj_ClientConfig->getAcceptURL(), ENT_NOQUOTES) .'</accept-url>';
						$xml .= '</client-config>';
						$xml .= '<transaction id="'. $obj_TxnInfo->getID() .'" order-no="'. htmlspecialchars($obj_TxnInfo->getOrderID(), ENT_NOQUOTES) .'" type-id="'. $obj_TxnInfo->getTypeID() .'" eua-id="'. $obj_TxnInfo->getAccountID() .'" language="'. $obj_TxnInfo->getLanguage() .'" auto-capture="'. General::bool2xml($obj_TxnInfo->useAutoCapture() ) .'" mode="'. $obj_TxnInfo->getMode() .'">';
						$xml .= $obj_XML->amount->asXML();
						if ($obj_TxnInfo->getPoints() > 0) { $xml .= $obj_XML->points->asXML(); }
						if ($obj_TxnInfo->getReward() > 0) { $xml .= $obj_XML->reward->asXML(); }
						$xml .= '<mobile country-id="'. $obj_CountryConfig->getID() .'" operator-id="'. $obj_TxnInfo->getOperator() .'">'. floatval($obj_TxnInfo->getMobile() ) .'</mobile>';
						if (trim($obj_TxnInfo->getEMail() ) != "") { $xml .= $obj_XML->email->asXML(); }
						$xml .= $obj_XML->{'callback-url'}->asXML();
						$xml .= $obj_XML->{'accept-url'}->asXML();
						$xml .= '</transaction>';
						$obj_XML = simplexml_load_string($obj_mPoint->getCards($obj_TxnInfo->getAmount() ) );
						
						// End-User already has an account and payment with Account enabled
						if ($iAccountID > 0 && count($obj_XML->xpath("/cards/item[@type-id = 11]") ) == 1)
						{
							$obj_mPoint = new CreditCard($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo);
							$aObj_XML = simplexml_load_string($obj_mPoint->getStoredCards($iAccountID, $obj_ClientConfig->showAllCards() ) );
							$aObj_XML = $aObj_XML->xpath("/stored-cards/card[client/@id = ". $obj_ClientConfig->getID() ."]");
						}
						else { $aObj_XML = array(); }
						$xml .= '<cards>';
						for ($j=0; $j<count($obj_XML->item); $j++)
						{
							// Card does not represent "My Account" or the End-User has an acccount with Stored Cards or Stored Value Account is available
							if ($obj_XML->item[$j]["type-id"] != 11
								|| ($iAccountID > 0 && (count($aObj_XML) > 0 || $obj_ClientConfig->getStoreCard() == 2) ) )
							{
								$xml .= '<card id="'. $obj_XML->item[$j]["id"] .'" type-id="'. $obj_XML->item[$j]["type-id"] .'" psp-id="'. $obj_XML->item[$j]["pspid"] .'" min-length="'. $obj_XML->item[$j]["min-length"] .'" max-length="'. $obj_XML->item[$j]["max-length"] .'" cvc-length="'. $obj_XML->item[$j]["cvc-length"] .'">';
								$xml .= '<name>'. htmlspecialchars($obj_XML->item[$j]->name, ENT_NOQUOTES) .'</name>';
								$xml .= $obj_XML->item[$j]->prefixes->asXML();
								$xml .= htmlspecialchars($obj_XML->item[$j]->name, ENT_NOQUOTES);	// Backward compatibility
								$xml .= '</card>';
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
								if (strlen($aObj_XML[$j]->{'card-holder-name'}) > 0) { $xml .= $aObj_XML[$j]->{'card-holder-name'}->asXML(); }
								if (count($aObj_XML[$j]->address) == 1) { $xml .= $aObj_XML[$j]->address->asXML(); }
								$xml .= '</card>';
							}
							$xml .= '</stored-cards>';
						}
						if ($iAccountID > 0 && $obj_ClientConfig->getStoreCard() == 2)
						{
							$obj_XML = simplexml_load_string($obj_mPoint->getAccountInfo($iAccountID) );
							$xml .= '<account id="'. $iAccountID .'">';
							$xml .= $obj_XML->balance->asXML();
							$xml .= $obj_XML->points->asXML();
							$xml .= '</account>';
						}
					}
					// Internal Error
					catch (mPointException $e)
					{
						trigger_error("Unknown error: ". $e->getMessage() ."(". $e->getCode() .")" ."\n". $e->getTrace(), E_USER_WARNING);
						
						header("HTTP/1.1 500 Internal Server Error");
						
						$xml = '<status code="'. $e->getCode() .'">'. htmlspecialchars($e->getMessage(), ENT_NOQUOTES) .'</status>';
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

header("Content-Type: text/xml; charset=\"UTF-8\"");

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<root>';
echo $xml;
echo '</root>';
?>