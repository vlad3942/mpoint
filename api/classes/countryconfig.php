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
 * @subpackage CountryConfig
 * @version 1.0
 */

/**
 * Data class for hold the configuration for the Country a Transaction is processed in
 *
 */
class CountryConfig extends BasicConfig
{
	
	
	/**
	 * Configuration for the Currency the transactions was processed in
	 *
	 * @var CurrencyConfig
	 */
	private $_obj_CurrencyConfig;
	
	/**
	 * 3 digit ISO-4217 code for the currency used in the Country.
	 *
	 * @var string
	 */
	private $_sCurrency;
	/**
	 * Symbol used to represent the country's currency
	 *
	 * @var string
	 */
	private $_sSymbol;
	/**
	 * Max balance, in country's smallest currency, that a prepaid end-user account may contain in order to comply with the local regulations
	 *
	 * @var integer
	 */
	private $_iMaxBalance;
	/**
	 * Min amount which may be transferred between End-User Accounts in country's smallest currency
	 *
	 * @var integer
	 */
	private $_iMinTransfer;
	/**
	 * Min value a valid Mobile Number can have in the Country
	 *
	 * @var string
	 */
	private $_sMinMobile;
	/**
	 * Max value a valid Mobile Number can have in the Country
	 *
	 * @var string
	 */
	private $_sMaxMobile;
	/**
	 * GoMobile channel used for communicating with the customers in the Country
	 *
	 * @var string
	 */
	private $_sChannel;
	/**
	 * Price Format used in the Country.
	 * $X.XX for USA, X.XXkr for Denmark etc.
	 *
	 * @var string
	 */
	private $_sPriceFormat;
	/**
	 * Number of Decimals used for Prices in the Country:
	 * 2 for USA, 0 for Denmark etc.
	 *
	 * @var integer
	 */
	private $_iNumDecimals;
	/**
	 * Boolean Flag indicating whether an Address Lookup Service is available in the Country
	 *
	 * @var boolean
	 */
	private $_bAddressLookup;
	/**
	 * Boolean Flag indicating whether an Operators in the Country required Double Opt-In for payments made via Premium SMS
	 *
	 * @var boolean
	 */
	private $_bDoubleOptIn;
	
	/**
	 * The amount charged to the end-user when storing the card details for a new card as part of the end-user's account.
	 *
	 * @var integer
	 */
	private $_iAddCardAmount;
	/**
	 * The max amount, in country's smallest currency, that can be charged with a Premium SMS based flow (no authentication)
	 *
	 * @var integer
	 */
	private $_iMaxPSMSAmount;
	/**
	 * The min amount, in country's smallest currency, for a transaction before password authentication is required
	 *
	 * @var integer
	 */
	private $_iMinPwdAmount;
	/**
	 * The min amount, in country's smallest currency, for a transaction before 2-Factor Authentication is required
	 *
	 * @var integer
	 */
	private $_iMin2FAAmount;
	/**
	 * The 3 Digit alphabetic code as per the ISO 3166 standards
	 *
	 * @var string
	 */
	private $_sAlpha3Code;
    /**
     * The 2 Digit alphabetic code as per the ISO 3166 standards
     *
     * @var string
     */
    private $_sAlpha2Code;
    /**
     * The 3 Digit numeric code as per the ISO 3166 standards
     *
     * @var integer
     */
    private $_iNumericCode;

	/**
	 * Default Constructor
	 *
	 * @param 	integer $id 		Unique ID for the Country, this MUST match the GoMobile's ID for the Country
	 * @param 	string $name 		mPoint's Name for the Country
	 * @param 	string $currency 	3 digit ISO-4217 code for the currency used in the Country.
	 * @param 	string $sym 		Symbol used to represent the country's currency
	 * @param 	integer $maxbal 	Max balance, in country's smallest currency, that a prepaid end-user account may contain 
	 * @param 	integer $mt 		Min amount which may be transferred between End-User Accounts in country's smallest currency
	 * @param 	string $minmob 		Min value a valid Mobile Number can have in the Country
	 * @param 	string $maxmob 		Max value a valid Mobile Number can have in the Country
	 * @param 	string $ch 			GoMobile channel used for communicating with the customers in the Country
	 * @param 	string $pf 			Price Format used in the Country
	 * @param 	integer $dec 		Number of Decimals used for Prices in the Country
	 * @param 	boolean $als 		Boolean Flag indicating whether an Address Lookup Service is available in the Country
	 * @param 	boolean $doi 		Boolean Flag indicating whether an Operators in the Country required Double Opt-In for payments made via Premium SMS
	 * @param	integer $aca		The amount charged to the end-user when storing the card details for a new card as part of the end-user's account.
	 * @param	integer $mpsms		The max amount that can be charged with a Premium SMS based flow (no authentication) 
	 * @param	integer $mpwd		The min amount for a transaction before Password authentication is required
	 * @param	integer $m2fa		The min amount for a transaction before 2-Factor Authentication is required
	 * @param	string $a2code		The 2 Digit alphabetic code as per the ISO 3166 standards
	 * @param	string $a3code		The 3 Digit alphabetic code as per the ISO 3166 standards
	 * @param	integer $a3code		The 3 Digit numeric code as per the ISO 3166 standards
	 */
	public function __construct($id, $name, $currency, CurrencyConfig &$oCC, $sym, $maxbal, $mt, $minmob, $maxmob, $ch, $pf, $dec, $als, $doi, $aca, $mpsms, $mpwd, $m2fa, $a2code, $a3code, $numcode)
	{
		parent::__construct($id, $name);
		
		$this->_obj_CurrencyConfig = $oCC;
		$this->_sCurrency = trim($currency);
		$this->_sSymbol = trim($sym);
		$this->_iMaxBalance = (integer) $maxbal;
		$this->_iMinTransfer = (integer) $mt;
		$this->_sMinMobile = trim($minmob);
		$this->_sMaxMobile = trim($maxmob);
		$this->_sChannel = trim($ch);
		$this->_sPriceFormat = trim($pf);
		$this->_iNumDecimals = (integer) $dec;
		$this->_bAddressLookup = $als;
		$this->_bDoubleOptIn = $doi;
		$this->_iAddCardAmount = (integer) $aca;
		$this->_iMaxPSMSAmount = (integer) $mpsms;
		$this->_iMinPwdAmount = (integer) $mpwd;
		$this->_iMin2FAAmount = (integer) $m2fa; 
		$this->_sAlpha2Code = trim($a2code);
		$this->_sAlpha3Code = trim($a3code);
		$this->_iNumericCode = (integer) $numcode;
	}
	
	/**
	 * Returns the Configuration for the Currency the transactions was processed in
	 *
	 * @return 	CurrencyConfig
	 */
	public function getCurrencyConfig() { return $this->_obj_CurrencyConfig; }
	
	/**
	 * Returns the 3 digit ISO-4217 code for the currency used in the Country.
	 *
	 * @return 	string
	 */
	public function getCurrency() { return $this->_obj_CurrencyConfig->getCode(); }
	/**
	 * Returns the Symbol used to represent the country's currency
	 *
	 * @return 	string
	 */
	public function getSymbol() { return $this->_sSymbol; }
	/**
	 * Returns the Max balance, in the country's smallest currency, that a prepaid end-user account may contain in order to comply with the local regulations
	 *
	 * @return 	integer
	 */
	public function getMaxBalance() { return $this->_iMaxBalance; }
	/**
	 * Returns the Minimum amount which may be transferred between End-User Accounts in country's smallest currency
	 *
	 * @return 	integer
	 */
	public function getMinTransfer() { return $this->_iMinTransfer; }
	/**
	 * Returns the Min value a valid Mobile Number can have in the Country
	 *
	 * @return 	string
	 */
	public function getMinMobile() { return $this->_sMinMobile; }
	/**
	 * Returns the Max value a valid Mobile Number can have in the Country
	 *
	 * @return 	string
	 */
	public function getMaxMobile() { return $this->_sMaxMobile; }
	/**
	 * Returns the GoMobile channel used for communicating with the customers in the Country
	 *
	 * @return 	string
	 */
	public function getChannel() { return $this->_sChannel; }
	/**
	 * Returns the Price Format used in the Country.
	 * $X.XX for USA, X.XXkr for Denmark etc.
	 *
	 * @return 	string
	 */
	public function getPriceFormat() { return $this->_sPriceFormat; }
	/**
	 * Returns the Number of Decimals used for Prices in the Country:
	 * 2 for USA, 0 for Denmark etc.
	 *
	 * @return integer
	 */
	public function getDecimals() { return $this->_iNumDecimals; }
	/**
	 * Returns True if an Address Lookup Service is available in the Country otherwise false.
	 *
	 * @return boolean
	 */
	public function hasAddressLookup() { return $this->_bAddressLookup; }
	/**
	 * Returns True if the Mobile Network Operators in the Country requires Double Opt-In for payments made via Premiums SMS.
	 *
	 * @return boolean
	 */
	public function hasDoubleOptIn() { return $this->_bDoubleOptIn; }
	
	/**
	 * Returns the amount charged to the end-user when storing the card details for a new card as part of the end-user's account.
	 *
	 * @return integer
	 */
	public function getAddCardAmount() { return $this->_iAddCardAmount; }
	/**
	 * Returns the max amount, in country's smallest currency, that can be charged with a Premium SMS based flow (no authentication)
	 *
	 * @return integer
	 */
	public function getMaxPSMSAmount() { return $this->_iMaxPSMSAmount; }
	/**
	 * Returns the min amount, in country's smallest currency, for a transaction before password authentication is required
	 *
	 * @return integer
	 */
	public function getMinPwdAmount() { return $this->_iMinPwdAmount; }
	/**
	 * Returns the min amount, in country's smallest currency, for a transaction before 2-Factor Authentication is required
	 *
	 * @return integer
	 */
	public function getMin2FAAmount() { return $this->_iMin2FAAmount; }

	public function getAlpha2code() { return $this->_sAlpha2Code; }
	public function getAlpha3code() { return $this->_sAlpha3Code; }
	public function getNumericCode() { return $this->_iNumericCode; }

	/**
	 * Constructs an XML Document with the Country Configuration.
	 * The configuration is returned as an XML Document in the following format:
	 * 	<country-config id="{UNIQUE ID FOR THE COUNTRY}">
	 *		<name>{NAME OF THE COUNTRY}</name>
	 *		<currency symbol="{SYMBOL USED TO REPRESENT THE CURRENCY}">{ISO-4217 CURRENCY CODE USED IN THE COUNTRY}</currency>
	 *		<max-balance>{MAX BALANCE, IN COUNTRY'S SMALLEST CURRENCY, THAT A PREPAID ACCOUNT MAY CONTAIN}</max-balance>
	 *		<min-transfer>{MIN AMOUNT WHICH MAY BE TRANSFERRED BETWEEN ACCOUNTS IN COUNTRY'S SMALLEST CURRENCY}</min-transfer>
	 *		<min-mobile>{MIN VALUE FOR A VALID MOBILE NUMBER (MSISDN) IN THE COUNTRY}</min-mobile>
	 *		<max-mobile>{MAX VALUE FOR A VALID MOBILE NUMBER (MSISDN) IN THE COUNTRY}</max-mobile>
	 *		<channel>{CHANNEL USED FOR SENDING MESSAGE'S TO AN END-USER'S MOBILE PHONE}</channel>
	 *		<price-format>{PRICE FORMAT USED IN THE COUNTRY, i.e. $XX.XX for USA and XX,XXkr FOR DENMARK}</price-format>
	 *		<decimals>{NUMBER OF DECIMALS USED WHEN DISPLAYING PRICES}</decimals>
	 *		<address-lookup>{BOOLEAN FLAG INDICATING WHETHER ADDRESS LOOKUP BASED ON A MOBILE NUMBER IS AVAILABLE IN THE COUNTRY}</address-lookup>
	 *		<double-opt-in>{BOOLEAN FLAG INDICATING WHETHER THE MOBILE NETWORK OPERATOR'S IN THE COUNTRY REQUIRE DOUBLE OPT-IN WHEN CHARGING VIA PREMIUM SMS}</double-opt-in>
	 *		<add-card-amount>{AMOUNT THAT THE END-USER'S ACCOUNT IS TOPPED UP WITH WHEN CARD DETAILS FOR A NEW CARD IS STORED}</add-card-amount>
	 *		<max-psms-amount>{MAX AMOUNT THAT CAN BE CHARGED WITH A PREMIUM SMS BASED FLOW (NO AUTHENTICATION)}</max-psms-amount>
	 *		<min-pwd-amount>{MIN AMOUNT BEFORE A TRANSACTION REQUIRES PASSWORD AUTHENTICATION}</min-pwd-amount>
	 *		<min-2fa-amount>{MIN AMOUNT BEFORE A TRANSACTION REQUIRES 2-FACTOR AUTHENTICATION}</min-2fa-amount>
	 * 	</country-configs>
	 *
	 * @return 	xml
	 */
	public function toXML()
	{
		$xml = '<country-config id="'. $this->getID() .'">';
		$xml .= '<name>'. htmlspecialchars($this->getName(), ENT_NOQUOTES) .'</name>';
		$xml .= '<currency symbol="'. $this->_sSymbol .'">'. $this->_sCurrency .'</currency>';
		$xml .= '<max-balance>'. $this->_iMaxBalance .'</max-balance>';
		$xml .= '<min-transfer>'. $this->_iMinTransfer .'</min-transfer>';
		$xml .= '<min-mobile>'. $this->_sMaxMobile .'</min-mobile>';
		$xml .= '<max-mobile>'. $this->_sMinMobile .'</max-mobile>';
		$xml .= '<channel>'. $this->_sChannel .'</channel>';
		$xml .= '<price-format>'. $this->_sPriceFormat .'</price-format>';
		$xml .= '<num-decimals>'. $this->_iNumDecimals .'</num-decimals>';
		$xml .= '<address-lookup>'. General::bool2xml($this->_bAddressLookup) .'</address-lookup>';
		$xml .= '<double-opt-in>'. General::bool2xml($this->_bDoubleOptIn) .'</double-opt-in>';
		$xml .= '<add-card-amount>'. $this->_iAddCardAmount .'</add-card-amount>';
		$xml .= '<max-psms-amount>'. $this->_iMaxPSMSAmount .'</max-psms-amount>';
		$xml .= '<min-pwd-amount>'. $this->_iMinPwdAmount .'</min-pwd-amount>';
		$xml .= '<min-2fa-amount>'. $this->_iMin2FAAmount .'</min-2fa-amount>';
		$xml .= '<alpha2code>'. $this->_sAlpha2Code .'</alpha2code>';
		$xml .= '<alpha3code>'. $this->_sAlpha3Code .'</alpha3code>';
		$xml .= '<numeric-code>'. $this->_iNumericCode .'</numeric-code>';
		$xml .= '</country-config>';
		
		return $xml;
	}
	
	/**
	 * Produces a new instance of a Country Configuration Object.
	 *
	 * @param 	RDB $oDB 		Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param 	integer $id 	Unique ID for the Country the request is performed in
	 * @return 	CountryConfig
	 */
	public static function produceConfig(RDB &$oDB, $id)
	{
		$sql = "SELECT CT.id, CT.name, CUT.code AS currency, CT.symbol, CT.maxbalance, CT.mintransfer, CT.minmob, CT.maxmob, 
                CT.channel, CT.priceformat, CUT.decimals,
					CT.addr_lookup, CT.doi, CT.add_card_amount, CT.max_psms_amount, CT.min_pwd_amount, CT.min_2fa_amount, 
					CT.alpha2code, CT.alpha3code, CT.code,CUT.id AS currencyid
				FROM System".sSCHEMA_POSTFIX.".Country_Tbl CT
				INNER JOIN System".sSCHEMA_POSTFIX.".Currency_Tbl CUT ON CT.currencyid = CUT.id
				WHERE CT.id = ". intval($id) ." AND CT.enabled = '1' AND CUT.enabled = '1'";
//		echo $sql ."\n";
		$RS = $oDB->getName($sql);
		
		$obj_CurrencyConfig = CurrencyConfig::produceConfig($oDB, $RS["CURRENCYID"]);
		
		return new CountryConfig($RS["ID"], $RS["NAME"],$RS["CURRENCY"], $obj_CurrencyConfig, $RS["SYMBOL"], $RS["MAXBALANCE"], $RS["MINTRANSFER"], $RS["MINMOB"], $RS["MAXMOB"], $RS["CHANNEL"], $RS["PRICEFORMAT"], $RS["DECIMALS"], $RS["ADDR_LOOKUP"], $RS["DOI"], $RS["ADD_CARD_AMOUNT"], $RS["MAX_PSMS_AMOUNT"], $RS["MIN_PWD_AMOUNT"], $RS["MIN_2FA_AMOUNT"], $RS['ALPHA2CODE'],$RS['ALPHA3CODE'],$RS['CODE']);
	}
	
	/**
	 * Updates the country configuration to the Country_Tbl
	 * 
     * @param 	int    $id          unique identifier of the Country
	 * @param 	string $name 		mPoint's Name for the Country
	 * @param 	string $currency 	3 digit ISO-4217 code for the currency used in the Country.
	 * @param 	string $sym 		Symbol used to represent the country's currency
	 * @param 	string $pf 			Price Format used in the Country
     * @param   bool   $al          address lookup
     * @param 	string $minmob 		Min value a valid Mobile Number can have in the Country
	 * @param 	string $maxmob 		Max value a valid Mobile Number can have in the Country
     * 
     * @return bool Boolean A boolean value indicates if the operation was successful or not
	 */
	public static function updateConfig(RDB &$oDB, $id, $name, $currencyid, $sym, $pf, $al, $minmob, $maxmob)
	{
        if ($al === TRUE) { $addr_lookup = 'TRUE'; }
        else { $addr_lookup = 'FALSE'; }
        
		$sql = "UPDATE System".sSCHEMA_POSTFIX.".Country_Tbl "
                . "SET name = '". $oDB->escStr($name) ."'"
                . ", currencyid = '" . $oDB->escStr($currencyid) ."'"
                . ", symbol = '". $oDB->escStr($sym) ."'"
                . ", priceformat = '". $oDB->escStr($pf) ."'"
                . ", addr_lookup = ". $addr_lookup
                . ", minmob = '" . $oDB->escStr($minmob)."', maxmob = '". $oDB->escStr($maxmob) ."'"
				. " WHERE id = ".intval($id);
        
        return is_resource($oDB->query($sql) );
	}
	

	public static function setISO3166Attributes(SimpleDOMElement &$obj_XMLDOM, RDB &$oDB, $countryid)
	{
		$obj_CountryConfig = self::produceConfig($oDB, $countryid);
		
		if($obj_CountryConfig->getAlpha2code() > 0)
		$obj_XMLDOM->addAttribute("alpha2code", $obj_CountryConfig->getAlpha2code());
		if($obj_CountryConfig->getAlpha3code() > 0)
		$obj_XMLDOM->addAttribute("alpha3code", $obj_CountryConfig->getAlpha3code());
		if($obj_CountryConfig->getNumericCode() > 0)
		$obj_XMLDOM->addAttribute("code", $obj_CountryConfig->getNumericCode());
	}
}
?>