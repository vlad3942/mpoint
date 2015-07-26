<?php
/**
 * 
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package mConsole
 * @subpackage Search
 * @version 1.00
 */

/**
 * Data class for hold all data relevant Message Information for each State a Transaction has passed through
 *
 */
class MessageInfo
{
	/**
	 * Unique ID for the Message Entry
	 *
	 * @var integer
	 */
	private $_iID;
	/**
	 * Unique ID for the State that generated the Message
	 *
	 * @var integer
	 */
	private $_iStateID;
	/**
	 * The timestamp when the message was created in the format: YYYY-MM-DD hh:mm:ss+00:00
	 *
	 * @var timestamp
	 */
	private $_timestamp;
	/**
	 * Debug data associated with the message
	 *
	 * @var string
	 */
	private $_sDebugData;

	/**
	 * Default constructor
	 * 
	 * @param integer $id		Unique ID for the Message Entry
	 * @param integer $sid		Unique ID for the State that generated the Message
	 * @param timestamp $ts		The timestamp when the message was created in the format: YYYY-MM-DD hh:mm:ss+00:00
	 * @param string $data		Debug data associated with the message
	 */
	public function __construct($id, $sid, $ts, $data)
	{
		$this->_iID =  (integer) $id;
		$this->_iStateID = (integer) $sid;
		$this->_timestamp = trim($ts);
		$this->_sDebugData = trim($data);
	}

	public function getID() { return $this->_iID; }
	public function getStateID() { return $this->_iStateID; }
	public function getTimestamp() { return $this->_timestamp; }
	public function getDebugData() { return $this->_sDebugData; }

	public function toXML()
	{
		$xml  = '<message id="'. $this->_iID  .'" state-id="'. $this->_iStateID .'">';
		$xml .= '<timestamp>'. htmlspecialchars(str_replace(" ", "T", $this->_timestamp), ENT_NOQUOTES) .'</timestamp>';
		$xml .= '<data>'. htmlspecialchars($this->_sDebugData, ENT_NOQUOTES) .'</data>';
		$xml  .= '</message>';

		return $xml;
	}
}
?>