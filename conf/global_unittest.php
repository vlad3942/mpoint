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
$HTTP_CONN_INFO["mvault"]["mvault-contenttype"] = "application/xml";
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
 * Message Queue Provider Information
 *
 */
$aMessage_Queue_Provider_info['provider'] = 'googlepubsub';
//First Preference to KeyFile
$aMessage_Queue_Provider_info['keyfile'] = '{"type":"service_account","project_id":"cpd-pujaplayground","private_key_id":"e2688609e91b508f47fa60c2b5d68010ac428ddc","private_key":"-----BEGIN PRIVATE KEY-----\nMIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCzOy7Pf0hjvruc\nHyMT9c804Qm2wCXLO4BdNbiRv4nWgasaiVzkSQDubpozKy5j65cxtUMW0qUrYpfH\nLXRVy6oKc0rzDSmr+mBT4bc3gMuHEPNsgaIWTUR8YJjOXozUWhh/TUt1ZyXRV30M\nMxE7GAuOMvWpUcaVQ3mA6VK+qJw0dpMrdBKf+nVyBSpBSYYDZ8i9gSX/UuptEzCF\noIlA8yBqhETgAIddrz70Vcf1I0zV5wFNERuszcnpa3ykd2LuYt+BIKVku0yyYhc8\nPuz6DLURrflnO4aGOAplpTcgMGJD3EVO7ieadB2CkH+UC/MvQz5Qc5m51+8aMz9F\nEOvuGvmHAgMBAAECggEACSYa5u9HBrBpwI0RAVJ+1+LU2BtJfUJK9HcVi3sM0iz0\nSvp6tLx1QQvW8IhiadSmIcdwO+4CDbY5VoAffBxVUGCU5tXj1qov2JhLqJhOdf/g\n18fA5QU0sGn4jYWOCBjr6d5MMnCoIYjRvURYu3zSui4nvr40i3ynlcj94aBBuPvD\nzFb0mHKjnlMov64Dk9w1WrevHw/WLTbgCtOu+pPjUyWKhICxhsFZx118iPpDt4W1\nnACMvWFRxGAlf4DXfVq3VLCWRYKWRxttC7zadCfzHcUDy+tbvwUiNr0MQHiEQB0y\nEGPvZl/6QNc9hxd53AsTuZYhCgdbw9FAsznVYcArAQKBgQDz9iBrKQ+OypGF741Y\nDQXN6it1bS/lsiY+ryFNHl+uanWXGfQyRLxcMQmXa0RPXU/25q5R5TinNwj7ss1x\ng95FwcfuM+PuelBv+S/XhpQaFZH662NwCkq2lfFxvRo5lxRKUhD1UiVjTZvVUCPT\nIcOR9ODO5UW3tQ6JaR34Lp4c4QKBgQC8E1feMx/Z04V3aWY5CuhdrOQWsxIwulW8\n9Ox5yzh9mpISUs/GFv56BcbxSzZ7I/9vfEJ9NZ7Kt4tGgY4QKfdv24OL+Pa1U8sj\nF3KaHktBrTphysevqD5zc4VsZn8yhhondGU41v8yInm9EJwJvo5IHGAVz/MCmvBT\n+SP7vQW7ZwKBgQCBLpBmDh0V4IAEax/uQx8StXADwyh8ucP0p2m721yREABqXazo\nPWt8ad8JVhya5e9k7yvZY8aHDOZt2XVeKZS2XXFP2hxU+GHFmS7TMokT8t4U/zXt\naxW671UlhBvx6OUuoZwnOzNfDQZ6gvAlaZiUnhW4mME9ENu8uXPMKmtBIQKBgQCm\ngOl1igVUrvKl+OXK8mEtLXbwsbAU+6IUGzGP0d49NK7FEhNn58t688pgrJmbAw+M\n/5FNkD74cO4YiXHf1Yd9u/UF4m9nsLtSYdvPnao6hsX89a07UdOYGlmw0j0h2Z8l\n9uH2JEDhfawRObcq2UzVgml+Zg9Z6xmA/jxhDcMZWQKBgGLS3sWA8HRL65ObDfzM\nT7y4alQ0mthKTi78PjPZeNHp+mdrY+uMFCJbNg2E0s10tjWnraICvKI7hYAFJcFJ\nQ9CbTemY9Rh6Q9SdOUl/CxXrOeBi41QJokPc1vYARhzUe+9aVZTd6sQxyc1PYX+b\nMHtN0h6jhx4ye/Xut1mOjWom\n-----END PRIVATE KEY-----\n","client_email":"pujatest@cpd-pujaplayground.iam.gserviceaccount.com","client_id":"107859010056750591329","auth_uri":"https://accounts.google.com/o/oauth2/auth","token_uri":"https://oauth2.googleapis.com/token","auth_provider_x509_cert_url":"https://www.googleapis.com/oauth2/v1/certs","client_x509_cert_url":"https://www.googleapis.com/robot/v1/metadata/x509/pujatest%40cpd-pujaplayground.iam.gserviceaccount.com"}';
$aMessage_Queue_Provider_info['keyfilepath'] = '';
$aMessage_Queue_Provider_info['projectid'] = 'cpd-pujaplayground';
$aMessage_Queue_Provider_info['topicname'] = 'sagartesttopic';


?>
