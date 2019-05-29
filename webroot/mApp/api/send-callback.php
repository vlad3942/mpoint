<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:send-callback.php
 */

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
// Require specific Business logic for the UATP component
require_once(sCLASS_PATH . "/uatp.php");
// Require specific Business logic for the eGHL FPX component
require_once(sCLASS_PATH ."/eghl.php");
// Require specific Business logic for the chase component
require_once(sCLASS_PATH ."/chase.php");

$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH));

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true) {
    $clientId = (integer)$_GET["client-id"];
    $code = Validate::valClient($_OBJ_DB, $clientId);
    if ($code == 100) {

        $obj_Config = ClientConfig::produceConfig($_OBJ_DB, $clientId);

        if($_SERVER['PHP_AUTH_USER'] == $obj_Config->getUsername() && $_SERVER['PHP_AUTH_PW'] == $obj_Config->getPassword() ) {
            if (isset($_GET["order-id"])) {
                $sOrderId = $_GET["order-id"];
            } else {
                $sOrderId = "";
            }

            if (isset($_GET["transaction-id"])) {
                $sTransactionId = $_GET["transaction-id"];
            } else {
                $sTransactionId = "";
            }

            if ($sOrderId !== "" || $sTransactionId !== "") {
                $aOrderId = explode(',', $sOrderId);
                $aTransactionId = explode(',', $sTransactionId);

                $sql = "SELECT DISTINCT ID FROM LOG". sSCHEMA_POSTFIX .".TRANSACTION_TBL 
                WHERE PSPID NOTNULL AND ";

                if ($sOrderId !== "") {
                    $sql .= " ORDERID IN ( '" . implode("', '", $aOrderId) . "' )";
                }
                if ($sOrderId !== "" && $sTransactionId !== "") {
                    $sql .= " OR";
                }

                if ($sTransactionId !== "") {
                    $sql .= " ID IN ( '" . implode("', '", $aTransactionId) . "' )";
                }

                $aRS = $_OBJ_DB->getAllNames($sql);
                if (is_array($aRS) === true && count($aRS) > 0) {
                    for ($i = 0; $i < count($aRS); $i++) {
                        $obj_TxnInfo = TxnInfo::produceInfo($aRS[$i]["ID"], $_OBJ_DB);
                        $obj_mPoint = Callback::producePSP($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO);

                        $sql1 = "SELECT DISTINCT data
                            FROM LOG". sSCHEMA_POSTFIX .".MESSAGE_TBL
                            WHERE stateid = 1991 AND txnid = " . $aRS[$i]["ID"];

                        $aRS1 = $_OBJ_DB->getAllNames($sql1);
                        if (is_array($aRS1) === true && count($aRS1) > 0) {
                            for ($j = 0; $j < count($aRS1); $j++) {
                                $obj_mPoint->retryCallback($aRS1[$j]["DATA"]);
                            }
                        }
                    }
                }

                header("HTTP/1.1 200 OK");
            } else {
                header("HTTP/1.1 400 Bad Request");
            }
        }
        else{
            header("HTTP/1.1 401 Unauthorized");
        }

    } else {
        header("HTTP/1.1 400 Bad Request");
    }

} else {
    header("HTTP/1.1 401 Unauthorized");
}


?>