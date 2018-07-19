<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:settlement.php
 */

// <editor-fold defaultstate="collapsed" desc="Sample request">
/*$_SERVER['PHP_AUTH_USER'] = "1415";
$_SERVER['PHP_AUTH_PW'] = "Ghdy4_ah1G";

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
$HTTP_RAW_POST_DATA .=  '        </payment-settlement>';
$HTTP_RAW_POST_DATA .= '    </payment-settlements>';
$HTTP_RAW_POST_DATA .= '</root>';*/

// </editor-fold>

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
// Require specific Business logic for mpoint Settlement component
require_once(sCLASS_PATH . "/mPointSettlement.php");
// Require specific Business logic for Amex Settlement component
require_once(sCLASS_PATH . "/amexSettlement.php");
// Require specific Business logic for Settlement Factory component
require_once(sCLASS_PATH . "/settlementFactory.php");

// </editor-fold>



$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH));

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true || true)
{
    if (($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH . "mpoint.xsd") === true && count($obj_DOM->{'payment-settlements'}) > 0)
    {
        $obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);
        $paymentSettlementNodes = $obj_DOM->{'payment-settlements'};
        $arrayClients = [];
        // <editor-fold defaultstate="collapsed" desc="if request contains the client id">
        $paymentSettlementNodes = $paymentSettlementNodes->children();
        if (count($paymentSettlementNodes ) > 0)
        {
            foreach ($paymentSettlementNodes as $paymentSettlementNode)
            {
                $arrayServiceProviders = [];
                $clientId= (int)$paymentSettlementNode['client-id'];
                if (array_key_exists($clientId, $arrayClients) == false)
                {
                    $arrayClients[$clientId] =[];
                }
                // <editor-fold defaultstate="collapsed" desc="if request contains the psp id / information">
                $serviceProviders = $paymentSettlementNode->{'service-providers'}->children();
                if(count($serviceProviders) > 0 )
                {
                    foreach ($serviceProviders as $serviceProvider)
                    {
                        array_push($arrayServiceProviders,(int)$serviceProvider["id"]);
                    }
                }
                // </editor-fold>

                // <editor-fold defaultstate="collapsed" desc="otherwise fetch details from data for all psp ids configured for client">
                else
                {
                    $aRS = $obj_mPoint->getStaticRouteData($clientId);
                    if (is_array($aRS) === true && count($aRS) > 0)
                    {
                        foreach ($aRS as $rs)
                        {
                            array_push($arrayServiceProviders,(int)$rs["PSPID"]);
                        }
                    }
                }
                // </editor-fold>
                $arrayClients[$clientId] = $arrayServiceProviders;
            }
        }
        // </editor-fold>

        // <editor-fold defaultstate="collapsed" desc="otherwise fetch details from data for all client id">
        else
        {
            $aRS = $obj_mPoint->getStaticRouteData();
            if (is_array($aRS) === true && count($aRS) > 0)
            {
                foreach ($aRS as $rs)
                {
                    $clientId = $rs["CLIENTID"];
                    if (array_key_exists($clientId, $arrayClients) == false)
                    {
                        $arrayClients[$clientId] =[];
                    }
                    array_push($arrayClients[$clientId], (int)$rs["PSPID"]);
                }
            }
        }
        // </editor-fold>

        // <editor-fold defaultstate="collapsed" desc="fetch all transaction which is authorized">
        foreach ($arrayClients as $clientid => &$client)
        {
            foreach ($client as $pspid)
            {
                $obj_Settlement = SettlementFactory::create($_OBJ_TXT, $clientid, $pspid, $aHTTP_CONN_INFO);
                if($obj_Settlement != NULL)
                {
                    $obj_Settlement->capture($_OBJ_DB);
                    $obj_Settlement->sendRequest($_OBJ_DB);
                     $obj_Settlement->refund($_OBJ_DB);
                    $obj_Settlement->sendRequest($_OBJ_DB);
                }
            }

        }
    }

    // Error: Invalid XML Document
    elseif (($obj_DOM instanceof SimpleDOMElement) === false) {
        header("HTTP/1.1 415 Unsupported Media Type");

        $xml = '<status code="415">Invalid XML Document</status>';
    } // Error: Wrong operation
    elseif (count($obj_DOM->{'payment-settlements'}) == 0) {
        header("HTTP/1.1 400 Bad Request");

        $xml = '';
        foreach ($obj_DOM->children() as $obj_Elem) {
            $xml .= '<status code="400">Wrong operation: ' . $obj_Elem->getName() . '</status>';
        }
    } // Error: Invalid Input
    else {
        header("HTTP/1.1 400 Bad Request");
        $aObj_Errs = libxml_get_errors();

        $xml = '';
        for ($i = 0; $i < count($aObj_Errs); $i++) {
            $xml .= '<status code="400">' . htmlspecialchars($aObj_Errs[$i]->message, ENT_NOQUOTES) . '</status>';
        }
    }

} else {
    header("HTTP/1.1 401 Unauthorized");
}
header("HTTP/1.1 200 Ok");