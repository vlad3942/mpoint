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
// Require specific Business logic for the CPM FRAUD GATEWAY component
require_once(sINTERFACE_PATH ."/cpm_fraud.php");
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
// Require specific Business logic for the Mada Mpgs component
require_once(sCLASS_PATH ."/mada_mpgs.php");
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
// Require specific Business logic for the UATP component
require_once(sCLASS_PATH . "/uatp.php");
// Require specific Business logic for the UATP Card Account services
require_once(sCLASS_PATH . "/uatp_card_account.php");
// Require specific Business logic for the chase component
require_once(sCLASS_PATH ."/chase.php");
// Require specific Business logic for the PayU component
require_once(sCLASS_PATH ."/payu.php");
// Require specific Business logic for the Cielo component
require_once(sCLASS_PATH ."/cielo.php");
// Require specific Business logic for the cellulant component
require_once(sCLASS_PATH ."/cellulant.php");

require_once(sCLASS_PATH ."/wallet_processor.php");

require_once(sCLASS_PATH ."/post_auth_action.php");
// Require specific Business logic for the Global payments component
require_once(sCLASS_PATH ."/global-payments.php");
// Require specific Business logic for the cybs component
require_once(sCLASS_PATH ."/cybersource.php");
// Require specific Business logic for the VeriTrans4G component
require_once(sCLASS_PATH ."/psp/veritrans4g.php");
// Require specific Business logic for the DragonPay component
require_once(sCLASS_PATH ."/aggregator/dragonpay.php");
// Require specific Business logic for the SWISH component
require_once(sCLASS_PATH ."/apm/swish.php");

// Require specific Business logic for the FirstData component
require_once(sCLASS_PATH ."/first-data.php");

require_once sCLASS_PATH . '/txn_passbook.php';
require_once sCLASS_PATH . '/passbookentry.php';

require_once(sCLASS_PATH ."/fraud/provider/ezy.php");
require_once(sCLASS_PATH ."/fraud/provider/cyberSourceFsp.php");
require_once(sCLASS_PATH ."/fraud/provider/cebuRmfss.php");
require_once(sCLASS_PATH ."/core/card.php");
require_once(sCLASS_PATH ."/validation/cardvalidator.php");
require_once sCLASS_PATH . '/routing_service.php';
require_once sCLASS_PATH . '/routing_service_response.php';
require_once sCLASS_PATH . '/fraud/fraud_response.php';
require_once sCLASS_PATH . '/fraud/fraudResult.php';
require_once(sCLASS_PATH . '/payment_route.php');
require_once(sCLASS_PATH .'/apm/paymaya.php');
require_once(sCLASS_PATH . '/paymentSecureInfo.php');
require_once(sCLASS_PATH . '/Route.php');

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

$obj_DOM = simpledom_load_string(file_get_contents("php://input") );

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
							if ($obj_TxnInfo->hasEitherState($_OBJ_DB, array(Constants::iPAYMENT_WITH_ACCOUNT_STATE, Constants::iPAYMENT_WITH_VOUCHER_STATE, Constants::iPAYMENT_ACCEPTED_STATE, Constants::iPAYMENT_3DS_VERIFICATION_STATE) ) === false)
							{

							    $isVoucherRedeem = FALSE;
							    $isVoucherRedeemStatus = -1;
                                $sessiontype = (int)$obj_ClientConfig->getAdditionalProperties(0,'sessiontype');
							    if (count($obj_DOM->{'authorize-payment'}[$i]->transaction->voucher) > 0) // Authorize voucher payment
								{
								    $isVoucherPreferred = $obj_ClientConfig->getAdditionalProperties(0,'isVoucherPreferred');

                                    $cardNode = $obj_DOM->{'authorize-payment'}[$i]->transaction->card;

                                    $iAmount = (int)$obj_TxnInfo->getAmount();
                                    if(isset($obj_DOM->{'authorize-payment'}[$i]->transaction->voucher->amount) === TRUE)
                                    {
                                        $iAmount = (int) $obj_DOM->{'authorize-payment'}[$i]->transaction->voucher->amount;
                                    }

                                    $iPSPID = -1;
                                    $aPaymentMethods = $obj_mPoint->getClientConfig()->getPaymentMethods($_OBJ_DB);
                                    foreach ($aPaymentMethods as $m) {
                                        if ($m->getPaymentMethodID() === Constants::iVOUCHER_CARD) {
                                            $iPSPID = $m->getPSPID();
                                        }
                                    }
                                    $isVoucherErrorFound = FALSE;
								    if($iPSPID > 1 && $sessiontype >= 1 && $isVoucherPreferred === "false" && is_object($cardNode) && count($cardNode) > 0 )
                                    {
                                        foreach ($obj_DOM->{'authorize-payment'}[$i]->transaction->voucher as $voucher)
                                        {
                                            $additionalTxnData = [];
                                            $additionalTxnData[0]['name'] = 'voucherid';
                                            $additionalTxnData[0]['value'] = (string)$voucher['id'];
                                            $additionalTxnData[0]['type'] = 'Transaction';

                                            $txnObj = $obj_mPoint->createTxnFromTxn($obj_TxnInfo, (int)$iAmount, FALSE, (string)$iPSPID, $additionalTxnData);
                                            if ($txnObj !== NULL) {
                                                $_OBJ_DB->query('COMMIT');
                                                $_OBJ_DB->query('START TRANSACTION');
                                            } else {
                                                $_OBJ_DB->query('ROLLBACK');
                                            }
                                             $isVoucherErrorFound = TRUE; //TO Bypass error flow
                                        }
                                    }
                                    elseif ($sessiontype > 1 && $iPSPID > 0)
                                    {
                                        $pendingAmount = $obj_TxnInfo->getPaymentSession()->getPendingAmount();
                                        if ($iAmount > $pendingAmount) {
                                            $aMsgCds[53] = "Amount is more than pending amount: " . $iAmount;
                                            $xml .= '<status code="53">Amount is more than pending amount:  '. $iAmount . '</status>';
                                            $isVoucherErrorFound = TRUE;
                                        } else {
                                            $obj_TxnInfo->updateTransactionAmount($_OBJ_DB, $iAmount);
                                        }
                                        $isVoucherRedeem = TRUE;
                                    }
                                    else if((int)$obj_TxnInfo->getAmount() !== $iAmount)
                                    {
                                        $aMsgCds[52] = "Amount is more than pending amount: " . $iAmount;
                                        $isVoucherErrorFound = TRUE;
                                        $xml .= '<status code="52">Amount is more than pending amount:  '. $iAmount . '</status>';
                                        $isVoucherRedeem = TRUE;
                                    }

                                    if($iPSPID > 0 && $isVoucherErrorFound === FALSE && $isVoucherPreferred !== "false" ) {
                                        $is_legacy = $obj_TxnInfo->getClientConfig()->getAdditionalProperties (Constants::iInternalProperty, 'IS_LEGACY');
                                        foreach ($obj_DOM->{'authorize-payment'}[$i]->transaction->voucher as $voucher) {
                                            $isVoucherRedeem = TRUE;
                                            if (strtolower($is_legacy) == 'false') {
                                                $obj_PSPConfig = PSPConfig::produceConfiguration($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), $iPSPID);
                                            } else {
                                                $obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), $iPSPID);
                                            }
                                            $obj_PSP = Callback::producePSP($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO, $obj_PSPConfig);
                                            $obj_Authorize = new Authorize($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $obj_PSP);

                                            $additionalData = array();
                                            if(isset($obj_DOM->{'authorize-payment'}[$i]->transaction->{'additional-data'}))
									        {
                                                $additionalDataParamsCount = count($obj_DOM->{'authorize-payment'}[$i]->transaction->{'additional-data'}->children());
                                                for ($index = 0; $index < $additionalDataParamsCount; $index++)
                                                {
                                                    $name = (string)$obj_DOM->{'authorize-payment'}[$i]->transaction->{'additional-data'}->param[$index]['name'];
                                                    $value = (string)$obj_DOM->{'authorize-payment'}[$i]->transaction->{'additional-data'}->param[$index];
                                                    $additionalData[$name] = $value;
                                                }
                                            }

                                            $txnPassbookObj = TxnPassbook::Get($_OBJ_DB, $obj_TxnInfo->getID(), $obj_TxnInfo->getClientConfig()->getID());

                                            $passbookEntry = new PassbookEntry
                                            (
                                                NULL,
                                                $iAmount,
                                                $obj_TxnInfo->getCurrencyConfig()->getID(),
                                                Constants::iAuthorizeRequested
                                            );
                                            if ($txnPassbookObj instanceof TxnPassbook) {
                                                $txnPassbookObj->addEntry($passbookEntry);
                                                $txnPassbookObj->performPendingOperations();
                                            }
                                            $isVoucherRedeemStatus = $obj_Authorize->redeemVoucher((string)$voucher["id"], $iAmount, $additionalData);
                                            if ($isVoucherRedeemStatus === 100) {
                                                $xml .= '<status code="100">Payment authorized using Voucher</status>';
                                            } elseif ($isVoucherRedeemStatus === 43) {
                                                header("HTTP/1.1 402 Payment Required");
                                                $xml .= '<status code="43">Insufficient balance on voucher</status>';
                                            } elseif ($isVoucherRedeemStatus === 45) {
                                                header("HTTP/1.1 401 Unauthorized");
                                                $xml .= '<status code="45">Voucher and Redeem device-ids not equal</status>';
                                            } elseif ($isVoucherRedeemStatus ===48) {
                                                header("HTTP/1.1 423 Locked");
                                                $xml .= '<status code="48">Voucher payment temporarily locked</status>';
                                            } else {
                                                header("HTTP/1.1 502 Bad Gateway");
                                                $xml .= '<status code="92">Payment rejected by voucher issuer</status>';
                                            }
                                        }
                                    }
								    else if( $isVoucherErrorFound === FALSE){
                                        header("HTTP/1.1 412 Precondition Failed");

                                        $isVoucherRedeemStatus = 99;
                                        $xml .= '<status code="99">Voucher payment not configured for client</status>';

                                    }
								}


								if ((($sessiontype > 1 && $isVoucherRedeem === TRUE && $isVoucherRedeemStatus === 100) || ($isVoucherRedeem === FALSE && $isVoucherRedeemStatus === -1)) && is_object($obj_DOM->{'authorize-payment'}[$i]->transaction->card) && count($obj_DOM->{'authorize-payment'}[$i]->transaction->card) > 0)
								{

                                    if ($sessiontype > 1 && $isVoucherRedeem === TRUE && $isVoucherRedeemStatus === 100) {

                                        $txnObj = $obj_mPoint->createTxnFromTxn($obj_TxnInfo, $obj_TxnInfo->getPaymentSession()->getPendingAmount());
                                        if ($txnObj !== NULL) {

                                            $obj_TxnInfo = $txnObj;
                                            $_OBJ_DB->query('COMMIT');
                                            $_OBJ_DB->query('START TRANSACTION');
                                        } else {
                                            $_OBJ_DB->query('ROLLBACK');
                                        }
                                    }

                                    $isStoredCardPayment = ((int)$obj_DOM->{'authorize-payment'}[$i]->transaction->card["id"] > 0)?true:false;
                                    $isCardTokenExist = (empty($obj_DOM->{'authorize-payment'}[$i]->transaction->card->token) === false)?true:false;
                                    $isCardNetworkExist = (empty($obj_DOM->{'authorize-payment'}[$i]->transaction->card["network"]) === false)?true:false;
                                    $additionalTxnData = [];
                                    $is_legacy = $obj_TxnInfo->getClientConfig()->getAdditionalProperties (Constants::iInternalProperty, 'IS_LEGACY');

									if ($isStoredCardPayment === true)
									{
										// Add control state and immediately commit database transaction
										$obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE, "");
									}

									if(isset($obj_DOM->{'authorize-payment'}[$i]->transaction->{'additional-data'}))
									{
										$additionalDataParamsCount = count($obj_DOM->{'authorize-payment'}[$i]->transaction->{'additional-data'}->children());
										for ($index = 0; $index < $additionalDataParamsCount; $index++)
										{
											$additionalTxnData[$index]['name'] = (string)$obj_DOM->{'authorize-payment'}[$i]->transaction->{'additional-data'}->param[$index]['name'];
											$additionalTxnData[$index]['value'] = (string)$obj_DOM->{'authorize-payment'}[$i]->transaction->{'additional-data'}->param[$index];
											$additionalTxnData[$index]['type'] = (string)'Transaction';
										}
									}
									if(count($additionalTxnData) > 0)
									{
										$obj_TxnInfo->setAdditionalDetails($_OBJ_DB,$additionalTxnData,$obj_TxnInfo->getID());
									}
									
									$_OBJ_DB->query("COMMIT");

									//TODO: Move most of the logic of this for-loop into model layer, api/classes/authorize.php
									for ($j=0; $j<count($obj_DOM->{'authorize-payment'}[$i]->transaction->card); $j++)
									{

                                        $oUA = null;
										$obj_XML = simpledom_load_string($obj_mPoint->getStoredCards($obj_TxnInfo->getAccountID(), $obj_ClientConfig, true, $oUA, array(), $obj_TxnInfo->getCountryConfig()->getID(), $is_legacy) );

										$obj_Validator = new Validate($obj_ClientConfig->getCountryConfig() );
										$obj_card = new Card($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j], $_OBJ_DB);
										$obj_CardValidator = new CardValidator($obj_card);
										if (count($obj_DOM->{'authorize-payment'}[$i]->{'auth-token'}) == 0 && count($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->token) == 0 && 
										(intval($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]["type-id"]) !== Constants::iINVOICE && $isStoredCardPayment === true))
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

                                        $obj_ClientInfo = ClientInfo::produceInfo($obj_DOM->{'authorize-payment'}[$i]->{'client-info'}, CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'authorize-payment'}[$i]->{'client-info'}->mobile["country-id"]), $_SERVER['HTTP_X_FORWARDED_FOR']);

                                        // Call get payment data API for wallet and stored card payment
                                        $card_psp_id = -1;
                                        if ($isStoredCardPayment === true)
                                        {
                                            $card_psp_id = (int)$obj_mPoint->getCardPSPId($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]["id"]);
                                        }
                                        $walletId = NULL;
                                        $wallet_Processor = NULL;
                                        $typeId = (int)$obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]["type-id"];
                                        $iPaymentType = $obj_card->getPaymentType();

                                        if($isCardTokenExist === true  || $card_psp_id === Constants::iMVAULT_PSP|| $iPaymentType == Constants::iPROCESSOR_TYPE_WALLET)
                                        {
                                            if($card_psp_id == Constants::iMVAULT_PSP) {
                                                $typeId = Constants::iMVAULT_WALLET;
                                            }
                                            $walletId = $typeId;
                                            if ($typeId > 0)
                                            {
                                                $wallet_Processor = WalletProcessor::produceConfig($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $typeId , $aHTTP_CONN_INFO);
                                                if(empty($wallet_Processor) === false && is_object($wallet_Processor) == true)
                                                {
                                                    $obj_PaymentDataXML = simpledom_load_string($wallet_Processor->getPaymentData($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]) );
                                                    if (count($obj_PaymentDataXML->{'payment-data'}) == 1)
                                                    {
                                                        $paymentCardTypeId = (int) $obj_PaymentDataXML->{'payment-data'}->card["type-id"];
                                                        if($paymentCardTypeId > 0)
                                                        {
                                                            $typeId = $paymentCardTypeId;
                                                        }
                                                    }
                                                }
                                            }
                                        }

                                        $aRoutes = array();
                                        $iPrimaryRoute = 0 ;
                                        $obj_CardXML = '';

                                        $is_legacy = $obj_TxnInfo->getClientConfig()->getAdditionalProperties(Constants::iInternalProperty, 'IS_LEGACY');
                                        if (strtolower($is_legacy) == 'false') {
                                            $iPSPId = $obj_TxnInfo->getPSPID();
                                            $iPrimaryRoute = $obj_TxnInfo->getRouteConfigID();

                                            if($iPrimaryRoute <=0 || $isCardTokenExist === true || $card_psp_id === Constants::iMVAULT_PSP  || $iPaymentType == Constants::iPROCESSOR_TYPE_WALLET)
                                            {
                                                $obj_RS = new RoutingService($obj_TxnInfo, $obj_ClientInfo, $aHTTP_CONN_INFO['routing-service'], $obj_DOM->{'authorize-payment'}[$i]["client-id"], $obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->amount["country-id"], $obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->amount["currency-id"], $obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->amount, $typeId, $issuerIdentificationNumber, $obj_card->getCardName(), NULL, $walletId);
                                                if($obj_RS instanceof RoutingService)
                                                {
                                                    $objTxnRoute = new PaymentRoute($_OBJ_DB, $obj_TxnInfo->getSessionId());
                                                    $iPrimaryRoute = $obj_RS->getAndStoreRoute($objTxnRoute);
                                                    # Update routeconfig ID in log.transaction table
                                                    $obj_TxnInfo->setRouteConfigID($iPrimaryRoute);
                                                    $obj_mPoint->logTransaction($obj_TxnInfo);
                                                }
                                            }
                                            $obj_CardXML = simpledom_load_string($obj_mCard->getCardConfigurationXML( (integer) $obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->amount, (int)$obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]["type-id"], $iPrimaryRoute) );
                                        }else{
                                            $obj_CardXML = simpledom_load_string($obj_mCard->getCards( (integer) $obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->amount) );
                                        }

                                        $issuerIdentificationNumber = NULL;
                                        if($isStoredCardPayment === true){
                                            $maskCardNumber = $obj_mPoint->getMaskCard($obj_TxnInfo->getAccountID(), $obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]["id"]);
                                            $issuerIdentificationNumber = General::getIssuerIdentificationNumber($maskCardNumber);
                                        }elseif ($isStoredCardPayment === false && $isCardTokenExist === false && $isCardNetworkExist === false){
                                            $issuerIdentificationNumber = General::getIssuerIdentificationNumber((string)$obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->{'card-number'});
                                        }

                                        //Check if card or payment method is enabled or disabled by merchant
										//Same check is  also implemented at app side.
                                        if(strtolower($is_legacy) == 'false') {
                                            $obj_Elem = $obj_CardXML->xpath("/cards/item[@type-id = " . intval($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]["type-id"]) . "]");
                                        }else{
                                            $obj_Elem = $obj_CardXML->xpath("/cards/item[@type-id = " . intval($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]["type-id"]) . " and @state-id=1 and @walletid = '']");
                                        }
										if (count($obj_Elem) == 0) { $aMsgCds[24] = "The selected payment card is not available"; } // Card disabled

										if($isStoredCardPayment === false && $isCardTokenExist === false && $isCardNetworkExist === false && $obj_CardValidator->valCardNumber() !== 720)
										{
										    $aMsgCds[21] = 'Invalid Card Number: ' . $obj_card->getCardNumber();
										}

                                        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                                        $ips = array_map('trim', $ips);
                                        $ip = $ips[0];
                                        $obj_ClientInfo = ClientInfo::produceInfo($obj_DOM->{'authorize-payment'}[$i]->{'client-info'},
                                            CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'authorize-payment'}[$i]->{'client-info'}->mobile["country-id"]),
                                            $ip);

                                        $obj_TransacionCountryConfig = null;
                                        if(empty($obj_DOM->{'authorize-payment'}[$i]->transaction->card->amount["country-id"]) === false)
                                        {
                                            $obj_TransacionCountryConfig = CountryConfig::produceConfig( $_OBJ_DB,$obj_DOM->{'authorize-payment'}[$i]->transaction->card->amount["country-id"]);
                                        }
                                        // Hash based Message Authentication Code (HMAC) enabled for client and payment transaction is not an attempt to simply save a card
                                        if (strlen($obj_ClientConfig->getSalt() ) > 0 && $obj_ClientConfig->getAdditionalProperties(Constants::iInternalProperty, "sessiontype") != 2 && (empty($obj_DOM->{'authorize-payment'}[$i]->transaction->{'foreign-exchange-info'}->{'sale-amount'})  === true &&  $obj_TxnInfo->getInitializedCurrencyConfig()->getID() === $obj_TxnInfo->getCurrencyConfig()->getID()))
                                        {
                                            $authToken = trim($obj_DOM->{'authorize-payment'}[$i]->{'auth-token'});
                                            if ($obj_Validator->valHMAC(trim($obj_DOM->{'authorize-payment'}[$i]->transaction->hmac), $obj_ClientConfig, $obj_ClientInfo, trim($obj_TxnInfo->getOrderID()), intval($obj_DOM->{'authorize-payment'}[$i]->transaction->card->amount), intval($obj_DOM->{'authorize-payment'}[$i]->transaction->card->amount["country-id"]),$obj_TransacionCountryConfig,$authToken) != 10) { $aMsgCds[210] = "Invalid HMAC:".trim($obj_DOM->{'authorize-payment'}[$i]->transaction->hmac); }
                                        }
                                        //made hmac mandatory for dcc
                                        else if (General::xml2bool($obj_Elem["dcc"]) === true)
                                        {
											$iForeignExchangeId = $obj_DOM->{'authorize-payment'}[$i]->transaction->{'foreign-exchange-info'}->{'id'};
											if(empty($iForeignExchangeId) === true){
												$iForeignExchangeId = $obj_TxnInfo->getExternalRef(Constants::iForeignExchange, $obj_TxnInfo->getPSPID());
											}
											if ($obj_Validator->valDccHMAC(trim($obj_DOM->{'authorize-payment'}[$i]->transaction->hmac), $obj_ClientConfig, $obj_ClientInfo, intval($obj_DOM->{'authorize-payment'}[$i]->transaction->card->amount), intval($obj_DOM->{'authorize-payment'}[$i]->transaction->card->amount["country-id"]),$obj_TransacionCountryConfig,$obj_TxnInfo, $iForeignExchangeId) != 10) { $aMsgCds[210] = "Invalid HMAC:".trim($obj_DOM->{'authorize-payment'}[$i]->transaction->hmac); }
                                        }
                                        $iSessionType = $obj_ClientConfig->getAdditionalProperties(Constants::iInternalProperty, "sessiontype");
                                        $pendingAmount = $obj_TxnInfo->getPaymentSession()->getPendingAmount();

                                        if($iSessionType > 1 &&  General::xml2bool($obj_Elem["dcc"]) === false)
                                        {
                                            if((integer)$obj_DOM->{'authorize-payment'}[$i]->transaction->card->amount > $pendingAmount)
                                            {
                                                $aMsgCds[53] = "Amount is more than pending amount: ". (integer)$obj_DOM->{'authorize-payment'}[$i]->transaction->card->amount;
                                            }
                                            else{
                                                $obj_TxnInfo->updateTransactionAmount($_OBJ_DB,(integer)$obj_DOM->{'authorize-payment'}[$i]->transaction->card->amount);
                                            }
                                        }
                                       else
                                       {
											$iForeignExchangeId = $obj_TxnInfo->getExternalRef(Constants::iForeignExchange, $obj_TxnInfo->getPSPID());
											if(General::xml2bool($obj_Elem["dcc"]) === true && empty($iForeignExchangeId) === true && empty($obj_DOM->{'authorize-payment'}[$i]->transaction->{'foreign-exchange-info'}->{'id'}) === false)
											{
												$obj_TxnInfo->setExternalReference($_OBJ_DB,intval($obj_Elem["pspid"]),Constants::iForeignExchange,$obj_DOM->{'authorize-payment'}[$i]->transaction->{'foreign-exchange-info'}->{'id'});
											}
											$iSaleAmount = (integer)$obj_DOM->{'authorize-payment'}[$i]->transaction->{'foreign-exchange-info'}->{'sale-amount'};
                                           if (General::xml2bool($obj_Elem["dcc"]) === true && empty($obj_DOM->{'authorize-payment'}[$i]->transaction->{'foreign-exchange-info'}->{'sale-amount'}) === false &&
                                               (($iSaleAmount < $pendingAmount && $iSessionType > 1) || $iSaleAmount === (int)$obj_TxnInfo->getAmount()  ) && ((int)$obj_DOM->{'authorize-payment'}[$i]->transaction->card->amount["currency-id"]) !== $obj_TxnInfo->getCurrencyConfig()->getID())
                                               {
                                                   $obj_CurrencyConfig = CurrencyConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->amount["currency-id"]);

                                                   if($iSessionType > 1 && $iSaleAmount < (int)$obj_TxnInfo->getAmount()) { $misc["amount"] = $iSaleAmount; }

                                                   $data['converted-currency-config'] = $obj_CurrencyConfig;
                                                   $data['converted-amount'] = (integer) $obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->amount;
                                                   $data['conversion-rate'] = $obj_DOM->{'authorize-payment'}[$i]->transaction->{'foreign-exchange-info'}->{'conversion-rate'};
                                                   $obj_TxnInfo = TxnInfo::produceInfo($obj_TxnInfo->getID(),$_OBJ_DB, $obj_TxnInfo, $data);
                                                   $obj_mPoint->logTransaction($obj_TxnInfo);

                                               }
                                             else if( $iSessionType > 1 && $iSaleAmount > $pendingAmount) { $aMsgCds[53] = "Amount is more than pending amount: ". (integer)$obj_DOM->{'authorize-payment'}[$i]->transaction->card->amount; }
                                             else if($obj_TxnInfo->getAmount() != intval($obj_DOM->{'authorize-payment'}[$i]->transaction->card->amount))
                                             {
                                                 $aMsgCds[52] = "Invalid amount:" . $obj_DOM->{'authorize-payment'}[$i]->transaction->card->amount;
                                             }
                                        }

                                        if($obj_card->getCardHolderName() !== '' && $obj_CardValidator->valCardFullName() !== 730){
                                                $aMsgCds[62] = "Please Enter valid name";
                                        }

                                        // Validate currency if explicitly passed in request, which defer from default currency of the country
                                        if(intval($obj_DOM->{'authorize-payment'}[$i]->transaction->card->amount["currency-id"]) > 0)
                                        {
                                        	if($obj_Validator->valCurrency($_OBJ_DB, intval($obj_DOM->{'authorize-payment'}[$i]->transaction->card->amount["currency-id"]) ,$obj_TransacionCountryConfig, intval( $obj_DOM->{'authorize-payment'}[$i]["client-id"])) != 10 ){
                                        		$aMsgCds[56] = "Invalid Currency:".intval($obj_DOM->{'authorize-payment'}[$i]->transaction->card->amount["currency-id"]) ;
                                        	}
                                        }

                                        // Validate service type id if explicitly passed in request
                                        $fxServiceTypeId = (integer)$obj_DOM->{'authorize-payment'}[$i]->transaction->{'foreign-exchange-info'}->{'service-type-id'};
                                        if($fxServiceTypeId > 0){
                                            if($obj_Validator->valFXServiceType($_OBJ_DB,$fxServiceTypeId) !== 10 ){
                                                $aMsgCds[57] = "Invalid service type id :".$fxServiceTypeId;
                                            }
                                        }
                                        if ($fxServiceTypeId)
                                        {
                                            $data['fxservicetypeid'] = $fxServiceTypeId;
                                        }

                                        if (isset($obj_Elem->capture_type) > 0)
                                        {
                                            $data['auto-capture'] = intval($obj_Elem->capture_type);
                                            $obj_TxnInfo = TxnInfo::produceInfo($obj_TxnInfo->getID(),$_OBJ_DB, $obj_TxnInfo, $data);
                                            $obj_mPoint->logTransaction($obj_TxnInfo);
                                        }

                                        // sso verification conditions checking 
										$sosPreference =  $obj_ClientConfig->getAdditionalProperties(Constants::iInternalProperty, "SSO_PREFERENCE");
					       				$sosPreference = strtoupper($sosPreference); 

										// Single Sign-On
					                    $authenticationURL = $obj_ClientConfig->getAuthenticationURL();
										$authToken = trim($obj_DOM->{'authorize-payment'}[$i]->{'auth-token'});
										$clientId = $obj_ClientConfig->getID(); 
                                        $userAuthenticationCode = -1;
										if (empty($authenticationURL) === false && empty($authToken)=== false)
										{	
											$obj_CustomerInfo = new CustomerInfo(0, $obj_DOM->{'authorize-payment'}[$i]->{'client-info'}->mobile["country-id"], $obj_DOM->{'authorize-payment'}[$i]->{'client-info'}->mobile, (string)$obj_DOM->{'authorize-payment'}[$i]->{'client-info'}->email, $obj_DOM->{'authorize-payment'}[$i]->{'client-info'}->{'customer-ref'}, "", $obj_DOM->{'authorize-payment'}[$i]->{'client-info'}["language"], $obj_DOM->{'authorize-payment'}[$i]->{'client-info'}["profileid"]);

											if(empty($obj_CustomerInfo) === false) {
                                                
                                                if ( $sosPreference === 'STRICT' )
						                        {
						                        	$userAuthcode = $obj_mPoint->auth($obj_TxnInfo->getClientConfig(), $obj_CustomerInfo, $authToken, $clientId, $sosPreference);

						                        	if ($userAuthenticationCode == 212)
														{
						                                	$aMsgCds[$userAuthenticationCode] = 'Mandatory fields are missing' ;
						                          	} 
						                          	if ($userAuthenticationCode == 1) {
						                          		 $aMsgCds[213] = 'Profile authentication failed' ;
						                          	}
						                        } 
						                        else {
													
													$userAuthenticationCode = $obj_mPoint->auth($obj_TxnInfo->getClientConfig(), $obj_CustomerInfo, $authToken, $clientId);
												}

                                            }
											else{
											    //Account Not Found
											    if ( $sosPreference !== 'STRICT' )
						                        {
									        		$userAuthenticationCode = 5;
									            }
											    
                                            }
										}
										else 
							            {
							            	if ( $sosPreference === 'STRICT' )
					                        {
								        		if (empty($authToken) === true)
								                { 
								                     $aMsgCds[211] = 'Auth token or SSO token not received' ;
								                } else {
								                     $aMsgCds[209] = 'Auth url not configured' ;
								                }
								            }
							            }

										// Success: Input Valid
										if (count($aMsgCds) == 0)
										{
											//Authentication is required if third party sso is enable and payment is stored card or request is containing password
                                            //Invoice payment type is not in use hence removed it from condition.
                                            if(($isStoredCardPayment === true || empty($obj_DOM->{'authorize-payment'}[$i]->password) === false) && $userAuthenticationCode === -1 )
                                            {
                                                $code = $obj_mPoint->auth($obj_TxnInfo->getAccountID(), (string) $obj_DOM->{'authorize-payment'}[$i]->password);
                                            }
                                            else
                                            {
                                                $code = $userAuthenticationCode === -1 ? 10 : $userAuthenticationCode;
                                            }

											// Authentication succeeded
											if ($code == 10 || ($code == 11 && $obj_ClientConfig->smsReceiptEnabled() === false) )
											{
												
												if ($obj_TxnInfo->getMobile() > 0) { $obj_mPoint->saveMobile($obj_TxnInfo->getAccountID(), $obj_TxnInfo->getMobile(), true); }
												switch ($iTypeID)
												{
                                                 // Not in use as of now hence commenting this code  (reference jira - CMP-4079)
                                                /***********************
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
															catch (HTTPException $ignore) { // Ignore  }
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
                                                **********************************************/
												case (Constants::iCARD_PURCHASE_TYPE):		// Authorize Purchase using Stored Card
												default:

													// 3rd Party Wallet
													if(count($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->token) == 1 || $card_psp_id === Constants::iMVAULT_PSP)
													{
                                                        if (empty($wallet_Processor) === true) {
                                                            $obj_XML = simpledom_load_string($obj_mCard->getCards((integer)$obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->amount));

                                                            if (count($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->cvc) == 1) {
                                                                $obj_Elem->cvc = (integer)$obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->cvc;
                                                            }

                                                            if (count($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->token) == 1) {
                                                                $obj_Elem->ticket = (string)$obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]->token;
                                                            }
                                                        }

														if(isset($wallet_Processor) == true && is_object($wallet_Processor) == true)
														{
															if (count($obj_PaymentDataXML->{'payment-data'}) == 1)
															{
																$obj_Elem = $obj_PaymentDataXML->{'payment-data'}->card;
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

                                                                if(strtolower($is_legacy) == 'false') {
                                                                    $obj_XML = $obj_CardXML->xpath("/cards/item[@type-id = " . (int)$obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]["type-id"] . "]");
                                                                }else{
                                                                    $obj_XML = $obj_CardXML->xpath("/cards/item[@type-id = ". $obj_Elem["type-id"] ." and @state-id=1 and @walletid=".$wallet_Processor->getPSPConfig()->getID()."]");
                                                                }

                                                                if (count($obj_XML) == 0)
                                                                {
                                                                    $code = 5;
                                                                    $xml = '<status code="24">The selected payment card is not available</status>';
                                                                } // Card disabled

                                                                $obj_Elem ["pspid"] = (int) $obj_XML["pspid"];
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
														$aPaymentMethods = $obj_mPoint->getClientConfig()->getPaymentMethods($_OBJ_DB);
														foreach ($aPaymentMethods as $m)
														{
															if ($m->getPaymentMethodID() == Constants::iINVOICE) { $iPSPID = $m->getPSPID(); }
														}
	
														if ($iPSPID > 0)
														{
                                                            if(strtolower($is_legacy) == 'false') {
                                                                $obj_PSPConfig = PSPConfig::produceConfiguration($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), $iPSPID, $obj_TxnInfo->getRouteConfigID());
                                                            }else{
                                                                $obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), $iPSPID);
                                                            }
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
													else if ($isStoredCardPayment === false && $isCardTokenExist === false && $isCardNetworkExist === false)
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

													//In case of Wallet payment card node will update so, Refresh the card and validator object
                                                    $obj_card = new Card($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j], $_OBJ_DB);
										            $obj_CardValidator = new CardValidator($obj_card);

                                                    if ((bool)$obj_Elem['CVCMANDATORY'] === TRUE) {
                                                        $cvcValidationCode = $obj_CardValidator->validateCVC();
                                                        if ($cvcValidationCode !== 710) {
                                                            $aMsgCds[22] = 'Invalid CVC';
                                                        }
                                                    }

													if ($code >= 10)
													{
														try
														{
														    if($obj_Elem["pspid"] > 0) {
                                                                if(strtolower($is_legacy) == 'false') {
                                                                    $obj_PSPConfig = PSPConfig::produceConfiguration($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), intval($obj_Elem["pspid"]), $obj_TxnInfo->getRouteConfigID());
                                                                }else{
                                                                    $obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), intval($obj_Elem["pspid"]));
                                                                }

																//For processorType 4 and 7, we trigger authorize passbook entry from pay.php itself
                                                                $txnPassbookObj = TxnPassbook::Get($_OBJ_DB, $obj_TxnInfo->getID(), $obj_TxnInfo->getClientConfig()->getID());

                                                                if($obj_PSPConfig->getProcessorType() !== Constants::iPROCESSOR_TYPE_APM && $obj_PSPConfig->getProcessorType() !== Constants::iPROCESSOR_TYPE_GATEWAY)
																{
																	$passbookEntry = new PassbookEntry
																	(
																		NULL,
																		$obj_TxnInfo->getAmount(),
																		$obj_TxnInfo->getCurrencyConfig()->getID(),
                                                                        Constants::iAuthorizeRequested,
                                                                        '',
                                                                        0,
                                                                        '',
                                                                        '',
                                                                        TRUE,
                                                                        NULL,
                                                                        NULL,
                                                                        $obj_TxnInfo->getClientConfig()->getID(),
                                                                        $obj_TxnInfo->getInitializedAmount()
																	);
																	if ($txnPassbookObj instanceof TxnPassbook)
																	{
																		$txnPassbookObj->addEntry($passbookEntry);
																		$txnPassbookObj->performPendingOperations();
																	}
																}
                                                                //Refresh TxnInfo obj In case of Wallet payment to get wallet-id
                                                                if($obj_card->getPaymentType() === 3)
                                                                $obj_TxnInfo =  TxnInfo::produceInfo( (integer) $obj_TxnInfo->getID(), $_OBJ_DB);

                                                                $fraudCheckResponse = CPMFRAUD::attemptFraudCheckIfRoutePresent($obj_Elem,$_OBJ_DB,$obj_ClientInfo, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO,$obj_mCard,$obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]["type-id"]);
                                                                if ($fraudCheckResponse->isFraudCheckAccepted() === true || $fraudCheckResponse->isFraudCheckAttempted() === false)
                                                                {
                                                                    if($obj_TxnInfo->hasEitherState($_OBJ_DB, array(Constants::iPRE_FRAUD_CHECK_ACCEPTED_STATE)) === false && $_OBJ_DB->countAffectedRows($obj_mCard->getFraudCheckRoute($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]["type-id"],Constants::iPROCESSOR_TYPE_POST_FRAUD_GATEWAY)) > 0)
                                                                    {
                                                                        $obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), Constants::iMVAULT_PSP);

                                                                        $obj_PSP = Callback::producePSP($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO, $obj_PSPConfig);
                                                                        $obj_PSP->saveCard($obj_Elem);
                                                                    }

                                                                   $bStoreBillingAddrs = $obj_ClientConfig->getAdditionalProperties(Constants::iInternalProperty, "IS_STORE_BILLING_ADDRS");

                                                                    if(General::xml2bool($bStoreBillingAddrs) === true)
                                                                    {
                                                                        if(empty($obj_Elem->address) === false)
                                                                        {

                                                                            $aBillingAddr['billing_address'][0]['street'] = (string) $obj_Elem->{'address'}->street;
                                                                            $aBillingAddr['billing_address'][0]['street2'] = (string) $obj_Elem->{'address'}->street2;
                                                                            $aBillingAddr['billing_address'][0]['city'] = (string) $obj_Elem->{'address'}->city;
                                                                            $aBillingAddr['billing_address'][0]['state'] = (string) $obj_Elem->{'address'}->state;
                                                                            $aBillingAddr['billing_address'][0]['zip'] = (string) $obj_Elem->{'address'}->{'postal-code'};
                                                                            $aBillingAddr['billing_address'][0]['country'] = (string) $obj_Elem->{'address'}['country-id'];
                                                                            $aBillingAddr['billing_address'][0]['reference_type'] = "transaction";
                                                                            $aBillingAddr['billing_address'][0]['reference_id'] = $obj_TxnInfo->getID();
                                                                            if (count($obj_Elem->address->{'full-name'}) == 1)
                                                                            {
                                                                                $pos = strrpos($obj_Elem->address->{'full-name'}, " ");
                                                                                if ($pos > 0)
                                                                                {
                                                                                    $aBillingAddr['billing_address'][0]['first_name'] = (string) trim(substr($obj_Elem->address->{'full-name'}, 0, $pos) );
                                                                                    $aBillingAddr['billing_address'][0]['last_name'] = (string) trim(substr($obj_Elem->address->{'full-name'}, $pos) );
                                                                                }
                                                                                else
                                                                                {
                                                                                    $aBillingAddr['billing_address'][0]['first_name'] = (string) trim($obj_Elem->address->{'full-name'});
                                                                                    $aBillingAddr['billing_address'][0]['last_name'] = "";
                                                                                }
                                                                            }
                                                                            else
                                                                            {
                                                                                $aBillingAddr['billing_address'][0]['first_name'] = $obj_Elem->address->{'first-name'};
                                                                                $aBillingAddr['billing_address'][0]['last_name'] = $obj_Elem->address->{'last-name'} ;
                                                                            }

                                                                            if (empty($obj_Elem->address->{'contact-details'}->mobile) === false &&
                                                                                empty($obj_Elem->address->{'contact-details'}->email) === false) {
                                                                                $aBillingAddr['billing_address'][0]['mobile'] = (string)$obj_Elem->address->{'contact-details'}->mobile;
                                                                                $aBillingAddr['billing_address'][0]['email'] = (string)$obj_Elem->address->{'contact-details'}->email;
                                                                                $aBillingAddr['billing_address'][0]['mobile_country_id'] = $obj_Elem->address->{'contact-details'}->{'mobile'}['country-id'];
                                                                            } else {
                                                                                $aBillingAddr['billing_address'][0]['mobile'] = "";
                                                                                $aBillingAddr['billing_address'][0]['email'] = "";
                                                                                $aBillingAddr['billing_address'][0]['mobile_country_id'] = "";
                                                                            }
                                                                            
                                                                            $shipping_id = $obj_TxnInfo->setShippingDetails($_OBJ_DB, $aBillingAddr['billing_address']);
                                                                        }
                                                                    }
                                                                    switch (intval($obj_Elem["pspid"])) {
                                                                        case (Constants::iSTRIPE_PSP):
                                                                            $obj_PSP = new Stripe_PSP($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, array());
                                                                            $aLogin = $obj_PSP->getMerchantLogin($obj_TxnInfo->getClientConfig()->getID(), Constants::iSTRIPE_PSP, true);
                                                                            $code = $obj_PSP->authTicket((integer)$obj_Elem->ticket, $aLogin["password"]);
                                                                            if ($code == "OK") {
                                                                                if ($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]["type-id"] === Constants::iAPPLE_PAY) {
                                                                                    $xml .= '<status code="100">Payment Authorized using Apple Pay</status>';
                                                                                } else {
                                                                                    $xml .= '<status code="100">Payment Authorized using Stored Card</status>';
                                                                                }
                                                                            } // Error: Authorization declined
                                                                            else {
                                                                                $obj_mPoint->delMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);

                                                                                header("HTTP/1.1 502 Bad Gateway");

                                                                                $xml .= '<status code="92">Authorization failed, Stripe returned error: ' . $code . '</status>';
                                                                            }
                                                                            break;
                                                                        case (Constants::iDIBS_PSP):    // DIBS
                                                                            // Authorise payment with PSP based on Ticket

                                                                            $obj_PSP = new DIBS($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO['dibs']);
                                                                            $iTxnID = $obj_PSP->authTicket($obj_Elem);
                                                                            // Authorization succeeded
                                                                            if ($iTxnID > 0) {
                                                                                // Only generate internal callback for payments made with a Stored Card
                                                                                if (count($obj_Elem->ticket) == 1) {
                                                                                    try {
                                                                                        // Initialise Callback to Client
                                                                                        $aCPM_CONN_INFO["path"] = "/callback/dibs.php";
                                                                                        $obj_PSP->initCallback(HTTPConnInfo::produceConnInfo($aCPM_CONN_INFO), intval($obj_Elem->type["id"]), $iTxnID, (string)$obj_Elem->mask, (string)$obj_Elem->expiry);
                                                                                    } catch (HTTPException $ignore) { /* Ignore */
                                                                                    }

                                                                                    //$xml = '<status code="100">Payment Authorized using Stored Card</status>';
                                                                                } //else { $xml = '<status code="2000">Payment authorized using new card</status>'; }
                                                                                $xml = '<status code="100">Payment Authorized using Stored Card</status>';
                                                                            } // Error: Authorization declined
                                                                            else {
                                                                                $obj_mPoint->delMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);

                                                                                header("HTTP/1.1 502 Bad Gateway");

                                                                                $xml .= '<status code="92">Authorization failed, DIBS returned error code' . $iTxnID . '</status>';
                                                                            }
                                                                            break;
                                                                        case (Constants::iWANNAFIND_PSP):    // WannaFind
                                                                            // Authorise payment with PSP based on Ticket
                                                                            $obj_PSP = new WannaFind($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO["wannafind"]);
                                                                            $iTxnID = $obj_PSP->authTicket((integer)$obj_Elem->ticket);
                                                                            // Authorization succeeded
                                                                            if ($iTxnID > 0) {
                                                                                try {
                                                                                    // Initialise Callback to Client
                                                                                    $aCPM_CONN_INFO["path"] = "/callback/wannafind.php";
                                                                                    $obj_PSP->initCallback(HTTPConnInfo::produceConnInfo($aCPM_CONN_INFO), intval($obj_Elem->type["id"]), $iTxnID);
                                                                                } catch (HTTPException $ignore) { /* Ignore */
                                                                                }

                                                                                $xml .= '<status code="100">Payment Authorized using Stored Card</status>';
                                                                            } // Error: Authorization declined
                                                                            else {
                                                                                $obj_mPoint->delMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);

                                                                                header("HTTP/1.1 502 Bad Gateway");

                                                                                $xml .= '<status code="92">Authorization failed, WannaFind returned error code' . $iTxnID . '</status>';
                                                                            }
                                                                            break;
                                                                        case (Constants::iNETAXEPT_PSP): // NetAxept
                                                                            if(strtolower($is_legacy) == 'false') {
                                                                                $obj_PSPConfig = PSPConfig::produceConfiguration($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), Constants::iNETAXEPT_PSP, $obj_TxnInfo->getRouteConfigID());
                                                                            }else{
                                                                                $obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), Constants::iNETAXEPT_PSP);
                                                                            }
                                                                            $obj_PSP = new NetAxept($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO["netaxept"], $obj_PSPConfig);

                                                                            if ($obj_TxnInfo->getMode() > 0) {
                                                                                $aHTTP_CONN_INFO["netaxept"]["host"] = str_replace("epayment.", "epayment-test.", $aHTTP_CONN_INFO["netaxept"]["host"]);
                                                                            }
                                                                            $aHTTP_CONN_INFO["netaxept"]["username"] = $obj_PSPConfig->getUsername();
                                                                            $aHTTP_CONN_INFO["netaxept"]["password"] = $obj_PSPConfig->getPassword();
                                                                            $oCI = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["netaxept"]);

                                                                            $code = $obj_PSP->authTicket($obj_Elem->ticket, $oCI, $obj_PSPConfig->getMerchantAccount());
                                                                            // Authorization succeeded
                                                                            if ($code == "OK") {
                                                                                $xml .= '<status code="100">Payment Authorized using Stored Card</status>';
                                                                            } // Error: Authorization declined
                                                                            else {
                                                                                $obj_mPoint->delMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);

                                                                                header("HTTP/1.1 502 Bad Gateway");

                                                                                $xml .= '<status code="92">Authorization failed, NetAxcept returned error: ' . $code . '</status>';
                                                                            }
                                                                            break;
                                                                        case (Constants::iCPG_PSP):
                                                                            $obj_PSP = new CPG($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO["cpg"]);
                                                                            if(strtolower($is_legacy) == 'false') {
                                                                                $obj_PSPConfig = PSPConfig::produceConfiguration($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), Constants::iCPG_PSP, $obj_TxnInfo->getRouteConfigID());
                                                                            }else{
                                                                                $obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), Constants::iCPG_PSP);
                                                                            }
                                                                            $aHTTP_CONN_INFO["cpg"]["username"] = $obj_PSPConfig->getUsername();
                                                                            $aHTTP_CONN_INFO["cpg"]["password"] = $obj_PSPConfig->getPassword();
                                                                            $obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["cpg"]);

                                                                                $xml .= $obj_PSP->authTicket($obj_ConnInfo, $obj_Elem);
                                                                            break;
                                                                        case (Constants::iGLOBAL_COLLECT_PSP): // GlobalCollect
                                                                            if(strtolower($is_legacy) == 'false') {
                                                                                $obj_PSPConfig = PSPConfig::produceConfiguration($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), Constants::iGLOBAL_COLLECT_PSP, $obj_TxnInfo->getRouteConfigID());
                                                                            }else{
                                                                                $obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), Constants::iGLOBAL_COLLECT_PSP);
                                                                            }
                                                                            $obj_PSP = new GlobalCollect($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO["global-collect"]);

                                                                            $response = $obj_PSP->authorize($obj_PSPConfig, $obj_Elem, $obj_ClientInfo);
                                                                            $code = $response->code;
                                                                            // Authorization succeeded
                                                                            if ($code == "100") {
                                                                                $obj_TxnInfo = TxnInfo::produceInfo((integer)$obj_TxnInfo->getID(), $_OBJ_DB);
                                                                                $obj_PSP->initCallback($obj_PSPConfig, $obj_TxnInfo, Constants::iPAYMENT_ACCEPTED_STATE, "Payment Authorized using store card.", intval($obj_Elem->type["id"]));

                                                                                $xml .= '<status code="100">Payment authorized using  card</status>';
                                                                            } else if ($code == "2000") {
                                                                                $xml .= '<status code="2000">Payment authorized using card</status>';
                                                                            } else if (strpos($code, '2005') !== false) {
                                                                                header("HTTP/1.1 303");
                                                                                $xml .= $response->body;
                                                                            } else if (is_null($token) == false) {
                                                                                $xml .= '<status code="' . $code . '">Globalcollect returned : ' . $code . '</status>';
                                                                            } // Error: Authorization declined
                                                                            else {
                                                                                $obj_mPoint->delMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);

                                                                                header("HTTP/1.1 502 Bad Gateway");

                                                                                $xml .= '<status code="92">Authorization failed, Globalcollect returned error: ' . $code . '</status>';
                                                                            }
                                                                            break;

                                                                        case (Constants::iCHUBB_PSP): // CHUBB
                                                                            if(strtolower($is_legacy) == 'false') {
                                                                                $obj_PSPConfig = PSPConfig::produceConfiguration($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), Constants::iCHUBB_PSP, $obj_TxnInfo->getRouteConfigID());
                                                                            }else{
                                                                                $obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), Constants::iCHUBB_PSP);
                                                                            }
                                                                            $obj_PSP = new CHUBB($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO["chubb"]);

                                                                            $response = $obj_PSP->authorize($obj_PSPConfig, $obj_Elem, $obj_ClientInfo);
                                                                            $code = $response->code;
                                                                            // Authorization succeeded
                                                                            if ($code == "100") {
                                                                                $xml .= '<status code="100">Payment Authorized using stored card</status>';
                                                                            } else if ($code == "2000") {
                                                                                $xml .= '<status code="2000">Payment authorized</status>';
                                                                            } else if ($code == "2009") {
                                                                                $xml .= '<status code="2009">Payment authorized and card stored.</status>';
                                                                            } // Error: Authorization declined
                                                                            else {
                                                                                $obj_mPoint->delMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);

                                                                                header("HTTP/1.1 502 Bad Gateway");

                                                                                $xml .= '<status code="92">Authorization failed, CHUBB returned error: ' . $code . '</status>';
                                                                            }
                                                                            break;
                                                                        case (Constants::iSWISH_APM): // SWISH
                                                                            try {

                                                                                $obj_Processor = PaymentProcessor::produceConfig($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, intval($obj_Elem["pspid"]), $aHTTP_CONN_INFO);
                                                                                
                                                                                if ($obj_Processor->getPSPConfig()->getAdditionalProperties(Constants::iInternalProperty, "3DVERIFICATION") === 'mpi') {
                                                                                    $request = str_replace("authorize-payment", "authenticate", $HTTP_RAW_POST_DATA);
                                                                                    $response = $obj_Processor->authenticate($request,$obj_Elem,$obj_ClientInfo);
                                                                                    $code = $response->code;
                                                                                } else {
                                                                                    $response = $obj_Processor->authorize($obj_Elem, $obj_ClientInfo);
                                                                                    $code = $response->code;
                                                                                }
                                                                                
                                                                                // Authorization succeeded
                                                                                //2001 is not expected response code for any request
                                                                                if ($code == "2000" || $code == "2001") {
                                                                                    $xml .= '<status code="2000">Payment authorized</status>';
                                                                                } // Error: Authorization declined'
                                                                                else if ($code == "2010") {
                                                                                    $xml .= '<status code="2010">Payment declined</status>';
                                                                                } // Error: Authorization declined'
                                                                                
                                                                                else {
                                                                                    $obj_mPoint->delMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);
                                                                                    
                                                                                    header("HTTP/1.1 502 Bad Gateway");
                                                                                    
                                                                                    $xml .= '<status code="92">Authorization failed, ' . $obj_Processor->getPSPConfig()->getName() . ' returned error: ' . $code . '</status>';
                                                                                }
                                                                                
                                                                            } catch (PaymentProcessorException $e) {
                                                                                $obj_mPoint->delMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);
                                                                                
                                                                                header("HTTP/1.1 500 Internal Server Error");
                                                                                
                                                                                $xml .= '<status code="99">' . $e->getMessage() . '</status>';
                                                                            }
                                                                            break;

                                                                        default:    // Use Payment processor for PSP.
                                                                            try {
                                                                                $obj_Processor = PaymentProcessor::produceConfig($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, intval($obj_Elem["pspid"]), $aHTTP_CONN_INFO);
                                                                                $response = NULL;
                                                                                if ($obj_Processor->getPSPConfig()->getAdditionalProperties(Constants::iInternalProperty, "3DVERIFICATION") === 'mpi') {
                                                                                    $request = str_replace("authorize-payment", "authenticate", file_get_contents("php://input"));
                                                                                    $response = $obj_Processor->authenticate($request,$obj_Elem,$obj_ClientInfo);
                                                                                } else {
                                                                                    $response = $obj_Processor->authorize($obj_Elem, $obj_ClientInfo);
																				}
																				$code = $response->code;

                                                                                $paymentRetryWithAlternateRoute = $obj_TxnInfo->getClientConfig()->getAdditionalProperties(Constants::iInternalProperty, 'PAYMENT_RETRY_WITH_ALTERNATE_ROUTE');
                                                                                $xml = $obj_mPoint->processAuthResponse($obj_TxnInfo, $obj_Processor, $aHTTP_CONN_INFO, $obj_Elem, $response, $is_legacy, $paymentRetryWithAlternateRoute);

                                                                            } catch (PaymentProcessorException $e) {
                                                                                $obj_mPoint->delMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);

                                                                                header("HTTP/1.1 500 Internal Server Error");

                                                                                $xml .= '<status code="99">' . $e->getMessage() . '</status>';
                                                                            }
                                                                            break;

                                                                    }
                                                                }
                                                                else
                                                                {
                                                                    $obj_Processor = PaymentProcessor::produceConfig($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, (int)$obj_Elem["pspid"], $aHTTP_CONN_INFO);
                                                                    $aCallbackArgs = array("amount" => $obj_TxnInfo->getAmount(),
                                                                        "cardid" =>  $obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]);
                                                                    if ($obj_TxnInfo->getCallbackURL() != "") { $obj_Processor->notifyClient(Constants::iPAYMENT_REJECTED_STATE, $aCallbackArgs, $obj_TxnInfo->getClientConfig()->getSurePayConfig($_OBJ_DB)); }


                                                                    $obj_mPoint->newMessage($obj_TxnInfo->getID(),Constants::iPAYMENT_REJECTED_STATE,'Authorization Declined Due to Failed Fraud Check And Authorization is not attempted');
                                                                    $obj_Processor->getPSPInfo()->updateSessionState(Constants::iPAYMENT_REJECTED_STATE,$obj_Processor->getPSPInfo()->getPSPID(),$obj_TxnInfo->getAmount(),"",null,"",$obj_TxnInfo->getClientConfig()->getSurePayConfig($_OBJ_DB));
                                                                    $xml .= '<status code="2010">Authorization Declined Due to Failed Fraud Check And Authorization is not attempted.</status>';
                                                                }
                                                            }

                                                            
                                                            /*Complete Tokenization after successful authorization*/
                                                            if ($code >= Constants::iPAYMENT_ACCEPTED_STATE and $code < Constants::iPAYMENT_REJECTED_STATE)
                                                            {
                                                                $iTokenzationProcessor = intval($obj_mCard->getTokenizationRoute(intval(intval($obj_DOM->{'authorize-payment'}[$i]->transaction->card[$j]["type-id"]) ) ) );

                                                                if(empty($iTokenzationProcessor) === false)
                                                                {
                                                                    $obj_TxnInfo = $obj_TxnInfo = TxnInfo::produceInfo( (integer) $obj_TxnInfo->getID(), $_OBJ_DB);
                                                                    $obj_TokenizationPSP = PaymentProcessor::produceConfig($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, intval($iTokenzationProcessor), $aHTTP_CONN_INFO);
                                                                    $sToken = $obj_TokenizationPSP->tokenize($aHTTP_CONN_INFO, $obj_Elem);

                                                                    if(empty($sToken) === false)
                                                                    {
                                                                        $xml .= $sToken;
                                                                    }

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
                                                        header("HTTP/1.1 403 Forbidden");
														//The node <status> is returned along with the status code
														if (count($obj_XML->status) > 0) { $xml =  str_replace('<?xml version="1.0"?>', '', $obj_XML->status->asXML() ); }
														if (empty($xml) === true && count($obj_XML->status) == 0)
                                                        {
                                                            $xml = '<status code="79">An unknown error occurred while retrieving payment data from 3rd party wallet</status>';
                                                        }
                                                        $xml =  str_replace('<?xml version="1.0"?>', '', $xml);
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
                                else if($isVoucherRedeem === FALSE) 
                                {
									$_OBJ_DB->query("ROLLBACK");
									if($isVoucherRedeemStatus === -1) {
                                        header("HTTP/1.1 400 Bad Request");
                                        $xml .= '<status code="400">Invalid Tender</status>';
                                    }
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
