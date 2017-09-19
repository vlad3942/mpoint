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
class CurrencyConfig extends BasicConfig
{
    /**
     * The 3 Digit alphabetic code as per the ISO 4127 standards
     *
     * @var integer
     */
    private $_sCode;


	public function __construct($id, $name, $code)
	{
		parent::__construct($id, $name);
		$this->_sCode = $code;
	}

	public function getCode() { return $this->_sCode; }


	/**
	 * Produces a new instance of a Currency Configuration Object.
	 *
	 * @param 	RDB $oDB 		Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param 	integer $id 	Unique ID for the Currency the request is performed in
	 * @return 	CurrencyConfig
	 */
	public static function produceConfig(RDB &$oDB, $id)
	{
		$sql = "SELECT id, name, code
				FROM System".sSCHEMA_POSTFIX.".Currency_Tbl CT			
				WHERE CT.id = ". intval($id) ." AND CT.enabled = '1'";
//		echo $sql ."\n";
		$RS = $oDB->getName($sql);

		return new CurrencyConfig($RS["ID"], $RS["NAME"], $RS['CODE']);
	}
}
?>