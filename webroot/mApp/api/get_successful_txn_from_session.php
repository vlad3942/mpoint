<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:get_transaction_status.php
 */

// Require Global Include File
require_once("../../inc/include.php");

// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH . "simpledom.php");
// Require Business logic for the validating client Input
require_once(sCLASS_PATH . "/validate.php");
// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH . "/enduser_account.php");
// Require general Business logic for the Callback module
require_once(sCLASS_PATH . "/callback.php");

global $_OBJ_TXT;
// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH));

/*$_SERVER['PHP_AUTH_USER'] = "CPMDemo";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

$HTTP_RAW_POST_DATA =  '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '    <get-successful-txn-from-session>';
$HTTP_RAW_POST_DATA .= '        <client-id>10099</client-id>';
$HTTP_RAW_POST_DATA .= '        <session-id>1814879</session-id>';
$HTTP_RAW_POST_DATA .= '    </get-successful-txn-from-session>';
$HTTP_RAW_POST_DATA .= '</root>';*/
$xml = '';
$obj_DOM = simpledom_load_string(file_get_contents('php://input'));
if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true) {
    if (($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH . "mpoint.xsd") === true && count($obj_DOM->{'get-successful-txn-from-session'}) > 0) {

        $sessionid = (int)$obj_DOM->{'get-successful-txn-from-session'}->{'session-id'};
        $clientid = (int)$obj_DOM->{'get-successful-txn-from-session'}->{'client-id'};

        $obj_mPoint = new Home($_OBJ_DB, $_OBJ_TXT);

        $aTxnData = [];
        $aTxnId = $obj_mPoint->getSuccessfulTxnFromSession($clientid, $sessionid);
        $obj_TxnInfo = null;

        foreach ($aTxnId as $txnId) {
            $obj_TxnInfo = TxnInfo::produceInfo($txnId, $_OBJ_DB);
            array_push($aTxnData, $obj_mPoint->constructTransactionInfo($obj_TxnInfo));
        }

        $session = PaymentSession::Get($_OBJ_DB, $sessionid);
        $status = $session->getStateId();
        $sub_code = null;
        $response = $obj_mPoint->constructSessionInfo($obj_TxnInfo, $aTxnData, $status, $sub_code);
        $xml = xml_encode($response);
    } elseif (($obj_DOM instanceof SimpleDOMElement) === false) {
        header("HTTP/1.1 415 Unsupported Media Type");

        $xml = '<status code="415">Invalid XML Document</status>';
    } // Error: Wrong operation
    elseif (count($obj_DOM->{'get-successful-txn-from-session'}) == 0) {
        header("HTTP/1.1 400 Bad Request");

        $xml = '';
        foreach ($obj_DOM->children() as $obj_Elem) {
            $xml .= '<status code="400">Wrong operation: ' . $obj_Elem->getName() . '</status>';
        }
    } // Error: Invalid Input
    else {
        header("HTTP/1.1 400 Bad Request");
        $aObj_Errs = libxml_get_errors();

        $xml = '';
        for ($i = 0; $i < count($aObj_Errs); $i++) {
            $xml = '<status code="400">' . htmlspecialchars($aObj_Errs[$i]->message, ENT_NOQUOTES) . '</status>';
        }
    }
} else {
    header("HTTP/1.1 401 Unauthorized");

    $xml = '<status code="401">Authorization required</status>';
}
header("Content-Type: text/xml; charset=\"UTF-8\"");

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<root>';
echo $xml;
echo '</root>';
?>