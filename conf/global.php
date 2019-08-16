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
$aHTTP_CONN_INFO["iemendo"]["host"] = "iemendo.test.cellpointmobile.com";
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
$aUA_CONN_INFO["host"] = "iemendo.test.cellpointmobile.com";
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
$aHTTP_CONN_INFO["dibs"]["paths"]["auth-ticket"] = "/cgi-ssl/ticket_auth.cgi";
$aHTTP_CONN_INFO["dibs"]["paths"]["auth-new-card"] = "/cgi-ssl/auth.cgi";
$aHTTP_CONN_INFO["dibs"]["paths"]["capture"] = "/cgi-bin/capture.cgi";
$aHTTP_CONN_INFO["dibs"]["paths"]["cancel"] = "/cgi-adm/cancel.cgi";
$aHTTP_CONN_INFO["dibs"]["paths"]["refund"] = "/cgi-adm/refund.cgi";
$aHTTP_CONN_INFO["dibs"]["paths"]["status"] = "/transstatus.pml";


/**
 * Connection info for connecting to WorldPay
 */
$aHTTP_CONN_INFO["worldpay"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["worldpay"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["worldpay"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["worldpay"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["worldpay"]["path"] =""; // Set by calling class
$aHTTP_CONN_INFO["worldpay"]["method"] =  $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["worldpay"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["worldpay"]["paths"]["initialize"] = "/mpoint/worldpay/initialize";
$aHTTP_CONN_INFO["worldpay"]["paths"]["auth"] = "/mpoint/worldpay/authorize-payment";
$aHTTP_CONN_INFO["worldpay"]["paths"]["capture"] = "/mpoint/worldpay/capture";
$aHTTP_CONN_INFO["worldpay"]["paths"]["status"] = "/mpoint/worldpay/status";
$aHTTP_CONN_INFO["worldpay"]["paths"]["cancel"] = "/mpoint/worldpay/cancel";
$aHTTP_CONN_INFO["worldpay"]["paths"]["refund"] = "/mpoint/worldpay/refund";

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
$aHTTP_CONN_INFO["adyen"]["paths"]["void"] = "/mpoint/adyen/void";
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
$aHTTP_CONN_INFO["dsb"]["paths"]["get-external-payment-methods"] = "/mpoint/dsb/get-external-payment-methods";
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
 * Connection info for connecting to Data Cash
 */
$aHTTP_CONN_INFO["mada-mpgs"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["mada-mpgs"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["mada-mpgs"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["mada-mpgs"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["mada-mpgs"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["mada-mpgs"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["mada-mpgs"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["mada-mpgs"]["paths"]["initialize"] = "/mpoint/data-cash/initialize";
$aHTTP_CONN_INFO["mada-mpgs"]["paths"]["auth"] = "/mpoint/data-cash/authorize-payment";
$aHTTP_CONN_INFO["mada-mpgs"]["paths"]["capture"] = "/mpoint/data-cash/capture";
$aHTTP_CONN_INFO["mada-mpgs"]["paths"]["status"] = "/mpoint/data-cash/status";
$aHTTP_CONN_INFO["mada-mpgs"]["paths"]["cancel"] = "/mpoint/data-cash/cancel";
$aHTTP_CONN_INFO["mada-mpgs"]["paths"]["refund"] = "/mpoint/data-cash/refund";

/**
 * Connection info for connecting to Wire Card
 */
$aHTTP_CONN_INFO["wire-card"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["wire-card"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["wire-card"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["wire-card"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["wire-card"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["wire-card"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["wire-card"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["wire-card"]["paths"]["initialize"] = "/mpoint/wire-card/initialize";
$aHTTP_CONN_INFO["wire-card"]["paths"]["auth"] = "/mpoint/wire-card/authorize-payment";
$aHTTP_CONN_INFO["wire-card"]["paths"]["capture"] = "/mpoint/wire-card/capture";
$aHTTP_CONN_INFO["wire-card"]["paths"]["status"] = "/mpoint/wire-card/status";
$aHTTP_CONN_INFO["wire-card"]["paths"]["refund"] = "/mpoint/wire-card/refund";
$aHTTP_CONN_INFO["wire-card"]["paths"]["callback"] = "/mpoint/wire-card/callback";
$aHTTP_CONN_INFO["wire-card"]["paths"]["cancel"] = "/mpoint/wire-card/cancel";
$aHTTP_CONN_INFO["wire-card"]["paths"]["post-status"] = "/mpoint/wire-card/post-status";


/**
 * Connection info for connecting to Android Pay
 */
$aHTTP_CONN_INFO["android-pay"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["android-pay"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["android-pay"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["android-pay"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["android-pay"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["android-pay"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["android-pay"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["android-pay"]["paths"]["initialize"] = "/mpoint/android-pay/initialize";
$aHTTP_CONN_INFO["android-pay"]["paths"]["get-payment-data"] = "/mpoint/android-pay/get-payment-data";

/**
 * Connection info for connecting to GlobalCollect
 */
$aHTTP_CONN_INFO["global-collect"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["global-collect"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["global-collect"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["global-collect"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["global-collect"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["global-collect"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["global-collect"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["global-collect"]["paths"]["initialize"] = "/mpoint/global-collect/initialize";
$aHTTP_CONN_INFO["global-collect"]["paths"]["auth"] = "/mpoint/global-collect/authorize";
$aHTTP_CONN_INFO["global-collect"]["paths"]["capture"] = "/mpoint/global-collect/capture";
$aHTTP_CONN_INFO["global-collect"]["paths"]["status"] = "/mpoint/global-collect/status";
$aHTTP_CONN_INFO["global-collect"]["paths"]["refund"] = "/mpoint/global-collect/refund";
$aHTTP_CONN_INFO["global-collect"]["paths"]["callback"] = "/mpoint/global-collect/callback";
$aHTTP_CONN_INFO["global-collect"]["paths"]["cancel"] = "/mpoint/global-collect/cancel";
$aHTTP_CONN_INFO["global-collect"]["paths"]["auth-complete"] = "/mpoint/global-collect/auth-complete";


/**
 * Connection info for connecting to SecureTrading
 */
$aHTTP_CONN_INFO["secure-trading"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["secure-trading"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["secure-trading"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["secure-trading"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["secure-trading"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["secure-trading"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["secure-trading"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["secure-trading"]["paths"]["initialize"] = "/mpoint/secure-trading/initialize";
$aHTTP_CONN_INFO["secure-trading"]["paths"]["auth"] = "/mpoint/secure-trading/authorize-payment";
$aHTTP_CONN_INFO["secure-trading"]["paths"]["capture"] = "/mpoint/secure-trading/capture";
$aHTTP_CONN_INFO["secure-trading"]["paths"]["status"] = "/mpoint/secure-trading/status";
$aHTTP_CONN_INFO["secure-trading"]["paths"]["refund"] = "/mpoint/secure-trading/refund";
$aHTTP_CONN_INFO["secure-trading"]["paths"]["callback"] = "/mpoint/secure-trading/callback";
$aHTTP_CONN_INFO["secure-trading"]["paths"]["cancel"] = "/mpoint/secure-trading/cancel";


/**
 * Connection info for connecting to PayFort
 */

$aHTTP_CONN_INFO["payfort"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["payfort"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["payfort"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["payfort"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["payfort"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["payfort"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["payfort"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["payfort"]["paths"]["initialize"] = "/mpoint/payfort/initialize";
$aHTTP_CONN_INFO["payfort"]["paths"]["auth"] = "/mpoint/payfort/authorize-payment";
$aHTTP_CONN_INFO["payfort"]["paths"]["capture"] = "/mpoint/payfort/capture";
$aHTTP_CONN_INFO["payfort"]["paths"]["refund"] = "/mpoint/payfort/refund";
$aHTTP_CONN_INFO["payfort"]["paths"]["status"] = "/mpoint/payfort/status";
$aHTTP_CONN_INFO["payfort"]["paths"]["cancel"] = "/mpoint/payfort/cancel";
$aHTTP_CONN_INFO["payfort"]["paths"]["callback"] = "/mpoint/payfort/callback";


/**
 * Connection info for connecting to PayPal
 */
$aHTTP_CONN_INFO["paypal"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["paypal"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["paypal"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["paypal"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["paypal"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["paypal"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["paypal"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["paypal"]["paths"]["initialize"] = "/mpoint/paypal/initialize";
$aHTTP_CONN_INFO["paypal"]["paths"]["auth"] = "/mpoint/paypal/authorize-payment";
$aHTTP_CONN_INFO["paypal"]["paths"]["capture"] = "/mpoint/paypal/capture";
$aHTTP_CONN_INFO["paypal"]["paths"]["refund"] = "/mpoint/paypal/refund";
$aHTTP_CONN_INFO["paypal"]["paths"]["cancel"] = "/mpoint/paypal/cancel";


/**
 * Connection info for connecting to CCAvenue
 */
$aHTTP_CONN_INFO["ccavenue"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["ccavenue"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["ccavenue"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["ccavenue"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["ccavenue"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["ccavenue"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["ccavenue"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["ccavenue"]["paths"]["initialize"] = "/mpoint/ccavenue/initialize";
$aHTTP_CONN_INFO["ccavenue"]["paths"]["auth"] = "/mpoint/ccavenue/authorize-payment";
$aHTTP_CONN_INFO["ccavenue"]["paths"]["capture"] = "/mpoint/ccavenue/capture";
$aHTTP_CONN_INFO["ccavenue"]["paths"]["refund"] = "/mpoint/ccavenue/refund";
$aHTTP_CONN_INFO["ccavenue"]["paths"]["status"] = "/mpoint/ccavenue/status";
$aHTTP_CONN_INFO["ccavenue"]["paths"]["cancel"] = "/mpoint/ccavenue/cancel";
$aHTTP_CONN_INFO["ccavenue"]["paths"]["callback"] = "/mpoint/ccavenue/callback";

/**
 * Connection info for connecting to 2C2P
 */
$aHTTP_CONN_INFO["2c2p"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["2c2p"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["2c2p"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["2c2p"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["2c2p"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["2c2p"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["2c2p"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["2c2p"]["paths"]["initialize"] = "/mpoint/2c2p/initialize";
$aHTTP_CONN_INFO["2c2p"]["paths"]["auth"] = "/mpoint/2c2p/authorize-payment";
$aHTTP_CONN_INFO["2c2p"]["paths"]["capture"] = "/mpoint/2c2p/capture";
$aHTTP_CONN_INFO["2c2p"]["paths"]["refund"] = "/mpoint/2c2p/refund";
$aHTTP_CONN_INFO["2c2p"]["paths"]["status"] = "/mpoint/2c2p/status";
$aHTTP_CONN_INFO["2c2p"]["paths"]["cancel"] = "/mpoint/2c2p/cancel";
$aHTTP_CONN_INFO["2c2p"]["paths"]["callback"] = "/mpoint/2c2p/callback";


/**
 * Connection info for connecting to PublicBank
 */
$aHTTP_CONN_INFO["public-bank"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["public-bank"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["public-bank"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["public-bank"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["public-bank"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["public-bank"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["public-bank"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["public-bank"]["paths"]["initialize"] = "/mpoint/public-bank/initialize";
$aHTTP_CONN_INFO["public-bank"]["paths"]["auth"] = "/mpoint/public-bank/authorize-payment";
$aHTTP_CONN_INFO["public-bank"]["paths"]["capture"] = "/mpoint/public-bank/capture";
$aHTTP_CONN_INFO["public-bank"]["paths"]["refund"] = "/mpoint/public-bank/refund";
$aHTTP_CONN_INFO["public-bank"]["paths"]["status"] = "/mpoint/public-bank/status";
$aHTTP_CONN_INFO["public-bank"]["paths"]["cancel"] = "/mpoint/public-bank/cancel";
$aHTTP_CONN_INFO["public-bank"]["paths"]["callback"] = "/mpoint/public-bank/callback";

/**
 * Connection info for connecting to MayBank
 */
$aHTTP_CONN_INFO["maybank"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["maybank"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["maybank"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["maybank"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["maybank"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["maybank"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["maybank"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["maybank"]["paths"]["initialize"] = "/mpoint/maybank/initialize";
$aHTTP_CONN_INFO["maybank"]["paths"]["auth"] = "/mpoint/maybank/authorize-payment";
$aHTTP_CONN_INFO["maybank"]["paths"]["capture"] = "/mpoint/maybank/capture";
$aHTTP_CONN_INFO["maybank"]["paths"]["refund"] = "/mpoint/maybank/refund";
$aHTTP_CONN_INFO["maybank"]["paths"]["status"] = "/mpoint/maybank/status";
$aHTTP_CONN_INFO["maybank"]["paths"]["cancel"] = "/mpoint/maybank/cancel";
$aHTTP_CONN_INFO["maybank"]["paths"]["callback"] = "/mpoint/maybank/callback";

/**
 * Connection info for connecting to AliPay
 */
$aHTTP_CONN_INFO["alipay"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["alipay"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["alipay"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["alipay"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["alipay"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["alipay"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["alipay"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["alipay"]["paths"]["initialize"] = "/mpoint/alipay/initialize";
$aHTTP_CONN_INFO["alipay"]["paths"]["refund"] = "/mpoint/alipay/refund";
$aHTTP_CONN_INFO["alipay"]["paths"]["status"] = "/mpoint/alipay/status";
$aHTTP_CONN_INFO["alipay"]["paths"]["callback"] = "/mpoint/alipay/callback";

/**
 * Connection info for connecting to POLi
 */
$aHTTP_CONN_INFO["poli"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["poli"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["poli"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["poli"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["poli"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["poli"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["poli"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["poli"]["paths"]["initialize"] = "/mpoint/poli/initialize";
$aHTTP_CONN_INFO["poli"]["paths"]["callback"] = "/mpoint/poli/callback";

/**
 * Connection info for connecting to QIWI Wallet
 */
$aHTTP_CONN_INFO["qiwi"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["qiwi"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["qiwi"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["qiwi"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["qiwi"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["qiwi"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["qiwi"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["qiwi"]["paths"]["initialize"] = "/mpoint/qiwi/initialize";
$aHTTP_CONN_INFO["qiwi"]["paths"]["refund"] = "/mpoint/qiwi/refund";
$aHTTP_CONN_INFO["qiwi"]["paths"]["status"] = "/mpoint/qiwi/status";
$aHTTP_CONN_INFO["qiwi"]["paths"]["cancel"] = "/mpoint/qiwi/cancel";
$aHTTP_CONN_INFO["qiwi"]["paths"]["callback"] = "/mpoint/qiwi/callback";


/**
 * Connection info for connecting to Klarna
 */
$aHTTP_CONN_INFO["klarna"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["klarna"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["klarna"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["klarna"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["klarna"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["klarna"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["klarna"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["klarna"]["paths"]["initialize"] = "/mpoint/klarna/initialize";
$aHTTP_CONN_INFO["klarna"]["paths"]["auth"] = "/mpoint/klarna/authorize-payment";
$aHTTP_CONN_INFO["klarna"]["paths"]["refund"] = "/mpoint/klarna/refund";
$aHTTP_CONN_INFO["klarna"]["paths"]["status"] = "/mpoint/klarna/status";
$aHTTP_CONN_INFO["klarna"]["paths"]["cancel"] = "/mpoint/klarna/cancel";
$aHTTP_CONN_INFO["klarna"]["paths"]["callback"] = "/mpoint/klarna/callback";



/**
 * Connection info for connecting to Trustly
 */
$aHTTP_CONN_INFO["trustly"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["trustly"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["trustly"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["trustly"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["trustly"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["trustly"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["trustly"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["trustly"]["paths"]["initialize"] = "/mpoint/trustly/initialize";
$aHTTP_CONN_INFO["trustly"]["paths"]["auth"] = "/mpoint/trustly/authorize-payment";
$aHTTP_CONN_INFO["trustly"]["paths"]["refund"] = "/mpoint/trustly/refund";
$aHTTP_CONN_INFO["trustly"]["paths"]["status"] = "/mpoint/trustly/status";
$aHTTP_CONN_INFO["trustly"]["paths"]["cancel"] = "/mpoint/trustly/cancel";
$aHTTP_CONN_INFO["trustly"]["paths"]["callback"] = "/mpoint/trustly/callback";

/**
 * Connection info for connecting to MobilePay Online
 */
$aHTTP_CONN_INFO["mobilepay-online"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["mobilepay-online"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["mobilepay-online"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["mobilepay-online"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["mobilepay-online"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["mobilepay-online"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["mobilepay-online"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["mobilepay-online"]["paths"]["initialize"] = "/mpoint/mobilepay-online/initialize";
$aHTTP_CONN_INFO["mobilepay-online"]["paths"]["auth"] = "/mpoint/mobilepay-online/authorize-payment";

/**
 * Connection info for connecting to Nets
 */
$aHTTP_CONN_INFO["nets"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["nets"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["nets"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["nets"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["nets"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["nets"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["nets"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["nets"]["paths"]["auth"] = "/mpsp/nets/authorize-payment";
$aHTTP_CONN_INFO["nets"]["paths"]["authenticate"] = "/mpoint/authenticate";
$aHTTP_CONN_INFO["nets"]["paths"]["capture"] = "/mpsp/nets/capture";
$aHTTP_CONN_INFO["nets"]["paths"]["initialize"] = "/mpsp/nets/initialize";
$aHTTP_CONN_INFO["nets"]["paths"]["refund"] = "/mpsp/nets/refund";
$aHTTP_CONN_INFO["nets"]["paths"]["cancel"] = "/mpsp/nets/cancel";

$aHTTP_CONN_INFO["netsmpi"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["netsmpi"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["netsmpi"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["netsmpi"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["netsmpi"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["netsmpi"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["netsmpi"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["netsmpi"]["paths"]["authenticate"] = "/mpi/nets/authentication";
/**
 * Connection info for connecting to mVault
 */
$aHTTP_CONN_INFO["mvault"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["mvault"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["mvault"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["mvault"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["mvault"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["mvault"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["mvault"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["mvault"]["paths"]["get-payment-data"] = "/mpoint/mvault/get-payment-data";
$aHTTP_CONN_INFO["mvault"]["paths"]["get-token"] = "/mpoint/mvault/get-token";
$aHTTP_CONN_INFO["mvault"]["paths"]["tokenize"] = "/mpoint/mvault/save-card";
$aHTTP_CONN_INFO["mvault"]["paths"]["save-card"] = "/mpoint/save-card";

/**
 * Connection info for connecting to Paytabs
 */
$aHTTP_CONN_INFO["paytabs"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["paytabs"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["paytabs"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["paytabs"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["paytabs"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["paytabs"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["paytabs"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["paytabs"]["paths"]["initialize"] = "/mpoint/paytabs/initialize";
$aHTTP_CONN_INFO["paytabs"]["paths"]["refund"] = "/mpoint/paytabs/refund";

/**
 * Connection info for connecting to 2C2P ALC
 */
$aHTTP_CONN_INFO["2c2p-alc"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["2c2p-alc"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["2c2p-alc"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["2c2p-alc"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["2c2p-alc"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["2c2p-alc"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["2c2p-alc"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["2c2p-alc"]["paths"]["initialize"] = "/mpoint/2c2p-alc/initialize";
$aHTTP_CONN_INFO["2c2p-alc"]["paths"]["auth"] = "/mpoint/2c2p-alc/authorize-payment";
$aHTTP_CONN_INFO["2c2p-alc"]["paths"]["capture"] = "/mpoint/2c2p-alc/capture";
$aHTTP_CONN_INFO["2c2p-alc"]["paths"]["refund"] = "/mpoint/2c2p-alc/refund";
$aHTTP_CONN_INFO["2c2p-alc"]["paths"]["cancel"] = "/mpoint/2c2p-alc/cancel";
$aHTTP_CONN_INFO["2c2p-alc"]["paths"]["callback"] = "/mpoint/2c2p-alc/callback";
$aHTTP_CONN_INFO["2c2p-alc"]["paths"]["post-status"] = "/mpoint/2c2p-alc/post-status";
$aHTTP_CONN_INFO["2c2p-alc"]["paths"]["status"] = "/mpoint/2c2p-alc/status";


/**
 * Connection info for connecting to Citcon
 */
$aHTTP_CONN_INFO["citcon"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["citcon"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["citcon"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["citcon"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["citcon"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["citcon"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["citcon"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["citcon"]["paths"]["initialize"] = "/mpoint/citcon/initialize";
$aHTTP_CONN_INFO["citcon"]["paths"]["refund"] = "/mpoint/citcon/refund";
$aHTTP_CONN_INFO["citcon"]["paths"]["status"] = "/mpoint/citcon/status";


/**
 * Connection info for connecting to AliPay Chinese
 */
$aHTTP_CONN_INFO["alipay-chinese"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["alipay-chinese"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["alipay-chinese"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["alipay-chinese"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["alipay-chinese"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["alipay-chinese"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["alipay-chinese"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["alipay-chinese"]["paths"]["initialize"] = "/mpoint/alipay-chinese/initialize";
$aHTTP_CONN_INFO["alipay-chinese"]["paths"]["refund"] = "/mpoint/alipay-chinese/refund";
$aHTTP_CONN_INFO["alipay-chinese"]["paths"]["status"] = "/mpoint/alipay-chinese/status";
$aHTTP_CONN_INFO["alipay-chinese"]["paths"]["callback"] = "/mpoint/alipay-chinese/callback";

/**
 * Connection info for connecting to Google Pay
 */
$aHTTP_CONN_INFO["google-pay"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["google-pay"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["google-pay"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["google-pay"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["google-pay"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["google-pay"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["google-pay"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["google-pay"]["paths"]["get-payment-data"] = "/mpoint/google-pay/get-payment-data";
$aHTTP_CONN_INFO["google-pay"]["paths"]["initialize"] = "/mpoint/google-pay/initialize";
/**
 * Connection info for connecting to PPRO
 */
$aHTTP_CONN_INFO["ppro"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["ppro"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["ppro"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["ppro"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["ppro"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["ppro"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["ppro"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["ppro"]["paths"]["initialize"] = "/mpoint/ppro/initialize";
$aHTTP_CONN_INFO["ppro"]["paths"]["auth"] = "/mpoint/ppro/authorize-payment";
$aHTTP_CONN_INFO["ppro"]["paths"]["refund"] = "/mpoint/ppro/refund";
$aHTTP_CONN_INFO["ppro"]["paths"]["callback"] = "/mpoint/ppro/callback";
/**
 * Connection info for connecting to Amex
 */
$aHTTP_CONN_INFO["amex"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["amex"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["amex"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["amex"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["amex"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["amex"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["amex"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["amex"]["paths"]["auth"] = "/mpsp/amex/authorize-payment";
$aHTTP_CONN_INFO["amex"]["paths"]["authenticate"] = "/mpoint/authenticate";
$aHTTP_CONN_INFO["amex"]["paths"]["capture"] = "/mpsp/amex/capture";
$aHTTP_CONN_INFO["amex"]["paths"]["initialize"] = "/mpsp/amex/initialize";
$aHTTP_CONN_INFO["amex"]["paths"]["refund"] = "/mpsp/amex/refund";
$aHTTP_CONN_INFO["amex"]["paths"]["cancel"] = "/mpsp/amex/cancel";
$aHTTP_CONN_INFO["amex"]["paths"]["settlement"] = "/mpsp/amex/payment-settlement";
$aHTTP_CONN_INFO["amex"]["paths"]["process-settlement"] = "/mpsp/amex/process-settlement";


/**
 * Connection info for connecting to Modirum MPI
 */
$aHTTP_CONN_INFO["modirummpi"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["modirummpi"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["modirummpi"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["modirummpi"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["modirummpi"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["modirummpi"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["modirummpi"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["modirummpi"]["paths"]["authenticate"] = "/mpi/modirum/authenticate";

/**
 * Connection info for connecting to CHUBB
 */
$aHTTP_CONN_INFO["chubb"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["chubb"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["chubb"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["chubb"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["chubb"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["chubb"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["chubb"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["chubb"]["paths"]["initialize"] = "/mpoint/chubb/initialize";
$aHTTP_CONN_INFO["chubb"]["paths"]["auth"] = "/mpoint/chubb/authorize-payment";
$aHTTP_CONN_INFO["chubb"]["paths"]["callback"] = "/mpoint/chubb/callback";


/**
 * Connection info for connecting to UATP
 */
$aHTTP_CONN_INFO["uatp"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["uatp"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["uatp"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["uatp"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["uatp"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["uatp"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["uatp"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["uatp"]["paths"]["auth"] = "/mpsp/uatp/authorize-payment";
$aHTTP_CONN_INFO["uatp"]["paths"]["initialize"] = "/mpsp/uatp/initialize";
$aHTTP_CONN_INFO["uatp"]["paths"]["tokenize"] = "/mpoint/uatp/generate-suvtp";
$aHTTP_CONN_INFO["uatp"]["paths"]["process-settlement"] = "/mpsp/uatp/bulk-settlement";
$aHTTP_CONN_INFO["uatp"]["paths"]["callback"] = "/mpoint/uatp/notify-client";


/**
 * Connection info for connecting to eGHL-FPX
 */
$aHTTP_CONN_INFO["eghl"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["eghl"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["eghl"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["eghl"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["eghl"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["eghl"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["eghl"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["eghl"]["paths"]["initialize"] = "/mpoint/eghl/initialize";
$aHTTP_CONN_INFO["eghl"]["paths"]["get-payment-methods"] = "/mpoint/eghl/get-payment-methods";



/**
 * Connection info for connecting to Chase
 */
$aHTTP_CONN_INFO["chase"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["chase"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["chase"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["chase"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["chase"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["chase"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["chase"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["chase"]["paths"]["auth"] = "/mpsp/chase/authorize-payment";
$aHTTP_CONN_INFO["chase"]["paths"]["initialize"] = "/mpsp/chase/initialize";
$aHTTP_CONN_INFO["chase"]["paths"]["cancel"] = "/mpsp/chase/cancel";
$aHTTP_CONN_INFO["chase"]["paths"]["authenticate"] = "/mpoint/authenticate";
$aHTTP_CONN_INFO["chase"]["paths"]["settlement"] = "/mpsp/chase/payment-settlement";
$aHTTP_CONN_INFO["chase"]["paths"]["process-settlement"] = "/mpsp/chase/process-settlement";


/**
 * Connection info for connecting to PayU
 */
$aHTTP_CONN_INFO["payu"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["payu"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["payu"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["payu"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["payu"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["payu"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["payu"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["payu"]["paths"]["initialize"] = "/mpoint/payu/initialize";
$aHTTP_CONN_INFO["payu"]["paths"]["auth"] = "/mpoint/payu/authorize-payment";
$aHTTP_CONN_INFO["payu"]["paths"]["refund"] = "/mpoint/payu/refund";
$aHTTP_CONN_INFO["payu"]["paths"]["callback"] = "/mpoint/payu/callback";

/**
 * Connection info for connecting to Cielo
 */
$aHTTP_CONN_INFO["cielo"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["cielo"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["cielo"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["cielo"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["cielo"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["cielo"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["cielo"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["cielo"]["paths"]["auth"] = "/mpsp/cielo/authorize-payment";
$aHTTP_CONN_INFO["cielo"]["paths"]["capture"] = "/mpsp/cielo/capture";
$aHTTP_CONN_INFO["cielo"]["paths"]["initialize"] = "/mpsp/cielo/initialize";
$aHTTP_CONN_INFO["cielo"]["paths"]["refund"] = "/mpsp/cielo/void";
$aHTTP_CONN_INFO["cielo"]["paths"]["cancel"] = "/mpsp/cielo/void";


/**
 * Connection info for connecting to Global Payments
 */
$aHTTP_CONN_INFO["global-payments"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["global-payments"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["global-payments"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["global-payments"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["global-payments"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["global-payments"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["global-payments"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["global-payments"]["paths"]["auth"] = "/mpoint/global-payments/authorize-payment";
$aHTTP_CONN_INFO["global-payments"]["paths"]["capture"] = "/mpoint/global-payments/capture";
$aHTTP_CONN_INFO["global-payments"]["paths"]["initialize"] = "/mpoint/global-payments/initialize";
$aHTTP_CONN_INFO["global-payments"]["paths"]["refund"] = "/mpoint/global-payments/refund";
$aHTTP_CONN_INFO["global-payments"]["paths"]["cancel"] = "/mpoint/global-payments/cancel";


/**
 * Connection info for connecting to Data Cash
 */
$aHTTP_CONN_INFO["cellulant"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["cellulant"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["cellulant"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["cellulant"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["cellulant"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["cellulant"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["cellulant"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["cellulant"]["paths"]["initialize"] = "/mpoint/cellulant/initialize";
$aHTTP_CONN_INFO["cellulant"]["paths"]["auth"] = "/mpoint/cellulant/authorize-payment";
$aHTTP_CONN_INFO["cellulant"]["paths"]["get-payment-options"] = "/mpoint/cellulant/GetPaymentOptions";
$aHTTP_CONN_INFO["cellulant"]["paths"]["status"] = "/mpoint/cellulant/status";
$aHTTP_CONN_INFO["cellulant"]["paths"]["acknowledge-payments"] = "/mpoint/cellulant/acknowledge-payments";
$aHTTP_CONN_INFO["cellulant"]["paths"]["refund"] = "/mpoint/cellulant/refund";


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
$aCPM_CONN_INFO["host"] = "mpoint.local.cellpointmobile.com";
$aCPM_CONN_INFO["port"] = 80;
$aCPM_CONN_INFO["timeout"] = 20;
$aCPM_CONN_INFO["path"] = "/callback/cpm.php";
$aCPM_CONN_INFO["method"] = "POST";
$aCPM_CONN_INFO["contenttype"] = "application/x-www-form-urlencoded";
//$aCPM_CONN_INFO["username"] = "";
//$aCPM_CONN_INFO["password"] = "";

/**
 *
 *     Configuration for HPP
 *
 */

$aHTTP_CONN_INFO["hpp"]["protocol"] = "https";



/**
 * Template for website design
 */
define("sTEMPLATE", "velocity");

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
