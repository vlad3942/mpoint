<?php
/**
 * Set error types that are to be reported by the error handler
 * Both errors and warnings are reported, notices however are not
 */
error_reporting(E_ERROR | E_PARSE | E_WARNING | E_NOTICE | E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE);

/**
 * Path to Log Files directory
 */
define("sLOG_PATH", sSYSTEM_PATH ."/log/");
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
define("iOUTPUT_METHOD", 2);
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
 * Database settings for Application's database
 */
$aDB_CONN_INFO["app"]["host"] = "localhost";
$aDB_CONN_INFO["app"]["port"] = 5432;
$aDB_CONN_INFO["app"]["path"] = "app";
$aDB_CONN_INFO["app"]["username"] = "app";
$aDB_CONN_INFO["app"]["password"] = "Jona";
$aDB_CONN_INFO["app"]["timeout"] = 10;
$aDB_CONN_INFO["app"]["charset"] = "ISO8859_1";
$aDB_CONN_INFO["app"]["class"] = "PostGreSQL";
$aDB_CONN_INFO["app"]["connmode"] = "normal";
$aDB_CONN_INFO["app"]["errorpath"] = sLOG_PATH ."db_error_". date("Y-m-d") .".log";
$aDB_CONN_INFO["app"]["errorhandling"] = 3;
$aDB_CONN_INFO["app"]["exectime"] = 0.3;
$aDB_CONN_INFO["app"]["execpath"] = sLOG_PATH ."db_exectime_". date("Y-m-d") .".log";
$aDB_CONN_INFO["app"]["keycase"] = CASE_UPPER;
$aDB_CONN_INFO["app"]["debuglevel"] = 2;
$aDB_CONN_INFO["app"]["method"] = 1;

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
 * Template for website design
 */
define("sTEMPLATE", "default");

/**
 * Language for GUI
 */
define("sLANG", "uk");

/**
 * Define min & max lenght for authentication info such as username/password 
 */
define("iAUTH_MIN_LENGTH", 4);
define("iAUTH_MAX_LENGTH", 50);
?>