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
 * @version 1.10
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
	 * Customer's Mobile Number (MSISDN)
	 *
	 * @var string
	 */
	private $_sMobile;
	/**
	 * GoMobile's ID for the Customer's Mobile Network Operator
	 *
	 * @var integer
	 */
	private $_iOperatorID;
	/**
	 * Customer's E-Mail Address where a receipt is sent to upon successful completion of the payment transaction
	 *
	 * @var string
	 */
	private $_sEMail;

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
	 * The Client Mode in which the Transaction is Processed
	 * 	0. Production
	 * 	1. Test Mode with prefilled card Info
	 * 	2. Certification Mode
	 *
	 * @var integer
	 */
	private $_iMode;
	/**
	 * Boolean Flag indicating whether mPoint should use Auto Capture for the Transaction.
	 *
	 * @var boolean
	 */
	private $_bAutoCapture;
	/**
	 * GoMobile's Unique ID for the MO-SMS that was used to start the payment transaction.
	 * The ID is used for payment via Premium SMS.
	 *
	 * @var integer
	 */
	private $_iGoMobileID;
	/**
	 * Unique ID for the End-User's prepaid account that has been associated with this Transaction
	 *
	 * @var integer
	 */
	private $_iAccountID = -1;

	/**
	 * Default Constructor
	 *
	 * @param 	integer $id 		Unique ID for the Transaction
	 * @param 	integer $tid 		Unique ID for the Transaction Type
	 * @param 	ClientConfig $oCC 	Configuration for the Client who owns the Transaction
	 * @param 	integer $a 			Total amount the customer will pay for the Transaction
	 * @param 	string $orid 		Clients Order ID of the Transaction
	 * @param 	string $addr 		Customer's Mobile Number (MSISDN)
	 * @param 	integer $oid 		GoMobile's ID for the Customer's Mobile Network Operator
	 * @param 	string $email 		Customer's E-Mail Address where a receipt is sent to upon successful completion of the payment transaction
	 * @param 	string $lurl 		Absolute URL to the Client's Logo which will be displayed on all payment pages
	 * @param 	string $cssurl 		Absolute URL to the CSS file that should be used to customising the payment pages
	 * @param 	string $aurl 		Absolute URL where the Customer should be returned to upon successfully completing the Transaction
	 * @param 	string $curl 		Absolute URL where the Customer should be returned to in case he / she cancels the Transaction midway
	 * @param 	string $cburl 		Absolute URL to the Client's Back Office where mPoint should send the Payment Status to
	 * @param 	string $l 			The language that all payment pages should be rendered in by default for the Client
	 * @param 	integer $m 			The Client Mode in which the Transaction should be Processed
	 * @param 	boolean $ac			Boolean Flag indicating whether Auto Capture should be used for the transaction
	 * @param 	integer $accid 		Unique ID for the End-User's prepaid account that the transaction should be associated with
	 * @param 	integer $gmid 		GoMobile's Unique ID for the MO-SMS that was used to start the payment transaction. Defaults to -1.
	 */
	public function __construct($id, $tid, ClientConfig &$oCC, $a, $orid, $addr, $oid, $email, $lurl, $cssurl, $aurl, $curl, $cburl, $l, $m, $ac, $accid=-1, $gmid=-1)
	{
		if ($orid == -1) { $orid = $id; }
		$this->_iID =  (integer) $id;
		$this->_iTypeID =  (integer) $tid;
		$this->_obj_ClientConfig = $oCC;
		$this->_iAmount =  (integer) $a;
		$this->_sOrderID =  trim($orid);
		$this->_sMobile = trim($addr);
		$this->_iOperatorID =  (integer) $oid;
		$this->_sEMail = trim($email);

		$this->_sLogoURL = trim($lurl);
		$this->_sCSSURL = trim($cssurl);
		$this->_sAcceptURL = trim($aurl);
		$this->_sCancelURL = trim($curl);
		$this->_sCallbackURL = trim($cburl);

		$this->_sLanguage = trim($l);
		$this->_iMode = (integer) $m;
		$this->_bAutoCapture = (bool) $ac;
		
		$this->_iAccountID = (integer) $accid;
		$this->_iGoMobileID = (integer) $gmid;
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
	 * Returns the Customer's Mobile Number (MSISDN)
	 *
	 * @return 	string
	 */
	public function getMobile() { return $this->_sMobile; }
	/**
	 * Returns the GoMobile's ID for the Customer's Mobile Network Operator
	 *
	 * @return 	integer
	 */
	public function getOperator() { return $this->_iOperatorID; }
	/**
	 * Returns the Customer's E-Mail Address where a receipt is sent to upon successful completion of the payment transaction
	 *
	 * @return 	string
	 */
	public function getEMail() { return $this->_sEMail; }

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
	 * Returns the Client Mode in which the Transaction is Processed
	 * 	0. Production
	 * 	1. Test Mode with prefilled card Info
	 * 	2. Certification Mode
	 *
	 * @return 	integer
	 */
	public function getMode() { return $this->_iMode; }
	/**
	 * Boolean Flag indicating whether mPoint should use Auto Capture for the Client.
	 *
	 * @return 	boolean
	 */
	public function useAutoCapture() { return $this->_bAutoCapture; }
	/**
	 * Returns the GoMobile's Unique ID for the MO-SMS that was used to start the payment transaction.
	 * The ID is used for payment via Premium SMS.
	 *
	 * @return 	integer
	 */
	public function getGoMobileID() { return $this->_iGoMobileID; }
	/**
	 * Returns the associated End-User prepaid account associated with the Transaction.
	 *
	 * @return 	integer		Unique ID for the End-User's prepaid account or -1 if no account has been associated
	 */
	public function getAccountID() { return $this->_iAccountID; }

	/**
	 * Updates the information for the Transaction with the Customer's E-Mail Address where a receipt is sent to upon successful completion of the payment transaction
	 *
	 * @param 	string $email 	Customer's E-Mail Address where a receipt is sent to upon successful completion of the payment transaction
	 */
	public function setEMail($email) { $this->_sEMail = $email; }
	/**
	 * Associates an End-User's prepaid account with the Transaction.
	 *
	 * @param 	integer $id 	Unique ID for the End-User's prepaid account
	 */
	public function setAccountID($id) { $this->_iAccountID = $id; }

	/**
	 * Converts the data object into XML.
	 * If a User Agent Profile is provided, the method will automatically calculate the width and height of the client logo
	 * after it has been resized to fit the screen resolution of the customer's mobile device.
	 *
	 * The method will return an XML document in the following format:
	 * 	<transaction id="{UNIQUE ID FOR THE TRANSACTION}" type="{ID FOR THE TRANSACTION TYPE}">
	 *		<amount currency="{CURRENCY AMOUNT IS CHARGED IN}">{TOTAL AMOUNT THE CUSTOMER IS CHARGED FOR THE TRANSACTION}</amount>
	 * 		<price>{AMOUNT FORMATTED FOR BEING DISPLAYED IN THE GIVEN COUNTRY}</price>
	 *		<order-id>{CLIENT'S ORDER ID FOR THE TRANSACTION}</order-id>
	 *		<mobile>{CUSTOMER'S MSISDN WHERE SMS MESSAGE CAN BE SENT TO}</mobile>
	 *		<operator>{GOMOBILE ID FOR THE CUSTOMER'S MOBILE NETWORK OPERATOR}</operator>
	 * 		<email>{CUSTOMER'S E-MAIL ADDRESS WHERE RECEIPT WILL BE SENT TO}</email>
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
	 * 		<auto-capture>{FLAG INDICATING WHETHER MPOINT SHOULD USE AUTO CAPTURE FOR THE TRANSACTION}</auto-capture>
	 *	</transaction>
	 *
	 * @see 	iCLIENT_LOGO_SCALE
	 * @see 	General::formatAmount()
	 *
	 * @param 	UAProfile $oUA 	Reference to the User Agent Profile for the Customer's Mobile Device (optional)
	 * @return 	string
	 */
	public function toXML(UAProfile &$oUA=null)
	{
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
			$iWidth = "100%";
			$iHeight = iCLIENT_LOGO_SCALE ."%";
		}

		$xml = '<transaction id="'. $this->_iID .'" type="'. $this->_iTypeID .'" gmid="'. $this->_iGoMobileID .'" mode="'. $this->_iMode .'">';
		$xml .= '<amount currency="'. $this->_obj_ClientConfig->getCountryConfig()->getCurrency() .'" symbol="'. $this->_obj_ClientConfig->getCountryConfig()->getSymbol() .'">'. $this->_iAmount .'</amount>';
		$xml .= '<price>'. General::formatAmount($this->_obj_ClientConfig->getCountryConfig(), $this->_iAmount) .'</price>';
		$xml .= '<orderid>'. $this->_sOrderID .'</orderid>';
		$xml .= '<mobile>'. $this->_sMobile .'</mobile>';
		$xml .= '<operator>'. $this->_iOperatorID .'</operator>';
		$xml .= '<email>'. $this->_sEMail .'</email>';
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
		$xml .= '<auto-capture>'. General::bool2xml($this->_bAutoCapture) .'</auto-capture>';
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
	 * @param 	integer $id 						Unique ID for the Transaction that should be instantiated
	 * @param 	[TxnInfo|ClientConfig|RDB] $obj 	Reference to one of the following objects:
	 * 												- An instance of the Data Object with the Transaction Information that the new Data Object should be based on
	 * 												- An instance of the Client Configuration of the Client who owns the Transaction
	 * 												- A Database Object which handles the active connection to mPoint's database
	 * @param 	array $misc 						Reference to array of miscelaneous data that is used for instantiating the data object with the Transaction Information
	 * @return 	TxnInfo
	 * @throws 	E_USER_ERROR, TxnInfoException
	 */
	public static function produceInfo($id, &$obj, array &$misc=null)
	{
		$obj_TxnInfo = null;
		switch (true)
		{
		case ($obj instanceof TxnInfo):	// Instantiate from array of new Transaction Information
			// Use data from provided Data Object for all unspecified values
			if (array_key_exists("typeid", $misc) === false) { $misc["typeid"] = $obj->getTypeID(); }
			if (array_key_exists("client_config", $misc) === false) { $misc["client_config"] = $obj->getClientConfig(); }
			if (array_key_exists("amount", $misc) === false) { $misc["amount"] = $obj->getAmount(); }
			if (array_key_exists("orderid", $misc) === false) { $misc["orderid"] = $obj->getOrderID(); }
			if (array_key_exists("mobile", $misc) === false) { $misc["mobile"] = $obj->getMobile(); }
			if (array_key_exists("operator", $misc) === false) { $misc["operator"] = $obj->getOperator(); }
			if (array_key_exists("email", $misc) === false) { $misc["email"] = $obj->getEMail(); }
			if (array_key_exists("logo-url", $misc) === false) { $misc["logo-url"] = $obj->getLogoURL(); }
			if (array_key_exists("css-url", $misc) === false) { $misc["css-url"] = $obj->getCSSURL(); }
			if (array_key_exists("accept-url", $misc) === false) { $misc["accept-url"] = $obj->getAcceptURL(); }
			if (array_key_exists("cancel-url", $misc) === false) { $misc["cancel-url"] = $obj->getCancelURL(); }
			if (array_key_exists("callback-url", $misc) === false) { $misc["callback-url"] = $obj->getCallbackURL(); }
			if (array_key_exists("language", $misc) === false) { $misc["language"] = $obj->getLanguage(); }
			if (array_key_exists("mode", $misc) === false) { $misc["mode"] = $obj->getMode(); }
			if (array_key_exists("auto-capture", $misc) === false) { $misc["auto-capture"] = $obj->useAutoCapture(); }
			if (array_key_exists("gomobileid", $misc) === false) { $misc["gomobileid"] = $obj->getGoMobileID(); }
			if (array_key_exists("accountid", $misc) === false) { $misc["accountid"] = $obj->getAccountID(); }

			$obj_TxnInfo = new TxnInfo($id, $misc["typeid"], $misc["client_config"], $misc["amount"], $misc["orderid"], $misc["mobile"], $misc["operator"], $misc["email"], $misc["logo-url"], $misc["css-url"], $misc["accept-url"], $misc["cancel-url"], $misc["callback-url"], $misc["language"], $misc["mode"], $misc["auto-capture"], $misc["accountid"], $misc["gomobileid"]);
			break;
		case ($obj instanceof ClientConfig):	// Instantiate from array of Client Input
			if (array_key_exists("email", $misc) === false) { $misc["email"] = ""; }
			if (array_key_exists("accountid", $misc) === false) { $misc["accountid"] = -1; }
			
			$obj_TxnInfo = new TxnInfo($id, $misc["typeid"], $obj, $misc["amount"], $misc["orderid"], $misc["mobile"], $misc["operator"], $misc["email"], $misc["logo-url"], $misc["css-url"], $misc["accept-url"], $misc["cancel-url"], $misc["callback-url"], $misc["language"], $obj->getMode(), $obj->useAutoCapture(), $misc["accountid"], $misc["gomobileid"]);
			break;
		case ($obj instanceof RDB):				// Instantiate from Transaction Log
			$sql = "SELECT id, typeid, amount, orderid, mobile, operatorid, email, lang, logourl, cssurl, accepturl, cancelurl, callbackurl, mode, auto_capture, gomobileid,
						clientid, accountid, keywordid, COALESCE(euaid, -1) AS euaid
					FROM Log.Transaction_Tbl
					WHERE id = ". intval($id);
			if (is_array($misc) === true) { $sql .= " AND date_trunc('second', created) = '". $obj->escStr($misc[0]) ."'"; }
//			echo $sql ."\n";
			$RS = $obj->getName($sql);

			// Transaction found
			if (is_array($RS) === true)
			{
				$obj_ClientConfig = ClientConfig::produceConfig($obj, $RS["CLIENTID"], $RS["ACCOUNTID"], $RS["KEYWORDID"]);

				$obj_TxnInfo = new TxnInfo($RS["ID"], $RS["TYPEID"], $obj_ClientConfig, $RS["AMOUNT"], $RS["ORDERID"], $RS["MOBILE"], $RS["OPERATORID"], $RS["EMAIL"], $RS["LOGOURL"], $RS["CSSURL"], $RS["ACCEPTURL"], $RS["CANCELURL"], $RS["CALLBACKURL"], $RS["LANG"], $RS["MODE"], $RS["AUTO_CAPTURE"], $RS["EUAID"], $RS["GOMOBILEID"]);
			}
			// Error: Transaction not found
			else { throw new TxnInfoException("Transaction with ID: ". $id ." not found using creation timestamp: ". $misc[0], 1001); }
			break;
		default:								// Error: Argument 2 is an instance of an invalid class
			trigger_error("Argument 2 passed to TxnInfo::produceInfo() must be an instance of ClientConfig or of RDB", E_USER_ERROR);
			break;
		}

		return $obj_TxnInfo;
	}
}
?>