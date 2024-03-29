<?php
/**
 * The Configuration package contains various data classes holding information such as:
 * 	- Configuration for the Country the transaction is processed in
 * 	- Configuration for the Client on whose behalf mPoint is processing the transaction
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Config
 * @subpackage ClientConfig
 * @version 1.10
 */

use api\classes\merchantservices\MetaData\ClientServiceStatus;

/**
 * Data class holding the Client Configuration as well as the client's default data fields including:
 * 	- logo-url
 * 	- css-url
 * 	- accept-url
 * 	- cancel-url
 * 	- callback-url
 *
 */
class ClientConfig extends BasicConfig
{

    private static $instances = [];

    /**
	 * Constants for each URL Type
	 *
	 * @var integer
	 */
	const iCUSTOMER_IMPORT_URL = 1;
	const iAUTHENTICATION_URL = 2;
	const iNOTIFICATION_URL = 3;
	const iMESB_URL = 4;
	const iLOGO_URL = 5;
	const iCSS_URL = 6;
	const iCALLBACK_URL = 7;
	const iACCEPT_URL = 8;
	const iCANCEL_URL = 9;
	const iICON_URL = 10;	
	const iDECLINE_URL = 11;
	const iPARSE_3DSECURE_CHALLENGE_URL = 12;
	const iMERCHANT_APP_RETURN_URL = 13;
    const iBASE_IMAGE_URL = 14;
    const iTHREED_REDIRECT_URL= 15;
    const iBASE_ASSET_URL= 16;
    const iHPP_URL= 17;
	/**
	 * ID of the Flow the Client's customers have to go through in order to complete the Payment Transaction
	 *
	 * @var integer
	 */
	private $_iFlowID;
	/**
	 * Configuration for the Account the Transaction will be associated with
	 *
	 * @var AccountConfig
	 */
	private $_obj_AccountConfig;
	/**
	 * Configuration for the Multiple Accounts the Transaction will be associated with
	 *
	 * @var Array
	 */
	private $_aObj_AccountsConfigurations;
    /**
     * Services status Configuration for the Clients
     *
     * @var Array
     */
    private $_aObj_ClientServicesStatus;

	/**
	 * Configuration for Multiple Merchant Accounts the Transaction will be associated with
	 *
	 * @var Array
	 */
	private $_aObj_MerchantAccounts;
	/**
	 * Configuration for the Cards used by the client.
	 *
	 * @var Array
	 */
	private $_aObj_PaymentMethodConfigurations;
	/**
	 * Configuration for the Issuer Identification ranges for the client.
	 *
	 * @var Array
	 */
	private $_aObj_IINRangeConfigurations;
    /**
     * Configuration for the GoMobile Channel Specific for the client.
     *
     * @var Array
     */
    private $_aObj_GoMobileConfigurations;
    /**
     * Configuration for the Communication Channels enabled for the client.
     * This is a sum of the values as :
     * 1. SMS channel enabled
     * 3. PUSH notifications channel enabled
     * 5. Email channel enabled
     *
     * @var Array
     */
    private $_obj_CommunicationChannelsConfig;
	/**
	 * Client's Username for GoMobile
	 *
	 * @var string
	 */
	private $_sUsername;
	/**
	 * Client's Password for GoMobile
	 *
	 * @var string
	 */
	private $_sPassword;
	/**
	 * Configuration for the Country the Client can process transactions in
	 *
	 * @var CountryConfig
	 */
	private $_obj_CountryConfig;
	/**
	 * Configuration for the Keyword the Client uses to send messages through
	 *
	 * @var KeywordConfig
	 */
	private $_obj_KeywordConfig;

	/**
	 * Object that holds the URL to the Client's Logo which will be displayed on all payment pages
	 *
	 * @var ClientURLConfig
	 */
	private $_obj_LogoURL;
	/**
	 * Object that holds the URL to the CSS file that should be used to customising the payment pages
	 *
	 * @var string
	 */
	private $_obj_CSSURL;
	/**
	 * Object that holds the URL where the Customer should be returned to upon successfully completing the Transaction
	 *
	 * @var ClientURLConfig
	 */
	private $_obj_AcceptURL;
	/**
	 * Object that holds the URL where the Customer should be returned to in case he / she cancels the Transaction midway
	 *
	 * @var ClientURLConfig
	 */
	private $_obj_CancelURL;
	/**
	 * Object that holds where the Customer should be returned to in case the PSP declines the Transaction
	 *
	 * @var ClientURLConfig
	 */
	private $_obj_DeclineURL;
	/**
	 * Object that holds the URL to the Client's Back Office where mPoint should send the Payment Status to
	 *
	 * @var ClientURLConfig
	 */
	private $_obj_CallbackURL;
	/**
	 * Object that holds the URL to the Client's My Account Icon
	 *
	 * @var ClientURLConfig
	 */
	private $_obj_IconURL;
	/**
	 * Object that holds the URL to the Client URL for parsing 3D secure challenge
	 *
	 * @var ClientURLConfig
	 */
	private $_obj_Parse3DSecureChallengeURL;
	/**
	 * Object that holds the customer import URL
	 *
	 * @var ClientURLConfig
	 */
	private $_obj_CustomerImportURL;
	/**
	 * Object that holds the customer customer authetication URL
	 *
	 * @var ClientURLConfig
	 */
	private $_obj_AuthenticationURL;
	/**
	 * Object that holds the customer notification URL
	 *
	 * @var ClientURLConfig
	 */
	private $_obj_NotificationURL;
	/**
	 * Object that holds the MESB URL
	 *
	 * @var ClientURLConfig
	 */
	private $_obj_MESBURL;
	/**
	 * Max Amount an mPoint Transaction can cost the customer for the Client
	 *
	 * @var integer
	 */
	private $_iMaxAmount;
	/**
	 * The language that all payment pages should be rendered in by default for the Client
	 *
	 * @var string
	 */
	private $_sLanguage;
	/**
	 * Boolean Flag indicating whether mPoint should send out an SMS Receipt to the Customer by the Callback Module
	 * upon successful completion of the Payment
	 *
	 * @var boolean
	 */
	private $_bSMSReceipt;
	/**
	 * Boolean Flag indicating whether access to the E-Mail Receipt component should be enabled for customers
	 *
	 * @var boolean
	 */
	private $_bEmailReceipt;
	/**
	 * The method used by mPoint when performing a Callback to the Client.
	 * This can be one of the following:
	 * 	- mPoint, Callback is perfomed using mPoint's native protocol
	 * 	- PSP, Callback is performed using the PSP's protocol by re-constructing the request received from the PSP
	 *
	 * @see Callback::notifyClient()
	 *
	 * @var string
	 */
	private $_sMethod;

	/**
	 * Terms & Conditions for the Shop
	 *
	 * @var string
	 */
	private $_sTerms;
	/**
	 * Client mode in which all Transactions are Processed
	 * 	0. Production
	 * 	1. Test Mode with prefilled card Info
	 * 	2. Certification Mode
	 *
	 * @var integer
	 */
	private $_iMode;
	/**
	 * Boolean Flag indicating whether mPoint should use enable CVV for the Client.
	 *
	 * @var boolean
	 */
	private $_bEnableCVV;
	/**
	 * Boolean Flag indicating whether mPoint should use Auto Capture for the Client.
	 *
	 * @var boolean
	 */
	private $_bAutoCapture;
	/**
	 * Boolean Flag indicating whether mPoint should include the PSP's ID for the Payment in the Callback to the Client.
	 *
	 * @var boolean
	 */
	private $_bSendPSPID;
	/**
	 * Setting determining if / how the end-user's Card Info is stored:
	 * 	0. Off
	 * 	1. Client Only
	 * 	2. Global
	 *
	 * @var integer
	 */
	private $_iStoreCard;
	/**
	 * List of IP white-listed by The System
	 *
	 * @var array
	 */
	private $_aIPList;

	/**
	 * Boolean Flag indicating whether to include disabled/expired cards; default is false
	 *
	 * @var boolean
	 */
	private $_bShowAllCards;
	/**
	 * Max Amount of cards for a user on the  Client
	 *
	 * @var integer
	 */
	private $_iMaxCards;
	/**
	 * Set of binary flags which specifies how customers may be identified
	 * 1. Only Customer Reference
	 * 2. Only Mobile Number
	 * 3. Identify using either Customer Reference or Mobile Number
	 * 4. Only E-Mail Address
	 * 5. Identify using either Customer Reference or E-Mail Address
	 * 6. Identify using either Mobile or E-Mail Address
	 * 7. Identify using either Customer Reference, Mobile Number or E-Mail Address
	 * 8. Both Mobile Number & E-Mail Address must match
	 *
	 * @var integer
	 */
	private $_iIdentification;	
	
	/**
	 * Transaction time to leave in seconds 
	 *
	 * @var integer
	 */
	private $_iTransactionTTL;
	/**
	 * The number of the last 4 masked digits, which should be returned for a Stored Card
	 *
	 * @var integer
	 */
	private $_iNumMaskedDigits;
	/**
	 * The salt value per client used to hash the all the incoming request for generated HMAC
	 *
	 * @var string
	 */
	private $_sSalt;
	/**
	 * The  key shared by thirdparty gateway for client
	 *
	 * @var string
	 */
	private $_sSecretKey;
	
	/**
	 * Object that holds the URL of Merchant App URL scheme should be returned to upon successfully completing the Transaction
	 *
	 * @var ClientURLConfig
	 */
	private $_obj_AppURL;

    /**
     * Object that holds the Base URL of Image or Icon to show card APM icons
     *
     * @var ClientURLConfig
     */
    private $_obj_BaseImageURL;
    /**
     * Object that holds the threed redirect transformation endpoint
     *
     * @var ClientURLConfig
     */
    private $_obj_ThreedRedirectURL;

    /**
     * Object that holds the Base Asset URL of client
     *
     * @var ClientURLConfig
     */
    private $_obj_BaseAssetURL;

    /*
     * Array that hold the Addotional Data in
     * @var array
     */
    private $_aAdditionalProperties=array();

    /**
     * Setting to enable installment option for merchant:
     * 	0. Disabled
     * 	1. Offline Installment - Merchant or Issue Financed
     *
     * @var integer
     */
    private $_iInstallment;

    /**
     * Max number of installments allowed.
     *
     * @var integer
     */
    private $_iMaxInstallments;

    /**
     * Installment frequency
     *
     * @var integer
     */
    private $_iInstallmentFrequency;


    /**
     * SurePay Configuration
     *
     * @var object
     */
    private $_objSurePayConfig;

    /**
     * Object that holds the transaction type configurations
     *
     * @var TransactionTypeConfig
     */
    private $_aObj_TransactionTypeConfigurations;


    /**
     *Object that hold the HPP URL
     *
     * @var ClientURLConfig
     */
    private $_obj_HPPURL;

	/**
	 * Default Constructor
	 *
	 * @param 	integer $id 								Unique ID for the Client in mPoint
	 * @param 	integer $fid 								ID of the Flow the Client's customers have to go through in order to complete the Payment Transaction
	 * @param 	string $name 								Client's name in mPoint
	 * @param 	AccountConfig $oAC 							Configuration for the Account the Transaction will be associated with
	 * @param 	string $un 									Client's Username for GoMobile
	 * @param 	string $pw 									Client's Password for GoMobile
	 * @param 	CountryConfig $oCC 							Configuration for the Country the Client can process transactions in
	 * @param 	KeywordConfig $oKC 							Configuration for the Keyword the Client uses to send messages through
	 * @param 	ClientURLConfig $oLURL 						Object that holds the URL to the Client's Logo which will be displayed on all payment pages
	 * @param 	ClientURLConfig $oCSSURL 					Object that holds the URL to the CSS file that should be used to customising the payment pages
	 * @param 	ClientURLConfig $oAccURL 					Object that holds the URL where the Customer should be returned to upon successfully completing the Transaction
	 * @param 	ClientURLConfig $oCURL 						Object that holds the URL where the Customer should be returned to in case he / she cancels the Transaction midway
	 * @param	ClientURLConfig $oDURL						Object that holds the URL where the Customer should be returned to when the transaction is declined
	 * @param 	ClientURLConfig $oCBURL 					Object that holds the URL to the Client's Back Office where mPoint should send the Payment Status to
	 * @param 	ClientURLConfig $oIURL 						Object that holds the  URL to the Client's My Account Icon
	 * @param 	ClientURLConfig $oParse3DSecureChallengeURL Object that holds the  URL to the Client's My Account Icon
	 * @param 	string $ma 									Max Amount an mPoint Transaction can cost the customer for the Client
	 * @param 	string $l 									The language that all payment pages should be rendered in by default for the Client
	 * @param 	boolean $sms 								Boolean Flag indicating whether mPoint should send out an SMS Receipt to the Customer upon successful completion of the Payment
	 * @param 	boolean $email								Boolean Flag indicating whether access to the E-Mail Receipt component should be enabled for customers
	 * @param 	string $mtd									The method used by mPoint when performing a Callback to the Client
	 * @param 	string $terms 								Terms & Conditions for the Shop
	 * @param 	integer $m 									Client mode: 0 = Production, 1 = Test Mode with prefilled card Info, 2 = Certification Mode
	 * @param 	boolean $ac									Boolean Flag indicating whether Auto Capture should be used for the transactions
	 * @param 	boolean $ecvv								Boolean Flag indicating whether enable CVV should be used for the transactions
	 * @param 	boolean $sp									Boolean Flag indicating whether the PSP's ID for the Payment should be included in the Callback
	 * @param	array $aIPs									List of Whitelisted IP addresses in mPoint, pass an empty array to disable IP Whitelisting
	 * @param 	boolean $dc									Boolean Flag indicating whether to include disabled/expired cards; default is false
	 * @param 	integer $mc									The max number of cards a user can have on the Client, set to -1 for inifite
	 * @param 	integer $ident								Set of binary flags which specifies how customers may be identified
	 * @param 	integer $txnttl								Transaction time to live value in seconds
	 * @param   integer $nmd								Number of the last 4 masked digits, which should be returned for a Stored Card
	 * @param   ClientURLConfig $oCIURL						Object that holds the customer import URL.
	 * @param   ClientURLConfig $oAURL						Object that holds the customer customer authetication URL
	 * @param   ClientURLConfig $oNURL						Object that holds the customer notification URL
	 * @param   ClientURLConfig $oMESBURL					Object that holds the MESB URL
	 * @param   array $aObj_ACs								List of Configurations for the Accounts the Transaction can be processed through
	 * @param   array $aObj_MAs								List of Merchant Accounts for each Payment Service Providers
	 * @param   array $aObj_PMs								List of Payment Methods (Cards) that the client offers
	 * @param   array $aObj_IINRs							List of IIN Range values for the client.
	 */
    public function __construct($id, $name, $fid, AccountConfig $oAC, $un, $pw, CountryConfig $oCC, KeywordConfig $oKC, ClientURLConfig $oLURL=NULL, ClientURLConfig $oCSSURL=NULL, ClientURLConfig $oAccURL=NULL, ClientURLConfig $oCURL=NULL, ClientURLConfig $oDURL=NULL, ClientURLConfig $oCBURL=NULL, ClientURLConfig $oIURL=NULL, ClientURLConfig $oParse3DSecureChallengeURL=NULL, $ma, $l, $sms, $email, $mtd, $terms, $m, $ecvv, $sp, $sc, $aIPs, $dc, $mc=-1, $ident=7, $txnttl, $nmd=4, $salt, ClientURLConfig $oCIURL=NULL, ClientURLConfig $oAURL=NULL, ClientURLConfig $oNURL=NULL, ClientURLConfig $oMESBURL=NULL, $aObj_ACs=array(), $aObj_MAs=array(), $aObj_PMs=array(), $aObj_IINRs = array(), $aObj_GMPs = array(), ClientCommunicationChannelsConfig $obj_CCConfig=NULL, ClientURLConfig $oAppURL=NULL,$aAdditionalProperties=array(),ClientURLConfig $oBaseImageURL=NULL,ClientURLConfig $oThreedRedirectURL=NULL,$secretkey=NULL, $installment=0, $maxInstallments=0, $installmentFrequency=0, $oBaseAssetURL=NULL, $obj_TransactionTypeConfig=NULL, $oHPPURL = null, ?ClientServiceStatus $clientServicesStatus)
	{
		parent::__construct($id, $name);

		$this->_iFlowID = (integer) $fid;

		$this->_obj_AccountConfig = $oAC;
		$this->_sUsername = trim($un);
		$this->_sPassword = trim($pw);
		$this->_obj_CountryConfig = $oCC;
		$this->_obj_KeywordConfig = $oKC;

		$this->_obj_LogoURL = $oLURL;
		$this->_obj_CSSURL = $oCSSURL;
		$this->_obj_AcceptURL = $oAccURL;
		$this->_obj_AppURL = $oAppURL;
		$this->_obj_CancelURL = $oCURL;
		$this->_obj_DeclineURL = $oDURL;
		$this->_obj_CallbackURL = $oCBURL;
		$this->_obj_IconURL = $oIURL;
		$this->_obj_Parse3DSecureChallengeURL = $oParse3DSecureChallengeURL;
		$this->_obj_BaseImageURL = $oBaseImageURL;
		$this->_obj_ThreedRedirectURL= $oThreedRedirectURL;
        $this->_obj_BaseAssetURL = $oBaseAssetURL;
		$this->_iMaxAmount = (integer) $ma;
		$this->_sLanguage = trim($l);

		$this->_bSMSReceipt = (bool) $sms;
		$this->_bEmailReceipt = (bool) $email;
		$this->_sMethod = $mtd;

		$this->_sTerms = trim($terms);
		$this->_iMode = (integer) $m;
		$this->_bAutoCapture = false;
		$this->_bEnableCVV = (bool) $ecvv;
		$this->_bSendPSPID = (bool) $sp;
		$this->_iStoreCard = (integer) $sc;

		$this->_obj_CustomerImportURL = $oCIURL;
		$this->_obj_AuthenticationURL = $oAURL;
		$this->_obj_NotificationURL = $oNURL;
		$this->_obj_MESBURL = $oMESBURL;
				
		$this->_aIPList = $aIPs;
		$this->_bShowAllCards = (bool) $dc;
		$this->_iMaxCards = (integer) $mc;
		$this->_iIdentification = (integer) $ident;
		$this->_iTransactionTTL = (integer) $txnttl;
		$this->_iNumMaskedDigits = (integer) $nmd;
		$this->_sSalt = trim($salt);
		if(!is_null($secretkey))
		$this->_sSecretKey= trim($secretkey);
		$this->_aObj_AccountsConfigurations = $aObj_ACs;
		$this->_aObj_MerchantAccounts = $aObj_MAs;
		$this->_aObj_PaymentMethodConfigurations = $aObj_PMs;
		$this->_aObj_IINRangeConfigurations = $aObj_IINRs;		
		$this->_aObj_GoMobileConfigurations = $aObj_GMPs;
		$this->_obj_CommunicationChannelsConfig = $obj_CCConfig;
		$this->_aAdditionalProperties=$aAdditionalProperties;
		$this->_iInstallment = (integer) $installment;
		$this->_iMaxInstallments = (integer) $maxInstallments;
		$this->_iInstallmentFrequency = (integer) $installmentFrequency;
        $this->_aObj_TransactionTypeConfigurations = $obj_TransactionTypeConfig;
        $this->_obj_HPPURL = $oHPPURL;
        $this->_aObj_ClientServicesStatus = $clientServicesStatus;
		
	}

	/**
	 * Returns the ID of the Flow the Client's customers have to go through in order to complete the Payment Transaction
	 *
	 * @return 	integer
	 */
	public function getFlowID() { return $this->_iFlowID; }
	/**
	 * Returns the Configuration for the Account the Transaction will be associated with
	 *
	 * @return 	AccountConfig
	 */
	public function getAccountConfig() { return $this->_obj_AccountConfig; }

    /**
     * Returns the array of Configurations for the Accounts the Transaction can be processed through
     *
     * @param \RDB|null $oDB
     *
     * @return    array
     */
	public function getAccountsConfigurations(RDB &$oDB = NULL) {
        if ($this->_aObj_AccountsConfigurations === NULL && $oDB !== NULL) {
            $this->_aObj_AccountsConfigurations = AccountConfig::produceConfigurations($oDB, $this->getID());
        }
        return $this->_aObj_AccountsConfigurations;
    }


    /**
     * Returns Object of Client Services
     *
     * @param \RDB|null $oDB
     *
     * @return    Object
     */
    public function getClientServices(RDB &$oDB = NULL): ClientServiceStatus {

        if ($this->_aObj_ClientServicesStatus === NULL && $oDB !== NULL) {
            $this->_aObj_ClientServicesStatus = ClientServiceStatus::produceConfig($oDB, $this->getID());
        }
        return $this->_aObj_ClientServicesStatus;
    }

    /**
     * Returns the array of Configurations for the Merchant Accounts that communicate with the PSPs
     *
     * @param \RDB|null $oDB
     *
     * @return    Array
     */
    public function getMerchantAccounts(RDB &$oDB = NULL)
    {
        if ($this->_aObj_MerchantAccounts === NULL && $oDB !== NULL) {

            if($this->getClientServices()->isLegacyFlow() === false) {
                $this->_aObj_MerchantAccounts = ClientMerchantAccountConfig::getConfigurations($oDB, $this->getID());
            }else{
                $this->_aObj_MerchantAccounts = ClientMerchantAccountConfig::produceConfigurations($oDB, $this->getID());
            }
        }
        return $this->_aObj_MerchantAccounts;
    }

    /**
     * Returns the array of Configurations for the Cards used by the client.
     *
     * @param \RDB|null $oDB
     *
     * @return    Array
     */
    public function getPaymentMethods(RDB &$oDB = NULL, $aWalletCardSchemes = array())
    {
        if ($this->_aObj_PaymentMethodConfigurations === NULL && $oDB !== NULL )
        {
            if($this->getClientServices()->isLegacyFlow() === false) {
                $this->_aObj_PaymentMethodConfigurations = ClientPaymentMethodConfig::getConfigurations($oDB, $aWalletCardSchemes);
            }else{
                $this->_aObj_PaymentMethodConfigurations = ClientPaymentMethodConfig::produceConfigurations($oDB, $this->getID());
            }
        }
        return $this->_aObj_PaymentMethodConfigurations;
    }
	/**
	 * Returns the Client's Username for GoMobile
	 *
	 * @return 	string
	 */
	public function getUsername() { return $this->_sUsername; }
	/**
	 * Returns the Client's Password for GoMobile
	 *
	 * @return 	string
	 */
	public function getPassword() { return $this->_sPassword; }
	/**
	 * Returns the Configuration for the Country the Client can process transactions in
	 *
	 * @return 	CountryConfig
	 */
	public function getCountryConfig() { return $this->_obj_CountryConfig; }
	/**
	 * Returns the Configuration for the Keyword the Client uses to send messages through
	 *
	 * @return 	KeywordConfig
	 */
	public function getKeywordConfig() { return $this->_obj_KeywordConfig; }
    /**
     * Returns the Configuration for the Communication Channels for the Client
     *
     * @return 	ClientCommunicationChannelsConfig
     */
    public function getCommunicationChannelsConfig(RDB $oDB) {
        if($this->_obj_CommunicationChannelsConfig === NULL)
        {
            $this->_obj_CommunicationChannelsConfig = ClientCommunicationChannelsConfig::produceConfig($oDB, $this->getID());
        }
        return $this->_obj_CommunicationChannelsConfig;
    }

	/**
	 * Returns the Absolute URL to the Client's Logo which will be displayed on all payment pages
	 *
	 * @return 	string
	 */
	public function getLogoURL()
	{
		if ( ($this->_obj_LogoURL instanceof ClientURLConfig) === true)
		{
			return $this->_obj_LogoURL->getURL();
		}
		else { return ""; }
	}
	/**
	 * Returns the Absolute URL to the CSS file that should be used to customising the payment pages
	 *
	 * @return 	string
	 */
	public function getCSSURL()
	{
		if ( ($this->_obj_CSSURL instanceof ClientURLConfig) === true)
		{
			return $this->_obj_CSSURL->getURL();
		}
		else { return ""; }
	}
	/**
	 * Returns the Absolute URL where the Customer should be returned to upon successfully completing the Transaction
	 *
	 * @return 	string
	 */
	public function getAcceptURL()
	{
		if ( ($this->_obj_AcceptURL instanceof ClientURLConfig) === true)
		{
			return $this->_obj_AcceptURL->getURL();
		}
		else { return ""; }
	}
	/**
	 * Returns the Merchant App URL scheme where the Customer should be returned to upon successfully completing the Transaction
	 *
	 * @return 	string
	 */
	public function getAppURL()
	{
		if ( ($this->_obj_AppURL instanceof ClientURLConfig) === true)
		{
			return $this->_obj_AppURL->getURL();
		}
		else { return ""; }
	}
	
	/**
	 * Returns the Absolute URL where the Customer should be returned to in case he / she cancels the Transaction midway
	 *
	 * @return 	string
	 */
	public function getCancelURL()
	{
		if ( ($this->_obj_CancelURL instanceof ClientURLConfig) === true)
		{
			return $this->_obj_CancelURL->getURL();
		}
		else { return ""; }
	}
	/**
	 * Returns the Absolute URL to the Client's Back Office where mPoint should send the Payment Status to
	 *
	 * @return 	string
	 */
	public function getCallbackURL()
	{
		if ( ($this->_obj_CallbackURL instanceof ClientURLConfig) === true)
		{
			return $this->_obj_CallbackURL->getURL();
		}
		else { return ""; }
	}
	/**
	 * Object that holds where the Customer should be returned to in case the PSP declines the Transaction
	 *
	 * @return 	string
	 */
	public function getDeclineURL()
	{
		if ( ($this->_obj_DeclineURL instanceof ClientURLConfig) === true)
		{
			return $this->_obj_DeclineURL->getURL();
		}
		else { return ""; }
	}
	/**
	 * Returns the Absolute URL to the Client's Icon for My Account
	 *
	 * @return 	string
	 */
	public function getIconURL() 
	{
		if ( ($this->_obj_IconURL instanceof ClientURLConfig) === true)
		{
			return $this->_obj_IconURL->getURL();
		}
		else { return ""; }
	}
	/**
	 * Returns the Absolute URL for parsing 3D secure challenge
	 *
	 * @return ClientURLConfig
	 */
	public function getParse3DSecureChallengeURLConfig() { return $this->_obj_Parse3DSecureChallengeURL; }
	/**
	 * Absolute URL to the external system where customer data may be imported from.
	 * This is generally an existing e-Commerce site or a CRM system.
	 *
	 * @return 	string
	 */
	public function getCustomerImportURL() 
	{ 
		if ( ($this->_obj_CustomerImportURL instanceof ClientURLConfig) === true)
		{
			return $this->_obj_CustomerImportURL->getURL(); 
		}
		else { return ""; }
	}
	/**
	 * Absolute URL to the external system where customer may be authenticated.
	 * This is generally an existing e-Commerce site or a CRM system.
	 *
	 * @return 	string
	 */
	public function getAuthenticationURL() 
	{ 
		if ( ($this->_obj_AuthenticationURL instanceof ClientURLConfig) === true)
		{
			return $this->_obj_AuthenticationURL->getURL();
		}
		else { return ""; }
	}
	/**
	 * Absolute URL to the external system that needs To by Notify When Stored Cards changes.
	 *
	 * @return 	string
	 */
	public function getNotificationURL() 
	{
		if ( ($this->_obj_NotificationURL instanceof ClientURLConfig) === true)
		{
			return $this->_obj_NotificationURL->getURL();
		}
		else { return ""; }
	}
	/**
	 * Absolute URL to the Mobile Enterprise Servicebus (MESB)
	 *
	 * @return 	string
	 */
	public function getMESBURL()
	{
		if ( ($this->_obj_MESBURL instanceof ClientURLConfig) === true)
		{
			return $this->_obj_MESBURL->getURL();
		}
		else  { return ""; }
	}

    /**
     * Returns the Base Image URL scheme
     *
     * @return 	string
     */
    public function getBaseImageURL()
    {
        if ( ($this->_obj_BaseImageURL instanceof ClientURLConfig) === true)
        {
            return $this->_obj_BaseImageURL->getURL();
        }
        else { return ""; }
    }
    /**
     * Returns the Base Image URL scheme
     *
     * @return 	string
     */
    public function getThreedRedirectURL()
    {
    	if ( ($this->_obj_ThreedRedirectURL instanceof ClientURLConfig) === true)
    	{
    		return $this->_obj_ThreedRedirectURL->getURL();
    	}
    	else { return ""; }
    }

    /**
     * Returns the Base Asset Image URL scheme
     *
     * @return 	string
     */
    public function getBaseAssetURL()
    {
        if ( ($this->_obj_BaseAssetURL instanceof ClientURLConfig) === true)
        {
            return $this->_obj_BaseAssetURL->getURL();
        }
        else { return ""; }
    }

	/**
	 * Returns the Max Amount an mPoint Transaction can cost the customer for the Client
	 *
	 * @return 	integer
	 */
	public function getMaxAmount() { return $this->_iMaxAmount; }
	/**
	 * Returns the language that all payment pages should be rendered in by default for the Client
	 *
	 * @return 	string
	 */
	public function getLanguage() { return $this->_sLanguage; }
	/**
	 * Returns the Boolean Flag that indicates whether mPoint should send out an SMS Receipt to the Customer by the Callback Module
	 * upon successful completion of the Payment
	 *
	 * @return 	boolean
	 */
	public function smsReceiptEnabled() { return $this->_bSMSReceipt; }
	/**
	 * Returns an Array of the ip-list for the client
	 *
	 * @return 	array
	 */
	public function getIPList() { return $this->_aIPList; }
	/**
	 * Boolean Flag indicating whether access to the E-Mail Receipt component should be enabled for customers
	 *
	 * @return 	boolean
	 */
	public function emailReceiptEnabled() { return $this->_bEmailReceipt; }
	/**
	 * Returns the method that mPoint uses to perform a Callback to the Client.
	 * This can be one of the following:
	 * 	- mPoint, Callback is perfomed using mPoint's native protocol
	 * 	- PSP, Callback is performed using the PSP's protocol by re-constructing the request received from the PSP
	 *
	 * @see Callback::notifyClient()
	 *
	 * @return 	string
	 */
	public function getMethod() { return $this->_sMethod; }
	/**
	 * Returns the Terms & Conditions for the Shop
	 *
	 * @return 	string
	 */
	public function getTerms() { return $this->_sTerms; }
	/**
	 * Returns Salt value for generating the HMAC for all incoming request
	 *
	 * @return 	string
	 */
	public function getSalt() { return $this->_sSalt; }
	/**
	 * Returns Secret Key value for generating the HMAC for outgoing request
	 *
	 * @return 	string
	 */
	public function getSecretKey() { return $this->_sSecretKey; }
	/**
	 * Returns the Client Mode in which all Transactions are Processed
	 * 	0. Production
	 * 	1. Test Mode with prefilled card Info
	 * 	2. Certification Mode
	 *
	 * @return 	integer
	 */
	public function getMode() { return $this->_iMode; }
	/**
	 * Boolean Flag indicating whether mPoint should enable CVV for the Client.
	 *
	 * @return 	boolean
	 */
	public function getCVVenabled() { return $this->_bEnableCVV; }
	/**
	 * Boolean Flag indicating whether mPoint should use Auto Capture for the Client.
	 *
	 * @return 	boolean
	 */
	public function useAutoCapture() { return $this->_bAutoCapture; }
	/**
	 * Boolean Flag indicating whether mPoint should include the PSP's ID for the Payment in the Callback to the Client.
	 *
	 * @return 	boolean
	 */
	public function sendPSPID() { return $this->_bSendPSPID; }
	/**
	 * Returns the max amount of cards a enduser can have on a Client of this value is not set for the client -1 will be returned
	 * 	-1. Allow an infinite number of cards to be stored
	 * 	1+. The max number of cards a user may have stored
	 *
	 * @return 	integer
	 */
	public function getMaxCards() { return $this->_iMaxCards; }
	/**
	 * Set of binary flags which specifies how customers may be identified
	 * 1. Only Customer Reference
	 * 2. Only Mobile Number
	 * 3. Identify using either Customer Reference or Mobile Number
	 * 4. Only E-Mail Address
	 * 5. Identify using either Customer Reference or E-Mail Address
	 * 6. Identify using either Mobile or E-Mail Address
	 * 7. Identify using either Customer Reference, Mobile Number or E-Mail Address
	 * 8. Both Mobile Number & E-Mail Address must match
	 *
	 * @return 	integer
	 */
	public function getIdentification() { return $this->_iIdentification; }
	/**
	 * Returns the setting determining if / how the end-user's Card Info is stored:
	 * 	0. OFF - Cards are not stored
	 * 	1. INVALID!!!
	 * 	2. Stored cards are available only for the specific client
	 * 	3. Only use Stored Cards and only make the cards available for the specific client (e-money based prepaid account will be unavailable)
	 * 	4. Stored cards are globally available (mPoint must be hosted by a Master Merchant)
	 * 	5. Only use Stored Cards but make them globally available (mPoint must be hosted by a Master Merchant and e-money based prepaid account will be unavailable)
	 *
	 * @return 	integer
	 */
	public function getStoreCard() { return $this->_iStoreCard; }
	public function getSecret() { return sha1($this->getID() . $this->_sPassword); }

	public function showAllCards() { return $this->_bShowAllCards; }
	
	/**
	 * Returns the number of the last 4 masked digits, which should be returned for a Stored Card
	 * 
	 * @return 	integer
	 */
	public function getNumberOfMaskedDigits() { return $this->_iNumMaskedDigits; }


    /**
     * @return int
     */
    public function getInstallment()
    {
        return $this->_iInstallment;
    }

    /**
     * @return int
     */
    public function getMaxInstallments()
    {
        return $this->_iMaxInstallments;
    }

    /**
     * @return int
     */
    public function getInstallmentFrequency()
    {
        return $this->_iInstallmentFrequency;
    }

	/**
	 * Returns the XML payload of array of Configurations for the Cards used by the client.
	 *
	 * @return 	String
	 */
	private function _getPaymentMethodsAsXML(RDB &$oDB, $aWalletCardSchemes = array())
	{
		$xml = '<payment-methods store-card="'. $this->_iStoreCard .'" show-all-cards="'. General::bool2xml($this->_bShowAllCards) .'" max-stored-cards="'. $this->_iMaxCards .'">';
		foreach ($this->getPaymentMethods($oDB, $aWalletCardSchemes) as $obj_PM)
		{
			if ( ($obj_PM instanceof ClientPaymentMethodConfig) === true)
			{
				$xml .= $obj_PM->toXML();
			}
		}
		$xml .= '</payment-methods>';
			
		return $xml;
	}
	/**
	 * Returns the XML payload of array of Configurations for the Accounts the Transaction will be associated with
	 *
	 * @return 	String
	 */
	private function _getAccountsConfigurationsAsXML(RDB &$oDB)
	{
		$xml = '<account-configurations>';
		foreach ($this->getAccountsConfigurations($oDB) as $obj_AccountConfig)
		{
			if ( ($obj_AccountConfig instanceof AccountConfig) == true)
			{
				$xml .= $obj_AccountConfig->toFullXML();
			}
		}
		$xml .= '</account-configurations>';
			
		return $xml;
	}
	/**
	 * Returns the XML payload of array of Configurations for the Accounts the Transaction will be associated with.
	 *
	 * @return 	String
	 */
	private function _getMerchantAccountsConfigAsXML(RDB &$oDB)
	{
		$xml = '<payment-service-providers>';
		foreach ($this->getMerchantAccounts($oDB) as $obj_MA)
		{
			if ( ($obj_MA instanceof ClientMerchantAccountConfig) === true)
			{
				$xml .= $obj_MA->toXML();
			}
		}
		$xml .= '</payment-service-providers>';
			
		return $xml;
	}
	
	/**
	 * Returns the XML payload of array of Configurations for the Client's Issuer Identification Number ranges.
	 *
	 * @return 	String
	 */
	private function _getIINRangesConfigAsXML(RDB &$oDB = NULL)
	{
	    if($this->_aObj_IINRangeConfigurations === NULL && $oDB !== NULL)
        {
            $this->_aObj_IINRangeConfigurations = ClientIINRangeConfig::produceConfigurations($oDB, $this->getID());
        }
		$xml = '<issuer-identification-number-ranges>';
		foreach ($this->_aObj_IINRangeConfigurations as $obj_IINR)
		{
			if ( ($obj_IINR instanceof ClientIINRangeConfig) === true)
			{
				$xml .= $obj_IINR->toXML();
			}
		}
		$xml .= '</issuer-identification-number-ranges>';
			
		return $xml;
	}

	/**
	 * Returns the XML payload of array of Configurations for the Client's GoMobile Account.
	 *
	 * @return 	String
	 */
	private function _getGoMobileConfigAsXML()
	{
	    if(empty($this->_aAdditionalProperties)=== false)
        {
            $this->_aObj_GoMobileConfigurations = ClientGoMobileConfig::produceConfigurations($this->_aAdditionalProperties);
        }
		$xml = '<gomobile-configuration-params>';
        if(empty($this->_aObj_GoMobileConfigurations) === false)
        {
            foreach ($this->_aObj_GoMobileConfigurations as $obj_GMP)
            {
                if ( ($obj_GMP instanceof ClientGoMobileConfig) === true)
                {
                    $xml .= $obj_GMP->toXML();
                }
            }
        }
		$xml .= '</gomobile-configuration-params>';

		return $xml;
	}

    /**
     * Returns the XML payload of array of Configurations for the Transaction Type
     *
     * @return 	String
     */
	private function getTransactionTypeConfigXML(RDB $oDB)
    {
        if($this->_aObj_TransactionTypeConfigurations === NULL)
        {
            $this->_aObj_TransactionTypeConfigurations = TransactionTypeConfig::produceConfig();
        }
        $xml = '<transaction-types>';
        foreach ($this->_aObj_TransactionTypeConfigurations as $obj_TransactionType)
        {
            if ( ($obj_TransactionType instanceof TransactionTypeConfig) === true)
            {
                $xml .= $obj_TransactionType->toXML();
            }
        }
        $xml .= '</transaction-types>';
        return $xml;
    }
	
	/**
	 * Returns the transaction time to live in seconds.	 
	 *
	 * @return 	integer
	 */
	public function getTransactionTTL() { return $this->_iTransactionTTL; }

	public function toXML($propertyScope = 2)
	{
		$xml = '<client-config id="'. $this->getID() .'" flow-id="'. $this->_iFlowID .'" mode="'. $this->_iMode .'" max-cards="'. $this->_iMaxCards .'" identification="'. $this->_iIdentification .'" masked-digits="'. $this->_iNumMaskedDigits . 'enable-cvv=' . General::bool2xml($this->_bEnableCVV) .'">';
		$xml .= '<name>'. htmlspecialchars($this->getName(), ENT_NOQUOTES) .'</name>';
		$xml .= '<username>'. htmlspecialchars($this->getUsername(), ENT_NOQUOTES) .'</username>';
		$xml .= '<logo-url>'. htmlspecialchars($this->getLogoURL(), ENT_NOQUOTES) .'</logo-url>';
		$xml .= '<css-url>'. htmlspecialchars($this->getCSSURL(), ENT_NOQUOTES) .'</css-url>';
		$xml .= '<accept-url>'. htmlspecialchars($this->getAcceptURL(), ENT_NOQUOTES) .'</accept-url>';
		$xml .= '<app-url>'. htmlspecialchars($this->getAppURL(), ENT_NOQUOTES) .'</app-url>';
		$xml .= '<base-image-url>'. htmlspecialchars($this->getBaseImageURL(), ENT_NOQUOTES) .'</base-image-url>';
		$xml .= '<cancel-url>'. htmlspecialchars($this->getCancelURL(), ENT_NOQUOTES) .'</cancel-url>';
		$xml .= '<decline-url>'. htmlspecialchars($this->getDeclineURL(), ENT_NOQUOTES) .'</decline-url>';
		$xml .= '<callback-url>'. htmlspecialchars($this->getCallbackURL(), ENT_NOQUOTES) .'</callback-url>';
		$xml .= '<icon-url>'. htmlspecialchars($this->getIconURL(), ENT_NOQUOTES) .'</icon-url>';
		$xml .= '<customer-import-url>'. htmlspecialchars($this->getCustomerImportURL(), ENT_NOQUOTES) .'</customer-import-url>';
		$xml .= '<authentication-url>'. htmlspecialchars($this->getAuthenticationURL(), ENT_NOQUOTES) .'</authentication-url>';
		$xml .= '<notification-url>'. htmlspecialchars($this->getNotificationURL(), ENT_NOQUOTES) .'</notification-url>';
		$xml .= '<sms-receipt>'. General::bool2xml($this->_bSMSReceipt) .'</sms-receipt>';
		$xml .= '<email-receipt>'. General::bool2xml($this->_bEmailReceipt) .'</email-receipt>';
		$xml .= '<auto-capture>'. General::bool2xml($this->_bAutoCapture) .'</auto-capture>';
		$xml .= '<store-card>'. $this->_iStoreCard .'</store-card>';
		$xml .= '<salt>'. htmlspecialchars($this->_sSalt, ENT_NOQUOTES) .'</salt>';
		$xml .= '<secret-key>'. htmlspecialchars($this->_sSecretKey, ENT_NOQUOTES) .'</secret-key>';
		$xml .= '<ip-list>';
		foreach ($this->_aIPList as $value)
		{
			$xml .= '<ip>'.$value.'</ip>';
		}
		$xml .= '</ip-list>';
		$xml .= '<additional-config>';
        foreach ($this->getAdditionalProperties($propertyScope) as $aAdditionalProperty)
        {
            $xml .= '<property name="'.$aAdditionalProperty['key'].'">'.$aAdditionalProperty['value'].'</property>';
        }
        $xml .= '</additional-config>';
		$xml .= '<show-all-cards>'. $this->_bShowAllCards .'</show-all-cards>';
		$xml .= '</client-config>';

		return $xml;
	}
	
	public function toFullXML(RDB &$oDB,$propertyScope=2, $aWalletCardSchemes = array())
	{
		$xml = '<client-config id="'. $this->getID() .'" auto-capture = "'. General::bool2xml($this->_bAutoCapture) .'" enable-cvv = "'. General::bool2xml($this->_bEnableCVV) .'" country-id = "'.$this->getCountryConfig()->getID().'" language = "'.$this->_sLanguage.'" sms-receipt = "'.General::bool2xml($this->_bSMSReceipt).'" email-receipt = "'.General::bool2xml($this->_bEmailReceipt).'" mode="'. $this->_iMode .'" masked-digits="'. $this->_iNumMaskedDigits .'">';
		$xml .= '<name>'. htmlspecialchars($this->getName(), ENT_NOQUOTES) .'</name>';
		$xml .= '<username>'. htmlspecialchars($this->getUsername(), ENT_NOQUOTES) .'</username>';
		$xml .= '<password>'. htmlspecialchars($this->getPassword(), ENT_NOQUOTES) .'</password>';
		$xml .= '<max-amount country-id = "'.$this->getCountryConfig()->getID().'">'. htmlspecialchars($this->getMaxAmount(), ENT_NOQUOTES) .'</max-amount>';
		$xml .= '<urls>';
		if ( ($this->_obj_LogoURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_LogoURL->toXML(); }
		if ( ($this->_obj_CSSURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_CSSURL->toXML(); }
		if ( ($this->_obj_AcceptURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_AcceptURL->toXML(); }
		if ( ($this->_obj_AppURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_AppURL->toXML(); }
		if ( ($this->_obj_CancelURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_CancelURL->toXML(); }
		if ( ($this->_obj_DeclineURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_DeclineURL->toXML(); }
		if ( ($this->_obj_CallbackURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_CallbackURL->toXML(); }
		if ( ($this->_obj_IconURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_IconURL->toXML(); }
		if ( ($this->_obj_Parse3DSecureChallengeURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_Parse3DSecureChallengeURL->toXML(); }
		if ( ($this->_obj_CustomerImportURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_CustomerImportURL->toXML(); }
		if ( ($this->_obj_AuthenticationURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_AuthenticationURL->toXML(); }
		if ( ($this->_obj_NotificationURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_NotificationURL->toXML(); }
		if ( ($this->_obj_MESBURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_MESBURL->toXML(); }
        if ( ($this->_obj_BaseImageURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_BaseImageURL->toXML(); }
        if ( ($this->_obj_ThreedRedirectURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_ThreedRedirectURL->toXML(); }
        if ( ($this->_obj_HPPURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_HPPURL->toXML(); }
        $xml .= '</urls>';
		$xml .= '<keyword id = "'.$this->getKeywordConfig()->getID().'">'.$this->getKeywordConfig()->getName().'</keyword>';
		$xml .= $this->_getPaymentMethodsAsXML($oDB, $aWalletCardSchemes);
		$xml .= $this->_getMerchantAccountsConfigAsXML($oDB);
		$xml .= $this->_getAccountsConfigurationsAsXML($oDB);
		$xml .= $this->_getGoMobileConfigAsXML();
        $xml .= $this->_getCommunicationCannelConfigAsXML($oDB);
		$xml .= '<callback-protocol send-psp-id = "'.General::bool2xml($this->sendPSPID()).'">'. htmlspecialchars($this->_sMethod, ENT_NOQUOTES) .'</callback-protocol>';
		$xml .= '<identification>'. $this->_iIdentification .'</identification>';
		$xml .= '<transaction-time-to-live>'. $this->getTransactionTTL() .'</transaction-time-to-live>';
		$xml .= $this->_getIINRangesConfigAsXML($oDB);
		$xml .= '<salt>'. htmlspecialchars($this->_sSalt, ENT_NOQUOTES) .'</salt>';
		$xml .= '<secret-key>'. htmlspecialchars($this->_sSecretKey, ENT_NOQUOTES) .'</secret-key>';
        $xml .= '<additional-config>';
        foreach ($this->getAdditionalProperties($propertyScope) as $aAdditionalProperty)
        {
            $xml .= '<property name="'.$aAdditionalProperty['key'].'">'.$aAdditionalProperty['value'].'</property>';
        }
        $xml .= '</additional-config>';
        $xml .= '<decimal>'.$this->getCountryConfig()->getDecimals().'</decimal>';
        $xml .= $this->getTransactionTypeConfigXML($oDB);
		$xml .= '</client-config>';
		
		return $xml;
	}
    public function toAttributeLessXML() : string
    {
        $xml ='<client_configuration>';
        $xml .='<id>'.$this->getID().'</id>';
        $xml .='<name>'.$this->getName().'</name>';
        $xml .='<language>'.$this->getLanguage().'</language>';
        $xml .='<username>'.$this->getUsername().'</username>';
        $xml .='<salt>'.$this->getSalt().'</salt>';
        $xml .='<max_amount>'.$this->getMaxAmount().'</max_amount>';
        $xml .='<country_id>'.$this->getCountryConfig()->getID().'</country_id>';
        $xml .='<email_notification>'.General::bool2xml($this->emailReceiptEnabled()).'</email_notification>';
        $xml .='<sms_notification>'.General::bool2xml($this->smsReceiptEnabled()).'</sms_notification>';
        $xml .='<client_urls>';
        if ( ($this->_obj_Parse3DSecureChallengeURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_Parse3DSecureChallengeURL->toAttributeLessXML(); }
        if ( ($this->_obj_CustomerImportURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_CustomerImportURL->toAttributeLessXML(); }
        if ( ($this->_obj_AuthenticationURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_AuthenticationURL->toAttributeLessXML(); }
        if ( ($this->_obj_NotificationURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_NotificationURL->toAttributeLessXML(); }
        if ( ($this->_obj_MESBURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_MESBURL->toAttributeLessXML(); }
        if ( ($this->_obj_CallbackURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_CallbackURL->toAttributeLessXML(); }
        if ( ($this->_obj_CSSURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_CSSURL->toAttributeLessXML(); }
        if ( ($this->_obj_AcceptURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_AcceptURL->toAttributeLessXML(); }
        if ( ($this->_obj_CancelURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_CancelURL->toAttributeLessXML(); }
        if ( ($this->_obj_BaseImageURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_BaseImageURL->toAttributeLessXML(); }
        if ( ($this->_obj_LogoURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_LogoURL->toAttributeLessXML(); }
        if ( ($this->_obj_HPPURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_HPPURL->toAttributeLessXML(); }
        if ( ($this->_obj_ThreedRedirectURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_ThreedRedirectURL->toAttributeLessXML(); }
        if ( ($this->_obj_BaseAssetURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_BaseAssetURL->toAttributeLessXML(); }
        $xml .='</client_urls>';
        $xml .=$this->_aObj_ClientServicesStatus->toXML();
        $accountsConfigurations = $this->getAccountsConfigurations($oDB);
        $xml .= '<account_configurations>';
        if($accountsConfigurations != null)
        {
            foreach ($accountsConfigurations as $obj_AccountConfig)
            {
                if ( ($obj_AccountConfig instanceof AccountConfig) == true)
                {
                    $xml .= $obj_AccountConfig->toAttributeLessXML();
                }
            }
        }

        $xml .= '</account_configurations>';
        $xml .= '</client_configuration>';
        return $xml;
    }
	function toCompactXML(){
        $xml = '<client-config id="'. $this->getID() .'" auto-capture = "'. General::bool2xml($this->_bAutoCapture) .'" enable-cvv = "'. General::bool2xml($this->_bEnableCVV) .'" country-id = "'.$this->getCountryConfig()->getID().'" language = "'.$this->_sLanguage.'" sms-receipt = "'.General::bool2xml($this->_bSMSReceipt).'" email-receipt = "'.General::bool2xml($this->_bEmailReceipt).'" mode="'. $this->_iMode .'" masked-digits="'. $this->_iNumMaskedDigits .'">';
        $xml .= '<name>'. htmlspecialchars($this->getName(), ENT_NOQUOTES) .'</name>';
        $xml .= '<username>'. htmlspecialchars($this->getUsername(), ENT_NOQUOTES) .'</username>';
        $xml .= '<password>'. htmlspecialchars($this->getPassword(), ENT_NOQUOTES) .'</password>';
        $xml .= '<max-amount country-id = "'.$this->getCountryConfig()->getID().'">'. htmlspecialchars($this->getMaxAmount(), ENT_NOQUOTES) .'</max-amount>';
        $xml .= '<urls>';
        if ( ($this->_obj_LogoURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_LogoURL->toXML(); }
        if ( ($this->_obj_CSSURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_CSSURL->toXML(); }
        if ( ($this->_obj_AcceptURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_AcceptURL->toXML(); }
        if ( ($this->_obj_AppURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_AppURL->toXML(); }
        if ( ($this->_obj_CancelURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_CancelURL->toXML(); }
        if ( ($this->_obj_DeclineURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_DeclineURL->toXML(); }
        if ( ($this->_obj_CallbackURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_CallbackURL->toXML(); }
        if ( ($this->_obj_IconURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_IconURL->toXML(); }
        if ( ($this->_obj_Parse3DSecureChallengeURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_Parse3DSecureChallengeURL->toXML(); }
        if ( ($this->_obj_CustomerImportURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_CustomerImportURL->toXML(); }
        if ( ($this->_obj_AuthenticationURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_AuthenticationURL->toXML(); }
        if ( ($this->_obj_NotificationURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_NotificationURL->toXML(); }
        if ( ($this->_obj_MESBURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_MESBURL->toXML(); }
        if ( ($this->_obj_BaseImageURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_BaseImageURL->toXML(); }
        if ( ($this->_obj_HPPURL instanceof ClientURLConfig) === true) { $xml .= $this->_obj_HPPURL->toXML(); }
        $xml .= '</urls>';
        $embeddedHpp = $this->getAdditionalProperties(Constants::iInternalProperty,"isEmbeddedHpp");
        $isAutoRedirect = $this->getAdditionalProperties(Constants::iInternalProperty,"isAutoRedirect");
        if (empty($embeddedHpp) === false) {
            $xml .= '<embedded-hpp>' . $embeddedHpp . '</embedded-hpp>';
        }
        if (empty($isAutoRedirect) === false) {
            $xml .= '<auto-redirect>' . $isAutoRedirect . '</auto-redirect>';
        }
        $enableHppAuthentication = $this->getAdditionalProperties(Constants::iInternalProperty,"enableHppAuthentication");
        if (empty($enableHppAuthentication) === false) {
            $xml .= '<enable-hpp-authentication>' . $enableHppAuthentication . '</enable-hpp-authentication>';
        }
        $xml .= '<additional-config>';
        foreach ($this->getAdditionalProperties(Constants::iPublicProperty) as $aAdditionalProperty)
        {
            $xml .= '<property name="'.$aAdditionalProperty['key'].'">'.$aAdditionalProperty['value'].'</property>';
        }
        $xml .= '</additional-config>';
        $xml .= '</client-config>';

        return $xml;
    }

	/**
	 * Produces a new instance of a Client Configuration Object.
	 *
	 * @param 	RDB $oDB 		Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param 	integer $id 	Unique ID for the Client performing the request
	 * @param 	integer $acc 	Unique Account ID or Account Number that the transaction should be associated with, set to -1 to use the default account
	 * @param 	integer $kw 	Unique ID for the Keyword that all messages sent to the customer should belong to, defaults to -1 for client's default keyword.
	 * @return 	ClientConfig
	 */
	public static function produceConfig(RDB $oDB, $id, $acc=-1, $kw=-1)
	{
        if(array_key_exists($id.$acc,self::$instances) === false)
        {
            self::$instances[$id.$acc] = ClientConfig::_Get($oDB,$id,$acc,$kw);
        }
		return self::$instances[$id.$acc];
	}

    //To handle Unit test cases
    public static function tearDown()
    {
        self::$instances = [];
    }

    private static function _Get(RDB $oDB, $id, $acc=-1, $kw=-1)
    {
        $acc = (integer) $acc;
        $sql = "SELECT CL.id AS clientid, CL.name AS client, CL.flowid, CL.username, CL.passwd,
					CL.logourl, CL.cssurl, CL.accepturl, CL.cancelurl, CL.declineurl, CL.callbackurl, CL.iconurl,
					CL.smsrcpt, CL.emailrcpt, CL.method,
					CL.maxamount, CL.lang, CL.terms,
					CL.\"mode\", CL.enable_cvv, CL.send_pspid, CL.store_card, CL.show_all_cards, CL.max_cards,
					CL.identification, CL.transaction_ttl, CL.num_masked_digits, CL.salt,CL.secretkey,CL.communicationchannels AS channels, CL.installment, CL.max_installments, CL.installment_frequency,
					C.id AS countryid,
					Acc.id AS accountid, Acc.name AS account, Acc.mobile, Acc.markup, Acc.businesstype, 
					KW.id AS keywordid, KW.name AS keyword
				FROM Client". sSCHEMA_POSTFIX .".Client_Tbl CL
				INNER JOIN System". sSCHEMA_POSTFIX .".Country_Tbl C ON CL.countryid = C.id AND C.enabled = '1'
				INNER JOIN Client". sSCHEMA_POSTFIX .".Account_Tbl Acc ON CL.id = Acc.clientid AND Acc.enabled = '1'
				INNER JOIN Client". sSCHEMA_POSTFIX .".Keyword_Tbl KW ON CL.id = KW.clientid AND KW.enabled = '1'
                WHERE CL.id = ". intval($id) ." AND CL.enabled = '1'";
		// Use Default Keyword
		if ($kw == -1)
		{
			$sql .= " AND KW.standard = '1'";
		}
		// Use specific Keyword
		else { $sql .= " AND KW.id = ". intval($kw); }
		$sql .= "{ACCOUNT CLAUSE}
				GROUP BY CL.id, CL.name, CL.flowid, CL.username, CL.passwd,
					CL.logourl, CL.cssurl, CL.accepturl, CL.cancelurl, CL.declineurl, CL.callbackurl, CL.iconurl,
					CL.smsrcpt, CL.emailrcpt, CL.method,
					CL.maxamount, CL.lang, CL.terms,
					CL.\"mode\", CL.send_pspid, CL.store_card, CL.show_all_cards, CL.max_cards,
					CL.identification, CL.transaction_ttl,
					C.id,
					Acc.id, Acc.name, Acc.mobile, Acc.markup,
					KW.id, KW.name";
		// Use Default Account
		if ($acc == -1)
		{
			$sql .= "
					ORDER BY Acc.id ASC, KW.id ASC";
		}
		// Use Account Number (Not supported if running on Oracle)
		elseif ($acc < 1000)
		{
			$sql .= "
					ORDER BY Acc.id ASC, KW.id ASC
					LIMIT 1 OFFSET ". $acc;
		}
		// Use Account ID
		else
		{
			$sql = str_replace("{ACCOUNT CLAUSE}", " AND Acc.id = ". $acc, $sql);
			$sql .= "
					ORDER BY KW.id ASC";
		}
		// Remove Account clause if it hasn't been already
		$sql = str_replace("{ACCOUNT CLAUSE}", "", $sql);
	    //echo $sql ."\n";
		$RS = $oDB->getName($sql);

		if (is_array($RS) === true && $RS["CLIENTID"] > 0)
		{
			$obj_CountryConfig = CountryConfig::produceConfig($oDB, $RS["COUNTRYID"]);
			$obj_AccountConfig = new AccountConfig($RS["ACCOUNTID"], $RS["CLIENTID"], $RS["ACCOUNT"], $RS["MOBILE"], $RS["MARKUP"], array(),$RS["BUSINESSTYPE"]);
			$obj_KeywordConfig = new KeywordConfig($RS["KEYWORDID"], $RS["CLIENTID"], $RS["KEYWORD"], 0);
			$aObj_AccountsConfigurations = NULL;//AccountConfig::produceConfigurations($oDB, $id);
			$aObj_ClientMerchantAccountConfigurations = NULL;//ClientMerchantAccountConfig::produceConfigurations($oDB, $id);
			$aObj_ClientCardsAccountConfigurations = NULL;//ClientPaymentMethodConfig::produceConfigurations($oDB, $id);
			$aObj_ClientIINRangesConfigurations = NULL;//ClientIINRangeConfig::produceConfigurations($oDB, $id);
			$aObj_ClientGoMobileConfigurations = NULL;//ClientGoMobileConfig::produceConfigurations($oDB, $id);
			$obj_ClientCommunicationChannels = NULL;//ClientCommunicationChannelsConfig::produceConfig($oDB, $id);
            $obj_TransactionTypeConfig = NULL;//TransactionTypeConfig::produceConfig($oDB);

			$obj_LogoURL = NULL;
			$obj_CSSURL = NULL;
			$obj_AcceptURL = NULL;
			$obj_CancelURL = NULL;
			$obj_DeclineURL = NULL;
			$obj_CallbackURL = NULL;
			$obj_IconURL = NULL;
			$obj_CustomerImportURL = NULL;
			$obj_AuthenticationURL = NULL;
			$obj_NotificationURL = NULL;
			$obj_MESBURL = NULL;
			$obj_Parse3DSecureURL = NULL;
			$obj_AppURL = NULL;
			$obj_BaseImageURL = NULL;
			$obj_ThreedRedirectURL = NULL;
            $obj_BaseAssetURL = NULL;
            $obj_HPPURL = NULL;

            $sql  = "SELECT id,url, urltypeid
					 FROM Client". sSCHEMA_POSTFIX .".URL_Tbl
					 WHERE clientid = ". intval($id) ." AND enabled = true ORDER BY urltypeid ASC";

            $aRS = $oDB->getAllNames($sql);
           if (is_array($aRS) === true && count($aRS) > 0)
            {
                for ($i=0; $i<count($aRS); $i++)
                {
                   switch ($aRS[$i]["URLTYPEID"])
                   {
                       case self::iCUSTOMER_IMPORT_URL:
                           $obj_CustomerImportURL = new ClientURLConfig($aRS[$i]["ID"], self::iCUSTOMER_IMPORT_URL, $aRS[$i]["URL"],"","CLIENT");
                           break;
                       case self::iAUTHENTICATION_URL:
                           $obj_AuthenticationURL = new ClientURLConfig($aRS[$i]["ID"], self::iAUTHENTICATION_URL, $aRS[$i]["URL"],'Single Sign-On Authentication',"CLIENT");
                           break;
                       case self::iNOTIFICATION_URL:
                           $obj_NotificationURL = new ClientURLConfig($aRS[$i]["ID"], self::iNOTIFICATION_URL, $aRS[$i]["URL"],"","CLIENT");
                           break;
                       case self::iMESB_URL:
                           $obj_MESBURL = new ClientURLConfig($aRS[$i]["ID"], self::iMESB_URL, $aRS[$i]["URL"],'Mobile Enterprise Servicebus',"CLIENT");
                           break;
                       case self::iPARSE_3DSECURE_CHALLENGE_URL:
                           $obj_Parse3DSecureURL = new ClientURLConfig($aRS[$i]["ID"], self::iPARSE_3DSECURE_CHALLENGE_URL, $aRS[$i]["URL"],'Parse 3D Secure Challenge URL',"CLIENT");
                           break;
                       case self::iMERCHANT_APP_RETURN_URL:
                           $obj_AppURL = new ClientURLConfig($aRS[$i]["ID"], self::iMERCHANT_APP_RETURN_URL, $aRS[$i]["URL"],"","MERCHANT");
                           break;
                       case self::iBASE_IMAGE_URL :
                           $obj_BaseImageURL = new ClientURLConfig($aRS[$i]["ID"], self::iBASE_IMAGE_URL, $aRS[$i]["URL"],'Base URL for Images',"HPP");
                           break;
                       case self::iTHREED_REDIRECT_URL:
                           $obj_ThreedRedirectURL= new ClientURLConfig($aRS[$i]["ID"], self::iTHREED_REDIRECT_URL, $aRS[$i]["URL"],"","CLIENT");
                           break;
                       case self::iBASE_ASSET_URL:
                           $obj_BaseAssetURL= new ClientURLConfig($aRS[$i]["ID"], self::iBASE_ASSET_URL, $aRS[$i]["URL"],"","HPP");
                           break;
                       case self::iHPP_URL:
                           $obj_HPPURL= new ClientURLConfig($aRS[$i]["ID"], self::iHPP_URL, $aRS[$i]["URL"],"HPP", "HPP");
                           break;
                   }
                }
            }

			if (strlen($RS["LOGOURL"]) > 0) { $obj_LogoURL = new ClientURLConfig($RS["CLIENTID"], self::iLOGO_URL, $RS["LOGOURL"],'Logo URL',"HPP"); }
			if (strlen($RS["CSSURL"]) > 0) { $obj_CSSURL = new ClientURLConfig($RS["CLIENTID"], self::iCSS_URL, $RS["CSSURL"],'CSS URL',"HPP"); }
			if (strlen($RS["ACCEPTURL"]) > 0) { $obj_AcceptURL = new ClientURLConfig($RS["CLIENTID"], self::iACCEPT_URL, $RS["ACCEPTURL"],'Accept URL',"MERCHANT"); }
			if (strlen($RS["CANCELURL"]) > 0) { $obj_CancelURL = new ClientURLConfig($RS["CLIENTID"], self::iCANCEL_URL, $RS["CANCELURL"],'Cancel URL',"MERCHANT"); }
			if (strlen($RS["DECLINEURL"]) > 0) { $obj_DeclineURL = new ClientURLConfig($RS["CLIENTID"], self::iDECLINE_URL, $RS["DECLINEURL"],"","MERCHANT"); }
			if (strlen($RS["CALLBACKURL"]) > 0) { $obj_CallbackURL = new ClientURLConfig($RS["CLIENTID"], self::iCALLBACK_URL, $RS["CALLBACKURL"],'Callback URL',"MERCHANT"); }
			if (strlen($RS["ICONURL"]) > 0) { $obj_IconURL = new ClientURLConfig($RS["CLIENTID"], self::iICON_URL, $RS["ICONURL"],"","HPP"); }

            
			$sql  = "SELECT ipaddress
					 FROM Client". sSCHEMA_POSTFIX .".IPAddress_Tbl
					 WHERE clientid = ". intval($id) ."
					 AND enabled = true ";
	//		echo $sql ."\n";
			$aRS = $oDB->getAllNames($sql);
			$aIPs = array();
			if (is_array($aRS) === true && count($aRS) > 0)
			{
				for ($i=0; $i<count($aRS); $i++)
				{
					$aIPs[] = $aRS[$i]["IPADDRESS"];
				}
			}
            // Get Client Services
            $clientServicesStatus = ClientServiceStatus::produceConfig($oDB, $RS["CLIENTID"]);
            $sql  = "SELECT key, value, scope 
					 FROM Client". sSCHEMA_POSTFIX .".AdditionalProperty_tbl
					 WHERE externalid = ". intval($id) ." and type='client' and enabled=true";

            if($clientServicesStatus->isLegacyFlow() === false)
            {
                $sql  = "SELECT sp.name as key,cp.value,pc.scope from SYSTEM". sSCHEMA_POSTFIX .".client_property_tbl sp 
                  INNER JOIN CLIENT". sSCHEMA_POSTFIX .".client_property_tbl cp on cp.propertyid = sp.id  AND cp.enabled=true AND sp.enabled AND clientid =".$id." INNER JOIN SYSTEM". sSCHEMA_POSTFIX .".property_category_tbl pc on sp.category = pc.id ";
            }

            //		echo $sql ."\n";
            $aRS = $oDB->getAllNames($sql);
            $aAdditionalProperties = array();
            if (is_array($aRS) === true && count($aRS) > 0)
            {
                $iConstOfRows = count($aRS);
                for ($i=0; $i < $iConstOfRows; $i++)
                {
                	$aAdditionalProperties[$i]["key"] =$aRS[$i]["KEY"];
                	$aAdditionalProperties[$i]["value"] = $aRS[$i]["VALUE"];
                	$aAdditionalProperties[$i]["scope"] = $aRS[$i]["SCOPE"];
                }
            }

            /*Adding is_legacy flag for mesb side of backward compatibility
             Post all client migrated to CRS this flag can be removed and mesb side needs to be refactored*/
            if($clientServicesStatus->isLegacyFlow() === false)
            {
                $i = sizeof($aAdditionalProperties);
                $aAdditionalProperties[$i]["key"] ="IS_LEGACY";
                $aAdditionalProperties[$i]["value"] = "false";
                $aAdditionalProperties[$i]["scope"] = Constants::iPublicProperty;

                //TODO Cannot use ReadOnlyConfigRepo its required txninfo obj and refactoring it to taking client id in
                // repo will becomes recursion ex created repo obj here repo will again create clientinfo obj
                // Solution all addon config details need to injected from outside
                $sql = "SELECT version from client". sSCHEMA_POSTFIX .".mpi_property_tbl WHERE enabled=true and clientid=".$id;
                $aPropRS = $oDB->getName($sql);

                if (is_array($aPropRS) === true)
                {
                    $i++;
                    $aAdditionalProperties[$i]["key"] = "3DSVERSION";
                    $aAdditionalProperties[$i]["value"] = $aPropRS["VERSION"];
                    $aAdditionalProperties[$i]["scope"] = Constants::iPrivateProperty;
                }
            }

            return new ClientConfig($RS["CLIENTID"], $RS["CLIENT"], $RS["FLOWID"], $obj_AccountConfig, $RS["USERNAME"], $RS["PASSWD"], $obj_CountryConfig, $obj_KeywordConfig, $obj_LogoURL, $obj_CSSURL, $obj_AcceptURL, $obj_CancelURL, $obj_DeclineURL, $obj_CallbackURL, $obj_IconURL, $obj_Parse3DSecureURL, $RS["MAXAMOUNT"], $RS["LANG"], $RS["SMSRCPT"], $RS["EMAILRCPT"], $RS["METHOD"], utf8_decode($RS["TERMS"]), $RS["MODE"], $RS["ENABLE_CVV"], $RS["SEND_PSPID"], $RS["STORE_CARD"], $aIPs, $RS["SHOW_ALL_CARDS"], $RS["MAX_CARDS"], $RS["IDENTIFICATION"], $RS["TRANSACTION_TTL"], $RS["NUM_MASKED_DIGITS"], $RS["SALT"], $obj_CustomerImportURL, $obj_AuthenticationURL, $obj_NotificationURL, $obj_MESBURL, $aObj_AccountsConfigurations, $aObj_ClientMerchantAccountConfigurations, $aObj_ClientCardsAccountConfigurations, $aObj_ClientIINRangesConfigurations, $aObj_ClientGoMobileConfigurations, $obj_ClientCommunicationChannels, $obj_AppURL,$aAdditionalProperties,$obj_BaseImageURL,$obj_ThreedRedirectURL,$RS["SECRETKEY"],$RS["INSTALLMENT"], $RS["MAX_INSTALLMENTS"], $RS["INSTALLMENT_FREQUENCY"],$obj_BaseAssetURL, $obj_TransactionTypeConfig, $obj_HPPURL,$clientServicesStatus);
		}
		// Error: Client Configuration not found
		else { trigger_error("Client Configuration not found using ID: ". $id .", Account: ". $acc .", Keyword: ". $kw, E_USER_WARNING); }

		return NULL;
    }

	public static function authenticate($obj_DB, $clientID, $accountID, $username, $password, $ip='')
	{
		$obj_ClientConfig = self::produceConfig($obj_DB, $clientID, $accountID);
		if ( ($obj_ClientConfig instanceof ClientConfig) === true)
		{
			if ($obj_ClientConfig->getUsername() == $username && $obj_ClientConfig->getPassword() == $password)
			{
				if ($obj_ClientConfig->hasAccess($ip) === true) { return $obj_ClientConfig; }
				else { throw new mPointSecurityException(mPointSecurityException::FORBIDDEN); }
			}
			else { throw new mPointSecurityException(mPointSecurityException::INVALID_CREDENTIALS); }
		}

		return $obj_ClientConfig;
	}

	/**
	 * Function to check for the IP whitelisting
	 *
	 * @param string $ip	the IP address as a string
	 * @return boolean
	 */
	public function hasAccess($ip)
	{
		if (count($this->_aIPList) == 0) { return true; }
		else { return in_array($ip, $this->_aIPList); }
	}

	public function getClientGoMobileConfigurationToXML(RDB &$oDB)
    {
        $xml = '<client-config id="'. $this->getID() .'">';
        $xml .= $this->_getGoMobileConfigAsXML($oDB);
        $xml .= '</client-config>';
        return $xml;
    }

    /*
	 * Get Additional properties
	 * If key is send as parameter then value of that key will return
	 * Otherwise all properties will return
	 *
     * @param int scope
	 * @param string key
	 *
	 * return string or array
	 */
    public function getAdditionalProperties($scope, $key = '')
    {
        $isAll = false;
        $returnProperties = [];
        if ($key == '')
        {
            $isAll = true;
        }

        foreach ($this->_aAdditionalProperties as $additionalProperty)
        {
            if ($isAll || $additionalProperty['key'] === $key)
            {
                $propertyScope = (integer)$additionalProperty['scope'];
                if($propertyScope >= $scope)
                {
                    if($isAll === false)
                    {
                        return $additionalProperty['value'];
                    }
                    array_push($returnProperties,$additionalProperty);
                }
            }
        }

        if ($isAll)
        {
            return $returnProperties;
        }

        return false;
    }

    /**
     * @return object
     */
    public function getSurePayConfig(RDB $oDB)
    {
        if(isset($this->_objSurePayConfig) === FALSE)
        {
            $this->_objSurePayConfig = SurePayConfig::produceConfig( $oDB, $this->getID());
        }
        return $this->_objSurePayConfig;
    }

    private function _getCommunicationCannelConfigAsXML(RDB $oDB)
    {
        if($this->_obj_CommunicationChannelsConfig === NULL)
        {
            $this->_obj_CommunicationChannelsConfig = ClientCommunicationChannelsConfig::produceConfig($oDB, $this->getID());
        }
        if (($this->_obj_CommunicationChannelsConfig instanceof ClientCommunicationChannelsConfig) === TRUE) {
            return $this->_obj_CommunicationChannelsConfig->toXML();
        }
        return "";
    }

    /**
     * @return ClientURLConfig
     */
    public function getHPPURLObject()
    {
        return $this->_obj_HPPURL;
    }
}
?>