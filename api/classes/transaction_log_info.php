<?php
/**
 * 
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package mConsole
 * @subpackage Search
 * @version 1.00
 */

/**
 * Data class for hold all data relevant for a Transaction Log Entry
 *
 */
class TransactionLogInfo
{
	/**
	 * Unique ID for the Transaction
	 *
	 * @var integer
	 */
	private $_iID;
	/**
	 * Unique ID for the Transaction Type.
	 * mPoint currently supports the following Transaction Types:
	 * 	11. Call Centre Purchase
	 * 	12. Call Centre Subscruption
	 * 	21. SMS Purchase
	 * 	22.	SMS Subscription
	 * 	31. Web Purchase
	 * 	32. Web Subscription
	 *
	 * @var integer
	 */
	private $_iTypeID;
	/**
	 * Client's Order Number of the Transaction
	 *
	 * @var string
	 */
	private $_sOrderNumber;
	/**
	 * External ID of the Transaction (usually the txn ref of the Payment Service Provider)
	 *
	 * @var string
	 */
	private $_sExternalID;
	/**
	 * Basic configuration for the Client who owns the Transaction
	 *
	 * @var BasicConfig
	 */
	private $_obj_Client;
	/**
	 * Basic configuration for the Sub-Account through which mPoint processed the Transaction
	 *
	 * @var BasicConfig
	 */
	private $_obj_SubAccount;
	/**
	 * Basic configuration for the Payment Service Provider (PSP) who processed the transaction
	 *
	 * @var BasicConfig
	 */
	private $_obj_PSP;
	/**
	 * Basic configuration for the Payment Method (Card) that was used for the Transaction
	 *
	 * @var BasicConfig
	 */
	private $_obj_PaymentMethod;
	/**
	 * The current state of the Transaction
	 *
	 * @var integer
	 */
	private $_iStateID;
	/**
	 * Configuration for the Country the transaction was processed in
	 *
	 * @var CountryConfig
	 */
	private $_obj_CountryConfig;
	/**
	 * Total amount the customer paid for the Transaction without fee
	 *
	 * @var AmountInfo
	 */
	private $_obj_Amount;
	/**
	 * The full amount that has been captured for the Transaction
	 *
	 * @var AmountInfo
	 */
	private $_obj_CapturedAmount;
	/**
	 * Total number of points the customer paid  for the Transaction
	 *
	 * @var AmountInfo
	 */
	private $_obj_Points;
	/**
	 * Total number of points the customer was rewarded for completing the Transaction
	 *
	 * @var AmountInfo
	 */
	private $_obj_Reward;
	/**
	 * Total amount the customer has been refunded for the Transaction
	 *
	 * @var AmountInfo
	 */
	private $_obj_Refund;
	/**
	 * The amount the customer paid in fees for the Transaction
	 *
	 * @var AmountInfo
	 */
	private $_obj_Fee;
	/**
	 * The Client Mode in which the Transaction is Processed
	 * 	0. Production
	 * 	1. Test Mode with prefilled card Info
	 * 	2. Certification Mode
	 *
	 * @var integer
	 */
	private $_iMode;
	/**
	 * The customer's profile
	 *
	 * @var CustomerInfo
	 */
	private $_obj_CustomerInfo;
	/**
	 * The IP address where the transaction originated from
	 *
	 * @var string
	 */
	private  $_sIP;
	/**
	 * The timestamp when the transaction was created in the format: YYYY-MM-DD hh:mm:ss+00:00
	 *
	 * @var timestamp
	 */
	private  $_sTimestamp;
	/**
	 * List of Message Information instances identifying which states the transaction has gone through
	 * 
	 * @var array
	 */
	private $_aObj_MessageInfos = array();
	/**
	 * The Description of a Order for a Customer
	 *
	 * @var string
	 */
	private  $_sDescription;
	
	/**
	 * Default constructor
	 * 
	 * @param integer $id 				mPoint's unique ID for the Transaction
	 * @param integer $tid 				mPoint's unique ID for the Transaction Type
	 * @param string $ono				Clients Order Number of the Transaction
	 * @param string $extid 			External ID of the Transaction (usually the txn ref of the Payment Service Provider)
	 * @param BasicConfig $oClient		Basic configuration for the Client who owns the Transaction
	 * @param BasicConfig $oSubAccount	Basic configuration for the Sub-Account through which mPoint processed the Transaction
	 * @param BasicConfig $oPSP			Basic configuration for the Payment Service Provider (PSP) who processed the transaction
	 * @param BasicConfig $oPM			Basic configuration for the Payment Method (Card) that was used for the Transaction
	 * @param integer $sid 				The current state of the Transaction
	 * @param CountryConfig $oCC		Configuration for the Country the transaction was processed in
	 * @param long $amt 				Total amount the customer will pay for the Transaction without fee
	 * @param long $cptamt				The Full amount that has been captured for the Transaction
	 * @param integer $pnt 				Total number of points the customer will pay for the Transaction
	 * @param integer $rwd 				Total number of points the customer will be rewarded for completing the transaction
	 * @param integer $rfnd 			Total amount the customer has been refunded for the Transaction
	 * @param integer $fee				The amount the customer will pay in fee´s for the Transaction.
	 * @param integer $m 				The Client Mode in which the Transaction should be Processed
	 * @param CustomerInfo $oCI			The customer's profile
	 * @param string $ip				String that holds the customers IP address
	 * @param timestamp $ts				The timestamp when the transaction was created in the format: YYYY-MM-DD hh:mm:ss+00:00
	 * @param array $aObj_Msgs			List of Message Information instances identifying which states the transaction has gone through
	 * @param string $desc 				String that holds the description of an order
	 */
	public function __construct($id, $tid, $ono, $extid, ClientConfig $oClient, BasicConfig $oSubAccount, BasicConfig $oPSP=null, BasicConfig $oPM=null, $sid, CountryConfig $oCC, $amt, $cptamt, $pnt, $rwd, $rfnd, $fee, $m, CustomerInfo $oCI, $ip, $ts, array $aObj_Msgs, $desc="")
	{
		$this->_iID =  (integer) $id;
		$this->_iTypeID =  (integer) $tid;
		$this->_sOrderNumber = trim($ono);
		$this->_sExternalID = trim($extid);
		$this->_obj_Client = $oClient;
		$this->_obj_SubAccount = $oSubAccount;
		$this->_obj_PSP = $oPSP;
		$this->_obj_PaymentMethod = $oPM;
		$this->_iStateID =  (integer) $sid;
		$this->_obj_CountryConfig = $oCC;
		
		$_sCurrencyCode =  $oCC->getCurrency();
		if($currencyCode != null)
			$_sCurrencyCode = $currencyCode;
		
		$this->_obj_Amount = new AmountInfo($amt, $oCC->getID(), $_sCurrencyCode, $oCC->getSymbol(), $oCC->getPriceFormat() );
		if (intval($cptamt) > 0) { $this->_obj_CapturedAmount = new AmountInfo($cptamt, $oCC->getID(), $oCC->getCurrency(), $oCC->getSymbol(), $oCC->getPriceFormat() ); }
		if (intval($pnt) > 0) { $this->_obj_Points = new AmountInfo($pnt, 0, "points", "points", "{PRICE} {CURRENCY}"); }
		if (intval($rwd) > 0) { $this->_obj_Reward = new AmountInfo($rwd, 0, "points", "points", "{PRICE} {CURRENCY}"); }
		if (intval($rfnd) > 0) { $this->_obj_Refund = new AmountInfo($rfnd, $oCC->getID(), $oCC->getCurrency(), $oCC->getSymbol(), $oCC->getPriceFormat() ); }
		if (intval($fee) > 0) { $this->_obj_Fee = new AmountInfo($fee, $oCC->getID(), $oCC->getCurrency(), $oCC->getSymbol(), $oCC->getPriceFormat() ); }
		
		$this->_iMode = (integer) $m;
		$this->_obj_CustomerInfo = $oCI;
		$this->_sIP = trim($ip);
		$this->_sTimestamp = trim($ts);
		
		$this->_aObj_MessageInfos = $aObj_Msgs;
		$this->_sDescription = trim($desc);
	}

	public function getID() { return $this->_iID; }
	/**
	 * Returns the Unique ID for the Transaction Type.
	 * mPoint currently supports the following Transaction Types:
	 * 	11. Call Centre Purchase
	 * 	12. Call Centre Subscruption
	 * 	21. SMS Purchase
	 * 	22.	SMS Subscription
	 * 	31. Web Purchase
	 * 	32. Web Subscription
	 *
	 * @return 	integer
	 */
	public function getTypeID() { return $this->_iTypeID; }
	public function getOrderNumber() { return $this->_sOrderNumber; }
	public function getExternalID() { return $this->_sExternalID; }
	public function getClient() { return $this->_obj_Client; }
	public function getSubAccount() { return $this->_obj_SubAccount; }
	public function getPaymentServiceProvider() { return $this->_obj_PSP; }
	public function getPaymentMethod() { return $this->_obj_PaymentMethod; }
	public function getStateID() { return $this->_iStateID; }
	public function getCountryConfig() { return $this->_obj_CountryConfig; }
	public function getAmountInfo() { return $this->_obj_Amount; }
	public function getCapturedAmountInfo() { return $this->_obj_CapturedAmount; }
	public function getPointsInfo() { return $this->_obj_Points; }
	public function getRewardInfo() { return $this->_obj_Reward; }
	public function getRefundInfo() { return $this->_obj_Refund; }
	public function getFeeInfo() { return $this->_obj_Fee; }
	public function getCustomerInfo() { return $this->_obj_CustomerInfo; }
	/*
	 * Returns the mode in which the Transaction is Processed
	 * 	0. Production
	 * 	1. Test Mode with prefilled card info
	 * 	2. Certification Mode
	 *
	 * @return 	integer
	 */
	public function getMode() { return $this->_iMode; }
	public function getIP() { return $this->_sIP; }
	public function getTimestamp() { return $this->_sTimestamp; }
	public function getMessages() { return $this->_aObj_MessageInfos; }
	public function getDescription() { return $this->_sDescription; }
	
	public function toXML()
	{
		$xml  = '<transaction id="'. $this->_iID .'" type-id="'. $this->_iTypeID .'" state-id="'. $this->_iStateID .'"';
		if (strlen($this->_sOrderNumber) > 0) { $xml .= ' order-no="'. htmlspecialchars($this->_sOrderNumber, ENT_NOQUOTES) .'"'; }
		if (strlen($this->_sExternalID) > 0) { $xml .= ' external-id="'. htmlspecialchars($this->_sExternalID, ENT_NOQUOTES) .'"'; }
		$xml .= ' mode="'. $this->_iMode .'">';
		$xml .= '<client id="'. $this->_obj_Client->getID() .'">'. htmlspecialchars($this->_obj_Client->getName(), ENT_NOQUOTES) .'</client>';
		if( ($this->_obj_Client instanceof ClientConfig) === true)
        {
            $xml .= $this->_obj_Client->getCommunicationChannelsConfig()->toXML();
        }
		$xml .= $this->_getSubAccountXML();
		if ( ($this->_obj_PSP instanceof BasicConfig) === true) { $xml .= '<payment-service-provider id="'. $this->_obj_PSP->getID() .'">'. htmlspecialchars($this->_obj_PSP->getName(), ENT_NOQUOTES) .'</payment-service-provider>'; }
		if ( ($this->_obj_PaymentMethod instanceof BasicConfig) === true) { $xml .= '<payment-method id="'. $this->_obj_PaymentMethod->getID() .'">'. htmlspecialchars($this->_obj_PaymentMethod->getName(), ENT_NOQUOTES) .'</payment-method>'; }
		
		$xml .= $this->_obj_Amount->toXML("amount");
		if ( ($this->_obj_CapturedAmount instanceof AmountInfo) === true) { $xml .= $this->_obj_CapturedAmount->toXML("captured-amount"); }
		if ( ($this->_obj_Points instanceof AmountInfo) === true) { $xml .= $this->_obj_Points->toXML("points"); }
		if ( ($this->_obj_Reward instanceof AmountInfo) === true) { $xml .= $this->_obj_Reward->toXML("reward"); }
		if ( ($this->_obj_Refund instanceof AmountInfo) === true) { $xml .= $this->_obj_Refund->toXML("refund"); }
		if ( ($this->_obj_Fee instanceof AmountInfo) === true) { $xml .= $this->_obj_Fee->toXML("fee"); }

		if ( ($this->_obj_CustomerInfo instanceof CustomerInfo) === true) { $xml .= $this->_obj_CustomerInfo->toXML(); }
		$xml .= '<ip>'. htmlspecialchars($this->_sIP, ENT_NOQUOTES) .'</ip>';
		$xml .= '<timestamp>'. htmlspecialchars(str_replace(" ", "T", $this->_sTimestamp), ENT_NOQUOTES) .'</timestamp>';
		if (count($this->_aObj_MessageInfos) > 0)
		{
			$xml .= '<messages>';
			foreach ($this->_aObj_MessageInfos as $obj_MsgInfo)
			{
				$xml .= $obj_MsgInfo->toXML();
			}
			$xml .= '</messages>';
		}
		if (strlen($this->_sDescription) > 0) { $xml .= '<description>'. htmlspecialchars($this->_sDescription, ENT_NOQUOTES) .'</description>'; }
		$xml .= '</transaction>';

		return $xml;
	}
	
	private function _getSubAccountXML(){
		
		$sReturnString = '<sub-account id="'. $this->_obj_SubAccount->getID() .'"';
			
		if(($this->_obj_SubAccount instanceof AccountConfig) === true)
		{
			$sReturnString .= ' markup="'. $this->_obj_SubAccount->getMarkupLanguage().'"';
		}
		$sReturnString .= '>'. htmlspecialchars($this->_obj_SubAccount->getName(), ENT_NOQUOTES) .'</sub-account>';
		
		return $sReturnString;
	}
}
?>