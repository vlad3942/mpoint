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
   private int $_iID;
    /**
     * Hold name of the transaction type
     *
     * @var integer
     */
   private string $_iName;
    /**
     * Hold transaction type status
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
     * Returns unique ID of transaction type
     * @return 	integer
     */
	public function getID() : int
    {
        return $this->_iID;
    }
    /**
     * Returns name of the transaction type
     * @return 	integer
     */
	public function getName() : string
    {
        return $this->_iName;
    }
    /**
     * Returns transaction type status
     * @return 	boolean
     */
	public function getEnabled() : bool
    {
        return $this->_bEnabled;
    }

    /**
     * Returns the XML payload of Configurations for transaction type.
     *
     * @return 	String
     */
	public function toXML() : string
	{
        $xml = '<transaction-type  id="'.$this->getID().'" name="'.$this->getName().'" enabled="'.General::bool2xml($this->getEnabled()).'" />';
		return $xml;
	}

    public function toAttributelessXML() : string
    {
        $xml = '<transaction_type>';
        $xml .= '<id>'.$this->getID() .'</id>';
        $xml .= '<name>'.$this->getName().'</name>';
        $xml .= '<enabled>'.General::bool2xml($this->getEnabled()).'</enabled>';
        $xml .= '</transaction_type>';
        return $xml;
    }
	

	/**
	 * Creates a list of al transaction type configuration instances that are enabled in the database
	 * @return array
	 */
	public static function produceConfig(): array
	{
        $transactionTypes = array(Constants::iTRANSACTION_TYPE_SHOPPING_ONLINE=>'Shopping Online',
                                   Constants::iTRANSACTION_TYPE_SHOPPING_OFFLINE=>'Shopping Offline',
                                   Constants::iTRANSACTION_TYPE_SELF_SERVICE_ONLINE=>'Self Service Online',
                                   Constants::iTRANSACTION_TYPE_SELF_SERVICE_OFFLINE=>'Self Service Offline',
                                   Constants::iTRANSACTION_TYPE_SELF_SERVICE_ONLINE_WITH_ADDITIONAL_RULES_ON_FOP=>'Self Service Online with additional rules on FOP',
                                   Constants::iTRANSACTION_TYPE_PAYMENT_LINK_TRANSACTION=>'Payment Link Transaction',
                                   Constants::iTRANSACTION_TYPE_CALL_CENTER_PURCHASE=>'Call Center Purchase');
        
		foreach($transactionTypes as $txnType=>$value)
		{
			$aObj_Configurations[] = new TransactionTypeConfig ($txnType, $value, true);
		}
		return $aObj_Configurations;
	}
}
?>