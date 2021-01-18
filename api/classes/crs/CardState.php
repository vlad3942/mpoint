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
 * Data class for holding Card State Configurations
 *
 */
class CardState
{
    /**
     * Hold unique ID of card state
     *
     * @var integer
     */
   private int $_iID;
    /**
     * Hold name of the card state
     *
     * @var integer
     */
   private string $_iName;
    /**
     * Hold card state status
     *
     * @var boolean
     */
   private bool $_bEnabled;

	public function __construct(int $id, string $name, bool $enabled)
	{
	    $this->_iID = $id;
	    $this->_iName = $name;
        $this->_bEnabled = $enabled;
	}

    /**
     * Returns unique ID of card state
     * @return 	integer
     */
	public function getID() : int
    {
        return $this->_iID;
    }
    /**
     * Returns name of the card state
     * @return 	integer
     */
	public function getName() : string
    {
        return $this->_iName;
    }
    /**
     * Returns transaction card state status
     * @return 	boolean
     */
	public function getEnabled() : bool
    {
        return $this->_bEnabled;
    }

    /**
     * Returns the XML payload of Configurations for card state.
     *
     * @return 	String
     */
	public function toXML() : string
	{
        $xml = '<card_state>';
        $xml .= '<id>'.$this->getID() .'</id>';
        $xml .= '<name>'.$this->getName().'</name>';
        $xml .= '<enabled>'.General::bool2xml($this->getEnabled()).'</enabled>';
        $xml .= '</card_state>';
        return $xml;
	}
	

	/**
	 * Creates a list of al card state configuration instances that are available in the database
	 * 
	 * @param	RDB $oDB 	Reference to the Database Object that holds the active connection to the mPoint Database
	 * @return	CardState   $aObj_Configurations	List of Card  State Configurations
	 */
	public static function produceConfig(RDB $oDB) : array
	{
        $aObj_Configurations = array();
		$sql = "SELECT id,name,enabled FROM System". sSCHEMA_POSTFIX .".CardState_Tbl ORDER BY id ASC";
		try {
            $res = $oDB->query($sql);
            while ($RS = $oDB->fetchName($res)) {
                $aObj_Configurations[] = new CardState ($RS["ID"], $RS["NAME"], $RS["ENABLED"]);
            }
        } catch (SQLQueryException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
        return $aObj_Configurations;
	}
}
?>