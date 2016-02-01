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
	 * @param 	integer $txnid 	Transaction ID returned by the PSP
	 * @param 	integer $cid 	Unique ID for the Credit Card the customer used to pay for the Purchase
	 * @param 	integer $sid 	Unique ID indicating that final state of the Transaction
	 * @param 	integer $fee	The amount the customer will pay in fees for the Transaction. Default value 0
	 * @param 	array $debug 	Array of Debug data which should be logged for the state (optional)
	 * @return	integer
	 * @throws 	CallbackException
	 */
	public function completeTransaction($pspid, $txnid, $cid, $sid, $fee=0, array $debug=null)
	{
		if (intval($txnid) == 0) { $sql = ""; }
		else { $sql = ", extid = '". $this->getDBConn()->escStr($txnid) ."'"; }
		if ($this->_obj_TxnInfo->getAccountID() > 0) { $sql .= ", euaid = ". $this->_obj_TxnInfo->getAccountID(); }
		else { $sql .= ", euaid = NULL"; }
	
		$sql = "UPDATE Log".sSCHEMA_POSTFIX.".Transaction_Tbl
				SET pspid = ". intval($pspid) .", cardid = ". intval($cid).", fee =".intval($fee) . $sql ."
				WHERE id = ". $this->_obj_TxnInfo->getID();
		//		echo $sql ."\n";
		$res = $this->getDBConn()->query($sql);
	
		// Transaction completed successfully
		if (is_resource($res) === true)
		{
			if ($this->getDBConn()->countAffectedRows($res) == 1 || $sid != Constants::iPAYMENT_ACCEPTED_STATE) 
			{ 
				$this->newMessage($this->_obj_TxnInfo->getID(), $sid, var_export($debug, true) ); 
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
}
