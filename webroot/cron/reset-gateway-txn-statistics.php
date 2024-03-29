<?php

if (PHP_SAPI == "cli") {

    $_SERVER['HTTP_HOST'] = getenv('MPOINT_HOST');
    $_SERVER['DOCUMENT_ROOT'] ='/opt/cpm/mPoint/webroot';
}
include $_SERVER['DOCUMENT_ROOT'].'/cron/cron-include.php';
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/include.php');


/**
 * This task will be invoked once on 1st of every months at 00:00 and will disable all records of last month
 * 
 */
$sql = "UPDATE Client" . sSCHEMA_POSTFIX . ".gatewaystat_tbl SET enabled='0' 
				WHERE statetypeid=1 AND enabled = '1'";

$res = $_OBJ_DB->query($sql);
$aMessages = array();

if (is_resource ( $res ) === true) {
	trigger_error ( "Reset all txn volume records " , E_USER_NOTICE );
} else {
	trigger_error ( "Failed to reset  txn volume records " ,  E_USER_ERROR  );
}

