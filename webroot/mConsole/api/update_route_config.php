<?php
/**
 *
 * @author Anna Lagad
 * @copyright Cellpoint Digital
 * @link http://www.cellpointdigital.com
 * @package mConsole
 * @version 1.0
 */

// Require Global Include File
require_once("../../inc/include.php");
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");
// Require Business logic for General Administration of mPoint
require_once(sCLASS_PATH ."admin.php");
// Require Business logic for the mConsole Module
require_once(sCLASS_PATH ."/mConsole.php");
// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
require_once(sCLASS_PATH ."/crs/ClientRouteConfig.php");
require_once(sCLASS_PATH ."/crs/RouteFeature.php");
require_once(sCLASS_PATH ."/crs/MerchantRouteProperty.php");
require_once(sCLASS_PATH ."/crs/ClientRouteCountry.php");
require_once(sCLASS_PATH ."/crs/ClientRouteCurrency.php");

$xml = '';
$obj_DOM = simpledom_load_string(file_get_contents('php://input'));

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
    if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mconsole.xsd") === true && count($obj_DOM->{'route_configuration'}) > 0)
    {
        $clientId = (integer)$obj_DOM->{'route_configuration'}->client_id;
        $code = Validate::valClient($_OBJ_DB, $clientId);
        if ($code === 100)
        {
            $obj_mPoint = new mConsole($_OBJ_DB, $_OBJ_TXT);
            global $aHTTP_CONN_INFO;
            $code = $obj_mPoint->SSOCheck($aHTTP_CONN_INFO['mconsole'], $clientId);
            if ($code === mConsole::iAUTHORIZATION_SUCCESSFUL)
            {
                $xml .= '<route_configuration_response>';

                if(count($obj_DOM->{'route_configuration'}->route_features) == 1)
                {
                    $obj_Route_Features_DOM = $obj_DOM->{'route_configuration'}->route_features;
                    $iRouteConfigId = (int) $obj_DOM->{'route_configuration'}->id;
                    $iRouteFeatureCount = count($obj_Route_Features_DOM->{'route_feature'});
                    $aExistingFeatureId = RouteFeature::getAllFeatureByRouteConfigID($_OBJ_DB, $iRouteConfigId);
                    $aDuplicatefeatureId  = array();
                    for($i=0;$i<$iRouteFeatureCount;$i++){
                        $featureId = (int)$obj_Route_Features_DOM->{'route_feature'}[$i]->id;
                        if(in_array($featureId, $aExistingFeatureId)){
                            $aDuplicatefeatureId[] = $featureId;
                        }else{
                            $objRouteFeature = new RouteFeature((int)$obj_Route_Features_DOM->{'route_feature'}[$i]->id, (string)$obj_Route_Features_DOM->{'route_feature'}[$i]->fname);
                            $response = $objRouteFeature->AddRouteFeature($_OBJ_DB, $clientId, $iRouteConfigId);
                        }
                    }
                    $aFeatureIdToBeDelete = array_diff($aExistingFeatureId, $aDuplicatefeatureId);
                    if(!$objRouteFeature instanceof RouteFeature)
                        $objRouteFeature = new RouteFeature();
                    $response = $objRouteFeature->deleteRouteFeature($_OBJ_DB, $clientId, $iRouteConfigId, $aFeatureIdToBeDelete);
                    $xml .=  $objRouteFeature->getUpdateRouteFeatureResponseAsXML($response);
                }
                elseif(count($obj_DOM->{'route_configuration'}->additional_data) == 1)
                {
                    $obj_Route_Property_DOM = $obj_DOM->{'route_configuration'}->additional_data;
                    $iRouteConfigId = (int) $obj_DOM->{'route_configuration'}->id;
                    $iMerchantPropertyCount = count($obj_Route_Property_DOM->{'param'});
                    $aAdditionalProperty = array();
                    for($i=0;$i<$iMerchantPropertyCount;$i++){
                        $aAdditionalProperty[(string)$obj_Route_Property_DOM->{'param'}[$i]->key] = (string)$obj_Route_Property_DOM->{'param'}[$i]->value;
                    }
                    $objMerchantRouteProperty = new MerchantRouteProperty($_OBJ_DB, $clientId, $iRouteConfigId);
                    $response = $objMerchantRouteProperty->updateAdditionalMerchantProperty($aAdditionalProperty);
                    $xml .= $objMerchantRouteProperty->getUpdateAdditionalPropertyResponseAsXML($response);
                }
                else
                {
                    $objClientRouteConfig = new ClientRouteConfig();
                    $objClientRouteConfig->setInputParams($_OBJ_DB, $obj_DOM->{'route_configuration'});
                    $response = $objClientRouteConfig->updateRoute();
                    $xml .=  $objClientRouteConfig->getUpdateRouteResponseAsXML($response);
                }
                $xml .= '</route_configuration_response>';
            }
            else
            {
                $response = $obj_mPoint->getSSOValidationError($code);
                header($response['http_message']);
                $xml = $response['response'];
            }
        }
        elseif ($code === 2)
        {
            header("HTTP/1.1 400 Bad Request");

            $xml = '<status code="' . $code . '">Invalid Client ID</status>';
        }
        elseif ($code === 3)
        {
            header("HTTP/1.1 400 Bad Request");

            $xml = '<status code="' . $code . '">Unknown Client ID</status>';
        }
        elseif ($code === 4)
        {
            header("HTTP/1.1 400 Bad Request");

            $xml = '<status code="' . $code . '">Client Disabled</status>';
        }
        else
        {
            header("HTTP/1.1 400 Bad Request");

            $xml = '<status code="' . $code . '">Undefined Client ID</status>';
        }
    }
    // Error: Invalid XML Document
    elseif ( ($obj_DOM instanceof SimpleDOMElement) === false)
    {
        header("HTTP/1.1 415 Unsupported Media Type");
        $xml = '<status code="415">Invalid XML Document</status>';
    }
    // Error: Wrong operation
    elseif (count($obj_DOM->{'route_configuration'}) == 0)
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
echo $xml;
?>