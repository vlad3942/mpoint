<?php
/**
 * The Configuration package contains various data classes holding information such as:
 * 	- Configuration for the Country the transaction is processed in
 * 	- Configuration for the Client on whose behalf mPoint is processing the transaction
 * 	- Configuration for one of the Client's Shops
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Config
 * @subpackage ShopConfig
 * @version 1.0
 */

/**
 * Data class holding the Shop Configuration.
 *
 */
class ShopConfig extends BasicConfig
{
	/**
	 * Configuration for the Country the Client can process transactions in
	 *
	 * @var CountryConfig
	 */
	private $_obj_CountryConfig;
	
	/**
	 * Name of the Shipping company used
	 *
	 * @var string
	 */
	private $_sShippingCompany;
	/**
	 * Cost of Shipping in Country's smallest currency
	 *
	 * @var integer
	 */
	private $_iShippingCost;
	/**
	 * Min amount for Orders for Free Shipping
	 *
	 * @var integer
	 */
	private $_iMinFreeShipping;
	/**
	 * Boolean Flag indicating whether the customer can select a Delivery Date
	 *
	 * @var boolean
	 */
	private $_bDeliveryDate;
	
	/**
	 * Default Constructor
	 *
	 * @param 	integer $id 		Unique ID for the Shop in mPoint
	 * @param 	CountryConfig $oCC 	Configuration for the Country the Client can process transactions in
	 * @param 	string $ship 		Name of the Shipping company used
	 * @param 	integer $sc 		Cost of Shipping in Countrys smallest currency
	 * @param 	integer $mfs 		Min amount for Orders for Free Shipping
	 * @param 	boolean $dd			Boolean Flag indicating whether the customer can select a Delivery Date
	 */
	public function __construct($id, CountryConfig &$oCC, $ship, $sc, $mfs, $dd)
	{
		parent::__construct($id, "");
		
		$this->_obj_CountryConfig = $oCC;
		
		$this->_sShippingCompany = trim($ship);
		$this->_iShippingCost = (integer) $sc;
		$this->_iMinFreeShipping = $mfs;
		$this->_bDeliveryDate = (bool) $dd;
	}
	/**
	 * Returns the Configuration for the Country the Client can process transactions in
	 *
	 * @return 	CountryConfig
	 */
	public function getCountryConfig() { return $this->_obj_CountryConfig; }
	
	/**
	 * Returns the Name of the Shipping company used
	 *
	 * @return 	string
	 */
	public function getShippingCompany() { return $this->_sShippingCompany; }
	/**
	 * Returns the Cost of Shipping in Country's smallest currency
	 *
	 * @return 	integer
	 */
	public function getShippingCost() { return $this->_iShippingCost; }
	/**
	 * Returns the Min amount for Orders for Free Shipping
	 *
	 * @return 	inteer
	 */
	public function getMinFreeShipping() { return $this->_iMinFreeShipping; }
	/**
	 * Returns True if the customer can select a Delivery Date, otherwise False
	 *
	 * @return 	boolean
	 */
	public function useDeliveryDate() { return $this->_bDeliveryDate; }
	
	public function toXML()
	{
		$xml = '<shop-config id="'. $this->getID() .'">';
		$xml .= '<shipping>';
		$xml .= '<company>'. htmlspecialchars(($this->_sShippingCompany) ) .'</company>';
		$xml .= '<cost currency="'. $this->_obj_CountryConfig->getCurrency() .'">'. $this->_iShippingCost .'</cost>';
		$xml .= '<price>'. General::formatAmount($this->_obj_CountryConfig, $this->_iShippingCost) .'</price>';
		$xml .= '<min-order currency="'. $this->_obj_CountryConfig->getCurrency() .'">'. $this->_iMinFreeShipping .'</min-order>';
		$xml .= '</shipping>';
		$xml .= '<delivery-date>'. General::bool2xml($this->_bDeliveryDate) .'</delivery-date>';
		$xml .= '</shop-config>';
		
		return $xml;
	}
	
	/**
	 * Produces a new instance of a Shop Configuration Object.
	 *
	 * @param 	RDB $oDB 			Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param 	ClientConfig $oCC 	Configuration for the Client who owns the Transaction
	 * @return 	ShopConfig
	 * @throws 	mPointException
	 */
	public static function produceConfig(RDB &$oDB, ClientConfig &$oCC)
	{
		$sql = "SELECT id, shipping, ship_cost, free_ship, del_date
				FROM Client.Shop_Tbl
				WHERE clientid = ". $oCC->getID() ." AND keywordid = ". $oCC->getKeywordConfig()->getID();
				
//		echo $sql ."\n";
		$RS = $oDB->getName($sql);
		
		if (is_array($RS) === false)
		{
			throw new mPointException("No Shop Configuration found for Client: ". $oCC->getID() ." using Keyword: ". $oCC->getKeywordConfig()->getID(), 1021);
		}
		return new ShopConfig($RS["ID"], $oCC->getCountryConfig(), $RS["SHIPPING"], $RS["SHIP_COST"], $RS["FREE_SHIP"], $RS["DEL_DATE"]);
	}
}
?>