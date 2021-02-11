<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @version 1.11
 */

/* ==================== Callback Exception Classes Start ==================== */
/**
 * Exception class for all Callback exceptions
 */
class CallbackException extends mPointException { }
/* ==================== Callback Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for handling the Callback request from the Payment Service Provider (PSP).
 * The class contains methods that completes the transaction log with information received from the PSP, notifies the Client
 * and sends out an SMS Receipt to the Customer.
 *
 */
abstract class Callback extends EndUserAccount
{
	/**
	 * Data object with the Transaction Information
	 *
	 * @var TxnInfo
	 */
	private $_obj_TxnInfo;

	/**
	 * Data array with Connection Information for the specific PSP
	 *
	 * @var array
	 */
	protected $aCONN_INFO;

	/**
	 * Data object with PSP configuration Information
	 *
	 * @var PSPConfig
	 */
	private $_obj_PSPConfig;


	/**
	 * Data object with PSP configuration Information
	 *
	 * @var ClientInfo
	 */
	private $_obj_ClientInfo;

	/*
	 * Integer identifier to identify the Settlement Mode
	 *
	 *  0 - Real Time
	 *	2 - bulk capture
	 *	3 - bulk refund
	 *	6 - bulk capture + bulk  refund
	 *
	 */
	private $_iCaptureMethod = null;

	/**
	 * Default Constructor.
	 *
	 * @param	RDB $oDB				Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param	TranslateText $oTxt 	Text Translation Object for translating any text into a specific language
	 * @param 	TxnInfo $oTI 			Data object with the Transaction Information
	 * @param 	PSPConfig $oPSPConfig 	Configuration object with the PSP Information
	 */
	public function __construct(RDB $oDB, TranslateText $oTxt, TxnInfo $oTI, array $aConnInfo, PSPConfig $oPSPConfig = null, ClientInfo $oClientInfo = null)
	{
		parent::__construct($oDB, $oTxt, $oTI->getClientConfig() );

		$this->_obj_TxnInfo = $oTI;
		$this->_obj_ClientInfo = $oClientInfo;
		$pspID = (integer)$this->getPSPID() > 0 ? $this->getPSPID() : $oTI->getPSPID();
        if(empty($aConnInfo) === false )
        {
            $this->aCONN_INFO = $aConnInfo;
        }
        else
        {
            throw new CallbackException("Connection Configuration not found for the given PSP ID ". $pspID);
        }
        $is_legacy = $oTI->getClientConfig()->getAdditionalProperties (Constants::iInternalProperty, 'IS_LEGACY');
        if ($oPSPConfig == null) {
        	if(strtolower($is_legacy) == 'false'){
                $oPSPConfig = PSPConfig::produceConfiguration($oDB, $oTI->getClientConfig()->getID(), $oTI->getClientConfig()->getAccountConfig()->getID(), $pspID, $oTI->getRouteConfigID());
			}else {
                $oPSPConfig = PSPConfig::produceConfig($oDB, $oTI->getClientConfig()->getID(), $oTI->getClientConfig()->getAccountConfig()->getID(), $pspID);
            }
        }
		$this->_obj_PSPConfig = $oPSPConfig;
	}

	protected function updateTxnInfoObject()
	{
		$this->_obj_TxnInfo = TxnInfo::produceInfo( $this->_obj_TxnInfo->getID(), $this->getDBConn());
		$this->_obj_TxnInfo->produceOrderConfig($this->getDBConn());
	}

	/**
	 * Returns the Data object with the Transaction Information.
	 *
	 * @return TxnInfo
	 */
	public function getTxnInfo() { return $this->_obj_TxnInfo; }
	
	/**
	 * Returns the Data object with the Client Information.
	 *
	 * @return ClientInfo
	 */
	public function getClientInfo() { return $this->_obj_ClientInfo; }


	/**
	 * Returns the Configuration object with the PSP Information.
	 *
	 * @return PSPConfig
	 */
	public function getPSPConfig() { return $this->_obj_PSPConfig; }

	/**
	 * Sends an SMS Receipt with Payment Information to the Customer through GoMobile.
	 *
	 * @see 	GoMobileClient
	 * @see 	Constants::iMT_SMS_TYPE
	 * @see 	Constants::iMT_PRICE
	 *
	 * @param 	GoMobileConnInfo $oCI 	Reference to the data object with the Connection Info required to communicate with GoMobile
	 */
	public function sendSMSReceipt(GoMobileConnInfo &$oCI)
	{
		$sBody = $this->getText()->_("mPoint - SMS Receipt");
		$sBody = str_replace("{MPOINTID}", $this->_obj_TxnInfo->getID(), $sBody);
		// Order Number Provided
		if (strlen($this->_obj_TxnInfo->getOrderID() ) > 0)
		{
			$sBody = str_replace("{ORDERID}", $this->_obj_TxnInfo->getOrderID(), $sBody);
		}
		else
		{
			$aLines = explode("\n", $sBody);
			$sBody = "";
			foreach ($aLines as $line)
			{
				if (stristr($line, "{ORDERID}") == false) { $sBody .= trim($line) ."\n"; }
			}
			$sBody = trim($sBody);
		}
		$sBody = str_replace("{PRICE}", General::formatAmount($this->_obj_TxnInfo->getClientConfig()->getCountryConfig(), $this->_obj_TxnInfo->getAmount() ), $sBody);
		$sBody = str_replace("{CLIENT}", $this->_obj_TxnInfo->getClientConfig()->getName(), $sBody);
		/*
		// Instantiate Message Object for holding the message data which will be sent to GoMobile
		$obj_MsgInfo = GoMobileMessage::produceMessage(Constants::iMT_SMS_TYPE, $this->_obj_TxnInfo->getClientConfig()->getCountryConfig()->getID(), $this->_obj_TxnInfo->getOperator(), $this->_obj_TxnInfo->getClientConfig()->getCountryConfig()->getChannel(), $this->_obj_TxnInfo->getClientConfig()->getKeywordConfig()->getKeyword(), Constants::iMT_PRICE, $this->_obj_TxnInfo->getMobile(), utf8_decode($sBody) );
		$obj_MsgInfo->setDescription("mPoint - Receipt");
		if ($this->getCountryConfig()->getID() != 200) { $obj_MsgInfo->setSender(substr($this->_obj_TxnInfo->getClientConfig()->getName(), 0, 11) ); }

		$this->sendMT($oCI, $obj_MsgInfo, $this->_obj_TxnInfo);
		*/
	}

	/**
	 * Completes the Transaction by updating the Transaction Log with the final details for the Payment.
	 * The method will verify that the transaction hasn't accidentally been duplicated by the Payment Service Provider due to
	 * bugs or network issues.
	 * Additionally the method will insert a final entry in the Message Log with the provided debug data.
	 * The method will throw a Callback Exception wit code 1001 if the update fails.
	 *
	 * @see 	General::newMessage()
	 *
	 * @param 	integer $pspid 	Unique ID for the Payment Service Provider (PSP) mPoint used to clear the transaction
	 * @param 	integer $txnid 	Transaction ID returned by the PSP
	 * @param 	integer $cid 	Unique ID for the Credit Card the customer used to pay for the Purchase
	 * @param 	integer $sid 	Unique ID indicating that final state of the Transaction
	 * @param 	integer $sub_code_id 	Unique ID indicating sub error code of the failed Transaction
	 * @param 	integer $fee	The amount the customer will pay in fees for the Transaction. Default value 0
	 * @param 	array $debug 	Array of Debug data which should be logged for the state (optional)
	 * @return	integer
	 * @throws 	CallbackException
	 */
	public function completeTransaction($pspid, $txnid, $cid, $sid, $sub_code_id = 0, $fee=0, array $debug=null, $issuingbank=null, $sSwishPaymentID=null)
	{
		if (intval($txnid) == -1) { $sql = ""; }
		else { $sql = ", extid = '". $this->getDBConn()->escStr($txnid) ."'"; }
		if ($this->_obj_TxnInfo->getAccountID() > 0) { $sql .= ", euaid = ". $this->_obj_TxnInfo->getAccountID(); }
		else { $sql .= ", euaid = NULL"; }
		if($issuingbank != '')
		{
			 $sql .= ", issuing_bank = '".$issuingbank."'";
		}
		if(empty($sSwishPaymentID) === false)
		{
            $sql .= ", authoriginaldata = '".$sSwishPaymentID."'";
		}
		if(intval($fee) > 0)
		{
			$sql .= ", fee = ".intval($fee);
		}
		
		$sql = "UPDATE Log".sSCHEMA_POSTFIX.".Transaction_Tbl
				SET pspid = ". intval($pspid) .", cardid = ". intval($cid). $sql ."
				WHERE id = ". $this->_obj_TxnInfo->getID();
	//	if (intval($txnid) != -1) { $sql .= " AND (extid IS NULL OR extid = '' OR extid = '". $this->getDBConn()->escStr($txnid) ."')"; }
	//	echo $sql ."\n";
		$res = $this->getDBConn()->query($sql);

		// Transaction completed successfully
		if (is_resource($res) === true)
		{
				$iIsCompleteTransactionStateLogged = $this->_obj_TxnInfo->hasEitherState ( $this->getDBConn (), $sid );
				if ($iIsCompleteTransactionStateLogged > 0 && $sid == Constants::iPAYMENT_ACCEPTED_STATE) {
					$this->newMessage ( $this->_obj_TxnInfo->getID (), Constants::iPAYMENT_DUPLICATED_STATE, var_export ( $debug, true ) );
					$sid = Constants::iPAYMENT_DUPLICATED_STATE;
				} else if ($iIsCompleteTransactionStateLogged == 0 ) {
					$this->newMessage ( $this->_obj_TxnInfo->getID (), $sid, var_export ( $debug, true ) );
					if($sub_code_id != 0) {
						$this->newMessage ( $this->_obj_TxnInfo->getID (), $sub_code_id, var_export ( $debug, true ) );
					}
				}
			
		}
		// Error: Unable to complete log for Transaction
		else
		{
			$this->newMessage($this->_obj_TxnInfo->getID(), $sid, var_export($debug, true) );
			throw new CallbackException("Unable to complete log for Transaction: ". $this->_obj_TxnInfo->getID(), 1001);
		}
		return $sid;
	}
	
	/**
	 * Completes the Capture for the Transaction by updating the Transaction Log with the final details for the Payment.
	 * Additionally the method will insert a final entry in the Message Log with the provided debug data.
	 *
	 * @see 	General::newMessage()
	 *
	 * @param 	integer $amount		The amount that has been captured for the customer Transaction. Default value 0
	 * @param 	integer $fee		The amount the customer will pay in fee�s for the Transaction. Default value 0
	 * @param 	array $debug 		Array of Debug data which should be logged for the state (optional)
	 * @return	boolean
	 */
	public function completeCapture($amount, $fee=0, array $debug=null)
	{
		$sql = "UPDATE Log".sSCHEMA_POSTFIX.".Transaction_Tbl
				SET fee = ".intval($fee) ." + fee, 
					captured = ". intval($amount) ." + captured
				WHERE id = ". $this->getDBConn()->escStr($this->_obj_TxnInfo->getID() ) ."";
//		echo $sql ."\n";
		$res = $this->getDBConn()->query($sql);

		$txnPassbookObj = TxnPassbook::Get($this->getDBConn(), $this->_obj_TxnInfo->getID(), $this->_obj_TxnInfo->getClientConfig()->getID());

		// Capture completed successfully
		if (is_resource($res) === true && $this->getDBConn()->countAffectedRows($res) == 1)
		{
            $retStatus = $txnPassbookObj->updateInProgressOperations($amount, Constants::iPAYMENT_CAPTURED_STATE, Constants::sPassbookStatusDone);
            if($retStatus === TRUE)
            {
            	$this->newMessage($this->_obj_TxnInfo->getID(), Constants::iPAYMENT_CAPTURED_STATE, var_export($debug, true));
            }
			return true;
		}
		else { return false; }
	}

	/**
	 * Performs the Callback request via HTTP POST and sends the Payment Status to the Client.
	 * The method will update the message log with the progression of the Callback request.
	 * If the Callback fails the method will trigger an E_USER_ERROR and if the Callback succeeds the
	 * method will trigger an E_USER_WARNING.
	 *
	 * @param 	string $body 	HTTP Body to send as the Callback to the Client
	 * @throws 	E_USER_WARNING, E_USER_NOTICE
	 */
	protected function performCallback($body, SurePayConfig &$obj_SurePay=null, $attempt=0 ,$sid =0)
	{
		$this->newMessage($this->_obj_TxnInfo->getID(), Constants::iCB_CONSTRUCTED_STATE, $body);
		/* ========== Instantiate Connection Info Start ========== */
		$aURLInfo = parse_url($this->_obj_TxnInfo->getCallbackURL() );

		if (array_key_exists("port", $aURLInfo) === false)
		{
			if (array_key_exists("scheme", $aURLInfo) === true)
			{
				if ( $aURLInfo["scheme"] == "https") { $aURLInfo["port"] = 443; }
				else { $aURLInfo["port"] = 80; }
			}
			else { $aURLInfo["port"] = 80; }
		}
		if (array_key_exists("query", $aURLInfo) === true) { $aURLInfo["path"] .= "?". $aURLInfo["query"]; }

		$obj_ConnInfo = new HTTPConnInfo($aURLInfo["scheme"], $aURLInfo["host"], $aURLInfo["port"], 20, $aURLInfo["path"], "POST", "application/x-www-form-urlencoded");
		/* ========== Instantiate Connection Info End ========== */
		$obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);

		/* ========== Perform Callback Start ========== */
		$iCode = -1;
		try
		{
			$obj_HTTP->connect();
			$this->newMessage($this->_obj_TxnInfo->getID(), Constants::iCB_CONNECTED_STATE, "Host: ". $obj_ConnInfo->getHost() .", Port: ". $obj_ConnInfo->getPort() .", Path: ". $obj_ConnInfo->getPath() );
			// Send Callback data
			$iCode = $obj_HTTP->send($this->constHTTPHeaders(), $body);
			$obj_HTTP->disConnect();
			if (200 <= $iCode && $iCode < 300)
			{
				trigger_error("mPoint Callback request  succeeded for Transaction: ". $this->_obj_TxnInfo->getID(), E_USER_NOTICE);
				if ($sid == Constants::iPAYMENT_TIME_OUT_STATE) {
					$this->newMessage ( $this->_obj_TxnInfo->getID (), Constants::iCB_ACCEPTED_TIME_OUT_STATE, $obj_HTTP->getReplyHeader () );
				} else {
					$this->newMessage ( $this->_obj_TxnInfo->getID (), Constants::iCB_ACCEPTED_STATE, $obj_HTTP->getReplyHeader () );
				}
			}
			else
			{
				trigger_error("mPoint Callback request failed for Transaction: ". $this->_obj_TxnInfo->getID(), E_USER_WARNING);
				$this->newMessage($this->_obj_TxnInfo->getID(), Constants::iCB_REJECTED_STATE, $obj_HTTP->getReplyHeader() );
			}
		}
		// Error: Unable to establish Connection to Client
		catch (HTTPConnectionException $e)
		{
			trigger_error("mPoint Callback request failed for Transaction: ". $this->_obj_TxnInfo->getID(), E_USER_WARNING);
			$this->newMessage($this->_obj_TxnInfo->getID(), Constants::iCB_CONN_FAILED_STATE, $e->getMessage() ."(". $e->getCode() .")");
		}
		// Error: Unable to send Callback to Client
		catch (HTTPSendException $e)
		{
			trigger_error("mPoint Callback request failed for Transaction: ". $this->_obj_TxnInfo->getID(), E_USER_WARNING);
			$this->newMessage($this->_obj_TxnInfo->getID(), Constants::iCB_SEND_FAILED_STATE, $e->getMessage() ."(". $e->getCode() .")");
		}
		/* ========== Perform Callback End ========== */

		// Callback failed
		if ($iCode < 200 || 300 < $iCode)
		{
			if ( ($obj_SurePay instanceof SurePayConfig) === true && $attempt < $obj_SurePay->getMax() )
			{
				$attempt++;
				sleep($obj_SurePay->getDelay() * $attempt);
				trigger_error("mPoint Callback request retried for Transaction: ". $this->_obj_TxnInfo->getID(), E_USER_NOTICE);
				$this->newMessage($this->_obj_TxnInfo->getID(), Constants::iCB_RETRIED_STATE, "Attempt ". $attempt ." of ". $obj_SurePay->getMax() );
				$this->performCallback($body, $obj_SurePay, $attempt,$sid);
			}
			//Retrial Based On Configuration Ends
		}
	}

	/**
	 * Notifies the Client of the Payment Status by performing a callback via HTTP.
	 * The method will re-construct the data received from NetAxept after having removed the following mPoint specific fields:
	 *    - width
	 *    - height
	 *    - format
	 *    - PHPSESSID (found using PHP's session_name() function)
	 *    - language
	 *    - cardid
	 * Additionally the method will add mPoint's Unique ID for the Transaction.
	 *
	 * @param integer             $sid         Unique ID of the State that the Transaction terminated in
	 * @param array               $vars
	 * @param \SurePayConfig|null $obj_SurePay SurePay Configuration Object. Default value null
	 *
	 * @see    Callback::send()
	 * @see    Callback::getVariables()
	 *
	 */

	public function notifyClient(int $sid, array $vars, ?SurePayConfig $obj_SurePay=null)
	{
		$pspId=  "";
		$amount =(int)$vars["amount"];
		$sAdditionalData = "";
		$exp = null;
		$cardNo = 0;
		$fee = 0;
		$cardId = 0;

		if(isset($vars["transact"]) === TRUE ){
			$pspId = $vars["transact"];
		}

		if(isset($vars["expiry"]) === TRUE ){
			$exp = $vars["expiry"];
		}

        if(isset($vars["additionaldata"]) === TRUE ){
        	$sAdditionalData = $vars["additionaldata"];
        }

        if(isset($vars["cardnomask"]) === TRUE )
		{
			if(isset($vars['cardprefix']) === TRUE)
			{
			$cardno= $vars['cardprefix'] . str_replace("X", "*", substr($vars['cardnomask'], strlen($vars['cardprefix']) ) );
			}
			elseif(strpos($vars["cardnomask"], 'X'))
			{
				 $cardno = str_replace("X", "*", $vars["cardnomask"]);
			}
			else
			{
				$cardno = $vars["cardnomask"];
			}
		}

        if(isset($vars["fee"]) === TRUE )
        {
        	$fee = (int)$vars["fee"];
        }

        if(isset($vars["cardid"]) === TRUE )
        {
        	$cardId = (int)$vars["cardid"];
        }
        elseif(isset($vars["card-id"]) === TRUE)
		{
			$cardId = (int)$vars["card-id"];
		}

		$this->notifyToClient($sid, $pspId, $amount, $cardNo, $cardId, $exp, $sAdditionalData, $obj_SurePay, $fee );
	}

	/**
	 * Notifies the Client of the Payment Status by performing a callback via HTTP.
	 * The method will construct the default mPoint callback:
	 *    mpoint-id={UNIQUE ID FOR THE TRANSACTION}
	 *    &orderid={CLIENT'S ORDER ID FOR THE TRANSACTION}
	 *    &status={STATUS CODE FOR THE TRANSACTION}
	 *    &amount={TOTAL AMOUNT THE CUSTOMER WAS CHARGED FOR THE TRANSACTION without fee}
	 *    &currency={CURRENCY AMOUNT IS CHARGED IN}
	 *    &mobile={CUSTOMER'S MSISDN WHERE SMS MESSAGE CAN BE SENT TO}
	 *    &email={CUSTOMER'S EMAIL ADDRESS WHERE ORDER STATUS CAN BE SENT TO}
	 *    &operator={GOMOBILE ID FOR THE CUSTOMER'S MOBILE NETWORK OPERATOR}
	 *    &fee={AMOUNT THE USER HAS TO PAY IN FEE�S}
	 * Additionally the method will append all custom Client Variables that were sent to mPoint as part of the original request
	 * as well as the following Customer Input:
	 *    - Purchased Products
	 *    - Delivery Information
	 *    - Shipping Information
	 *
	 * @param integer             $sid    Unique ID of the State that the Transaction terminated in
	 * @param string              $pspid  The Payment Service Provider's (PSP) unique ID for the transaction
	 * @param integer             $amt    Total amount the customer will pay for the Transaction without fee
	 * @param string              $cardno The masked card number for the card that was used for the payment
	 * @param integer             $cardid mPoint's unique ID for the card type
	 * @param null                $exp
	 * @param string              $sAdditionalData
	 * @param \SurePayConfig|null $obj_SurePay
	 * @param integer             $fee    The amount the customer will pay in fee�s for the Transaction. Default value 0
	 *
	 * @throws \Exception
	 * @see    Callback::send()
	 * @see    Callback::getVariables()
	 */
	public function notifyToClient(int $sid, string $pspid, int $amt, string $cardno="", int $cardid=0, $exp=null, string $sAdditionalData="", ?SurePayConfig $obj_SurePay=null, int $fee=0): void
	{
		$sDeviceID = $this->_obj_TxnInfo->getDeviceID();
		$sEmail = $this->_obj_TxnInfo->getEMail();
		/* ----- Construct Body Start ----- */
		$sBody = "";
		$sBody .= "mpoint-id=". $this->_obj_TxnInfo->getID();
		if(strlen($sAdditionalData) > 0)
		$sBody .= "&".$sAdditionalData;
		$sBody .= "&orderid=". urlencode($this->_obj_TxnInfo->getOrderID() );
		if($this->hasTransactionFailureState($sid) === true)
		{
            $sBody .= "&status=". substr($sid, 0 ,4);
            $sBody .= "&errorcode=". $sid;
		}
		else
		{
            $sBody .= "&status=". $sid;
		}
		$sBody .= "&desc=". urlencode($this->getStatusMessage($sid) );
		$sBody .= "&amount=". $amt;
		$sBody .= "&fee=". intval($fee);
		$sBody .= "&currency=". urlencode($this->_obj_TxnInfo->getCurrencyConfig()->getCode() );
		$sBody .= "&mobile=". urlencode($this->_obj_TxnInfo->getMobile() );
		$sBody .= "&operator=". urlencode($this->_obj_TxnInfo->getOperator() );
		$sBody .= "&language=". urlencode($this->_obj_TxnInfo->getLanguage() );
		if (intval($cardid) > 0) { $sBody .= "&card-id=". $cardid; }
		if (empty($cardno) === false) { $sBody .= "&card-number=". urlencode($cardno); }
		if ($this->_obj_TxnInfo->getClientConfig()->sendPSPID() === true)
		{
			$pspId = $this->_obj_TxnInfo->getPSPID();
			$sBody .= "&pspid=". urlencode($pspid);
			$sBody .= "&psp-name=". urlencode($this->getPSPName($pspId));
        }
		if ( strlen($this->_obj_TxnInfo->getDescription() ) > 0) { $sBody .= "&description=". urlencode($this->_obj_TxnInfo->getDescription() ); }
		$sBody .= $this->getVariables();
		$sBody .= "&hmac=". urlencode($this->_obj_TxnInfo->getHMAC() );
		if(empty($sDeviceID) === false)
		{
		$sBody .= "&device-id=". urlencode($sDeviceID);
		}
		if(empty($sEmail) === false)
		{
		$sBody .= "&email=". urlencode($sEmail);
		}
		if(empty($exp)===false)
		{
			$sBody .= "&expiry=". $exp;
		}
        $sBody .= "&session-id=". $this->_obj_TxnInfo->getSessionId();
		/* Adding customer Info as part of the callback query params */
		if (($this->_obj_TxnInfo->getAccountID() > 0) === true )
        {
            $obj_CustomerInfo = CustomerInfo::produceInfo($this->getDBConn(), $this->_obj_TxnInfo->getAccountID());
            $sBody .= "&customer-country-id=". $obj_CustomerInfo->getCountryID();
        }
        if (strlen($this->_obj_TxnInfo->getApprovalCode()) >0){
        	$sBody .= "&approval-code=". $this->_obj_TxnInfo->getApprovalCode();
        }

		if (($this->_obj_TxnInfo->getWalletID() > 0) === true )
		{
        	$sBody .= "&wallet-id=". $this->_obj_TxnInfo->getWalletID();
        }

		$objb_getPaymentMethod =  $this->_obj_TxnInfo->getPaymentMethod($this->getDBConn());
        $sBody .= '&payment-method=' . $objb_getPaymentMethod->PaymentMethod;
		$sBody .= '&payment-type=' . $objb_getPaymentMethod->PaymentType;
		$sBody .= '&payment-provider-id=' . $this->_obj_TxnInfo->getPSPID();

        $shortCode = $this->_obj_PSPConfig->getAdditionalProperties(Constants::iInternalProperty, 'SHORT-CODE');
        if($shortCode !== false)
        {
        	$sBody .= '&short-code='. $shortCode;
        }

        $aTxnAdditionalData = $this->_obj_TxnInfo->getAdditionalData();
        if($aTxnAdditionalData !== null)
        {
            foreach ($aTxnAdditionalData as $key => $value)
            {
				$sBody .= '&' .$key. '=' . $value;
            }
        }

        $dateTime = new DateTime($this->_obj_TxnInfo->getCreatedTimestamp());
		$sBody .= '&date-time=' . $dateTime->format('c');
		$timeZone = $this->_obj_TxnInfo->getClientConfig()->getAdditionalProperties(Constants::iInternalProperty, 'TIMEZONE');
		if($timeZone !== null && $timeZone !== '' && $timeZone !== false )
		{
			$dateTime->setTimezone(new DateTimeZone($timeZone));
			$sBody .= '&local-date-time=' . $dateTime->format('c');
		}
		if (strlen($this->_obj_TxnInfo->getIssuingBankName()) >0){
			$sBody .= "&issuing-bank=". $this->_obj_TxnInfo->getIssuingBankName();
		}
        /* ----- Construct Body End ----- */
        $this->performCallback($sBody, $obj_SurePay ,0 ,$sid);
	}

	/**
	 * Function used to make a callback to the foreign Exchange instance for updating it with the transaction status.
	 * @param array $aStateId Transaction state array
	 * @param array $aCI Connection Information Array
	 */
	public function notifyForeignExchange(array $aStateId,$aCI)
	{
		//Refreshing txnInfo object to reflect updated values after payment operation performed 
		$this->_obj_TxnInfo = TxnInfo::produceInfo($this->_obj_TxnInfo->getID(), $this->getDBConn());

		$iForeignExchangeId = $this->_obj_TxnInfo->getExternalRef(Constants::iForeignExchange,$this->_obj_TxnInfo->getPSPID());
		if($iForeignExchangeId !==null && empty($iForeignExchangeId) === false && sizeof($aStateId)>0)
		{
			$this->_obj_TxnInfo->produceOrderConfig($this->getDBConn());
			$b = '<?xml version="1.0" encoding="UTF-8"?>';
			$b .= '<root>';
			$b .= '<callback>';
			$b .= '<clientId>' . $this->_obj_TxnInfo->getClientConfig()->getID() . '</clientId>';
			$b .= '<account>' . $this->_obj_TxnInfo->getClientConfig()->getAccountConfig()->getID() . '</account>';
			$b .= '<clientConfig>';
			$b .= '<additionalConfig>';
			foreach ($this->_obj_TxnInfo->getClientConfig()->getAdditionalProperties(Constants::iPrivateProperty) as $aAdditionalProperty)
			{
				$b .= '<property>';
				$b .= '<name>' . $aAdditionalProperty['key'] . '</name>';
				$b .= '<value>' . $aAdditionalProperty['value'] . '</value>';
				$b .= '</property>';
			}
			$b .= '</additionalConfig>';
			$b .= '</clientConfig>';
			$b .= $this->_obj_PSPConfig->toAttributeLessXML(Constants::iPrivateProperty);
			$s = '<status>';
			foreach ($aStateId as $iStateId) {
				$s .= '<code>' . $iStateId . '</code>';
			}

			$s .= '</status>';

			$s .= '<cfxId>' . $iForeignExchangeId . '</cfxId>';
			$aExcludeNode = array();
			array_push($aExcludeNode, 'fee');
			array_push($aExcludeNode, 'price');
			array_push($aExcludeNode, 'points');
			array_push($aExcludeNode, 'reward');
			array_push($aExcludeNode, 'mobile');

			$b .= str_replace("</transaction>", $s . "</transaction>", $this->_obj_TxnInfo->toAttributeLessXML($aExcludeNode));
			$b .= '</callback>';
			$b .= '</root>';
			$this->newMessage($this->_obj_TxnInfo->getID(), Constants::iACKFX_CONSTRUCTED_STATE, $s);

			$aURLInfo = parse_url($this->getClientConfig()->getMESBURL());
			$obj_ConnInfo = new HTTPConnInfo($aCI["protocol"], $aURLInfo["host"], $aCI["port"], $aCI["timeout"], $aCI["paths"]["callback"], $aCI["method"], $aCI["contenttype"], $this->getClientConfig()->getUsername(), $this->getClientConfig()->getPassword());
			try
			{
				$obj_HTTP = $this->send($obj_ConnInfo, $this->constHTTPHeaders(), $b);
				$iCode = $obj_HTTP->getReturnCode();
				if (intval($iCode) === 200 || intval($iCode) === 202)
				{
					trigger_error("mPoint Callback request to Foreign Exchange succeeded for Transaction: " . $this->_obj_TxnInfo->getID(), E_USER_NOTICE);
					$this->newMessage($this->_obj_TxnInfo->getID(), Constants::iACKFX_ACCEPTED_STATE, $obj_HTTP->getReplyHeader());

				}
				else
				{
					trigger_error("mPoint Callback request to Foreign Exchange failed for Transaction: " . $this->_obj_TxnInfo->getID(), E_USER_WARNING);
					$this->newMessage($this->_obj_TxnInfo->getID(), Constants::iACKFX_REJECTED_STATE, $obj_HTTP->getReplyHeader());
				}

			} // Error: Unable to establish Connection to Client
			catch (HTTPConnectionException $e)
			{
				trigger_error("mPoint Callback request to Foreign Exchange failed for Transaction: " . $this->_obj_TxnInfo->getID(), E_USER_WARNING);
				$this->newMessage($this->_obj_TxnInfo->getID(), Constants::iACKFX_CONN_FAILED_STATE, $e->getMessage() . "(" . $e->getCode() . ")");
			} // Error: Unable to send Callback to Client
			catch (HTTPSendException $e)
			{
				trigger_error("mPoint Callback request to Foreign Exchange failed for Transaction: " . $this->_obj_TxnInfo->getID(), E_USER_WARNING);
				$this->newMessage($this->_obj_TxnInfo->getID(), Constants::iACKFX_SEND_FAILED_STATE, $e->getMessage() . "(" . $e->getCode() . ")");
			}
		}
	}

	/*
	 * Function to verify if the transaction has a failure state
	 *
	 * @param	interger $sid	Incoming state ID to mPoint
	 * @return string
	 * */
	public function hasTransactionFailureState($sid)
	{
        $sParentStatusCode = substr($sid, 0 ,4);
        return (in_array($sParentStatusCode, array(Constants::iPAYMENT_DECLINED_STATE, Constants::iPAYMENT_REJECTED_STATE)) === true);
	}

    /**
     * Returns the Status Messgae for mPoint's internal status codes
     *
     * @param 	integer $sid	mPoint ststua code
     * @return 	string
     */
	public function getStatusMessage($sid)
	{
        $sql = "SELECT name
				FROM Log".sSCHEMA_POSTFIX.".State_Tbl
				WHERE id = ". intval($sid);
		//echo $sql ."\n";
        $RS = $this->getDBConn($sql)->getName($sql);

        return $RS["NAME"];
	}


	/**
	 * Retrieves all Custom Client Variables and Customer Input from the Database and serialises them
	 * into a urlencoded string.
	 * The method will return each variable class with the following prefix:
	 * 	- Custom Client Variable: var_
	 * 	- Purchased Products: prod_
	 * 	- Delivery Information: addr_
	 * 	- Shipping Information: ship_
	 * For Purchased Producs the following data will be returned for each product:
	 * 	- prod_{ID}_name, The name of the Product Purchased
	 * 	- prod_{ID}_quantity, The number of Units Purchased of the Product
	 * 	- prod_{ID}_price, The price for each unit
	 * For Delivery Information the following data will be returned:
	 * 	- addr_name, The recipient's name
	 * 	- addr_company, The company or C/O of the recipient
	 * 	- addr_street, The streetname where the purchase should be delivered
	 * 	- addr_zipcode, The Zip Code identifying the region where the purchase should be delivered
	 * 	- addr_city, The City the purchase should be delivered in
	 * 	- addr_delivery-date, The date the purchase should be delivered
	 * For Shipping Information the following data will be returned:
	 * 	- ship_company
	 * 	- ship_price
	 *
	 * @see 	Constants::iCLIENT_VARS_STATE
	 * @see 	Constants::iPRODUCTS_STATE
	 * @see 	Constants::iDELIVERY_INFO_STATE
	 * @see 	Constants::iSHIPPING_INFO_STATE
	 * @see 	General::getMessageData()
	 *
	 * @return 	string
	 */
	protected function getVariables($transactionid = null)
	{
		if($transactionid === null)
		{
			$transactionId = $this->_obj_TxnInfo->getID();
		}
		else
		{
			$transactionId = intval($transactionid);
		}
		// Get custom Client Variables and Customer Input
		$aClientVars = $this->getMessageData($transactionId, Constants::iCLIENT_VARS_STATE);
		$aProducts = $this->getMessageData($transactionId, Constants::iPRODUCTS_STATE);
		$aDeliveryInfo = $this->getMessageData($transactionId, Constants::iDELIVERY_INFO_STATE);
		$aShippingInfo = $this->getMessageData($transactionId, Constants::iSHIPPING_INFO_STATE);

		$sBody = "";
		// Add custom Client Variables to Callback Body
		foreach ($aClientVars as $name => $value)
		{
			$sBody .= "&". $name ."=". urlencode($value);
		}
		// Add Purchased Products to Callback Body
		if (count($aProducts) > 0)
		{
			foreach ($aProducts["names"] as $key => $name)
			{
				$sBody .= "&prod_". $key ."_name=". urlencode($name);
				$sBody .= "&prod_". $key ."_quantity=". intval($aProducts["quantities"][$key]);
				$sBody .= "&prod_". $key ."_price=". intval($aProducts["prices"][$key]);
			}
		}
		// Add Delivery Information to Callback Body
		foreach ($aDeliveryInfo as $name => $value)
		{
			$sBody .= "&addr_". $name ."=". urlencode($value);
		}
		// Add Shipping Information to Callback Body
		foreach ($aShippingInfo as $name => $value)
		{
			$sBody .= "&ship_". $name ."=". urlencode($value);
		}

		return $sBody;
	}

	/**
	 * Performs the HTTP Request to the specified URL using the provided headers and body
	 *
	 * @see		HTTPConnInfo
	 * @see		HTTPClient
	 *
	 * @param 	string|HTTPConnInfo $url Absolute URL the request should be made to
	 * @param 	string $h				 HTTP Headers to send as part of the request
	 * @param 	string $b				 HTTP Body to send as part of the request
	 * @return	HTTPClient				 Reference to the created HTTP Client object
	 * @throws 	HTTPException
	 */
	public function &send($url, $h, $b, $un="", $pw="")
	{
		if ( ($url instanceof HTTPConnInfo) === true)
		{
			$obj_ConnInfo = $url;
		}
		else
		{
			/* ========== Instantiate Connection Info Start ========== */
			$aURLInfo = parse_url($url);

			if (array_key_exists("port", $aURLInfo) === false)
			{
				if ($aURLInfo["scheme"] == "https") { $aURLInfo["port"] = 443; }
				else { $aURLInfo["port"] = 80; }
			}
			if (array_key_exists("query", $aURLInfo) === true) { $aURLInfo["path"] .= "?". $aURLInfo["query"]; }

			$obj_ConnInfo = new HTTPConnInfo($aURLInfo["scheme"], $aURLInfo["host"], $aURLInfo["port"], 60, $aURLInfo["path"], (empty($b) === true ? "GET" : "POST"), "application/x-www-form-urlencoded", $un, $pw);
		}

		/* ========== Instantiate Connection Info End ========== */
		$obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);

		/* ========== Perform HTTP request Start ========== */
		$obj_HTTP->connect();
		$iCode = $obj_HTTP->send($h, $b);
		$obj_HTTP->disConnect();
		/* ========== Perform HTTP request End ========== */

		return $obj_HTTP;
	}

	/**
	 * Returns the Client's Merchant Account ID for the PSP
	 *
	 * @param 	integer $clid	Unique ID of the Client whose Merchant Account should be found
	 * @param 	integer $pspid	Unique ID for the PSP the Merchant Account should be found for
	 * @param	boolean	$sc		Return the Merchant Login used for authorizing a stored card, defaults to false
	 * @return 	string
	 */
	public function getMerchantAccount($clid, $pspid, $sc=false)
	{
		$sql = "SELECT name
				FROM Client".sSCHEMA_POSTFIX.".MerchantAccount_Tbl
				WHERE clientid = ". intval($clid) ." AND pspid = ". intval($pspid) ." AND enabled = '1'";
		if ($sc === true) { $sql .= " AND stored_card = '1'"; }
		else { $sql .= " AND (stored_card = '0' OR stored_card IS NULL)"; }
//		echo $sql ."\n";
		$RS = $this->getDBConn($sql)->getName($sql);

		return $RS["NAME"];
	}
	/**
	 * Returns the Client's Merchant Login (Username / Password) for the PSP
	 *
	 * @param 	integer $clid	Unique ID of the Client whose Merchant Account should be found
	 * @param 	integer $pspid	Unique ID for the PSP the Merchant Account should be found for
	 * @param	boolean	$sc		Return the Merchant Login used for authorizing a stored card, defaults to false
	 * @return 	array
	 */
	public function getMerchantLogin($clid, $pspid, $sc=false)
	{
		$sql = "SELECT username, passwd AS password , name
				FROM Client".sSCHEMA_POSTFIX.".MerchantAccount_Tbl
				WHERE clientid = ". intval($clid) ." AND pspid = ". intval($pspid) ." AND enabled = '1'";
		if ($sc === true) { $sql .= " AND stored_card = '1'"; }
		else { $sql .= " AND (stored_card = '0' OR stored_card IS NULL)"; }
//		echo $sql ."\n";

		$RS = $this->getDBConn($sql)->getName($sql);

		return is_array($RS) === true ? array_change_key_case($RS, CASE_LOWER) : array();
	}

	/**
	 * Returns the Client's Merchant Sub-Account ID for the PSP
	 *
	 * @param 	integer $accid	Unique ID for the Account
	 * @param 	integer $pspid	Unique ID for the PSP the Merchant Account should be found for
	 * @return 	string
	 */
	public function getMerchantSubAccount($accid, $pspid)
	{
		$sql = "SELECT name
				FROM Client".sSCHEMA_POSTFIX.".MerchantSubAccount_Tbl
				WHERE accountid = ". intval($accid) ." AND pspid = ". intval($pspid) ." AND enabled = '1'";
//		echo $sql ."\n";
		$RS = $this->getDBConn($sql)->getName($sql);

		return $RS["NAME"];
	}

	/**
	 * Returns the specified PSP's currency code for the provided country
	 *
	 * @param 	integer $cid	Unique ID for the Country that the Currency should be found in
	 * @param 	integer $pspid	Unique ID for the PSP that the currency code should be found for
	 * @return 	string
	 */
	public function getCurrency($cid, $pspid)
	{
		$sql = "SELECT name
				FROM System".sSCHEMA_POSTFIX.".PSPCurrency_Tbl
				WHERE currencyid = ". intval($cid) ." AND pspid = ". intval($pspid) ." AND enabled = '1'";
//		echo $sql ."\n";
		$RS = $this->getDBConn($sql)->getName($sql);

		return $RS["NAME"];
	}

    /**
     * Returns the specified PSP's name
     *
     * @param 	integer $pspid	Unique ID for the PSP
     * @return 	string
     */
    public function getPSPName($pspid)
    {
        $sql = "SELECT name
				FROM System".sSCHEMA_POSTFIX.".PSP_Tbl
				WHERE id = ". intval($pspid) ." AND enabled = '1'";
//		echo $sql ."\n";
        $RS = $this->getDBConn($sql)->getName($sql);

        return $RS["NAME"];
    }

	/**
	 * Static method for retrieving mPoint's unique Transaction ID based on the Client's Order Number and
	 * the Payment Service Provider who processed the payment transction.
	 * The method returns -1 if mPoint's unique Transaction ID could not be found.
	 *
	 * @param 	RDB $oDB			Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param 	string $orderno		Client's Order Number
	 * @param 	integer $pspid		mPoint's unique ID for the Payment Service Provider who processed the payment transction
	 * @return 	integer
	 */
	public static function getTxnIDFromOrderNo(RDB &$oDB, $orderno, $pspid)
	{
		$sql = "SELECT Max(id) AS id
				FROM Log".sSCHEMA_POSTFIX.".Transaction_Tbl
				WHERE orderid = '". $oDB->escStr($orderno) ."' AND pspid = ". intval($pspid);
//		echo $sql ."\n";
		$RS = $oDB->getName($sql);

		return is_array($RS) === true && intval($RS["ID"]) > 0? $RS["ID"] : -1;
	}

	/**
	 * Static method for retrieving mPoint's unique Transaction ID based on the Client's Order Number and
	 * the Payment Service Provider who processed the payment transction.
	 * The method returns -1 if mPoint's unique Transaction ID could not be found.
	 *
	 * @param 	RDB $oDB			Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param 	string $extid		The PSP's transaction ID
	 * @param 	integer $pspid		mPoint's unique ID for the Payment Service Provider who processed the payment transction
	 * @return 	integer
	 */
	public static function getTxnIDFromExtID(RDB &$oDB, $extid, $pspid)
	{
		$sql = "SELECT Max(id) AS id
				FROM Log".sSCHEMA_POSTFIX.".Transaction_Tbl
				WHERE extid = '". $oDB->escStr($extid) ."' AND pspid = ". intval($pspid);
//		echo $sql ."\n";
		$RS = $oDB->getName($sql);

		return is_array($RS) === true && intval($RS["ID"]) > 0? $RS["ID"] : -1;
	}


	public static function producePSP(RDB $obj_DB, ? TranslateText $obj_Txt, TxnInfo $obj_TxnInfo, array $aConnInfo, PSPConfig $obj_PSPConfig=null)
	{
		if (isset($obj_PSPConfig) == true && intval($obj_PSPConfig->getID() ) > 0) { $iPSPID = $obj_PSPConfig->getID(); }
		else { $iPSPID = $obj_TxnInfo->getPSPID(); }

		switch ($iPSPID)
		{
		case (Constants::iDIBS_PSP):
			return new DIBS($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["dibs"]);
		case (Constants::iWORLDPAY_PSP):
			return new WorldPay($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["worldpay"]);
		case (Constants::iWANNAFIND_PSP):
			return new WannaFind($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["wannafind"]);
		case (Constants::iNETAXEPT_PSP):
			return new NetAxept($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["netaxept"]);
		case (Constants::iMOBILEPAY_PSP):
			return new MobilePay($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["mobilepay"]);
		case (Constants::iADYEN_PSP):
			return new Adyen($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["adyen"]);
		case (Constants::iDSB_PSP):
			return new DSB($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["dsb"], $obj_PSPConfig);
		case (Constants::iVISA_CHECKOUT_PSP) :
			return new VISACheckout($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["visa-checkout"]);
		case (Constants::iAPPLE_PAY_PSP):
			return new ApplePay($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["apple-pay"]);
		case (Constants::iCPG_PSP):
			return new CPG($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["cpg"]);
		case (Constants::iMASTER_PASS_PSP):
			return new MasterPass($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["masterpass"]);
		case (Constants::iAMEX_EXPRESS_CHECKOUT_PSP):
			return new AMEXExpressCheckout($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["amex-express-checkout"]);
		case (Constants::iWIRE_CARD_PSP):
			return new WireCard($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["wire-card"]);
		case (Constants::iDATA_CASH_PSP):
			return new DataCash($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["data-cash"]);
		case (Constants::iMADA_MPGS_PSP):
			return new MadaMpgs($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["mada-mpgs"]);
		case (Constants::iGLOBAL_COLLECT_PSP):
			return new GlobalCollect($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["global-collect"]);
		case (Constants::iSECURE_TRADING_PSP):
			return new SecureTrading($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["secure-trading"]);
		case (Constants::iPAYFORT_PSP):
			return new PayFort($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["payfort"]);
		case (Constants::iPAYPAL_PSP):
			return new PayPal($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["paypal"]);
		case (Constants::iCCAVENUE_PSP):
			return new CCAvenue($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["ccavenue"]);
		case (Constants::i2C2P_PSP):
			return new CCPP($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["2c2p"]);
		case (Constants::iMAYBANK_PSP):
			return new MayBank($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["maybank"]);			
		case (Constants::iPUBLIC_BANK_PSP):
			return new PublicBank($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["public-bank"]);
		case (Constants::iALIPAY_PSP):
	        return new AliPay($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["alipay"]);
		case (Constants::iQIWI_PSP):
			return new Qiwi($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["qiwi"]);
		case (Constants::iPOLI_PSP):
            return new Poli($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["poli"]);
		case (Constants::iKLARNA_PSP):
				return new Klarna($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["klarna"]);
        case (Constants::iMVAULT_PSP):
            return new MVault($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["mvault"]);
        case (Constants::iNETS_ACQUIRER):
            return new Nets($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["nets"]);
        case (Constants::iTRUSTLY_PSP):
            	return new Trustly($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["trustly"]);
        case (Constants::iPAY_TABS_PSP):
        	return new PayTabs($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["paytabs"]);
        case (Constants::i2C2P_ALC_PSP):
        		return new CCPPALC($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["2c2p-alc"]);
        case (Constants::iALIPAY_CHINESE_PSP):
                return new AliPayChinese($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["alipay-chinese"]);
        case (Constants::iCITCON_PSP):
                return new Citcon($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["citcon"]);
        case (Constants::iPPRO_GATEWAY):
            return new PPRO($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["ppro"]);
        case (Constants::iAMEX_ACQUIRER):
                return new Amex($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["amex"]);
        case (Constants::iCHUBB_PSP):
            return new CHUBB($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["chubb"]);
        case (Constants::iUATP_ACQUIRER):
            return new UATP($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["uatp"]);
		case (Constants::iUATP_CARD_ACCOUNT):
            return new UATPCardAccount($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["uatp"]);
        case (Constants::iEGHL_PSP):
            return new EGHL($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["eghl"]);
        case (Constants::iGOOGLE_PAY_PSP) :
            return new GooglePay($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["google-pay"]);
        case (Constants::iCHASE_ACQUIRER):
            return new Chase($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["chase"]);
        case (Constants::iPAYU_PSP):
            return new PayU($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["payu"]);
		case (Constants::iCielo_ACQUIRER):
            return new Cielo($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["cielo"]);
		case (Constants::iGlobal_Payments_PSP):
			return new GlobalPayments($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["global-payments"]);
		case (Constants::iVeriTrans4G_PSP):
		    return new VeriTrans4G($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["veritrans4g"]);
        case (Constants::iEZY_PSP):
			return new EZY($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["ezy"]);
		case (Constants::iCellulant_PSP):
				return new Cellulant($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["cellulant"]);
		case (Constants::iDragonPay_AGGREGATOR):
		    return new DragonPay($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["dragonpay"]);
        case (Constants::iFirstData_PSP):
			return new FirstData($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["first-data"]);
        case (Constants::iCyberSource_PSP):
			return new CyberSource($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["cybersource"]);
        case (Constants::iSWISH_APM):
            return new SWISH($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["swish"]);
        case (Constants::iPAYMAYA_WALLET):
            return new Paymaya($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["paymaya"]);
		case (Constants::iGRAB_PAY_PSP):
			return new GrabPay($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["grabpay"]);
		case (Constants::iCEBUPAYMENTCENTER_APM):
			return new CebuPaymentCenter($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["grabpay"]);

		default:
 			throw new CallbackException("Unkown Payment Service Provider: ". $obj_TxnInfo->getPSPID() ." for transaction: ". $obj_TxnInfo->getID(), 1001);
		}
	}

	public abstract function getPSPID();
	
	/**
	 * Returns exponent of currency of country on given country-id and psp-id.
	 *
	 * @param 	integer $cid	Unique ID for the Country that the Currency should be found in
	 * @param 	integer $pspid	Unique ID for the PSP that the currency code should be found for
	 * @return 	int
	 */
	
	public function getCurrencyExponent($cid, $pspid)
	{
		$currency_name = $this->getCurrency($cid, $pspid);
		
		$sql = "SELECT decimals
				FROM System".sSCHEMA_POSTFIX.".Country_Tbl
				WHERE id = ". intval($cid) ." AND currency = '". $currency_name ."' AND enabled = '1'";
		//		echo $sql ."\n";
		$RS = $this->getDBConn($sql)->getName($sql);
		
		return $RS["DECIMALS"];
	}

    function retryCallback($body, SurePayConfig &$obj_SurePay=null, $attempt=0){
        $this->performCallback($body,$obj_SurePay, $attempt);
    }

    public function updateSessionState($sid, $pspid, $amt, $cardno="", $cardid=0, $exp=null, $sAdditionalData="", SurePayConfig $obj_SurePay=null, $fee=0 )
    {
        $sessionObj = $this->getTxnInfo()->getPaymentSession();
        $isStateUpdated = $sessionObj->updateState();
        if($isStateUpdated == 1) {
            $checkSessionCallback = $sessionObj->checkSessionCompletion();
            if (empty($checkSessionCallback) === true) {
				$sDeviceID = $this->_obj_TxnInfo->getDeviceID();
				$sEmail = $this->_obj_TxnInfo->getEMail();
				/* ----- Construct Body Start ----- */
				$sBody = "";
				$sBody .= "session-id=" . $this->_obj_TxnInfo->getSessionId();
				$sBody .= "&orderid=" . urlencode($this->_obj_TxnInfo->getOrderID());
				$sBody .= "&status=" . $sessionObj->getStateId();
				$sBody .= "&amount=" . $sessionObj->getAmount();
				$sBody .= "&mobile=" . urlencode($this->_obj_TxnInfo->getMobile());
				$sBody .= "&operator=" . urlencode($this->_obj_TxnInfo->getOperator());
				$sBody .= "&language=" . urlencode($this->_obj_TxnInfo->getLanguage());
				if (intval($cardid) > 0) {
					$sBody .= "&card-id=" . $cardid;
				}
				if (empty($cardno) === false) {
					$sBody .= "&card-number=" . urlencode($cardno);
				}
				if ($this->_obj_TxnInfo->getClientConfig()->sendPSPID() === true) {
					$sBody .= "&pspid=" . urlencode($pspid);
				}
				if (strlen($this->_obj_TxnInfo->getDescription()) > 0) {
					$sBody .= "&description=" . urlencode($this->_obj_TxnInfo->getDescription());
				}
				$sBody .= $this->getVariables();
				if (empty($sDeviceID) === false) {
					$sBody .= "&device-id=" . urlencode($sDeviceID);
				}
				if (empty($sEmail) === false) {
					$sBody .= "&email=" . urlencode($sEmail);
				}
				if (empty($exp) === false) {
					$sBody .= "&expiry=" . $exp;
				}

				/* Adding customer Info as part of the callback query params */
				if (($this->_obj_TxnInfo->getAccountID() > 0) === true) {
					$obj_CustomerInfo = CustomerInfo::produceInfo($this->getDBConn(), $this->_obj_TxnInfo->getAccountID());
					$sBody .= "&customer-country-id=" . $obj_CustomerInfo->getCountryID();
				}

				if (strlen($this->_obj_TxnInfo->getIssuingBankName()) > 0)
				{
					$sBody .= "&issuing-bank=" . urlencode($this->_obj_TxnInfo->getIssuingBankName());
				}


				$aTransaction = $this->_obj_TxnInfo->getPaymentSession()->getTransactions();

				$aTransactionData = [];
				$aTransactionData['transaction-data'] = [];
				foreach ($aTransaction as $transactionId)
				{
					$transactionData =[];
					$objTransaction = TxnInfo::produceInfo($transactionId, $this->getDBConn());

					// TransactionData array
					$transactionData['status']= $objTransaction->getLatestPaymentState($this->getDBConn());
					$transactionData['hmac']= $objTransaction->getHMAC();
					$transactionData['product-type']= $objTransaction->getProductType();
					$transactionData['amount']= $objTransaction->getAmount();
					$transactionData['currency']= $objTransaction->getCurrencyConfig()->getCode();
					$transactionData['fee']= $objTransaction->getFee();
					$transactionData['issuer-approval-code']= $objTransaction->getApprovalCode();
					if (intval($objTransaction->getCardID()) > 0)
					{
						$transactionData['card-id'] = $objTransaction->getCardID();
					}
					$cardMask = $objTransaction->getCardMask();
					if (empty($cardMask) === false)
					{
						$transactionData['card-number'] = $cardMask;
					}
					if ($objTransaction->getClientConfig()->sendPSPID() === true)
					{
						$transactionData['pspid']= $objTransaction->getExternalID();
						$transactionData['psp-name']= $this->getPSPName($objTransaction->getPSPID());
        			}
					if ($objTransaction->getDescription() !== '')
					{
						$transactionData['description'] = $objTransaction->getDescription();
					}
					$sVariables = $this->getVariables();
					if($sVariables !== '')
					{
						$aVariables = [];
						parse_str($sVariables, $aVariables);
						array_push($transactionData[$transactionId], $aVariables);
					}

					$sDeviceID = $objTransaction->getDeviceID();
					if(empty($sDeviceID) === false)
					{
						$transactionData['device-id']=$sDeviceID;
					}

					$expiry = $objTransaction->getCardExpiry();
					if(empty($expiry)===false)
					{
						$transactionData['expiry'] = $expiry;
					}

					if ($objTransaction->getApprovalCode() !== '')
					{
        				$transactionData['approval-code']= $objTransaction->getApprovalCode();
        			}

					if (($objTransaction->getWalletID() > 0) === true )
					{
        				$transactionData['wallet-id']= $objTransaction->getWalletID();
        			}

					$objb_getPaymentMethod = $objTransaction->getPaymentMethod($this->getDBConn());
					$transactionData['payment-method'] = $objb_getPaymentMethod->PaymentMethod;
					$transactionData['payment-type'] = $objb_getPaymentMethod->PaymentType;
					$transactionData['payment-provider-id'] = $objTransaction->getPSPID();

					$shortCode = $this->getAdditionalPropertyFromDB('SHORT-CODE', $objTransaction->getClientConfig()->getID(),$objTransaction->getPSPID());
        			if($shortCode !== false)
        			{
        				$transactionData['short-code']=  $shortCode;
        			}

        			$aTxnAdditionalData = $objTransaction->getAdditionalData();
        			if($aTxnAdditionalData !== null)
        			{
            			foreach ($aTxnAdditionalData as $key => $value)
            			{
            				if($key !== '')
            				{
								$transactionData[$key] = $value;
							}
            			}
        			}

        			$dateTime = new DateTime($this->_obj_TxnInfo->getCreatedTimestamp());
					$transactionData['date-time']= $dateTime->format('c');
					$timeZone = $this->_obj_TxnInfo->getClientConfig()->getAdditionalProperties(Constants::iInternalProperty,'TIMEZONE');
					if($timeZone !== null && $timeZone !== '' && $timeZone !== false )
					{
						$dateTime->setTimezone(new DateTimeZone($timeZone));
						$transactionData['local-date-time'] = $dateTime->format('c');
					}

					if (strlen($this->_obj_TxnInfo->getIssuingBankName()) > 0)
					{
						$transactionData['issuing-bank'] =  $this->_obj_TxnInfo->getIssuingBankName();
					}

					$aTransactionData['transaction-data'][$transactionId] = $transactionData;
				}

				$sBody .= '&' .http_build_query($aTransactionData);

				 if ($sessionObj->getStateId() !== Constants::iSESSION_CREATED)
				{
					$iSessionStateValidation = $this->_obj_TxnInfo->hasEitherState($this->getDBConn(), $sessionObj->getStateId());
					if ($iSessionStateValidation !== 1)
					{
						$this->newMessage($this->_obj_TxnInfo->getID(), $sessionObj->getStateId(), $sBody);
						if ($sessionObj->getPendingAmount() === 0)
						{
							$this->performCallback($sBody, $obj_SurePay);
						}
					}
				}
        	}
        }
    }

    public function getCaptureMethod()
	{
		if($this->_iCaptureMethod === null) {
			$sql = "SELECT capture_method FROM client" . sSCHEMA_POSTFIX . ".cardaccess_Tbl
				WHERE pspid = " . $this->_obj_TxnInfo->getPSPID() . " 
				AND cardid = " . $this->_obj_TxnInfo->getCardID() . "  			
				AND clientid = " . $this->_obj_TxnInfo->getClientConfig()->getID() . "  			
				AND (countryid = " . $this->_obj_TxnInfo->getCountryConfig()->getID() ." 
				OR countryid IS NULL) AND enabled = '1'";
			$res = $this->getDBConn()->query($sql);

			if (is_resource($res) === true) {
				while ($RS = $this->getDBConn()->fetchName($res) )
                {
                    $this->_iCaptureMethod =  $RS['CAPTURE_METHOD'];
                }
			}
		}
		return $this->_iCaptureMethod;
	}

	protected function updateTxnInfoObjectUsingId(int $id)
	{
		$oldPSPId = $this->_obj_TxnInfo->getPSPID();
		$this->_obj_TxnInfo = TxnInfo::produceInfo( $id, $this->getDBConn());
		$this->_obj_TxnInfo->produceOrderConfig($this->getDBConn());

		if($oldPSPId !=  $this->_obj_TxnInfo->getPSPID()) {
			$this->_obj_PSPConfig = PSPConfig::produceConfig($oDB, $this->_obj_TxnInfo->getClientConfig()->getID(), $this->_obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), $this->_obj_TxnInfo->getPSPID());
		}
		$this->setClientConfig($this->_obj_TxnInfo->getClientConfig());
	}
}
?>
