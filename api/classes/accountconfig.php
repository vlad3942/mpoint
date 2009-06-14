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
 * @subpackage AccountConfig
 * @version 1.0
 */

/**
 * Data class for hold the configuration for the Account a Transaction is associated with
 *
 */
class AccountConfig extends BasicConfig
{
	/**
	 * Unique ID for the Client to whom the Account belongs
	 *
	 * @var integer
	 */
	private $_iClientID;
	/**
	 * The Mobile Number (MSISDN) for the account holder.
	 *
	 * @var string
	 */
	private $_sMobile;

	/**
	 * Default Constructor
	 *
	 * @param 	integer $id 	Unique ID for the Account
	 * @param 	integer $clid 	Unique ID for the Client to whom the Account belongs
	 * @param 	string $name 	Name of the Account
	 * @param 	string $mob 	Mobile Number (MSISDN) for the account holder.
	 */
	public function __construct($id, $clid, $name, $mob)
	{
		parent::__construct($id, $name);

		$this->_iClientID = (integer) $clid;
		$this->_sMobile = trim($mob);

	}
	/**
	 * Returns the Unique ID for the Client to whom the Account belongs
	 *
	 * @return 	integer
	 */
	public function getClientID() { return $this->_iClientID; }
	/**
	 * Returns the Mobile Number (MSISDN) for the account holder.
	 *
	 * @return 	string
	 */
	public function getMobile() { return $this->_sMobile; }
}
?>