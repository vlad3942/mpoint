<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:update-transction-status.php
 */

$interval = '15 MINUTE ';
if (PHP_SAPI == "cli") {
    if ($argc < 2) {
        echo "Expected 1 arguments, but got " . ($argc - 1) . PHP_EOL;
        echo "Syntax : php update-transction-status.php <interval>" . PHP_EOL;
        die();
    }

    [$filePath, $interval] = $argv;
    $_SERVER['HTTP_HOST'] = getenv('MPOINT_HOST');
    $_SERVER['DOCUMENT_ROOT'] = '/opt/cpm/mPoint/webroot';
}else{
    $interval = $_GET["interval"];
}

include $_SERVER['DOCUMENT_ROOT'].'/cron/cron-include.php';
// <editor-fold defaultstate="collapsed" desc="all required files">
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/include.php');


// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");

// Require data class for Payment Service Provider Configurations
require_once(sCLASS_PATH ."/pspconfig.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require Business logic for the Mobile Web module
require_once(sCLASS_PATH ."/mobile_web.php");
// Require Business logic for the Select Credit Card component
require_once(sCLASS_PATH ."/credit_card.php");

// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for the CPM PSP component
require_once(sINTERFACE_PATH ."/cpm_psp.php");
// Require specific Business logic for the CPM ACQUIRER component
require_once(sINTERFACE_PATH ."/cpm_acquirer.php");
// Require specific Business logic for the CPM GATEWAY component
require_once(sINTERFACE_PATH ."/cpm_gateway.php");
// Require specific Business logic for the DIBS component
require_once(sCLASS_PATH ."/dibs.php");
// Require specific Business logic for the PayEx component
require_once(sCLASS_PATH ."/payex.php");
// Require specific Business logic for the NetAxept component
require_once(sCLASS_PATH ."/netaxept.php");
// Require specific Business logic for the MobilePay component
require_once(sCLASS_PATH ."/mobilepay.php");
// Require specific Business logic for the WireCard component
require_once(sCLASS_PATH ."/wirecard.php");
// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
// Require specific Business logic for the Nets component
require_once(sCLASS_PATH ."/nets.php");
// Require specific Business logic for the mVault component
require_once(sCLASS_PATH ."/mvault.php");
// Require specific Business logic for the Amex component
require_once(sCLASS_PATH ."/amex.php");
// Require specific Business logic for the CHUBB component
require_once(sCLASS_PATH ."/chubb.php");
// Require Data Class for Client Information
require_once(sCLASS_PATH ."/clientinfo.php");
// Require specific Business logic for the UATP component
require_once(sCLASS_PATH . "/uatp.php");
// Require specific Business logic for the Chase component
require_once(sCLASS_PATH ."/chase.php");

require_once(sCLASS_PATH ."/payment_processor.php");
require_once(sCLASS_PATH ."/wallet_processor.php");

// </editor-fold>

ignore_user_abort(true);
set_time_limit(-1);



$query = "SELECT txnid as id, pspid
          FROM (
                 SELECT DISTINCT ON (txnid)
                   txnid,
                   stateid,
                   pspid
                 FROM log.message_tbl msg
                 INNER JOIN log.transaction_tbl txn
                   ON txn.id = msg.txnid
                   where
                       pspid NOTNULL
                    AND cardid NOTNULL
                    AND sessionid NOTNULL
                    AND txn.created >= (now() - INTERVAL '". $interval ."')
                    AND stateid in (".Constants::iPAYMENT_INIT_WITH_PSP_STATE. ', ' .Constants::iPAYMENT_3DS_VERIFICATION_STATE. ', ' .Constants::iPAYMENT_ACCEPTED_STATE. ', ' .Constants::iPAYMENT_CAPTURED_STATE. ')
                 ORDER BY txnid, msg.created DESC
               ) sub
          WHERE stateid IN ('.Constants::iPAYMENT_INIT_WITH_PSP_STATE. ', ' .Constants::iPAYMENT_3DS_VERIFICATION_STATE. ')';

    $resultObj = $_OBJ_DB->query($query);

    while ($RESULTSET = $_OBJ_DB->fetchName($resultObj))
    {
        try
        {
            $obj_TxnInfo = TxnInfo::produceInfo(intval($RESULTSET['ID']), $_OBJ_DB);
            $obj_Processor = PaymentProcessor::produceConfig($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, intval($RESULTSET['PSPID']), $aHTTP_CONN_INFO);
            $obj_Processor->status();
        }
        catch (Exception $e)
        {
          //  trigger_error("Update Status CRON exception: " . $e->getCode() . " and message: " . $e->getMessage(), E_USER_ERROR);
        }
    }

header("HTTP/1.1 200 Ok");
echo "<status>ok<status>";
