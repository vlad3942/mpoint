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
require_once(sCLASS_PATH ."/client_payment_metadata.php");
require_once(sCLASS_PATH ."/client_route_config.php");
require_once(sCLASS_PATH ."/client_country_currency_config.php");
require_once(sCLASS_PATH ."/route_feature.php");

$obj_mConsole = new mConsole($_OBJ_DB, $_OBJ_TXT);

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
    $clientId = (integer)$_REQUEST['client_id'];
    $code = Validate::valClient($_OBJ_DB, $clientId);
    if ($code === 100)
    {
        $aHTTP_CONN_INFO["mesb"]["path"] = Constants::sMCONSOLE_SINGLE_SIGN_ON_PATH;
        $aHTTP_CONN_INFO["mesb"]["username"] = trim($_SERVER['PHP_AUTH_USER']);
        $aHTTP_CONN_INFO["mesb"]["password"] = trim($_SERVER['PHP_AUTH_PW']);
        $obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["mesb"]);

        $obj_mPoint = new mConsole($_OBJ_DB, $_OBJ_TXT);
        $code = $obj_mPoint->singleSignOn($obj_ConnInfo, $_SERVER['HTTP_X_AUTH_TOKEN'], mConsole::sPERMISSION_GET_CLIENTS, $aClientIDs, $_SERVER['HTTP_VERSION']);
        switch ($code)
        {
            case (mConsole::iSERVICE_CONNECTION_TIMEOUT_ERROR):
                header("HTTP/1.1 504 Gateway Timeout");
                $xml = '<status code="'. $code .'">Single Sign-On Service is unreachable</status>';
                break;
            case (mConsole::iSERVICE_READ_TIMEOUT_ERROR):
                header("HTTP/1.1 502 Bad Gateway");
                $xml = '<status code="'. $code .'">Single Sign-On Service is unavailable</status>';
                break;
            case (mConsole::iUNAUTHORIZED_USER_ACCESS_ERROR):
                header("HTTP/1.1 401 Unauthorized");
                $xml = '<status code="'. $code .'">Unauthorized User Access</status>';
                break;
            case (mConsole::iINSUFFICIENT_USER_PERMISSIONS_ERROR):
                header("HTTP/1.1 403 Forbidden");
                $xml = '<status code="'. $code .'">Insufficient User Permissions</status>';
                break;
            case (mConsole::iINSUFFICIENT_CLIENT_LICENSE_ERROR):
                header("HTTP/1.1 402 Payment Required");

                $xml = '<status code="'. $code .'">Insufficient Client License</status>';
                break;
            case (mConsole::iAUTHORIZATION_SUCCESSFUL):

                $obj_Config = ClientPaymentMetadata::produceConfig($_OBJ_DB, $clientId);
                if ($obj_Config instanceof ClientPaymentMetadata)
                {
                    $xml .= $obj_Config->toXML();
                }
                // No Client Configurations found
                if (empty($xml) === true)
                {
                    header("HTTP/1.1 404 Not Found");
                    $xml = '<status code="404">Configuration not found for clients: '. $clientId .'</status>';
                }
                break;
            default:
                header("HTTP/1.1 500 Internal Server Error");
                $xml = '<status code="'. $code .'">Internal Error</status>';
                break;
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