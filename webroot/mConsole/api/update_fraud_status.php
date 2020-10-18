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
require_once(sCLASS_PATH ."/fraudStatus.php");

/*
$_SERVER['PHP_AUTH_USER'] = "CPMDemo";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<update-fraud-status>';
$HTTP_RAW_POST_DATA .= '<client-id>123</client-id>';
$HTTP_RAW_POST_DATA .= '<transaction-id>123</transaction-id>';
$HTTP_RAW_POST_DATA .= '<status_id>3117</status_id>';
$HTTP_RAW_POST_DATA .= '<comment>hdfy28abdl</comment>';
$HTTP_RAW_POST_DATA .= '</update-fraud-status>';
$HTTP_RAW_POST_DATA .= '</root>';
*/
$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

$obj_mConsole = new mConsole($_OBJ_DB, $_OBJ_TXT);

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
    $iClientId = (integer)$obj_DOM->{'update-fraud-status'}->{'client-id'};
    $iTransactionId = (integer)$obj_DOM->{'update-fraud-status'}->{'transaction-id'};
    $iStatusId = (integer)$obj_DOM->{'update-fraud-status'}->{'status-id'};
    $sComment = (string)$obj_DOM->{'update-fraud-status'}->comment;
    $obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);
    $objFraudStatus = new FraudStatus($aHTTP_CONN_INFO, $_OBJ_DB, $obj_mPoint, $obj_mConsole, $iClientId, $iTransactionId, $iStatusId,  $sComment);

    if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mconsole.xsd") === true && count($obj_DOM->{'update-fraud-status'}) > 0)
    {
        $code = $objFraudStatus->SSOCheck();
        if($code == mConsole::iAUTHORIZATION_SUCCESSFUL)
        {
            $xml = $objFraudStatus->updateFraudStatus();
        }
        else
        {
            $xml = $objFraudStatus->getSSOValidationError($code);
        }
    }
    else
    {
      $xml = $objFraudStatus->getRequestValidationError($obj_DOM);
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