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
 * @subpackage BasicConfig
 * @version 1.0
 */

/**
 * Data class for hold the basic data for all types of configurations
 *
 */
class ClientInfo
{
	/**
	 * The ID of the App that the Client Info is constructed for:
	 * 	1. mTicket - Public Transportation
	 * 	2. mTicket - Cinema
	 * 	3. mTicket - Public Transportation with mRewards
	 * 
	 * @var integer
	 */
	private $_iAppID;
	/**
	 * Client Platform
	 * 
	 * @var string
	 */
	private $_sPlatform;
	/**
	 * Client Version: v1.00, v2.00, v2.10 etc.
	 * 
	 * @var float
	 */
	private $_fVersion;
	/**
	 * Configuration for the Country the Customer is located in
	 *
	 * @var CountryConfig
	 */
	private $_obj_CountryConfig;
	/**
	 * The Customer's Mobile Number
	 * 
	 * @var string
	 */
	private $_sMobile;
	/**
	 * The Customer's E-Mail Address
	 * 
	 * @var string
	 */
	private $_sEMail;
	/**
	 * The unique Device ID (IMEI, UDID etc.) for the customer's phone.
	 * 
	 * @var string
	 */
	private $_sDeviceID;
	/**
	 * The IP Address the request is originating from
	 * 
	 * @var string
	 */
	private $_sIP;
	/**
	 * The language used by the Client
	 * 
	 * @var string
	 */
	private $_sLanguage;

    /**
     * The profile id of registered user
     * @var string
	 */
	private $_iProfileID;
	
	/**
	 * The SDKVersion of SDK
	 * @var integer
	 */
	private $_fSDKVersion = -1;
	
	/**
	 * The AppVersion of SDK
	 * @var integer
	 */
	private $_fAppVersion = -1;

    /**
     * Hold customer profile type id
     * @var integer
     */
    private $_iprofileTypeID;
	
	/**
     * The locale used by the Client
     * @var string
     */
    private $_sLocale;

    /**
     * Hold customer reference
     * @var string
     */
    private $_sCustomerRef;
	
//	sdk-version

	/**
	 * Default Constructor.
	 *
	 */
	public function __construct($appid, $pf, $ver, CountryConfig $oCC, $mob, $email, $dvc, $lang, $ip="", $profileid='', $sdkversion=0, $appversion=0, $profileTypeId=null, $locale=null, $customerRef=null)
	{
		$this->_iAppID = (integer) $appid;
		$this->_sPlatform = trim($pf);
		$this->_fVersion = (float) $ver;
		$this->_obj_CountryConfig = $oCC;
		$this->_sMobile = (float) $mob;
		$this->_sEMail = trim($email);
		$this->_sDeviceID = trim($dvc);
		$this->_sIP = trim($ip);
		$this->_sLanguage = trim($lang);
		if(empty(trim($profileid)))
		{
			$this->_iProfileID = '';
		}
		else
		{
			$this->_iProfileID = (string) $profileid;
		}
		$this->_fAppVersion = $appversion;
		$this->_fSDKVersion = $sdkversion;
		$this->_iprofileTypeID = $profileTypeId;
		$this->_sLocale = trim($locale);
		$this->_sCustomerRef = trim($customerRef);
	}
	/**
	 * Returns the ID of the App that the Client Info is constructed for:
	 * 	1. mTicket - Public Transportation
	 * 	2. mTicket - Cinema
	 * 	3. mTicket - Public Transportation with mRewards
	 *
	 * @return 	integer
	 */
	public function getAppID() { return $this->_iAppID; }
	/**
	 * Returns the Operation System: iOS, Android, Windows Phone 7 etc.
	 *
	 * @return 	string
	 */
	public function getPlatform() { return $this->_sPlatform; }
	/**
	 * Returns the Client Version: v1.00, v2.00, v2.10 etc.
	 *
	 * @return 	string
	 */
	public function getVersion() { return $this->_fVersion; }
	/**
	 * Returns the Configuration for the Country the Client Info is applicable for
	 *
	 * @return 	CountryConfig
	 */
	public function getCountryConfig() { return $this->_obj_CountryConfig; }
	/**
	 * Returns the Customer's Mobile Number
	 *
	 * @return 	float
	 */
	public function getMobile() { return $this->_sMobile; }
	/**
	 * Returns the Customer's E-Mail address
	 *
	 * @return 	string
	 */
	public function getEMail() { return $this->_sEMail; }
	/**
	 * Returns the unique Device ID (IMEI, UDID etc.) for the customer's phone.   
	 *
	 * @return 	string
	 */
	public function getDeviceID() { return $this->_sDeviceID; }
	/**
	 * Returns the IP Address the request is originating from
	 * 
	 * @var string
	 */
	public function getIP() { return $this->_sIP; }
	/**
	 * Returns the language used by the Client
	 * 
	 * @var string
	 */
	public function getLanguage() { return $this->_sLanguage; }

    /**
     * Returns the profile id of the registered user
     *
     * @return 	string|null
     */
    public function getProfileID()
    { return $this->_iProfileID; }
    
    /**
     * Returns the Client SDK Version: v1.00, v2.00, v2.10 etc.
     *
     * @return 	string
     */
    public function getSDKVersion() { return $this->_fSDKVersion; }
    /**
     * Returns the Client APP Version: v1.00, v2.00, v2.10 etc.
     *
     * @return 	string
     */
    public function getAPPVersion() { return $this->_fAppVersion; }
    /**
     * Returns the customer profile type ID
     *
     * @return 	integer
     */
	public function getProfileTypeID() { return $this->_iprofileTypeID; }
	/**
	 * Returns the locale used by the Client
	 * 
	 * @var string
	 */
	public function getLocale() { return $this->_sLocale; }
    /**
     * Returns the customer reference
     *
     * @var string
     */
    public function getCustomerRef() { return $this->_sCustomerRef; }
    

	public function toXML()
	{
		$xml = '<client-info app-id="'. $this->_iAppID .'" platform="'. htmlspecialchars($this->_sPlatform, ENT_NOQUOTES) .'" version="'. number_format($this->_fVersion, 2) .'" language="'. htmlspecialchars($this->_sLanguage, ENT_NOQUOTES).'"' ;
		if ($this->getProfileID() !== '') {
		    $xml .= ' profileid="'.$this->getProfileID().'"';
        }
        
        if(empty($this->_fSDKVersion) === false){
            $xml .= ' sdk-version="'.$this->getSDKVersion().'"';
        }
        
        if(empty($this->_fAppVersion) === false){
            $xml .= ' app-version="'.$this->getAPPVersion().'"';
        }
        
        if(empty($this->_sLocale) === false){
            $xml .= ' locale="'.$this->getLocale().'"';
        }
        
		$xml .= '>';
        $xml .= '<mobile country-id="'. $this->_obj_CountryConfig->getID() .'" country-code="'. $this->_obj_CountryConfig->getCountryCode() .'">'. $this->_sMobile .'</mobile>';
		$xml .= '<email>'. htmlspecialchars($this->_sEMail, ENT_NOQUOTES) .'</email>';
		$xml .= '<device-id>'. htmlspecialchars($this->_sDeviceID, ENT_NOQUOTES) .'</device-id>';
		$xml .= '<ip>'. htmlspecialchars($this->_sIP, ENT_NOQUOTES) .'</ip>';
		$xml .= '</client-info>';
		return $xml;
	}

    public function toAttributeLessXML()
    {
        $xml = '';
        $xml .= '<platform>'.htmlspecialchars($this->_sPlatform, ENT_NOQUOTES).'</platform>';
        $xml .= '<language>'.htmlspecialchars($this->_sLanguage, ENT_NOQUOTES).'</language>';
        $xml .= '<version>'.number_format($this->_fVersion, 2).'</version>';
        if(empty($this->_fSDKVersion) === false){
            $xml .= '<sdk-version>'.$this->_fSDKVersion.'</sdk-version>';
        }
        
        if(empty($this->_fAppVersion) === false){
            $xml .= '<app-version>'.$this->_fAppVersion.'</app-version>';
        }
        
        if(empty($this->_iAppID) == false)
        {
            $xml .= '<app_id>'.$this->_iAppID.'</app_id>';
		}
		
        if(empty($this->_sLocale) === false){
            $xml .= '<locale>'.$this->_iAppID.'</locale>';
        }
        if(empty($this->_sMobile) === false)
        {
            $xml .= '<mobile>';
            $xml .= '<mobile>'.$this->_sMobile.'</mobile>';
            $xml .= '<mobile_type>MobileEnriched</mobile_type>';
            $xml .= '<country_id>'.$this->_obj_CountryConfig->getID().'</country_id>';
            $xml .= '<validated>true</validated>';
            $xml .= '</mobile>';

        }
        if(empty($this->_sEMail) === false)
        {
            $xml .= '<email>';
            $xml .= '<email>'. htmlspecialchars($this->_sEMail, ENT_NOQUOTES) .'</email>';
            $xml .= '<email_type>EmailEnriched</email_type>';
            $xml .= '<validated>true</validated>';
            $xml .= '</email>';
        }
        if(empty($this->_sDeviceID) === false)
        {
            $xml .= '<device_id>'. htmlspecialchars($this->_sDeviceID, ENT_NOQUOTES) .'</device_id>';
        }
        if(empty($this->_sIP) === false){
            $xml .= '<ip>'. htmlspecialchars($this->_sIP, ENT_NOQUOTES) .'</ip>';
        }
        if(empty($this->_iprofileTypeID) === false){
            $xml .= '<customer_type>'. $this->getProfileTypeID() .'</customer_type>';
        }
        return $xml;
    }
	
	public static function produceInfo()
	{
		$aArgs = func_get_args();
		switch (count($aArgs) )
		{
		case (2):
			return self::_produceInfoFromDatabase($aArgs[0], $aArgs[1]);
			break;
		case (3):
			return self::_produceInfoFromXML($aArgs[0], $aArgs[1], $aArgs[2]);
			break;
        case (4):
            return self::_produceInfoFromXML($aArgs[0], $aArgs[1], $aArgs[2], $aArgs[3]);
            break;
		default:
			return null;
			break;
		}
	}
	/**
	 * Produces a new instance of a Customer Information Object.
	 *
	 * @param 	RDB $oDB 		Reference to the Database Object that holds the active connection to the mRetail Database
	 * @param 	integer $id 	Unique ID for the Transaction
	 * @return 	CustomerInfo
	 */
	private static function _produceInfoFromDatabase(RDB &$oDB, $id)
	{
		$sql = "SELECT T.countryid, T.platform, T.version, T.deviceid, P.mobile, P.email,
					L.code, T.clientid AS language
				FROM Log".sSCHEMA_POSTFIX.".Transaction_Tbl T
				INNER JOIN Customer".sSCHEMA_POSTFIX.".Profile_Tbl P ON T.customerid = P.id AND P.enabled = '1'
				INNER JOIN System".sSCHEMA_POSTFIX.".Language_Tbl L ON T.languageid = L.id AND L.enabled = '1'
				WHERE T.id = ". intval($id);
//		echo $sql ."\n";
		$RS = $oDB->getName($sql);
		
		if (is_array($RS) === true & count($RS) > 0)
		{
			$oCC = CountryConfig::produceConfig($oDB, $RS["COUNTRYID"], true);

			return new ClientInfo(-1, $RS["PLATFORM"], $RS["VERSION"], $oCC, $RS["MOBILE"], $RS["EMAIL"], $RS["DEVICEID"], $RS["LANGUAGE"], "");
		}
		else { return null; }
	}
	/**
	 * Produces a new instance of a Customer Information Object.
	 *
	 * @param 	SimpleXMLElement $oXML 	Reference to the XML document that the Client Information should be constructed from
	 * @param 	CountryConfig $oCC 		Reference to Country Configuration
	 * @param 	string $ip 				The IP address that the request originates from
     * @param 	string $profileTypeId 	Unique ID for customer profile type
	 * @return 	CustomerInfo
	 */
	private static function _produceInfoFromXML(SimpleXMLElement &$oXML, CountryConfig $oCC, $ip, $profileTypeId = null)
	{
		if (empty($oXML["language"]) === true) { $oXML["language"] = sLANG; }
		$httpXForwardedForIps = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $httpXForwardedForIps = array_map('trim', $httpXForwardedForIps);
        $httpXForwardedForIp = $httpXForwardedForIps[0];

        return new ClientInfo($oXML["app-id"], $oXML["platform"], $oXML["version"], $oCC, (float) $oXML->mobile, (string) $oXML->email, (string) $oXML->{'device-id'}, $oXML["language"], $httpXForwardedForIp, $oXML["profileid"], $oXML["sdk-version"], $oXML["app-version"], $profileTypeId, $oXML["locale"], (string) $oXML->{"customer-ref"});
	}
}
?>