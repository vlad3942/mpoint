<?php
/**
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package mConsole
 * @subpackage Config
 * @version 1.00
 */

/**
 * Data class for holding a Payment Service Provider's currency configurations
 *
 */
class PSPCurrencyConfig extends BasicConfig
{
	/**
	 * The unique ID for the country that the Payment Service Provider uses this currency in
	 *
	 * @var integer
	 */
	private $_iCurrencyID;

    /**
     * The Decimals number for the currency that the Payment Service Provider uses
     *
     * @var integer
     */
    private $_iDecimals;

	/**
	 * Default constructor
	 * 
	 * @param integer $id			The unique ID for the Payment Service Provider's currency configuration
	 * @param string $name			The Payment Service Provider's name for the currency
	 * @param integer $countryid	The unique ID for the country that the Payment Service Provider uses this currency in
	 */
	public function __construct($id, $name, $currencyid, $decimals)
	{
		parent::__construct($id, $name);
		$this->_iCurrencyID = (integer) $currencyid;
        $this->_iDecimals = (integer) $decimals;
	}

	public function getCurrencyID() { return $this->_iCurrencyID; }

    public function getDecimals() { return $this->_iDecimals; }
	
	/**
	 * Marshalls the object as an XML element in the following format:
	 * 	<currency id="[INTEGER]" country-id="[INTEGER]">[STRING]</currency>
	 * 
	 * @return string
	 */
	public function toXML()
	{
		$xml = '<currency id="'. $this->getID() .'" country-id="'. $this->_iCurrencyID .'" currency-id="'. $this->_iCurrencyID .'" decimals="'. $this->_iDecimals .'">';
		$xml .= htmlspecialchars($this->getName(), ENT_NOQUOTES);
		$xml .= '</currency>';
		
		return $xml;
	}
	
	/**
	 * Creates a new instance for the specified ID from the database 
	 * 
	 * @param 	RDB $oDB 		Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param 	integer $id 	The unique ID for the currency configuration that should be instantiated
	 * @return	CardPrefixConfig|NULL
	 */
	public static function produceConfig(RDB $oDB, $id)
	{
		$sql = "SELECT id, currencyid, name
				FROM System". sSCHEMA_POSTFIX .".PSPCurrency_Tbl
				WHERE id = ". intval($id) ." AND enabled = '1'";
//		echo $sql ."\n";
		$RS = $oDB->query($sql);
		if (is_array($RS) === true && $RS["ID"] > 0)
		{
			return new CardPrefixConfig($RS["ID"], $RS["MIN"], $RS["MAX"]);
		}
		else { return null; }
	}
	
	/**
	 * Creates a list of currency configuration instances for the specified Payment Service Provider ID from the database
	 * 
	 * @param 	RDB $oDB 			Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param	integer $pspid		The unique ID for the Payment Service Provider whose currency configurations should be instantiated
	 * @return	array
	 */
	public static function produceConfigurations(RDB $oDB, $pspid)
	{
		$sql = "SELECT PC.id, PC.currencyid, PC.name, C.decimals
				FROM System". sSCHEMA_POSTFIX .".PSPCurrency_Tbl PC
				INNER JOIN System". sSCHEMA_POSTFIX .".Currency_Tbl C ON PC.currencyid = C.id
				WHERE PC.pspid = ". intval($pspid) ." AND PC.enabled = '1' 
				ORDER BY id ASC";
//		echo $sql ."\n";
		$res = $oDB->query($sql);
		$aObj_Currencies = array();
		while ($RS = $oDB->fetchName($res) )
		{
			if (is_array($RS) === true && $RS["ID"] > 0)
			{
				$aObj_Currencies[] = new PSPCurrencyConfig($RS["ID"], $RS["NAME"], $RS["CURRENCYID"], $RS["DECIMALS"]);
			}
		}
		
		return $aObj_Currencies;
	}
}
?>