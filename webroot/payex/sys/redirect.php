<?php
/**
 * This files contains the Controller for initializing a payment through PayEx and redirecting the customer
 * to PayEx's payment pages.
 * The file will make the necesarry XML calls to WorldPay to initialize the payment transaction.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Payment
 * @subpackage PayEx
 * @version 1.00
 */

// Require Global Include File
require_once("../../inc/include.php");


// Require API for handling Microsoft SOAP Services
require_once(sAPI_CLASS_PATH ."ms_soap_client.php");

// Require Data Class for holding all SOAP Connection Info
require_once(sCLASS_PATH ."soap_conninfo.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");

// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for the PayEx component
require_once(sCLASS_PATH ."/payex.php");

header("Content-Type: text/plain");

$obj_mPoint = new PayEx($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo']);

if ($_SESSION['obj_TxnInfo']->getMode() > 0) { $aHTTP_CONN_INFO["payex"]["host"] = str_replace("confined.", "test-external.", $aHTTP_CONN_INFO["payex"]["host"]); }

$obj_ConnInfo = new SOAPConnInfo($aHTTP_CONN_INFO["payex"]["protocol"] ."://". $aHTTP_CONN_INFO["payex"]["host"] . $aHTTP_CONN_INFO["payex"]["path"], $_POST['accountNumber'], $aHTTP_CONN_INFO["payex"]["password"]);
$obj_XML = $obj_mPoint->initialize($obj_ConnInfo, $_POST['accountNumber'], $_POST['currency'], $obj_mPoint->getCardName($_POST['cardid']) );
var_dump($obj_XML);

//header("location: ". $url);
?>