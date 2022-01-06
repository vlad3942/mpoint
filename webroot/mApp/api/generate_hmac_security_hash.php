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
use api\classes\SecurityHashResponse;

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH));

$obj_DOM = simpledom_load_string(file_get_contents('php://input'));


if (($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH . "mpoint.xsd") === true && count($obj_DOM->{'hmac_parameters'}) > 0) {
    try {
            $xml = '<security_token_details>';
            for ($i=0; $i<count($obj_DOM->{'hmac_parameters'}->{'hmac_parameter_details'}->{'hmac_parameter_detail'}); $i++)
            {
                $hmacType = (string) $obj_DOM->{'hmac_parameters'}->{'hmac_parameter_details'}->{'hmac_parameter_detail'}[$i]->{'hmac_type'};
                $clientId = (integer) $obj_DOM->{'hmac_parameters'}->{'hmac_parameter_details'}->{'hmac_parameter_detail'}[$i]->{'client_id'};
                $uniqueReference = (integer) $obj_DOM->{'hmac_parameters'}->{'hmac_parameter_details'}->{'hmac_parameter_detail'}[$i]->{'unique_reference_identifier'};
                $nonce = $obj_DOM->{'hmac_parameters'}->{'hmac_parameter_details'}->{'hmac_parameter_detail'}[$i]->{'nonce'};
                $orderId = (string) $obj_DOM->{'hmac_parameters'}->{'hmac_parameter_details'}->{'hmac_parameter_detail'}[$i]->{'order_number'};
                $amount = (integer) $obj_DOM->{'hmac_parameters'}->{'hmac_parameter_details'}->{'hmac_parameter_detail'}[$i]->{'amount'};
                $countryid = (integer) $obj_DOM->{'hmac_parameters'}->{'hmac_parameter_details'}->{'hmac_parameter_detail'}[$i]->{'country_id'};
                $saleAmount = (integer) $obj_DOM->{'hmac_parameters'}->{'hmac_parameter_details'}->{'hmac_parameter_detail'}[$i]->{'sale_amount'};
                $saleCurrency = (integer) $obj_DOM->{'hmac_parameters'}->{'hmac_parameter_details'}->{'hmac_parameter_detail'}[$i]->{'sale_currency'};
                $mobile = (string) $obj_DOM->{'hmac_parameters'}->{'hmac_parameter_details'}->{'hmac_parameter_detail'}[$i]->{'client_info'}->{'mobile'};
                $mobileCountry = (integer) $obj_DOM->{'hmac_parameters'}->{'hmac_parameter_details'}->{'hmac_parameter_detail'}[$i]->{'client_info'}->{'mobile_country'};
                $email = (string) $obj_DOM->{'hmac_parameters'}->{'hmac_parameter_details'}->{'hmac_parameter_detail'}[$i]->{'client_info'}->{'email'};
                $device = (string) $obj_DOM->{'hmac_parameters'}->{'hmac_parameter_details'}->{'hmac_parameter_detail'}[$i]->{'client_info'}->{'device_id'};
                
                $code = Validate::valClient($_OBJ_DB, $clientId);
                
                if ($code == 100) {
                    $obj_Config = ClientConfig::produceConfig($_OBJ_DB, $clientId);
                    if ($obj_Config->getID() > 0) {

                        $obj_HmacSecurityHash = new HmacSecurityHash($clientId, $orderId, $amount, $countryid, $obj_Config->getSalt());
                        
                        $obj_HmacSecurityHash->setHmacType($hmacType);
                        $obj_HmacSecurityHash->setMobile($mobile);
                        $obj_HmacSecurityHash->setMobileCountry($mobileCountry);
                        $obj_HmacSecurityHash->setEMail($email);
                        $obj_HmacSecurityHash->setDeviceId($device);                
                        $obj_HmacSecurityHash->setSaleAmount($saleAmount);
                        $obj_HmacSecurityHash->setSaleCurrency($saleCurrency);
                        $obj_HmacSecurityHash->setCfxID($uniqueReference);                
                        $hmac = $obj_HmacSecurityHash->generateHmac();
                        
                        $obj_SecurityHashResponse[] = new SecurityHashResponse($hmac, $uniqueReference);
                    }else{
                        $obj_SecurityHashResponse[] = new SecurityHashResponse("", $uniqueReference, "Configuration not found for client: " . $clientId);
                        trigger_error("Configuration not found for client: " . $clientId, E_USER_WARNING);
                    }
                }else{
                    $obj_SecurityHashResponse[] = new SecurityHashResponse("", $uniqueReference, "Invalid client detail: " . $clientId);
                    trigger_error("Invalid client detail: " . $clientId, E_USER_WARNING); 
                }
            }
            $xml .= xml_encode($obj_SecurityHashResponse);
            $xml .='</security_token_details>';
    } catch(mPointException | Exception $e) {
        header("HTTP/1.1 ".$e->getCode());
        $xml = '<status code="'.$e->getCode().'">'.$e->getMessage().'</status>';
    }    
} elseif (($obj_DOM instanceof SimpleDOMElement) === false) {
    header("HTTP/1.1 415 Unsupported Media Type");

    $xml = '<status code="415">Invalid XML Document</status>';
} // Error: Wrong operation
elseif (count($obj_DOM->{'hmac_parameters'}) == 0) {
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