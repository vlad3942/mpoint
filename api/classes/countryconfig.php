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
	 * Default Constructor
	 *
	 * @param 	integer $id 		Unique ID for the Country, this MUST match the GoMobile's ID for the Country
	 * @param 	string $name 		mPoint's Name for the Country
	 * @param 	string $currency 	Currency used in the Country
	 * @param 	string $minmob 		Min value a valid Mobile Number can have in the Country
	 * @param 	string $maxmob 		Max value a valid Mobile Number can have in the Country
	 * @param 	string $ch 			GoMobile channel used for communicating with the customers in the Country
	 * @param 	string $pf 			Price Format used in the Country
	 */
	public function __construct($id, $name, $currency, $minmob, $maxmob, $ch, $pf)
	{
		parent::__construct($id, $name);
		
		$this->_sCurrency = trim($currency);
		$this->_sMinMobile = trim($minmob);
		$this->_sMaxMobile = trim($maxmob);
		$this->_sChannel = trim($ch);
		$this->_sPriceFormat = trim($pf);
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
}
?>