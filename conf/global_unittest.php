<?php

$aDB_CONN_INFO["mpoint"]["host"] = "localhost";
$aDB_CONN_INFO["mpoint"]["port"] = 5432;
//$aDB_CONN_INFO["mpoint"]["path"] = "mpoint_". TESTDB_TOKEN;
$aDB_CONN_INFO["mpoint"]["path"] = "mpoint";
$aDB_CONN_INFO["mpoint"]["username"] = "postgres";
$aDB_CONN_INFO["mpoint"]["password"] = "postgres";
$aDB_CONN_INFO["mpoint"]["class"] = "PostGreSQL";
$aDB_CONN_INFO["mpoint"]["timeout"] = 10;
$aDB_CONN_INFO["mpoint"]["charset"] = "UTF8";
$aDB_CONN_INFO["mpoint"]["connmode"] = "normal";
$aDB_CONN_INFO["mpoint"]["errorpath"] = sLOG_PATH ."db_error_".".log";
$aDB_CONN_INFO["mpoint"]["errorhandling"] = 3;
$aDB_CONN_INFO["mpoint"]["exectime"] = 0.3;
$aDB_CONN_INFO["mpoint"]["execpath"] = sLOG_PATH ."db_exectime_".".log";
$aDB_CONN_INFO["mpoint"]["keycase"] = CASE_UPPER;
$aDB_CONN_INFO["mpoint"]["debuglevel"] = 2;
$aDB_CONN_INFO["mpoint"]["method"] = 1;

$aDB_CONN_INFO["session"]["username"] = "postgres";
$aDB_CONN_INFO["session"]["password"] = "postgres";

$aMPOINT_CONN_INFO["protocol"] = "http";
$aMPOINT_CONN_INFO["host"] = "mpoint.local.cellpointmobile.com";
$aMPOINT_CONN_INFO["port"] = 80;
$aMPOINT_CONN_INFO["timeout"] = 20;	// In seconds
$aMPOINT_CONN_INFO["path"] = "/";
$aMPOINT_CONN_INFO["method"] = "POST";
$aMPOINT_CONN_INFO["contenttype"] = "text/xml";
$aMPOINT_CONN_INFO["username"] = "";		// Set from the Client Configuration
$aMPOINT_CONN_INFO["password"] = "";		// Set from the Client Configuration
$aMPOINT_CONN_INFO["logpath"] = sLOG_PATH;

$aHTTP_CONN_INFO["dibs"]["protocol"] = "http";
$aHTTP_CONN_INFO["dibs"]["host"] = "mpoint.local.cellpointmobile.com";
$aHTTP_CONN_INFO["dibs"]["port"] = 80;
$aHTTP_CONN_INFO["dibs"]["paths"]["pay"] = "/_test/simulators/dibs/pay.php";
$aHTTP_CONN_INFO["dibs"]["paths"]["auth-ticket"] = "/_test/simulators/dibs/auth.php";
$aHTTP_CONN_INFO["dibs"]["paths"]["capture"] = "/_test/simulators/dibs/capture.php";
$aHTTP_CONN_INFO["dibs"]["paths"]["cancel"] = "/_test/simulators/dibs/cancel.php";
$aHTTP_CONN_INFO["dibs"]["paths"]["refund"] = "/_test/simulators/dibs/refund.php";
$aHTTP_CONN_INFO["dibs"]["paths"]["status"] = "/_test/simulators/dibs/status.php";
$aHTTP_CONN_INFO["dibs"]["path"] = $aHTTP_CONN_INFO["dibs"]["paths"]["pay"];

$aHTTP_CONN_INFO["mobilepay"]["protocol"] = "http";
$aHTTP_CONN_INFO["mobilepay"]["host"] = "mpoint.local.cellpointmobile.com";
$aHTTP_CONN_INFO["mobilepay"]["port"] = 80;
//$aHTTP_CONN_INFO["mobilepay"]["paths"]["auth"] = "/_test/simulator/dibs/ticket_auth.php";
$aHTTP_CONN_INFO["mobilepay"]["paths"]["capture"] = "/_test/simulators/mobilepay/capture.php";
$aHTTP_CONN_INFO["mobilepay"]["paths"]["status"] = "/_test/simulators/mobilepay/status.php";
$aHTTP_CONN_INFO["mobilepay"]["paths"]["cancel"] = "/_test/simulators/mobilepay/cancel.php";
$aHTTP_CONN_INFO["mobilepay"]["paths"]["refund"] = "/_test/simulators/mobilepay/refund.php";
//$aHTTP_CONN_INFO["dibs"]["paths"]["status"] = "/_test/simulators/dibs/transstatus.php";

$aHTTP_CONN_INFO["netaxept"]["protocol"] = "http";
$aHTTP_CONN_INFO["netaxept"]["host"] = "mpoint.local.cellpointmobile.com";
$aHTTP_CONN_INFO["netaxept"]["port"] = 80;
$aHTTP_CONN_INFO["netaxept"]["timeout"] = 120;
$aHTTP_CONN_INFO["netaxept"]["path"] = "/_test/simulators/netaxept/netaxept.php?wsdl";
$aHTTP_CONN_INFO["netaxept"]["method"] = "POST";
$aHTTP_CONN_INFO["netaxept"]["contenttype"] = "application/x-www-form-urlencoded";

$aHTTP_CONN_INFO["dsb"]["protocol"] = "http";
$aHTTP_CONN_INFO["dsb"]["host"] = "mpoint.local.cellpointmobile.com";
$aHTTP_CONN_INFO["dsb"]["port"] = 80;
$aHTTP_CONN_INFO["dsb"]["paths"]["redeem"] = "/_test/simulators/dsb/redeem.php";
$aHTTP_CONN_INFO["dsb"]["paths"]["callback"] = "/callback/general.php";
$aHTTP_CONN_INFO["dsb"]["paths"]["cancel"] = "/_test/simulators/mobilepay/cancel.php";


/**
 * Connection info for connecting to Amex
 */
$aHTTP_CONN_INFO["amex"]["protocol"] = "http";
$aHTTP_CONN_INFO["amex"]["host"] = "mpoint.local.cellpointmobile.com";
$aHTTP_CONN_INFO["amex"]["port"] = 80;
$aHTTP_CONN_INFO["amex"]["timeout"] = 120;
$aHTTP_CONN_INFO["amex"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["amex"]["paths"]["auth"] = "/_test/simulators/amex/auth.php";
$aHTTP_CONN_INFO["amex"]["paths"]["pay"] = "/_test/simulators/amex/pay.php";
$aHTTP_CONN_INFO["amex"]["paths"]["initialize"] = "/_test/simulators/amex/pay.php";
$aHTTP_CONN_INFO["amex"]["path"] = $aHTTP_CONN_INFO["amex"]["paths"]["pay"];

$aCPM_CONN_INFO = $aMPOINT_CONN_INFO;
$aCPM_CONN_INFO["contenttype"] = "application/x-www-form-urlencoded";

$aGM_CONN_INFO = $aMPOINT_CONN_INFO;
$aGM_CONN_INFO["path"] = "/_test/simulators/gomobile/send.php";
$aGM_CONN_INFO["contenttype"] = "text/xml; charset=ISO-8859-1";
$aGM_CONN_INFO["mode"] = 1;

/**
 * Connection info for connecting to Wire Card
 */
$aHTTP_CONN_INFO["wire-card"]["protocol"] = "http";
$aHTTP_CONN_INFO["wire-card"]["host"] = "mpoint.local.cellpointmobile.com";
$aHTTP_CONN_INFO["wire-card"]["port"] = 80;
$aHTTP_CONN_INFO["wire-card"]["timeout"] = 120;
$aHTTP_CONN_INFO["wire-card"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["wire-card"]["method"] = "POST";
$aHTTP_CONN_INFO["wire-card"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["wire-card"]["paths"]["initialize"] = "/_test/simulators/wire-card/pay.php";
$aHTTP_CONN_INFO["wire-card"]["paths"]["pay"] = "/_test/simulators/wire-card/pay.php";
$aHTTP_CONN_INFO["wire-card"]["paths"]["auth"] = "/_test/simulators/wire-card/auth.php";
$aHTTP_CONN_INFO["wire-card"]["paths"]["capture"] = "/_test/simulators/wire-card/capture.php";
$aHTTP_CONN_INFO["wire-card"]["paths"]["cancel"] = "/_test/simulators/mobilepay/cancel.php";
$aHTTP_CONN_INFO["wire-card"]["paths"]["refund"] = "/_test/simulators/mobilepay/refund.php";
unset($aHTTP_CONN_INFO["wire-card"]["paths"]["status"]);

/**
 * Connection info for connecting to 2C2P ALC
 */
$aHTTP_CONN_INFO["2c2p-alc"]["protocol"]    = 'http';
$aHTTP_CONN_INFO["2c2p-alc"]["host"]        = "mpoint.local.cellpointmobile.com";
$aHTTP_CONN_INFO["2c2p-alc"]["port"]        = 80;
$aHTTP_CONN_INFO["2c2p-alc"]["timeout"]     = 120;
$aHTTP_CONN_INFO["2c2p-alc"]["path"]        = ""; // Set by calling class
$aHTTP_CONN_INFO["2c2p-alc"]["method"]      = 'POST';
$aHTTP_CONN_INFO["2c2p-alc"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["2c2p-alc"]["paths"]["initialize"] = "/_test/simulators/2c2p-alc/pay.php";
$aHTTP_CONN_INFO["2c2p-alc"]["paths"]["auth"] = "/_test/simulators/2c2p-alc/auth.php";
$aHTTP_CONN_INFO["2c2p-alc"]["paths"]["capture"] = "/_test/simulators/2c2p-alc/capture.php";

/**
 * Connection info for connecting to First data
 */
$aHTTP_CONN_INFO["first-data"]["protocol"]    = 'http';
$aHTTP_CONN_INFO["first-data"]["host"]        = "mpoint.local.cellpointmobile.com";
$aHTTP_CONN_INFO["first-data"]["port"]        = 80;
$aHTTP_CONN_INFO["first-data"]["timeout"]     = 120;
$aHTTP_CONN_INFO["first-data"]["path"]        = ""; // Set by calling class
$aHTTP_CONN_INFO["first-data"]["method"]      = 'POST';
$aHTTP_CONN_INFO["first-data"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["first-data"]["paths"]["initialize"] = "/_test/simulators/first-data/pay.php";
$aHTTP_CONN_INFO["first-data"]["paths"]["auth"] = "/_test/simulators/first-data/auth.php";

/**
 * Connection info for connecting to DataCash
 */
$aHTTP_CONN_INFO["data-cash"]["protocol"] = "http";
$aHTTP_CONN_INFO["data-cash"]["host"] = "mpoint.local.cellpointmobile.com";
$aHTTP_CONN_INFO["data-cash"]["port"] = 80;
$aHTTP_CONN_INFO["data-cash"]["timeout"] = 120;
$aHTTP_CONN_INFO["data-cash"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["data-cash"]["paths"]["auth"] = "/_test/simulators/datacash/auth.php";
$aHTTP_CONN_INFO["data-cash"]["paths"]["pay"] = "/_test/simulators/datacash/pay.php";
$aHTTP_CONN_INFO["data-cash"]["paths"]["initialize"] = "/_test/simulators/datacash/pay.php";
$aHTTP_CONN_INFO["data-cash"]["path"] = $aHTTP_CONN_INFO["data-cash"]["paths"]["pay"];


/**
 * Connection info for connecting to VisaCheckout
 */
$aHTTP_CONN_INFO["visa-checkout"]["protocol"] = "http";
$aHTTP_CONN_INFO["visa-checkout"]["host"] = "mpoint.local.cellpointmobile.com";
$aHTTP_CONN_INFO["visa-checkout"]["port"] = 80;
$aHTTP_CONN_INFO["visa-checkout"]["timeout"] = 120;
$aHTTP_CONN_INFO["visa-checkout"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["visa-checkout"]["paths"]["get-payment-data"] = "/_test/simulators/visacheckout/get-payment-data.php";
$aHTTP_CONN_INFO["visa-checkout"]["paths"]["pay"] = "/_test/simulators/visacheckout/pay.php";
$aHTTP_CONN_INFO["visa-checkout"]["paths"]["initialize"] = "/_test/simulators/visacheckout/pay.php";
$aHTTP_CONN_INFO["visa-checkout"]["path"] = $aHTTP_CONN_INFO["visa-checkout"]["paths"]["pay"];

/**
 * Connection info for connecting to ApplePay
 */
$aHTTP_CONN_INFO["apple-pay"]["protocol"] = "http";
$aHTTP_CONN_INFO["apple-pay"]["host"] = "mpoint.local.cellpointmobile.com";
$aHTTP_CONN_INFO["apple-pay"]["port"] = 80;
$aHTTP_CONN_INFO["apple-pay"]["timeout"] = 120;
$aHTTP_CONN_INFO["apple-pay"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["apple-pay"]["paths"]["get-payment-data"] = "/_test/simulators/applepay/get-payment-data.php";
$aHTTP_CONN_INFO["apple-pay"]["paths"]["pay"] = "/_test/simulators/applepay/pay.php";
$aHTTP_CONN_INFO["apple-pay"]["paths"]["initialize"] = "/_test/simulators/applepay/pay.php";
$aHTTP_CONN_INFO["apple-pay"]["path"] = $aHTTP_CONN_INFO["apple-pay"]["paths"]["pay"];

/**
 * Connection info for connecting to DataCash
 */
$aHTTP_CONN_INFO["alipay"]["protocol"] = "http";
$aHTTP_CONN_INFO["alipay"]["host"] = "mpoint.local.cellpointmobile.com";
$aHTTP_CONN_INFO["alipay"]["port"] = 80;
$aHTTP_CONN_INFO["alipay"]["timeout"] = 120;
$aHTTP_CONN_INFO["alipay"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["alipay"]["paths"]["auth"] = "/_test/simulators/alipay/auth.php";
$aHTTP_CONN_INFO["alipay"]["paths"]["pay"] = "/_test/simulators/alipay/pay.php";
$aHTTP_CONN_INFO["alipay"]["paths"]["initialize"] = "/_test/simulators/alipay/pay.php";
$aHTTP_CONN_INFO["alipay"]["path"] = $aHTTP_CONN_INFO["alipay"]["paths"]["pay"];

/**
 * Connection info for connecting to worldpay
 */
$aHTTP_CONN_INFO["worldpay"]["protocol"] = "http";
$aHTTP_CONN_INFO["worldpay"]["host"] = "mpoint.local.cellpointmobile.com";
$aHTTP_CONN_INFO["worldpay"]["port"] = 80;
$aHTTP_CONN_INFO["worldpay"]["timeout"] = 120;
$aHTTP_CONN_INFO["worldpay"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["worldpay"]["method"] = "POST";
$aHTTP_CONN_INFO["worldpay"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["worldpay"]["paths"]["initialize"] = "/_test/simulators/wire-card/init.php";//Files are same for most of PSP
$aHTTP_CONN_INFO["worldpay"]["paths"]["auth"] = "/_test/simulators/wire-card/auth.php";//Files are same for most of PSP
$aHTTP_CONN_INFO["worldpay"]["paths"]["status"] = "/_test/simulators/status.php";
$aHTTP_CONN_INFO["worldpay"]["paths"]["authenticate"] = "/_test/simulators/modirum/authenticate.php";


/**
 * Connection info for connecting to Modirum MPI
 */
$aHTTP_CONN_INFO["modirummpi"]["protocol"] = "http";
$aHTTP_CONN_INFO["modirummpi"]["host"] = "mpoint.local.cellpointmobile.com";
$aHTTP_CONN_INFO["modirummpi"]["port"] = 80;
$aHTTP_CONN_INFO["modirummpi"]["timeout"] = 120;
$aHTTP_CONN_INFO["modirummpi"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["modirummpi"]["method"] = "POST";
$aHTTP_CONN_INFO["modirummpi"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["modirummpi"]["paths"]["authenticate"] = "/_test/simulators/modirum/modirum_authenticate.php";


/**
 * Connection info for connecting to UATP
 */
$aHTTP_CONN_INFO["uatp"]["protocol"] = "http";
$aHTTP_CONN_INFO["uatp"]["host"] = "mpoint.local.cellpointmobile.com";
$aHTTP_CONN_INFO["uatp"]["port"] = 80;
$aHTTP_CONN_INFO["uatp"]["timeout"] = 120;
$aHTTP_CONN_INFO["uatp"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["uatp"]["paths"]["tokenize"] = "/_test/simulators/uatp/generate-suvtp.php";
$aHTTP_CONN_INFO["uatp"]["paths"]["process-settlement"] = "/_test/simulators/uatp/bulk-settlement.php";

/**
 * Connection info for connecting to EZY-fraud check
 */
$aHTTP_CONN_INFO["ezy"]["protocol"] = "http";
$aHTTP_CONN_INFO["ezy"]["host"] = "mpoint.local.cellpointmobile.com";
$aHTTP_CONN_INFO["ezy"]["port"] = 80;
$aHTTP_CONN_INFO["ezy"]["timeout"] = 120;
$aHTTP_CONN_INFO["ezy"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["ezy"]["method"] = "POST";
$aHTTP_CONN_INFO["ezy"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["ezy"]["paths"]["fraud-check"] = "/_test/simulators/check-fraud-status.php";

/**
 * Connection info for connecting to routing service
 */
$aHTTP_CONN_INFO["routing-service"]["protocol"] = "http";
$aHTTP_CONN_INFO["routing-service"]["host"] = "mpoint.local.cellpointmobile.com";
$aHTTP_CONN_INFO["routing-service"]["port"] = 80;
$aHTTP_CONN_INFO["routing-service"]["timeout"] = 120;
$aHTTP_CONN_INFO["routing-service"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["routing-service"]["method"] = "POST";
$aHTTP_CONN_INFO["routing-service"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["routing-service"]["paths"]["get-payment-methods"] = "/_test/simulators/routingservice/get-payment-methods.php";
$aHTTP_CONN_INFO["routing-service"]["paths"]["get-routes"] = "/_test/simulators/routingservice/get-routes.php";

unset($aHTTP_CONN_INFO["eghl"]["paths"]["get-payment-methods"]);


/*
 * Connection info for connecting to Foreign Exchange
 */
$aHTTP_CONN_INFO["foreign-exchange"]["protocol"] = "http";
$aHTTP_CONN_INFO["foreign-exchange"]["host"] = "mpoint.local.cellpointmobile.com";
$aHTTP_CONN_INFO["foreign-exchange"]["port"] = 80;
$aHTTP_CONN_INFO["foreign-exchange"]["timeout"] = 120;
$aHTTP_CONN_INFO["foreign-exchange"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["foreign-exchange"]["method"] = "POST";
$aHTTP_CONN_INFO["foreign-exchange"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["foreign-exchange"]["paths"]["callback"] = "/_test/simulators/dcc/callback.php";

/**
 * Connection info for connecting to mVault
 */
$aHTTP_CONN_INFO["mvault"]["protocol"] = $aHTTP_CONN_INFO["mesb"]["protocol"];
$aHTTP_CONN_INFO["mvault"]["host"] = $aHTTP_CONN_INFO["mesb"]["host"];
$aHTTP_CONN_INFO["mvault"]["port"] = 80;
$aHTTP_CONN_INFO["mvault"]["timeout"] = $aHTTP_CONN_INFO["mesb"]["timeout"];
$aHTTP_CONN_INFO["mvault"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["mvault"]["method"] = $aHTTP_CONN_INFO["mesb"]["method"];
$aHTTP_CONN_INFO["mvault"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["mvault"]["mvault-contenttype"] = "application/xml";
$aHTTP_CONN_INFO["mvault"]["paths"]["save-card"] = "/_test/simulators/mvault/save-card.php";
$aHTTP_CONN_INFO["mvault"]["paths"]["get-card-details"] = "/_test/simulators/mvault/get-card-details.php";

/**
 * Connection info for connecting to SSO
 */
$aHTTP_CONN_INFO["mconsole"]["protocol"] = "http";
$aHTTP_CONN_INFO["mconsole"]["host"] = "mpoint.local.cellpointmobile.com";
$aHTTP_CONN_INFO["mconsole"]["port"] = 80;
$aHTTP_CONN_INFO["mconsole"]["timeout"] = 120;
$aHTTP_CONN_INFO["mconsole"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["mconsole"]["method"] = "POST";
$aHTTP_CONN_INFO["mconsole"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["mconsole"]["paths"]["single-sign-on"] = "/_test/simulators/mconsole/single-sign-on.php";

$aHTTP_CONN_INFO["uatp"]["paths"]["cancel"] = "/_test/simulators/uatp/cancel-suvtp.php";

/**
 * Connection info for connecting to Travel Fund
 */

$aHTTP_CONN_INFO["travel-fund"]["protocol"] = "http";
$aHTTP_CONN_INFO["travel-fund"]["host"] = "mpoint.local.cellpointmobile.com";
$aHTTP_CONN_INFO["travel-fund"]["port"] = 80;
$aHTTP_CONN_INFO["travel-fund"]["method"] = "POST";
$aHTTP_CONN_INFO["travel-fund"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["travel-fund"]["paths"]["redeem"] = "/_test/simulators/travel-fund/redeem.php";

/**
 * Connection info for connecting to SafetyPay
 */
$aHTTP_CONN_INFO["safetypay"]["protocol"] = "http";
$aHTTP_CONN_INFO["safetypay"]["host"] = "mpoint.local.cellpointmobile.com";
$aHTTP_CONN_INFO["safetypay"]["port"] = 80;
$aHTTP_CONN_INFO["safetypay"]["timeout"] = 120;
$aHTTP_CONN_INFO["safetypay"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["safetypay"]["paths"]["pay"] = "/_test/simulators/safetypay/pay.php";
$aHTTP_CONN_INFO["safetypay"]["paths"]["initialize"] = "/_test/simulators/safetypay/pay.php";
$aHTTP_CONN_INFO["safetypay"]["paths"]["get-payment-methods"] = "/_test/simulators/safetypay/get-payment-methods.php";
$aHTTP_CONN_INFO["safetypay"]["path"] = $aHTTP_CONN_INFO["safetypay"]["paths"]["pay"];

/**
 * Connection info for connecting to PayU
 */
$aHTTP_CONN_INFO["payu"]["protocol"] = "http";
$aHTTP_CONN_INFO["payu"]["host"] = "mpoint.local.cellpointmobile.com";
$aHTTP_CONN_INFO["payu"]["port"] = 80;
$aHTTP_CONN_INFO["payu"]["timeout"] = 120;
$aHTTP_CONN_INFO["payu"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["payu"]["paths"]["pay"] = "/_test/simulators/payu/pay.php";
$aHTTP_CONN_INFO["payu"]["paths"]["initialize"] = "/_test/simulators/payu/pay.php";
$aHTTP_CONN_INFO["payu"]["paths"]["get-payment-methods"] = "/_test/simulators/payu/get-payment-methods.php";
$aHTTP_CONN_INFO["payu"]["path"] = $aHTTP_CONN_INFO["payu"]["paths"]["pay"];


/**
 * Connection info for connecting to MPGS
 */
$aHTTP_CONN_INFO["mpgs"]["protocol"] = "http";
$aHTTP_CONN_INFO["mpgs"]["host"] = "mpoint.local.cellpointmobile.com";
$aHTTP_CONN_INFO["mpgs"]["port"] = 80;
$aHTTP_CONN_INFO["mpgs"]["timeout"] = 120;
$aHTTP_CONN_INFO["mpgs"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["mpgs"]["paths"]["auth"] = "/_test/simulators/datacash/auth.php";
$aHTTP_CONN_INFO["mpgs"]["paths"]["pay"] = "/_test/simulators/datacash/pay.php";
$aHTTP_CONN_INFO["mpgs"]["paths"]["initialize"] = "/_test/simulators/datacash/pay.php";
$aHTTP_CONN_INFO["mpgs"]["path"] = $aHTTP_CONN_INFO["mpgs"]["paths"]["pay"];


/**
 * Connection info for connecting to Paymaya-Acq
 */
$aHTTP_CONN_INFO["paymaya_acq"]["protocol"] = "http";
$aHTTP_CONN_INFO["paymaya_acq"]["host"] = "mpoint.local.cellpointmobile.com";
$aHTTP_CONN_INFO["paymaya_acq"]["port"] = 80;
$aHTTP_CONN_INFO["paymaya_acq"]["timeout"] = 120;
$aHTTP_CONN_INFO["paymaya_acq"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["paymaya_acq"]["method"] = "POST";
$aHTTP_CONN_INFO["paymaya_acq"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["paymaya_acq"]["paths"]["initialize"] = "/_test/simulators/paymaya-acq/init.php";
$aHTTP_CONN_INFO["paymaya_acq"]["paths"]["auth"] = "/_test/simulators/paymaya-acq/auth.php";

/**
 * Connection info for connecting to Stripe
 */
$aHTTP_CONN_INFO["stripe"]["protocol"] = "http";
$aHTTP_CONN_INFO["stripe"]["host"] = "mpoint.local.cellpointmobile.com";
$aHTTP_CONN_INFO["stripe"]["port"] = 80;
$aHTTP_CONN_INFO["stripe"]["timeout"] = 120;
$aHTTP_CONN_INFO["stripe"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["stripe"]["path"] = ""; // Set by calling class
$aHTTP_CONN_INFO["stripe"]["paths"]["auth"] = "/_test/simulators/stripe/auth.php";
$aHTTP_CONN_INFO["stripe"]["paths"]["pay"] = "/_test/simulators/stripe/pay.php";
$aHTTP_CONN_INFO["stripe"]["paths"]["initialize"] = "/_test/simulators/stripe/pay.php";

/**
 * Message Queue Provider Information
 *
 */
$aMessage_Queue_Provider_info['provider'] = 'googlepubsub';
//First Preference to KeyFile
$aMessage_Queue_Provider_info['keyfile'] = '{"type":"service_account","project_id":"cpm-development","private_key_id":"bf5d8a48b48c811aed70265ba3f9bc4077f29c76","private_key":"-----BEGIN PRIVATE KEY-----\nMIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCmHL64UyTtD63N\nRW3BnlYBrETgKNnbez8Ata2AKVO3zRnGMSZbpLYKf388NXhqGXILwdBUG1uY/tzV\nBsh1xA0mE89Cw3d5FD/nerJQeT0b7lWjxmS9hY3ThTMlX5Y5MVVw4/COlXUH5cyW\nosAEBCxJt4Iki6VZDIKK2GAs0omeJKbkYq+WnPZKMZUG3y1LBk7Pl0Lxhql1a+9J\nBlAKESuIsla7zCqjU4lSkPq8OJaH0vRl5lKwEMfv6xdUsyjq77NIzOn5uxZpykxq\nNTRTgaSxj+pdPSLpFSMME6fTAZO3daeiZyS+4fdhJQmNcneWcg9zoJf5Lt+8cEVw\nNYm8aya5AgMBAAECggEAAj7QeamfDcjI2MXb21KGd1iSqESJGFlIw1vRU/KLEAiY\n1PfgXLwD3W22hLP01BtSsjO9GwvxH6bvSX5hWo0rP9Tj9/MwM93pFLoQ+7s9zxk4\nRWHWxgSOg5nQ9Iv/mfePn/pmy6ibx77slmsuBQfg6OCvBMsx8ZuUjqeo2iZA57cx\nwLih8/eX6mMOe8nsv+Vg5I65wvDvDTWGJbj6VstV8I1GOHOSbacR5VtgJnZJpoDl\nOv3+JOWvu3vFYCSAU43SFw7I6jTLf+/EahgsmNpM0DaGXrvrt9NPcdpFc7iE36Xe\n5D/LafzSMMl9uoj3+fMw8PwxEy3qXp+dcsT6gQnMIQKBgQDn/gPZo06OBB5x7wBP\nxKKgPnsJ2yA9mxjKufV6FLNB29imQOr6I8lnoEib8AIvkR13qeL+VF+sRan+ukNK\nyuDwoqG9EG5xFxymP3GXT9ToPc+sDYfXkEml+XTB30MsKx77hHVoXA/qnVhFYW99\n0TDv6mS0xhcH4GWVz6IKusrwoQKBgQC3TWzDycCEF/2SwaL2ktCLOK8vd4NbhTLI\nTZNdVs+YSaHfqbfdK8WlvsZdCdr61lR3h3ZpmAd5wEm75t8iRboesCyzU13aUeoy\ngbivZJXAGMThE5/sdEafz8SO5jTJZFEGmvJNhE9zpYNJ48gqO/oHiTILei9YQ4A+\nc+nuL71HGQKBgHCNuOh6Zr5YGT1Fq20IBa6hIaiie33oJZsVpZdZO68ULoRasqYx\nfqGcDh45z5WsXhOUhODHprM6CUPgso0y6PdWsAm+UxbjE69E0KOMw8r5UiwzVFit\nVE4GPTNiUoC3WwzEMwIyyDYXqJ/gqers90Uu/zUFdl8H68FHP2LdRWCBAoGAUbwV\nfqwsyvoDmPf9GaPXl+zbuPe6vAmF3mkxB9LY2JgktR3xnY6SIFkUDJKDcYw2t+HQ\nfD9NPg3vEvlcj+S1nE+EbyYCWIJkQsczOgpI+BJTX+WnTwF+KG61v96ItTClLKPU\n3Znc771i8ITAUYzS7Z9QkGuYRuw6eB6ptgrVhKkCgYBqMLMu2G+YqfDgwc5kD/t/\n8c00RjlMdmKBGJTRhajzjy2/z1vWURZd6KVvJAuv18mv06/7u0M9variobLhYBXz\n/5q3Kt8azqxucudYf68/tjGWW6/ZeIOY0Yl1m8hBLeqcwpxeR3OOdiAwUpv6jwU5\nOaLyOQeLW9+9AT3H+89onw==\n-----END PRIVATE KEY-----\n","client_email":"mpoint-phpunittest-publisher@cpm-development.iam.gserviceaccount.com","client_id":"114317260328047162452","auth_uri":"https://accounts.google.com/o/oauth2/auth","token_uri":"https://oauth2.googleapis.com/token","auth_provider_x509_cert_url":"https://www.googleapis.com/oauth2/v1/certs","client_x509_cert_url":"https://www.googleapis.com/robot/v1/metadata/x509/mpoint-phpunittest-publisher%40cpm-development.iam.gserviceaccount.com"}';
$aMessage_Queue_Provider_info['keyfilepath'] = '';
$aMessage_Queue_Provider_info['projectid'] = 'cpm-development';
$aMessage_Queue_Provider_info['topicname'] = 'mpoint-phpunittest';

?>
