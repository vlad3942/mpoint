<?php
/**
 *
 * @author Anna Lagad
 * @copyright Cellpoint Digital
 * @link http://www.cellpointdigital.com
 * @package mConsole
 * @version 1.01
 */

/**
 * Data class for holding Transaction Type Configurations
 *
 */
class TransactionTypeConfig
{
    /**
     * Hold unique ID of transaction type
     *
     * @var integer
     */
   private $_iID;
    /**
     * Hold name of the transaction type
     *
     * @var integer
     */
   private $_iName;
    /**
     * Hold transaction type status
     *
     * @var boolean
     */
   private $_bEnabled;

	public function __construct($id, $name, $enabled)
	{
	    $this->_iID = $id;
	    $this->_iName = $name;
        $this->_bEnabled = $enabled;
	}

    /**
     * Returns unique ID of transaction type
     * @return 	integer
     */
	public function getID() { return $this->_iID; }
    /**
     * Returns name of the transaction type
     * @return 	integer
     */
	public function getName() { return $this->_iName; }
    /**
     * Returns transaction type status
     * @return 	boolean
     */
	public function getEnabled() { return $this->_bEnabled; }

    /**
     * Returns the XML payload of Configurations for transaction type.
     *
     * @return 	String
     */
	public function toXML()
	{
        $xml = '<transaction  type-id="'.$this->getID().'" name="'.$this->getName().'" enabled="'.General::bool2xml($this->getEnabled()).'" />';
		return $xml;
	}
	

	/**
	 * Creates a list of al transaction type configuration instances that are enabled in the database
	 * 
	 * @param	RDB $oDB 		Reference to the Database Object that holds the active connection to the mPoint Database
	 * @return	TransactionTypeConfig $aObj_Configurations	List of Transaction Type Configurations
	 */
	public static function produceConfig(RDB $oDB)
	{
		$sql = "SELECT id,name,enabled FROM System". sSCHEMA_POSTFIX .".Type_Tbl ORDER BY id ASC";
		$res = $oDB->query($sql);
		$aObj_Configurations = array();
		while ($RS = $oDB->fetchName($res) )
		{
			$aObj_Configurations[] = new TransactionTypeConfig ($RS["ID"], $RS["NAME"], $RS["ENABLED"]);
		}
		return $aObj_Configurations;
	}
}
?>