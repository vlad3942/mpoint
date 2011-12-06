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
 * @subpackage BasicConfig
 * @version 1.0
 */

/**
 * Data class for hold the basic data for all types of configurations
 *
 */
abstract class BasicConfig
{
	/**
	 * Unique ID for the Configuration
	 *
	 * @var integer
	 */
	private $_iID;
	/**
	 * Name for the Configuration
	 *
	 * @var string
	 */
	private $_sName;
	
	/**
	 * Default Constructor.
	 *
	 * @param 	integer $id 	Unique ID for the Configuration
	 * @param 	string $name 	Name for the Configuration
	 */
	public function __construct($id, $name)
	{
		$this->_iID =  (integer) $id;
		$this->_sName = trim($name);
	}
	
	/**
	 * Returns the Unique ID for the Configuration
	 *
	 * @return 	integer
	 */
	public function getID() { return $this->_iID; }
	
	/**
	 * Returns the Name for the Configuration
	 *
	 * @return 	string
	 */
	public function getName() { return $this->_sName; }
}
?>