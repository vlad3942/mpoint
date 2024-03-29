<?php

/**
 * Path to Log Files directory
 */
define("sLOG_PATH", "/opt/cpm/mPoint/log/");

$aEnvVariables = [
    //mPoint Database setings
    'database.mpoint.host' => 'db',
    'database.mpoint.port' => '5432',
    'database.mpoint.path' => 'mpoint',
    'database.mpoint.username' => 'postgres',
    'database.mpoint.password' => 'postgres',
    'database.mpoint.class' => 'PostGreSQL',
    'database.mpoint.timeout' => 10,
    'database.mpoint.charset' => 'UTF8',
    'database.mpoint.connmode' => 'normal',
    'database.mpoint.errorpath' => sLOG_PATH ."db_error_". date("Y-m-d") .".log",
    'database.mpoint.errorhandling' => 3,
    'database.mpoint.exectime' => 0.3,
    'database.mpoint.execpath' => sLOG_PATH ."db_exectime_". date("Y-m-d") .".log",
    'database.mpoint.keycase' => CASE_UPPER,
    'database.mpoint.debuglevel' => 2,
    'database.mpoint.method' => 1,


    //Session Database settings
    'database.session.host' => 'db',
    'database.session.port' => '5432',
    'database.session.path' => 'session',
    'database.session.username' => 'session',
    'database.session.password' => '2a2ac8447e',
    'database.session.class' => 'PostGreSQL',
    'database.session.timeout' => 10,
    'database.session.charset' => 'ISO8859_1',
    'database.session.connmode' => 'normal',
    'database.session.errorpath' => sLOG_PATH ."db_error_". date("Y-m-d") .".log",
    'database.session.errorhandling' => 3,
    'database.session.exectime' => 0.3,
    'database.session.execpath' => sLOG_PATH ."db_exectime_". date("Y-m-d") .".log",
    'database.session.keycase' => CASE_UPPER,
    'database.session.debuglevel' => 2,
    'database.session.method' => 1,

    //Connection info for sending error reports to a remote host
    'http.mesb.protocol' => 'http',
    'http.mesb.host' => 'test.mesb.dev-test.cellpointmobile.com',
    'http.mesb.port' => 10080,
    'http.mesb.timeout' => 120,
    'http.mesb.path' => '/',
    'http.mesb.method' => 'POST',
    'http.mesb.contenttype' => 'text/xml',
    'http.mesb.username' => 'CPMTEST',
    'http.mesb.password' => 'DEMOisNO_2',

    //Connection info for sending error reports to a remote host
    'http.iemendo.protocol' => 'http',
    'http.iemendo.host' => 'iemendo.test.cellpointmobile.com',
    'http.iemendo.port' => 80,
    'http.iemendo.timeout' => 20,
    'http.iemendo.path' => '/api/uaprofile.php',
    'http.iemendo.method' => 'POST',
    'http.iemendo.contenttype' => 'text/xml',

    //Connection info for identifying a mobile device by sending its UA Profile information to iEmendo
    'ua.iemendo.protocol' => 'http',
    'ua.iemendo.host' => 'iemendo.test.cellpointmobile.com',
    'ua.iemendo.port' => 80,
    'ua.iemendo.timeout' => 20,
    'ua.iemendo.path' => '/api/uaprofile.php',
    'ua.iemendo.method' => 'POST',
    'ua.iemendo.contenttype' => 'text/xml',


    //Connection info for connecting to PayEx
    'http.payex.protocol' => 'https',
    'http.payex.host' => 'external.payex.com',
    'http.payex.port' => 443,
    'http.payex.timeout' => 120,
    'http.payex.path' => '/PxOrder/Pxorder.asmx?WSDL',
    'http.payex.method' => 'POST',
    'http.payex.contenttype' => 'text/xml',
    'http.payex.password' => 'b9ppZDPbRcJNEgHM57BV',


    //Connection info for connecting to CPG
    'http.cpg.protocol' => 'https',
    'http.cpg.host' => 'pgstaging.emirates.com',
    'http.cpg.port' => 443,
    'http.cpg.timeout' => 120,
    'http.cpg.path' => '/cpg/Order.jsp',
    'http.cpg.method' => 'POST',
    'http.cpg.contenttype' => 'text/xml',

    //Connection info for connecting to Authorize.Net
    'http.authorize.net.protocol' => 'https',
    'http.authorize.net.host' => 'secure.authorize.net',
    'http.authorize.net.port' => 443,
    'http.authorize.net.timeout' => 120,
    'http.authorize.net.path' => '/gateway/transact.dll',
    'http.authorize.net.method' => 'POST',
    'http.authorize.net.contenttype' => 'application/x-www-form-urlencoded',

    //Connection info for connecting to WannaFind
    'http.wannafind.protocol' => 'https',
    'http.wannafind.host' => 'betaling.wannafind.dk',
    'http.wannafind.port' => 443,
    'http.wannafind.timeout' => 120,
    'http.wannafind.path' => '/auth.php',
    'http.wannafind.method' => 'POST',
    'http.wannafind.contenttype' => 'application/x-www-form-urlencoded',


    //Connection info for connecting to NetAxept
    'http.netaxept.protocol' => 'https',
    'http.netaxept.host' => 'epayment-test.bbs.no',
    'http.netaxept.port' => 443,
    'http.netaxept.timeout' => 120,
    'http.netaxept.path' => '/netaxept.svc?wsdl',
    'http.netaxept.method' => 'POST',
    'http.netaxept.contenttype' => 'application/x-www-form-urlencoded',

    //Connection info connecting to GoMobile.
    'http.gm.protocol' => 'http',
    'http.gm.host' => 'gomobile.cellpointmobile.com',
    'http.gm.port' => 8000,
    'http.gm.timeout' => 20,
    'http.gm.path' => '/',
    'http.gm.method' => 'POST',
    'http.gm.contenttype' => 'text/xml',
    'http.gm.username' => '',
    'http.gm.password' => '',
    'http.gm.logpath' => sLOG_PATH,
    /**
     * 1 - Write log entry to file
     * 2 - Output log entry to screen
     * 3 - Write log entry to file and output to screen
     *
     */
    'http.gm.mode' => 1,

    //Connection info connecting to the same host, used while running unit tests
    'http.mpoint.protocol' => 'http',
    'http.mpoint.host' => 'mpoint.local.cellpointmobile.com',
    'http.mpoint.port' => 80,
    'http.mpoint.timeout' => 120,
    'http.mpoint.path' => "/callback/cpm.php",
    'http.mpoint.method' => 'POST',
    'http.mpoint.contenttype' => 'application/x-www-form-urlencoded',

];

foreach ($aEnvVariables as $key => $value) {
    putenv("$key=$value");
}
?>