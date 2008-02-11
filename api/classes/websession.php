<?php
/**
 * The Web Session package holds the user data that should be serialised and stored in a database or session file
 * The package uses the PHP Magic method: __sleep to ensure that session data is serialised correctly
 *
 * @author Jonatan Evald Buus
 * @package Websession
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * API class for handling all Web Sessions using an Object to store session info
 *
 */
class WebSession
{
	/**
	 * Array for holding general info used in the Web Session
	 *
	 * @var	array $aInfo
	 */
	private $_aInfo = array();
	
	/**
	 * Default Constructor
	 *
	 * @return 	Websession	Reference to the initialized Web Session Object
	 */
	public function __construct()
	{
	}
	
	/**
	 * Magic function for Serialization before storing in Session
	 *
	 */
	public function __sleep()
	{
		return array("_aInfo");
	}
	
	/**
	 * Magic function for De-Serialization from Session
	 *
	 */
	public function __wakeup()
	{

	}
	
	/**
	 * Returns the data for the specified entry from the info array
	 *
	 * @see		Websession::$_aInfo
	 *
	 * @param	mixed $k 	Key of data from Info struct to return
	 * @return	mixed		Specified data, false on error
	 */
	public function getInfo($k="all")
	{
		// Define alias keys
		switch (strtolower($k) )
		{
		default:
			break;
		}
		// Determine return
		switch (true)
		{
		case (array_key_exists($k, $this->_aInfo) ):	// Specific part of Userinfo
			$mData = $this->_aInfo[$k];
			break;
		case ($k == "all"):	// Array of All Userinfo
			$mData = $this->_aInfo;
			break;
		default:	// Error
			$mData = false;
			break;
		}
		
		return $mData;
	}
	
	/**
	 * Adds a data entry for the info array
	 *
	 * @see		Websession::$_aInfo
	 *
	 * @param	mixed $k 	Key to reference provided data in the Info struct
	 * @param	mixed $d 	Data to add to the Info struct
	 * @return	boolean		True on Success, false on error
	 */	
	public function setInfo($k, $d)
	{
		/* ---------- Error Handling Start ---------- */
		$aErrCd = array();
		if (empty($k) === true) { $aErrCd[11] = "Invalid Key"; }
		if (empty($d) === true) { $aErrCd[21] = "Invalid Info"; }
		/* ---------- Error Handling End ---------- */
		$bStatus = false;
		
		// No Errors found
		if (count($aErrCd) == 0)
		{
			// Define alias keys
			switch (strtolower($k) )
			{
			default:
				break;
			}
			$this->_aInfo[$k] = $d;
			$bStatus = true;
		}
		// Errors found
		else
		{
		}
		
		return $bStatus;
	}
	
	/**
	 * Returns the data for the specified entry from the info array
	 *
	 * @see		Websession::$_aInfo
	 *
	 * @param	mixed $k 	Key of data from Info struct to return
	 * @return	mixed		Specified data, false on error
	 */
	public function delInfo($k="na")
	{
		// Define alias keys
		switch (strtolower($k) )
		{
		default:
			break;
		}
		// Determine return
		switch (true)
		{
		case (array_key_exists($k, $this->_aInfo) ):	// Specific part of Userinfo
			unset($this->_aInfo[$k]);
			$bStatus = true;
			break;
		case ($k == "all"):	// Array of All Userinfo
			unset($this->_aInfo);
			$bStatus = true;
			break;
		default:	// Error
			$bStatus = false;
			break;
		}
		
		return $bStatus;
	}

}
?>