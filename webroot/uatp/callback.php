<?php
/**
 * This file processes the callback received by a UATP transaction completion and produces the data required for
 * sending the onward call to UATP multiple calls.
 *
 */

// Require Global Include File
require_once("../inc/include.php");
// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require Business logic for the End-User Account Factory Provider
require_once(sCLASS_PATH ."/customer_info.php");
// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for Capture component (for use with auto-capture functionality)
require_once(sCLASS_PATH ."/capture.php");
// Require specific Business logic for the CPM PSP component
require_once(sINTERFACE_PATH ."/cpm_psp.php");
// Require specific Business logic for the CPM ACQUIRER component
require_once(sINTERFACE_PATH ."/cpm_acquirer.php");
// Require specific Business logic for the CPM GATEWAY component
require_once(sINTERFACE_PATH ."/cpm_gateway.php");
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");
// Require specific Business logic for the UATP component
require_once(sCLASS_PATH . "/uatp_card_account.php");



set_time_limit(600);
// Standard retry strategy connecting to the database has proven inadequate
$i = 0;
while ( ($_OBJ_DB instanceof RDB) === false && $i < 5)
{
    // Instantiate connection to the Database
    $_OBJ_DB = RDB::produceDatabase($aDB_CONN_INFO["mpoint"]);
    $i++;
}
$obj_XML = simplexml_load_string(file_get_contents("php://input") );


$id = (integer)$obj_XML->callback->transaction["id"];
$xml = '';

$aStateId = array();

try
{
    $obj_TxnInfo = TxnInfo::produceInfo($id, $_OBJ_DB);
    $_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH . $obj_TxnInfo->getLanguage() ."/global.txt", sLANGUAGE_PATH . $obj_TxnInfo->getLanguage() ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");
    $obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), intval($obj_XML->callback->{"psp-config"}["id"]) );

    $iStateID = (integer) $obj_XML->callback->status["code"];
    $isTicketLevelSettlement = $obj_PSPConfig->getAdditionalProperties(Constants::iInternalProperty,'IS_TICKET_LEVEL_SETTLEMENT');
    $aTicketNumbers = [];
    if($isTicketLevelSettlement === 'true')
    {
        $sAdditionalData = $obj_XML->callback->{'additional-data'};
        $aAdditionalData = [];
        parse_str($sAdditionalData, $aAdditionalData);
        $aTicketNumbers = explode(',', $aAdditionalData['tickernumbers']);
    }
    $obj_TxnInfo->produceOrderConfig($_OBJ_DB, $aTicketNumbers);
    $aMessages = $obj_TxnInfo->getMessageHistory($_OBJ_DB);
    $createdtimestamp = null;
    foreach ($aMessages as $m) {
        $iMessageID = (integer)$m["id"];
        $iStateId = (integer)$m["stateid"];
        if($iStateId === $iStateID)
        {
            $createdtimestamp = $m["created"];
            break;
        }
    }
    //Suppress only 4030 callback as a part of CMP-3052
    if($iStateID !== Constants::iSESSION_COMPLETED)
    {
	    $obj_UATP = Callback::producePSP($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO, $obj_PSPConfig);
	    $code = $obj_UATP->initCallback($obj_PSPConfig, $obj_TxnInfo, $iStateID, '',$obj_TxnInfo->getCardID(),$createdtimestamp);

	    if($code === 1000)
	    {
	        header("HTTP/1.1 200 Ok");
	        header("Content-Type: text/xml; charset=\"UTF-8\"");
	        echo '<?xml version="1.0" encoding="UTF-8"?>';
	        echo '<root>';
	        echo '<status code="1000">Callback Success</status>';
	        echo '</root>';
	    }
	    else
	    {
	        header("HTTP/1.1 400 Bad Request");
	        header("Content-Type: text/xml; charset=\"UTF-8\"");
	        echo '<?xml version="1.0" encoding="UTF-8"?>';
	        echo '<root>';
	        echo '<status code="'.$code.'">Callback Failed</status>';
	        echo '</root>';
	    }
    }
    else
    {
		header("HTTP/1.1 200 Ok");
		header("Content-Type: text/xml; charset=\"UTF-8\"");
		echo '<?xml version="1.0" encoding="UTF-8"?>';
		echo '<root>';
		echo '<status code="1000">Callback Suppressed</status>';
		echo '</root>';
    }

}
catch (TxnInfoException $e)
{
    header("HTTP/1.1 500 Internal Server Error");
    $xml .= '<status code="'. $e->getCode() .'">'. htmlspecialchars($e->getMessage(), ENT_NOQUOTES). '</status>';
    trigger_error($e->getMessage() ."\n". $HTTP_RAW_POST_DATA, E_USER_WARNING);
}
catch (CallbackException $e)
{
    header("HTTP/1.1 500 Internal Server Error");
    $xml .= '<status code="'. $e->getCode() .'">'. htmlspecialchars($e->getMessage(), ENT_NOQUOTES). '</status>';
    trigger_error($e->getMessage() ."\n". $HTTP_RAW_POST_DATA, E_USER_WARNING);
}

