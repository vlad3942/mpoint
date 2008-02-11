<?php
/**
 * The General package provides low level functionality that are shared accross several modules and/or pages
 * Obvious choices for functionality in this class are:
 * 	- Authentication
 * 	- Access Validation
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package General
 * @version 1.0
 */

/* ==================== mPoint Exception Classes Start ==================== */
/**
 * Abstract super class for all mPoint Exceptions
 */
class mPointException extends Exception { }
/* ==================== mPoint Exception Classes End ==================== */

/**
 * General class for functionality methods which are used by several different modules or components
 *
 */
class General
{
	/**
	 * Handles the active database connection
	 *
	 * @var RDB
	 */
	private $_obj_DB;
	/**
	 * Handles the translation of text strings into a specific language
	 *
	 * @var TranslateText
	 */
	private $_obj_Txt;
	
	/**
	 * Default Constructor
	 *
	 * @param	RDB $oDB			Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param	TranslateText $oDB 	Text Translation Object for translating any text into a specific language
	 */
	public function __construct(RDB &$oDB, TranslateText &$oTxt)
	{
		$this->_obj_DB = $oDB;
		$this->_obj_Txt = $oTxt;
	}
	
	/**
	 * Returns the active Database connection.
	 *
	 * @return RDB
	 */
	protected function &getDBConn() { return $this->_obj_DB; }
	/**
	 * Returns the object for translating any text into a specific language.
	 *
	 * @return TranslateText
	 */
	protected function &getText() { return $this->_obj_Txt; }
	/**
	 * Translates message codes into messages.
	 * Both the message and the message code is returned as an XML Document in the following format:
	 * 	<messages>
	 * 		<item id="{MESSAGE CODE}">{MESSAGE TEXT}</item>
	 * 		<item id="{MESSAGE CODE}">{MESSAGE TEXT}</item>
	 * 		...
	 * </messages>
	 * The input argument can be used to differentiate between message codes in different files
	 * when translating the the message text into the appropriate language.
	 * The messages should be translated by the TranslateText module in the PHP4API
	 * using the translate dynamic text feature.
	 * The translations can be found in the custom.txt file in: webroot/text/{LANGUAGE}/custom.txt
	 * 
	 * @see 	TranslateText::_()
	 *
	 * @param 	string $type 	Type of message, used to differentiate when the same message code is used in different files with different meaning
	 * @return 	string
	 */
	public function getMessages($type)
	{
		$xml = '<messages>';
		// Message codes returned from server
		if (array_key_exists("msg", $_GET) === true)
		{
			settype($_GET['msg'], "array");
			// Loop through all returned message codes
			for ($i=0; $i<count($_GET['msg']); $i++)
			{
				$xml .= '<item id="'. $_GET['msg'][$i] .'">'. $this->_obj_Text->_($type ." - ". $_GET['msg'][$i]) .'</item>';
			}
		}
		$xml .= '</messages>';
		
		return $xml;
	}
	
	/**
	 * Returns the content of the temporary session.
	 * The session is returned as an XML Document in the following format:
	 * 	<session>
	 * 		<{NAME OF SESSION VARIABLE}>{VALUE OF SESSION VARIABLE}</{NAME OF SESSION VARIABLE}>
	 * 		<{NAME OF SESSION VARIABLE}>{VALUE OF SESSION VARIABLE}</{NAME OF SESSION VARIABLE}>
	 * 		<{NAME OF SESSION VARIABLE}>
	 * 			<item id="{ELEMENT NUMBER IN ARRAY}">{VALUE OF SESSION VARIABLE}</item>
	 * 			<item id="{ELEMENT NUMBER IN ARRAY}">{VALUE OF SESSION VARIABLE}</item>
	 * 			...
	 * 		</{NAME OF SESSION VARIABLE}>
	 * 		<{NAME OF SESSION VARIABLE}>
	 * 			<item id="{ELEMENT NUMBER IN ARRAY}">{VALUE OF SESSION VARIABLE}</item>
	 * 			<item id="{ELEMENT NUMBER IN ARRAY}">{VALUE OF SESSION VARIABLE}</item>
	 * 			...
	 * 		</{NAME OF SESSION VARIABLE}>
	 * 		...
	 * </session>
	 *
	 * @return string
	 */
	public function getSession()
	{
		$xml = '<session>';
		// Temporary Session has been set by the server
		if (array_key_exists("temp", $_SESSION) === true)
		{
			settype($_GET['temp'], "array");
			// Loop through each returned data field
			foreach ($_SESSION['temp'] as $key => $val)
			{
				// Multiple values in current data field, i.e. it's an array
				if (is_array($val) === true)
				{
					$xml .= '<'. $key .'>';
					// Loop through all array items for the session variable
					for ($i=0; $i<count($val); $i++)
					{
						$xml .= '<item id="'. $i .'">'. htmlspecialchars($val[$i], ENT_QUOTES) .'</item>';
					}
					$xml .= '</'. $key .'>';
				}
				// Single value in current data field
				else
				{
					$xml .= '<'. $key .'>'. htmlspecialchars($val, ENT_QUOTES) .'</'. $key .'>';
				}
			}
		}
		$xml .= '</session>';
		
		return $xml;
	}
	
	/**
	 * Translates a boolean flag that was retrieved from the Database into a true/false string.
	 *
	 * @param boolean $b 	Boolean flag as retrieved from the Database
	 * @return string 		"true" if flag is true, "false" if flag is false
	 */
	public function bool2xml($b)
	{
		if ($b === true)  { $b = "true"; }
		elseif ($b === false)  { $b = "false"; }
		elseif (empty($b) === true) { $b = "false"; }
		elseif ($b == "f")  { $b = "false"; }
		else { $b = "true"; }
		
		return $b;
	}
	
	/**
	 * Translaters an XML boolean (true/false string) into a PHP boolean.
	 *
	 * @param string $b 	String with XML boolean string
	 * @return boolean 		true if string is "true" or "yes", false if string is "false" or "no"
	 */
	public function xml2bool($b)
	{
		if ($b == "true")  { $b = true; }
		elseif ($b == "yes")  { $b = true; }
		elseif ($b == "false")  { $b = false; }
		elseif ($b == "no")  { $b = true; }
		
		return $b;
	}
}
?>