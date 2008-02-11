<?php
/**
 * The Info package contains various data classes holding information such as:
 * 	- Transaction specific information as determined by the input from the Client and the Customer's Mobile Device
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Info
 * @subpackage TxnInfo
 * @version 1.0
 */

/**
 * Data class for hold all data relevant for a Transaction
 *
 */
class TxnInfo
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
	 * Configuration for the Client who owns the Transaction
	 *
	 * @var ClientConfig
	 */
	private $_obj_ClientConfig;
	
	/**
	 * Total amount the customer will pay for the Transaction
	 *
	 * @var integer
	 */
	private $_iAmount;
	/**
	 * Client's Order ID of the Transaction
	 *
	 * @var string
	 */
	private $_sOrderID;
	/**
	 * Customer's Device Address, this is most likely the customer's MSISDN to his / her mobile phone
	 *
	 * @var string
	 */
	private $_sAddress;
	/**
	 * GoMobile's ID for the Customer's Mobile Network Operator
	 *
	 * @var integer
	 */
	private $_iOperatorID;
	
	/**
	 * Absolute URL to the Client's Logo which will be displayed on all payment pages
	 *
	 * @var string
	 */
	private $_sLogoURL;
	/**
	 * Absolute URL to the CSS file that should be used to customising the payment pages
	 *
	 * @var string
	 */
	private $_sCSSURL;
	/**
	 * Absolute URL where the Customer should be returned to upon successfully completing the Transaction
	 *
	 * @var string
	 */
	private $_sAcceptURL;
	/**
	 * Absolute URL where the Customer should be returned to in case he / she cancels the Transaction midway
	 *
	 * @var string
	 */
	private $_sCancelURL;
	/**
	 * Absolute URL to the Client's Back Office where mPoint should send the Payment Status to
	 *
	 * @var string
	 */
	private $_sCallbackURL;
	/**
	 * The language that all payment pages should be rendered in by default for the Client
	 *
	 * @var string
	 */
	private $_sLanguage;
	
	/**
	 * Default Constructor
	 *
	 * @param 	integer $id 		Unique ID for the Transaction
	 * @param 	integer $tid 		Unique ID for the Transaction Type
	 * @param 	ClientConfig $oCC 	Configuration for the Client who owns the Transaction
	 * @param 	integer $a 			Total amount the customer will pay for the Transaction
	 * @param 	string $orid 		Clients Order ID of the Transaction
	 * @param 	string $addr 		Customer's Device Address, this is most likely the customer's MSISDN to his / her mobile phone
	 * @param 	integer $oid 		GoMobile's ID for the Customer's Mobile Network Operator
	 * @param 	string $lurl 		Absolute URL to the Client's Logo which will be displayed on all payment pages
	 * @param 	string $cssurl 		Absolute URL to the CSS file that should be used to customising the payment pages
	 * @param 	string $aurl 		Absolute URL where the Customer should be returned to upon successfully completing the Transaction
	 * @param 	string $curl 		Absolute URL where the Customer should be returned to in case he / she cancels the Transaction midway
	 * @param 	string $cburl 		Absolute URL to the Client's Back Office where mPoint should send the Payment Status to
	 * @param 	string $l 			The language that all payment pages should be rendered in by default for the Client
	 */
	public function __construct($id, $tid, ClientConfig &$oCC, $a, $orid, $addr, $oid, $lurl, $cssurl, $aurl, $curl, $cburl, $l)
	{
		$this->_iID =  (integer) $id;
		$this->_iTypeID =  (integer) $tid;
		$this->_obj_ClientConfig = $oCC;
		$this->_iAmount =  (integer) $a;
		$this->_iOrderID =  trim($orid);
		$this->_sAddress = trim($addr);
		$this->_iOperatorID =  (integer) $oid;
		
		$this->_sLogoURL = trim($lurl);
		$this->_sCSSURL = trim($cssurl);
		$this->_sAcceptURL = trim($aurl);
		$this->_sCancelURL = trim($curl);
		$this->_sCallbackURL = trim($cburl);
		
		$this->_sLanguage = trim($l);
	}
	
	/**
	 * Returns the Unique ID for the Transaction
	 *
	 * @return 	integer
	 */
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
	/**
	 * Returns the Configuration for the Client who owns the Transaction.
	 *
	 * @return 	ClientConfig
	 */
	public function getClientConfig() { return $this->_obj_ClientConfig; }
	/**
	 * Returns the Total amount the customer will pay for the Transaction
	 *
	 * @return 	integer
	 */
	public function getAmount() { return $this->_iAmount; }
	/**
	 * Returns the Client's Order ID of the Transaction.
	 *
	 * @return 	string
	 */
	public function getOrderID() { return $this->_sOrderID; }
	/**
	 * Returns the Customer's Device Address, this is most likely the customer's MSISDN to his / her mobile phone
	 *
	 * @return 	string
	 */
	public function getAddress() { return $this->_sAddress; }
	/**
	 * Returns the GoMobile's ID for the Customer's Mobile Network Operator
	 *
	 * @return 	integer
	 */
	public function getOperator() { return $this->_iOperatorID; }
	
	/**
	 * Returns the Absolute URL to the Client's Logo which will be displayed on all payment pages
	 *
	 * @return 	string
	 */
	public function getLogoURL() { return $this->_sLogoURL; }
	/**
	 * Returns the Absolute URL to the CSS file that should be used to customising the payment pages
	 *
	 * @return 	string
	 */
	public function getCSSURL() { return $this->_sCSSURL; }
	/**
	 * Returns the Absolute URL where the Customer should be returned to upon successfully completing the Transaction
	 *
	 * @return 	string
	 */
	public function getAcceptURL() { return $this->_sAcceptURL; }
	
	/**
	 * Returns the Absolute URL where the Customer should be returned to in case he / she cancels the Transaction midway
	 *
	 * @return 	string
	 */
	public function getCancelURL() { return $this->_sCancelURL; }
	/**
	 * Returns the Absolute URL to the Client's Back Office where mPoint should send the Payment Status to
	 *
	 * @return 	string
	 */
	public function getCallbackURL() { return $this->_sCallbackURL; }
	/**
	 * Returns the language that all payment pages should be rendered in by default for the Client
	 *
	 * @return 	string
	 */
	public function getLanguage() { return $this->_sLanguage; }
	
	/**
	 * Overloaded factory method for producing a new instance of a Transaction Info object.
	 * The data object can either be instantiated from an array of Client Input or from the Transaction Log.
	 * In the latter case the method will first instantiate a new object with the correct Client Configuration for the Transaction.
	 * 
	 * @see 	ClientConfig::produceConfig
	 * 
	 * @param 	integer $id 				Unique ID for the Transaction that should be instantiated
	 * @param 	[ClientConfig|RDB] $obj 	Reference to either a Database Object which handles the active connection to mPoint's database or to and instance of the Client Configuration of the Client who owns the Transaction
	 * @param 	array $input 				Reference to the array of Client Input, this parameter is only needed if the Client Configuration is passed as argument 2.
	 * @return 	TxnInfo
	 */
	public static function produceInfo($id, &$obj, array &$input=null)
	{
		$obj_TxnInfo = null;
		switch (true)
		{
		case ($obj instanceof ClientConfig):	// Instantiate from array of Client Input
			$obj_TxnInfo = new TxnInfo($id, $input["typeid"], $obj, $input["amount"], $input["orderid"], $input["recipient"], $input["operator"], $input["logo-url"], $input["css-url"], $input["accept-url"], $input["cancel-url"], $input["callback-url"], $input["language"]);
			break;
		case ($obj instanceof RDB):				// Instantiate from Transaction Log
			$sql = "SELECT id, typeid, amount, orderid, address, operatorid, logourl, cssurl, accepturl, cancelurl, callbackurl,
						clientid, accountid, keywordid, 
					FROM Log.Transaction_Tbl
					WHERE id = ". intval($id);
			echo $sql ."\n";
			$RS = $obj->getName($sql);
			
			$obj_ClientConfig = ClientConfig::produceConfig($obj, $RS["CLIENTID"], $RS["ACCOUNTID"], $RS["KEYWORDID"]);
			
			$obj_TxnInfo = new TxnInfo($RS["ID"], $RS["TYPEID"], $obj_ClientConfig, $RS["AMOUNT"], $RS["ORDERID"], $RS["ADDRESS"], $RS["OPERATORID"], $RS["LOGOURL"], $RS["CSSURL"], $RS["ACCEPTURL"], $RS["CANCELURL"], $RS["CALLBACKURL"], $RS["LANGUAGE"]);
			break;
		default:								// Error: Argument 2 is an instance of an invalid class
			trigger_error("Argument 2 passed to TxnInfo::produceInfo() must be an instance of ClientConfig or of RDB", E_ERROR);
			break;
		}
		
		return $obj_TxnInfo;
	}
}
?>