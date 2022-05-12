<?php
/**
 *
 * @author Devansh Sah
 * @copyright Cellpoint Digital
 * @link http://www.cellpointdigital.com
 * @package mAPP
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

/*Sample Request
All Fields Required
id -> psp-id
client_id -> Client ID
http://mpoint.local.cellpointmobile.com/mApp/api/get_provider_config.php?id=50&client_id=10069
*/

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
            if($obj_PSPConfig){
                $toXML = "<client_provider_configuration>".$obj_PSPConfig->toXML(Constants::iPrivateProperty)."</client_provider_configuration>";
            } else {
                header("HTTP/1.1 400 Bad Request");
                $toXML = "<status><code>99</code><description>Invalid PSP</description></status>";
            }

    }
    elseif ($code === 2)
    {
        header("HTTP/1.1 400 Bad Request");

        $toXML = '<status><code>'.$code.'</code><description>Invalid Client ID</description></status>';
    }
    elseif ($code === 3)
    {
        header("HTTP/1.1 400 Bad Request");

        $toXML = '<status><code>'.$code.'</code><description>Unknown Client ID</description></status>';
    }
    elseif ($code === 4)
    {
        header("HTTP/1.1 400 Bad Request");

        $toXML = '<status><code>'.$code.'</code><description>Client Disabled</description></status>';
    }
    else
    {
        header("HTTP/1.1 400 Bad Request");

        $toXML = '<status><code>'.$code.'</code><description>Undefined Client ID</description></status>';
    }
}
else
{
    header("HTTP/1.1 401 Unauthorized");

    $toXML = '<status><code>401</code><description>Authorization required</description></status>';
}
header("Content-Type: text/xml; charset=\"UTF-8\"");

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo $toXML;
?>