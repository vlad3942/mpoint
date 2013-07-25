<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol.
 * The WorldPay subpackage is a specific implementation capable of imitating WorldPay's own protocol.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage Authorize.Net
 * @version 1.00
 */

/**
 * Model Class containing all the Business Logic for handling interaction with NetAxept
 *
 */
class NetAxept extends Callback
{

	public function initialize(HTTPConnInfo &$oCI, $merchant, $account, $currency, $cardid)
	{
		$obj_SOAP = new SOAPClient("https://". $oCI->getHost() . $oCI->getPath(), array("trace" => true,
																						"exceptions" => true) );
		$sOrderNo = $this->getTxnInfo()->getOrderID();
		if (empty($sOrderNo) === true) { $sOrderNo = $this->getTxnInfo()->getID(); }
		$aParams = array("merchantId" => $merchant,
						 "token" => $oCI->getPassword(),
						 "request" => array("Description" => "mPoint Transaction: ". $this->getTxnInfo()->getID() ." for Order: ". $this->getTxnInfo()->getOrderID(),
											"Environment" => array("WebServicePlatform" => "PHP5"),
											"Order" => array("Amount" => $this->getTxnInfo()->getAmount(),
															 "CurrencyCode" => $currency,
															 "OrderNumber" => $sOrderNo /*,
															 "UpdateStoredPaymentInfo" => ""*/),
//											"Recurring" => array("PanHash" => "",
//																 "Type" => "S"),
											"ServiceType" => "M",
											"Terminal" => array("Language" => "en_GB",
																"RedirectUrl" => "http://". $_SERVER['HTTP_HOST'] ."/netaxept/accept.php?mpoint-id=". $this->getTxnInfo()->getID(),
																"SinglePage" => "true"),
											"TransactionId" => $this->getTxnInfo()->getID() ."-". time(),
											/*"TransactionReconRef" => ""*/) );
file_put_contents(sLOG_PATH ."/jona.log", var_export($aParams, true) );
		$obj_Std = $obj_SOAP->Register($aParams);
		
		if (intval($obj_Std->RegisterResult->TransactionId) == $this->getTxnInfo()->getID() )
		{
			/*
			$fp = fopen("https://". $oCI->getHost() ."/Terminal/default.aspx?merchantId=". $merchant ."&transactionId=". $obj_Std->RegisterResult->TransactionId, "r");
			if (is_resource($fp) === true)
			{
				$str = "";
				while (feof($fp) === false)
				{
					$str .= fread($fp, 8096);
				}
				fclose($fp);
			}
			if (strlen($str) > 0)
			{
				$aMatches = array();
				if (preg_match_all('/<input type="hidden" name="(.+?)" .*?value="(.+?)".*>/i', $str, &$aMatches) > 0)
				{
					
				}
				
			}
			*/
			$xml = '<?xml version="1.0" encoding="UTF-8"?>';
			$xml .= '<root>';
			$xml .= '<url method="post" content-type="application/x-www-form-urlencoded">https://'. $oCI->getHost() .'/Terminal/default.aspx</url>';
			$xml .= '<card-number>pan</card-number>';
			$xml .= '<expiry-month>expiryDate</expiry-month>';
			$xml .= '<expiry-year>expiryDate</expiry-year>';
			$xml .= '<cvc>securityCode</cvc>';
			$xml .= '<hidden-fields>';
			$xml .= '<merchantId>'. $merchant .'</merchantId>';
			$xml .= '<transactionId>'. $obj_Std->RegisterResult->TransactionId .'</transactionId>';
			$xml .= '</hidden-fields>';
			/*
			$xml .= '<hidden-fields>';
			for ($i=0; $i<count($aMatches[1]); $i++)
			{
				$tag = htmlspecialchars($aMatches[1][$i], ENT_NOQUOTES);
				$xml .= '<'. $tag .'>'. htmlspecialchars(urlencode($aMatches[2][$i]), ENT_NOQUOTES) .'</'. $tag .'>';
			}
			$xml .= '</hidden-fields>';
			*/
			$xml .= '</root>';
			$data = array("psp-id" => Constants::iNETAXEPT_PSP,
						  "url" => var_export($obj_Std, true) );
			$this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_INIT_WITH_PSP_STATE, serialize($data) );
			
			$obj_XML = simplexml_load_string($xml);
		}
		// Error: Unable to initialize payment transaction
		else
		{
			trigger_error("Unable to initialize payment transaction with NetAxept. HTTP Response Code: ". $code ."\n". var_export($obj_HTTP, true), E_USER_WARNING);
			
			throw new mPointException("NetAxept returned HTTP Code: ". $code, 1100);
		}
		
		return $obj_XML;
	}
	/**
	 * Notifies the Client of the Payment Status by performing a callback via HTTP.
	 * The method will re-construct the data received from WannaFind after having removed the following mPoint specific fields:
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
	 * @param 	array $_post 	Array of data received from WannaFind via HTTP POST
	 */
	public function notifyClient($sid, array $_post)
	{
		parent::notifyClient($sid, $_post["transact"], $_post["amount"], $_post["cardid"], str_replace("X", "*", $_post["cardnomask"]) );
	}
	
	/**
	 * Authorises a payment with WannaFind for the transaction using the provided ticket.
	 * The ticket represents a previously stored card.
	 * The method will return WannaFind' transaction ID if the authorisation is accepted or one of the following status codes if the authorisation is declined:
	 * 	-1. Rejected by WannaFind.
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
		
		$b = "?batchlist=". $ticket;
		$b .= ";". $this->getTxnInfo()->getAmount();
		$b .= ";". $this->getTxnInfo()->getID();
		$aLogin = $this->getMerchantLogin($this->getTxnInfo()->getClientConfig()->getID(), Constants::iWANNAFIND_PSP);
		
		$obj_HTTP = parent::send("https://betaling.wannafind.dk/authsubscribe.php". $b, $this->constHTTPHeaders(), "", $aLogin["username"], $aLogin["password"]);
		
		$aStatus = explode(";", $obj_HTTP->getReplyBody() );
		$id = -1;
		// Authorization Approved
		if ($aStatus[0] == "APPROVED")
		{
			$id = $aStatus[2];
		}
		else { trigger_error("Authorisation declined by WannaFind for Ticket: ". $ticket .", ". trim($obj_HTTP->getReplyBody() ), E_USER_WARNING); }

		return $id;
	}

	/**
	 * Performs a capture operation with WannaFind for the provided transaction.
	 * The method will log one the following status codes from WannaFind:
	 * 	0. Capture succeeded
	 * 	1. No response from acquirer.
	 * 	2. Error in the parameters sent to the WannaFind server. An additional parameter called "message" is returned, with a value that may help identifying the error.
	 * 	3. Credit card expired.
	 * 	4. Rejected by acquirer.
	 * 	5. Authorisation older than7 days.
	 * 	6. Transaction status on the WannaFind server does not allow capture.
	 * 	7. Amount too high.
	 * 	8. Amount is zero.
	 * 	9. Order number (orderid) does not correspond to the authorisation order number.
	 * 10. Re-authorisation of the transaction was rejected.
	 * 11. Not able to communicate with the acquier.
	 * 12. Confirm request error
	 * 14. Capture is called for a transaction which is pending for batch - i.e. capture was already called
	 * 15. Capture was blocked by WannaFind.
	 * 
	 * @link	http://tech.dibs.dk/toolbox/dibs-error-codes/
	 * 
	 * @param 	integer $txn	Transaction ID previously returned by WannaFind during authorisation
	 * @return	integer
	 * @throws	E_USER_WARNING
	 */
	public function capture($txn)
	{
//		$code = $this->status($txn);
		$code = 2;
		// Transaction ready for Capture
		if ($code == 2)
		{
			$b = "transacknum=". $txn;
			$b .= "&shopid=". $this->getMerchantAccount($this->getTxnInfo()->getClientConfig()->getID(), Constants::iWANNAFIND_PSP);
			$aLogin = $this->getMerchantLogin($this->getTxnInfo()->getClientConfig()->getID(), Constants::iWANNAFIND_PSP);
			
			$obj_HTTP = parent::send("https://betaling.wannafind.dk/api/pg.api.capturetransack.php?". $b, $this->constHTTPHeaders(), "", $aLogin["username"], $aLogin["password"]);
			$aStatus = explode("<BR>", $obj_HTTP->getReplyBody() );
			
			// Payment successfully captured
			if (substr($obj_HTTP->getReplyBody(), 0, 8) == "APPROVED" || $aStatus[0] == "APPROVED")
			{
				$this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_CAPTURED_STATE, utf8_encode($obj_HTTP->getReplyBody() ) );
				
				return 0;
			}
			// Capture Declined
			else
			{
				$this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_DECLINED_STATE, var_export($aStatus, true) );
				trigger_error("Capture declined by WannaFind for Transaction: ". $txn .", ". trim($obj_HTTP->getReplyBody() ), E_USER_WARNING);
				
				return 1;
			}
		}
		// Capture already completed
		elseif ($code == 11)
		{
			$this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_CAPTURED_STATE, "WannaFind returned code: ". $code ." from status call");
			
			return 0;
		}
		else { return $code; }
	}
	

	/**
	 * Initialises Callback to the Client.
	 *
	 * @param 	HTTPConnInfo $oCI 	Connection Info required to communicate with the Callback component for Cellpoint Mobile
	 * @param 	integer $cardid		Unique ID of the Card Type that was used in the payment transaction
	 * @param 	integer $txnid		Transaction ID from WannaFind returned in the "transact" parameter
	 */
	public function initCallback(HTTPConnInfo &$oCI, $cardid, $txnid)
	{
		$b = "mpoint-id=". $this->getTxnInfo()->getID();
		$b .= "&transact=". $txnid;
		$b .= "&cardid=". $cardid;
		$b .= "&language=". $this->getTxnInfo()->getLanguage();
		$b .= "&actioncode=0&authtype=auth";
		$b .= "&amount=". $this->getTxnInfo()->getAmount();

		$obj_HTTP = new HTTPClient(new Template(), $oCI);
		$obj_HTTP->connect();
		$obj_HTTP->send($this->constHTTPHeaders(), $b);
		$obj_HTTP->disConnect();
	}
}
?>