<?php
/**
 *
 * @author Devansh Sah
 * @copyright Cellpoint Digital
 * @link http://www.cellpointdigital.com
 * @package mAPP
 * @version 1.0
 */
use api\classes\merchantservices\Repositories\ReadOnlyConfigRepository;
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
require_once(sCLASS_PATH ."/clientinfo.php");



/*Sample Request
All Fields Required
id -> psp-id
client_id -> Client ID
account_id -> Client's account id (optional)
http://mpoint.local.cellpointmobile.com/mApp/api/get_provider_config.php?id=50&client_id=10069
*/

$obj_DOM = simpledom_load_string(file_get_contents('php://input'));
$_OBJ_TXT = new api\classes\core\TranslateText(array(sLANGUAGE_PATH . $_POST['language'] ."/global.txt", sLANGUAGE_PATH . $_POST['language'] ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true) {
    if($_SERVER['REQUEST_METHOD'] == "GET"){
        if($_REQUEST['client_id'] && $_REQUEST['id']){
            $clientId = (integer)$_REQUEST['client_id'];
            $pspId = (integer)$_REQUEST['id'];
            $accountId = (integer)$_REQUEST['account_id'];
            $code = Validate::valClient($_OBJ_DB, $clientId);
            if ($code === 100) {
                if(!$accountId){
                    $clientAccountIds = PSPConfig::getClientAccountIds($_OBJ_DB, $clientId, $pspId);
                    $accountId = $clientAccountIds[0];
                }
                $obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $clientId, $accountId, $pspId);
                if($obj_PSPConfig){
                    $toXML = "<client_provider_configuration>".$obj_PSPConfig->toXML(Constants::iPrivateProperty)."</client_provider_configuration>";
                } else {
                    header("HTTP/1.1 400 Bad Request");
                    $toXML = "<status><code>99</code><description>Invalid PSP</description></status>";
                }
            } else {
                header("HTTP/1.1 400 Bad Request");
                if ($code === 2) {
                    $description = "Invalid Client ID";
                } elseif ($code === 3) {
                    $description = "Unknown Client ID";
                } elseif ($code === 4) {
                    $description = "Client Disabled";
                } else {
                    $description = "Undefined Client ID";
                }
                $toXML = '<status><code>' . $code . '</code><description>' . $description . '</description></status>';
            }
        }
    } else {
        $clientId = (integer)$obj_DOM->client_provider_configuration->clientid;
        $transactionId = (integer)$obj_DOM->client_provider_configuration->transaction->{'id'};
        $cardId = (integer)$obj_DOM->client_provider_configuration->transaction->{'cardid'};

        $obj_ClientInfo = ClientInfo::produceInfo($obj_DOM->client_provider_configuration->{'client-info'}, CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->client_provider_configuration->{'client-info'}->mobile["country-id"]), $_SERVER['HTTP_X_FORWARDED_FOR']);
        $obj_TxnInfo = TxnInfo::produceInfo($transactionId, $_OBJ_DB);
        $repository = new ReadOnlyConfigRepository($_OBJ_DB,$obj_TxnInfo);
        $accountId = $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID();
        $pspId = $obj_TxnInfo->getPSPID();
        $obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);

        $route_config = General::getRouteConfiguration(
            $repository,
            $_OBJ_DB,
            $obj_mPoint,
            $obj_TxnInfo,
            $obj_ClientInfo,
            $aHTTP_CONN_INFO['routing-service'], $clientId,
            $obj_TxnInfo->getCountryConfig()->getID(), $obj_TxnInfo->getCurrencyConfig()->getID(), $obj_TxnInfo->getAmount(),
            $cardId, NULL, NULL, NULL,
            NULL);
        //print_r($route_config); exit;
        $pspId = (int)$route_config['PSPID'];
        $obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $clientId, $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), $pspId);
        $toXML = "<client_provider_configuration>".$obj_PSPConfig->toXML(Constants::iPrivateProperty).$obj_PSPConfig->toRouteConfigXML()."</client_provider_configuration>";
    }

} else {
    header("HTTP/1.1 401 Unauthorized");
    $toXML = '<status><code>401</code><description>Authorization required</description></status>';
}
header("Content-Type: text/xml; charset=\"UTF-8\"");
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo $toXML;
?>