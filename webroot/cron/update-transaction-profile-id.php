<?php
//default time interval
$interval = '15 MINUTE ';
if (PHP_SAPI == "cli") {
    if ($argc < 4) {
        echo "Expected 3 arguments, but got " . ($argc - 1) . PHP_EOL;
        echo "Syntax : php update-transaction-profile-id.php <clientid> <interval> <limit>" . PHP_EOL;
        die();
    }

    [$filePath, $interval,$clientid,$limit] = $argv;
    $_SERVER['HTTP_HOST'] = getenv('MPOINT_HOST');
    $_SERVER['DOCUMENT_ROOT'] = '/opt/cpm/mPoint/webroot';
}else{
    $interval = urldecode($_GET["interval"]);
    $clientid = $_GET["clientid"];
    $limit    = $_GET["limit"];
}

include $_SERVER['DOCUMENT_ROOT'].'/cron/cron-include.php';
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/include.php');
set_time_limit(-1);

$sql = "SELECT txn.id, txn.clientid, txn.accountid, txn.countryid, txn.mobile, txn.operatorid, txn.email, txn.customer_ref FROM log" . sSCHEMA_POSTFIX . ".transaction_tbl txn WHERE";
$sql .= " txn.clientid in (select prop.externalid from client" . sSCHEMA_POSTFIX . ".additionalproperty_tbl prop where prop.key='ENABLE_PROFILE_ANONYMIZATION' and prop.value='true'";
if (empty($clientid) === false) {
    $sql .= " and prop.externalid = " . intval($clientid) . " ) ";
} else {
    $sql .= " ) ";
}
$sql .= " and txn.profileid IS NULL AND txn.created >= (now() - INTERVAL '" . $interval . "') order by txn.id asc";
if (empty($limit) === false) {
    $sql .= " limit " . intval($limit);
}

try {
    $res = $_OBJ_DB->query($sql);

    while ($RS = $_OBJ_DB->fetchName($res)) {
        $cid = $RS["COUNTRYID"];
        if ($RS["MOBILE"] > 0 && $RS["OPERATORID"] > 0) {
            $cid = substr($RS["OPERATORID"], 0, 3);
        }

// Call Save-Profile API for each transaction
        $obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, $RS["CLIENTID"], $RS['ACCOUNTID']);
        $obj_mPoint = new Home($_OBJ_DB, $_OBJ_TXT);
        $profileId = $obj_mPoint->saveProfile($obj_ClientConfig, $cid, $RS["MOBILE"], $RS["EMAIL"], $RS["CUSTOMER_REF"], "true");
        if ($profileId !== '') {
            try {
                $updateQuery = "UPDATE log" . sSCHEMA_POSTFIX . ".transaction_tbl SET EMAIL=NULL, mobile=NULL, operatorid=NULL, customer_ref=NULL, profileid = '" . $profileId . "' WHERE id= " . intval($RS["ID"]);
                $result = $_OBJ_DB->query($updateQuery);
            } catch (Exception $e) {
                trigger_error("Failed to update profile for txn id =" . $RS["ID"], E_USER_ERROR);
            }
        }
        //trigger_error("Updated txn id =" . $RS["ID"], E_USER_NOTICE);
    }
    trigger_error("Total updated txn ids =" . $_OBJ_DB->countAffectedRows($res), E_USER_NOTICE);
} catch (Exception $e) {
    trigger_error("Error during cron run." , E_USER_ERROR);
}
header('HTTP/1.1 200 Ok');
echo '<root><status>ok<status></root>';
