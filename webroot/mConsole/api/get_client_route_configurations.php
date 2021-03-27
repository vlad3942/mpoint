<?php
/**
 *
 * @author Vikas Gupta
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
require_once(sCLASS_PATH ."/crs/RouteFeature.php");
require_once(sCLASS_PATH ."/crs/ClientRouteConfigurations.php");
require_once(sCLASS_PATH ."/crs/ClientRouteCountry.php");
require_once(sCLASS_PATH ."/crs/ClientRouteCurrency.php");
require_once(sCLASS_PATH ."/crs/AdditionalProperties.php");

$obj_mConsole = new mConsole($_OBJ_DB, $_OBJ_TXT);

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
    $clientId = (integer)$_REQUEST['client_id'];
    $routeConfigId = (integer)$_REQUEST['route_config_id'];
    $code = Validate::valClient($_OBJ_DB, $clientId);

    if ($code === 100)
    {
        $aHTTP_CONN_INFO["mesb"]["path"] = Constants::sMCONSOLE_SINGLE_SIGN_ON_PATH;
        $aHTTP_CONN_INFO["mesb"]["username"] = trim($_SERVER['PHP_AUTH_USER']);
        $aHTTP_CONN_INFO["mesb"]["password"] = trim($_SERVER['PHP_AUTH_PW']);
        $obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["mesb"]);

        $obj_mPoint = new mConsole($_OBJ_DB, $_OBJ_TXT);
        global $aHTTP_CONN_INFO;
        $code = $obj_mConsole->SSOCheck($aHTTP_CONN_INFO['mconsole'], $clientId);

        if ($code === mConsole::iAUTHORIZATION_SUCCESSFUL) {

            $obj_Config = ClientRouteConfigurations::produceConfig($_OBJ_DB, $clientId, $routeConfigId);
            if ($obj_Config instanceof ClientRouteConfigurations)
            {
                $xml = $obj_Config->toXML();
            }

        } else {
            $response = $obj_mConsole->getSSOValidationError($code);
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