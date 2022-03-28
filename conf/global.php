<?php
/**
 * Set error types that are to be reported by the error handler
 * Both errors and warnings are reported, notices however are not
 * TODO CMP-4527 Extend logging functionality to support json formatted logging to std. out
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
 *  8 - Output to SAPI logging handler in json format
 */
define("iOUTPUT_METHOD", env('LOG_OUTPUT_METHOD', 8));

/**
 * General debug level for the error handler
 *	0 - Output error
 *	1 - Add stack trace for exceptions and variable scope for errors to log message
 *	2 - Add custom trace using the {TRACE <DATA>} syntax
 */
define("iDEBUG_LEVEL", env('LOG_DEBUG_LEVEL', 2));

/**
 * Path to the application error log
 */
define("sERROR_LOG", sLOG_PATH ."app_error_".".log");

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

$aDB_CONN_INFO["mpoint"]["host"] = env("database.mpoint.host", "localhost");
$aDB_CONN_INFO["mpoint"]["port"] = env("database.mpoint.port", 5432);
$aDB_CONN_INFO["mpoint"]["path"] = env("database.mpoint.path", "mpoint");
$aDB_CONN_INFO["mpoint"]["username"] = env("database.mpoint.username", "mpoint");
$aDB_CONN_INFO["mpoint"]["password"] = env("database.mpoint.password", "");
$aDB_CONN_INFO["mpoint"]["class"] = env("database.mpoint.class", "PostGreSQL");
$aDB_CONN_INFO["mpoint"]["timeout"] = env("database.mpoint.timeout", 10);
$aDB_CONN_INFO["mpoint"]["charset"] = env("database.mpoint.charset", "UTF8");
$aDB_CONN_INFO["mpoint"]["connmode"] = env("database.mpoint.connmode", "normal");
$aDB_CONN_INFO["mpoint"]["errorpath"] = env("database.mpoint.errorpath", sLOG_PATH ."db_error_".".log");
$aDB_CONN_INFO["mpoint"]["errorhandling"] = env("database.mpoint.errorhandling", 3);
$aDB_CONN_INFO["mpoint"]["exectime"] = env("database.mpoint.exectime", 0.3);
$aDB_CONN_INFO["mpoint"]["execpath"] = env("database.mpoint.execpath", sLOG_PATH ."db_exectime_".".log");
$aDB_CONN_INFO["mpoint"]["keycase"] = env("database.mpoint.keycase", CASE_UPPER);
$aDB_CONN_INFO["mpoint"]["debuglevel"] = iDEBUG_LEVEL;
$aDB_CONN_INFO["mpoint"]["method"] = iOUTPUT_METHOD;

/**
 * Database settings for Session database
 */
$aDB_CONN_INFO["session"]["host"] = env("database.session.host", "localhost");
$aDB_CONN_INFO["session"]["port"] = env("database.session.port", 5432);
$aDB_CONN_INFO["session"]["path"] = env("database.session.path", "session");
$aDB_CONN_INFO["session"]["username"] = env("database.session.username", "session");
$aDB_CONN_INFO["session"]["password"] = env("database.session.password", "");
$aDB_CONN_INFO["session"]["timeout"] = env("database.session.timeout", 10);
$aDB_CONN_INFO["session"]["charset"] = env("database.session.charset", "ISO8859_1");
$aDB_CONN_INFO["session"]["class"] = env("database.session.class", "PostGreSQL");
$aDB_CONN_INFO["session"]["connmode"] = env("database.session.connmode", "normal");
$aDB_CONN_INFO["session"]["errorpath"] = env("database.session.errorpath", sLOG_PATH ."db_error_".".log");
$aDB_CONN_INFO["session"]["errorhandling"] = env("database.session.errorhandling", 3);
$aDB_CONN_INFO["session"]["exectime"] = env("database.session.exectime", 0.3);
$aDB_CONN_INFO["session"]["execpath"] = env("database.session.execpath", sLOG_PATH ."db_exectime_".".log");
$aDB_CONN_INFO["session"]["keycase"] = env("database.session.keycase", CASE_UPPER);
$aDB_CONN_INFO["session"]["debuglevel"] = iDEBUG_LEVEL;
$aDB_CONN_INFO["session"]["method"] = iOUTPUT_METHOD;


/**
 * Connection info for sending error reports to a remote host
 * TODO CMP-4529 All mESB URLs must be configurable via env
 */
$aHTTP_CONN_INFO["mesb"]["protocol"] = env("http.mesb.protocol", "http");
$aHTTP_CONN_INFO["mesb"]["host"] = env("http.mesb.host", "localhost");
$aHTTP_CONN_INFO["mesb"]["port"] = env("http.mesb.port", 10080);
$aHTTP_CONN_INFO["mesb"]["timeout"] = env("http.mesb.timeout", 120);
$aHTTP_CONN_INFO["mesb"]["path"] = env("http.mesb.path", "/");
$aHTTP_CONN_INFO["mesb"]["method"] = env("http.mesb.method", "POST");
$aHTTP_CONN_INFO["mesb"]["contenttype"] = env("http.mesb.contenttype", "text/xml");
$aHTTP_CONN_INFO["mesb"]["username"] = env("http.mesb.username", "");
$aHTTP_CONN_INFO["mesb"]["password"] = env("http.mesb.password", "");

/**
 * Connection info for sending error reports to a remote host
 */
$aHTTP_CONN_INFO["iemendo"]["protocol"] = env("http.iemendo.protocol", "http");
$aHTTP_CONN_INFO["iemendo"]["host"] = env("http.iemendo.host", "iemendo.test.cellpointmobile.com");
$aHTTP_CONN_INFO["iemendo"]["port"] = env("http.iemendo.port", 80);
$aHTTP_CONN_INFO["iemendo"]["timeout"] = env("http.iemendo.timeout", 20);
$aHTTP_CONN_INFO["iemendo"]["path"] = env("http.iemendo.path", "/api/receive_report.php");
$aHTTP_CONN_INFO["iemendo"]["method"] = env("http.iemendo.method", "POST");
$aHTTP_CONN_INFO["iemendo"]["contenttype"] = env("http.iemendo.contenttype", "text/xml");


/**
 * Connection info for identifying a mobile device by sending its UA Profile information to iEmendo
 */
$aUA_CONN_INFO["protocol"] = env("ua.iemendo.protocol", "http");
$aUA_CONN_INFO["host"] = env("ua.iemendo.host", "iemendo.test.cellpointmobile.com");
$aUA_CONN_INFO["port"] = env("ua.iemendo.port", 80);
$aUA_CONN_INFO["timeout"] = env("ua.iemendo.timeout", 20);
$aUA_CONN_INFO["path"] = env("ua.iemendo.path", "/api/uaprofile.php");
$aUA_CONN_INFO["method"] = env("ua.iemendo.method", "POST");
$aUA_CONN_INFO["contenttype"] = env("ua.iemendo.contenttype", "text/xml");

//$aUA_CONN_INFO["username"] = "";
//$aUA_CONN_INFO["password"] = "";

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
$aHTTP_CONN_INFO[4]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[4]["path"] =""; // Set by calling class
$aHTTP_CONN_INFO[4]["method"] =  $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[4]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[4]["paths"]["initialize"] = "/mpoint/worldpay/initialize";
$aHTTP_CONN_INFO[4]["paths"]["authenticate"] = "/mpoint/authenticate";
$aHTTP_CONN_INFO[4]["paths"]["auth"] = "/mpoint/worldpay/authorize-payment";
$aHTTP_CONN_INFO[4]["paths"]["capture"] = "/mpoint/worldpay/capture";
$aHTTP_CONN_INFO[4]["paths"]["status"] = "/mpoint/worldpay/status";
$aHTTP_CONN_INFO[4]["paths"]["cancel"] = "/mpoint/worldpay/cancel";
$aHTTP_CONN_INFO[4]["paths"]["refund"] = "/mpoint/worldpay/refund";

/**
 * Connection info for connecting to PayEx
 */
$aHTTP_CONN_INFO["payex"]["protocol"] = env("http.payex.protocol", "https");
$aHTTP_CONN_INFO["payex"]["host"] = env("http.payex.host", "external.payex.com");
$aHTTP_CONN_INFO["payex"]["port"] = env("http.payex.port", 443);
$aHTTP_CONN_INFO["payex"]["timeout"] = env("http.payex.timeout", 120);
$aHTTP_CONN_INFO["payex"]["path"] = env("http.payex.path", "/PxOrder/Pxorder.asmx?WSDL");
$aHTTP_CONN_INFO["payex"]["method"] = env("http.payex.method", "POST");
$aHTTP_CONN_INFO["payex"]["contenttype"] = env("http.payex.contenttype", "text/xml");
$aHTTP_CONN_INFO["payex"]["password"] = env("http.payex.password", "");

/**
 * Connection info for connecting to CPG
 */
$aHTTP_CONN_INFO["cpg"]["protocol"] = env("http.cpg.protocol", "https");
$aHTTP_CONN_INFO["cpg"]["host"] = env("http.cpg.host", "pgstaging.emirates.com");
$aHTTP_CONN_INFO["cpg"]["port"] = env("http.cpg.port", 443);
$aHTTP_CONN_INFO["cpg"]["timeout"] = env("http.cpg.timeout", 120);
$aHTTP_CONN_INFO["cpg"]["path"] = env("http.cpg.path", "/cpg/Order.jsp");
$aHTTP_CONN_INFO["cpg"]["method"] = env("http.cpg.method", "POST");
$aHTTP_CONN_INFO["cpg"]["contenttype"] = env("http.cpg.contenttype", "text/xml");

/**
 * Connection info for connecting to Authorize.Net
 */
$aHTTP_CONN_INFO["authorize.net"]["protocol"] = env("http.authorize.net.protocol", "https");
$aHTTP_CONN_INFO["authorize.net"]["host"] = env("http.authorize.net.host", "secure.authorize.net");
$aHTTP_CONN_INFO["authorize.net"]["port"] = env("http.authorize.net.port", 443);
$aHTTP_CONN_INFO["authorize.net"]["timeout"] = env("http.authorize.net.timeout", 120);
$aHTTP_CONN_INFO["authorize.net"]["path"] = env("http.authorize.net.path", "/gateway/transact.dll");
$aHTTP_CONN_INFO["authorize.net"]["method"] = env("http.authorize.net.method", "POST");
$aHTTP_CONN_INFO["authorize.net"]["contenttype"] = env("http.authorize.net.contenttype", "application/x-www-form-urlencoded");


/**
 * Connection info for connecting to WannaFind
 */
$aHTTP_CONN_INFO["wannafind"]["protocol"] = env("http.wannafind.protocol", "https");
$aHTTP_CONN_INFO["wannafind"]["host"] = env("http.wannafind.host", "betaling.wannafind.dk");
$aHTTP_CONN_INFO["wannafind"]["port"] = env("http.wannafind.port", 443);
$aHTTP_CONN_INFO["wannafind"]["timeout"] = env("http.wannafind.timeout", 120);
$aHTTP_CONN_INFO["wannafind"]["path"] = env("http.wannafind.path", "/auth.php");
$aHTTP_CONN_INFO["wannafind"]["method"] = env("http.wannafind.method", "POST");
$aHTTP_CONN_INFO["wannafind"]["contenttype"] = env("http.wannafind.contenttype", "application/x-www-form-urlencoded");

/**
 * Connection info for connecting to NetAxept
 */
$aHTTP_CONN_INFO["netaxept"]["protocol"] = env("http.netaxept.protocol", "https");
$aHTTP_CONN_INFO["netaxept"]["host"] = env("http.netaxept.host", "epayment-test.bbs.no");
$aHTTP_CONN_INFO["netaxept"]["port"] = env("http.netaxept.port", 443);
$aHTTP_CONN_INFO["netaxept"]["timeout"] = env("http.netaxept.timeout", 120);
$aHTTP_CONN_INFO["netaxept"]["path"] = env("http.netaxept.path", "/netaxept.svc?wsdl");
$aHTTP_CONN_INFO["netaxept"]["method"] = env("http.netaxept.method", "POST");
$aHTTP_CONN_INFO["netaxept"]["contenttype"] = env("http.netaxept.contenttype", "application/x-www-form-urlencoded");

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
$aHTTP_CONN_INFO[12]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[12]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[12]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[12]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[12]["paths"]["initialize"] = "/mpoint/aggregator/adyen/initialize";
$aHTTP_CONN_INFO[12]["paths"]["auth"] = "/mpoint/aggregator/adyen/authorize-payment";
$aHTTP_CONN_INFO[12]["paths"]["capture"] = "/mpoint/aggregator/adyen/capture";
$aHTTP_CONN_INFO[12]["paths"]["void"] = "/mpoint/aggregator/adyen/void";
$aHTTP_CONN_INFO[12]["paths"]["cancel"] = "/mpoint/aggregator/adyen/cancel";
$aHTTP_CONN_INFO[12]["paths"]["refund"] = "/mpoint/aggregator/adyen/refund";
$aHTTP_CONN_INFO[12]["paths"]["authenticate"] = "/mpoint/authenticate";

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
$aHTTP_CONN_INFO[13]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[13]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[13]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[13]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[13]["paths"]["initialize"] = "/mpoint/visa-checkout/initialize";
$aHTTP_CONN_INFO[13]["paths"]["get-payment-data"] = "/mpoint/visa-checkout/get-payment-data";
$aHTTP_CONN_INFO[13]["paths"]["callback"] = "/mpoint/visa-checkout/callback";

/**
 * Connection info for connecting to AMEX Express Checkout
 */
$aHTTP_CONN_INFO[16]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[16]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[16]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[16]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[16]["paths"]["initialize"] = "/mpoint/amex-express-checkout/initialize";
$aHTTP_CONN_INFO[16]["paths"]["get-payment-data"] = "/mpoint/amex-express-checkout/get-payment-data";
$aHTTP_CONN_INFO[16]["paths"]["callback"] = "/mpoint/amex-express-checkout/callback";


/**
 * Connection info for connecting to Apple Pay
 */
$aHTTP_CONN_INFO[14]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[14]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[14]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[14]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[14]["paths"]["initialize"] = "/mpoint/apple-pay/initialize";
$aHTTP_CONN_INFO[14]["paths"]["get-payment-data"] = "/mpoint/apple-pay/get-payment-data";

/**
 * Connection info for connecting to MasterPass
 */
$aHTTP_CONN_INFO[15]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[15]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[15]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[15]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[15]["paths"]["initialize"] = "/mpoint/masterpass/initialize";
$aHTTP_CONN_INFO[15]["paths"]["get-payment-data"] = "/mpoint/masterpass/get-payment-data";
$aHTTP_CONN_INFO[15]["paths"]["callback"] = "/mpoint/masterpass/callback";

/**
 * Connection info for connecting to Data Cash
 */
$aHTTP_CONN_INFO[17]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[17]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[17]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[17]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[17]["paths"]["initialize"] = "/mpoint/data-cash/initialize";
$aHTTP_CONN_INFO[17]["paths"]["auth"] = "/mpoint/data-cash/authorize-payment";
$aHTTP_CONN_INFO[17]["paths"]["capture"] = "/mpoint/data-cash/capture";
$aHTTP_CONN_INFO[17]["paths"]["status"] = "/mpoint/data-cash/status";
$aHTTP_CONN_INFO[17]["paths"]["cancel"] = "/mpoint/data-cash/cancel";
$aHTTP_CONN_INFO[17]["paths"]["refund"] = "/mpoint/data-cash/refund";

/**
 * Connection info for connecting to Data Cash
 */
$aHTTP_CONN_INFO[57]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[57]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[57]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[57]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[57]["paths"]["initialize"] = "/mpoint/data-cash/initialize";
$aHTTP_CONN_INFO[57]["paths"]["auth"] = "/mpoint/data-cash/authorize-payment";
$aHTTP_CONN_INFO[57]["paths"]["capture"] = "/mpoint/data-cash/capture";
$aHTTP_CONN_INFO[57]["paths"]["status"] = "/mpoint/data-cash/status";
$aHTTP_CONN_INFO[57]["paths"]["cancel"] = "/mpoint/data-cash/cancel";
$aHTTP_CONN_INFO[57]["paths"]["refund"] = "/mpoint/data-cash/refund";

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
$aHTTP_CONN_INFO[20]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[20]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[20]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[20]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[20]["paths"]["initialize"] = "/mpoint/android-pay/initialize";
$aHTTP_CONN_INFO[20]["paths"]["get-payment-data"] = "/mpoint/android-pay/get-payment-data";

/**
 * Connection info for connecting to GlobalCollect
 */
$aHTTP_CONN_INFO[21]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[21]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[21]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[21]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[21]["paths"]["initialize"] = "/mpoint/global-collect/initialize";
$aHTTP_CONN_INFO[21]["paths"]["auth"] = "/mpoint/global-collect/authorize";
$aHTTP_CONN_INFO[21]["paths"]["capture"] = "/mpoint/global-collect/capture";
$aHTTP_CONN_INFO[21]["paths"]["status"] = "/mpoint/global-collect/status";
$aHTTP_CONN_INFO[21]["paths"]["refund"] = "/mpoint/global-collect/refund";
$aHTTP_CONN_INFO[21]["paths"]["callback"] = "/mpoint/global-collect/callback";
$aHTTP_CONN_INFO[21]["paths"]["cancel"] = "/mpoint/global-collect/cancel";
$aHTTP_CONN_INFO[21]["paths"]["auth-complete"] = "/mpoint/global-collect/auth-complete";


/**
 * Connection info for connecting to SecureTrading
 */
$aHTTP_CONN_INFO[22]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[22]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[22]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[22]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[22]["paths"]["initialize"] = "/mpoint/secure-trading/initialize";
$aHTTP_CONN_INFO[22]["paths"]["auth"] = "/mpoint/secure-trading/authorize-payment";
$aHTTP_CONN_INFO[22]["paths"]["capture"] = "/mpoint/secure-trading/capture";
$aHTTP_CONN_INFO[22]["paths"]["status"] = "/mpoint/secure-trading/status";
$aHTTP_CONN_INFO[22]["paths"]["refund"] = "/mpoint/secure-trading/refund";
$aHTTP_CONN_INFO[22]["paths"]["callback"] = "/mpoint/secure-trading/callback";
$aHTTP_CONN_INFO[22]["paths"]["cancel"] = "/mpoint/secure-trading/cancel";


/**
 * Connection info for connecting to PayFort
 */
$aHTTP_CONN_INFO[23]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[23]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[23]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[23]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[23]["paths"]["initialize"] = "/mpoint/payfort/initialize";
$aHTTP_CONN_INFO[23]["paths"]["auth"] = "/mpoint/payfort/authorize-payment";
$aHTTP_CONN_INFO[23]["paths"]["capture"] = "/mpoint/payfort/capture";
$aHTTP_CONN_INFO[23]["paths"]["refund"] = "/mpoint/payfort/refund";
$aHTTP_CONN_INFO[23]["paths"]["status"] = "/mpoint/payfort/status";
$aHTTP_CONN_INFO[23]["paths"]["cancel"] = "/mpoint/payfort/cancel";
$aHTTP_CONN_INFO[23]["paths"]["callback"] = "/mpoint/payfort/callback";


/**
 * Connection info for connecting to PayPal
 */
$aHTTP_CONN_INFO[24]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[24]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[24]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[24]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[24]["paths"]["initialize"] = "/mpoint/paypal/initialize";
$aHTTP_CONN_INFO[24]["paths"]["auth"] = "/mpoint/paypal/authorize-payment";
$aHTTP_CONN_INFO[24]["paths"]["capture"] = "/mpoint/paypal/capture";
$aHTTP_CONN_INFO[24]["paths"]["refund"] = "/mpoint/paypal/refund";
$aHTTP_CONN_INFO[24]["paths"]["cancel"] = "/mpoint/paypal/cancel";
$aHTTP_CONN_INFO[24]["paths"]["status"] = "/mpoint/paypal/status";


/**
 * Connection info for connecting to CCAvenue
 */
$aHTTP_CONN_INFO[25]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[25]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[25]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[25]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[25]["paths"]["initialize"] = "/mpoint/ccavenue/initialize";
$aHTTP_CONN_INFO[25]["paths"]["auth"] = "/mpoint/ccavenue/authorize-payment";
$aHTTP_CONN_INFO[25]["paths"]["capture"] = "/mpoint/ccavenue/capture";
$aHTTP_CONN_INFO[25]["paths"]["refund"] = "/mpoint/ccavenue/refund";
$aHTTP_CONN_INFO[25]["paths"]["status"] = "/mpoint/ccavenue/status";
$aHTTP_CONN_INFO[25]["paths"]["cancel"] = "/mpoint/ccavenue/cancel";
$aHTTP_CONN_INFO[25]["paths"]["callback"] = "/mpoint/ccavenue/callback";

/**
 * Connection info for connecting to 2C2P
 */
$aHTTP_CONN_INFO[26]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[26]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[26]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[26]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[26]["paths"]["initialize"] = "/mpoint/2c2p/initialize";
$aHTTP_CONN_INFO[26]["paths"]["auth"] = "/mpoint/2c2p/authorize-payment";
$aHTTP_CONN_INFO[26]["paths"]["capture"] = "/mpoint/2c2p/capture";
$aHTTP_CONN_INFO[26]["paths"]["refund"] = "/mpoint/2c2p/refund";
$aHTTP_CONN_INFO[26]["paths"]["status"] = "/mpoint/2c2p/status";
$aHTTP_CONN_INFO[26]["paths"]["cancel"] = "/mpoint/2c2p/cancel";
$aHTTP_CONN_INFO[26]["paths"]["callback"] = "/mpoint/2c2p/callback";


/**
 * Connection info for connecting to PublicBank
 */
$aHTTP_CONN_INFO[28]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[28]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[28]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[28]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[28]["paths"]["initialize"] = "/mpoint/public-bank/initialize";
$aHTTP_CONN_INFO[28]["paths"]["auth"] = "/mpoint/public-bank/authorize-payment";
$aHTTP_CONN_INFO[28]["paths"]["capture"] = "/mpoint/public-bank/capture";
$aHTTP_CONN_INFO[28]["paths"]["refund"] = "/mpoint/public-bank/refund";
$aHTTP_CONN_INFO[28]["paths"]["status"] = "/mpoint/public-bank/status";
$aHTTP_CONN_INFO[28]["paths"]["cancel"] = "/mpoint/public-bank/cancel";
$aHTTP_CONN_INFO[28]["paths"]["callback"] = "/mpoint/public-bank/callback";

/**
 * Connection info for connecting to MayBank
 */
$aHTTP_CONN_INFO[27]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[27]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[27]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[27]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[27]["paths"]["initialize"] = "/mpoint/maybank/initialize";
$aHTTP_CONN_INFO[27]["paths"]["auth"] = "/mpoint/maybank/authorize-payment";
$aHTTP_CONN_INFO[27]["paths"]["capture"] = "/mpoint/maybank/capture";
$aHTTP_CONN_INFO[27]["paths"]["refund"] = "/mpoint/maybank/refund";
$aHTTP_CONN_INFO[27]["paths"]["status"] = "/mpoint/maybank/status";
$aHTTP_CONN_INFO[27]["paths"]["cancel"] = "/mpoint/maybank/cancel";
$aHTTP_CONN_INFO[27]["paths"]["callback"] = "/mpoint/maybank/callback";

/**
 * Connection info for connecting to AliPay
 */
$aHTTP_CONN_INFO[30]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[30]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[30]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[30]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[30]["paths"]["initialize"] = "/mpoint/alipay/initialize";
$aHTTP_CONN_INFO[30]["paths"]["refund"] = "/mpoint/alipay/refund";
$aHTTP_CONN_INFO[30]["paths"]["status"] = "/mpoint/alipay/status";
$aHTTP_CONN_INFO[30]["paths"]["callback"] = "/mpoint/alipay/callback";

/**
 * Connection info for connecting to POLi
 */
$aHTTP_CONN_INFO[32]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[32]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[32]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[32]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[32]["paths"]["initialize"] = "/mpoint/poli/initialize";
$aHTTP_CONN_INFO[32]["paths"]["callback"] = "/mpoint/poli/callback";

/**
 * Connection info for connecting to QIWI Wallet
 */
$aHTTP_CONN_INFO[31]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[31]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[31]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[31]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[31]["paths"]["initialize"] = "/mpoint/qiwi/initialize";
$aHTTP_CONN_INFO[31]["paths"]["refund"] = "/mpoint/qiwi/refund";
$aHTTP_CONN_INFO[31]["paths"]["status"] = "/mpoint/qiwi/status";
$aHTTP_CONN_INFO[31]["paths"]["cancel"] = "/mpoint/qiwi/cancel";
$aHTTP_CONN_INFO[31]["paths"]["callback"] = "/mpoint/qiwi/callback";


/**
 * Connection info for connecting to Klarna
 */
$aHTTP_CONN_INFO[37]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[37]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[37]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[37]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[37]["paths"]["initialize"] = "/mpoint/klarna/initialize";
$aHTTP_CONN_INFO[37]["paths"]["auth"] = "/mpoint/klarna/authorize-payment";
$aHTTP_CONN_INFO[37]["paths"]["refund"] = "/mpoint/klarna/refund";
$aHTTP_CONN_INFO[37]["paths"]["status"] = "/mpoint/klarna/status";
$aHTTP_CONN_INFO[37]["paths"]["cancel"] = "/mpoint/klarna/cancel";
$aHTTP_CONN_INFO[37]["paths"]["callback"] = "/mpoint/klarna/callback";



/**
 * Connection info for connecting to Trustly
 */
$aHTTP_CONN_INFO[39]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[39]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[39]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[39]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[39]["paths"]["initialize"] = "/mpoint/trustly/initialize";
$aHTTP_CONN_INFO[39]["paths"]["auth"] = "/mpoint/trustly/authorize-payment";
$aHTTP_CONN_INFO[39]["paths"]["refund"] = "/mpoint/trustly/refund";
$aHTTP_CONN_INFO[39]["paths"]["status"] = "/mpoint/trustly/status";
$aHTTP_CONN_INFO[39]["paths"]["cancel"] = "/mpoint/trustly/cancel";
$aHTTP_CONN_INFO[39]["paths"]["callback"] = "/mpoint/trustly/callback";

/**
 * Connection info for connecting to MobilePay Online
 */
$aHTTP_CONN_INFO[33]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[33]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[33]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[33]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[33]["paths"]["initialize"] = "/mpoint/mobilepay-online/initialize";
$aHTTP_CONN_INFO[33]["paths"]["auth"] = "/mpoint/mobilepay-online/authorize-payment";

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
$aHTTP_CONN_INFO["mvault"]["mvault-contenttype"] = "application/xml";
$aHTTP_CONN_INFO["mvault"]["paths"]["save-card"] = "/mvault/save-card";
$aHTTP_CONN_INFO["mvault"]["paths"]["get-card-details"] = "/mvault/get-card-details";

/**
 * Connection info for connecting to Paytabs
 */
$aHTTP_CONN_INFO[38]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[38]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[38]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[38]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[38]["paths"]["initialize"] = "/mpoint/paytabs/initialize";
$aHTTP_CONN_INFO[38]["paths"]["refund"] = "/mpoint/paytabs/refund";

/**
 * Connection info for connecting to 2C2P ALC
 */
$aHTTP_CONN_INFO[40]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[40]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[40]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[40]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[40]["paths"]["initialize"] = "/mpoint/2c2p-alc/initialize";
$aHTTP_CONN_INFO[40]["paths"]["auth"] = "/mpoint/2c2p-alc/authorize-payment";
$aHTTP_CONN_INFO[40]["paths"]["capture"] = "/mpoint/2c2p-alc/capture";
$aHTTP_CONN_INFO[40]["paths"]["refund"] = "/mpoint/2c2p-alc/void";
$aHTTP_CONN_INFO[40]["paths"]["cancel"] = "/mpoint/2c2p-alc/void";
$aHTTP_CONN_INFO[40]["paths"]["void"] = "/mpoint/2c2p-alc/void";
$aHTTP_CONN_INFO[40]["paths"]["callback"] = "/mpoint/2c2p-alc/callback";
$aHTTP_CONN_INFO[40]["paths"]["post-status"] = "/mpoint/2c2p-alc/post-status";
$aHTTP_CONN_INFO[40]["paths"]["status"] = "/mpoint/2c2p-alc/status";


/**
 * Connection info for connecting to Citcon
 */
$aHTTP_CONN_INFO[41]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[41]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[41]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[41]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[41]["paths"]["initialize"] = "/mpoint/citcon/initialize";
$aHTTP_CONN_INFO[41]["paths"]["refund"] = "/mpoint/citcon/refund";
$aHTTP_CONN_INFO[41]["paths"]["status"] = "/mpoint/citcon/status";


/**
 * Connection info for connecting to AliPay Chinese
 */
$aHTTP_CONN_INFO[43]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[43]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[43]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[43]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[43]["paths"]["initialize"] = "/mpoint/alipay-chinese/initialize";
$aHTTP_CONN_INFO[43]["paths"]["refund"] = "/mpoint/alipay-chinese/refund";
$aHTTP_CONN_INFO[43]["paths"]["status"] = "/mpoint/alipay-chinese/status";
$aHTTP_CONN_INFO[43]["paths"]["callback"] = "/mpoint/alipay-chinese/callback";

/**
 * Connection info for connecting to Google Pay
 */
$aHTTP_CONN_INFO[44]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[44]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[44]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[44]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[44]["paths"]["get-payment-data"] = "/mpoint/google-pay/get-payment-data";
$aHTTP_CONN_INFO[44]["paths"]["initialize"] = "/mpoint/google-pay/initialize";
/**
 * Connection info for connecting to PPRO
 */
$aHTTP_CONN_INFO[46]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[46]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[46]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[46]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[46]["paths"]["initialize"] = "/mpoint/ppro/initialize";
$aHTTP_CONN_INFO[46]["paths"]["auth"] = "/mpoint/ppro/authorize-payment";
$aHTTP_CONN_INFO[46]["paths"]["refund"] = "/mpoint/ppro/refund";
$aHTTP_CONN_INFO[46]["paths"]["callback"] = "/mpoint/ppro/callback";
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
$aHTTP_CONN_INFO["uatp"]["paths"]["cancel"] = "/mpoint/uatp/cancel-suvtp";


/**
 * Connection info for connecting to eGHL-FPX
 */
$aHTTP_CONN_INFO[51]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[51]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[51]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[51]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[51]["paths"]["initialize"] = "/mpoint/eghl/initialize";
$aHTTP_CONN_INFO[51]["paths"]["get-payment-methods"] = "/mpoint/eghl/get-payment-methods";



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
 * Connection info for connecting to PayU aggretor
 */
$aHTTP_CONN_INFO[53]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[53]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[53]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[53]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[53]["paths"]["initialize"] = "/mpoint/aggregator/payu/initialize";
$aHTTP_CONN_INFO[53]["paths"]["auth"] = "/mpoint/aggregator/payu/authorize-payment";
$aHTTP_CONN_INFO[53]["paths"]["refund"] = "/mpoint/aggregator/payu/refund";
$aHTTP_CONN_INFO[53]["paths"]["callback"] = "/mpoint/aggregator/payu/callback";
$aHTTP_CONN_INFO[53]["paths"]["get-payment-methods"] = "/mpoint/aggregator/payu/get-payment-methods";

/**
 * Connection info for connecting to Cielo
 */
$aHTTP_CONN_INFO[54]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[54]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[54]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[54]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[54]["paths"]["auth"] = "/mpsp/cielo/authorize-payment";
$aHTTP_CONN_INFO[54]["paths"]["capture"] = "/mpsp/cielo/capture";
$aHTTP_CONN_INFO[54]["paths"]["initialize"] = "/mpsp/cielo/initialize";
$aHTTP_CONN_INFO[54]["paths"]["refund"] = "/mpsp/cielo/void";
$aHTTP_CONN_INFO[54]["paths"]["cancel"] = "/mpsp/cielo/void";


/**
 * Connection info for connecting to Global Payments
 */
$aHTTP_CONN_INFO[56]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[56]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[56]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[56]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[56]["paths"]["auth"] = "/mpoint/global-payments/authorize-payment";
$aHTTP_CONN_INFO[56]["paths"]["capture"] = "/mpoint/global-payments/capture";
$aHTTP_CONN_INFO[56]["paths"]["initialize"] = "/mpoint/global-payments/initialize";
$aHTTP_CONN_INFO[56]["paths"]["refund"] = "/mpoint/global-payments/refund";
$aHTTP_CONN_INFO[56]["paths"]["cancel"] = "/mpoint/global-payments/cancel";
$aHTTP_CONN_INFO[56]["paths"]["authenticate"] = "/mpoint/authenticate";


/**
 * Connection info for connecting to EZY fraud gateway
 */
$aHTTP_CONN_INFO[60]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[60]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[60]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[60]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[60]["paths"]["fraud-check"] = "/fraud/ezy/check-fraud-status";

/**
 * Connection info for connecting to VeriTrans4G
 */
$aHTTP_CONN_INFO[59]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[59]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[59]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[59]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[59]["paths"]["initialize"] = "/mpoint/veritrans4g/initialize";
$aHTTP_CONN_INFO[59]["paths"]["auth"] = "/mpoint/veritrans4g/authorize-payment";
$aHTTP_CONN_INFO[59]["paths"]["capture"] = "/mpoint/veritrans4g/capture";
$aHTTP_CONN_INFO[59]["paths"]["refund"] = "/mpoint/veritrans4g/refund";
$aHTTP_CONN_INFO[59]["paths"]["cancel"] = "/mpoint/veritrans4g/cancel";
$aHTTP_CONN_INFO[59]["paths"]["callback"] = "/mpoint/veritrans4g/callback";


/**
 * Connection info for connecting to DragonPay
 */
$aHTTP_CONN_INFO[61]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[61]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[61]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[61]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[61]["paths"]["initialize"] = "/mpoint/aggregator/dragonpay/initialize";
$aHTTP_CONN_INFO[61]["paths"]["auth"] = "/mpoint/aggregator/dragonpay/authorize-payment";
$aHTTP_CONN_INFO[61]["paths"]["status"] = "/mpoint/aggregator/dragonpay/status";
$aHTTP_CONN_INFO[61]["paths"]["callback"] = "/mpoint/aggregator/dragonpay/callback";



/*
 * Connection info for connecting to Data Cash
 */
$aHTTP_CONN_INFO[58]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[58]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[58]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[58]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[58]["paths"]["initialize"] = "/mpoint/cellulant/initialize";
$aHTTP_CONN_INFO[58]["paths"]["auth"] = "/mpoint/cellulant/authorize-payment";
$aHTTP_CONN_INFO[58]["paths"]["get-payment-options"] = "/mpoint/cellulant/GetPaymentOptions";
$aHTTP_CONN_INFO[58]["paths"]["status"] = "/mpoint/cellulant/status";
$aHTTP_CONN_INFO[58]["paths"]["acknowledge-payments"] = "/mpoint/cellulant/acknowledge-payments";
$aHTTP_CONN_INFO[58]["paths"]["refund"] = "/mpoint/cellulant/refund";

/**
 * Connection info for connecting to FirstData
 */
$aHTTP_CONN_INFO[62]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[62]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[62]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[62]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[62]["paths"]["auth"] = "/mpoint/first-data/authorize-payment";
$aHTTP_CONN_INFO[62]["paths"]["capture"] = "/mpoint/first-data/capture";
$aHTTP_CONN_INFO[62]["paths"]["initialize"] = "/mpoint/first-data/initialize";
$aHTTP_CONN_INFO[62]["paths"]["refund"] = "/mpoint/first-data/refund";
$aHTTP_CONN_INFO[62]["paths"]["cancel"] = "/mpoint/first-data/cancel";
$aHTTP_CONN_INFO[62]["paths"]["status"] = "/mpoint/first-data/status";
$aHTTP_CONN_INFO[62]["paths"]["authenticate"] = "/mpoint/authenticate";
/*
 * Connection info for connecting to Routing Service
 */
$aHTTP_CONN_INFO["routing-service"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["routing-service"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["routing-service"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["routing-service"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["routing-service"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["routing-service"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["routing-service"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["routing-service"]["paths"]["get-payment-methods"] = "/crs/routingservice/get-payment-methods";
$aHTTP_CONN_INFO["routing-service"]["paths"]["get-routes"] = "/crs/routingservice/get-routes";
/*
 * Connection info for connecting to Foreign Exchange
 */
$aHTTP_CONN_INFO["foreign-exchange"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["foreign-exchange"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["foreign-exchange"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["foreign-exchange"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["foreign-exchange"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["foreign-exchange"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["foreign-exchange"]["contenttype"] = "application/xml";
$aHTTP_CONN_INFO["foreign-exchange"]["paths"]["callback"] = "/foreignexchange/notify";
/**
 * Connection info for connecting to CYBS
 */
$aHTTP_CONN_INFO[63]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[63]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[63]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[63]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[63]["paths"]["auth"] = "/mpoint/global-payments/authorize-payment";
$aHTTP_CONN_INFO[63]["paths"]["capture"] = "/mpoint/global-payments/capture";
$aHTTP_CONN_INFO[63]["paths"]["initialize"] = "/mpoint/global-payments/initialize";
$aHTTP_CONN_INFO[63]["paths"]["refund"] = "/mpoint/global-payments/refund";
$aHTTP_CONN_INFO[63]["paths"]["cancel"] = "/mpoint/global-payments/cancel";
$aHTTP_CONN_INFO[63]["paths"]["fraud-check"] = "/fraud/cybersource/fraud-check";
$aHTTP_CONN_INFO[63]["paths"]["callback"] = "/fraud/cybersource/fraud-check";
$aHTTP_CONN_INFO[63]["paths"]["authenticate"] = "/mpoint/authenticate";

/**
 * Connection info for connecting to CYBS fraud
 */
$aHTTP_CONN_INFO[64]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[64]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[64]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[64]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[64]["paths"]["fraud-check"] = "/fraud/cybersource/fraud-check";
$aHTTP_CONN_INFO[64]["paths"]["callback"] = "/fraud/cybersource/fraud-check";

/**
 * Connection info for connecting to GrabPay
 */
$aHTTP_CONN_INFO[67]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[67]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[67]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[67]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[67]["paths"]["capture"] = "/mpoint/grab-pay/capture";
$aHTTP_CONN_INFO[67]["paths"]["initialize"] = "/mpoint/grab-pay/initialize";
$aHTTP_CONN_INFO[67]["paths"]["refund"] = "/mpoint/grab-pay/refund";
$aHTTP_CONN_INFO[67]["paths"]["status"] = "/mpoint/grab-pay/status";




/**
 * Connection info for connecting to CEBU-RMFSS
 */
$aHTTP_CONN_INFO[65]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[65]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[65]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[65]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[65]["paths"]["fraud-check"] = "/fraud/cebu-rmfss/check-fraud-status";


/**
 * Connection info for connecting to SWISH
 */
$aHTTP_CONN_INFO[66]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[66]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[66]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[66]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[66]["paths"]["initialize"] = "/mpoint/apm/swish/initialize";
$aHTTP_CONN_INFO[66]["paths"]["auth"] = "/mpoint/apm/swish/authorize-payment";
$aHTTP_CONN_INFO[66]["paths"]["refund"] = "/mpoint/apm/swish/refund";
$aHTTP_CONN_INFO[66]["paths"]["callback"] = "/mpoint/apm/swish/callback";
$aHTTP_CONN_INFO[66]["paths"]["callback"] = "/mpoint/apm/swish/failed-txn-refund-callback";

/**
 * Connection info for connecting to Travel Fund
 */
$aHTTP_CONN_INFO["travel-fund"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["travel-fund"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["travel-fund"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["travel-fund"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["travel-fund"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["travel-fund"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["travel-fund"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["travel-fund"]["paths"]["redeem"] = "/mpoint/travel-fund/redeem";
$aHTTP_CONN_INFO["travel-fund"]["paths"]["refund"] = "/mpoint/travel-fund/refund";

/**
 * Connection info for connecting to Paymaya
 */
$aHTTP_CONN_INFO[68]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[68]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[68]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[68]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[68]["paths"]["initialize"] = "/mpoint/apm/paymaya/initialize";
$aHTTP_CONN_INFO[68]["paths"]["callback"] = "/mpoint/apm/paymaya/callback";
$aHTTP_CONN_INFO[68]["paths"]["void"] = "/mpoint/apm/paymaya/void";
$aHTTP_CONN_INFO[68]["paths"]["refund"] = "/mpoint/apm/paymaya/void";
$aHTTP_CONN_INFO[68]["paths"]["cancel"] = "/mpoint/apm/paymaya/void";
$aHTTP_CONN_INFO[68]["paths"]["status"] = "/mpoint/apm/paymaya/status";

/**
 * Connection info for connecting to SSO
 */
$aHTTP_CONN_INFO["mconsole"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["mconsole"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["mconsole"]["port"] = $aHTTP_CONN_INFO["mesb"]["port"];
$aHTTP_CONN_INFO["mconsole"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["mconsole"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["mconsole"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["mconsole"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["mconsole"]["paths"]["single-sign-on"] = "/mconsole/single-sign-on";

/**
 * Connection info for connecting to MPGS
 */
$aHTTP_CONN_INFO[72]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[72]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[72]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[72]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[72]["paths"]["initialize"] = "/mpoint/mpgs/initialize";
$aHTTP_CONN_INFO[72]["paths"]["auth"] = "/mpoint/mpgs/authorize-payment";
$aHTTP_CONN_INFO[72]["paths"]["capture"] = "/mpoint/mpgs/capture";
$aHTTP_CONN_INFO[72]["paths"]["status"] = "/mpoint/mpgs/status";
$aHTTP_CONN_INFO[72]["paths"]["cancel"] = "/mpoint/mpgs/cancel";
$aHTTP_CONN_INFO[72]["paths"]["refund"] = "/mpoint/mpgs/refund";

/**
 * Connection info for connecting to SafetyPay
 */
$aHTTP_CONN_INFO[70]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[70]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[70]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[70]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[70]["paths"]["initialize"] = "/mpoint/aggregator/safetypay/initialize";
$aHTTP_CONN_INFO[70]["paths"]["callback"] = "/mpoint/aggregator/safetypay/callback";
$aHTTP_CONN_INFO[70]["paths"]["void"] = "/mpoint/aggregator/safetypay/void";
$aHTTP_CONN_INFO[70]["paths"]["refund"] = "/mpoint/aggregator/safetypay/refund";
$aHTTP_CONN_INFO[70]["paths"]["cancel"] = "/mpoint/aggregator/safetypay/void";
$aHTTP_CONN_INFO[70]["paths"]["status"] = "/mpoint/aggregator/safetypay/status";
$aHTTP_CONN_INFO[70]["paths"]["get-payment-methods"] = "/mpoint/aggregator/safetypay/get-payment-methods";
$aHTTP_CONN_INFO[70]["paths"]["generate-receipt"] = "/mpoint/generate-receipt";

/**
 * Connection info for connecting to Paymaya-Acq
 */
$aHTTP_CONN_INFO[73]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[73]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[73]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[73]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[73]["paths"]["initialize"] = "/mpsp/paymaya-acq/initialize";
$aHTTP_CONN_INFO[73]["paths"]["auth"] = "/mpsp/paymaya-acq/authorize-payment";
$aHTTP_CONN_INFO[73]["paths"]["refund"] = "/mpsp/paymaya-acq/refund";
$aHTTP_CONN_INFO[73]["paths"]["status"] = "/mpsp/paymaya-acq/status";
$aHTTP_CONN_INFO[73]["paths"]["void"] = "/mpsp/paymaya-acq/refund";

/**
 * Connection info for connecting to Stripe
 */
$aHTTP_CONN_INFO[10]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[10]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[10]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[10]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[10]["paths"]["initialize"] = "/mpoint/aggregator/stripe/initialize";
$aHTTP_CONN_INFO[10]["paths"]["auth"] = "/mpoint/aggregator/stripe/authorize-payment";
$aHTTP_CONN_INFO[10]["paths"]["capture"] = "/mpoint/aggregator/stripe/capture";
$aHTTP_CONN_INFO[10]["paths"]["refund"] = "/mpoint/aggregator/stripe/refund";
$aHTTP_CONN_INFO[10]["paths"]["cancel"] = "/mpoint/aggregator/stripe/cancel";
$aHTTP_CONN_INFO[10]["paths"]["callback"] = "/mpoint/aggregator/stripe/callback";
$aHTTP_CONN_INFO[10]["paths"]["status"] = "/mpoint/aggregator/stripe/status";

/**
 * Connection info for connecting to Nmi Credomatic
 */
$aHTTP_CONN_INFO[74]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[74]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[74]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[74]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[74]["paths"]["initialize"] = "/mpoint/psp/nmi-credomatic/initialize";
$aHTTP_CONN_INFO[74]["paths"]["auth"] = "/mpoint/psp/nmi-credomatic/authorize-payment";
$aHTTP_CONN_INFO[74]["paths"]["capture"] = "/mpoint/psp/nmi-credomatic/capture";
$aHTTP_CONN_INFO[74]["paths"]["refund"] = "/mpoint/psp/nmi-credomatic/refund";
$aHTTP_CONN_INFO[74]["paths"]["cancel"] = "/mpoint/psp/nmi-credomatic/cancel";
$aHTTP_CONN_INFO[74]["paths"]["callback"] = "/mpoint/psp/nmi-credomatic/callback";
$aHTTP_CONN_INFO[74]["paths"]["status"] = "/mpoint/psp/nmi-credomatic/status";
$aHTTP_CONN_INFO[74]["paths"]["authenticate"] = "/mpoint/authenticate";

/**
 * Connection info for connecting to AUB
 */
$aHTTP_CONN_INFO[75]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO[75]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO[75]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO[75]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO[75]["paths"]["initialize"] = "/mpoint/aggregator/aub/initialize";
$aHTTP_CONN_INFO[75]["paths"]["refund"] = "/mpoint/aggregator/aub/refund";
$aHTTP_CONN_INFO[75]["paths"]["callback"] = "/mpoint/aggregator/aub/callback";
$aHTTP_CONN_INFO[75]["paths"]["status"] = "/mpoint/aggregator/aub/status";
$aHTTP_CONN_INFO[75]["paths"]["redirect"] = "/mpoint/aggregator/aub/redirect";

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
$aGM_CONN_INFO["protocol"] = env("gomobile.protocol", "http");
$aGM_CONN_INFO["host"] = env("gomobile.host", "gomobile.cellpointmobile.com");
$aGM_CONN_INFO["port"] = env("gomobile.port", 8000);
$aGM_CONN_INFO["timeout"] = env("gomobile.timeout", 20);	// In seconds
$aGM_CONN_INFO["path"] = env("gomobile.path", "/");
$aGM_CONN_INFO["method"] = env("gomobile.method", "POST");
$aGM_CONN_INFO["contenttype"] = env("gomobile.contenttype", "text/xml");
$aGM_CONN_INFO["username"] = env("gomobile.username", "");		// Set from the Client Configuration
$aGM_CONN_INFO["password"] = env("gomobile.password", "");		// Set from the Client Configuration
$aGM_CONN_INFO["logpath"] = env("gomobile.logpath", sLOG_PATH);
/**
 * 1 - Write log entry to file
 * 2 - Output log entry to screen
 * 3 - Write log entry to file and output to screen
 *
 */
$aGM_CONN_INFO["mode"] = env("gomobile.mode", 1);


//Connection info connecting to the same host, used while running unit tests
$aCPM_CONN_INFO["protocol"] = env("mpoint.protocol", "http");
$aCPM_CONN_INFO["host"] = env("mpoint.host", "mpoint.local.cellpointmobile.com");
$aCPM_CONN_INFO["port"] = env("mpoint.port", 80);
$aCPM_CONN_INFO["timeout"] = env("mpoint.timeout", 20);
$aCPM_CONN_INFO["path"] = env("mpoint.path", "/callback/cpm.php");
$aCPM_CONN_INFO["method"] = env("mpoint.method", "POST");
$aCPM_CONN_INFO["contenttype"] = env("mpoint.contenttype", "application/x-www-form-urlencoded");


/**
 *
 *     Configuration for HPP
 *
 */

$aHTTP_CONN_INFO["hpp"]["protocol"] = "https";

/**
 * Message Queue Provider Information
 *
 */
$aMessage_Queue_Provider_info['provider'] = env("messagequeue.provider", "googlepubsub");
//First Preference to KeyFile
$aMessage_Queue_Provider_info['keyfile'] = env("messagequeue.keyfile", "");
$aMessage_Queue_Provider_info['keyfilepath'] = env("messagequeue.keyfilepath", "");
$aMessage_Queue_Provider_info['projectid'] =  env("messagequeue.projectid", "");
$aMessage_Queue_Provider_info['topicname'] =  env("messagequeue.topicname", "");



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