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
 * @subpackage KeywordConfig
 * @version 1.0
 */

/**
 * Data class for hold the configuration for the Keyword used to communicate with the Customer
 *
 */
class KeywordConfig extends BasicConfig
{
	/**
	 * Unique ID for the Client who owns the Keyword
	 *
	 * @var integer
	 */
	private $_iClientID;
	/**
	 * Total price for the Product(s) associated with this Keyword in Country's smallest currency
	 *
	 * @var integer
	 */
	private $_iPrice;
	
	/**
	 * Default Constructor
	 *
	 * @param 	integer $id 	Unique ID for the Keyword
	 * @param 	integer $clid 	Unique ID for the Client who owns the Keyword
	 * @param 	string $kw 		Keyword on the Channel, a keyword is defined as the first word in an MO-SMS
	 * @param 	integer $prc 	Total price for the Product(s) associated with this Keyword in Country's smallest currency
	 */
	public function __construct($id, $clid, $kw, $prc)
	{
		parent::__construct($id, $kw);
		
		$this->_iClientID = (integer) $clid;
		$this->_iPrice = (integer) $prc;
	}
	
	/**
	 * Returns the Keyword on the Channel, a keyword is defined as the first word in an MO-SMS.
	 * Please note that this is simply a wrapper method for method: getName() from class: BasicConfig intended
	 * to make it more intuitive to access the keyword.
	 * 
	 * @see 	BasicConfig::getName()
	 *
	 * @return 	string
	 */
	public function getKeyword() { return $this->getName(); }
	
	/**
	 * Returns the Unique ID for the Client who owns the Keyword
	 *
	 * @return 	integer
	 */
	public function getClientID() { return $this->_iClientID; }
	/**
	 * Returns the Total price for the Product(s) associated with this Keyword in Country's smallest currency
	 *
	 * @return 	integer
	 */
	public function getPrice() { return $this->_iPrice; }
}
?>