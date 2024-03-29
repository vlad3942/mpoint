<?php
/**
 * This files contains the for the Callback component which handles transactions processed through mPoints general PSP.
 * The file will update the Transaction status and add the following data fields:
 * 	- PSP Transaction ID
 * 	- ID of the card used for payment.
 * 
 *
 * Additionally the component sends out SMS Receipts and performs a callback to the client.
 */

// Require Global Include File
use api\classes\merchantservices\configuration\AddonServiceType;
use api\classes\merchantservices\Repositories\ReadOnlyConfigRepository;

require_once("../inc/include.php");

// Require the PHP API for handling the connection to GoMobile
require_once(sAPI_CLASS_PATH ."/gomobile.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require Business logic for the End-User Account Factory Provider
require_once(sCLASS_PATH ."/customer_info.php");
// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for Capture component (for use with auto-capture functionality)
require_once(sCLASS_PATH ."/capture.php");
// Require specific Business logic for the Refund component
require_once(sCLASS_PATH ."/refund.php");
// Require specific Business logic for the CPM PSP component
require_once(sINTERFACE_PATH ."/cpm_psp.php");
// Require specific Business logic for the CPM FRAUD GATEWAY component
require_once(sINTERFACE_PATH ."/cpm_fraud.php");
// Require specific Business logic for the CPM ACQUIRER component
require_once(sINTERFACE_PATH ."/cpm_acquirer.php");
// Require specific Business logic for the CPM GATEWAY component
require_once(sINTERFACE_PATH ."/cpm_gateway.php");
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");
// Require specific Business logic for the DSB PSP component
require_once(sCLASS_PATH ."/dsb.php");
// Require specific Business logic for the Emirates' Corporate Payment Gateway (CPG) component
require_once(sCLASS_PATH ."/cpg.php");
// Require specific Business logic for the Wirecard component
require_once(sCLASS_PATH ."/wirecard.php");
// Require specific Business logic for the DIBS component
require_once(sCLASS_PATH ."/dibs.php");
// Require specific Business logic for the Nets component
require_once(sCLASS_PATH ."/nets.php");
// Require specific Business logic for the mVault component
require_once(sCLASS_PATH ."/mvault.php");
// Require specific Business logic for the Amex component
require_once(sCLASS_PATH ."/amex.php");
// Require specific Business logic for the CHUBB component
require_once(sCLASS_PATH ."/chubb.php");
// Require specific Business logic for the UATP component
require_once(sCLASS_PATH . "/uatp.php");
// Require specific Business logic for the chase component
require_once(sCLASS_PATH ."/chase.php");
require_once sCLASS_PATH . '/txn_passbook.php';
require_once sCLASS_PATH . '/passbookentry.php';
require_once sCLASS_PATH . '/routing_service.php';
require_once sCLASS_PATH . '/routing_service_response.php';
require_once sCLASS_PATH . '/fraud/fraud_response.php';
require_once sCLASS_PATH . '/fraud/fraudResult.php';
require_once(sCLASS_PATH . '/payment_route.php');
require_once(sCLASS_PATH . '/paymentSecureInfo.php');

// Require Business logic for the Select Credit Card component
require_once(sCLASS_PATH .'/credit_card.php');
// Require specific Business logic for the CEBU Payment Center component
require_once(sCLASS_PATH .'/apm/CebuPaymentCenter.php');
// Require specific Business logic for the CEBU Payment Center component
require_once(sCLASS_PATH .'/voucher/TravelFund.php');
// Require model class for Payment Authorization
require_once(sCLASS_PATH ."/authorize.php");

/**
 * Input XML format
 *
<?xml version="1.0" encoding="UTF-8"?>
<root>
	<callback>
		<psp-config id="12">
			<name>CellpointMobileCOM</name>
		</psp-config>
		<transaction id="1825317" order-no="970-253176" external-id="8814395474257619">
			<amount country-id="100" currency="DKK">10000</amount>
			<card type-id="8">
				<card-number>411111*******4123</card-number>
				<expiry>
					<month>6</month>
					<year>16</year>
				</expiry>
				<token>31232121ddd</token>
			</card>
		</transaction>
		<status code="2000">17103%3A1111%3A6%2F2016</status>
		<approval-code>45TE24355</approval-code>
	</callback>
</root>
 */


set_time_limit(600);
// Standard retry strategy connecting to the database has proven inadequate
$i = 0;
while ( ($_OBJ_DB instanceof RDB) === false && $i < 5)
{
	// Instantiate connection to the Database
	$_OBJ_DB = RDB::produceDatabase($aDB_CONN_INFO["mpoint"]);
	$i++;
}
$obj_XML = simplexml_load_string(file_get_contents("php://input") );

	
$id = (integer)$obj_XML->callback->transaction["id"];
$xml = '';

$aStateId = array();

try
{
	$obj_TxnInfo = TxnInfo::produceInfo($id, $_OBJ_DB);
    $repository = new ReadOnlyConfigRepository($_OBJ_DB,$obj_TxnInfo);
	$iAccountValidation = $obj_TxnInfo->hasEitherState($_OBJ_DB,Constants::iPAYMENT_ACCOUNT_VALIDATED);
    $is_legacy = $obj_TxnInfo->getClientConfig()->getClientServices()->isLegacyFlow();
	// Intialise Text Translation Object
	$_OBJ_TXT = new api\classes\core\TranslateText(array(sLANGUAGE_PATH . $obj_TxnInfo->getLanguage() ."/global.txt", sLANGUAGE_PATH . $obj_TxnInfo->getLanguage() ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");

    $obj_PSPConfig = null;
    $isTxnRollInitiated = false;
    $iStateID = (integer) $obj_XML->callback->status["code"];
	$iSubCodeID = (integer) $obj_XML->callback->status["sub-code"];
    $provider_message = (string) $obj_XML->callback->status["provider-message"];
    $provider_status_code = (string) $obj_XML->callback->status["provider-status-code"];

    // In case of the primary PSP is down, and secondary route is configured for this client, authorize via alternate route
    $drService = $obj_TxnInfo->getClientConfig()->getAdditionalProperties(Constants::iInternalProperty, 'DR_SERVICE');
    $paymentRetryWithAlternateRoute = $obj_TxnInfo->getClientConfig()->getAdditionalProperties(Constants::iInternalProperty, 'PAYMENT_RETRY_WITH_ALTERNATE_ROUTE');
    $bHoldSessionComplete = false;
    if($iStateID == Constants::iPAYMENT_REJECTED_STATE && strtolower($drService) == 'true' && strtolower($paymentRetryWithAlternateRoute) == 'true')
    {
        // Check whether sub code is a part of transaction soft declined
        if ($obj_TxnInfo->hasEitherSoftDeclinedState($iSubCodeID) === true)
        {
            $iPSPID = (int)$obj_XML->callback->{"psp-config"}["id"];
            $objTxnRoute = new PaymentRoute($_OBJ_DB, $obj_TxnInfo->getSessionId());
            $iAlternateRoutes = $objTxnRoute->getRoutes();
            $retry_count = array_search($iPSPID, $iAlternateRoutes);

            if($retry_count < count($iAlternateRoutes)){
                $bHoldSessionComplete = true;
            }
        }
    }

    if($bHoldSessionComplete === false) {

        $pspid = (int)$obj_XML->callback->{"psp-config"}["id"];
        if ($iStateID == Constants::iPAYMENT_3DS_SUCCESS_STATE || $iStateID == Constants::iPAYMENT_3DS_FAILURE_STATE) {
            $pspid = $obj_TxnInfo->getPSPID();
        }

        $obj_PaymentProcessor = PaymentProcessor::produceConfig($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $pspid, $aHTTP_CONN_INFO);
        $obj_mPoint = $obj_PaymentProcessor->getPSPInfo();
        $obj_PSPConfig = $obj_PaymentProcessor->getPSPConfig();
        // check if callback state is already logged if logged then log appropriate duplicate callback state and prevent further processing
        $iIsCompleteTransactionStateLogged = $obj_TxnInfo->hasEitherState($_OBJ_DB, $iStateID);
        if ($iIsCompleteTransactionStateLogged > 0) {
            $dupStateId = '';
            if ($iStateID == Constants::iPAYMENT_ACCEPTED_STATE) {
                $dupStateId = Constants::iPAYMENT_DUPLICATED_STATE;
            } else {
                $dupStateId = Constants::iCALLBACK_DUPLICATED_STATE;
            }
            if ($dupStateId != '') {
                $obj_mPoint->newMessage($obj_TxnInfo->getID(), $dupStateId, var_export(array(file_get_contents("php://input")), true));
            }
            header("HTTP/1.1 400 Bad Request");
            header("Content-Length: 0");
            header("Connection: Close");
        } else {
            $year = '';
            if (strlen($obj_XML->callback->transaction->card->expiry->year) === 2) {
                $year = substr(strftime("%Y"), 0, 2);
            }
            $sExpirydate = $year . $obj_XML->callback->transaction->card->expiry->year . "-" . $obj_XML->callback->transaction->card->expiry->month;


            $propertyValue = $obj_PSPConfig->getAdditionalProperties(Constants::iInternalProperty, "3DVERIFICATION");

            if (($obj_PSPConfig->getProcessorType() === Constants::iPROCESSOR_TYPE_ACQUIRER || $obj_PSPConfig->getProcessorType() === Constants::iPROCESSOR_TYPE_PSP) && $propertyValue === 'mpi' && $iStateID == Constants::iPAYMENT_3DS_SUCCESS_STATE) {
                if ($iStateID == Constants::iPAYMENT_3DS_SUCCESS_STATE) {
                    $mvault = new MVault($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO['mvault']);
                    $xmlString = "<card id='" . $obj_XML->callback->transaction->card["type-id"] . "'><token>" . $obj_TxnInfo->getToken() . "</token></card>";
                    $obj_Elem = $mvault->getPaymentData($obj_PSPConfig, simplexml_load_string($xmlString));
                    $card_obj = simplexml_load_string($obj_Elem);
                    $card_obj = $card_obj->{'payment-data'};
                    $card_obj->card->cvc = base64_decode(strrev($obj_TxnInfo->getExternalID()));
                    $card_obj->card['type-id'] = $obj_XML->callback->transaction->card["type-id"];
                    $cryptogram = $card_obj->card->{'info-3d-secure'}->addChild('cryptogram', $obj_XML->callback->transaction->card->{'info-3d-secure'}->cryptogram);
                    $cryptogram->addAttribute('eci', $obj_XML->callback->transaction->card->{'info-3d-secure'}->cryptogram['eci']);
                    $cryptogram->addAttribute('algorithm-id', $obj_XML->callback->transaction->card->{'info-3d-secure'}->cryptogram['algorithm-id']);

                    $response = $obj_mPoint->authorize($obj_PSPConfig, $card_obj->card);
                    $code = $response->code;
                } else {
                    $sql = "UPDATE Log" . sSCHEMA_POSTFIX . ".Transaction_Tbl
                                SET extid=''
                                WHERE id = " . $obj_XML->callback->transaction['external-id'];
                    //echo $sql ."\n";
                    $_OBJ_DB->query($sql);
                }
            }
            if ($iStateID == Constants::iPAYMENT_ACCEPTED_STATE && empty($obj_XML->callback->{'approval-code'}) === false) {
                $sql = "UPDATE Log" . sSCHEMA_POSTFIX . ".Transaction_Tbl
                                SET approval_action_code= '" . $obj_XML->callback->{'approval-code'} . "' WHERE id = " . $obj_XML->callback->transaction['id'];
                $_OBJ_DB->query($sql);
            } else if ($iStateID == Constants::iPAYMENT_REJECTED_STATE || $iStateID == Constants::iPAYMENT_REJECTED_PSP_UNAVAILABLE_STATE || $iStateID == Constants::iPAYMENT_REJECTED_INCORRECT_INFO_STATE ||
                $iStateID == Constants::iPAYMENT_REJECTED_3D_SECURE_FAILURE_STATE || $iStateID == Constants::iPAYMENT_TIME_OUT_STATE || $iStateID == Constants::iPSP_TIME_OUT_STATE) {
                // In case of Declined tramsaction increase the attempt count as there's possibility of retrial
                $sql = "UPDATE Log" . sSCHEMA_POSTFIX . ".Transaction_Tbl
                                SET attempt = attempt + 1 WHERE id = " . $obj_XML->callback->transaction['id'];
                $_OBJ_DB->query($sql);
            }

            $sAdditionalData = (string)$obj_XML->callback->{'additional-data'};
            if (isset($sAdditionalData)) {

                $aAddtionalData = explode('&', $sAdditionalData);
                $additionalTxnDataIndex = -1;
                $additionalTxnData = [];
                foreach ($aAddtionalData as $addtionalData) {
                    $additionalTxnDataIndex++;
                    $txnData = explode('=', $addtionalData);
                    $additionalTxnData[$additionalTxnDataIndex]['name'] = (isset($txnData[0]) === true) ? (string)$txnData[0] : '';
                    $additionalTxnData[$additionalTxnDataIndex]['value'] = (isset($txnData[1]) === true) ? (string)$txnData[1] : '';
                    $additionalTxnData[$additionalTxnDataIndex]['type'] = (string)'Transaction';
                }
                if ($additionalTxnDataIndex > -1) {
                    $obj_TxnInfo->setAdditionalDetails($_OBJ_DB, $additionalTxnData, $obj_TxnInfo->getID());
                }
            }
            $fraudCheckResponse = new FraudResult();
            $obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID());
            $isConsolidate = filter_var($obj_ClientConfig->getAdditionalProperties(Constants::iInternalProperty, 'cumulativesettlement'), FILTER_VALIDATE_BOOLEAN);
            $isCancelPriority = filter_var($obj_ClientConfig->getAdditionalProperties(Constants::iInternalProperty, 'preferredvoidoperation'), FILTER_VALIDATE_BOOLEAN);
            $isMutualExclusive = filter_var($obj_ClientConfig->getAdditionalProperties(Constants::iInternalProperty, 'ismutualexclusive'), FILTER_VALIDATE_BOOLEAN);

            if ($iAccountValidation != 1) {
                $saveCard = true;
                $isMVault = $obj_TxnInfo->getClientConfig()->getAdditionalProperties(Constants::iInternalProperty, 'mvault');
                if ($isMVault == 'true') {
                    $saveCard = false;
                }

                // Save Ticket ID representing the End-User's stored Card Info
                if ($saveCard && $iStateID == Constants::iPAYMENT_ACCEPTED_STATE && count($obj_XML->callback->transaction->card->token) == 1) {
                    $obj_mPoint->delMessage($obj_TxnInfo->getID(), Constants::iTICKET_CREATED_STATE);
                    $obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iTICKET_CREATED_STATE, "Ticket: " . $obj_XML->callback->transaction->card->token);

                    $year = $obj_XML->callback->transaction->card->expiry->year;
                    if (strlen($year) === 4) {
                        $year = substr($year, 2, 2);
                    }
                    $sExpiry = $obj_XML->callback->transaction->card->expiry->month . "/" . $year;

                    $iStatus = $obj_mPoint->saveCard($obj_TxnInfo,
                        $obj_TxnInfo->getMobile(),
                        (integer)$obj_XML->callback->transaction->card["type-id"],
                        (integer)$obj_XML->callback->{'psp-config'}["id"],
                        $obj_XML->callback->transaction->card->token,
                        $obj_XML->callback->transaction->card->{'card-number'},
                        preg_replace('/\s+/', '', $sExpiry)); // Remove all whitespaces from string.
                    // The End-User's existing account was linked to the Client when the card was stored
                    if ($iStatus == 1) {
                        $obj_mPoint->sendLinkedInfo(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO), $obj_TxnInfo);
                    } // New Account automatically created when Card was saved
                    else if ($iStatus == 2) {
                        $iAccountID = EndUserAccount::getAccountID_Static($_OBJ_DB, $obj_TxnInfo->getClientConfig(), $obj_TxnInfo->getMobile());
                        if ($iAccountID == -1 && trim($obj_TxnInfo->getEMail()) != "") {
                            $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_TxnInfo->getClientConfig(), $obj_TxnInfo->getEMail());
                        }
                        $obj_TxnInfo->setAccountID($iAccountID);
                        $obj_mPoint->getTxnInfo()->setAccountID($iAccountID);
                        // SMS communication enabled
                        if ($obj_TxnInfo->getClientConfig()->smsReceiptEnabled() === true) {
                            $obj_mPoint->sendAccountInfo(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO), $obj_TxnInfo);
                        }
                    }
                    // E-Mail has been provided for the transaction
                    if ($obj_TxnInfo->getEMail() != "") {
                        $obj_mPoint->saveEMail($obj_TxnInfo->getMobile(), $obj_TxnInfo->getEMail());
                    }
                }
                $txnPassbookObj = TxnPassbook::Get($_OBJ_DB, $id, $obj_TxnInfo->getClientConfig()->getID());
                // check if the transaction is partial txn
                if ($obj_XML->callback->transaction->amount != $obj_TxnInfo->getAmount()) {
                    //check if the total captured amount is matching the txn amt i.e. partial capture case
                    //if partial capture operation is not supported then we can skip this query
                    $totalCapturedAmt =$obj_TxnInfo->getUpdatedCapturedAmount($_OBJ_DB,$obj_TxnInfo->getID());
                    if ($iStateID === Constants::iPAYMENT_CAPTURED_STATE && ($totalCapturedAmt != $obj_TxnInfo->getAmount())) {
                        $obj_Capture = new Capture($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $obj_mPoint);
                        $capturedAmt = $obj_Capture->updateCapturedAmount((integer)$obj_XML->callback->transaction->amount);
                        if($capturedAmt == $obj_TxnInfo->getAmount()){
                            $iStateID = Constants::iPAYMENT_CAPTURED_STATE;
                        }else {
                            $iStateID = Constants::iPAYMENT_PARTIALLY_CAPTURED_STATE;
                        }
                    } else {
                        //the sql cost for the below function is very high but for the below two cases there is no other way so have to use this but it is not recommended
                        $txnPassbookObj->UpdateAmounts();
                        if ($iStateID === Constants::iPAYMENT_REFUNDED_STATE && ($txnPassbookObj->getRefundedAmount() < $txnPassbookObj->getAuthorizedAmount())) {
                            $iStateID = Constants::iPAYMENT_PARTIALLY_REFUNDED_STATE;
                        } elseif ($iStateID === Constants::iPAYMENT_CANCELLED_STATE && ($txnPassbookObj->getCancelledAmount() < $txnPassbookObj->getAuthorizedAmount())) {
                            $iStateID = Constants::iPAYMENT_PARTIALLY_CANCELLED_STATE;
                        }
                    }
                }

                array_push($aStateId, $iStateID);

                $fee = 0;
                $sIssuingBank = (string)$obj_XML->callback->{'issuing-bank'};
                $authOriginalData = (string)$obj_XML->callback->{'auth-original-data'};

                if ($iStateID === Constants::iPAYMENT_PENDING_STATE && (int)$obj_TxnInfo->getPaymentMethod($_OBJ_DB)->PaymentType === Constants::iPAYMENT_TYPE_OFFLINE) {
                    $obj_TxnInfo = $obj_mPoint->getTxnInfo();
                    $obj_TxnInfo->setExternalId($obj_XML->callback->transaction["external-id"]);
                    $obj_mPoint->logTransaction($obj_TxnInfo);
                    try {
                        $obj_mPoint->generate_receipt();
                    }catch (mPointException $e) {
                        trigger_error($e->getMessage(), E_USER_NOTICE);
                    }
                }

                // change split details status
                if($obj_TxnInfo->getPaymentSession()->getSessionType() > 1) {
                    $obj_general = new General($_OBJ_DB, $_OBJ_TXT);
                    if($iStateID === Constants::iPAYMENT_REJECTED_STATE)
                    {
                        $splitPaymentAddOn = $repository->getAddonConfiguration(AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::eSPLIT_PAYMENT),array(),true);
                        $obj_general->changeSplitDetailStatus($obj_TxnInfo->getID(),'Failed');
                        $isReoffer = $splitPaymentAddOn->getProperties()["is_reoffer"];
                        $isManualRefund = !$splitPaymentAddOn->getProperties()["is_rollback"];
                        if ($isReoffer === false) {
                            $obj_general->changeSplitSessionStatus($obj_ClientConfig->getID(), $obj_TxnInfo->getPaymentSession()->getId(), 'Failed', $isManualRefund);
                        }
                    }
                    elseif ($iStateID === Constants::iPAYMENT_ACCEPTED_STATE){
                        $obj_general->changeSplitDetailStatus($obj_TxnInfo->getID(),'Success');
                    }
                }

                //Post-Auth-Fraud Check call
                $isPostAuthFraudGatewayEnabled = false;
                $postFraudAddon = $repository->getAddonConfiguration(AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::eFraud,'post_auth'));
                $obj_mCard = new CreditCard($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo);
                if (($iStateID == Constants::iPAYMENT_ACCEPTED_STATE) && $obj_TxnInfo->hasEitherState($_OBJ_DB, array(Constants::iPRE_FRAUD_CHECK_ACCEPTED_STATE, Constants::iPOST_FRAUD_CHECK_INITIATED_STATE,Constants::iPOST_FRAUD_CHECK_SKIP_RULE_MATCHED_STATE)) === false
                    && count($postFraudAddon->getConfiguration()) > 0) {
                    $isPostAuthFraudGatewayEnabled = true;
                    $obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iPOST_AUTH_FRAUD_CHECK_REQUIRED_STATE, '');
                }

                $obj_mPoint->completeTransaction((integer)$obj_XML->callback->{'psp-config'}["id"],
                    $obj_XML->callback->transaction["external-id"],
                    (integer)$obj_XML->callback->transaction->card["type-id"],
                    $iStateID,
                    $iSubCodeID,
                    $fee,
                    array(file_get_contents("php://input")),
                    $sIssuingBank, $authOriginalData);
                // Payment Authorized: Perform a callback to the 3rd party Wallet if required
                if ($iStateID == Constants::iPAYMENT_ACCEPTED_STATE) {
                    $objWallet_PSPConfig = null;
                    $purchaseDate = null;

                    switch (intval($obj_XML->callback->transaction->card["type-id"])) {
                        case (Constants::iVISA_CHECKOUT_WALLET):
                            $obj_Wallet = new VisaCheckout($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO[Constants::iVISA_CHECKOUT_PSP]);
                            $objWallet_PSPConfig = General::producePSPConfigObject($_OBJ_DB, $obj_TxnInfo, Constants::iVISA_CHECKOUT_PSP);
                            break;
                        case (Constants::iAMEX_EXPRESS_CHECKOUT_WALLET):
                            $obj_Wallet = new AMEXExpressCheckout($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO["amex-express-checkout"]);
                            $objWallet_PSPConfig = General::producePSPConfigObject($_OBJ_DB, $obj_TxnInfo, Constants::iAMEX_EXPRESS_CHECKOUT_PSP);
                            break;
                        case (Constants::iMASTER_PASS_WALLET):
                            $obj_Wallet = new MasterPass($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO["masterpass"]);
                            $objWallet_PSPConfig = General::producePSPConfigObject($_OBJ_DB, $obj_TxnInfo, Constants::iMASTER_PASS_PSP);
                            if ($obj_XML->callback->transaction->PurchaseDate == "") {
                                $purchaseDate = date('c', time());
                            } else {
                                $purchaseDate = $obj_XML->callback->transaction->PurchaseDate;
                            }

                            break;
                        case (Constants::iAPPLE_PAY):
                        default:
                            break;
                    }
                    // 3rd party Wallet requires Callback
                    if (($objWallet_PSPConfig instanceof PSPConfig) === true) {
                        $obj_Wallet->callback($objWallet_PSPConfig, $obj_XML->callback->transaction->card, $purchaseDate);
                    }
                }
                // Account Top-Up
                if ($obj_TxnInfo->getAccountID() > 0 && $iStateID == Constants::iPAYMENT_ACCEPTED_STATE && $obj_TxnInfo->getTypeID() >= 100 && $obj_TxnInfo->getTypeID() <= 109) {
                    if ($obj_TxnInfo->getAccountID() > 0) {
                        $iAccountID = $obj_TxnInfo->getAccountID();
                    } else {
                        $obj_Home = new Home($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo->getClientConfig()->getCountryConfig());
                        $iAccountID = $obj_Home->getAccountID($obj_TxnInfo->getClientConfig()->getCountryConfig(), $obj_TxnInfo->getMobile());
                        if ($iAccountID == -1 && trim($obj_TxnInfo->getEMail()) != "") {
                            $iAccountID = $obj_Home->getAccountID($obj_TxnInfo->getClientConfig()->getCountryConfig(), $obj_TxnInfo->getEMail());
                        }

                        $obj_mPoint->link($iAccountID);
                        $obj_TxnInfo->setAccountID($iAccountID);
                    }
                    switch ($obj_TxnInfo->getTypeID()) {
                        case (Constants::iPURCHASE_OF_EMONEY):
                            $obj_mPoint->topup($iAccountID, Constants::iTOPUP_OF_EMONEY, $obj_TxnInfo->getID(), $obj_TxnInfo->getAmount());
                            break;
                        case (Constants::iPURCHASE_OF_POINTS):
                            $obj_mPoint->topup($iAccountID, Constants::iTOPUP_OF_POINTS, $obj_TxnInfo->getID(), $obj_TxnInfo->getPoints());
                            break;
                    }
                }
                if ($iStateID == Constants::iPAYMENT_ACCEPTED_STATE && $obj_TxnInfo->getReward() > 0 && $obj_TxnInfo->getAccountID() > 0) {
                    $obj_mPoint->topup($obj_TxnInfo->getAccountID(), Constants::iREWARD_OF_POINTS, $obj_TxnInfo->getID(), $obj_TxnInfo->getReward());
                }

                // Customer has an account
                if ($iStateID == Constants::iPAYMENT_ACCEPTED_STATE && $obj_TxnInfo->getAccountID() > 0) {
                    $obj_mPoint->associate($obj_TxnInfo->getAccountID(), $obj_TxnInfo->getID());
                }

                // Client has SMS Receipt enabled
                if ($iStateID == Constants::iPAYMENT_ACCEPTED_STATE && $obj_TxnInfo->getClientConfig()->smsReceiptEnabled() === true) {
                    $obj_mPoint->sendSMSReceipt(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO));
                }

                $data['card-id'] = $obj_XML->callback->transaction->card["type-id"];
                $obj_TxnInfo = TxnInfo::produceInfo($obj_TxnInfo->getID(), $_OBJ_DB, $obj_TxnInfo, $data);

                $paymentType = (int)$obj_TxnInfo->getPaymentMethod($_OBJ_DB)->PaymentType;
                $currencyId = (int)$obj_XML->callback->transaction->amount["currency-id"];
                if ($currencyId > 0 && ($iStateID === Constants::iPAYMENT_ACCEPTED_STATE || $iStateID === Constants::iPAYMENT_REJECTED_STATE || $iStateID === Constants::iPAYMENT_REQUEST_CANCELLED_STATE || $iStateID === Constants::iPAYMENT_REQUEST_EXPIRED_STATE) && $paymentType === Constants::iPAYMENT_TYPE_OFFLINE && $currencyId !== $obj_TxnInfo->getCurrencyConfig()->getID()) {

                    $offlineAmount = (integer)$obj_XML->callback->transaction->amount;
                    $obj_CurrencyConfig = CurrencyConfig::produceConfig($_OBJ_DB, $currencyId);
                    $data['converted-currency-config'] = $obj_CurrencyConfig;
                    $data['converted-amount'] = $offlineAmount;
                    $data['conversion-rate'] = (float)$obj_XML->callback->transaction->amount / (float)$obj_TxnInfo->getAmount();
                    $obj_TxnInfo = TxnInfo::produceInfo($obj_TxnInfo->getID(), $_OBJ_DB, $obj_TxnInfo, $data);
                    $obj_mPoint->logTransaction($obj_TxnInfo);
                    if ($txnPassbookObj instanceof TxnPassbook) {
                        $txnPassbookObj->updatePerformedOptEntry($paymentType, $offlineAmount, $currencyId);
                    }
                }
                if ($txnPassbookObj instanceof TxnPassbook) {
                    foreach ($aStateId as $iStateId) {
                        $state = 0;
                        $status = '';
                        switch ((int)$iStateId) {
                            case Constants::iPAYMENT_ACCEPTED_STATE:
                                $state = Constants::iPAYMENT_ACCEPTED_STATE;
                                $status = Constants::sPassbookStatusDone;
                                break;
                            case Constants::iPAYMENT_REJECTED_STATE:
                            case Constants::iPAYMENT_REQUEST_CANCELLED_STATE:
                                $state = Constants::iPAYMENT_ACCEPTED_STATE;
                                $status = Constants::sPassbookStatusError;
                                break;
                            case Constants::iPAYMENT_CAPTURED_STATE:
                            case Constants::iPAYMENT_PARTIALLY_CAPTURED_STATE:
                                $state = Constants::iPAYMENT_CAPTURED_STATE;
                                $status = Constants::sPassbookStatusDone;
                                break;
                            case Constants::iPAYMENT_CAPTURE_FAILED_STATE:
                                $state = Constants::iPAYMENT_CAPTURED_STATE;
                                $status = Constants::sPassbookStatusError;
                                break;
                            case Constants::iPAYMENT_REFUNDED_STATE:
                            case Constants::iPAYMENT_PARTIALLY_REFUNDED_STATE:
                                $state = Constants::iPAYMENT_REFUNDED_STATE;
                                $status = Constants::sPassbookStatusDone;
                                break;
                            case Constants::iPAYMENT_REFUND_FAILED_STATE:
                                $state = Constants::iPAYMENT_REFUNDED_STATE;
                                $status = Constants::sPassbookStatusError;
                                break;
                            case Constants::iPAYMENT_CANCELLED_STATE:
                            case Constants::iPAYMENT_PARTIALLY_CANCELLED_STATE:
                                $state = Constants::iPAYMENT_CANCELLED_STATE;
                                $status = Constants::sPassbookStatusDone;
                                break;
                            case Constants::iPAYMENT_CANCEL_FAILED_STATE:
                                $state = Constants::iPAYMENT_CANCELLED_STATE;
                                $status = Constants::sPassbookStatusError;
                                break;
                        }
                        if ($state !== 0) {
                            $txnPassbookObj->updateInProgressOperations($obj_XML->callback->transaction->amount, $state, $status);
                        }
                    }
                }

                $obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID());
                $isConsolidate = filter_var($obj_ClientConfig->getAdditionalProperties(Constants::iInternalProperty, 'cumulativesettlement'), FILTER_VALIDATE_BOOLEAN);
                $isCancelPriority = filter_var($obj_ClientConfig->getAdditionalProperties(Constants::iInternalProperty, 'preferredvoidoperation'), FILTER_VALIDATE_BOOLEAN);
                $isMutualExclusive = filter_var($obj_ClientConfig->getAdditionalProperties(Constants::iInternalProperty, 'ismutualexclusive'), FILTER_VALIDATE_BOOLEAN);

                $aCallbackArgs = array("transact" => $obj_XML->callback->transaction["external-id"],
                    "amount" => $obj_TxnInfo->getAmount(),
                    "card-id" => $obj_XML->callback->transaction->card["type-id"]);
                $obj_TxnInfo = TxnInfo::produceInfo($id, $_OBJ_DB);
                $obj_PaymentProcessor = PaymentProcessor::produceConfig($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $pspid, $aHTTP_CONN_INFO);
                $obj_mPoint = $obj_PaymentProcessor->getPSPInfo();

                $paymentSecureInfo = null;
                if ($obj_XML->callback->transaction->card->{'info-3d-secure'}) {
                    $paymentSecureInfo = PaymentSecureInfo::produceInfo($obj_XML->callback->transaction->card->{'info-3d-secure'}, (integer)$obj_XML->callback->{'psp-config'}["id"], $obj_TxnInfo->getID());

                    if ($paymentSecureInfo !== null) $obj_mPoint->storePaymentSecureInfo($paymentSecureInfo);
                }

                $fraudCheckResponse = new FraudResult();
                if ($isPostAuthFraudGatewayEnabled === true) {
                    $aFraudRule = array();
                    $bIsSkipFraud = false;

                    if ($paymentSecureInfo === null) {
                        $paymentSecureInfo = PaymentSecureInfo::produceInfo($_OBJ_DB, $obj_TxnInfo->getID());
                        if ($paymentSecureInfo !== null) {
                            $paymentSecureInfo->attachPaymentSecureNode($obj_XML->callback->transaction->card);
                        }
                    }
                    if ($obj_PSPConfig->getAdditionalProperties(Constants::iInternalProperty, "post_fraud_rule") !== false) {
                        $aRules = $obj_PSPConfig->getAdditionalProperties(Constants::iInternalProperty);
                        foreach ($aRules as $value) {
                            if (strpos($value['key'], 'post_fraud_rule') !== false) {
                                $aFraudRule[] = $value['value'];

                            }
                        }
                    } else if ($obj_TxnInfo->getClientConfig()->getAdditionalProperties(Constants::iInternalProperty, "post_fraud_rule") !== false) {
                        $aRules = $obj_TxnInfo->getClientConfig()->getAdditionalProperties(Constants::iInternalProperty);
                        foreach ($aRules as $value) {
                            if (strpos($value['key'], 'post_fraud_rule') !== false) {
                                $aFraudRule[] = $value['value'];

                            }
                        }
                    }
                    if (empty($aFraudRule) === false) {
                        $bIsSkipFraud = $obj_mPoint->applyRule([$obj_XML,$obj_TxnInfo->toXML()], $aFraudRule);
                    }
                    if ($bIsSkipFraud === true) {
                        $obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iPOST_FRAUD_CHECK_SKIP_RULE_MATCHED_STATE, 'Fraud Check Skipped due to rule matched');
                    } else {
                        $obj_PaymentProcessor = PaymentProcessor::produceConfig($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, Constants::iMVAULT_PSP, $aHTTP_CONN_INFO);
                        $obj_mVaultPSP = $obj_PaymentProcessor->getPSPInfo();
                        $obj_mVaultPSPConfig = $obj_PaymentProcessor->getPSPConfig();
                        $obj_CardElem = $obj_mVaultPSP->getCardDetails();
                        if ($paymentSecureInfo !== null && $obj_CardElem !== null) {
                            $paymentSecureInfo->attachPaymentSecureNode($obj_CardElem);
                        }
                        $fraudCheckResponse = CPMFRAUD::attemptFraudCheckIfRoutePresent($obj_CardElem, $_OBJ_DB, null, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO, $obj_mCard, (int)$obj_XML->callback->transaction->card["type-id"], Constants::iPROCESSOR_TYPE_POST_FRAUD_GATEWAY);
                        $bisRollBack = $postFraudAddon->getProperties()['is_rollback'];

                        if($bisRollBack === true
                            && $fraudCheckResponse->isFraudCheckAccepted() === false
                            && $fraudCheckResponse->isFraudCheckAttempted() === true)
                        {
                            $isTxnRollInitiated = true;
                        }
                    }
                } else if ($iStateID == Constants::iPAYMENT_REJECTED_STATE && $obj_TxnInfo->hasEitherState($_OBJ_DB, array(Constants::iPRE_FRAUD_CHECK_REVIEW_STATE)) === true) {
                    $fraudCheckResponse = CPMFRAUD::attemptFraudInitCallback(Constants::iPRE_FRAUD_CHECK_REVIEW_FAIL_STATE, 'Review Closed', $_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO, (int)$obj_XML->callback->transaction->card["type-id"]);
                }

                // Transaction uses one step authorization then no need of PSP call
                if ($obj_TxnInfo->useAutoCapture() == AutoCaptureType::ePSPLevelAutoCapt && $iStateID == Constants::iPAYMENT_ACCEPTED_STATE) {

                    $code = 0;
                    $txnPassbookObj = TxnPassbook::Get($_OBJ_DB, $obj_TxnInfo->getID(), $obj_TxnInfo->getClientConfig()->getID());
                    $passbookEntry = new PassbookEntry
                    (
                        NULL,
                        $obj_TxnInfo->getAmount(),
                        $obj_TxnInfo->getCurrencyConfig()->getID(),
                        Constants::iCaptureRequested,
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
                    if ($txnPassbookObj instanceof TxnPassbook) {
                        $txnPassbookObj->addEntry($passbookEntry);
                        try {
                            $codes = $txnPassbookObj->performPendingOperations($_OBJ_TXT, $aHTTP_CONN_INFO, $isConsolidate, $isMutualExclusive, FALSE, FALSE);
                            $code = reset($codes);
                        } catch (Exception $e) {
                            trigger_error($e, E_USER_WARNING);
                        }
                    }
                }
            }

            $sAdditionalData = (string)$obj_XML->callback->{'additional-data'};
            // Callback URL has been defined for Client

            /*
             * Return the success code 202 to indicate Request Accepted and
             * the request to notify the upstream  retail system.
            */
            ignore_user_abort(true);//not required
            set_time_limit(0);
            ob_start(); // do initial processing here
            header("HTTP/1.1 202 Accepted");
            header("Content-Length: 0");
            header("Connection: Close");
            ob_end_flush();
            flush();
            fastcgi_finish_request();

            $obj_TxnInfo->setApprovalCode($obj_XML->callback->{'approval-code'});

            //update captured amt when psp returns captured callback
            if ($iStateID == Constants::iPAYMENT_CAPTURED_STATE) {
                //if partial capture operation is not supported then we can skip this query
                $totalCapturedAmt =$obj_TxnInfo->getUpdatedCapturedAmount($_OBJ_DB,$obj_TxnInfo->getID());
                if($totalCapturedAmt != $obj_TxnInfo->getAmount()) {
                    $obj_Capture = new Capture($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $obj_mPoint);
                    $obj_Capture->updateCapturedAmount((integer)$obj_XML->callback->transaction->amount);
                }
            }

            foreach ($aStateId as $iStateId) {
                if ($iStateId == Constants::iPAYMENT_ACCEPTED_STATE) {
                    $obj_mPoint->notifyClient($iStateId, array("transact" => (string)$obj_XML->callback->transaction['external-id'], "amount" => $obj_XML->callback->transaction->amount, "cardnomask" => (string)$obj_XML->callback->transaction->card->{'card-number'}, "cardid" => (int)$obj_XML->callback->transaction->card["type-id"], "expiry" => $sExpirydate,"provider_message"=>$provider_message,"provider_status_code"=>$provider_status_code), $obj_TxnInfo->getClientConfig()->getSurePayConfig($_OBJ_DB), $iSubCodeID);
                } else if ($iStateId == Constants::iPAYMENT_TIME_OUT_STATE) {
                    $count = $obj_TxnInfo->hasEitherState($_OBJ_DB, Constants::iCB_ACCEPTED_TIME_OUT_STATE);
                    //Check whether a notification has already been sent to retail system with status 20109
                    // Sending duplicate 20109 status may end up to retail sending time out emails to customers more than once
                    if ($count == 0) {
                        $obj_mPoint->notifyClient($iStateId, array("transact" => (string)$obj_XML->callback->transaction['external-id'], "amount" => $obj_XML->callback->transaction->amount, "cardnomask" => (string)$obj_XML->callback->transaction->card->{'card-number'}, "cardid" => (int)$obj_XML->callback->transaction->card["type-id"], "expiry" => $sExpirydate,"provider_message"=>$provider_message,"provider_status_code"=>$provider_status_code), $obj_TxnInfo->getClientConfig()->getSurePayConfig($_OBJ_DB), $iSubCodeID);
                    }
                } else {
                    $obj_mPoint->notifyClient($iStateId, array("transact" => (string)$obj_XML->callback->transaction['external-id'], "amount" => $obj_XML->callback->transaction->amount, "cardnomask" => (string)$obj_XML->callback->transaction->card->{'card-number'}, "cardid" => (int)$obj_XML->callback->transaction->card["type-id"],"provider_message"=>$provider_message,"provider_status_code"=>$provider_status_code), $obj_TxnInfo->getClientConfig()->getSurePayConfig($_OBJ_DB), $iSubCodeID);
                }
            }

            if ($iStateID === Constants::iPAYMENT_ACCEPTED_STATE && $obj_TxnInfo->hasEitherState($_OBJ_DB, array(Constants::iPOST_FRAUD_CHECK_REJECTED_STATE)) === false)
            {
                $obj_mPoint->updateSessionState($iStateID, (string)$obj_XML->callback->transaction['external-id'], (int)$obj_XML->callback->transaction->amount, (string)$obj_XML->callback->transaction->card->{'card-number'}, (int)$obj_XML->callback->transaction->card["type-id"], $sExpirydate, (string)$sAdditionalData, $obj_TxnInfo->getClientConfig()->getSurePayConfig($_OBJ_DB), $fee,null,$iSubCodeID,$provider_message,$provider_status_code);
                trigger_error("Voucher Redeem Fraud condition pass: Txn-Id " . $obj_TxnInfo->getID() . " State updated: " .$iStateID);

                $obj_TxnInfo = TxnInfo::produceInfo($obj_TxnInfo->getID(), $_OBJ_DB);
                $sessiontype = (int)$obj_ClientConfig->getAdditionalProperties(0, 'sessiontype');
                if ($sessiontype > 1 && $obj_TxnInfo->getPaymentSession()->getStateId() == Constants::iSESSION_PARTIALLY_COMPLETED) {
                    try {
                        $whereClause = 'message_tbl.stateid = ' . Constants::iTRANSACTION_CREATED . " AND transaction_tbl.created >= '" . $obj_TxnInfo->getCreatedTimestamp() . "' order by transaction_tbl.id ASC LIMIT 1 ";
                        $newTxnInfoIds = $obj_TxnInfo->getPaymentSession()->getFilteredTransaction($whereClause);
                        if (count($newTxnInfoIds) > 0) {
                            $newTxnInfo = TxnInfo::produceInfo($newTxnInfoIds[0], $_OBJ_DB);
                            $iPSPID = $newTxnInfo->getPSPID();
                            $iAmount = (int)$newTxnInfo->getAmount();

                            $obj_PaymentProcessor = PaymentProcessor::produceConfig($_OBJ_DB, $_OBJ_TXT, $newTxnInfo, $iPSPID, $aHTTP_CONN_INFO);
                            $obj_PSPConfig = $obj_PaymentProcessor->getPSPConfig();
                            if (($obj_PSPConfig->getProcessorType() === Constants::iPROCESSOR_TYPE_VOUCHER)
                                && ($newTxnInfo->hasEitherState($_OBJ_DB, array(Constants::iPAYMENT_WITH_VOUCHER_STATE, Constants::iPAYMENT_ACCEPTED_STATE, Constants::iPAYMENT_REJECTED_STATE)) === FALSE)) {
                                $obj_PSP = $obj_PaymentProcessor->getPSPInfo();
                                $obj_Authorize = new Authorize($_OBJ_DB, $_OBJ_TXT, $newTxnInfo, $obj_PSP);

                                $voucherId = $newTxnInfo->getAdditionalData('voucherid');


                                // <editor-fold defaultstate="collapsed" desc="Update Passbook Table">

                                $txnPassbookObj = TxnPassbook::Get($_OBJ_DB, $newTxnInfo->getID(), $newTxnInfo->getClientConfig()->getID());

                                $passbookEntry = new PassbookEntry
                                (
                                    NULL,
                                    $iAmount,
                                    $newTxnInfo->getCurrencyConfig()->getID(),
                                    Constants::iInitializeRequested
                                );
                                if ($txnPassbookObj instanceof TxnPassbook) {
                                    $txnPassbookObj->addEntry($passbookEntry);
                                    $txnPassbookObj->performPendingOperations();
                                }

                                $passbookEntry = new PassbookEntry
                                (
                                    NULL,
                                    $iAmount,
                                    $newTxnInfo->getCurrencyConfig()->getID(),
                                    Constants::iAuthorizeRequested
                                );
                                if ($txnPassbookObj instanceof TxnPassbook) {
                                    $txnPassbookObj->addEntry($passbookEntry);
                                    $txnPassbookObj->performPendingOperations();
                                }
                                // </editor-fold>
                                $isVoucherRedeemStatus = $obj_Authorize->redeemVoucher((string)$voucherId, $iAmount);

                                // <editor-fold defaultstate="collapsed" desc="Parse Voucher Response">

                                if ($isVoucherRedeemStatus === 100) {
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
                                // </editor-fold>

                            }else{
                                trigger_error("Voucher Redeem PSP condition fail : Txn-Id " . $obj_TxnInfo->getID() . " PSP-ID " . $iPSPID);
                            }
                        }else{
                            trigger_error("Split Txn not found: Txn-Id " . $obj_TxnInfo->getID());

                        }
                    } catch (Exception $e) {
                        trigger_error("Voucher Redeem Fail in general.php, message - " . $e->getMessage());
                    }
                }else{
                    trigger_error("Voucher Redeem state condition fail: Txn-Id " . $obj_TxnInfo->getID() . " Session Type " . $sessiontype . " State Id " . $obj_TxnInfo->getPaymentSession()->getStateId());
                }
            }


            if($isTxnRollInitiated === true)
            {
                $txnPassbookObj = TxnPassbook::Get($_OBJ_DB, $obj_TxnInfo->getID(), $obj_TxnInfo->getClientConfig()->getID());
                $passbookEntry = new PassbookEntry
                (
                    NULL,
                    $obj_TxnInfo->getAmount(),
                    $obj_TxnInfo->getCurrencyConfig()->getID(),
                    Constants::iVoidRequested
                );
                if ($txnPassbookObj instanceof TxnPassbook) {
                    try {
                        $txnPassbookObj->addEntry($passbookEntry);
                    } catch (Exception $e) {
                        trigger_error($e, E_USER_WARNING);
                    }
                }
            }

            if ($obj_TxnInfo->hasEitherState($_OBJ_DB,Constants::iPAYMENT_REFUNDED_STATE) === false && (($obj_TxnInfo->useAutoCapture() == AutoCaptureType::ePSPLevelAutoCapt
                && ($iStateID == Constants::iPAYMENT_CAPTURED_STATE || $obj_TxnInfo->hasEitherState($_OBJ_DB,Constants::iPAYMENT_CAPTURED_STATE) === true))
                    || ($obj_TxnInfo->useAutoCapture() != AutoCaptureType::ePSPLevelAutoCapt && $iStateID == Constants::iPAYMENT_ACCEPTED_STATE)))
            {
                try {
                    $txnPassbookObj = TxnPassbook::Get($_OBJ_DB, $obj_TxnInfo->getID(), $obj_TxnInfo->getClientConfig()->getID());
                    $codes = $txnPassbookObj->performPendingOperations($_OBJ_TXT, $aHTTP_CONN_INFO, $isConsolidate, $isMutualExclusive);
                    $code = reset($codes);
                } catch (Exception $e) {
                    $code = 99;
                    trigger_error($e, E_USER_WARNING);
                }
                if (in_array($code, [Constants::iTRANSACTION_CREATED, Constants::iINPUT_VALID_STATE]))
                {
                    if($obj_TxnInfo->hasEitherState($_OBJ_DB, Constants::iPAYMENT_REFUNDED_STATE) === true) {
                        $obj_mPoint->notifyClient(Constants::iPAYMENT_REFUNDED_STATE, array("transact" => (string)$obj_XML->callback->transaction['external-id'], "amount" => $obj_XML->callback->transaction->amount, "cardnomask" => (string)$obj_XML->callback->transaction->card->{'card-number'}, "cardid" => (int)$obj_XML->callback->transaction->card["type-id"],"provider_message"=>$provider_message,"provider_status_code"=>$provider_status_code), $obj_TxnInfo->getClientConfig()->getSurePayConfig($_OBJ_DB));
                    } else if($obj_TxnInfo->hasEitherState($_OBJ_DB, Constants::iPAYMENT_CANCELLED_STATE) === true) {
                        $obj_mPoint->notifyClient(Constants::iPAYMENT_CANCELLED_STATE, array("transact" => (string)$obj_XML->callback->transaction['external-id'], "amount" => $obj_XML->callback->transaction->amount, "cardnomask" => (string)$obj_XML->callback->transaction->card->{'card-number'}, "cardid" => (int)$obj_XML->callback->transaction->card["type-id"],"provider_message"=>$provider_message,"provider_status_code"=>$provider_status_code), $obj_TxnInfo->getClientConfig()->getSurePayConfig($_OBJ_DB));
                    }
                }
            }

            // Transaction uses Auto Capture and Authorization was accepted
            if ($isTxnRollInitiated === false && $obj_TxnInfo->useAutoCapture() == AutoCaptureType::eMerchantLevelAutoCapt && $iStateID == Constants::iPAYMENT_ACCEPTED_STATE && ($fraudCheckResponse->isFraudCheckAccepted() === true || $fraudCheckResponse->isFraudCheckAttempted() === false)) {

                $code = 0;
                $txnPassbookObj = TxnPassbook::Get($_OBJ_DB, $obj_TxnInfo->getID(), $obj_TxnInfo->getClientConfig()->getID());
                $passbookEntry = new PassbookEntry
                (
                    NULL,
                    $obj_TxnInfo->getAmount(),
                    $obj_TxnInfo->getCurrencyConfig()->getID(),
                    Constants::iCaptureRequested
                );
                if ($txnPassbookObj instanceof TxnPassbook) {
                    $txnPassbookObj->addEntry($passbookEntry);
                    try {
                        $codes = $txnPassbookObj->performPendingOperations($_OBJ_TXT, $aHTTP_CONN_INFO, $isConsolidate, $isMutualExclusive);
                        $code = reset($codes);
                    } catch (Exception $e) {
                        trigger_error($e, E_USER_WARNING);
                    }
                }

                // Refresh transactioninfo object once the capture is performed
                $obj_TxnInfo = TxnInfo::produceInfo($id, $_OBJ_DB);

                if ($code == 1000) {
                    $stateId = Constants::iPAYMENT_CAPTURED_STATE;
                    $obj_mPoint->notifyClient($stateId, array("transact" => (string)$obj_XML->callback->transaction['external-id'], "amount" => $obj_XML->callback->transaction->amount, "cardnomask" => (string)$obj_XML->callback->transaction->card->{'card-number'}, "cardid" => (int)$obj_XML->callback->transaction->card["type-id"],"provider_message"=>$provider_message,"provider_status_code"=>$provider_status_code), $obj_TxnInfo->getClientConfig()->getSurePayConfig($_OBJ_DB), $iSubCodeID);

                } else {
                    $stateId = Constants::iPAYMENT_CAPTURE_FAILED_STATE;
                    $obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_CAPTURE_FAILED_STATE, "Payment Declined (2010)");
                    $obj_mPoint->notifyClient($stateId, array("transact" => (string)$obj_XML->callback->transaction['external-id'], "amount" => $obj_XML->callback->transaction->amount, "cardnomask" => (string)$obj_XML->callback->transaction->card->{'card-number'}, "cardid" => (int)$obj_XML->callback->transaction->card["type-id"],"provider_message"=>$provider_message,"provider_status_code"=>$provider_status_code), $obj_TxnInfo->getClientConfig()->getSurePayConfig($_OBJ_DB), $iSubCodeID);
                }

            }


        }
    }
}
catch (TxnInfoException $e)
{
	header("HTTP/1.1 500 Internal Server Error");
	$xml .= '<status code="'. $e->getCode() .'">'. htmlspecialchars($e->getMessage(), ENT_NOQUOTES). '</status>';
	trigger_error($e->getMessage() ."\n". $HTTP_RAW_POST_DATA, E_USER_WARNING);
}
catch (CallbackException $e)
{
	header("HTTP/1.1 500 Internal Server Error");
	$xml .= '<status code="'. $e->getCode() .'">'. htmlspecialchars($e->getMessage(), ENT_NOQUOTES). '</status>';
	trigger_error($e->getMessage() ."\n". $HTTP_RAW_POST_DATA, E_USER_WARNING);
}

