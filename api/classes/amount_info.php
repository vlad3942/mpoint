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
	 * Default constructor
	 * 
	 * @param long $amt			The total amount in the country's smallest currency
	 * @param integer $cid		The unique ID of the country the amount is associated with
	 * @param string $cur		The currency (DDK, USD, EUR etc.) that the amount is in based on the country
	 * @param string $sym		The symbol that is used to represent the currency (kr. $, € etc.)
	 * @param string $fmt		The price format that is used when displaying prices in the country
	 */
	public function __construct($amt, $cid, $cur, $sym, $fmt)
	{
		$this->_lAmount =  (float) $amt;
		$this->_iCountryID = (integer) $cid;
		$this->_sCurrency = trim($cur);
		$this->_sSymbol = trim($sym);
		$this->_sFormat = trim($fmt);
	}

	public function getAmount() { return $this->_lAmount; }
	public function getCountryID() { return $this->_iCountryID; }
	public function getCurrency() { return $this->_sCurrency; }
	public function getSymbol() { return $this->_sSymbol; }
	public function getFormat() { return $this->_sFormat; }

	public function toXML($name="amount")
	{
		$xml  = '<'. $name .' country-id="'. $this->_iCountryID .'" currency="'. htmlspecialchars($this->_sCurrency, ENT_NOQUOTES) .'"';
		if (strlen($this->_sSymbol) > 0) { $xml .= ' symbol="'. htmlspecialchars($this->_sCurrency, ENT_NOQUOTES) .'"'; }
		if (strlen($this->_sFormat) > 0) { $xml .= ' format="'. htmlspecialchars($this->_sFormat, ENT_NOQUOTES) .'"'; }
		$xml  .= '>';
		$xml .= $this->_lAmount; 
		$xml  .= '</'. $name .'>';

		return $xml;
	}
}
?>