<?php
/**
 * The Mobile Web package provides the business logic for mPoint's Web API.
 * This API will start a new mPoint transaction and display the "Select Credit Card" page using the compnent's Viewer.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package MobileWeb
 * @version 1.0
 */

/**
 * Model Class containing all the Business Logic for handling an mPoint Transaction from either a Mobile Internet site or an Mobile Application.
 *
 */
class MobileWeb extends General
{
	/**
	 * Data object with the Client's configuration
	 *
	 * @var ClientConfig
	 */
	private $_obj_ClientConfig;
	/**
	 * Unique ID for the Started Transaction
	 *
	 * @var integer
	 */
	private $_iTransactionID;
	
	/**
	 * Default Constructor.
	 *
	 * @param	RDB $oDB			Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param	TranslateText $oDB 	Text Translation Object for translating any text into a specific language
	 * @param 	ClientConfig $oCC 	Data object with the Client's configuration
	 */
	public function __construct(RDB &$oDB, TranslateText &$oTxt, ClientConfig &$oCC)
	{
		parent::__construct($oDB, $oTxt);
		
		$this->_obj_ClientConfig = $oCC;
	}
	
	/**
	 * Returns the Unique ID for the Started Transaction.
	 *
	 * @return integer
	 */
	protected function getTransactionID() { return $this->_iTransactionID; }
	/**
	 * Returns the Data object with the Client's configuration
	 *
	 * @return ClientConfig
	 */
	public function &getClientConfig() { return $this->_obj_ClientConfig; }
	
	/**
	 * Starts a new Transaction and generates a unique ID for the log entry.
	 * Additionally the method sets the private variable: _iTransactionID and returns the generated Transaction ID.
	 * The method will throw an mPointException with either code 1001 or 1002 if one of the database queries fails.
	 * 
	 * @see 	General::newTransaction()
	 *
	 * @param 	integer $tid 	Unique ID for the Type of Transaction that is started 
	 * @return 	integer
	 * @throws 	mPointException
	 */
	public function newTransaction($tid)
	{
		$this->_iTransactionID = parent::newTransaction($this->_obj_ClientConfig, $tid);
		
		return $this->_iTransactionID;
	}
	
/**
	 * Logs the custom variables provided by the Client for easy future retrieval.
	 * Custom variables are defined as an entry in the input arrays which key starts with var_
	 * 
	 * @see 	General::logClientVars()
	 *
	 * @param 	array $aInput 	Array of Input as received from the Client.
	 */
	public function logClientVars(array &$aInput)
	{
		parent::logClientVars($this->_iTransactionID, $aInput);
	}
}
?>