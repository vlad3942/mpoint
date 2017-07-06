<?php
/**
 * The HTTP Client package is part of the general HTTP package and provides methods for easily
 * connecting to a remote HTTP compliant server using standardised method calls.
 *
 * @author Jonatan Evald Buus
 * @package HTTP
 * @subpackage HTTPClient
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 1.01
 *
 */

/**
 * Abstract super class for HTTP Exceptions
 */
abstract class HTTPException extends Exception { }

/**
 * Exception class used for throwing an exception when the configuration array used for
 * instantiating the class contains invalid configuration data
 *
 * @see	HTTPClient::__construct()
 */
class HTTPInvalidConnInfoException extends HTTPException { }

/**
 * Thrown if a connection cannot be established to the external application
 *
 * @see	HTTPClient::connect()
 */
class HTTPConnectionException extends HTTPException { }

/**
 * Thrown if an error occurs while sending the XML document to the external application
 *
 * @see	HTTPClient::send()
 */
class HTTPSendException extends HTTPException { }

/**
 * Thrown if an internal error occurs
 *
 * @see	HTTPClient::setState()
 */
class HTTPInternalException extends HTTPException { }

/**
 * Container class for holding all information required to connect to a remote HTTP Server
 *
 */
class HTTPConnInfo
{
	/**
	 * Protocol used for the connection
	 * 	- tcp
	 * 	- http (alias for tcp)
	 * 	- ssl
	 * 	- tls
	 * 	- https (alias for tls)
	 *
	 * @var array
	 */
	private $_aProtocols = array("tcp", "ssl", "tls");

	/**
	 * Supported connection methods for the HTTP connection, currently the following methods are available:
	 * 	- POST
	 * 	- GET
	 *
	 * @var array
	 */
	private $_aMethods = array("POST", "GET");

	/**
	 * Supported Content Types for the HTTP connection, currently the following content types are available:
	 * 	- text/xml
	 * 	- application/x-www-form-urlencoded
	 * 	- application/x-www-url-form-encoded
	 *	- application/www-url-form-encoded
	 *
	 * @var array
	 */
	private $_aContentTypes = array("text/xml", "application/x-www-form-urlencoded", "application/x-www-url-form-encoded", "application/www-url-form-encoded", "application/json");

	/**
	 * Protocol to use for connecting to the remote server
	 *
	 * @var string
	 */
	private $_sProtocol;

	/**
	 * Host the remote server can be contacted at
	 *
	 * @var string
	 */
	private $_sHost;
	/**
	 * IP address the remote server can be contacted at
	 *
	 * @var string
	 */
	private $_IP;

	/**
	 * Port the remote server is listening on
	 *
	 * @var integer
	 */
	private $_iPort;

	/**
	 * Number of seconds before the connection times out either when establishing the connection or when awaiting a reply from the remote server
	 *
	 * @var integer
	 */
	private $_iTimeout;

	/**
	 * The path to the application on the remote server
	 *
	 * @var string
	 */
	private $_sPath;

	/**
	 * Method used for sending the HTTP Request, currently the following methods are available:
	 * 	- POST
	 * 	- GET
	 *
	 * @var string
	 */
	private $_sMethod;

	/**
	 * Content Type that the body should be encoded in, currently the following content types are available:
	 * 	- text/xml
	 * 	- application/x-www-form-urlencoded
	 *
	 * @var string
	 */
	private $_sContentType;

	/**
	 * Username for authenticating with user to the remote server
	 *
	 * @var string
	 */
	private $_sUsername;

	/**
	 * Password for authenticating with user to the remote server
	 *
	 * @var string
	 */
	private $_sPassword;

	/**
	 * Active connection to the remote server
	 *
	 * @var resource
	 */
	private $_rConnection;

	/**
	 * Constructed HTTP Header to send
	 *
	 * @var string
	 */
	private $_sRequestHeader;

	/**
	 * Body of the HTTP request to send
	 *
	 * @var string
	 */
	private $_sRequestBody;

	/**
	 * HTTP Header returned by the remote server in the reply
	 *
	 * @var string
	 */
	private $_sReplyHeader;

	/**
	 * Body returned by the remote server in the reply
	 *
	 * @var string
	 */
	private $_sReplyBody;

	/**
	 * The HTTP Code that was returned by the remote server
	 * Defaults to -1 if not defined
	 *
	 * @var integer
	 */
	private $_iReturnCode = -1;

	/**
	 * The Error Code that was returned when attempting to establish a connection to the remote server
	 *
	 * @var integer
	 */
	private $_iErrCode;

	/**
	 * The Error Message that was returned when attempting to establish a connection to the remote server
	 *
	 * @var string
	 */
	private $_sErrMsg;

	/**
	 * Default constructor
	 *
	 * @param string $p 	Protocol used for the connection (tcp or http)
	 * @param string $h 	Host the remote server can be contacted at
	 * @param integer $prt 	Port the remote server is listening on
	 * @param integer $to 	Number of seconds before the connection times out either when establishing the connection or when awaiting a reply from the remote server
	 * @param string $pth 	The path to the application on the remote server
	 * @param string $m 	Method used for sending the HTTP Request (GET or POST)
	 * @param string $ct 	Content Type that the body should be encoded in (text/xml or application/x-www-form-urlencoded)
	 * @param string $un 	Username for authenticating with user to the remote server
	 * @param string $pw 	Password for authenticating with user to the remote server
	 * @param string $ip 	IP address the remote server can be contacted at
	 *
	 * @throws 				HTTPInvalidConnInfoException
	 */
	public function __construct($p, $h, $prt, $to, $pth, $m, $ct, $un="", $pw="", $ip="")
	{
		/* ---------- Input Validation Start ---------- */
		$p = strtolower($p);
		$m = strtoupper($m);
		$ct = strtolower($ct);

		if ($p == "http")	{ $p = "tcp"; }
		if ($p == "https")	{ $p = "tls"; }

		if (in_array($p, $this->_aProtocols) === false) { throw new HTTPInvalidConnInfoException("Invalid Protocol: ". $p, 1011); }
		if (intval($prt) <= 0) { throw new HTTPInvalidConnInfoException("Invalid Port: ". $prt, 1012); }
		if (intval($to) <= 0) { throw new HTTPInvalidConnInfoException("Invalid Timeout: ". $to, 1013); }
		if (empty($m) === false && in_array($m, $this->_aMethods) === false) { throw new HTTPInvalidConnInfoException("Invalid Method: ". $m, 1014); }
//		if (strtoupper($m) == "POST" && in_array($ct, $this->_aContentTypes) === false) { throw new HTTPInvalidConnInfoException("Invalid Content Type: ". $ct, 1015); }
		/* ---------- Input Validation End ---------- */

		$this->_sProtocol = (string) $p;
		$this->_sHost = (string) $h;
		$this->_iPort = (integer) $prt;
		$this->_iTimeout = (integer) $to;
		$this->_sPath = (string) $pth;
		$this->_sMethod = (string) $m;
		$this->_sContentType = (string) $ct;
		$this->_sUsername = (string) $un;
		$this->_sPassword = (string) $pw;
		if (empty($ip) === true) { $this->_IP = gethostbyname($h); }
		else { $this->_IP = $ip; }
	}

	/**
	 * Produces an HTTP Connection Info object which holds the necessary data for communicating with
	 * a remote server using the HTTP Client
	 *
	 * @see 	HTTPClient
	 *
	 * @return 	HTTPConnInfo
	 *
 	 * @throws 	HTTPInvalidConnInfoException
	 */
	public static function produceConnInfo()
	{
		$aArgs = func_get_args();
		// Parameters passed as array
		if(count($aArgs) == 1 && is_array($aArgs[0]) === true)
		{
			if (array_key_exists("ip", $aArgs[0]) === true) 		{ $aArgs[9] = $aArgs[0]["ip"]; }
			if (array_key_exists("password", $aArgs[0]) === true) 	{ $aArgs[8] = $aArgs[0]["password"]; }
			if (array_key_exists("username", $aArgs[0]) === true) 	{ $aArgs[7] = $aArgs[0]["username"]; }
			if (array_key_exists("contenttype", $aArgs[0]) === true){ $aArgs[6] = $aArgs[0]["contenttype"]; }
			if (array_key_exists("method", $aArgs[0]) === true) 	{ $aArgs[5] = $aArgs[0]["method"]; }
			if (array_key_exists("path", $aArgs[0]) === true) 		{ $aArgs[4] = $aArgs[0]["path"]; }
			if (array_key_exists("timeout", $aArgs[0]) === true) 	{ $aArgs[3] = $aArgs[0]["timeout"]; }
			if (array_key_exists("port", $aArgs[0]) === true) 		{ $aArgs[2] = $aArgs[0]["port"]; }
			if (array_key_exists("host", $aArgs[0]) === true) 		{ $aArgs[1] = $aArgs[0]["host"]; }
			if (array_key_exists("protocol", $aArgs[0]) === true) 	{ $aArgs[0] = $aArgs[0]["protocol"]; }
			else { unset($aArgs[0]); }
		}
		// Parameters passed as URL
		elseif (count($aArgs) == 1)
		{
			$aInfo = parse_url($aArgs[0]);
			if (array_key_exists("scheme", $aInfo) === true) { $aArgs[0] = $aInfo["scheme"]; }
			else { $aArgs[0] = "http"; }
			$aArgs[1] = $aInfo["host"];
			if (array_key_exists("port", $aInfo) === true) { $aArgs[2] = $aInfo["port"]; }
			elseif ($aArgs[0] == "https") { $aArgs[2] = 443; }
			else { $aArgs[2] = 80; }
			$aArgs[3] = 20;
			if (array_key_exists("path", $aInfo) === true) { $aArgs[4] = $aInfo["path"]; }
			else { $aArgs[4] = "/"; }
			if (array_key_exists("query", $aInfo) === true) { $aArgs[4] .= "?". $aInfo["query"]; }
			if (array_key_exists("fragment", $aInfo) === true) { $aArgs[4] = "#". $aInfo["fragment"]; }
			$aArgs[5] = "";
			$aArgs[6] = "application/x-www-form-urlencoded";
			if (array_key_exists("user", $aInfo) === true) { $aArgs[7] = $aInfo["user"]; }
			if (array_key_exists("pass", $aInfo) === true) { $aArgs[8] = $aInfo["pass"]; }
		}

		switch (count($aArgs) )
		{
		case (7):	// Parameters passed individually
			return new HTTPConnInfo($aArgs[0], $aArgs[1], $aArgs[2], $aArgs[3], $aArgs[4], $aArgs[5], $aArgs[6]);
			break;
		case (8):	// Parameters passed individually
			return new HTTPConnInfo($aArgs[0], $aArgs[1], $aArgs[2], $aArgs[3], $aArgs[4], $aArgs[5], $aArgs[6], "", "", $aArgs[9]);
			break;
		case (9):	// Parameters passed individually
			return new HTTPConnInfo($aArgs[0], $aArgs[1], $aArgs[2], $aArgs[3], $aArgs[4], $aArgs[5], $aArgs[6], $aArgs[7], $aArgs[8]);
			break;
		case (10):	// Parameters passed individually
			return new HTTPConnInfo($aArgs[0], $aArgs[1], $aArgs[2], $aArgs[3], $aArgs[4], $aArgs[5], $aArgs[6], $aArgs[7], $aArgs[8], $aArgs[9]);
			break;
		default:	// Error: Invalid number of arguments
			throw new HTTPInvalidConnInfoException("Invalid number of arguments: ". count($aArgs), 1001);
			break;
		}
	}

	/**
	 * Wrapper for produceConnInfo to ensure backwards compatibility.
	 *
	 * @deprecated
	 *
	 * @return 	HTTPConnInfo
	 */
	public static function produceHTTPConnInfo()
	{
		$aArgs = func_get_args();
		if (count($aArgs) === 1) { $aArgs = $aArgs[0]; }

		return HTTPConnInfo::produceConnInfo($aArgs);
	}

	/**
	 * Retrieves the Protocol used for the connection (tcp or http)
	 *
	 * @return string
	 */
	public function getProtocol() { return $this->_sProtocol; }
	/**
	 * Retrieves the Host address the remote server can be contacted at
	 *
	 * @return string
	 */
	public function getHost() { return $this->_sHost; }
	/**
	 * Retrieves the IP address the remote server can be contacted at
	 *
	 * @return string
	 */
	public function getIP() { return $this->_IP; }
	/**
	 * Retrieves the Port the remote server is listening on
	 *
	 * @return integer
	 */
	public function getPort() { return $this->_iPort; }
	/**
	 * Retrieves the Number of seconds before the connection times out either when establishing the connection or when awaiting a reply from the remote server
	 *
	 * @return integer
	 */
	public function getTimeout() { return $this->_iTimeout; }
	/**
	 * Retrieves the Path to the application on the remote server
	 *
	 * @return string
	 */
	public function getPath() { return $this->_sPath; }
	/**
	 * Retrieves the  Method used for sending the HTTP Request, currently the following methods are available:
	 * 	- POST
	 * 	- GET
	 *
	 * @return string
	 */
	public function getMethod() { return $this->_sMethod; }
	/**
	 * Retrieves the Content Type that the body should be encoded in, currently the following content types are available:
	 * 	- text/xml
	 * 	- application/x-www-form-urlencoded
	 *
	 * @return string
	 */
	public function getContentType() { return $this->_sContentType; }
	/**
	 * Retrieves the Username for authenticating with user to the remote server
	 *
	 * @return string
	 */
	public function getUsername() { return $this->_sUsername; }
	/**
	 * Retrieves the Password for authenticating with user to the remote server
	 *
	 * @return string
	 */
	public function getPassword() { return $this->_sPassword; }

	/**
	 * Retrieves the Active connection to the remote server
	 *
	 * @return resource
	 */
	public function getConnection() { return $this->_rConnection; }
	/**
	 * Retrieves the Constructed HTTP Header to send
	 *
	 * @return string
	 */
	public function getRequestHeader() { return $this->_sRequestHeader; }
	/**
	 * Retrieves the Body of the HTTP request to send
	 *
	 * @return string
	 */
	public function getRequestBody() { return $this->_sRequestBody; }
	/**
	 * Retrieves the HTTP Header returned by the remote server in the reply
	 *
	 * @return string
	 */
	public function getReplyHeader() { return $this->_sReplyHeader; }
	/**
	 * Retrieves the Body returned by the remote server in the reply
	 *
	 * @return string
	 */
	public function getReplyBody() { return $this->_sReplyBody; }
	/**
	 * Retrieves the HTTP Code that was returned by the remote server
	 *
	 * @return integer
	 */
	public function getReturnCode() { return $this->_iReturnCode; }
	/**
	 * Retrieves the Error Code that was returned when attempting to establish a connection to the remote server
	 *
	 * @return integer
	 */
	public function getErrCode() { return $this->_iReturnCode; }
	/**
	 * Retrieves the Error Message that was returned when attempting to establish a connection to the remote server
	 *
	 * @return string
	 */
	public function getErrMsg() { return $this->_iReturnCode; }
	/**
	 * Constructs the HTTP Connection Info as a URL
	 *
	 * @return string
	 */
	public function toURL() { return $this->_sProtocol ."://". $this->_sHost .":". $this->_iPort . $this->_sPath; }

	public function setConnection($c) { $this->_rConnection = $c; }
	public function setRequestHeader($h) { $this->_sRequestHeader = $h; }
	public function setRequestBody($b) { $this->_sRequestBody = $b; }
	public function setReplyHeader($h) { $this->_sReplyHeader = $h; }
	public function setReplyBody($b) { $this->_sReplyBody = $b; }
	public function setReturnCode($c) { $this->_iReturnCode = $c; }

	public function setErrCode($c) { $this->_iErrCode = $c; }
	public function setErrMsg($m) { $this->_sErrMsg = $m; }

	public function delConnection()
	{
		if (is_resource($this->_rConnection) === true) { fclose($this->_rConnection); }
		unset($this->_rConnection);
	}
}

/**
 * Basic class for handling HTTP connections to a HTTP server
 *
 */
class HTTPClient
{
	/**
	 * Defines state for when Connection Info has been succesfully loaded for the transaction.
	 */
	const iHTTP_CONNINFO = 11;

	/**
	 * Defines state for when a connection has been established with the remote server.
	 */
	const iHTTP_CONNECT = 12;

	/**
	 * Defines state for when the request data has been sent through the previously established
	 * connection to the remote server.
	 */
	const iHTTP_SENT = 13;

	/**
	 * Defines state for when a reply has been received from the remote server.
	 * after the request with data has previously been sent.
	 */
	const iHTTP_REPLY = 14;

	/**
	 * Defines state for to identify that an error occured during communication with the
	 * remote server.
	 * This state can be set by any of the methods invoked for the communication process:
	 * 	- connect
	 * 	- send
	 * 	- disconnect
	 * Please note, a transaction will never end in this state, when the log method encounters
	 * this state it will retrieve the $_iPrevState variable and log that instead as that will
	 * in fact be the actual state the transaction ended in.
	 *
	 * @see	HTTPClient::connect()
	 * @see	HTTPClient::send()
	 * @see	HTTPClient::disConnect()
	 */
	const iHTTP_ERROR = 10;

	/**
	 * Platform specific Carriage Return Line Feed, will be:
	 * 	- \n for *nix
	 * 	- \r\n for Windows
	 * 	- \r for Macintosh
	 */
	const CRLF = "\r\n";

	/**
	 * Template object for constructing the HTTP header
	 *
	 * @var Template $_obj_Template
	 */
	private $_obj_Template;

	/**
	 * Info object with the Connection Info required to establish a connection with the HTTP Server
	 *
	 * @var HTTPConnInfo $_obj_ConnInfo
	 */
	private $_obj_ConnInfo;

	/**
	 * Defines the current state that the current Transaction is in
	 *
	 * @var	integer $_iState
	 */
	private $_iState = 0;

	/**
	 * Defines the previos state that the current Transaction was in
	 *
	 * @var	integer $_iPrevState
	 */
	private $_iPrevState = 0;

	/**
	 * Default constructor
	 *
	 * @param 	Template $oTemplate 	Template object for constructing the HTTP header
	 * @param 	HTTPConnInfo $oCI 		Info object with the Connection Info required to establish a connection with the HTTP Server
	 */
	public function __construct(Template &$oTemplate, HTTPConnInfo &$oConnInfo)
	{
		$this->_obj_Template = $oTemplate;
		$this->_obj_ConnInfo = $oConnInfo;

		$this->setState(self::iHTTP_CONNINFO);
	}

	/* ---------- Set Methods Start ---------- */
	/**
	 * Sets the current state for the transaction and updates the Previous State accordingly
	 *
	 * @param	integer $s 	State of current transaction
	 *
	 * @throws 	HTTPInternalException
	 */
	private function setState($s)
	{
		/* ---------- Error Handling Start ---------- */
		if (empty($s) === true) { throw new HTTPInternalException("Undefined State", 1091); }
		if (intval($s) < 0) 	{ throw new HTTPInternalException("Invalid State: ". $s, 1092); }
		/* ---------- Error Handling End ---------- */

		$this->_iPrevState = $this->_iState;
		$this->_iState = $s;
	}
	/* ---------- Set Methods End ---------- */

	/* ---------- Get Methods Start ---------- */
	/**
	 * Retrieves the current state that the current Transaction is in
	 *
	 * @return 	integer		Current state that the current Transaction is in
	 */
	public function getState() { return $this->_iState; }

	/**
	 * Retrieves the Template object used for constructing the HTTP header
	 *
	 * @return 	Template	Template object for constructing the HTTP header
	 */
	private function getTemplate() { return $this->_obj_Template; }

	/**
	 * Retrieves the object with the Connection Info required to establish a connection with the HTTP Server
	 *
	 * @return 	HTTPConnInfo	Object with the Connection Info required to establish a connection with the HTTP Server
	 */
	private function getConnInfo() { return $this->_obj_ConnInfo; }

	/**
	 * Retrieves the Header for the HTTP Request
	 *
	 * @return 	string	HTTP Header for the request
	 */
	public function getRequestHeader() { return $this->getConnInfo()->getRequestHeader(); }

	/**
	 * Retrieves the Body for the HTTP Request
	 *
	 * @return 	string	HTTP Body for the request
	 */
	public function getRequestBody() { return $this->getConnInfo()->getRequestBody(); }

	/**
	 * Retrieves the HTTP Reply Header from the remote server
	 *
	 * @return 	string	HTTP reply header from the remote server
	 */
	public function getReplyHeader() { return $this->getConnInfo()->getReplyHeader(); }

	/**
	 * Retrieves the HTTP Reply Body from the remote server
	 *
	 * @return 	string	HTTP reply body from the remote server
	 */
	public function getReplyBody() { return $this->getConnInfo()->getReplyBody(); }

	/**
	 * Retrieves the HTTP Return Code from the remote server
	 *
	 * @return 	string	HTTP return code from the remote server
	 */
	public function getReturnCode() { return $this->getConnInfo()->getReturnCode(); }
	/* ---------- Get Methods End ---------- */

	/* ---------- HTTP Methods Start ---------- */
	/**
	 * Opens a new connection to a remote server that can be contacted using the HTTP protocol
	 * Please note: This method sets the connection in the internal HTTPConnInfo object: $_obj_ConnInfo
	 *
	 * @see 	HTTPConnInfo
	 * @see 	HTTPClient::$_obj_ConnInfo
	 *
	 * @throws 	HTTPConnectionException
	 */
	public function connect()
	{
		// Object has been initialised with Connection Info
		if ($this->getState() == self::iHTTP_CONNINFO)
		{
			$iErrNo = 0;
			$sErrMsg = "";

			// Open socket connection to host at port
			$sp = stream_socket_client("tcp://". $this->getConnInfo()->getIP() .":". $this->getConnInfo()->getPort(), $iErrNo, $sErrMsg, $this->getConnInfo()->getTimeout() );

			// Success: Connection established
			if (is_resource($sp) === true)
			{
				switch (strtolower($this->getConnInfo()->getProtocol() ) )
				{
				case "ssl":	// SSL Encryption: Not recommended after the identification of the POODLE vulnerability in SSLv3
					stream_socket_enable_crypto($sp, true, STREAM_CRYPTO_METHOD_SSLv23_CLIENT);
					break;
				case "tls":	// TLS Enryption
					stream_socket_enable_crypto($sp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
					break;
				default:	// Unencrypted
					break;
				}
				// Set timeout for socket
				if (stream_set_timeout($sp, $this->getConnInfo()->getTimeout() ) === false) { trigger_error("13 - Could not set socket timeout", E_USER_NOTICE); }
				// Set blocking mode for socket non-block: please refer to dk.php.net/manual/en/function.stream-set-blocking.php for further info
//				if (stream_set_blocking($sp, 0) === false) { trigger_error("14 - Could not set socket blocking mode", E_USER_NOTICE); }

				$this->getConnInfo()->setConnection($sp);
				$this->setState(self::iHTTP_CONNECT);
			}
			// Error: Unable to connect server
			else
			{
				// Set Status
				$this->getConnInfo()->setErrCode($iErrNo);
				$this->getConnInfo()->setErrMsg($sErrMsg);
				// Log Error
				$this->setState(self::iHTTP_ERROR);

				throw new HTTPConnectionException("Unable to connect server. Host: ". $this->getConnInfo()->getHost() ." at IP: ". $this->getConnInfo()->getIP() ." on Port: ". $this->getConnInfo()->getPort() ." with Timeout: ". $this->getConnInfo()->getTimeout() . self::CRLF . $sErrMsg ."(". $iErrNo .")", 1002);
			}
		}
		// Error: Connection info not provided at object initialisation
		else
		{
			$this->setState(self::iHTTP_ERROR);
			throw new HTTPConnectionException("Connection info not provided at object initialisation", 1001);
		}
	}

	/**
	 * Disconnects from an application that can be contacted using the HTTP protocol
	 * Please note: This method deletes the connection in the internal HTTPConnInfo object: $_obj_ConnInfo
	 *
	 * @see 	HTTPConnInfo
	 * @see 	HTTPClient::$_obj_ConnInfo
	 *
	 * @throws 	HTTPConnectionException
	 */
	public function disConnect()
	{
		// Connected to HTTP Server, close connection
		if (is_resource($this->getConnInfo()->getConnection() ) === true)
		{
			// Success: Connection closed
			if (fclose($this->getConnInfo()->getConnection() ) === true)
			{
				$this->getConnInfo()->delConnection();
			}
			// Error: Unable to close connection to external application
			else
			{
				$this->setState(self::iHTTP_ERROR);
				throw new HTTPConnectionException("Unable to close connection to HTTP Server", 1003);
			}
		}
	}

	/**
	 * Sends the provided XML document to an external application
	 * The application is contacted using the connection info found in the private $_obj_ConnInfo object
	 *
	 * @see 	HTTPConnInfo
	 * @see 	HTTPClient::$_obj_ConnInfo
	 *
	 * @param 	string $h 	Path to file with HTTP Header template file or actual HTTP Template
	 * @param 	string $b 	HTTP Body to send to the remote server
	 * @return 	integer		HTTP Status code, -1 on error
	 *
	 * @throws 	HTTPSendException
	 */
	public function send($h, $b="")
	{
		/* ---------- Error Handling Start ---------- */
		if (empty($h) === true) { throw new HTTPSendException("Undefined Header document", 1001); }
		if (empty($b) === true && strlen($this->getConnInfo()->getMethod() ) > 0 && $this->getConnInfo()->getMethod() != "GET") { throw new HTTPSendException("Undefined Body document", 1002); }
		/* ---------- Error Handling End ---------- */
		$code = -1;

		$this->getConnInfo()->setRequestBody($b);

		// Connection has been established to external application
		if ($this->getState() == self::iHTTP_CONNECT)
		{
			// Construct HTTP header
			$sHeader = $this->constHeader($h, strlen($b) );
			$this->getConnInfo()->setRequestHeader($sHeader);

			// Success: HTTP Header Constructed
			if (empty($sHeader) === false)
			{
				/* ---------- Construct HTTP Message Start ---------- */
				$sMsg = "";
				// Define Message Header
				$sMsg .= trim($sHeader);
				// Headers End, Body Start
				$sMsg .= self::CRLF . self::CRLF;
				// Define Message Body
				$sMsg .= $b;
				/* ---------- Construct HTTP Message End ---------- */

				// Send Message in chunks of 32kb
				$byte = 0;
				$i = 1;
				while ($byte < strlen($sMsg) && $byte >= 0)
				{
					// Calculate length of the chunk
					if (strlen($sMsg) - $byte > 8*1024) { $iLength = 8*1024; }
					else { $iLength = strlen($sMsg) - $byte; }

					$res = fputs($this->getConnInfo()->getConnection(), substr($sMsg, $byte, $iLength) );
					/*
					 * An error occurred when sending the data to the remote server.
					 * Despite what the PHP manual says, fputs / fwrite will only return false due to invalid arguments,
					 * any other error, such as a broken pipe or closed connection, will will result in a return value of
					 * less than strlen(substr($sMsg, $byte, $iLength) ), in most cases 0.
					 */
					if ($res === false || $res == 0) { $byte = -1; }
					else
					{
						$byte += $res;
						usleep(10);
					}
				}

				// Success: Message(s) sent to HTTP Server
				if ($byte == strlen($sMsg) )
				{
					$this->setState(self::iHTTP_SENT);

					//Wait before attempting to retrieve host reply
					usleep(10);
					$i = 0;
					$sReply = "";
					$length = -1;
					// Get return status from external application
					while (feof($this->getConnInfo()->getConnection() ) === false && $i < $this->getConnInfo()->getTimeout() && (strlen($sReply) < $length || $length < 0) )
					{
						$s = @fgets($this->getConnInfo()->getConnection(), 1024);
						// No Reply, wait 1 second
						if (empty($s) === true)
						{
							$i++;
							sleep(1);
						}
						// Headers received
						elseif ($s == self::CRLF && $length == -1)
						{
							$this->_obj_ConnInfo->setReplyHeader(trim($sReply) );
							$aHeaders = explode(self::CRLF, trim($sReply) );
							foreach ($aHeaders as $header)
							{
								$pos = strlen("content-length");
								if (strtolower(substr($header, 0, $pos) ) == "content-length") { $length = trim(substr($header, $pos + 1) ); }
							}
							if ($length == -1) { $length = -2; }	// No Content-Length header
							$sReply = "";
							$i = 0;	// Reset timeout
						}
						else
						{
							$sReply .= $s;
							$i = 0;	// Reset timeout
						}
					}
					// Return Status successfully retrieved from external application
					if ($length >= 0 || strlen($this->_obj_ConnInfo->getReplyHeader() ) > 0)
					{
						$this->setState(self::iHTTP_REPLY);

						$this->_obj_ConnInfo->setReplyBody(trim($sReply) );

						// Parse reply from external application
						$code = $this->parseReply($this->_obj_ConnInfo->getReplyHeader() );
						if ($code == -1)
						{
							trigger_error("Invalid HTTP Headers returned by URL: ". $this->getConnInfo()->toURL() ."\n". "Headers: ". $this->_obj_ConnInfo->getReplyHeader(), E_USER_WARNING);
						}
						// Server responded with HTTP Code: 401 Unauthorized
						elseif ($this->useAuth($code) === true)
						{
							$h = trim($h) . self::CRLF . $this->constAuth() . self::CRLF;
							$this->_obj_ConnInfo->setReturnCode($code);
							$this->disConnect();

							$this->setState(self::iHTTP_CONNINFO);
							$this->connect();
							$code = $this->send($h, $b);
						}
						$this->_obj_ConnInfo->setReturnCode($code);
					}
					// Error: Could not get return status from remote server
					else
					{
						$this->setState(self::iHTTP_ERROR);
						throw new HTTPSendException("Could not get return status from remote server. Host: ". $this->getConnInfo()->getHost() .", Port: ". $this->getConnInfo()->getPort(), 1022);
					}
				}
				// Error: Could not send Message to remote server
				else
				{
					$this->setState(self::iHTTP_ERROR);
					throw new HTTPSendException("Could not send Message to remote server. Host: ". $this->getConnInfo()->getHost() .", Port: ". $this->getConnInfo()->getPort(), 1021);
				}
			}
			// Error: Unable to construct Header for Message to send
			else
			{
				$this->setState(self::iHTTP_ERROR);
				throw new HTTPSendException("Unable to construct Header for Message to send", 1012);
			}
		}
		// Error: No connection has been established with remote server
		else
		{
			$this->setState(self::iHTTP_ERROR);
			throw new HTTPSendException("No connection has been established with remote server. Host: ". $this->getConnInfo()->getHost() .", Port: ". $this->getConnInfo()->getPort(), 1011);
		}

		return $code;
	}

	/**
	 * Parses and constructs the HTTP Header and HTTP Body using the templates found in the Connection Info struct
	 * The method will encode the link, title and message parts of the message according to the content type
	 * Please note: This method uses the Template class to parse the templates
	 *
	 * @see Template::create()
	 * @see HTTPClient::$_obj_ConnInfo
	 * @see HTTPConnInfo
	 *
	 * @param 	string $h 	Path to file with HTTP Header template file or actual HTTP Template
	 * @param	integer $l	Total length of the body
	 * @return 	string		Constructed headers on success, empty string on error
	 */
	private function constHeader($h, $l)
	{
		// Initialise array of Header Data
		$aHeaderInfo = array();

		/* ---------- Connection Info Start ---------- */
		$aHeaderInfo["username"] = $this->getConnInfo()->getUsername();
		$aHeaderInfo["password"] = $this->getConnInfo()->getPassword();
		/* ---------- Connection Info End ---------- */

		/* ---------- HTTP Header Fields Start ---------- */
		$aHeaderInfo["host"] = $this->getConnInfo()->getHost();
		$aHeaderInfo["path"] = $this->getConnInfo()->getPath();
		$aHeaderInfo["method"] = $this->getConnInfo()->getMethod();
		if (empty($aHeaderInfo["method"]) === true)
		{
			if ($l == 0) { $aHeaderInfo["method"] = "GET"; }
			else { $aHeaderInfo["method"] = "POST"; }
		}
		$aHeaderInfo["contenttype"] = $this->getConnInfo()->getContentType();
        //$protocol =  stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https' : 'http';
        $protocol = isset($_SERVER["HTTPS"]) ? 'https' : 'http';
		// Construct referer
		if (isset($_SERVER['SCRIPT_NAME']) === true && isset($_SERVER['HTTP_HOST']) === true) { $aHeaderInfo["referer"] = $protocol."://". $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']; }
		elseif (isset($_SERVER['HTTP_HOST']) === true) { $aHeaderInfo["referer"] = $protocol."://". $_SERVER['HTTP_HOST'] . end(explode(" ", $_SERVER['argv'][0]) ); }
		elseif (isset($_SERVER['SCRIPT_NAME']) === true) { $aHeaderInfo["referer"] = $_SERVER['SCRIPT_NAME']; }
		else { $aHeaderInfo["referer"] = end(explode(" ", $_SERVER['argv'][0]) ); }
		// Calculate content length after parsing
		$aHeaderInfo["contentlength"] = $l;
		/* ---------- HTTP Header Fields End ---------- */

		/* ---------- Construct Header Start ---------- */
		// Header template provided
		if (empty($h) === false)
		{
			// Initialize template object
			$this->getTemplate()->setTemplate($h);
			// Parse template
			$sHeader = $this->getTemplate()->create($aHeaderInfo);
			// Save constructed header
			$this->getConnInfo()->setRequestHeader($sHeader);
		}
		/* ---------- Construct Header End ---------- */

		return $sHeader;
	}

	/**
	 * Parses the HTTP Header document that an external application will reply with.
	 *
	 * @param 	string $h	HTTP Header document
	 * @return 	integer		HTTP Status code, -1 on error
	 *
	 * @throws 	HTTPSendException
	 */
	private function parseReply($h)
	{
		/* ---------- Error Handling Start ---------- */
		if (empty($h) === true) { throw new HTTPSendException("Undefined HTTP Header to parse", 1003); }
		/* ---------- Error Handling End ---------- */
		$code = -1;

		// Split each header line
		$aHeaders = explode("\r\n", $h);
		if (count($aHeaders) <= 1) { $aHeaders = explode("\n", $h); }
		if (count($aHeaders) <= 1) { $aHeaders = explode("\r", $h); }

		// Loop through all headers
		for ($i=0; $i<count($aHeaders); $i++)
		{
			// Current header holds the HTTP Return Code
			if (substr_count(strtoupper($aHeaders[$i]), "HTTP/1.") > 0)
			{
				// Split header into protocol and return code
				list(, $code) = explode(" ", $aHeaders[$i]);
				// Break loop
				$i = count($aHeaders);
			}
		}

		return $code;
	}

	/**
	 * Constructs the correct authentication header for accessing the remote server which requires
	 * the of an HTTP Authentication Scheme.
	 * Please refer to the HTTP 1.0 specifications for details.
	 *
	 * The method will return the correct HTTP Header for authorization in the format:
	 * 	authorization: {METHOD} {CREDENTIALS}
	 * {METHOD} can be one of:
	 * 	- Basic Authentication Scheme
	 * 	- Digest Access Authentication Scheme
	 * The value for {CREDENTIALS} is dependant upong the authentication method used:
	 * 	- Basic Authentication Scheme, Base64 encoding of {USERNAME}:{PASSWORD}
	 * 	- Digest Access Authentication Scheme, md5 hash of
	 *
	 * @link 	http://www.w3.org/Protocols/HTTP/1.0/draft-ietf-http-spec.html#AA
	 *
	 * @todo 	Add support for Digest Access Authentication Scheme
	 *
	 * @return 	string
	 */
	private function constAuth()
	{
		$s = "";
		$aTemp = explode("\n", $this->getReplyHeader() );

		for ($i=0; $i<count($aTemp); $i++)
		{
			// Split into header name / value, ignoring warnings
			@list($header, $value) = explode(":", $aTemp[$i]);
			if (strtolower(trim($header) ) == "www-authenticate")
			{
				list($method, $realm) = explode(" ", trim($value) );
				$realm = @end(explode("=", $realm) );
				$realm = str_replace('"', '', $realm);
				$s = "authorization: ".$method ." ". base64_encode($this->_obj_ConnInfo->getUsername() .":". $this->_obj_ConnInfo->getPassword() );
				$i = count($aTemp);
			}
		}

		return $s;
	}

	/**
	 * Detects whether the remote server requries the use of an HTTP Authentication Scheme:
	 * 	- Basic Authentication Scheme
	 * 	- Digest Access Authentication Scheme
	 *
	 * @param 	integer $code 	HTTP Response code
	 * @return 	boolean
	 */
	private function useAuth($code)
	{
		if ($code == 401 && $this->_obj_ConnInfo->getReturnCode() != 401 && $this->_obj_ConnInfo->getUsername() != "" && $this->_obj_ConnInfo->getPassword() != "")
		{
			return true;
		}
		else { return false; }
	}
	/* ---------- HTTP Methods End ---------- */
}
