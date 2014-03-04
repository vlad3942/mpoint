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
class Callback extends EndUserAccount
{
	/**
	 * Data object with the Transaction Information
	 *
	 * @var TxnInfo
	 */
	private $_obj_TxnInfo;

	/**
	 * Default Constructor.
	 *
	 * @param	RDB $oDB				Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param	TranslateText $oTxt 	Text Translation Object for translating any text into a specific language
	 * @param 	TxnInfo $oTI 			Data object with the Transaction Information
	 */
	public function __construct(RDB &$oDB, TranslateText &$oTxt, TxnInfo &$oTI)
	{
		parent::__construct($oDB, $oTxt, $oTI->getClientConfig() );

		$this->_obj_TxnInfo = $oTI;
	}

	/**
	 * Returns the Data object with the Transaction Information.
	 *
	 * @return TxnInfo
	 */
	public function &getTxnInfo() { return $this->_obj_TxnInfo; }

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
	 * @param 	array $debug 	Array of Debug data which should be logged for the state (optional)
	 * @return	integer
	 * @throws 	CallbackException
	 */
	public function completeTransaction($pspid, $txnid, $cid, $sid, array $debug=null)
	{
		if (intval($txnid) == -1) { $sql = ""; }
		else { $sql = ", extid = '". $this->getDBConn()->escStr($txnid) ."'"; }
		if ($this->_obj_TxnInfo->getAccountID() > 0) { $sql .= ", euaid = ". $this->_obj_TxnInfo->getAccountID(); }
		else { $sql .= ", euaid = NULL"; }
		$sql = "UPDATE Log".sSCHEMA_POSTFIX.".Transaction_Tbl
				SET pspid = ". intval($pspid) .", cardid = ". intval($cid) . $sql ."
				WHERE id = ". $this->_obj_TxnInfo->getID() ." AND (cardid IS NULL OR cardid = 0)";
		if (intval($txnid) != -1) { $sql .= " AND (extid IS NULL OR extid = '' OR extid = '". $this->getDBConn()->escStr($txnid) ."')"; }
//		echo $sql ."\n";
		$res = $this->getDBConn()->query($sql);

		// Transaction completed successfully
		if (is_resource($res) === true)
		{
			if ($this->getDBConn()->countAffectedRows($res) == 1 || $sid != Constants::iPAYMENT_ACCEPTED_STATE) { $this->newMessage($this->_obj_TxnInfo->getID(), $sid, var_export($debug, true) ); }
			else
			{
				$this->newMessage($this->_obj_TxnInfo->getID(), Constants::iPAYMENT_DUPLICATED_STATE, var_export($debug, true) );
				$sid = Constants::iPAYMENT_DUPLICATED_STATE;
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
	 * Performs the Callback request via HTTP POST and sends the Payment Status to the Client.
	 * The method will update the message log with the progression of the Callback request.
	 * If the Callback fails the method will trigger an E_USER_ERROR and if the Callback succeeds the
	 * method will trigger an E_USER_WARNING.
	 *
	 * @param 	string $body 	HTTP Body to send as the Callback to the Client
	 * @throws 	E_USER_WARNING, E_USER_NOTICE
	 */
	protected function performCallback($body)
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
			if ($iCode >= 200 && $iCode < 300)
			{
				$this->newMessage($this->_obj_TxnInfo->getID(), Constants::iCB_ACCEPTED_STATE, $obj_HTTP->getReplyHeader() );
			}
			else { $this->newMessage($this->_obj_TxnInfo->getID(), Constants::iCB_REJECTED_STATE, $obj_HTTP->getReplyHeader() ); }
			$obj_HTTP->disConnect();
		}
		// Error: Unable to establish Connection to Client
		catch (HTTPConnectionException $e)
		{
			$this->newMessage($this->_obj_TxnInfo->getID(), Constants::iCB_CONN_FAILED_STATE, $e->getMessage() ."(". $e->getCode() .")");
		}
		// Error: Unable to send Callback to Client
		catch (HTTPSendException $e)
		{
			$this->newMessage($this->_obj_TxnInfo->getID(), Constants::iCB_SEND_FAILED_STATE, $e->getMessage() ."(". $e->getCode() .")");
		}
		/* ========== Perform Callback End ========== */

		if ($iCode >= 200 && $iCode < 300) { trigger_error("mPoint Callback request succeeded for Transaction: ". $this->_obj_TxnInfo->getID(), E_USER_NOTICE); }
		else { trigger_error("mPoint Callback request failed for Transaction: ". $this->_obj_TxnInfo->getID(), E_USER_WARNING); }
	}

	/**
	 * Notifies the Client of the Payment Status by performing a callback via HTTP.
	 * The method will construct the default mPoint callback:
	 *	mpoint-id={UNIQUE ID FOR THE TRANSACTION}
	 *	&orderid={CLIENT'S ORDER ID FOR THE TRANSACTION}
	 *	&status={STATUS CODE FOR THE TRANSACTION}
	 *	&amount={TOTAL AMOUNT THE CUSTOMER WAS CHARGED FOR THE TRANSACTION}
	 *	&currency={CURRENCY AMOUNT IS CHARGED IN}
	 *	&mobile={CUSTOMER'S MSISDN WHERE SMS MESSAGE CAN BE SENT TO}
	 *	&email={CUSTOMER'S EMAIL ADDRESS WHERE ORDER STATUS CAN BE SENT TO}
	 *	&operator={GOMOBILE ID FOR THE CUSTOMER'S MOBILE NETWORK OPERATOR}
	 * Additionally the method will append all custom Client Variables that were sent to mPoint as part of the original request
	 * as well as the following Customer Input:
	 * 	- Purchased Products
	 * 	- Delivery Information
	 * 	- Shipping Information
	 *
	 * @see 	Callback::send()
	 * @see 	Callback::getVariables()
	 *
	 * @param 	integer $sid 	Unique ID of the State that the Transaction terminated in
	 * @param 	string $pspid 	The Payment Service Provider's (PSP) unique ID for the transaction
	 * @param 	integer $cardid mPoint's unique ID for the card type
	 * @param 	string $cardno 	The masked card number for the card that was used for the payment
	 */
	public function notifyClient($sid, $pspid, $amt, $cardid=0, $cardno="")
	{
		/* ----- Construct Body Start ----- */
		$sBody = "";
		$sBody .= "mpoint-id=". $this->_obj_TxnInfo->getID();
		$sBody .= "&orderid=". urlencode($this->_obj_TxnInfo->getOrderID() );
		$sBody .= "&status=". $sid;
		$sBody .= "&amount=". $amt;
		$sBody .= "&currency=". urlencode($this->_obj_TxnInfo->getClientConfig()->getCountryConfig()->getCurrency() );
		$sBody .= "&mobile=". urlencode($this->_obj_TxnInfo->getMobile() );
		$sBody .= "&operator=". urlencode($this->_obj_TxnInfo->getOperator() );
		$sBody .= "&language=". urlencode($this->_obj_TxnInfo->getLanguage() );
		if (intval($cardid) > 0) { $sBody .= "&card-id=". $cardid; }
		if (empty($cardno) === false) { $sBody .= "&card-number=". urlencode($cardno); }
		if ($this->_obj_TxnInfo->getClientConfig()->sendPSPID() === true) { $sBody .= "&pspid=". urlencode($pspid); }
		$sBody .= $this->getVariables();
		$sBody .= "&cheksum=". sha1($this->_obj_TxnInfo->getID() . $this->_obj_TxnInfo->getOrderID() . $amt . $this->_obj_TxnInfo->getClientConfig()->getPassword() );
		/* ----- Construct Body End ----- */

		$this->performCallback($sBody);
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
	protected function getVariables()
	{
		// Get custom Client Variables and Customer Input
		$aClientVars = $this->getMessageData($this->_obj_TxnInfo->getID(), Constants::iCLIENT_VARS_STATE);
		$aProducts = $this->getMessageData($this->_obj_TxnInfo->getID(), Constants::iPRODUCTS_STATE);
		$aDeliveryInfo = $this->getMessageData($this->_obj_TxnInfo->getID(), Constants::iDELIVERY_INFO_STATE);
		$aShippingInfo = $this->getMessageData($this->_obj_TxnInfo->getID(), Constants::iSHIPPING_INFO_STATE);

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
	 * @param 	string $url Absolute URL the request should be made to
	 * @param 	string $h 	HTTP Headers to send as part of the request
	 * @param 	string $b 	HTTP Body to send as part of the request
	 * @return	HTTPClient	Reference to the created HTTP Client object
	 * @throws 	HTTPException
	 */
	public function &send($url, $h, $b, $un="", $pw="")
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
		$sql = "SELECT username, passwd AS password
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
				WHERE countryid = ". intval($cid) ." AND pspid = ". intval($pspid) ." AND enabled = '1'";
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
		//echo $sql ."\n";
		$RS = $oDB->getName($sql);

		return is_array($RS) === true && intval($RS["ID"]) > 0? $RS["ID"] : -1;
	}
}
?>