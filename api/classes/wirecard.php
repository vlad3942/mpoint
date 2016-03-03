<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol. 
 *
 * @author Abhishek Sawant
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage Wire Card
 * @version 1.00
 */

/* ==================== Data Cash Exception Classes Start ==================== */
/**
 * Super class for all Data Cash Exceptions
 */
class WireCardException extends CallbackException { }
/* ==================== Data Cash Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: Data Cash
 *
 */
class WireCard extends CPMPSP
{
	public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new DataCashException("Method: getPaymentData is not supported by Wire Card"); }

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
	 * @param 	string $txnid 	Transaction ID returned by the PSP
	 * @param 	integer $cid 	Unique ID for the Credit Card the customer used to pay for the Purchase
	 * @param 	integer $sid 	Unique ID indicating that final state of the Transaction
	 * @param 	integer $fee	The amount the customer will pay in fees for the Transaction. Default value 0
	 * @param 	array $debug 	Array of Debug data which should be logged for the state (optional)
	 * @return	integer
	 * @throws 	CallbackException
	 */
	public function completeTransaction($pspid, $txnid, $cid, $sid, $fee=0, array $debug=null)
	{
		if (empty($txnid) == true) { $sql = ""; }
		else { $sql = ", extid = '". $this->getDBConn()->escStr($txnid) ."'"; }
		if ($this->getTxnInfo()->getAccountID() > 0) { $sql .= ", euaid = ". $this->getTxnInfo()->getAccountID(); }
		else { $sql .= ", euaid = NULL"; }
	
		$sql = "UPDATE Log".sSCHEMA_POSTFIX.".Transaction_Tbl
				SET pspid = ". intval($pspid) .", cardid = ". intval($cid).", fee =".intval($fee) . $sql ."
				WHERE id = ". $this->getTxnInfo()->getID();
		//		echo $sql ."\n";exit;
		$res = $this->getDBConn()->query($sql);
	
		// Transaction completed successfully
		if (is_resource($res) === true)
		{
			if ($this->getDBConn()->countAffectedRows($res) == 1 || $sid != Constants::iPAYMENT_ACCEPTED_STATE) 
			{ 
				$this->newMessage($this->getTxnInfo()->getID(), $sid, var_export($debug, true) ); 
			}
		}
		// Error: Unable to complete log for Transaction
		else
		{
			$this->newMessage($this->getTxnInfo()->getID(), $sid, var_export($debug, true) );
			throw new CallbackException("Unable to complete log for Transaction: ". $this->getTxnInfo()->getID(), 1001);
		}
	
		return $sid;
	}
	
	public function getPSPID() { return Constants::iWIRE_CARD_PSP; }
	
	public function authTicket($obj_PSPConfig, $obj_Elem)
	{
		$code = 0;
		$b  = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<authorize client-id="'. $this->getClientConfig()->getID(). '" account="'. $this->getClientConfig()->getAccountConfig()->getID(). '">';
		$b .= $obj_PSPConfig->toXML();
		
		$txnXML = $this->_constTxnXML();
		$b .= $txnXML;
		
		list($expiry_month, $expiry_year) = explode("/", $obj_Elem->expiry);
		
		$b .= '<card type="'.strtolower(trim($obj_Elem->type)).'">';
		$b .= '<masked_account_number>'. $obj_Elem->mask .'</masked_account_number>';
		$b .= '<expiry-month>'. $expiry_month .'</expiry-month>';
		$b .= '<expiry-year>'. $expiry_year .'</expiry-year>';
		$b .= '<token>'. $obj_Elem->ticket .'</token>';
		$b .= '</card>';
	
		$obj_txnXML = simpledom_load_string($txnXML);
		$euaid = intval($obj_txnXML->xpath("/transaction/@eua-id")->{'eua-id'});
		if ($euaid > 0) { $b .= $this->getAccountInfo($euaid); }
		
		$b .= '</authorize>';
		$b .= '</root>';
				
		try
		{
			$obj_ConnInfo = $this->_constConnInfo($this->aCONN_INFO["paths"]["auth"]);
		
			$obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
			$obj_HTTP->connect();
			$code = $obj_HTTP->send($this->constHTTPHeaders(), $b);
			$obj_HTTP->disConnect();
			if ($code == 200)
			{
				$obj_XML = simplexml_load_string($obj_HTTP->getReplyBody() );
				$code = $obj_XML->status["code"];
				// save ext id in database
				$sql = "UPDATE Log".sSCHEMA_POSTFIX.".Transaction_Tbl
						SET pspid = ". $obj_PSPConfig->getID() ."
						WHERE id = ". $this->getTxnInfo()->getID();
				//				echo $sql ."\n";
				$this->getDBConn()->query($sql);
			}
			else { throw new mPointException("Authorization failed with PSP: ". $obj_PSPConfig->getName() ." responded with HTTP status code: ". $code. " and body: ". $obj_HTTP->getReplyBody(), $code ); }
		}
		catch (mPointException $e)
		{
			trigger_error("Authorization failed of txn: ". $this->getTxnInfo()->getID(). " failed with code: ". $e->getCode(). " and message: ". $e->getMessage(), E_USER_ERROR);
		}
		
		return $code;
	}
	
}
