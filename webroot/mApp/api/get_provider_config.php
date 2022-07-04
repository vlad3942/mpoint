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
require_once(sCLASS_PATH ."/core/card.php");



/*Sample Request
All Fields Required

http://mpoint.local.cellpointmobile.com/mApp/api/get_provider_config.php

<?xml version="1.0" encoding="UTF-8"?>
<root>
    <client_provider_configuration>
        <clientid>10069</clientid>
        <accountid>100690</accountid>
        <pspid>50</pspid>
            <transaction>
                <id>895623</id>
                <cardid>7</cardid>
            </transaction>
            <client-info platform="iOS" version="1.00" language="da">
                <mobile country-id="100" operator-id="10000">28882861</mobile>
                <email>jona@oismail.com</email>
                <customer-ref>jona@oismail.com</customer-ref>
            </client-info>
    </client_provider_configuration>
</root>
*/

$obj_DOM = simpledom_load_string(file_get_contents('php://input'));
$_OBJ_TXT = new api\classes\core\TranslateText(array(sLANGUAGE_PATH . $_POST['language'] ."/global.txt", sLANGUAGE_PATH . $_POST['language'] ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true) {
    $clientId = (integer)$obj_DOM->client_provider_configuration->clientid;
    $code = Validate::valClient($_OBJ_DB, $clientId);
    if ($code === 100) {
        if (($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH . "mpoint.xsd") === true && count($obj_DOM->{'client_provider_configuration'}) > 0) {
            if(count($obj_DOM->{'client_provider_configuration'}->{'transaction'}) > 0){
                $transactionId = (integer)$obj_DOM->client_provider_configuration->transaction->{'id'};
                $cardId = (integer)$obj_DOM->client_provider_configuration->transaction->{'cardid'};
                $obj_ClientInfo = ClientInfo::produceInfo($obj_DOM->client_provider_configuration->{'client-info'}, CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->client_provider_configuration->{'client-info'}->mobile["country-id"]), $_SERVER['HTTP_X_FORWARDED_FOR']);

                try{ $obj_TxnInfo = TxnInfo::produceInfo($transactionId, $_OBJ_DB);
                } catch (TxnInfoException $e) { $obj_TxnInfo = null; }

                if(!$obj_TxnInfo) {
                    $toXML = "<status><code>404</code><description>Transaction with ID: ".$transactionId." not found.</description></status>";
                } else {
                    $repository = new ReadOnlyConfigRepository($_OBJ_DB, $obj_TxnInfo);
                    $obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);
                    $obj_card = new Card(['ID' => $cardId], $_OBJ_DB);
                    $obj_RouteConfiguration = General::getRouteConfiguration($repository, $_OBJ_DB, $obj_mPoint, $obj_TxnInfo, $obj_ClientInfo, $aHTTP_CONN_INFO['routing-service'], $clientId, $obj_TxnInfo->getCountryConfig()->getID(), $obj_TxnInfo->getCurrencyConfig()->getID(), $obj_TxnInfo->getAmount(), $cardId, NULL, $obj_card->getCardName(), NULL, NULL);
                    if ($obj_RouteConfiguration) {
                        $pspId = (int)$obj_RouteConfiguration['PSPID'];
                        $obj_PSPConfig = General::producePSPConfigObject($_OBJ_DB, $obj_TxnInfo, $pspId);
                        $toXML = "<client_provider_configuration>" . $obj_PSPConfig->toXML(Constants::iPrivateProperty) . $obj_PSPConfig->toRouteConfigXML() . "</client_provider_configuration>";
                    } else {
                        $toXML = "<status><code>24</code><description>The selected payment card is not available</description></status>";
                    }
                }
            } if(count($obj_DOM->{'client_provider_configuration'}->{'pspid'}) > 0) {
                $pspId = (integer)$obj_DOM->client_provider_configuration->pspid;
                $accountId = (integer)$obj_DOM->client_provider_configuration->accountid;
                if (!$accountId) {
                    $clientAccountIds = PSPConfig::getClientAccountIds($_OBJ_DB, $clientId, $pspId);
                    $accountId = $clientAccountIds[0];
                }
                $obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $clientId, $accountId, $pspId);
                if ($obj_PSPConfig) {
                    $toXML = "<client_provider_configuration>" . $obj_PSPConfig->toXML(Constants::iPrivateProperty) . "</client_provider_configuration>";
                } else {
                    header("HTTP/1.1 400 Bad Request");
                    $toXML = "<status><code>99</code><description>Invalid PSP</description></status>";
                }
            }
        } else {
            header("HTTP/1.1 415 Unsupported Media Type");
            $toXML = '<status><code>415</code><description>Invalid XML Document</description></status>';
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
} else {
    header("HTTP/1.1 401 Unauthorized");
    $toXML = '<status><code>401</code><description>Authorization required</description></status>';
}
header("Content-Type: text/xml; charset=\"UTF-8\"");
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo $toXML;
?>