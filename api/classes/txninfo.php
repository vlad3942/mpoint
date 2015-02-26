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
	 * Configuration for the Country the transactions was processed in
	 *
	 * @var CountryConfig
	 */
	private $_obj_CountryConfig;
	/**
	 * Total amount the customer will pay for the Transaction without fee
	 *
	 * @var long
	 */
	private $_lAmount;
	/**
	 * Total number of points the customer will pay for the Transaction
	 *
	 * @var integer
	 */
	private $_iPoints;
	/**
	 * Total number of points the customer will be rewarded for completing the Transaction
	 *
	 * @var integer
	 */
	private $_iReward;
	/**
	 * Total amount the customer has been refunded for the Transaction
	 *
	 * @var integer
	 */
	private $_iRefund;
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
	 * Absolute URL to the Client's My Account Icon
	 *
	 * @var string
	 */
	private $_sIconURL;
	/**
	 * Absolute URL to the external system where customer authenticated.
	 * This is generally an existing e-Commerce site or a CRM system.
	 *
	 * @var string
	 */
	private $_sAuthenticationURL;
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
	 * The Client's Reference for the Customer
	 *
	 * @var string
	 */
	private $_sCustomerRef;
	/**
	 * Boolean Flag indicating whether the "Save Card Info" box should automatically be checked on the payment page
	 *
	 * @var boolean
	 */
	private $_bAutoStoreCard;

	/**
	 * String indicating the markup language used to render the payment pages.
	 * The value must match a folder in /templates/[TEMPLATE NAME]/
	 *
	 * @var string
	 */
	private $_sMarkupLanguage;
	/**
	 * The Description of a Order for a Customer
	 *
	 * @var string
	 */
	private  $_sDescription;
	/**
	 * The IP address of a  Customer
	 *
	 * @var string
	 */
	private  $_sIP;
	/**
	 * Unique ID for the The PSP used for the transaction
	 *
	 * @var integer
	 */
	private $_iPSPID = -1;
	/**
	 * The amount the customer will pay in fee�s for the Transaction
	 *
	 * @var integer
	 */
	private $_iFee;
	/**
	/**
	 * The Full amount that has been captured for the Transaction
	 *
	 * @var long
	 */
	private $_lCapturedAmount;
	/**
	 * Default Constructor
	 *
	 * @param 	integer $id 		Unique ID for the Transaction
	 * @param 	integer $tid 		Unique ID for the Transaction Type
	 * @param 	ClientConfig $oClC 	Configuration for the Client who owns the Transaction
	 * @param 	long $amt 			Total amount the customer will pay for the Transaction without fee
	 * @param 	integer $pnt 		Total number of points the customer will pay for the Transaction
	 * @param 	integer $rwd 		Total number of points the customer will be rewarded for completing the transaction
	 * @param 	integer $rfnd 		Total amount the customer has been refunded for the Transaction
	 * @param 	string $orid 		Clients Order ID of the Transaction
	 * @param 	string $addr 		Customer's Mobile Number (MSISDN)
	 * @param 	integer $oid 		GoMobile's ID for the Customer's Mobile Network Operator
	 * @param 	string $email 		Customer's E-Mail Address where a receipt is sent to upon successful completion of the payment transaction
	 * @param 	string $lurl 		Absolute URL to the Client's Logo which will be displayed on all payment pages
	 * @param 	string $cssurl 		Absolute URL to the CSS file that should be used to customising the payment pages
	 * @param 	string $accurl 		Absolute URL where the Customer should be returned to upon successfully completing the Transaction
	 * @param 	string $curl 		Absolute URL where the Customer should be returned to in case he / she cancels the Transaction midway
	 * @param 	string $cburl 		Absolute URL to the Client's Back Office where mPoint should send the Payment Status to
	 * @param 	string $iurl 		Absolute URL to the Client's My Account Icon
	 * @param 	string $aurl 		Absolute URL to the external system where a customer may be authenticated. This is generally an existing e-Commerce site or a CRM system
	 * @param 	string $l 			The language that all payment pages should be rendered in by default for the Client
	 * @param 	integer $m 			The Client Mode in which the Transaction should be Processed
	 * @param 	boolean $ac			Boolean Flag indicating whether Auto Capture should be used for the transaction
	 * @param 	integer $accid 		Unique ID for the End-User's prepaid account that the transaction should be associated with
	 * @param	string $cr			The Client's Reference for the Customer
	 * @param 	integer $gmid 		GoMobile's Unique ID for the MO-SMS that was used to start the payment transaction. Defaults to -1.
	 * @param 	boolean $asc		Boolean Flag indicating whether the "Save Card Info" box should automatically be checked on the payment page
	 * @param 	string $mrk 		String indicating the markup language used to render the payment pages
	 * @param 	string $desc 		String that holds the description of an order
	 * @param 	string $ip			String that holds the customers IP address
	 * @param 	integer $pspid		Unique ID for the The PSP used for the transaction Defaults to -1.
	 * @param 	integer $fee		The amount the customer will pay in fee´s for the Transaction.
	 * @param	long $cptamt		The Full amount that has been captured for the Transaction
	 *
	 */
	public function __construct($id, $tid, ClientConfig &$oClC, CountryConfig &$oCC, $amt, $pnt, $rwd, $rfnd, $orid, $addr, $oid, $email, $lurl, $cssurl, $accurl, $curl, $cburl, $iurl, $aurl, $l, $m, $ac, $accid=-1, $cr="", $gmid=-1, $asc=false, $mrk="xhtml", $desc="", $ip="", $pspid=-1, $fee=0, $cptamt=0)
	{
		if ($orid == -1) { $orid = $id; }
		$this->_iID =  (integer) $id;
		$this->_iTypeID =  (integer) $tid;
		$this->_obj_ClientConfig = $oClC;
		$this->_obj_CountryConfig = $oCC;
		$this->_lAmount = (float) $amt;
		$this->_iPoints = (integer) $pnt;
		$this->_iReward = (integer) $rwd;
		$this->_iRefund = (integer) $rfnd;
		$this->_sOrderID = trim($orid);
		$this->_sMobile = trim($addr);
		$this->_iOperatorID =  (integer) $oid;
		$this->_sEMail = trim($email);

		$this->_sLogoURL = trim($lurl);
		$this->_sCSSURL = trim($cssurl);
		$this->_sAcceptURL = trim($accurl);
		$this->_sCancelURL = trim($curl);
		$this->_sCallbackURL = trim($cburl);
		$this->_sIconURL = trim($iurl);
		$this->_sAuthenticationURL = trim($aurl);

		$this->_sLanguage = trim($l);
		$this->_iMode = (integer) $m;
		$this->_bAutoCapture = (bool) $ac;

		$this->_iAccountID = (integer) $accid;
		$this->_sCustomerRef = trim($cr);
		$this->_iGoMobileID = (integer) $gmid;
		$this->_bAutoStoreCard = (bool) $asc;

		$this->_sMarkupLanguage = trim($mrk);
		$this->_sDescription = trim($desc);
		$this->_sIP = trim($ip);
		$this->_iPSPID = (integer) $pspid;
		$this->_iFee = (integer) $fee;
		$this->_lCapturedAmount = (float) $cptamt;
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
	 * Returns the Configuration for the Country the transactions was processed in
	 *
	 * @return 	CountryConfig
	 */
	public function getCountryConfig() { return $this->_obj_CountryConfig; }
	/**
	 * Returns the Total amount the customer will pay for the Transaction without fee
	 *
	 * @return 	long
	 */
	public function getAmount() { return $this->_lAmount; }
	/**
	 * Returns the amount the customer will pay in fee´s for the Transaction
	 *
	 * @return 	integer
	 */
	public function getFee() { return $this->_iFee; }
	/**
	 * Returns the full amount that has been captured for the Transaction
	 *
	 * @return 	long
	 */
	public function getCapturedAmount() { return $this->_lCapturedAmount; }
	/**
	 * Returns the number of points the customer will pay for the Transaction
	 *
	 * @return 	integer
	 */
	public function getPoints() { return $this->_iPoints; }
	/**
	 * Returns the number of points the customer will be rewarded for completing the Transaction
	 *
	 * @return 	integer
	 */
	public function getReward() { return $this->_iReward; }
	/**
	 * Returns the Total amount the customer has been refunded for the Transaction
	 *
	 * @return 	integer
	 */
	public function getRefund() { return $this->_iRefund; }
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
	 * Returns the Absolute URL to the Client's My Account Icon
	 *
	 * @return 	string
	 */
	public function getIconURL() { return $this->_sIconURL; }
	/**
	 * Absolute URL to the external system where customer may be authenticated.
	 * This is generally an existing e-Commerce site or a CRM system.
	 *
	 * @return 	string
	 */
	public function getAuthenticationURL() { return $this->_sAuthenticationURL; }
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
	 * Returns true mPoint should use Auto Capture for the Client.
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
	 * Returns the Client's Reference for the Customer
	 *
	 * @return 	string		The Client's Reference for the Customer or an emptry string if no customer reference has been provided
	 */
	public function getCustomerRef() { return $this->_sCustomerRef; }
	/**
	 * Returns true if mPoint should automatically check the "Save Card Info" box on the payment page
	 *
	 * @return 	boolean
	 */
	public function autoStoreCard() { return $this->_bAutoStoreCard; }
	/**
	 * Returns the the markup language used to render the payment pages.
	 * The value must match a folder in /templates/[TEMPLATE NAME]/
	 *
	 * @return 	string
	 */
	public function getMarkupLanguage() { return $this->_sMarkupLanguage; }
	/**
	 * Returns the Description for the Order
	 *
	 * @return 	string		 The Description for the Order or an emptry string if no Description has been provided
	 */
	public function getDescription() { return $this->_sDescription; }
	/**
	 * Returns the Customer IP Address
	 *
	 * @return 	string		Customer IP Address
	 */
	public function getIP() { return $this->_sIP; }
	/**
	 * Returns the Message Authentication Code (MAC) for the Transaction using the sha1 algorithm.
	 * The Message Authentication Code is calculated from the following fields (in that order):
	 * 	- Client ID
	 * 	- Account ID
	 * 	- Transaction ID
	 * 	- Order ID
	 * 	- Country ID
	 * 	- Amount
	 * 	- Customer Reference
	 * 	- E-Mail Address
	 * 	- Mobile Number
	 * 	- Client Password
	 *
	 * @return 	string		Message Authentication Code
	 */
	public function getMAC() { return sha1($this->_obj_ClientConfig->getID() . $this->_obj_ClientConfig->getAccountConfig()->getID() . $this->_iID . $this->_sOrderID . $this->_obj_CountryConfig->getID() . $this->_lAmount . $this->_sCustomerRef . $this->_sEMail . $this->_sMobile . $this->_obj_ClientConfig->getPassword() ); }
	/**
	 * Returns Unique ID for the The PSP used for the transaction Defaults to -1.
	 *
	 * @return 	integer		PSP id for the transaction
	 */
	public function getPSPID() { return $this->_iPSPID; }
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
	 * 		<markup-language>{THE MARKUP LANGUAGE USED TO RENDER THE PAYMENT PAGES}</markup-language>
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
		if (is_null($oUA) === false && strlen($this->_sLogoURL) > 0)
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

		$xml = '<transaction id="'. $this->_iID .'" type="'. $this->_iTypeID .'" gmid="'. $this->_iGoMobileID .'" mode="'. $this->_iMode .'" eua-id="'. $this->_iAccountID .'" psp-id="'. $this->_iPSPID .'">';
		$xml .= '<captured-amount country-id="'. $this->_obj_CountryConfig->getID() .'" currency="'. $this->_obj_CountryConfig->getCurrency() .'" symbol="'. $this->_obj_CountryConfig->getSymbol() .'" format="'. $this->_obj_CountryConfig->getPriceFormat() .'">'. $this->_lCapturedAmount .'</captured-amount>';
		$xml .= '<amount country-id="'. $this->_obj_CountryConfig->getID() .'" currency="'. $this->_obj_CountryConfig->getCurrency() .'" symbol="'. $this->_obj_CountryConfig->getSymbol() .'" format="'. $this->_obj_CountryConfig->getPriceFormat() .'">'. $this->_lAmount .'</amount>';
		$xml .= '<fee country-id="'. $this->_obj_CountryConfig->getID() .'" currency="'. $this->_obj_CountryConfig->getCurrency() .'" symbol="'. $this->_obj_CountryConfig->getSymbol() .'" format="'. $this->_obj_CountryConfig->getPriceFormat() .'">'. $this->_iFee .'</fee>';
		$xml .= '<price>'. General::formatAmount($this->_obj_CountryConfig, $this->_lAmount) .'</price>';
		$xml .= '<points country-id="0" currency="points" symbol="points" format="{PRICE} {CURRENCY}">'. $this->_iPoints .'</points>';
		$xml .= '<reward country-id="0" currency="points" symbol="points" format="{PRICE} {CURRENCY}">'. $this->_iReward .'</reward>';
		$xml .= '<refund country-id="'. $this->_obj_CountryConfig->getID() .'" currency="'. $this->_obj_CountryConfig->getCurrency() .'" symbol="'. $this->_obj_CountryConfig->getSymbol() .'" format="'. $this->_obj_CountryConfig->getPriceFormat() .'">'. $this->_iRefund .'</refund>';
		$xml .= '<orderid>'. $this->_sOrderID .'</orderid>';
		$xml .= '<mobile country-id="'. intval($this->_iOperatorID/100) .'">'. $this->_sMobile .'</mobile>';
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
		$xml .= '<icon-url>'. htmlspecialchars($this->_sIconURL, ENT_NOQUOTES) .'</icon-url>';
		$xml .= '<auth-url>'. htmlspecialchars($this->_sAuthenticationURL, ENT_NOQUOTES) .'</auth-url>';
		$xml .= '<language>'. $this->_sLanguage .'</language>';
		$xml .= '<auto-capture>'. General::bool2xml($this->_bAutoCapture) .'</auto-capture>';
		$xml .= '<auto-store-card>'. General::bool2xml($this->_bAutoStoreCard) .'</auto-store-card>';
		$xml .= '<markup-language>'. $this->_sMarkupLanguage .'</markup-language>';
		$xml .= '<customer-ref>'. htmlspecialchars($this->_sCustomerRef, ENT_NOQUOTES) .'</customer-ref>';
		$xml .= '<description>'. htmlspecialchars($this->_sDescription, ENT_NOQUOTES) .'</description>';
		$xml .= '<ip>'. htmlspecialchars($this->_sIP, ENT_NOQUOTES) .'</ip>';
		$xml .= '<mac>'. htmlspecialchars($this->getMAC(), ENT_NOQUOTES) .'</mac>';
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
			if (array_key_exists("client-config", $misc) === false) { $misc["client-config"] = $obj->getClientConfig(); }
			if (array_key_exists("country-config", $misc) === false) { $misc["country-config"] = $obj->getCountryConfig(); }
			if (array_key_exists("amount", $misc) === false) { $misc["amount"] = $obj->getAmount(); }
			if (array_key_exists("points", $misc) === false) { $misc["points"] = $obj->getPoints(); }
			if (array_key_exists("reward", $misc) === false) { $misc["reward"] = $obj->getReward(); }
			if (array_key_exists("orderid", $misc) === false) { $misc["orderid"] = $obj->getOrderID(); }
			if (array_key_exists("mobile", $misc) === false) { $misc["mobile"] = $obj->getMobile(); }
			if (array_key_exists("operator", $misc) === false) { $misc["operator"] = $obj->getOperator(); }
			if (array_key_exists("email", $misc) === false) { $misc["email"] = $obj->getEMail(); }
			if (array_key_exists("logo-url", $misc) === false) { $misc["logo-url"] = $obj->getLogoURL(); }
			if (array_key_exists("css-url", $misc) === false) { $misc["css-url"] = $obj->getCSSURL(); }
			if (array_key_exists("accept-url", $misc) === false) { $misc["accept-url"] = $obj->getAcceptURL(); }
			if (array_key_exists("cancel-url", $misc) === false) { $misc["cancel-url"] = $obj->getCancelURL(); }
			if (array_key_exists("callback-url", $misc) === false) { $misc["callback-url"] = $obj->getCallbackURL(); }
			if (array_key_exists("icon-url", $misc) === false) { $misc["icon-url"] = $obj->getIconURL(); }
			if (array_key_exists("language", $misc) === false) { $misc["language"] = $obj->getLanguage(); }
			if (array_key_exists("mode", $misc) === false) { $misc["mode"] = $obj->getMode(); }
			if (array_key_exists("auto-capture", $misc) === false) { $misc["auto-capture"] = $obj->useAutoCapture(); }
			if (array_key_exists("gomobileid", $misc) === false) { $misc["gomobileid"] = $obj->getGoMobileID(); }
			if (array_key_exists("accountid", $misc) === false) { $misc["accountid"] = $obj->getAccountID(); }
			if (array_key_exists("customer-ref", $misc) === false && strlen($obj->getCustomerRef() ) > 0) { $misc["customer-ref"] = $obj->getCustomerRef(); }
			if (array_key_exists("markup", $misc) === false) { $misc["markup"] = $obj->getMarkupLanguage(); }
			if (array_key_exists("auto-store-card", $misc) === false) { $misc["auto-store-card"] = false; }
			if (array_key_exists("refund", $misc) === false) { $misc["refund"] = 0; }
			if (array_key_exists("auth-url", $misc) === false) { $misc["auth-url"] = $obj->getAuthenticationURL(); }
			if (array_key_exists("description", $misc) === false) { $misc["description"] = $obj->getDescription(); }
			if (array_key_exists("ip", $misc) === false) { $misc["ip"] = $obj->getIP(); }
			if (array_key_exists("psp-id", $misc) === false) { $misc["psp-id"] = $obj->getPSPID(); }
			if (array_key_exists("fee", $misc) === false) { $misc["fee"] = $obj->getFee(); }
			if (array_key_exists("captured-amount", $misc) === false) { $misc["captured-amount"] = $obj->getCapturedAmount(); }
				
				
			$obj_TxnInfo = new TxnInfo($id, $misc["typeid"], $misc["client-config"], $misc["country-config"], $misc["amount"], $misc["points"], $misc["reward"], $misc["refund"], $misc["orderid"], $misc["mobile"], $misc["operator"], $misc["email"], $misc["logo-url"], $misc["css-url"], $misc["accept-url"], $misc["cancel-url"], $misc["callback-url"], $misc["icon-url"], $misc["auth-url"], $misc["language"], $misc["mode"], $misc["auto-capture"], $misc["accountid"], @$misc["customer-ref"], $misc["gomobileid"], $misc["auto-store-card"], $misc["markup"], $misc["description"], $misc["ip"],  $misc["psp-id"],  $misc["fee"], $misc["captured-amount"]);
			break;
		case ($obj instanceof ClientConfig):	// Instantiate from array of Client Input
			if (array_key_exists("country-config", $misc) === false) { $misc["country-config"] = $obj->getCountryConfig(); }
			if (array_key_exists("points", $misc) === false) { $misc["points"] = -1; }
			if (array_key_exists("reward", $misc) === false) { $misc["reward"] = -1; }
			if (array_key_exists("email", $misc) === false) { $misc["email"] = ""; }
			if (array_key_exists("accountid", $misc) === false) { $misc["accountid"] = -1; }
			if (array_key_exists("auto-store-card", $misc) === false) { $misc["auto-store-card"] = false; }
			if (array_key_exists("refund", $misc) === false) { $misc["refund"] = 0; }
			if (array_key_exists("auth-url", $misc) === false) { $misc["auth-url"] = $obj->getAuthenticationURL(); }

			$obj_TxnInfo = new TxnInfo($id, $misc["typeid"], $obj, $misc["country-config"], $misc["amount"], $misc["points"], $misc["reward"], $misc["refund"], $misc["orderid"], $misc["mobile"], $misc["operator"], $misc["email"], $misc["logo-url"], $misc["css-url"], $misc["accept-url"], $misc["cancel-url"], $misc["callback-url"], $misc["icon-url"], $misc["auth-url"], $misc["language"], $obj->getMode(), $obj->useAutoCapture(), $misc["accountid"], @$misc["customer-ref"], $misc["gomobileid"], $misc["auto-store-card"], $misc["markup"], $misc["description"], $misc["ip"]);
			break;
		case ($obj instanceof RDB):				// Instantiate from Transaction Log
			$sql = "SELECT id, typeid, countryid, amount, Coalesce(points, -1) AS points, Coalesce(reward, -1) AS reward, orderid, mobile, operatorid, email, lang, logourl, cssurl, accepturl, cancelurl, callbackurl, iconurl, \"mode\", auto_capture, gomobileid,
						clientid, accountid, keywordid, Coalesce(euaid, -1) AS euaid, customer_ref, markup, refund, authurl, ip, description, pspid, fee, captured
					FROM Log".sSCHEMA_POSTFIX.".Transaction_Tbl
					WHERE id = ". intval($id);

			$sDebug = "";
			if (is_array($misc) === true)
			{
//				preg_match('/2[0-9]{3}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}')
				// Creation Timestamp for Transaction provided
				if (substr_count($misc[0], "-") == 2 && substr_count($misc[0], ":") == 2 && strtotime($misc[0]) > 0)
				{
					$sDebug = " using creation timestamp: ". $misc[0];
					$sql .= " AND date_trunc('second', created) = '". $obj->escStr($misc[0]) ."'";
				}
				// Order ID for Transaction provided
				else
				{
					$sDebug = " using orderID: ". $misc[0];
					$sql .= " AND orderid = '". $obj->escStr($misc[0]) ."'";
				}
			}
//			echo $sql ."\n";
			$RS = $obj->getName($sql);

			// Transaction found
			if (is_array($RS) === true)
			{
				$obj_ClientConfig = ClientConfig::produceConfig($obj, $RS["CLIENTID"], $RS["ACCOUNTID"], $RS["KEYWORDID"]);
				$obj_CountryConfig = CountryConfig::produceConfig($obj, $RS["COUNTRYID"]);
				
				$obj_TxnInfo = new TxnInfo($RS["ID"], $RS["TYPEID"], $obj_ClientConfig, $obj_CountryConfig, $RS["AMOUNT"], $RS["POINTS"], $RS["REWARD"], $RS["REFUND"], $RS["ORDERID"], $RS["MOBILE"], $RS["OPERATORID"], $RS["EMAIL"], $RS["LOGOURL"], $RS["CSSURL"], $RS["ACCEPTURL"], $RS["CANCELURL"], $RS["CALLBACKURL"], $RS["ICONURL"], $RS["AUTHURL"], $RS["LANG"], $RS["MODE"], $RS["AUTO_CAPTURE"], $RS["EUAID"], $RS["CUSTOMER_REF"], $RS["GOMOBILEID"], false, $RS["MARKUP"], $RS["DESCRIPTION"], $RS["IP"], $RS["PSPID"], $RS["FEE"], $RS["CAPTURED"]);
			}
			// Error: Transaction not found
			else { throw new TxnInfoException("Transaction with ID: ". $id ." not found". $sDebug, 1001); }
			break;
		default:								// Error: Argument 2 is an instance of an invalid class
			trigger_error("Argument 2 passed to TxnInfo::produceInfo() must be an instance of ClientConfig or of RDB", E_USER_ERROR);
			break;
		}

		return $obj_TxnInfo;
	}

	public function getMessageHistory(RDB $obj_DB)
	{
		$sql = "SELECT id, stateid, created
				FROM Log".sSCHEMA_POSTFIX.".Message_Tbl
				WHERE txnid = ". $this->getID() ." AND enabled = TRUE
				ORDER BY created DESC";
//		echo $sql;

		$RS = $obj_DB->getAllNames($sql);

		if (is_array($RS) === true)
		{
			return $RS;
		}
		else
		{
			return array();
		}
	}

}
?>