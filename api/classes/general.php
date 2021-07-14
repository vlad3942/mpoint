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

require_once sCLASS_PATH .'/Parser.php';
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
		[$sTimestamp, $iTxnID] = spliti("Z", $chk);
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
			case (preg_match('/iPhone/', $_SERVER['HTTP_USER_AGENT']) ):	// Mobile Device supports HTML5
			case (preg_match('/iPod/', $_SERVER['HTTP_USER_AGENT']) ):
			case (preg_match('/Android/', $_SERVER['HTTP_USER_AGENT']) ):
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
		case preg_match("/iPod/i", $_SERVER['HTTP_USER_AGENT']):
		case preg_match("/iPhone/i", $_SERVER['HTTP_USER_AGENT']):
			$platform = "iPhone";
			break;
		case preg_match("/iPad/i", $_SERVER['HTTP_USER_AGENT']):
			$platform = "iPad";
			break;
		case preg_match("/Firefox/i", $_SERVER['HTTP_USER_AGENT']):
			$platform = "Firefox";
			break;
		case preg_match("/Skyfire/i", $_SERVER['HTTP_USER_AGENT']):
			$platform = "Skyfire";
			break;
		case preg_match("/Android/i", $_SERVER['HTTP_USER_AGENT']):
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
		}  else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && array_key_exists("HTTP_X_FORWARDED_FOR", $_SERVER) === true)
		{
			$ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ips = array_map('trim', $ips);
            $ip = $ips[0];
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
					countryid = ". $oTI->getCountryConfig()->getID() .",currencyid = ". $oTI->getInitializedCurrencyConfig()->getID().", keywordid = ". $oTI->getClientConfig()->getKeywordConfig()->getID() .",
					amount = ". $oTI->getInitializedAmount() .", points = ". ($oTI->getPoints() > 0 ? $oTI->getPoints() : "NULL") .", reward = ". ($oTI->getReward() > 0 ? $oTI->getReward() : "NULL") .",
					orderid = '". $this->getDBConn()->escStr($oTI->getOrderID() ) ."', lang = '". $this->getDBConn()->escStr($oTI->getLanguage() ) ."',
					mobile = ". floatval($oTI->getMobile() ) .", operatorid = ". $oTI->getOperator() .", email = '". $this->getDBConn()->escStr($oTI->getEMail() ) ."',
					logourl = '". $this->getDBConn()->escStr($oTI->getLogoURL() ) ."', cssurl = '". $this->getDBConn()->escStr($oTI->getCSSURL() ) ."',
					accepturl = '". $this->getDBConn()->escStr($oTI->getAcceptURL() ) ."', declineurl = '". $this->getDBConn()->escStr($oTI->getDeclineURL() ) ."', cancelurl = '". $this->getDBConn()->escStr($oTI->getCancelURL() ) ."',
					callbackurl = '". $this->getDBConn()->escStr($oTI->getCallbackURL() ) ."', iconurl = '". $this->getDBConn()->escStr($oTI->getIconURL() ) ."',
					authurl = '". $this->getDBConn()->escStr($oTI->getAuthenticationURL() ) ."', customer_ref = '". $this->getDBConn()->escStr($oTI->getCustomerRef() ) ."',
					gomobileid = ". $oTI->getGoMobileID() .", auto_capture = ". $oTI->useAutoCapture() .", markup = '". $this->getDBConn()->escStr($oTI->getMarkupLanguage() ) ."',
					description = '". $this->getDBConn()->escStr($oTI->getDescription() ) ."',
					deviceid = '". $this->getDBConn()->escStr($oTI->getDeviceID()) ."', attempt = ".intval($oTI->getAttemptNumber()) .", producttype = ".intval($oTI->getProductType()).",
					convertedamount = ". $oTI->getConvertedAmount() .",convertedcurrencyid = ". ($oTI->getConvertedCurrencyConfig() === null ?"NULL": $oTI->getConvertedCurrencyConfig()->getID()).",
					conversionrate = ". $oTI->getConversationRate().", fee = ".$oTI->getFee();

		if (strlen($oTI->getIP() ) > 0) { $sql .= " , ip = '". $this->getDBConn()->escStr( $oTI->getIP() ) ."'"; }
		if ($oTI->getAccountID() > 0) { $sql .= ", euaid = ". $oTI->getAccountID(); }
		elseif ($oTI->getAccountID() == -1) { $sql .= ", euaid = NULL"; }
		if($oTI->getInstallmentValue()>0) {
            $sql .= " , installment_value = '". $oTI->getInstallmentValue() ."'";
        }

        if ($oTI->getFXServiceTypeID() > 0) {
            $sql .= " , fxservicetypeid = '". $oTI->getFXServiceTypeID() ."'";
        }
        if ($oTI->getProfileID() !== '') {
            $sql .= " , profileid = '". $oTI->getProfileID() ."'";
        }
        if ($oTI->getWalletID() !== -1) {
            $sql .= ", walletid = ". $oTI->getWalletID();
        }
        if ($oTI->getRouteConfigID() > 0) {
            $sql .= ", routeconfigid = ". $oTI->getRouteConfigID();
        }
        if ($oTI->getPSPID() > 0) {
            $sql .= ", pspid = ". $oTI->getPSPID();
        }
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
		}else{
		    // Update attempt count of original transaction
            $sql = "UPDATE Log".sSCHEMA_POSTFIX.".Transaction_Tbl
				SET sessionid = ".$iSessionId." , attempt = attempt+1 WHERE id=".$oTI->getID() ;

            if (is_resource($this->getDBConn()->query($sql) ) === false)
            {
                throw new mPointException("Unable to update attemp count of original transaction: ".$oTI->getID(), 1004);
            }
        }
		return $iTxnID ;
	}

    /**
     * @param \TxnInfo $txnInfo
     * @param int      $newAmount
     * @param bool     $isInitiateTxn
     * @param string   $pspid
     * @param array    $additionalTxnData
     *
     * @return \TxnInfo|null
     * @throws \SQLQueryException
     * @throws \mPointException
     */
    public function createTxnFromTxn(TxnInfo $txnInfo, int $newAmount, bool $isInitiateTxn = TRUE, string $pspid = '', array $additionalTxnData = [],array $misc = []): ?TxnInfo
    {
        $iAssociatedTxnId =  $this->newTransaction($txnInfo->getClientConfig(), $txnInfo->getTypeID());
	    try
        {
             $data = $misc;
             $data["card-id"] = '';
             $data["wallet-id"] = '';
             $data["amount"] = $newAmount;
             $data["extid"] = '';
             $data["psp-id"] = $pspid;
             $data["captured-amount"] = '';
             $data["externalref"] = '';
             $data["currency-config"] = $txnInfo->getInitializedCurrencyConfig();
             $data["converted-currency-config"] = $txnInfo->getInitializedCurrencyConfig();
             $data["converted-amount"] = $newAmount;
             $data["conversion-rate"] = 1;
             $txnInfo->setFXServiceTypeID(0);
             $obj_AssociatedTxnInfo = TxnInfo::produceInfo($iAssociatedTxnId, $this->getDBConn(), $txnInfo, $data);

             //link parent transaction to new created txn
             $index = count($additionalTxnData);
             $additionalTxnData[$index]['name'] = 'linked_txn_id';
             $additionalTxnData[$index]['value'] = (string)$txnInfo->getID();
             $additionalTxnData[$index]['type'] = 'Transaction';
             $obj_AssociatedTxnInfo->setAdditionalDetails($this->getDBConn(), $additionalTxnData, $iAssociatedTxnId);

             //link new transaction to parent txn
             $additionalData[0]['name'] = 'linked_txn_id';
             $additionalData[0]['value'] = (string)$iAssociatedTxnId;
             $additionalData[0]['type'] = 'Transaction';
             $txnInfo->setAdditionalDetails($this->getDBConn(), $additionalData, $txnInfo->getID());

             $this->newMessage($iAssociatedTxnId, Constants::iTRANSACTION_CREATED, '');
             $this->logTransaction($obj_AssociatedTxnInfo);

             $txnPassbookObj = TxnPassbook::Get($this->getDBConn(), $iAssociatedTxnId, $txnInfo->getClientConfig()->getID());
             if($isInitiateTxn === true) {
                 $passbookEntry = new PassbookEntry
                 (
                     NULL,
                     $newAmount,
                     $obj_AssociatedTxnInfo->getCurrencyConfig()->getID(),
                     Constants::iInitializeRequested
                 );
                 if ($txnPassbookObj instanceof TxnPassbook) {
                     $txnPassbookObj->addEntry($passbookEntry);
                     $txnPassbookObj->performPendingOperations();
                 }
             }
             return $obj_AssociatedTxnInfo;
         }
         catch (Exception $e)
         {
             trigger_error("Error while creating new transaction ($iAssociatedTxnId). Transaction is rollback - " .$e->getMessage() , E_USER_ERROR);
         }

		return NULL;
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

		$bindParam = array($txnid, $sid, $data);
		$resultSet = $this->getDBConn()->executeQuery($sql, $bindParam);
		
		if (!is_resource($resultSet)) {
			throw new mPointException("Unable to insert new message for Transaction: ". $txnid ." and State: ". $sid, 1003);
		}
			
	}

	/**
	 * Create a new transaction with same session id as the original transaction,
	 * and authorize the new transaction using secondary PSP as part of Dynamic Routing
	 * 
	 * @param TxnInfo $obj_TxnInfo
	 * @param unknown $iSecondaryRoute
	 * @return string
	 */
	public function authWithAlternateRoute(TxnInfo $obj_TxnInfo ,$iSecondaryRoute ,$aHTTP_CONN_INFO, $obj_Elem )
	{
        global $_OBJ_TXT;

        $obj_PSPConfig = PSPConfig::produceConfiguration($this->getDBConn(), $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), -1, $iSecondaryRoute);
        $iAssociatedTxnId = $this->newAssociatedTransaction ( $obj_TxnInfo );

        // Update Associated Transaction ID
	    $data = array();
        $obj_AssociatedTxnInfo = TxnInfo::produceInfo( (integer) $iAssociatedTxnId, $this->getDBConn(),$obj_TxnInfo,$data);
        $this->logTransaction($obj_AssociatedTxnInfo);

        // Update Parent Transaction Route Config ID
        $obj_TxnInfo->setRouteConfigID($iSecondaryRoute);
        $this->logTransaction($obj_TxnInfo);

        /*******************************
        $txnPassbookObj = TxnPassbook::Get($this->getDBConn(), $iAssociatedTxnId, $obj_TxnInfo->getClientConfig ()->getID ());
        $passbookEntry = new PassbookEntry
        (
            NULL,
            $obj_TxnInfo->getAmount(),
            $obj_TxnInfo->getCurrencyConfig()->getID(),
            Constants::iInitializeRequested
        );
        if($txnPassbookObj instanceof TxnPassbook) {
            $txnPassbookObj->addEntry($passbookEntry);
            $txnPassbookObj->performPendingOperations();
        }

        $txnPassbookObj = TxnPassbook::Get($this->getDBConn(), $iAssociatedTxnId, $obj_TxnInfo->getClientConfig ()->getID ());
        $passbookEntry = new PassbookEntry
        (
            NULL,
            $obj_TxnInfo->getAmount(),
            $obj_TxnInfo->getCurrencyConfig()->getID(),
            Constants::iAuthorizeRequested
        );
        if($txnPassbookObj instanceof TxnPassbook) {
            $txnPassbookObj->addEntry($passbookEntry);
            $txnPassbookObj->performPendingOperations();
        }

        $txnPassbookObj->updateInProgressOperations($obj_TxnInfo->getAmount(), Constants::iPAYMENT_ACCEPTED_STATE, Constants::sPassbookStatusError);
        ********************************/

        $this->newMessage($iAssociatedTxnId, Constants::iPAYMENT_RETRIED_USING_DR_STATE, "Payment retried using dynamic routing");
        $obj_second_PSP = Callback::producePSP ( $this->getDBConn(), $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO, $obj_PSPConfig );

        return $obj_second_PSP->authorize( $obj_PSPConfig, $obj_Elem );

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
		$h .= "user-agent: mPoint-{USER-AGENT}" .HTTPClient::CRLF;
		$h .= "X-CPM-Merchant-Domain: {X-CPM-MERCHANT-DOMAIN}" .HTTPClient::CRLF;
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
			case (preg_match("/Alcatel/i", $_SERVER['HTTP_USER_AGENT']) ):
			case (preg_match("/Amoi Electronics/i", $_SERVER['HTTP_USER_AGENT']) ):
			case (preg_match("/Asustek/i", $_SERVER['HTTP_USER_AGENT']) ):
			case (preg_match("/Audiovox/i", $_SERVER['HTTP_USER_AGENT']) ):
			case (preg_match("/Ericsson/i", $_SERVER['HTTP_USER_AGENT']) ):
			case (preg_match("/Fujitsu/i", $_SERVER['HTTP_USER_AGENT']) ):
			case (preg_match("/Handspring/i", $_SERVER['HTTP_USER_AGENT']) ):
			case (preg_match("/HP/i", $_SERVER['HTTP_USER_AGENT']) ):
			case (preg_match("/Hewlett[^a-z]Packard/i", $_SERVER['HTTP_USER_AGENT']) ):
			case (preg_match("/Hitachi/i", $_SERVER['HTTP_USER_AGENT']) ):
			case (preg_match("/High Tech Computer Corporation/i", $_SERVER['HTTP_USER_AGENT']) ):
			case (preg_match("/HTC/i", $_SERVER['HTTP_USER_AGENT']) ):
			case (preg_match("/Huawei/i", $_SERVER['HTTP_USER_AGENT']) ):
			case (preg_match("/Kyocera/i", $_SERVER['HTTP_USER_AGENT']) ):
			case (preg_match("/LG/i", $_SERVER['HTTP_USER_AGENT']) ):
			case (preg_match("/Motorola/i", $_SERVER['HTTP_USER_AGENT']) ):
			case (preg_match("/NEC/i", $_SERVER['HTTP_USER_AGENT']) ):
			case (preg_match("/Nokia/i", $_SERVER['HTTP_USER_AGENT']) ):
			case (preg_match("/Openwave/i", $_SERVER['HTTP_USER_AGENT']) ):
			case (preg_match("/Palm/i", $_SERVER['HTTP_USER_AGENT']) ):
			case (preg_match("/Panasonic/i", $_SERVER['HTTP_USER_AGENT']) ):
			case (preg_match("/Pantech/i", $_SERVER['HTTP_USER_AGENT']) ):
			case (preg_match("/RIM/i", $_SERVER['HTTP_USER_AGENT']) ):
			case (preg_match("/Research In Motion/i", $_SERVER['HTTP_USER_AGENT']) ):
			case (preg_match("/Sagem/i", $_SERVER['HTTP_USER_AGENT']) ):
			case (preg_match("/Samsung/i", $_SERVER['HTTP_USER_AGENT']) ):
			case (preg_match("/Sanyo/i", $_SERVER['HTTP_USER_AGENT']) ):
			case (preg_match("/Sharp/i", $_SERVER['HTTP_USER_AGENT']) ):
			case (preg_match("/Siemens/i", $_SERVER['HTTP_USER_AGENT']) ):
			case (preg_match("/Sony Ericsson/i", $_SERVER['HTTP_USER_AGENT']) ):
			case (preg_match("/Toshiba/i", $_SERVER['HTTP_USER_AGENT']) ):
			case (preg_match("/UTStar/i", $_SERVER['HTTP_USER_AGENT']) ):
			// Specific Mobile Devices
			case (preg_match("/Android/i", $_SERVER['HTTP_USER_AGENT']) ):
			case (preg_match("/Blackberry/i", $_SERVER['HTTP_USER_AGENT']) ):
			case (preg_match("/iPhone/i", $_SERVER['HTTP_USER_AGENT']) ):
			case (preg_match("/Pocket/i", $_SERVER['HTTP_USER_AGENT']) ):	// Pocket Internet Explorer
			case (preg_match("/Mini/i", $_SERVER['HTTP_USER_AGENT']) ):		// Opera Mini
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

    public function getTxnAttemptsFromOrderID(ClientConfig $clientConfig, CountryConfig $countryConfig, $orderid, int $txnId = NULL)
    {
        $txnIdCheck = '';

        if($txnId !== NULL && $txnId >1)
        {
            $txnIdCheck = " id= $txnId AND ";
        }

        $sql = "SELECT max(attempt) as attempt FROM Log" . sSCHEMA_POSTFIX . ".Transaction_Tbl
					WHERE {$txnIdCheck} orderid = '" . trim($orderid) . "' AND enabled = true
					AND clientid= ".$clientConfig->getID(). ' AND accountid = ' .$clientConfig->getAccountConfig()->getID(). '
					AND countryid = '.$countryConfig->getID()."
					AND created > NOW() - interval '15 days' ";
//			echo $sql ."\n";
        $RS = $this->getDBConn()->getName($sql);

        if (is_array($RS) === true) {   $code = intval($RS['ATTEMPT']);  } //Transaction attempt will have values 1/2
        else { $code = 0; }    // Transaction not found

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
        $sql = "SELECT DISTINCT clientid, pspid
                    FROM client" . sSCHEMA_POSTFIX . ".cardaccess_tbl
                    WHERE capture_method <> 0 
                    AND enabled ";
         if($clientId !== "" )
         {
            $sql .= "AND clientid = $clientId" ;
         }
         $aRS = $this->getDBConn()->getAllNames($sql);
         return $aRS;
    }

    public static function getMaskCardNumber($cardno)
    {
        return substr($cardno, 0, 6) . str_repeat("*", strlen($cardno) - 10) . substr($cardno, -4);
    }

    public function getAdditionalPropertyFromDB($key, $clientId, $pspid=0)
    {
        try
        {
            $sql = "SELECT value FROM Client" . sSCHEMA_POSTFIX . ".AdditionalProperty_tbl ";

            if ($pspid == 0)
            {
                $sql .= "WHERE externalid = " . intval($clientId) . " and type='client'";
            }
            else
            {
                $sql .= "WHERE externalid = (
                    SELECT id FROM Client" . sSCHEMA_POSTFIX . ".MerchantAccount_tbl 
                    WHERE pspid=" . intval($pspid) . " and clientid = " . intval($clientId) . ") and type='merchant'";
            }
            $sql .= " and key = '".$key."' and enabled=true";

            $RS = $this->getDBConn()->getName($sql);
            if (is_array($RS) === false)
            {
                return null;
            }
            else
            {
                return $RS["VALUE"];
            }
        }
        catch (mPointException $mPointException)
        {
            trigger_error ( 'Get AdditionalProperty From DB error - .' . $mPointException->getMessage(), E_USER_ERROR );
        }
    }

    /***
     * Function is used to process Authorize Response and based on Status Code, Do retry
     *
     * @param     $obj_TxnInfo              Transaction Info
     * @param     $obj_Processor            Processor Object
     * @param     $aHTTP_CONN_INFO Http     Connection Detail
     * @param     $obj_Elem
     * @param     $response                 Auth Response
     * @param     $is_legacy                Check for Legacy flow or not
     * @param     $paymentRetryWithAlternateRoute Is Client configured for Alternate route.
     * @param int $preference               Alternate Route Preference.
     *
     * @return string XML String
     */
    public function processAuthResponse($obj_TxnInfo, $obj_Processor, $aHTTP_CONN_INFO, $obj_Elem, $response, $is_legacy, $preference = Constants::iSECOND_ALTERNATE_ROUTE): ?string
    {
        $xml = '';
        $code = $response->code;
        $subCode = $response->sub_code;

        switch ($code) {
            case 100 :
                $xml = '<status code="100">Payment Authorized using Stored Card</status>';
                break;
            case Constants::iPAYMENT_ACCEPTED_STATE :
                $xml = '<status code="2000">Payment authorized</status>';
                break;
            case Constants::iTICKET_CREATED_STATE :
                $xml = '<status code="2009">Payment authorized and Card Details Stored.</status>';
                break;
            case Constants::iPAYMENT_3DS_VERIFICATION_STATE :
                header("HTTP/1.1 303");
                $xml = $response->body;
                break;
            case Constants::iPAYMENT_3DS_FAILURE_STATE :
                $xml = $response->body;
                break;
            case Constants::iPAYMENT_REJECTED_STATE :
            case 504:
                if ($code == 504 || $obj_TxnInfo->hasEitherSoftDeclinedState($subCode) === true) {
                    if(strtolower($is_legacy) == 'false') {

                        $objTxnRoute = new PaymentRoute($this->_obj_DB, $obj_TxnInfo->getSessionId());
                        $iAlternateRoute = $objTxnRoute->getAlternateRoute($preference);

                        if (empty($iAlternateRoute) === false) {
                            $response = $this->authWithAlternateRoute($obj_TxnInfo, $iAlternateRoute, $aHTTP_CONN_INFO, $obj_Elem);
                            // Check for another preference
                            $preference++;
                            return $this->processAuthResponse($obj_TxnInfo, $obj_Processor, $aHTTP_CONN_INFO, $obj_Elem, $response, $is_legacy, $preference);
                        } else {
                            $xml = '<status code="92">Authorization failed, ' . $obj_Processor->getPSPConfig()->getName() . ' returned error: ' . $code . '</status>';
                        }
                    }
                }
                else {
                    $xml = '<status code="2010">Payment rejected by PSP</status>';
                }
                break;
            default :
                $this->delMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);
                header("HTTP/1.1 502 Bad Gateway");
                $xml = '<status code="92">Authorization failed, ' . $obj_Processor->getPSPConfig()->getName() . ' returned error: ' . $code . '</status>';
                break;
        }

        if($subCode > 0)
        {
            $responseXML = simpledom_load_string($xml);
            $responseXML['sub-code'] = $response->sub_code;
            $xml =str_replace(["<?xml version=\"1.0\"?>", "\n"], '',  $responseXML->asXML());
        }
        return $xml;
    }


    public static function applyRule(SimpleXMLElement $obj_XML,$aRuleProperties=array())
    {
        $parser = new  \mPoint\Core\Parser();
        $parser->setContext($obj_XML);
        foreach ($aRuleProperties as $value )
        {
            $parser->setRules($value);
        }
        return $parser->parse();;
    }

    /**
     * Logs payment 3ds secure information.
     * @param PaymentSecureInfo $paymentSecureInfo

     */
    public function storePaymentSecureInfo(PaymentSecureInfo $paymentSecureInfo)
    {
        $sql = "INSERT INTO Log".sSCHEMA_POSTFIX.".paymentsecureinfo_tbl
					(txnid, pspid, status, msg, veresEnrolledStatus, paresTxStatus,eci,cavv,cavvAlgorithm, protocol)
				VALUES ($1,$2, $3, $4, $5, $6,$7,$8,$9,$10)";

		$aParams = array(
			$paymentSecureInfo->getTransactionID(),
			$paymentSecureInfo->getPSPID(),
			$paymentSecureInfo->getStatus(),
			$paymentSecureInfo->getMsg(),
			$paymentSecureInfo->getVeresEnrolledStatus(),
			$paymentSecureInfo->getParestxstatus(),
			$paymentSecureInfo->getECI(),
			$paymentSecureInfo->getCAVV(),
			$paymentSecureInfo->getCavvAlgorithm(),
			$paymentSecureInfo->getProtocol()
		);
	
		$resource = $this->getDBConn()->executeQuery($sql, $aParams);

		if ($resource === false) {
			trigger_error("Unable to insert new payment secure message for txn id: ". $paymentSecureInfo->getTransactionID(), E_USER_ERROR);
		}
		
    }

    /**
     * Retrieves Issuer identification number from given card number
     * @param $cardno integer  Card Number
     * @return string          Issuer identification number
     */
    public static function getIssuerIdentificationNumber($cardno)
    {
        return substr($cardno, 0, 6);
    }

    /**
     * @return string
     */
    public static function getProtocol(): string
    {
        if (array_key_exists("HTTPS", $_SERVER) && 'on' === $_SERVER["HTTPS"]) {
            return 'https';
        }
        if (array_key_exists("SERVER_PORT", $_SERVER) && 443 === (int)$_SERVER["SERVER_PORT"]) {
            return 'https';
        }
        if (array_key_exists("HTTP_X_FORWARDED_SSL", $_SERVER) && 'on' === $_SERVER["HTTP_X_FORWARDED_SSL"]) {
            return 'https';
        }
        if (array_key_exists("HTTP_X_FORWARDED_PROTO", $_SERVER) && 'https' === $_SERVER["HTTP_X_FORWARDED_PROTO"]) {
            return 'https';
        }
        return 'http';
    }

    /**
     * Returns the list of settlement currencies for given client-id, card-id and salecurrency-id
     * @param  $RDB 			Object   Database object reference
     * @param  $clientid 		integer  client-id
     * @param  $cardid	 		integer  card-id
     * @param  $salecurrencyid 	integer  currency-id
     * @return array
     */
    public static function getPresentmentCurrencies(RDB &$oDB, int $clientid, int $cardid, int $salecurrencyid) : array
    {
		$presentmentCurrencies = array ();

		if ($oDB instanceof RDB) {

			// Added Distinct clause as one card-id may have multiple pspid hence to avoid occurence of duplicate settlement-currency-id
			$sql = "SELECT DISTINCT CCMT.Settlement_Currency_Id
					FROM Client" . sSCHEMA_POSTFIX . ".Card_Currency_Mapping_Tbl CCMT
					WHERE CCMT.client_id = " . $clientid . "
					AND CCMT.enabled = '1'
					AND CCMT.is_presentment = '1'
					AND CCMT.card_id = " . $cardid . "
					AND CCMT.sale_currency_id = " . $salecurrencyid . "";

			//echo $sql ."\n";die;
			$aRS = $oDB->getAllNames($sql);

			if (is_array($aRS) === true && count($aRS) > 0)
			{
				for ($i = 0; $i < count($aRS); $i++)
				{
					$settlementCurrencyId = $aRS[$i]['SETTLEMENT_CURRENCY_ID'];
					array_push($presentmentCurrencies, $settlementCurrencyId);
				}
			}
		}

		return $presentmentCurrencies;
	}

    /**
     * @param \RDB $obj_DB
     * @param int  $pspId
     *
     * @return int
     */
    public static function getPSPType(RDB $obj_DB, int $pspId) : int
	{
		try
        {
            $query = "SELECT system_type FROM system" . sSCHEMA_POSTFIX . ".psp_tbl WHERE id = " . $pspId;

            $resultSet = $obj_DB->getName($query);
            if (is_array($resultSet) === true)
            {
                $processorType = (int)$resultSet['SYSTEM_TYPE'];
                if($processorType !== null && $processorType !== 0)
                {
                    return $processorType;
                }
            }
        }
        catch (Exception $mPointException)
        {
            trigger_error("Unable to fetch System Type of PSP : " . $pspId, E_USER_WARNING);
        }
        return -1;
    }

    /**
     * Finds if txn under session has logged 3015,3115 state means fraud detected
     * returned value will be used to disable FOP.
     * temporary implementation later will be moved to CRS
     * @param \RDB $obj_DB
     * @param int  $pspId
     *
     * @return int
     */
    public function findFraudDetected(int $sessionId) : int
    {

        $sql = "SELECT t.cardid FROM LOG".sSCHEMA_POSTFIX.".transaction_tbl t INNER JOIN LOG".sSCHEMA_POSTFIX.".MESSAGE_TBL m on t.id = m.txnid
                WHERE m.stateid in (".Constants::iPRE_FRAUD_CHECK_REJECTED_STATE.",".Constants::iPOST_FRAUD_CHECK_REJECTED_STATE.")
                AND t.sessionid = ".$sessionId.";
				";
        $res = $this->getDBConn()->getName($sql);
        if (is_array($res) === true)
        {
            //Returning hardcoded paymentmethod because dcc will be offered only on card and later FOP disable on fraud deteccted functionality will be moved to crs
           return Constants::iPAYMENT_TYPE_CARD;
        }
        return -1;
    }

    // Get PSP Config Object
    public static function producePSPConfigObject(RDB $oDB, TxnInfo $oTI, ?int $cardId, ?int $pspID, bool $bForceLegacy = false): ?PSPConfig
    {
        $isLegacy           = $oTI->getClientConfig()->getAdditionalProperties (Constants::iInternalProperty, 'IS_LEGACY');
        $iProcessorType     = self::getPSPType($oDB, $pspID);
        $iCardType          = OnlinePaymentCardPSPMapping[$cardId];
        $isOfflineType      = (int)$oTI->getPaymentMethod($oDB)->PaymentType;
        $routeConfigID      = (int)$oTI->getRouteConfigID();

        if($bForceLegacy === true || strtolower($isLegacy) == 'true') {
            $oPSPConfig = PSPConfig::produceConfig($oDB, $oTI->getClientConfig()->getID(), $oTI->getClientConfig()->getAccountConfig()->getID(), $pspID);
        }
        else if(strtolower($isLegacy) == 'false' && ($isOfflineType !== Constants::iPAYMENT_TYPE_OFFLINE || !isset($iCardType) || $iProcessorType != Constants::iPROCESSOR_TYPE_WALLET ) && $routeConfigID > 0 ){
            $oPSPConfig = PSPConfig::produceConfiguration($oDB, $oTI->getClientConfig()->getID(), $oTI->getClientConfig()->getAccountConfig()->getID(), $pspID, $routeConfigID);
        }
        else {
            $oPSPConfig = PSPConfig::produceConfig($oDB, $oTI->getClientConfig()->getID(), $oTI->getClientConfig()->getAccountConfig()->getID(), $pspID);
        }
        return $oPSPConfig;
    }

    /**
     * Function to check txn status
     *
     * @param RDB $_OBJ_DB
     * @param int $stateId
     * @param int $txnId
     * @return string
     */
    public static function checkTxnStatus(RDB $_OBJ_DB,int $paymentMethod,int $txnId): string
    {
        if ($paymentMethod == Constants::iPAYMENT_TYPE_OFFLINE) {
            $stateId = Constants::iPAYMENT_PENDING_STATE;
        } else
        {
            $sql = 'SELECT auto_capture
         		FROM Log'.sSCHEMA_POSTFIX.'.Transaction_Tbl
				WHERE id = '. $txnId;
            $RS = $_OBJ_DB->getName($sql);
            $auto_capture = (int)$RS['AUTO_CAPTURE'];

            if($auto_capture === AutoCaptureType::ePSPLevelAutoCapt ){
                //if psp level capture then check for 2001 is logged and fraud states are not logged, if so payment is complete
                $stateId = Constants::iPAYMENT_CAPTURED_STATE;
            }else{
                //if other capture then check for 2000 is logged and fraud states are not logged, if so payment is complete
                $stateId = Constants::iPAYMENT_ACCEPTED_STATE;
            }
        }


        $sql = 'WITH WT1 as
                         (SELECT DISTINCT stateid, m.id
                          FROM Log'.sSCHEMA_POSTFIX.'.Message_Tbl m
                          WHERE txnid = '.$txnId.'
                            and M.enabled = true),
                     WT2 as (SELECT payment_status,
                                    (
                                        SELECT fraud_status
                                        FROM (SELECT stateid as fraud_status, rank() over (order by id desc)
                                              FROM WT1
                                              WHERE stateid in (
                                                                    '.Constants::iPRE_FRAUD_CHECK_ACCEPTED_STATE.',
                                                                    '.Constants::iPRE_FRAUD_CHECK_UNAVAILABLE_STATE.',
                                                                    '.Constants::iPRE_FRAUD_CHECK_UNKNOWN_STATE.',
                                                                    '.Constants::iPRE_FRAUD_CHECK_REVIEW_STATE.',
                                                                    '.Constants::iPRE_FRAUD_CHECK_REJECTED_STATE.',
                                                                    '.Constants::iPRE_FRAUD_CHECK_CONNECTION_FAILED_STATE.',
                                                                    '.Constants::iPOST_AUTH_FRAUD_CHECK_REQUIRED_STATE.',
                                                                    '.Constants::iPOST_FRAUD_CHECK_ACCEPTED_STATE.',
                                                                    '.Constants::iPOST_FRAUD_CHECK_UNAVAILABLE_STATE.',
                                                                    '.Constants::iPOST_FRAUD_CHECK_UNKNOWN_STATE.',
                                                                    '.Constants::iPOST_FRAUD_CHECK_REVIEW_STATE.',
                                                                    '.Constants::iPOST_FRAUD_CHECK_REJECTED_STATE.',
                                                                    '.Constants::iPOST_FRAUD_CHECK_CONNECTION_FAILED_STATE.',
                                                                    '.Constants::iPOST_FRAUD_CHECK_SKIP_RULE_MATCHED_STATE.'
                                                  )) s1
                                        where s1.rank = 1)
                             FROM (SELECT stateid as payment_status, rank() over (order by id desc)
                                   FROM WT1
                                   WHERE stateid in (
                                                    '.Constants::iPAYMENT_REJECTED_STATE.',
                                                    '.Constants::iPAYMENT_CAPTURE_FAILED_STATE.',
                                                    '.Constants::iPAYMENT_REQUEST_EXPIRED_STATE.', 
                                                    '.$stateId.'
                                                )) s
                             where s.rank = 1
                     )
                SELECT *
                FROM WT2;';

        $res = $_OBJ_DB->getName($sql);
        $fraudStatus = (int)$res['FRAUD_STATUS'];
        $paymentStatus = (int)$res['PAYMENT_STATUS'];
        $TransactionStatus = 'Pending';
        if($fraudStatus !== 0 || $paymentStatus !== 0)
        {
            if($fraudStatus === Constants::iPOST_AUTH_FRAUD_CHECK_REQUIRED_STATE)
            {
                $TransactionStatus = 'Pending';
            }
            elseif ( true === in_array($fraudStatus, [Constants::iPRE_FRAUD_CHECK_REJECTED_STATE, Constants::iPOST_FRAUD_CHECK_REJECTED_STATE] ))
            {
                $TransactionStatus = 'Failed';
            }
            elseif ($paymentStatus === $stateId)
            {
                $TransactionStatus = 'Complete';
            }
            else
            {
                $TransactionStatus = 'Failed';
            }
        }

        return $TransactionStatus;
    }

    /**
     * Function to create linked transaction xml
     *
     * @param RDB $_OBJ_DB
     * @param int $linkedTxnId
     * @param int $txnId
     * @param int $paymentMethod
     * @return string
     */
    public static function getLinkedTransactions(RDB $_OBJ_DB,int $linkedTxnId,int $txnId,int $paymentMethod) : string
    {
        $linkedTxnData     = [$txnId,$linkedTxnId];
        $TxnPaymentStatus  = [];
        $linkedTxnXml  	   = '<linked_transactions>';
        foreach($linkedTxnData as $linkedTxn){
            $status = self::checkTxnStatus($_OBJ_DB,$paymentMethod,$linkedTxn);
            array_push($TxnPaymentStatus, $status);
            $linkedTxnXml  .= '<transaction_details>';
            $linkedTxnXml  .= '<id>'.$linkedTxn.'</id>';
            $linkedTxnXml  .= '<status>'.$status.'</status>';
            $linkedTxnXml  .= '</transaction_details>';
        }
        $linkedTxnXml .= "</linked_transactions>";

        $checkPaymentStatus = array_count_values($TxnPaymentStatus);
        if($checkPaymentStatus['Complete'] == 2){
            $paymentStatus = 'Complete';
        }
        else if(in_array('Failed',$TxnPaymentStatus)){
            $paymentStatus = 'Failed';
        }else {
            $paymentStatus = 'Pending';
        }
        $paymentStatusXML = '<payment_status>' . $paymentStatus . '</payment_status>';

        return $paymentStatusXML.$linkedTxnXml;
    }

	 /***
     * Function is used to process evaluate fetch-balance element value.
     *
     * @param	$obj_TxnInfo		Transaction Info
     * @param	$cardid				integer  card-id
     *
     * @return string autoFetchBalance bool
     */
	public static function isAutoFetchBalance(TxnInfo $obj_TxnInfo, int $cardId): bool
    {
		$isAutoFetchBalance = false;

		$autoFetchBalance = $obj_TxnInfo->getClientConfig()->getAdditionalProperties(0,"autoFetchBalance");
		$fetchBalanceUserType = $obj_TxnInfo->getClientConfig()->getAdditionalProperties(0,"fetchBalanceUserType");
		$fetchBalancePaymentMethods = $obj_TxnInfo->getClientConfig()->getAdditionalProperties(0,"fetchBalancePaymentMethods");

		if (isset($fetchBalanceUserType) === true) {
			$fetchBalanceUserType = json_decode($fetchBalanceUserType, true);
		}

		if (isset($fetchBalancePaymentMethods) === true) {
			$fetchBalancePaymentMethods = json_decode($fetchBalancePaymentMethods, true);
		}

		if($obj_TxnInfo->getAdditionalData() !== null)
		{
			foreach ($obj_TxnInfo->getAdditionalData() as $key=>$value)
			{
				if($key === "customer-type"){
					$customerType = $value;
					break;
				}
			}
		}

		if($autoFetchBalance === "true" && in_array($customerType, $fetchBalanceUserType) && in_array($cardId, $fetchBalancePaymentMethods)){
			$isAutoFetchBalance = true;
		}
		return $isAutoFetchBalance;
    }

    public function saveOrderDetails(RDB $_OBJ_DB, TxnInfo $obj_TxnInfo, CountryConfig $obj_CountryConfig, SimpleDOMElement $obj_orderDom, TxnPassbook $txnPassbookObj = NULL) : bool
    {
        try {
            $lineItemCnt = count($obj_orderDom->{'line-item'});
            for ($j=0; $j<$lineItemCnt; $j++ )
            {
                $ticketNumber = !empty($obj_orderDom->{'line-item'}[$j]->product["order-ref"]) ? (string) $obj_orderDom->{'line-item'}[$j]->product["order-ref"] : $obj_TxnInfo->getOrderId();
                if (!$obj_TxnInfo->isTicketNumberIsAlreadyLogged($_OBJ_DB, $ticketNumber)) {
                    $data['orders'][0]['product-sku'] = (string)$obj_orderDom->{'line-item'}[$j]->product["sku"];
                    $data['orders'][0]['orderref'] = $ticketNumber;
                    $data['orders'][0]['product-name'] = (string)$obj_orderDom->{'line-item'}[$j]->product->name;
                    $data['orders'][0]['type'] = (empty($obj_orderDom->{'line-item'}[$j]->product->type) === false) ? (string)$obj_orderDom->{'line-item'}[$j]->product->type : '100';
                    $data['orders'][0]['product-description'] = (string)$obj_orderDom->{'line-item'}[$j]->product->description;
                    $data['orders'][0]['product-image-url'] = (string)$obj_orderDom->{'line-item'}[$j]->product->{'image-url'};
                    $data['orders'][0]['amount'] = (float)$obj_orderDom->{'line-item'}[$j]->amount;
                    $collectiveFees = 0;
                    if ($obj_orderDom->{'line-item'}[$j]->fees->fee) {
                        $feeCnt = count($obj_orderDom->{'line-item'}[$j]->fees->fee);
                        for ($k = 0; $k < $feeCnt; $k++) {
                            $collectiveFees += $obj_orderDom->{'line-item'}[$j]->fees->fee[$k];
                        }
                    }
                    $data['orders'][0]['fees'] = (float)$collectiveFees;
                    $data['orders'][0]['country-id'] = $obj_CountryConfig->getID();
                    $data['orders'][0]['points'] = (float)$obj_orderDom->{'line-item'}[$j]->points;
                    $data['orders'][0]['reward'] = (float)$obj_orderDom->{'line-item'}[$j]->reward;
                    $data['orders'][0]['quantity'] = (float)$obj_orderDom->{'line-item'}[$j]->quantity;

                    if (isset($obj_orderDom->{'line-item'}[$j]->{'additional-data'})) {
                        $orderAdditionalDataCnt = count($obj_orderDom->{'line-item'}[$j]->{'additional-data'}->children());
                        for ($k = 0; $k < $orderAdditionalDataCnt; $k++) {
                            $data['orders'][0]['additionaldata'][$k]['name'] = (string)$obj_orderDom->{'line-item'}[$j]->{'additional-data'}->param[$k]['name'];
                            $data['orders'][0]['additionaldata'][$k]['value'] = (string)$obj_orderDom->{'line-item'}[$j]->{'additional-data'}->param[$k];
                            $data['orders'][0]['additionaldata'][$k]['type'] = (string)'Order';
                        }
                    }

                    $order_id = $obj_TxnInfo->setOrderDetails($_OBJ_DB, $data['orders']);

                    if ($obj_orderDom->{'line-item'}[$j]->product->{'airline-data'}->{'billing-summary'}) {
                        $billingSummary = $obj_orderDom->{'line-item'}[$j]->product->{'airline-data'}->{'billing-summary'};
                        $fareCnt = 0;
                        if($billingSummary->{'fare-detail'}->fare !== null)
                        {
                            $fareCnt = count($billingSummary->{'fare-detail'}->fare);
                        }

                        if ($fareCnt > 0) {
                            for ($k = 0; $k < $fareCnt; $k++) {
                                $fare = $billingSummary->{'fare-detail'}->fare[$k];
                                $fareArr = array();
                                $fareArr['order_id'] = $order_id;
                                $fareArr['bill_type'] = (string)'Fare';
                                $fareArr['type'] = (string)$fare->{'type'};
                                $fareArr['profile_seq'] = (int)$fare->{'profile-seq'};
                                $fareArr['trip_tag'] = (int)$fare->{'trip-tag'};
                                $fareArr['trip_seq'] = (int)$fare->{'trip-seq'};
                                $fareArr['description'] = (string)$fare->{'description'};
                                $fareArr['currency'] = (string)$fare->{'currency'};
                                $fareArr['amount'] = (string)$fare->{'amount'};
                                $fareArr['product_code'] = (string)$fare->{'product-code'};
                                $fareArr['product_category'] = (string)$fare->{'product-category'};
                                $fareArr['product_item'] = (string)$fare->{'product-item'};
                                $obj_TxnInfo->setBillingSummary($_OBJ_DB, $fareArr);
                            }
                        }

                        $addOnCnt = ($billingSummary->{'add-ons'}->{'add-on'}) ? count($billingSummary->{'add-ons'}->{'add-on'}) : 0;
                        if ($addOnCnt > 0) {
                            for ($k = 0; $k < $addOnCnt; $k++) {
                                $addOn = $billingSummary->{'add-ons'}->{'add-on'}[$k];
                                $addOnArr = array();
                                $addOnArr['order_id'] = $order_id;
                                $addOnArr['bill_type'] = (string)'Add-on';
                                $addOnArr['type'] = (int)$addOn->{'type'};
                                $addOnArr['profile_seq'] = $addOn->{'profile-seq'};
                                $addOnArr['trip_tag'] = $addOn->{'trip-tag'};
                                $addOnArr['trip_seq'] = $addOn->{'trip-seq'};
                                $addOnArr['description'] = (string)$addOn->{'description'};
                                $addOnArr['currency'] = (string)$addOn->{'currency'};
                                $addOnArr['amount'] = (string)$addOn->{'amount'};
                                $addOnArr['product_code'] = (string)$addOn->{'product-code'};
                                $addOnArr['product_category'] = (string)$addOn->{'product-category'};
                                $addOnArr['product_item'] = (string)$addOn->{'product-item'};
                                $obj_TxnInfo->setBillingSummary($_OBJ_DB, $addOnArr);
                            }
                        }
                    }

                    $tripCnt = count($obj_orderDom->{'line-item'}[$j]->product->{'airline-data'}->trips->trip);
                    if ($tripCnt > 0) {
                        for ($k = 0; $k < $tripCnt; $k++) {
                            $flight = $obj_orderDom->{'line-item'}[$j]->product->{'airline-data'}->trips->trip[$k];
                            $service_level = array_search(strtoupper((string) $flight->{'service-level'}),array_map('strtoupper', Constants::aServiceLevelAndIdMapp));
                            if($service_level === false) { $service_level = '0'; }
                            $data['flights']['service_level'] = $service_level;
                            $data['flights']['service_class'] = (string)$flight->{'booking-class'};
                            $data['flights']['arrival_date'] = (string)$flight->{'arrival-time'};
                            $data['flights']['departure_date'] = (string)$flight->{'departure-time'};
                            $data['flights']['departure_terminal'] = (string)$flight->origin['terminal'];
                            $data['flights']['arrival_terminal'] = (string)$flight->destination['terminal'];
                            $data['flights']['departure_city'] = (string)$flight->origin;
                            $data['flights']['arrival_city'] = (string)$flight->destination;
                            $data['flights']['departure_timezone'] = (string)$flight->origin['time-zone'];
                            $data['flights']['arrival_timezone'] = (string)$flight->destination['time-zone'];

                            $data['flights']['departure_airport'] = (string)$flight->origin['external-id'];
                            $data['flights']['arrival_airport'] = (string)$flight->destination['external-id'];
                            $data['flights']['op_airline_code'] = (string)$flight->transportation->carriers->carrier['code'];
                            $data['flights']['mkt_airline_code'] = (string)$flight->transportation['code'];
                            $data['flights']['aircraft_type'] = (string)$flight->transportation->carriers->carrier['type-id'];
                            $data['flights']['op_flight_number'] = (string)$flight->transportation['number'];
                            $data['flights']['mkt_flight_number'] = (string)$flight->transportation->carriers->carrier->number;
                            $data['flights']['departure_country'] = (int)$flight->origin['country-id'];
                            $data['flights']['arrival_country'] = (int)$flight->destination['country-id'];
                            $data['flights']['order_id'] = $order_id;
                            $data['flights']['tag'] = (string)$flight['tag'];
                            $data['flights']['trip_count'] = (string)$flight['seq'];

                            if (count($flight->{'additional-data'}) > 0) {
                                $flightAdditionalDataCnt = count($flight->{'additional-data'}->children());
                                for ($l = 0; $l < $flightAdditionalDataCnt; $l++) {
                                    $data['additional'][$l]['name'] = (string)$flight->{'additional-data'}->param[$l]['name'];
                                    $data['additional'][$l]['value'] = (string)$flight->{'additional-data'}->param[$l];
                                    $data['additional'][$l]['type'] = (string)"Flight";
                                }
                            } else {
                                $data['additional'] = array();
                            }
                            $flight = $obj_TxnInfo->setFlightDetails($_OBJ_DB, $data['flights'], $data['additional']);
                        }
                    }

                    $profileCnt = count($obj_orderDom->{'line-item'}[$j]->product->{'airline-data'}->profiles->profile);
                    if ($profileCnt > 0) {
                        for ($k = 0; $k < $profileCnt; $k++) {
                            $profile = $obj_orderDom->{'line-item'}[$j]->product->{'airline-data'}->profiles->profile[$k];
                            $data['passenger']['seq'] = (integer)$profile->{'seq'};
                            $data['passenger']['first_name'] = (string)$profile->{'first-name'};
                            $data['passenger']['last_name'] = (string)$profile->{'last-name'};
                            $data['passenger']['type'] = (string)$profile->{'type'};
                            $data['passenger']['amount'] = (integer)$profile->{'amount'};
                            $data['passenger']['order_id'] = $order_id;
                            $data['passenger']['title'] = (string)$profile->{'title'};
                            $data['passenger']['email'] = (string)$profile->{'contact-info'}->email;
                            $data['passenger']['mobile'] = (string)$profile->{'contact-info'}->mobile;
                            $data['passenger']['country_id'] = (string)$profile->{'contact-info'}->mobile["country-id"];

                            if (count($profile->{'additional-data'}) > 0) {
                                $profileAdditionalDataChildCnt = count($profile->{'additional-data'}->children());
                                for ($l = 0; $l < $profileAdditionalDataChildCnt; $l++) {
                                    $data['additionalp'][$l]['name'] = (string)$profile->{'additional-data'}->param[$l]['name'];
                                    $data['additionalp'][$l]['value'] = (string)$profile->{'additional-data'}->param[$l];
                                    $data['additionalp'][$l]['type'] = (string)"Passenger";
                                }
                            } else {
                                $data['additionalp'] = array();
                            }
                            $passenger = $obj_TxnInfo->setPassengerDetails($_OBJ_DB, $data['passenger'], $data['additionalp']);
                        }
                    }
                    if ($obj_TxnInfo->useAutoCapture() === AutoCaptureType::eTicketLevelAutoCapt) {

                        $passbookEntry = new PassbookEntry
                        (
                            NULL,
                            $data['orders'][0]['amount'],
                            $obj_TxnInfo->getCurrencyConfig()->getID(),
                            Constants::iCaptureRequested,
                            $ticketNumber,
                            'log.order_tbl  - orderref'
                        );
                        if ($txnPassbookObj instanceof TxnPassbook) {
                            try {
                                $txnPassbookObj->addEntry($passbookEntry);
                            } catch (Exception $e) {
                                trigger_error($e, E_USER_WARNING);
                                throw $e;
                            }
                        }
                    }
                }
            }

            $shippingAddressCnt = count($obj_orderDom->{'shipping-address'});
            if($shippingAddressCnt > 0)
            {
                for ($j=0; $j<$shippingAddressCnt; $j++ )
                {
                    $data['shipping_address'][$j]['first_name'] = (string) $obj_orderDom->{'shipping-address'}[$j]->name;
                    $data['shipping_address'][$j]['last_name'] = "";
                    $data['shipping_address'][$j]['street'] = (string) $obj_orderDom->{'shipping-address'}[$j]->street;
                    $data['shipping_address'][$j]['street2'] = (string) $obj_orderDom->{'shipping-address'}[$j]->street2;
                    $data['shipping_address'][$j]['city'] = (string) $obj_orderDom->{'shipping-address'}[$j]->city;
                    $data['shipping_address'][$j]['state'] = (string) $obj_orderDom->{'shipping-address'}[$j]->state;
                    $data['shipping_address'][$j]['zip'] = (string) $obj_orderDom->{'shipping-address'}[$j]->zip;
                    $data['shipping_address'][$j]['country'] = (string) $obj_orderDom->{'shipping-address'}[$j]->country;
                    $data['shipping_address'][$j]['reference_type'] = (string) "order";
                    if($order_id!="")
                    {
                        $data['shipping_address'][$j]['reference_id'] = $order_id;
                    }
                }
                $shipping_id = $obj_TxnInfo->setShippingDetails($_OBJ_DB, $data['shipping_address']);
            }
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }
}
?>