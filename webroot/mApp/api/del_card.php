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

// Require the PHP API for handling the connection to GoMobile
require_once(sAPI_CLASS_PATH ."/gomobile.php");
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require Business logic for the My Account component
require_once(sCLASS_PATH ."/my_account.php");
// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
// Require Data Class for Client Information
require_once(sCLASS_PATH ."/clientinfo.php");

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );
/*
$_SERVER['PHP_AUTH_USER'] = "CPMDemo";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<delete-card client-id="10007" account="100007">';
$HTTP_RAW_POST_DATA .= '<card>62371</card>';
$HTTP_RAW_POST_DATA .= '<password>oisJona</password>';
//$HTTP_RAW_POST_DATA .= '<auth-token>test1234</auth-token>';
//$HTTP_RAW_POST_DATA .= '<auth-url>http://mpoint.test.cellpointmobile.com/_test/auth.php</auth-url>';
$HTTP_RAW_POST_DATA .= '<client-info platform="iOS" version="1.00" language="da">';
$HTTP_RAW_POST_DATA .= '<customer-ref>ABC-123</customer-ref>';
$HTTP_RAW_POST_DATA .= '<mobile country-id="100" operator-id="10000">28882861</mobile>';
$HTTP_RAW_POST_DATA .= '<email>jona@oismail.com</email>';
$HTTP_RAW_POST_DATA .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
$HTTP_RAW_POST_DATA .= '</client-info>';
$HTTP_RAW_POST_DATA .= '</delete-card>';
$HTTP_RAW_POST_DATA .= '</root>';
*/
$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mpoint.xsd") === true && count($obj_DOM->{'delete-card'}) > 0)
	{	
		$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);
		
		for ($i=0; $i<count($obj_DOM->{'delete-card'}); $i++)
		{
			// Set Global Defaults
			if (empty($obj_DOM->{'delete-card'}[$i]["account"]) === true || intval($obj_DOM->{'delete-card'}[$i]["account"]) < 1) { $obj_DOM->{'delete-card'}[$i]["account"] = -1; }
		
			// Validate basic information
			$code = Validate::valBasic($_OBJ_DB, (integer) $obj_DOM->{'delete-card'}[$i]["client-id"], (integer) $obj_DOM->{'delete-card'}[$i]["account"]);
			if ($code == 100)
			{
				$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'delete-card'}[$i]["client-id"], (integer) $obj_DOM->{'delete-card'}[$i]["account"]);
				
				// Client successfully authenticated
				if ($obj_ClientConfig->getUsername() == trim($_SERVER['PHP_AUTH_USER']) && $obj_ClientConfig->getPassword() == trim($_SERVER['PHP_AUTH_PW']) )
				{
					for ($j=0; $j<count($obj_DOM->{'delete-card'}[$i]->card); $j++)
					{
						$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'delete-card'}[$i]->{'client-info'}->mobile["country-id"]);
						if ( ($obj_CountryConfig instanceof CountryConfig) === false) { $obj_CountryConfig = $obj_ClientConfig->getCountryConfig(); }
					
						$obj_mPoint = new MyAccount($_OBJ_DB, $_OBJ_TXT, $obj_CountryConfig);
						$obj_Validator = new Validate($obj_CountryConfig);
						$aMsgCds = array();
						
						$iAccountID = -1;
						if (count($obj_DOM->{'delete-card'}[$i]->{'client-info'}->{'customer-ref'}) == 1) { $iAccountID = EndUserAccount::getAccountIDFromExternalID($_OBJ_DB, $obj_ClientConfig, $obj_DOM->{'delete-card'}[$i]->{'client-info'}->{'customer-ref'}); }
						if ($iAccountID < 0 && count($obj_DOM->{'delete-card'}[$i]->{'client-info'}->mobile) == 1) { $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_DOM->{'delete-card'}[$i]->{'client-info'}->mobile, $obj_CountryConfig); }
						if ($iAccountID < 0 && count($obj_DOM->{'delete-card'}[$i]->{'client-info'}->email) == 1) { $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_DOM->{'delete-card'}[$i]->{'client-info'}->email, $obj_CountryConfig); }
						if ($iAccountID < 0) { $iAccountID = $obj_mPoint->getAccountID($obj_CountryConfig, $obj_DOM->{'delete-card'}[$i]->{'client-info'}->mobile); }
						if ($iAccountID < 0) { $iAccountID = $obj_mPoint->getAccountID($obj_CountryConfig, $obj_DOM->{'delete-card'}[$i]->{'client-info'}->email); }
                        if (strlen((string) $obj_DOM->{'delete-card'}[$i]->password) > 1 && $obj_Validator->valPassword( (string) $obj_DOM->{'delete-card'}[$i]->password) != 10)
                        {
                            $aMsgCds[] = $obj_Validator->valPassword( (string) $obj_DOM->{'delete-card'}[$i]->password) + 20;
                        }
                        if ($obj_Validator->valStoredCard($_OBJ_DB, $iAccountID, (integer) $obj_DOM->{'delete-card'}[$i]->card) != 10) { $aMsgCds[] = $obj_Validator->valStoredCard($_OBJ_DB, $iAccountID, (integer) $obj_DOM->{'delete-card'}[$i]->card) + 40; }
					
						// Input valid
						if (count($aMsgCds) == 0)
						{
							if (count($obj_DOM->{'delete-card'}[$i]->{'auth-token'}) == 1
							&& (count($obj_DOM->{'delete-card'}[$i]->{'auth-url'}) == 1 || strlen($obj_ClientConfig->getAuthenticationURL() ) > 0) )
							{
								$url = $obj_ClientConfig->getAuthenticationURL();
								if (count($obj_DOM->{'delete-card'}[$i]->{'auth-url'}) == 1) { $url = (string) $obj_DOM->{'delete-card'}[$i]->{'auth-url'}; }
								if ($obj_Validator->valURL($url, $obj_ClientConfig->getAuthenticationURL() ) == 10)
								{
									$code = $obj_mPoint->auth(HTTPConnInfo::produceConnInfo($url), $obj_DOM->{'delete-card'}[$i]->{'client-info'}->{'customer-ref'}, (string) $obj_DOM->{'delete-card'}[$i]->{'auth-token'} );
								}
								else { $code = 8; }
							}
							else { $code = $obj_mPoint->auth($iAccountID, (string) $obj_DOM->{'delete-card'}[$i]->password); }

							// Authentication succeeded
							if ($code >= 10)
							{
                                if (strlen( (string) $obj_DOM->{'delete-card'}[$i]->password) > 1)
                                { 
                                    // Generate new security token
                                    if ($code == 11) { setcookie("token", General::genToken($iAccountID, $obj_ClientConfig->getSecret() ) ); }
                                    $code = General::authToken($iAccountID, $obj_ClientConfig->getSecret(), $_COOKIE['token']);
                                }
								// Authentication succeeded
								if ($code == 10 || ($code == 11 && $obj_ClientConfig->smsReceiptEnabled() === false) )
								{
									$_OBJ_DB->query("START TRANSACTION");
										
									// Success: Stored Card Deleted
									if ($obj_mPoint->delStoredCard($iAccountID, (integer) $obj_DOM->{'delete-card'}[$i]->card) === true)
									{
										// Success: Card saved
										if ($code > 0 && $obj_ClientConfig->getNotificationURL() != "")
										{
											$obj_mPoint = new EndUserAccount($_OBJ_DB, $_OBJ_TXT, $obj_ClientConfig);
											try
											{
												$obj_ClientInfo = ClientInfo::produceInfo($obj_DOM->{'delete-card'}[$i]->{'client-info'}, $obj_CountryConfig, @$_SERVER['HTTP_X_FORWARDED_FOR']);
										
												$aObj_XML = simplexml_load_string($obj_mPoint->getStoredCards($iAccountID, $obj_ClientConfig->showAllCards() ) );
												$aObj_XML = $aObj_XML->xpath("/stored-cards/card[client/@id = ". $obj_ClientConfig->getID() ."]");
													
												$aURL_Info = parse_url($obj_ClientConfig->getNotificationURL() );
												$aHTTP_CONN_INFO["mesb"]["protocol"] = $aURL_Info["scheme"];
												$aHTTP_CONN_INFO["mesb"]["host"] = $aURL_Info["host"];
												$aHTTP_CONN_INFO["mesb"]["port"] = $aURL_Info["port"];
												$aHTTP_CONN_INFO["mesb"]["path"] = $aURL_Info["path"];
												if (array_key_exists("query", $aURL_Info) === true) { $aHTTP_CONN_INFO["mesb"]["path"] .= "?". $aURL_Info["query"]; }
												$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["mesb"]);
													
												switch ($obj_mPoint->notify($obj_ConnInfo, $obj_ClientInfo, $iAccountID, $obj_DOM->{'delete-card'}[$i]->{'auth-token'}, count($aObj_XML) ) )
												{
												case (1):	// Error: Unknown response from CRM System
													header("HTTP/1.1 502 Bad Gateway");
													$_OBJ_DB->query("ROLLBACK");	
													$xml .= '<status code="98">Invalid response from CRM System</status>';
													break;
												case (2):	// Error: Notification Rejected by CRM System
													$_OBJ_DB->query("ROLLBACK");
													header("HTTP/1.1 502 Bad Gateway");
									
													$xml .= '<status code="97">Notification rejected by CRM System</status>';
													break;
												case (10):	// Success: Card successfully saved
													$_OBJ_DB->query("COMMIT");
														
													$xml = '<status code="100">Card successfully deleted and CRM system notified</status>';
													break;
												default:	// Error: Unknown response from CRM System
													header("HTTP/1.1 502 Bad Gateway");
													$_OBJ_DB->query("ROLLBACK");
													$xml .= '<status code="99">Unknown response from CRM System</status>';
													break;
												}
											}
											// Error: Unable to connect to CRM System
											catch (HTTPConnectionException $e)
											{
												$_OBJ_DB->query("ROLLBACK");
												header("HTTP/1.1 504 Gateway Timeout");
												$xml = '<?xml version="1.0" encoding="UTF-8"?>';
												$xml .= '<root>';
												$xml .= '<status code="91">Unable to connect to CRM System</status>';
												$xml .= '</root>';
											}
											// Error: No response received from CRM System
											catch (HTTPSendException $e)
											{
												$_OBJ_DB->query("ROLLBACK");
												header("HTTP/1.1 504 Gateway Timeout");
										
												$xml = '<?xml version="1.0" encoding="UTF-8"?>';
												$xml .= '<root>';
												$xml .= '<status code="92">No response received from CRM System</status>';
												$xml .= '</root>';
											}
										}
										// Success: Card successfully saved
										else { $xml = '<status code="100">Card successfully deleted</status>'; }
									}
									else
									{
										header("HTTP/1.1 500 Internal Server Error");
													
										$xml = '<status code="90">Unable to delete card</status>';
									}
								}
								// Authentication succeeded - But Mobile number not verified
								elseif ($code == 11)
								{
									header("HTTP/1.1 403 Forbidden");
										
									$xml = '<status code="37">Mobile number not verified</status>';
								}
								// Authentication failed
								else
								{
									// Account disabled due to too many failed login attempts
									if ($code == 3)
									{
										// Re-Intialise Text Translation Object based on transaction
										$_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH . $obj_DOM->{'delete-card'}[$i]->{'client-info'}["language"] ."/global.txt", sLANGUAGE_PATH . $obj_DOM->{'delete-card'}[$i]->{'client-info'}["language"] ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");
										$obj_mPoint = new EndUserAccount($_OBJ_DB, $_OBJ_TXT, $obj_ClientConfig);
										$obj_mPoint->sendAccountDisabledNotification(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO), $obj_DOM->{'delete-card'}[$i]->{'client-info'}->mobile);
									}
										
									header("HTTP/1.1 403 Forbidden");
		
									$xml = '<status code="'. ($code+30) .'">Authentication failed</status>';
								}
							}
							// Authentication failed
							else
							{
								header("HTTP/1.1 403 Forbidden");
					
                                if ( strlen((string) $obj_DOM->{'delete-card'}[$i]->{'auth-token'}) > 0 && strlen((string) $obj_DOM->{'delete-card'}[$i]->{'auth-url'} ) > 0)
                                {
                                    $xml = '<status code="38">Invalid Auth Token: '. (string) $obj_DOM->{'delete-card'}[$i]->{'auth-token'} .'</status>';
                                }
                                else
                                {
                                    $xml = '<status code="38">Invalid Security Token: '. $_COOKIE['token'] .'</status>';
                                }
							}
						}
						else
						{
							header("HTTP/1.1 400 Bad Request");
							
							$message = 'Invalid card number';
							if ($aMsgCds[0] === 43) { $message = 'Card not found.';  }
							if ($aMsgCds[0] === 44) { $message = 'Card is disabled.';  }
														
							$xml = '<status code="'. $aMsgCds[0] .'" >'. $message.'</status>';
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
	elseif (count($obj_DOM->{'delete-card'}) == 0)
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
$obj_mPoint->newAuditMessage(Constants::iOPERATION_CARD_SAVED, $obj_DOM->{'delete-card'}[0]->{'client-info'}->mobile, $obj_DOM->{'delete-card'}[0]->{'client-info'}->email, $obj_DOM->{'delete-card'}[0]->{'client-info'}->{'customer-ref'}, $obj_XML->status["code"], (string) $obj_XML->status);
?>