<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package:
 * File Name:get-states.php
 */

// Require Global Include File
require_once("../../inc/include.php");
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");
// Require Business logic for the validating client Input
require_once(sCLASS_PATH . "/validate.php");
// Require Business logic for General Administration of mPoint
require_once (sCLASS_PATH . "admin.php");
// Require Business logic for the mConsole Module
require_once (sCLASS_PATH . "/mConsole.php");
require_once (sCLASS_PATH . "/core/State.php");

/*
$_SERVER['PHP_AUTH_USER'] = "CPMDemo";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";
$_GET['client-id'] = '10007'
*/
$obj_mConsole = new mConsole($_OBJ_DB, $_OBJ_TXT);
if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true) {
    $iClientId = (integer)$_GET['client-id'];
    global $aHTTP_CONN_INFO;
    $code = $obj_mConsole->SSOCheck($aHTTP_CONN_INFO['mconsole'], $iClientId);
    if ($code === mConsole::iAUTHORIZATION_SUCCESSFUL) {
        $aStates = $obj_mConsole->getStates();

        $xml = '<states>';
        foreach ($aStates as $state)
        {
            $xml .= $state->asXML();
        }
        $xml .= '</states>';

    } else {
        $xml = $obj_mConsole->getSSOValidationError($code);
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