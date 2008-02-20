<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @version 1.0
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
class Callback extends General
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
	 * @param	RDB $oDB			Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param	TranslateText $oDB 	Text Translation Object for translating any text into a specific language
	 * @param 	TxnInfo $oTI 		Data object with the Transaction Information
	 */
	public function __construct(RDB &$oDB, TranslateText &$oTxt, TxnInfo &$oTI)
	{
		parent::__construct($oDB, $oTxt);
		
		$this->_obj_TxnInfo = $oTI;
	}
	
	/**
	 * Returns the Data object with the Transaction Information.
	 *
	 * @return TxnInfo
	 */
	public function &getTxnInfo() { return $this->_obj_TxnInfo; }
	
	public function completeTransaction($pspid, $txnid, $cid, $sid, array $debug=null)
	{
		$sql = "UPDATE Log.Transaction_Tbl
				SET pspid = ". intval($pspid) .", extid = '". $this->getDBConn()->escStr($txnid) ."', cardid = ". intval($cid) ."
				WHERE id = ". $this->_obj_TxnInfo->getID();
//		echo $sql ."\n";
		$res = $this->getDBConn()->query($sql);
		
		$this->newMessage($this->_obj_TxnInfo->getID(), $sid, var_export($debug, true) );
		
		if (is_resource($res) === false)
		{
			throw new CallbackException("Unable to complete log for Transaction: ". $this->_obj_TxnInfo->getID(), 1001);
		}
	}
	
	/**
	 * Construct standard mPoint HTTP Headers for notifying the Client via HTTP.
	 *
	 * @return string
	 */
	protected function constHeaders()
	{
		/* ----- Construct HTTP Header Start ----- */
		$h = "{METHOD} {PATH} HTTP/1.0" .HTTPClient::CRLF;
		$h .= "host: {HOST}" .HTTPClient::CRLF;
		$h .= "referer: {REFERER}" .HTTPClient::CRLF;
		$h .= "content-length: {CONTENTLENGTH}" .HTTPClient::CRLF;
		$h .= "content-type: {CONTENTTYPE}; charset=ISO-8859-15" .HTTPClient::CRLF;
		/* ----- Construct HTTP Header End ----- */
		
		return $h;
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
	protected function send($body)
	{
		$this->newMessage($this->_obj_TxnInfo->getID(), Constants::iCB_CONSTRUCTED_STATE, $body);
		/* ========== Instantiate Connection Info Start ========== */
		$aURLInfo = parse_url($this->_obj_TxnInfo->getCallbackURL() );
		
		if (array_key_exists("port", $aURLInfo) === false) { $aURLInfo["port"] = 80; }
		if (array_key_exists("query", $aURLInfo) === true) { $aURLInfo["path"] .= "?". $aURLInfo["query"]; }
		
		$obj_ConnInfo = new HTTPConnInfo($aURLInfo["scheme"], $aURLInfo["host"], $aURLInfo["port"], 20, $aURLInfo["path"], "POST", "application/www-url-form-encoded");
		/* ========== Instantiate Connection Info End ========== */
		$obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
		
		/* ========== Perform Callback Start ========== */
		$iCode = -1;
		try
		{
			$obj_HTTP->connect();
			$this->newMessage($this->_obj_TxnInfo->getID(), Constants::iCB_CONNECTED_STATE, "Host: ". $obj_ConnInfo->getHost() .", Port: ". $obj_ConnInfo->getPort() .", Path: ". $obj_ConnInfo->getPath() );
			// Send Callback data
			$iCode = $obj_HTTP->send($this->constHeaders(), $body);
			if ($iCode == 200)
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
		
		if ($iCode == 200) { trigger_error("mPoint Callback request succeeded for Transaction: ". $this->_obj_TxnInfo->getID(), E_USER_NOTICE); }
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
	 *	&recipient={CUSTOMER'S MSISDN WHERE SMS MESSAGE CAN BE SENT TO}
	 *	&operator={GOMOBILE ID FOR THE CUSTOMER'S MOBILE NETWORK OPERATOR}
	 * Additionally the method will append all custom Client Variables that were sent to mPoint as part of the original request.
	 * 
	 * @see 	Callback::send()
	 *
	 * @param 	integer $sid 	Unique ID of the State that the Transaction terminated in
	 */
	public function notifyClient($sid)
	{
		// Get custom Client Variables
		$aClientVars = $this->getMessageData($this->_obj_TxnInfo->getID(), Constants::iCLIENT_VARS_STATE);
		/* ----- Construct Body Start ----- */
		$sBody = "";
		$sBody .= "mpoint-id=". $this->_obj_TxnInfo->getID();
		$sBody .= "&orderid=". urlencode($this->_obj_TxnInfo->getOrderID() );
		$sBody .= "&status=". $sid;
		$sBody .= "&amount=". $this->_obj_TxnInfo->getAmount();
		$sBody .= "&currency=". urlencode($this->_obj_TxnInfo->getClientConfig()->getCountryConfig()->getCurrency() );
		$sBody .= "&recipient=". urlencode($this->_obj_TxnInfo->getAddress() );
		$sBody .= "&operator=". urlencode($this->_obj_TxnInfo->getOperator() );
		// Add custom Client Variables to Callback Body
		foreach ($aClientVars as $name => $value)
		{
			$sBody .= "&". $name ."=". urlencode($value);
		}
		/* ----- Construct Body End ----- */
		
		$this->send($sBody);
	}
	
	/**
	 * Sends an SMS Receipt with Payment Information to the Customer through GoMobile.
	 *
	 * @see 	GoMobileClient
	 * @see 	Constants::iMT_SMS_TYPE
	 * @see 	Constants::iMT_PRICE
	 *
	 * @param 	GoMobileConnInfo $oCI 	Connection Info required to communicate with GoMobile
	 */
	public function sendReceipt(GoMobileConnInfo &$oCI)
	{
		$sBody = utf8_decode($this->getText()->_("mPoint - SMS Receipt") );
		$sBody = str_replace("{MPOINTID}", $this->_obj_TxnInfo->getID(), $sBody);
		$sBody = str_replace("{ORDERID}", $this->_obj_TxnInfo->getOrderID(), $sBody);
		$sBody = str_replace("{PRICE}", General::formatAmount($this->_obj_TxnInfo->getClientConfig()->getCountryConfig(), $this->_obj_TxnInfo->getAmount() ), $sBody);
		$sBody = str_replace("{CLIENT}", $this->_obj_TxnInfo->getClientConfig()->getName(), $sBody);
		
		// Instantiate Message Object for holding the message data which will be sent to GoMobile
		$obj_MsgInfo = GoMobileMessage::produceMessage(Constants::iMT_SMS_TYPE, $this->_obj_TxnInfo->getClientConfig()->getCountryConfig()->getID(), $this->_obj_TxnInfo->getOperator(), $this->_obj_TxnInfo->getClientConfig()->getCountryConfig()->getChannel(), $this->_obj_TxnInfo->getClientConfig()->getKeywordConfig()->getKeyword(), Constants::iMT_PRICE, $this->_obj_TxnInfo->getAddress(), $sBody);
		$this->sendMT($oCI, $obj_MsgInfo, $this->_obj_TxnInfo);
	}
}
?>