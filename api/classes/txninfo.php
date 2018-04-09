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
	 * Configuration for the Currency the transactions was processed in
	 *
	 * @var CurrencyConfig
	 */
	private $_obj_CurrencyConfig;
	/**
	 * Configuration for the Orders in the cart of the user send as part of the transaction.
	 *
	 * @var OrderInfo
	 */
	private $_obj_OrderConfigs = null;
	/**
	 * Configuration for the Flights in the cart of the user send as part of the transaction.
	 *
	 * @var OrderInfo
	 */
	private $_obj_FlightConfigs = null;
	/**
	 * Configuration for the Passenger in the cart of the user send as part of the transaction.
	 *
	 * @var OrderInfo
	 */
	private $_obj_PassengerConfigs = null;
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
	 * Card-id used for the payment
	 *
	 * @var long
	 */
	private $_iCardID;
	/**
	 * Customer's Device id of the platform which is used for transaction
	 *
	 * @var string
	 */
	private $_sDeviceID;
	/**
	 * Transaction's attempt number
	 *
	 * @var integer
	 */
	private $_iAttempt;

    private $_mask;
    private $_expiry;
    private $_token;
    private $_authOriginalData;
    private $_actionCode = "";
    private $_approvalCode="";

    private $_obj_PaymentSession;

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
	 * @param 	string $extid 		External ID of the Transaction (usually the txn ref of the PSP)
	 * @param 	string $addr 		Customer's Mobile Number (MSISDN)
	 * @param 	integer $oid 		GoMobile's ID for the Customer's Mobile Network Operator
	 * @param 	string $email 		Customer's E-Mail Address where a receipt is sent to upon successful completion of the payment transaction
	 * @param 	string $devid 		Customer's Device id of the platform which is used for transaction
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
	public function __construct($id, $tid, ClientConfig &$oClC, CountryConfig &$oCC, CurrencyConfig &$oCR=null, $amt, $pnt, $rwd, $rfnd, $orid, $extid, $addr, $oid, $email, $devid, $lurl, $cssurl, $accurl, $curl, $cburl, $iurl, $aurl, $l, $m, $ac, $accid=-1, $cr="", $gmid=-1, $asc=false, $mrk="xhtml", $desc="", $ip="",$attempt, $paymentSession, $pspid=-1, $fee=0, $cptamt=0, $cardid = -1,$mask="",$expiry="",$token="",$authOriginalData="")
	{
		if ($orid == -1) { $orid = $id; }
		$this->_iID =  (integer) $id;
		$this->_iTypeID =  (integer) $tid;
		$this->_obj_ClientConfig = $oClC;
		$this->_obj_CountryConfig = $oCC;
		$this->_obj_CurrencyConfig = $oCR;

		$this->_lAmount = (float) $amt;
		$this->_iPoints = (integer) $pnt;
		$this->_iReward = (integer) $rwd;
		$this->_iRefund = (integer) $rfnd;
		$this->_sOrderID = trim($orid);
		$this->_sExternalID = trim($extid);
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
		$this->_iCardID = (integer) $cardid;
		$this->_sDeviceID = trim($devid);

		$this->_mask = trim($mask);
        $this->_expiry =trim($expiry);
        $this->_token = trim($token);
        $this->_authOriginalData = trim($authOriginalData);
        $this->_iAttempt = (integer) $attempt;

        $codes = explode(":",$this->_sExternalID);
        if(count($codes) == 2){
            $this->_approvalCode = $codes[0];
            $this->_actionCode = $codes[1];
        }

        $this->_obj_PaymentSession = $paymentSession;
        $this->_obj_PaymentSession->updateTransaction($this->_iID);
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
	 * Returns the Configuration for the Currency the transactions was processed in
	 *
	 * @return 	CurrencyConfig
	 */
	public function getCurrencyConfig() {
		if(is_null($this->_obj_CurrencyConfig) === false  && strlen($this->_obj_CurrencyConfig->getCode()) > 0)	{return $this->_obj_CurrencyConfig ;}
		else {return $this->_obj_CountryConfig->getCurrencyConfig();}
	}
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
	 * Returns the External ID of the Transaction.
	 *
	 * @return 	string
	 */
	public function getExternalID() { return $this->_sExternalID; }
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
	 * Returns the Customer's Device id of the platform which is used for transaction
	 *
	 * @return 	string
	 */
	public function getDeviceID() { return $this->_sDeviceID; }

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
	 * Returns the Message Authentication Code (HMAC) for the Transaction using the sha1 algorithm.
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
	public function getHMAC() { return sha1($this->_obj_ClientConfig->getID() . $this->_obj_ClientConfig->getAccountConfig()->getID() . $this->_iID . $this->_sOrderID . $this->_obj_CountryConfig->getID() . $this->_lAmount . $this->_sCustomerRef . $this->_sEMail . $this->_sMobile . $this->_obj_ClientConfig->getPassword() ); }
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
	 * Set to -1 to clear the End-User Account from the transaction. 
	 *
	 * @param 	integer $id 	Unique ID for the End-User's prepaid account
	 */
	public function setAccountID($id) { $this->_iAccountID = (integer) $id; }
	/**
	 * Returns Unique ID for the The card used for the transaction Defaults to -1.
	 *
	 * @return 	integer		Card id for the transaction
	 */
	public function getCardID() { return $this->_iCardID; }
	/*
	 * Returns the Transactions's attempt number
	 *
	 * @return 	integer		Attempt number
	 * */
	public function getAttemptNumber() {  return $this->_iAttempt;  }

	public function getPaymentSession() { return $this->_obj_PaymentSession;}
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
	 * 		<device-id>{CUSTOMER'S DEVICE ID OF THE PLATFORM WHICH IS USED FOR TRANSACTION}</device-id>
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

		$xml  = '<transaction id="'. $this->_iID .'" type="'. $this->_iTypeID .'" gmid="'. $this->_iGoMobileID .'" mode="'. $this->_iMode .'" eua-id="'. $this->_iAccountID .'" psp-id="'. $this->_iPSPID .'" card-id="'. $this->_iCardID .'" external-id="'. htmlspecialchars($this->getExternalID(), ENT_NOQUOTES) .'">';
		$xml .= '<captured-amount country-id="'. $this->_obj_CountryConfig->getID() .'" currency="'. $this->_obj_CurrencyConfig->getCode() .'" symbol="'. $this->_obj_CountryConfig->getSymbol() .'" format="'. $this->_obj_CountryConfig->getPriceFormat() .'" alpha2code="'. $this->_obj_CountryConfig->getAlpha2code() .'" alpha3code="'. $this->_obj_CountryConfig->getAlpha3code() .'" code="'. $this->_obj_CountryConfig->getNumericCode() .'">'. $this->_lCapturedAmount .'</captured-amount>';
		$xml .= '<amount country-id="'. $this->_obj_CountryConfig->getID() .'" currency-id="'. $this->getCurrencyConfig()->getID() .'" currency="'.$this->getCurrencyConfig()->getCode() .'" symbol="'. $this->_obj_CountryConfig->getSymbol() .'" format="'. $this->_obj_CountryConfig->getPriceFormat() .'" alpha2code="'. $this->_obj_CountryConfig->getAlpha2code() .'" alpha3code="'. $this->_obj_CountryConfig->getAlpha3code() .'" code="'. $this->_obj_CountryConfig->getNumericCode() .'">'. $this->_lAmount .'</amount>';
		$xml .= '<fee country-id="'. $this->_obj_CountryConfig->getID() .'" currency="'. $this->_obj_CurrencyConfig->getCode() .'" symbol="'. $this->_obj_CountryConfig->getSymbol() .'" format="'. $this->_obj_CountryConfig->getPriceFormat() .'">'. $this->_iFee .'</fee>';
		$xml .= '<price>'. General::formatAmount($this->_obj_CountryConfig, $this->_lAmount) .'</price>';
		$xml .= '<points country-id="0" currency="points" symbol="points" format="{PRICE} {CURRENCY}">'. $this->_iPoints .'</points>';
		$xml .= '<reward country-id="0" currency="points" symbol="points" format="{PRICE} {CURRENCY}">'. $this->_iReward .'</reward>';
		$xml .= '<refund country-id="'. $this->_obj_CountryConfig->getID() .'" currency="'. $this->_obj_CountryConfig->getCurrency() .'" symbol="'. $this->_obj_CountryConfig->getSymbol() .'" format="'. $this->_obj_CountryConfig->getPriceFormat() .'">'. $this->_iRefund .'</refund>';
		$xml .= '<orderid>'. $this->_sOrderID .'</orderid>';
		$xml .= '<mobile country-id="'. intval($this->_iOperatorID/100) .'">'. $this->_sMobile .'</mobile>';
		$xml .= '<operator>'. $this->_iOperatorID .'</operator>';
		$xml .= '<email>'. $this->_sEMail .'</email>';
		$xml .= '<device-id>'. $this->_sDeviceID .'</device-id>';
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
		$xml .= '<hmac>'. htmlspecialchars($this->getHMAC(), ENT_NOQUOTES) .'</hmac>';

		if(!empty($this->_token))
            $xml .= '<token>'.htmlspecialchars($this->_token, ENT_NOQUOTES).'</token>';

        if(!empty($this->_mask))
		    $xml .= '<card-mask>'.htmlspecialchars($this->_mask, ENT_NOQUOTES).'</card-mask>';

        if(!empty($this->_expiry))
            $xml .= '<expiry>'.htmlspecialchars($this->_expiry, ENT_NOQUOTES).'</expiry>';

        if(!empty($this->_approvalCode))
            $xml .= '<approval-code>'.htmlspecialchars($this->_approvalCode, ENT_NOQUOTES).'</approval-code>';

        if(!empty($this->_actionCode))
            $xml .= '<action-code>'.htmlspecialchars($this->_actionCode, ENT_NOQUOTES).'</action-code>';

        if(!empty($this->_authOriginalData))
            $xml .= '<auth-original-data>'.htmlspecialchars($this->_authOriginalData, ENT_NOQUOTES).'</auth-original-data>';

        if( empty($this->_obj_OrderConfigs) === false )
		{
			
			$xml .= $this->getOrdersXML();
		}
		$xml .= '</transaction>';

		return $xml;
	}

	public static function produceInfoFromOrderNoAndMerchant(RDB $obj, $orderNo, $merchant = '', array $data = array() )
	{
		$sql  = self::_constProduceQuery();
		if (strlen($merchant) > 0)
		{
			$sql .= " INNER JOIN Client".sSCHEMA_POSTFIX.".MerchantAccount_Tbl ma ON t.clientid = ma.clientid AND ma.name = '". $obj->escStr($merchant) ."' AND ma.enabled = true";
		}
		$sql .= " WHERE orderid = '". $obj->escStr($orderNo) ."'";
//		echo $sql ."\n";

		$RS = $obj->getName($sql);
		$obj_TxnInfo = self::_produceFromResultSet($obj, $RS);

		if ( ($obj_TxnInfo instanceof TxnInfo) === false) { throw new TxnInfoException("Transaction with orderno: ". $orderNo. " not found", 1001); }
		return self::produceInfo($obj_TxnInfo->getID(),  $obj, $obj_TxnInfo, $data);
	}

	private static function _constProduceQuery()
	{
		$sql = "SELECT t.id, typeid, countryid,currencyid, amount, Coalesce(points, -1) AS points, Coalesce(reward, -1) AS reward, orderid, extid, mobile, operatorid, email, lang, logourl, cssurl, accepturl, cancelurl, callbackurl, iconurl, \"mode\", auto_capture, gomobileid,
						t.clientid, accountid, keywordid, Coalesce(euaid, -1) AS euaid, customer_ref, markup, refund, authurl, ip, description, t.pspid, fee, captured, cardid, deviceid, mask, expiry, token, authoriginaldata,attempt,sessionid
				FROM Log".sSCHEMA_POSTFIX.".Transaction_Tbl t";

		return $sql;
	}

	/**
	 * @param RDB $obj
	 * @param $RS
	 * @return null|TxnInfo
	 */
	private static function _produceFromResultSet(RDB $obj, $RS)
	{
		// Transaction found
		$obj_TxnInfo = null;
		if (is_array($RS) === true)
		{
			$obj_ClientConfig = ClientConfig::produceConfig($obj, $RS["CLIENTID"], $RS["ACCOUNTID"], $RS["KEYWORDID"]);
			$obj_CountryConfig = CountryConfig::produceConfig($obj, $RS["COUNTRYID"]);
			$obj_CurrencyConfig = CurrencyConfig::produceConfig($obj, $RS["CURRENCYID"]);

            $paymentSession = null;
            if($RS["SESSIONID"] == -1){
                $paymentSession = PaymentSession::Get($obj, $obj_ClientConfig,$obj_CountryConfig,$obj_CurrencyConfig,$RS["AMOUNT"], $RS["ORDERID"],"",$RS["MOBILE"], $RS["EMAIL"], $RS["EXTID"],$RS["DEVICEID"], $RS["IP"]);
            }
            else{
                $paymentSession = PaymentSession::Get($obj,$RS["SESSIONID"]);
            }

			$obj_TxnInfo = new TxnInfo($RS["ID"], $RS["TYPEID"], $obj_ClientConfig, $obj_CountryConfig,$obj_CurrencyConfig, $RS["AMOUNT"], $RS["POINTS"], $RS["REWARD"], $RS["REFUND"], $RS["ORDERID"], $RS["EXTID"], $RS["MOBILE"], $RS["OPERATORID"], $RS["EMAIL"], $RS["DEVICEID"], $RS["LOGOURL"], $RS["CSSURL"], $RS["ACCEPTURL"], $RS["CANCELURL"], $RS["CALLBACKURL"], $RS["ICONURL"], $RS["AUTHURL"], $RS["LANG"], $RS["MODE"], $RS["AUTO_CAPTURE"], $RS["EUAID"], $RS["CUSTOMER_REF"], $RS["GOMOBILEID"], false, $RS["MARKUP"], $RS["DESCRIPTION"], $RS["IP"], $RS["ATTEMPT"], $paymentSession, $RS["PSPID"], $RS["FEE"], $RS["CAPTURED"],$RS["CARDID"],$RS["MASK"],$RS["EXPIRY"],$RS["TOKEN"],$RS["AUTHORIGINALDATA"]);
		}
		return $obj_TxnInfo;
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
	 * 												- An instance of the Datansaction
	 * 												- A Database Object which handles the active connection to mPoint's databasea Object with the Transaction Information that the new Data Object should be based on
	 * 												- An instance of the Client Configuration of the Client who owns the Tr
	 * @param 	array $misc 						Reference to array of miscelaneous data that is used for instantiating the data object with the Transaction Information
	 * @return 	TxnInfo
	 * @throws 	E_USER_ERROR, TxnInfoException
	 */
	public static function produceInfo($id, RDB $obj_db, &$obj= null, array &$misc=null)
	{
		$obj_TxnInfo = null;
		switch (true)
		{
		case ($obj instanceof TxnInfo):	// Instantiate from array of new Transaction Information
			// Use data from provided Data Object for all unspecified values
			if (array_key_exists("typeid", $misc) === false) { $misc["typeid"] = $obj->getTypeID(); }
			if (array_key_exists("client-config", $misc) === false) { $misc["client-config"] = $obj->getClientConfig(); }
			if (array_key_exists("country-config", $misc) === false) { $misc["country-config"] = $obj->getCountryConfig(); }
			if (array_key_exists("currency-config", $misc) === false) { $misc["currency-config"] = $obj->getCurrencyConfig(); }
			if (array_key_exists("card-id", $misc) === false) { $misc["card-id"] = $obj->getCardID(); }
			if (array_key_exists("amount", $misc) === false) { $misc["amount"] = $obj->getAmount(); }
			if (array_key_exists("points", $misc) === false) { $misc["points"] = $obj->getPoints(); }
			if (array_key_exists("reward", $misc) === false) { $misc["reward"] = $obj->getReward(); }
			if (array_key_exists("orderid", $misc) === false) { $misc["orderid"] = $obj->getOrderID(); }
			if (array_key_exists("extid", $misc) === false) { $misc["extid"] = $obj->getExternalID(); }
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
            if (array_key_exists("device-id", $misc) === false) { $misc["device-id"] = NULL ; }
            if (array_key_exists("attempt", $misc) === false) { $misc["attempt"] = 1 ; }
            if (array_key_exists("sessionid", $misc) === false) { $misc["sessionid"] = $obj->getSessionId(); }
            if (array_key_exists("sessiontype", $misc) === false) { $misc["sessiontype"] = 1; }

            $paymentSession = null;
            if( $misc["sessionid"] == -1){
                $paymentSession = PaymentSession::Get($obj_db, $misc["client-config"],$misc["country-config"],$misc["currency-config"],$misc["amount"], $misc["orderid"],$misc["sessiontype"],$misc["mobile"], $misc["email"], $misc["extid"],$misc["device-id"], $misc["ip"]);
            }
            else{
                $paymentSession = PaymentSession::Get($obj_db,$misc["sessionid"]);
            }

			$obj_TxnInfo = new TxnInfo($id, $misc["typeid"], $misc["client-config"], $misc["country-config"], $misc["currency-config"], $misc["amount"], $misc["points"], $misc["reward"], $misc["refund"], $misc["orderid"], $misc["extid"], $misc["mobile"], $misc["operator"], $misc["email"],  $misc["device-id"],$misc["logo-url"], $misc["css-url"], $misc["accept-url"], $misc["cancel-url"], $misc["callback-url"], $misc["icon-url"], $misc["auth-url"], $misc["language"], $misc["mode"], $misc["auto-capture"], $misc["accountid"], @$misc["customer-ref"], $misc["gomobileid"], $misc["auto-store-card"], $misc["markup"], $misc["description"], $misc["ip"], $misc["attempt"], $paymentSession, $misc["psp-id"],  $misc["fee"], $misc["captured-amount"], $misc["card-id"]);
			break;
		case ($obj instanceof ClientConfig):	// Instantiate from array of Client Input
			if (array_key_exists("country-config", $misc) === false) { $misc["country-config"] = $obj->getCountryConfig(); }
			if (array_key_exists("currency-config", $misc) === false) { $misc["currency-config"] =  $obj->getCountryConfig()->getCurrencyConfig(); }
			if (array_key_exists("points", $misc) === false) { $misc["points"] = -1; }
			if (array_key_exists("reward", $misc) === false) { $misc["reward"] = -1; }
			if (array_key_exists("extid", $misc) === false) { $misc["extid"] = -1; }
			if (array_key_exists("email", $misc) === false) { $misc["email"] = ""; }
			if (array_key_exists("device-id", $misc) === false) { $misc["device-id"] = ""; }
			if (array_key_exists("accountid", $misc) === false) { $misc["accountid"] = -1; }
			if (array_key_exists("auto-store-card", $misc) === false) { $misc["auto-store-card"] = false; }
			if (array_key_exists("refund", $misc) === false) { $misc["refund"] = 0; }
			if (array_key_exists("auth-url", $misc) === false) { $misc["auth-url"] = $obj->getAuthenticationURL(); }
            if (array_key_exists("sessiontype", $misc) === false) { $misc["sessiontype"] = 1; }
            if(isset($misc["sessionid"]) == false || empty($misc["sessionid"]) == true)
                $misc["sessionid"] = -1;

            $paymentSession = null;
            if($misc["sessionid"] == -1){
                $paymentSession = PaymentSession::Get($obj_db, $obj,$misc["country-config"], $misc["currency-config"], $misc["amount"], $misc["orderid"], $misc["sessiontype"], $misc["mobile"], $misc["email"], $misc["extid"],$misc["device-id"], $misc["ip"]);
            }
            else{
                $paymentSession = PaymentSession::Get($obj_db,$misc["sessionid"]);
            }

			$obj_TxnInfo = new TxnInfo($id, $misc["typeid"], $obj, $misc["country-config"],$misc["currency-config"], $misc["amount"], $misc["points"], $misc["reward"], $misc["refund"], $misc["orderid"], $misc["extid"], $misc["mobile"], $misc["operator"], $misc["email"], $misc["device-id"], $misc["logo-url"], $misc["css-url"], $misc["accept-url"], $misc["cancel-url"], $misc["callback-url"], $misc["icon-url"], $misc["auth-url"], $misc["language"], $obj->getMode(), $obj->useAutoCapture(), $misc["accountid"], @$misc["customer-ref"], $misc["gomobileid"], $misc["auto-store-card"], $misc["markup"], $misc["description"], $misc["ip"], $misc["attempt"], $paymentSession);
			break;
		case ($obj_db instanceof RDB):		// Instantiate from Transaction Log
            $obj = $obj_db;
			$sql  = self::_constProduceQuery();
			$sql .= " WHERE id = ". intval($id);

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
			$obj_TxnInfo = self::_produceFromResultSet($obj, $RS);

			// Transaction found
			if ( ($obj_TxnInfo instanceof TxnInfo) === false) { throw new TxnInfoException("Transaction with ID: ". $id ." not found". $sDebug, 1001); }
			break;
		default:								// Error: Argument 2 is an instance of an invalid class
			trigger_error("Argument 2 passed to TxnInfo::produceInfo() must be an instance of ClientConfig or of RDB", E_USER_ERROR);
			break;
		}


		return $obj_TxnInfo;
	}

	public function getMessageHistory(RDB $obj_DB,$testingRequset = false)
	{

		$sql = "SELECT id, stateid, created ";
		if($testingRequset)
            $sql .= ",data ";
        $sql .= " FROM Log".sSCHEMA_POSTFIX.".Message_Tbl
				WHERE txnid = ". $this->getID() ." AND enabled = '1'
				ORDER BY id DESC";
//		echo $sql;
		$res = $obj_DB->query($sql);
		$aMessages = array();
		while ($RS = $obj_DB->fetchName($res) )
		{
			$aMessages[] = array_change_key_case($RS, CASE_LOWER);
		}
		
		return $aMessages;  
	}

	public function hasEitherState(RDB $obj_DB, $aStateID)
	{
		if (is_array($aStateID) === false) { $aStateID = array($aStateID); }
		$sStates = implode(',', array_map("intval", $aStateID) );

		$sql = "SELECT COUNT(id) AS C
				FROM Log".sSCHEMA_POSTFIX.".Message_Tbl
				WHERE txnid = ". $this->getID() ." AND stateid IN (". $sStates .") AND enabled = '1'";
//		echo $sql;
		$res = $obj_DB->getName($sql);

		if ($res === false) { trigger_error("Failed to determine whether transaction #". $this->getID() . " has states: ". $sStates, E_USER_WARNING); }
		return is_array($res) === true && isset($res["C"]) === true && intval($res["C"]) > 0;
	}
	
	
	public function setOrderDetails(RDB $obj_DB, $aOrderData)
	{
	
	
	
		if( is_array($aOrderData) === true )
		{
			foreach ($aOrderData as $aOrderDataObj)
			{
				$sql = "SELECT Nextvalue('Log".sSCHEMA_POSTFIX.".Order_Tbl_id_seq') AS id FROM DUAL";
				$RS = $obj_DB->getName($sql);
				// Error: Unable to generate a new Order ID
				if (is_array($RS) === false) { throw new mPointException("Unable to generate new Order ID", 1001); }
	
	
				$sql = "INSERT INTO Log".sSCHEMA_POSTFIX.".Order_Tbl
							(id, txnid, countryid, amount, quantity, productsku, productname, productdescription, productimageurl, points, reward)
						VALUES
							(". $RS["ID"] .", ". $this->getID() .", ". $aOrderDataObj["country-id"] .", ". $aOrderDataObj["amount"] .", ". $aOrderDataObj["quantity"] .", '". $obj_DB->escStr($aOrderDataObj["product-sku"]) ."', '". $obj_DB->escStr($aOrderDataObj["product-name"]) ."',
							 '". $obj_DB->escStr($aOrderDataObj["product-description"]) ."', '". $obj_DB->escStr($aOrderDataObj["product-image-url"]) ."', ". $aOrderDataObj["points"] .", ". $aOrderDataObj["reward"] ." )";
				//echo $sql ."\n";exit;
				// Error: Unable to insert a new order record in the Order Table
				if (is_resource($obj_DB->query($sql) ) === false)
				{
					if (is_array($RS) === false) { throw new mPointException("Unable to insert new record for Order: ". $RS["ID"], 1002); }
				}
				else
				{
						
					$order_iD = $RS["ID"];
                    $this->setAdditionalDetails($obj_DB,$aOrderDataObj['additionaldata'],$order_iD);
				}
			}
				
			return $order_iD;
		}
	}
	
	/**
	 * Function to insert new records in the Shipping Address Related to that Order in table that are send as part of the transaction cart details
	 *
	 * @param 	Array $aShippingData	Data object with the Shipping Address Data details
	 *
	 */
	public function setShippingDetails(RDB $obj_DB, $aShippingData)
	{
		if( is_array($aShippingData) === true )
		{
			foreach ($aShippingData as $aShippingObj)
			{
				$sql = "SELECT Nextvalue('Log".sSCHEMA_POSTFIX.".Address_Tbl_id_seq') AS id FROM DUAL";
				
				$RS = $obj_DB->getName($sql);
				// Error: Unable to generate a new Order ID
				if (is_array($RS) === false) { throw new mPointException("Unable to generate new address ID", 1001); }
	
	
				$sql = "INSERT INTO Log".sSCHEMA_POSTFIX.".Address_Tbl
							(id, name, street, street2, city, state, zip, country, reference_id, reference_type)
						VALUES
							(". $RS["ID"] .", '". $aShippingObj["name"] ."', '". $aShippingObj["street"] ."', '". $aShippingObj["street2"] ."', '". $aShippingObj["city"] ."', '". $aShippingObj["state"] ."',
							 '". $aShippingObj["zip"] ."', '". $aShippingObj["country"] ."', '". $aShippingObj["reference_id"] ."', '". $aShippingObj["reference_type"] ."' )";
				//echo $sql ."\n";exit;
				// Error: Unable to insert a new order record in the Order Table
				if (is_resource($obj_DB->query($sql) ) === false)
				{
					if (is_array($RS) === vxxx) { throw new mPointException("Unable to insert new record for Address: ". $RS["ID"], 1002); }
				}
				else
				{
	
					$Address_iD = $RS["ID"];
	
				}
			}
	
			return $Address_iD;
		}
	}
	
	
	
	/**
	 * Function to insert new records in the Additional Data table that are send as part of the transaction cart details
	 *
	 * @param 	Array $additionalData	Data object with the Additional Data details
	 *
	 */
	public function setAdditionalDetails(RDB $obj_DB, $aAdditionalData, $ExternalID)
	{
		$additional_id = "";
		if( is_array($aAdditionalData) === true )
		{
			foreach ($aAdditionalData as $aAdditionalDataObj)
			{
				$sql = "SELECT Nextvalue('Log".sSCHEMA_POSTFIX.".additional_data_Tbl_id_seq') AS id FROM DUAL";
				$RS = $obj_DB->getName($sql);
				// Error: Unable to generate a new Additional Data ID
				if (is_array($RS) === false) { throw new mPointException("Unable to generate new Additional Data ID", 1001); }
				$sql = "INSERT INTO log".sSCHEMA_POSTFIX.".additional_data_tbl(id, name, value, type, externalid)
								VALUES(". $RS["ID"] .", '". $aAdditionalDataObj["name"] ."', '". $aAdditionalDataObj["value"] ."', '". $aAdditionalDataObj["type"] ."','". $ExternalID ."')";
				// Error: Unable to insert a new Additional Data record in the Additional Data Table
				if (is_resource($obj_DB->query($sql) ) === false)
				{
					if (is_array($RS) === false) { throw new mPointException("Unable to insert new record for Additional Data: ". $RS["ID"], 1002); }
				}
				else
				{
					$additional_id = $RS["ID"];
				}
			}	
			return $additional_id;	
		}
	}
	
	/**
	 * Function to insert new records in the Flight table that are send as part of the transaction cart details
	 *
	 * @param 	Array $flightData   	Data object with the flight details
	 * @param 	Array $aAdditionalDatas   	Data object with the Additional data details
	 *
	 */
	public function setFlightDetails(RDB $obj_DB, $aFlightData,  $aAdditionalDatas)
	{
	
		$aReturnValue = "";
	
		if( is_array($aFlightData) === true )
		{
			
				$sql = "SELECT Nextvalue('Log".sSCHEMA_POSTFIX.".flight_Tbl_id_seq') AS id FROM DUAL";
				$RS = $obj_DB->getName($sql);

				// Error: Unable to generate a new Flight ID
				if (is_array($RS) === false) { throw new mPointException("Unable to generate new Flight ID", 1001); }

				$sql = "INSERT INTO Log".sSCHEMA_POSTFIX.".flight_Tbl(id, service_class,flight_number, departure_airport, arrival_airport, airline_code, order_id, arrival_date, departure_date, created, modified, tag, trip_count, service_level)
					VALUES('". $RS["ID"] ."','". $aFlightData["service_class"] ."','". $aFlightData["flight_number"] ."','". $aFlightData["departure_airport"] ."','". $aFlightData["arrival_airport"] ."','". $aFlightData["airline_code"] ."','". $aFlightData["order_id"] ."','". $aFlightData["arrival_date"] ."', '". $aFlightData["departure_date"] ."',now(),now(), '". $aFlightData["tag"] ."', '". $aFlightData["trip_count"] ."', '". $aFlightData["service_level"] ."')";
				$this->setAdditionalDetails($obj_DB, $aAdditionalDatas, $RS["ID"]);
				
				// Error: Unable to insert a new flight record in the Flight Table
				if (is_resource($obj_DB->query($sql) ) === false)
				{
					if (is_array($RS) === false) { throw new mPointException("Unable to insert new record for Flight: ". $RS["ID"], 1002); }
				}
				else
				{
					$aReturnValue = $RS["ID"];
	
				}
	
			
			return $aReturnValue;
		}
	}
	
	/**
	 * Function to insert new records in the passenger table that are send as part of the transaction cart details
	 *
	 * @param 	Array $passengerData   	Data object with the passenger details
	 * @param 	Array $aAdditionalDatas   	Data object with the Additional data details
	 *
	 */
	public function setPassengerDetails(RDB $obj_DB, $aPassengerData, $aAdditionalDatas)
	{
		$aReturnValue = "";
		if( is_array($aPassengerData) === true )
		{
			
				$sql = "SELECT Nextvalue('Log".sSCHEMA_POSTFIX.".passenger_Tbl_id_seq') AS id FROM DUAL";
				$RS = $obj_DB->getName($sql);
				// Error: Unable to generate a new Passenger ID
				if (is_array($RS) === false) { throw new mPointException("Unable to generate new Passenger ID", 1001); }
	
				
						$sql = "INSERT INTO Log".sSCHEMA_POSTFIX.".passenger_tbl(id, first_name, last_name, type, order_id, created, modified, title, email, mobile, country_id)
						VALUES(". $RS["ID"] .", '". $aPassengerData["first_name"] ."', '". $aPassengerData["last_name"] ."','". $aPassengerData["type"] ."', ". $aPassengerData["order_id"] .", now(), now(), '". $aPassengerData["title"] ."', '". $aPassengerData["email"] ."', '". $aPassengerData["mobile"] ."', '". $aPassengerData["country_id"] ."')";
				// Error: Unable to insert a new passenger record in the Passenger Table
						$this->setAdditionalDetails($obj_DB, $aAdditionalDatas, $RS["ID"]);
				if (is_resource($obj_DB->query($sql) ) === false)
				{
					if (is_array($RS) === false) { throw new mPointException("Unable to insert new record for Passenger: ". $RS["ID"], 1002); }
				}
				else
				{
					$aReturnValue = $RS["ID"];
	
				}
	
			
			return $aReturnValue;
		}
	}

	public function produceOrderConfig(RDB $obj_DB)
	{
		//Get Order Detail of a given transaction if supplied by the e-commerce platform.
		$this->_obj_OrderConfigs = OrderInfo::produceConfigurations($obj_DB, $this->getID());
		
		
	}
	
	public function getOrdersXML()
	{
		$xml = '';
		if( empty($this->_obj_OrderConfigs) === false )
		{
			$xml .= '<orders>';
			foreach ($this->_obj_OrderConfigs as $obj_OrderInfo)
			{
				if( ($obj_OrderInfo instanceof OrderInfo) === true )
				{
					
					$xml .= $obj_OrderInfo->toXML();
					
					
				}
			}
			$xml .= '</orders>';
		}
		
		return $xml;
	}

	function getSessionId(){
	    if($this->_obj_PaymentSession instanceof PaymentSession)
        {
            return $this->_obj_PaymentSession->getId();
        }
        return -1;
    }

    function getPaymentSessionXML(){
	    return $this->_obj_PaymentSession->toXML();
    }

    function updateTransactionAmount(RDB $obj_DB,$amount){
        $sql = "UPDATE log" . sSCHEMA_POSTFIX . ".Transaction_Tbl SET amount = ".$amount." WHERE id = " . $this->_iID;
        $obj_DB->query($sql);
    }

    /**
     * Returns the Card token.
     *
     * @return 	String
     */
    function getToken(){
	    return $this->_token;
    }

}
?>