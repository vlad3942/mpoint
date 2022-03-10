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
use api\classes\merchantservices\configuration\AddonServiceType;
use api\classes\merchantservices\Repositories\ReadOnlyConfigRepository;
use api\classes\splitpayment\config\Configuration;

require_once("../../inc/include.php");

// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require Business logic for the Mobile Web module
require_once(sCLASS_PATH ."/mobile_web.php");
// Require data data class for Customer Information
require_once(sCLASS_PATH ."/customer_info.php");
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
// Require specific Business logic for the DSB PSP component
require_once(sCLASS_PATH ."/dsb.php");
// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
// Require Data Class for Client Information
require_once(sCLASS_PATH ."/clientinfo.php");
// Require Data Class for Client Account Information
require_once(sCLASS_PATH ."/account_config.php");
// Require Data class for Payment processor
require_once(sCLASS_PATH ."/payment_processor.php");
// Require Data class for Wallet processor
require_once(sCLASS_PATH ."/wallet_processor.php");
// Require specific Business logic for the mVault component
require_once(sCLASS_PATH ."/mvault.php");
require_once sCLASS_PATH . '/txn_passbook.php';
require_once sCLASS_PATH . '/passbookentry.php';
require_once(sCLASS_PATH ."/core/card.php");
require_once(sCLASS_PATH ."/card_prefix_config.php");
require_once sCLASS_PATH . '/routing_service.php';
require_once sCLASS_PATH . '/routing_service_response.php';
require_once sCLASS_PATH . '/FailedPaymentMethodConfig.php';
require_once sCLASS_PATH . '/crs/payment_method.php';
require_once(sCLASS_PATH . '/apm/CebuPaymentCenter.php');
require_once(sCLASS_PATH . '/payment_route.php');
$aMsgCds = array();

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );
/*
$_SERVER['PHP_AUTH_USER'] = "1415";
$_SERVER['PHP_AUTH_PW'] = "Ghdy4_ah1G";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<initialize-payment client-id="10019" account="100026">';
$HTTP_RAW_POST_DATA .= '<transaction order-no="904-70158922">';
$HTTP_RAW_POST_DATA .= '<amount country-id="100">2400</amount>';
$HTTP_RAW_POST_DATA .= '<callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url>';
$HTTP_RAW_POST_DATA .= '<hmac>0489be0b8439cc6543787bd722f8d8352e23fc7e</hmac>';
$HTTP_RAW_POST_DATA .= '</transaction>';
$HTTP_RAW_POST_DATA .= '<client-info platform="iOS" version="5.1.1" language="da" profileid="123456">';
$HTTP_RAW_POST_DATA .= '<mobile country-id="100" operator-id="10000">28882861</mobile>';
$HTTP_RAW_POST_DATA .= '<email>jona@oismail.com</email>';
$HTTP_RAW_POST_DATA .= '<device-id>4615F4E94A9749D7B7BB9654EAC00ED314212383</device-id>';
$HTTP_RAW_POST_DATA .= '</client-info>';
$HTTP_RAW_POST_DATA .= '</initialize-payment>';
$HTTP_RAW_POST_DATA .= '</root>';
*/
global $aHTTP_CONN_INFO;
$obj_DOM = simpledom_load_string(file_get_contents('php://input'));

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mpoint.xsd") === true && count($obj_DOM->{'initialize-payment'}) > 0)
	{
		$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);

		for ($i=0; $i<count($obj_DOM->{'initialize-payment'}); $i++)
		{
		    $obj_ClientInfo = null;
			// Set Global Defaults
			if (empty($obj_DOM->{'initialize-payment'}[$i]["account"]) === true || intval($obj_DOM->{'initialize-payment'}[$i]["account"]) < 1) { $obj_DOM->{'initialize-payment'}[$i]["account"] = -1; }

			// Validate basic information
			$code = Validate::valBasic($_OBJ_DB, (integer) $obj_DOM->{'initialize-payment'}[$i]["client-id"], (integer) $obj_DOM->{'initialize-payment'}[$i]["account"]);
			if ($code == 100)
			{
				$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'initialize-payment'}[$i]["client-id"], (integer) $obj_DOM->{'initialize-payment'}[$i]["account"]);
				$obj_ClientAccountsConfig = AccountConfig::produceConfigurations($_OBJ_DB, $obj_ClientConfig->getID());
				if ($obj_ClientConfig->getUsername() == trim($_SERVER['PHP_AUTH_USER']) && $obj_ClientConfig->getPassword() == trim($_SERVER['PHP_AUTH_PW'])
					&& $obj_ClientConfig->hasAccess($_SERVER['REMOTE_ADDR']) === true)
				{
				    $httpXForwardedForIps = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                    $httpXForwardedForIps = array_map('trim', $httpXForwardedForIps);
                    $httpXForwardedForIp = $httpXForwardedForIps[0];
					$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'initialize-payment'}[$i]->transaction->amount["country-id"]);
					if ( ($obj_CountryConfig instanceof CountryConfig) === false || $obj_CountryConfig->getID() < 1) { $obj_CountryConfig = $obj_ClientConfig->getCountryConfig(); }

					$obj_Validator = new Validate($obj_ClientConfig->getCountryConfig() );
					$iValResult = $obj_Validator->valPrice($obj_ClientConfig->getMaxAmount(), (integer) $obj_DOM->{'initialize-payment'}[$i]->transaction->amount);
					if ($obj_ClientConfig->getMaxAmount() > 0 && $iValResult != 10) { $aMsgCds[$iValResult + 50] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->amount; }

                    $isSplitAbandoned = false;
                    $initAmount = (int) $obj_DOM->{'initialize-payment'}[$i]->transaction->amount;

                    $sessionId = (int)$obj_DOM->{'initialize-payment'}[$i]->transaction["session-id"];
                    $sessionType =  $obj_ClientConfig->getAdditionalProperties(Constants::iInternalProperty,"sessiontype");
                    if($sessionId > 1 )
                    {
                        $obj_PaymentSession = PaymentSession::Get($_OBJ_DB,$sessionId);
                        if($obj_PaymentSession->getSessionType() > 1 && (int)$obj_PaymentSession->getAmount() === $initAmount)
                        {
                            $isSplitAbandoned = true;
                            $obj_PaymentSession->updateSessionTypeId($initAmount,$obj_PaymentSession->getAmount(),$sessionId);
                        }
                    }
					// Hash based Message Authentication Code (HMAC) enabled for client and payment transaction is not an attempt to simply save a card
					if (strlen($obj_ClientConfig->getSalt() ) > 0 && ($isSplitAbandoned === true || $sessionId == 0))
					{
						$obj_ClientInfo = ClientInfo::produceInfo($obj_DOM->{'initialize-payment'}[$i]->{'client-info'},
																  CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->mobile["country-id"]),
																  $httpXForwardedForIp);
                        $authToken = trim($obj_DOM->{'initialize-payment'}[$i]->{'auth-token'});
						if ($obj_Validator->valHMAC(trim($obj_DOM->{'initialize-payment'}[$i]->transaction->hmac), $obj_ClientConfig, $obj_ClientInfo, trim($obj_DOM->{'initialize-payment'}[$i]->transaction['order-no']), intval($obj_DOM->{'initialize-payment'}[$i]->transaction->amount), intval($obj_DOM->{'initialize-payment'}[$i]->transaction->amount["country-id"]),$obj_CountryConfig, $authToken ) != 10) { $aMsgCds[210] = "Invalid HMAC:".trim($obj_DOM->{'initialize-payment'}[$i]->transaction->hmac); }
					}

					// Validate currency if explicitly passed in request, which defer from default currency of the country
					if(intval($obj_DOM->{'initialize-payment'}[$i]->transaction->amount["currency-id"]) > 0){
					$obj_TransacionCountryConfig = CountryConfig::produceConfig($_OBJ_DB, intval($obj_DOM->{'initialize-payment'}[$i]->transaction->amount["country-id"])) ;
					if($obj_Validator->valCurrency($_OBJ_DB, intval($obj_DOM->{'initialize-payment'}[$i]->transaction->amount["currency-id"]) ,$obj_TransacionCountryConfig, intval( $obj_DOM->{'initialize-payment'}[$i]["client-id"])) != 10 ){
						$aMsgCds[56] = "Invalid Currency:".intval($obj_DOM->{'initialize-payment'}[$i]->transaction->amount["currency-id"]) ;
					  }
					}
					
					// sso verification conditions checking 
					$obj_mPoint = new MobileWeb($_OBJ_DB, $_OBJ_TXT, $obj_ClientConfig);
					$sosPreference =  $obj_ClientConfig->getAdditionalProperties(Constants::iInternalProperty, "SSO_PREFERENCE");
       				$sosPreference = strtoupper($sosPreference); 

					// Single Sign-On
                    $bIsSingleSingOnPass = false;
                    $authenticationURL = $obj_ClientConfig->getAuthenticationURL();
					$authToken = trim($obj_DOM->{'initialize-payment'}[$i]->{'auth-token'});
                    $profileTypeId = null;
                    $userType = null;
                    $clientId = (integer)$obj_DOM->{'initialize-payment'}[$i]["client-id"] ; 
                    if (empty($authenticationURL) === false && empty($authToken)=== false)
                    {

                    	$obj_CustomerInfo = new CustomerInfo(0, $obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->mobile["country-id"], $obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->mobile, (string)$obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->email, $obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->{'customer-ref'}, "", $obj_DOM->{'initialize-payment'}[$i]->{'client-info'}["language"],$obj_DOM->{'initialize-payment'}[$i]->{'client-info'}["profileid"]);
                        
                        if ( $sosPreference === 'STRICT' )
                        {
                        	$code = $obj_mPoint->auth($obj_ClientConfig, $obj_CustomerInfo, $authToken, $clientId, $sosPreference);

                        	if ($code == 212) {
                                $aMsgCds[212] = 'Mandatory fields are missing' ;
                          	} 
                          	if ($code == 1) {
                          		 $aMsgCds[213] = 'Profile authentication failed' ;
                          	}

                        } else {
							
								$code = $obj_mPoint->auth($obj_ClientConfig, $obj_CustomerInfo, $authToken, $clientId);
						}

                        if ($code == 10) {
                            $bIsSingleSingOnPass = true;
                            $profileTypeId = $obj_CustomerInfo->getProfileTypeID();
                            $userType = $obj_CustomerInfo->getUserType();
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

                    // Validate service type id if explicitly passed in request
                    $fxServiceTypeId = (integer)$obj_DOM->{'initialize-payment'}[$i]->transaction->{'foreign-exchange-info'}->{'service-type-id'};
                    if($fxServiceTypeId > 0){
                        if($obj_Validator->valFXServiceType($_OBJ_DB,$fxServiceTypeId) !== 10 ){
                            $aMsgCds[57] = "Invalid service type id :".$fxServiceTypeId ;
                        }
                    }



					// Success: Input Valid
					if (count($aMsgCds) == 0)
					{
                        $obj_PaymentMethodResponse = null;
						$iTxnID = $obj_mPoint->newTransaction($obj_ClientConfig,Constants::iPURCHASE_VIA_APP);
						try
						{
							// Update Transaction State
							$obj_mPoint->newMessage($iTxnID, Constants::iINPUT_VALID_STATE, $obj_DOM->asXML() );
                            $aTransactionTypes = array(Constants::iTRANSACTION_TYPE_SHOPPING_ONLINE, Constants::iTRANSACTION_TYPE_SHOPPING_OFFLINE, Constants::iTRANSACTION_TYPE_SELF_SERVICE_ONLINE, Constants::iTRANSACTION_TYPE_SELF_SERVICE_OFFLINE,Constants::iTRANSACTION_TYPE_SELF_SERVICE_ONLINE_WITH_ADDITIONAL_RULES_ON_FOP,Constants::iTRANSACTION_TYPE_PAYMENT_LINK_TRANSACTION);

                            $iTransactionTypeId = (integer)$obj_DOM->{'initialize-payment'}[$i]->transaction["type-id"];
                            if(in_array($iTransactionTypeId, $aTransactionTypes)){
                                $data['typeid'] = (integer)$obj_DOM->{'initialize-payment'}[$i]->transaction["type-id"];
                            }else{
                                $data['typeid'] = Constants::iTRANSACTION_TYPE_SHOPPING_ONLINE;
                            }
                            if ($fxServiceTypeId)
                            {
                                $data['fxservicetypeid'] = $fxServiceTypeId;
                            }

							$data['amount'] = (float) $obj_DOM->{'initialize-payment'}[$i]->transaction->amount;
							$data['converted-amount'] = (float) $obj_DOM->{'initialize-payment'}[$i]->transaction->amount;
							$data['country-config'] = $obj_CountryConfig;
							if (count($obj_DOM->{'initialize-payment'}[$i]->transaction->points) == 1)
							{
								$data['points'] = (integer) $obj_DOM->{'initialize-payment'}[$i]->transaction->points;
							}
							if (count($obj_DOM->{'initialize-payment'}[$i]->transaction->reward) == 1)
							{
								$data['reward'] = (integer) $obj_DOM->{'initialize-payment'}[$i]->transaction->reward;
								$data['reward-type'] = (integer) $obj_DOM->{'initialize-payment'}[$i]->transaction->reward["type-id"];
							}
                            if ($obj_DOM->{'initialize-payment'}[$i]->transaction->fees->fee)
                            {
                                $data['fee'] = (integer) $obj_DOM->{'initialize-payment'}[$i]->transaction->fees->fee[0];
                            }
                            $data['description'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->description;
							$data['gomobileid'] = -1;
							$data['orderid'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction["order-no"];

							// Adding/Updating the client data in to the enduser account.
                            if (empty($obj_DOM->{'initialize-payment'}[$i]->{'client-info'}["profileid"]) === false) {
                              $data['profileid'] = (string) $obj_DOM->{'initialize-payment'}[$i]->{'client-info'}["profileid"];
                            }

							$data['customer-ref'] = (string) $obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->{'customer-ref'};
							$data['mobile'] = (float) $obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->mobile;
							$data['operator'] = (integer) $obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->mobile["operator-id"];
							if ($data['operator'] === 0) { $data['operator'] = (int)$obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->mobile["country-id"] * 100; }
							$data['email'] = (string) $obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->email;
							$data['device-id'] = (string) $obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->{'device-id'};

                            if (count($obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->ip) === 1) {
                                $data['ip'] = (string)$obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->ip;
                            } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) === TRUE) {
                                $data['ip'] = $httpXForwardedForIp;
                            } else {
                                $data['ip'] = $_SERVER['REMOTE_ADDR'];
                            }

							$data['logo-url'] = $obj_ClientConfig->getLogoURL();
							$data['css-url'] = $obj_ClientConfig->getCSSURL();
							/*
							 *  Added capability to accept the transaction specific accept URL.
							 *  Used by Master Pass wallet for sending a callback along with the checkout URL for getting user's card details
							 * */
							if (count($obj_DOM->{'initialize-payment'}[$i]->transaction->{'accept-url'}) == 1)
							{
								$data['accept-url'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->{'accept-url'};
							}
							else { $data['accept-url'] = $obj_ClientConfig->getAcceptURL(); }

							if (count($obj_DOM->{'initialize-payment'}[$i]->transaction->{'decline-url'}) == 1)
                            {
                                $data['decline-url'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->{'decline-url'};
                            }
                            else { $data['decline-url'] = $obj_ClientConfig->getDeclineURL(); }

							if (count($obj_DOM->{'initialize-payment'}[$i]->transaction->{'cancel-url'}) == 1)
                            {
                                $data['cancel-url'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->{'cancel-url'};
                            }
                            else { $data['cancel-url'] = $obj_ClientConfig->getCancelURL(); }
							
							if (count($obj_DOM->{'initialize-payment'}[$i]->transaction->{'callback-url'}) == 1)
							{
								$data['callback-url'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->{'callback-url'};
							}
							else { $data['callback-url'] = $obj_ClientConfig->getCallbackURL(); }
							if (count($obj_DOM->{'initialize-payment'}[$i]->transaction->{'auth-url'}) == 1)
							{
								$data['auth-url'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->{'auth-url'};
							}
							else { $data['auth-url'] = $obj_ClientConfig->getAuthenticationURL(); }
							$data['icon-url'] = "";
							$data['language'] = (string) $obj_DOM->{'initialize-payment'}[$i]->{'client-info'}["language"];
							$data['markup'] = $obj_ClientConfig->getAccountConfig()->getMarkupLanguage();
							
							$obj_CurrencyConfig = CurrencyConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'initialize-payment'}[$i]->transaction->amount["currency-id"]);
							$data['currency-config']= $obj_CurrencyConfig ;
							$data['converted-currency-config']= $obj_CurrencyConfig ;
							$data['conversion-rate']= 1 ;

							$parentTxnId = NULL;
							if(isset($obj_DOM->{'initialize-payment'}[$i]->transaction->{'additional-data'}))
                            {
                                $parentTxnId = (int)$obj_DOM->xpath('initialize-payment/transaction/additional-data/param[@name="parent-transacation-id"]');
                            }

                             //Set attempt value based on the previous attempts using the same orderid
                            $iAttemptNumber = $obj_mPoint->getTxnAttemptsFromOrderID($obj_ClientConfig, $obj_CountryConfig, $data['orderid'], $parentTxnId);
                            $data['attempt'] = $iAttemptNumber = $iAttemptNumber+1;

                            $data['sessionid'] = $sessionId;
                            if($sessionType > 1 )
                                $data['sessiontype']=$sessionType;
                            //var_dump($data['attempt']);die;
                            if (isset($obj_DOM->{'initialize-payment'}[$i]->transaction["product-type"]) == true) {
                                $data['producttype'] = (string)$obj_DOM->{'initialize-payment'}[$i]->transaction["product-type"];
                            }

                            if($userType){
                                $data['additionaldata']['customer-type'] = $userType;
                            }

                            $obj_TxnInfo = TxnInfo::produceInfo($iTxnID,$_OBJ_DB, $obj_ClientConfig, $data);
                            $repository = new ReadOnlyConfigRepository($_OBJ_DB,$obj_TxnInfo);
                            if($isSplitAbandoned === true)
                            {
                                $splitPaymentAddOn = $repository->getAddonConfiguration(AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::eSPLIT_PAYMENT),array(),true);
                                $isManualRefund = !$splitPaymentAddOn->getProperties()["is_rollback"];
                                $obj_general = new General($_OBJ_DB, $_OBJ_TXT);
                                $obj_general->changeSplitSessionStatus($obj_ClientConfig->getID(), $obj_PaymentSession->getId(), 'Failed', $isManualRefund);
                            }

                            $txnPassbookObj = TxnPassbook::Get($_OBJ_DB, $iTxnID, $obj_ClientConfig->getID());
                            $passbookEntry = new PassbookEntry
                            (
                                NULL,
                                $obj_TxnInfo->getAmount(),
                                $obj_TxnInfo->getCurrencyConfig()->getID(),
                                Constants::iInitializeRequested
                            );
                            if($txnPassbookObj instanceof TxnPassbook) {
                                $txnPassbookObj->addEntry($passbookEntry);
                                $txnPassbookObj->performPendingOperations();
                            }
                            $is_legacy = $obj_TxnInfo->getClientConfig()->getClientServices()->isLegacyFlow();
                            $maxSessionRetryCount = (int)$obj_TxnInfo->getClientConfig()->getAdditionalProperties (Constants::iInternalProperty, 'max_session_retry_count');
                            if($maxSessionRetryCount <= 0)
                            {
                                $maxSessionRetryCount = 3;
                            }
                            if($obj_TxnInfo->getPaymentSession()->getPendingAmount() == 0)
                            {
                                $xml = '<status code="'. Constants::iSESSION_ALREADY_COMPLETED .'">Payment session is already completed</status>';
                                $obj_mPoint->newMessage($iTxnID, Constants::iSESSION_ALREADY_COMPLETED, "Payment session is already completed, Session id - ". $obj_TxnInfo->getSessionId());
                            }
                            elseif($obj_TxnInfo->getPaymentSession()->getPendingAmount() < $obj_TxnInfo->getAmount())
                            {
                                $xml = '<status code="53">Amount is more than pending amount:'. $obj_TxnInfo->getAmount() .'</status>';
                            }
                            elseif ($is_legacy === false && $obj_mPoint->getTxnAttemptsFromSessionID($data['sessionid']) >= $maxSessionRetryCount)
                            {
                                $xml = '<status code="'.Constants::iSESSION_FAILED_MAXIMUM_ATTEMPTS.'">Payment failed: You have exceeded the maximum number of attempts</status>';
                                $obj_mPoint->newMessage($iTxnID, Constants::iSESSION_FAILED_MAXIMUM_ATTEMPTS, "You have exceeded the maximum number of attempts, Session id - ". $obj_TxnInfo->getSessionId());
                                $obj_TxnInfo->getPaymentSession()->updateState(Constants::iSESSION_FAILED_MAXIMUM_ATTEMPTS);
                            }
                            else {
                                // Associate End-User Account (if exists) with Transaction
                                $obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer)$obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->mobile["country-id"]);

                                if ($obj_ClientConfig->getAdditionalProperties(Constants::iInternalProperty, "ENABLE_PROFILE_ANONYMIZATION") == "true" && $obj_TxnInfo->getProfileID() !== '') {
                                    $obj_TxnInfo->setAccountID(EndUserAccount::getAccountID_Static($_OBJ_DB, $obj_ClientConfig, $obj_CountryConfig, $obj_TxnInfo->getCustomerRef(), $obj_TxnInfo->getMobile(), $obj_TxnInfo->getEMail(), $obj_TxnInfo->getProfileID()));
                                } else {
                                    $obj_TxnInfo->setAccountID(EndUserAccount::getAccountID_Static($_OBJ_DB, $obj_ClientConfig, $obj_CountryConfig, $obj_TxnInfo->getCustomerRef(), $obj_TxnInfo->getMobile(), $obj_TxnInfo->getEMail()));
                                }
                                // Update Transaction Log
							$obj_mPoint->logTransaction($obj_TxnInfo);

                            $sOrderXML = '';

                            $additionalTxnData = [];
                            $additionalTxnDataIndex = -1;
                            if(isset($obj_DOM->{'initialize-payment'}[$i]->transaction['booking-ref']))
                            {
                                $additionalTxnDataIndex++;
                                $additionalTxnData[$additionalTxnDataIndex]['name'] = "booking-ref";
                                $additionalTxnData[$additionalTxnDataIndex]['value'] = (string)$obj_DOM->{'initialize-payment'}[$i]->transaction['booking-ref'];
                                $additionalTxnData[$additionalTxnDataIndex]['type'] = (string) 'Transaction';
							}
							if(isset($obj_DOM->{'initialize-payment'}[$i]->{'client-info'}["locale"]))
                            {
                                $additionalTxnDataIndex++;
                                $additionalTxnData[$additionalTxnDataIndex]['name'] = "locale";
                                $additionalTxnData[$additionalTxnDataIndex]['value'] = (string)$obj_DOM->{'initialize-payment'}[$i]->{'client-info'}["locale"];
                                $additionalTxnData[$additionalTxnDataIndex]['type'] = (string) 'Transaction';
                            }
                            if (isset($obj_DOM->{'initialize-payment'}[$i]->transaction->hmac)) {
                                $additionalTxnDataIndex++;
                                $additionalTxnData[$additionalTxnDataIndex]['name'] = "hmac";
                                $additionalTxnData[$additionalTxnDataIndex]['value'] = (string)$obj_DOM->{'initialize-payment'}[$i]->transaction->hmac;
                                $additionalTxnData[$additionalTxnDataIndex]['type'] = (string) 'Transaction';
                            }
							$aSessionAdditionalData = [];
                            if(isset($obj_DOM->{'initialize-payment'}[$i]->transaction->{'additional-data'}))
                            {
                                for ($index = 0; $index < count($obj_DOM->{'initialize-payment'}[$i]->transaction->{'additional-data'}->children()); $index++)
                                {
                                    $aSessionAdditionalData[$index]['name'] = (string)$obj_DOM->{'initialize-payment'}[$i]->transaction->{'additional-data'}->param[$index]['name'];
                                    $aSessionAdditionalData[$index]['value'] = (string)$obj_DOM->{'initialize-payment'}[$i]->transaction->{'additional-data'}->param[$index];
                                    $aSessionAdditionalData[$index]['type'] = (string)'Session';
                                }
                            }

                            if($additionalTxnDataIndex > -1)
                            {
                                $obj_TxnInfo->setAdditionalDetails($_OBJ_DB,$additionalTxnData,$obj_TxnInfo->getID());
                            }
                            if (empty($aSessionAdditionalData) === FALSE) {
                                $obj_TxnInfo->getPaymentSession()->setSessionAdditionalDetails($_OBJ_DB, $aSessionAdditionalData, $obj_TxnInfo->getSessionId());
                            }

                                //Test if the order/cart details are passed as part of the input XML request.
							if(count( $obj_DOM->{'initialize-payment'}[$i]->transaction->orders) == 1 && count( $obj_DOM->{'initialize-payment'}[$i]->transaction->orders->children()) > 0 )
							{
                                $sOrderXML = $obj_DOM->{'initialize-payment'}[$i]->transaction->orders->asXML();
                                $obj_DOMOrder = $obj_DOM->{'initialize-payment'}[$i]->transaction->orders;
                                $obj_mPoint->saveOrderDetails($_OBJ_DB, $obj_TxnInfo, $obj_CountryConfig, $obj_DOMOrder, null);
							}
							elseif((is_object($obj_DOM->{'initialize-payment'}[$i]->transaction->orders) && count( $obj_DOM->{'initialize-payment'}[$i]->transaction->orders) == 0) && $iAttemptNumber > 1 )
                            {
                                $aObj_OrderInfoConfigs = OrderInfo::produceConfigurationsFromOrderID($_OBJ_DB, $obj_TxnInfo);
                                if (count($aObj_OrderInfoConfigs) > 0)
                                {
                                    $sOrderXML .= '<orders>';
                                    foreach ($aObj_OrderInfoConfigs as $obj_OrderInfo)
                                    {
                                        $sOrderXML .= $obj_OrderInfo->toXML();
                                    }
                                    $sOrderXML .= '</orders>';
                                }
                            }

							if (count($obj_DOM->{'initialize-payment'}[$i]->transaction->{'custom-variables'}) == 1 && count($obj_DOM->{'initialize-payment'}[$i]->transaction->{'custom-variables'}->children() ) > 0)
							{
								$aVars = array();
								foreach ($obj_DOM->{'initialize-payment'}[$i]->transaction->{'custom-variables'}->children() as $obj_Var)
								{
									if (substr($obj_Var->getName(), 0, 4) == "var_")
									{
										$aVars[$obj_Var->getName()] = (string) $obj_Var;
									}
									else { $aVars["var_". $obj_Var->getName()] = (string) $obj_Var; }
								}
								// Log additional data
								$obj_mPoint->logClientVars($aVars);
							}


							$obj_mPoint = new CreditCard($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo);
							$obj_XML = simplexml_load_string($obj_TxnInfo->toXML(), "SimpleXMLElement", LIBXML_COMPACT);
                            $aFailedPMArray = array();
						/*	if($iAttemptNumber > 1)
                            {
                                $aFailedPMArray = $obj_mPoint->getPreviousFailedAttempts($obj_TxnInfo->getOrderID(), (integer) $obj_DOM->{'initialize-payment'}[$i]["client-id"]);
                            }
						*/	$xml = '<client-config id="'. $obj_ClientConfig->getID() .'" account="'. $obj_ClientConfig->getAccountConfig()->getID() .'" store-card="'. $obj_ClientConfig->getStoreCard() .'" max-stored-cards="'. $obj_ClientConfig->getMaxCards() .'" auto-capture="'. General::bool2xml($obj_ClientConfig->useAutoCapture() ) .'" enable-cvv="'. General::bool2xml($obj_ClientConfig->getCVVenabled() ) .'" mode="'. $obj_ClientConfig->getMode() .'">';
                            if($obj_ClientConfig->getInstallment()>0)
                            {
                                $xml .= '<installment type="' . htmlspecialchars($obj_ClientConfig->getInstallment(), ENT_NOQUOTES) . '" max-installments="' . htmlspecialchars($obj_ClientConfig->getMaxInstallments(), ENT_NOQUOTES) . '" frequency="' . htmlspecialchars($obj_ClientConfig->getInstallmentFrequency(), ENT_NOQUOTES) . '" />';
                            }
							$xml .= '<name>'. htmlspecialchars($obj_ClientConfig->getName(), ENT_NOQUOTES) .'</name>';
							$xml .= '<callback-url>'. htmlspecialchars($obj_ClientConfig->getCallbackURL(), ENT_NOQUOTES) .'</callback-url>';
							$xml .= '<accept-url>'. htmlspecialchars($obj_ClientConfig->getAcceptURL(), ENT_NOQUOTES) .'</accept-url>';
							$xml .= '<cancel-url>'. htmlspecialchars($obj_ClientConfig->getCancelURL(), ENT_NOQUOTES) .'</cancel-url>';
							$xml .= '<app-url>'. htmlspecialchars($obj_ClientConfig->getAppURL(), ENT_NOQUOTES) .'</app-url>';
							$xml .= '<css-url>'. htmlspecialchars($obj_ClientConfig->getCSSURL(), ENT_NOQUOTES) .'</css-url>';
                            $xml .= '<logo-url>'. htmlspecialchars($obj_ClientConfig->getLogoURL(), ENT_NOQUOTES) .'</logo-url>';
                            $xml .= '<base-image-url>'. htmlspecialchars($obj_ClientConfig->getBaseImageURL(), ENT_NOQUOTES) .'</base-image-url>';
                            $xml .= '<additional-config>';
                            foreach ($obj_ClientConfig->getAdditionalProperties(Constants::iPublicProperty) as $aAdditionalProperty)
                            {
                                $xml .= '<property name="'.$aAdditionalProperty['key'].'">'.$aAdditionalProperty['value'].'</property>';
                            }
                            $xml .= '</additional-config>';
							$xml .= '<accounts>';
                            foreach ($obj_ClientAccountsConfig as $obj_AccountConfig)
                            {
                                $xml .= '<account id= "'. $obj_AccountConfig->getID() .'" markup= "'. $obj_AccountConfig->getMarkupLanguage() .'" />';
                            }
                            $xml .= '</accounts>';
							$xml .= '</client-config>';
							$euaId = -1;
							if ($bIsSingleSingOnPass === true || (empty($authenticationURL) === true && empty($authToken)=== true))
                            {
                                $euaId = $obj_TxnInfo->getAccountID();
                            }
                            // Call routing service to get eligible payment methods if the client is configured to use it.
                            $obj_PaymentMethods = null;
                            $obj_FailedPaymentMethod = null;
                            if ($is_legacy === false)
                            {
                                if (empty($sessionId) === false)
                                {
                                    $obj_FailedPaymentMethod = FailedPaymentMethodConfig::produceFailedTxnInfoFromSession($_OBJ_DB, $sessionId, $obj_DOM->{'initialize-payment'}[$i]["client-id"]);
                                }
                                $obj_TxnInfo->produceOrderConfig($_OBJ_DB);
                                $obj_ClientInfo = ClientInfo::produceInfo($obj_DOM->{'initialize-payment'}[$i]->{'client-info'}, CountryConfig::produceConfig($_OBJ_DB, (integer)$obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->mobile["country-id"]), $_SERVER['HTTP_X_FORWARDED_FOR'], $profileTypeId);
                                $obj_RS = new RoutingService($obj_TxnInfo, $obj_ClientInfo, $aHTTP_CONN_INFO['routing-service'], $obj_DOM->{'initialize-payment'}[$i]["client-id"], $obj_DOM->{'initialize-payment'}[$i]->transaction->amount["country-id"], $obj_DOM->{'initialize-payment'}[$i]->transaction->amount["currency-id"], $obj_DOM->{'initialize-payment'}[$i]->transaction->amount, null, null, null, $obj_FailedPaymentMethod);
                                $obj_PaymentMethodResponse = null;
                                if ($obj_RS instanceof RoutingService) {
                                    $obj_PaymentMethodResponse = $obj_RS->getPaymentMethods();

                                    if ($obj_PaymentMethodResponse instanceof RoutingServiceResponse)
                                    {
                                        $obj_PaymentMethods = $obj_PaymentMethodResponse->getPaymentMethods();
                                        $obj_PM = $repository->getCardConfigurationsByCardIds($_OBJ_TXT,$obj_PaymentMethods);
                                        ksort($obj_PM, 1);
                                        $cOj_XML = '<cards>';
                                        foreach ($obj_PM as $key => $value)
                                        {
                                            if (($value instanceof PaymentMethod) === true) $cOj_XML .= $value->toXML();

                                        }
                                        $cOj_XML .='</cards>';
                                        $CardObj_XML = simplexml_load_string($cOj_XML, "SimpleXMLElement", LIBXML_COMPACT);
                                    }
                                }
                            } else {
                                $CardObj_XML = simplexml_load_string($obj_mPoint->getCards($obj_TxnInfo->getAmount(), $aFailedPMArray), "SimpleXMLElement", LIBXML_COMPACT);
                            }
                            $paymentTypes = array();
                            $aDCCPmid = array();
                            for ($j=0, $jMax = count($CardObj_XML->item); $j< $jMax; $j++)
                            {
                                $paymentTypes[]= (int)$CardObj_XML->item[$j]['payment-type'] ;
                                if(General::xml2bool((string)$CardObj_XML->item[$j]["dcc"]) === true)  array_push($aDCCPmid,(string)$CardObj_XML->item[$j]["id"]);

                            }

                            $presentmentCurrencies = null;
                            if(empty($aDCCPmid) === false && $obj_ClientConfig->getClientServices()->isPcc() === true) {
                                $presentmentCurrencies = $repository->getAddonConfiguration(AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::ePCC),$aDCCPmid);
                            }

                            if($sessionType > 1)
                            {
                                try {
                                    $splitPaymentConfig = Configuration::ProduceConfig($_OBJ_DB,$clientId,array_unique($paymentTypes),$sessionId);
                                    if ($splitPaymentConfig instanceof Configuration) {
                                        $xml .= "<split_payment>";
                                        $xml .= $splitPaymentConfig->toXML();
                                        $xml .= "</split_payment>";
                                    }
                                }
                                catch (JsonException $e) {
                                    trigger_error("SplitPayment Configuration for client id $clientId invalid" , E_USER_WARNING);
                                }
                            }
							$xml .= '<transaction id="'. $obj_TxnInfo->getID() .'" order-no="'. htmlspecialchars($obj_TxnInfo->getOrderID(), ENT_NOQUOTES) .'" type-id="'. $obj_TxnInfo->getTypeID() .'" eua-id="'. $euaId .'" language="'. $obj_TxnInfo->getLanguage() .'" auto-capture="'. htmlspecialchars($obj_TxnInfo->useAutoCapture() === AutoCaptureType::ePSPLevelAutoCapt ? "true" : "false") .'" mode="'. $obj_TxnInfo->getMode() .'">';
							$xml .= $obj_XML->amount->asXML();
							if (empty($sOrderXML) === false )  { $xml .= $sOrderXML; }
							if ($obj_TxnInfo->getPoints() > 0) { $xml .= $obj_XML->points->asXML(); }
							if ($obj_TxnInfo->getReward() > 0) { $xml .= $obj_XML->reward->asXML(); }
							if (trim($obj_TxnInfo->getMobile() ) != "" && $obj_TxnInfo->getMobile() > 0)
							{
                                $xml .= '<mobile country-id="' . $obj_CountryConfig->getID() . '" operator-id="' . $obj_TxnInfo->getOperator() . '">' . floatval($obj_TxnInfo->getMobile()) . '</mobile>';
                            }
							if (trim($obj_TxnInfo->getEMail() ) != "") { $xml .= $obj_XML->email->asXML(); }
							if (trim($obj_TxnInfo->getCustomerRef() ) != "")
							{
							    $xml .= $obj_XML->{'customer-ref'}->asXML();
							}
							$xml .= $obj_XML->{'callback-url'}->asXML();
							$xml .= $obj_XML->{'accept-url'}->asXML();
							$xml .= $obj_XML->{'cancel-url'}->asXML();
							$xml .= '</transaction>';
							$xml .= $obj_TxnInfo->getPaymentSessionXML();

                            $obj_XML=$CardObj_XML;
							// End-User already has an account and payment with Account enabled
							if ($obj_TxnInfo->getAccountID() > 0 && count($obj_XML->xpath("/cards/item[@type-id = 11]") ) == 1 && $bIsSingleSingOnPass === true)
							{
								$obj_mPoint = new CreditCard($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo);
                                if(empty($obj_PaymentMethods) === false){
                                    $oUA = null;
                                    $aPaymentMethods = $obj_PaymentMethods->payment_methods->payment_method;
                                    $aObj_XML = simplexml_load_string($obj_mPoint->getStoredCards($obj_TxnInfo->getAccountID(), $obj_ClientConfig, true, $oUA, $aPaymentMethods, $obj_TxnInfo->getCountryConfig()->getID(), $is_legacy), "SimpleXMLElement", LIBXML_COMPACT);
                                }else{
                                    $aObj_XML = simplexml_load_string($obj_mPoint->getStoredCards($obj_TxnInfo->getAccountID(), $obj_ClientConfig, FALSE,$oUA, array(), $obj_TxnInfo->getCountryConfig()->getID() ), "SimpleXMLElement", LIBXML_COMPACT);
                                }
								if ($obj_ClientConfig->getStoreCard() <= 3) {
								    $aObj_XML = $aObj_XML->xpath("/stored-cards/card[client/@id = ". $obj_ClientConfig->getID() ."]");
								}
								else {
								    $aObj_XML = $aObj_XML->xpath("/stored-cards/card");
								}
							}
							else {
							    $aObj_XML = array();
							}

							$version = 1;
							$isnewcardconfig = FALSE;
							if(empty($obj_DOM->{'initialize-payment'}[$i]->{'client-info'}['sdk-version']) === FALSE)
                            {
                                $version =  (int)$obj_DOM->{'initialize-payment'}[$i]->{'client-info'}['sdk-version'];
                            }

							if($version >= 2 )
                            {
                                $isnewcardconfig = TRUE;
                            }

							$aPSPs = array();
							$cardsXML = '<cards>';
							$walletsXML = '<wallets>';
							$apmsXML = '<apms>';
							$aggregatorsXML = '<aggregators>';
							$offlineXML = '<offline>';
							$voucherXML = '<vouchers>';

//							$splitPaymentFOPConfig = null;
//							if($sessionType > 1)
//                            {
//                                $splitPaymentFOPConfig = $obj_TxnInfo->getClientConfig()->getAdditionalProperties(0,"SplitPaymentFOPConfig");
//
//                                if (isset($splitPaymentFOPConfig) === true) {
//                                    $splitPaymentFOPConfig = json_decode($splitPaymentFOPConfig, TRUE, 512, JSON_THROW_ON_ERROR);
//                                }
//                            }

                           $aPresentmentConf= array();
                           if($presentmentCurrencies !== null) {
                               $aPresentmentConf = $presentmentCurrencies->toKeyValueConfigArray("getPaymentMethodId");
                           }


                            for ($j=0, $jMax = count($obj_XML->item); $j< $jMax; $j++)
							{
							    $cardXML = '';
								// Card does not represent "My Account" or the End-User has an acccount with Stored Cards or Stored Value Account is available
                                if (((int)$obj_XML->item[$j]['type-id'] !== 11
                                    || ($obj_TxnInfo->getAccountID() > 0 && (count($aObj_XML) > 0 || $obj_ClientConfig->getStoreCard() === 2)))  && empty($obj_XML->item[$j]["walletid"]) === true )
                                {
                                    if (in_array((integer)$obj_XML->item[$j]["pspid"], $aPSPs) === FALSE)
                                    {
                                        $aPSPs[] = intval($obj_XML->item[$j]["pspid"]);
                                    }

                                    $processorType = (int)$obj_XML->item[$j]['payment-type'] ;
                                    $cardId = (int)$obj_XML->item[$j]["id"];
//                                    $splittable = "false";
//                                    if($sessionType > 1 && isset($splitPaymentFOPConfig) && array_key_exists($processorType, $splitPaymentFOPConfig) === TRUE) {
//                                        if (is_array($splitPaymentFOPConfig[$processorType]) === FALSE || in_array($cardId, $splitPaymentFOPConfig[$processorType], TRUE) === TRUE) {
//                                            $splittable = "true";
//                                        }
//                                    }

                                    $fetchBalance = $obj_mPoint->isAutoFetchBalance($obj_TxnInfo, $cardId);
                                    $isDCCEnabled = false;
                                    if($fxServiceTypeId !== Constants::iExternalMCPOpted)
									{
 										$isDCCEnabled = General::xml2bool($obj_XML->item [$j] ["dcc"]);
									}
									$bPresentmentCurrency = false;
                                    if($fxServiceTypeId !== Constants::iExternalMCPOpted && isset($aPresentmentConf[(int)$obj_XML->item[$j]["id"]]) === true) {
                                        $bPresentmentCurrency = true;
                                    }

                                    $cardXML = '<card id="' . $obj_XML->item[$j]["id"] . '" type-id="' . $obj_XML->item[$j]['type-id'] . '" psp-id="' . $obj_XML->item[$j]['pspid'] . '" min-length="' . $obj_XML->item[$j]['min-length'] . '" max-length="' . $obj_XML->item[$j]['max-length'] . '" cvc-length="' . $obj_XML->item[$j]['cvc-length'] . '" state-id="' . $obj_XML->item[$j]['state-id'] . '" payment-type="' . $obj_XML->item[$j]['payment-type'] . '" preferred="' . $obj_XML->item[$j]['preferred'] . '" enabled="' . $obj_XML->item[$j]['enabled'] . '" processor-type="' . $obj_XML->item[$j]['processor-type'] . '" installment="' . $obj_XML->item[$j]['installment'] . '" cvcmandatory="' . $obj_XML->item[$j]['cvcmandatory'] . '" dcc="'. General::bool2xml($isDCCEnabled).'" presentment-currency="'.General::bool2xml($bPresentmentCurrency).'">';
                                    $cardXML .= '<name>' . htmlspecialchars($obj_XML->item[$j]->name, ENT_NOQUOTES) . '</name>';
                                    if($fetchBalance === true){
                                        $cardXML .= '<fetch-balance>true</fetch-balance>';
                                    }

									if($bPresentmentCurrency === true)
                                    {
										$cardXML .= '<settlement-currencies>';
										for($k = 0; $k < count($aPresentmentConf[(int)$obj_XML->item[$j]["id"]]); $k++)
										{
											$cardXML .= '<settlement-currency>';
											$cardXML .= '<id>'.$aPresentmentConf[(int)$obj_XML->item[$j]["id"]][$k]->getSettlementCurrencyId().'</id>';
											$cardXML .= '</settlement-currency>';
										}
										$cardXML .= '</settlement-currencies>';
									}

                                    $cardXML .= $obj_XML->item[$j]->prefixes->asXML();

                                    if (((int)$obj_XML->item[$j]['payment-type']) === Constants::iPAYMENT_TYPE_ONLINE_BANKING) {
                                        try {
                                            $pspId  = (int)$obj_XML->item[$j]['pspid'];
                                            if ($is_legacy === false) {
                                                $obj_CardResultSet = General::getRouteConfiguration($repository,$_OBJ_DB,$obj_mPoint,$obj_TxnInfo, $obj_ClientInfo, $aHTTP_CONN_INFO['routing-service'], $clientId, $obj_TxnInfo->getCountryConfig()->getID(), $obj_TxnInfo->getCurrencyConfig()->getID(), $obj_TxnInfo->getAmount(), (int)$obj_XML->item[$j]["type-id"], NULL,(string)$obj_XML->item[$j]->name,(int)$obj_XML->item[$j]["walletid"]);
                                                $pspId = (int)$obj_CardResultSet['PSPID'];
                                            }
                                            $obj_Processor = PaymentProcessor::produceConfig($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $pspId, $aHTTP_CONN_INFO);
                                            if ($obj_Processor !== FALSE) {
                                                $activePaymentMenthodsResponseXML = $obj_Processor->getPaymentMethods();
                                                if ($activePaymentMenthodsResponseXML !== NULL) {
                                                    $cardXML .= $activePaymentMenthodsResponseXML->{'active-payment-menthods'}->asXML();
                                                }
                                            }
                                        }
                                        catch (Exception $e) {}
                                    }
                                    elseif ((int)$obj_XML->item[$j]["processor-type"] === Constants::iPROCESSOR_TYPE_WALLET)
                                    {
                                        try
                                        {
                                            $obj_Processor = WalletProcessor::produceConfig($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, (int)$obj_XML->item[$j]['id'], $aHTTP_CONN_INFO);
                                            if ($obj_Processor !== FALSE)
                                            {
                                                if($obj_PaymentMethodResponse instanceof  RoutingServiceResponse){
                                                    $obj_Processor->setWalletCardSchemes($obj_PaymentMethodResponse->getCardSchemes());
                                                }

                                                $initResponseXML = $obj_Processor->initialize();
                                                foreach ($initResponseXML->children() as $obj_Elem)
                                                {
                                                    if ($obj_Elem->getName() !== 'name')
                                                    {
                                                        $cardXML .= trim($obj_Elem->asXML());
                                                    }
                                                }
                                            }
                                        }
                                        catch (Exception $e){}
                                    }

                                    $cardXML .= htmlspecialchars($obj_XML->item[$j]->name, ENT_NOQUOTES);    // Backward compatibility
                                    $cardXML .= '</card>';
                                }

                                if($isnewcardconfig === TRUE)
                                {
                                    switch ((int)$obj_XML->item[$j]["payment-type"]) {
                                        case Constants::iPAYMENT_TYPE_WALLET;
                                            $walletsXML .= $cardXML;
                                            break;
                                        case Constants::iPAYMENT_TYPE_APM;

                                            $apmsXML .= $cardXML;
                                            break;
                                        case Constants::iPAYMENT_TYPE_ONLINE_BANKING;
                                            $aggregatorsXML .= $cardXML;
                                            break;
                                        case Constants::iPAYMENT_TYPE_OFFLINE;
                                            $offlineXML .= $cardXML;
                                            break;
                                        case Constants::iPAYMENT_TYPE_VOUCHER;
                                            $voucherXML .= $cardXML;
                                            break;
                                        default:
                                            $cardsXML .= $cardXML;
                                    }
                                }
                                else {
                                    if((int)$obj_XML->item[$j]["processor-type"] === Constants::iPROCESSOR_TYPE_WALLET)
                                    {
                                        $walletsXML .= $cardXML;
                                    }
                                    $cardsXML .= $cardXML;
                                }

							}

                            $cardsXML .= '</cards>';
                            $walletsXML .= '</wallets>';
                            $apmsXML .= '</apms>';
                            $aggregatorsXML .= '</aggregators>';
                            $offlineXML .= '</offline>';
                            $voucherXML .= '</vouchers>';

                            $xml .= $cardsXML;
                            $xml .= $walletsXML;
                            if ($isnewcardconfig === TRUE) {
                                $xml .= $apmsXML;
                                $xml .= $aggregatorsXML;
                                $xml .= $offlineXML;
                                $xml .= $voucherXML;
                            }

							for ($j=0, $jMax = count($aPSPs); $j< $jMax; $j++)
							{
								switch ($aPSPs[$j])
								{
								case (Constants::iDSB_PSP):
                                    $obj_PSPConfig = General::producePSPConfigObject($_OBJ_DB, $obj_TxnInfo, Constants::iDSB_PSP);
									$obj_PSP = new DSB($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO["dsb"]);
									$cardsXML =  $obj_PSP->getExternalPaymentMethods($cardsXML);
									break;
								default:
									break;
								}
							}

							// End-User has Stored Cards available
							if (is_array($aObj_XML) === true && count($aObj_XML) > 0)
							{
								$xml .= '<stored-cards>';
								for ($j=0, $jMax = count($aObj_XML); $j< $jMax; $j++)
								{
									// Get list of presentment currencies

									$isDCCEnabled = false;
                                    if($fxServiceTypeId !== Constants::iExternalMCPOpted)
									{
 										$isDCCEnabled = General::xml2bool($obj_XML->item [$j] ["dcc"]);
									}

                                    $bPresentmentCurrency = false;
									if($fxServiceTypeId !== Constants::iExternalMCPOpted && isset($aPresentmentConf[(int)$aObj_XML[$j]["id"]]) === true) {
                                        $bPresentmentCurrency = true;
                                    }

									$xml .= '<card id="'. $aObj_XML[$j]["id"] .'" type-id="'. $aObj_XML[$j]->type["id"] .'" psp-id="'. $aObj_XML[$j]["pspid"] .'" preferred="'. $aObj_XML[$j]["preferred"] .'" state-id="'. $aObj_XML[$j]["state-id"] .'" charge-type-id="'. $aObj_XML[$j]["charge-type-id"] .'" cvc-length="'. $aObj_XML[$j]["cvc-length"] .'" expired="' . $aObj_XML[$j]["expired"] .'" cvcmandatory="' . $aObj_XML[$j]["cvcmandatory"] .'" dcc="' . General::bool2xml($isDCCEnabled) .'" presentment-currency="'.General::bool2xml($bPresentmentCurrency).'">';
									if (strlen($aObj_XML[$j]->name) > 0) { $xml .= $aObj_XML[$j]->name->asXML(); }

									if($bPresentmentCurrency === true)
									{
										$xml .= '<settlement-currencies>';
										for($k = 0; $k < count($aPresentmentConf[(int)$aObj_XML[$j]["id"]]); $k++)
										{
											$xml .= '<settlement-currency>';
											$xml .= '<id>'.$aPresentmentConf[(int)$aObj_XML[$j]["id"]][$k]->getSettlementCurrencyId().'</id>';
											$xml .= '</settlement-currency>';
										}
										$xml .= '</settlement-currencies>';
									}

									$xml .= '<card-number-mask>'. $aObj_XML[$j]->mask .'</card-number-mask>';
									$xml .= $aObj_XML[$j]->expiry->asXML();
									if (strlen($aObj_XML[$j]->{'card-holder-name'}) > 0) { $xml .= $aObj_XML[$j]->{'card-holder-name'}->asXML(); }
									if (count($aObj_XML[$j]->address) == 1) { $xml .= $aObj_XML[$j]->address->asXML(); }
									$xml .= '</card>';
								}
								$xml .= '</stored-cards>';
							}

							if ($obj_TxnInfo->getAccountID() > 0 && $obj_ClientConfig->getStoreCard() == 2)
							{
								$obj_XML = simplexml_load_string($obj_mPoint->getAccountInfo($obj_TxnInfo->getAccountID() ) );
								$xml .= '<account id="'. $obj_TxnInfo->getAccountID() .'">';
								$xml .= $obj_XML->balance->asXML();
								$xml .= $obj_XML->points->asXML();
								$xml .= '</account>';
							}
                            }
						}
						// Internal Error
						catch (mPointException $e)
						{
							trigger_error("Unknown error: ". $e->getMessage() ."(". $e->getCode() .")" ."\n". $e->getTrace(), E_USER_WARNING);
	
							header("HTTP/1.1 500 Internal Server Error");
	
							$xml = '<status code="'. $e->getCode() .'">'. htmlspecialchars($e->getMessage(), ENT_NOQUOTES) .'</status>';
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
	elseif (count($obj_DOM->{'initialize-payment'}) == 0)
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
echo preg_replace('~\s*(<([^>]*)>[^<]*</\2>|<[^>]*>)\s*~', '$1', $xml);
echo '</root>';
?>