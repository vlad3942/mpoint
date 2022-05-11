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
//require_once(sCLASS_PATH ."/mConsole.php");
// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
require_once(sCLASS_PATH ."/crs/ClientPaymentMetadata.php");
require_once(sCLASS_PATH ."/crs/ClientRouteConfig.php");
require_once(sCLASS_PATH ."crs/ClientCountryCurrencyConfig.php");
require_once(sCLASS_PATH ."/crs/RouteFeature.php");
require_once(sCLASS_PATH ."/crs/TransactionTypeConfig.php");
require_once(sCLASS_PATH ."/crs/CardState.php");
require_once(sCLASS_PATH ."/crs/FxServiceType.php");

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{

    $clientId = (integer)$_REQUEST['client_id'];
    $pspId = (integer)$_REQUEST['id'];
    $code = Validate::valClient($_OBJ_DB, $clientId);

    if ($code === 100)
    {
            $clientAccountIds = PSPConfig::getClientAccountIds($_OBJ_DB, $clientId, $pspId);
            $clientAccountId = (integer)$clientAccountIds[0];
            $obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $clientId, $clientAccountId , $pspId);
            $toXML = "<client_provider_configuration>".$obj_PSPConfig->toXML(Constants::iPrivateProperty)."</client_provider_configuration>";
    }
    elseif ($code === 2)
    {
        header("HTTP/1.1 400 Bad Request");

        $toXML = '<status><code>'.$code.'</code><text_code>1</text_code><description>Invalid Client ID</description></status>';
    }
    elseif ($code === 3)
    {
        header("HTTP/1.1 400 Bad Request");

        $toXML = '<status><code>'.$code.'</code><text_code>2</text_code><description>Unknown Client ID</description></status>';
    }
    elseif ($code === 4)
    {
        header("HTTP/1.1 400 Bad Request");

        $toXML = '<status><code>'.$code.'</code><text_code>3</text_code><description>Client Disabled</description></status>';
    }
    else
    {
        header("HTTP/1.1 400 Bad Request");

        $toXML = '<status><code>'.$code.'</code><text_code>4</text_code><description>Undefined Client ID</description></status>';
    }
}
else
{
    header("HTTP/1.1 401 Unauthorized");

    $toXML = '<status><code>401</code><text_code>5</text_code><description>Authorization required</description></status>';
}
header("Content-Type: text/xml; charset=\"UTF-8\"");

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo $toXML;
?>