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
try{
if (($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH . "security_hash.xsd") === true && count($obj_DOM->{'init_token_parameter_details'}) > 0) {
    $detailCount = count($obj_DOM->{'init_token_parameter_details'}->{'init_token_parameter_detail'});
    $xml = '<init_token_response>';
    $xml .= '<security_token_details>';
    for ($i=0; $i < $detailCount; $i++)
    {
        $clientId = (integer) $obj_DOM->{'init_token_parameter_details'}->{'init_token_parameter_detail'}[$i]->{'client_id'};
        $uniqueReference = (integer) $obj_DOM->{'init_token_parameter_details'}->{'init_token_parameter_detail'}[$i]->{'unique_reference_identifier'};
        $nonce = $obj_DOM->{'init_token_parameter_details'}->{'init_token_parameter_detail'}[$i]->{'nonce'};
        $acceptUrl = $obj_DOM->{'init_token_parameter_details'}->{'init_token_parameter_detail'}[$i]->{'accept_url'};
        
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
                header("HTTP/1.1 400 Bad Request");
                throw new mPointException("Configuration not found for client: " . $clientId, 400);
            }
        }else{
            header("HTTP/1.1 400 Bad Request");
            throw new mPointException("Invalid client detail: " . $clientId, 400);
        }
    }
    $xml .= xml_encode($obj_SecurityHashResponse);
    $xml .='</security_token_details>';
    $xml .='</init_token_response>';
    
} elseif (($obj_DOM instanceof SimpleDOMElement) === false) {
    header("HTTP/1.1 415 Unsupported Media Type");
    throw new mPointException("Invalid XML Document", 415);
} // Error: Wrong operation
elseif (count($obj_DOM->{'init_token_parameter_details'}) == 0) {
    foreach ($obj_DOM->children() as $obj_Elem) {
        $errorMsg[] = $obj_Elem->getName();
    }
    $errorMsgStr = implode("", $errorMsg);
    header("HTTP/1.1 400 Bad Request");
    throw new mPointException("Wrong operation: " . $errorMsgStr, 400);
}
else
{
    $aObj_Errs = libxml_get_errors();
    for ($i=0; $i<count($aObj_Errs); $i++)
    {
        $errorMsg[] = htmlspecialchars($aObj_Errs[$i]->message, ENT_NOQUOTES);
    }
    $errorMsgStr = implode("", $errorMsg);
    header("HTTP/1.1 400 Bad Request");
    throw new mPointException($errorMsgStr, 400);
}

}catch (mPointException $e) {
    $xml = '<status>';
    $xml .= '<code>'.$e->getCode().'</code>';
    //$xml = '<text_code>{{sub code}}</text_code>';
    $xml .= '<description>'.$e->getMessage().'</description>';
    $xml .= '<uuid>'.uniqid().'</uuid>';
    $xml .= '</status>';
}

header("Content-Type: text/xml; charset=\"UTF-8\"");
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo $xml;
?>