<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:authenticate.php
 */


// Require Global Include File
require_once("../../inc/include.php");

// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");
// Require data class for Payment Service Provider Configurations
require_once(sCLASS_PATH ."/pspconfig.php");
// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for the CPM PSP component
require_once(sINTERFACE_PATH ."/cpm_psp.php");
// Require specific Business logic for the CPM ACQUIRER component
require_once(sINTERFACE_PATH ."/cpm_acquirer.php");
// Require specific Business logic for the CPM GATEWAY component
require_once(sINTERFACE_PATH ."/cpm_gateway.php");
// Require specific Business logic for the CPM MPI component
require_once(sINTERFACE_PATH ."/cpm_mpi.php");
// Require specific Business logic for the CPM PSP component
require_once(sCLASS_PATH ."/Mpi.php");
// Require specific Business logic for Nets MPI component
//require_once(sINTERFACE_PATH ."/send.php");
// Require specific Business logic for Nets MPI component
require_once(sCLASS_PATH ."/netsmpi.php");
// Require specific Business logic for Modirum MPI component
require_once(sCLASS_PATH ."/modirummpi.php");

set_time_limit(120);

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH));


/*$_SERVER['PHP_AUTH_USER'] = "CPMDemo";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<authenticate account="100007" client-id="10007">';
$HTTP_RAW_POST_DATA .= '<transaction id="1811369">';
$HTTP_RAW_POST_DATA .= '<card type-id="8">';
$HTTP_RAW_POST_DATA .= '<amount country-id="603">100</amount>';
$HTTP_RAW_POST_DATA .= '<card-holder-name>Manish</card-holder-name>';
$HTTP_RAW_POST_DATA .= '<card-number>5413330300001006</card-number>';
$HTTP_RAW_POST_DATA .= '<expiry>01/19</expiry>';
$HTTP_RAW_POST_DATA .= '<cvc>006</cvc>';
$HTTP_RAW_POST_DATA .= '</card>';
$HTTP_RAW_POST_DATA .= '</transaction>';
$HTTP_RAW_POST_DATA .= '<password>oisJona</password>';
$HTTP_RAW_POST_DATA .= '<client-info platform="iOS" version="1.00" language="da">';
$HTTP_RAW_POST_DATA .= '<mobile country-id="100" operator-id="10000">1031339943</mobile>';
$HTTP_RAW_POST_DATA .= '<email>a@a.com</email>';
$HTTP_RAW_POST_DATA .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
$HTTP_RAW_POST_DATA .= '</client-info>';
$HTTP_RAW_POST_DATA .= '</authenticate>';
$HTTP_RAW_POST_DATA .= '</root>';*/

$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

try {
    if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true) {
        if (($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH . "mpoint.xsd") === true && count($obj_DOM->{'authenticate'}) > 0) {


            $xml = '';
            for ($i = 0; $i < count($obj_DOM->{'authenticate'}); $i++) {
                // Set Global Defaults

                $clientId = -1;
                $accoutntId = -1;
                if (empty($obj_DOM->{'authenticate'}[$i]["account"]) === true || intval($obj_DOM->{'authenticate'}[$i]["account"]) < 1) {
                    $obj_DOM->{'authenticate'}[$i]["account"] = -1;
                    $code = 2;
                }
                else {
                    // Validate basic information
                    $clientId =(integer)$obj_DOM->{'authenticate'}[$i]["client-id"];
                    $accoutntId = (integer)$obj_DOM->{'authenticate'}[$i]["account"];
                    $code = Validate::valBasic($_OBJ_DB, $clientId , $accoutntId);
                }
                if ($code == 100) {
                    $obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, $clientId, $accoutntId);
                    // Client successfully authenticated
                    if ($obj_ClientConfig->getUsername() == trim($_SERVER['PHP_AUTH_USER']) && $obj_ClientConfig->getPassword() == trim($_SERVER['PHP_AUTH_PW'])
                        && $obj_ClientConfig->hasAccess($_SERVER['REMOTE_ADDR']) === true) {

                        try
                        {
                            $obj_TxnInfo = TxnInfo::produceInfo( (integer) $obj_DOM->{'authenticate'}[$i]->transaction["id"], $_OBJ_DB);

                            $obj_TxnInfo->produceOrderConfig($_OBJ_DB);
                       // Re-Intialise Text Translation Object based on transaction
							$_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH . $obj_TxnInfo->getLanguage() ."/global.txt", sLANGUAGE_PATH . $obj_TxnInfo->getLanguage() ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");

                            for ($j=0; $j<count($obj_DOM->{'authenticate'}[$i]->transaction->card); $j++) {
                                $countryId = intval($obj_DOM->{'authenticate'}[$i]->transaction->card[$j]->amount["country-id"]) ;
                                $cardId = intval($obj_DOM->{'authenticate'}[$i]->transaction->card[$j]["type-id"]);
                                if (true) {

                                    $obj_Card = $obj_DOM->{'authenticate'}[$i]->transaction->card[$j];

                                    if (count($obj_DOM->{'authenticate'}[$i]->transaction->card[$j]->cvc) == 1)
                                    {
                                        $obj_Card->cvc = (string) $obj_DOM->{'authenticate'}[$i]->transaction->card[$j]->cvc;
                                    }

                                    $obj_MpiRoute = new Mpi();
                                    $obj_Mpi = $obj_MpiRoute->GetMpi($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo,$obj_Card,$aHTTP_CONN_INFO, $clientId, $countryId, $cardId);
                                    $xml = $obj_Mpi->authenticate();
                                } else {
                                    header("HTTP/1.1 404 File Not Found");
                                    $xml = '<status code="404">Transaction with ID: ' . $obj_DOM->{'authenticate'}[$i]->transaction["id"] . ' not found</status>';
                                }
                            }

                        }
                        catch (TxnInfoException $e) { $obj_TxnInfo = null; echo $e->getMessage();} // Transaction not found
                    } else {
                        header("HTTP/1.1 401 Unauthorized");
                        $xml = '<status code="401">Username / Password doesn\'t match</status>';
                    }
                } else {
                    header("HTTP/1.1 400 Bad Request");
                    $xml = '<status code="' . $code . '">Client ID / Account doesn\'t match</status>';
                }
            }

        } elseif (($obj_DOM instanceof SimpleDOMElement) === false) {
            header("HTTP/1.1 415 Unsupported Media Type");
            $xml = '<status code="415">Invalid XML Document</status>';
        } // Error: Wrong operation
        elseif
        (count($obj_DOM->{'authenticate'}) == 0) {
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
                $xml .= '<status code="400">' . htmlspecialchars($aObj_Errs[$i]->message, ENT_NOQUOTES) . '</status>';
            }
        }
    } else {
        header("HTTP/1.1 401 Unauthorized");
        $xml = '<status code="401">Authorization required</status>';
    }

} catch (Exception $e) {
    header("HTTP/1.1 500 Internal Server Error");
    $xml = '<status code="500">' . $e->getMessage() . '</status>';
    trigger_error("Exception thrown in mApp/api/authorize.php: " . $e->getMessage() . "\n" . $e->getTraceAsString(), E_USER_ERROR);
}

header("Content-Type: text/xml; charset=\"UTF-8\"");
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<root>';
echo $xml;
echo '</root>';
