<?php
/**
 * This file serves to illustrate how a fake MO-SMS message can be sent to GoMobile. This will in turn cause
 * GoMobile to route the message back to the client using the message's channel and keyword to determine the 
 * correct destination. This approach is intended to allow a client an easy mean of testing their implementation.
 * GoMobile's Client API uses the underlying HTTP Client API for handling all the HTTP Communication to
 * the GoMobile server.
 * The HTTP Client API in return uses the Template class for parsing its header template and perform
 * replacement of the {TEXT_TAGS} found.
 *
 * Please refer to GoMobile Overview.pdf for details about the values the following variables can take:
 * 	- $iType
 * 	- $iCountry
 * 	- $iOperator
 * 	- $sChannel
 * 	- $sKeyword
 * 	- $iPrice
 * 	- $sSender
 * 	- $sBody
 * Any status codes returned by GoMobile if the message sending fails can also be found in this document.
 * 
 * Please refer to GoMobile Client API for PHP for details on how to use the GoMobile client classes:
 * 	- GoMobileMessage
 * 	- GoMobileConnInfo
 * 	- GoMobileClient
 * 
 * @author Jonatan Evald Buus
 * @package GoMobile
 * @subpackage Examples
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com Cellpoint Mobile
 * 
 */

/**
 * Define path to the directory which holds the different API class files
 *
 */
define("sAPI_CLASS_PATH", "../../../php5api/classes");

// Require API for parsing HTTP Header Template with text tags: {TEXT_TAG}
require_once(sAPI_CLASS_PATH ."/template.php");
// Require API for handling the connection to a remote webserver using HTTP
require_once(sAPI_CLASS_PATH ."/http_client.php");
// Require the PHP API for handling the connection to GoMobile
require_once(sAPI_CLASS_PATH ."/gomobile.php");

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
$aGM_CONN_INFO["host"] = "mpoint.localhost";
$aGM_CONN_INFO["port"] = 80;
$aGM_CONN_INFO["timeout"] = 20;	// In seconds
$aGM_CONN_INFO["path"] = "/buy/sms.php";
$aGM_CONN_INFO["method"] = "POST";
$aGM_CONN_INFO["contenttype"] = "text/xml";
$aGM_CONN_INFO["username"] = "";		// Set from the Client Configuration
$aGM_CONN_INFO["password"] = "";		// Set from the Client Configuration
$aGM_CONN_INFO["logpath"] = "../../log/";
/**
 * 1 - Write log entry to file
 * 2 - Output log entry to screen
 * 3 - Write log entry to file and output to screen
 * 
 */
$aGM_CONN_INFO["mode"] = 1;


// Instantiate object for holding the necessary information for connecting to GoMobile
$obj_ConnInfo = GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO);
// Instantiate client object for communicating with GoMobile
$obj_GoMobile = new GoMobileClient($obj_ConnInfo);

/* ========== Create fake MO-SMS Start ========== */
$iType = 1;					// MO-SMS
$iCountry = 20;				// USA
$iOperator = 20001;			// Bulk Operator for the US
$sChannel = "20100";		// Internal channel for testing
$sKeyword = "CPT";
$iPrice = 0;				// Non-Premium SMS
$sSender = "3053315242";	// MSISDN of the Sender, must be a string as PHP only supports 32 bit integers
$sBody = "Fake MO-SMS generated through GoMobile";

// Instantiate Message Object for holding the message data which will be sent to GoMobile
$obj_MsgInfo = GoMobileMessage::produceMessage($iType, $iCountry, $iOperator, $sChannel, $sKeyword, $iPrice, $sSender, $sBody);
$obj_MsgInfo->enableConcatenation();
/* ========== Create fake MO-SMS End ========== */

// Send fake MO-SMS to GoMobile
if ($obj_GoMobile->send($obj_MsgInfo) == 200)
{
	echo "Message successfully sent with ID: ". $obj_MsgInfo->getGoMobileID();
}
// Error
else
{
	echo "Message sending failed with return codes: ". $obj_MsgInfo->getReturnCodes();
}
echo "<pre>";
var_dump($obj_GoMobile);
?>