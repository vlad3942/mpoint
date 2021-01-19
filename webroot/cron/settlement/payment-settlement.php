<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package: cron
 * File Name:payment-settlement.php
 */

//Username and password is only required for making http request
$_SERVER['PHP_AUTH_USER'] = "test";
$_SERVER['PHP_AUTH_PW'] = "test";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '    <payment-settlements>';
/*$HTTP_RAW_POST_DATA .= '        <payment-settlement client-id="10007">';
$HTTP_RAW_POST_DATA .= '            <service-providers>';
$HTTP_RAW_POST_DATA .= '                <service-provider id="45"/>';
//$HTTP_RAW_POST_DATA .=  '                <service-provider id="23"/>';
$HTTP_RAW_POST_DATA .= '            </service-providers>';
$HTTP_RAW_POST_DATA .= '        </payment-settlement>';
/*$HTTP_RAW_POST_DATA .=  '        <payment-settlement client-id="111">';
$HTTP_RAW_POST_DATA .=  '            <service-providers>';
$HTTP_RAW_POST_DATA .=  '                <service-provider id="222"/>';
$HTTP_RAW_POST_DATA .=  '                <service-provider id="223"/>';
$HTTP_RAW_POST_DATA .=  '            </service-providers>';
$HTTP_RAW_POST_DATA .=  '        </payment-settlement>';*/
$HTTP_RAW_POST_DATA .= '    </payment-settlements>';
$HTTP_RAW_POST_DATA .= '</root>';

$paymentSettlementRequest = '';
if (PHP_SAPI == "cli") {
    if ($argc < 2) {
        echo "Expected 1 arguments, but got " . ($argc - 1) . PHP_EOL;
        echo "Syntax : php payment-settlement.php <requestData>" . PHP_EOL;
        die();
    }

    if ($argc === 2) {
        [$filePath, $paymentSettlementRequest] = $argv;
    }
    $_SERVER['HTTP_HOST'] = getenv('MPOINT_HOST');
    $_SERVER['DOCUMENT_ROOT'] = '/opt/cpm/mPoint/webroot';
}
include $_SERVER['DOCUMENT_ROOT'].'/cron/cron-include.php';
include $_SERVER['DOCUMENT_ROOT'].'/mApp/api/settlement.php';

//header("HTTP/1.1 200 Ok");
//echo "<status>ok<status>";