<?php

// Require API for Simple DOM manipulation
require_once(sLIB_PATH ."stripe/Stripe.php");
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol.
 * The Stripe' subpackage is a specific implementation capable of imitating Stripe' own protocol.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage DIBS
 * @version 1.01
 */

/**
 * Model Class containing all the Business Logic for handling Callback requests from Stripe.
 *
 */
class Stripe_PSP extends Callback
{

	public function notifyClient($sid, array $_post)
	{
		
	}
	
	public function auth($ticket, $apiKey, $cardID, $storecard)
	{		
		// Construct Order ID
		$oid = $this->getTxnInfo()->getOrderID();
		if (empty($oid) === true) { $oid = $this->getTxnInfo()->getID(); }		
		Stripe::setApiKey($apiKey);
		if(empty($ticket) === true )
		{
			$ticket = array('number' => '4242424242424242', 'exp_month' => 5, 'exp_year' => 2018);	
		}
		$customerID;
		
		try 
		{
			if ($storecard == true)
			{
				$customer =	Stripe_Customer::create(array("description" => "",
						"card" => $cardID) );
				
				$charge = Stripe_Charge::create(array("amount" =>  $this->getTxnInfo()->getAmount(),
													  "currency" => $this->getTxnInfo()->getCountryConfig()->getCurrency(),
													  "customer" => $customer->id,
													  "capture" => "false", $aParams) );
				
				
			}
			else 
			{	
				$charge = Stripe_Charge::create(array("amount" =>  $this->getTxnInfo()->getAmount(),
						"currency" => $this->getTxnInfo()->getCountryConfig()->getCurrency(),
						"card" => $ticket,
						"capture" => "false", $aParams) );
			}
				
			$this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_ACCEPTED_STATE, serialize($charge) );
				
			if ($charge->paid === true)
			{
				$sql = "UPDATE Log".sSCHEMA_POSTFIX.".Transaction_Tbl
								SET pspid = ". Constants::iSTRIPE_PSP .", extid = '". $this->getDBConn()->escStr($charge->id)."' cardid = ". intval($cardID) ."
								WHERE id = ". $this->getTxnInfo()->getID();
//					echo $sql ."\n";
				$this->getDBConn()->query($sql);
				$iStateID = $this->completeTransaction(Constants::iSTRIPE_PSP, 
													   $charge->id,
													   $cardID, Constants::iPAYMENT_ACCEPTED_STATE,
													   $this->getTxnInfo()->getFee(),
													   array('0' => var_export($charge, true) ) );
				if ($this->getTxnInfo()->useAutoCapture() === true)
				{
					return	$this->capture($charge->id, $apiKey);
				}
				else { return 2000; }
			
			}
			
				
		}
		catch (Stripe_CardError $e) 
		{
			
		}
		
	}
	
	public function capture($transactionID, $apiKey)
	{
		Stripe::setApiKey($apiKey);
		try
		{
			$charge = Stripe_Charge::retrieve($transactionID);
			$charge->capture();
			if ($charge->captured === true)
			{
				$this->completeCapture(intval($charge->amount) , 0, array('0' => var_export($charge, true) ) );
			}
			return 2001;
		}
		catch (Stripe_CardError $e)
		{
		
		}
		
	}
	
	
	public function refund($txn, $amount, $apiKey)
	{
		Stripe::setApiKey($apiKey);
		try
		{
			$deposit = Stripe_Charge::retrieve($txn);
			$refund = $deposit->refunds->create();

			$this->newMessage( $this->getTxnInfo ()->getID (), Constants::iPAYMENT_REFUNDED_STATE, var_export($refund,true) );
			return 0;	
		}
		catch (Stripe_CardError $e)
		{
			trigger_error("Transaction: ". $this->getTxnInfo()->getID() ."(". $transactionID .") Could not be  Refunded, Stripe returned : ". $e->getMessage(), E_USER_WARNING);
			return -1;
		}
	}
	


	public function initCallback(HTTPConnInfo &$oCI, $cardid, $txnid, $cardno, $expiry)
	{

	}
	
	public function initialize(HTTPConnInfo &$oCI, $merchant, $account, $currency, $cardid)
	{
	
	}
}
?>