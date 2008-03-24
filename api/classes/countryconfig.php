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
	 * Currency used in the Country.
	 *
	 * @var string
	 */
	private $_sCurrency;
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
	 * Default Constructor
	 *
	 * @param 	integer $id 		Unique ID for the Country, this MUST match the GoMobile's ID for the Country
	 * @param 	string $name 		mPoint's Name for the Country
	 * @param 	string $currency 	Currency used in the Country
	 * @param 	string $minmob 		Min value a valid Mobile Number can have in the Country
	 * @param 	string $maxmob 		Max value a valid Mobile Number can have in the Country
	 * @param 	string $ch 			GoMobile channel used for communicating with the customers in the Country
	 * @param 	string $pf 			Price Format used in the Country
	 * @param 	integer $dec 		Number of Decimals used for Prices in the Country
	 * @param 	boolean $als 		Boolean Flag indicating whether an Address Lookup Service is available in the Country
	 * @param 	boolean $doi 		Boolean Flag indicating whether an Operators in the Country required Double Opt-In for payments made via Premium SMS
	 */
	public function __construct($id, $name, $currency, $minmob, $maxmob, $ch, $pf, $dec, $als, $doi)
	{
		parent::__construct($id, $name);
		
		$this->_sCurrency = trim($currency);
		$this->_sMinMobile = trim($minmob);
		$this->_sMaxMobile = trim($maxmob);
		$this->_sChannel = trim($ch);
		$this->_sPriceFormat = trim($pf);
		$this->_iNumDecimals = (integer) $dec;
		$this->_bAddressLookup = $als;
		$this->_bDoubleOptIn = $doi;
	}
	
	/**
	 * Returns the Currency used in the Country.
	 *
	 * @return 	string
	 */
	public function getCurrency() { return $this->_sCurrency; }
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
	
	public function toXML()
	{
		$xml = '<country-config id="'. $this->getID() .'">';
		$xml .= '<name>'. htmlspecialchars($this->getName(), ENT_NOQUOTES) .'</name>';
		$xml .= '<currency>'. $this->_sCurrency .'</currency>';
		$xml .= '<min-mobile>'. $this->_sMaxMobile .'</min-mobile>';
		$xml .= '<max-mobile>'. $this->_sMinMobile .'</max-mobile>';
		$xml .= '<channel>'. $this->_sChannel .'</channel>';
		$xml .= '<price-format>'. $this->_sPriceFormat .'</price-format>';
		$xml .= '<num-decimals>'. $this->_iNumDecimals .'</num-decimals>';
		$xml .= '<address-lookup>'. General::bool2xml($this->_bAddressLookup) .'</address-lookup>';
		$xml .= '<double-opt-in>'. General::bool2xml($this->_bDoubleOptIn) .'</double-opt-in>';
		$xml .= '</country-config>';
		
		return $xml;
	}
}
?>