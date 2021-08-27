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
 * Data class for hold all data relevant for a Customer
 *
 */
use api\interfaces\XMLSerializable;

class CustomerInfo implements JsonSerializable, XMLSerializable
{
	/**
	 * Unique ID for the Customer
	 *
	 * @var integer
	 */
	private $_iID;
	/**
	 * GoMobile's ID for the Customer's Country
	 *
	 * @var integer
	 */
	private $_iCountryID;
	/**
	 * Customer's Mobile Number (MSISDN)
	 *
	 * @var long
	 */
	private $_lMobile;
	/**
	 * Customer's E-Mail Address
	 *
	 * @var string
	 */
	private $_sEMail;

	/**
	 * The Client's Reference for the Customer
	 *
	 * @var string
	 */
	private $_sCustomerRef;
	/**
	 * The customer's full name
	 *
	 * @var string
	 */
	private $_sFullName;
	/**
	 * The language that all payment pages should be rendered in by default for the Client
	 *
	 * @var string
	 */
	private $_sLanguage;

    /**
     * The profile id of registered or guest user associated with the transaction
     * @var string
     */
    private $_iProfileID;

    /**
     * Hold customer profile type id
     * @var integer
     */
    private $_iprofileTypeId;

    /**
     * @var string
     */
    private $_sDeviceId;

    /**
     * @var integer
     */
	private $_iOperator;
	/**
     * Hold user type
     * @var integer
     */
    private $_iUserType;

	/**
	 * Default constructor
	 * 
	 * @param integer $id		Unique ID for the Customer
	 * @param integer $cid		GoMobile's ID for the Customer's Country
	 * @param long $mob			Customer's Mobile Number (MSISDN)
	 * @param string $email		Customer's E-Mail Address
	 * @param string $cr		The Client's Reference for the Customer
	 * @param string $name		The customer's full name
	 * @param string $lang		The language that all payment pages should be rendered in by default for the Client
     * @param integer $profileid The profile id associated with the transaction, registered or guest user
	 */
	public function __construct($id, $cid, $mob, $email, $cr, $name, $lang, $profileid='')
	{
	    if($id>-1)
        {
            $this->_iID =  (integer) $id;
        }
		if(empty($cid) ===FALSE)
        {
            $this->_iCountryID = (integer) $cid;
        }

		if(empty($mob) ===FALSE) {
            $this->_lMobile = (float)$mob;
        }

		if(empty($email) ===FALSE) {
            $this->_sEMail = trim($email);
        }

		if(empty($cr) ===FALSE) {
            $this->_sCustomerRef = trim($cr);
        }

		if(empty($name) ===FALSE) {
            $this->_sFullName = trim($name);
        }

		if(empty($lang) ===FALSE) {
            $this->_sLanguage = trim($lang);
        }

		if(empty(trim($profileid)))
		{
			$this->_iProfileID = '';
		}
		else
        {
            $this->_iProfileID = trim($profileid);
        }
	}

	public function getID() { return $this->_iID; }
	public function getCountryID() { return $this->_iCountryID; }
	public function getMobile() { return $this->_lMobile; }
	public function getEMail() { return $this->_sEMail; }
	public function getCustomerRef() { return $this->_sCustomerRef; }
	public function getFullName() { return $this->_sFullName; }
	public function getLanguage() { return $this->_sLanguage; }
    public function getProfileID() { return $this->_iProfileID; }
    public function setProfileTypeID($profileTypeId) { $this->_iprofileTypeId = $profileTypeId; }
	public function getProfileTypeID() { return $this->_iprofileTypeId; }
	public function setUserType(int $userType): void { $this->_iUserType = $userType; }
    public function getUserType() { return $this->_iUserType; }

	public function toXML()
	{
		$xml  = '<customer';
		if ($this->_iID > 0) { $xml .= ' id="'. $this->_iID .'"'; }
		if (strlen($this->_sCustomerRef) > 0) { $xml .= ' customer-ref="'. htmlspecialchars($this->_sCustomerRef, ENT_NOQUOTES) .'"'; }
		if (empty($this->_iProfileID) === false ) { $xml .= ' profile-id="'.htmlspecialchars($this->_iProfileID, ENT_NOQUOTES) .'"' ;}
		$xml  .= '>';
		if (strlen($this->_sFullName) > 0) { $xml .= '<full-name>'. htmlspecialchars($this->_sFullName, ENT_NOQUOTES) .'</full-name>'; }
		if ($this->_lMobile > 0) { $xml .= '<mobile country-id="'. $this->_iCountryID .'">'. $this->_lMobile .'</mobile>'; }
		if (strlen($this->_sEMail) > 0) { $xml .= '<email>'. htmlspecialchars($this->_sEMail, ENT_NOQUOTES) .'</email>'; }
		$xml  .= '</customer>';

		return $xml;
	}
//	public static function produceInfo(SimpleXMLElement $obj_XML)
//	public static function produceInfo(RDB $obj_DB, $id)
	public static function produceInfo()
	{
		$aArgs = func_get_args();
		switch (count($aArgs) )
		{
		case (1):	// Instantiate from XML Element
			return self::_produceInfoFromXML($aArgs[0]);
			break;
		case (2):	// Instantiage from Database
			return self::_produceInfoFromDatabase($aArgs[0], $aArgs[1]);
			break;
		default:	// Error: Unknown number of arguments
			trigger_error("Unknown number of arguments: ". count($aArgs), E_USER_WARNING);
			break;
		}
	}
	private static function _produceInfoFromDatabase(RDB $obj_DB, $id)
	{
		$sql = "SELECT *
				FROM EndUser". sSCHEMA_POSTFIX .".Account_Tbl
				WHERE id = ". intval($id);
//		echo $sql ."\n";
		$RS = $obj_DB->getName($sql);
		
		if (is_array($RS) === true)
		{
			return new CustomerInfo($RS["ID"], $RS["COUNTRYID"], $RS["MOBILE"], $RS["EMAIL"], $RS["EXTERNALID"], trim($RS["FIRSTNAME"] ." ". $RS["LASTNAME"]), ""); 
		}
		else { return null; }
	}
	private static function _produceInfoFromXML(SimpleXMLElement $obj_XML)
	{ 
		return new CustomerInfo( (integer) @$obj_XML["id"],
								 (integer) @$obj_XML->mobile["country-id"],
								 (float) @$obj_XML->mobile,
								 @trim($obj_XML->email),
								 trim($obj_XML["customer-ref"]),
								 @trim($obj_XML->{'full-name'}),
								 @trim($obj_XML["language"]), 
								 @trim($obj_XML["profile-id"]) );
	}

    /**
     * @param long $lMobile
     */
    public function setMobile($lMobile)
    {
        if(empty($lMobile) === FALSE)
        {
        $this->_lMobile = $lMobile;
        }
    }

    /**
     * @param string $sEMail
     */
    public function setEMail($sEMail)
    {
        if (empty($sEMail) === FALSE) {
            $this->_sEMail = trim($sEMail);
        }
    }

    /**
     * @param string $sCustomerRef
     */
    public function setCustomerRef($sCustomerRef)
    {
        if (empty($sCustomerRef) === FALSE) {
            $this->_sCustomerRef = trim($sCustomerRef);
        }
    }

    /**
     * @param string $sLanguage
     */
    public function setLanguage($sLanguage)
    {
        if (empty($sLanguage) === FALSE) {
            $this->_sLanguage = trim($sLanguage);
        }
    }

    /**
     * @param string $sDeviceId
     */
    public function setDeviceId($sDeviceId)
    {
        if (empty($sDeviceId) === FALSE) {
            $this->_sDeviceId = $sDeviceId;
        }
    }

    /**
     * @param int $iOperator
     */
    public function setOperator($iOperator)
    {
        if (empty($iOperator) === FALSE) {
            $this->_iOperator = $iOperator;
        }
    }

    public function jsonSerialize()
    {
        $response = [
            'language' => $this->_sLanguage
        ];

        if(empty($this->_sEMail) === FALSE) {
            $response['email'] = $this->_sEMail;
        }

        if(empty($this->_iCountryID) === FALSE) {
            $response['country_id'] = $this->_iCountryID;
        }

        if(empty($this->_lMobile) === FALSE) {
            $response['mobile'] = $this->_lMobile;
        }

        if(empty($this->_iOperator) === FALSE) {
            $response['operator'] = $this->_iOperator;
        }

        if(empty($this->_sDeviceId) === FALSE) {
            $response['device_id'] = $this->_sDeviceId;
        }

        return $response;
    }

    /**
     * @return array
    */
    public function xmlSerialize()
    {
        $response = [
            'language' => $this->_sLanguage
        ];

        if(empty($this->_sEMail) === FALSE) {
            $response['email'] = $this->_sEMail;
        }

        if(empty($this->_iCountryID) === FALSE) {
            $response['country_id'] = $this->_iCountryID;
        }

        if(empty($this->_lMobile) === FALSE) {
            $response['mobile'] = $this->_lMobile;
        }

        if(empty($this->_iOperator) === FALSE) {
            $response['operator'] = $this->_iOperator;
        }

        if(empty($this->_sDeviceId) === FALSE) {
            $response['device_id'] = $this->_sDeviceId;
        }

        return $response;
    }


}
?>