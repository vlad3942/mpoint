<?php
/**
 * Created by IntelliJ IDEA.
 * User: Chaitenya Yadav
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:generate_init_token_security_hash.php
 */

// Require Global Include File
require_once("../../inc/include.php");

// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH . "simpledom.php");
// Require Business logic for the validating client Input
require_once(sCLASS_PATH . "/validate.php");
// Require Data Class for Client Information
require_once(sCLASS_PATH . "/clientinfo.php");
use api\classes\InitTokenSecurityHash;
use api\classes\SecurityHashResponse;

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH));

$obj_DOM = simpledom_load_string(file_get_contents('php://input'));


if (($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH . "mpoint.xsd") === true && count($obj_DOM->{'init_token_parameters'}) > 0) {
    $xml = '<init_token_response>';
    for ($i=0; $i<count($obj_DOM->{'init_token_parameters'}->{'init_token_parameter_details'}->{'init_token_parameter_detail'}); $i++)
    {
        $clientId = (integer) $obj_DOM->{'init_token_parameters'}->{'init_token_parameter_details'}->{'init_token_parameter_detail'}[$i]->{'client_id'};
        $uniqueReference = (integer) $obj_DOM->{'init_token_parameters'}->{'init_token_parameter_details'}->{'init_token_parameter_detail'}[$i]->{'unique_reference_identifier'};
        $nonce = $obj_DOM->{'init_token_parameters'}->{'init_token_parameter_details'}->{'init_token_parameter_detail'}[$i]->{'nonce'};
        $acceptUrl = $obj_DOM->{'init_token_parameters'}->{'init_token_parameter_details'}->{'init_token_parameter_detail'}[$i]->{'accept_url'};
        
        $code = Validate::valClient($_OBJ_DB, $clientId);
        
        if ($code == 100) {
            $obj_Config = ClientConfig::produceConfig($_OBJ_DB, $clientId);
            if ($obj_Config->getID() > 0) {
                $username = htmlspecialchars($obj_Config->getUsername(), ENT_NOQUOTES);
                $password = htmlspecialchars($obj_Config->getPassword(), ENT_NOQUOTES);
                $obj_InitTokenSecurityHash = new InitTokenSecurityHash($clientId, $nonce, $username, $password);
                $obj_InitTokenSecurityHash->setAcceptUrl($acceptUrl);
                $initToken = $obj_InitTokenSecurityHash->generate512Hash();
                $obj_SecurityHashResponse[] = new SecurityHashResponse($initToken, $uniqueReference);
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
    $xml .='</init_token_response>';
    
} elseif (($obj_DOM instanceof SimpleDOMElement) === false) {
    header("HTTP/1.1 415 Unsupported Media Type");

    $xml = '<status code="415">Invalid XML Document</status>';
} // Error: Wrong operation
elseif (count($obj_DOM->{'init_token_parameters'}) == 0) {
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