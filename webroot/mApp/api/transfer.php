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
// Require the PHP API for handling the connection to GoMobile
require_once(sAPI_CLASS_PATH ."/gomobile.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require Business logic for the E-Money Transfer component
require_once(sCLASS_PATH ."/transfer.php");
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
$HTTP_RAW_POST_DATA .= '<transfer client-id="10007" account="100007">';
$HTTP_RAW_POST_DATA .= '<amount country-id="100">1000</amount>';
$HTTP_RAW_POST_DATA .= '<mobile country-id="100">28882861</mobile>';
//$HTTP_RAW_POST_DATA .= '<email>oksana.zubko123@gmail.com</email>';
$HTTP_RAW_POST_DATA .= '<password>oisJona1</password>';
$HTTP_RAW_POST_DATA .= '<message>test message</message>';
$HTTP_RAW_POST_DATA .= '<client-info platform="iOS" version="1.00" language="da">';
$HTTP_RAW_POST_DATA .= '<mobile country-id="100" operator-id="10000">28880019</mobile>';
$HTTP_RAW_POST_DATA .= '<email>jona@oismail.com</email>';
$HTTP_RAW_POST_DATA .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
$HTTP_RAW_POST_DATA .= '</client-info>';
$HTTP_RAW_POST_DATA .= '</transfer>';
$HTTP_RAW_POST_DATA .= '</root>';
*/
$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mpoint.xsd") === true && count($obj_DOM->transfer) > 0)
	{	
		$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);
		$obj_Validator = new Validate();
		$xml = '';
		
		for ($i=0; $i<count($obj_DOM->transfer); $i++)
		{
			// Set Global Defaults
			if (empty($obj_DOM->transfer[$i]["account"]) === true || intval($obj_DOM->transfer[$i]["account"]) < 1) { $obj_DOM->transfer[$i]["account"] = -1; }
		
			// Validate basic information
			$code = Validate::valBasic($_OBJ_DB, (integer) $obj_DOM->transfer[$i]["client-id"], (integer) $obj_DOM->transfer[$i]["account"]);
			if ($code == 100)
			{
				$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->transfer[$i]["client-id"], (integer) $obj_DOM->transfer[$i]["account"]);
				
				// Client successfully authenticated
				if ($obj_ClientConfig->getUsername() == trim($_SERVER['PHP_AUTH_USER']) && $obj_ClientConfig->getPassword() == trim($_SERVER['PHP_AUTH_PW']) )
				{
					$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->transfer[$i]->{'client-info'}->mobile["country-id"]);
					if ( ($obj_CountryConfig instanceof CountryConfig) === false) { $obj_CountryConfig = $obj_ClientConfig->getCountryConfig(); }
					
					$obj_mPoint = new Transfer($_OBJ_DB, $_OBJ_TXT, $obj_CountryConfig);
					$iSenderAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_DOM->transfer[$i]->{'client-info'}->mobile, $obj_CountryConfig);
					if ($iSenderAccountID < 0 && count($obj_DOM->transfer[$i]->{'client-info'}->email) == 1) { $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_DOM->transfer[$i]->{'client-info'}->email, $obj_CountryConfig); }
					if ($iSenderAccountID < 0) { $iSenderAccountID = $obj_mPoint->getAccountID($obj_CountryConfig, $obj_DOM->transfer[$i]->{'client-info'}->mobile); }
					if ($iSenderAccountID < 0) { $iSenderAccountID = $obj_mPoint->getAccountID($obj_CountryConfig, $obj_DOM->transfer[$i]->{'client-info'}->email); }
					
					if ($obj_Validator->valPassword( (string) $obj_DOM->transfer[$i]->password) < 10) { $aMsgCds[] = $obj_Validator->valPassword( (string) $obj_DOM->transfer[$i]->password) + 20; }
					if ($obj_Validator->valCountry($_OBJ_DB, (integer) $obj_DOM->transfer[$i]->amount["country-id"]) == 10)
					{
						$obj_CC = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->transfer[$i]->amount["country-id"]);
						$obj_Validator = new Validate($obj_CC);
						
						$obj_XML = simplexml_load_string($obj_mPoint->getFees(Constants::iTRANSFER_FEE, $obj_CC->getID() ) );
						$obj_XML = $obj_XML->xpath("/fees/item[@toid = ". $obj_CountryConfig->getID() ."]");
						$obj_XML = $obj_XML[0];
						if (intval($obj_XML->basefee) + intval($obj_DOM->transfer[$i]->amount) * floatval($obj_XML->share) > intval($obj_XML->minfee) ) { $iFee = intval($obj_XML->basefee) + intval($obj_DOM->transfer[$i]->amount) * floatval($obj_XML->share); }
						else { $iFee = (integer) $obj_XML->minfee; }
						
						$obj_AccountXML = simplexml_load_string($obj_mPoint->getAccountInfo($iSenderAccountID) );
						
						if ($obj_Validator->valAmount( (integer) $obj_AccountXML->balance, (intval($obj_DOM->transfer[$i]->amount) + $iFee) / 100) < 10) { $aMsgCds[] =  $obj_Validator->valAmount( (integer) $obj_AccountXML->balance, (intval($obj_DOM->transfer[$i]->amount) + $iFee) / 100) + 44; }
					}
					else { $aMsgCds[] = $obj_Validator->valCountry($_OBJ_DB, (integer) $obj_DOM->transfer[$i]->amount["country-id"]) + 40; }
					if (count($obj_DOM->transfer[$i]->mobile) == 1)
					{
						if ($obj_Validator->valCountry($_OBJ_DB, (integer) $obj_DOM->transfer[$i]->mobile["country-id"]) == 10)
						{
							$obj_CC = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->transfer[$i]->mobile["country-id"]);
							$obj_Validator = new Validate($obj_CC);
							if ($obj_Validator->valMobile( (float) $obj_DOM->transfer[$i]->mobile) < 10) { $aMsgCds[] = $obj_Validator->valMobile( (float) $obj_DOM->transfer[$i]->mobile) + 54; } 
						}
						else { $aMsgCds[] = $obj_Validator->valCountry($_OBJ_DB, (integer) $obj_DOM->transfer[$i]->mobile["country-id"]) + 50; }
					}
					elseif (count($obj_DOM->transfer[$i]->email) == 1)
					{
						if ($obj_Validator->valEMail( (string) $obj_DOM->transfer[$i]->email) < 10) { $aMsgCds[] = $obj_Validator->valEMail( (string) $obj_DOM->transfer[$i]->email) + 60; } 
					}
					// Unknown Recipient
					else { $aMsgCds[] = 69; }
					
					// Input valid
					if (count($aMsgCds) == 0)
					{
						$code = General::authToken($iSenderAccountID, $obj_ClientConfig->getSecret(), $_COOKIE['token']);
						// Authentication succeeded
						if ($code >= 10)
						{
							// Generate new security token
							if ($code == 11) { setcookie("token", General::genToken($iSenderAccountID, $obj_ClientConfig->getSecret() ) ); }
							$code = $obj_mPoint->auth($iSenderAccountID, (string) $obj_DOM->transfer[$i]->password);
							// Authentication succeeded
							if ($code == 10 || ($code == 11 && $obj_ClientConfig->smsReceiptEnabled() === false) )
							{
								// National Transfer
								$obj_CC = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->transfer[$i]->amount["country-id"]);
								if ($obj_ClientConfig->getCountryConfig()->getID() == $obj_CC->getID() )
								{
									$iAmountSent = intval($obj_DOM->transfer[$i]->amount);
									$iAmountReceived = intval($obj_DOM->transfer[$i]->amount);
								}
								// International Remittance
								else
								{
									$iAmountSent = intval($obj_DOM->transfer[$i]->amount);
									$iAmountReceived = $obj_mPoint->convert($obj_CC, intval($obj_DOM->transfer[$i]->amount) );
								}
								
								$iRecipientAccountID = -1;
								if (count($obj_DOM->transfer[$i]->mobile) == 1)
								{
									$obj_Cfg = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->transfer[$i]->mobile["country-id"]);
									$iRecipientAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, (float) $obj_DOM->transfer[$i]->mobile, $obj_Cfg);
									if ($iRecipientAccountID < 0) { $iRecipientAccountID = $obj_mPoint->getAccountID($obj_Cfg, (float) $obj_DOM->transfer[$i]->mobile); }
								}
								elseif (count($obj_DOM->transfer[$i]->email) == 1)
								{
									$iRecipientAccountID = $obj_mPoint->getAccountID($obj_CC, (string) $obj_DOM->transfer[$i]->email);
								}
								$code = 0;
								// Recipient doesn't yet have an account
								if ($iRecipientAccountID <= 0)
								{
									// Recipient's Mobile Number provided by sender
									if (count($obj_DOM->transfer[$i]->mobile) == 1)
									{
										$mob = (string) $obj_DOM->transfer[$i]->mobile;
										$email = "";
									}
									// Recipient's E-Mail Address provided by sender
									elseif (count($obj_DOM->transfer[$i]->email) == 1)
									{
										$mob = "";
										$email = (string) $obj_DOM->transfer[$i]->email;
									}
									$iRecipientAccountID = $obj_mPoint->newAccount($obj_CountryConfig->getID(), $mob, "", $email);
									
									// Account successfully created - send notification SMS to recipient
									if ($iRecipientAccountID > 0 && empty($mob) === false)
									{
										// Send Account Creation notification via SMS
										if ($obj_mPoint->sendNewAccountSMS(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO), $obj_ClientConfig, $iRecipientAccountID, $obj_AccountXML, $iAmountReceived) == 10)
										{
											$code = 1;
										}
										// Error: Unable to send Account Creation notification via SMS
										else { $code = -3; }
									}
									// Account successfully created - send notification E-Mail to recipient
									elseif ($iRecipientAccountID > 0)
									{
										// Send Account Creation notification via E-Mail
										if ($obj_mPoint->sendNewAccountEMail($iRecipientAccountID, $obj_AccountXML, $iAmountReceived) == 10)
										{
											$code = 2;
										}
										// Error: Unable to send Account Creation notification via E-Mail
									else { $code = -3; }
									}
									// Error: Unable to create new account
									else { $iAmountReceived = -6; }
								}
								// Currency conversion successful for Amount - Verify that recipient's balance doesn't exceed allowed amount
								if ($iRecipientAccountID > 0 && $iAmountReceived > 0)
								{
									$obj_AccountXML = simplexml_load_string($obj_mPoint->getAccountInfo($iRecipientAccountID) );
									if (intval($obj_AccountXML->balance) + $iAmountReceived > $obj_CC->getMaxBalance() )
									{
										$iAmountReceived = -5;
									}
									if ($iAmountReceived > 0)
									{
										$obj_XML = simplexml_load_string($obj_mPoint->getFees(Constants::iTRANSFER_FEE, $obj_CC->getID() ) );
										$obj_XML = $obj_XML->xpath("/fees/item[@toid = ". $obj_CC->getID() ."]");
										$obj_XML = $obj_XML[0];
										if (intval($obj_XML->basefee) + intval($obj_DOM->transfer[$i]->amount) * floatval($obj_XML->share) > intval($obj_XML->minfee) ) { $iFee = intval($obj_XML->basefee) + intval($obj_DOM->transfer[$i]->amount) * floatval($obj_XML->share); }
										else { $iFee = (integer) $obj_XML->minfee; }
										// User hasn't completed registration yet
										/*
										if (General::xml2bool($obj_AccountXML->mobile["verified"]) === false)
										{
											if ($obj_mPoint->sendNewAccountSMS(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO), $obj_ClientConfig, $iRecipientAccountID, $obj_AccountXML, $iAmountReceived) == 10)
											{
												$code = 1;
											}
											// Error: Unable to send Account Creation notification via SMS
											else { $code = -3; }
										}
										*/
										$c = $obj_mPoint->makeTransfer($iRecipientAccountID, $iSenderAccountID, $iAmountReceived, $iAmountSent, $iFee, (string) $obj_DOM->transfer[$i]->message, $code == 0 ? Constants::iTRANSACTION_COMPLETED_STATE : Constants::iTRANSFER_PENDING_STATE);
										if ($c == 10) { $xml = '<status code="'. ($code + 100) .'">Success</status>'; }
										else
										{
											header("HTTP/1.1 500 Internal Server Error");
									
											$xml = '<status code="'. ($c+97) .'" />';
										}
									}
									else
									{
										header("HTTP/1.1 500 Internal Server Error");
									
										$xml = '<status code="'. (abs($iAmountReceived)+90) .'" />';
									}
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
								if ($code == 3) { $obj_mPoint->sendAccountDisabledNotification(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO), $obj_DOM->transfer[$i]->{'client-info'}->mobile); }
									
								header("HTTP/1.1 403 Forbidden");
									
								$xml = '<status code="'. ($code+30) .'" />';
							}						
						}
						// Authentication failed
						else
						{
							header("HTTP/1.1 403 Forbidden");
							
							$xml = '<status code="38">Invalid Security Token: '. $_COOKIE['token'] .'</status>';
						}
					}
					// Error in Input
					else
					{
						header("HTTP/1.1 400 Bad Request");
					
						foreach ($aMsgCds as $code)
						{
							$xml .= '<status code="'. $code .'" />';
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
	elseif (count($obj_DOM->transfer) == 0)
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