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
class DIBS extends Callback implements Captureable, Refundable
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
			if ($sid == Constants::iPAYMENT_ACCEPTED_STATE) { parent::notifyClient($sid, $_post["transact"], $_post["amount"], $_post['cardid'], $_post['cardprefix'] . str_replace("X", "*", substr($_post['cardnomask'], strlen($_post['cardprefix']) ) ) ); }
			else { parent::notifyClient($sid, $_post["transact"], @$_post["amount"]); }
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
	 * @see		DIBS::_authTicket()
	 * @see		DIBS::authNewCard()
	 *  
	 * @param 	SimpleXMLElement $obj_XML	The "card" XML element which contains either a valid token which references a previously stored card or the card details of a new card 
	 * @return 	integer
	 * @throws	E_USER_WARNING
	 */
	public function authTicket(SimpleXMLElement $obj_XML)
	{
		if (count($obj_XML->ticket) == 1)
		{
			return $this->_authTicket( (integer) $obj_XML->ticket);
		}
		else
		{
			// expiry date received in mm/yy format 
			return $this->authNewCard(
					$obj_XML->{'card-number'}, 
					substr($obj_XML->{'expiry'}, 0, 2), 
					substr($obj_XML->{'expiry'}, -2), 
					$obj_XML->cvc, 
					$obj_XML->{'card-holder-name'},
					$obj_XML['type-id']
			);
		}			
	}
	
	/**
	 * 
	 * @param 	integer $ticket		Valid ticket which references a previously stored card
	 * @return 	integer
	 * @throws	E_USER_WARNING
	 */
	private function _authTicket($ticket)
	{
		// Construct Order ID
		$oid = $this->getTxnInfo()->getOrderID();
		if (empty($oid) === true) { $oid = $this->getTxnInfo()->getID(); }
//		$oid .= "-". date("Y-m-d H:i:s");
	
		$b = "merchant=". $this->getMerchantAccount($this->getTxnInfo()->getClientConfig()->getID(), Constants::iDIBS_PSP);
		$b .= "&mpointid=". $this->getTxnInfo()->getID();
		$b .= "&ticket=". $ticket;
		$b .= "&amount=". $this->getTxnInfo()->getAmount();
		$b .= "&currency=". $this->getCurrency($this->getTxnInfo()->getCountryConfig()->getID(), Constants::iDIBS_PSP);
		$b .= "&orderid=". urlencode($oid);
		if ($this->getTxnInfo()->getClientConfig()->getMode() > 0) { $b .= "&test=". $this->getTxnInfo()->getClientConfig()->getMode(); }
		$b .= "&textreply=true";
	
		$aConnInfo = $this->aCONN_INFO;
		$aConnInfo["path"] = $aConnInfo["paths"]["auth-ticket"];
		$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aConnInfo);
		$obj_HTTP = parent::send($obj_ConnInfo, $this->constHTTPHeaders(), $b);
	
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
	 * 
	 * @param long $cardno			Card Number
	 * @param integer $expmonth		Expiry Month
	 * @param integer $expyear		Expiry Year
	 * @param integer $cvc			CVC / CVV
	 * @param string $chn			Card Holder Name (optional)
	 * @return integer
	 * @throws E_USER_WARNING
	 */
	public function authNewCard($cardno, $expmonth, $expyear, $cvc, $chn="", $cardid)
	{
		// Construct Order ID
		$oid = $this->getTxnInfo()->getOrderID();
		if (empty($oid) === true) { $oid = $this->getTxnInfo()->getID(); }
	
		$b = "merchant=". $this->getMerchantAccount($this->getTxnInfo()->getClientConfig()->getID(), Constants::iDIBS_PSP);
		$b .= "&mpointid=". $this->getTxnInfo()->getID();
		$b .= "&cardno=". trim($cardno);
		$b .= "&expmon=". trim($expmonth);
		$b .= "&expyear=". trim($expyear);
		$b .= "&cvc=". trim($cvc);
		$b .= "&amount=". $this->getTxnInfo()->getAmount();
		$b .= "&currency=". $this->getCurrency($this->getTxnInfo()->getCountryConfig()->getID(), Constants::iDIBS_PSP);
		$b .= "&orderid=". urlencode($oid);
		if ($this->getTxnInfo()->getClientConfig()->getMode() > 0) { $b .= "&test=". $this->getTxnInfo()->getClientConfig()->getMode(); }
		$b .= "&textreply=true";
		$b .= "&callbackurl=". urlencode("http://". $_SERVER['HTTP_HOST'] ."/callback/dibs.php");
		$b .= "&fullreply=true";
		$b .= "&language=". $this->getTxnInfo()->getLanguage();
		$b .= "&cardid=". $cardid;
		$b .= "&clientid=". $this->getTxnInfo()->getClientConfig()->getID();
		$b .= "&accountid=". $this->getTxnInfo()->getClientConfig()->getAccountConfig()->getID();
		$b .= "&store_card=". $this->getTxnInfo()->getClientConfig()->getStoreCard();
		$b .= "&auto_store_card=". parent::bool2xml($this->getTxnInfo()->autoStoreCard() );
		
		if(count($this->getMessageData($this->getTxnInfo()->getID(), Constants::iTICKET_CREATED_STATE, false) ) == 1 )
		{
			$b .= "&preauth=true";
		} else { $b .= "&preauth=false"; }
		

		$aConnInfo = $this->aCONN_INFO;
		$aConnInfo["path"] = $aConnInfo["paths"]["auth-new-card"];
		$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aConnInfo);
		$obj_HTTP = parent::send($obj_ConnInfo, $this->constHTTPHeaders(), $b);

		$aStatus = array();
		parse_str($obj_HTTP->getReplyBody(), $aStatus);
		// Auhtorisation Declined
		if (strtoupper($aStatus["status"]) != "ACCEPTED")
		{
			trigger_error(trim("Authorisation declined by DIBS for Card Details: ". $this->_getMaskedCardNumber($cardno) .", Reason Code: ". $aStatus["reason"] ."\n". @$aStatus["message"]), E_USER_WARNING);
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
	 * @param 	integer $iAmount	Transaction amount to capture
	 * @return	integer
	 * @throws	E_USER_WARNING
	 */
	public function capture($iAmount = -1)
	{
		$extID = $this->getTxnInfo()->getExternalID();
		if ($iAmount == -1) { $iAmount = $this->getTxnInfo()->getAmount(); }

		$code = $this->status($extID);
		// Transaction ready for Capture
		if ($code == 2)
		{
			$b = "merchant=". $this->getMerchantAccount($this->getTxnInfo()->getClientConfig()->getID(), Constants::iDIBS_PSP);
			$b .= "&mpointid=". $this->getTxnInfo()->getID();
			$b .= "&transact=". $extID;
			$b .= "&amount=". $iAmount;
			$b .= "&orderid=". urlencode($this->getTxnInfo()->getOrderID() );
			if ($this->getMerchantSubAccount($this->getTxnInfo()->getClientConfig()->getAccountConfig()->getID(), Constants::iDIBS_PSP) > -1) { $b .= "&account=". $this->getMerchantSubAccount($this->getTxnInfo()->getClientConfig()->getAccountConfig()->getID(), Constants::iDIBS_PSP); }
			$b .= "&textreply=true";

			$aConnInfo = $this->aCONN_INFO;
			$aConnInfo["path"] = $aConnInfo["paths"]["capture"];
			$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aConnInfo);

			$obj_HTTP = parent::send($obj_ConnInfo, $this->constHTTPHeaders(), $b);
			if ($obj_HTTP->getReturnCode() == 200)
			{
				$aStatus = array();
				parse_str($obj_HTTP->getReplyBody(), $aStatus);
				
				// Capture Declined
				if (array_key_exists("result", $aStatus) === false || $aStatus["result"] > 0)
				{
					$this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_DECLINED_STATE, var_export($aStatus, true) );
					trigger_error("Capture declined by DIBS for Transaction: ". $this->getTxnInfo()->getID() ."(". $extID ."), Result Code: ". @$aStatus["result"], E_USER_WARNING);
					
					return $aStatus["result"];
				}
				// Payment successfully captured
				else
				{
					// Needs to be updated to support DIBS splitpay
					$this->completeCapture($iAmount, $this->getTxnInfo()->getFee(), array(utf8_encode($obj_HTTP->getReplyBody() ) ) );

					return 1000;
				}
			}
			else
			{
				trigger_error("Capture declined by DIBS for Transaction: ". $this->getTxnInfo()->getID() ."(". $extID ."), HTTP Response Code: ". $obj_HTTP->getReturnCode(), E_USER_WARNING);
				
				return 20;
			}
		}
		// Capture already completed
		elseif ($code == 5)
		{
			$this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_CAPTURED_STATE, "DIBS returned code: ". $code ." from status call");
			
			return 0;
		}
		else
		{
			trigger_error("Transaction: ". $this->getTxnInfo()->getID() ."(". $extID .") not ready for Capture, DIBS returned code: ". $code, E_USER_WARNING);
			
			return $code;
		}
	}
	
	/**
	 * Performs a refund operation with DIBS for the provided transaction.
	 * The method will log one the following status codes from DIBS:
	 * 	0. Refund succeeded
	 * 	1. No response from acquirer.
	 * 	2. Error in the parameters sent to the DIBS server. An additional parameter called "message" is returned, with a value that may help identifying the error.
	 * 	3. Credit card expired.
	 * 	4. Rejected by acquirer.
	 * 	5. Authorisation older than 7 days.
	 * 	6. Transaction status on the DIBS server does not allow capture.
	 * 	7. Amount too high.
	 * 	8. Amount is zero.
	 * 	9. Order number (orderid) does not correspond to the authorisation order number.
	 * 10. Re-authorisation of the transaction was rejected.
	 * 11. Not able to communicate with the acquier.
	 * 12. Confirm request error
	 * 14. Refund is called for a transaction which is pending for batch - i.e. capture was already called
	 * 15. Refund was blocked by DIBS.
	 * 
	 * @link	http://tech.dibs.dk/toolbox/dibs-error-codes/
	 * 
	 * @param 	integer $iAmount	full amount that needed to be refunded
	 * @param 	integer $code	allows to control from the outside whether to cancel or refund the transaction
	 * 							if this value is unset (-1), the txn status will be first queried at DIBS and the needed action (cancel/refund) will be performed
	 * @return	integer
	 * @throws	E_USER_WARNING
	 */
	public function refund($iAmount = -1, $code = -1)
	{
		$extID = $this->getTxnInfo()->getExternalID();
		if ($iAmount == -1) { $iAmount = $this->getTxnInfo()->getAmount(); }

		$aConnInfo = $this->aCONN_INFO;

		if ($code == -1) { $code = $this->status($extID); }

		//Set the api type depending on the return value that is returned from DIBS
		switch ($code)
		{
			case (2):
				$aConnInfo["path"] = $aConnInfo["paths"]["cancel"];
				break;
			case (5):
				$aConnInfo["path"] = $aConnInfo["paths"]["refund"];
				break;
		}

		// Transaction ready for Refund or cancel
		if ($code == 5 || $code == 2)
		{
			$b = "merchant=". $this->getMerchantAccount($this->getTxnInfo()->getClientConfig()->getID(), Constants::iDIBS_PSP);
			$b .= "&mpointid=". $this->getTxnInfo()->getID();
			$b .= "&transact=". $extID;
			
			if($code == 5) 
			{
				$b .= "&amount=". $iAmount;
			}
			
			$b .= "&orderid=". urlencode($this->getTxnInfo()->getOrderID() );
			if ($this->getMerchantSubAccount($this->getTxnInfo()->getClientConfig()->getAccountConfig()->getID(), Constants::iDIBS_PSP) > -1) { $b .= "&account=". $this->getMerchantSubAccount($this->getTxnInfo()->getClientConfig()->getAccountConfig()->getID(), Constants::iDIBS_PSP); }
			$b .= "&textreply=true";

			$aConnInfo["username"] = $this->getPSPConfig()->getUsername();
			$aConnInfo["password"] = $this->getPSPConfig()->getPassword();
			$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aConnInfo);

			$obj_HTTP = parent::send($obj_ConnInfo, $this->constHTTPHeaders(), $b);
			if ($obj_HTTP->getReturnCode() == 200)
			{
				$aStatus = array();
				parse_str($obj_HTTP->getReplyBody(), $aStatus);
				// Refund Declined
				if (array_key_exists("result", $aStatus) === false || $aStatus["result"] > 0)
				{
					if (array_key_exists("result", $aStatus) === false) { $str = var_export($aStatus, true); }
					else { $str = "Result Code: ". $aStatus["result"]; }
					
					if($code == 2)
					{
						trigger_error("Cancel declined by DIBS for Transaction: ". $this->getTxnInfo()->getID() ."(". $extID ."), ". $str, E_USER_WARNING);
					} 
					else 
					{
						trigger_error("Refund declined by DIBS for Transaction: ". $this->getTxnInfo()->getID() ."(". $extID ."), ". $str, E_USER_WARNING);
					}
					
					return $aStatus["result"];
				}
				// Payment successfully refunded/cancelled
				else
				{
					if ($code == 2)
					{
						$this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_CANCELLED_STATE, utf8_encode($obj_HTTP->getReplyBody() ) );
						return 1001;
					}
					else
					{
						$this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_REFUNDED_STATE, utf8_encode($obj_HTTP->getReplyBody() ) );
						return 1000;
					}
				}
			}
			else
			{
				trigger_error("Refund declined by DIBS for Transaction: ". $this->getTxnInfo()->getID() ."(". $extID ."), HTTP Response Code: ". $obj_HTTP->getReturnCode(), E_USER_WARNING);
				
				return 20;
			}
		}
		// Refund already completed
		elseif ($code == 11)
		{
			$this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_REFUNDED_STATE, "DIBS returned code: ". $code ." from status call");
			
			return 0;
		}
		else
		{
			trigger_error("Transaction: ". $this->getTxnInfo()->getID() ."(". $extID .") not ready for Refund, DIBS returned code: ". $code, E_USER_WARNING);
			
			return $code;
		}
	}
	
	/**
	 * Performs a status operation with DIBS for the provided transaction.
	 * The method will log one the following status codes from DIBS:
	 * 	 0. transaction inserted (not approved)
	 * 	 1. declined
	 * 	 2. authorization approved
	 * 	 3. capture sent to acquirer
	 * 	 4. capture declined by acquirer
	 * 	 5. capture completed
	 * 	 6. authorization deleted
	 * 	 7. capture balanced
	 * 	 8. partially refunded and balanced
	 * 	 9. refund sent to acquirer
	 * 	10. refund declined
	 * 	11. refund completed
	 * 	12. capture pending
	 * 	13. "ticket" transaction
	 * 	14. deleted "ticket" transaction
	 * 	15. refund pending
	 * 	16. waiting for shop approval
	 * 	17. declined by DIBS
	 * 	18. multicap transaction open
	 * 	19. multicap transaction closed
	 * 
	 * @link	http://tech.dibs.dk/dibs_api/status_functions/transstatuspml/
	 * 
	 * @param 	integer $txn	Transaction ID previously returned by DIBS during authorisation
	 * @return	integer
	 * @throws	E_USER_WARNING
	 */
	public function status($txn)
	{
		$b = "merchant=". $this->getMerchantAccount($this->getTxnInfo()->getClientConfig()->getID(), Constants::iDIBS_PSP);
		$b .= "&transact=". $txn;

		$aConnInfo = $this->aCONN_INFO;
		$aConnInfo["path"] = $aConnInfo["paths"]["status"];
		$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aConnInfo);

		$obj_HTTP = parent::send($obj_ConnInfo, $this->constHTTPHeaders(), $b);
		if ($obj_HTTP->getReturnCode() == 200)
		{
			return trim($obj_HTTP->getReplyBody() );
		}
		else 
		{
			trigger_error("DIBS returned HTTP error on transstatus call for txn: ". $txn ." - code: ". $obj_HTTP->getReturnCode() ." body: ".  trim($obj_HTTP->getReplyBody() ), E_USER_WARNING);
			return -1;
		}
	}

	public function normalizeStatusCode($iStatus)
	{
		switch ($iStatus)
		{
		case 0:
			return Constants::iPAYMENT_INIT_WITH_PSP_STATE;
		case 1:
			return Constants::iPAYMENT_REJECTED_STATE;
		case 2:
			return Constants::iPAYMENT_ACCEPTED_STATE;
		case 4:
		case 17:
			return Constants::iPAYMENT_DECLINED_STATE;
		case 3:
		case 5:
		case 7:
		case 10: //refund declined is mapped to state CAPTURED in order to signal to mPoint to try refund again if possible
		case 12:
			return Constants::iPAYMENT_CAPTURED_STATE;
		case 6:
		case 14:
			return Constants::iPAYMENT_CANCELLED_STATE;
		case 8:
		case 9:
		case 11:
		case 15:
			return Constants::iPAYMENT_REFUNDED_STATE;
		case 13:
			return Constants::iTICKET_CREATED_STATE;

		}

		return -1;
	}

	/**
	 * Initialises Callback to the Client.
	 *
	 * @param 	HTTPConnInfo $oCI 	Connection Info required to communicate with the Callback component for Cellpoint Mobile
	 * @param 	integer $cardid		Unique ID of the Card Type that was used in the payment transaction
	 * @param 	integer $txnid		Transaction ID from DIBS returned in the "transact" parameter
	 */
	public function initCallback(HTTPConnInfo &$oCI, $cardid, $txnid, $cardno, $expiry)
	{
		$mask = str_replace(" ", "", $cardno);
		$b = "mpointid=". $this->getTxnInfo()->getID();
		$b .= "&transact=". $txnid;
		$b .= "&cardid=". $cardid;
		$b .= "&clientid=". $this->getTxnInfo()->getClientConfig()->getID();
		$b .= "&language=". $this->getTxnInfo()->getLanguage();
		$b .= "&preauth=false";
		$b .= "&cardnomask=". $mask;
		$b .= "&cardprefix=". substr($mask, 0, strpos($mask, "*") );
		$b .= "&cardexpdate=". substr($expiry, strpos($expiry, "/") + 1) . substr($expiry, 0, strpos($expiry, "/") );
		$b .= "&amount=". $this->getTxnInfo()->getAmount();

		$obj_HTTP = new HTTPClient(new Template(), $oCI);
		$obj_HTTP->connect();
		$obj_HTTP->send($this->constHTTPHeaders(), $b);
		$obj_HTTP->disConnect();
	}
	
	public function initialize(HTTPConnInfo &$oCI, $merchant, $account, $currency, $cardid)
	{
		$oid = urlencode($this->getTxnInfo()->getOrderID() );
		if (empty($oid) === true) { $oid = $this->getTxnInfo()->getID(); }
		// DIBS Required Data
		$b = "merchant=". $merchant;
		// Client is in Test or Certification mode
		if ($this->getTxnInfo()->getMode() > 0) { $b .= "&test=". $this->getTxnInfo()->getMode(); }
		$b .= "&callbackurl=". urlencode("http://". $_SERVER['HTTP_HOST'] ."/callback/dibs.php");
		$b .= "&accepturl=". urlencode($this->getClientConfig()->getAcceptURL() );
		$b .= "&cancelurl=". urlencode($this->getClientConfig()->getCancelURL() );
		$b .= "&declineurl=". urlencode($this->getClientConfig()->getDeclineURL() );
		$b .= "&amount=". $this->getTxnInfo()->getAmount();
		$b .= "&currency=". $currency;
		$b .= "&orderid=". $oid;
		$b .= "&fullreply=true";
		// Sub-Account configured for DIBS
		if ($account > 0) { $b .= "&account=". $account; }
		// mPoint Required data
		$b .= "&language=". $this->getTxnInfo()->getLanguage();
		$b .= "&cardid=". $cardid;
		$b .= "&mpointid=". $this->getTxnInfo()->getID();
		$b .= "&markup=". $this->getTxnInfo()->getClientConfig()->getAccountConfig()->getMarkupLanguage();
		$b .= "&eauid=". $this->getTxnInfo()->getAccountID();
		$b .= "&clientid=". $this->getTxnInfo()->getClientConfig()->getID();
		$b .= "&accountid=". $this->getTxnInfo()->getClientConfig()->getAccountConfig()->getID();
		$b .= "&store_card=". $this->getTxnInfo()->getClientConfig()->getStoreCard();
		$b .= "&auto_store_card=". parent::bool2xml($this->getTxnInfo()->autoStoreCard() );
		
		$obj_HTTP = new HTTPClient(new Template(), $oCI);
		$obj_HTTP->connect();
		$code = $obj_HTTP->send($this->constHTTPHeaders(), $b);
		$obj_HTTP->disConnect();
		if ($code == 200)
		{
			$sql = "UPDATE Log".sSCHEMA_POSTFIX.".Transaction_Tbl
					SET pspid = ". Constants::iDIBS_PSP ."
					WHERE id = ". $this->getTxnInfo()->getID();
//			echo $sql ."\n";
			$this->getDBConn()->query($sql);
			
			$data = array("psp-id" => Constants::iDIBS_PSP,
						  "url" => $obj_HTTP->getReplyBody() );
			$this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_INIT_WITH_PSP_STATE, serialize($data) );
			$xml = str_replace("card_number>", "card-number>", $obj_HTTP->getReplyBody() );
			$xml = str_replace("expiry_month>", "expiry-month>", $xml);
			$xml = str_replace("expiry_year>", "expiry-year>", $xml);
			$xml = str_replace("accept_url>", "accept-url>", $xml);
			$xml = str_replace("cancel_url>", "cancel-url>", $xml);
			$xml = str_replace("decline_url>", "decline-url>", $xml);
			$xml = str_replace("hidden_fields>", "hidden-fields>", $xml);
			// Replace _ with - without changing the hidden fields where "store_card" is also present
			$obj_XML = simplexml_load_string($xml); 
			$obj_XML->{'store-card'} = (string) $obj_XML->{'store_card'};
			unset($obj_XML->{'store_card'});
		}
		// Error: Unable to initialize payment transaction
		else
		{
			trigger_error("Unable to initialize payment transaction with DIBS. HTTP Response Code: ". $code ."\n". var_export($obj_HTTP, true), E_USER_WARNING);
			
			throw new mPointException("DIBS returned HTTP Code: ". $code, 1100);
		}
		
		return $obj_XML;
	}

	public function getPSPID() { return Constants::iDIBS_PSP; }
	
	private function _getMaskedCardNumber($cardno)
	{
		return substr($cardno, 0, 6) . str_repeat("*", strlen($cardno) - 10) . substr($cardno, -4);
	}
}
?>