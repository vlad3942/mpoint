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
// Require model class for Payment Authorization
require_once(sCLASS_PATH ."/authorize.php");

// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for the CPM PSP component
require_once(sINTERFACE_PATH ."/cpm_psp.php");
// Require specific Business logic for the CPM ACQUIRER component
require_once(sINTERFACE_PATH ."/cpm_acquirer.php");
// Require specific Business logic for the CPM GATEWAY component
require_once(sINTERFACE_PATH ."/cpm_gateway.php");
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
// Require specific Business logic for the DSB PSP component
require_once(sCLASS_PATH ."/dsb.php");
// Require specific Business logic for the VISA checkout component
require_once(sCLASS_PATH ."/visacheckout.php");
if (function_exists("json_encode") === true && function_exists("curl_init") === true)
{
	// Require specific Business logic for the Stripe component
	require_once(sCLASS_PATH ."/stripe.php");
}
// Require specific Business logic for the Adyen component
require_once(sCLASS_PATH ."/adyen.php");
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
// Require specific Business logic for the Global Collect component
require_once(sCLASS_PATH ."/globalcollect.php");
// Require specific Business logic for the Android Pay component
require_once(sCLASS_PATH ."/androidpay.php");
// Require specific Business logic for the Secure Trading component
require_once(sCLASS_PATH ."/securetrading.php");
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
// Require specific Business logic for the MobilePay Online component
require_once(sCLASS_PATH ."/mobilepayonline.php");
// Require specific Business logic for the Klarna Online component
require_once(sCLASS_PATH ."/klarna.php");
// Require Data Class for Client Information
require_once(sCLASS_PATH ."/clientinfo.php");
// Require specific Business logic for the Nets component
require_once(sCLASS_PATH ."/nets.php");
// Require specific Business logic for the mVault component
require_once(sCLASS_PATH ."/mvault.php");
// Require specific Business logic for the 2c2p alc component
require_once(sCLASS_PATH ."/ccpp_alc.php");
// Require specific Business logic for the Google Pay component
require_once(sCLASS_PATH ."/googlepay.php");

// Require specific Business logic for the PPro component
require_once(sCLASS_PATH ."/ppro.php");
require_once(sCLASS_PATH ."/bre.php");
// Require specific Business logic for the Amex component
require_once(sCLASS_PATH ."/amex.php");
// Require specific Business logic for the CHUBB component
require_once(sCLASS_PATH ."/chubb.php");
// Require specific Business logic for the CHUBB component
require_once(sCLASS_PATH ."/payment_processor.php");

require_once(sCLASS_PATH ."/wallet_processor.php");

require_once(sCLASS_PATH ."/post_auth_action.php");

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

try
{
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
						try
						{
							$obj_TxnInfo = TxnInfo::produceInfo( (integer) $obj_DOM->{'authorize-payment'}[$i]->transaction["id"], $_OBJ_DB);
						
							$obj_TxnInfo->produceOrderConfig($_OBJ_DB);
							
						}
						catch (TxnInfoException $e) { $obj_TxnInfo = null; } // Transaction not found

						if ( ($obj_TxnInfo instanceof TxnInfo) === true)
						{
							// Re-Intialise Text Translation Object based on transaction
							$_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH . $obj_TxnInfo->getLanguage() ."/global.txt", sLANGUAGE_PATH . $obj_TxnInfo->getLanguage() ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");
							$obj_mPoint = new EndUserAccount($_OBJ_DB, $_OBJ_TXT, $obj_ClientConfig);

							// Payment has not previously been attempted for transaction
							$_OBJ_DB->query("START TRANSACTION");
							if ($obj_TxnInfo->hasEitherState($_OBJ_DB, array(Constants::iPAYMENT_WITH_ACCOUNT_STATE, Constants::iPAYMENT_WITH_VOUCHER_STATE) ) === false)
							{
								if (count($obj_DOM->{'authorize-payment'}[$i]->transaction->card) > 0)
								{
									if (intval($obj_DOM->{'authorize-payment'}[$i]->transaction["type-id"] ) !== Constants::iNEW_CARD_PURCHASE_TYPE)
									{
										// Add control state and immediately commit database transaction
										$obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE, "");
									}
									
									$_OBJ_DB->query("COMMIT");
									
									//TODO: Move most of the logic of this for-loop into model layer, api/classes/authorize.php
									for ($j=0; $j<count($obj_DOM->{'authorize-payment'}[$i]->transaction->card); $j++)
									{
											
										$obj_XML = simpledom_load_string($obj_mPoint->getStoredCards($obj_TxnInfo->getAccountID(), $obj_ClientConfig, true) );
	
										$obj_Validator = new Validate($obj_ClientConfig->getCountryConfig() );
										
										if (count($obj_DOM->{'authorize-payment'}[$i]->{'auth-token'}) == 0 && count($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->token) == 0 && 
										(intval($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]["type-id"]) !== Constants::iINVOICE && intval($obj_DOM->{'authorize-payment'}[$i]->transaction["type-id"]) !== Constants::iNEW_CARD_PURCHASE_TYPE))
										{
											if ($obj_Validator->valPassword( (string) $obj_DOM->{'authorize-payment'}[$i]->password) != 10) { $aMsgCds[] = $obj_Validator->valPassword( (string) $obj_DOM->{'authorize-payment'}[$i]->password) + 25; }
										}
										$iTypeID = intval($obj_DOM->{'authorize-payment'}[$i]->transaction["type-id"]);
										// Authorize Purchase using Stored Value Account
										if ($iTypeID == Constants::iCARD_PURCHASE_TYPE && count($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->token) == 0 &&
											intval($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]["id"]) > 0 && 
											(intval($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]["type-id"]) !== Constants::iINVOICE || intval($obj_DOM->{'authorize-payment'}[$i]->transaction["type-id"]) !== Constants::iNEW_CARD_PURCHASE_TYPE) &&
											$obj_Validator->valStoredCard($_OBJ_DB, $obj_TxnInfo->getAccountID(), $obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]["id"]) < 10)
											{ $aMsgCds[] = $obj_Validator->valStoredCard($_OBJ_DB, $obj_TxnInfo->getAccountID(), $obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]["id"]) + 40; }
										
										
										$obj_mCard = new CreditCard($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo);
										
										$drEnabled = false;
										$aRoutes = array();
										
										foreach ( $obj_TxnInfo->getClientConfig ()->getAdditionalProperties () as $aAdditionalProperty ) {
											if ($aAdditionalProperty ['key'] == 'DR_SERVICE' && $aAdditionalProperty ['value'] == 'true') {
												$drEnabled = true;
												break;
											}
										}
										
										if ($drEnabled) {
											$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );
											$obj_BRE= new Bre($_OBJ_DB, $_OBJ_TXT);
											$obj_XML = $obj_BRE->getroute($obj_TxnInfo,$obj_ConnInfo,$obj_DOM->{'authorize-payment'} [$i] ["client-id"] , $obj_DOM->{'authorize-payment'}[$i]) ;
											$aRoutes = $obj_XML->{'get-routes-response'}->{'transaction'}->routes->route ;
										}
										
										$obj_CardXML = '';
										$iSecondaryRoute = 0 ;
$iPrimaryRoute = 0 ;
										
										if (count ( $aRoutes ) == 0) {
											$obj_CardXML = simpledom_load_string($obj_mCard->getCards( (integer) $obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->amount) );
										} else {
											foreach ( $aRoutes as $oRoute ) {
												if ($oRoute {'type-id'} == 1) {
													$empty = array();
													$obj_CardXML = simpledom_load_string($obj_mCard->getCards( (integer) $obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->amount,$empty,$oRoute) );
$iPrimaryRoute = $oRoute ;
												}
												else{
													$iSecondaryRoute = $oRoute ;
												}
											}
										}
										
										
										//Check if card or payment method is enabled or disabled by merchant
										//Same check is  also implemented at app side.
										$obj_Elem = $obj_CardXML->xpath("/cards/item[@type-id = ". intval($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]["type-id"]) ." and @state-id=1]");
										if (count($obj_Elem) == 0) { $aMsgCds[24] = "The selected payment card is not available"; } // Card disabled									 
										
										if(count($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->{'card-number'}) > 0 && 
											intval($obj_DOM->{'authorize-payment'}[$i]->transaction["type-id"]) === Constants::iNEW_CARD_PURCHASE_TYPE &&
											$obj_Validator->valCardNumber($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->{'card-number'}) != 10										
										) {$aMsgCds[21] = "Invalid Card Number: ".$obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->{'card-number'}; }

                                        if($obj_ClientConfig->getAdditionalProperties("sessiontype") > 1 ){
                                            $pendingAmount = $obj_TxnInfo->getPaymentSession()->getPendingAmount();
                                            if((integer)$obj_DOM->{'authorize-payment'}[$i]->transaction->card->amount > $pendingAmount)
                                            {
                                                $aMsgCds[53] = "Amount is more than pending amount: ". (integer)$obj_DOM->{'authorize-payment'}[$i]->transaction->card->amount;
                                            }
                                            else{
                                                $obj_TxnInfo->updateTransactionAmount($_OBJ_DB,(integer)$obj_DOM->{'authorize-payment'}[$i]->transaction->card->amount);
                                            }
                                        }else {
                                            if ($obj_TxnInfo->getAmount() != intval($obj_DOM->{'authorize-payment'}[$i]->transaction->card->amount)) {
                                                $aMsgCds[52] = "Invalid amount:" . $obj_DOM->{'authorize-payment'}[$i]->transaction->card->amount;
                                            }
                                        }

                                        if(count($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->{"card-holder-name"}) > 0){
                                            $chkName = $obj_Validator->valCardFullname((string)$obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->{"card-holder-name"});
                                            if($chkName != 10){
                                                $aMsgCds[62] = "Please Enter valid name";
                                            }
                                        }

                                        // Validate currency if explicitly passed in request, which defer from default currency of the country
                                        if(intval($obj_DOM->{'authorize-payment'}[$i]->transaction->card->amount["currency-id"]) > 0){
                                        	$obj_TransacionCountryConfig = CountryConfig::produceConfig($_OBJ_DB, intval($obj_DOM->{'authorize-payment'}[$i]->transaction->card->amount["country-id"])) ;
                                        	if($obj_Validator->valCurrency($_OBJ_DB, intval($obj_DOM->{'authorize-payment'}[$i]->transaction->card->amount["currency-id"]) ,$obj_TransacionCountryConfig, intval( $obj_DOM->{'authorize-payment'}[$i]["client-id"])) != 10 ){
                                        		$aMsgCds[56] = "Invalid Currency:".intval($obj_DOM->{'authorize-payment'}[$i]->transaction->card->amount["currency-id"]) ;
                                        	}
                                        }
                                        
										$obj_ClientInfo = ClientInfo::produceInfo($obj_DOM->{'authorize-payment'}[$i]->{'client-info'},
                                        CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'authorize-payment'}[$i]->{'client-info'}->mobile["country-id"]),
                                        $_SERVER['HTTP_X_FORWARDED_FOR']);

										// Hash based Message Authentication Code (HMAC) enabled for client and payment transaction is not an attempt to simply save a card
										if (strlen($obj_ClientConfig->getSalt() ) > 0 && count($obj_DOM->{'authorize-payment'}[$i]->transaction->hmac) == 1)
										{

											if ($obj_Validator->valHMAC(trim($obj_DOM->{'authorize-payment'}[$i]->transaction->hmac), $obj_ClientConfig, $obj_ClientInfo, trim($obj_TxnInfo->getOrderID()), intval($obj_DOM->{'authorize-payment'}[$i]->transaction->card->amount), intval($obj_DOM->{'authorize-payment'}[$i]->transaction->card->amount["country-id"]) ) != 10) { $aMsgCds[210] = trim($obj_DOM->{'authorize-payment'}[$i]->transaction->hmac); }
										} 
										// Success: Input Valid
										if (count($aMsgCds) == 0)
										{
											// Single Sign-On
											if (count($obj_DOM->{'authorize-payment'}[$i]->{'auth-token'}) == 1 && strlen($obj_TxnInfo->getAuthenticationURL() ) > 0)
											{
												$obj_CustomerInfo = CustomerInfo::produceInfo($_OBJ_DB, $obj_TxnInfo->getAccountID() );
												$obj_Customer = simplexml_load_string($obj_CustomerInfo->toXML() );
												if (strlen($obj_TxnInfo->getCustomerRef() ) > 0) { $obj_Customer["customer-ref"] = $obj_TxnInfo->getCustomerRef(); }
												if (floatval($obj_TxnInfo->getMobile() ) > 0)
												{
													$obj_Customer->mobile = $obj_TxnInfo->getMobile();
													$obj_Customer->mobile["country-id"] = intval($obj_TxnInfo->getCountryConfig ()->getID ());
													$obj_Customer->mobile["operator-id"] = $obj_TxnInfo->getOperator();
												}
												if (strlen($obj_TxnInfo->getEMail() ) > 0) { $obj_Customer->email = $obj_TxnInfo->getEMail(); }
												$obj_CustomerInfo = CustomerInfo::produceInfo($obj_Customer);
												$code = $obj_mPoint->auth(HTTPConnInfo::produceConnInfo($obj_TxnInfo->getAuthenticationURL() ), $obj_CustomerInfo, trim($obj_DOM->{'authorize-payment'}[$i]->{'auth-token'}),(integer) $obj_DOM->{'authorize-payment'}[$i]["client-id"] );
											}
											// Authentication is not required for payment methods that are sending a token or Invoice
											elseif ( (count($obj_DOM->{'authorize-payment'}[$i]->password) == 0 && count($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->token) == 1) || 
											(intval($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]["type-id"]) === Constants::iINVOICE || intval($obj_DOM->{'authorize-payment'}[$i]->transaction["type-id"]) === Constants::iNEW_CARD_PURCHASE_TYPE) )
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
																//Initialise Callback to Client
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

                                                    $card_psp_id = $obj_mPoint->getCardPSPId($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]["id"]);
													// 3rd Party Wallet
													if(count($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->token) == 1 || intval($card_psp_id)== Constants::iMVAULT_PSP)
													{
                                                        if (intval($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]["type-id"]) > 0) {
                                                            $wallet_Processor = WalletProcessor::produceConfig($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, intval($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]["type-id"]), $aHTTP_CONN_INFO, $card_psp_id);
                                                            if (empty($wallet_Processor) === true) {
                                                                $obj_XML = simpledom_load_string($obj_mCard->getCards((integer)$obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->amount));

                                                                if (count($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->cvc) == 1) {
                                                                    $obj_Elem->cvc = (integer)$obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->cvc;
                                                                }

                                                                if (count($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->token) == 1) {
                                                                    $obj_Elem->ticket = (string)$obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->token;
                                                                }
                                                            }
                                                        }
														if(isset($wallet_Processor) == true && is_object($wallet_Processor) == true)
														{
															$obj_XML = simpledom_load_string($wallet_Processor->getPaymentData($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]) );
															if (count($obj_XML->{'payment-data'}) == 1)
															{
																$obj_Elem = $obj_XML->{'payment-data'}->card;
																// Add billing address from request as no billing address was returned by the 3rd party wallet
																if (count($obj_Elem->address) == 0 && count($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->address) == 1)
																{
																	/*
																	 * Some versions of LibXML will report a wrong element name for "address" unless the XML element is marshalled into a string first
																	 */
																	$obj_Elem->addChild(simplexml_load_string($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->address->asXML() ) );
																	// Normalize full name into first name / last name
																	if (count($obj_Elem->address->{'full-name'}) == 1)
																	{
																		$pos = strrpos($obj_Elem->address->{'full-name'}, " ");
																		if ($pos > 0)
																		{
																			$obj_Elem->address->{'first-name'} = trim(substr($obj_Elem->address->{'full-name'}, 0, $pos) );
																			$obj_Elem->address->{'last-name'} = trim(substr($obj_Elem->address->{'full-name'}, $pos) );
																		}
																		else { $obj_Elem->address->{'first-name'} = trim($obj_Elem->address->{'full-name'}); }
																	}
																}
																// Merge billing address from request with the billing address returned by the 3rd party wallet
																elseif (count($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->address) == 1)
																{
																	$obj_Elem->address["country-id"] = (integer) $obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->address["country-id"];
																	// Normalize full name into first name / last name
																	if (count($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->address->{'full-name'}) == 1)
																	{
																		$pos = strrpos($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->address->{'full-name'}, " ");
																		if ($pos > 0)
																		{
																			$obj_Elem->address->{'first-name'} = trim(substr($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->address->{'full-name'}, 0, $pos) );
																			$obj_Elem->address->{'last-name'} = trim(substr($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->address->{'full-name'}, $pos) );
																		}
																		else { $obj_Elem->address->{'first-name'} = trim($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->address->{'full-name'}); }
																	}
																	else
																	{
																		if (count($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->address->{'first-name'}) == 1) { $obj_Elem->address->{'first-name'} = trim($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->address->{'first-name'}); }
																		if (count($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->address->{'last-name'}) == 1) { $obj_Elem->address->{'last-name'} = trim($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->address->{'last-name'}); }
																	}
																	$obj_Elem->address->street = trim($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->address->street);
																	if (count($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->address->{'postal-code'}) == 1) { $obj_Elem->address->{'postal-code'} = trim($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->address->{'postal-code'}); }
																	$obj_Elem->address->city = trim($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->address->city);
																	if (count($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->address->state) == 1) { $obj_Elem->address->state = trim($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->address->state); }
																}
																//For stored card if we do not have address element, fetch billing address from mpoint enduser.address_tbl
                                                                else {

                                                                    //Fetch address from db
                                                                    $RS = $obj_mPoint->getAddressFromCardId($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]["id"]);
                                                                    if (is_array ( $RS ) === true && count ( $RS ) > 0)
                                                                    {
                                                                        $obj_Elem->addChild('address','');
                                                                        $obj_Elem->address["country-id"] =$RS ["COUNTRYID"];
                                                                        $obj_Elem->address->{'first-name'} = $RS ["FIRSTNAME"];
                                                                        $obj_Elem->address->{'last-name'} =$RS ["LASTNAME"];
                                                                        $obj_Elem->address->{'full-name'} =$RS ["FIRSTNAME"]." ".$RS ["LASTNAME"];
                                                                        $obj_Elem->address->street =$RS ["STREET"];
                                                                        $obj_Elem->address->city =$RS ["CITY"];
                                                                        $obj_Elem->address->state=$RS ["STATE"];
                                                                        $obj_Elem->address->{'postal-code'}=$RS ["POSTALCODE"];
                                                                    }
                                                                }
																// Merge CVC / CVV code from request
																if (count($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->cvc) == 1)
																{
																	$obj_Elem->cvc = (string) $obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->cvc;
																}
																															
																$obj_PSPConfig = $wallet_Processor->getPSPConfigForRoute ( intval ( $obj_DOM->{'authorize-payment'} [$i]->transaction->card [$j] ["type-id"] ), intval ( $obj_DOM->{'authorize-payment'} [$i]->transaction->card [$j]->amount ["country-id"] ) );
																	
																	if ($iPrimaryRoute > 0) {
																		$obj_Elem ["pspid"] = $iPrimaryRoute;
																	} else {
																		$obj_Elem ["pspid"] = $obj_PSPConfig->getID ();
																	}
																	$obj_Elem ["wallet-type-id"] = intval ( $obj_DOM->{'authorize-payment'} [$i]->transaction->card [$j] ["type-id"] );
																}
															// 3rd Party Wallet returned error	
															elseif (count($obj_XML->status) == 1)
															{
																$obj_XML->status["code"] = intval($obj_XML->status["code"]) - 20;
																$xml = str_replace('<?xml version="1.0"?>', '', $obj_XML->status->asXML() );
																$code = 5;
															}
	
															// 3rd Party Wallet returned unknown error
															else { $code = 6; }
	
														}
													}
													else if (intval($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]["type-id"] ) == Constants::iINVOICE)
													{
														$iPSPID = -1;
														$aPaymentMethods = $obj_mPoint->getClientConfig()->getPaymentMethods();
														foreach ($aPaymentMethods as $m)
														{
															if ($m->getPaymentMethodID() == Constants::iINVOICE) { $iPSPID = $m->getPSPID(); }
														}
	
														if ($iPSPID > 0)
														{
															$obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), $iPSPID);
															$obj_PSP = Callback::producePSP($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO, $obj_PSPConfig);
															$obj_Authorize = new Authorize($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $obj_PSP);
															$iInvoiceStatus = $obj_Authorize->invoice($obj_DOM->{'authorize-payment'}[$i]->transaction->description);
																// Set the code to 5 so stored card payemnt if statment is skipped.
																$code = 5;
															if ($iInvoiceStatus == 100) { $xml .= '<status code="100">Payment authorized using Invoice</status>'; }
															else
															{
																header("HTTP/1.1 502 Bad Gateway");
																$xml .= '<status code="92">Payment rejected by invoice issuer</status>';
															}
														}
														else
														{
															header("HTTP/1.1 412 Precondition Failed");
															$xml .= '<status code="99">Invoice payment not configured for client</status>';
														}
													} 
													else if (intval($obj_DOM->{'authorize-payment'}[$i]->transaction["type-id"] ) == Constants::iNEW_CARD_PURCHASE_TYPE)
													{
																											
														$obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->addAttribute("pspid", $obj_Elem["pspid"]);
														
														$obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->addChild("name", $obj_Elem->name);
														
														$obj_Elem = $obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j];
																												
														if (count($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->cvc) == 1) 
														{ 
															$obj_Elem->cvc = (string) $obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->cvc; 
														}
													}
													else
													{
														$obj_Elem = $obj_XML->xpath("/stored-cards/card[@id = ". $obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]["id"] ."]");
														if (count($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->cvc) == 1) { $obj_Elem->cvc = (string) $obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->cvc; }
														if (count($obj_Elem->mask) == 1 && intval($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]["type-id"]) != 28 )  { $code = $obj_Validator->valIssuerIdentificationNumber($_OBJ_DB, $obj_ClientConfig->getID(), substr(str_replace(" ", "", $obj_Elem->mask), 0, 6) ); }
														else { $code = 10; }
													}

													if ($code >= 10)
													{
														try
														{
														    if($obj_Elem["pspid"] > 0) {
                                                                $obj_Processor = PaymentProcessor::produceConfig($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, intval($obj_Elem["pspid"]), $aHTTP_CONN_INFO);
                                                                $code = $obj_Processor->authorize($obj_Elem);

                                                                // Authorization succeeded
                                                                if ($code == "100") {
                                                                    $xml .= '<status code="100">Payment Authorized Using Stored Card</status>';
                                                                } else if ($code == "2000") {
                                                                    $xml .= '<status code="2000">Payment authorized</status>';
                                                                } else if ($code == "2009") {
                                                                    $xml .= '<status code="2009">Payment authorized and Card Details Stored.</status>';
                                                                } else if (strpos($code, '2005') !== false) {
                                                                    header("HTTP/1.1 303");
                                                                    $xml .= $code;
                                                                } else if ($code == "20102" && $iSecondaryRoute > 0) {
                                                                    // In case of the primary PSP is down, and secondary route is configured for this client, authorize via secondary route
                                                                    $xml .= $obj_mPoint->authWithSecondaryPSP($obj_TxnInfo, $iSecondaryRoute, $aHTTP_CONN_INFO, $obj_Elem);
                                                                } // Error: Authorization declined

                                                                else {
                                                                    $obj_mPoint->delMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);

                                                                    header("HTTP/1.1 502 Bad Gateway");

                                                                    $xml .= '<status code="92">Authorization failed, PSP returned error: ' . $code . '</status>';
                                                                }
                                                            }

														}
														catch (HTTPException $e)
														{
															$obj_mPoint->delMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);
	
															header("HTTP/1.1 504 Gateway Timeout");
	
															$xml = '<status code="90">'. htmlspecialchars($e->getTraceAsString(), ENT_NOQUOTES) .'</status>';
														}
													}
													// 3rd Party Wallet returned error
													elseif ($code > 4)
													{
														//The node <status> is returned along with the status code
														$xml =  str_replace('<?xml version="1.0"?>', '', $obj_XML->status->asXML() );
														if (empty($xml) === true) { $xml = '<status code="79">An unknown error occurred while retrieving payment data from 3rd party wallet</status>'; }
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
	
											foreach ($aMsgCds as $key => $value)
											{
												$xml .= '<status code="'. $key .'">'.$value.'</status>';
											}
										}
									}	// End card loop
								}
								else if (count($obj_DOM->{'authorize-payment'}[$i]->transaction->voucher) > 0) // Authorize voucher payment
								{
									foreach ($obj_DOM->{'authorize-payment'}[$i]->transaction->voucher as $voucher)
									{
										$iPSPID = -1;
										$aPaymentMethods = $obj_mPoint->getClientConfig()->getPaymentMethods();
										foreach ($aPaymentMethods as $m)
										{
											if ($m->getPaymentMethodID() == Constants::iVOUCHER_CARD) { $iPSPID = $m->getPSPID(); }
										}

										if ($iPSPID > 0)
										{
											$obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), $iPSPID);
											$obj_PSP = Callback::producePSP($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO, $obj_PSPConfig);
											$obj_Authorize = new Authorize($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $obj_PSP);
											$code = $obj_Authorize->redeemVoucher(intval($voucher["id"]) );
											if ($code == 100) { $xml .= '<status code="100">Payment authorized using Voucher</status>'; }
											else if ($code == 43)
											{
												header("HTTP/1.1 402 Payment Required");
												$xml .= '<status code="43">Insufficient balance on voucher</status>';
											}
											else if ($code == 45)
											{
												header("HTTP/1.1 401 Unauthorized");
												$xml .= '<status code="45">Voucher and Redeem device-ids not equal</status>';
											}
											else if ($code == 48)
											{
												header("HTTP/1.1 423 Locked");
												$xml .= '<status code="48">Voucher payment temporarily locked</status>';
											}
											else
											{
												header("HTTP/1.1 502 Bad Gateway");
												$xml .= '<status code="92">Payment rejected by voucher issuer</status>';
											}
										}
										else
										{
											header("HTTP/1.1 412 Precondition Failed");
											$xml .= '<status code="99">Voucher payment not configured for client</status>';
										}
									}
								}
								else
								{
									$_OBJ_DB->query("ROLLBACK");

									header("HTTP/1.1 400 Bad Request");
									$xml .= '<status code="400">Invalid Tender</status>';
								}
							}
							else
							{
								$_OBJ_DB->query("COMMIT");

								$xml .= '<status code="103">Authorization already in progress</status>';
							}
						}
						else
						{
							header("HTTP/1.1 404 File Not Found");
							$xml = '<status code="404">Transaction with ID: '. $obj_DOM->{'authorize-payment'}[$i]->transaction["id"] .' not found</status>';
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
}
catch (Exception $e)
{
	header("HTTP/1.1 500 Internal Server Error");
	$xml = '<status code="500">'. $e->getMessage() .'</status>';
	trigger_error("Exception thrown in mApp/api/authorize.php: ". $e->getMessage() ."\n". $e->getTraceAsString(), E_USER_ERROR);
}
header("Content-Type: text/xml; charset=\"UTF-8\"");
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<root>';
echo $xml;
echo '</root>';
