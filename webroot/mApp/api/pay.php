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
use api\classes\merchantservices\Repositories\ReadOnlyConfigRepository;

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
// Require specific Business logic for the PayEx component
require_once(sCLASS_PATH ."/payex.php");
// Require specific Business logic for the NetAxept component
require_once(sCLASS_PATH ."/netaxept.php");
// Require specific Business logic for the MobilePay component
require_once(sCLASS_PATH ."/mobilepay.php");
// Require specific Business logic for the WireCard component
require_once(sCLASS_PATH ."/wirecard.php");
// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
// Require specific Business logic for the Nets component
require_once(sCLASS_PATH ."/nets.php");
// Require specific Business logic for the mVault component
require_once(sCLASS_PATH ."/mvault.php");
// Require specific Business logic for the Amex component
require_once(sCLASS_PATH ."/amex.php");
// Require specific Business logic for the CHUBB component
require_once(sCLASS_PATH ."/chubb.php");
// Require Data Class for Client Information
require_once(sCLASS_PATH ."/clientinfo.php");
// Require specific Business logic for the UATP component
require_once(sCLASS_PATH . "/uatp.php");
// Require specific Business logic for the Chase component
require_once(sCLASS_PATH ."/chase.php");
require_once(sCLASS_PATH . '/txn_passbook.php');
require_once(sCLASS_PATH . '/passbookentry.php');
require_once(sCLASS_PATH ."/core/card.php");
require_once sCLASS_PATH . '/routing_service.php';
require_once sCLASS_PATH . '/routing_service_response.php';
require_once(sCLASS_PATH . '/payment_processor.php');
require_once(sCLASS_PATH . '/wallet_processor.php');
require_once(sCLASS_PATH . '/payment_route.php');
// Require specific Business logic for the CEBU Payment Center component
require_once(sCLASS_PATH .'/apm/CebuPaymentCenter.php');
// Require data data class for Customer Information
require_once(sCLASS_PATH ."/customer_info.php");

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
$obj_DOM = simpledom_load_string(file_get_contents('php://input'));

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mpoint.xsd") === true && count($obj_DOM->pay) > 0)
	{
		$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);

        $xml = '';
		for ($i=0, $iMax = count($obj_DOM->pay); $i< $iMax; $i++)
		{
			// Set Global Defaults
			if (empty($obj_DOM->pay[$i]["account"]) === true || (int)$obj_DOM->pay[$i]["account"] < 1) { $obj_DOM->pay[$i]["account"] = -1; }

			$obj_TxnInfo = null;
            // Validate basic information
            $obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->pay[$i]["client-id"], (integer) $obj_DOM->pay[$i]["account"]);
            if($obj_ClientConfig instanceof ClientConfig === false)
            {
                $code = Validate::valBasic($_OBJ_DB, (integer) $obj_DOM->pay[$i]["client-id"], (integer) $obj_DOM->pay[$i]["account"])  ;
                $aMsgCds[$code] =  $code!=100? "Client ID / Account doesn't match":"";
            }
           	else
			{
				$obj_TxnInfo = TxnInfo::produceInfo($obj_DOM->pay[$i]->transaction["id"], $_OBJ_DB);
			}

			if(count($aMsgCds) === 0 && $obj_TxnInfo->hasEitherState($_OBJ_DB, array(Constants::iPAYMENT_WITH_ACCOUNT_STATE, Constants::iPAYMENT_WITH_VOUCHER_STATE, Constants::iPAYMENT_ACCEPTED_STATE, Constants::iPAYMENT_3DS_VERIFICATION_STATE, constants::iPAYMENT_PENDING_STATE)) === true)
			{
				$aMsgCds[103] = "Authorization already in progress";
			}
			if (count($aMsgCds) === 0)
			{

				// Client successfully authenticated
 				if ($obj_ClientConfig->hasAccess($_SERVER['HTTP_X_ORIGINAL_FORWARDED_FOR']) === true && $obj_ClientConfig->getUsername() === trim($_SERVER['PHP_AUTH_USER']) && $obj_ClientConfig->getPassword() === trim($_SERVER['PHP_AUTH_PW']))
                 {
                    $repository = new ReadOnlyConfigRepository($_OBJ_DB,$obj_TxnInfo);
                    $isVoucherRedeem = FALSE;
                    $isVoucherRedeemStatus = -1;
                    $validRequest= true; // for split payment request validation
                    $isTxnCreated = False; // for split txn is already is created or not
                    $checkPaymentType = array();
                    $iSessionType = (int)$obj_ClientConfig->getAdditionalProperties(0, 'sessiontype');
                    $is_legacy = $obj_TxnInfo->getClientConfig()->getClientServices()->isLegacyFlow();
                    $obj_mCard = new CreditCard($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo);

                    // check voucher node is appearing before card node and according to that set preference
                    $getNodes = $obj_DOM->xpath('//pay/transaction/*');
                    foreach($getNodes as $voucherPreferred) {
                        $preference[] = $voucherPreferred->getName();
                    }
                    $isVoucherPreferred = "true";
                    if($preference[0] == 'card'){
                        $isVoucherPreferred = "false";
                    }
                    $paymentTypes= array();
                    for ($j = 0, $jMax = count($obj_DOM->pay[$i]->transaction->card); $j < $jMax; $j++) {
                        $obj_card = new Card($obj_DOM->pay[$i]->transaction->card[$j], $_OBJ_DB);
                        $iPaymentTypes['PAYMENTTYPE'] = $obj_card->getPaymentType();
                        //if card comes first then seq is 1 otherwise 2
                        $iPaymentTypes['SEQUENCE_NO'] = 1;
                        if($isVoucherPreferred == "true"){
                            $iPaymentTypes['SEQUENCE_NO'] = 2;
                        }
                        $paymentTypes[] = $iPaymentTypes;
                    }
                    if (count($obj_DOM->{'pay'}[$i]->transaction->voucher) > 0){
                        $iPaymentTypes['PAYMENTTYPE'] = Constants::iPAYMENT_TYPE_VOUCHER;
                        $iPaymentTypes['SEQUENCE_NO'] = 2;
                        //if voucher comes first then seq is 1 otherwise 2
                        if($isVoucherPreferred == "true"){
                            $iPaymentTypes['SEQUENCE_NO'] = 1;
                        }
                        array_push($paymentTypes,$iPaymentTypes);
                    }

                    //validate the request against active split
                    if($iSessionType > 1 && $obj_TxnInfo->getPaymentSession()->getAmount() !== (integer)$obj_DOM->pay[$i]->transaction->card->amount)
                    {
                        // check if txn is retry in same split session
                        $checkTxnSplit = $obj_TxnInfo->getActiveSplitSession($_OBJ_DB,$obj_TxnInfo->getSessionId());
                        if($checkTxnSplit > 0 && $checkTxnSplit == $obj_TxnInfo->getSessionId()){
                            $validateCombinations = \General::getApplicableCombinations($_OBJ_DB,$paymentTypes,(integer) $obj_DOM->pay[$i]["client-id"],$obj_TxnInfo->getSessionId(),true);
                            if(empty($validateCombinations)){
                                $validRequest = false;
                                header("HTTP/1.1 502 Bad Gateway");
                                $xml .= '<status code="99">The given request Split Combination is not configured for the client</status>';
                            }
                        }
                    }
                    $checkPaymentType= array_column($paymentTypes, 'PAYMENTTYPE');
                    if (count($obj_DOM->{'pay'}[$i]->transaction->voucher) > 0 && $validRequest==true) // voucher payment
				    {
                         if (in_array(Constants::iPAYMENT_TYPE_APM, $checkPaymentType)) {
                            $processVoucher = General::processVoucher($_OBJ_DB, $obj_DOM->{'pay'}[$i], $obj_TxnInfo, $obj_mPoint, $obj_mCard, $aHTTP_CONN_INFO, $isVoucherPreferred, $iSessionType, $is_legacy);
                            if(isset($processVoucher['code'])) {
                                if ($processVoucher['code'] == 52) {
                                    $aMsgCds[52] = "Amount is more than pending amount: " . $processVoucher['iAmount'];
                                    $isVoucherErrorFound = TRUE;
                                    $xml .= '<status code="52">Amount is more than pending amount:  ' . $processVoucher['iAmount'] . '</status>';
                                    $isVoucherRedeem = TRUE;
                                } else if ($processVoucher['code'] == 53) {
                                    $aMsgCds[53] = "Amount is more than pending amount: " . $processVoucher['iAmount'];
                                    $xml .= '<status code="53">Amount is more than pending amount:  ' . $processVoucher['iAmount'] . '</status>';
                                } else {
                                    $isVoucherRedeem = TRUE;
                                }
                            }

                            $isVoucherErrorFound = !empty($processVoucher) ? $processVoucher['isVoucherErrorFound'] : FALSE;
                            $isVoucherPreferred = !empty($processVoucher) ? $processVoucher['isVoucherPreferred'] : 'true';
                            $isVoucherRedeem = !empty($processVoucher) ? $processVoucher['isVoucherRedeem'] : FALSE;
                            $isTxnCreated = !empty($processVoucher) ? $processVoucher['isTxnCreated'] : FALSE;

                            $cardNode = $obj_DOM->{'pay'}[$i]->transaction->card;
                            if ($isVoucherErrorFound === FALSE && ((is_object($cardNode) === false || count($cardNode) === 0) || $isVoucherPreferred !== "false")) {
                                $VoucherRedeemStatus = General::redeemVoucherAuth($_OBJ_DB, $aHTTP_CONN_INFO, $obj_DOM->{'pay'}[$i], $obj_TxnInfo, $obj_mPoint, $_OBJ_TXT, $obj_mCard, $is_legacy);
                                $isVoucherRedeemStatus = $VoucherRedeemStatus['code'];
                                $isVoucherRedeem       = $VoucherRedeemStatus['isVoucherRedeem'];
                                if($isVoucherRedeemStatus === 24){
                                    $xml .= '<status code="24">The selected payment card is not available</status>';
                                }else if ($isVoucherRedeemStatus === 100) {
                                    $xml .= '<status code="100">Payment authorized using Voucher</status>';
                                } elseif ($isVoucherRedeemStatus === 43) {
                                    header("HTTP/1.1 402 Payment Required");
                                    $xml .= '<status code="43">Insufficient balance on voucher</status>';
                                } elseif ($isVoucherRedeemStatus === 45) {
                                    header("HTTP/1.1 401 Unauthorized");
                                    $xml .= '<status code="45">Voucher and Redeem device-ids not equal</status>';
                                } elseif ($isVoucherRedeemStatus === 48) {
                                    header("HTTP/1.1 423 Locked");
                                    $xml .= '<status code="48">Voucher payment temporarily locked</status>';
                                } else {
                                    header("HTTP/1.1 502 Bad Gateway");
                                    $xml .= '<status code="92">Payment rejected by voucher issuer</status>';
                                }
                            } else if ($isVoucherErrorFound === FALSE) {
                                header("HTTP/1.1 412 Precondition Failed");
                                $isVoucherRedeemStatus = 99;
                                $xml .= '<status code="99">Voucher payment not configured for client</status>';
                            }
                        }
                    }

                    if ((($iSessionType > 1 && $isVoucherRedeem === TRUE && $isVoucherRedeemStatus === 100) || ($isVoucherRedeem === FALSE && $isVoucherRedeemStatus === -1)) && is_object($obj_DOM->{'pay'}[$i]->transaction->card) && count($obj_DOM->{'pay'}[$i]->transaction->card) > 0 && $validRequest==true) {
                        if ($iSessionType > 1 && $isVoucherRedeem === TRUE && $isVoucherRedeemStatus === 100 && (in_array(Constants::iPAYMENT_TYPE_APM, $checkPaymentType)))
                        {
                            $misc = [];
                            $misc["routeconfigid"] = -1;

                            $txnObj = $obj_mPoint->createTxnFromTxn($obj_TxnInfo, $obj_TxnInfo->getPaymentSession()->getPendingAmount(),TRUE, '', array(),$misc);
                            if ($txnObj !== NULL) {
                                $isTxnCreated = true;
                                $obj_TxnInfo = $txnObj;
                                $_OBJ_DB->query('COMMIT');
                                $_OBJ_DB->query('START TRANSACTION');
                            } else {
                                $_OBJ_DB->query('ROLLBACK');
                            }
                        }
                        $amount      = (integer)$obj_DOM->pay[$i]->transaction->card->amount;
                        $iSaleAmount = (integer)$obj_DOM->pay[$i]->transaction->{'foreign-exchange-info'}->{'sale-amount'};
                        if($iSaleAmount > 0 ) {
                            $amount =  $iSaleAmount;
                        }
                        $obj_TxnInfo->updateSessionType($amount);
                        if($isTxnCreated == false && $iSessionType > 1 && in_array(Constants::iPAYMENT_TYPE_APM, $checkPaymentType)){
                            $obj_TxnInfo->setSplitSessionDetails($_OBJ_DB,$obj_TxnInfo->getSessionId(),[$obj_TxnInfo->getID()]);
                        }
                        $additionalTxnData = [];
                        if (isset($obj_DOM->{'pay'}[$i]->transaction->{'additional-data'})) {
                        $additionalDataParamsCount = count($obj_DOM->{'pay'}[$i]->transaction->{'additional-data'}->children());
                        for ($index = 0; $index < $additionalDataParamsCount; $index++)
                        {
                            $additionalTxnData[$index]['name'] = (string)$obj_DOM->{'pay'}[$i]->transaction->{'additional-data'}->param[$index]['name'];
                            $additionalTxnData[$index]['value'] = (string)$obj_DOM->{'pay'}[$i]->transaction->{'additional-data'}->param[$index];
                            $additionalTxnData[$index]['type'] = (string)'Transaction';
                        }

                        if(count($additionalTxnData) > 0)
                        {
                            $obj_TxnInfo->setAdditionalDetails($_OBJ_DB,$additionalTxnData,$obj_TxnInfo->getID());
                        }
                    }

					$obj_Validator = new Validate($obj_ClientConfig->getCountryConfig() );
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

						// Validate foreign exchange service type id if explicitly passed in request
						$fxServiceTypeId =  (integer)$obj_DOM->pay[$i]->transaction->{'foreign-exchange-info'}->{'service-type-id'};

						if($fxServiceTypeId > 0)
                        {
                            if(isset(Constants::aFXServiceType[$fxServiceTypeId]) === false )
                            {
                                $aMsgCds[57] = "Invalid service type id :".$fxServiceTypeId ;
                            }
						}

						if ($fxServiceTypeId)
                        {
                            $obj_TxnInfo->setFXServiceTypeID($fxServiceTypeId);
                        }

                        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                        $ips = array_map('trim', $ips);
                        $ip = $ips[0];
                        $obj_ClientInfo = ClientInfo::produceInfo($obj_DOM->pay[$i]->{'client-info'},
                            CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->pay[$i]->{'client-info'}->mobile["country-id"]),
                            $ip);
                        $obj_card = new Card($obj_DOM->pay[$i]->transaction->card[$j], $_OBJ_DB);
						$walletId = NULL;
						$iPaymentType = $obj_card->getPaymentType();
						if($iPaymentType == Constants::iPROCESSOR_TYPE_WALLET)
						{
							$walletId = (int) $obj_DOM->pay[$i]->transaction->card[$j]["type-id"];
						}
                        $cardName = $obj_card->getCardName();

                        $obj_CardResultSet = FALSE;
						$aRoutes = array();
                        $pspId = -1;

                        if($obj_card->getPaymentType($_OBJ_DB) === Constants::iPAYMENT_TYPE_OFFLINE)
                        {
							$data['auto-capture'] = 2;
							//For Offline payment method fee is considered as holding charges required to add in actual amount
							if($obj_TxnInfo->getFee() > 0 && (((integer)$obj_DOM->pay[$i]->transaction->card->amount)+ $obj_TxnInfo->getFee()) === (integer)($obj_TxnInfo->getAmount() + $obj_TxnInfo->getFee()))
							{
								$data['converted-amount'] = $obj_TxnInfo->getAmount() + $obj_TxnInfo->getFee();
							}
						}

                        if ($is_legacy === false)
                        {
                                $obj_CardResultSet = General::getRouteConfiguration($repository,$_OBJ_DB, $obj_mCard, $obj_TxnInfo, $obj_ClientInfo, $aHTTP_CONN_INFO['routing-service'], (int)$obj_DOM->pay [$i]["client-id"], (int)$obj_DOM->pay[$i]->transaction->card[$j]->amount["country-id"], (int)$obj_DOM->pay[$i]->transaction->card[$j]->amount["currency-id"], $obj_DOM->pay[$i]->transaction->card[$j]->amount, (int)$obj_DOM->pay[$i]->transaction->card[$j]["type-id"], $obj_DOM->pay[$i]->transaction->card[$j]->{'issuer-identification-number'}, $obj_card->getCardName(), NULL, $walletId);
                        } else {
                                $obj_CardResultSet = $obj_mCard->getCardObject(( integer )$obj_DOM->pay [$i]->transaction->card [$j]->amount, (int)$obj_DOM->pay[$i]->transaction->card[$j]['type-id'], 1, -1);
                        }

                        if ($obj_CardResultSet === FALSE) {
                            $aMsgCds[24] = "The selected payment card is not available";
                        } // Card disabled

                        $pspId = (int)$obj_CardResultSet['PSPID'];

                        if (strlen($obj_ClientConfig->getSalt() ) > 0 && $iSessionType != 2 && empty($obj_DOM->pay[$i]->transaction->{'foreign-exchange-info'}->{'sale-amount'}) === true)
                        {
                            $authToken = trim($obj_DOM->pay[$i]->{'auth-token'});
                            if ($obj_Validator->valHMAC(trim($obj_DOM->{'pay'}[$i]->transaction->hmac), $obj_ClientConfig, $obj_ClientInfo, trim($obj_TxnInfo->getOrderID()), (int)$obj_DOM->{'pay'}[$i]->transaction->card->amount, (int)$obj_DOM->{'pay'}[$i]->transaction->card->amount["country-id"],$obj_TransacionCountryConfig,$authToken) !== 10) { $aMsgCds[210] = "Invalid HMAC:".trim($obj_DOM->{'pay'}[$i]->transaction->hmac); }
                        }  //made hmac mandatory for dcc
                        else if($obj_CardResultSet["DCCENABLED"] === true && empty($obj_DOM->pay[$i]->transaction->{'foreign-exchange-info'}->{'sale-amount'}) === false)
						{
                            $initAmount = (integer)$obj_DOM->pay[$i]->transaction->{'foreign-exchange-info'}->{'sale-amount'};
                            $conversionRate = (string)$obj_DOM->pay[$i]->transaction->{'foreign-exchange-info'}->{'conversion-rate'};
                           	if ($obj_Validator->valDccHMAC(trim($obj_DOM->{'pay'}[$i]->transaction->hmac), $obj_ClientConfig, $obj_ClientInfo, (int)$obj_DOM->{'pay'}[$i]->transaction->card->amount, (int)$obj_DOM->{'pay'}[$i]->transaction->card->amount["country-id"],$obj_TransacionCountryConfig,$obj_TxnInfo,$obj_DOM->pay[$i]->transaction->{'foreign-exchange-info'}->{'id'},$initAmount,$conversionRate) !== 10) { $aMsgCds[210] = "Invalid HMAC:".trim($obj_DOM->{'pay'}[$i]->transaction->hmac); }
						}

						$pendingAmount = $obj_TxnInfo->getPaymentSession()->getPendingAmount();
						$iSaleAmount = 0;

						if($iSessionType > 1 && empty($obj_DOM->pay[$i]->transaction->{'foreign-exchange-info'}->{'sale-amount'}) === true )
						{
							if((integer)$obj_DOM->pay[$i]->transaction->card->amount > $pendingAmount)
							{
								$aMsgCds[53] = "Amount is more than pending amount: ". (integer)$obj_DOM->pay[$i]->transaction->card->amount;
							}
							else{
								$obj_TxnInfo->updateTransactionAmount($_OBJ_DB,(integer)$obj_DOM->pay[$i]->transaction->card->amount);
								$obj_TxnInfo->updateSessionType((integer)$obj_DOM->pay[$i]->transaction->card->amount);
							}
						}
						else
						{
							$iSaleAmount = (integer)$obj_DOM->pay[$i]->transaction->{'foreign-exchange-info'}->{'sale-amount'};
							if($obj_CardResultSet["DCCENABLED"] === true  && empty($obj_DOM->pay[$i]->transaction->{'foreign-exchange-info'}->{'sale-amount'}) === false
							   && intval($obj_DOM->pay[$i]->transaction->card->amount["currency-id"]) !== $obj_TxnInfo->getCurrencyConfig()->getID())
                            {

                                if($iSaleAmount > $pendingAmount && $iSessionType > 1)
								{
									$aMsgCds[53] = "Amount is more than pending amount: ". (integer)$obj_DOM->pay[$i]->transaction->card->amount;
								}
                                else if((int)$obj_DOM->pay[$i]->transaction->{'foreign-exchange-info'}->{'sale-amount'} !== (int)$obj_TxnInfo->getAmount() && $iSessionType <= 1)
                                {
                                	$aMsgCds[$iValResult + 50] = 'Invalid Amount ' . (string)$obj_DOM->pay[$i]->transaction->card->amount;
                                }
                                else
                                {
                                    $obj_TxnInfo->updateSessionType($iSaleAmount);
                                }
                            }
						    else
						    {
								$iValResult = $obj_Validator->valPrice($obj_TxnInfo->getAmount(), (integer)$obj_DOM->pay[$i]->transaction->card->amount);
                                if ($iValResult != 10) {
                                    $aMsgCds[$iValResult + 50] = (string)$obj_DOM->pay[$i]->transaction->card->amount;
                                }
                                elseif($iSessionType > 1)
                                {
                                    $obj_TxnInfo->updateSessionType((integer)$obj_DOM->pay[$i]->transaction->card->amount);
                                }
                            }

						}
					// sso verification conditions checking 
					$obj_mPoint = new MobileWeb($_OBJ_DB, $_OBJ_TXT, $obj_ClientConfig);
					$sosPreference =  $obj_ClientConfig->getAdditionalProperties(Constants::iInternalProperty, "SSO_PREFERENCE");
       				$sosPreference = strtoupper($sosPreference); 

					// Single Sign-On
                    $authenticationURL = $obj_ClientConfig->getAuthenticationURL();
					$authToken = trim($obj_DOM->{'pay'}[$i]->{'auth-token'});
                    $clientId = (integer)$obj_DOM->{'pay'}[$i]["client-id"] ;
                    if (empty($authenticationURL) === false && empty($authToken)=== false)
                    {

                    	$obj_CustomerInfo = new CustomerInfo(0, $obj_DOM->{'pay'}[$i]->{'client-info'}->mobile["country-id"], $obj_DOM->{'pay'}[$i]->{'client-info'}->mobile, (string)$obj_DOM->{'pay'}[$i]->{'client-info'}->email, $obj_DOM->{'pay'}[$i]->{'client-info'}->{'customer-ref'}, "", $obj_DOM->{'pay'}[$i]->{'client-info'}["language"],$obj_DOM->{'pay'}[$i]->{'client-info'}["profileid"]);
                        
                        if ( $sosPreference === 'STRICT' )
                        {
                        	$code = $obj_mPoint->auth($obj_ClientConfig, $obj_CustomerInfo, $authToken, $clientId, $sosPreference);

                        	if ($code == 212) {
                                $aMsgCds[$code] = 'Mandatory fields are missing' ;
                          	} 
                          	if ($code == 1) {
                          		 $aMsgCds[213] = 'Profile authentication failed' ;
                          	}

                        } else {
							
								$code = $obj_mPoint->auth($obj_ClientConfig, $obj_CustomerInfo, $authToken, $clientId);
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
									$iAccountID = EndUserAccount::getAccountID_Static($_OBJ_DB, $obj_ClientConfig, $obj_CountryConfig, trim($obj_DOM->{'pay'}[$i]->{'client-info'}->{'customer-ref'}), (float) $obj_DOM->{'pay'}[$i]->{'client-info'}->mobile, trim($obj_DOM->{'pay'}[$i]->{'client-info'}->email), $obj_DOM->{'pay'}[$i]->{'client-info'}["profileid"]);

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
                                $paymentType = $obj_card->getPaymentType($_OBJ_DB);
								// Success: Payment Service Provider Configuration found
                                if (($obj_paymentProcessor instanceof WalletProcessor || $obj_paymentProcessor instanceof PaymentProcessor ) && ( $paymentType === Constants::iPAYMENT_TYPE_OFFLINE || $paymentType === Constants::iPAYMENT_TYPE_MOBILE_MONEY || $obj_paymentProcessor->getPSPConfig() instanceof PSPConfig === true ))
								{
									try
									{
										$processorType = -1;
										$merchantAccount = "-1";
										$processorType = -1;
										if($obj_paymentProcessor->getPSPConfig() !== NULL)
										{
											$processorType = $obj_paymentProcessor->getPSPConfig()->getProcessorType() ;
											$pspId = $obj_paymentProcessor->getPSPConfig()->getID();
											$merchantAccount = htmlspecialchars($obj_paymentProcessor->getPSPConfig()->getMerchantAccount(), ENT_NOQUOTES);
											$processorType = $obj_paymentProcessor->getPSPConfig()->getProcessorType();
										}
										else
										{
											$processorType = General::getPSPType($_OBJ_DB, $pspId);
										}
										if(empty($obj_DOM->pay[$i]->transaction->{'foreign-exchange-info'}->{'id'}) === FALSE) {
											$obj_TxnInfo->setExternalReference($_OBJ_DB, $pspId, Constants::iForeignExchange, $obj_DOM->pay[$i]->transaction->{'foreign-exchange-info'}->{'id'});
										}
										// TO DO: Extend to add support for Split Tender
										$data['amount'] = (integer) $obj_DOM->pay[$i]->transaction->card[$j]->amount;
										$data['client-config'] = $obj_TxnInfo->getClientConfig();
										if(($data['client-config'] instanceof ClientConfig) === TRUE )
                                        {
                                            $data['markup'] = $data['client-config']->getAccountConfig()->getMarkupLanguage();
                                        }
                                        $data['producttype'] = $obj_TxnInfo->getProductType();
										$data['installment-value'] = (integer) $obj_DOM->pay[$i]->transaction->installment->value;
										if($obj_paymentProcessor->getPSPConfig() !== NULL && (int) $obj_paymentProcessor->getPSPConfig()->getProcessorType() === Constants::iPROCESSOR_TYPE_WALLET) {
											$data['wallet-id'] = $obj_paymentProcessor->getPSPConfig()->getID();
										}
										if(empty($data['auto-capture']) === true) { $data['auto-capture'] = (int)$obj_CardResultSet['CAPTURE_TYPE']; }
										if(empty($obj_DOM->pay[$i]->transaction->{'foreign-exchange-info'}->{'conversion-rate'}) === FALSE)
										{
											$obj_CurrencyConfig = CurrencyConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->pay[$i]->transaction->card[$j]->amount["currency-id"]);
											$data['externalref'] = array(Constants::iForeignExchange =>array((integer)$obj_TxnInfo->getClientConfig()->getID() => (string)$obj_DOM->pay[$i]->transaction->{'foreign-exchange-info'}->{'id'} ));
											$data['converted-currency-config'] = $obj_CurrencyConfig;
											$data['converted-amount'] = (integer) $obj_DOM->pay[$i]->transaction->card[$j]->amount;
											$data['conversion-rate'] = $obj_DOM->pay[$i]->transaction->{'foreign-exchange-info'}->{'conversion-rate'};
											$data['amount'] = $iSaleAmount;
										}
										//For Offline payment method fee is considered as holding charges required to add in actual amount
										if($obj_CardResultSet['PAYMENTTYPE'] == Constants::iPAYMENT_TYPE_OFFLINE && $obj_TxnInfo->getFee() > 0 && (((integer)$obj_DOM->pay[$i]->transaction->card->amount)+ $obj_TxnInfo->getFee()) === (integer)($obj_TxnInfo->getAmount() + $obj_TxnInfo->getFee()))
										{
											$data['converted-amount'] = $obj_TxnInfo->getAmount() + $obj_TxnInfo->getFee();
										}


										$oTI = TxnInfo::produceInfo($obj_TxnInfo->getID(),$_OBJ_DB, $obj_TxnInfo, $data);

										$obj_mPoint->logTransaction($oTI);
										//getting order config with transaction to pass to particular psp for initialize with psp for AID
										$oTI->produceOrderConfig($_OBJ_DB);

										//For APM and Gateway only we have to trigger authorize requested so that passbook will get updated with authorize requested and performed opt entry
										if( $processorType === Constants::iPAYMENT_TYPE_OFFLINE || $processorType === Constants::iPAYMENT_TYPE_MOBILE_MONEY || $processorType === Constants::iPROCESSOR_TYPE_APM || $processorType === Constants::iPROCESSOR_TYPE_GATEWAY)
										{
											$txnPassbookObj = TxnPassbook::Get($_OBJ_DB, $obj_TxnInfo->getID(), $obj_TxnInfo->getClientConfig()->getID());
											$passbookEntry = new PassbookEntry
											(
													NULL,
												$oTI->getAmount(),
												$oTI->getCurrencyConfig()->getID(),
													Constants::iAuthorizeRequested,
												'',
												0,
												'',
												'',
												TRUE,
												NULL,
												NULL,
												$oTI->getClientConfig()->getID(),
												$oTI->getInitializedAmount()
											);
											if ($txnPassbookObj instanceof TxnPassbook)
											{
												$txnPassbookObj->addEntry($passbookEntry);
												$txnPassbookObj->performPendingOperations();
											}
										}

										// Initialize payment with Payment Service Provider
										$xml = '<psp-info id="'. $pspId .'" merchant-account="'. $merchantAccount .'"  type="'. $processorType .'">';

										switch ($pspId) {
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
												$obj_XML = $obj_PSP->initialize($obj_ConnInfo,
                                                                                $obj_paymentProcessor->getPSPConfig()->getMerchantAccount(),
                                                                                (string)$obj_CardResultSet['CURRENCY'],
                                                                                $cardName);
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

												$obj_XML = $obj_PSP->initialize($obj_paymentProcessor->getPSPConfig(), $obj_TxnInfo->getAccountID(), FALSE, $cardName);

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
												$authToken = (trim($obj_DOM->{'pay'}[$i]->{'auth-token'}))?(trim($obj_DOM->{'pay'}[$i]->{'auth-token'})):(NULL);
												$obj_XML = $obj_paymentProcessor->initialize($obj_DOM->pay[$i]->transaction->card["type-id"], $token, $billingAddress, $obj_ClientInfo, General::xml2bool($obj_DOM->pay[$i]->transaction['store-card']), $authToken, $cardName);
												if (General::xml2bool($obj_DOM->pay[$i]->transaction["store-card"]) === TRUE) {
													$obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iTICKET_CREATED_STATE, "");
												}
												foreach ($obj_XML->children() as $obj_Elem) {
													$xml .= trim($obj_Elem->asXML());
												}

										}
										$message = '';
										if($obj_paymentProcessor->getPSPConfig() !== NULL)
										{
											$message = htmlspecialchars($obj_paymentProcessor->getPSPConfig()->getMessage($obj_TxnInfo->getLanguage() ), ENT_NOQUOTES);
										}
										$xml .= '<message language="'. htmlspecialchars($obj_TxnInfo->getLanguage(), ENT_NOQUOTES) .'">'. $message  .'</message>';
										$xml .= '</psp-info>';

										if( $oTI->hasEitherState($_OBJ_DB, Constants::iPAYMENT_PENDING_STATE) === true)
                                        {
                                            $xml .= '<status code="'.Constants::iPAYMENT_PENDING_STATE.'">Payment Pending</status>';
                                        }
										else if($oTI->hasEitherState($_OBJ_DB,Constants::iPAYMENT_INIT_WITH_PSP_STATE) === true)
                                        {
                                            $xml .= '<status code="'.Constants::iPAYMENT_INIT_WITH_PSP_STATE.'">Payment Initialize with PSP</status>';
                                        }
									}
                                    catch (PaymentProcessorInitializeException $e)
                                    {
                                        $xml = '<status code="' . $e->getCode() . '" sub-code="' . $e->getSubcode() . '">' . $e->getMessage() . '</status>';
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
                    }else if($isVoucherRedeem === FALSE && $validRequest==true)
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
					header("HTTP/1.1 401 Unauthorized");

					$xml = '<status code="401">Username / Password doesn\'t match</status>';
				}
			}
			else
			{
				header("HTTP/1.1 400 Bad Request");

				foreach ($aMsgCds as $code => $data)
				{
					$xml .= '<status code="'. $code .'">'. htmlspecialchars($data, ENT_NOQUOTES) .'</status>';
				}
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