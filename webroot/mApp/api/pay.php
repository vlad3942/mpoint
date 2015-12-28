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
 * @version 1.20
 */

// Require Global Include File
require_once("../../inc/include.php");

// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");

// Require data class for Payment Service Provider Configurations
require_once(sCLASS_PATH ."/pspconfig.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require Business logic for the Mobile Web module
require_once(sCLASS_PATH ."/mobile_web.php");
// Require Business logic for the Select Credit Card component
require_once(sCLASS_PATH ."/credit_card.php");

// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for the CPM PSP component
require_once(sINTERFACE_PATH ."/cpm_psp.php");
// Require specific Business logic for the DIBS component
require_once(sCLASS_PATH ."/dibs.php");
// Require specific Business logic for the WorldPay component
require_once(sCLASS_PATH ."/worldpay.php");
// Require specific Business logic for the PayEx component
require_once(sCLASS_PATH ."/payex.php");
// Require specific Business logic for the NetAxept component
require_once(sCLASS_PATH ."/netaxept.php");
// Require specific Business logic for the Stripe component
if (function_exists("json_encode") === true && function_exists("curl_init") === true)
{
   require_once(sCLASS_PATH ."/stripe.php");
}
// Require specific Business logic for the MobilePay component
require_once(sCLASS_PATH ."/mobilepay.php");
// Require specific Business logic for the Adyen component
require_once(sCLASS_PATH ."/adyen.php");
// Require specific Business logic for the VISA checkout component
require_once(sCLASS_PATH ."/visacheckout.php");
// Require specific Business logic for the Apple Pay component
require_once(sCLASS_PATH ."/applepay.php");
// Require specific Business logic for the Data Cash component
require_once(sCLASS_PATH ."/datacash.php");
// Require specific Business logic for the Master Pass component
require_once(sCLASS_PATH ."/masterpass.php");
// Require specific Business logic for the AMEX Express Checkout component
require_once(sCLASS_PATH ."/amexexpresscheckout.php");

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
$HTTP_RAW_POST_DATA .= '<pay client-id="10001">';
$HTTP_RAW_POST_DATA .= '<transaction id="1002469" store-card="false">';
$HTTP_RAW_POST_DATA .= '<card type-id="16">';
$HTTP_RAW_POST_DATA .= '<amount country-id="200">200</amount>';
$HTTP_RAW_POST_DATA .= '<issuer-identification-number>500191</issuer-identification-number>';
$HTTP_RAW_POST_DATA .= '</card>';
$HTTP_RAW_POST_DATA .= '</transaction>';
$HTTP_RAW_POST_DATA .= '<client-info platform="iOS" version="1.00" language="da">';
$HTTP_RAW_POST_DATA .= '<mobile country-id="100" operator-id="10000">28882861</mobile>';
$HTTP_RAW_POST_DATA .= '<email>jona@oismail.com</email>';
$HTTP_RAW_POST_DATA .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
$HTTP_RAW_POST_DATA .= '</client-info>';
$HTTP_RAW_POST_DATA .= '</pay>';
$HTTP_RAW_POST_DATA .= '</root>';
*/
$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mpoint.xsd") === true && count($obj_DOM->pay) > 0)
	{
		$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);

		for ($i=0; $i<count($obj_DOM->pay); $i++)
		{
			// Set Global Defaults
			if (empty($obj_DOM->pay[$i]["account"]) === true || intval($obj_DOM->pay[$i]["account"]) < 1) { $obj_DOM->pay[$i]["account"] = -1; }

			// Validate basic information
			$code = Validate::valBasic($_OBJ_DB, (integer) $obj_DOM->pay[$i]["client-id"], (integer) $obj_DOM->pay[$i]["account"]);
			if ($code == 100)
			{
				$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->pay[$i]["client-id"], (integer) $obj_DOM->pay[$i]["account"]);
				
				// Client successfully authenticated
 				if ($obj_ClientConfig->getUsername() == trim($_SERVER['PHP_AUTH_USER']) && $obj_ClientConfig->getPassword() == trim($_SERVER['PHP_AUTH_PW'])
					&& $obj_ClientConfig->hasAccess($_SERVER['REMOTE_ADDR']) === true)
				{
					$obj_Validator = new Validate($obj_ClientConfig->getCountryConfig() );
					$obj_TxnInfo = TxnInfo::produceInfo($obj_DOM->pay[$i]->transaction["id"], $_OBJ_DB);
					$aObj_PSPConfigs = array();
					for ($j=0; $j<count($obj_DOM->pay[$i]->transaction->card); $j++)
					{
						$obj_mPoint = new CreditCard($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo);
//						$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->pay[$i]->transaction->card[$j]->amount["country-id"]);
//						if ( ($obj_CountryConfig instanceof CountryConfig) === false) { $obj_CountryConfig = $obj_ClientConfig->getCountryConfig(); }

						if (count($obj_DOM->pay[$i]->transaction->card[$j]->{'issuer-identification-number'}) == 1)
						{
							$code = $obj_Validator->valIssuerIdentificationNumber($_OBJ_DB, $obj_ClientConfig->getID(), (integer) $obj_DOM->pay[$i]->transaction->card[$j]->{'issuer-identification-number'});
						}
						else { $code = 10; }
						
						if ($code >= 10)
						{
							if ($obj_TxnInfo->getAccountID() == -1 && General::xml2bool($obj_DOM->pay[$i]->transaction["store-card"]) === true)
							{
								$iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_TxnInfo->getCountryConfig(), trim($obj_DOM->{'pay'}[$i]->{'client-info'}->{'customer-ref'}), (float) $obj_DOM->{'pay'}[$i]->{'client-info'}->mobile, trim($obj_DOM->{'pay'}[$i]->{'client-info'}->email) );
							
								//	Create a new user as some PSP's needs our End-User Account ID for storing cards
								if ($iAccountID < 0)
								{
									$obj_EUA = new EndUserAccount($_OBJ_DB, $_OBJ_TXT, $obj_ClientConfig);
									$iAccountID = $obj_EUA->newAccount($obj_ClientConfig->getCountryConfig()->getID(),
																	   (float) $obj_DOM->{'pay'}[$i]->{'client-info'}->mobile,
																	   "",
																	   trim($obj_DOM->{'pay'}[$i]->{'client-info'}->email),
																	   trim($obj_DOM->{'pay'}[$i]->{'client-info'}->{'customer-ref'}) );
								}
								$obj_TxnInfo->setAccountID($iAccountID);
							}
							$obj_PSPConfig = null;
							switch (intval($obj_DOM->pay[$i]->transaction->card[$j]["type-id"]) )
							{
							case (Constants::iAPPLE_PAY):	// 3rd Party Wallet: Apple Pay
								$obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_ClientConfig->getID(), $obj_ClientConfig->getAccountConfig()->getID(), Constants::iAPPLE_PAY_PSP);
								break;
							case (Constants::iVISA_CHECKOUT_WALLET):	// 3rd Party Wallet: VISA Checkout
								$obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_ClientConfig->getID(), $obj_ClientConfig->getAccountConfig()->getID(), Constants::iVISA_CHECKOUT_PSP);
								break;
							case (Constants::iMASTER_PASS_WALLET):	// 3rd Party Wallet: Master Pass
								$obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_ClientConfig->getID(), $obj_ClientConfig->getAccountConfig()->getID(), Constants::iMASTER_PASS_PSP);
								break;
							case (Constants::iAMEX_EXPRESS_CHECKOUT_WALLET):	// 3rd Party Wallet: AMEX Express Checkout
								$obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_ClientConfig->getID(), $obj_ClientConfig->getAccountConfig()->getID(), Constants::iAMEX_EXPRESS_CHECKOUT_PSP);
								break;
							default:	// Standard Payment Service Provider
								// Find Configuration for Payment Service Provider
								$obj_XML = simpledom_load_string($obj_mPoint->getCards( (integer) $obj_DOM->pay[$i]->transaction->card[$j]->amount) );
								// Determine Payment Service Provider based on selected card
								$obj_Elem = $obj_XML->xpath("/cards/item[@id = ". intval($obj_DOM->pay[$i]->transaction->card[$j]["type-id"]) ."]");
								if (array_key_exists(intval($obj_Elem["pspid"]), $aObj_PSPConfigs) === false)
								{
									$aObj_PSPConfigs[intval($obj_Elem["pspid"])] = PSPConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->pay[$i]["client-id"],  (integer) $obj_DOM->pay[$i]["account"], (integer) $obj_Elem["pspid"]);
								}
								$obj_PSPConfig = $aObj_PSPConfigs[intval($obj_Elem["pspid"])];
								break;
							}
							
							// Success: Payment Service Provider Configuration found
							if ( ($obj_PSPConfig instanceof PSPConfig) === true)
							{
								try
								{
									// TO DO: Extend to add support for Split Tender
									$data['amount'] = (integer) $obj_DOM->pay[$i]->transaction->card[$j]->amount;
									$oTI = TxnInfo::produceInfo($obj_TxnInfo->getID(), $obj_TxnInfo, $data);
									// Initialize payment with Payment Service Provider
									$xml = '<psp-info id="'. $obj_PSPConfig->getID() .'" merchant-account="'. htmlspecialchars($obj_PSPConfig->getMerchantAccount(), ENT_NOQUOTES) .'">';
									switch ($obj_PSPConfig->getID() )
									{
									case (Constants::iDIBS_PSP):
										$obj_PSP = new DIBS($_OBJ_DB, $_OBJ_TXT, $oTI, $aHTTP_CONN_INFO['dibs']);
	
										$aHTTP_CONN_INFO["dibs"]["path"] = str_replace("{account}", $obj_PSPConfig->getMerchantAccount(), $aHTTP_CONN_INFO["dibs"]["path"]);
										$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["dibs"]);
										$obj_XML = $obj_PSP->initialize($obj_ConnInfo, $obj_PSPConfig->getMerchantAccount(), $obj_PSPConfig->getMerchantSubAccount(), (string) $obj_Elem->currency, (integer) $obj_DOM->pay[$i]->transaction->card[$j]["type-id"]);
										foreach ($obj_XML->children() as $obj_XMLElem)
										{
											// Hidden Fields
											if (count($obj_XMLElem->children() ) > 0)
											{
												$xml .= '<'. $obj_XMLElem->getName() .'>';
												foreach ($obj_XMLElem->children() as $obj_Child)
												{
													$xml .= $obj_Child->asXML();
												}
												$xml .= '</'. $obj_XMLElem->getName() .'>';
											}
											else { $xml .= $obj_XMLElem->asXML(); }
										}
										break;
									case (Constants::iWORLDPAY_PSP):
										// Construct list of cards supported by the Payment Service Provider
										$aCards = array();
										foreach ($obj_XML->children() as $obj_XMLElem)
										{
											if ($obj_PSPConfig->getID() == $obj_XMLElem["pspid"]) { $aCards[] = $obj_XMLElem["type-id"]; }
										}
										$obj_PSP = new WorldPay($_OBJ_DB, $_OBJ_TXT, $oTI, $aHTTP_CONN_INFO["worldpay"]);
										if ($obj_TxnInfo->getMode() > 0) { $aHTTP_CONN_INFO["worldpay"]["host"] = str_replace("secure.", "secure-test.", $aHTTP_CONN_INFO["worldpay"]["host"]); }
										$aMerchantAccount =  $obj_PSP->getMerchantLogin($obj_DOM->pay[$i]["client-id"], Constants::iWORLDPAY_PSP);
										$aHTTP_CONN_INFO["worldpay"]["username"] = $aMerchantAccount["username"];
										$aHTTP_CONN_INFO["worldpay"]["password"] = $aMerchantAccount["password"];
										$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["worldpay"]);
										// Redirect XML API
										$url = $obj_PSP->initialize($obj_ConnInfo, $aMerchantAccount["username"], $obj_PSPConfig->getMerchantSubAccount(), (string) $obj_Elem->currency, $aCards);
										
										if (General::xml2bool($obj_DOM->pay[$i]->transaction["store-card"]) === true) { $obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iTICKET_CREATED_STATE, ""); }
											
										$url .= "&preferredPaymentMethod=". $obj_PSP->getCardName( (integer) $obj_DOM->pay[$i]->transaction->card[$j]["type-id"]) ."&language=". $obj_TxnInfo->getLanguage();
										$xml .= '<url method="get" content-type="none">'. htmlspecialchars($url, ENT_NOQUOTES) .'</url>';
										break;
									case (Constants::iPAYEX_PSP):
										$obj_PSP = new PayEx($_OBJ_DB, $_OBJ_TXT, $oTI, $aHTTP_CONN_INFO["payex"]);
	
										if ($obj_TxnInfo->getMode() > 0) { $aHTTP_CONN_INFO["payex"]["host"] = str_replace("external.", "test-external.", $aHTTP_CONN_INFO["payex"]["host"]); }
										$aHTTP_CONN_INFO["payex"]["username"] = $obj_PSPConfig->getUsername();
										$aHTTP_CONN_INFO["payex"]["password"] = $obj_PSPConfig->getPassword();
										$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["payex"]);
										$obj_XML = $obj_PSP->initialize($obj_ConnInfo, $obj_PSPConfig->getMerchantAccount(), (string) $obj_Elem->currency);
										foreach ($obj_XML->children() as $obj_XMLElem)
										{
											// Hidden Fields
											if (count($obj_XMLElem->children() ) > 0)
											{
												$xml .= '<'. $obj_XMLElem->getName() .'>';
												foreach ($obj_XMLElem->children() as $obj_Child)
												{
													$xml .= $obj_Child->asXML();
												}
												$xml .= '</'. $obj_XMLElem->getName() .'>';
											}
											else { $xml .= $obj_XMLElem->asXML(); }
										}
										break;
									case (Constants::iWANNAFIND_PSP):
										break;
									case (Constants::iNETAXEPT_PSP):
										$obj_PSP = new NetAxept($_OBJ_DB, $_OBJ_TXT, $oTI, $aHTTP_CONN_INFO["netaxept"], $obj_PSPConfig);
	
										if ($obj_TxnInfo->getMode() > 0) { $aHTTP_CONN_INFO["netaxept"]["host"] = str_replace("epayment.", "epayment-test.", $aHTTP_CONN_INFO["netaxept"]["host"]); }
										$aHTTP_CONN_INFO["netaxept"]["username"] = $obj_PSPConfig->getUsername();
										$aHTTP_CONN_INFO["netaxept"]["password"] = $obj_PSPConfig->getPassword();
	
										$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["netaxept"]);
										// get boolean value of store card.
										$storecard = (strcasecmp($obj_DOM->pay[$i]->transaction["store-card"], "true") == 0 );
										$obj_XML = $obj_PSP->initialize($obj_ConnInfo,
																		$obj_PSPConfig->getMerchantAccount(),
																		$obj_PSPConfig->getMerchantSubAccount(),
																		(string) $obj_Elem->currency,
																		(integer) $obj_DOM->pay[$i]->transaction->card[$j]["type-id"],
																		$storecard);
	
										foreach ($obj_XML->children() as $obj_XMLElem)
										{
											$xml .= trim($obj_XMLElem->asXML() );
										}
										break;
									case (Constants::iSTRIPE_PSP):
										$obj_PSP = new Stripe_PSP($_OBJ_DB, $_OBJ_TXT, $oTI, array() );
										$aLogin = $obj_PSP->getMerchantLogin($obj_TxnInfo->getClientConfig()->getID(), Constants::iSTRIPE_PSP, false);
										$storecard = (strcasecmp($obj_DOM->pay[$i]->transaction["store-card"], "true") == 0 );
										$code =	$obj_PSP->auth( $obj_DOM->pay[$i]->transaction->card[$j]->{'apple-pay-token'}, $aLogin["password"], (integer) $obj_DOM->pay[$i]->transaction->card[$j]["type-id"], $storecard);
										if ($code >= 2000)
										{
											if ($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]["type-id"] === Constants::iAPPLE_PAY) { $xml .= '<status code="'. $code .'">Payment Authorized using Apple Pay</status>'; }
											else { $xml .= '<status code="'. $code .'">Payment Authorized</status>'; }
										}
										// Error: Authorization declined
										else
										{
											$obj_mPoint->delMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);
												
											header("HTTP/1.1 502 Bad Gateway");
											$xml .= '<status code="92">Authorization failed, Stripe returned error: '. $code .'</status>';
										}
										break;
									case (Constants::iMOBILEPAY_PSP):
										
										$obj_PSP = new MobilePay($_OBJ_DB, $_OBJ_TXT, $oTI, $aHTTP_CONN_INFO["mobilepay"]);
										$obj_XML = $obj_PSP->initialize($obj_PSPConfig);
										foreach ($obj_XML->children() as $obj_XMLElem)
										{
											$xml .= trim($obj_XMLElem->asXML() );
										}
										break;
									case (Constants::iCPG_PSP):
										if (intval($obj_DOM->pay[$i]->transaction->card[$j]["type-id"]) === Constants::iAPPLE_PAY)
										{
											$xml .= '<url method="app" />';
										}
										break;
									case (Constants::iADYEN_PSP):
										$obj_PSP = new Adyen($_OBJ_DB, $_OBJ_TXT, $oTI, $aHTTP_CONN_INFO["adyen"]);
					
										$obj_XML = $obj_PSP->initialize($obj_PSPConfig, $obj_TxnInfo->getAccountID(), General::xml2bool($obj_DOM->pay[$i]->transaction["store-card"]) );
										if (General::xml2bool($obj_DOM->pay[$i]->transaction["store-card"]) === true) { $obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iTICKET_CREATED_STATE, ""); }
										
										foreach ($obj_XML->children() as $obj_XMLElem)
										{
											$xml .= trim($obj_XMLElem->asXML() );
										}
										break;
									case (Constants::iVISA_CHECKOUT_PSP):
										$obj_PSP = new VISACheckout($_OBJ_DB, $_OBJ_TXT, $oTI, $aHTTP_CONN_INFO["visa-checkout"]);
										
										$obj_XML = $obj_PSP->initialize($obj_PSPConfig, $obj_TxnInfo->getAccountID(), false);
										
										foreach ($obj_XML->children() as $obj_XMLElem)
										{
											$xml .= trim($obj_XMLElem->asXML() );
										}
										break;
									case (Constants::iMASTER_PASS_PSP):
										$obj_PSP = new MasterPass($_OBJ_DB, $_OBJ_TXT, $oTI, $aHTTP_CONN_INFO["masterpass"]);
										
										$obj_XML = $obj_PSP->initialize($obj_PSPConfig, $obj_TxnInfo->getAccountID(), false);
										
										foreach ($obj_XML->children() as $obj_Elem)
										{
											$xml .= trim($obj_Elem->asXML() );
										}
										break;
									case (Constants::iAMEX_EXPRESS_CHECKOUT_PSP):
										$xml .= '<url method="overlay" />';
										$obj_PSP = new AMEXExpressCheckout($_OBJ_DB, $_OBJ_TXT, $oTI, $aHTTP_CONN_INFO["amex-express-checkout"]);
										
										$obj_XML = $obj_PSP->initialize($obj_PSPConfig, $obj_TxnInfo->getAccountID(), false);
										
										foreach ($obj_XML->children() as $obj_Elem)
										{
											$xml .= trim($obj_Elem->asXML() );
										}
										break;	
									case (Constants::iAPPLE_PAY_PSP):
										$xml .= '<url method="app" />';
										break;
									case (Constants::iDATA_CASH_PSP):
										$obj_PSP = new DataCash($_OBJ_DB, $_OBJ_TXT, $oTI, $aHTTP_CONN_INFO["data-cash"]);
											
										$obj_XML = $obj_PSP->initialize($obj_PSPConfig, $obj_TxnInfo->getAccountID(), General::xml2bool($obj_DOM->pay[$i]->transaction["store-card"]) );
//										if (General::xml2bool($obj_DOM->pay[$i]->transaction["store-card"]) === true) { $obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iTICKET_CREATED_STATE, ""); }
									
										foreach ($obj_XML->children() as $obj_Elem)
										{
											$xml .= trim($obj_Elem->asXML() );
										}
										break;
									}
									$xml .= '<message language="'. htmlspecialchars($obj_TxnInfo->getLanguage(), ENT_NOQUOTES) .'">'. htmlspecialchars($obj_PSPConfig->getMessage($obj_TxnInfo->getLanguage() ), ENT_NOQUOTES) .'</message>';
									$xml .= '</psp-info>';
								}
								catch (mPointException $e)
								{
									header("HTTP/1.1 502 Bad Gateway");
	
									$xml = '<status code="92">Unable to initialize payment transaction with Payment Service Provider.' ."\n". 'Error: '. htmlspecialchars($e->getMessage(), ENT_NOQUOTES) .'</status>';
								}
								catch (HTTPException $e)
								{
									header("HTTP/1.1 504 Gateway Timeout");
	
									$xml = '<status code="91">'. htmlspecialchars($e->getMessage(), ENT_NOQUOTES) .'</status>';
								}
							}
							// Error: Unable to find Payment Service Provider
							else
							{
								header("HTTP/1.1 500 Internal Server Error");
	
								$xml = '<status code="90">Unable to find configuration for Payment Service Provider using Card: '. $obj_DOM->pay[$i]->transaction->card[$j]["type-id"] .' and Amount: '. $obj_DOM->pay[$i]->transaction->card[$j]->amount .'</status>';
							}
						}
						// Error: Card has been blocked 
						else
						{
							header("HTTP/1.1 403 Forbidden");
						
							$xml = '<status code="'. ($code+85) .'">Card has been blocked</status>';
						}
					}	// Card Loop End
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
	elseif (count($obj_DOM->pay) == 0)
	{
		header("HTTP/1.1 400 Bad Request");

		$xml = '';
		foreach ($obj_DOM->children() as $obj_XMLElem)
		{
			$xml .= '<status code="400">Wrong operation: '. $obj_XMLElem->getName() .'</status>';
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
