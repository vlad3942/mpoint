<?php
/**
 * The GoMobile Client package provides a full featured API which handles all communicationg
 * with GoMobile.
 * The API provides methods for receiving the following messages type from GoMobile:
 *	 1. MO-SMS
 *	 5. Delivery Report
 *	 6. MO-MMS
 *	10. MO-Push Notification
 * Additionally the API provides methods for sending any of the following MT types:
 *	 2. MT-SMS
 *	 3. MT-WAP Push
 *	 4. MT-Binary SMS
 *	 7. MT-MMS
 *	11. MT-Push Notification
 * GoMobile currently has support for Apple Push Notification and Google Cloud Message (GCM).
 *
 * @author Jonatan Evald Buus
 * @package GoMobile
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com Cellpoint Mobile
 * @version 1.30
 *
 */

/**
 * Data class for holding the Connection Info for communicating with GoMobile.
 *
 * @package GoMobile
 *
 */
class GoMobileConnInfo extends HTTPConnInfo
{
	/**
	 * Path to the directory where the log file should be written
	 * Log files are named gomobile_yyyy-mm-dd.log
	 *
	 * @var string
	 */
	private $_sLogPath;

	/**
	 * Boolean flags defining how exceptions and log entries are handled
	 * 	1. Log entries are written to the log file and exceptions are re-thrown
	 *	2. Log entries and exceptions are dumped to stdout
	 *
	 * @var integer
	 */
	private $_iMode;

	/**
	 * Default constructor
	 *
	 * @param string $p 	Protocol used for the connection (tcp or http)
	 * @param string $h 	Host or IP address the remote server can be contacted at
	 * @param integer $prt 	Port the remote server is listening on
	 * @param integer $to 	Number of seconds before the connection times out either when establishing the connection or when awaiting a reply from the remote server
	 * @param string $pth 	The path to the application on the remote server
	 * @param string $mtd 	Method used for sending the HTTP Request (GET or POST)
	 * @param string $ct 	Content Type that the body should be encoded in (text/xml or application/x-www-form-urlencoded)
	 * @param string $un 	Username for authenticating with user to the remote server
	 * @param string $pw 	Password for authenticating with user to the remote server
	 * @param string $lp 	Path to the directory where the log file should be written
	 * @param string $md 	Mode to run the GoMobile Client in: (1 - production, 2 - debug)
	 *
	 * @throws 				E_USER_ERROR
	 */
	public function __construct($p, $h, $prt, $to, $pth, $mtd, $ct, $un, $pw, $lp, $md)
	{
		parent::__construct($p, $h, $prt, $to, $pth, $mtd, $ct, $un, $pw);

		/* ----- Input Validation Start ----- */
		// Validate Log Path
		if (empty($lp) === true)	{ trigger_error("Undefined Log Path", E_USER_ERROR); }
		if (is_dir($lp) === false)	{ trigger_error("Specified log path: ". $lp ." is NOT a directory", E_USER_ERROR); }
		/**
		 * Verify write permissions on path
		 * Please note: fopen() is used rather than is_writable() due to a bug on Windows for is_writable().
		 * Please refer to bug report: http://bugs.php.net/bug.php?id=27609 for more information
		 */
		$file = uniqid("gomobile_");
		// Open file for appending, suppress errors
		$fp = @fopen($lp . $file, "a");
		// Write permissions OK, close file handle
		if (is_resource($fp) === true)
		{
			fclose($fp);
			unlink($lp . $file);
		}
		else { trigger_error("Write permissions not set for log path: ". $lp, E_USER_ERROR); }
		/* ----- Input Validation End ----- */

		if(substr($lp, -1) != "/") { $lp .= "/"; }
		$this->_sLogPath = (string) $lp;
		$this->_iMode = (integer) $md;
	}

	/**
	 * Produces a Connection Info object which holds the necessary data for communicating with
	 * a GoMobile using the HTTP Client.
	 *
	 * This method is an overloaded method and a accepts a number of parameters as follows:	<br>
	 * <b>1: Connection details passed as an array: </b><br>
	 * 	array $ci 	List of connection details. The array must have the following keys: <br>
	 * 				"protocol", the protocol used for communicating with GoMobile, should always be: http <br>
	 * 				"host", the host address for GoMobile, should always be: gomobile.cellpointmobile.com <br>
	 * 				"port", the port that requestes are sent to, should always be: 8000 <br>
	 * 				"timeout", general timeout in seconds. The timeout is used in the following instances:
	 * 					- When opening a new connection to GoMobile
	 * 					- When retrieving the response from GoMobile
	 * 				"path", the server side path where requests are sent to, should always be: / <br>
	 * 				"method", the HTTP method used for the data transfer, should always be: POST <br>
	 * 				"contenttype", the HTTP Mimetype of the data, should always be: text/xml <br>
	 * 				"username", the username used for authenticating the client with GoMobile. <br>
	 * 				"password", the password used for generating the checksum which is sent to GoMobile for authentication <br>
	 * 				"logpath", the path to the directory where the API will write its log files. <br>
	 * 				"mode", the logging mode the API should use:
	 * 					1. Write log entry to file
	 * 					2. Output log entry to screen
	 * 					3. Write log entry to file and output to screen
	 *
	 * <b>11: Connection details passed as individual parameters: </b><br>
	 * 	string $p 		Protocol used for the connection to GoMobile, should always be: http <br>
	 * 	string $h 		GoMobile's Host or IP address, should always be: gomobile.cellpointmobile.com <br>
	 * 	integer $prt 	Port that requestes are sent to, should always be: 8000 <br>
	 * 	integer $to 	Number of seconds before the connection times out. The timeout is used in the following instances:
	 * 						- When opening a new connection to GoMobile
	 * 						- When retrieving the response from GoMobile
	 * 	string $pth 	The path on GoMobile where requests are sent to, should always be: / <br>
	 * 	string $mtd 	HTTP method used for the data transfer, should always be: POST <br>
	 * 	string $ct 		HTTP Mimetype of the data, should always be: text/xml <br>
	 * 	string $un 		Username for authenticating with GoMobile <br>
	 * 	string $pw 		Password for authenticating with GoMobile <br>
	 * 	string $lp 		Path to the directory where the the API will write its log files. <br>
	 * 	string $md 		Mode to run the GoMobile Client in: (1 - production, 2 - debug) <br>
	 *
	 * @see 	GoMobileClient
	 *
	 * @return 	GoMobileConnInfo
	 *
	 * @throws 	E_USER_ERROR
	 */
	public static function produceConnInfo()
	{
		$aArgs = func_get_args();
		// Parameters passed as array
		if(count($aArgs) == 1 && is_array($aArgs[0]) === true)
		{
			if (array_key_exists("mode", $aArgs[0]) === true) 		{ $aArgs[10] = $aArgs[0]["mode"]; }
			if (array_key_exists("logpath", $aArgs[0]) === true) 	{ $aArgs[9] = $aArgs[0]["logpath"]; }
			if (array_key_exists("password", $aArgs[0]) === true) 	{ $aArgs[8] = $aArgs[0]["password"]; }
			if (array_key_exists("username", $aArgs[0]) === true) 	{ $aArgs[7] = $aArgs[0]["username"]; }
			if (array_key_exists("contenttype", $aArgs[0]) === true){ $aArgs[6] = $aArgs[0]["contenttype"]; }
			if (array_key_exists("method", $aArgs[0]) === true) 	{ $aArgs[5] = $aArgs[0]["method"]; }
			if (array_key_exists("path", $aArgs[0]) === true) 		{ $aArgs[4] = $aArgs[0]["path"]; }
			if (array_key_exists("timeout", $aArgs[0]) === true) 	{ $aArgs[3] = $aArgs[0]["timeout"]; }
			if (array_key_exists("port", $aArgs[0]) === true) 		{ $aArgs[2] = $aArgs[0]["port"]; }
			if (array_key_exists("host", $aArgs[0]) === true) 		{ $aArgs[1] = $aArgs[0]["host"]; }
			if (array_key_exists("protocol", $aArgs[0]) === true) 	{ $aArgs[0] = $aArgs[0]["protocol"]; }
		}

		switch(count($aArgs) )
		{
		case (11):	// Parameters passed individually
			return new GoMobileConnInfo($aArgs[0], $aArgs[1], $aArgs[2], $aArgs[3], $aArgs[4], $aArgs[5], $aArgs[6], $aArgs[7], $aArgs[8], $aArgs[9], $aArgs[10]);
			break;
		default:	// Error: Invalid number of arguments
			trigger_error("Invalid number of arguments: ". count($aArgs), E_USER_ERROR);
			return null;
			break;
		}
	}

	/**
	 * Retrieves the Path to the Directory where the log file should be written
	 *
	 * @return string
	 */
	public function getLogPath() { return $this->_sLogPath; }
	/**
	 * Retrieves the Boolean flags which defines how exceptions and log entries are handled
	 * 	1. Log entries are written to the log file and exceptions are re-thrown
	 *	2. Log entries and exceptions are dumped to stdout
	 * 	3. Log entries and exceptions are dumped to stdout as well as written to the log file, furthermore exceptions are re-thrown
	 *
	 * @return integer
	 */
	public function getMode() { return $this->_iMode; }
}

/**
 * Abstract super class for GoMobile specific data class for message information.
 *
 * For MO-SMS the class also offers convenience methods which can be used to
 * for viewing the configuration data received from GoMobile.
 * These methods includes:
 * 	- getIDC, returns the International Dialling Code for the Country as supplied by GoMobile
 * 	- getCurrency, returns the Currency used in the Country as supplied by GoMobile
 *
 */
abstract class GoMobileMessage
{
	const iMO_SMS = 1;
	const iMT_SMS = 2;
	const iMT_WAP_PUSH = 3;
	const iMT_BINARY_SMS = 4;
	const iDELIVERY_REPORT = 5;
	const iMO_MMS = 6;
	const iMT_MMS = 7;
	const iINCOMING_EMAIL = 8;
	const iOUTGOING_EMAIL = 9;
	const iMO_PUSH_NOTIFICATION = 10;
	const iMT_PUSH_NOTIFICATION = 11;

	/**
	 * ID of the Message
	 *
	 * @var string
	 */
	private $_sClientID;
	/**
	 * Message type:
	 * 	1. MO-SMS
	 * 	2. MT-SMS
	 * 	3. MT-WAP Push
	 * 	4. Binary MT-SMS
	 * 	5. Delivery Report
	 * 	6. MO-MMS
	 * 	7. MT-MMS
	 * 	8. Incoming E-Mail
	 * 	9. Outgoing E-Mail
	 * 10. MO-Push Notification
	 * 11. MT-Push Notification
	 *
	 * @var integer
	 */
	private $_iType;

	/**
	 * Internal ID of the country the message will be sent to
	 *
	 * @var integer
	 */
	private $_iCountry;
	/**
	 * Currency for the Country the message is valid in
	 *
	 * @var string
	 */
	private $_sCurrency;
	/**
	 * International Dialling code for the Country the message is valid in
	 *
	 * @var integer
	 */
	private $_iIDC;

	/**
	 * Comma separated list of status codes returned for the message by GoMobile
	 * Defaults to "-1" if no status codes has been received.
	 *
	 * @var string
	 */
	private $_sReturnCodes = "-1";

	/**
	 * Unique ID assigned to the message by GoMobile.
	 * Defaults to -1 if no ID has been assigned to the message.
	 *
	 * @var integer
	 */
	private $_iGoMobileID = -1;

	/**
	 * Default Constructor
	 *
	 * @param integer $t 	Message type:
	 * 						 1. MO-SMS
	 * 						 2. MT-SMS
	 * 						 3. MT-WAP Push
	 * 						 4. MT-Binary SMS
	 * 						 5. Delivery Report
	 * 						 6. MO-MMS
	 * 						 7. MT-MMS
	 * 						10. MO-Push Notification
	 * 						11. MT-Push Notification
	 * @param integer $c 	Country the message was received from / will be sent to
	 */
	public function __construct($t, $c)
	{
		$this->_sClientID = md5(uniqid("", true) );
		$this->_iType = (integer) $t;
		$this->_iCountry = (integer) $c;
	}

	/**
	 * Produces a Message Info object which holds the relevant data for the message to send
	 * via the GoMobile.
	 * This method is an overloaded method and its exact behaviour will depend on the number of parameters
	 * passed to the method.
	 *
	 * The method either accepts a number of parameters passed individually which is used to construct an SMS, Delivery Report, MMS or Push Notification
	 * of the correct type or an XML document which is parsed and then used to construct an MO-SMS, a Delivery Report, an MO-MMS or an MO-Push Notification.
	 * The expected parameters are as follows:<br>
	 * 	<b>1: MO-SMS, Delivery Report, MO-MMS or MO-Push Notification</b><br>
	 * 		string $xml		XML Document with the MO-SMS, Delivery Report, MO-MMS or MO-Push Notification received from GoMobile. <br>
	 * 	<b>5: Parameters passed individually for an MO-Push Notification: </b><br>
	 * 		integer $t 		Message type: (10 - MO-Push Notification) <br>
	 * 		string $ch 		Channel the message will be routed via, this is the short code for SMS messages <br>
	 * 		string $kw 		Keyword the message belongs to <br>
	 * 		string $addr 	Address to the End-User's device <br>
	 * 		string $b 		Message that will be sent to the recipient, should be max 235 characters for Apple Push Notification and 4096 characters for Google Cloud Messages<br>
	 * 	<b>6: Parameters passed individually for a Delivery Report</b><br>
	 * 		integer $c 		Internal ID of the country the message will be sent to <br>
	 * 		string $addr 	Address to the End-User's device <br>
	 * 		integer $mtid 	ID of the Transaction the Delivery Report is for <br>
	 * 		integer $id 	GoMobile's ID for the Delivery Report <br>
	 * 		integer $s 		Code Indicating the Status for the MT that the Delivery Report is valid for <br>
	 * 		string $d 		Textual description of the Status Code <br>
	 * 	<b>6: Parameters passed individually for an MT-Push Notification: </b><br>
	 * 		integer $t 		Message type: (11 - MT-Push Notification) <br>
	 * 		string $ch 		Channel the message will be routed via, this is the short code for SMS messages <br>
	 * 		string $kw 		Keyword the message belongs to <br>
	 * 		string $addr 	Address to the End-User's device <br>
	 * 		string $b 		Message that will be sent to the recipient, should be max 235 characters for Apple Push Notification and 4096 characters for Google Cloud Messages<br>
	 * 		string $id 		ID of the corresponding Mobile Originated transaction <br>
	 * 	<b>7: Parameters passed individually for a Delivery Report: </b><br>
	 * 		integer $t 		Message type: 5 - Delivery Report <br>
	 * 		integer $c 		Internal ID of the country the message will be sent to <br>
	 * 		string $addr 	Address to the End-User's device <br>
	 * 		integer $mtid 	ID of the Transaction the Delivery Report is for <br>
	 * 		integer $id 	GoMobile's ID for the Delivery Report <br>
	 * 		integer $s 		Code Indicating the Status for the MT that the Delivery Report is valid for <br>
	 * 		string $d 		Textual description of the Status Code <br>
	 * 	<b>8: Parameters passed individually for an SMS but with no MO ID: </b><br>
	 * 		integer $t 		Message type: (1 - MO-SMS, 2 - MT-SMS) <br>
	 * 		integer $c 		Internal ID of the country the message will be sent to <br>
	 * 		integer $o 		Internal ID of the Operator the message will be sent through <br>
	 * 		string $ch 		Channel the message will be routed via, this is the short code for SMS messages <br>
	 * 		string $kw 		Keyword the message belongs to <br>
	 * 		integer $p 		Price in country's smallest currency <br>
	 * 		string $addr 	Address to the End-User's device <br>
	 * 		string $b 		Message that will be sent to the recipient, should be max 160 characters <br>
	 * 		string $id 		ID of the corresponding Mobile Originated transaction <br>
	 * 		string $misc 	WAP Push URL or Hex Encoded UDH <br>
	 *  <b>9: Message Parameters passed individually for an SMS with either WAP Push URL or Hex Encoded UDH, a standard SMS with MO ID or an MMS without MO ID: </b><br>
	 * 		integer $t 		Message type: (1 - MO-SMS, 2 - MT-SMS, 3 - MT-WAP Push, 4 - MT-Binary SMS, 6 - MO-MMs, 7 - MT-MMS) <br>
	 * 		integer $c 		Internal ID of the country the message will be sent to <br>
	 * 		integer $o 		Internal ID of the Operator the message will be sent through <br>
	 * 		string $ch 		Channel the message will be routed via, this is the short code for SMS messages <br>
	 * 		string $kw 		Keyword the message belongs to <br>
	 * 		integer $p 		Price in country's smallest currency <br>
	 * 		string $addr 	Address to the End-User's device <br>
	 * 		string $misc1 	Message that will be sent to the recipient, should be max 160 characters for SMS or Subject that is part of the MMS<br>
	 * 		string $misc2 	ID of the corresponding Mobile Originated transaction, WAP Push URL, Hex Encoded UDH or Body for the MMS<br>
	 * <b>10: WAP Push or Binary SMS Parameters passed individually but with MO ID: </b><br>
	 * 		integer $t 		Message type: (3 - MT-WAP Push, 4 - MT-Binary SMS) <br>
	 * 		integer $c 		Internal ID of the country the message will be sent to <br>
	 * 		integer $o 		Internal ID of the Operator the message will be sent through <br>
	 * 		string $ch 		Channel the message will be routed via, this is the short code for SMS messages <br>
	 * 		string $kw 		Keyword the message belongs to <br>
	 * 		integer $p 		Price in country's smallest currency <br>
	 * 		string $addr 	Address to the End-User's device <br>
	 * 		string $misc1 	Message that will be sent to the recipient, should be max 160 characters for SMS or Subject that is part of the MMS<br>
	 * 		string $misc2 	ID of the corresponding Mobile Originated transaction for SMS or Body for the MMS<br>
	 * 		string $misc3 	WAP Push URL or Hex Encoded UDH for SMS or ID of the corresponding Mobile Originated transaction for MMS<br>
	 *
	 * @return 	GoMobileMessage
	 *
	 * @throws 	E_USER_ERROR
	 */
	public static function produceMessage($unkown)
	{
		$aArgs = func_get_args();
		$obj_MsgInfo = null;
		// Parameters passed as array
		if(count($aArgs) == 1 && is_array($aArgs[0]) === true)
		{
			$aArgs = array_values($aArgs[0]);
		}

		switch(count($aArgs) )
		{
		case (1):
			$obj_XML = simplexml_load_string(trim($unkown) );
			switch (intval($obj_XML->message["type"]) )
			{
			case (GoMobileMessage::iMO_SMS):				// MO-SMS
			case (GoMobileMessage::iMT_SMS):				// MT-SMS
			case (GoMobileMessage::iMT_WAP_PUSH):			// MT-WAP Push
			case (GoMobileMessage::iMT_BINARY_SMS):			// MT-Binary SMS
				$obj_MsgInfo = SMS::produceMessage($obj_XML);
				break;
			case (GoMobileMessage::iDELIVERY_REPORT):		// Delivery Report
				$obj_MsgInfo = DeliveryReport::produceMessage($obj_XML);
				break;
			case (GoMobileMessage::iMO_MMS):				// MO-MMS
			case (GoMobileMessage::iMT_MMS):				// MT-MMS
				$obj_MsgInfo = MMS::produceMessage($obj_XML);
				break;
			case (GoMobileMessage::iMO_PUSH_NOTIFICATION):	// MO-Push Notification
			case (GoMobileMessage::iMT_PUSH_NOTIFICATION):	// MT-Push Notification
				$obj_MsgInfo = PushNotification::produceMessage($obj_XML);
				break;
			default:	// Unsupported
				trigger_error("Message type: ". $obj_XML->message["type"] ." not supported", E_USER_ERROR);
				break;
			}
			break;
		case (5):	// Parameters passed individually for an MO-Push Notification or an MT-Push Notifcation with no MO ID
			$obj_MsgInfo = new PushNotification($aArgs[0], $aArgs[1], $aArgs[2], $aArgs[3], $aArgs[4]);
			break;
		case (6):	// Parameters passed individually for a Delivery Report or MT-Push Notification
			switch (intval($aArgs[0]) )
			{
			case (GoMobileMessage::iDELIVERY_REPORT):		// Delivery Report
				$obj_MsgInfo = new DeliveryReport($aArgs[0], $aArgs[1], $aArgs[2], $aArgs[3], $aArgs[4], $aArgs[5]);
				break;
			case (GoMobileMessage::iMT_PUSH_NOTIFICATION):	// MT-Push Notification
				$obj_MsgInfo = new PushNotification($aArgs[0], $aArgs[1], $aArgs[2], $aArgs[3], $aArgs[4], $aArgs[5]);
				break;
			default:	// Unsupported
				trigger_error("Message type: ". $obj_XML->message["type"] ." not supported", E_USER_ERROR);
				break;
			}
			break;
		case (7):	// Parameters passed individually for a Delivery Report including message type
			$obj_MsgInfo = new DeliveryReport($aArgs[1], $aArgs[2], $aArgs[3], $aArgs[4], $aArgs[5], $aArgs[6]);
			break;
		case (8):	// Parameters passed individually for an SMS but with no MO ID
			$obj_MsgInfo = new SMS($aArgs[0], $aArgs[1], $aArgs[2], $aArgs[3], $aArgs[4], $aArgs[5], $aArgs[6], $aArgs[7]);
			break;
		case (9):	// Message Parameters passed individually for an SMS with either WAP Push URL, Hex Encoded UDH, an SMS with MO ID or an MMS without MO ID
			switch ($aArgs[0])
			{
			case (2):	// MT-SMS with MO ID
				$obj_MsgInfo = new SMS($aArgs[0], $aArgs[1], $aArgs[2], $aArgs[3], $aArgs[4], $aArgs[5], $aArgs[6], $aArgs[7], $aArgs[8]);
				break;
			case (3):	// MT-WAP Push without MO ID
			case (4):	// MT-Binary SMS without MO ID
				$obj_MsgInfo = new SMS($aArgs[0], $aArgs[1], $aArgs[2], $aArgs[3], $aArgs[4], $aArgs[5], $aArgs[6], $aArgs[7], -1, $aArgs[8]);
				break;
			case (6):	// MO-MMS without MO ID
			case (7):	// MT-MMS without MO ID
				$obj_MsgInfo = new MMS($aArgs[0], $aArgs[1], $aArgs[2], $aArgs[3], $aArgs[4], $aArgs[5], $aArgs[6], $aArgs[7], -1, $aArgs[8]);
				break;
			default:
				break;
			}
			break;
		case (10):	// MT-WAP Push, MT-Binary SMS or MMS Parameters passed individually but with MO ID
			if ($aArgs[0] == 6 || $aArgs[0] == 7) { $obj_MsgInfo = new MMS($aArgs[0], $aArgs[1], $aArgs[2], $aArgs[3], $aArgs[4], $aArgs[5], $aArgs[6], $aArgs[7], $aArgs[8], $aArgs[9]); }
			else { $obj_MsgInfo = new SMS($aArgs[0], $aArgs[1], $aArgs[2], $aArgs[3], $aArgs[4], $aArgs[5], $aArgs[6], $aArgs[7], $aArgs[8], $aArgs[9]); }
			break;
		default:	// Error: Invalid number of arguments
			trigger_error("Invalid number of arguments: ". count($aArgs), E_USER_ERROR);
			break;
		}

		return $obj_MsgInfo;
	}

	/**
	 * Retrieves the ID of the Message
	 *
	 * @return string
	 */
	public function getClientID() { return $this->_sClientID; }
	/**
	 * Retrieves the Message type:
	 *	 1. MO-SMS
	 *	 2. MT-SMS
	 *	 3. MT-WAP Push
	 *	 4. Binary MT-SMS
	 *	 5. Delivery Report
	 *	 6. MO-MMS
	 *	 7. MT-MMS
	 *	10. MO-Push Notification
	 *	11. MT-Push Notification
	 *
	 * @return integer
	 */
	public function getType() { return $this->_iType; }
	/**
	 * Retrieves the Internal ID of the country the message will be sent to.
	 * Will be -1 for Push Notifications.
	 *
	 * @return integer
	 */
	public function getCountry() { return $this->_iCountry; }
	/**
	 * Retrieves the International Dialling Code for the Country the message is valid in
	 *
	 * @return integer
	 */
	public function getIDC() { return $this->_iIDC; }
	/**
	 * Retrieves the currency for the Country the message is valid in
	 *
	 * @return string
	 */
	public function getCurrency() { return $this->_sCurrency; }

	/**
	 * Retrieves a comma separated list of return codes received by GoMobile
	 * Code -1 indicates that no return code has been received and
	 * Code 200 indicates that the transaction succeeded
	 *
	 * @return string
	 */
	public function getReturnCodes() { return $this->_sReturnCodes; }
	/**
	 * Retrieves the ID returned by GoMobile for the specific message.
	 * This ID will uniquely identify the message in GoMobile
	 *
	 * @return integer
	 */
	public function getGoMobileID() { return $this->_iGoMobileID; }
	/**
	 * Sets the ID returned by GoMobile for the Message.
	 * If the ID has previously been set, the method will throw a Warning if the
	 * ID has already been set.
	 *
	 * @throws E_USER_WARNING
	 */
	public function setGoMobileID($id)
	{
		if($this->_iGoMobileID == -1) { $this->_iGoMobileID = $id; }
		else { trigger_error("Unable to set GoMobile ID to ". $id ." for message ". $this->_sClientID .". The ID has already been to: ". $this->_iGoMobileID, E_USER_WARNING); }
	}
	/**
	 * Appends a return code for the message to the comma separated list of return codes.
	 * If no return code has previously been appended (i.e. the return code is -1) then the
	 * method will simply set the return code
	 *
	 * @param integer $c 	Status Code for the Message to append
	 */
	public function appendReturnCode($c)
	{
		if($this->_sReturnCodes == -1) { $this->_sReturnCodes = $c; }
		else { $this->_sReturnCodes .= ", ". $c; }
	}

	/**
	 * Returns a Log entry for the Message which the GoMobile Client can write to the log file.
	 * The method will return a string with the following format:
	 * 	<code>
	 *
	 * 		Client ID = {CLIENT'S UNIQUE ID FOR THE TRANSACTION} ###
	 * 		Type = {MESSAGE TYPE} ###
	 * 		Country = {COUNTRY THE TRANSACTION TOOK PLACE IN FOR SMS / DELIVERY REPORT / MMS} ###
	 *
	 *	</code>
	 *
	 * @return 	string
	 */
	public function getLogEntry()
	{
		// Create Log entry
		$sEntry = "Client ID = ". $this->getClientID();
		$sEntry .= " ### ". "Type = ". $this->getType();
		if ($this->getCountry() > 0) { $sEntry .= " ### ". "Country = ". $this->getCountry(); }

		return $sEntry;
	}

	/**
	 * Sets the International Dialling Code for the Country
	 * For Incoming messages this will be set automatically by the factory method which
	 * produces the Message Information Object.
	 *
	 * @param 	integer $idc 	International Dialling Code for the Country
	 */
	public function setIDC($idc) { $this->_iIDC = $idc; }
	/**
	 * Sets the Currency used in the Country
	 * For Incoming messages this will be set automatically by the factory method which
	 * produces the Message Information Object.
	 *
	 * @param 	string $c 	Currency used in the Country
	 */
	public function setCurrency($c) { $this->_sCurrency = $c; }
}

/**
 * Client class for communicating with GoMobile.
 * The class will handle the HTTP communication towards GoMobile using the HTTPClient class for the actual HTTP communication
 * Additionally the class has methods for constructing the following message types:
 *	 1. MO-SMS
 *	 2. MT-SMS
 *	 3. MT-WAP Push
 *	 4. Binary MT-SMS
 *	 5. Delivery Report
 *	 6. MO-MMS
 *	 7. MT-MMS
 *	10. MO-Push Notification
 *	11. MT-Push Notification
 *
 * Finally it provides a method for parsing the reply from GoMobile
 *
 * All communication with GoMobile should be done using the method: communicate which will
 * perform the standard action depending on the message type.
 * If a non-standard action should be taken such as sending an MO-SMS to GoMobile
 * the send method should be called specifically.
 *
 * @see 	HTTPClient
 *
 */
class GoMobileClient
{
	/**
	 * Platform specific Carriage Return Line Feed.
	 * Please note, this variable is intended as a class constant and should be used as such
	 * eventhough PHP 4 does no support class constants.
	 */
	const CRLF = "\r\n";

	/**
	 * Info object with the Connection Info required to communicate with GoMobile
	 *
	 * @var 	GoMobileConnInfo
	 */
	private $_obj_ConnInfo;

	/**
	 * Array of GoMobile Message Info objects that each hold the relevant data for a message to send
	 * via GoMobile
	 *
	 * @var 	array
	 */
	private $_aObj_Msgs;

	/**
	 * HTTP Client object for handling the HTTP connection
	 *
	 * @var 	HTTPConnInfo
	 */
	private $_obj_HTTPClient;

	/**
	 * Default Constructor
	 *
	 * @param 	GoMobileConnInfo $oCI 	Connection Info required to communicate with GoMobile
	 */
	public function __construct(GoMobileConnInfo &$oCI)
	{
		$this->_obj_ConnInfo = $oCI;
		$this->_obj_HTTPClient = new HTTPClient(new Template(), $this->_obj_ConnInfo);
	}

	/**
	 * Retrives the HTTP Client object which handles the HTTP connection
	 *
	 * @return 	HTTPClient
	 */
	public function getHTTPClient() { return $this->_obj_HTTPClient; }
	/**
	 * Retrieves the array of GoMobile Message Info objects which holds the relevant data for each message
	 *
	 * @return 	array
	 */
	public function getMessages() { return $this->_aObj_Msgs; }
	/**
	 * Retrieves the Mode the GoMobile Client is running in
	 * 	1. Log entries are written to the log file and exceptions are re-thrown
	 *	2. Log entries and exceptions are dumped to stdout
	 *
	 * @return 	integer
	 */
	public function getMode() { return $this->_obj_ConnInfo->getMode(); }
	/**
	 * Retrieves the path to the log directory where all log files are placed
	 *
	 * @return 	string
	 */
	public function getLogPath() { return $this->_obj_ConnInfo->getLogPath(); }

	/**
	 * Adds a new log entry to today's log file: gomobile_YYYY-MM-DD.log.
	 * Each log entry in the file will be separated by a carriage return line feed (CRLF)
	 * The log entry has a client and a server section seperated by #####.
	 * Each data item in the entry are separated by ###
	 *
	 * The method will write the return string from a call to method: getLogEntry() on
	 * the Message Information Object currently held internally.
	 * Addtionally it'll add the following data to the server section.
	 * 	- State, State the HTTP connection ended in, 14 indicates that a reply was received from GoMobile
	 * 	- HTTP Code, HTTP Status code received from GoMobile.
	 * 	- Status Code(s), Comma separated list of status codes returned by GoMobile. -1 indicates that no code as returned, 200 indicates success
	 * 	- GoMobile ID, Unique ID assigned to the message by the GoMobile. -1 indicates that no ID was assigned.
	 *
	 * @see 	GoMobileMessage::getLogEntry()
	 * @see 	SMS::getLogEntry()
	 * @see 	DeliveryReport::getLogEntry()
	 * @see 	MMS::getLogEntry()
	 * @see 	PushNotification::getLogEntry()
	 *
	 * @throws 	E_USER_ERROR
	 */
	private function _log()
	{
		$sEntry = "";
		// Create log entry for each Message Information object
		foreach ($this->_aObj_Msgs as $obj_MsgInfo)
		{
			// Create Log entry
			$sEntry .= "Timestamp = ". date("Y-m-d H:i:s", time() ) ." ### ";
			$sEntry .= "Username = ". $this->_obj_ConnInfo->getUsername() ." ### ";
			$sEntry .= $obj_MsgInfo->getLogEntry();
			$sEntry .= " ##### ";
			// Add Type specific log entries
			switch ($obj_MsgInfo->getType() )
			{
			case (GoMobileMessage::iMO_SMS):				// MO-SMS
			case (GoMobileMessage::iDELIVERY_REPORT):		// Delivery Report
			case (GoMobileMessage::iMO_MMS):				// MO-MMS
			case (GoMobileMessage::iMO_PUSH_NOTIFICATION):	// MO-Push Notification
				break;
			default:	// MTs
				$sEntry .= "State = ". $this->_obj_HTTPClient->getState() ." ### ";
				$sEntry .= "HTTP Code = ". $this->_obj_HTTPClient->getReturnCode() ." ### ";
				$sEntry .= "Status Code(s) = ". $obj_MsgInfo->getReturnCodes() ." ### ";
				break;
			}
			$sEntry .= "GoMobile ID = ". $obj_MsgInfo->getGoMobileID() . self::CRLF;
		}

		// Write log entry to file
		if( ($this->getMode()&1) == 1)
		{
			$sLogFile = $this->getLogPath() ."gomobile_". date("Y-m-d", time() ) .".log";
			$fp = fopen($sLogFile, "a");
			// Success: File opened
			if(is_resource($fp) === true)
			{
				$iByte = fwrite($fp, $sEntry);
				fclose($fp);
				// Error: Writing to log file failed
				if(strlen($sEntry) != $iByte)
				{
					trigger_error("Writing to log file: ". $sLogFile ." failed. Number of bytes to write: ". strlen($sEntry) ." Number of bytes written: ". $iByte, E_USER_ERROR);
				}
			}
			// Error: Unable to open log file for writing
			else
			{
				trigger_error("Unable to open file: ". $sLogFile ." for writing log", E_USER_ERROR);
			}
		}

		// Output log entry to stdout
		if( ($this->getMode()&2) == 2)
		{
			echo $sEntry;
		}
	}

	/**
	 * Sends the provided message(s) to GoMobile.
	 * The method will create a new HTTP Client object and open a new socket to GoMobile.
	 * Through this socket the message(s) will be sent as a HTTP Request.
	 *
	 * @param 	mixed $mi 	GoMobile Message Info object which holds the relevant data for the message or array of GoMobile Message Info objects
	 * @return 	integer		HTTP Status code, -1 on error
	 *
	 * @throws 	E_USER_ERROR
	 */
	public function send(&$mi)
	{
		/* ----- Input Validation Start ----- */
		if ($mi instanceof GoMobileMessage === false && is_array($mi) === false)
		{
			trigger_error("Argument 1 passed to GoMobileClient::send() must be an instance of GoMobileMessage or an array of GoMobileMessage objects", E_USER_ERROR);
		}
		elseif ($mi instanceof GoMobileMessage === true)
		{
			$aObj_Msgs = array(&$mi);
		}
		else { $aObj_Msgs = &$mi; }
		/* ----- Input Validation End ----- */

		/* ----- Construct HTTP Header Start ----- */
		$h = $this->_constHeaders();
		/* ----- Construct HTTP Header End ----- */
		$code = -1;
		// Clear Internal array of Message Objects
		$this->_aObj_Msgs = array();

		/* ----- Construct XML Body Start ----- */
		$b = '<?xml version="1.0" encoding="ISO-8859-1"?>';
		$b .= '<root>';
		// Append XML Body for each message in accordance to its type
		foreach ($aObj_Msgs as $id => &$obj_Msg)
		{
			// Add message reference to internal array of messages
			$this->_aObj_Msgs[$obj_Msg->getClientID()] = &$obj_Msg;

			// Construct HTTP Body for the message
			switch ($obj_Msg->getType() )
			{
			case (GoMobileMessage::iMO_SMS):				// MO-SMS
				$b .= $this->_constMOSMS($obj_Msg);
				break;
			case (GoMobileMessage::iMT_SMS):				// MT-SMS
				$b .= $this->_constMTSMS($obj_Msg);
				break;
			case (GoMobileMessage::iMT_WAP_PUSH):			// MT-WAP Push
				$b .= $this->_constMTWAPPush($obj_Msg);
				break;
			case (GoMobileMessage::iMT_BINARY_SMS):			// MT-Binary SMS
				$b .= $this->_constBinMTSMS($obj_Msg);
				break;
			case (GoMobileMessage::iDELIVERY_REPORT):		// Delivery Report
				$b .= $this->_constDeliveryReport($obj_Msg);
				break;
			case (GoMobileMessage::iMO_MMS):				// MO-MMS
				$b .= $this->_constMOMMS($obj_Msg);
				break;
			case (GoMobileMessage::iMT_MMS):				// MT-MMS
				$b .= $this->_constMTMMS($obj_Msg);
				break;
/*
			case (GoMobileMessage::iINCOMING_EMAIL):		// Incoming E-Mail
				$b .= $this->_constIncomingEMail($obj_Msg);
				break;
			case (GoMobileMessage::iOUTGOING_EMAIL):		// Outgoing E-Mail
				$b .= $this->_constOutgoingEMail($obj_Msg);
				break;
*/
			case (GoMobileMessage::iMO_PUSH_NOTIFICATION):	// MO-Push Notification
				$b .= $this->_constMOPushNotification($obj_Msg);
				break;
			case (GoMobileMessage::iMT_PUSH_NOTIFICATION):	// MT-Push Notification
				$b .= $this->_constMTPushNotification($obj_Msg);
				break;
			default:	// Error: Unsupported message type
				trigger_error("Message type: ". $obj_Msg->getType() ." not supported", E_USER_ERROR);
				break;
			}
		}
		$b .= '</root>';
		/* ----- Construct XML Body End ----- */

		$this->_obj_HTTPClient = new HTTPClient(new Template(), $this->_obj_ConnInfo);
		// Send the message to GoMobile
		$this->_obj_HTTPClient->connect();
		$code = $this->_obj_HTTPClient->send($h, $b);
		$this->_obj_HTTPClient->disConnect();
		if($code > -1) { $this->_parseReply($this->_obj_HTTPClient->getReplyBody() ); }

		// Log Request
		$this->_log();

		return $code;
	}

	/**
	 * Receives a Message from GoMobile and provides a valid response.
	 * The internal data structure is updated with the received message and a new entry is added the log file.
	 * The method will automatically be echo the following XML Document to stdout:
	 * 	<?xml version="1.0" encoding="ISO-8859-1"?>
	 * 	<root>{CLIENT ID}</root>
	 *
	 * @param GoMobileMessage $oMI 	GoMobile Message Info object which holds the relevant data for the message
	 */
	public function receive(GoMobileMessage &$oMI)
	{
		// Add message reference to internal array of messages
		$this->_aObj_Msgs[] = &$oMI;
		$this->_log();
		$xml = '<?xml version="1.0" encoding="ISO-8859-1"?>';
		$xml .= '<root>'. $oMI->getClientID() .'</root>';

		ignore_user_abort(true);

		header("Content-Type: text/xml");
		header("Content-Length: ". strlen($xml) );
		header("Connection: close");

		echo $xml;
		flush();
	}

	/**
	 * Handles the communication with GoMobile depending the the message type.
	 * For the following message types the method will handle the receival appropriately:
	 *	 1. MO-SMS
	 *	 5. Delivery Report
	 *	 6. MO-MMS
	 * 	10. MO-Push Notification
	 * For the following message types the method will connect to GoMobile and send the message:
	 *	 2. MT-SMS
	 *	 3. MT-WAP Push
	 *	 4. MT-Binary SMS
	 *	 7. MT-MMS
	 * 	11. MT-Push Notification
	 *
	 * @see 	GoMobileClient::send()
	 * @see 	GoMobileClient::receive()
	 *
	 * @param 	GoMobileMessage $oMI 	GoMobile Message Info object which holds the relevant data for the message
	 *
	 * @throws 	E_USER_ERROR
	 */
	public function communicate(GoMobileMessage &$oMI)
	{
		switch ($oMI->getType() )
		{
		case (GoMobileMessage::iMO_SMS):				// MO-SMS
		case (GoMobileMessage::iDELIVERY_REPORT):		// Delivery Report
		case (GoMobileMessage::iMO_MMS):				// MO-MMS
		case (GoMobileMessage::iMO_PUSH_NOTIFICATION):	// MO-Push Notification
			return $this->receive($oMI);
			break;
		case (GoMobileMessage::iMT_SMS):				// MT-SMS
		case (GoMobileMessage::iMT_WAP_PUSH):			// MT-WAP Push
		case (GoMobileMessage::iMT_BINARY_SMS):			// MT-Binary SMS
		case (GoMobileMessage::iMT_SMS):				// MT-MMS
		case (GoMobileMessage::iMT_PUSH_NOTIFICATION):	// MT-Push Notification
			return $this->send($oMI);
			break;
		default:	// Error: Unsupported message type
			trigger_error("Message type: ". $oMI->getType() ." not supported", E_USER_ERROR);
			return null;
			break;
		}
	}

	/**
	 * Construct standard GoMobile HTTP Headers for the HTTP Request
	 *
	 * @return string
	 */
	private function _constHeaders()
	{
		/* ----- Construct HTTP Header Start ----- */
		// Construct authentication parameters for GoMobile
		$rnd = md5(uniqid("", true) );
		list ($ms, $s) = explode(" ", microtime() );
		$seq = $s . substr($ms, strpos($ms, ".")+1);

		$chk = sha1($this->_obj_ConnInfo->getUsername() . $this->_obj_ConnInfo->getPassword() . $seq . $rnd);

		$h = "{METHOD} {PATH}?username=". $this->_obj_ConnInfo->getUsername() ."&checksum=". $chk ."&sequence=". $seq ."&random=". $rnd ." HTTP/1.0" .self::CRLF;
		$h .= "host: {HOST}" .self::CRLF;
		$h .= "referer: {REFERER}" .self::CRLF;
		$h .= "content-length: {CONTENTLENGTH}" .self::CRLF;
		$h .= "content-type: {CONTENTTYPE}; charset=iso-8859-1" .self::CRLF;
		/* ----- Construct HTTP Header End ----- */

		return $h;
	}

	/**
	 * Constructs an MO-SMS Request to GoMobile.
	 * This method is useful for generating a test MO-SMS through GoMobile during development.
	 * GoMobile will forward the to the Client just as if it had received it from a Mobile Operator.
	 *
	 * The method will return an XML Document in the following format:
	 * <code>
	 *
	 * 	<message id="{CLIENT ID FOR THE MESSAGE}" type="1">
	 * 		<country>{ID OF THE COUNTRY THE MESSAGE WAS RECEIVED IN}</country>
	 *		<channel>{CHANNEL THE MESSAGE SHOULD WAS RECEIVED THROUGH}</channel>
	 * 		<operator>{ID OF THE OPERATOR THE MESSAGE WAS RECEIVED FROM}</country>
	 * 		<keyword>{KEYWORD THE MESSAGE BELONGS TO}</keyword>
	 * 		<sender>{MSISDN OF THE SENDER}</sender>
	 * 		<body>{XML ENCODED BODY FOR THE MESSAGE}</body>
	 * 	</message>
	 *
	 * </code>
	 *
	 * @param 	SMS $oMI 	SMS Info object which holds the relevant data for the message
	 * @return 	string
	 */
	private function _constMOSMS(SMS $oMI)
	{
		/* ----- Construct XML Body Start ----- */
		$b = '<message id="'. $oMI->getClientID() .'" type="'. GoMobileMessage::iMO_SMS .'">';
		$b .= '<country>'. $oMI->getCountry() .'</country>';
		$b .= '<channel>'. $oMI->getChannel() .'</channel>';
		$b .= '<operator>'. $oMI->getOperator() .'</operator>';
		$b .= '<keyword>'. $oMI->getKeyword() .'</keyword>';
		$b .= '<sender>'. $oMI->getSender() .'</sender>';
		$b .= '<body>'. htmlspecialchars($oMI->getBody(), ENT_NOQUOTES) .'</body>';
		$b .= '</message>';
		/* ----- Construct XML Body End ----- */

		return $b;
	}

	/**
	 * Constructs an MT-SMS Request to GoMobile.
	 *
	 * The method will return an XML Document in the following format:
	 * <code>
	 *
	 * 	<message id="{CLIENT ID FOR THE MESSAGE}" type="2">
	 * 		<transaction>{CORRESPONDING ID FOR THE RECEIVED MO-SMS TO WHICH THIS MESSAGE IS A RESPONSE}</transaction>
	 * 		<country>{ID OF THE COUNTRY THE MESSAGE SHOULD BE SENT IN}</country>
	 *		<channel>{CHANNEL THE MESSAGE SHOULD BE SENT THROUGH}</channel>
	 * 		<operator>{ID OF THE OPERATOR THE MESSAGE SHOULD BE ROUTED TO}</country>
	 * 		<keyword>{KEYWORD THE MESSAGE BELONGS TO}</keyword>
	 * 		<recipient>{MSISDN OF THE RECIPIENT THE MESSAGE IS SENT TO}</recipient>
	 * 		<price>{PRICE THE MESSAGE IS CHARGED AT IN COUNTRY'S SMALLEST CURRENCY}</price>
	 * 		<body concat="{FLAG INDICATING WHETHER MESSAGE CONCATENATION SHOULD BE USED}" type="{MESSAGE CONTENT TYPE}">{XML ENCODED BODY FOR THE MESSAGE}</body>
	 * 		<sender>{ALPHANUMERIC ORIGINATOR FOR THE MESSAGE}</sender>
	 * 		<description>{DESCRIPTION OF THE MESSAGE CONTENT USED BY THE OPERATORS TO enhance their customer service and billing statements}</description>
	 * 	</message>
	 *
	 * </code>
	 * Please note, the sender is optional and the transaction tag is should be included for ALL messages
	 * which doesn't belong to a Bulk Keyword.
	 *
	 * @param 	SMS $oMI 	SMS Info object which holds the relevant data for the message
	 * @return 	string
	 */
	private function _constMTSMS(SMS $oMI)
	{
		/* ----- Construct XML Body Start ----- */
		$b = '<message id="'. $oMI->getClientID() .'" type="'. GoMobileMessage::iMT_SMS .'">';
		/*
		 * Include corresponding ID for the received MO-SMS in the response
		 * This is required for ALL Keywords with the exception of a Bulk Keyword
		 */
		if ($oMI->getMOID() > 0) { $b .= '<transaction>'. $oMI->getMOID() .'</transaction>'; }
		$b .= '<country>'. $oMI->getCountry() .'</country>';
		$b .= '<channel>'. $oMI->getChannel() .'</channel>';
		$b .= '<operator>'. $oMI->getOperator() .'</operator>';
		$b .= '<keyword>'. $oMI->getKeyword() .'</keyword>';
		$b .= '<price>'. $oMI->getPrice() .'</price>';
		$b .= '<recipient>'. $oMI->getRecipient() .'</recipient>';
		/*
		 * The body of an SMS can be split into several MTs and concatenated by the recipient's
		 * mobile device if the body is over 160 character in length. GoMobile will split the body
		 * into several message with a length of 154 characters each.
		 * The type attribute will define the content type for the message sent to the recipient.
		 * This parameter is required by some Operators such as AT&T Mobility and Verizon Wireless in the US.
		 * Please refer to GoMobile - Overview.pdf for details.
		 */
		$b .= '<body concat="'. ($oMI->useConcatenation()===true?"true":"false") .'" type="'. $oMI->getContentType() .'">'. htmlspecialchars($oMI->getBody(), ENT_NOQUOTES) .'</body>';
		/*
		 * Send SMS with an Alphanumeric originator instead of the short code
		 * This will essentially replace the sender in the phone and make the user unable to use the reply
		 * option to send back a response.
		 */
		if ($oMI->getSender() !== false) { $b .= '<sender>'. htmlspecialchars($oMI->getSender(), ENT_NOQUOTES) .'</sender>'; }
		/*
		 * Add description of the MT to enhance Operator Customer Support.
		 * This is currently a requirement by AT&T Mobility in the US but will likely be required by other operators as well.
		 */
		$b .= '<description>'. htmlspecialchars($oMI->getDescription(), ENT_NOQUOTES) .'</description>';
		$b .= '</message>';
		/* ----- Construct XML Body End ----- */

		return $b;
	}

	/**
	 * Constructs an MT-WAP Push Request to GoMobile.
	 *
	 * The method will return an XML Document in the following format:
	 * <code>
	 *
	 * 	<message id="{CLIENT ID FOR THE MESSAGE}" type="3">
	 * 		<transaction>{CORRESPONDING ID FOR THE RECEIVED MO-SMS TO WHICH THIS MESSAGE IS A RESPONSE}</transaction>
	 * 		<country>{ID OF THE COUNTRY THE MESSAGE SHOULD BE SENT IN}</country>
	 *		<channel>{CHANNEL THE MESSAGE SHOULD BE SENT THROUGH}</channel>
	 * 		<operator>{ID OF THE OPERATOR THE MESSAGE SHOULD BE ROUTED TO}</country>
	 * 		<keyword>{KEYWORD THE MESSAGE BELONGS TO}</keyword>
	 * 		<recipient>{MSISDN OF THE RECIPIENT THE MESSAGE IS SENT TO}</recipient>
	 * 		<price>{PRICE THE MESSAGE IS CHARGED AT IN COUNTRY'S SMALLEST CURRENCY}</price>
	 * 		<body concat="false" type="{MESSAGE CONTENT TYPE}">{XML ENCODED INDICATION FOR THE WAP PUSH}</body>
	 * 		<sender>{ALPHANUMERIC ORIGINATOR FOR THE MESSAGE}</sender>
	 * 		<url>{ABSOLUTE URL WHICH SHOULD BE LOADED WHEN THE WAP PUSH IS ACTIVATED}</url>
	 * 		<description>{DESCRIPTION OF THE MESSAGE CONTENT USED BY THE OPERATORS TO enhance their customer service and billing statements}</description>
	 * 	</message>
	 *
	 * </code>
	 * Please note, the sender is optional and the transaction tag is should be included for ALL messages
	 * which doesn't belong to a Bulk Keyword.
	 *
	 * @param 	SMS $oMI 	SMS Info object which holds the relevant data for the message
	 * @return 	string
	 */
	private function _constMTWAPPush(SMS $oMI)
	{
		/* ----- Construct XML Body Start ----- */
		$b = '<message id="'. $oMI->getClientID() .'" type="'. GoMobileMessage::iMT_WAP_PUSH .'">';
		/*
		 * Include corresponding ID for the received MO-SMS in the response
		 * This is required for ALL Keywords with the exception of a Bulk Keyword
		 */
		if ($oMI->getMOID() > 0) { $b .= '<transaction>'. $oMI->getMOID() .'</transaction>'; }
		$b .= '<country>'. $oMI->getCountry() .'</country>';
		$b .= '<channel>'. $oMI->getChannel() .'</channel>';
		$b .= '<operator>'. $oMI->getOperator() .'</operator>';
		$b .= '<keyword>'. $oMI->getKeyword() .'</keyword>';
		$b .= '<price>'. $oMI->getPrice() .'</price>';
		$b .= '<recipient>'. $oMI->getRecipient() .'</recipient>';
		/*
		 * Concatenation of the Message body is not applicable for MT-WAP Push messages.
		 * The type attribute will define the content type for the message sent to the recipient.
		 * This parameter is required by some Operators such as AT&T Mobility and Verizon Wireless in the US.
		 * Please refer to GoMobile - Overview.pdf for details.
		 */
		$b .= '<body concat="false" type="'. $oMI->getContentType() .'">'. htmlspecialchars($oMI->getBody(), ENT_NOQUOTES) .'</body>';
		/*
		 * Send SMS with an Alphanumeric originator instead of the short code
		 * This will essentially replace the sender in the phone and make the user unable to use the reply
		 * option to send back a response.
		 */
		if ($oMI->getSender() !== false) { $b .= '<sender>'. htmlspecialchars($oMI->getSender(), ENT_NOQUOTES) .'</sender>'; }
		$b .= '<url>'. htmlspecialchars($oMI->getURL(), ENT_NOQUOTES) .'</url>';
		/*
		 * Add description of the MT to enhance Operator Customer Support.
		 * This is currently a requirement by AT&T Mobility in the US but will likely be required by other operators as well.
		 */
		$b .= '<description>'. htmlspecialchars($oMI->getDescription(), ENT_NOQUOTES) .'</description>';
		$b .= '</message>';
		/* ----- Construct XML Body End ----- */

		return $b;
	}

	/**
	 * Constructs a Binary MT-SMS Request to GoMobile.
	 * The request is constructed using a hex encoded UDH and a well-known XML Document as body.
	 * Currently the following well-known documents are supported by GoMobile:
	 * 	- Service Indication
	 * 	- Service Load
	 *
	 * The method will return an XML Document in the following format:
	 * <code>
	 *
	 * 	<message id="{CLIENT ID FOR THE MESSAGE}" type="4">
	 * 		<transaction>{CORRESPONDING ID FOR THE RECEIVED MO-SMS TO WHICH THIS MESSAGE IS A RESPONSE}</transaction>
	 * 		<country>{ID OF THE COUNTRY THE MESSAGE SHOULD BE SENT IN}</country>
	 *		<channel>{CHANNEL THE MESSAGE SHOULD BE SENT THROUGH}</channel>
	 * 		<operator>{ID OF THE OPERATOR THE MESSAGE SHOULD BE ROUTED TO}</country>
	 * 		<keyword>{KEYWORD THE MESSAGE BELONGS TO}</keyword>
	 * 		<recipient>{MSISDN OF THE RECIPIENT THE MESSAGE IS SENT TO}</recipient>
	 * 		<price>{PRICE THE MESSAGE IS CHARGED AT IN COUNTRY'S SMALLEST CURRENCY}</price>
	 * 		<body concat="{FLAG INDICATING WHETHER MESSAGE CONCATENATION SHOULD BE USED}" type="{MESSAGE CONTENT TYPE}">{WELL-KNOWN XML DOCUMENT ENCODED ACCORDING THE XML STANDARD}</body>
	 * 		<sender>{ALPHANUMERIC ORIGINATOR FOR THE MESSAGE}</sender>
	 * 		<udh>{HEX ENCODED USER DATA HEADER FOR THE WELL-KNOWN XML DOCUMENT}</udh>
	 * 		<description>{DESCRIPTION OF THE MESSAGE CONTENT USED BY THE OPERATORS TO enhance their customer service and billing statements}</description>
	 * 	</message>
	 *
	 * </code>
	 * Please note, the sender is optional and the transaction tag is should be included for ALL messages
	 * which doesn't belong to a Bulk Keyword.
	 *
	 * @param 	SMS $oMI 	SMS Info object which holds the relevant data for the message
	 * @return 	string
	 */
	private function _constBinMTSMS(SMS $oMI)
	{
		/* ----- Construct XML Body Start ----- */
		$b = '<message id="'. $oMI->getClientID() .'" type="'. GoMobileMessage::iMT_BINARY_SMS .'">';
		/*
		 * Include corresponding ID for the received MO-SMS in the response
		 * This is required for ALL Keywords with the exception of a Bulk Keyword
		 */
		if ($oMI->getMOID() > 0) { $b .= '<transaction>'. $oMI->getMOID() .'</transaction>'; }
		$b .= '<country>'. $oMI->getCountry() .'</country>';
		$b .= '<channel>'. $oMI->getChannel() .'</channel>';
		$b .= '<operator>'. $oMI->getOperator() .'</operator>';
		$b .= '<keyword>'. $oMI->getKeyword() .'</keyword>';
		$b .= '<price>'. $oMI->getPrice() .'</price>';
		$b .= '<recipient>'. $oMI->getRecipient() .'</recipient>';
		/*
		 * The body of an SMS can be split into several MTs and concatenated by the recipient's
		 * mobile device if the body is over 160 character in length. GoMobile will split the body
		 * into several message with a length of 154 characters each. This is useful to transmit
		 * old monofonic ringtones or logos to a mobile device.
		 * The type attribute will define the content type for the message sent to the recipient.
		 * This parameter is required by some Operators such as AT&T Mobility and Verizon Wireless in the US.
		 * Please refer to GoMobile - Overview.pdf for details.
		 */
		$b .= '<body concat="'. ($oMI->useConcatenation()===true?"true":"false") .'" type="'. $oMI->getContentType() .'">'. htmlspecialchars($oMI->getBody(), ENT_NOQUOTES) .'</body>';
		/*
		 * Send SMS with an Alphanumeric originator instead of the short code
		 * This will essentially replace the sender in the phone and make the user unable to use the reply
		 * option to send back a response.
		 */
		if ($oMI->getSender() !== false) { $b .= '<sender>'. htmlspecialchars($oMI->getSender(), ENT_NOQUOTES) .'</sender>'; }
		$b .= '<udh>'. $oMI->getUDH() .'</udh>';
		/*
		 * Add description of the MT to enhance Operator Customer Support.
		 * This is currently a requirement by AT&T Mobility in the US but will likely be required by other operators as well.
		 */
		$b .= '<description>'. htmlspecialchars($oMI->getDescription(), ENT_NOQUOTES) .'</description>';
		$b .= '</message>';
		/* ----- Construct XML Body End ----- */

		return $b;
	}
	/**
	 * Constructs a Delivery Report request to GoMobile.
	 * This method is useful for generating a test Delivery Report through GoMobile during development.
	 * GoMobile will forward the to the Client just as if it had received it from a Mobile Operator.
	 *
	 * The method will return an XML Document in the following format:
	 * <code>
	 *
	 * 	<message id="{CLIENT ID FOR THE DELIVERY REPORT}" type="5">
	 * 		<transaction>{ID OF THE TRANSACTION THE DELIVERY REPORT IS VALID FOR}</transaction>
	 * 		<status code="{CODE INDICATING THE STATUS FOR THE DELIVERY REPORT}" resend-interval="{RESEND INTERVAL WHICH SHOULD BE USED FOR THE TRANSACTION}">
	 * 			{TEXT DESCRIBING THE STATUS CODE}
	 * 		</status>
	 * 		<country idc="{INTERNATIONAL DIALLING CODE" currency="{COUNTRY'S CURRENCY}">{ID OF THE COUNTRY THE MESSAGE IS SENT IN}</country>
	 * 		<recipient>{MSISDN OF THE END-USER WHO RECEIVED THE MESSAGE}</recipient>
	 * 	</message>
	 *
	 * </code>
	 *
	 * @param 	DeliveryReport $oMI 	Delivery Report Info object which holds the relevant data for the message
	 * @return 	string
	 *
	 * @throws 	E_USER_ERROR
	 */
	private function _constDeliveryReport(DeliveryReport $oMI)
	{
		/* ----- Construct XML Body Start ----- */
		$b = '<message id="'. $oMI->getClientID() .'" type="'. GoMobileMessage::iDELIVERY_REPORT .'">';
		$b .= '<transaction>'. $oMI->getMTID() .'</transaction>';
		$b .= '<country>'. $oMI->getCountry() .'</country>';
		$b .= '<recipient>'. $oMI->getRecipient() .'</recipient>';
		$b .= '<status code="'. $oMI->getState() .'" resend-interval="'. $oMI->getResendInterval() .'">'. htmlspecialchars($oMI->getDescription(), ENT_NOQUOTES) .'</status>';
		$b .= '</message>';
		/* ----- Construct XML Body End ----- */

		return $b;
	}
	/**
	 * Constructs an MO-MMS Request to GoMobile.
	 * This method is useful for generating a test MO-MMS through GoMobile during development.
	 * GoMobile will forward the to the Client just as if it had received it from a Mobile Operator.
	 *
	 * The method will return an XML Document in the following format:
	 * <code>
	 *
	 * 	<message id="{CLIENT ID FOR THE MESSAGE}" type="6">
	 *		<country>{ID OF THE COUNTRY THE MESSAGE WAS RECEIVED IN}</country>
	 *		<channel>{CHANNEL THE MESSAGE SHOULD WAS RECEIVED THROUGH}</channel>
	 *		<operator>{ID OF THE OPERATOR THE MESSAGE WAS RECEIVED FROM}</country>
	 *		<keyword>{KEYWORD THE MESSAGE BELONGS TO}</keyword>
	 *		<sender>{MSISDN OF THE SENDER}</sender>
	 *		<subject>{XML ENCODED SUBJECT FOR THE MESSAGE}</subject>
	 *		<body>{XML ENCODED SMIL DOCUMENT FOR THE MESSAGE}</body>
	 *		<files>
	 *			<file name="{UNIQUE FILE ID REFERENCED IN SMIL}" type="{MIME TYPE FOR FILE}">{BASE64 ENCODED BINARY DATA}</file>
	 *			<file name="{UNIQUE FILE ID REFERENCED IN SMIL}" type="{MIME TYPE FOR FILE}">{BASE64 ENCODED BINARY DATA}</file>
	 *			...
	 *		</files>
	 *	</message>
	 *
	 * </code>
	 *
	 * @param 	MMS $oMI 	MMS Info object which holds the relevant data for the message
	 * @return 	string
	 */
	private function _constMOMMS(MMS $oMI)
	{
		/* ----- Construct XML Body Start ----- */
		$b = '<message id="'. $oMI->getClientID() .'" type="'. GoMobileMessage::iMO_MMS .'">';
		$b .= '<country>'. $oMI->getCountry() .'</country>';
		$b .= '<channel>'. $oMI->getChannel() .'</channel>';
		$b .= '<operator>'. $oMI->getOperator() .'</operator>';
		$b .= '<keyword>'. $oMI->getKeyword() .'</keyword>';
		$b .= '<sender>'. $oMI->getSender() .'</sender>';
		$b .= '<subject>'. htmlspecialchars($oMI->getSubject(), ENT_NOQUOTES) .'</subject>';
		$b .= '<body>'. htmlspecialchars($oMI->getBody(), ENT_NOQUOTES) .'</body>';
		$b .= '<files>';
		// Add Content Files for the MMS Message
		$aObj_Files = $oMI->getFiles();
		foreach ($aObj_Files as $id => $obj_File)
		{
			$b .= '<file name="'. $id .'" type="'. $obj_File->getType() .'">'. (stristr($obj_File->getType(), "text")==true?$obj_File->getData():base64_encode($obj_File->getData() ) ) .'</file>';
		}
		$b .= '</files>';
		$b .= '</message>';
		/* ----- Construct XML Body End ----- */

		return $b;
	}

	/**
	 * Constructs an MO-MMS Request to GoMobile.
	 * This method is useful for generating a test MO-MMS through GoMobile during development.
	 * GoMobile will forward the to the Client just as if it had received it from a Mobile Operator.
	 *
	 * The method will return an XML Document in the following format:
	 * <code>
	 *
	 * 	<message id="{CLIENT ID FOR THE MESSAGE}" type="7">
	 * 		<transaction>{CORRESPONDING ID FOR THE RECEIVED MO-SMS TO WHICH THIS MESSAGE IS A RESPONSE}</transaction>
	 *		<country>{ID OF THE COUNTRY THE MESSAGE WAS RECEIVED IN}</country>
	 *		<channel>{CHANNEL THE MESSAGE SHOULD WAS RECEIVED THROUGH}</channel>
	 *		<operator>{ID OF THE OPERATOR THE MESSAGE WAS RECEIVED FROM}</country>
	 *		<keyword>{KEYWORD THE MESSAGE BELONGS TO}</keyword>
	 *		<recipients>
	 *			<to>{MSISDN OF THE RECIPIENT THE MESSAGE IS SENT TO}</to>
	 *			<to>{MSISDN OF THE RECIPIENT THE MESSAGE IS SENT TO}</to>
	 *			...
	 *		</recipients>
	 * 		<price>{PRICE THE MESSAGE IS CHARGED AT IN COUNTRY'S SMALLEST CURRENCY}</price>
	 *		<subject>{XML ENCODED SUBJECT FOR THE MESSAGE}</subject>
	 *		<body type="{MESSAGE CONTENT TYPE}">{XML ENCODED SMIL DOCUMENT FOR THE MESSAGE}</body>
	 *		<files>
	 *			<file name="{UNIQUE FILE ID REFERENCED IN SMIL}" type="{MIME TYPE FOR FILE}">{BASE64 ENCODED BINARY DATA}</file>
	 *			<file name="{UNIQUE FILE ID REFERENCED IN SMIL}" type="{MIME TYPE FOR FILE}">{BASE64 ENCODED BINARY DATA}</file>
	 *			...
	 *		</files>
	 * 		<drm type="{DRM TYPE}">
	 *			<count>{NUMBER OF TIMES CONTENT CAN BE PLAYED / VIEWED}</count>
	 *			<start>{START TIMESTAMP DEFINING WHEN CONTENT CAN BE PLAYED / VIEWED FROM: YYYY-MM-DD hh:mm:ss}</start>
	 *			<end>{END TIMESTAMP DEFINING WHEN CONTENT CAN BE PLAYED / VIEWED TO: YYYY-MM-DD hh:mm:ss}</end>
	 *			<interval>
	 *				<years>{NUMBER OF YEARS CONTENT CAN BE PLAYED / VIEWED FROM BEING RECEIVED}</years>
	 *				<months>{NUMBER OF MONTHS CONTENT CAN BE PLAYED / VIEWED FROM BEING RECEIVED}</months>
	 *				<days>{NUMBER OF DAYS CONTENT CAN BE PLAYED / VIEWED FROM BEING RECEIVED}</days>
	 *				<hours>{NUMBER OF HOURS CONTENT CAN BE PLAYED / VIEWED FROM BEING RECEIVED}</hours>
	 *				<minutes>{NUMBER OF MINUTES CONTENT CAN BE PLAYED / VIEWED FROM BEING RECEIVED}</minutes>
	 *			</interval>
	 * 		</drm>
	 * 		<description>{DESCRIPTION OF THE MESSAGE CONTENT USED BY THE OPERATORS TO enhance their customer service and billing statements}</description>
	 *	</message>
	 *
	 * </code>
	 *
	 * Digital Rights Management Types:
	 *		0. Off
	 *		1. Forward Lock (drm tag empty)
	 *		2. Combined Delivery
	 *		3. Separate Delivery
	 * 	For the "drm" tag ONLY one of the following combinations should be specified and ONLY if Combined or Separate Delivery is used.
	 * 		- Count
	 *		- Start Timestamp / End Timestamp
	 *		- Interval
	 * 	If Digital Rights Managements is not used or Forward Locking is used these tags are simply ignored.
	 *
	 * @param 	MMS $oMI 	MMS Info object which holds the relevant data for the message
	 * @return 	string
	 */
	private function _constMTMMS(MMS $oMI)
	{
		/* ----- Construct XML Body Start ----- */
		$b = '<message id="'. $oMI->getClientID() .'" type="'. GoMobileMessage::iMT_MMS .'">';
		/*
		 * Include corresponding ID for the received MO in the response
		 * This is required for ALL Keywords with the exception of a Bulk Keyword
		 */
		if ($oMI->getMOID() > 0) { $b .= '<transaction>'. $oMI->getMOID() .'</transaction>'; }
		$b .= '<country>'. $oMI->getCountry() .'</country>';
		$b .= '<channel>'. $oMI->getChannel() .'</channel>';
		$b .= '<operator>'. $oMI->getOperator() .'</operator>';
		$b .= '<keyword>'. $oMI->getKeyword() .'</keyword>';
		// Add Recipients for the MMS Message
		$b .= '<recipients>';
		$aRecipients = $oMI->getRecipients();
		for ($i=0; $i<count($aRecipients); $i++)
		{
 			$b .= '<to>'. $aRecipients[$i] .'</to>';
		}
 		$b .= '</recipients>';
 		$b .= '<price>'. $oMI->getPrice() .'</price>';
		$b .= '<subject>'. htmlspecialchars($oMI->getSubject(), ENT_NOQUOTES) .'</subject>';
		/*
		 * The type attribute will define the content type for the message sent to the recipient.
		 * This parameter is required by some Operators such as AT&T Mobility and Verizon Wireless in the US.
		 * Please refer to GoMobile - Overview.pdf for details.
		 */
		$b .= '<body type="'. $oMI->getContentType() .'">'. htmlspecialchars($oMI->getBody(), ENT_NOQUOTES) .'</body>';
		// Add Content Files for the MMS Message
		$b .= '<files>';
		$aObj_Files = $oMI->getFiles();
		foreach ($aObj_Files as $id => $obj_File)
		{
			$b .= '<file name="'. $id .'" type="'. $obj_File->getType() .'">'. (stristr($obj_File->getType(), "text")==true?$obj_File->getData():base64_encode($obj_File->getData() ) ) .'</file>';
		}
		$b .= '</files>';
		/*
		 * Add Digital Rights Management for the MMS Message.
		 * Please refer to GoMobile - Overview.pdf and OMA-Download-DRMREL-V1_0-20040615-A.pdf for details
		 */
		$b .= '<drm type="'. $oMI->getDRM()->getType() .'">';
		if ($oMI->getDRM()->getType() == DRM::iCOMBINED_DELIVERY || $oMI->getDRM()->getType() == DRM::iSEPARATE_DELIVERY)
		{
			// Number of times the content files in the MMS Message can be played / viewd
			if ($oMI->getDRM()->getCount() > 0) { $b .= '<count>'. $oMI->getDRM()->getCount() .'</count>'; }
			// Start Timestamp defining when the content files in the MMS Message can be played / viewed from
			if (strlen($oMI->getDRM()->getStart() ) > 0) { $b .= '<start>'. $oMI->getDRM()->getStart() .'</start>'; }
			// End Timestamp defining when the content files in the MMS Message can be played / viewed to
			if (strlen($oMI->getDRM()->getEnd() ) > 0) { $b .= '<end>'. $oMI->getDRM()->getEnd() .'</end>'; }
			// Duration the content files in the MMS Message can be viewed in from the MMS is originally received
			if (is_null($oMI->getDRM()->getInterval() ) === false)
			{
				$b .= '<interval>';
				$b .= '<years>'. $oMI->getDRM()->getInterval()->getYears() .'</years>';
				$b .= '<months>'. $oMI->getDRM()->getInterval()->getMonths() .'</months>';
				$b .= '<days>'. $oMI->getDRM()->getInterval()->getDays() .'</days>';
				$b .= '<hours>'. $oMI->getDRM()->getInterval()->getHours() .'</hours>';
				$b .= '<minutes>'. $oMI->getDRM()->getInterval()->getMinutes() .'</minutes>';
				$b .= '</interval>';
			}
		}
		$b .= '</drm>';
		/*
		 * Add description of the MT to enhance Operator Customer Support.
		 * This is currently a requirement by AT&T Mobility in the US but will likely be required by other operators as well.
		 */
		$b .= '<description>'. htmlspecialchars($oMI->getDescription(), ENT_NOQUOTES) .'</description>';
		$b .= '</message>';
		/* ----- Construct XML Body End ----- */

		return $b;
	}
	/**
	 * Constructs an MO-Push Notification Request to GoMobile.
	 * This method is useful for generating a test MO-Push Notification through GoMobile during development.
	 * GoMobile will forward the to the Client just as if it had received it from a Mobile Operator.
	 *
	 * The method will return an XML Document in the following format:
	 * <code>
	 *
	 * 	<message id="{CLIENT ID FOR THE MESSAGE}" type="10">
	 *		<channel>{CHANNEL THE MESSAGE SHOULD WAS RECEIVED THROUGH}</channel>
	 * 		<keyword>{KEYWORD THE MESSAGE BELONGS TO}</keyword>
	 * 		<sender>{PUSH ID OF THE SENDER}</sender>
	 * 		<body>{XML ENCODED BODY FOR THE MESSAGE}</body>
	 * 	</message>
	 *
	 * </code>
	 *
	 * @param 	PushNotification $oMI 	Push Notification Info object which holds the relevant data for the message
	 * @return 	string
	 */
	private function _constMOPushNotification(PushNotification $oMI)
	{
		/* ----- Construct XML Body Start ----- */
		$b = '<message id="'. $oMI->getClientID() .'" type="'. GoMobileMessage::iMO_PUSH_NOTIFICATION .'">';
		$b .= '<channel>'. $oMI->getChannel() .'</channel>';
		$b .= '<keyword>'. $oMI->getKeyword() .'</keyword>';
		$b .= '<sender>'. $oMI->getSender() .'</sender>';
		$b .= '<body>'. htmlspecialchars($oMI->getBody(), ENT_NOQUOTES) .'</body>';
		$b .= '</message>';
		/* ----- Construct XML Body End ----- */

		return $b;
	}

	/**
	 * Constructs an MT-Push Notification Request to GoMobile.
	 *
	 * The method will return an XML Document in the following format:
	 * <code>
	 *
	 * 	<message id="{CLIENT ID FOR THE MESSAGE}" type="2">
	 * 		<transaction>{CORRESPONDING ID FOR THE RECEIVED MO-Push Notification TO WHICH THIS MESSAGE IS A RESPONSE}</transaction>
	 *		<channel>{CHANNEL THE MESSAGE SHOULD BE SENT THROUGH}</channel>
	 * 		<keyword>{KEYWORD THE MESSAGE BELONGS TO}</keyword>
	 * 		<recipient>{PUSH ID OF THE RECIPIENT THE MESSAGE IS SENT TO}</recipient>
	 * 		<body type="{MESSAGE CONTENT TYPE}">{XML ENCODED BODY FOR THE MESSAGE}</body>
	 * 		<description>{DESCRIPTION OF THE MESSAGE CONTENT USED BY THE OPERATORS TO enhance their customer service and billing statements}</description>
	 * 	</message>
	 *
	 * </code>
	 * Please note, the sender is optional and the transaction tag is should be included for ALL messages
	 * which doesn't belong to a Bulk Keyword.
	 *
	 * @param 	PushNotification $oMI 	Push Notification Info object which holds the relevant data for the message
	 * @return 	string
	 */
	private function _constMTPushNotification(PushNotification $oMI)
	{
		/* ----- Construct XML Body Start ----- */
		$b = '<message id="'. $oMI->getClientID() .'" type="'. GoMobileMessage::iMT_PUSH_NOTIFICATION .'">';
		/*
		 * Include corresponding ID for the received MO-Push Notification in the response
		* This is required for ALL Keywords with the exception of a Bulk Keyword
		*/
		if ($oMI->getMOID() > 0) { $b .= '<transaction>'. $oMI->getMOID() .'</transaction>'; }
		$b .= '<channel>'. $oMI->getChannel() .'</channel>';
		$b .= '<keyword>'. $oMI->getKeyword() .'</keyword>';
		$b .= '<recipient>'. $oMI->getRecipient() .'</recipient>';
		/*
		 * The body of an Push Notification can be split into several MTs and concatenated by the recipient's
		* mobile device if the body is over 160 character in length. GoMobile will split the body
		* into several message with a length of 154 characters each.
		* The type attribute will define the content type for the message sent to the recipient.
		* This parameter is required by some Operators such as AT&T Mobility and Verizon Wireless in the US.
		* Please refer to GoMobile - Overview.pdf for details.
		*/
		$b .= '<body type="'. $oMI->getContentType() .'">'. htmlspecialchars($oMI->getBody(), ENT_NOQUOTES) .'</body>';
		/*
		 * Add description of the MT to enhance Operator Customer Support.
		* This is currently a requirement by AT&T Mobility in the US but will likely be required by other operators as well.
		*/
		$b .= '<description>'. htmlspecialchars($oMI->getDescription(), ENT_NOQUOTES) .'</description>';
		$b .= '</message>';
		/* ----- Construct XML Body End ----- */

		return $b;
	}

	/**
	 * Parses the XML reply from GoMobile and appends each status code for a message
	 * to the internal comma separated list of status codes.
	 * For messages which have been sent to multiple recipients (such as MMS) GoMobile will respond with
	 * a transaction ID for each recipient. In order to properly log the transactions, the method will create
	 * a copy of the original message information object for each recipient with its own unique GoMobile ID.
	 * This however means that the original Client ID will be discarded and a new ID generated for each copy.
	 *
	 * @param 	string $xml 	XML Response from GoMobile
	 */
	private function _parseReply($xml)
	{
		$obj_XML = simplexml_load_string(trim($xml) );

		// Authentication failed
		if (count($obj_XML->message) == 0)
		{
			// Loop through all status codes for request
			for ($n=0; $n<count($obj_XML->status); $n++)
			{
				foreach ($this->_aObj_Msgs as $obj_Msg)
				{
					$obj_Msg->appendReturnCode(intval($obj_XML->status[$n]["code"]) );
				}
			}
		}
		// Parse response codes for each message
		else
		{
			// Loop through all messages
			for ($i=0; $i<count($obj_XML->message); $i++)
			{
				$id = strval($obj_XML->message[$i]["clientid"]);
				// Message has been sent to Multiple Recipients
				if ($this->_aObj_Msgs[$id]->getType() == GoMobileMessage::iMT_MMS && count($this->_aObj_Msgs[$id]->getRecipients() ) > 1)
				{
					/*
					 * Instantiate temporary array for keeping track of number of processed replies for each recipient.
					 * This ensures that each message is logged with its own unique GoMobile ID even if the same message has been sent multiple time to a recipient
					 */
					$aProcessedReplies = array();
					// Instantiate list of comma separated GoMobile IDs that will be added to the original Message Object
					$sGoMobileIDs = "";

					// Process GoMobile Response for each Recipient
					$aRecipients = $this->_aObj_Msgs[$id]->getRecipients();
					for ($n=0; $n<count($aRecipients); $n++)
					{
						// New Recipient
						if (array_key_exists(strval($aRecipients[$n]), $aProcessedReplies) === false) { $aProcessedReplies[$aRecipients[$n] ] = 0; }

						// Instantiate copy of the original MMS
						$obj_MsgInfo = new MMS($this->_aObj_Msgs[$id]->getType(), $this->_aObj_Msgs[$id]->getCountry(), $this->_aObj_Msgs[$id]->getOperator(), $this->_aObj_Msgs[$id]->getChannel(), $this->_aObj_Msgs[$id]->getKeyword(), $this->_aObj_Msgs[$id]->getPrice(), $aRecipients[$n], $this->_aObj_Msgs[$id]->getSubject(), $this->_aObj_Msgs[$id]->getBody(), $this->_aObj_Msgs[$id]->getMOID() );
						// Transfer files to the copy
						$aObj_Files = $this->_aObj_Msgs[$id]->getFiles();
						foreach ($aObj_Files as $obj_File)
						{
							$obj_MsgInfo->addFile($obj_File);
						}
						// Transfer other configurations to the copy
						$obj_MsgInfo->setDRM($this->_aObj_Msgs[$id]->getDRM() );
						$obj_MsgInfo->setContentType($this->_aObj_Msgs[$id]->getContentType() );

						// Extract status information for the specific copy from GoMobile's response
						$obj_Status = $obj_XML->xpath('/root/message[@clientid="'. $id .'" and @address="'. $aRecipients[$n] .'"]');
						$obj_Status = $obj_Status[$aProcessedReplies[$aRecipients[$n] ] ];
						// Set GoMobile ID for the Message
						$obj_MsgInfo->setGoMobileID($obj_Status["id"]);
						// Loop through all status codes for the message
						for($j=0; $j<count($obj_Status); $j++)
						{
							$obj_MsgInfo->appendReturnCode(intval($obj_Status->status[$j]["code"]) );
							$this->_aObj_Msgs[$id]->appendReturnCode(intval($obj_Status->status[$j]["code"]) );
						}
						// Add copy to internal array of sent messages
						$this->_aObj_Msgs[$obj_MsgInfo->getClientID()] = &$obj_MsgInfo;
						$sGoMobileIDs .= ", ". $obj_MsgInfo->getGoMobileID();
						// Clear memory and update processing counters
						unset($obj_MsgInfo);
						$aProcessedReplies[$aRecipients[$n] ]++;
						$i++;
					}
					$this->_aObj_Msgs[$id]->setGoMobileID(substr($sGoMobileIDs, 2) );
					// Remove the message from the internal array to avoid duplicate log entries
					unset($this->_aObj_Msgs[$id]);
				}
				// Message has been sent to a Single Recipient
				else
				{
					// Set GoMobile ID for the Message
					$this->_aObj_Msgs[$id]->setGoMobileID( (integer) $obj_XML->message[$i]["id"]);
					// Loop through all status codes for each message
					for ($n=0; $n<count($obj_XML->message[$i]->status); $n++)
					{
						$this->_aObj_Msgs[$id]->appendReturnCode(intval($obj_XML->message[$i]->status[$n]["code"]) );
					}
				}
			}
		}
	}
}

/**
 * Data class for holding all information for an SMS Message.
 * The Message can be any of the following SMS Types:
 * 	1. MO-SMS
 * 	2. MT-SMS
 * 	3. MT-WAP Push
 * 	4. MT-Binary SMS
 * For an MT-WAP Push a URL should be part of the data and for an MT-Binary SMS
 * a User Data Header (UDH) in Hex Format should be supplied.
 *
 * The class offers methods for adding additional data to an SMS such as defining
 * and alpha numeric send for an MT using the setSender method.
 * For MO-SMS the class also offers convenience methods which can be used to
 * for viewing the configuration data received from GoMobile.
 * These methods includes:
 * 	- usePostBill, indicates whether GoMobile has enabled Post Billing for the Keyword
 * 	- useWapPush, indicates whether the Operator supports MT-WAP Push messages
 * 	- getMsgLength, provides the max message length that GoMobile will accept for the Operator
 *
 */
class SMS extends GoMobileMessage
{
	/**
	 * Internal ID of the Operator the message will be sent through
	 *
	 * @var integer
	 */
	private $_iOperator;
	/**
	 * Boolean flag indicating whether the Operator supports WAP Push messages
	 *
	 * @var boolean
	 */
	private $_bWapPush = false;
	/**
	 * Max number of characters pr message for the Operator
	 *
	 * @var integer
	 */
	private $_iMsgLength = 160;

	/**
	 * Channel the message will be routed via
	 * Please note: This is the short code for SMS messages
	 *
	 * @var string
	 */
	private $_sChannel;

	/**
	 * Keyword the message belongs to
	 *
	 * @var string
	 */
	private $_sKeyword;
	/**
	 * Boolean flag indicating whether "Post Billing" should be used.
	 * Post billing is mainly used for download application where the user shouldn't be
	 * charged until after the content has been downloaded successfully
	 *
	 * @var boolean
	 */
	private $_bPostBill = false;

	/**
	 * Price in country's smallest currency
	 *
	 * @var integer
	 */
	private $_iPrice;

	/**
	 * Message that will be sent to the recipient
	 * Please note: This should be max 160 characters
	 *
	 * @var string
	 */
	private $_sBody;

	/**
	 * Recipient's mobile number
	 *
	 * @var string
	 */
	private $_sRecipient = false;
	/**
	 * Alphanumeric Sender / Originator of the SMS
	 * This will essentially replace the sender in the phone and make the user unable to use the reply
	 * option to send back a response.
	 * The value can be used to brand a company as the sender of the SMS.
	 *
	 * @var string
	 */
	private $_sSender = false;

	/**
	 * URL for the WAP Push message
	 *
	 * @var string
	 */
	private $_sURL = "";
	/**
	 * User Defined Header encoded as a Hex String
	 * If a UDH is used, the Message body must also be Hex Encoded
	 *
	 * @var string
	 */
	private $_sUDH = "";

	/**
	 * GoMobile ID of the corresponding Mobile Originated transaction
	 *
	 * @var integer
	 */
	private $_iMOID = -1;

	/**
	 * Boolean flag indicating whether Message Concatenation should be used.
	 * By concatenating several SMS messages it's possible to send a message with
	 * a body that is over 160 characters in length by splitting it up into several
	 * messages with a length of 154 characters each and specifying to the mobile device
	 * that these messages are related and should be concatenated into one message upon receival.
	 * This behaviour is only applicable for the following types of MTs:
	 * 	2. MT-SMS
	 * 	4. MT-Binary SMS
	 *
	 * @var boolean
	 */
	private $_bConcat = false;

	/**
	 * ID of the Content Type for the message.
	 * Please note this setting is only appplicable for MTs.
	 * The variable can take the following values:
	 * 	100. wap/other
	 * 	101. download/ringtone
	 * 	102. download/image
	 * 	103. download/game
	 * 	104. download/theme
	 * 	105. download/animation
	 * 	106. download/video
	 * 	200. text/other
	 * 	201. text/chat
	 * 	202. text/quiz
	 * 	203. text/sweepstake
	 * 	204. text/info
	 * 	205. text/entertainment
	 * 	206. text/sport
	 * 	207. text/vote or text/poll
	 * 	208. text/trivia
	 * 	209. text/alert
	 * 	210. text/tv
	 *
	 * The default for the variable is determined from the SMS type as follows:
	 * <pre>
	 * 	MO-SMS: 200. text/other
	 * 	MT-SMS: 200. text/other
	 * 	MT-WAP Push: 100. wap/other
	 * 	MT-Binary SMS: 100. wap/other
	 * </pre>
	 *
	 * @var integer
	 */
	private $_iContentType = -1;

	/**
	 * Description of the Message Content that Operators use to enhance their customer service and billing statements.
	 * AT&T Mobility in the US currently requires this field to be set but it is expected that it will likely be required by other operators as well.
	 *
	 * @var string
	 */
	private $_sDescription;

	/**
	 * Default Constructor
	 *
	 * @param integer $t 	Message type: (1 - MO-SMS, 2 - MT-SMS, 3 - MT-WAP Push, 4 - MT-Binary SMS)
	 * @param integer $c 	Internal ID of the country the message will be sent to
	 * @param integer $o 	Internal ID of the Operator the message will be sent through
	 * @param string $ch 	Channel the message will be routed via, this is the short code for SMS messages
	 * @param string $kw 	Keyword the message belongs to
	 * @param integer $p 	Price in country's smallest currency
	 * @param string $addr 	Address to the End-User's device
	 * @param string $b 	Message that will be sent to the recipient, should be max 160 characters
	 * @param string $id 	ID of the corresponding Mobile Originated transaction
	 * @param string $misc 	WAP Push URL or Hex Encoded UDH
	 */
	public function __construct($t, $c, $o, $ch, $kw, $p, $addr, $b, $id=-1, $misc="")
	{
		parent::__construct($t, $c);

		$this->_iOperator = (integer) $o;
		$this->_sChannel = (string) $ch;
		$this->_sKeyword = (string) strtoupper($kw);
		$this->_iPrice = (integer) $p;
		$this->_sBody = (string) $b;

		switch ($t)
		{
		case (parent::iMO_SMS):			// MO-SMS
			$this->_sSender = $addr;
			$this->setGoMobileID($id);
			$this->_iContentType = 200;
			break;
		case (parent::iMT_SMS):			// MT-SMS
			$this->_sRecipient = $addr;
			$this->_iMOID = $id;
			$this->_iContentType = 200;
			break;
		case (parent::iMT_WAP_PUSH):	// MT-WAP Push
			$this->_sRecipient = $addr;
			$this->_sURL = (string) $misc;
			$this->_iMOID = $id;
			$this->_iContentType = 100;
			break;
		case (parent::iMT_BINARY_SMS):	// Binary MT-SMS
			$this->_sRecipient = $addr;
			$this->_iMOID = $id;
			$this->_sUDH = (string) $misc;
			$this->_iContentType = 100;
			break;
		default:
			break;
		}
	}

	/**
	 * Produces a Message Info object which holds the relevant data for the MO-SMS received from GoMobile.
	 * The input array should be the result of a SAX Parsing of the following XML Document:
	 * <code>
	 *
	 * 	<?xml version="1.0" encoding="ISO-8859-1"?>
	 * 	<root>
	 * 		<message id="{GOMOBILE'S ID FOR THE MESSAGE}" type="1">
	 * 			<country idc="{INTERNATIONAL DIALLING CODE}" currency="{COUNTYRY'S CURRENCY}">{ID OF THE COUNTRY THE MESSAGE WAS RECEIVED IN}</country>
	 * 			<channel>{CHANNEL THE MESSAGE SHOULD WAS RECEIVED THROUGH}</channel>
	 * 			<operator wappush="{FLAG INDICATING WHETHER THE OPERATOR SUPPORTS WAP PUSH}">{ID OF THE OPERATOR THE MESSAGE WAS RECEIVED FROM}</country>
	 * 			<keyword postbill="{FLAG INDICATING WHETHER THE KEYWORD USES POST BILLING">{KEYWORD THE MESSAGE BELONGS TO}</keyword>
	 * 			<sender>{MSISDN OF THE SENDER}</sender>
	 * 			<body>{XML ENCODED BODY FOR THE MESSAGE}</body>
	 * 		</message>
	 * 	</root>
	 *
	 * </code>
	 *
	 * @param 	SimpleXMLElement $oXML 	XML Document with the MO-SMS
	 * @return 	SMS
	 * @throws 	E_USER_ERROR
	 */
	public static function produceMessage($oXML)
	{
		if ($oXML instanceof SimpleXMLElement === false) { trigger_error("Argument 1 passed to SMS::produceMessage() must be an object of type: SimpleXMLElement", E_USER_ERROR); }

		$obj_SMS = new SMS(intval($oXML->message["type"]), intval($oXML->message->country), intval($oXML->message->operator), utf8_decode(strval($oXML->message->channel) ), utf8_decode(strval($oXML->message->keyword) ), 0, utf8_decode(strval($oXML->message->sender) ), utf8_decode(strval($oXML->message->body) ), intval($oXML->message["id"]) );
		$obj_SMS->setIDC(intval($oXML->message->country["idc"]) );
		$obj_SMS->setCurrency(intval($oXML->message->country["currency"]) );
		if (strval($oXML->message->keyword["postbill"]) == "true") { $obj_SMS->enablePostBill(); }
		if (strval($oXML->message->operator["wappush"]) == "true") { $obj_SMS->enableWapPush(); }

		return $obj_SMS;
	}

	/**
	 * Retrieves GoMobile's ID of the Operator the message will be sent through
	 *
	 * @return integer
	 */
	public function getOperator() { return $this->_iOperator; }
	/**
	 * Retrieves the Channel the message will be routed via
	 * Please note: This is the short code for SMS messages
	 *
	 * @return string
	 */
	public function getChannel() { return $this->_sChannel; }
	/**
	 * Retrieves the Keyword the message belongs to
	 *
	 * @return string
	 */
	public function getKeyword() { return $this->_sKeyword; }
	/**
	 * Retrieves the Price in country's smallest currency
	 *
	 * @return integer
	 */
	public function getPrice() { return $this->_iPrice; }
	/**
	 * Retrieves the Message that will be sent to the recipient
	 * Please note: This should be max 160 characters.
	 * If the message is a WAP Push (type = 3) this will be used as the message indication
	 *
	 * @return string
	 */
	public function getBody() { return $this->_sBody; }
	/**
	 * Retrieves the Recipient's mobile number
	 *
	 * @return string
	 */
	public function getRecipient() { return $this->_sRecipient; }
	/**
	 * Returns the Sender of the SMS.
	 *
	 * For an MO-SMS this is the MSISDN of the end-user who sent the message to GoMobile.
	 * For an MT this is the Alphanumeric Sender / Originator of the SMS which has been set
	 *
	 * @return string
	 */
	public function getSender() { return $this->_sSender; }
	/**
	 * Retrieves the ID of the corresponding Mobile Originated transaction
	 *
	 * @return integer
	 */
	public function getMOID() { return $this->_iMOID; }
	/**
	 * Retrieves the URL for a WAP Push
	 *
	 * @return string
	 */
	public function getURL() { return $this->_sURL; }

	/**
	 * Retrieves the Hex Encoded User Data Header for the Binary MT-SMS
	 *
	 * @return string
	 */
	public function getUDH() { return $this->_sUDH; }

	/**
	 * Returns a Log entry for the Message which the GoMobile Client can write to the log file.
	 * The method will return a string with the following format:
	 * <code>
	 *  Client ID = {CLIENT'S UNIQUE ID FOR THE TRANSACTION} ###
	 * 	Type = {MESSAGE TYPE} ###
	 * 	Country = {COUNTRY THE TRANSACTION TOOK PLACE IN} ###
	 * 	Operator = {OPERATOR THE MESSAGE WAS ROUTED THROUGH} ###
	 * 	Channel = {CHANNEL THE MESSAGE WAS COMMUNICATED VIA} ###
	 * 	Keyword = {KEYWORD THE MESSAGE BELONGS TO} ###
	 * 	Sender = {MSISDN FOR THE END-USER WHO SENT THE MESSAGE FOR MO-SMS, CLIENT DEFINED ALPHANUMERIC SENDER FOR MTs} ###
	 * 	Recipient = {EMPTY FOR AN MO-SMS, MSISDN FOR THE END-USER WHO SHOULD RECEIVE THE MESSAGE FOR MTs} ###
	 * 	Body = {MESSAGE BODY} ###
	 * 	MO ID = {GOMOBILE'S ID FOR THE MO THAT THIS MT IS SENT IN RESPONSE TO} ###
	 *  Post Bill = {INDICATION OF WHETHER POST BILLING IS ENABLED FOR THIS TRANSACTION} ###
	 *  URL = {URL WHICH THE END-USER SHOULD GO TO UPON ACTIVATING THE WAP PUSH} ###
	 *  UDH = {USER DATA HEADER FOR THE BINARY SMS}
	 * </code>
	 * Please note, for Post Billing the word "Yes" is used if Post Billing is enabled and "No" if not
	 * For MOs this indicates whether the Keyword uses Post Billing.
	 * The Body will be an XML Document encoded in accordance with the XML Standard for an MT-Binary SMS
	 *
	 * @see 	GoMobileMessage::getLogEntry()
	 *
	 * @return 	string
	 */
	public function getLogEntry()
	{
		// Escape text formatting characters (\t, \n, \r) in body
		$body = $this->getBody();
		$body = str_replace("\t", "\\t", $body);
		$body = str_replace("\r", "\\r", $body);
		$body = str_replace("\n", "\\n", $body);

		// Create Log entry
		$sEntry = parent::getLogEntry();
		$sEntry .= " ### ". "Operator = ". $this->getOperator();
		$sEntry .= " ### ". "Channel = ". $this->getChannel();
		$sEntry .= " ### ". "Keyword = ". $this->getKeyword();
		$sEntry .= " ### ". "Price = ". $this->getPrice();
		if (strlen($this->getSender() ) > 0) { $sEntry .= " ### ". "Sender = ". $this->getSender(); }
		// MT
		if ($this->getType() > parent::iMO_SMS) {	$sEntry .= " ### ". "Recipient = ". $this->getRecipient(); }
		$sEntry .= " ### ". "Body = ". $body;
		$sEntry .= " ### ". "Content Type = ". $this->getContentType();
		if (strlen($this->getDescription() ) > 0) { $sEntry .= " ### ". "Description = ". $this->getDescription(); }
		// MT
		if ($this->getType() > parent::iMO_SMS)
		{
			$sEntry .= " ### ". "Post Bill = ". ($this->usePostBill()===true?"Yes":"No");
			$sEntry .= " ### ". "MO ID = ". $this->getMOID();
		}
		// Add Type specific log entries
		switch ($this->getType() )
		{
		case (parent::iMO_SMS):			// MO-SMS
		case (parent::iMT_SMS):			// MT-SMS
			break;
		case (parent::iMT_WAP_PUSH):	// MT-WAP Push
			$sEntry .= " ### ". "URL = ". $this->getURL();
			break;
		case (parent::iMT_BINARY_SMS):	// MT-Binary SMS
			$sEntry .= " ### ". "UDH = ". $this->getUDH();
			break;
		default:	// Error: Unsupported message type
			trigger_error("Message type: ". $this->getType() ." not supported", E_USER_ERROR);
			break;
		}

		return $sEntry;
	}

	/**
	 * Allows the client to set the Sender / Originator of the SMS to something other than the short code
	 * for an MT.
	 * This will essentially replace the sender in the phone and make the user unable to use the reply
	 * option to send back a response.
	 * The value can be used to brand a company as the sender of the SMS.
	 * Please note, this method should NOT be used for an MO-SMS
	 *
	 * @param string $str 	Alphanumeric Sender / Originator of the SMS
	 */
	public function setSender($str) { $this->_sSender = $str; }
	/**
	 * Enables "Post Billing" for the transaction.
	 * Post billing is mainly used for download application where the user shouldn't be
	 * charged until after the content has been downloaded successfully
	 */
	public function enablePostBill() { $this->_bPostBill = true; }
	/**
	 * Returns true if "Post Billing" has been enabled, otherwise false
	 * Post billing is mainly used for download application where the user shouldn't be
	 * charged until after the content has been downloaded successfully
	 *
	 * @return boolean
	 */
	public function usePostBill() { return $this->_bPostBill; }
	/**
	 * Enables WAP Push for the transaction
	 */
	public function enableWapPush() { $this->_bWapPush = true; }
	/**
	 * Returns true if the Operator supports WAP Push, otherwise false
	 *
	 * @return boolean
	 */
	public function useWapPush() { return $this->_bWapPush; }
	/**
	 * Sets the max number of characters pr message for the Operator
	 *
	 * @param integer $max 	Max length pr message for the Operator
	 */
	public function setMsgLength($max) { $this->_iMsgLength = $max; }
	/**
	 * Returns the max number of characters pr message for the Operator
	 *
	 * @return integer
	 */
	public function getMsgLength() { return $this->_iMsgLength; }

	/**
	 * Convenience method for returning the applicable address for the End-User's Mobile Device
	 * For an MO-SMS the method will return the Sender.
	 * For any form of MT the method will return the Recipient.
	 *
	 * @see 	SMS::getSender()
	 * @see 	SMS::getRecipient()
	 *
	 * @return 	string
	 *
	 * @throws 	E_USER_ERROR
	 */
	public function getAddress()
	{
		switch ($this->getType() )
		{
		case (parent::iMO_SMS):			// MO-SMS
			return $this->getSender();
			break;
		case (parent::iMT_SMS):			// MT-SMS
		case (parent::iMT_WAP_PUSH):	// MT-WAP Push
		case (parent::iMT_BINARY_SMS):	// MT-Binary SMS
			return $this->getRecipient();
			break;
		default:	// Error: Unsupported message type
			trigger_error("Message type: ". $this->getType() ." not supported", E_USER_ERROR);
			return null;
			break;
		}
	}

	/**
	 * Enables concatenation of the message body to allow a body longer
	 * than 160 characters to be transmitted over several SMS messages.
	 * By concatenating several SMS messages it's possible to send a message with
	 * a body that is over 160 characters in length by splitting it up into several
	 * messages with a length of 154 characters each and specifying to the mobile device
	 * that these messages are related and should be concatenated into one message upon receival.
	 * This behaviour is only applicable for the following types of MTs:
	 * <pre>
	 * 	2. MT-SMS
	 * 	4. MT-Binary SMS
	 * </pre>
	 *
	 */
	public function enableConcatenation() { $this->_bConcat = true; }
	/**
	 * Returns true if Concatenation of the message body has been enabled, otherwise false
	 *
	 * @see 	SMS::enableConcatenation()
	 *
	 * @return 	boolean
	 */
	public function useConcatenation() { return $this->_bConcat; }

	/**
	 * Returns the ID of the Content Type for the message.
	 * Please note this setting is only appplicable for MTs.
	 * The method can return the following values:
	 * 	100. wap/other
	 * 	101. download/ringtone
	 * 	102. download/image
	 * 	103. download/game
	 * 	104. download/theme
	 * 	105. download/animation
	 * 	106. download/video
	 * 	200. text/other
	 * 	201. text/chat
	 * 	202. text/quiz
	 * 	203. text/sweepstake
	 * 	204. text/info
	 * 	205. text/entertainment
	 * 	206. text/sport
	 * 	207. text/vote or text/poll
	 * 	208. text/trivia
	 * 	209. text/alert
	 * 	210. text/tv
	 *
	 * The default for the variable is determined from the SMS type as follows:
	 * <pre>
	 * 	MO-SMS: 200. text/other
	 * 	MT-SMS: 200. text/other
	 * 	MT-WAP Push: 100. wap/other
	 * 	MT-Binary SMS: 100. wap/other
	 * </pre>
	 *
	 * @return integer
	 */
	public function getContentType() { return $this->_iContentType; }
	/**
	 * Defines the Content Type for the message.
	 * Please note this setting is only appplicable for MTs.
	 * The input parameter should be one of the following values:
	 * 	100. wap/other
	 * 	101. download/ringtone
	 * 	102. download/image
	 * 	103. download/game
	 * 	104. download/theme
	 * 	105. download/animation
	 * 	106. download/video
	 * 	200. text/other
	 * 	201. text/chat
	 * 	202. text/quiz
	 * 	203. text/sweepstake
	 * 	204. text/info
	 * 	205. text/entertainment
	 * 	206. text/sport
	 * 	207. text/vote or text/poll
	 * 	208. text/trivia
	 * 	209. text/alert
	 * 	210. text/tv
	 *
	 * @param 	integer $ct 	Content Type for the message.
	 */
	public function setContentType($ct) { $this->_iContentType = $ct; }

	/**
	 * Retrieves the Message Description that Operators use to enhance their customer service and billing statements.
	 * AT&T Mobility in the US currently requires this field to be set but it is expected that it will likely be required by other operators as well.
	 *
	 * @return string
	 */
	function getDescription() { return $this->_sDescription; }
	/**
	 * Sets the Message Description that Operators use to enhance their customer service and billing statements.
	 * AT&T Mobility in the US currently requires this field to be set but it is expected that it will likely be required by other operators as well.
	 *
	 * @param 	string $desc 	Description of the Message Content, the description should be as unique as possible but limited to 20 alphanumeric characters
	 */
	function setDescription($desc) { $this->_sDescription = $desc; }
}

/**
 * Data class for holding all relevant information for a Delivery Report for a Transaction.
 * The Delivery Report can be used to determine the current state for the Transaction
 *
 */
class DeliveryReport extends GoMobileMessage
{
	/**
	 * Recipient's mobile number
	 *
	 * @var string
	 */
	private $_sRecipient = false;

	/**
	 * ID of the Transaction the Delivery Report is for
	 *
	 * @var integer
	 */
	private $_iMTID;

	/**
	 * The Status Code indicating the current state of the MT that the Delivery Report is valid for.
	 * Please refer to GoMobile - Overview.pdf for Details
	 *
	 * @var integer
	 */
	private $_iState;
	/**
	 * Textual description of the Status Code
	 *
	 * @var string
	 */
	private $_sDescription;
	/**
	 *  Resend interval which controls when / how an MT which returns in this state should be re-transmitted by GoMobile:
	 * 	-2: Do no resend
	 * 	-1: Re-route through alternative Connection
	 * 	 0: Resend immediately through the same Gateway
	 * 	1+: Schedule MT for resending after this number of seconds
	 *
	 * @var integer
	 */
	private $_iResendInteval;

	/**
	 * Default Constructor
	 *
	 * @param integer $c 	Internal ID of the country the message will be sent to
	 * @param string $addr 	Address to the End-User's device
	 * @param integer $txn 	ID of the Transaction the Delivery Report is for
	 * @param integer $id 	GoMobile's ID for the Delivery Report
	 * @param integer $s 	Code Indicating the Status for the MT that the Delivery Report is valid for
	 * @param string $d 	Textual description of the Status Code
	 * @param integer $ri 	Resend interval which controls when / how an MT which returns in this state should be re-transmitted by GoMobile, default to -2: DO NOT RESEND
	 */
	public function __construct($c, $addr, $txn, $id, $s, $d, $ri=-2)
	{
		parent::__construct(parent::iDELIVERY_REPORT, $c);

		$this->setGoMobileID($id);
		$this->_sRecipient = $addr;
		$this->_iMTID = $txn;
		$this->_iState = $s;
		$this->_sDescription = $d;
		$this->_iResendInterval = $ri;
	}

	/**
	 * Produces a Message Info object which holds the relevant data for the Delivery Report received from GoMobile.
	 * The input array should be the result of a SAX Parsing of the following XML Document:
	 * <code>
	 *
	 * 	<?xml version="1.0" encoding="ISO-8859-1"?>
	 * 	<root>
	 * 		<message type="5" id="{GOMOBILE'S ID FOR THE DELIVERY REPORT}">
	 * 			<transaction>{ID OF THE TRANSACTION THE DELIVERY REPORT IS VALID FOR}</transaction>
	 * 			<status code="{CODE INDICATING THE STATUS FOR THE DELIVERY REPORT}">{TEXT DESCRIBING THE STATUS CODE}</status>
	 * 			<country idc="{INTERNATIONAL DIALLING CODE}" currency="{COUNTRY'S CURRENCY}">{ID OF THE COUNTRY THE MESSAGE IS SENT IN}</country>
	 * 			<recipient>{MSISDN OF THE END-USER THE MESSAGE WAS SENT TO}</recipient>
	 * 		</message>
	 * 	</root>
	 *
	 * </code>
	 *
	 * @param 	SimpleXMLElement $oXML 	XML Document with the Delivery Report
	 * @return 	DeliveryReport
	 */
	public static function produceMessage($oXML)
	{
		if ($oXML instanceof SimpleXMLElement === false) { trigger_error("Argument 1 passed to DeliveryReport::produceMessage() must be an object of type: SimpleXMLElement", E_USER_ERROR); }

		$obj_DR = new DeliveryReport(intval($oXML->message->country), strval($oXML->message->recipient), intval($oXML->message->transaction), intval($oXML->message["id"]), intval($oXML->message->status["code"]), utf8_decode(strval($oXML->message->status) ) );
		$obj_DR->setIDC(intval($oXML->message->country["idc"]) );
		$obj_DR->setCurrency(intval($oXML->message->country["currency"]) );

		return $obj_DR;
	}

	/**
	 * Retrieves the Recipient's mobile number
	 *
	 * @return string
	 */
	public function getRecipient() { return $this->_sRecipient; }

	/**
	 * Retrieves the ID of the Transaction the Delivery Report is for
	 *
	 * @return integer
	 */
	public function getMTID() { return $this->_iMTID; }

	/**
	 * Retrieves the Status Code indicating the current state of the MT that the Delivery Report is valid for
	 * Please refer to GoMobile - Overview.pdf for Details
	 *
	 * @return integer
	 */
	public function getState() { return $this->_iState; }
	/**
	 * Retrieves the Textual description of the Status Code
	 *
	 * @return string
	 */
	public function getDescription() { return $this->_sDescription; }
	/**
	 * Returns the Resend interval which controls when / how an MT which returns in this state should be re-transmitted by GoMobile:
	 * 	-2. Do no resend
	 * 	-1. Re-route through alternative Connection
	 * 	 0. Resend immediately through the same Gateway
	 * 	1+. Schedule MT for resending after this number of seconds
	 *
	 * @return integer
	 */
	public function getResendInterval() { return $this->_iResendInterval; }

	/**
	 * Returns a Log entry for the Message which the GoMobile Client can write to the log file.
	 * The method will return a string with the following format:
	 * <code>
	 *
	 * 	Client ID = {CLIENT'S UNIQUE ID FOR THE TRANSACTION} ###
	 * 	Type = {MESSAGE TYPE} ###
	 * 	Country = {COUNTRY THE TRANSACTION TOOK PLACE IN} ###
	 * 	Recipient = {EMPTY FOR AN MO-SMS, MSISDN FOR THE END-USER WHO SHOULD RECEIVE THE MESSAGE FOR MTs} ###
	 * 	Body = {MESSAGE BODY} ###
	 * 	MT ID = {GOMOBILE'S ID FOR THE MT THAT THIS DELIVERY REPORT IS VALID FOR} ###
	 * 	State = {STATUS CODE INDICATING THE STATE FOR THE MT THE DELIVERY REPORT IS VALID FOR} ###
	 * 	Description = {TEXTUAL DESCRIPTION OF THE STATUSE CODE}
	 *
	 * </code>
	 * Please note, for Post Billing the word "Yes" is used if Post Billing is enabled and "No" if not
	 * For MOs this indicates whether the Keyword uses Post Billing.
	 * The Body will be an XML Document encoded in accordance with the XML Standard for an MT-Binary SMS
	 *
	 * @see 	GoMobileMessage::getLogEntry()
	 *
	 * @return 	string
	 */
	public function getLogEntry()
	{
		// Escape text formatting characters (\t, \n, \r) in body
		$desc = $this->getDescription();
		$desc = str_replace("\t", "\\t", $desc);
		$desc = str_replace("\r", "\\r", $desc);
		$desc = str_replace("\n", "\\n", $desc);

		// Create Log entry
		$sEntry = parent::getLogEntry();
		$sEntry .= " ### ". "Recipient = ". $this->getRecipient();
		$sEntry .= " ### ". "MT ID = ". $this->getMTID();
		$sEntry .= " ### ". "State = ". $this->getState();
		$sEntry .= " ### ". "Description = ". $desc;

		return $sEntry;
	}

	/**
	 * Wrapper for method: getRecipient to make the class conform to the behaviour
	 * of class: SMS.
	 *
	 * @see 	DeliveryReport::getRecipient()
	 *
	 * @return 	string
	 */
	public function getAddress() { return $this->getRecipient(); }
}

/**
 * Data class for holding all information for an MMMS Message.
 * The Message can be any of the following MMS Types:
 * 	6. MO-MMS
 * 	7. MT-MMS
 *
 * The class offers methods for adding additional data to an MMS such as:
 * 	- Adding additional recipients
 * 	- Adding additional content files
 * 	- Setting the Digital Rights Management
 *
 */
class MMS extends GoMobileMessage
{
	/**
	 * Internal ID of the Operator the message will be sent through
	 *
	 * @var integer
	 */
	private $_iOperator;

	/**
	 * Channel the message will be routed via
	 * Please note: This is the short code for SMS messages
	 *
	 * @var string
	 */
	private $_sChannel;

	/**
	 * Keyword the message belongs to
	 *
	 * @var string
	 */
	private $_sKeyword;

	/**
	 * Price in country's smallest currency
	 *
	 * @var integer
	 */
	private $_iPrice;

	/**
	 * The subject of the MMS Message
	 *
	 * @var string
	 */
	private $_sSubject = false;
	/**
	 * SMIL Document used to describe the the content of the MMS
	 *
	 * @var string
	 */
	private $_sBody;

	/**
	 * The MSISDN of the end-user who sent the message to GoMobile.
	 *
	 * @var string
	 */
	private $_sSender = false;
	/**
	 * List of Recipients' mobile numbers
	 *
	 * @var string
	 */
	private $_aRecipients;

	/**
	 * GoMobile ID of the corresponding Mobile Originated transaction
	 *
	 * @var integer
	 */
	private $_iMOID = -1;

	/**
	 * ID of the Content Type for the message.
	 * Please note this setting is only appplicable for MTs.
	 * The variable can take the following values:
	 * 	100. wap/other
	 * 	101. download/ringtone
	 * 	102. download/image
	 * 	103. download/game
	 * 	104. download/theme
	 * 	105. download/animation
	 * 	106. download/video
	 * 	200. text/other
	 * 	201. text/chat
	 * 	202. text/quiz
	 * 	203. text/sweepstake
	 * 	204. text/info
	 * 	205. text/entertainment
	 * 	206. text/sport
	 * 	207. text/vote or text/poll
	 * 	208. text/trivia
	 * 	209. text/alert
	 * 	210. text/tv
	 *
	 * The default for the variable is determined from the MMS type as follows:
	 * <pre>
	 * 	MO-MMS: 100. wap/other
	 * 	MT-MMS: 100. wap/other
	 * </pre>
	 *
	 * @var integer
	 */
	private $_iContentType = 100;

	/**
	 * Description of the Message Content that Operators use to enhance their customer service and billing statements.
	 * AT&T Mobility in the US currently requires this field to be set but it is expected that it will likely be required by other operators as well.
	 *
	 * @var string
	 */
	private $_sDescription;

	/**
	 * List of content files that are part of the MMS Message.
	 *
	 * @var array
	 */
	private $_aObj_Files = array();

	/**
	 * Digital Rights Management used for the MMS Message.
	 *
	 * @var DRM
	 */
	private $_obj_DRM = null;

	/**
	 * Default Constructor
	 *
	 * @param integer $t 	Message type: (6 - MO-MMS or 7 - MT-MMS)
	 * @param integer $c 	Internal ID of the country the message will be sent to
	 * @param integer $o 	Internal ID of the Operator the message will be sent through
	 * @param string $ch 	Channel the message will be routed via, this is the short code for SMS messages
	 * @param string $kw 	Keyword the message belongs to
	 * @param integer $p 	Price in country's smallest currency
	 * @param string $addr 	Address to the End-User's device
	 * @param string $s 	The subject of the MMS Message
	 * @param string $b 	SMIL Document used to describe the the content of the MMS
	 * @param string $id 	ID of the corresponding Mobile Originated transaction
	 *
	 */
	public function __construct($t, $c, $o, $ch, $kw, $p, $addr, $s, $b, $id=-1)
	{
		parent::__construct($t, $c);

		$this->_iOperator = (integer) $o;
		$this->_sChannel = (string) $ch;
		$this->_sKeyword = (string) strtoupper($kw);
		$this->_iPrice = (integer) $p;
		$this->_sSubject = (string) $s;
		$this->_sBody = (string) $b;

		switch ($t)
		{
		case (GoMobileMessage::iMO_MMS):	// MO-MMS
			$this->_sSender = $addr;
			$this->setGoMobileID($id);
			break;
		case (GoMobileMessage::iMT_MMS):	// MT-MMS
			settype($addr, "array");
			$this->_aRecipients = $addr;
			$this->_iMOID = $id;
			// No Digital Rights Management used for MMS
			$this->_obj_DRM = DRM::produceDRM(0);
			break;
		default:
			break;
		}
	}

	/**
	 * Produces a Message Info object which holds the relevant data for the MO-SMS received from GoMobile.
	 * The input array should be the result of a SAX Parsing of the following XML Document:
	 * <code>
	 *
	 * 	<?xml version="1.0" encoding="ISO-8859-1"?>
	 * 	<root>
	 * 		<message id="{CLIENT ID FOR THE MESSAGE}" type="6">
	 *			<country>{ID OF THE COUNTRY THE MESSAGE WAS RECEIVED IN}</country>
	 *			<channel>{CHANNEL THE MESSAGE SHOULD WAS RECEIVED THROUGH}</channel>
	 *			<operator>{ID OF THE OPERATOR THE MESSAGE WAS RECEIVED FROM}</country>
	 *			<keyword>{KEYWORD THE MESSAGE BELONGS TO}</keyword>
	 *			<sender>{MSISDN OF THE SENDER}</sender>
	 *			<subject>{XML ENCODED SUBJECT FOR THE MESSAGE}</subject>
	 *			<body>{XML ENCODED SMIL DOCUMENT FOR THE MESSAGE}</body>
	 *			<files>
	 *				<file id="{UNIQUE FILE ID REFERENCED IN SMIL}" type="{MIME TYPE FOR FILE}">{BASE64 ENCODED BINARY DATA}</file>
	 *				<file id="{UNIQUE FILE ID REFERENCED IN SMIL}" type="{MIME TYPE FOR FILE}">{BASE64 ENCODED BINARY DATA}</file>
	 *				...
	 *			</files>
	 *		</message>
	 * 	</root>
	 *
	 * </code>
	 *
	 * @param 	SimpleXMLElement $oXML 	XML Document with the MO-MMS
	 * @return 	MMS
	 * @throws 	E_USER_ERROR
	 */
	public static function produceMessage($oXML)
	{
		if ($oXML instanceof SimpleXMLElement === false) { trigger_error("Argument 1 passed to MMS::produceMessage() must be an object of type: SimpleXMLElement", E_USER_ERROR); }

		$obj_MMS = new MMS(intval($oXML->message["type"]), intval($oXML->message->country), intval($oXML->message->operator), utf8_decode(strval($oXML->message->channel) ), utf8_decode(strval($oXML->message->keyword) ), 0, utf8_decode(strval($oXML->message->sender) ), utf8_decode(strval($oXML->message->subject) ), htmlspecialchars_decode(utf8_decode(strval($oXML->message->body) ) ), intval($oXML->message["id"]) );
		// Add files
		for($i=0; $i<count($oXML->message->files->file); $i++)
		{
			$data = base64_decode(strval($oXML->message->files->file[$i]) );
			if (stristr(strval($oXML->message->files->file[$i]["type"]), "text") == true) { $data = utf8_decode($data); }
			$obj_MMS->addFile(new File(strval($oXML->message->files->file[$i]["id"]), strval($oXML->message->files->file[$i]["type"]), $data) );
		}

		return $obj_MMS;
	}

	/**
	 * Retrieves GoMobile's ID of the Operator the message will be sent through
	 *
	 * @return integer
	 */
	public function getOperator() { return $this->_iOperator; }
	/**
	 * Retrieves the Channel the message will be routed via
	 * Please note: This is the short code for SMS messages
	 *
	 * @return string
	 */
	public function getChannel() { return $this->_sChannel; }
	/**
	 * Retrieves the Keyword the message belongs to
	 *
	 * @return string
	 */
	public function getKeyword() { return $this->_sKeyword; }
	/**
	 * Retrieves the Price in country's smallest currency
	 *
	 * @return integer
	 */
	public function getPrice() { return $this->_iPrice; }
	/**
	 * Retrieves the subject of the MMS Message
	 *
	 * @return string
	 */
	public function getSubject() { return $this->_sSubject; }
	/**
	 * Retrieves the SMIL Document used to describe the the content of the MMS.
	 * If no SMIL Document has been defined for the body, the method will construct one
	 * for all content files using the order of the internal array of File objects.
	 *
	 * @return string
	 */
	public function getBody()
	{
		// Generate SMIL Document
		if (empty($this->_sBody) === true)
		{
			$smil = '<smil>';
			$smil .= '<head>';
			$smil .= '<layout>';
			$smil .= '<root-layout />';
			$smil .= '<region id="Message" top="0%" height="100%" fit="meet" />';
			$smil .= '</layout>';
			$smil .= '</head>';
			$smil .= '<body>';
			// Add Content Files for the MMS Message
			$aObj_Files = $this->getFiles();
			foreach ($aObj_Files as $obj_File)
			{
				$aTemp = explode("/", strtolower($obj_File->getType() ) );

				$smil .= '<par dur="5s">';
				switch ($aTemp[0])
				{
				case "audio":
					$smil .= '<audio src="cid:'. $obj_File->getID() .'" region="Message" />';
					break;
				case "image":
					$smil .= '<img src="cid:'. $obj_File->getID() .'" region="Message" />';
					break;
				case "text":
					$smil .= '<text src="cid:'. $obj_File->getID() .'" region="Message" />';
					break;
				case "video":
					$smil .= '<video src="cid:'. $obj_File->getID() .'" region="Message" />';
					break;
				default:
					break;
				}
				$smil .= '</par>';
			}
			$smil .= '</body>';
			$smil .= '</smil>';
		}
		else { $smil = $this->_sBody; }

		return $smil;
	}
	/**
	 * Retrieves the list of mobile numbers for each Recipient
	 *
	 * @return array
	 */
	public function getRecipients() { return $this->_aRecipients; }
	/**
	 * Returns the Sender of the MO-MMS.
	 * This is the MSISDN of the end-user who sent the message to GoMobile.
	 *
	 * @return string
	 */
	public function getSender() { return $this->_sSender; }
	/**
	 * Retrieves the ID of the corresponding Mobile Originated transaction
	 *
	 * @return integer
	 */
	public function getMOID() { return $this->_iMOID; }

	/**
	 * Returns a Log entry for the Message which the GoMobile Client can write to the log file.
	 * The method will return a string with the following format:
	 * <code>
	 *
	 *  Client ID = {CLIENT'S UNIQUE ID FOR THE TRANSACTION} ###
	 * 	Type = {MESSAGE TYPE} ###
	 * 	Country = {COUNTRY THE TRANSACTION TOOK PLACE IN} ###
	 * 	Operator = {OPERATOR THE MESSAGE WAS ROUTED THROUGH} ###
	 * 	Channel = {CHANNEL THE MESSAGE WAS COMMUNICATED VIA} ###
	 * 	Keyword = {KEYWORD THE MESSAGE BELONGS TO} ###
	 * 	Sender = {MSISDN FOR THE END-USER WHO SENT THE MESSAGE FOR MO-SMS, CLIENT DEFINED ALPHANUMERIC SENDER FOR MTs} ###
	 * 	Recipient = {EMPTY FOR AN MO-SMS, MSISDN FOR THE END-USER WHO SHOULD RECEIVE THE MESSAGE FOR MTs} ###
	 * 	Body = {MESSAGE BODY} ###
	 * 	MO ID = {GOMOBILE'S ID FOR THE MO THAT THIS MT IS SENT IN RESPONSE TO} ###
	 *  Post Bill = {INDICATION OF WHETHER POST BILLING IS ENABLED FOR THIS TRANSACTION} ###
	 *  URL = {URL WHICH THE END-USER SHOULD GO TO UPON ACTIVATING THE WAP PUSH} ###
	 *  UDH = {USER DATA HEADER FOR THE BINARY SMS}
	 *
	 * </code>
	 * Please note, for Post Billing the word "Yes" is used if Post Billing is enabled and "No" if not
	 * For MOs this indicates whether the Keyword uses Post Billing.
	 * The Body will be an XML Document encoded in accordance with the XML Standard for an MT-Binary SMS
	 *
	 * @see 	GoMobileMessage::getLogEntry()
	 *
	 * @return 	string
	 */
	public function getLogEntry()
	{
		// Escape text formatting characters (\t, \n, \r) in body
		$body = $this->getBody();
		$body = str_replace("\t", "\\t", $body);
		$body = str_replace("\r", "\\r", $body);
		$body = str_replace("\n", "\\n", $body);

		// Create Log entry
		$sEntry = parent::getLogEntry();
		$sEntry .= " ### ". "Operator = ". $this->getOperator();
		$sEntry .= " ### ". "Channel = ". $this->getChannel();
		$sEntry .= " ### ". "Keyword = ". $this->getKeyword();
		$sEntry .= " ### ". "Price = ". $this->getPrice();
		// Add Sender for the MO-MMS
		if ($this->getType() == parent::iMO_SMS)
		{
			$sEntry .= " ### ". "Sender = ". $this->getSender();
		}
		// Add Recipients for the MT-MMS
		else
		{
			$aRecipients = $this->getRecipients();
			$sEntry .= " ### ". "Recipient = ". $aRecipients[0];
			for ($i=1; $i<count($aRecipients); $i++)
			{
	 			$sEntry .= ", ". $aRecipients[$i];
			}
		}
		$sEntry .= " ### ". "Subject = ". $this->getSubject();
		$sEntry .= " ### ". "Body = ". $body;
		$sEntry .= " ### ". "Content Type = ". $this->getContentType();
		$sEntry .= " ### ". "Files = ";
		// Add Content Files for the MMS Message
		$aObj_Files = $this->getFiles();
		foreach ($aObj_Files as $id => $obj_File)
		{
			$sEntry .= $id .":". $obj_File->getType() .", ";
		}
		// Remove trailing ,
		$sEntry = substr($sEntry, 0, strlen($sEntry)-2);
		if (strlen($this->getDescription() ) > 0) { $sEntry .= " ### ". "Description = ". $this->getDescription(); }

		// Add Type specific log entries
		switch ($this->getType() )
		{
		case (6):	// MO-MMS
			break;
		case (7):	// MT-MMS
			$sEntry .= " ### ". "MO ID = ". $this->getMOID();
			$sEntry .= " ### ". "DRM = ". $this->getDRM()->getType();
			break;
		default:	// Error: Unsupported message type
			trigger_error("Message type: ". $this->getType() ." not supported", E_USER_ERROR);
			break;
		}

		return $sEntry;
	}

	/**
	 * Adds a Recipient for the MT-MMS.
	 *
	 * @param string $addr 	Address to the End-User's device
	 */
	public function addRecipient($addr) { $this->_aRecipients[] = (float) $addr; }
	/**
	 * Adds a content file to the MT-MMS.
	 *
	 * @param 	File $obj 	File Object for the file that should be added
	 */
	public function addFile(File &$obj) { $this->_aObj_Files[$obj->getID()] = $obj; }
	/**
	 * Sets the Digital Rights Managements for the MT-MMS.
	 *
	 * @param 	DRM $obj 	Object describing the Digital Rights Management that should be used for the MMS
	 */
	public function setDRM(DRM &$obj) { $this->_obj_DRM = $obj; }
	/**
	 * Returns all Content Files for the MMS Message
	 *
	 * @return 	array
	 */
	public function &getFiles() { return $this->_aObj_Files; }
	/**
	 * Returns the Digital Rights Managements for the MT-MMS.
	 *
	 * @return 	DRM
	 */
	public function &getDRM() { return $this->_obj_DRM; }

	/**
	 * Returns the ID of the Content Type for the message.
	 * Please note this setting is only appplicable for MTs.
	 * The method can return the following values:
	 * 	100. wap/other
	 * 	101. download/ringtone
	 * 	102. download/image
	 * 	103. download/game
	 * 	104. download/theme
	 * 	105. download/animation
	 * 	106. download/video
	 * 	200. text/other
	 * 	201. text/chat
	 * 	202. text/quiz
	 * 	203. text/sweepstake
	 * 	204. text/info
	 * 	205. text/entertainment
	 * 	206. text/sport
	 * 	207. text/vote or text/poll
	 * 	208. text/trivia
	 * 	209. text/alert
	 * 	210. text/tv
	 *
	 * The default for the variable is determined from the SMS type as follows:
	 * <pre>
	 * 	MO-MMS: 200. text/other
	 * 	MT-MMS: 200. text/other
	 * </pre>
	 *
	 * @return integer
	 */
	public function getContentType() { return $this->_iContentType; }
	/**
	 * Defines the Content Type for the message.
	 * Please note this setting is only appplicable for MTs.
	 * The input parameter should be one of the following values:
	 * 	100. wap/other
	 * 	101. download/ringtone
	 * 	102. download/image
	 * 	103. download/game
	 * 	104. download/theme
	 * 	105. download/animation
	 * 	106. download/video
	 * 	200. text/other
	 * 	201. text/chat
	 * 	202. text/quiz
	 * 	203. text/sweepstake
	 * 	204. text/info
	 * 	205. text/entertainment
	 * 	206. text/sport
	 * 	207. text/vote or text/poll
	 * 	208. text/trivia
	 * 	209. text/alert
	 * 	210. text/tv
	 *
	 * @param 	integer $ct 	Content Type for the message.
	 */
	public function setContentType($ct) { $this->_iContentType = $ct; }

	/**
	 * Retrieves the Message Description that Operators use to enhance their customer service and billing statements.
	 * AT&T Mobility in the US currently requires this field to be set but it is expected that it will likely be required by other operators as well.
	 *
	 * @return string
	 */
	function getDescription() { return $this->_sDescription; }
	/**
	 * Sets the Message Description that Operators use to enhance their customer service and billing statements.
	 * AT&T Mobility in the US currently requires this field to be set but it is expected that it will likely be required by other operators as well.
	 *
	 * @param 	string $desc 	Description of the Message Content, the description should be as unique as possible but limited to 20 alphanumeric characters
	 */
	function setDescription($desc) { $this->_sDescription = $desc; }
}

/**
 * Data class for a content file for the MMS Message
 *
 */
class File
{
	/**
	 * Unique ID of the File for referencing it in the SMIL Document that describes the MMS Message.
	 *
	 * @var string
	 */
	private $_sFileID;
	/**
	 * File's Mime type
	 *
	 * @var string
	 */
	private $_sMimeType;
	/**
	 * Binary Data for the File
	 *
	 * @var string
	 */
	private $_sData;

	/**
	 * Constructs a new object for representing a Content File in an MMS Message
	 *
	 * @param string $id 	Unique ID of the File for referencing it in the SMIL Document that describes the MMS Message.
	 * @param string $type 	File's Mime type
	 * @param string $data 	Binary Data for the File
	 */
	public function __construct($id, $type, $data)
	{
		$this->_sFileID = $id;
		$this->_sMimeType = $type;
		$this->_sData = $data;
	}

	/**
	 * Returns the Unique ID of the File for referencing it in the SMIL Document that describes the MMS Message.
	 *
	 * @return 	string
	 */
	public function getID() { return $this->_sFileID; }
	/**
	 * Returns the File's Mime type
	 *
	 * @return 	string
	 */
	public function getType() { return $this->_sMimeType; }
	/**
	 * Returns the Binary Data for the File
	 *
	 * @return 	string
	 */
	public function getData() { return $this->_sData; }
}

/**
 * Data class describing the Digital Rights Management (DRM) used to protect the content of an MT-MMS.
 * The Digital Rights Management can be one of the following types:
 * 	0. Off / No DRM
 * 	1. Forward Locking
 * 	2. Combined Delivery
 * 	3. Separate Delivery
 * Please refer to OMA-Download-DRMREL-V1_0-20040615-A.pdf for details on Digital Rights Management.
 *
 */
class DRM
{
	const iNONE = 0;
	const iFORWARD_LOCKING = 1;
	const iCOMBINED_DELIVERY = 2;
	const iSEPARATE_DELIVERY = 3;

	/**
	 * Type of Digtial Rights Management:
	 * 	0. Off / No DRM
	 * 	1. Forward Locking
	 * 	2. Combined Delivery
	 * 	3. Separate Delivery
	 *
	 * @var integer
	 */
	private $_iType;
	/**
	 * Number of times the content of the MMS can be Played / Viewed.
	 * Is only applicable for Combined Delivery and Separate Delivery.
	 *
	 * @var integer
	 */
	private $_iCount;
	/**
	 * Start Timestamp defining when the content files in the MMS Message can be played / viewed from.
	 * Is in the format: YYYY-MM-DD hh:mm:ss and is only applicable for Combined Delivery and Separate Delivery.
	 *
	 * @var timestamp
	 */
	private $_tsStart;
	/**
	 * End Timestamp defining when the content files in the MMS Message can be played / viewed to.
	 * Is in the format: YYYY-MM-DD hh:mm:ss and is only applicable for Combined Delivery and Separate Delivery.
	 *
	 * @var timestamp
	 */
	private $_tsEnd;
	/**
	 * Duration the content files in the MMS Message can be viewed in from the MMS is originally received.
	 * Is only applicable for Combined Delivery and Separate Delivery.
	 *
	 * @var Interval
	 */
	private $_obj_Interval;

	/**
	 * Constructs a new Digital Rights Management object for an MMS.
	 *
	 * @param integer $type 	Type of Digial Rights Management:
	 * 								0. Off / No DRM
	 * 								1. Forward Locking
	 * 								2. Combined Delivery
	 * 								3. Separate Delivery
	 * @param integer $count 	Number of times the content of the MMS can be Played / Viewed.
	 * @param timestamp $start 	Start Timstemp (YYYY-MM-DD hh:mm:ss) defining when the content files in the MMS Message can be played / viewed from
	 * @param timestamp $end 	End Timestamp (YYYY-MM-DD hh:mm:ss) defining when the content files in the MMS Message can be played / viewed to
	 * @param Interval $obj 	Duration the content files in the MMS Message can be viewed in from the MMS is originally received.
	 */
	public function __construct($type, $count, $start, $end, Interval &$obj=null)
	{
		$this->_iType = (integer) $type;
		$this->_iCount = (integer) $count;
		$this->_tsStart = trim($start);
		$this->_tsEnd = trim($end);
		$this->_obj_Interval = $obj;
	}
	/**
	 * Produces a Digital Rights Management object which holds the relevant data how an MMS is protected via DRM.
	 * This method is an overloaded method and its exact behaviour will depend on the number of parameters
	 * passed to the method.
	 *
	 * The method either accepts a number of parameters passed individually which is used to construct the DRM object of the correct type
	 * The expected parameters are as follows:<br>
	 * 	<b>1: DRM Off or Forward Locking: </b><br>
	 * 		integer $type	Type of Digial Rights Management:
	 * 								0. Off / No DRM
	 * 								1. Forward Locking
	 * 	<b>2: Combined or Separate Delivery:</b><br>
	 * 		integer $type	Type of Digial Rights Management:
	 * 								2. Combined Delivery
	 * 								3. Separate Delivery
	 * 	   The 2nd parameter must be one of the following:
	 * 		integer $count	Number of times the content of the MMS can be Played / Viewed
	 * 		timestamp $end 	End Timetamp (YYYY-MM-DD hh:mm:ss) defining when the content files in the MMS Message can be played / viewed to
	 * 		Interval $obj 	Duration the content files in the MMS Message can be viewed in from the MMS is originally received.
	 *	<b>3: Combined or Separate Delivery:</b><br>
	 * 		integer $type		Type of Digial Rights Management:
	 * 								2. Combined Delivery
	 * 								3. Separate Delivery
	 * 		timestamp $start 	Start Timestamp (YYYY-MM-DD hh:mm:ss) defining when the content files in the MMS Message can be played / viewed from
	 * 		timestamp $end 		End Timestamp (YYYY-MM-DD hh:mm:ss) defining when the content files in the MMS Message can be played / viewed to
	 *
	 * @return 	DRM
	 *
	 * @throws 	E_USER_ERROR
	 */
	public static function produceDRM()
	{
		$aArgs = func_get_args();
		$obj_DRM = null;
		// Parameters passed as array
		if(count($aArgs) == 1 && is_array($aArgs[0]) === true)
		{
			$aArgs = array_values($aArgs[0]);
		}
		switch(count($aArgs) )
		{
		case (1):	// No Digital Rights Management or Forward Lock
			$obj_DRM = new DRM($aArgs[0], 0, "", "");
			break;
		case (2):	// Combined or Separate Delivery
			switch (true)
			{
			case (is_int($aArgs[1]) ):	// Limit number of times MMS can be Played / Viewed
				$obj_DRM = new DRM($aArgs[0], intval($aArgs[1]), "", "");
				break;
			case ($aArgs[1] instanceof Interval):	// Limited period in which the MMS can be Played / Viewed
				$obj_DRM = new DRM($aArgs[0], 0, "", "", $aArgs[1]);
				break;
			case (strtotime($aArgs[1]) > time() ):	// MMS has Expiry Timestamp
				$obj_DRM = new DRM($aArgs[0], 0, "", $aArgs[1]);
				break;
			default:	// Unkown Argument type
				trigger_error("Argument 2 passed to DRM::produceMessage() must be: An Integer, An object of type Interval or a timestamp in the format: YYYY-MM-DD", E_USER_ERROR);
				break;
			}
			break;
		case (3):	// Combined or Separate Delivery with Start / End Timestamp
			$obj_DRM = new DRM($aArgs[0], 0, $aArgs[1], $aArgs[2], null);
			break;
		default:
			trigger_error("Invalid number of arguments: ". count($aArgs), E_USER_ERROR);
			break;
		}

		return $obj_DRM;
	}
	/**
	 * Type of Digital Rights Management:
	 * 	0. Off / No DRM
	 * 	1. Forward Locking
	 * 	2. Combined Delivery
	 * 	3. Separate Delivery
	 *
	 * @return 	integer
	 */
	public function getType() { return $this->_iType; }
	/**
	 * Returns the Number of times the content of the MMS can be Played / Viewed.
	 * Is only applicable for Combined Delivery and Separate Delivery.
	 *
	 * @return 	integer
	 */
	public function getCount() { return $this->_iCount; }
	/**
	 * Returns the Timestamp defining when the content files in the MMS Message can be played / viewed from.
	 * Is in the format: YYYY-MM-DD hh:mm:ss and is only applicable for Combined Delivery and Separate Delivery.
	 *
	 * @return 	timestamp
	 */
	public function getStart() { return $this->_tsStart; }
	/**
	 * Returns the Timestamp defining when the content files in the MMS Message can be played / viewed to.
	 * Is in the format: YYYY-MM-DD hh:mm:ss and is only applicable for Combined Delivery and Separate Delivery.
	 *
	 * @return 	timestamp
	 */
	public function getEnd() { return $this->_tsEnd; }
	/**
	 * Returns the Duration the content files in the MMS Message can be viewed in from the MMS is originally received.
	 * Is only applicable for Combined Delivery and Separate Delivery.
	 *
	 * @return 	Interval
	 */
	public function &getInterval() { return $this->_obj_Interval; }
}

/**
 * Data class for describing the period an MMS Message can be played / viewed in.
 * The period will start when the MMS is received by the end-user
 *
 */
class Interval
{
	/**
	 * Number of Years the MMS can be accessed in
	 *
	 * @var integer
	 */
	private $_iYears;
	/**
	 * Number of Months the MMS can be accessed in
	 *
	 * @var integer
	 */
	private $_iMonths;
	/**
	 * Number of Days the MMS can be accessed in
	 *
	 * @var integer
	 */
	private $_iDays;
	/**
	 * Number of Hours the MMS can be accessed in
	 *
	 * @var integer
	 */
	private $_iHours;
	/**
	 * Number of Minutes the MMS can be accessed in
	 *
	 * @var integer
	 */
	private $_iMinutes;

	/**
	 * Creates a new Interval object that describes the period the content files in an MMS message are accessible in
	 *
	 * @param integer $years 	Number of Years the MMS can be accessed in
	 * @param integer $months 	Number of Months the MMS can be accessed in
	 * @param integer $days 	Number of Days the MMS can be accessed in
	 * @param integer $hours 	Number of Hours the MMS can be accessed in
	 * @param integer $minutes 	Number of Minutes the MMS can be accessed in
	 */
	public function __construct($years, $months, $days, $hours, $minutes)
	{
		$this->_iYears = (integer) $years;
		$this->_iMonths = (integer) $months;
		$this->_iDays = (integer) $days;
		$this->_iHours = (integer) $hours;
		$this->_iMinutes = (integer) $minutes;
	}

	/**
	 * Returns the Number of Years the MMS can be accessed in
	 *
	 * @return 	integer
	 */
	public function getYears() { return $this->_iYears; }
	/**
	 * Returns the Number of Months the MMS can be accessed in
	 *
	 * @return 	integer
	 */
	public function getMonths() { return $this->_iMonths; }
	/**
	 * Returns the Number of Days the MMS can be accessed in
	 *
	 * @return 	integer
	 */
	public function getDays() { return $this->_iDays; }
	/**
	 * Returns the Number of Hours the MMS can be accessed in
	 *
	 * @return 	integer
	 */
	public function getHours() { return $this->_iHours; }
	/**
	 * Returns the Number of Minutes the MMS can be accessed in
	 *
	 * @return 	integer
	 */
	public function getMinutes() { return $this->_iMinutes; }
}

/**
 * Data class for holding all information for a Push Notification.
 * The Message can be any of the following Push Notification. Types:
 * 	10. MO-Push Notification
 * 	11. MT-Push Notification
 */
class PushNotification extends GoMobileMessage
{
	/**
	 * Recipient's Push ID
	 *
	 * @var string
	 */
	private $_sRecipient = false;
	/**
	 * The Push ID of the end-user who sent the message to GoMobile.
	 *
	 * @var string
	 */
	private $_sSender = false;
	/**
	 * Channel the message will be routed via
	 *
	 * @var string
	 */
	private $_sChannel;

	/**
	 * Keyword the message belongs to
	 *
	 * @var string
	 */
	private $_sKeyword;

	/**
	 * Message that will be sent to the recipient
	 * Please note: This should be max 160 characters
	 *
	 * @var string
	 */
	private $_sBody;
	/**
	 * GoMobile ID of the corresponding Mobile Originated transaction
	 *
	 * @var integer
	 */
	private $_iMOID = -1;

	/**
	 * ID of the Content Type for the message.
	 * Please note this setting is only appplicable for an MT-Push Notification.
	 * The variable can take the following values:
	 * 	100. wap/other
	 * 	101. download/ringtone
	 * 	102. download/image
	 * 	103. download/game
	 * 	104. download/theme
	 * 	105. download/animation
	 * 	106. download/video
	 * 	200. text/other
	 * 	201. text/chat
	 * 	202. text/quiz
	 * 	203. text/sweepstake
	 * 	204. text/info
	 * 	205. text/entertainment
	 * 	206. text/sport
	 * 	207. text/vote or text/poll
	 * 	208. text/trivia
	 * 	209. text/alert
	 * 	210. text/tv
	 *
	 * The default for the variable is determined from the Push Notification type as follows:
	 * <pre>
	 * 	MO-Push Notification: 200. text/other
	 * 	MT-Push Notification: 200. text/other
	 * </pre>
	 *
	 * @var integer
	 */
	private $_iContentType = -1;

	/**
	 * Description of the Message Content that Operators use to enhance their customer service and billing statements.
	 * AT&T Mobility in the US currently requires this field to be set but it is expected that it will likely be required by other operators as well.
	 *
	 * @var string
	 */
	private $_sDescription;

	/**
	 * Default Constructor
	 *
	 * @param integer $t 	Message type: (10 - MO-Push Notification, 11 - MT-Push Notification)
	 * @param string $ch 	Channel the message will be routed via
	 * @param string $kw 	Keyword the message belongs to
	 * @param string $addr 	Address to the End-User's device
	 * @param string $b 	Message that will be sent to the recipient, should be max 235 characters for an Apple Push Notifications and 4096 characters for a Google Cloud Message (GCM).
	 * @param string $id 	ID of the corresponding Mobile Originated transaction
	 */
	public function __construct($t, $ch, $kw, $addr, $b, $id=-1)
	{
		parent::__construct($t, -1);

		$this->_sChannel = (string) $ch;
		$this->_sKeyword = (string) strtoupper($kw);
		$this->_sBody = (string) $b;
		$this->_iContentType = 200;

		switch ($t)
		{
		case (parent::iMO_PUSH_NOTIFICATION):	// MO-Push Notification
			$this->_sSender = $addr;
			$this->setGoMobileID($id);
			break;
		case (parent::iMT_PUSH_NOTIFICATION):	// MT-Push Notification
			$this->_sRecipient = $addr;
			$this->_iMOID = $id;
			break;
		default:
			break;
		}
	}

	/**
	 * Produces a Message Info object which holds the relevant data for the MO-Push Notification received from GoMobile.
	 * <code>
	 *
	 * 	<?xml version="1.0" encoding="ISO-8859-1"?>
	 * 	<root>
	 * 		<message id="{GOMOBILE'S ID FOR THE MESSAGE}" type="10">
	 * 			<channel>{CHANNEL THE MESSAGE SHOULD WAS RECEIVED THROUGH}</channel>
	 * 			<keyword>{KEYWORD THE MESSAGE BELONGS TO}</keyword>
	 * 			<sender>{MSISDN OF THE SENDER}</sender>
	 * 			<body>{XML ENCODED BODY FOR THE MESSAGE}</body>
	 * 		</message>
	 * 	</root>
	 *
	 * </code>
	 *
	 * @param 	SimpleXMLElement $oXML 	XML Document with the MO-Push Notification
	 * @return 	PushNotification
	 * @throws 	E_USER_ERROR
	 */
	public static function produceMessage($oXML)
	{
		if ($oXML instanceof SimpleXMLElement === false) { trigger_error("Argument 1 passed to PushNotification::produceMessage() must be an object of type: SimpleXMLElement", E_USER_ERROR); }

		$obj_PushNotification = new PushNotification(intval($oXML->message["type"]), utf8_decode(strval($oXML->message->channel) ), utf8_decode(strval($oXML->message->keyword) ), utf8_decode(strval($oXML->message->sender) ), utf8_decode(strval($oXML->message->body) ), intval($oXML->message["id"]) );

		return $obj_PushNotification;
	}
	/**
	 * Retrieves the Channel the message will be routed via
	 *
	 * @return string
	 */
	public function getChannel() { return $this->_sChannel; }
	/**
	 * Retrieves the Keyword the message belongs to
	 *
	 * @return string
	 */
	public function getKeyword() { return $this->_sKeyword; }
	/**
	 * Retrieves the Message that will be sent to the recipient, it's assumed that the body will be a properly JSON encoded string that
	 * complies with Apple's or Google's requirements.
	 * Please note: This should be max 235 characters for an Apple Push Notifications and 4096 characters for a Google Cloud Message (GCM).
	 *
	 * @return string
	 */
	public function getBody() { return $this->_sBody; }
	/**
	 * Retrieves the Recipient's Push ID
	 *
	 * @return string
	 */
	public function getRecipient() { return $this->_sRecipient; }
	/**
	 * Returns the Sender of the MO-Push Notification.
	 *
	 * @return string
	 */
	public function getSender() { return $this->_sSender; }
	/**
	 * Retrieves the ID of the corresponding Mobile Originated transaction
	 *
	 * @return integer
	 */
	public function getMOID() { return $this->_iMOID; }

	/**
	 * Returns a Log entry for the Message which the GoMobile Client can write to the log file.
	 * The method will return a string with the following format:
	 * <code>
	 *  Client ID = {CLIENT'S UNIQUE ID FOR THE TRANSACTION} ###
	 * 	Type = {MESSAGE TYPE} ###
	 * 	Channel = {CHANNEL THE MESSAGE WAS COMMUNICATED VIA} ###
	 * 	Keyword = {KEYWORD THE MESSAGE BELONGS TO} ###
	 * 	Sender = {PUSH ID FOR THE END-USER WHO SENT THE MESSAGE FOR MO-PUSH NOTIFICATION} ###
	 * 	Recipient = {PUSH ID FOR THE END-USER WHO SHOULD RECEIVE THE MESSAGE FOR AN MT-PUSH NOTIFICATION} ###
	 * 	Body = {MESSAGE BODY} ###
	 * 	MO ID = {GOMOBILE'S ID FOR THE MO THAT THIS MT IS SENT IN RESPONSE TO} ###
	 * </code>
	 * The Body will be a JSON Document encoded in accordance with the JSON Standard.
	 *
	 * @see 	GoMobileMessage::getLogEntry()
	 *
	 * @return 	string
	 */
	public function getLogEntry()
	{
		// Escape text formatting characters (\t, \n, \r) in body
		$body = $this->getBody();
		$body = str_replace("\t", "\\t", $body);
		$body = str_replace("\r", "\\r", $body);
		$body = str_replace("\n", "\\n", $body);

		// Create Log entry
		$sEntry = parent::getLogEntry();
		$sEntry .= " ### ". "Channel = ". $this->getChannel();
		$sEntry .= " ### ". "Keyword = ". $this->getKeyword();
		if (strlen($this->getSender() ) > 0) { $sEntry .= " ### ". "Sender = ". $this->getSender(); }
		// MT-Push Notification
		if ($this->getType() == parent::iMT_PUSH_NOTIFICATION) {	$sEntry .= " ### ". "Recipient = ". $this->getRecipient(); }
		$sEntry .= " ### ". "Body = ". $body;
		$sEntry .= " ### ". "Content Type = ". $this->getContentType();
		if (strlen($this->getDescription() ) > 0) { $sEntry .= " ### ". "Description = ". $this->getDescription(); }
		// MT-Push Notification
		if ($this->getType() == parent::iMT_PUSH_NOTIFICATION) { $sEntry .= " ### ". "MO ID = ". $this->getMOID(); }

		return $sEntry;
	}

	/**
	 * Convenience method for returning the applicable address for the End-User's Mobile Device
	 * For an MO-Push Notification the method will return the Sender.
	 * For an MT-Push Notification method will return the Recipient.
	 *
	 * @see 	PushNotification::getSender()
	 * @see 	PushNotification::getRecipient()
	 *
	 * @return 	string
	 *
	 * @throws 	E_USER_ERROR
	 */
	public function getAddress()
	{
		switch ($this->getType() )
		{
		case (parent::iMO_PUSH_NOTIFICATION):	// MO-Push Notification
			return $this->getSender();
			break;
		case (parent::iMT_PUSH_NOTIFICATION):	// MT-Push Notification
			return $this->getRecipient();
			break;
		default:	// Error: Unsupported message type
			trigger_error("Message type: ". $this->getType() ." not supported", E_USER_ERROR);
			return null;
			break;
		}
	}

	/**
	 * Returns the ID of the Content Type for the message.
	 * Please note this setting is only appplicable for an MT-Push Notification.
	 * The method can return the following values:
	 * 	100. wap/other
	 * 	101. download/ringtone
	 * 	102. download/image
	 * 	103. download/game
	 * 	104. download/theme
	 * 	105. download/animation
	 * 	106. download/video
	 * 	200. text/other
	 * 	201. text/chat
	 * 	202. text/quiz
	 * 	203. text/sweepstake
	 * 	204. text/info
	 * 	205. text/entertainment
	 * 	206. text/sport
	 * 	207. text/vote or text/poll
	 * 	208. text/trivia
	 * 	209. text/alert
	 * 	210. text/tv
	 *
	 * The default for the variable is determined from the Push Notification type as follows:
	 * <pre>
	 * 	MO-Push Notification: 200. text/other
	 * 	MT-Push Notification: 200. text/other
	 * </pre>
	 *
	 * @return integer
	 */
	public function getContentType() { return $this->_iContentType; }
	/**
	 * Defines the Content Type for the message.
	 * Please note this setting is only appplicable for an MT-Push Notification.
	 * The input parameter should be one of the following values:
	 * 	100. wap/other
	 * 	101. download/ringtone
	 * 	102. download/image
	 * 	103. download/game
	 * 	104. download/theme
	 * 	105. download/animation
	 * 	106. download/video
	 * 	200. text/other
	 * 	201. text/chat
	 * 	202. text/quiz
	 * 	203. text/sweepstake
	 * 	204. text/info
	 * 	205. text/entertainment
	 * 	206. text/sport
	 * 	207. text/vote or text/poll
	 * 	208. text/trivia
	 * 	209. text/alert
	 * 	210. text/tv
	 *
	 * @param 	integer $ct 	Content Type for the message.
	 */
	public function setContentType($ct) { $this->_iContentType = $ct; }

	/**
	 * Retrieves the Message Description that Operators use to enhance their customer service and billing statements.
	 * AT&T Mobility in the US currently requires this field to be set but it is expected that it will likely be required by other operators as well.
	 *
	 * @return string
	 */
	function getDescription() { return $this->_sDescription; }
	/**
	 * Sets the Message Description that Operators use to enhance their customer service and billing statements.
	 * AT&T Mobility in the US currently requires this field to be set but it is expected that it will likely be required by other operators as well.
	 *
	 * @param 	string $desc 	Description of the Message Content, the description should be as unique as possible but limited to 20 alphanumeric characters
	 */
	function setDescription($desc) { $this->_sDescription = $desc; }
}
?>