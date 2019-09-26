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
// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH . "/enduser_account.php");
// Require Business logic for the End-User Account Factory Provider
require_once(sCLASS_PATH . "/customer_info.php");
// Require general Business logic for the Callback module
require_once(sCLASS_PATH . "/callback.php");
// Require specific Business logic for Capture component (for use with auto-capture functionality)
require_once(sCLASS_PATH . "/capture.php");
// Require specific Business logic for the CPM PSP component
require_once(sINTERFACE_PATH . "/cpm_psp.php");
// Require specific Business logic for the CPM ACQUIRER component
require_once(sINTERFACE_PATH . "/cpm_acquirer.php");
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH . "simpledom.php");
// Require specific Business logic for the Adyen component
require_once(sCLASS_PATH . "/adyen.php");
// Require specific Business logic for the DSB PSP component
require_once(sCLASS_PATH . "/dsb.php");
// Require specific Business logic for the VISA checkout component
require_once(sCLASS_PATH . "/visacheckout.php");
// Require specific Business logic for the Apple Pay component
require_once(sCLASS_PATH . "/applepay.php");
// Require specific Business logic for the Emirates' Corporate Payment Gateway (CPG) component
require_once(sCLASS_PATH . "/cpg.php");
// Require specific Business logic for the AMEX Express Checkout component
require_once(sCLASS_PATH . "/amexexpresscheckout.php");
// Require specific Business logic for the Master Pass component
require_once(sCLASS_PATH . "/masterpass.php");
// Require specific Business logic for the Wirecard component
require_once(sCLASS_PATH . "/wirecard.php");
// Require specific Business logic for the DIBS component
require_once(sCLASS_PATH . "/dibs.php");
// Require specific Business logic for the DIBS component
require_once(sCLASS_PATH . "/securetrading.php");
// Require specific Business logic for the CCAvenue component
require_once(sCLASS_PATH . "/ccavenue.php");
// Require specific Business logic for the PayPal component
require_once(sCLASS_PATH . "/paypal.php");
// Require specific Business logic for the PayFort component
require_once(sCLASS_PATH . "/payfort.php");
// Require specific Business logic for the DataCash component
require_once(sCLASS_PATH . "/datacash.php");
// Require specific Business logic for the Mada Mpgs component
require_once(sCLASS_PATH ."/mada_mpgs.php");
// Require specific Business logic for the 2C2P component
require_once(sCLASS_PATH . "/ccpp.php");
// Require specific Business logic for the MayBank component
require_once(sCLASS_PATH . "/maybank.php");
// Require specific Business logic for the PublicBank component
require_once(sCLASS_PATH . "/publicbank.php");
// Require specific Business logic for the AliPay component
require_once(sCLASS_PATH . "/alipay.php");
// Require specific Business logic for the POLi component
require_once(sCLASS_PATH . "/poli.php");
// Require specific Business logic for the QIWI component
require_once(sCLASS_PATH . "/qiwi.php");
// Require specific Business logic for the Nets component
require_once(sCLASS_PATH . "/nets.php");
// Require specific Business logic for the Klarna component
require_once(sCLASS_PATH . "/klarna.php");
// Require specific Business logic for the mVault component
require_once(sCLASS_PATH . "/mvault.php");
// Require specific Business logic for the Trustly component
require_once(sCLASS_PATH . "/trustly.php");
// Require specific Business logic for the 2C2P-ALC component
require_once(sCLASS_PATH . "/ccpp_alc.php");
// Require specific Business logic for the paytabs component
require_once(sCLASS_PATH . "/paytabs.php");
// Require specific Business logic for the AMEX component
require_once(sCLASS_PATH . "/amex.php");
// Require specific Business logic for mpoint Settlement component
require_once(sCLASS_PATH . "/mPointSettlement.php");
// Require specific Business logic for Amex Settlement component
require_once(sCLASS_PATH . "/amexSettlement.php");
// Require specific Business logic for Settlement Factory component
require_once(sCLASS_PATH . "/settlementFactory.php");
// Require specific Business logic for Amex Settlement component
require_once(sCLASS_PATH . "/ChaseSettlement.php");
// Require specific Business logic for Amex Settlement component
require_once(sCLASS_PATH . "/chase.php");
// Require specific Business logic for UATP Settlement component
require_once(sCLASS_PATH . "/UATPSettlement.php");
// Require specific Business logic for PSP Settlement component
require_once(sCLASS_PATH . "/pspSettlement.php");
// Require specific Business logic for the Cielo component
require_once(sCLASS_PATH ."/cielo.php");

// Require specific Business logic for the VeriTrans4G component
require_once(sCLASS_PATH ."/psp/veritrans4g.php");

require_once sCLASS_PATH . '/txn_passbook.php';
require_once sCLASS_PATH . '/passbookentry.php';
ini_set('max_execution_time', 1200);
// </editor-fold>

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
    try
    {
        $settlementRecords = mPointSettlement::getInprogressSettlements($_OBJ_DB);

        foreach ($settlementRecords as $settlementRecord)
        {
            $obj_Settlement = SettlementFactory::create($_OBJ_TXT, $settlementRecord['client'], $settlementRecord['psp'], $aHTTP_CONN_INFO);
            $obj_Settlement->getConfirmationReport($_OBJ_DB);
        }

        header("HTTP/1.1 200 Ok");
    }
    catch (Exception $e)
    {
        header("HTTP/1.1 500");
        trigger_error("Settlement Confirmation Process failed with code: " . $e->getCode() . " and message: " . $e->getMessage(), E_USER_ERROR);
    }
}
else {
    header("HTTP/1.1 401 Unauthorized");
}
