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
 * Model Class containing all the Business Logic for handling interaction with WannaFind
 *
 */
class WannaFind extends Callback implements Captureable
{
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
	 * 	5. Authorisation older than 7 days.
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
	 * @param 	integer $iAmount	Partial capture is currently unsupported by this implementation
	 * @return	integer
	 * @throws	E_USER_WARNING
	 */
	public function capture($iAmount = -1)
	{
		$txn = $this->getTxnInfo()->getExternalID();
		if ($iAmount != -1 || $iAmount != $this->getTxnInfo()->getAmount() ) { trigger_error("Partial capture not supported by wannafind PSP implementation. Input amount: ". $iAmount. " for transaction: ". $this->getTxnInfo()->getID(), E_USER_WARNING); }

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
				$this->completeCapture( $this->getTxnInfo()->getAmount(), $this->getTxnInfo()->getFee(), utf8_encode($obj_HTTP->getReplyBody() ) );
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

	public function getPSPID() { return Constants::iWANNAFIND_PSP; }
}
?>