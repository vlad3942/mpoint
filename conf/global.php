<?php
/**
 * Set error types that are to be reported by the error handler
 * Both errors and warnings are reported, notices however are not
 */
error_reporting(E_ERROR | E_PARSE | E_WARNING | E_NOTICE | E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE);

/**
 * Path to Log Files directory
 */
define("sLOG_PATH", "/Users/Jona/Development/mPoint/log/");
/**
 * Output method for the error handler:
 *	0 - Store Internally
 *	1 - Output to file
 *	2 - Output to screen
 *	3 - Output to file and screen
 *	4 - Send to remote server
 *	5 - Output to file and send remote server
 *	6 - Output to screen and send remote server
 *	7 - Output to file & screen and send remote server
 */
define("iOUTPUT_METHOD", 3);
/**
 * General debug level for the error handler
 *	0 - Output error
 *	1 - Add stack trace for exceptions and variable scope for errors to log message
 *	2 - Add custom trace using the {TRACE <DATA>} syntax
 */
define("iDEBUG_LEVEL", 2);
/**
 * Path to the application error log
 */
define("sERROR_LOG", sLOG_PATH ."app_error_". date("Y-m-d") .".log");

/**
 * Database settings for mPoint's database
 */
$aDB_CONN_INFO["mpoint"]["host"] = "localhost";
$aDB_CONN_INFO["mpoint"]["port"] = 5432;
$aDB_CONN_INFO["mpoint"]["path"] = "mpoint";
$aDB_CONN_INFO["mpoint"]["username"] = "mpoint";
$aDB_CONN_INFO["mpoint"]["password"] = "hspzr735abl";
$aDB_CONN_INFO["mpoint"]["timeout"] = 10;
$aDB_CONN_INFO["mpoint"]["charset"] = "ISO8859_1";
$aDB_CONN_INFO["mpoint"]["class"] = "PostGreSQL";
$aDB_CONN_INFO["mpoint"]["connmode"] = "normal";
$aDB_CONN_INFO["mpoint"]["errorpath"] = sLOG_PATH ."db_error_". date("Y-m-d") .".log";
$aDB_CONN_INFO["mpoint"]["errorhandling"] = 3;
$aDB_CONN_INFO["mpoint"]["exectime"] = 0.3;
$aDB_CONN_INFO["mpoint"]["execpath"] = sLOG_PATH ."db_exectime_". date("Y-m-d") .".log";
$aDB_CONN_INFO["mpoint"]["keycase"] = CASE_UPPER;
$aDB_CONN_INFO["mpoint"]["debuglevel"] = 2;
$aDB_CONN_INFO["mpoint"]["method"] = 3;

/**
 * Database settings for Session database
 */
$aDB_CONN_INFO["session"]["host"] = "localhost";
$aDB_CONN_INFO["session"]["port"] = 5432;
$aDB_CONN_INFO["session"]["path"] = "session";
$aDB_CONN_INFO["session"]["username"] = "session";
$aDB_CONN_INFO["session"]["password"] = "Jona";
$aDB_CONN_INFO["session"]["timeout"] = 10;
$aDB_CONN_INFO["session"]["charset"] = "ISO8859_1";
$aDB_CONN_INFO["session"]["class"] = "PostGreSQL";
$aDB_CONN_INFO["session"]["connmode"] = "normal";
$aDB_CONN_INFO["session"]["errorpath"] = sLOG_PATH ."db_error_". date("Y-m-d") .".log";
$aDB_CONN_INFO["session"]["errorhandling"] = 3;
$aDB_CONN_INFO["session"]["exectime"] = 0.3;
$aDB_CONN_INFO["session"]["execpath"] = sLOG_PATH ."db_exectime_". date("Y-m-d") .".log";
$aDB_CONN_INFO["session"]["keycase"] = CASE_UPPER;
$aDB_CONN_INFO["session"]["debuglevel"] = 2;
$aDB_CONN_INFO["session"]["method"] = 1;

/**
 * Connection info for sending error reports to a remote host
 */
$aHTTP_CONN_INFO["protocol"] = "http";
$aHTTP_CONN_INFO["host"] = "iemendo.cydev.biz";
$aHTTP_CONN_INFO["port"] = 80;
$aHTTP_CONN_INFO["timeout"] = 20;
$aHTTP_CONN_INFO["path"] = "/api/receive_report.php";
$aHTTP_CONN_INFO["method"] = "POST";
$aHTTP_CONN_INFO["contenttype"] = "text/xml";
//$aHTTP_CONN_INFO["username"] = "";
//$aHTTP_CONN_INFO["password"] = "";

/**
 * GoMobile Connection Info.
 * The array should contain the following indexes:
 * <code>
 * 
 * 	- protocol, the protocol used for communicating with GoMobile, should always be: http
 * 	- host, the host address for GoMobile, should always be: gomobile.cellpointmobile.com
 * 	- port, the port that requestes are sent to, should always be: 8000
 * 	- timeout, general timeout in seconds. The time is used in the following instances:
 * 		- When opening a new connection to GoMobile
 * 		- When retrieving the response from GoMobile
 * 	- path, the server side path where requestes are sent to, should always be: /
 * 	- method, the HTTP method used for the data transfer, should always be: POST
 * 	- contenttype, the HTTP Mimetype of the data, should always be: text/xml
 * 	- username, the username used for authenticating the client with GoMobile.
 * 	- password, the password used for generating the checksum which is sent to GoMobile for authentication
 * 	- logpath, the path to the directory where the API will write its log files.
 * 	- mode, the logging mode the API should use:
 * 		1 - Write log entry to file
 * 		2 - Output log entry to screen
 * 		3 - Write log entry to file and output to screen
 * 
 * </code>
 * 
 * @see 	GoMobileConnInfo::produceConnInfo()
 * 
 * @global 	array $aGM_CONN_INFO
 */
$aGM_CONN_INFO["protocol"] = "http";
$aGM_CONN_INFO["host"] = "gomobile.cellpointmobile.com";
$aGM_CONN_INFO["port"] = 8000;
$aGM_CONN_INFO["timeout"] = 20;	// In seconds
$aGM_CONN_INFO["path"] = "/";
$aGM_CONN_INFO["method"] = "POST";
$aGM_CONN_INFO["contenttype"] = "text/xml";
$aGM_CONN_INFO["username"] = "";		// Set from the Client Configuration
$aGM_CONN_INFO["password"] = "";		// Set from the Client Configuration
$aGM_CONN_INFO["logpath"] = sLOG_PATH;
/**
 * 1 - Write log entry to file
 * 2 - Output log entry to screen
 * 3 - Write log entry to file and output to screen
 * 
 */
$aGM_CONN_INFO["mode"] = 1;

/**
 * Template for website design
 */
define("sTEMPLATE", "default");

/**
 * Language for GUI
 */
define("sDEFAULT_LANGUAGE", "uk");

/**
 * Default mPoint Domain
 */
define("sDEFAULT_MPOINT_DOMAIN", "mpoint.cellpointmobile.com");
/**
 * Specific whitelied domain for Sprint
 */
define("sSPRINT_MPOINT_DOMAIN", "m62.sprintpcs.com");

/**
 * Default User Agent Profile URLs.
 * This URL is used if the Mobile Device doesn't supply a URL to its User Agent Profile
 * and is intended to provide an easy mean of defining af default device
 * The constant must be set to nothing for device detection to work on Verizon via mBlox as
 * mBlox doesn't supply a URL to the device's User Agent Profile but only a User Agent.
 */
define("sDEFAULT_UA_PROFILE", "");

/**
 * Determines what size Client Logos are scaled to.
 * The constant represents the percentage of the screen height that the logo can cover.
 *
 */
define("iCLIENT_LOGO_SCALE", 20);
/**
 * Determines what size Credit Card Logos are scaled to.
 * The constant represents the percentage of the screen width / height that the logo can cover.
 *
 */
define("iCARD_LOGO_SCALE", 15);
?>