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
// Require specific Business logic for the CPM ACQUIRER component
require_once(sINTERFACE_PATH ."/cpm_acquirer.php");
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
// Require specific Business logic for the WireCard component
require_once(sCLASS_PATH ."/wirecard.php");
// Require specific Business logic for the GlobalCollect component
require_once(sCLASS_PATH ."/globalcollect.php");
// Require specific Business logic for the Secure Trading component
require_once(sCLASS_PATH ."/securetrading.php");
// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
// Require specific Business logic for the PayFort component
require_once(sCLASS_PATH ."/payfort.php");
// Require specific Business logic for the PayPal component
require_once(sCLASS_PATH ."/paypal.php");
// Require specific Business logic for the CCAvenue component
require_once(sCLASS_PATH ."/ccavenue.php");
// Require specific Business logic for the 2C2P component
require_once(sCLASS_PATH ."/ccpp.php");
// Require specific Business logic for the MayBank component
require_once(sCLASS_PATH ."/maybank.php");
// Require specific Business logic for the PublicBank component
require_once(sCLASS_PATH ."/publicbank.php");
// Require specific Business logic for the AliPay component
require_once(sCLASS_PATH ."/alipay.php");
// Require specific Business logic for the POLi component
require_once(sCLASS_PATH ."/poli.php");
// Require specific Business logic for the Qiwi component
require_once(sCLASS_PATH ."/qiwi.php");
// Require specific Business logic for the Klarna component
require_once(sCLASS_PATH ."/klarna.php");
// Require specific Business logic for the MobilePay Online component
require_once(sCLASS_PATH ."/mobilepayonline.php");
// Require specific Business logic for the Nets component
require_once(sCLASS_PATH ."/nets.php");
// Require specific Business logic for the mVault component
require_once(sCLASS_PATH ."/mvault.php");
// Require specific Business logic for the PayTabs component
require_once(sCLASS_PATH ."/paytabs.php");
// Require specific Business logic for the 2C2P ALC component
require_once(sCLASS_PATH ."/ccpp_alc.php");

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

						$iValResult = $obj_Validator->valPrice($obj_TxnInfo->getAmount(), (integer)$obj_DOM->pay[$i]->transaction->card->amount);
						if ($iValResult != 10) { $aMsgCds[$iValResult + 50] = (string) $obj_DOM->pay[$i]->transaction->card->amount; }
						
						
						$obj_CardXML = simpledom_load_string($obj_mPoint->getCards( (integer) $obj_DOM->pay[$i]->transaction->card[$j]->amount) );
						
						//Check if card or payment method is enabled or disabled by merchant
						//Same check is  also implemented at app side.
						$obj_Elem = $obj_CardXML->xpath("/cards/item[@type-id = ". intval($obj_DOM->pay[$i]->transaction->card[$j]["type-id"]) ." and @state-id=1]");

						
						if (count($obj_Elem) == 0) { $aMsgCds[24] = "The selected payment card is not available"; } // Card disabled
						// Success: Input Valid
						if (count($aMsgCds) == 0)
						{
							
								
							if ($code >= 10)
							{
								if ($obj_TxnInfo->getAccountID() == -1 && General::xml2bool($obj_DOM->pay[$i]->transaction["store-card"]) === true)
								{
									$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, intval($obj_TxnInfo->getOperator()/100) );
									$iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_CountryConfig, trim($obj_DOM->{'pay'}[$i]->{'client-info'}->{'customer-ref'}), (float) $obj_DOM->{'pay'}[$i]->{'client-info'}->mobile, trim($obj_DOM->{'pay'}[$i]->{'client-info'}->email) );
	
									//	Create a new user as some PSP's needs our End-User Account ID for storing cards
									if ($iAccountID < 0)
									{
										$obj_EUA = new EndUserAccount($_OBJ_DB, $_OBJ_TXT, $obj_ClientConfig);
										$iAccountID = $obj_EUA->newAccount($obj_CountryConfig->getID(),
																		   (float) $obj_DOM->{'pay'}[$i]->{'client-info'}->mobile,
																		   "",
																		   trim($obj_DOM->{'pay'}[$i]->{'client-info'}->email),
																		   trim($obj_DOM->{'pay'}[$i]->{'client-info'}->{'customer-ref'}),
																		   $obj_DOM->{'pay'}[$i]->{'client-info'}["pushid"]);
									}
									$obj_TxnInfo->setAccountID($iAccountID);
									// Update Transaction Log
									$obj_mPoint->logTransaction($obj_TxnInfo);
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
								case (Constants::iANDROID_PAY_WALLET):				// 3rd Party Wallet: Android Pay
									$obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_ClientConfig->getID(), $obj_ClientConfig->getAccountConfig()->getID(), Constants::iANDROID_PAY_PSP);
									break;
								default:	// Standard Payment Service Provider
									if (array_key_exists(intval($obj_Elem["pspid"]), $aObj_PSPConfigs) === false)
									{
										if (intval($obj_Elem["pspid"]) > 0)
										{
											$aObj_PSPConfigs[intval($obj_Elem["pspid"])] = PSPConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->pay[$i]["client-id"],  (integer) $obj_DOM->pay[$i]["account"], (integer) $obj_Elem["pspid"]);
										}
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
										//getting order config with transaction to pass to particular psp for initialize with psp for AID
										$oTI->produceOrderConfig($_OBJ_DB);
										// Initialize payment with Payment Service Provider
										$xml = '<psp-info id="'. $obj_PSPConfig->getID() .'" merchant-account="'. htmlspecialchars($obj_PSPConfig->getMerchantAccount(), ENT_NOQUOTES) .'"  type="'.$obj_PSPConfig->getProcessorType().'">';
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
											
											if (General::xml2bool($obj_DOM->pay[$i]->transaction["store-card"]) === true) { $obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iTICKET_CREATED_STATE, ""); }
											
											break;
										case (Constants::iWORLDPAY_PSP):
											
											//TODO: Need to find some global logic for handling this condition.
											if($obj_TxnInfo->getMarkupLanguage() != "html5")
											{
												// Construct list of cards supported by the Payment Service Provider
												$aCards = array();
												foreach ($obj_CardXML->children() as $obj_XMLElem)
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
		
												$url .= "&preferredPaymentMethod=". $obj_PSP->getCardName( (integer) $obj_DOM->pay[$i]->transaction->card[$j]["type-id"]) ."&language=". $obj_TxnInfo->getLanguage();
												$xml .= '<url method="get" content-type="none">'. htmlspecialchars($url, ENT_NOQUOTES) .'</url>';
											} else { $xml .= '<url method="html5" />'; }
											
											if (General::xml2bool($obj_DOM->pay[$i]->transaction["store-card"]) === true) { $obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iTICKET_CREATED_STATE, ""); }
											
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
	
											
											$obj_XML = $obj_PSP->initialize($obj_PSPConfig, $obj_TxnInfo->getAccountID(), General::xml2bool($obj_DOM->pay[$i]->transaction["store-card"]) , $obj_DOM->pay[$i]->transaction->card["type-id"], '',  $obj_DOM->{'pay'}[$i]->transaction->{'billing-address'}, $obj_DOM->{'pay'}[$i]->{'client-info'});
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
										case (Constants::iAPPLE_PAY_PSP):
											$xml .= '<url method="app" />';
											break;
										case (Constants::iANDROID_PAY_PSP):
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
										case (Constants::iWIRE_CARD_PSP):
											$obj_PSP = new WireCard($_OBJ_DB, $_OBJ_TXT, $oTI, $aHTTP_CONN_INFO["wire-card"]);
											$obj_XML = $obj_PSP->initialize($obj_PSPConfig, $obj_TxnInfo->getAccountID(), General::xml2bool($obj_DOM->pay[$i]->transaction["store-card"]), $obj_DOM->pay[$i]->transaction->card["type-id"] );
	//										if (General::xml2bool($obj_DOM->pay[$i]->transaction["store-card"]) === true) { $obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iTICKET_CREATED_STATE, ""); }
										
											foreach ($obj_XML->children() as $obj_Elem)
											{
												$xml .= trim($obj_Elem->asXML() );
											}
											break;
										case (Constants::iGLOBAL_COLLECT_PSP):
												$obj_PSP = new GlobalCollect($_OBJ_DB, $_OBJ_TXT, $oTI, $aHTTP_CONN_INFO["global-collect"]);
												$obj_XML = $obj_PSP->initialize($obj_PSPConfig, $obj_TxnInfo->getAccountID(), General::xml2bool($obj_DOM->pay[$i]->transaction["store-card"]), $obj_DOM->pay[$i]->transaction->card["type-id"] );
											
												foreach ($obj_XML->children() as $obj_Elem)
												{
													$xml .= trim($obj_Elem->asXML() );
												}
												break;
										case (Constants::iSECURE_TRADING_PSP):
											$obj_PSP = new SecureTrading($_OBJ_DB, $_OBJ_TXT, $oTI, $aHTTP_CONN_INFO["secure-trading"]);
											$obj_XML = $obj_PSP->initialize($obj_PSPConfig, $obj_TxnInfo->getAccountID(), General::xml2bool($obj_DOM->pay[$i]->transaction["store-card"]), $obj_DOM->pay[$i]->transaction->card["type-id"] );
												
											foreach ($obj_XML->children() as $obj_Elem)
											{
												$xml .= trim($obj_Elem->asXML() );
											}
											break;
										case (Constants::iPAYPAL_PSP):
											$obj_PSP = new PayPal($_OBJ_DB, $_OBJ_TXT, $oTI, $aHTTP_CONN_INFO["paypal"]);
	
											
											$obj_XML = $obj_PSP->initialize($obj_PSPConfig, $obj_TxnInfo->getAccountID(), General::xml2bool($obj_DOM->pay[$i]->transaction["store-card"]) , $obj_DOM->pay[$i]->transaction->card["type-id"], '',  $obj_DOM->{'pay'}[$i]->transaction->{'billing-address'}, $obj_DOM->{'pay'}[$i]->{'client-info'} );
											if (General::xml2bool($obj_DOM->pay[$i]->transaction["store-card"]) === true) { $obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iTICKET_CREATED_STATE, ""); }
	
											foreach ($obj_XML->children() as $obj_XMLElem)
											{
												$xml .= trim($obj_XMLElem->asXML() );
											}
											break;
										case (Constants::iCCAVENUE_PSP):
												$obj_PSP = new CCAvenue($_OBJ_DB, $_OBJ_TXT, $oTI, $aHTTP_CONN_INFO["ccavenue"]);
												$obj_XML = $obj_PSP->initialize($obj_PSPConfig, $obj_TxnInfo->getAccountID(), General::xml2bool($obj_DOM->pay[$i]->transaction["store-card"]), $obj_DOM->pay[$i]->transaction->card["type-id"], '',  $obj_DOM->{'pay'}[$i]->transaction->{'billing-address'}, $obj_DOM->{'pay'}[$i]->{'client-info'} );
											
												foreach ($obj_XML->children() as $obj_Elem)
												{
													$xml .= trim($obj_Elem->asXML() );
												}
												break;
										case (Constants::iPAYFORT_PSP):
											$obj_PSP = new PayFort($_OBJ_DB, $_OBJ_TXT, $oTI, $aHTTP_CONN_INFO["payfort"]);
											$obj_XML = $obj_PSP->initialize($obj_PSPConfig, $obj_TxnInfo->getAccountID(), General::xml2bool($obj_DOM->pay[$i]->transaction["store-card"]), $obj_DOM->pay[$i]->transaction->card["type-id"], $obj_DOM->pay[$i]->transaction->card->token, $obj_DOM->{'pay'}[$i]->transaction->{'billing-address'}, $obj_DOM->{'pay'}[$i]->{'client-info'});
												
											foreach ($obj_XML->children() as $obj_Elem)
											{
												$xml .= trim($obj_Elem->asXML() );
											}
											break;
										case (Constants::i2C2P_PSP):
											$obj_PSP = new CCPP($_OBJ_DB, $_OBJ_TXT, $oTI, $aHTTP_CONN_INFO["2c2p"]);
											$obj_XML = $obj_PSP->initialize($obj_PSPConfig, $obj_TxnInfo->getAccountID(), General::xml2bool($obj_DOM->pay[$i]->transaction["store-card"]), $obj_DOM->pay[$i]->transaction->card["type-id"], $obj_DOM->pay[$i]->transaction->card->token, $obj_DOM->{'pay'}[$i]->transaction->{'billing-address'}, $obj_DOM->{'pay'}[$i]->{'client-info'});
											
											foreach ($obj_XML->children() as $obj_Elem)
											{
												$xml .= trim($obj_Elem->asXML() );
											}
											break;
										case (Constants::iMAYBANK_PSP):
											
											$obj_PSP = new MayBank($_OBJ_DB, $_OBJ_TXT, $oTI, $aHTTP_CONN_INFO["maybank"]);
											$obj_XML = $obj_PSP->initialize($obj_PSPConfig, $obj_TxnInfo->getAccountID(), General::xml2bool($obj_DOM->pay[$i]->transaction["store-card"]), $obj_DOM->pay[$i]->transaction->card["type-id"], $obj_DOM->pay[$i]->transaction->card->token, $obj_DOM->{'pay'}[$i]->transaction->{'billing-address'}, $obj_DOM->{'pay'}[$i]->{'client-info'});
												
											foreach ($obj_XML->children() as $obj_Elem)
											{
												$xml .= trim($obj_Elem->asXML() );
											}
											break;
										case (Constants::iPUBLIC_BANK_PSP):

											$obj_PSP = new PublicBank($_OBJ_DB, $_OBJ_TXT, $oTI, $aHTTP_CONN_INFO["public-bank"]);
											$obj_XML = $obj_PSP->initialize($obj_PSPConfig, $obj_TxnInfo->getAccountID(), General::xml2bool($obj_DOM->pay[$i]->transaction["store-card"]), $obj_DOM->pay[$i]->transaction->card["type-id"], $obj_DOM->pay[$i]->transaction->card->token, $obj_DOM->{'pay'}[$i]->transaction->{'billing-address'}, $obj_DOM->{'pay'}[$i]->{'client-info'});
												
											foreach ($obj_XML->children() as $obj_Elem)
											{
												$xml .= trim($obj_Elem->asXML() );
											}
											break;
										case (Constants::iALIPAY_PSP):
											$obj_PSP = new AliPay($_OBJ_DB, $_OBJ_TXT, $oTI, $aHTTP_CONN_INFO["alipay"]);
											$obj_XML = $obj_PSP->initialize($obj_PSPConfig, $obj_TxnInfo->getAccountID(), General::xml2bool($obj_DOM->pay[$i]->transaction["store-card"]), $obj_DOM->pay[$i]->transaction->card["type-id"]);	
												
											foreach ($obj_XML->children() as $obj_Elem)
											{
												$xml .= trim($obj_Elem->asXML() );
											}
											break;
										case (Constants::iPOLI_PSP):
											$obj_PSP = new Poli($_OBJ_DB, $_OBJ_TXT, $oTI, $aHTTP_CONN_INFO["poli"]);
											$obj_XML = $obj_PSP->initialize($obj_PSPConfig, $obj_TxnInfo->getAccountID(), General::xml2bool($obj_DOM->pay[$i]->transaction["store-card"]), $obj_DOM->pay[$i]->transaction->card["type-id"]);
											
											foreach ($obj_XML->children() as $obj_Elem)
											{
												$xml .= trim($obj_Elem->asXML() );
											}
											break;											
										case (Constants::iQIWI_PSP):
											$obj_PSP = new Qiwi($_OBJ_DB, $_OBJ_TXT, $oTI, $aHTTP_CONN_INFO["qiwi"]);
											$obj_XML = $obj_PSP->initialize($obj_PSPConfig, $obj_TxnInfo->getAccountID(), General::xml2bool($obj_DOM->pay[$i]->transaction["store-card"]), $obj_DOM->pay[$i]->transaction->card["type-id"]);
											
											foreach ($obj_XML->children() as $obj_Elem)
											{
												$xml .= trim($obj_Elem->asXML() );
											}
											break;
										case (Constants::iKLARNA_PSP):
											$obj_PSP = new Klarna($_OBJ_DB, $_OBJ_TXT, $oTI, $aHTTP_CONN_INFO["klarna"]);
											$obj_XML = $obj_PSP->initialize($obj_PSPConfig, $obj_TxnInfo->getAccountID(), General::xml2bool($obj_DOM->pay[$i]->transaction["store-card"]), $obj_DOM->pay[$i]->transaction->card["type-id"]);
												
											foreach ($obj_XML->children() as $obj_Elem)
											{
												$xml .= trim($obj_Elem->asXML() );
											}
											break;
										case (Constants::iPAY_TABS_PSP):
												$obj_PSP = new PayTabs($_OBJ_DB, $_OBJ_TXT, $oTI, $aHTTP_CONN_INFO["paytabs"]);
												$obj_XML = $obj_PSP->initialize($obj_PSPConfig, $obj_TxnInfo->getAccountID(), General::xml2bool($obj_DOM->pay[$i]->transaction["store-card"]), $obj_DOM->pay[$i]->transaction->card["type-id"]);
											
												foreach ($obj_XML->children() as $obj_Elem)
												{
													$xml .= trim($obj_Elem->asXML() );
												}
												break;
										case (Constants::i2C2P_ALC_PSP):
													$obj_PSP = new CCPPALC($_OBJ_DB, $_OBJ_TXT, $oTI, $aHTTP_CONN_INFO["2c2p-alc"]);
													$obj_XML = $obj_PSP->initialize($obj_PSPConfig, $obj_TxnInfo->getAccountID(), General::xml2bool($obj_DOM->pay[$i]->transaction["store-card"]), $obj_DOM->pay[$i]->transaction->card["type-id"]);
														
													foreach ($obj_XML->children() as $obj_Elem)
													{
														$xml .= trim($obj_Elem->asXML() );
													}
													break;
										case (Constants::iMOBILEPAY_ONLINE_PSP):
											$obj_PSP = new MobilePayOnline($_OBJ_DB, $_OBJ_TXT, $oTI, $aHTTP_CONN_INFO["mobilepay-online"]);
											$obj_XML = $obj_PSP->initialize($obj_PSPConfig, $obj_TxnInfo->getAccountID(), General::xml2bool($obj_DOM->pay[$i]->transaction["store-card"]), $obj_DOM->pay[$i]->transaction->card["type-id"]);
													
											foreach ($obj_XML->children() as $obj_Elem)
											{
												$xml .= trim($obj_Elem->asXML() );
											}
											break;
                                        case (Constants::iNETS_ACQUIRER):
                                            $obj_PSP = new Nets($_OBJ_DB, $_OBJ_TXT, $oTI, $aHTTP_CONN_INFO["mobilepay-online"]);
                                            $obj_XML = $obj_PSP->initialize($obj_PSPConfig, $obj_TxnInfo->getAccountID(), General::xml2bool($obj_DOM->pay[$i]->transaction["store-card"]), $obj_DOM->pay[$i]->transaction->card["type-id"]);

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
					    }
						// Error: Invalid Input
						else
						{
							header("HTTP/1.1 400 Bad Request");

							foreach ($aMsgCds as $code => $data)
							{
								$xml .= '<status code="'. $code .'">'. htmlspecialchars($data, ENT_NOQUOTES) .'</status>';
							}
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