<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:process-settlement.php
 */

// <editor-fold defaultstate="collapsed" desc="all required files">

// Require Global Include File
require_once("../../inc/include.php");

// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH . "simpledom.php");
// Require Business logic for the validating client Input
require_once(sCLASS_PATH . "/validate.php");
// Require Data Class for Client Information
require_once(sCLASS_PATH . "/clientinfo.php");
// Require specific Business logic for mpoint Settlement component
require_once(sCLASS_PATH . "/mPointSettlement.php");
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Sample Request">
/*<root>
    <updateSettlementStatus>
        <clientId>10007</clientId>
        <settlementFile>
            <id>7</id>
            <status>Accepted</status>
        </settlementFile>
        <settlementFile>
            <id>8</id>
            <status>Failed</status>
        </settlementFile>
    </updateSettlementStatus>
</root>*/
// </editor-fold>

$obj_DOM = simpledom_load_string(file_get_contents("php://input"));
if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true) {
     $clientId=(int)$obj_DOM->updateSettlementStatus->clientId;
    $obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, $clientId);
    // Client successfully authenticated
    if ($obj_ClientConfig->getUsername() === $_SERVER['PHP_AUTH_USER'] && $obj_ClientConfig->getPassword() === $_SERVER['PHP_AUTH_PW']) {
        try {
            $sizeOfSettlementFile = count($obj_DOM->updateSettlementStatus->settlementFile);
            for ($settlementFileIndex = 0; $settlementFileIndex < $sizeOfSettlementFile ; $settlementFileIndex++) {
                $settlementId = (int)$obj_DOM->updateSettlementStatus->settlementFile[$settlementFileIndex]->id;
                $settlementStatus = (string)$obj_DOM->updateSettlementStatus->settlementFile[$settlementFileIndex]->status;
                mPointSettlement::updateSettlementStatus($_OBJ_DB, $clientId, $settlementId, $settlementStatus);
            }
            header('HTTP/1.1 200 Ok');
        } catch (Exception $e) {
            header("HTTP/1.1 500");
            trigger_error('Filed to update settlement File status, Code: ' . $e->getCode() . ' and message: ' . $e->getMessage(), E_USER_ERROR);
        }
    } else {
        header('HTTP/1.1 401 Unauthorized');

        $xml = '<status code="401">Username / Password doesn\'t match</status>';
    }
} else {
    header('HTTP/1.1 401 Unauthorized');
}
