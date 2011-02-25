<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol.
 * The DIBS' subpackage is a specific implementation capable of imitating DIBS' own protocol.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage DIBS
 * @version 1.01
 */

/**
 * Model Class containing all the Business Logic for handling Callback requests from DIBS.
 *
 */
class DIBS extends Callback
{
	/**
	 * Notifies the Client of the Payment Status by performing a callback via HTTP.
	 * The method will re-construct the data received from DIBS after having removed the following mPoint specific fields:
	 * 	- width
	 * 	- height
	 * 	- format
	 * 	- PHPSESSID (found using PHP's session_name() function)
	 * 	- language
	 * 	- cardid
	 * Additionally the method will add mPoint's Unique ID for the Transaction.
	 *
	 * @see 	Callback::notifyClient()
	 * @see 	Callback::send()
	 * @see 	Callback::getVariables()
	 *
	 * @param 	integer $sid 	Unique ID of the State that the Transaction terminated in
	 * @param 	array $_post 	Array of data received from DIBS via HTTP POST
	 */
	public function notifyClient($sid, array $_post)
	{
		// Client is configured to use mPoint's protocol
		if ($this->getTxnInfo()->getClientConfig()->getMethod() == "mPoint")
		{
			parent::notifyClient($sid, $_post["transact"]);
		}
		// Client is configured to use DIBS' protocol
		else
		{
			// Remove mPoint specific data fields from Callback request
			unset($_post["width"], $_post["height"], $_post["format"], $_post[session_name()], $_post["language"], $_post["cardid"]);
			// Replace data fields previously overwritten by mPoint
			$_post["orderid"] = $this->getTxnInfo()->getOrderID();
			$_post["callbackurl"] = $this->getTxnInfo()->getCallbackURL();
			$_post["accepturl"] = $this->getTxnInfo()->getAcceptURL();
			// Re-Construct DIBS request
			$sBody = "mpoint-id=". $this->getTxnInfo()->getID();
			foreach ($_post as $key => $val)
			{
				$sBody .= "&". $key ."=". urlencode($val);
			}
			// Append Custom Client Variables and Customer Input
			$sBody .= "&". $this->getVariables();

			$this->performCallback($sBody);
		}
	}

	/**
	 * Returns the Client's Merchant Account ID for the PSP
	 * 
	 * @param 	integer $clid	Unique ID of the Client whose Merchant Account should be found
	 * @param 	integer $pspid	Unique ID for the PSP the Merchant Account should be found for
	 * @return 	string
	 */
	public function getMerchantAccount($clid, $pspid)
	{
		$sql = "SELECT name
				FROM Client.MerchantAccount_Tbl
				WHERE clientid = ". intval($clid) ." AND pspid = ". intval($pspid) ." AND enabled = true";
//		echo $sql ."\n";
		$RS = $this->getDBConn($sql)->getName($sql);

		return $RS["NAME"];
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
				FROM Client.MerchantSubAccount_Tbl
				WHERE accountid = ". intval($accid) ." AND pspid = ". intval($pspid) ." AND enabled = true";
//		echo $sql ."\n";
		$RS = $this->getDBConn($sql)->getName($sql);

		return $RS["NAME"];
	}

	/**
	 * Returns the specified PSP's currency code for the provided country 
	 * 
	 * @param 	integer $cid	Unique ID for the Country that the Currency should be found in
	 * @param 	integer $pspid	Unique ID for the PSP that the currency code should be found for
	 * @return unknown_type
	 */
	public function getCurrency($cid, $pspid)
	{
		$sql = "SELECT name
				FROM System.PSPCurrency_Tbl
				WHERE countryid = ". intval($cid) ." AND pspid = ". intval($pspid) ." AND enabled = true";
//		echo $sql ."\n";
		$RS = $this->getDBConn($sql)->getName($sql);

		return $RS["NAME"];
	}

	/**
	 * (non-PHPdoc)
	 * @see api/classes/EndUserAccount#delTicket($pspid, $ticket)
	 */
	public function delTicket($ticket)
	{
		$h = $this->constHTTPHeaders();
//		$h .= "authorization: Basic ". base64_encode($this->_obj_ConnInfo->getUsername() .":". $this->_obj_ConnInfo->getPassword() ) .HTTPClient::CRLF;
		$b = "merchant=". $this->getMerchantAccount($this->getTxnInfo()->getClientConfig()->getID(), Constants::iDIBS_PSP). "&ticket=". $ticket;

//		parent::send("https://payment.architrade.com/cgi-adm/delticket.cgi", $h, $b);
	}

	/**
	 * Authorises a payment with DIBS for the transaction using the provided ticket.
	 * The ticket represents a previously stored card.
	 * The method will return DIBS' transaction ID if the authorisation is accepted or one of the following status codes if the authorisation is declined:
	 * 	  0. Rejected by acquirer.
	 *   -1. Communication problems.
	 *   -2. Error in the parameters sent to the DIBS server.
	 *   -3. Error at the acquirer. 
	 *   -4. Credit card expired. 
	 *   -5. Your shop does not support this credit card type, the credit card type could not be identified, or the credit card number was not modulus correct.
	 *   -6. Instant capture failed.
	 *   -7. The order number (orderid) is not unique. 
	 *   -8. There number of amount parameters does not correspond to the number given in the split parameter.
	 *   -9. Control numbers (cvc) are missing.
	 *  -10. The credit card does not comply with the credit card type.
	 *  -11. Declined by DIBS Defender.
	 *  -20. Cancelled by user at 3D Secure authentication step
	 *  
	 * @link	http://tech.dibs.dk/toolbox/dibs-error-codes/
	 *  
	 * @param 	integer $ticket		Valid ticket which references a previously stored card 
	 * @return 	integer
	 * @throws	E_USER_WARNING
	 */
	public function authTicket($ticket)
	{
		// Construct Order ID
		$oid = $this->getTxnInfo()->getOrderID();
		if (empty($oid) === true) { $oid = $this->getTxnInfo()->getID(); }
//		$oid .= "-". date("Y-m-d H:i:s");
		
		$b = "merchant=". $this->getMerchantAccount($this->getTxnInfo()->getClientConfig()->getID(), Constants::iDIBS_PSP);
		$b .= "&mpointid=". $this->getTxnInfo()->getID();
		$b .= "&ticket=". $ticket;
		$b .= "&amount=". $this->getTxnInfo()->getAmount();
		$b .= "&currency=". $this->getCurrency($this->getTxnInfo()->getClientConfig()->getCountryConfig()->getID(), Constants::iDIBS_PSP);
		$b .= "&orderid=". urlencode($oid);
		if ($this->getTxnInfo()->getClientConfig()->getMode() > 0) { $b .= "&test=". $this->getTxnInfo()->getClientConfig()->getMode(); }
		$b .= "&textreply=true";
		
		$obj_HTTP = parent::send("https://payment.architrade.com/cgi-ssl/ticket_auth.cgi", $this->constHTTPHeaders(), $b);
		$aStatus = array();
		parse_str($obj_HTTP->getReplyBody(), $aStatus);
		// Auhtorisation Declined
		if (strtoupper($aStatus["status"]) != "ACCEPTED")
		{
			trigger_error(trim("Authorisation declined by DIBS for Ticket: ". $ticket .", Reason Code: ". $aStatus["reason"] ."\n". @$aStatus["message"]), E_USER_WARNING);
			$aStatus["transact"] = $aStatus["reason"] * -1;
		}

		return $aStatus["transact"];
	}

	/**
	 * Performs a capture operation with DIBS for the provided transaction.
	 * The method will log one the following status codes from DIBS:
	 * 	0. Capture succeeded
	 * 	1. No response from acquirer.
	 * 	2. Error in the parameters sent to the DIBS server. An additional parameter called "message" is returned, with a value that may help identifying the error.
	 * 	3. Credit card expired.
	 * 	4. Rejected by acquirer.
	 * 	5. Authorisation older than7 days.
	 * 	6. Transaction status on the DIBS server does not allow capture.
	 * 	7. Amount too high.
	 * 	8. Amount is zero.
	 * 	9. Order number (orderid) does not correspond to the authorisation order number.
	 * 10. Re-authorisation of the transaction was rejected.
	 * 11. Not able to communicate with the acquier.
	 * 12. Confirm request error
	 * 14. Capture is called for a transaction which is pending for batch - i.e. capture was already called
	 * 15. Capture was blocked by DIBS.
	 * 
	 * @link	http://tech.dibs.dk/toolbox/dibs-error-codes/
	 * 
	 * @param 	integer $txn	Transaction ID previously returned by DIBS during authorisation
	 * @return	integer
	 * @throws	E_USER_WARNING
	 */
	public function capture($txn)
	{
		$b = "merchant=". $this->getMerchantAccount($this->getTxnInfo()->getClientConfig()->getID(), Constants::iDIBS_PSP);
		$b .= "&mpointid=". $this->getTxnInfo()->getID();
		$b .= "&transact=". $txn;
		$b .= "&amount=". $this->getTxnInfo()->getAmount();
		$b .= "&orderid=". urlencode($this->getTxnInfo()->getOrderID() );
		if ($this->getMerchantSubAccount($this->getTxnInfo()->getClientConfig()->getAccountConfig()->getID(), Constants::iDIBS_PSP) > -1) { $b .= "&account=". $this->getMerchantSubAccount($this->getTxnInfo()->getClientConfig()->getAccountConfig()->getID(), Constants::iDIBS_PSP); }
		$b .= "&textreply=true";
		
		$obj_HTTP = parent::send("https://payment.architrade.com/cgi-bin/capture.cgi", $this->constHTTPHeaders(), $b);
		$aStatus = array();
		parse_str($obj_HTTP->getReplyBody(), $aStatus);
		
		// Capture Declined
		if (array_key_exists("result", $aStatus) === false || $aStatus["result"] > 0)
		{
			$this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_DECLINED_STATE, var_export($aStatus, true) );
			trigger_error("Capture declined by DIBS for Transaction: ". $txn .", Result Code: ". @$aStatus["result"], E_USER_WARNING);
			
			return $aStatus["result"];
		}
		// Payment successfully captured
		else
		{
			$this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_CAPTURED_STATE, "");
			
			return 0;
		}
	}

	/**
	 * Initialises Callback to the Client.
	 *
	 * @param 	HTTPConnInfo $oCI 	Connection Info required to communicate with the Callback component for Cellpoint Mobile
	 * @param 	integer $cardid		Unique ID of the Card Type that was used in the payment transaction
	 * @param 	integer $txnid		Transaction ID from DIBS returned in the "transact" parameter
	 */
	public function initCallback(HTTPConnInfo &$oCI, $cardid, $txnid)
	{
		$b = "mpointid=". $this->getTxnInfo()->getID();
		$b .= "&transact=". $txnid;
		$b .= "&cardid=". $cardid;
		$b .= "&clientid=". $this->getTxnInfo()->getClientConfig()->getID();
		$b .= "&language=". $this->getTxnInfo()->getLanguage();
		$b .= "&capturenow=". General::bool2xml($this->getTxnInfo()->getClientConfig()->useAutoCapture() );
		$b .= "preauth=false";

		$obj_HTTP = new HTTPClient(new Template(), $oCI);
		$obj_HTTP->connect();
		$obj_HTTP->send($this->constHTTPHeaders(), $b);
		$obj_HTTP->disConnect();
	}
}
?>