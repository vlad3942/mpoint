<?php
/**
 * This file serves to illustrate the configuration directives necesarry for communicating with GoMobile.
 * All information related to establishing the connection is held in the $aGM_CONN_INFO array
 *
 * @author Jonatan Evald Buus
 * @package GoMobile
 * @subpackage Configuration
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com Cellpoint Mobile
 *
 */

/**
 * Define path to the Log files.
 * This directory MUST be writeable for the application user / webserver user
 *
 */
define("sLOG_PATH",sSYSTEM_PATH ."/log/");

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
$aGM_CONN_INFO["username"] = "CPMDemo";
$aGM_CONN_INFO["password"] = "DEMOisNO_2";
$aGM_CONN_INFO["logpath"] = sLOG_PATH;
/**
 * 1 - Write log entry to file
 * 2 - Output log entry to screen
 * 3 - Write log entry to file and output to screen
 *
 */
$aGM_CONN_INFO["mode"] = 1;
?>