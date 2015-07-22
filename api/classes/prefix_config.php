<?php
/**
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package mConsole
 * @subpackage Config
 * @version 1.0
 */

/**
 * Data class for holding card prefix configurations
 *
 */
class PrefixConfig
{
	/**
	 * The unique ID for the prefix configuration
	 *
	 * @var integer
	 */
	private $_iID;
	/**
	 * The minimum value for the prefix range
	 *
	 * @var integer
	 */
	private $_iMin;
	/**
	 * The maximum value for the prefix range
	 *
	 * @var integer
	 */
	private $_iMax;

	/**
	 * Default constructor
	 * 
	 * @param integer $id	The unique ID for the prefix configuration
	 * @param integer $min	The minimum value for the prefix range
	 * @param integer $max	The maximum value for the prefix range
	 */
	public function __construct($id, $min, $max)
	{
		$this->_iID = (integer) $id;
		$this->_iMin = (integer) $min;
		$this->_iMax = (integer) $max;

		// Normalize Defaults
		if ($this->_iMin <= 0) { $this->_iMin = -1; }
		if ($this->_iMax <= 0) { $this->_iMax = -1; }
	}

	public function getID() { return $this->_iID; }
	public function getMin() { return $this->_iMin; }
	public function getMax() { return $this->_iMax; }
	
	/**
	 * Marshalls the object as an XML element in the following format:
	 * 	<prefix id="[INTEGER]">
	 * 		<min>[INTEGER]</min>
	 * 		<max>[INTEGER]</max>
	 * 	</prefix>
	 * 
	 * @return string
	 */
	public function toXML()
	{
		$xml = '<prefix id="'. $this->_iID .'">';
		$xml .= '<min>'. $this->_iMin .'</min>';
		$xml .= '<max>'. $this->_iMax .'</max>';
		$xml .= '</prefix>';
		
		return $xml;
	}
	
	/**
	 * Creates a new instance for the specified ID from the database 
	 * 
	 * @param 	RDB $oDB 		Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param 	integer $id 	The unique ID for the Prefix range that should be instantiated
	 * @return	PrefixConfig|NULL
	 */
	public static function produceConfig(RDB $oDB, $id)
	{
		$sql = "SELECT id, Coalesce(min, -1) AS min, Coalesce(max, -1) AS max
				FROM System". sSCHEMA_POSTFIX .".CardPrefix_Tbl
				WHERE id = ". intval($id) ." AND enabled = '1'";
//		echo $sql ."\n";
		$RS = $oDB->query($sql);
		if (is_array($RS) === true && $RS["ID"] > 0)
		{
			return new PrefixConfig($RS["ID"], $RS["MIN"], $RS["MAX"]);
		}
		else { return null; }
	}
	
	/**
	 * Creates a list of prefix configuration instances for the specified Card ID from the database
	 * 
	 * @param 	RDB $oDB 			Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param	integer $cardid		The unique ID for the card for what the prefix ranges that should be instantiated
	 * @return array
	 */
	public static function produceConfigurations(RDB $oDB, $cardid)
	{
		$sql = "SELECT id, Coalesce(min, -1) AS min, Coalesce(max, -1) AS max
				FROM System". sSCHEMA_POSTFIX .".CardPrefix_Tbl
				WHERE cardid = ". intval($cardid) ." AND enabled = '1' 
				ORDER BY id ASC";
//		echo $sql ."\n";
		$res = $oDB->query($sql);
		$aObj_CardPrefixes = array();
		while ($RS = $oDB->fetchName($res) )
		{
			if (is_array($RS) === true && $RS["ID"] > 0)
			{
				$aObj_CardPrefixes[] = new PrefixConfig($RS["ID"], $RS["MIN"], $RS["MAX"]);
			}
		}
		
		return $aObj_CardPrefixes;
	}
}
?>