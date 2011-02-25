<?php
/**
 *
 *
 * @author Jonatan Evald Buus
 * @package Callback
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com Cellpoint Mobile
 * @version 1.00
 */


/**
 *
 *
 */
class SOAPConnInfo
{
	private $_aOptions = array("encoding" => "ISO-8859-1",
							   "exceptions" => true,
							   "trace" => true,
							   "connection_timeout" => 10);
	private $_sURL;
	private $_sUsername;
	private $_sPassword;

	/**
	 * Default Constructor
	 *
	 * @param
	 */
	public function __construct($url, $un, $pw)
	{
		$this->_sURL = $url;
		$this->_sUsername = $un;
		$this->_sPassword = $pw;
	}

	public function getOptions() { return $this->_aOptions; }
	public function getURL() { return $this->_sURL; }
	public function getUsername() { return $this->_sUsername; }
	public function getPassword() { return $this->_sPassword; }
}
?>