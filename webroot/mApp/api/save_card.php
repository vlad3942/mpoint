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

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require Data Class for Client Information
require_once(sCLASS_PATH ."/clientinfo.php");

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );
/*
$_SERVER['PHP_AUTH_USER'] = "CPMDemo";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<save-card client-id="100" >';
$HTTP_RAW_POST_DATA .= '<card type-id="6" preferred="true">';
$HTTP_RAW_POST_DATA .= '<name>My VISA</name>';
$HTTP_RAW_POST_DATA .= '<card-number-mask>540287******5344</card-number-mask>';
$HTTP_RAW_POST_DATA .= '<expiry-month>10</expiry-month>';
$HTTP_RAW_POST_DATA .= '<expiry-year>14</expiry-year>';
$HTTP_RAW_POST_DATA .= '<token>123456-ABCD</token>';
$HTTP_RAW_POST_DATA .= '<card-holder-name>Jonatan Evad Buus</card-holder-name>';
$HTTP_RAW_POST_DATA .= '<address country-id="100">';
$HTTP_RAW_POST_DATA .= '<first-name>Jonatan Evald</first-name>';
$HTTP_RAW_POST_DATA .= '<last-name>Buus</last-name>';
$HTTP_RAW_POST_DATA .= '<street>Dexter Gordons Vej 3, 6.tv</street>';
$HTTP_RAW_POST_DATA .= '<postal-code>2450</postal-code>';
$HTTP_RAW_POST_DATA .= '<city>'. utf8_encode("København SV") .'</city>';
$HTTP_RAW_POST_DATA .= '<state>N/A</state>';
$HTTP_RAW_POST_DATA .= '</address>';
$HTTP_RAW_POST_DATA .= '</card>';
$HTTP_RAW_POST_DATA .= '<client-info platform="iOS" version="1.00" language="da">';
$HTTP_RAW_POST_DATA .= '<customer-ref>ABC-123</customer-ref>';
$HTTP_RAW_POST_DATA .= '<mobile country-id="100" operator-id="10000">28882861</mobile>';
$HTTP_RAW_POST_DATA .= '<email>jona@oismail.com</email>';
$HTTP_RAW_POST_DATA .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
$HTTP_RAW_POST_DATA .= '</client-info>';
$HTTP_RAW_POST_DATA .= '</save-card>';
$HTTP_RAW_POST_DATA .= '</root>';
*/
$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mpoint.xsd") === true && count($obj_DOM->{'save-card'}) > 0)
	{	
		$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);
		
		for ($i=0; $i<count($obj_DOM->{'save-card'}); $i++)
		{
			// Set Global Defaults
			if (empty($obj_DOM->{'save-card'}[$i]["account"]) === true || intval($obj_DOM->{'save-card'}[$i]["account"]) < 1) { $obj_DOM->{'save-card'}[$i]["account"] = -1; }
		
			// Validate basic information
			$code = Validate::valBasic($_OBJ_DB, (integer) $obj_DOM->{'save-card'}[$i]["client-id"], (integer) $obj_DOM->{'save-card'}[$i]["account"]);
			if ($code == 100)
			{
				$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'save-card'}[$i]["client-id"], (integer) $obj_DOM->{'save-card'}[$i]["account"]);
				
				// Client successfully authenticated
				if ($obj_ClientConfig->getUsername() == trim($_SERVER['PHP_AUTH_USER']) && $obj_ClientConfig->getPassword() == trim($_SERVER['PHP_AUTH_PW']) )
				{
					for ($j=0; $j<count($obj_DOM->{'save-card'}[$i]->card); $j++)
					{
						$obj_mPoint = new EndUserAccount($_OBJ_DB, $_OBJ_TXT, $obj_ClientConfig);
						$obj_Validator = new Validate($obj_ClientConfig->getCountryConfig() );
						$aMsgCds = array();
						
						if ($obj_Validator->valName( (string) $obj_DOM->{'save-card'}[$i]->card[$j]->name) != 10) { $aMsgCds[] = $obj_Validator->valName( (string) $obj_DOM->{'save-card'}[$i]->card[$j]->name) + 20; }
						if (intval($obj_DOM->{'save-card'}[$i]->card[$j]["type-id"]) == 0 && intval($obj_DOM->{'save-card'}[$i]->card[$j]["id"]) == 0)
						{
							$aMsgCds[] = 31;
						}
						if (intval($obj_DOM->{'save-card'}[$i]->card[$j]["type-id"]) > 0)
						{
							if ($obj_Validator->valCardTypeID($_OBJ_DB, (integer) $obj_DOM->{'save-card'}[$i]->card[$j]["type-id"])  != 10) { $aMsgCds[] = $obj_Validator->valCardTypeId($_OBJ_DB, (integer) $obj_DOM->{'save-card'}[$i]->card[$j]["type-id"]) + 40; }
						}
						
						$iAccountID = -2;
						if (intval($obj_DOM->{'save-card'}[$i]->card[$j]["id"]) > 0)
						{
							$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'save-card'}[$i]->{'client-info'}->mobile["country-id"]);

							$iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_DOM->{'save-card'}[$i]->{'client-info'}->mobile, $obj_CountryConfig);
							if ($iAccountID < 0 && count($obj_DOM->{'save-card'}[$i]->{'client-info'}->email) == 1) { $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_DOM->{'save-card'}[$i]->{'client-info'}->email, $obj_CountryConfig); }
							if ($iAccountID < 0) { $iAccountID = $obj_mPoint->getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_DOM->{'save-card'}[$i]->{'client-info'}->mobile, $obj_CountryConfig); }
							if ($iAccountID < 0) { $iAccountID = $obj_mPoint->getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_DOM->{'save-card'}[$i]->{'client-info'}->email, $obj_CountryConfig); }
							
							if ($obj_Validator->valStoredCard($_OBJ_DB, $iAccountID, (integer) $obj_DOM->{'save-card'}[$i]->card[$j]["id"])  != 10) { $aMsgCds[] = $obj_Validator->valStoredCard($_OBJ_DB, $iAccountID, (integer) $obj_DOM->{'save-card'}[$i]->card[$j]["id"]) + 50; }
						}
						
						// Success: Input Valid
						if (count($aMsgCds) == 0)
						{
							$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'save-card'}[$i]->{'client-info'}->mobile["country-id"]);
							// Start Transaction
							$_OBJ_DB->query("START TRANSACTION");  // START TRANSACTION does not work with Oracle db
							if (intval($obj_DOM->{'save-card'}[$i]->card[$j]["id"]) > 0)
							{
								$code = $obj_mPoint->saveCardName( $obj_DOM->{'save-card'}[$i]->card[$j]["id"], (string) $obj_DOM->{'save-card'}[$i]->card[$j],  General::xml2bool($obj_DOM->{'save-card'}[$i]->card[$j]["preferred"]) );
							}
							else
							{
								$iAccountID = -1;
								if (count($obj_DOM->{'save-card'}[$i]->{'client-info'}->{'customer-ref'}) == 1) { $iAccountID = EndUserAccount::getAccountIDFromExternalID($_OBJ_DB, $obj_ClientConfig, $obj_DOM->{'save-card'}[$i]->{'client-info'}->{'customer-ref'}); }
								if ($iAccountID < 0 && count($obj_DOM->{'save-card'}[$i]->{'client-info'}->mobile) == 1) { $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_DOM->{'save-card'}[$i]->{'client-info'}->mobile, $obj_CountryConfig); }
								if ($iAccountID < 0) { $iAccountID = $obj_mPoint->getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_DOM->{'save-card'}[$i]->{'client-info'}->mobile, $obj_CountryConfig); }
								if ($iAccountID < 0) { $iAccountID = $obj_mPoint->getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_DOM->{'save-card'}[$i]->{'client-info'}->email, $obj_CountryConfig); }
								if (count($obj_DOM->{'save-card'}[$i]->card[$j]->token) == 1)
								{
									// New End-User
									if ($iAccountID < 0)
									{
										$iAccountID = $obj_mPoint->newAccount($obj_CountryConfig->getID(), (float) $obj_DOM->{'save-card'}[$i]->{'client-info'}->mobile, (string) $obj_DOM->{'save-card'}[$i]->password, (string) $obj_DOM->{'save-card'}[$i]->{'client-info'}->email, (string) $obj_DOM->{'save-card'}[$i]->{'client-info'}->{'customer-ref'}); 
									}
									if (intval($obj_DOM->{'save-card'}[$i]->card[$j]->{'expiry-month'}) < 10) { $obj_DOM->{'save-card'}[$i]->card[$j]->{'expiry-month'} = "0". intval($obj_DOM->{'save-card'}[$i]->card[$j]->{'expiry-month'}); }
									$code = $obj_mPoint->saveCard($iAccountID, $obj_DOM->{'save-card'}[$i]->card[$j]["type-id"], $obj_DOM->{'save-card'}[$i]->card[$j]["psp-id"], (string) $obj_DOM->{'save-card'}[$i]->card[$j]->token, (string) $obj_DOM->{'save-card'}[$i]->card[$j]->{'card-number-mask'}, (string) $obj_DOM->{'save-card'}[$i]->card[$j]->{'expiry-month'} ."/". substr($obj_DOM->{'save-card'}[$i]->card[$j]->{'expiry-year'}, -2), (string) $obj_DOM->{'save-card'}[$i]->card[$j]->{'card-holder-name'}, (string) $obj_DOM->{'save-card'}[$i]->card[$j]->name, General::xml2bool($obj_DOM->{'save-card'}[$i]->card[$j]["preferred"]) ) + 1;		
								}
								else { $code = $obj_mPoint->saveCardName($iAccountID, $obj_DOM->{'save-card'}[$i]->card[$j]["type-id"], (string) $obj_DOM->{'save-card'}[$i]->card[$j]->name, General::xml2bool($obj_DOM->{'save-card'}[$i]->card[$j]["type-id"]) ); }							
							}
							// Save Address if passed and cards successfuly saved
							if (count($obj_DOM->{'save-card'}[$i]->card[$j]->{'address'}) == 1 && $code == 1)
							{
								$id = $obj_mPoint->getCardIDFromCardDetails($iAccountID, $obj_DOM->{'save-card'}[$i]->card[$j]["type-id"], (string) $obj_DOM->{'save-card'}[$i]->card[$j]->{'card-number-mask'}, (string) $obj_DOM->{'save-card'}[$i]->card[$j]->{'expiry-month'} ."/". substr($obj_DOM->{'save-card'}[$i]->card[$j]->{'expiry-year'}, -2) );
								$codeA = $obj_mPoint->saveAddress($id, (integer) $obj_DOM->{'save-card'}[$i]->card[$j]->address["country-id"], (string) $obj_DOM->{'save-card'}[$i]->card[$j]->address->state, (string) $obj_DOM->{'save-card'}[$i]->card[$j]->address->{'first-name'}, (string) $obj_DOM->{'save-card'}[$i]->card[$j]->address->{"last-name"}, (string) $obj_DOM->{'save-card'}[$i]->card[$j]->address->company, (string) $obj_DOM->{'save-card'}[$i]->card[$j]->address->street, (string) $obj_DOM->{'save-card'}[$i]->card[$j]->address->{"postal-code"}, (string) $obj_DOM->{'save-card'}[$i]->card[$j]->address->city);							
								if ($codeA == 10)
								{
									// Commit Transfer
									$_OBJ_DB->query("COMMIT");			
								}
								else
								{
									// Abort transaction and rollback to previous state
									$_OBJ_DB->query("ROLLBACK");
									$code = -2;
								}
							}
							elseif ($code == 1)
							{
								// Commit Transfer
								$_OBJ_DB->query("COMMIT");	
							}
							else
							{
								// Abort transaction and rollback to previous state
								$_OBJ_DB->query("ROLLBACK");
							}								
							// Success: Card saved
							if ($code > 0 && $obj_ClientConfig->getNotificationURL() != "")
							{								
								try
								{
									$obj_ClientInfo = ClientInfo::produceInfo($obj_DOM->{'save-card'}[$i]->{'client-info'}, $obj_CountryConfig, @$_SERVER['HTTP_X_FORWARDED_FOR']);
										
									$aObj_XML = simplexml_load_string($obj_mPoint->getStoredCards($iAccountID, $obj_ClientConfig->showAllCards() ) );
									$aObj_XML = $aObj_XML->xpath("/stored-cards/card[client/@id = ". $obj_ClientConfig->getID() ."]");
									
									$aURL_Info = parse_url($obj_mPoint->getClientConfig()->getNotificationURL() );
									$aHTTP_CONN_INFO["mesb"]["protocol"] = $aURL_Info["scheme"];
									$aHTTP_CONN_INFO["mesb"]["host"] = $aURL_Info["host"];
									$aHTTP_CONN_INFO["mesb"]["port"] = $aURL_Info["port"];
									$aHTTP_CONN_INFO["mesb"]["path"] = $aURL_Info["path"];
									if (array_key_exists("query", $aURL_Info) === true) { $aHTTP_CONN_INFO["mesb"]["path"] .= "?". $aURL_Info["query"]; }
									$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["mesb"]);
									
									switch ($obj_mPoint->notify($obj_ConnInfo, $obj_ClientInfo, $iAccountID, $obj_DOM->{'save-card'}[$i]->{'auth-token'}, count($aObj_XML) ) )
									{
									case (1):	// Error: Unknown response from External Server
										header("HTTP/1.1 502 Bad Gateway");
										
										$xml .= '<status code="98">Invalid response from External Server</status>';
										break;
									case (2):	// Error: Notification Rejected by External Server
										header("HTTP/1.1 502 Bad Gateway");
										
										$xml .= '<status code="97">Notification rejected by External Server</status>';
										break;
									case (10):	// Success: Card successfully saved
										$xml = '<status code="'. ($code+99) .'">Card successfully saved</status>';
										break;
									default:	// Error: Unknown response from External Server
										header("HTTP/1.1 502 Bad Gateway");
										
										$xml .= '<status code="99">Unknown response from External Server</status>';
										break;
									}
								}
								// Error: Unable to connect to External Server
								catch (HTTPConnectionException $e) 
								{
									header("HTTP/1.1 504 Gateway Timeout");
									
									$xml = '<?xml version="1.0" encoding="UTF-8"?>';
									$xml .= '<root>';
									$xml .= '<status code="91">Unable to connect to External Server</status>';
									$xml .= '</root>';
								}
								// Error: No response received from External Server
								catch (HTTPSendException $e)
								{
									header("HTTP/1.1 504 Gateway Timeout");
										
									$xml = '<?xml version="1.0" encoding="UTF-8"?>';
									$xml .= '<root>';
									$xml .= '<status code="92">No response received from External Server</status>';
									$xml .= '</root>';
								}
							}
							// Success: Card successfully saved
							elseif ($code > 0) { $xml = '<status code="'. ($code+99) .'">Card successfully saved</status>'; }
							// Internal Error: Unable to save Card
							else 
							{
								header("HTTP/1.1 500 Internal Server Error");
						
								$xml = '<status code="90">Unable to save Card ('. $code .')</status>';
							}
						}
						// Invalid Input
						else
						{
							header("HTTP/1.1 400 Bad Request");
							
							$xml = '<status code="'. $aMsgCds[0] .'" />';
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
	elseif (count($obj_DOM->{'save-card'}) == 0)
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

$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);
$obj_XML = simplexml_load_string('<root>'. $xml .'</root>');
$obj_mPoint->newAuditMessage(Constants::iOPERATION_CARD_SAVED, $obj_DOM->{'save-card'}[0]->{'client-info'}->mobile, $obj_DOM->{'save-card'}[0]->{'client-info'}->email, $obj_DOM->{'save-card'}[0]->{'client-info'}->{'customer-ref'}, $obj_XML->status["code"], (string) $obj_XML->status);
?>