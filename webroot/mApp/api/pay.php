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
// Require specific Business logic for the CPM GATEWAY component
require_once(sINTERFACE_PATH ."/cpm_gateway.php");
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
// Require specific Business logic for the Mada Mpgs component
require_once(sCLASS_PATH ."/mada_mpgs.php");
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
require_once(sCLASS_PATH ."/alipay_chinese.php");
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
// Require specific Business logic for the Trustly component
require_once(sCLASS_PATH ."/trustly.php");
// Require specific Business logic for the PayTabs component
require_once(sCLASS_PATH ."/paytabs.php");
// Require specific Business logic for the 2C2P ALC component
require_once(sCLASS_PATH ."/ccpp_alc.php");
// Require specific Business logic for the Citcon component
require_once(sCLASS_PATH ."/citcon.php");
// Require specific Business logic for the PPRO component
require_once(sCLASS_PATH ."/ppro.php");

require_once(sCLASS_PATH ."/bre.php");
// Require specific Business logic for the Amex component
require_once(sCLASS_PATH ."/amex.php");
// Require specific Business logic for the CHUBB component
require_once(sCLASS_PATH ."/chubb.php");
// Require Data Class for Client Information
require_once(sCLASS_PATH ."/clientinfo.php");
// Require specific Business logic for the Google Pay component
require_once(sCLASS_PATH ."/googlepay.php");
// Require specific Business logic for the UATP component
require_once(sCLASS_PATH . "/uatp.php");
// Require specific Business logic for the eGHL FPX component
require_once(sCLASS_PATH . "/eghl.php");

// Require specific Business logic for the Chase component
require_once(sCLASS_PATH ."/chase.php");
// Require specific Business logic for the PayU component
require_once(sCLASS_PATH ."/payu.php");
// Require specific Business logic for the Cielo component
require_once(sCLASS_PATH ."/cielo.php");
// Require specific Business logic for the cellulant component
require_once(sCLASS_PATH ."/cellulant.php");
// Require specific Business logic for the Global Payments component
require_once(sCLASS_PATH ."/global-payments.php");
// Require specific Business logic for the cybs component
require_once(sCLASS_PATH ."/cybersource.php");
// Require specific Business logic for the VeriTrans4G component
require_once(sCLASS_PATH ."/psp/veritrans4g.php");

// Require specific Business logic for the FirstData component
require_once(sCLASS_PATH ."/first-data.php");
// Require specific Business logic for the DragonPay component
require_once(sCLASS_PATH ."/aggregator/dragonpay.php");
// Require specific Business logic for the SWISH component
require_once(sCLASS_PATH ."/apm/swish.php");
require_once(sCLASS_PATH . '/txn_passbook.php');
require_once(sCLASS_PATH . '/passbookentry.php');
require_once(sCLASS_PATH ."/core/card.php");
require_once sCLASS_PATH . '/routing_service.php';
require_once sCLASS_PATH . '/routing_service_response.php';
require_once(sCLASS_PATH . '/payment_processor.php');
require_once(sCLASS_PATH . '/wallet_processor.php');
require_once(sCLASS_PATH . '/payment_route.php');
// Require specific Business logic for the Grab Pay component
require_once(sCLASS_PATH ."/grabpay.php");

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

		for ($i=0, $iMax = count($obj_DOM->pay); $i< $iMax; $i++)
		{
			// Set Global Defaults
			if (empty($obj_DOM->pay[$i]["account"]) === true || (int)$obj_DOM->pay[$i]["account"] < 1) { $obj_DOM->pay[$i]["account"] = -1; }

			// Validate basic information
			$code = Validate::valBasic($_OBJ_DB, (integer) $obj_DOM->pay[$i]["client-id"], (integer) $obj_DOM->pay[$i]["account"]);
			if ($code === 100)
			{
				$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->pay[$i]["client-id"], (integer) $obj_DOM->pay[$i]["account"]);
				// Client successfully authenticated
 				if ($obj_ClientConfig->hasAccess($_SERVER['REMOTE_ADDR']) === true && $obj_ClientConfig->getUsername() === trim($_SERVER['PHP_AUTH_USER']) && $obj_ClientConfig->getPassword() === trim($_SERVER['PHP_AUTH_PW'])
					)
				{

					$obj_Validator = new Validate($obj_ClientConfig->getCountryConfig() );
					$obj_TxnInfo = TxnInfo::produceInfo($obj_DOM->pay[$i]->transaction["id"], $_OBJ_DB);
					$aObj_PSPConfigs = array();
					for ($j=0, $jMax = count($obj_DOM->pay[$i]->transaction->card); $j< $jMax; $j++)
					{
						$obj_mPoint = new CreditCard($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo);
//						$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->pay[$i]->transaction->card[$j]->amount["country-id"]);
//						if ( ($obj_CountryConfig instanceof CountryConfig) === false) { $obj_CountryConfig = $obj_ClientConfig->getCountryConfig(); }

						if (count($obj_DOM->pay[$i]->transaction->card[$j]->{'issuer-identification-number'}) === 1)
						{
							$code = $obj_Validator->valIssuerIdentificationNumber($_OBJ_DB, $obj_ClientConfig->getID(), (integer) $obj_DOM->pay[$i]->transaction->card[$j]->{'issuer-identification-number'});
						}
						else { $code = 10; }

						$obj_TransacionCountryConfig = null;
						if(empty($obj_DOM->{'pay'}[$i]->transaction->card->amount["country-id"]) === false)
						{
							$obj_TransacionCountryConfig = CountryConfig::produceConfig( $_OBJ_DB,$obj_DOM->{'pay'}[$i]->transaction->card->amount["country-id"]);
						}

						// Validate currency if explicitly passed in request, which defer from default currency of the country
						if((int)$obj_DOM->pay[$i]->transaction->card->amount["currency-id"] > 0)
						{
							if($obj_Validator->valCurrency($_OBJ_DB, (int)$obj_DOM->pay[$i]->transaction->card->amount["currency-id"], $obj_TransacionCountryConfig, (int)$obj_DOM->pay[$i]["client-id"]) !== 10 ){
								$aMsgCds[56] = "Invalid Currency:". (int)$obj_DOM->pay[$i]->transaction->card->amount["currency-id"];
							}
						}

                        $obj_ClientInfo = ClientInfo::produceInfo($obj_DOM->pay[$i]->{'client-info'}, CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->pay[$i]->{'client-info'}->mobile["country-id"]), $_SERVER['HTTP_X_FORWARDED_FOR']);

                        $obj_card = new Card($obj_DOM->pay[$i]->transaction->card[$j], $_OBJ_DB);
                        $payment_type = $obj_card->getPaymentType();

						$aRoutes = array();
                        $iPrimaryRoute = 0 ;
						$drService = $obj_TxnInfo->getClientConfig()->getAdditionalProperties (Constants::iInternalProperty, 'DR_SERVICE');

						if ($payment_type == Constants::iPAYMENT_TYPE_CARD && strtolower($drService) == 'true') {
							$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );
                            $obj_RS = new RoutingService($obj_TxnInfo, $obj_ClientInfo, $aHTTP_CONN_INFO['routing-service'], $obj_DOM->pay [$i]["client-id"], $obj_DOM->pay[$i]->transaction->card[$j]->amount["country-id"], $obj_DOM->pay[$i]->transaction->card[$j]->amount["currency-id"], $obj_DOM->pay[$i]->transaction->card[$j]->amount, $obj_DOM->pay[$i]->transaction->card[$j]["type-id"], $obj_DOM->pay[$i]->transaction->card[$j]->{'issuer-identification-number'}, $obj_card->getCardName());
                            if($obj_RS instanceof RoutingService)
							{
                                $objTxnRoute = new PaymentRoute($_OBJ_DB, $obj_TxnInfo->getSessionId());
                                $iPrimaryRoute = $obj_RS->getAndStorePSP($objTxnRoute);
							}
						}

                        $obj_CardResultSet = array();
						if($iPrimaryRoute > 0){
                            $empty = array();
                            $obj_CardResultSet = $obj_mPoint->getCardsObjectForDR( (integer) $obj_DOM->pay [$i]->transaction->card [$j]->amount, $empty, $iPrimaryRoute, (int)$obj_DOM->pay[$i]->transaction->card[$j]['type-id'], -1);
                            $obj_CardResultSet['PSPID'] = (empty($obj_CardResultSet)===FALSE)?$iPrimaryRoute:FALSE;
						}else{
                            $obj_CardResultSet = $obj_mPoint->getCardObject(( integer ) $obj_DOM->pay [$i]->transaction->card [$j]->amount, (int)$obj_DOM->pay[$i]->transaction->card[$j]['type-id'] , 1,-1);
						}

                        if ($obj_CardResultSet === FALSE) { $aMsgCds[24] = "The selected payment card is not available"; } // Card disabled

						$pspId = (int)$obj_CardResultSet['PSPID'];

						$ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                        $ips = array_map('trim', $ips);
                        $ip = $ips[0];
                        $obj_ClientInfo = ClientInfo::produceInfo($obj_DOM->pay[$i]->{'client-info'},
                                CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->pay[$i]->{'client-info'}->mobile["country-id"]),
                                $ip);
                        if (strlen($obj_ClientConfig->getSalt() ) > 0 && $obj_ClientConfig->getAdditionalProperties(Constants::iInternalProperty,"sessiontype") != 2 && empty($obj_DOM->pay[$i]->transaction->{'foreign-exchange-info'}->{'sale-amount'}) === true)
                        {
                            $authToken = trim($obj_DOM->pay[$i]->{'auth-token'});
                            if ($obj_Validator->valHMAC(trim($obj_DOM->{'pay'}[$i]->transaction->hmac), $obj_ClientConfig, $obj_ClientInfo, trim($obj_TxnInfo->getOrderID()), (int)$obj_DOM->{'pay'}[$i]->transaction->card->amount, (int)$obj_DOM->{'pay'}[$i]->transaction->card->amount["country-id"],$obj_TransacionCountryConfig,$authToken) !== 10) { $aMsgCds[210] = "Invalid HMAC:".trim($obj_DOM->{'pay'}[$i]->transaction->hmac); }
                        }  //made hmac mandatory for dcc
                        else if($obj_CardResultSet["DCCENABLED"] === true && empty($obj_DOM->pay[$i]->transaction->{'foreign-exchange-info'}->{'sale-amount'}) === false)
						{
							if ($obj_Validator->valDccHMAC(trim($obj_DOM->{'pay'}[$i]->transaction->hmac), $obj_ClientConfig, $obj_ClientInfo, (int)$obj_DOM->{'pay'}[$i]->transaction->card->amount, (int)$obj_DOM->{'pay'}[$i]->transaction->card->amount["country-id"],$obj_TransacionCountryConfig,$obj_TxnInfo,$obj_DOM->pay[$i]->transaction->{'foreign-exchange-info'}->{'id'}) !== 10) { $aMsgCds[210] = "Invalid HMAC:".trim($obj_DOM->{'pay'}[$i]->transaction->hmac); }
						}

						if($obj_ClientConfig->getAdditionalProperties(Constants::iInternalProperty,"sessiontype") > 1 )
						{
							$pendingAmount = $obj_TxnInfo->getPaymentSession()->getPendingAmount();
							if((integer)$obj_DOM->pay[$i]->transaction->card->amount > $pendingAmount)
							{
								$aMsgCds[53] = "Amount is more than pending amount: ". (integer)$obj_DOM->pay[$i]->transaction->card->amount;
							}
							else{
								$obj_TxnInfo->updateTransactionAmount($_OBJ_DB,(integer)$obj_DOM->pay[$i]->transaction->card->amount);
							}
						}
						else
						{
						    if($obj_CardResultSet["DCCENABLED"] === true  && empty($obj_DOM->pay[$i]->transaction->{'foreign-exchange-info'}->{'sale-amount'}) === false
							   && intval($obj_DOM->pay[$i]->transaction->card->amount["currency-id"]) !== $obj_TxnInfo->getCurrencyConfig()->getID())
                            {
                                if((int)$obj_DOM->pay[$i]->transaction->{'foreign-exchange-info'}->{'sale-amount'} !== (int)$obj_TxnInfo->getAmount())
                                {
                                    $aMsgCds[$iValResult + 50] = 'Invalid Amount ' . (string)$obj_DOM->pay[$i]->transaction->card->amount;
                                }
                            }
						    else
						    {
                                $iValResult = $obj_Validator->valPrice($obj_TxnInfo->getAmount(), (integer)$obj_DOM->pay[$i]->transaction->card->amount);
                                if ($iValResult != 10) {
                                    $aMsgCds[$iValResult + 50] = (string)$obj_DOM->pay[$i]->transaction->card->amount;
                                }
                            }

						}

						// Success: Input Valid
						if (count($aMsgCds) === 0)
						{
							if ($code >= 10)
							{
								if ($obj_TxnInfo->getAccountID() === -1 && General::xml2bool($obj_DOM->pay[$i]->transaction["store-card"]) === true) {
                                    if (count($obj_DOM->{'pay'}[$i]->{'client-info'}->mobile)=== 1)
                                    {
                                        $obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer)$obj_DOM->{'pay'}[$i]->{'client-info'}->mobile["country-id"]);
                                    } else {
                                        $obj_CountryConfig = $obj_ClientConfig->getCountryConfig();
                                    }
									$iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_CountryConfig, trim($obj_DOM->{'pay'}[$i]->{'client-info'}->{'customer-ref'}), (float) $obj_DOM->{'pay'}[$i]->{'client-info'}->mobile, trim($obj_DOM->{'pay'}[$i]->{'client-info'}->email), $obj_DOM->{'pay'}[$i]->{'client-info'}["profileid"]);

									//	Create a new user as some PSP's needs our End-User Account ID for storing cards
									if ($iAccountID < 0)
									{
										$obj_EUA = new EndUserAccount($_OBJ_DB, $_OBJ_TXT, $obj_ClientConfig);
										$iAccountID = $obj_EUA->newAccount($obj_CountryConfig->getID(),
																		   (float) $obj_DOM->{'pay'}[$i]->{'client-info'}->mobile,
																		   "",
																		   trim($obj_DOM->{'pay'}[$i]->{'client-info'}->email),
																		   trim($obj_DOM->{'pay'}[$i]->{'client-info'}->{'customer-ref'}),
																		   $obj_DOM->{'pay'}[$i]->{'client-info'}["pushid"],false, $obj_DOM->{'pay'}[$i]->{'client-info'}["profileid"]);
									}
									$obj_TxnInfo->setAccountID($iAccountID);
									// Update Transaction Log
									try {
										$obj_mPoint->logTransaction($obj_TxnInfo);
									} catch (mPointException $e) {
										trigger_error('Error in updating log.transaction table in pay call. Transaction id : ' . $obj_TxnInfo->getID());
									}
								}
								$obj_paymentProcessor = WalletProcessor::produceConfig($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, (int)$obj_DOM->pay[$i]->transaction->card[$j]["type-id"], $aHTTP_CONN_INFO);

                                // Standard Payment Service Provider
								if((($obj_paymentProcessor instanceof WalletProcessor) === FALSE) || ($obj_paymentProcessor->getPSPConfig() instanceof PSPConfig) === FALSE)
								{
                                    if ((array_key_exists($pspId, $aObj_PSPConfigs) === false) && $pspId > 0 )
									{
										$aObj_PSPConfigs[$pspId] = PaymentProcessor::produceConfig($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $pspId, $aHTTP_CONN_INFO);
									}
									$obj_paymentProcessor = $aObj_PSPConfigs[$pspId];
								}

								// Success: Payment Service Provider Configuration found
								if (($obj_paymentProcessor instanceof WalletProcessor || $obj_paymentProcessor instanceof PaymentProcessor ) && ($obj_paymentProcessor->getPSPConfig() instanceof PSPConfig) === true)
								{
									try
									{
										if(empty($obj_DOM->pay[$i]->transaction->{'foreign-exchange-info'}->{'id'}) === false)
										$obj_TxnInfo->setExternalReference($_OBJ_DB,$obj_paymentProcessor->getPSPConfig()->getID(),Constants::iForeignExchange,$obj_DOM->pay[$i]->transaction->{'foreign-exchange-info'}->{'id'});
										// TO DO: Extend to add support for Split Tender
										$data['amount'] = (integer) $obj_DOM->pay[$i]->transaction->card[$j]->amount;
										$data['client-config'] = ClientConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(),(integer) $obj_DOM->pay[$i]['account']);
										if(($data['client-config'] instanceof ClientConfig) === true )
                                        {
                                            $data['markup'] = $data['client-config']->getAccountConfig()->getMarkupLanguage();
                                        }
                                        $data['producttype'] = $obj_TxnInfo->getProductType();
										$data['installment-value'] = (integer) $obj_DOM->pay[$i]->transaction->installment->value;
										if($obj_paymentProcessor->getPSPConfig()->getProcessorType() === Constants::iPROCESSOR_TYPE_WALLET) {
											$data['wallet-id'] = $obj_paymentProcessor->getPSPConfig()->getID();
										}
										$data['auto-capture'] = (int)$obj_CardResultSet['CAPTURE_TYPE'];
										if(empty($obj_DOM->pay[$i]->transaction->{'foreign-exchange-info'}->{'conversion-rate'}) === false)
										{
											$obj_CurrencyConfig = CurrencyConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->pay[$i]->transaction->card[$j]->amount["currency-id"]);
											$data['externalref'] = array(Constants::iForeignExchange =>array((integer)$obj_TxnInfo->getClientConfig()->getID() => (string)$obj_DOM->pay[$i]->transaction->{'foreign-exchange-info'}->{'id'} ));
											$data['converted-currency-config'] = $obj_CurrencyConfig;
											$data['converted-amount'] = (integer) $obj_DOM->pay[$i]->transaction->card[$j]->amount;
											$data['conversion-rate'] = $obj_DOM->pay[$i]->transaction->{'foreign-exchange-info'}->{'conversion-rate'};
											unset($data['amount']);
										}


										$oTI = TxnInfo::produceInfo($obj_TxnInfo->getID(),$_OBJ_DB, $obj_TxnInfo, $data);
										$obj_mPoint->logTransaction($oTI);
										//getting order config with transaction to pass to particular psp for initialize with psp for AID
										$oTI->produceOrderConfig($_OBJ_DB);

										//For APM and Gateway only we have to trigger authorize requested so that passbook will get updated with authorize requested and performed opt entry
										$processorType = $obj_paymentProcessor->getPSPConfig()->getProcessorType() ;
										if($processorType === Constants::iPROCESSOR_TYPE_APM || $processorType === Constants::iPROCESSOR_TYPE_GATEWAY)
										{
											$txnPassbookObj = TxnPassbook::Get($_OBJ_DB, $obj_TxnInfo->getID(), $obj_TxnInfo->getClientConfig()->getID());
											$passbookEntry = new PassbookEntry
											(
													NULL,
													$obj_TxnInfo->getAmount(),
													$obj_TxnInfo->getCurrencyConfig()->getID(),
													Constants::iAuthorizeRequested
											);
											if ($txnPassbookObj instanceof TxnPassbook)
											{
												$txnPassbookObj->addEntry($passbookEntry);
												$txnPassbookObj->performPendingOperations();
											}
										}

										// Initialize payment with Payment Service Provider
										$xml = '<psp-info id="'. $obj_paymentProcessor->getPSPConfig()->getID() .'" merchant-account="'. htmlspecialchars($obj_paymentProcessor->getPSPConfig()->getMerchantAccount(), ENT_NOQUOTES) .'"  type="'.$obj_paymentProcessor->getPSPConfig()->getProcessorType().'">';

										switch ($obj_paymentProcessor->getPSPConfig()->getID()) {
											case (Constants::iDIBS_PSP):
												$obj_PSP = new DIBS($_OBJ_DB, $_OBJ_TXT, $oTI, $aHTTP_CONN_INFO['dibs']);

												$aHTTP_CONN_INFO["dibs"]["path"] = str_replace("{account}", $obj_paymentProcessor->getPSPConfig()->getMerchantAccount(), $aHTTP_CONN_INFO["dibs"]["path"]);
												$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["dibs"]);
												$obj_XML = $obj_PSP->initialize($obj_ConnInfo, $obj_paymentProcessor->getPSPConfig()->getMerchantAccount(), $obj_paymentProcessor->getPSPConfig()->getMerchantSubAccount(), (string)$obj_CardResultSet['CURRENCY'], (integer)$obj_DOM->pay[$i]->transaction->card[$j]["type-id"]);
												foreach ($obj_XML->children() as $obj_XMLElem) {
													// Hidden Fields
													if (count($obj_XMLElem->children()) > 0) {
														$xml .= '<' . $obj_XMLElem->getName() . '>';
														foreach ($obj_XMLElem->children() as $obj_Child) {
															$xml .= $obj_Child->asXML();
														}
														$xml .= '</' . $obj_XMLElem->getName() . '>';
													} else {
														$xml .= $obj_XMLElem->asXML();
													}
												}

												if (General::xml2bool($obj_DOM->pay[$i]->transaction["store-card"]) === TRUE) {
													$obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iTICKET_CREATED_STATE, "");
												}

												break;
											case (Constants::iPAYEX_PSP):
												$obj_PSP = new PayEx($_OBJ_DB, $_OBJ_TXT, $oTI, $aHTTP_CONN_INFO["payex"]);

												if ($obj_TxnInfo->getMode() > 0) {
													$aHTTP_CONN_INFO["payex"]["host"] = str_replace("external.", "test-external.", $aHTTP_CONN_INFO["payex"]["host"]);
												}
												$aHTTP_CONN_INFO["payex"]["username"] = $obj_paymentProcessor->getPSPConfig()->getUsername();
												$aHTTP_CONN_INFO["payex"]["password"] = $obj_paymentProcessor->getPSPConfig()->getPassword();
												$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["payex"]);
												$obj_XML = $obj_PSP->initialize($obj_ConnInfo, $obj_paymentProcessor->getPSPConfig()->getMerchantAccount(), (string)$obj_CardResultSet['CURRENCY']);
												foreach ($obj_XML->children() as $obj_XMLElem) {
													// Hidden Fields
													if (count($obj_XMLElem->children()) > 0) {
														$xml .= '<' . $obj_XMLElem->getName() . '>';
														foreach ($obj_XMLElem->children() as $obj_Child) {
															$xml .= $obj_Child->asXML();
														}
														$xml .= '</' . $obj_XMLElem->getName() . '>';
													} else {
														$xml .= $obj_XMLElem->asXML();
													}
												}
												break;
											case (Constants::iWANNAFIND_PSP):
												break;
											case (Constants::iNETAXEPT_PSP):
												$obj_PSP = new NetAxept($_OBJ_DB, $_OBJ_TXT, $oTI, $aHTTP_CONN_INFO["netaxept"], $obj_paymentProcessor->getPSPConfig());

												if ($obj_TxnInfo->getMode() > 0) {
													$aHTTP_CONN_INFO["netaxept"]["host"] = str_replace("epayment.", "epayment-test.", $aHTTP_CONN_INFO["netaxept"]["host"]);
												}
												$aHTTP_CONN_INFO["netaxept"]["username"] = $obj_paymentProcessor->getPSPConfig()->getUsername();
												$aHTTP_CONN_INFO["netaxept"]["password"] = $obj_paymentProcessor->getPSPConfig()->getPassword();

												$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["netaxept"]);
												// get boolean value of store card.
												$storecard = (strcasecmp($obj_DOM->pay[$i]->transaction["store-card"], "true") === 0);
												$obj_XML = $obj_PSP->initialize($obj_ConnInfo,
																				$obj_paymentProcessor->getPSPConfig()->getMerchantAccount(),
																				$obj_paymentProcessor->getPSPConfig()->getMerchantSubAccount(),
																				(string)$obj_CardResultSet['currency'],
																				(integer)$obj_DOM->pay[$i]->transaction->card[$j]["type-id"],
																				$storecard);

												foreach ($obj_XML->children() as $obj_XMLElem) {
													$xml .= trim($obj_XMLElem->asXML());
												}
												break;
											case (Constants::iSTRIPE_PSP):
												$obj_PSP = new Stripe_PSP($_OBJ_DB, $_OBJ_TXT, $oTI, []);
												$aLogin = $obj_PSP->getMerchantLogin($obj_TxnInfo->getClientConfig()->getID(), Constants::iSTRIPE_PSP, FALSE);
												$storecard = (strcasecmp($obj_DOM->pay[$i]->transaction["store-card"], "true") === 0);
												$code = $obj_PSP->auth($obj_DOM->pay[$i]->transaction->card[$j]->{'apple-pay-token'}, $aLogin["password"], (integer)$obj_DOM->pay[$i]->transaction->card[$j]["type-id"], $storecard);
												if ($code >= 2000) {
													if ($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]["type-id"] === Constants::iAPPLE_PAY) {
														$xml .= '<status code="' . $code . '">Payment Authorized using Apple Pay</status>';
													} else {
														$xml .= '<status code="' . $code . '">Payment Authorized</status>';
													}
												} // Error: Authorization declined
												else {
													$obj_mPoint->delMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);

													header("HTTP/1.1 502 Bad Gateway");
													$xml .= '<status code="92">Authorization failed, Stripe returned error: ' . $code . '</status>';
												}
												break;
											case (Constants::iMOBILEPAY_PSP):

												$obj_PSP = new MobilePay($_OBJ_DB, $_OBJ_TXT, $oTI, $aHTTP_CONN_INFO["mobilepay"]);
												$obj_XML = $obj_PSP->initialize($obj_paymentProcessor->getPSPConfig());
												foreach ($obj_XML->children() as $obj_XMLElem) {
													$xml .= trim($obj_XMLElem->asXML());
												}
												break;
											case (Constants::iCPG_PSP):
												if ((int)$obj_DOM->pay[$i]->transaction->card[$j]["type-id"] === Constants::iAPPLE_PAY) {
													$xml .= '<url method="app" />';
												}
												break;
											case (Constants::iAMEX_EXPRESS_CHECKOUT_PSP):
												$xml .= '<url method="overlay" />';
												$obj_PSP = new AMEXExpressCheckout($_OBJ_DB, $_OBJ_TXT, $oTI, $aHTTP_CONN_INFO["amex-express-checkout"]);

												$obj_XML = $obj_PSP->initialize($obj_paymentProcessor->getPSPConfig(), $obj_TxnInfo->getAccountID(), FALSE);

												foreach ($obj_XML->children() as $obj_Elem) {
													$xml .= trim($obj_Elem->asXML());
												}
												break;
											case (Constants::iANDROID_PAY_PSP):
												$xml .= '<url method="app" />';
												break;

											default:

												if($obj_paymentProcessor instanceof WalletProcessor)
												{
													$obj_paymentProcessor = PaymentProcessor::produceConfig($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $obj_paymentProcessor->getPSPConfig()->getID(), $aHTTP_CONN_INFO);
												}

												$token = '';
												if (count($obj_DOM->pay[$i]->transaction->card->token) === 1) {
													$token = $obj_DOM->pay[$i]->transaction->card->token;
												}

												$billingAddress = NULL;
												if (count($obj_DOM->{'pay'}[$i]->transaction->{'billing-address'}) === 1) {
													$billingAddress = $obj_DOM->{'pay'}[$i]->transaction->{'billing-address'};
												}
												$obj_XML = $obj_paymentProcessor->initialize($obj_DOM->pay[$i]->transaction->card["type-id"], $token, $billingAddress, $obj_ClientInfo, General::xml2bool($obj_DOM->pay[$i]->transaction['store-card']));
												if (General::xml2bool($obj_DOM->pay[$i]->transaction["store-card"]) === TRUE) {
													$obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iTICKET_CREATED_STATE, "");
												}
												foreach ($obj_XML->children() as $obj_Elem) {
													$xml .= trim($obj_Elem->asXML());
												}

										}
										$xml .= '<message language="'. htmlspecialchars($obj_TxnInfo->getLanguage(), ENT_NOQUOTES) .'">'. htmlspecialchars($obj_paymentProcessor->getPSPConfig()->getMessage($obj_TxnInfo->getLanguage() ), ENT_NOQUOTES) .'</message>';
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
		for ($i=0, $iMax = count($aObj_Errs); $i< $iMax; $i++)
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
