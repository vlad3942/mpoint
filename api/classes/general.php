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
 * Super class for all mPoint Exceptions
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
	
	/**
	 * Re-Constructs the original URL for a 404 Document not Found HTTP Error.
	 * The method will reconstruct the absolute URL for both Internet Information Server and Apache using the 
	 * available fields from the $_SERVER super global.
	 *
	 * @return 	string
	 */
	public static function rebuildURL()
	{
		// Determine Web Server software
		switch(true)
		{
		case (stristr($_SERVER['SERVER_SOFTWARE'], "IIS") ):	// Internet Information Server
			$url = substr($_SERVER["REQUEST_URI"], strpos($_SERVER["REQUEST_URI"], "?") + 5);
			break;
		case (stristr($_SERVER['SERVER_SOFTWARE'], "Apache") ):	// Apache
			$url = strtolower(substr($_SERVER['SERVER_PROTOCOL'], 0, strpos($_SERVER['SERVER_PROTOCOL'], "/") ) ."://");
			$url .= $_SERVER['HTTP_HOST'];
			$url .= $_SERVER['REDIRECT_URL'];
			if (array_key_exists("REDIRECT_QUERY_STRING", $_SERVER) === true)
			{
				$url .= "?". $_SERVER['REDIRECT_QUERY_STRING'];
			}
			break;
		default:												// Error
			break;
		}
		
		return $url;
	}
	
	/**
	 * Instantiates the data object with the customer's Transaction Information using the data from the accessed URL.
	 * The method presumes that the customer has accessed the payment link generated by mPoint and analyzes the URL
	 * to obtain the Transaction ID and Creation Timestamp.
	 * 
	 * @see 	TxnInfo::produceInfo()
	 *
	 * @param	RDB $oDB 	Reference to the Database Object that holds the active connection to the mPoint Database
	 * @return 	TxnInfo
	 */
	public static function produceTxnInfo(RDB &$oDB)
	{
		// Re-Create global file information
		$aURLInfo = parse_url(General::rebuildURL() );
		$_SERVER['REQUEST_URI'] = $aURLInfo["path"];
		if (array_key_exists("query", $aURLInfo) === true) { $_SERVER['QUERY_STRING'] = $aURLInfo["query"]; }
		else { $_SERVER['QUERY_STRING'] = ""; }
		parse_str($_SERVER['QUERY_STRING'], $_GET);
		
		// Decode Transaction Information from URL
		list(, $chk) = explode("/", $aURLInfo["path"]);
		list($sTimestamp, $iTxnID) = spliti("Z", $chk);
		$sTimestamp = date("Y-m-d H:i:s", base_convert($sTimestamp, 32, 10) );
		$iTxnID = base_convert($iTxnID, 32, 10);
		$aTemp = array($sTimestamp);
		
		return TxnInfo::produceInfo($iTxnID, $oDB, $aTemp);
	}
	
	/**
	 * Determines the language that all payment pages should be translated into.
	 * The method will select the language by going through the following steps:
	 * 	1. Check if a language has already been determined for the Session
	 * 	2. Analyse the HTTP Header: Accept-Language provided by the customer's browser
	 * 	3. Use the language provided when the transaction was initialised
	 * 	4. Use the default system language: British English (uk)
	 * Once the language has been determined, the method will update the user session to expedite future
	 * queries.
	 * 
	 * @see 	Websession::getInfo()
	 * @see 	TxnInfo::getLanguage()
	 * @see 	sDEFAULT_LANGUAGE
	 *
	 * @return 	string
	 */
	public static function getLanguage()
	{
		switch (true)
		{
		case (isset($_SESSION) && $_SESSION['obj_Info']->getInfo("language") ):			// Language has previously been determined
			$sLang = $_SESSION['obj_Info']->getInfo("language");
			break;
		case (array_key_exists("HTTP_ACCEPT_LANGUAGE", $_SERVER) ):	// Analyse HTTP Header
			// Get language part from Browser headers
			$sLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
			// User has specified an English language
			if($sLang == "en")
			{
				// British English is available and Browser specifies language as British English
				if(stristr("gb", $_SERVER['HTTP_ACCEPT_LANGUAGE']) == true)
				{
					$sLang = "uk";
				}
				// Default to American English
				else
				{
					$sLang = "us";
				}
			}
			
			// User's selected language is unavailable
			if(is_dir(sLANGUAGE_PATH . $sLang) === false)
			{
				// Language provided when the Transaction was initialised
				if (isset($_SESSION) === true && array_key_exists("obj_TxnInfo", $_SESSION) === true)
				{
					$sLang = $_SESSION['obj_TxnInfo']->getLanguage();
				}
				// Use system default
				else { $sLang = sDEFAULT_LANGUAGE; }
			}
			break;
		case (array_key_exists("obj_TxnInfo", $_SESSION) ):			// Language provided when the Transaction was initialised
			$sLang = $_SESSION['obj_TxnInfo']->getLanguage();
			break;
		default:													// System Default
			$sLang = sDEFAULT_LANGUAGE;
			break;
		}
		// Update session
		if (isset($_SESSION) === true) { $_SESSION['obj_Info']->setInfo("language", $sLang); }
		
		return $sLang;
	}
	
	/**
	 * Determines what markup language is supported by the customer's Mobile Device using its User Agent Profile.
	 * If the method is unable to determine markup language support, it will throw an mPointException with code 1021.
	 *
	 * @param 	UAProfile $oUA 	Data object with the User Agent Profile for the Customer's Mobile Device
	 * @return 	string
	 * @throws 	mPointException
	 */
	public static function getMarkupLanguage(UAProfile &$oUA)
	{
		switch (true)
		{
		case ($oUA->hasXHTML() ):	// Mobile Device supports XHTML
			return "xhtml";
			break;
		case ($oUA->hasWML() ):		// Mobile Device supports WML
			return "wml";
			break;
		default:					// Unable to get supported Markup languages for Mobile Device
			throw new mPointException("Unable to get supported Markup languages for Mobile Device {TRACE ". var_export($oUA, true) ."}", 1021);
			break;
		}
	}
	
	/**
	 * Constructs an XML Document with general system information.
	 * The constructed XML document has the following format:
	 * 	<system>
	 *		<host>{SERVER HOST}</host>
	 *		<dir>{DIRECTORY WHERE THE SCRIPT IS LOCATED IN}</dir>
	 * 		<file>{NAME OF THE FILE THE CUSTOMER HAVE ACCESSED}</file>
	 *		<query-string>{XML ENCODED QUERY STRING}</query-string>
	 *		<session id="{USER'S SESSION ID}">{PHP'S NAME FOR THE SESSION VARIABLE}</session>
	 * 		<language>{LANGUAGE ALL PAYMENT PAGES ARE TRANSLATED INTO}</language>
	 *	</system>
	 * 
	 * @see 	sLANG
	 *
	 * @return 	string
	 */
	public function getSystemInfo()
	{
		if (array_key_exists("QUERY_STRING", $_SERVER) === false) { $_SERVER['QUERY_STRING'] = ""; }
		
		$xml = '<system>';
		$xml .= '<protocol>http</protocol>';
		$xml .= '<host>'. $_SERVER['HTTP_HOST'] .'</host>';
		$xml .= '<dir>'. dirname($_SERVER['PHP_SELF']) .'</dir>';
		$xml .= '<file>'. substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], "/")+1) .'</file>';
		$xml .= '<query-string>'. htmlspecialchars($_SERVER['QUERY_STRING'], ENT_NOQUOTES) .'</query-string>';
		$xml .= '<session id="'. session_id() .'">'. session_name() .'</session>';
		$xml .= '<language>'. sLANG .'</language>';
		$xml .= '</system>';
		
		return $xml;
	}
}
?>