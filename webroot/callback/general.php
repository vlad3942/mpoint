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
// Require specific Business logic for the CPM PSP component
require_once(sINTERFACE_PATH ."/cpm_psp.php");
// Require specific Business logic for the CPM ACQUIRER component
require_once(sINTERFACE_PATH ."/cpm_acquirer.php");
// Require specific Business logic for the CPM GATEWAY component
require_once(sINTERFACE_PATH ."/cpm_gateway.php");
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");
// Require specific Business logic for the Adyen component
require_once(sCLASS_PATH ."/adyen.php");
// Require specific Business logic for the DSB PSP component
require_once(sCLASS_PATH ."/dsb.php");
// Require specific Business logic for the VISA checkout component
require_once(sCLASS_PATH ."/visacheckout.php");
// Require specific Business logic for the Apple Pay component
require_once(sCLASS_PATH ."/applepay.php");
// Require specific Business logic for the Emirates' Corporate Payment Gateway (CPG) component
require_once(sCLASS_PATH ."/cpg.php");
// Require specific Business logic for the AMEX Express Checkout component
require_once(sCLASS_PATH ."/amexexpresscheckout.php");
// Require specific Business logic for the Master Pass component
require_once(sCLASS_PATH ."/masterpass.php");
// Require specific Business logic for the Wirecard component
require_once(sCLASS_PATH ."/wirecard.php");
// Require specific Business logic for the DIBS component
require_once(sCLASS_PATH ."/dibs.php");
// Require specific Business logic for the DIBS component
require_once(sCLASS_PATH ."/securetrading.php");
// Require specific Business logic for the CCAvenue component
require_once(sCLASS_PATH ."/ccavenue.php");
// Require specific Business logic for the PayPal component
require_once(sCLASS_PATH ."/paypal.php");
// Require specific Business logic for the PayFort component
require_once(sCLASS_PATH ."/payfort.php");
// Require specific Business logic for the DataCash component
require_once(sCLASS_PATH ."/datacash.php");
// Require specific Business logic for the Mada Mpgs component
require_once(sCLASS_PATH ."/mada_mpgs.php");
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
// Require specific Business logic for the QIWI component
require_once(sCLASS_PATH ."/qiwi.php");
// Require specific Business logic for the Nets component
require_once(sCLASS_PATH ."/nets.php");
// Require specific Business logic for the Klarna component
require_once(sCLASS_PATH ."/klarna.php");
// Require specific Business logic for the mVault component
require_once(sCLASS_PATH ."/mvault.php");
// Require specific Business logic for the Trustly component
require_once(sCLASS_PATH ."/trustly.php");
// Require specific Business logic for the 2C2P-ALC component
require_once(sCLASS_PATH ."/ccpp_alc.php");
// Require specific Business logic for the paytabs component
require_once(sCLASS_PATH ."/paytabs.php");
// Require specific Business logic for the citcon component
require_once(sCLASS_PATH ."/citcon.php");
// Require specific Business logic for the PPRO component
require_once(sCLASS_PATH ."/ppro.php");

// Require specific Business logic for the Amex component
require_once(sCLASS_PATH ."/amex.php");
// Require specific Business logic for the CHUBB component
require_once(sCLASS_PATH ."/chubb.php");
// Require specific Business logic for the UATP component
require_once(sCLASS_PATH . "/uatp.php");
// Require specific Business logic for the eGHL FPX component
require_once(sCLASS_PATH ."/eghl.php");
// Require specific Business logic for the chase component
require_once(sCLASS_PATH ."/chase.php");
// Require specific Business logic for the Global Collect component
require_once(sCLASS_PATH ."/globalcollect.php");
require_once(sCLASS_PATH ."/worldpay.php");
// Require specific Business logic for the PayU component
require_once(sCLASS_PATH ."/payu.php");
// Require specific Business logic for the Cielo component
require_once(sCLASS_PATH ."/cielo.php");
// Require specific Business logic for the cellulant component
require_once(sCLASS_PATH ."/cellulant.php");
require_once(sCLASS_PATH ."/global-payments.php");

// Require specific Business logic for the VeriTrans4G component
require_once(sCLASS_PATH ."/psp/veritrans4g.php");

require_once sCLASS_PATH . '/txn_passbook.php';
require_once sCLASS_PATH . '/passbookentry.php';
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
	$iAccountValidation = $obj_TxnInfo->hasEitherState($_OBJ_DB,Constants::iPAYMENT_ACCOUNT_VALIDATED);
	// Intialise Text Translation Object
	$_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH . $obj_TxnInfo->getLanguage() ."/global.txt", sLANGUAGE_PATH . $obj_TxnInfo->getLanguage() ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");
	$obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), intval($obj_XML->callback->{"psp-config"}["id"]) );

	$iStateID = (integer) $obj_XML->callback->status["code"];

    if ($iStateID == Constants::iPAYMENT_3DS_SUCCESS_STATE || $iStateID == Constants::iPAYMENT_3DS_FAILURE_STATE)
    {
        $obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), intval($obj_TxnInfo->getPSPID()) );
    }
    else
    {
        $obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), intval($obj_XML->callback->{"psp-config"}["id"]) );
    }
    $obj_mPoint = Callback::producePSP($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO, $obj_PSPConfig);

    $year ='';
    if(strlen($obj_XML->callback->transaction->card->expiry->year) === 2)
    {
        $year = substr(strftime("%Y"), 0, 2);
    }
	$sExpirydate =  $year.$obj_XML->callback->transaction->card->expiry->year ."-". $obj_XML->callback->transaction->card->expiry->month;
	// If transaction is in Account Validated i.e 1998 state no action to be done

    if($iStateID === Constants::iPAYMENT_REJECTED_STATE || $obj_TxnInfo->hasEitherState($_OBJ_DB, $iStateID) === false){
        array_push($aStateId,$iStateID);
    }

    $propertyValue = $obj_TxnInfo->getClientConfig()->getAdditionalProperties(Constants::iInternalProperty, "3DVERIFICATION");

    if($obj_PSPConfig->getProcessorType() === Constants::iPROCESSOR_TYPE_ACQUIRER && $propertyValue == true && $iStateID == Constants::iPAYMENT_3DS_SUCCESS_STATE) {
        if($iStateID == Constants::iPAYMENT_3DS_SUCCESS_STATE) {
            $obj_PSP = Callback::producePSP($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO);
            $mvault = new MVault($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO['mvault']);
            $xmlString = "<card id='" . $obj_XML->callback->transaction->card["type-id"] . "'><token>" . $obj_TxnInfo->getToken() . "</token></card>";
            $obj_Elem = $mvault->getPaymentData($obj_PSPConfig, simplexml_load_string($xmlString));
            $card_obj = simplexml_load_string($obj_Elem);
            $card_obj = $card_obj->{'payment-data'};
            $card_obj->card->cvc = base64_decode(strrev($obj_TxnInfo->getExternalID()) );
            $card_obj->card['type-id'] = $obj_XML->callback->transaction->card["type-id"];
            $cryptogram = $card_obj->card->{'info-3d-secure'}->addChild('cryptogram', $obj_XML->callback->transaction->card->{'info-3d-secure'}->cryptogram);
            $cryptogram->addAttribute('eci', $obj_XML->callback->transaction->card->{'info-3d-secure'}->cryptogram['eci']);
            $cryptogram->addAttribute('algorithm-id', $obj_XML->callback->transaction->card->{'info-3d-secure'}->cryptogram['algorithm-id']);

            $code = $obj_mPoint->authorize($obj_PSPConfig, $card_obj->card);
        }
        else{
            $sql = "UPDATE Log" . sSCHEMA_POSTFIX . ".Transaction_Tbl
                            SET extid=''
                            WHERE id = " . $obj_XML->callback->transaction['external-id'];
            //echo $sql ."\n";
            $_OBJ_DB->query($sql);
        }
    }
    if ($iStateID == Constants::iPAYMENT_ACCEPTED_STATE && empty($obj_XML->callback->{'approval-code'}) === false){
    	$sql = "UPDATE Log" . sSCHEMA_POSTFIX . ".Transaction_Tbl
                            SET approval_action_code= '".$obj_XML->callback->{'approval-code'}."' WHERE id = " . $obj_XML->callback->transaction['id'];
    	$_OBJ_DB->query($sql);
    }  else if ($iStateID == Constants::iPAYMENT_REJECTED_STATE || $iStateID == Constants::iPAYMENT_REJECTED_PSP_UNAVAILABLE_STATE|| $iStateID == Constants::iPAYMENT_REJECTED_INCORRECT_INFO_STATE ||
    		$iStateID == Constants::iPAYMENT_REJECTED_3D_SECURE_FAILURE_STATE || $iStateID == Constants::iPAYMENT_TIME_OUT_STATE|| $iStateID == Constants::iPSP_TIME_OUT_STATE){
    	// In case of Declined tramsaction increase the attempt count as there's possibility of retrial
       $sql = "UPDATE Log" . sSCHEMA_POSTFIX . ".Transaction_Tbl
                            SET attempt = attempt + 1 WHERE id = " . $obj_XML->callback->transaction['id'];
       $_OBJ_DB->query($sql);
    }

    $sAdditionalData = (string) $obj_XML->callback->{'additional-data'};
    if(isset($sAdditionalData))
    {

        $aAddtionalData = explode('&', $sAdditionalData);
        $additionalTxnDataIndex = -1;
        $additionalTxnData = [];
        foreach ($aAddtionalData as $addtionalData)
        {
            $additionalTxnDataIndex++;
            $txnData = explode('=', $addtionalData);
            $additionalTxnData[$additionalTxnDataIndex]['name'] = (string)$txnData[0];;
            $additionalTxnData[$additionalTxnDataIndex]['value'] = (string)$txnData[1];
            $additionalTxnData[$additionalTxnDataIndex]['type'] = (string)'Transaction';
        }
        if($additionalTxnDataIndex > -1)
        {
            $obj_TxnInfo->setAdditionalDetails($_OBJ_DB,$additionalTxnData,$obj_TxnInfo->getID());
        }
    }

    if($iAccountValidation != 1)
	{
        $saveCard = true;
        $isMVault = $obj_TxnInfo->getClientConfig()->getAdditionalProperties(Constants::iInternalProperty, 'mvault');
        if ($isMVault == 'true')
        {
            $saveCard = false;
        }

	// Save Ticket ID representing the End-User's stored Card Info
	if ($saveCard && $iStateID == Constants::iPAYMENT_ACCEPTED_STATE && count($obj_XML->callback->transaction->card->token) == 1)
	{
		$obj_mPoint->delMessage($obj_TxnInfo->getID(), Constants::iTICKET_CREATED_STATE);
		$obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iTICKET_CREATED_STATE, "Ticket: ". $obj_XML->callback->transaction->card->token);

		
		$sExpiry =  $obj_XML->callback->transaction->card->expiry->month ."/". $obj_XML->callback->transaction->card->expiry->year;
		
		$iStatus = $obj_mPoint->saveCard($obj_TxnInfo,
										 $obj_TxnInfo->getMobile(),
										 (integer) $obj_XML->callback->transaction->card["type-id"],
										 (integer) $obj_XML->callback->{'psp-config'}["id"],
										 $obj_XML->callback->transaction->card->token,
										 $obj_XML->callback->transaction->card->{'card-number'}, 
										 preg_replace('/\s+/', '', $sExpiry) ); // Remove all whitespaces from string.
		// The End-User's existing account was linked to the Client when the card was stored		
 		if ($iStatus == 1)
		{
			$obj_mPoint->sendLinkedInfo(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO), $obj_TxnInfo);
		}
		// New Account automatically created when Card was saved
		else if ($iStatus == 2)
		{
			$iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_TxnInfo->getClientConfig(), $obj_TxnInfo->getMobile() );
			if ($iAccountID == -1 && trim($obj_TxnInfo->getEMail() ) != "") { $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_TxnInfo->getClientConfig(), $obj_TxnInfo->getEMail() ); }
			$obj_TxnInfo->setAccountID($iAccountID);
			$obj_mPoint->getTxnInfo()->setAccountID($iAccountID);
			// SMS communication enabled
			if ($obj_TxnInfo->getClientConfig()->smsReceiptEnabled() === true)
			{
				$obj_mPoint->sendAccountInfo(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO), $obj_TxnInfo);
			}
		} 
		// E-Mail has been provided for the transaction
		if ($obj_TxnInfo->getEMail() != "") { $obj_mPoint->saveEMail($obj_TxnInfo->getMobile(), $obj_TxnInfo->getEMail() ); }
	}
	$fee = 0;
	$sIssuingBank = (string) $obj_XML->callback->{'issuing-bank'};
    $obj_mPoint->completeTransaction((integer)$obj_XML->callback->{'psp-config'}["id"],
        $obj_XML->callback->transaction["external-id"],
        (integer)$obj_XML->callback->transaction->card["type-id"],
        $iStateID,
        $fee,
        array($HTTP_RAW_POST_DATA),
        $sIssuingBank);
	// Payment Authorized: Perform a callback to the 3rd party Wallet if required
	if ($iStateID == Constants::iPAYMENT_ACCEPTED_STATE)
	{
		$obj_PSPConfig = null;
		$purchaseDate = null;
		
		switch (intval($obj_XML->callback->transaction->card["type-id"]) )
		{
		case (Constants::iVISA_CHECKOUT_WALLET):
			$obj_Wallet = new VisaCheckout($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO[Constants::iVISA_CHECKOUT_PSP]);
			$obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), Constants::iVISA_CHECKOUT_PSP);
			break;
		case (Constants::iAMEX_EXPRESS_CHECKOUT_WALLET):
			$obj_Wallet = new AMEXExpressCheckout($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO["amex-express-checkout"]);
			$obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), Constants::iAMEX_EXPRESS_CHECKOUT_PSP);
			break;
		case (Constants::iMASTER_PASS_WALLET):
			$obj_Wallet = new MasterPass($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO["masterpass"]);
			$obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), Constants::iMASTER_PASS_PSP);

			if($obj_XML->callback->transaction->PurchaseDate == "")
			{
				$purchaseDate = date('c',time());
			}
			else
			{
				$purchaseDate = $obj_XML->callback->transaction->PurchaseDate;
			}
			
			break;
		case (Constants::iAPPLE_PAY):
		default:
			break;
		}
		// 3rd party Wallet requires Callback
		if ( ($obj_PSPConfig instanceof PSPConfig) === true) { $obj_Wallet->callback($obj_PSPConfig, $obj_XML->callback->transaction->card, $purchaseDate); }
	}
	// Account Top-Up
	if ($obj_TxnInfo->getAccountID() > 0 && $iStateID == Constants::iPAYMENT_ACCEPTED_STATE && $obj_TxnInfo->getTypeID() >= 100 && $obj_TxnInfo->getTypeID() <= 109)
	{
		if ($obj_TxnInfo->getAccountID() > 0) { $iAccountID = $obj_TxnInfo->getAccountID(); }
		else
		{
			$obj_Home = new Home($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo->getClientConfig()->getCountryConfig() );
			$iAccountID = $obj_Home->getAccountID($obj_TxnInfo->getClientConfig()->getCountryConfig(), $obj_TxnInfo->getMobile() );
			if ($iAccountID == -1 && trim($obj_TxnInfo->getEMail() ) != "") { $iAccountID = $obj_Home->getAccountID($obj_TxnInfo->getClientConfig()->getCountryConfig(), $obj_TxnInfo->getEMail() ); }

			$obj_mPoint->link($iAccountID);
			$obj_TxnInfo->setAccountID($iAccountID);
		}
		switch ($obj_TxnInfo->getTypeID() )
		{
			case (Constants::iPURCHASE_OF_EMONEY):
				$obj_mPoint->topup($iAccountID, Constants::iTOPUP_OF_EMONEY, $obj_TxnInfo->getID(), $obj_TxnInfo->getAmount() );
				break;
			case (Constants::iPURCHASE_OF_POINTS):
				$obj_mPoint->topup($iAccountID, Constants::iTOPUP_OF_POINTS, $obj_TxnInfo->getID(), $obj_TxnInfo->getPoints() );
				break;
		}
	}
	if ($iStateID == Constants::iPAYMENT_ACCEPTED_STATE && $obj_TxnInfo->getReward() > 0 && $obj_TxnInfo->getAccountID() > 0)
	{
		$obj_mPoint->topup($obj_TxnInfo->getAccountID(), Constants::iREWARD_OF_POINTS, $obj_TxnInfo->getID(), $obj_TxnInfo->getReward() );
	}

	// Customer has an account
	if ($iStateID == Constants::iPAYMENT_ACCEPTED_STATE && $obj_TxnInfo->getAccountID() > 0)
	{
		$obj_mPoint->associate($obj_TxnInfo->getAccountID(), $obj_TxnInfo->getID() );
	}

	// Client has SMS Receipt enabled
	if ($iStateID == Constants::iPAYMENT_ACCEPTED_STATE && $obj_TxnInfo->getClientConfig()->smsReceiptEnabled() === true)
	{
		$obj_mPoint->sendSMSReceipt(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO) );
	}
	// Transaction uses Auto Capture and Authorization was accepted
	if ($obj_TxnInfo->useAutoCapture() === AutoCaptureType::eMerchantLevelAutoCapt && $iStateID == Constants::iPAYMENT_ACCEPTED_STATE)
	{
		// Reload so we have the newest version of the TxnInfo
		$obj_TxnInfo = TxnInfo::produceInfo($id, $_OBJ_DB);
		$obj_mPoint = Callback::producePSP($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO);
		
		$aCallbackArgs = array("transact" => $obj_XML->callback->transaction["external-id"],
							   "amount" => $obj_TxnInfo->getAmount(),
							   "card-id" =>  $obj_XML->callback->transaction->card["type-id"]);

        try
        {
            $responseCode = $obj_mPoint->capture($obj_TxnInfo->getAmount());

            if ($responseCode == 1000 || $responseCode == Constants::iPAYMENT_CAPTURED_AND_CALLBACK_SENT)
            {
                array_push($aStateId,Constants::iPAYMENT_CAPTURED_STATE);
                //$obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_CAPTURED_STATE, "");
            }
            else
            {
                array_push($aStateId,Constants::iPAYMENT_DECLINED_STATE);
                $obj_mPoint->newMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_DECLINED_STATE, "Payment Declined (2010)");
            }
        }
        catch (CallbackException $e)
        {
            //If capture is not supported for a PSP - log and continue
            trigger_error($e->getMessage(), E_USER_WARNING);
        }
	}
	
  }


    $txnPassbookObj = TxnPassbook::Get($_OBJ_DB, $id);
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
                    $state = Constants::iPAYMENT_ACCEPTED_STATE;
                    $status = Constants::sPassbookStatusError;
                    break;
            }
            if ($state !== 0) {
                $txnPassbookObj->updateInProgressOperations($obj_XML->callback->transaction->amount, $state, $status);
            }

        }
    }

  $sAdditionalData = (string) $obj_XML->callback->{'additional-data'};
  // Callback URL has been defined for Client
  if ($obj_TxnInfo->getCallbackURL() != "")
  {
    /*
     * Return the success code 202 to indicate Request Accepted and
     * the request to notify the upstream  retail system.
    */
      ignore_user_abort(true);
      header("HTTP/1.1 202 Accepted");
      header("Content-Length: 0");
      header("Connection: Close");
      flush();

     foreach ($aStateId as $iStateId) {
         if ($iStateId == 2000) {
             $obj_mPoint->notifyClient($iStateId, array("transact" => (string)$obj_XML->callback->transaction['external-id'], "amount" => $obj_XML->callback->transaction->amount, "card-no" => (string)$obj_XML->callback->transaction->card->{'card-number'}, "card-id" => $obj_XML->callback->transaction->card["type-id"], "expiry" => $sExpirydate , "additionaldata" => (string)$sAdditionalData));
         }
         else if ($iStateId == Constants::iPAYMENT_TIME_OUT_STATE){
         	$count = $obj_TxnInfo->hasEitherState($_OBJ_DB,Constants::iCB_ACCEPTED_TIME_OUT_STATE);
         	//Check whether a notification has already been sent to retail system with status 20109
         	// Sending duplicate 20109 status may end up to retail sending time out emails to customers more than once
         	if($count == 0)  {
         		$obj_mPoint->notifyClient($iStateId, array("transact" => (string)$obj_XML->callback->transaction['external-id'], "amount" => $obj_XML->callback->transaction->amount, "card-no" => (string)$obj_XML->callback->transaction->card->{'card-number'}, "card-id" => $obj_XML->callback->transaction->card["type-id"], "expiry" => $sExpirydate , "additionaldata" => (string)$sAdditionalData));
         	}
         }
         else {
             $obj_mPoint->notifyClient($iStateId, array("transact" => (string)$obj_XML->callback->transaction['external-id'], "amount" => $obj_XML->callback->transaction->amount, "card-no" => (string)$obj_XML->callback->transaction->card->{'card-number'}, "card-id" => $obj_XML->callback->transaction->card["type-id"], "additionaldata" => (string)$sAdditionalData));
        }
     }
     $obj_TxnInfo->setApprovalCode($obj_XML->callback->{'approval-code'});
     $obj_mPoint->updateSessionState($iStateId, (string)$obj_XML->callback->transaction['external-id'], $obj_XML->callback->transaction->amount, (string)$obj_XML->callback->transaction->card->{'card-number'}, $obj_XML->callback->transaction->card["type-id"], $sExpirydate, (string)$sAdditionalData);

      //update captured amt when psp returns captured callback
      if($iStateId == Constants::iPAYMENT_CAPTURED_STATE) {
          $obj_Capture = new Capture($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $obj_mPoint);
          $obj_Capture->updateCapturedAmount( (integer) $obj_XML->callback->transaction->amount);
      }
   }
  else {
      header("Content-Type: text/xml; charset=\"UTF-8\"");
      echo '<?xml version="1.0" encoding="UTF-8"?>';
      echo '<root>';
      echo '<status code="1000">Callback Success</status>';
      echo '</root>';
      $obj_mPoint->getTxnInfo()->getPaymentSession()->updateState();
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

