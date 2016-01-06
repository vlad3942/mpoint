<?php
/**
 * Set error types that are to be reported by the error handler
 * Both errors and warnings are reported, notices however are not
 */
error_reporting(E_ERROR | E_PARSE | E_WARNING | E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE);

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
define("iOUTPUT_METHOD", 1);
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
/*
// Emirates Lab
$aDB_CONN_INFO["mpoint"]["host"] = "localhost";
$aDB_CONN_INFO["mpoint"]["port"] = 6516;
$aDB_CONN_INFO["mpoint"]["path"] = "mpontod";
$aDB_CONN_INFO["mpoint"]["username"] = "mpoint_user";
$aDB_CONN_INFO["mpoint"]["password"] = "mpoint_user";
$aDB_CONN_INFO["mpoint"]["class"] = "Oracle";
/*
// Solar
$aDB_CONN_INFO["mpoint"]["host"] = "192.168.1.61";
$aDB_CONN_INFO["mpoint"]["port"] = 1521;
$aDB_CONN_INFO["mpoint"]["path"] = "xe";
$aDB_CONN_INFO["mpoint"]["username"] = "mpoint";
$aDB_CONN_INFO["mpoint"]["password"] = "mpoint";
$aDB_CONN_INFO["mpoint"]["class"] = "Oracle";
*/
$aDB_CONN_INFO["mpoint"]["host"] = "localhost";
$aDB_CONN_INFO["mpoint"]["port"] = 5432;
$aDB_CONN_INFO["mpoint"]["path"] = "mpoint";
$aDB_CONN_INFO["mpoint"]["username"] = "mpoint";
$aDB_CONN_INFO["mpoint"]["password"] = "hspzr735abl";
$aDB_CONN_INFO["mpoint"]["class"] = "PostGreSQL";
$aDB_CONN_INFO["mpoint"]["timeout"] = 10;
$aDB_CONN_INFO["mpoint"]["charset"] = "UTF8";
$aDB_CONN_INFO["mpoint"]["connmode"] = "normal";
$aDB_CONN_INFO["mpoint"]["errorpath"] = sLOG_PATH ."db_error_". date("Y-m-d") .".log";
$aDB_CONN_INFO["mpoint"]["errorhandling"] = 3;
$aDB_CONN_INFO["mpoint"]["exectime"] = 0.3;
$aDB_CONN_INFO["mpoint"]["execpath"] = sLOG_PATH ."db_exectime_". date("Y-m-d") .".log";
$aDB_CONN_INFO["mpoint"]["keycase"] = CASE_UPPER;
$aDB_CONN_INFO["mpoint"]["debuglevel"] = 2;
$aDB_CONN_INFO["mpoint"]["method"] = 1;

/**
 * Database settings for Session database
 */
$aDB_CONN_INFO["session"]["host"] = "localhost";
$aDB_CONN_INFO["session"]["port"] = 5432;
$aDB_CONN_INFO["session"]["path"] = "session";
$aDB_CONN_INFO["session"]["username"] = "session";
$aDB_CONN_INFO["session"]["password"] = "2a2ac8447e";
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
$aHTTP_CONN_INFO["mesb"]["protocol"] = "http";
//$aHTTP_CONN_INFO["mesb"]["host"] = "213.173.252.92";
$aHTTP_CONN_INFO["mesb"]["host"] = "localhost";
$aHTTP_CONN_INFO["mesb"]["port"] = 10080;
$aHTTP_CONN_INFO["mesb"]["timeout"] = 120;
$aHTTP_CONN_INFO["mesb"]["path"] = "/";
$aHTTP_CONN_INFO["mesb"]["method"] = "POST";
$aHTTP_CONN_INFO["mesb"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["mesb"]["username"] = "";
$aHTTP_CONN_INFO["mesb"]["password"] = "";

/**
 * Connection info for sending error reports to a remote host
 */
$aHTTP_CONN_INFO["iemendo"]["protocol"] = "http";
$aHTTP_CONN_INFO["iemendo"]["host"] = "iemendo.cellpointmobile.com";
$aHTTP_CONN_INFO["iemendo"]["port"] = 80;
$aHTTP_CONN_INFO["iemendo"]["timeout"] = 20;
$aHTTP_CONN_INFO["iemendo"]["path"] = "/api/receive_report.php";
$aHTTP_CONN_INFO["iemendo"]["method"] = "POST";
$aHTTP_CONN_INFO["iemendo"]["contenttype"] = "text/xml";
//$aHTTP_CONN_INFO["iemendo"]["username"] = "";
//$aHTTP_CONN_INFO["iemendo"]["password"] = "";

/**
 * Connection info for identifying a mobile device by sending its UA Profile information to iEmendo
 */
$aUA_CONN_INFO["protocol"] = "http";
$aUA_CONN_INFO["host"] = "iemendo.cellpointmobile.com";
$aUA_CONN_INFO["port"] = 80;
$aUA_CONN_INFO["timeout"] = 20;
$aUA_CONN_INFO["path"] = "/api/uaprofile.php";
$aUA_CONN_INFO["method"] = "POST";
$aUA_CONN_INFO["contenttype"] = "text/xml";

//$aUA_CONN_INFO["username"] = "";
//$aUA_CONN_INFO["password"] = "";

/**
 * HTTP Connection Information for using Interflora's Lookup Service in Denmark
 */
$aHTTP_CONN_INFO[100]["protocol"] = "http";
$aHTTP_CONN_INFO[100]["host"] = "www.interflora.dk";
$aHTTP_CONN_INFO[100]["port"] = 80;
$aHTTP_CONN_INFO[100]["timeout"] = 20;
$aHTTP_CONN_INFO[100]["path"] = "/rpc/tdc_lookup.php";
$aHTTP_CONN_INFO[100]["method"] = "GET";
$aHTTP_CONN_INFO[100]["contenttype"] = "application/www-url-form-encoded";
//$aHTTP_CONN_INFO[100]["username"] = "";
//$aHTTP_CONN_INFO[100]["password"] = "";


/**
 * Connection info for connecting to DIBS
 */
$aHTTP_CONN_INFO["dibs"]["protocol"] = "https";
$aHTTP_CONN_INFO["dibs"]["host"] = "payment.architrade.com";
$aHTTP_CONN_INFO["dibs"]["port"] = 443;
$aHTTP_CONN_INFO["dibs"]["timeout"] = 120;
$aHTTP_CONN_INFO["dibs"]["path"] = "/shoppages/{account}/payment.pml";
$aHTTP_CONN_INFO["dibs"]["method"] = "POST";
$aHTTP_CONN_INFO["dibs"]["contenttype"] = "application/x-www-form-urlencoded";
$aHTTP_CONN_INFO["dibs"]["paths"]["auth"] = "/cgi-ssl/ticket_auth.cgi";
$aHTTP_CONN_INFO["dibs"]["paths"]["capture"] = "/cgi-bin/capture.cgi";
$aHTTP_CONN_INFO["dibs"]["paths"]["cancel"] = "/cgi-adm/cancel.cgi";
$aHTTP_CONN_INFO["dibs"]["paths"]["refund"] = "/cgi-adm/refund.cgi";
$aHTTP_CONN_INFO["dibs"]["paths"]["status"] = "/transstatus.pml";


/**
 * Connection info for connecting to WorldPay
 */
$aHTTP_CONN_INFO["worldpay"]["protocol"] = "https";
$aHTTP_CONN_INFO["worldpay"]["host"] = "secure.wp3.rbsworldpay.com";
$aHTTP_CONN_INFO["worldpay"]["port"] = 443;
$aHTTP_CONN_INFO["worldpay"]["timeout"] = 120;
$aHTTP_CONN_INFO["worldpay"]["path"] = "/jsp/merchant/xml/paymentService.jsp";
$aHTTP_CONN_INFO["worldpay"]["method"] = "POST";
$aHTTP_CONN_INFO["worldpay"]["contenttype"] = "text/xml";
//$aHTTP_CONN_INFO["worldpay"]["username"] = "";	// Set from the Client Configuration 
$aHTTP_CONN_INFO["worldpay"]["password"] = "hspzr735abl";

/**
 * Connection info for connecting to PayEx
 */
$aHTTP_CONN_INFO["payex"]["protocol"] = "https";
$aHTTP_CONN_INFO["payex"]["host"] = "external.payex.com";
$aHTTP_CONN_INFO["payex"]["port"] = 443;
$aHTTP_CONN_INFO["payex"]["timeout"] = 120;
$aHTTP_CONN_INFO["payex"]["path"] = "/PxOrder/Pxorder.asmx?WSDL";
$aHTTP_CONN_INFO["payex"]["method"] = "POST";
$aHTTP_CONN_INFO["payex"]["contenttype"] = "text/xml";
//$aHTTP_CONN_INFO["payex"]["username"] = "";	// Set from the Client Configuration 
$aHTTP_CONN_INFO["payex"]["password"] = "b9ppZDPbRcJNEgHM57BV";

/**
 * Connection info for connecting to CPG
 */
$aHTTP_CONN_INFO["cpg"]["protocol"] = "https";
$aHTTP_CONN_INFO["cpg"]["host"] = "pgstaging.emirates.com";
$aHTTP_CONN_INFO["cpg"]["port"] = 443;
$aHTTP_CONN_INFO["cpg"]["timeout"] = 120;
$aHTTP_CONN_INFO["cpg"]["path"] = "/cpg/Order.jsp";
$aHTTP_CONN_INFO["cpg"]["method"] = "POST";
$aHTTP_CONN_INFO["cpg"]["contenttype"] = "text/xml";
//$aHTTP_CONN_INFO["emirates"]["username"] = "";	// Set from the Client Configuration
//$aHTTP_CONN_INFO["emirates"]["password"] = "";

/**
 * Connection info for connecting to Authorize.Net
 */
$aHTTP_CONN_INFO["authorize.net"]["protocol"] = "https";
$aHTTP_CONN_INFO["authorize.net"]["host"] = "secure.authorize.net";
$aHTTP_CONN_INFO["authorize.net"]["port"] = 443;
$aHTTP_CONN_INFO["authorize.net"]["timeout"] = 120;
$aHTTP_CONN_INFO["authorize.net"]["path"] = "/gateway/transact.dll";
$aHTTP_CONN_INFO["authorize.net"]["method"] = "POST";
$aHTTP_CONN_INFO["authorize.net"]["contenttype"] = "application/x-www-form-urlencoded";
//$aHTTP_CONN_INFO["authorize.net"]["username"] = "";	// Set from the Client Configuration 
//$aHTTP_CONN_INFO["authorize.net"]["password"] = "";	// Set from the Client Configuration


/**
 * Connection info for connecting to WannaFind
 */
$aHTTP_CONN_INFO["wannafind"]["protocol"] = "https";
$aHTTP_CONN_INFO["wannafind"]["host"] = "betaling.wannafind.dk";
$aHTTP_CONN_INFO["wannafind"]["port"] = 443;
$aHTTP_CONN_INFO["wannafind"]["timeout"] = 120;
$aHTTP_CONN_INFO["wannafind"]["path"] = "/auth.php";
$aHTTP_CONN_INFO["wannafind"]["method"] = "POST";
$aHTTP_CONN_INFO["wannafind"]["contenttype"] = "application/x-www-form-urlencoded";
//$aHTTP_CONN_INFO["wannafind"]["username"] = "";	// Set from the Client Configuration 
//$aHTTP_CONN_INFO["wannafind"]["password"] = "";	// Set from the Client Configuration 

/**
 * Connection info for connecting to NetAxept
 */
$aHTTP_CONN_INFO["netaxept"]["protocol"] = "https";
$aHTTP_CONN_INFO["netaxept"]["host"] = "epayment-test.bbs.no";
$aHTTP_CONN_INFO["netaxept"]["port"] = 443;
$aHTTP_CONN_INFO["netaxept"]["timeout"] = 120;
$aHTTP_CONN_INFO["netaxept"]["path"] = "/netaxept.svc?wsdl";
$aHTTP_CONN_INFO["netaxept"]["method"] = "POST";
$aHTTP_CONN_INFO["netaxept"]["contenttype"] = "application/x-www-form-urlencoded";
//$aHTTP_CONN_INFO["netaxept"]["username"] = "";	// Set from the Client Configuration 
//$aHTTP_CONN_INFO["netaxept"]["password"] = "";	// Set from the Client Configuration 

/**
 * Connection info for connecting to MobilePay
 */
$aHTTP_CONN_INFO["mobilepay"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["mobilepay"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["mobilepay"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["mobilepay"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["mobilepay"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["mobilepay"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["mobilepay"]["contenttype"] = "text/xml";
//$aHTTP_CONN_INFO["mobilepay"]["paths"]["auth"] = "/cgi-ssl/ticket_auth.cgi";
$aHTTP_CONN_INFO["mobilepay"]["paths"]["capture"] = "/mpoint/danskebank/capture";
$aHTTP_CONN_INFO["mobilepay"]["paths"]["status"] = "/mpoint/danskebank/status";
//$aHTTP_CONN_INFO["mobilepay"]["paths"]["cancel"] = "/cgi-adm/cancel.cgi";
$aHTTP_CONN_INFO["mobilepay"]["paths"]["refund"] = "/mpoint/danskebank/refund";
//$aHTTP_CONN_INFO["mobilepay"]["paths"]["status"] = "/transstatus.pml";

/**
 * Connection info for connecting to Adyen
 */
$aHTTP_CONN_INFO["adyen"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["adyen"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["adyen"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["adyen"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["adyen"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["adyen"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["adyen"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["adyen"]["paths"]["initialize"] = "/mpoint/adyen/initialize";
$aHTTP_CONN_INFO["adyen"]["paths"]["auth"] = "/mpoint/adyen/authorize-payment";
$aHTTP_CONN_INFO["adyen"]["paths"]["capture"] = "/mpoint/adyen/capture";
//$aHTTP_CONN_INFO["adyen"]["paths"]["status"] = "/mpoint/adyen/status";
$aHTTP_CONN_INFO["adyen"]["paths"]["cancel"] = "/mpoint/adyen/cancel";
$aHTTP_CONN_INFO["adyen"]["paths"]["refund"] = "/mpoint/adyen/refund";

/**
 * Connection info for DSB PSP
 */
$aHTTP_CONN_INFO["dsb"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["dsb"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["dsb"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["dsb"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["dsb"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["dsb"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["dsb"]["paths"]["redeem"] = "/mpoint/dsb/redeem";
$aHTTP_CONN_INFO["dsb"]["paths"]["refund"] = "/mpoint/dsb/refund";
$aHTTP_CONN_INFO["dsb"]["paths"]["callback"] = "/mpoint/dsb/callback";
$aHTTP_CONN_INFO["dsb"]["paths"]["get-extenal-payment-methods"] = "/mpoint/dsb/get-extenal-payment-methods";
$aHTTP_CONN_INFO["dsb"]["paths"]["cancel"] = "/mpoint/dsb/cancel";

/**
 * Connection info for connecting to VISA Checkout
 */
$aHTTP_CONN_INFO["visa-checkout"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["visa-checkout"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["visa-checkout"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["visa-checkout"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["visa-checkout"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["visa-checkout"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["visa-checkout"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["visa-checkout"]["paths"]["initialize"] = "/mpoint/visa-checkout/initialize";
$aHTTP_CONN_INFO["visa-checkout"]["paths"]["get-payment-data"] = "/mpoint/visa-checkout/get-payment-data";
$aHTTP_CONN_INFO["visa-checkout"]["paths"]["callback"] = "/mpoint/visa-checkout/callback";

/**
 * Connection info for connecting to AMEX Express Checkout
 */
$aHTTP_CONN_INFO["amex-express-checkout"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["amex-express-checkout"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["amex-express-checkout"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["amex-express-checkout"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["amex-express-checkout"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["amex-express-checkout"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["amex-express-checkout"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["amex-express-checkout"]["paths"]["initialize"] = "/mpoint/amex-express-checkout/initialize";
$aHTTP_CONN_INFO["amex-express-checkout"]["paths"]["get-payment-data"] = "/mpoint/amex-express-checkout/get-payment-data";
$aHTTP_CONN_INFO["amex-express-checkout"]["paths"]["callback"] = "/mpoint/amex-express-checkout/callback";


/**
 * Connection info for connecting to Apple Pay
 */
$aHTTP_CONN_INFO["apple-pay"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["apple-pay"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["apple-pay"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["apple-pay"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["apple-pay"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["apple-pay"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["apple-pay"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["apple-pay"]["paths"]["initialize"] = "/mpoint/apple-pay/initialize";
$aHTTP_CONN_INFO["apple-pay"]["paths"]["get-payment-data"] = "/mpoint/apple-pay/get-payment-data";

/**
 * Connection info for connecting to MasterPass
 */
$aHTTP_CONN_INFO["masterpass"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["masterpass"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["masterpass"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["masterpass"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["masterpass"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["masterpass"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["masterpass"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["masterpass"]["paths"]["initialize"] = "/mpoint/masterpass/initialize";
$aHTTP_CONN_INFO["masterpass"]["paths"]["get-payment-data"] = "/mpoint/masterpass/get-payment-data";
$aHTTP_CONN_INFO["masterpass"]["paths"]["callback"] = "/mpoint/masterpass/callback";

/**
 * Connection info for connecting to Data Cash
 */
$aHTTP_CONN_INFO["data-cash"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["data-cash"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["data-cash"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["data-cash"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["data-cash"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["data-cash"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["data-cash"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["data-cash"]["paths"]["initialize"] = "/mpoint/data-cash/initialize";
$aHTTP_CONN_INFO["data-cash"]["paths"]["auth"] = "/mpoint/data-cash/authorize-payment";
$aHTTP_CONN_INFO["data-cash"]["paths"]["capture"] = "/mpoint/data-cash/capture";
$aHTTP_CONN_INFO["data-cash"]["paths"]["status"] = "/mpoint/data-cash/status";
$aHTTP_CONN_INFO["data-cash"]["paths"]["cancel"] = "/mpoint/data-cash/cancel";
$aHTTP_CONN_INFO["data-cash"]["paths"]["refund"] = "/mpoint/data-cash/refund";
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

$aCPM_CONN_INFO["protocol"] = "http";
$aCPM_CONN_INFO["host"] = "mpoint.localhost";
$aCPM_CONN_INFO["port"] = 80;
$aCPM_CONN_INFO["timeout"] = 20;
$aCPM_CONN_INFO["path"] = "/callback/cpm.php";
$aCPM_CONN_INFO["method"] = "POST";
$aCPM_CONN_INFO["contenttype"] = "application/x-www-form-urlencoded";
//$aCPM_CONN_INFO["username"] = "";
//$aCPM_CONN_INFO["password"] = "";

/**
 * Template for website design
 */
define("sTEMPLATE", "default");

/**
 * Language for GUI
 */
define("sDEFAULT_LANGUAGE", "gb");

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
define("sDEFAULT_UA_PROFILE", General::getBrowserType() == "web" ? "http://iemendo.cellpointmobile.com/data/mpoint-ajax.xml" : "http://wap.sonyericsson.com/UAprof/K790iR201.xml");

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
define("iCARD_LOGO_SCALE", 20);
/**
 * Determines what size the mPoint Logo is scaled to.
 * The constant represents the percentage of the screen width / height that the logo can cover.
 *
 */
define("iMPOINT_LOGO_SCALE", 30);

/**
 * URL for the Default Product Logo to display on the Order Overview if no other URL has been provided.
 *
 */
define("sDEFAULT_PRODUCT_LOGO", "http://". $_SERVER['HTTP_HOST'] ."/img/default_product_logo.gif");

/**
 * List of Words used to Accept an SMS Purchase
 * 
 * @var array
 */
$aACCEPT_WORDS = array("JA", "OK", "YES", "GO", "YUP", "YEAH", "Y");
/**
 * List of Words used to Reject an SMS Purchase
 * 
 * @var array
 */
$aREJECT_WORDS = array("NEJ", "NO", "NOPE", "N", "CANCEL", "QUIT", "END");

/**
 * Absolute Path to XML Schemas defining mRetail's different Protocols
 *
 */
define("sPROTOCOL_XSD_PATH", $_SERVER['DOCUMENT_ROOT'] ."/protocols/");
/**
 * Constant For Databse Schemas PostFix Used for Emirates
 *
 */
//define("sSCHEMA_POSTFIX","_ownr");
define("sSCHEMA_POSTFIX","");
/**
 *	Number of days before a log entry in Log.Message_tbl and Log.Auditlog_Tbl 
 *	will be expire. When set to "0" No logs will be purged. 
 */
define("iPURGED_DAYS", 30);
?>