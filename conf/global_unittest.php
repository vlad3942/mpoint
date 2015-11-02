<?php

$aDB_CONN_INFO["mpoint"]["host"] = "localhost";
$aDB_CONN_INFO["mpoint"]["port"] = 5432;
$aDB_CONN_INFO["mpoint"]["path"] = "mpoint_". TESTDB_TOKEN;
$aDB_CONN_INFO["mpoint"]["username"] = "postgres";
$aDB_CONN_INFO["mpoint"]["password"] = "postgres";
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
$aHTTP_CONN_INFO["dibs"]["paths"]["auth"] = "/_test/simulators/dibs/auth.php";
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

$aCPM_CONN_INFO = $aMPOINT_CONN_INFO;
$aCPM_CONN_INFO["contenttype"] = "application/x-www-form-urlencoded";

$aGM_CONN_INFO = $aMPOINT_CONN_INFO;
$aGM_CONN_INFO["path"] = "/_test/simulators/gomobile/send.php";
$aGM_CONN_INFO["contenttype"] = "text/xml; charset=ISO-8859-1";
$aGM_CONN_INFO["mode"] = 1;
?>