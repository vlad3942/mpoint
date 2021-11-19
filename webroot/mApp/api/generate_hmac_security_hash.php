<?php
/**
 * Created by IntelliJ IDEA.
 * User: Chaitenya Yadav
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:generate_hmac_security_hash.php
 */

// Require Global Include File
require_once("../../inc/include.php");

// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH . "simpledom.php");
// Require Business logic for the validating client Input
require_once(sCLASS_PATH . "/validate.php");
// Require Data Class for Client Information
require_once(sCLASS_PATH . "/clientinfo.php");
use api\classes\HmacSecurityHash;

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH));

$obj_DOM = simpledom_load_string(file_get_contents('php://input'));


if (($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH . "mpoint.xsd") === true && count($obj_DOM->{'generate-hmac-security-hash'}) > 0) {
    $xml = '<hmac-security-hashes>';
    for ($i=0; $i<count($obj_DOM->{'generate-hmac-security-hash'}->{'transactions'}->{'transaction'}); $i++)
    {
        $hmacType = $obj_DOM->{'generate-hmac-security-hash'}->{'transactions'}->{'transaction'}[$i]->{'hmac-type'};
        $clientId = $obj_DOM->{'generate-hmac-security-hash'}->{'transactions'}->{'transaction'}[$i]->{'client-id'};
        $uniqueReference = $obj_DOM->{'generate-hmac-security-hash'}->{'transactions'}->{'transaction'}[$i]->{'unique-reference'};
        $nonce = $obj_DOM->{'generate-hmac-security-hash'}->{'transactions'}->{'transaction'}[$i]->{'nonce'};
        $orderId = $obj_DOM->{'generate-hmac-security-hash'}->{'transactions'}->{'transaction'}[$i]->{'order-no'};
        $amount = $obj_DOM->{'generate-hmac-security-hash'}->{'transactions'}->{'transaction'}[$i]->{'amount'};
        $countryid = $obj_DOM->{'generate-hmac-security-hash'}->{'transactions'}->{'transaction'}[$i]->{'country-id'};
        $saleAmount = $obj_DOM->{'generate-hmac-security-hash'}->{'transactions'}->{'transaction'}[$i]->{'sale-amount'};
        $saleCurrency = $obj_DOM->{'generate-hmac-security-hash'}->{'transactions'}->{'transaction'}[$i]->{'sale-currency'};
        $mobile = $obj_DOM->{'generate-hmac-security-hash'}->{'transactions'}->{'transaction'}[$i]->{'client-info'}->{'mobile'};
        $mobileCountry = $obj_DOM->{'generate-hmac-security-hash'}->{'transactions'}->{'transaction'}[$i]->{'client-info'}->{'mobile-country'};
        $email = $obj_DOM->{'generate-hmac-security-hash'}->{'transactions'}->{'transaction'}[$i]->{'client-info'}->{'email'};
        $device = $obj_DOM->{'generate-hmac-security-hash'}->{'transactions'}->{'transaction'}[$i]->{'client-info'}->{'device-id'};
        
        $code = Validate::valClient($_OBJ_DB, $clientId);
        
        if ($code == 100) {
            $obj_Config = ClientConfig::produceConfig($_OBJ_DB, $clientId);
            if ($obj_Config->getID() > 0) {

                if($hmacType == "FX"){
                    $hmacStr = $clientId.$orderId.$amount.$countryid.$mobile.$mobileCountry.$email.$device.$obj_Config->getSalt().$saleAmount.$saleCurrency.$uniqueReference;
                }else{
                    $hmacStr = $clientId.$orderId.$amount.$countryid.$mobile.$mobileCountry.$email.$device.$obj_Config->getSalt();
                }
                $hmac = hash('sha512', $hmacStr);
                $init_token = "";
                if($nonce != ''){
                    $init_token = hash('sha512', $clientId.htmlspecialchars($obj_Config->getUsername(), ENT_NOQUOTES).htmlspecialchars($obj_Config->getPassword(), ENT_NOQUOTES).$nonce);
                }
                $obj_HmacSecurityHash[] = new HmacSecurityHash($hmac, $uniqueReference, $init_token);
            }
        }else{
            trigger_error("Configuration not found for client: " . $clientId, E_USER_WARNING);                
        }
    }
    $xml .= xml_encode($obj_HmacSecurityHash);
    $xml .='</hmac-security-hashes>';
    
} elseif (($obj_DOM instanceof SimpleDOMElement) === false) {
    header("HTTP/1.1 415 Unsupported Media Type");

    $xml = '<status code="415">Invalid XML Document</status>';
} // Error: Wrong operation
elseif (count($obj_DOM->{'generate-hmac-security-hash'}) == 0) {
    header("HTTP/1.1 400 Bad Request");

    $xml = '';
    foreach ($obj_DOM->children() as $obj_Elem) {
        $xml .= '<status code="400">Wrong operation: ' . $obj_Elem->getName() . '</status>';
    }
}
else
{
    header("HTTP/1.1 400 Bad Request");
    $aObj_Errs = libxml_get_errors();

    $xml = '';
    for ($i=0; $i<count($aObj_Errs); $i++)
    {
        $xml .= '<status code="400">'. htmlspecialchars($aObj_Errs[$i]->message, ENT_NOQUOTES) .'</status>';
    }
}

header("Content-Type: text/xml; charset=\"UTF-8\"");

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<root>';
echo $xml;
echo '</root>';
?>