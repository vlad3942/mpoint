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
// Require specific Business logic for the DIBS component
require_once(sCLASS_PATH ."/dibs.php");
// Require general Business logic for the Cellpoint Mobile module
require_once(sCLASS_PATH ."/cpm.php");
// Require specific Business logic for the WannaFind component
require_once(sCLASS_PATH ."/wannafind.php");
// Require specific Business logic for the NetAxept component
require_once(sCLASS_PATH ."/netaxept.php");
// Require specific Business logic for the WorldPay component
require_once(sCLASS_PATH ."/worldpay.php");
// Require specific Business logic for the Emirates' Corporate Payment Gateway (CPG) component
require_once(sCLASS_PATH ."/cpg.php");
// Require specific Business logic for the VISA checkout component
require_once(sCLASS_PATH ."/visacheckout.php");
if (function_exists("json_encode") === true)
{
	// Require specific Business logic for the Stripe component
	require_once(sCLASS_PATH ."/stripe.php");
}
// Require specific Business logic for the Adyen component
require_once(sCLASS_PATH ."/adyen.php");

ignore_user_abort(true);
set_time_limit(120);

$aMsgCds = array();

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );
/*
$_SERVER['PHP_AUTH_USER'] = "1415";
$_SERVER['PHP_AUTH_PW'] = "Ghdy4_ah1G";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<authorize-payment client-id="10019">';
$HTTP_RAW_POST_DATA .= '<transaction id="1814929">';
$HTTP_RAW_POST_DATA .= '<card id="66597" type-id="7">';
$HTTP_RAW_POST_DATA .= '<amount country-id="100">100</amount>';
//$HTTP_RAW_POST_DATA .= '<card-number>5272342200069702</card-number>';
//$HTTP_RAW_POST_DATA .= '<expiry>03/31</expiry>';
//$HTTP_RAW_POST_DATA .= '<cryptogram type="3ds">AKh96OOsGf2HAIDEhKulAoABFA==</cryptogram>';
$HTTP_RAW_POST_DATA .= '</card>';
$HTTP_RAW_POST_DATA .= '</transaction>';
$HTTP_RAW_POST_DATA .= '<password>oisJona</password>';
$HTTP_RAW_POST_DATA .= '<client-info platform="iOS" version="1.00" language="da">';
$HTTP_RAW_POST_DATA .= '<mobile country-id="100" operator-id="10000">28882861</mobile>';
$HTTP_RAW_POST_DATA .= '<email>jona@oismail.com</email>';
$HTTP_RAW_POST_DATA .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
$HTTP_RAW_POST_DATA .= '</client-info>';
$HTTP_RAW_POST_DATA .= '</authorize-payment>';
$HTTP_RAW_POST_DATA .= '</root>';
*/
$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mpoint.xsd") === true && count($obj_DOM->{'authorize-payment'}) > 0)
	{
		$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);

		$xml = '';
		for ($i=0; $i<count($obj_DOM->{'authorize-payment'}); $i++)
		{
			// Set Global Defaults
			if (empty($obj_DOM->{'authorize-payment'}[$i]["account"]) === true || intval($obj_DOM->{'authorize-payment'}[$i]["account"]) < 1) { $obj_DOM->{'authorize-payment'}[$i]["account"] = -1; }

			// Validate basic information
			$code = Validate::valBasic($_OBJ_DB, (integer) $obj_DOM->{'authorize-payment'}[$i]["client-id"], (integer) $obj_DOM->{'authorize-payment'}[$i]["account"]);
			if ($code == 100)
			{
				$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'authorize-payment'}[$i]["client-id"], (integer) $obj_DOM->{'authorize-payment'}[$i]["account"]);
				// Client successfully authenticated
 				if ($obj_ClientConfig->getUsername() == trim($_SERVER['PHP_AUTH_USER']) && $obj_ClientConfig->getPassword() == trim($_SERVER['PHP_AUTH_PW'])
 					&& $obj_ClientConfig->hasAccess($_SERVER['REMOTE_ADDR']) === true)
				{
					$obj_TxnInfo = TxnInfo::produceInfo( (integer) $obj_DOM->{'authorize-payment'}[$i]->transaction["id"], $_OBJ_DB);
					// Re-Intialise Text Translation Object based on transaction
					$_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH . $obj_TxnInfo->getLanguage() ."/global.txt", sLANGUAGE_PATH . $obj_TxnInfo->getLanguage() ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");
					$obj_mPoint = new EndUserAccount($_OBJ_DB, $_OBJ_TXT, $obj_ClientConfig);

					// Payment has not previously been attempted for transaction
					$_OBJ_DB->query("START TRANSACTION");
					if (count($obj_mPoint->getMessageData($obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE, true) ) == 0)
					{
						// Add control state and immediately commit database transaction
						$obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE, "");
						$_OBJ_DB->query("COMMIT");
						$obj_XML = simpledom_load_string($obj_mPoint->getStoredCards($obj_TxnInfo->getAccountID(), $obj_ClientConfig, true) );

						for ($j=0; $j<count($obj_DOM->{'authorize-payment'}[$i]->transaction->card); $j++)
						{
//							$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->amount["country-id"]);
//							if ( ($obj_CountryConfig instanceof CountryConfig) === false) { $obj_CountryConfig = $obj_ClientConfig->getCountryConfig(); }
							$obj_Validator = new Validate($obj_ClientConfig->getCountryConfig() );
							if (count($obj_DOM->{'authorize-payment'}[$i]->{'auth-token'}) == 0 && count($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->cryptogram) == 0)
							{
								if ($obj_Validator->valPassword( (string) $obj_DOM->{'authorize-payment'}[$i]->password) != 10) { $aMsgCds[] = $obj_Validator->valPassword( (string) $obj_DOM->{'authorize-payment'}[$i]->password) + 25; }
							}
							$iTypeID = intval($obj_DOM->{'authorize-payment'}[$i]->transaction["type-id"]);
							// Authorize Purchase using Stored Value Account
							if ($iTypeID == Constants::iCARD_PURCHASE_TYPE && count($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->cryptogram) == 0 && intval($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]["id"]) > 0 && $obj_Validator->valStoredCard($_OBJ_DB, $obj_TxnInfo->getAccountID(), $obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]["id"]) < 10) { $aMsgCds[] = $obj_Validator->valStoredCard($_OBJ_DB, $obj_TxnInfo->getAccountID(), $obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]["id"]) + 40; }

							// Success: Input Valid
							if (count($aMsgCds) == 0)
							{
								// Single Sign-On
								if (count($obj_DOM->{'authorize-payment'}[$i]->{'auth-token'}) == 1 && strlen($obj_TxnInfo->getAuthenticationURL() ) > 0)
								{
									$code = $obj_mPoint->auth(HTTPConnInfo::produceConnInfo($obj_TxnInfo->getAuthenticationURL() ), CustomerInfo::produceInfo($_OBJ_DB, $obj_TxnInfo->getAccountID() ), trim($obj_DOM->{'authorize-payment'}[$i]->{'auth-token'}) );
								}
								// Authentication is not required for payment methods that are sending a cryptogram
								elseif (count($obj_DOM->{'authorize-payment'}[$i]->password) == 0 && count($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->cryptogram) == 1)
								{
									$code = 10;
								}
								else { $code = $obj_mPoint->auth($obj_TxnInfo->getAccountID(), (string) $obj_DOM->{'authorize-payment'}[$i]->password); }
								// Authentication succeeded
								if ($code == 10 || ($code == 11 && $obj_ClientConfig->smsReceiptEnabled() === false) )
								{
									if ($obj_TxnInfo->getMobile() > 0) { $obj_mPoint->saveMobile($obj_TxnInfo->getAccountID(), $obj_TxnInfo->getMobile(), true); }
									switch ($iTypeID)
									{
									case (Constants::iPURCHASE_USING_EMONEY):	// Authorize Purchase using Stored Value Account
									case (Constants::iPURCHASE_USING_POINTS):
										$obj_XML = simplexml_load_string($obj_mPoint->getAccountInfo($obj_TxnInfo->getAccountID() ) );
										if ($iTypeID == Constants::iPURCHASE_USING_EMONEY && intval($obj_XML->balance) < $obj_TxnInfo->getAmount() )
										{
											$code = 1;
											$xml .= '<status code="'. ($code+50) .'">Insufficient balance on e-money account</status>';
										}
										elseif ($iTypeID == Constants::iPURCHASE_USING_POINTS && intval($obj_XML->points) < $obj_TxnInfo->getPoints() )
										{
											$code = 2;
											$xml .= '<status code="'. ($code+50) .'">Insufficient points on loyalty account</status>';
										}
										elseif ( ($iTypeID == Constants::iPURCHASE_USING_EMONEY && $obj_TxnInfo->getTypeID() == Constants::iTOPUP_OF_EMONEY)
												|| ($iTypeID == Constants::iPURCHASE_USING_POINTS && $obj_TxnInfo->getTypeID() == Constants::iTOPUP_OF_POINTS) )
										{
											$code = 9;
											$xml .= '<status code="'. ($code+50) .'">Authorization using: '. $iTypeID .' is not supported for transaction type: '. $obj_TxnInfo->getTypeID() .'</status>';
										}
										// Sufficient balance / points on Stored Value Account
										if ($code >= 10)
										{
											$iAmount = $obj_TxnInfo->getAmount();
											if ($iTypeID == Constants::iPURCHASE_USING_POINTS) { $iAmount = $obj_TxnInfo->getPoints(); }
											// Complete the purchase using the user's Stored Value Account
											if ($obj_mPoint->purchase( (integer) $obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]["id"], (integer) $obj_DOM->{'authorize-payment'}[$i]->transaction["type-id"], $obj_TxnInfo->getID(), $iAmount) )
											{
												try
												{
													$obj_PSP = new CellpointMobile($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo);
													// Initialise Callback to Client
													$obj_PSP->initCallback(HTTPConnInfo::produceConnInfo($aCPM_CONN_INFO), Constants::iWALLET, Constants::iPAYMENT_ACCEPTED_STATE);
												}
												catch (HTTPException $ignore) { /* Ignore */ }
												if ($iTypeID == Constants::iPURCHASE_USING_POINTS) { $xml .= '<status code="102">Payment Authorized using Loyalty Account (Points)</status>'; }
												else { $xml .= '<status code="101">Payment Authorized using Pre-Paid Account (E-Money)</status>'; }
											}
											// Error: Unable to debit account
											else
											{
												$obj_mPoint->delMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);

												header("HTTP/1.1 500 Internal Server Error");

												$xml .= '<status code="91">Unable to debit account</status>';
											}
										}
										// Error: Insufficient balance / points on Stored Value Account
										else
										{
											$obj_mPoint->delMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);

											header("HTTP/1.1 400 Bad Request");
										}
										break;
									case (Constants::iCARD_PURCHASE_TYPE):		// Authorize Purchase using Stored Card
									default:
										if (intval($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]["type-id"]) == Constants::iAPPLE_PAY)
										{
											$obj_Elem = $obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j];
											
											$obj_CC = new CreditCard($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo);
											$obj_XML = simpledom_load_string($obj_CC->getCards($obj_TxnInfo->getAmount() ) );
											$obj_XML = $obj_XML->xpath("/cards/item[@type-id = ". Constants::iAPPLE_PAY ."]");
											$obj_Elem["pspid"] = (integer) $obj_XML["pspid"];
										}
										else
										{
											$obj_Elem = $obj_XML->xpath("/stored-cards/card[@id = ". $obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]["id"] ."]");
											if (count($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->cvc) == 1) { $obj_Elem->cvc = (integer) $obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->cvc; }	
										}
										
										if(count($obj_Elem->mask) == 1) { $code = $obj_Validator->valIssuerIdentificationNumber($_OBJ_DB, $obj_ClientConfig->getID(), substr(str_replace(" ", "", $obj_Elem->mask), 0, 6) ); }
										else { $code = 10; }
										if ($code >= 10)
										{
											try
											{
												switch (intval($obj_Elem["pspid"]) )
												{
												case (Constants::iSTRIPE_PSP):
													$obj_PSP = new Stripe_PSP($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, array() );
													$aLogin = $obj_PSP->getMerchantLogin($obj_TxnInfo->getClientConfig()->getID(), Constants::iSTRIPE_PSP, true);
													
													$code =	$obj_PSP->authTicket( (integer) $obj_Elem->ticket, $aLogin["password"]);
													if ($code == "OK")
													{
														if ($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]["type-id"] === Constants::iAPPLE_PAY) { $xml .= '<status code="100">Payment Authorized using Apple Pay</status>'; }
														else { $xml .= '<status code="100">Payment Authorized using Stored Card</status>'; }
													}
													// Error: Authorization declined
													else
													{
														$obj_mPoint->delMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);
													
														header("HTTP/1.1 502 Bad Gateway");
													
														$xml .= '<status code="92">Authorization failed, Stripe returned error: '. $code .'</status>';
													}
													break;
												case (Constants::iWORLDPAY_PSP):
													// Authorise payment with PSP based on Ticket
													$obj_PSP = new WorldPay($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO["worldpay"]);
													if ($obj_TxnInfo->getMode() > 0) { $aHTTP_CONN_INFO["worldpay"]["host"] = str_replace("secure.", "secure-test.", $aHTTP_CONN_INFO["worldpay"]["host"]); }
													$aLogin = $obj_PSP->getMerchantLogin($obj_TxnInfo->getClientConfig()->getID(), Constants::iWORLDPAY_PSP, true);
													$aHTTP_CONN_INFO["worldpay"]["username"] = $aLogin["username"];
													$aHTTP_CONN_INFO["worldpay"]["password"] = $aLogin["password"];
						
													$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["worldpay"]);
													$obj_XML = $obj_PSP->authTicket($obj_ConnInfo, $obj_Elem);
													// Authorization succeeded
													if (is_null($obj_XML) === false && ($obj_XML instanceof SimpleXMLElement) === true && intval($obj_XML["code"]) == Constants::iPAYMENT_ACCEPTED_STATE)
													{
														try
														{
															// Initialise Callback to Client
															$aCPM_CONN_INFO["path"] = "/callback/worldpay.php";
															$aCPM_CONN_INFO["contenttype"] = "text/xml";
															$obj_PSP->initCallback(HTTPConnInfo::produceConnInfo($aCPM_CONN_INFO), $obj_XML);
														}
														catch (HTTPException $ignore) { /* Ignore */ }
														$xml = '<status code="100">Payment Authorized using Stored Card</status>';	
													}	
													
													else
													{
														$obj_mPoint->delMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);
														
														header("HTTP/1.1 502 Bad Gateway");
														
														$xml .= '<status code="92">Authorization failed, WorldPay returned error code: '. $obj_XML->reply->error["code"] .'</status>';
														
														$obj_mPoint->delMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);
													}
													break;
												case (Constants::iDIBS_PSP):	// DIBS
													// Authorise payment with PSP based on Ticket
													$obj_PSP = new DIBS($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO['dibs']);
													$iTxnID = $obj_PSP->authTicket( (integer) $obj_Elem->ticket);
													// Authorization succeeded
													if ($iTxnID > 0)
													{
														try
														{
															// Initialise Callback to Client
															$aCPM_CONN_INFO["path"] = "/callback/dibs.php";
															$obj_PSP->initCallback(HTTPConnInfo::produceConnInfo($aCPM_CONN_INFO), intval($obj_Elem->type["id"]), $iTxnID, (string) $obj_Elem->mask, (string) $obj_Elem->expiry);
														}
														catch (HTTPException $ignore) { /* Ignore */ }
	
														$xml = '<status code="100">Payment Authorized using Stored Card</status>';
													}
													// Error: Authorization declined
													else
													{
														$obj_mPoint->delMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);
	
														header("HTTP/1.1 502 Bad Gateway");
	
														$xml .= '<status code="92">Authorization failed, DIBS returned error code'. $iTxnID .'</status>';
													}
													break;
												case (Constants::iWANNAFIND_PSP):	// WannaFind
													// Authorise payment with PSP based on Ticket
													$obj_PSP = new WannaFind($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO["wannafind"]);
													$iTxnID = $obj_PSP->authTicket( (integer) $obj_Elem->ticket);
													// Authorization succeeded
													if ($iTxnID > 0)
													{
														try
														{
															// Initialise Callback to Client
															$aCPM_CONN_INFO["path"] = "/callback/wannafind.php";
															$obj_PSP->initCallback(HTTPConnInfo::produceConnInfo($aCPM_CONN_INFO), intval($obj_Elem->type["id"]), $iTxnID);
														}
														catch (HTTPException $ignore) { /* Ignore */ }
	
														$xml .= '<status code="100">Payment Authorized using Stored Card</status>';
													}
													// Error: Authorization declined
													else
													{
														$obj_mPoint->delMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);
	
														header("HTTP/1.1 502 Bad Gateway");
	
														$xml .= '<status code="92">Authorization failed, WannaFind returned error code'. $iTxnID .'</status>';
													}
													break;
												case (Constants::iNETAXEPT_PSP): // NetAxept
													$obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), Constants::iNETAXEPT_PSP);
													$obj_PSP = new NetAxept($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO["netaxept"], $obj_PSPConfig);
	
													if ($obj_TxnInfo->getMode() > 0) { $aHTTP_CONN_INFO["netaxept"]["host"] = str_replace("epayment.", "epayment-test.", $aHTTP_CONN_INFO["netaxept"]["host"]); }
													$aHTTP_CONN_INFO["netaxept"]["username"] = $obj_PSPConfig->getUsername();
													$aHTTP_CONN_INFO["netaxept"]["password"] = $obj_PSPConfig->getPassword();
													$oCI = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["netaxept"]);
	
													$code = $obj_PSP->authTicket($obj_Elem->ticket, $oCI, $obj_PSPConfig->getMerchantAccount() );
													// Authorization succeeded
													if ($code == "OK")
													{
														$xml .= '<status code="100">Payment Authorized using Stored Card</status>';
													}
													// Error: Authorization declined
													else
													{
														$obj_mPoint->delMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);
	
														header("HTTP/1.1 502 Bad Gateway");
	
														$xml .= '<status code="92">Authorization failed, NetAxcept returned error: '. $code .'</status>';
													}
													break;
												case (Constants::iCPG_PSP):
													$obj_PSP = new CPG($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO["cpg"]);
													$obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), Constants::iCPG_PSP);
	
													$aHTTP_CONN_INFO["cpg"]["username"] = $obj_PSPConfig->getUsername();
													$aHTTP_CONN_INFO["cpg"]["password"] = $obj_PSPConfig->getPassword();
													$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["cpg"]);
	
													$xml .= $obj_PSP->authTicket($obj_ConnInfo, $obj_Elem);
													break;
												case (Constants::iADYEN_PSP): // NetAxept
														$obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), Constants::iADYEN_PSP);
														
														$obj_PSP = new Adyen($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO["adyen"]);
													
														$code = $obj_PSP->authTicket($obj_PSPConfig ,$obj_Elem->ticket);
														// Authorization succeeded
														if ($code == "100")
														{
															$xml .= '<status code="100">Payment Authorized using Stored Card</status>';
														}
														// Error: Authorization declined
														else
														{
															$obj_mPoint->delMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);
													
															header("HTTP/1.1 502 Bad Gateway");
													
															$xml .= '<status code="92">Authorization failed, Adyen returned error: '. $code .'</status>';
														}
														break;
												default:	// Unkown Error
													$obj_mPoint->delMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);
	
													header("HTTP/1.1 500 Internal Server Error");
	
													$xml .= '<status code="99">Unknown Payment Service Provider: '. $obj_Elem["pspid"] .'</status>';
													break;
												}
											}
											catch (HTTPException $e)
											{
												$obj_mPoint->delMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);
	
												header("HTTP/1.1 504 Gateway Timeout");
	
												$xml = '<status code="90">'. htmlspecialchars($e->getTraceAsString(), ENT_NOQUOTES) .'</status>';
											}
										}
										// Error: Card has been blocked
										else
										{
											$obj_mPoint->delMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);
											header("HTTP/1.1 403 Forbidden");
		
											$xml = '<status code="'. ($code+85) .'">Card has been blocked</status>';
										}
									}
								}
								// Authentication succeeded - But Mobile number not verified
								elseif ($code == 11)
								{
									$obj_mPoint->delMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);
									header("HTTP/1.1 403 Forbidden");

									$xml = '<status code="37">Mobile number not verified</status>';
								}
								// Authentication failed
								else
								{
									$obj_mPoint->delMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);
									// Account disabled due to too many failed login attempts
									if ($code == 3)
									{
										// Remove End-User's Account ID from transaction log
										$obj_TxnInfo->setAccountID(-1);
										$obj_mPoint->logTransaction($obj_TxnInfo);
										$obj_mPoint->sendAccountDisabledNotification(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO), $obj_TxnInfo->getMobile() );
									}

									header("HTTP/1.1 403 Forbidden");

									$xml = '<status code="'. ($code+30) .'" />';
								}
							}
							// Error in Input
							else
							{
								$obj_mPoint->delMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);

								header("HTTP/1.1 400 Bad Request");

								foreach ($aMsgCds as $code)
								{
									$xml .= '<status code="'. $code .'" />';
								}
							}
						}	// End card loop
					}
					else
					{
						$_OBJ_DB->query("COMMIT");

						$xml .= '<status code="103">Authorization already in progress</status>';
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
	elseif (count($obj_DOM->{'authorize-payment'}) == 0)
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