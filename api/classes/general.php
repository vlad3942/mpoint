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
 * @version 1.10
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
			settype($_GET['msg'], "array");
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
	public function xml2bool($b)
	{
		if ($b == "true")  { $b = true; }
		elseif ($b == "yes")  { $b = true; }
		elseif (strval($b) == "1")  { $b = true; }
		elseif ($b == "false")  { $b = false; }
		elseif ($b == "no")  { $b = true; }
		elseif (strval($b) == "0")  { $b = false; }

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
		// Language has previously been determined
		if (isset($_SESSION) === true && $_SESSION['obj_Info']->getInfo("language") !== false)
		{
			$sLang = $_SESSION['obj_Info']->getInfo("language");
		}
		// Language provided by Client as part of the Transaction data
		elseif (array_key_exists("language", $_REQUEST) === true && empty($_REQUEST['language']) === false
				&& is_dir(sLANGUAGE_PATH ."/". $_REQUEST['language']) === true)
		{
			$sLang = $_REQUEST['language'];
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

		$dir = str_replace("\\", "/", dirname($_SERVER['PHP_SELF']) );
		if (substr($dir, -1) != "/") { $dir .= "/"; }
		$xml = '<system>';
		$xml .= '<protocol>http</protocol>';
		$xml .= '<host>'. $_SERVER['HTTP_HOST'] .'</host>';
		$xml .= '<dir>'. $dir .'</dir>';
		$xml .= '<file>'. substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], "/")+1) .'</file>';
		$xml .= '<query-string>'. htmlspecialchars($_SERVER['QUERY_STRING'], ENT_NOQUOTES) .'</query-string>';
		$xml .= '<session id="'. session_id() .'">'. session_name() .'</session>';
		$xml .= '<language>'. sLANG .'</language>';
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
		$sql = "SELECT Nextval('Log.Transaction_Tbl_id_seq') AS id";
		$RS = $this->getDBConn()->getName($sql);
		// Error: Unable to generate a new Transaction ID
		if (is_array($RS) === false) { throw new mPointException("Unable to generate new Transaction ID", 1001); }

		$sql = "INSERT INTO Log.Transaction_Tbl
					(id, typeid, clientid, accountid, countryid, keywordid, mode, ip)
				VALUES
					(". $RS["ID"] .", ". intval($tid) .", ". $oCC->getID() .", ". $oCC->getAccountConfig()->getID() .", ". $oCC->getCountryConfig()->getID() .", ". $oCC->getKeywordConfig()->getID() .", ". $oCC->getMode() .", '". $_SERVER['REMOTE_ADDR'] ."')";
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
		$sql = "UPDATE Log.Transaction_Tbl
				SET typeid = ". $oTI->getTypeID() .", clientid = ". $oTI->getClientConfig()->getID() .", accountid = ". $oTI->getClientConfig()->getAccountConfig()->getID() .",
					countryid = ". $oTI->getClientConfig()->getCountryConfig()->getID() .", keywordid = ". $oTI->getClientConfig()->getKeywordConfig()->getID() .",
					amount = ". $oTI->getAmount() .", orderid = '". $this->getDBConn()->escStr($oTI->getOrderID() ) ."', lang = '". $this->getDBConn()->escStr($oTI->getLanguage() ) ."',
					mobile = ". floatval($oTI->getMobile() ) .", operatorid = ". $oTI->getOperator() .", email = '". $this->getDBConn()->escStr($oTI->getEMail() ) ."',
					logourl = '". $this->getDBConn()->escStr($oTI->getLogoURL() ) ."', cssurl = '". $this->getDBConn()->escStr($oTI->getCSSURL() ) ."',
					accepturl = '". $this->getDBConn()->escStr($oTI->getAcceptURL() ) ."', cancelurl = '". $this->getDBConn()->escStr($oTI->getCancelURL() ) ."',
					callbackurl = '". $this->getDBConn()->escStr($oTI->getCallbackURL() ) ."', gomobileid = ". $oTI->getGoMobileID() .", auto_capture = ". General::bool2xml($oTI->useAutoCapture() );
		if ($oTI->getAccountID() > 0) { $sql .= ", euaid = ". $oTI->getAccountID(); } 
		$sql .= "
				WHERE id = ". $oTI->getID();
//		echo $sql ."\n";
		// Error: Unable to update Transaction
		if (is_resource($this->getDBConn()->query($sql) ) === false)
		{
			throw new mPointException("Unable to update Transaction: ". $oTI->getID(), 1004);
		}
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
		$sql = "INSERT INTO Log.Message_Tbl
					(txnid, stateid, data)
				VALUES
					(". intval($txnid) ." , ". intval($sid) .", '". $this->getDBConn()->escStr(utf8_encode($data) ) ."')";
//		echo $sql ."\n";
		// Error: Unable to insert a new message for Transaction
		if (is_resource($this->getDBConn()->query($sql) ) === false)
		{
			throw new mPointException("Unable to insert new message for Transaction: ". $txnid ." and State: ". $sid, 1003);
		}
	}

	/**
	 * Retrieves the data for a given transaction state from the Message database table.
	 * The retrieved data is unserialised before being returned.
	 *
	 * @see 	unserialize()
	 *
	 * @param 	integer $txnid 		ID of the Transaction that message data should be retrieved from
	 * @param 	integer $stateid 	ID of the State to which the data belongs
	 * @return 	mixed
	 */
	protected function getMessageData($txnid, $stateid)
	{
		$sql = "SELECT data
				FROM Log.Message_Tbl
				WHERE txnid = ". intval($txnid) ." AND stateid = ". intval($stateid) ."
				ORDER BY id DESC
				LIMIT 1";
//		echo $sql ."\n";
		$RS = $this->getDBConn()->getName($sql);

		return is_array($RS)===true?unserialize(utf8_decode($RS["DATA"]) ):array();
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
	 * i.e. $X.XX for USA, X.XXkr for Denmark etc.
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
		$sPrice = str_replace("{PRICE}", number_format($amount / 100, $oCC->getDecimals() ), $sPrice);

		return $sPrice;
	}

	/**
	 * Construct standard mPoint HTTP Headers for notifying the Client via HTTP.
	 *
	 * @return string
	 */
	protected function constHTTPHeaders()
	{
		/* ----- Construct HTTP Header Start ----- */
		$h = "{METHOD} {PATH} HTTP/1.0" .HTTPClient::CRLF;
		$h .= "host: {HOST}" .HTTPClient::CRLF;
		$h .= "referer: {REFERER}" .HTTPClient::CRLF;
		$h .= "content-length: {CONTENTLENGTH}" .HTTPClient::CRLF;
		$h .= "content-type: {CONTENTTYPE}; charset=ISO-8859-15" .HTTPClient::CRLF;
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
				FROM System.Country_Tbl
				WHERE enabled = true
				ORDER BY name ASC";
//		echo $sql ."\n";
		$res = $this->getDBConn()->query($sql);

		$xml = '<country-configs>';
		while ($RS = $this->getDBConn()->fetchName($res) )
		{
			$xml .= '<config id="'. $RS["ID"] .'">';
			$xml .= '<name>'. htmlspecialchars($RS["NAME"], ENT_NOQUOTES) .'</name>';
			$xml .= '<currency symbol="'. $RS["SYMBOL"] .'">'. $RS["CURRENCY"] .'</currency>';
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
					FROM System.IPRange_Tbl
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
		if (count($aClientVars) > 0) { $this->newMessage($txnid, Constants::iCLIENT_VARS_STATE, serialize($aClientVars) ); }
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
}
?>