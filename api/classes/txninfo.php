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

/* ==================== Transaction Information Exception Classes Start ==================== */
/**
 * Exception class for all Transaction Information exceptions
 */
class TxnInfoException extends mPointException { }
/* ==================== Transaction Information Exception Classes End ==================== */

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
		$this->_sOrderID =  trim($orid);
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
	 * Converts the data object into XML.
	 * If a User Agent Profile is provided, the method will automatically calculate the width and height of the client logo
	 * after it has been resized to fit the screen resolution of the customer's mobile device.
	 * If not, the width and height will be set to -1.
	 * 
	 * The method will return an XML document in the following format:
	 * 	<transaction id="{UNIQUE ID FOR THE TRANSACTION}" type="{ID FOR THE TRANSACTION TYPE}">
	 *		<amount currency="{CURRENCY AMOUNT IS CHARGED IN}">{TOTAL AMOUNT THE CUSTOMER IS CHARGED FOR THE TRANSACTION}</amount>
	 * 		<price>{AMOUNT FORMATTED FOR BEING DISPLAYED IN THE GIVEN COUNTRY}</price>
	 *		<order-id>{CLIENT'S ORDER ID FOR THE TRANSACTION}</order-id>
	 *		<address>{CUSTOMER'S MSISDN WHERE SMS MESSAGE CAN BE SENT TO}</address>
	 *		<operator>{GOMOBILE ID FOR THE CUSTOMER'S MOBILE NETWORK OPERATOR}</operator>
	 *		<logo>
	 * 			<url>{ABSOLUTE URL TO THE CLIENT'S LOGO}</url>
	 * 			<width>{WIDTH OF THE LOGO AFTER IT HAS BEEN SCALED TO FIT THE SCREENSIZE OF THE CUSTOMER'S MOBILE DEVICE}</width>
	 * 			<height>{HEIGHT OF THE LOGO AFTER IT HAS BEEN SCALED TO FIT THE SCREENSIZE OF THE CUSTOMER'S MOBILE DEVICE}</height>
	 *		</logo>
	 *		<css-url>{ABSOLUTE URL TO THE CSS FILE PROVIDED BY THE CLINET}</css-url>
	 *		<accept-url>{ABSOLUTE URL TO WHERE THE CUSTOMER SHOULD BE DIRECTED UPON SUCCESSFULLY COMPLETING THE PAYMENT}</accept-url>
	 *		<cancel-url>{ABSOLUTE URL TO WHERE THE CUSTOMER SHOULD BE DIRECTED IF THE TRANSACTION IS CANCELLED}</accept-url>
	 *		<callback-url>{ABSOLUTE URL TO WHERE MPOINT SHOULD SEND THE PAYMENT STATUS}</callback-url>
	 *		<language>{LANGUAGE THAT ALL PAYMENT PAGES SHOULD BE TRANSLATED INTO}</language>
	 *	</transaction>
	 *
	 * @see 	iCLIENT_LOGO_SCALE
	 * 
	 * @param 	UAProfile $oUA 	Reference to the User Agent Profile for the Customer's Mobile Device (optional)
	 * @return 	string
	 */
	public function toXML(UAProfile &$oUA=null)
	{
		$sPrice = $this->_obj_ClientConfig->getCountryConfig()->getPriceFormat();
		$sPrice = str_replace("{CURRENCY}", $this->_obj_ClientConfig->getCountryConfig()->getCurrency(), $sPrice);
		$sPrice = str_replace("{PRICE}", number_format($this->_iAmount, 2), $sPrice);
		
		if (is_null($oUA) === false)
		{
			$obj_Image = new Image($this->_sLogoURL);
			if ($oUA->getHeight() * iCLIENT_LOGO_SCALE / 100 < $obj_Image->getSrcHeight() ) { $iHeight = $oUA->getHeight() * iCLIENT_LOGO_SCALE / 100; }
			else { $iHeight = $obj_Image->getSrcHeight(); }
			$obj_Image->resize($oUA->getWidth(), $iHeight);
			
			$iWidth = $obj_Image->getTgtWidth();
			$iHeight = $obj_Image->getTgtHeight();
		}
		else
		{
			$iWidth = -1;
			$iHeight = -1;
		}
		
		$xml = '<transaction id="'. $this->_iID .'" type="'. $this->_iTypeID .'">';
		$xml .= '<amount currency="'. $this->_obj_ClientConfig->getCountryConfig()->getCurrency() .'">'. $this->_iAmount .'</amount>';
		$xml .= '<price>'. $sPrice .'</price>';
		$xml .= '<order-id>'. $this->_sOrderID .'</order-id>';
		$xml .= '<address>'. $this->_sAddress .'</address>';
		$xml .= '<operator>'. $this->_iOperatorID .'</operator>';
		$xml .= '<logo>';
		$xml .= '<url>'. htmlspecialchars($this->_sLogoURL, ENT_NOQUOTES) .'</url>';
		$xml .= '<width>'. $iWidth .'</width>';
		$xml .= '<height>'. $iHeight .'</height>';
		$xml .= '</logo>';
		$xml .= '<css-url>'. htmlspecialchars($this->_sCSSURL, ENT_NOQUOTES) .'</css-url>';
		$xml .= '<accept-url>'. htmlspecialchars($this->_sAcceptURL, ENT_NOQUOTES) .'</accept-url>';
		$xml .= '<cancel-url>'. htmlspecialchars($this->_sCancelURL, ENT_NOQUOTES) .'</cancel-url>';
		$xml .= '<callback-url>'. htmlspecialchars($this->_sCallbackURL, ENT_NOQUOTES) .'</callback-url>';
		$xml .= '<language>'. $this->_sLanguage .'</language>';
		$xml .= '</transaction>';
		
		return $xml;
	}
	
	/**
	 * Overloaded factory method for producing a new instance of a Transaction Info object.
	 * The data object can either be instantiated from an array of Client Input or from the Transaction Log.
	 * In the latter case the method will first instantiate a new object with the correct Client Configuration for the Transaction.
	 * The method will throw a TxnInfoException with code 1001 if it's unable to instantiate the data object with the Transaction Information.
	 * 
	 * @see 	ClientConfig::produceConfig
	 * 
	 * @param 	integer $id 				Unique ID for the Transaction that should be instantiated
	 * @param 	[ClientConfig|RDB] $obj 	Reference to either a Database Object which handles the active connection to mPoint's database or to and instance of the Client Configuration of the Client who owns the Transaction
	 * @param 	array $misc 				Reference to array of miscelaneous data that is used for instantiating the data object with the Transaction Information
	 * @return 	TxnInfo
	 * @throws 	TxnInfoException
	 */
	public static function produceInfo($id, &$obj, array &$misc=null)
	{
		$obj_TxnInfo = null;
		switch (true)
		{
		case ($obj instanceof ClientConfig):	// Instantiate from array of Client Input
			$obj_TxnInfo = new TxnInfo($id, $misc["typeid"], $obj, $misc["amount"], $misc["orderid"], $misc["recipient"], $misc["operator"], $misc["logo-url"], $misc["css-url"], $misc["accept-url"], $misc["cancel-url"], $misc["callback-url"], $misc["language"]);
			break;
		case ($obj instanceof RDB):				// Instantiate from Transaction Log
			$sql = "SELECT id, typeid, amount, orderid, address, operatorid, lang, logourl, cssurl, accepturl, cancelurl, callbackurl,
						clientid, accountid, keywordid
					FROM Log.Transaction_Tbl
					WHERE id = ". intval($id) ." AND created LIKE '". $obj->escStr($misc[0]) ."%'";
//			echo $sql ."\n";
			$RS = $obj->getName($sql);
			
			// Transaction found
			if (is_array($RS) === true)
			{
				$obj_ClientConfig = ClientConfig::produceConfig($obj, $RS["CLIENTID"], $RS["ACCOUNTID"], $RS["KEYWORDID"]);
				
				$obj_TxnInfo = new TxnInfo($RS["ID"], $RS["TYPEID"], $obj_ClientConfig, $RS["AMOUNT"], $RS["ORDERID"], $RS["ADDRESS"], $RS["OPERATORID"], $RS["LOGOURL"], $RS["CSSURL"], $RS["ACCEPTURL"], $RS["CANCELURL"], $RS["CALLBACKURL"], $RS["LANG"]);
			}
			// Error: Transaction not found
			else { throw new TxnInfoException("Transaction with ID: ". $id ." not found using creation timestamp: ". $misc[0], 1001); }
			break;
		default:								// Error: Argument 2 is an instance of an invalid class
			trigger_error("Argument 2 passed to TxnInfo::produceInfo() must be an instance of ClientConfig or of RDB", E_ERROR);
			break;
		}
		
		return $obj_TxnInfo;
	}
}
?>