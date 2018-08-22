<?php
/**
 * The General package provides low level functionality that are shared accross several modules and/or pages
 * Obvious choices for functionality in this class are:
 * 	- Authentication
 * 	- Access Validation
 * 	- Logging
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package General
 * @version 1.11
 */

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
		// Enable Timestamp compatibility for Oracle
		if ( ($this->_obj_DB instanceof Oracle) === true)
		{
			$this->_obj_DB->query("ALTER SESSION SET nls_timestamp_format = 'YYYY-MM-DD HH24:MI:SS.FF9'");
		}
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
	 * 	</messages>
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
           // settype($_GET['msg'], "array");
			// Loop through all returned message codes
			for ($i=0; $i<count($_GET['msg']); $i++)
			{
				$xml .= '<item id="'. $_GET['msg'][$i] .'">'. htmlspecialchars($this->_obj_Txt->_($type ." - ". $_GET['msg'][$i]), ENT_NOQUOTES) .'</item>';
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
					foreach ($val as $k => $v)
					{
						$xml .= '<item id="'. $k .'">'. htmlspecialchars($v, ENT_QUOTES) .'</item>';
					}
					$xml .= '</'. $key .'>';
				}
				// Single value in current data field
				elseif (is_object($val) === false)
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
	public static function bool2xml($b)
	{
		if ($b === true)  { $b = "true"; }
		elseif ($b === false)  { $b = "false"; }
		elseif (empty($b) === true) { $b = "false"; }
		elseif ($b == "f")  { $b = "false"; }
		else { $b = "true"; }

		return $b;
	}

	/**
	 * Translates an XML boolean (true/false string) into a PHP boolean.
	 *
	 * @param string $b 	String with XML boolean string
	 * @return boolean 		true if string is "true", "yes" or "1", false if string is "false", "no" or "0"
	 */
	public static function xml2bool($b)
	{
		if ($b == "true") { $b = true; }
		elseif ($b == "yes") { $b = true; }
		elseif (strval($b) == "1") { $b = true; }
		elseif ($b == "false") { $b = false; }
		elseif ($b == "no") { $b = true; }
		elseif (strval($b) == "0") { $b = false; }
		elseif (empty($b) === true) { $b = false; }

		return $b;
	}

	/**
	 * Instantiates the data object with the customer's Transaction Information using the data from the accessed URL.
	 * The method presumes that the customer has accessed the payment link generated by mPoint in the format:
	 * 	{TIMESTAMP}Z{TRANSACTION ID}
	 * Both parts of the checksum MUST have been converted to base32.
	 *
	 * @see 	TxnInfo::produceInfo()
	 *
	 * @param	RDB $oDB 		Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param	string $chk 	Checksum generated by mPoint as part of the Payment Link
	 * @return 	TxnInfo
	 */
	public static function produceTxnInfo(RDB &$oDB, $chk)
	{
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
	 * 	2. Check if the Client has specified a language as part of the Transaction data
	 * 	3. Analyse the HTTP Header: Accept-Language provided by the customer's browser
	 * 	4. Use the language provided when the transaction was initialised
	 * 	5. Use the default system language: British English (gb)
	 * Once the language has been determined, the method will update the user session to expedite future
	 * queries.
	 *
	 * @see 	sDEFAULT_LANGUAGE
	 * @see 	sLANGUAGE_PATH
	 * @see 	Websession::getInfo()
	 * @see 	TxnInfo::getLanguage()
	 *
	 * @return 	string
	 */
	public static function getLanguage()
	{
		// Language provided by Client as part of the Transaction data
		if (array_key_exists("language", $_REQUEST) === true && empty($_REQUEST['language']) === false
				&& is_dir(sLANGUAGE_PATH ."/". $_REQUEST['language']) === true)
		{
			$sLang = $_REQUEST['language'];
		}
		// Language has previously been determined
		elseif (isset($_SESSION) === true && $_SESSION['obj_Info']->getInfo("language") !== false)
		{
			$sLang = $_SESSION['obj_Info']->getInfo("language");
		}
		// Analyse HTTP Header
		elseif (array_key_exists("HTTP_ACCEPT_LANGUAGE", $_SERVER) === true)
		{
			/* ========== Determine Language from HTTP Header Start ========== */
			// Open current directory
			$dh = opendir(sLANGUAGE_PATH);
			// Directory opened successfully
			if (is_resource($dh) === true)
			{
				// Lopp through files in directory
				while ( ($dir = readdir($dh) ) !== false && isset($sLang) === false)
				{
					// Current entry is a directory with a language translation
					if ($dir != "." && $dir != ".." && is_dir(sLANGUAGE_PATH ."/". $dir) === true)
					{
						// Language directory found in HTTP Header
						if (stristr($_SERVER['HTTP_ACCEPT_LANGUAGE'], $dir) == true)
						{
							$sLang = $dir;
						}
					}
				}
				closedir($dh);
			}
			/* ========== Determine Language from HTTP Header End ========== */
			
			/* ========== Determine Configuration Start ========== */
			// User's selected language is unavailable as a translation
			if (isset($sLang) === false)
			{
				// Customer has set the Mobile Device to English
				if (stristr($_SERVER['HTTP_ACCEPT_LANGUAGE'], "en") == true)
				{
					// Language provided when the Transaction was initialised is either British or American English
					if (isset($_SESSION) === true && array_key_exists("obj_TxnInfo", $_SESSION) === true
						&& ($_SESSION['obj_TxnInfo']->getLanguage() == "gb" || $_SESSION['obj_TxnInfo']->getLanguage() == "us") )
					{
						$sLang = $_SESSION['obj_TxnInfo']->getLanguage();
					}
					// Default to British English
					else { $sLang = "gb"; }
				}
				// Language has been provided when the Transaction was initialised
				elseif (isset($_SESSION) === true && array_key_exists("obj_TxnInfo", $_SESSION) === true)
				{
					$sLang = $_SESSION['obj_TxnInfo']->getLanguage();
				}
				// Use system default
				else { $sLang = sDEFAULT_LANGUAGE; }
			}
			/* ========== Determine Configuration End ========== */
		}
		// Language provided when the Transaction was initialised
		elseif (isset($_SESSION) === true && array_key_exists("obj_TxnInfo", $_SESSION) === true)
		{
			$sLang = $_SESSION['obj_TxnInfo']->getLanguage();
		}
		// System Default
		else
		{
			$sLang = sDEFAULT_LANGUAGE;
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
	 * @param 	TxnInfo $oTI 	Data object with the customer's Transaction Info
	 * @return 	string
	 * @throws 	mPointException
	 */
	public static function getMarkupLanguage(UAProfile &$oUA, TxnInfo &$oTI=null)
	{
		if (is_null($oTI) === false && ($oTI->getMarkupLanguage() == "html5" || $oTI->getMarkupLanguage() == "app") )
		{
			return $oTI->getMarkupLanguage();
		}
		else
		{
			switch (true)
			{
			case (eregi('iPhone', $_SERVER['HTTP_USER_AGENT']) ):	// Mobile Device supports HTML5
			case (eregi('iPod', $_SERVER['HTTP_USER_AGENT']) ):
			case (eregi('Android', $_SERVER['HTTP_USER_AGENT']) ):
				return (is_null($oTI) === false ? $oTI->getMarkupLanguage() : "xhtml");
				break;
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
    public function getSystemInfo($protocol)
    {
        $protocol = isset($protocol) === true ? $protocol : "http";
		if (array_key_exists("QUERY_STRING", $_SERVER) === false) { $_SERVER['QUERY_STRING'] = ""; }
	switch (true)
		{
		case eregi("iPod", $_SERVER['HTTP_USER_AGENT']):
		case eregi("iPhone", $_SERVER['HTTP_USER_AGENT']):
			$platform = "iPhone";
			break;
		case eregi("iPad", $_SERVER['HTTP_USER_AGENT']):
			$platform = "iPad";
			break;
		case eregi("Firefox", $_SERVER['HTTP_USER_AGENT']):
			$platform = "Firefox";
			break;
		case eregi("Skyfire", $_SERVER['HTTP_USER_AGENT']):
			$platform = "Skyfire";
			break;
		case eregi("Android", $_SERVER['HTTP_USER_AGENT']):
			$platform = "Android";
			break;
		default:
			$platform = "Unknown";
			break;
		}
		$dir = str_replace("\\", "/", dirname($_SERVER['PHP_SELF']) );
		if (substr($dir, -1) != "/") { $dir .= "/"; }
		$xml = '<system>';
		$xml .= '<protocol>'.$protocol.'</protocol>';
		$xml .= '<host>'. $_SERVER['HTTP_HOST'] .'</host>';
		$xml .= '<dir>'. $dir .'</dir>';
		$xml .= '<file>'. substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], "/")+1) .'</file>';
		$xml .= '<query-string>'. htmlspecialchars($_SERVER['QUERY_STRING'], ENT_NOQUOTES) .'</query-string>';
		$xml .= '<language>'. sLANG .'</language>';
		$xml .= '<spinner format="base64">'. base64_encode(file_get_contents($_SERVER['DOCUMENT_ROOT'] ."/img/loader.gif") ) .'</spinner>';
		$xml .= '<loading>'. $this->getText()->_("Loading...") .'</loading>';
		$xml .= '<platform>'. $platform .'</platform>';
		$xml .= '</system>';

		return $xml;
	}

	/**
	 * Starts a new Transaction and generates a unique ID for the log entry.
	 * Additionally the method sets the private variable: _iTransactionID and returns the generated Transaction ID.
	 * The method will throw an mPointException with either code 1001 or 1002 if one of the database queries fails.
	 *
	 * @param 	ClientConfig $oCC 	Data object with the Client's configuration
	 * @param 	integer $tid 		Unique ID for the Type of Transaction that is started
	 * @return 	integer
	 * @throws 	mPointException
	 */
	public function newTransaction(ClientConfig &$oCC, $tid)
	{
		$sql = "SELECT Nextvalue('Log".sSCHEMA_POSTFIX.".Transaction_Tbl_id_seq') AS id FROM DUAL";
		$RS = $this->getDBConn()->getName($sql);
		// Error: Unable to generate a new Transaction ID
		if (is_array($RS) === false) { throw new mPointException("Unable to generate new Transaction ID", 1001); }

		//Will take ip on the basis of interface used for accessing project.
		if(php_sapi_name() == "cli")
		{
			$ip = gethostbyname(gethostname());
		}  else if (array_key_exists("HTTP_X_FORWARDED_FOR", $_SERVER) === true)
		{
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}else if(isset($_SERVER['REMOTE_ADDR']) == true)
        {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

		$sql = "INSERT INTO Log".sSCHEMA_POSTFIX.".Transaction_Tbl
					(id, typeid, clientid, accountid, countryid, keywordid, \"mode\", ip,cssurl,logourl)
				VALUES
					(". $RS["ID"] .", ". intval($tid) .", ". $oCC->getID() .", ". $oCC->getAccountConfig()->getID() .", ". $oCC->getCountryConfig()->getID() .", ". $oCC->getKeywordConfig()->getID() .", ". $oCC->getMode() .", '". $this->getDBConn()->escStr($ip) ."','". $oCC->getCSSURL()."','". $oCC->getLogoURL()."')";
//		echo $sql ."\n";
		// Error: Unable to insert a new record in the Transaction Log
		if (is_resource($this->getDBConn()->query($sql) ) === false)
		{
			if (is_array($RS) === false) { throw new mPointException("Unable to insert new record for Transaction: ". $RS["ID"], 1002); }
		}

		return $RS["ID"];
	}

	/**
	 * Updates the Transaction Log record for the provided transaction with all data.
	 * The method will throw an mPointException with code 1004 if the database update fails.
	 *
	 * @param 	TxnInfo $oTI 	Data Object for the Transaction which should be updated
	 * @throws 	mPointException
	 */
	public function logTransaction(TxnInfo &$oTI)
	{
		$sql = "UPDATE Log".sSCHEMA_POSTFIX.".Transaction_Tbl
				SET typeid = ". $oTI->getTypeID() .", clientid = ". $oTI->getClientConfig()->getID() .", accountid = ". $oTI->getClientConfig()->getAccountConfig()->getID() .",
					countryid = ". $oTI->getCountryConfig()->getID() .",currencyid = ". $oTI->getCurrencyConfig()->getID().", keywordid = ". $oTI->getClientConfig()->getKeywordConfig()->getID() .",
					amount = ". $oTI->getAmount() .", points = ". ($oTI->getPoints() > 0 ? $oTI->getPoints() : "NULL") .", reward = ". ($oTI->getReward() > 0 ? $oTI->getReward() : "NULL") .",
					orderid = '". $this->getDBConn()->escStr($oTI->getOrderID() ) ."', lang = '". $this->getDBConn()->escStr($oTI->getLanguage() ) ."',
					mobile = ". floatval($oTI->getMobile() ) .", operatorid = ". $oTI->getOperator() .", email = '". $this->getDBConn()->escStr($oTI->getEMail() ) ."',
					logourl = '". $this->getDBConn()->escStr($oTI->getLogoURL() ) ."', cssurl = '". $this->getDBConn()->escStr($oTI->getCSSURL() ) ."',
					accepturl = '". $this->getDBConn()->escStr($oTI->getAcceptURL() ) ."', cancelurl = '". $this->getDBConn()->escStr($oTI->getCancelURL() ) ."',
					callbackurl = '". $this->getDBConn()->escStr($oTI->getCallbackURL() ) ."', iconurl = '". $this->getDBConn()->escStr($oTI->getIconURL() ) ."',
					authurl = '". $this->getDBConn()->escStr($oTI->getAuthenticationURL() ) ."', customer_ref = '". $this->getDBConn()->escStr($oTI->getCustomerRef() ) ."',
					gomobileid = ". $oTI->getGoMobileID() .", auto_capture = '". ($oTI->useAutoCapture() === true ? "1" : "0") ."', markup = '". $this->getDBConn()->escStr($oTI->getMarkupLanguage() ) ."',
					description = '". $this->getDBConn()->escStr($oTI->getDescription() ) ."',
					deviceid = '". $this->getDBConn()->escStr($oTI->getDeviceID()) ."', attempt = ".intval($oTI->getAttemptNumber()) .", producttype = ".intval($oTI->getProductType());
		if (strlen($oTI->getIP() ) > 0) { $sql .= " , ip = '". $this->getDBConn()->escStr( $oTI->getIP() ) ."'"; }
		if ($oTI->getAccountID() > 0) { $sql .= ", euaid = ". $oTI->getAccountID(); }
		elseif ($oTI->getAccountID() == -1) { $sql .= ", euaid = NULL"; }
		$sql .= "
				WHERE id = ". $oTI->getID();
//		echo $sql ."\n";
		// Error: Unable to update Transaction
		if (is_resource($this->getDBConn()->query($sql) ) === false)
		{
			throw new mPointException("Unable to update Transaction: ". $oTI->getID(), 1004);
		}
	}
	
	public function newAssociatedTransaction(TxnInfo &$oTI)
	{
		$iTxnID = $this->newTransaction($oTI->getClientConfig(), $oTI->getTypeID());
		
		$iSessionId = $oTI->getSessionId() ;
		
		$sql = "UPDATE Log".sSCHEMA_POSTFIX.".Transaction_Tbl
				SET sessionid = ".$iSessionId." WHERE id=".$iTxnID ;
		
		if (is_resource($this->getDBConn()->query($sql) ) === false)
		{
			throw new mPointException("Unable to update associated transaction: ". $iTxnID. " of original transaction: ".$oTI->getID(), 1004);
		}
		return $iTxnID ;
	}

	/**
	 * Adds a new entry to the Message log with the provided debug data.
	 * The method will throw an mPointException with code 1003 if the database query fails.
	 *
	 * @param 	integer $txnid 	Unique ID of the Transaction the Message should be logged for
	 * @param 	integer $sid 	Unique ID of the State that the data is associated with
	 * @param 	string $data 	Debug data to associate with the state
	 * @throws 	mPointException
	 */
	public function newMessage($txnid, $sid, $data)
	{
		// Use prepared statement to support log entries with more than 4000 characters of debug data on Oracle
		$sql = "INSERT INTO Log".sSCHEMA_POSTFIX.".Message_Tbl
					(txnid, stateid, data)
				VALUES
					($1, $2, $3)";
//		echo $sql ."\n";
		$res = $this->getDBConn()->prepare($sql);
		if (is_resource($res) === true)
		{
			$aParams = array($txnid, $sid, $data);
			if ($this->getDBConn()->execute($res, $aParams) === false)
			{
				throw new mPointException("Unable to insert new message for Transaction: ". $txnid ." and State: ". $sid, 1003);
			}
		}
		else { throw new mPointException("Unable to insert new message for Transaction: ". $txnid ." and State: ". $sid, 1003); }
	}
	
	/**
	 * Create a new transaction with same session id as the original transaction,
	 * and authorize the new transaction using secondary PSP as part of Dynamic Routing
	 * 
	 * @param TxnInfo $obj_TxnInfo
	 * @param unknown $iSecondaryRoute
	 * @return string
	 */
	public function authWithSecondaryPSP(TxnInfo $obj_TxnInfo ,$iSecondaryRoute ,$aHTTP_CONN_INFO, $obj_Elem )
	{
		$xml = "" ;
		$obj_PSPConfig = PSPConfig::produceConfig ( $this->getDBConn(), $obj_TxnInfo->getClientConfig ()->getID (), $obj_TxnInfo->getClientConfig ()->getAccountConfig ()->getID (), $iSecondaryRoute );
	    $iAssociatedTxnId = $this->newAssociatedTransaction ( $obj_TxnInfo );
	    $data = array();
	    
	    $data['amount'] = $obj_TxnInfo->getAmount();
	    $data['country-config'] = $obj_TxnInfo->getCountryConfig();
	    $data['currency-config'] = $obj_TxnInfo->getCurrencyConfig();
	    $data['orderid'] = $obj_TxnInfo->getOrderID();
	    $data['mobile'] = $obj_TxnInfo->getMobile();
	    $data['operator'] = $obj_TxnInfo->getOperator();
	    $data['email'] = $obj_TxnInfo->getEMail();
	    $data['device-id'] = $obj_TxnInfo->getDeviceID();
	    $data['markup'] = $obj_TxnInfo->getMarkupLanguage();
	    $data['orderid'] = $obj_TxnInfo->getOrderID();
	    $data['sessionid'] = $obj_TxnInfo->getSessionId();
	    
		$obj_AssociatedTxnInfo = TxnInfo::produceInfo( (integer) $iAssociatedTxnId, $this->getDBConn(),$obj_TxnInfo->getClientConfig(),$data);
		
		$obj_second_PSP = Callback::producePSP ( $this->getDBConn(), $_OBJ_TXT, $obj_AssociatedTxnInfo, $aHTTP_CONN_INFO, $obj_PSPConfig );
		
		$code = $obj_second_PSP->authorize( $obj_PSPConfig, $obj_Elem );
		if ($code == "100") {
			$xml .= '<status code="100">Payment Authorized Using Stored Card</status>';
		} else if ($code == "2000") {
			$xml .= '<status code="2000">Payment authorized</status>';
		} else if (strpos ( $code, '2005' ) !== false) {
			header ( "HTTP/1.1 303" );
			$xml .= $code;
		} else {
			$xml .= '<status code="92">Authorization failed, ' . $obj_PSPConfig->getName () . ' returned error: ' . $code . '</status>';
		}
	
		return $xml ;
			
	}

	/**
	 * Retrieves the data for a given transaction state from the Message database table.
	 * The retrieved data is unserialised before being returned.
	 * 
	 * A START TRANSACTION should be issued prior to calling this method if it is used to serialize requests by passing TRUE
	 * as the third parameter and a COMMIT / ROLLBACK issued once serialization is no longer needed.
	 *
	 * @see 	unserialize()
	 *
	 * @param 	integer $txnid 		ID of the Transaction that message data should be retrieved from
	 * @param 	integer $stateid 	ID of the State to which the data belongs
	 * @param	boolean $serialize	Serialize the transaction using a FOR UPDATE, defaults to false
	 * @return 	array
	 */
	public function getMessageData($txnid, $stateid, $serialize=false)
	{
		$sql = "SELECT data
				FROM Log".sSCHEMA_POSTFIX.".Message_Tbl
				WHERE txnid = ". intval($txnid) ." AND stateid = ". intval($stateid) ."
				ORDER BY id DESC";
		// Serialize the transaction using a FOR UPDATE
		if ($serialize === true)
		{
			$sql .= "
					FOR UPDATE";
		}
//		echo $sql ."\n";
		$RS = @$this->getDBConn()->getName($sql);
		$data = array();
		if (is_array($RS) === true)
		{
			if ($stateid == Constants::iCLIENT_VARS_STATE)
			{
				$data = unserialize(base64_decode($RS["DATA"]) );
			}
			else
			{
				$RS["DATA"] = utf8_decode($RS["DATA"]);
				$data = @unserialize($RS["DATA"]);
			}
			if ($data === false) { $data = array($RS["DATA"]); }
		}

		return $data;
	}
	
	/**
	 * Deletes a message state from the database, use with care!!
	 * This method MUST ONLY be called to remove state: iPAYMENT_WITH_ACCOUNT_STATE if authorization with the
	 * Payment Service Provider fails.
	 * 
	 * @see		Constants::iPAYMENT_WITH_ACCOUNT_STATE
	 *
	 * @param 	integer $txnid 	Unique ID of the Transaction the Message should be deleted for
	 * @param 	integer $sid 	Unique ID of the State that the deleted Message is associated with
	 * @return 	boolean
	 */
	public function delMessage($txnid, $sid)
	{
		$sql = "DELETE FROM Log".sSCHEMA_POSTFIX.".Message_Tbl
				WHERE txnid = ". intval($txnid) ." AND stateid = ". intval($sid);
//		echo $sql ."\n";

		return is_resource($this->getDBConn()->query($sql) );
	}

	/**
	 *	Will purge the Log.Message_Tbl for all expired logs.
	 *	That are older then NOW() minus the given number of days. 
	 *
	 * @param 	$days	The Number of days before the log expires 
	 * 
	 * @return integer	Number of affected rows.
	 */
	
	public function purgeMessageLogs($days)
	{
		$iAffectedRows = 0;
		
		$timeStamp = date("Y-m-d", strtotime('-'.$days .' days', time() ) );
		
		$sql = "DELETE FROM Log".sSCHEMA_POSTFIX.".Message_Tbl
				WHERE stateid IN (".Constants::iPSP_PAYMENT_REQUEST_STATE.", ". Constants::iPSP_PAYMENT_RESPONSE_STATE.") 
					AND created <  DATE '". $this->getDBConn()->escStr($timeStamp) ."' AND data IS NOT NULL";

//		echo $sql ."\n";
		$res  = $this->getDBConn()->query($sql);
		if (is_resource($res) === true)
		{
			$iAffectedRows = $this->getDBConn()->countAffectedRows($res);
		
			$sql = "UPDATE Log".sSCHEMA_POSTFIX.".Message_Tbl
					SET data = NULL
					WHERE created < DATE '". $this->getDBConn()->escStr($timeStamp) ."' AND data IS NOT NULL";

//			echo $sql ."\n";	
			$res  = $this->getDBConn()->query($sql);
			if (is_resource($res) === true) { $iAffectedRows += $this->getDBConn()->countAffectedRows($res); }
		}
		
		return $iAffectedRows;
	}
	
	/**
	 *	Will purge the Log.AuditLog_Tbl for all expired logs.
	 *  That are older then NOW() minus the given number of days.
	 *
	 * @param 	$days	The Number of days before a log expires
	 *
	 * @return integer	Number of affected rows.
	 */
	
	public function purgeAuditLogs($days)
	{
		$iAffectedRows = 0;
	
		$timeStamp = date("Y-m-d", strtotime('-'.$days .' days', time() ) );
	
		$sql = "DELETE FROM Log".sSCHEMA_POSTFIX.".AuditLog_Tbl
				WHERE created <  DATE '". $this->getDBConn()->escStr($timeStamp) ."'";
		//		echo $sql ."\n";
		$res  = $this->getDBConn()->query($sql);
		if (is_resource($res) === true) { $iAffectedRows = $this->getDBConn()->countAffectedRows($res); }
	
		return $iAffectedRows;
	}
	/**
	 * Sends an MT to GoMobile
	 * Prior to sending the message the method will updated the provided Connection Info object with the Client's username / password for GoMobile.
	 * The method will throw an mPointException with an error code in the following scenarios:
	 * 	1012. Message rejected by GoMobile
	 * 	1013. Unable to connect to GoMobile
	 *
	 * @see 	GoMobileClient
	 * @see 	SMS
	 *
	 * @param 	GoMobileConnInfo $oCI 	Reference to the data object with the Connection Info required to communicate with GoMobile
	 * @param 	SMS $oMI 				Reference to the Message Object for holding the message data which will be sent to GoMobile
	 * @param 	TxnInfo $oTI 			Reference to the data object holding the Transaction for which an MT should be send out
	 * @throws 	mPointException
	 */
	public function sendMT(GoMobileConnInfo &$oCI, SMS &$oMI, TxnInfo &$oTI)
	{
		// Re-Instantiate Connection Information for GoMobile using the Client's username / password
		$oCI = new GoMobileConnInfo($oCI->getProtocol(), $oCI->getHost(), $oCI->getPort(), $oCI->getTimeout(), $oCI->getPath(), $oCI->getMethod(), $oCI->getContentType(), $oTI->getClientConfig()->getUsername(), $oTI->getClientConfig()->getPassword(), $oCI->getLogPath(), $oCI->getMode() );

		// Instantiate client object for communicating with GoMobile
		$obj_GoMobile = new GoMobileClient($oCI);

		/* ========== Send MT Start ========== */
		$bSend = true;		// Continue to send messages
		$iAttempts = 0;		// Number of Attempts
		// Send messages
		while ($bSend === true && $iAttempts < 3)
		{
			$iAttempts++;
			try
			{
				// Error: Message rejected by GoMobile
				if ($obj_GoMobile->communicate($oMI) != 200)
				{
					$this->newMessage($oTI->getID(), Constants::iMSG_REJECTED_BY_GM_STATE, var_export($oMI, true) );
					throw new mPointException("Message rejected by GoMobile with code(s): ". $oMI->getReturnCodes(), 1012);
				}
				$this->newMessage($oTI->getID(), Constants::iMSG_ACCEPTED_BY_GM_STATE, var_export($oMI, true) );
				$bSend = false;
			}
			// Communication error, retry message sending
			catch (HTTPException $e)
			{
				// Error: Unable to connect to GoMobile
				if ($iAttempts == 3)
				{
					$this->newMessage($oTI->getID(), Constants::iGM_CONN_FAILED_STATE, var_export($oCI, true) );
					throw new mPointException("Unable to connect to GoMobile", 1013);
				}
				else { sleep(pow(5, $iAttempts) ); }
			}
		}
		/* ========== Send MT End ========== */
	}

	/**
	 * Formats the Total Amount for a Transaction into humanreadable format.
	 * The method will divide the amount by 100 and format it using the price format of the provided Country,
	 * i.e. $X.XX for USA, X,XXkr for Denmark etc.
	 *
	 * @param 	CountryConfig $oCC 	Reference to the Data Object for the Country Configuration that should be used for formatting the Amount
	 * @param 	integer $amount 	Amount to format
	 * @return 	string
	 */
	public static function formatAmount(CountryConfig &$oCC, $amount)
	{
		// Format amount to be human readable
		$sPrice = $oCC->getPriceFormat();
		$sPrice = str_replace("{CURRENCY}", $oCC->getSymbol(), $sPrice);
		if ($oCC->getID() == 103 || $oCC->getID() == 200) { $seperator = "."; }
		else { $seperator = ","; }
		$sPrice = str_replace("{PRICE}", number_format(floatval($amount) / floatval(100), $oCC->getDecimals(), $seperator, ""), $sPrice);

		return $sPrice;
	}

	/**
	 * Construct standard mPoint HTTP Headers for notifying the Client via HTTP.
	 *
	 * @return string
	 */
	public function constHTTPHeaders()
	{
		/* ----- Construct HTTP Header Start ----- */
		$h = "{METHOD} {PATH} HTTP/1.0" .HTTPClient::CRLF;
		$h .= "host: {HOST}" .HTTPClient::CRLF;
		$h .= "referer: {REFERER}" .HTTPClient::CRLF;
		$h .= "content-length: {CONTENTLENGTH}" .HTTPClient::CRLF;
		$h .= "content-type: {CONTENTTYPE}; charset=UTF-8" .HTTPClient::CRLF;
		$h .= "user-agent: mPoint" .HTTPClient::CRLF;
		/* ----- Construct HTTP Header End ----- */

		return $h;
	}

	/**
	 * Constructs an MT and sends it to GoMobile.
	 * Prior to sending the message the method will updated the provided Connection Info object with the Client's username / password for GoMobile.
	 * Additionally the method will determine from the customer's Mobile Network Operator whether to send an MT-WAP Push (default) or an MT-SMS
	 * with the link embedded.
	 * The method will throw an mPointException with an error code in the following scenarios:
	 * 	1011. Operator not supported
	 * 	1012. Message rejected by GoMobile
	 * 	1013. Unable to connect to GoMobile
	 *
	 * @see 	GoMobileClient
	 * @see 	Constants::iMT_SMS_TYPE
	 * @see 	Constants::iMT_WAP_PUSH_TYPE
	 * @see 	Constants::iMT_PRICE
	 * @see 	General::newMessage()
	 *
	 * @param 	GoMobileConnInfo $oCI 	Connection Info required to communicate with GoMobile
	 * @param 	TxnInfo $oTI 			Data Object for the Transaction for which an MT with the payment link should be send out
	 * @param 	string $url 			Absolute URL to mPoint that will be sent to the customer
	 * @throws 	mPointException
	 */
	public function sendLink(GoMobileConnInfo &$oCI, TxnInfo &$oTI, $url)
	{
		switch ($oTI->getOperator() )
		{
		case (20002):	// Verizon Wireless - USA
		case (20005):	// Nextel - USA
		case (20006):	// Boost - USA
		case (20007):	// Alltel - USA
		case (20010):	// US Cellular - USA
			$this->newMessage($oTI->getID(), Constants::iUNSUPPORTED_OPERATOR, var_export($obj_MsgInfo, true) );
			throw new mPointException("Operator: ". $oTI->getOperator() ." not supported", 1011);
			break;
		case (20004):	// Sprint - USA
		case (13003):	// 3 - UK
			$sBody = $this->getText()->_("mPoint - Embedded link Indication") ."\n". $url;
			$sBody = str_replace("{CLIENT}", $oTI->getClientConfig()->getName(), $sBody);
			// Instantiate Message Object for holding the message data which will be sent to GoMobile
			$obj_MsgInfo = GoMobileMessage::produceMessage(Constants::iMT_SMS_TYPE, $oTI->getClientConfig()->getCountryConfig()->getID(), $oTI->getOperator(), $oTI->getClientConfig()->getCountryConfig()->getChannel(), $oTI->getClientConfig()->getKeywordConfig()->getKeyword(), Constants::iMT_PRICE, $oTI->getMobile(), utf8_decode($sBody) );
			break;
		default:
			$sIndication = $this->getText()->_("mPoint - WAP Push Indication");
			$sIndication = str_replace("{CLIENT}", $oTI->getClientConfig()->getName(), $sIndication);
			// Instantiate Message Object for holding the message data which will be sent to GoMobile
			$obj_MsgInfo = GoMobileMessage::produceMessage(Constants::iMT_WAP_PUSH_TYPE, $oTI->getClientConfig()->getCountryConfig()->getID(), $oTI->getOperator(), $oTI->getClientConfig()->getCountryConfig()->getChannel(), $oTI->getClientConfig()->getKeywordConfig()->getKeyword(), Constants::iMT_PRICE, $oTI->getMobile(), utf8_decode($sIndication), $url);
			break;
		}
		$obj_MsgInfo->setDescription("mPoint - WAP Link");
		// Send Link to Customer
		$this->sendMT($oCI, $obj_MsgInfo, $oTI);
	}

	/**
	 * Authenticates user for access to the system as well as access to the current report
	 *	1000. Success: Access granted
	 *	1001. Unauthorized System Access
	 *	1002. Unauthorized Module Access
	 *
	 * @return	integer 	1000 if access is granted
	 */
	public static function val()
	{
		// Success: Access granted
		if ($_SESSION['obj_Info']->getInfo("accountid") > 0)
		{
			$code = 1000;
		}
		// Error: Unauthorized system access
		else
		{
			$code = 1001;
		}

		return $code;
	}

	/**
	 * Fetches all active countries from the database.
	 * The countries are returned as an XML Document in the following format:
	 * 	<country-configs>
	 * 		<config id="{UNIQUE ID FOR THE COUNTRY}">
	 *			<name>{NAME OF THE COUNTRY}</name>
	 *			<currency symbol="{SYMBOL USED TO REPRESENT THE CURRENCY}">{ISO-4217 CURRENCY CODE USED IN THE COUNTRY}</currency>
	 *			<max-balance>{MAX BALANCE, IN COUNTRY'S SMALLEST CURRENCY, THAT A PREPAID ACCOUNT MAY CONTAIN}</max-balance>
	 *			<min-transfer>{MIN AMOUNT WHICH MAY BE TRANSFERRED BETWEEN ACCOUNTS IN COUNTRY'S SMALLEST CURRENCY}</min-transfer>
	 *			<min-mobile>{MIN VALUE FOR A VALID MOBILE NUMBER (MSISDN) IN THE COUNTRY}</min-mobile>
	 *			<max-mobile>{MAX VALUE FOR A VALID MOBILE NUMBER (MSISDN) IN THE COUNTRY}</max-mobile>
	 *			<channel>{CHANNEL USED FOR SENDING MESSAGE'S TO AN END-USER'S MOBILE PHONE}</channel>
	 *			<price-format>{PRICE FORMAT USED IN THE COUNTRY, i.e. $XX.XX for USA and XX,XXkr FOR DENMARK}</price-format>
	 *			<decimals>{NUMBER OF DECIMALS USED WHEN DISPLAYING PRICES}</decimals>
	 *			<address-lookup>{BOOLEAN FLAG INDICATING WHETHER ADDRESS LOOKUP BASED ON A MOBILE NUMBER IS AVAILABLE IN THE COUNTRY}</address-lookup>
	 *			<double-opt-in>{BOOLEAN FLAG INDICATING WHETHER THE MOBILE NETWORK OPERATOR'S IN THE COUNTRY REQUIRE DOUBLE OPT-IN WHEN CHARGING VIA PREMIUM SMS}</double-opt-in>
	 *			<add-card-amount>{AMOUNT THAT THE END-USER'S ACCOUNT IS TOPPED UP WITH WHEN CARD DETAILS FOR A NEW CARD IS STORED}</add-card-amount>
	 *			<max-psms-amount>{MAX AMOUNT THAT CAN BE CHARGED WITH A PREMIUM SMS BASED FLOW (NO AUTHENTICATION)}</max-psms-amount>
	 *			<min-pwd-amount>{MIN AMOUNT BEFORE A TRANSACTION REQUIRES PASSWORD AUTHENTICATION}</min-pwd-amount>
	 *			<min-2fa-amount>{MIN AMOUNT BEFORE A TRANSACTION REQUIRES 2-FACTOR AUTHENTICATION}</min-2fa-amount>
	 *		</config>
	 *		<config id="{UNIQUE ID FOR THE COUNTRY}">
	 *			<name>{NAME OF THE COUNTRY}</name>
	 *			<currency symbol="{SYMBOL USED TO REPRESENT THE CURRENCY}">{ISO-4217 CURRENCY CODE USED IN THE COUNTRY}</currency>
	 *			<max-balance>{MAX BALANCE, IN COUNTRY'S SMALLEST CURRENCY, THAT A PREPAID ACCOUNT MAY CONTAIN}</max-balance>
	 *			<min-transfer>{MIN AMOUNT WHICH MAY BE TRANSFERRED BETWEEN ACCOUNTS IN COUNTRY'S SMALLEST CURRENCY}</min-transfer>
	 *			<min-mobile>{MIN VALUE FOR A VALID MOBILE NUMBER (MSISDN) IN THE COUNTRY}</min-mobile>
	 *			<max-mobile>{MAX VALUE FOR A VALID MOBILE NUMBER (MSISDN) IN THE COUNTRY}</max-mobile>
	 *			<channel>{CHANNEL USED FOR SENDING MESSAGE'S TO AN END-USER'S MOBILE PHONE}</channel>
	 *			<price-format>{PRICE FORMAT USED IN THE COUNTRY, i.e. $XX.XX for USA and XX,XXkr FOR DENMARK}</price-format>
	 *			<decimals>{NUMBER OF DECIMALS USED WHEN DISPLAYING PRICES}</decimals>
	 *			<address-lookup>{BOOLEAN FLAG INDICATING WHETHER ADDRESS LOOKUP BASED ON A MOBILE NUMBER IS AVAILABLE IN THE COUNTRY}</address-lookup>
	 *			<double-opt-in>{BOOLEAN FLAG INDICATING WHETHER THE MOBILE NETWORK OPERATOR'S IN THE COUNTRY REQUIRE DOUBLE OPT-IN WHEN CHARGING VIA PREMIUM SMS}</double-opt-in>
	 *			<add-card-amount>{AMOUNT THAT THE END-USER'S ACCOUNT IS TOPPED UP WITH WHEN CARD DETAILS FOR A NEW CARD IS STORED}</add-card-amount>
	 *			<max-psms-amount>{MAX AMOUNT THAT CAN BE CHARGED WITH A PREMIUM SMS BASED FLOW (NO AUTHENTICATION)}</max-psms-amount>
	 *			<min-pwd-amount>{MIN AMOUNT BEFORE A TRANSACTION REQUIRES PASSWORD AUTHENTICATION}</min-pwd-amount>
	 *			<min-2fa-amount>{MIN AMOUNT BEFORE A TRANSACTION REQUIRES 2-FACTOR AUTHENTICATION}</min-2fa-amount>
	 *		</config>
	 *		...
	 * 	</country-configs>
	 *
	 * @return 	xml
	 */
	public function getCountryConfigs()
	{
		$sql = "SELECT id, name, currency, symbol, maxbalance, mintransfer, minmob, maxmob, channel, priceformat, decimals,
					addr_lookup, doi, add_card_amount, max_psms_amount, min_pwd_amount, min_2fa_amount
				FROM System".sSCHEMA_POSTFIX.".Country_Tbl
				WHERE enabled = '1'
				ORDER BY name ASC";
//		echo $sql ."\n";
		$res = $this->getDBConn()->query($sql);

		$xml = '<country-configs>';
		while ($RS = $this->getDBConn()->fetchName($res) )
		{
			$xml .= '<config id="'. $RS["ID"] .'">';
			$xml .= '<name>'. htmlspecialchars($RS["NAME"], ENT_NOQUOTES) .'</name>';
			$xml .= '<currency symbol="'. utf8_encode($RS["SYMBOL"]) .'">'. $RS["CURRENCY"] .'</currency>';
			$xml .= '<max-balance>'. $RS["MAXBALANCE"] .'</max-balance>';
			$xml .= '<min-transfer>'. $RS["MINTRANSFER"] .'</min-transfer>';
			$xml .= '<min-mobile>'. $RS["MINMOB"] .'</min-mobile>';
			$xml .= '<max-mobile>'. $RS["MAXMOB"] .'</max-mobile>';
			$xml .= '<channel>'. $RS["CHANNEL"] .'</channel>';
			$xml .= '<price-format>'. $RS["PRICEFORMAT"] .'</price-format>';
			$xml .= '<decimals>'. $RS["DECIMALS"] .'</decimals>';
			$xml .= '<address-lookup>'. General::bool2xml($RS["ADDR_LOOKUP"]) .'</address-lookup>';
			$xml .= '<double-opt-in>'. General::bool2xml($RS["DOI"]) .'</double-opt-in>';
			$xml .= '<add-card-amount>'. $RS["ADD_CARD_AMOUNT"] .'</add-card-amount>';
			$xml .= '<max-psms-amount>'. $RS["MAX_PSMS_AMOUNT"] .'</max-psms-amount>';
			$xml .= '<min-pwd-amount>'. $RS["MIN_PWD_AMOUNT"] .'</min-pwd-amount>';
			$xml .= '<min-2fa-amount>'. $RS["MIN_2FA_AMOUNT"] .'</min-2fa-amount>';
			$xml .= '</config>';
		}
		$xml .= '</country-configs>';

		return $xml;
	}

	/**
	 * Attempts to determine the user's country based on the IP address.
	 * The method will return one of the following:
	 *     0. Country not found
	 *	  -1. IP address is invalid
	 * 	100+. Unique ID of the user's country based on the IP address
	 * The method will automatically use the IP Address found in $_SERVER['REMOTE_ADDR'] if no address parameter
	 * is passed.
	 *
	 * @see		http://dk.php.net/manual/en/reserved.variables.server.php
	 *
	 * @param	string		IP address that should be used to determine the user's country. Defaults to $_SERVER['REMOTE_ADDR'].
	 * @return	integer 	   0. Country not found
	 * 						  -1. IP address is invalid
	 * 						100+. Unique ID of the user's country based on the IP address
	 */
	public function getCountryFromIP($ip="")
	{
		if (empty($ip) === true) { $ip = $_SERVER['REMOTE_ADDR']; }
		$ip = ip2long($ip);

		if ($ip !== false)
		{
			$sql = "SELECT countryid
					FROM System".sSCHEMA_POSTFIX.".IPRange_Tbl
					WHERE min <= ". $ip ." AND max >= ". $ip;
//			echo nl2br($sql);
			$RS = $this->getDBConn()->getName($sql);
			// Unable to determine country based on IP Address
			if (is_array($RS) === false) { $RS["COUNTRYID"] = 0; }
		}
		else { $RS["COUNTRYID"] = -1; }

		return $RS["COUNTRYID"];
	}
	
	/**
	 * Logs the custom variables provided by the Client for easy future retrieval.
	 * Custom variables are defined as an entry in the input arrays which key starts with var_
	 * 
	 * @see 	Constants::iCLIENT_VARS_STATE
	 * @see 	General::newMessage()
	 *
	 * @param 	integer $txnid 	integer $txnid 	Unique ID of the Transaction the Message should be logged for
	 * @param 	array $aInput 	Array of Input as received from the Client.
	 */
	public function logClientVars($txnid, array &$aInput)
	{
		$aClientVars = array();
		foreach ($aInput as $key => $val)
		{
			if (substr($key, 0, 4) == "var_") { $aClientVars[$key] = $val; }
		}
		if (count($aClientVars) > 0) { $this->newMessage($txnid, Constants::iCLIENT_VARS_STATE, base64_encode(serialize($aClientVars) ) ); }
	}
	
	/**
	 * Attempts to determine the browser type based on the available HTTP Headers.
	 * The method will return one of the following:
	 * 	- mobile
	 * 	- web
	 * 	- unknown
	 * 
	 * @return 	string
	 */
	public static function getBrowserType()
	{
		// Mobile Browser
		if (array_key_exists("HTTP_X_WAP_PROFILE", $_SERVER) === true)
		{
			$sBrowser = "mobile";
		}
		// Determine whether browser type is web or mobile based on the HTTP User-Agent
		elseif (array_key_exists("HTTP_USER_AGENT", $_SERVER) === true)
		{
			// Determine whether browser is Mobile or Web Browser based on the vendor name in the HTTP User-Agent
			switch (true)
			{
			// Mobile Device Vendors
			case (eregi("Alcatel", $_SERVER['HTTP_USER_AGENT']) ):
			case (eregi("Amoi Electronics", $_SERVER['HTTP_USER_AGENT']) ):
			case (eregi("Asustek", $_SERVER['HTTP_USER_AGENT']) ):
			case (eregi("Audiovox", $_SERVER['HTTP_USER_AGENT']) ):
			case (eregi("Ericsson", $_SERVER['HTTP_USER_AGENT']) ):
			case (eregi("Fujitsu", $_SERVER['HTTP_USER_AGENT']) ):
			case (eregi("Handspring", $_SERVER['HTTP_USER_AGENT']) ):
			case (eregi("HP", $_SERVER['HTTP_USER_AGENT']) ):
			case (eregi("Hewlett[^a-z]Packard", $_SERVER['HTTP_USER_AGENT']) ):
			case (eregi("Hitachi", $_SERVER['HTTP_USER_AGENT']) ):
			case (eregi("High Tech Computer Corporation", $_SERVER['HTTP_USER_AGENT']) ):
			case (eregi("HTC", $_SERVER['HTTP_USER_AGENT']) ):
			case (eregi("Huawei", $_SERVER['HTTP_USER_AGENT']) ):
			case (eregi("Kyocera", $_SERVER['HTTP_USER_AGENT']) ):
			case (eregi("LG", $_SERVER['HTTP_USER_AGENT']) ):
			case (eregi("Motorola", $_SERVER['HTTP_USER_AGENT']) ):
			case (eregi("NEC", $_SERVER['HTTP_USER_AGENT']) ):
			case (eregi("Nokia", $_SERVER['HTTP_USER_AGENT']) ):
			case (eregi("Openwave", $_SERVER['HTTP_USER_AGENT']) ):
			case (eregi("Palm", $_SERVER['HTTP_USER_AGENT']) ):
			case (eregi("Panasonic", $_SERVER['HTTP_USER_AGENT']) ):
			case (eregi("Pantech", $_SERVER['HTTP_USER_AGENT']) ):
			case (eregi("RIM", $_SERVER['HTTP_USER_AGENT']) ):
			case (eregi("Research In Motion", $_SERVER['HTTP_USER_AGENT']) ):
			case (eregi("Sagem", $_SERVER['HTTP_USER_AGENT']) ):
			case (eregi("Samsung", $_SERVER['HTTP_USER_AGENT']) ):
			case (eregi("Sanyo", $_SERVER['HTTP_USER_AGENT']) ):
			case (eregi("Sharp", $_SERVER['HTTP_USER_AGENT']) ):
			case (eregi("Siemens", $_SERVER['HTTP_USER_AGENT']) ):
			case (eregi("Sony Ericsson", $_SERVER['HTTP_USER_AGENT']) ):
			case (eregi("Toshiba", $_SERVER['HTTP_USER_AGENT']) ):
			case (eregi("UTStar", $_SERVER['HTTP_USER_AGENT']) ):
			// Specific Mobile Devices
			case (eregi("Android", $_SERVER['HTTP_USER_AGENT']) ):
			case (eregi("Blackberry", $_SERVER['HTTP_USER_AGENT']) ):
			case (eregi("iPhone", $_SERVER['HTTP_USER_AGENT']) ):
			case (eregi("Pocket", $_SERVER['HTTP_USER_AGENT']) ):	// Pocket Internet Explorer
			case (eregi("Mini", $_SERVER['HTTP_USER_AGENT']) ):		// Opera Mini
				$sBrowser = "mobile";
				break;
			default:	// Web Browser
				$sBrowser = "web";
				break;
			}
		}
		// Browser unknown
		else { $sBrowser = "unknown"; }
		
		return $sBrowser;
	}
	
	public static function genToken($id, $secret, $min=30)
	{
		$minutes = date('i', time() );
		$minutes += $min - ($minutes % 15);
		return sha1($id . $secret . gmdate("Y-m-D H:") . $minutes .":00+00:00");
	}
	public static function authToken($id, $secret, $token)
	{
		if (self::genToken($id, $secret) == $token) { return 10; }			// Token is valid
		elseif (self::genToken($id, $secret, 15) == $token) { return 11; }	// Token is valid but about to expire and a new token should be generated
		else { return 1; }													// Invalid Token
	}
	public function orderAlreadyAuthorized($oid)
	{
		$sql = "SELECT Txn.id
				FROM Log.Transaction_Tbl Txn
				INNER JOIN Log.Message_Tbl M ON Txn.id = M.txnid
				WHERE Txn.clientid = ". $this->getClientConfig()->getID() ." AND orderid = '". $this->getDBConn()->escStr($oid) ."'
					AND M.stateid IN (". Constants::iPAYMENT_ACCEPTED_STATE .", ". Constants::iPAYMENT_CAPTURED_STATE .")
				ORDER BY Txn.id DESC
				LIMIT 1";
		//		echo $sql ."\n";
		$RS = $this->getDBConn()->getName($sql);
	
		return is_array($RS) === true && $RS["ID"] > 0 ? true : false;
	}
	
	/**
	 * Creates a log for an operation made, e.g. saving a card, deleting a card, etc.
	 * 
	 * @param integer $oid
	 * @param integer $mobile
	 * @param string $email
	 * @param string $cr
	 * @param integer $code
	 * @param string $msg
	 * @throws mPointException
	 */
	public function newAuditMessage($oid, $mobile, $email, $cr, $code, $msg) 
	{
		$sql = "INSERT INTO Log".sSCHEMA_POSTFIX.".AuditLog_Tbl
					(operationid, mobile, email, customer_ref, code, message)
				VALUES
					(". intval($oid). ", ". floatval($mobile) .", '". $this->getDBConn()->escStr($email) ."', '". $this->getDBConn()->escStr($cr) ."', '". intval($code) ."', '". $this->getDBConn()->escStr($msg) ."')";
//		echo $sql ."\n";
		if (is_resource($this->getDBConn()->query($sql) ) === false)
		{
			throw new mPointException("Unable to insert new audit message for operation: ". $oid, 1005);
		}
	}
	/**
	 * Returns the logs for a given search
	 *
	 * @param integer $mobile
	 * @param string $email
	 * @param string $cr
	 * @param integer $start
	 * @param string $end
	 * @retun string 	The logs that match the search 
	 */
	public function getAuditLog($mobile=-1, $email="", $cr="", $start="", $end="")
	{
		$sql = "SELECT id, operationid,  mobile,  email, customer_ref, code, message, created
				FROM Log".sSCHEMA_POSTFIX.".AuditLog_Tbl
				WHERE enabled = '1'";
		if (floatval($mobile) > 0) { $sql .= " AND mobile = '". floatval($mobile) ."'"; }
		if (empty($email) === false && strlen($email) > 0) { $sql .= " AND email = '". $this->getDBConn()->escStr($email) ."'";}
		if (empty($cr) === false && strlen($cr) > 0) { $sql .= " AND customer_ref = '". $this->getDBConn()->escStr($cr) ."'";}
		if (empty($start) === false && strlen($start) > 0) { $sql .= " AND '". $this->getDBConn()->escStr(date("Y-m-d H:i:s", strtotime($start) ) ) ."' <= created"; }
		if (empty($end) === false && strlen($end) > 0) { $sql .= " AND created <= '". $this->getDBConn()->escStr(date("Y-m-d H:i:s", strtotime($end) ) ) ."'"; }
		$sql .= "
				ORDER BY id ASC";
//		echo $sql ."\n";
		$res = $this->getDBConn()->query($sql);
		$xml = '<audit-logs>';
		while ($RS = $this->getDBConn()->fetchName($res) )
		{
			$xml .= '<audit-log id="'. $RS["ID"] .'" operation-id="'. $RS["OPERATIONID"] .'">';
			$xml .= '<customer customer-ref="'. htmlspecialchars($RS["CUSTOMER_REF"], ENT_NOQUOTES) .'">';
			$xml .= '<mobile>'. $RS["MOBILE"] .'</mobile>';
			$xml .= '<email>'. $RS["EMAIL"] .'</email>';
			$xml .= '</customer>';
			$xml .= '<message code="'. $RS["CODE"] .'">'. htmlspecialchars($RS["MESSAGE"], ENT_NOQUOTES) .'</message>';
			$xml .= '<timestamp>'. gmdate("Y-m-d H:i:sP", strtotime(substr($RS["CREATED"], 0, strpos($RS["CREATED"], ".") ) ) ) .'</timestamp>';
			$xml .= '</audit-log>';
		}
		$xml .= '</audit-logs>';
	
		return $xml;
	}
	
	protected function constHeader()
	{
		/* ----- Construct HTTP Header Start ----- */
		$h = "{METHOD} {PATH} HTTP/1.0" .HTTPClient::CRLF;
		$h .= "host: {HOST}" .HTTPClient::CRLF;
		$h .= "referer: {REFERER}" .HTTPClient::CRLF;
		$h .= "content-length: {CONTENTLENGTH}" .HTTPClient::CRLF;
		$h .= "content-type: {CONTENTTYPE}; charset=\"UTF-8\"" .HTTPClient::CRLF;
		$h .= "user-agent: mRetail" .HTTPClient::CRLF;
		/* ----- Construct HTTP Header End ----- */
	
		return $h;
	}
	
	/**
	 * Gets transacation status based on given seconds
	 * 
	 * @param HTTPConnInfo 	$oCI	connection
	 * @param integer 		$sec 	number of seconds
	 * @throws mPointException
	 * @return string
	 */
	public function getTransactionStatus(HTTPConnInfo &$oCI, $sec)
	{
		$sql = "SELECT Txn.id, Txn.orderid , URL.url
				FROM Log".sSCHEMA_POSTFIX.".Transaction_Tbl Txn
				INNER JOIN Log".sSCHEMA_POSTFIX.".Message_Tbl M ON Txn.id = M.txnid AND M.Stateid != ". Constants::iPAYMENT_ACCEPTED_STATE ."
				INNER JOIN Client".sSCHEMA_POSTFIX.".Url_Tbl URL ON Txn.clientid = URL.clientid AND URL.urltypeid = ". Constants::iURL_TYPE_GET_TRANSACTION_STATUS ."
				WHERE Txn.created >= (now() - '".$sec." seconds'::INTERVAL) FOR UPDATE";
	
		$res = $this->getDBConn()->query($sql);
		$Obj_SendData = array();
	
		while ($RS = $this->getDBConn()->fetchName($res) )
		{
			$Obj_SendData[$RS["URL"] ][$RS["ID"] ] = $RS["ORDERID"];
		}	
		foreach ($Obj_SendData AS $url => $tnx)
		{
			$xml = '<?xml version="1.0" encoding="UTF-8"?>';
			$xml .=	"<root>";
			foreach ($tnx AS $key => $value)
			{
				$xml .= '<transaction id="'.$key.'">'. $value .'</transaction>';
			}
			$xml .="</root>";
			
			$aURL_Info = parse_url($url);
			$aHTTP_CONN_INFO["mesb"]["protocol"] = $oCI->getProtocol();
			$aHTTP_CONN_INFO["mesb"]["host"] = $aURL_Info["host"];
			$aHTTP_CONN_INFO["mesb"]["port"] = $aURL_Info["port"];
			$aHTTP_CONN_INFO["mesb"]["timeout"] = $oCI->getTimeout();
			$aHTTP_CONN_INFO["mesb"]["path"] = $aURL_Info["path"];
			$aHTTP_CONN_INFO["mesb"]["method"] = $oCI->getMethod();
			$aHTTP_CONN_INFO["mesb"]["contenttype"] = $oCI->getContentType();
			$aHTTP_CONN_INFO["mesb"]["username"] = $oCI->getUsername();
			$aHTTP_CONN_INFO["mesb"]["password"] = $oCI->getPassword();
			
			$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["mesb"]);
				
			$obj_HTTP = new HTTPClient(new Template(), $oCI);
			$obj_HTTP->connect();
				
			$code = $obj_HTTP->send($this->constHeader(), $xml);
			$obj_HTTP->disconnect();
			
				$obj_DOM = simpledom_load_string($b);
				for ($i=0; $i<count($obj_DOM->transaction); $i++)
				{
					$sql = "INSERT INTO Log".sSCHEMA_POSTFIX.".Message_Tbl
							(txnid, Stateid, data)
							VALUES	(". $obj_DOM->transaction['id']. ", ". $obj_DOM->transaction->status['code'] .",  '". $this->getDBConn()->escStr( $obj_DOM->transaction->status) ."')";
					
					if (is_resource($this->getDBConn()->query($sql) ) === false)
					{
						throw new mPointException("Unable to insert new audit message for operation: ". $txnid, 1003);
					}
					
					
					$sql = "INSERT INTO Log".sSCHEMA_POSTFIX.".Message_Tbl
							SET Stateid = ".$obj_DOM->transaction." 
							WHERE txnid = ".$obj_DOM->transaction['id']."";
				}
			
		}
		return $xml;
	}

	/*
	 * Fetch Transaction based on the orderID
	 *
	 * 	 1. First attempt
	 * 	 2. Second attempt
	 * 	 8. Invalid OrderID
	 * 	 9. Transaction not found
	 *
	 *
	 * @param integer 	$orderid  OrderID from input
	 * @return string
	 * */

    public function getTxnAttemptsFromOrderID($orderid)
    {
        $sql = "SELECT attempt FROM Log" . sSCHEMA_POSTFIX . ".Transaction_Tbl
					WHERE orderid = '" . trim($orderid) . "' AND enabled = true 
					ORDER BY created DESC LIMIT 1";
//			echo $sql ."\n";
        $RS = $this->getDBConn()->getName($sql);

        if (is_array($RS) === true) {   $code = intval($RS['ATTEMPT']);  } //Transaction attempt will have values 1/2
        else { $code = -1; }    // Transaction not found

        return $code;
    }

    public function getPreviousFailedAttempts($orderid, $clientid)
    {
        $aPMArray = array();
        $aRejectedStates = array(Constants::iPAYMENT_REJECTED_PSP_UNAVAILABLE_STATE);
        $sql = "SELECT Txn.cardid FROM Log".sSCHEMA_POSTFIX.".Transaction_Tbl Txn
                INNER JOIN Log".sSCHEMA_POSTFIX.".Message_Tbl Msg on Txn.id = Msg.txnid
                WHERE Txn.orderid = '" . trim($orderid) . "' AND Txn.enabled = true AND Txn.clientid = $clientid
                GROUP BY Txn.id
                HAVING MAX(Msg.stateid) IN (".implode(",",$aRejectedStates).")";
        $res = $this->getDBConn()->query($sql);

        while ($RS = $this->getDBConn()->fetchName($res) ) {
            array_push($aPMArray, intval($RS['CARDID'] ) );
        }

        return $aPMArray;
    }
    
    public function getTxnAttemptsFromSessionID($sessionid)
    {
        $sql = "SELECT count(txn.id) AS attempts  
          FROM log" . sSCHEMA_POSTFIX . ".transaction_tbl txn 
            INNER JOIN log" . sSCHEMA_POSTFIX . ".message_tbl msg ON txn.id = msg.txnid 
          WHERE sessionid = " . $sessionid .  " 
            AND msg.stateid in (20109, 20103, 20102, 20101, 2011, 2010) GROUP BY txn.sessionid";

        $res = $this->getDBConn()->getName($sql);
        $attempts = 0;
        if (is_array($res) === true) {
            $attempts = intval($res['ATTEMPTS']);
        }
        return $attempts;
    }

    public function getStaticRouteData($clientId = "")
    {
        $sql = "SELECT clientid, pspid
                    FROM client" . sSCHEMA_POSTFIX . ".cardaccess_tbl
                    WHERE pspid IN
                          (SELECT id
                           FROM system.psp_tbl
                           WHERE capture_method <> 0)
                    AND enabled ";
         if($clientId !== "" )
         {
            $sql .= "AND clientid = $clientId" ;
         }
         $aRS = $this->getDBConn()->getAllNames($sql);
         return $aRS;
    }
}
?>