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
	 * Configuration for the Currency the transactions was processed in for DCC transaction
	 *
	 * @var Converted CurrencyConfig
	 */
	private $_obj_ConvertedCurrencyConfig;
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
	 * Total amount the customer will pay for the Transaction without fee for DCC converted transaction
	 *
	 * @var long
	 */
	private $_lConvertedAmount;

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
     * Absolute URL where the Customer should be returned to upon Transaction failure
     *
     * @var string
     */
	private $_sDeclineURL;
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
	private $_eAutoCapture;
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
	 * Unique ID for the profile created and associated with this Transaction
	 *
	 * @var integer
	 */
	private $_iProfileID = -1;

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
	 * Wallet-id used for the payment
	 *
	 * @var long
	 */
	private $_iWalletID;
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
    /**
     * Transaction's virtual payment data that is stored in a tokenized format
     *
     * @var string
     */
    private $_sVirtualPaymentToken;

    /**
     * Transaction's attempt number
     *
     * @var integer
     */
    private $_mask;

    /**
     * Maskcard number of card used for trascation
     *
     * @var string
     */
    private $_expiry;

    /**
     * Expiry year and month of card used for transaction [MM/YY]
     *
     * @var string
     */
    private $_token;

    /**
     *  AuthOriginalData used for future reference
     *
     * @var string
     */
    private $_authOriginalData;

    /**
     * ActionCode
     *
     * @var integer
     */
    private $_actionCode = "";

    /**
     * ApprovalCode
     *
     * @var integer
     */
    private $_approvalCode="";

    /**
     * Payment Session object
     *
     * @var object
     */
    private $_obj_PaymentSession;

    /**
     * Product Type of transaction
     *
     * @var object
     */

    private $_iProductType;


    private $_createdTimestamp;

    /**
     * Transaction's Additional Data
     *
     * @var array
     */
    private $_aAdditionalData;


    /*
     *  Payment type based on card used for transaction
     *
     * @var integer
     */
    private $_iPaymentType = 0;

	/*
     *  card name of card used for transaction
     *
     * @var integer
     */
	private $_sCardName = 0;

    /*
     *  Processor type based on psp used for transaction
     *
     * @var integer
     */
    private $_iProcessorType = 0;

	/*
    *  PSP name based on psp used for transaction
    *
    * @var integer
    */
	private $_sPSPName = 0;

    /**
     * User selected to pay in these many installments
     *
     * @var integer
     */
    private $_iInstallmentValue;

    /**
     * Exchange reference ids
     *
     * @var array
     */
    private $_aExternalRef;

    /**
     * Exchange reference ids
     *
     * @var float
     */
    private $_fconversionRate;

    /**
     * Issuing Bank
     *
     * @var string
     */
    private $_sIssuingBank;

	/**
	 * Billing address
	 *
	 * @var string
	 */
	private $_aBillingAddr;

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
	 * @param 	integer $paymentSession			Unique ID for payment session used for transation, Defaults to 1
	 * @param 	integer $productType			Unique ID for product type used for transation, Defaults to 100
	 * @param 	integer $pspid		Unique ID for the The PSP used for the transaction Defaults to -1.
	 * @param 	integer $fee		The amount the customer will pay in fee´s for the Transaction.
	 * @param	long $cptamt		The Full amount that has been captured for the Transaction
	 * @param	array $aExternalRef	 External Reference Ids
	 * @param	integer $ofAmt	 Offered DCC Amount
	 * @param	ClientConfig $oFCR	 Offered DCC Currency
	 * @param	float $fconversionRate	 Offered DCC Conversion Rate
	 * @param	string $sIssuing_Bank	 Issuing Bank Name
	 * @param	array $_aBillingAddr	 Billing Address
	 *
	 */
	public function __construct($id, $tid, ClientConfig &$oClC, CountryConfig &$oCC, CurrencyConfig &$oCR=null, $amt, $pnt, $rwd, $rfnd, $orid, $extid, $addr, $oid, $email, $devid, $lurl, $cssurl, $accurl, $declineurl, $curl, $cburl, $iurl, $aurl, $l, $m, $ac=1, $accid=-1, $cr="", $gmid=-1, $asc=false, $mrk="xhtml", $desc="", $ip="",$attempt=1, $paymentSession = 1, $productType = 100, $installmentValue=0, $profileid=-1, $pspid=-1, $fee=0, $cptamt=0, $cardid = -1,$walletid = -1,$mask="",$expiry="",$token="",$authOriginalData="",$approvalActionCode="", $createdTimestamp = "",$virtualtoken = "", $additionalData=[],$aExternalRef = [],$ofAmt = -1,CurrencyConfig &$oFCR = null,$fconversionRate = 1, $sIssuingBank = "", $aBillingAddr = [])
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
        $this->_sDeclineURL = trim($declineurl);
		$this->_sCancelURL = trim($curl);
		$this->_sCallbackURL = trim($cburl);
		$this->_sIconURL = trim($iurl);
		$this->_sAuthenticationURL = trim($aurl);

		$this->_sLanguage = trim($l);
		$this->_iMode = (integer) $m;
		$this->_eAutoCapture = (int) $ac;

		$this->_iAccountID = (integer) $accid;
		$this->_iProfileID = (integer) $profileid;
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
		if($walletid === null)
		{
			$this->_iWalletID = -1;
		}
		else {
			$this->_iWalletID = (integer)$walletid;
		}
		$this->_sDeviceID = trim($devid);

		$this->_mask = trim($mask);
        $this->_expiry =trim($expiry);
        $this->_token = trim($token);
        $this->_authOriginalData = trim($authOriginalData);
        $this->_iAttempt = (integer) $attempt;
        $this->_sVirtualPaymentToken = trim($virtualtoken);

        $codes = explode(":",$approvalActionCode);
        if(count($codes) == 2){
            $this->_approvalCode = $codes[0];
            $this->_actionCode = $codes[1];
        }else{
        	$this->_approvalCode = $approvalActionCode;
        }

        $this->_obj_PaymentSession = $paymentSession;
        $this->_obj_PaymentSession->updateTransaction($this->_iID);

        $this->_iProductType = (integer) $productType;
        $this->_createdTimestamp =$createdTimestamp;
        $this->_aAdditionalData = $additionalData;
        $this->_iInstallmentValue = $installmentValue;
        $this->_aExternalRef = $aExternalRef;
        $this->_lConvertedAmount = (float) $ofAmt;
        $this->_obj_ConvertedCurrencyConfig = $oFCR;
        $this->_fconversionRate = (float)$fconversionRate;
        $this->_sIssuingBank = trim($sIssuingBank);
        $this->_aBillingAddr = $aBillingAddr;

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
	 * if dcc opt converted currency return and for normal payment _lConvertedAmount has Originally initialized currency
	 * @return 	CurrencyConfig
	 */
	public function getCurrencyConfig()
	{
		$ccCOde = is_null($this->_obj_ConvertedCurrencyConfig) === false ? $this->_obj_ConvertedCurrencyConfig->getCode() : "";
		if(empty ($ccCOde) === false) {	return $this->_obj_ConvertedCurrencyConfig ; }
		else {return $this->_obj_CountryConfig->getCurrencyConfig();}
	}

	/**
	 * Returns the  Configuration for the Currency the transactions was Originally initialized in
	 *
	 * @return 	CurrencyConfig
	 */
	public function getInitializedCurrencyConfig()
	{
		$ccCOde = is_null($this->_obj_CurrencyConfig) === false ? $this->_obj_CurrencyConfig->getCode() : "";
		if(empty ($ccCOde) === false)	{return $this->_obj_CurrencyConfig ;}
		else {return $this->_obj_CountryConfig->getCurrencyConfig();}
	}


	/**
	 * Returns the Configuration for the Currency the DCC transactions was processed in for
	 *
	 * @return 	CurrencyConfig
	 */
	public function getConvertedCurrencyConfig()
	{
		$ccCOde = is_null($this->_obj_ConvertedCurrencyConfig) === false ? $this->_obj_ConvertedCurrencyConfig->getCode() : "";
		if(empty ($ccCOde) === false) {	return $this->_obj_ConvertedCurrencyConfig ; }
		else {return $this->_obj_CountryConfig->getCurrencyConfig();}
	}
	/**
	 * Returns the Total amount the customer will pay for the Transaction without fee
	 * if dcc opt converted amount return and for normal payment _lConvertedAmount has Originally initialized amount
	 *
	 * @return 	long
	 */
	public function getAmount() { return $this->_lConvertedAmount; }

	/**
	 * Returns the Original initialized  Total amount the customer will pay for the Transaction without fee
	 *
	 * @return 	long
	 */
	public function getInitializedAmount() { return $this->_lAmount; }

	/**
	 * Returns the Total offered amount the customer will pay for the Transaction without fee for DCC Transaction
	 *
	 * @return 	long
	 */
	public function getConvertedAmount() { return $this->_lConvertedAmount; }
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
     * Returns the Absolute URL where the Customer should be returned to upon Transaction failure
     *
     * @return 	string
     */
    public function getDeclineURL() { return $this->_sDeclineURL; }

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
	public function useAutoCapture() { return $this->_eAutoCapture; }
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
	 * Returns the profile id associated with the Transaction.
	 *
	 * @return 	integer		Unique Profile id or -1 if no account has been associated
	 */
	public function getProfileID() { return $this->_iProfileID; }

	/**
	 * Returns the txn issuer approval code
	 *
	 * @return 	string		
	 */
	public function getApprovalCode() { return $this->_approvalCode; }
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
	public function getHMAC() { return hash('sha512',$this->_obj_ClientConfig->getID() . $this->_sOrderID . $this->_lAmount . $this->_obj_CountryConfig->getID() . $this->_sMobile . $this->_obj_CountryConfig->getID() . $this->_sEMail . $this->_sDeviceID . $this->_obj_ClientConfig->getSalt()); }
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
	 * Associates an mProfile id with the Transaction.
	 * Set to -1 if not present for the transaction.
	 *
	 * @param 	integer $profileID 	Unique ID for the mProfile
	 */
	public function setProfileID($profileID) { $this->_iProfileID = (integer) $profileID; }

	/**
	 * Updates the issuer approaval code
	 *
	 * @param 	integer $approvalCode 	Unique ID for the for the transaction
	 */
	public function setApprovalCode($approvalCode) { $this->_approvalCode = $approvalCode; }
	/**
	 * Returns Unique ID for the The card used for the transaction Defaults to -1.
	 *
	 * @return 	integer		Card id for the transaction
	 */
	public function getCardID() { return $this->_iCardID; }
	/**
	 * Returns Unique ID for the The Wallet type used for the transaction Defaults to -1.
	 *
	 * @return 	integer		Wallet id for the transaction
	 */
	public function getWalletID() { return $this->_iWalletID; }
	/*
	 * Returns the Transactions's attempt number
	 *
	 * @return 	integer		Attempt number
	 * */
	public function getAttemptNumber() {  return $this->_iAttempt;  }
    /*
     * Returns the Transactions's virtual token
     *
     * @return 	string		virtual token
     * */
    public function getVirtualToken() {  return $this->_sVirtualPaymentToken;  }

	public function getPaymentSession() { return $this->_obj_PaymentSession;}


	/*
     * Returns the Transactions's Additional data
	 * if param is sent returns value of property
     *
	 * @param string    key
     * @return 	string
     * */
	public function getAdditionalData($key = "")
    {
        try
        {
            if (empty($key) === true)
            {
                if (count($this->_aAdditionalData) > 0)
                {
                    return $this->_aAdditionalData;
                }
                return null;
            }
            if ($this->_aAdditionalData != null && array_key_exists($key, $this->_aAdditionalData) === true)
            {
                return $this->_aAdditionalData[$key];
            }
        }
        catch (Exception $e)
        {

        }
        return null;
    }


	/*
     * Returns the Transactions's External Reference data
	 * if param is sent returns value of property
     *
	 * @param type   external ref type 1=SUVTP,2=Foreign Exchange etc
     * @return 	string
     * */
	public function getExternalRef($type = 0,$pspid = 0)
	{
		try
		{
			if ($type === 0 && $pspid === 0 )
			{
				if (count($this->_aExternalRef) > 0)
				{
					return $this->_aExternalRef;
				}
				return null;
			}
			if ($this->_aExternalRef != null && array_key_exists($type, $this->_aExternalRef) === true)
			{
				$aExternalRef = $this->_aExternalRef[$type];
				if($pspid === 0)	return $aExternalRef;
				else if (array_key_exists($pspid, $aExternalRef) === true)
				{
					return $this->_aExternalRef[$type][$pspid];
				}

			}

		}
		catch (Exception $e)
		{

		}
		return null;
	}

    /**
     * @return int
     */
    public function getInstallmentValue()
    {
        return $this->_iInstallmentValue;
    }


    /**
     * @return float
     */
    public function getConversationRate()
    {
        return $this->_fconversionRate;
    }

    /**
     * @return string
     */
	public function getIssuingBankName()
	{
		return $this->_sIssuingBank;
	}

	/**
	 * @return array
	 */
	public function getBillingAddr()
	{
		return $this->_aBillingAddr;
	}

    /**
	 * Converts the data object into XML.
	 * If a User Agent Profile is provided, the method will automatically calculate the width and height of the client logo
	 * after it has been resized to fit the screen resolution of the customer's mobile device.
	 *
	 * The method will return an XML document in the following format:
	 *    <transaction id="{UNIQUE ID FOR THE TRANSACTION}" type="{ID FOR THE TRANSACTION TYPE}">
	 *        <amount currency="{CURRENCY AMOUNT IS CHARGED IN}">{TOTAL AMOUNT THE CUSTOMER IS CHARGED FOR THE TRANSACTION}</amount>
	 *        <price>{AMOUNT FORMATTED FOR BEING DISPLAYED IN THE GIVEN COUNTRY}</price>
	 *        <order-id>{CLIENT'S ORDER ID FOR THE TRANSACTION}</order-id>
	 *        <mobile>{CUSTOMER'S MSISDN WHERE SMS MESSAGE CAN BE SENT TO}</mobile>
	 *        <operator>{GOMOBILE ID FOR THE CUSTOMER'S MOBILE NETWORK OPERATOR}</operator>
	 *        <email>{CUSTOMER'S E-MAIL ADDRESS WHERE RECEIPT WILL BE SENT TO}</email>
	 *        <device-id>{CUSTOMER'S DEVICE ID OF THE PLATFORM WHICH IS USED FOR TRANSACTION}</device-id>
	 *        <logo>
	 *            <url>{ABSOLUTE URL TO THE CLIENT'S LOGO}</url>
	 *            <width>{WIDTH OF THE LOGO AFTER IT HAS BEEN SCALED TO FIT THE SCREENSIZE OF THE CUSTOMER'S MOBILE DEVICE}</width>
	 *            <height>{HEIGHT OF THE LOGO AFTER IT HAS BEEN SCALED TO FIT THE SCREENSIZE OF THE CUSTOMER'S MOBILE DEVICE}</height>
	 *        </logo>
	 *        <css-url>{ABSOLUTE URL TO THE CSS FILE PROVIDED BY THE CLINET}</css-url>
	 *        <accept-url>{ABSOLUTE URL TO WHERE THE CUSTOMER SHOULD BE DIRECTED UPON SUCCESSFULLY COMPLETING THE PAYMENT}</accept-url>
	 *        <cancel-url>{ABSOLUTE URL TO WHERE THE CUSTOMER SHOULD BE DIRECTED IF THE TRANSACTION IS CANCELLED}</accept-url>
	 *        <callback-url>{ABSOLUTE URL TO WHERE MPOINT SHOULD SEND THE PAYMENT STATUS}</callback-url>
	 *        <language>{LANGUAGE THAT ALL PAYMENT PAGES SHOULD BE TRANSLATED INTO}</language>
	 *        <auto-capture>{FLAG INDICATING WHETHER MPOINT SHOULD USE AUTO CAPTURE FOR THE TRANSACTION}</auto-capture>
	 *        <markup-language>{THE MARKUP LANGUAGE USED TO RENDER THE PAYMENT PAGES}</markup-language>
	 *    </transaction>
	 *
	 * @param UAProfile $oUA Reference to the User Agent Profile for the Customer's Mobile Device (optional)
	 * @param int       $iAmount
	 * @param null      $ticketNumbers
	 *
	 * @return    string
	 * @throws \ImageException
	 * @see    General::formatAmount()
	 *
	 * @see    iCLIENT_LOGO_SCALE
	 */
	public function toXML(UAProfile &$oUA=null, $iAmount = -1, $ticketNumbers = null)
	{
		$obj_CurrencyConfig = $this->getCurrencyConfig();

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

		$xml  = '<transaction id="'. $this->_iID .'" type="'. $this->_iTypeID .'" gmid="'. $this->_iGoMobileID .'" mode="'. $this->_iMode .'" eua-id="'. $this->_iAccountID .'" attempt="'. $this->_iAttempt.'" psp-id="'. $this->_iPSPID .'" card-id="'. $this->_iCardID .'" wallet-id="'. $this->_iWalletID .'" product-type="'. $this->_iProductType .'" external-id="'. htmlspecialchars($this->getExternalID(), ENT_NOQUOTES) .'" >';
		$xml .= '<captured-amount country-id="'. $this->_obj_CountryConfig->getID() .'" currency="'. $obj_CurrencyConfig->getCode() .'" symbol="'. $this->_obj_CountryConfig->getSymbol() .'" format="'. $this->_obj_CountryConfig->getPriceFormat() .'" alpha2code="'. $this->_obj_CountryConfig->getAlpha2code() .'" alpha3code="'. $this->_obj_CountryConfig->getAlpha3code() .'" code="'. $this->_obj_CountryConfig->getNumericCode() .'">'. $this->_lCapturedAmount .'</captured-amount>';
		if($iAmount < 0)
		{
			 $iAmount = $this->getAmount();
		}

		$xml .= '<amount country-id="'. $this->_obj_CountryConfig->getID() .'" currency-id="'. $obj_CurrencyConfig->getID() .'" currency="'.$obj_CurrencyConfig->getCode() .'" decimals="'. $obj_CurrencyConfig->getDecimals().'" symbol="'. $obj_CurrencyConfig->getSymbol() .'" format="'. $this->_obj_CountryConfig->getPriceFormat() .'" alpha2code="'. $this->_obj_CountryConfig->getAlpha2code() .'" alpha3code="'. $this->_obj_CountryConfig->getAlpha3code() .'" code="'. $this->_obj_CountryConfig->getNumericCode() .'">'. $iAmount .'</amount>';
		
		$xml .= '<amount_info>';
		$xml .= '<country-id>'. $this->_obj_CountryConfig->getID() .'</country-id>';
		$xml .= '<currency-id>'. $obj_CurrencyConfig->getID() .'</currency-id>';
		$xml .= '<currency>'. $obj_CurrencyConfig->getCode() .'</currency>';
		$xml .= '<decimals>'. $obj_CurrencyConfig->getDecimals() .'</decimals>';
		$xml .= '<symbol>'. $this->_obj_CountryConfig->getSymbol() .'</symbol>';
		$xml .= '<format>'. $this->_obj_CountryConfig->getPriceFormat() .'</format>';
		$xml .= '<alpha2code>'. $this->_obj_CountryConfig->getAlpha2code() .'</alpha2code>';
		$xml .= '<alpha3code>'. $this->_obj_CountryConfig->getAlpha3code() .'</alpha3code>';
		$xml .= '<code>'. $this->_obj_CountryConfig->getNumericCode() .'</code>';
		$xml .= '<amount>'. $iAmount .'</amount>';
		$xml .= '</amount_info>';
		
		$xml .= '<fee country-id="'. $this->_obj_CountryConfig->getID() .'" currency="'. $obj_CurrencyConfig->getCode() .'" symbol="'. $this->_obj_CountryConfig->getSymbol() .'" format="'. $this->_obj_CountryConfig->getPriceFormat() .'">'. $this->_iFee .'</fee>';
		$xml .= '<price>'. General::formatAmount($this->_obj_CountryConfig, $this->_lAmount) .'</price>';
		$xml .= '<points country-id="0" currency="points" symbol="points" format="{PRICE} {CURRENCY}">'. $this->_iPoints .'</points>';
		$xml .= '<reward country-id="0" currency="points" symbol="points" format="{PRICE} {CURRENCY}">'. $this->_iReward .'</reward>';
		$xml .= '<refund country-id="'. $this->_obj_CountryConfig->getID() .'" currency="'. $this->_obj_CountryConfig->getCurrency() .'" symbol="'. $this->_obj_CountryConfig->getSymbol() .'" format="'. $this->_obj_CountryConfig->getPriceFormat() .'">'. $this->_iRefund .'</refund>';
		$xml .= '<orderid>'. $this->_sOrderID .'</orderid>';
		$xml .= '<mobile country-id="'. intval($this->_iOperatorID/100) .'" country-code="'. $this->_obj_CountryConfig->getCountryCode() .'">'. $this->_sMobile .'</mobile>';
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
        $xml .= '<decline-url>'. htmlspecialchars($this->_sDeclineURL, ENT_NOQUOTES) .'</decline-url>';
        $xml .= '<callback-url>'. htmlspecialchars($this->_sCallbackURL, ENT_NOQUOTES) .'</callback-url>';
		$xml .= '<icon-url>'. htmlspecialchars($this->_sIconURL, ENT_NOQUOTES) .'</icon-url>';
		$xml .= '<auth-url>'. htmlspecialchars($this->_sAuthenticationURL, ENT_NOQUOTES) .'</auth-url>';
		$xml .= '<language>'. $this->_sLanguage .'</language>';
		$xml .= '<auto-capture>'. htmlspecialchars($this->_eAutoCapture == AutoCaptureType::ePSPLevelAutoCapt ? "true" : "false") .'</auto-capture>';
		$xml .= '<auto-store-card>'. General::bool2xml($this->_bAutoStoreCard) .'</auto-store-card>';
		$xml .= '<markup-language>'. $this->_sMarkupLanguage .'</markup-language>';
		$xml .= '<customer-ref>'. htmlspecialchars($this->_sCustomerRef, ENT_NOQUOTES) .'</customer-ref>';
		$xml .= '<description>'. htmlspecialchars($this->_sDescription, ENT_NOQUOTES) .'</description>';
		$xml .= '<ip>'. htmlspecialchars($this->_sIP, ENT_NOQUOTES) .'</ip>';
		$xml .= '<hmac>'. htmlspecialchars($this->getHMAC(), ENT_NOQUOTES) .'</hmac>';
        $xml .= '<created-date>'. htmlspecialchars(date("Ymd", strtotime($this->getCreatedTimestamp())), ENT_NOQUOTES) .'</created-date>'; //CCYYMMDD
        $xml .= '<created-time>'. htmlspecialchars(date("His", strtotime($this->getCreatedTimestamp())), ENT_NOQUOTES) .'</created-time>'; //hhmmss

        if($this->getAdditionalData("booking-ref") != null)
        {
            $xml .= '<booking-ref>'. htmlspecialchars($this->getAdditionalData("booking-ref"), ENT_NOQUOTES) .'</booking-ref>';
        }

        if($this->getAdditionalData('invoiceid') !== null)
        {
            $xml .= '<invoiceid>'. htmlspecialchars($this->getAdditionalData("invoiceid"), ENT_NOQUOTES) .'</invoiceid>';
        }

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
		if($this->getAdditionalData() != null)
        {
            $xml .= "<additional-data>";
            foreach ($this->getAdditionalData() as $key=>$value)
            {
            	 if (strpos($key, 'rule') === false) {
					 $xml .= "<param name='" . $key . "'>" . $value . "</param>";
				 }
            }
            $xml .="</additional-data>";
        }

        if(!empty($this->_iInstallmentValue) && $this->getInstallmentValue() > 0)
        {
            $xml .= '<installment><value>'.htmlspecialchars($this->_iInstallmentValue, ENT_NOQUOTES).'</value></installment>';
		}

		if($this->getProfileID() > 0)
		{
			$xml .= '<profileid>'.htmlspecialchars($this->getProfileID(), ENT_NOQUOTES).'</profileid>';
		}

		$xml .= '</transaction>';

		return $xml;
	}

	/**
	 * Converts the data object into Attribute Less XML.
	 * If a User Agent Profile is provided, the method will automatically calculate the width and height of the client logo
	 * after it has been resized to fit the screen resolution of the customer's mobile device.
	 * @param array $aExcludeNodes node to exclude
	 * @param int       $iAmount
	 * @param UAProfile $oUA Reference to the User Agent Profile for the Customer's Mobile Device (optional)
	 * @param null      $ticketNumbers
	 *
	 * @return    string
	 * @throws \ImageException
	 * @see    General::formatAmount()
	 *
	 * @see    iCLIENT_LOGO_SCALE
	 */
	public function toAttributeLessXML($aExcludeNodes = array(),$iAmount = -1,$ticketNumbers = null)
	{
		$obj_CurrencyConfig = $this->getCurrencyConfig();

		$xml  = '<transaction>';
		$xml .= '<id>'.$this->_iID.'</id>';
		$xml .= '<type>'.$this->_iTypeID.'</type>';
		$xml .= '<gmid>'.$this->_iGoMobileID.'</gmid>';
		$xml .= '<mode>'.$this->_iMode.'</mode>';
		$xml .= '<attempt>'.$this->_iAttempt.'</attempt>';
		$xml .= '<pspId>'.$this->_iPSPID.'</pspId>';
		$xml .= '<cardId>'.$this->_iCardID.'</cardId>';
		$xml .= '<walletId>'.$this->_iWalletID.'</walletId>';
		$xml .= '<productType>'.$this->_iProductType.'</productType>';
		$xml .= '<externalId>'.htmlspecialchars($this->getExternalID(), ENT_NOQUOTES) .'</externalId>';

		if(in_array("capturedAmount", $aExcludeNodes) === false)
		{
			$xml .= '<capturedAmount>';
			$xml .= '<countryId>'.$this->_obj_CountryConfig->getID() .'</countryId>';
			$xml .= '<currency>'.$obj_CurrencyConfig->getCode() .'</currency>';
			$xml .= '<symbol>'.$this->_obj_CountryConfig->getSymbol() .'</symbol>';
			$xml .= '<format>'.$this->_obj_CountryConfig->getPriceFormat()  .'</format>';
			$xml .= '<alpha2code>'.$this->_obj_CountryConfig->getAlpha2code() .'</alpha2code>';
			$xml .= '<alpha3code>'.$this->_obj_CountryConfig->getAlpha3code() .'</alpha3code>';
			$xml .= '<code>'.$this->_obj_CountryConfig->getNumericCode().'</code>';
			$xml .= '<amount>'.$this->_lCapturedAmount .'</amount>';
			$xml .= '</capturedAmount>';
		}

		if($iAmount < 0)
		{
			 $iAmount = $this->getAmount();
		}
		if(in_array("amount", $aExcludeNodes) === false)
		{
			$xml .= '<amount>';
			$xml .= '<countryId>'.$this->_obj_CountryConfig->getID().'</countryId>';
			$xml .= '<currencyId>'.$obj_CurrencyConfig->getID().'</currencyId>';
			$xml .= '<currency>'.$obj_CurrencyConfig->getCode().'</currency>';
			$xml .= '<decimals>'.$obj_CurrencyConfig->getDecimals().'</decimals>';
			$xml .= '<symbol>'.$this->_obj_CountryConfig->getSymbol().'</symbol>';
			$xml .= '<format>'.$this->_obj_CountryConfig->getPriceFormat().'</format>';
			$xml .= '<alpha2code>'.$this->_obj_CountryConfig->getAlpha2code().'</alpha2code>';
			$xml .= '<alpha3code>'.$this->_obj_CountryConfig->getAlpha3code().'</alpha3code>';
			$xml .= '<code>'.$this->_obj_CountryConfig->getNumericCode().'</code>';
			$xml .= '<value>'.$iAmount.'</value>';
			$xml .= '</amount>';
		}
		if(in_array("amountInfo", $aExcludeNodes) === false)
		{
			$xml .= '<amountInfo>';
			$xml .= '<countryId>'. $this->_obj_CountryConfig->getID() .'</countryId>';
			$xml .= '<currencyId>'. $obj_CurrencyConfig->getID() .'</currencyId>';
			$xml .= '<currency>'. $obj_CurrencyConfig->getCode() .'</currency>';
			$xml .= '<decimals>'. $obj_CurrencyConfig->getDecimals() .'</decimals>';
			$xml .= '<symbol>'. $this->_obj_CountryConfig->getSymbol() .'</symbol>';
			$xml .= '<format>'. $this->_obj_CountryConfig->getPriceFormat() .'</format>';
			$xml .= '<alpha2code>'. $this->_obj_CountryConfig->getAlpha2code() .'</alpha2code>';
			$xml .= '<alpha3code>'. $this->_obj_CountryConfig->getAlpha3code() .'</alpha3code>';
			$xml .= '<code>'. $obj_CurrencyConfig->getID() .'</code>';
			$xml .= '<amount>'. $this->_lAmount .'</amount>';
			$xml .= '</amountInfo>';
		}


		if(in_array("fee", $aExcludeNodes) === false)
		{
			$xml .= '<fee>';
			$xml .= '<countryId>'.$this->_obj_CountryConfig->getID().'</countryId>';
			$xml .= '<currency>'.$obj_CurrencyConfig->getCode().'</currency>';
			$xml .= '<symbol>'.$this->_obj_CountryConfig->getSymbol().'</symbol>';
			$xml .= '<format>'.$this->_obj_CountryConfig->getPriceFormat().'</format>';
			$xml .= '<amount>'.$this->_iFee.'</amount>';
			$xml .= '</fee>';
		}

		if(in_array("price", $aExcludeNodes) === false)
		{
			$xml .= '<price>'. General::formatAmount($this->_obj_CountryConfig, $this->_lAmount) .'</price>';
		}
		if(in_array("points", $aExcludeNodes) === false)
		{
			$xml .= '<points>';
			$xml .= '<countryId>0</countryId>';
			$xml .= '<currency>points</currency>';
			$xml .= '<symbol>points</symbol>';
			$xml .= '<format>{PRICE} {CURRENCY}</format>';
			$xml .= '<amount>'.$this->_iPoints.'</amount>';
			$xml .= '</points>';
		}

		if(in_array("reward", $aExcludeNodes) === false)
		{
			$xml .= '<reward>';
			$xml .= '<countryId>0</countryId>';
			$xml .= '<currency>points</currency>';
			$xml .= '<symbol>points</symbol>';
			$xml .= '<format>{PRICE} {CURRENCY}</format>';
			$xml .= '<amount>'.$this->_iReward.'</amount>';
			$xml .= '</reward>';
		}

		if(in_array("refund", $aExcludeNodes) === false)
		{
			$xml .= '<refund>';
			$xml .= '<countryId>'.$this->_obj_CountryConfig->getID().'</countryId>';
			$xml .= '<currency>'.$obj_CurrencyConfig->getCode().'</currency>';
			$xml .= '<symbol>'.$this->_obj_CountryConfig->getSymbol().'</symbol>';
			$xml .= '<format>'.$this->_obj_CountryConfig->getPriceFormat().'</format>';
			$xml .= '<amount>'.$this->_iRefund.'</amount>';
			$xml .= '</refund>';
		}

		$xml .= '<orderid>'. $this->_sOrderID .'</orderid>';
		if(in_array("mobile", $aExcludeNodes) === false)
		{
			$xml .= '<mobile>';
			$xml .= '<countryId>'.intval($this->_iOperatorID/100).'</countryId>';
			$xml .= '<countryCode>'.$this->_obj_CountryConfig->getCountryCode().'</countryCode>';
			$xml .= '<value>'.$this->_sMobile.'</value>';
			$xml .= '</mobile>';
		}

		$xml .= '<operator>'. $this->_iOperatorID .'</operator>';
		$xml .= '<email>'. $this->_sEMail .'</email>';
		$xml .= '<deviceId>'. $this->_sDeviceID .'</deviceId>';
		$xml .= '<logo>';
		$xml .= '<url>'. htmlspecialchars($this->_sLogoURL, ENT_NOQUOTES) .'</url>';
		$xml .= '</logo>';
		$xml .= '<cssUrl>'. htmlspecialchars($this->_sCSSURL, ENT_NOQUOTES) .'</cssUrl>';
		$xml .= '<acceptUrl>'. htmlspecialchars($this->_sAcceptURL, ENT_NOQUOTES) .'</acceptUrl>';
		$xml .= '<cancelUrl>'. htmlspecialchars($this->_sCancelURL, ENT_NOQUOTES) .'</cancelUrl>';
		$xml .= '<declineUrl>'. htmlspecialchars($this->_sDeclineURL, ENT_NOQUOTES) .'</declineUrl>';
		$xml .= '<callbackUrl>'. htmlspecialchars($this->_sCallbackURL, ENT_NOQUOTES) .'</callbackUrl>';
		$xml .= '<iconUrl>'. htmlspecialchars($this->_sIconURL, ENT_NOQUOTES) .'</iconUrl>';
		$xml .= '<authUrl>'. htmlspecialchars($this->_sAuthenticationURL, ENT_NOQUOTES) .'</authUrl>';
		$xml .= '<language>'. $this->_sLanguage .'</language>';
		$xml .= '<autoCapture>'. General::bool2xml($this->_bAutoCapture) .'</autoCapture>';
		$xml .= '<autoStoreCard>'. General::bool2xml($this->_bAutoStoreCard) .'</autoStoreCard>';
		$xml .= '<markupLanguage>'. $this->_sMarkupLanguage .'</markupLanguage>';
		$xml .= '<customerRef>'. htmlspecialchars($this->_sCustomerRef, ENT_NOQUOTES) .'</customerRef>';
		$xml .= '<description>'. htmlspecialchars($this->_sDescription, ENT_NOQUOTES) .'</description>';
		$xml .= '<ip>'. htmlspecialchars($this->_sIP, ENT_NOQUOTES) .'</ip>';
		$xml .= '<hmac>'. htmlspecialchars($this->getHMAC(), ENT_NOQUOTES) .'</hmac>';
		$xml .= '<createdDate>'. htmlspecialchars(date("Ymd", strtotime($this->getCreatedTimestamp())), ENT_NOQUOTES) .'</createdDate>'; //CCYYMMDD
		$xml .= '<createdTime>'. htmlspecialchars(date("His", strtotime($this->getCreatedTimestamp())), ENT_NOQUOTES) .'</createdTime>'; //hhmmss

		if($this->getAdditionalData("booking-ref") != null)
		{
			$xml .= '<bookingRef>'. htmlspecialchars($this->getAdditionalData("booking-ref"), ENT_NOQUOTES) .'</bookingRef>';
		}

		if($this->getAdditionalData('invoiceid') !== null)
		{
			$xml .= '<invoiceid>'. htmlspecialchars($this->getAdditionalData("invoiceid"), ENT_NOQUOTES) .'</invoiceid>';
		}

		if(!empty($this->_token))
			$xml .= '<token>'.htmlspecialchars($this->_token, ENT_NOQUOTES).'</token>';

		if(!empty($this->_mask))
			$xml .= '<cardMask>'.htmlspecialchars($this->_mask, ENT_NOQUOTES).'</cardMask>';

		if(!empty($this->_expiry))
			$xml .= '<expiry>'.htmlspecialchars($this->_expiry, ENT_NOQUOTES).'</expiry>';

		if(!empty($this->_approvalCode))
			$xml .= '<approvalCode>'.htmlspecialchars($this->_approvalCode, ENT_NOQUOTES).'</approvalCode>';

		if(!empty($this->_actionCode))
			$xml .= '<actionCode>'.htmlspecialchars($this->_actionCode, ENT_NOQUOTES).'</actionCode>';

		if(!empty($this->_authOriginalData))
			$xml .= '<authOriginalData>'.htmlspecialchars($this->_authOriginalData, ENT_NOQUOTES).'</authOriginalData>';

		if( empty($this->_obj_OrderConfigs) === false && in_array("orders", $aExcludeNodes) === false)
		{

			$xml .= $this->getAttributeLessOrdersXML();
		}
		if($this->getAdditionalData() != null && in_array("additionalData", $aExcludeNodes) === false)
		{
			$xml .= "<additionalData>";
			foreach ($this->getAdditionalData() as $key=>$value)
			{
				if (strpos($key, 'rule') === false)
				{
					$xml .= '<param>';
					$xml .=  '<name>'. $key . '</name>';
					$xml .=  '<value>'. $value . '</value>';
					$xml .= '</param>';
				}
			}
			$xml .="</additionalData>";
		}

		if(!empty($this->_iInstallmentValue) && $this->getInstallmentValue() > 0)
		{
			$xml .= '<installment><value>'.htmlspecialchars($this->_iInstallmentValue, ENT_NOQUOTES).'</value></installment>';
		}

		if($this->getProfileID() > 0)
		{
			$xml .= '<profileid>'.htmlspecialchars($this->getProfileID(), ENT_NOQUOTES).'</profileid>';
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

	public static function produceTxnInfoFromExternalRef(RDB $obj, $sToken, array $data = array())
	{
		$sql = self::_constProduceQuery();

		$sql .= " INNER JOIN Log".sSCHEMA_POSTFIX.".ExternalReference_Tbl er
		            ON t.id = er.txnid AND er.externalid = ". $obj->escStr($sToken);
        //echo $sql; exit;
		$RS = $obj->getName($sql);
		$obj_TxnInfo = self::_produceFromResultSet($obj, $RS);

        if ( ($obj_TxnInfo instanceof TxnInfo) === false) { throw new TxnInfoException("Transaction with Token: ". $sToken. " not found", 1001); }
        return self::produceInfo($obj_TxnInfo->getID(),  $obj, $obj_TxnInfo, $data);
	}

	private static function _constProduceQuery()
	{
		$sql = "SELECT t.id, typeid, countryid,currencyid, amount, Coalesce(points, -1) AS points, Coalesce(reward, -1) AS reward, orderid, extid, mobile, operatorid, email, lang, logourl, cssurl, accepturl, declineurl, cancelurl, callbackurl, iconurl, \"mode\", auto_capture, gomobileid,
						t.clientid, accountid, keywordid, Coalesce(euaid, -1) AS euaid, customer_ref, markup, refund, authurl, ip, description, t.pspid, fee, captured, cardid, walletid, deviceid, mask, expiry, token, authoriginaldata,attempt,sessionid, producttype,approval_action_code, t.created,virtualtoken, installment_value, t.profileid,
						COALESCE(convetredcurrencyid,currencyid) as convetredcurrencyid,COALESCE(convertedamount,amount) as convertedamount,COALESCE(conversionrate,1) as conversionrate,issuing_bank  
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
			$obj_ConvertedCurrencyConfig = null;
			if(intval($RS["CONVETREDCURRENCYID"]  )>0) $obj_ConvertedCurrencyConfig = CurrencyConfig::produceConfig($obj, $RS["CONVETREDCURRENCYID"]);
            $obj_AdditionaData = self::_produceAdditionalData($obj, $RS["ID"]);
            $obj_ExternalRefData = self::_produceExternalReference($obj, $RS["ID"]);
            $aBillingAddr = self::_produceBillingAddr($obj, $RS["ID"]);
			$paymentSession = null;
            if($RS["SESSIONID"] == -1){
                $paymentSession = PaymentSession::Get($obj, $obj_ClientConfig,$obj_CountryConfig,$obj_CurrencyConfig,$RS["AMOUNT"], $RS["ORDERID"],"",$RS["MOBILE"], $RS["EMAIL"], $RS["EXTID"],$RS["DEVICEID"], $RS["IP"]);
            }
            else{
                $paymentSession = PaymentSession::Get($obj,$RS["SESSIONID"]);
            }

            $obj_TxnInfo = new TxnInfo($RS["ID"], $RS["TYPEID"], $obj_ClientConfig, $obj_CountryConfig,$obj_CurrencyConfig, $RS["AMOUNT"], $RS["POINTS"], $RS["REWARD"], $RS["REFUND"], $RS["ORDERID"], $RS["EXTID"], $RS["MOBILE"], $RS["OPERATORID"], $RS["EMAIL"], $RS["DEVICEID"], $RS["LOGOURL"], $RS["CSSURL"], $RS["ACCEPTURL"], $RS["DECLINEURL"], $RS["CANCELURL"], $RS["CALLBACKURL"], $RS["ICONURL"], $RS["AUTHURL"], $RS["LANG"], $RS["MODE"], $RS["AUTO_CAPTURE"], $RS["EUAID"], $RS["CUSTOMER_REF"], $RS["GOMOBILEID"], false, $RS["MARKUP"], $RS["DESCRIPTION"], $RS["IP"], $RS["ATTEMPT"], $paymentSession, $RS["PRODUCTTYPE"], $RS["INSTALLMENT_VALUE"], $RS["PROFILEID"], $RS["PSPID"], $RS["FEE"], $RS["CAPTURED"],$RS["CARDID"],$RS["WALLETID"],$RS["MASK"],$RS["EXPIRY"],$RS["TOKEN"],$RS["AUTHORIGINALDATA"],$RS["APPROVAL_ACTION_CODE"],$RS['CREATED'],$RS["VIRTUALTOKEN"], $obj_AdditionaData,$obj_ExternalRefData,$RS["CONVERTEDAMOUNT"],$obj_ConvertedCurrencyConfig,$RS["CONVERSIONRATE"],$RS["ISSUING_BANK"],$aBillingAddr);
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
	public static function produceInfo($id, RDB $obj_db, &$obj= null, array &$misc=null )
	{
		$obj_TxnInfo = null;
		switch (true)
		{
		case ($obj instanceof TxnInfo):	// Instantiate from array of new Transaction Information
			// Use data from provided Data Object for all unspecified values
			if (array_key_exists("typeid", $misc) === false) { $misc["typeid"] = $obj->getTypeID(); }
			if (array_key_exists("client-config", $misc) === false) { $misc["client-config"] = $obj->getClientConfig(); }
			if (array_key_exists("country-config", $misc) === false) { $misc["country-config"] = $obj->getCountryConfig(); }
			if (array_key_exists("currency-config", $misc) === false) { $misc["currency-config"] = $obj->getInitializedCurrencyConfig(); }
			if (array_key_exists("card-id", $misc) === false) { $misc["card-id"] = $obj->getCardID(); }
			if (array_key_exists("wallet-id", $misc) === false) { $misc["wallet-id"] = $obj->getWalletID(); }
			if (array_key_exists("amount", $misc) === false) { $misc["amount"] = $obj->getInitializedAmount();}
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
            if (array_key_exists("decline-url", $misc) === false) { $misc["decline-url"] = $obj->getDeclineURL(); }
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
            if (array_key_exists("attempt", $misc) === false) { $misc["attempt"] = $obj->getAttemptNumber(); }
            if (array_key_exists("sessionid", $misc) === false) { $misc["sessionid"] = $obj->getSessionId(); }
            if (array_key_exists("sessiontype", $misc) === false) { $misc["sessiontype"] = 1; }
            if (array_key_exists("producttype", $misc) === false) { $misc["producttype"] = 100; }
            if (array_key_exists("mask", $misc) === false) { $misc["mask"] = $obj->getCardMask(); }
            if (array_key_exists("expiry", $misc) === false) { $misc["expiry"] = $obj->getCardExpiry(); }
            if (array_key_exists("token", $misc) === false) { $misc["token"] = $obj->getToken(); }
            if (array_key_exists("authoriginaldata", $misc) === false) { $misc["authoriginaldata"] = $obj->getAuthoriginalData(); }
            if (array_key_exists("approval_action_code", $misc) === false) { $misc["approval_action_code"] = $obj->getApprovalActionCode(); }
            if (array_key_exists("created", $misc) === false) { $misc["created"] = $obj->getCreatedTimestamp(); }
            if (array_key_exists("additionaldata", $misc) === false) { $misc["additionaldata"] = $obj->getAdditionalData(); }
            if (array_key_exists("externalref", $misc) === false) { $misc["externalref"] = $obj->getExternalRef(); }
            if (array_key_exists("converted-currency-config", $misc) === false) { $misc["converted-currency-config"] = $obj->getConvertedCurrencyConfig(); }
            if (array_key_exists("converted-amount", $misc) === false) { $misc["converted-amount"] = $obj->getConvertedAmount(); }
            if (array_key_exists("conversion-rate", $misc) === false) { $misc["conversion-rate"] = $obj->getConversationRate(); }
            if (array_key_exists("issuing-bank", $misc) === false) { $misc["issuing-bank"] = $obj->getIssuingBankName(); }
            if (array_key_exists("billingAddr", $misc) === false) { $misc["billingAddr"] = $obj->getBillingAddr(); }

            $paymentSession = null;
            if( $misc["sessionid"] == -1){
                $paymentSession = PaymentSession::Get($obj_db, $misc["client-config"],$misc["country-config"],$misc["currency-config"],$misc["amount"], $misc["orderid"],$misc["sessiontype"],$misc["mobile"], $misc["email"], $misc["extid"],$misc["device-id"], $misc["ip"]);
            }
            else{
                $paymentSession = PaymentSession::Get($obj_db,$misc["sessionid"]);
            }
            if (array_key_exists("installment-value", $misc) === false) { $misc["installment-value"] = $obj->getInstallmentValue(); }
			if (array_key_exists("profileid", $misc) === false) { $misc["profileid"] = $obj->getProfileID(); }


			$obj_TxnInfo = new TxnInfo($id, $misc["typeid"], $misc["client-config"], $misc["country-config"], $misc["currency-config"], $misc["amount"], $misc["points"], $misc["reward"], $misc["refund"], $misc["orderid"], $misc["extid"], $misc["mobile"], $misc["operator"], $misc["email"],  $misc["device-id"],$misc["logo-url"], $misc["css-url"], $misc["accept-url"], $misc["decline-url"], $misc["cancel-url"], $misc["callback-url"], $misc["icon-url"], $misc["auth-url"], $misc["language"], $misc["mode"], $misc["auto-capture"], $misc["accountid"], @$misc["customer-ref"], $misc["gomobileid"], $misc["auto-store-card"], $misc["markup"], $misc["description"], $misc["ip"], $misc["attempt"], $paymentSession, $misc["producttype"], $misc["installment-value"], $misc["profileid"],$misc["psp-id"],  $misc["fee"], $misc["captured-amount"], $misc["card-id"], $misc["wallet-id"],$misc["mask"],$misc["expiry"],$misc["token"],$misc["authoriginaldata"],$misc["approval_action_code"],$misc["created"],"",$misc["additionaldata"],
					$misc["externalref"],$misc["converted-amount"],$misc["converted-currency-config"],$misc["conversion-rate"],$misc["issuing-bank"],$misc["billingAddr"]);


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
            if (array_key_exists("producttype", $misc) === false) { $misc["producttype"] = 100; }
			if (array_key_exists("attempt", $misc) === false) { $misc["attempt"] = 0 ; }
			if (array_key_exists("installment-value", $misc) === false) { $misc["installment-value"] = 0 ; }
			if (array_key_exists("converted-currency-config", $misc) === false) { $misc["converted-currency-config"] = $obj->getConvertedCurrencyConfig(); }
			if (array_key_exists("converted-amount", $misc) === false) { $misc["converted-amount"] = $obj->getConvertedAmount(); }
			if (array_key_exists("conversion-rate", $misc) === false) { $misc["conversion-rate"] = $obj->getConversationRate(); }
			if (array_key_exists("profileid", $misc) === false) { $misc["profileid"] = -1; }

			if(isset($misc["sessionid"]) == false || empty($misc["sessionid"]) == true)
                $misc["sessionid"] = -1;

            $paymentSession = null;
            if($misc["sessionid"] == -1){
                $paymentSession = PaymentSession::Get($obj_db, $obj,$misc["country-config"], $misc["currency-config"], $misc["amount"], $misc["orderid"], $misc["sessiontype"], $misc["mobile"], $misc["email"], $misc["extid"],$misc["device-id"], $misc["ip"]);
            }
            else{
                $paymentSession = PaymentSession::Get($obj_db,$misc["sessionid"]);
            }

            $obj_TxnInfo = new TxnInfo($id, $misc["typeid"], $obj, $misc["country-config"],$misc["currency-config"], $misc["amount"], $misc["points"], $misc["reward"], $misc["refund"], $misc["orderid"], $misc["extid"], $misc["mobile"], $misc["operator"], $misc["email"], $misc["device-id"], $misc["logo-url"], $misc["css-url"], $misc["accept-url"], $misc["decline-url"], $misc["cancel-url"], $misc["callback-url"], $misc["icon-url"], $misc["auth-url"], $misc["language"], $obj->getMode(), AutoCaptureType::eRunTimeAutoCapt, $misc["accountid"], @$misc["customer-ref"], $misc["gomobileid"], $misc["auto-store-card"], $misc["markup"], $misc["description"], $misc["ip"], $misc["attempt"], $paymentSession, $misc["producttype"],$misc["installment-value"], $misc["profileid"],-1,0,0,-1,-1,"","","","","","","",array(),array(),$misc["converted-amount"],$misc["converted-currency-config"],$misc["conversion-rate"],"");
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

	public static function  _produceAdditionalData($_OBJ_DB, $txnId)
    {
        $additionalData = [];
        $sqlA = "SELECT name, value FROM log" . sSCHEMA_POSTFIX . ".additional_data_tbl WHERE type='Transaction' and externalid=" . $txnId;
        $rsa = $_OBJ_DB->getAllNames ( $sqlA );
        if (empty($rsa) === false )
        {
            foreach ($rsa as $rs)
            {
                $additionalData[$rs["NAME"] ] = $rs ["VALUE"];
            }
        }
        return $additionalData;
    }

	public static function  _produceBillingAddr($_OBJ_DB, $txnId)
	{
		$aBillingAddr = [];
		$sqlA = "SELECT id, name, street, street2, city, state, zip, country FROM log" . sSCHEMA_POSTFIX . ".address_tbl WHERE reference_type='transaction' and reference_id=" . $txnId;
		$rsa = $_OBJ_DB->getAllNames ( $sqlA );
		if (empty($rsa) === false )
		{
			foreach ($rsa as $rs)
			{
				$aBillingAddr["name" ] = $rs ["NAME"];
				$aBillingAddr["street" ] = $rs ["STREET"];
				$aBillingAddr["street2" ] = $rs ["STREET2"];
				$aBillingAddr["city" ] = $rs ["CITY"];
				$aBillingAddr["state" ] = $rs ["STATE"];
				$aBillingAddr["zip" ] = $rs ["ZIP"];
				$aBillingAddr["country" ] = $rs ["COUNTRY"];
			}
		}
		return $aBillingAddr;
	}

    static function  _produceExternalReference($_OBJ_DB, $txnId)
    {
        $externalRefTypeData = [];
        $sqlA = "SELECT externalid, pspid,type FROM Log" . sSCHEMA_POSTFIX . ".Externalreference_Tbl WHERE txnid=" . $txnId ;

        $rsa = $_OBJ_DB->getAllNames ( $sqlA );
        if (empty($rsa) === false )
        {
            foreach ($rsa as $rs)
            {
                $externaltypeData = [];
                $externaltypeData[$rs["PSPID"] ] = $rs ["EXTERNALID"];
                $externalRefTypeData[$rs["TYPE"] ] = $externaltypeData;
            }
        }

        return $externalRefTypeData;
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

				$orderFees = isset($aOrderDataObj["fees"]) ? $aOrderDataObj["fees"] : 0;
				$sql = "INSERT INTO Log".sSCHEMA_POSTFIX.".Order_Tbl
							(id, orderref, txnid, countryid, amount, quantity, productsku, productname, productdescription, productimageurl, points, reward,fees)
						VALUES
							(". $RS["ID"] .", '". (string)$aOrderDataObj["orderref"] ."', ". $this->getID() .", ". $aOrderDataObj["country-id"] .", ". $aOrderDataObj["amount"] .", ". $aOrderDataObj["quantity"] .", '". $obj_DB->escStr($aOrderDataObj["product-sku"]) ."', '". $obj_DB->escStr($aOrderDataObj["product-name"]) ."',
							 '". $obj_DB->escStr($aOrderDataObj["product-description"]) ."', '". $obj_DB->escStr($aOrderDataObj["product-image-url"]) ."', ". $aOrderDataObj["points"] .", ". $aOrderDataObj["reward"] ." ,".$orderFees.")";
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
	 * Function to insert new records in the Billing Summary Related to that Order in table that are send as part of the transaction cart details
	 *
	 * @param 	Array $aBillingSummary	Data object with the Billing summary Data details
	 *
	 */
	public function setBillingSummary(RDB $obj_DB, $aBillingSummary)
	{
		if( is_array($aBillingSummary) === true )
		{
			$sql = "SELECT Nextvalue('Log".sSCHEMA_POSTFIX.".Billing_Summary_Tbl_id_seq') AS id FROM DUAL";
			$RS = $obj_DB->getName($sql);

			if (is_array($RS) === false) { throw new mPointException("Unable to generate new Billing Summary ID", 1001); }

			$sql = "INSERT INTO Log".sSCHEMA_POSTFIX.".Billing_Summary_Tbl
						(id, order_id, journey_ref, bill_type, type_id, description, amount, currency, created, modified)
					VALUES
						(". $RS["ID"] .", '". $aBillingSummary["order_id"] ."', '". $aBillingSummary["journey_ref"] ."', '". $aBillingSummary["bill_type"] ."', '". $aBillingSummary["type_id"] ."', '". $aBillingSummary["description"] ."', '". $aBillingSummary["amount"] ."', '". $aBillingSummary["currency"] ."',now(),now())";
			
			if (is_resource($obj_DB->query($sql) ) === false)
			{
				if (is_array($RS) === false) { throw new mPointException("Unable to insert new record for billing summary: ". $RS["ID"], 1002); }
			}
			else
			{
				$Billing_Summary_iD = $RS["ID"];
			}
			return $Billing_Summary_iD;
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
			    $name = $aAdditionalDataObj["name"];
			    $value = $aAdditionalDataObj["value"];
			    if($name === null || empty($name) === true || $value === null || empty($value) === true)
                {
                    return $additional_id;
                }
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
	 * Function to insert new records in the externalreference_tbl
	 *
	 * @param 	Array $aExternalReference	Data object with the External Reference Data details
	 *
	 */
	public function setExternalReference(RDB $obj_DB, $iPSPId,$iExternalRefType,$sReference)
	{
		$aExternalRef[$iExternalRefType] = array($iPSPId => $sReference);
		self::setExternalReferences($obj_DB,$aExternalRef);
	}

	/**
	 * Function to insert new records in the externalreference_tbl
	 *
	 * @param Array $aExternalReferences Data object with the External Reference Data details
	 *
	 * @throws mPointException
	 */
	public function setExternalReferences(RDB $obj_DB, $aExternalReferences)
	{
		if( is_array($aExternalReferences) === true )
		{
			foreach ($aExternalReferences as $refTypekey => $aExternalRefObj)
			{
				if (is_array($aExternalRefObj))
				{
					foreach ($aExternalRefObj as $pspidKey => $externalRef)
					{
						$sql = "INSERT INTO Log" . sSCHEMA_POSTFIX . ".ExternalReference_Tbl
					        (type,txnid, externalid, pspid) VALUES (" . $refTypekey . "," . $this->getID() . ", " . $externalRef . ", " . $pspidKey . ")";

						if (is_resource($obj_DB->query($sql)) === false)
						{
							throw new mPointException("Unable to insert new record for External Reference Table: for external reference id " . $refTypekey . " and pspid id " . $pspidKey, 1002);
						}
					}
				}
			}
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

				$sql = "INSERT INTO Log".sSCHEMA_POSTFIX.".flight_Tbl(id, service_class,flight_number, departure_airport, arrival_airport, airline_code, order_id, arrival_date, departure_date, created, modified, tag, trip_count, service_level, departure_countryid, arrival_countryid)
					VALUES('". $RS["ID"] ."','". $aFlightData["service_class"] ."','". $aFlightData["flight_number"] ."','". $aFlightData["departure_airport"] ."','". $aFlightData["arrival_airport"] ."','". $aFlightData["airline_code"] ."','". $aFlightData["order_id"] ."','". $aFlightData["arrival_date"] ."', '". $aFlightData["departure_date"] ."',now(),now(), '". $aFlightData["tag"] ."', '". $aFlightData["trip_count"] ."', '". $aFlightData["service_level"] ."', '". $aFlightData["departure_country"] ."', '". $aFlightData["arrival_country"] ."')";
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

	public function produceOrderConfig(RDB $obj_DB, $ticketNumbers=null)
	{
		//Get Order Detail of a given transaction if supplied by the e-commerce platform.
		$this->_obj_OrderConfigs = OrderInfo::produceConfigurations($obj_DB, $this->getID(), $ticketNumbers);
		
		
	}
	
	public function getOrdersXML($ticketNumbers = null)
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

	public function getAttributeLessOrdersXML($ticketNumbers = null)
	{
		$xml = '';
		if( empty($this->_obj_OrderConfigs) === false )
		{
			$xml .= '<orders>';
			foreach ($this->_obj_OrderConfigs as $obj_OrderInfo)
			{
				if( ($obj_OrderInfo instanceof OrderInfo) === true )
				{
					$xml .= $obj_OrderInfo->toAttributeLessXML();
				}
			}
			$xml .= '</orders>';
		}

		return $xml;
	}

    /**
     * Returns Payment Session ID
     *
     * @return integer
     *
     */
	function getSessionId(){
	    if($this->_obj_PaymentSession instanceof PaymentSession)
        {
            return $this->_obj_PaymentSession->getId();
        }
        return -1;
    }

    /**
     * Returns Payment Session information in XML format
     *
     * @return string
     *
     */
    function getPaymentSessionXML(){
	    return $this->_obj_PaymentSession->toXML();
    }

    /**
     * Function to update the transaction amount
     *
     * @param $obj_DB     Database object
     * @param $amount     New amount of transaction
     *
     */
    function updateTransactionAmount(RDB $obj_DB,$amount){
        $sql = "UPDATE log" . sSCHEMA_POSTFIX . ".Transaction_Tbl SET amount = ".$amount." and convertedamount = ".$amount."  WHERE id = " . $this->_iID;
        $obj_DB->query($sql);
    }

    /**
     * Returns the Product Type
     *
     * @return 	integer
     */
    public function getProductType() { return $this->_iProductType; }

    /**
     * Returns the Card token.
     *
     * @return 	String
     */
    function getToken() {
	    return $this->_token;
    }

    /**
     * Returns the Card Mask.
     *
     * @return 	String
     */
    function getCardMask() {
        return $this->_mask;
    }

    /**
     * Returns the Card Expiry.
     *
     * @return 	String
     */
    function getCardExpiry() {
        return $this->_expiry;
    }

    /**
     * Returns the Auth original Data
     *
     * @return 	String
     */
    function getAuthoriginalData() {
        return $this->_authOriginalData;
    }

    /**
     * Returns the Approcal and action code.
     *
     * @return 	String
     */
    function getApprovalActionCode() {
        return $this->_approvalCode. ":".$this->_actionCode;
    }

    /**
     * Returns the Transaction created date and time.
     *
     * @return 	String
     */
    function getCreatedTimestamp() {
        return $this->_createdTimestamp;
    }


	/**
	 * @param RDB $obj_DB
	 * @param integer $cardid Card used for payment
	 * @param string $mask Mask card number
	 * @param string $expiry Expiry of card
	 * @param integer $pspId
	 * @throws SQLQueryException
	 */
	function updateCardDetails(RDB $obj_DB, $cardid, $mask = null, $expiry= null, $pspId = null)
    {
       try
       {
           $sql = "UPDATE Log" . sSCHEMA_POSTFIX . ".Transaction_Tbl SET cardid = " . intval($cardid) .", pspid = ". $pspId;

           if(empty($mask) ===false && empty($expiry) === false)
           {
			   $sql .= " ,mask = '" . $mask . "' , expiry = '" . $expiry . "'";
		   }
			$sql .= " WHERE id=". $this->getID();
           $obj_DB->query($sql);
       }
       catch (mPointException $e)
       {
            trigger_error("Failed to update card details (log.transaction_tbl)", E_USER_ERROR);
       }
    }

    public function getPSPType(RDB $obj_DB)
	{
		try
        {
            if($this->_iProcessorType === 0)
            {
                $query = "SELECT system_type,name FROM system" . sSCHEMA_POSTFIX . ".psp_tbl WHERE id = '" . $this->_iPSPID . "'";

                $resultSet = $obj_DB->getName($query);
                if (is_array($resultSet) === true)
                {
                    $processorType = $resultSet['SYSTEM_TYPE'];
                    if($processorType !== null && $processorType !== '')
                    {
                        $this->_iProcessorType = $processorType;
                        $this->_sPSPName = $resultSet['NAME'];
                    }
                }
            }

        }
        catch (mPointException $mPointException)
        {
            trigger_error("Failed to update psp details (log.transaction_tbl)", E_USER_ERROR);
        }
		$stdClassObj=new stdClass();
		$stdClassObj->ProcessorType = $this->_iProcessorType;
		$stdClassObj->PSPName = $this->_sPSPName;
		return $stdClassObj;
	}

    /**
     * @param RDB $obj_DB
     * @return string
     */
    public function getPaymentMethod(RDB $obj_DB)
    {
        try
        {
            if($this->_iPaymentType == 0)
            {
                $query = "SELECT paymenttype,name FROM system" . sSCHEMA_POSTFIX . ".card_tbl WHERE id = '" . $this->_iCardID . "'";

                $resultSet = $obj_DB->getName($query);
                if (is_array($resultSet) === true)
                {
                    $paymentType = $resultSet['PAYMENTTYPE'];
                    if($paymentType !== null && $paymentType !== '')
                    {
                        $this->_iPaymentType = $paymentType;
                        $this->_sCardName = $resultSet['NAME'];
                    }
                }
            }

        }
        catch (mPointException $mPointException)
        {
            trigger_error("Failed to update card details (log.transaction_tbl)", E_USER_ERROR);
        }
		$paymentMethod = 'CASH';
		switch ($this->_iPaymentType) {
			case 1:
				$paymentMethod = 'CD';
				break;
			case 2:
				$paymentMethod = 'CASH';
				break;
			case 3:
				$paymentMethod = 'eWallet';
				break;
			case 4:
				$paymentMethod = 'CASH';
				break;
			case 7:
				$paymentMethod = 'DD';
				break;
			default:
				$paymentMethod = 'CASH';
		}

		$stdClassObj=new stdClass();
		$stdClassObj->PaymentType = $this->_iPaymentType;
		$stdClassObj->PaymentMethod = $paymentMethod;
		$stdClassObj->CardName = $this->_sCardName;
		return $stdClassObj;
    }

    public function getLatestPaymentState(RDB $obj_DB)
    {
        $stateId = 0;
        try
        {
            $query = "SELECT stateid FROM log" . sSCHEMA_POSTFIX . ".message_tbl WHERE txnid = '" . $this->getID() . "'";

            $resultSet = $obj_DB->getName($query);
            if (is_array($resultSet) === true)
            {
                $stateid = $resultSet['STATEID'];
                if($stateid !== null && $stateid !== '')
                {
                    $stateId = $stateid;
                }
            }

        }
        catch (mPointException $mPointException)
        {
            trigger_error("Failed to Get Transaction's Latest State (log.message_tbl)", E_USER_ERROR);
        }
        return $stateId;
    }

    public function setInvoiceId(RDB $obj_DB, $invoiceId)
    {
    	if(isset($invoiceId))
    	{

	 		$additionalTxnData = [];
            $additionalTxnData[0]['name'] = 'invoiceid';
            $additionalTxnData[0]['value'] = $invoiceId;
            $additionalTxnData[0]['type'] = 'Transaction';
			$this->setAdditionalDetails($obj_DB, $additionalTxnData,$this->getID());
			$this->_aAdditionalData['invoiceid'] = $invoiceId;
		}
    }

    public function getMessageData(RDB $obj_DB,$stateIds)
	{
		if(empty($stateIds) === true ) return null;
		$stateIds = implode(",", $stateIds);
		$sql = "SELECT id, stateid, created, data 
         		FROM Log".sSCHEMA_POSTFIX.".Message_Tbl
				WHERE txnid = ". $this->getID() ." AND enabled = '1'
				AND stateid in (". $stateIds.")
				ORDER BY id DESC";
		$res = $obj_DB->query($sql);
		$aMessages = array();
		while ($RS = $obj_DB->fetchName($res) )
		{
			$aMessages[] = array_change_key_case($RS, CASE_LOWER);
		}
		return $aMessages;
	}

	public function getFinalSettlementAmount(RDB $obj_DB,$aStateIDs)
	{
		$captureAmount = 0;
		$messageData = $this->getMessageData($obj_DB, $aStateIDs);
		$captureAmount = isset($messageData[0]['data'])?(int)$messageData[0]['data']:0;
		if ($captureAmount === 0) {
			$captureAmount = -1;
		}
		return $captureAmount;
	}

	public function isTicketNumberIsAlreadyLogged(RDB $obj_DB, $ticketNumber)
	{
		$isTicketNumberIsAlreadyLogged = FALSE;
		$sql = 'SELECT count(t.id) FROM log.order_tbl t				
     			WHERE ORDERREF = \''.$ticketNumber.'\'
				AND TXNID = '.$this->getID();

		$RS = $obj_DB->getName($sql);

		if (is_array($RS) === true && $RS['COUNT'] > 0)
		{
			$isTicketNumberIsAlreadyLogged = TRUE;
		}
		return $isTicketNumberIsAlreadyLogged;
	}

	public function getOrderConfigs()
	{
		return $this->_obj_OrderConfigs;
	}

	public function updateRefundedAmount(RDB $obj_DB, $iAmount)
	{
		$retStatus = FALSE;
		if (($obj_DB instanceof RDB) === false && $obj_DB != null) {  throw new Exception("Failed to connect to database"); }

		try
		{
			$sql = "UPDATE Log".sSCHEMA_POSTFIX.".Transaction_Tbl
						SET refund = refund + ". (int)$iAmount ."
						WHERE id = ". $this->getID();
			//			echo $sql ."\n";die;
			$res = $obj_DB->query($sql);

			// Refund amount updated successfully
			if(is_resource($res) === true && $obj_DB->countAffectedRows($res) === 1){ $retStatus = TRUE; }
		}
		catch (mPointException $mPointException)
		{
			trigger_error("Failed to update refund amount (log.transaction_tbl)", E_USER_ERROR);
		}
		return $retStatus;
	}
}
?>
