<?php
/**
 *
 * @author Anna Lagad
 * @copyright Cellpoint Digital
 * @link http://www.cellpointdigital.com
 * @package mPoint
 * @version 1.0
 */

// Require Global Include File
require_once("../../inc/include.php");
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");
// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."validate.php");
require_once(sCLASS_PATH ."crs/ValidateRule.php");

/*
$_SERVER['PHP_AUTH_USER'] = "CPMDemo";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<rule_validation_details>';
$HTTP_RAW_POST_DATA .= '<card_ids>';
$HTTP_RAW_POST_DATA .= '<card_id>8</card_id>';
$HTTP_RAW_POST_DATA .= '<card_id>7</card_id>';
$HTTP_RAW_POST_DATA .= '</card_ids>';
$HTTP_RAW_POST_DATA .= '<currency_ids>';
$HTTP_RAW_POST_DATA .= '<currency_id>644</currency_id>';
$HTTP_RAW_POST_DATA .= '<currency_id>356</currency_id>';
$HTTP_RAW_POST_DATA .= '</currency_ids>';
$HTTP_RAW_POST_DATA .= '<country_ids>';
$HTTP_RAW_POST_DATA .= '<country_id>200</country_id>';
$HTTP_RAW_POST_DATA .= '<country_id>603</country_id>';
$HTTP_RAW_POST_DATA .= '</country_ids>';
$HTTP_RAW_POST_DATA .= '<routeconfig_ids>';
$HTTP_RAW_POST_DATA .= '<routeconfig_id>12</routeconfig_id>';
$HTTP_RAW_POST_DATA .= '<routeconfig_id>78</routeconfig_id>';
$HTTP_RAW_POST_DATA .= '</routeconfig_ids>';
$HTTP_RAW_POST_DATA .= '</rule_validation_details>';
$HTTP_RAW_POST_DATA .= '</root>';
*/

$xml = '';
$obj_DOM = simpledom_load_string(file_get_contents('php://input'));
//print_r($obj_DOM);
if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
    if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mpoint.xsd") === true && count($obj_DOM->{'rule_validation_details'}) > 0)
    {
        $aCards = (array) $obj_DOM->{'rule_validation_details'}->{'card_ids'}->{'card_id'} ?? null;
        $aCountries = (array) $obj_DOM->{'rule_validation_details'}->{'country_ids'}->{'country_id'} ?? null;
        $aCurrencies = (array) $obj_DOM->{'rule_validation_details'}->{'currency_ids'}->{'currency_id'} ?? null;
        $sRoutes = (array) $obj_DOM->{'rule_validation_details'}->{'routeconfig_ids'}->{'routeconfig_id'} ?? null;
        $aMissingRouteConfiguration = array();
        foreach ($sRoutes as $route){
            $obj_validateRule = ValidateRule::produceConfig($_OBJ_DB, $route, $aCards, $aCountries, $aCurrencies);
            if(empty($obj_validateRule) === false){
                if(!$obj_validateRule->getRouteConfigCount()){
                    $aMissingRouteConfiguration[] = $obj_validateRule->getRouteConfigId();
                }
            }
        }
        $xml .= $obj_validateRule->toXML($aMissingRouteConfiguration);
    }
    // Error: Invalid XML Document
    elseif ( ($obj_DOM instanceof SimpleDOMElement) === false)
    {
        header("HTTP/1.1 415 Unsupported Media Type");
        $xml = '<status code="415">Invalid XML Document</status>';
    }
    // Error: Wrong operation
    elseif (count($obj_DOM->{'rule_validation_details'}) == 0)
    {
        header("HTTP/1.1 400 Bad Request");
        $xml = '';
        foreach ($obj_DOM->children() as $obj_Elem)
        {
            $xml .= '<status code="400">Wrong operation: '. $obj_Elem->getName() .'</status>';
        }
    }
    // Error: Invalid Input
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
}
else
{
    header("HTTP/1.1 401 Unauthorized");

    $xml = '<status code="401">Authorization required</status>';
}
header("Content-Type: text/xml; charset=\"UTF-8\"");

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<root>';
echo $xml;
echo '</root>';
?>