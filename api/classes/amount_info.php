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
 * Data class for hold all data relevant for an Amount
 *
 */
class AmountInfo
{
	/**
	 * The total amount in the country's smallest currency
	 *
	 * @var integer
	 */
	private $_lAmount;
	/**
	 * The unique ID of the country the amount is associated with
	 *
	 * @var integer
	 */
	private $_iCountryID;
	/**
	 * The currency (DDK, USD, EUR etc.) that the amount is in based on the country
	 *
	 * @var string
	 */
	private $_sCurrency;
	/**
	 * The symbol that is used to represent the currency (kr. $, € etc.)
	 *
	 * @var string
	 */
	private $_sSymbol;
	/**
	 * The price format that is used when displaying prices in the country
	 *
	 * @var string
	 */
	private $_sFormat;

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
	 * Number of Decimals used for Prices in the Country:
	 * 2 for USA, 0 for Denmark etc.
	 *
	 * @var integer
	 */
	private $_iNumDecimals;

	/**
     * Numeric code of Currency as per the ISO 3166 standards
     *
     * @var integer
     */
	private $_iCurrencyId;

	/**
	 * Default constructor
	 * 
	 * @param long $amt			The total amount in the country's smallest currency
	 * @param integer $cid		The unique ID of the country the amount is associated with
	 * @param string $cur		The currency (DDK, USD, EUR etc.) that the amount is in based on the country
	 * @param string $sym		The symbol that is used to represent the currency (kr. $, € etc.)
	 * @param string $fmt		The price format that is used when displaying prices in the country
	 * @param string $alpha2Code		The alpha2code that is used to when displaying alpha2code for amount
	 * @param string $alpha3Code		The alpha3code that is used to when displaying alpha3code for amount
	 * @param integer $numericCode		The numericCode that is used to when displaying ISO standard county code
	 * @param integer $decimals		The decimals that is used to amount with decimal point
	 * @param integer $currencyId		The currency id that is used to display currency id for amount
	 */
	public function __construct($amt, $cid, $cur, $sym, $fmt,$alpha2Code="", $alpha3Code="", $numericCode=0, $decimals=0, $currencyId=0)
	{
		$this->_lAmount =  (float) $amt;
		$this->_iCountryID = (integer) $cid;
		$this->_sCurrency = trim($cur);
		$this->_sSymbol = trim($sym);
		$this->_sFormat = trim($fmt);
		$this->_sAlpha2Code = trim($alpha2Code);
		$this->_sAlpha3Code = trim($alpha3Code);
		$this->_iNumericCode = (integer)$numericCode;
		$this->_iNumDecimals = (integer)$decimals;
		$this->_iCurrencyId = (integer)$currencyId;
	}

	public function getAmount() { return $this->_lAmount; }
	public function getCountryID() { return $this->_iCountryID; }
	public function getCurrency() { return $this->_sCurrency; }
	public function getSymbol() { return $this->_sSymbol; }
	public function getFormat() { return $this->_sFormat; }
	public function getAlpha2code() { return $this->_sAlpha2Code; }
	public function getAlpha3code() { return $this->_sAlpha3Code; }
	public function getNumericCode() { return $this->_iNumericCode; }
	public function getDecimals() { return $this->_iNumDecimals; }
	public function getCurrencyId() { return $this->_iCurrencyId; }

	public function toXML($name="amount")
	{
		$xml  = '<'. $name .' country-id="'. $this->_iCountryID .'" currency="'. htmlspecialchars($this->_sCurrency, ENT_NOQUOTES) .'"';
		if (strlen($this->_sSymbol) > 0) { $xml .= ' symbol="'. htmlspecialchars($this->_sCurrency, ENT_NOQUOTES) .'"'; }
		if (strlen($this->_sFormat) > 0) { $xml .= ' format="'. htmlspecialchars($this->_sFormat, ENT_NOQUOTES) .'"'; }
		if (strlen($this->_sAlpha2Code) > 0) { $xml .= ' alpha2code="'. htmlspecialchars($this->_sAlpha2Code, ENT_NOQUOTES) .'"'; }
		if (strlen($this->_sAlpha3Code) > 0) { $xml .= ' alpha3code="'. htmlspecialchars($this->_sAlpha3Code, ENT_NOQUOTES) .'"'; }
		if (strlen($this->_iNumericCode) > 0) { $xml .= ' code="'. htmlspecialchars($this->_iNumericCode, ENT_NOQUOTES) .'"'; }
		if (strlen($this->_iNumDecimals) > 0) { $xml .= ' decimals="'. htmlspecialchars($this->_iNumDecimals, ENT_NOQUOTES) .'"'; }
		if (strlen($this->_iCurrencyId) > 0) { $xml .= ' currency-id="'. htmlspecialchars($this->_iCurrencyId, ENT_NOQUOTES) .'"'; }
		$xml  .= '>';
		$xml .= $this->_lAmount; 
		$xml  .= '</'. $name .'>';

		return $xml;
	}
}
?>