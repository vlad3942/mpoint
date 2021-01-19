<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package: cron
 * File Name:process-settlement.php
 */

$_SERVER['PHP_AUTH_USER'] = "test";
$_SERVER['PHP_AUTH_PW'] = "test";

if (PHP_SAPI == "cli") {
    $_SERVER['HTTP_HOST'] = getenv('MPOINT_HOST');
    $_SERVER['DOCUMENT_ROOT'] = '/opt/cpm/mPoint/webroot';
}
include $_SERVER['DOCUMENT_ROOT'].'/cron/cron-include.php';
include $_SERVER['DOCUMENT_ROOT'].'/mApp/api/process-settlement.php';

header("HTTP/1.1 200 Ok");
echo "<status>ok<status>";