<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:update-issuing-bank-statistical.php
 * Description : This api will be used by cron to update the usage count of issuing bank
 *               This file in written as a temporary solution so that containing some hardcoded values.
 *               It will remove once Dataware House is completed
 *
 */

if (PHP_SAPI == "cli") {
    $_SERVER['HTTP_HOST'] = getenv('MPOINT_HOST');
    $_SERVER['DOCUMENT_ROOT'] ='/opt/cpm/mPoint/webroot';
}
include $_SERVER['DOCUMENT_ROOT'].'/cron/cron-include.php';
// <editor-fold defaultstate="collapsed" desc="all required files">
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/include.php');

// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH . "simpledom.php");

// Require data class for Payment Service Provider Configurations
require_once(sCLASS_PATH . "/pspconfig.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH . "/enduser_account.php");
// Require Business logic for the Mobile Web module
require_once(sCLASS_PATH . "/mobile_web.php");
// Require Business logic for the Select Credit Card component
require_once(sCLASS_PATH . "/credit_card.php");

// Require general Business logic for the Callback module
require_once(sCLASS_PATH . "/callback.php");
// Require specific Business logic for the CPM PSP component
require_once(sINTERFACE_PATH . "/cpm_psp.php");
// Require specific Business logic for the CPM ACQUIRER component
require_once(sINTERFACE_PATH . "/cpm_acquirer.php");
// Require specific Business logic for the CPM GATEWAY component
require_once(sINTERFACE_PATH . "/cpm_gateway.php");
// Require specific Business logic for the DIBS component
require_once(sCLASS_PATH . "/dibs.php");
// Require specific Business logic for the PayEx component
require_once(sCLASS_PATH . "/payex.php");
// Require specific Business logic for the NetAxept component
require_once(sCLASS_PATH . "/netaxept.php");
// Require specific Business logic for the MobilePay component
require_once(sCLASS_PATH . "/mobilepay.php");
// Require specific Business logic for the WireCard component
require_once(sCLASS_PATH . "/wirecard.php");
// Require Business logic for the validating client Input
require_once(sCLASS_PATH . "/validate.php");
// Require specific Business logic for the Nets component
require_once(sCLASS_PATH . "/nets.php");
// Require specific Business logic for the mVault component
require_once(sCLASS_PATH . "/mvault.php");
// Require specific Business logic for the Amex component
require_once(sCLASS_PATH . "/amex.php");
// Require specific Business logic for the CHUBB component
require_once(sCLASS_PATH . "/chubb.php");
// Require Data Class for Client Information
require_once(sCLASS_PATH . "/clientinfo.php");
// Require specific Business logic for the UATP component
require_once(sCLASS_PATH . "/uatp.php");
// Require specific Business logic for the Chase component
require_once(sCLASS_PATH . "/chase.php");
require_once(sCLASS_PATH . "/payment_processor.php");
require_once(sCLASS_PATH . "/wallet_processor.php");
// </editor-fold>


$cardAccessQuery = 'SELECT clientid, pspid
                    FROM client.cardaccess_tbl ca
                             INNER JOIN system.card_tbl c ON ca.cardid = c.id
                    WHERE c.paymenttype = 7
                      AND ca.enabled';

$caResultObj = $_OBJ_DB->query($cardAccessQuery);

while ($RESULTSET1 = $_OBJ_DB->fetchName($caResultObj)) {
    $clientId = (int)$RESULTSET1['CLIENTID'];
    $pspId = (int)$RESULTSET1['PSPID'];
    $query = "SELECT DISTINCT (issuing_bank) AS issuing_bank, count(*) AS usage
            FROM log.transaction_tbl txn
                     INNER JOIN log.message_tbl msg ON txn.id = msg.txnid
            WHERE msg.stateid = 2000
              AND msg.created > (now() - INTERVAL '30 DAY')
              AND txn.clientid=$clientId  
              AND txn.pspid=$pspId  
            GROUP BY issuing_bank";
    $resultObj = $_OBJ_DB->query($query);

    while ($RESULTSET = $_OBJ_DB->fetchName($resultObj)) {
        $issuing_bank = $RESULTSET["ISSUING_BANK"];
        if ($issuing_bank != '') {
            $count = $RESULTSET["USAGE"];
            $issuing_bank = strtolower($issuing_bank);
            try {
                $upsertQuery = "UPDATE client.additionalproperty_tbl SET value = '$count' WHERE key= 'issuing_bank_$issuing_bank';
            INSERT INTO client.additionalproperty_tbl (key, value, externalid, enabled, type)
            SELECT  'issuing_bank_$issuing_bank', $count, $clientId, true, 'client'
            WHERE NOT EXISTS (SELECT 1 FROM client.additionalproperty_tbl WHERE key = 'issuing_bank_$issuing_bank');";
                $result = $_OBJ_DB->query($upsertQuery);
            } catch (Exception $e) {
            }
        }
    }
}
header('HTTP/1.1 200 Ok');
echo '<root><status>ok<status></root>';