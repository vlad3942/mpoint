<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:get_client_configuration.php
 */

// Require Global Include File
require_once("../../inc/include.php");

// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH . "simpledom.php");
// Require Business logic for the validating client Input
require_once(sCLASS_PATH . "/validate.php");
// Require Data Class for Client Information
require_once(sCLASS_PATH . "/clientinfo.php");

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH));

/*$_SERVER['PHP_AUTH_USER'] = "CPMDemo";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

$HTTP_RAW_POST_DATA =  '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<get-client-configuration>';
$HTTP_RAW_POST_DATA .= '<client-id>10007</client-id>';
$HTTP_RAW_POST_DATA .= '</get-client-configuration>';
$HTTP_RAW_POST_DATA .= '</root>';*/


$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true) {
    if (($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH . "mpoint.xsd") === true && count($obj_DOM->{'get-client-configuration'}) > 0) {

        $clientId = (integer)$obj_DOM->{'get-client-configuration'}->{"client-id"};
        $code = Validate::valClient($_OBJ_DB, $clientId);

        if ($code == 100) {
            $obj_Config = ClientConfig::produceConfig($_OBJ_DB, $clientId);
            if ($obj_Config->getID() > 0) {
                $xml .= $obj_Config->toCompactXML();

                if (empty($xml) === true) {
                    header("HTTP/1.1 404 Not Found");
                    $xml = '<status code="404">Configuration not found for clients: ' . $clientId . '</status>';
                }
            } else {
                header("HTTP/1.1 400 Bad Request");
                $xml = '<status code="3">Client id Not Found</status>';
            }
        } else {
            header("HTTP/1.1 400 Bad Request");

            $xml = '<status code="' . $code . '">Client id Not Found</status>';
        }
    } elseif (($obj_DOM instanceof SimpleDOMElement) === false) {
        header("HTTP/1.1 415 Unsupported Media Type");

        $xml = '<status code="415">Invalid XML Document</status>';
    } // Error: Wrong operation
    elseif (count($obj_DOM->{'get-client-configuration'}) == 0) {
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
} else {
    header("HTTP/1.1 401 Unauthorized");

    $xml = '<status code="401">Authorization required</status>';
}
header("Content-Type: text/xml; charset=\"UTF-8\"");

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<root>';
echo $xml;
echo '</root>';
?>