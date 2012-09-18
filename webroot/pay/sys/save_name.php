<?php
/**
 * This file contains the Controller for saving the name of a card.
 * The file will ensure that the card name is valid if a name is provided.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Payment
 * @subpackage SaveName
 * @version 1.00
 */

// Require Global Include File
require_once("../../inc/include.php");

// Require the PHP API for handling the connection to GoMobile
require_once(sAPI_CLASS_PATH ."/gomobile.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");

$_SESSION['temp'] = $_POST;

// Re-initialization of Session required
if (array_key_exists("mpoint-id", $_REQUEST) === true
	&& (array_key_exists("obj_TxnInfo", $_SESSION) === false || ($_SESSION['obj_TxnInfo'] instanceof TxnInfo) === false) )
{
	$_SESSION['obj_TxnInfo'] = TxnInfo::produceInfo($_REQUEST['mpoint-id'], $_OBJ_DB);
}

$aMsgCds = array();
$obj_Validator = new Validate($_SESSION['obj_TxnInfo']->getClientConfig()->getCountryConfig() );
$obj_mPoint = new EndUserAccount($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo']->getClientConfig() );

if ($obj_Validator->valName($_POST['name']) > 1 && $obj_Validator->valName($_POST['name']) != 10) { $aMsgCds[] = $obj_Validator->valName($_POST['name']) + 33; }

// Success: Input Valid
if (count($aMsgCds) == 0)
{
	if (strlen(@$_POST['name']) > 0) { $iStatus = $obj_mPoint->saveCardName($_SESSION['obj_TxnInfo']->getMobile(), $_POST['cardid'], $_POST['name']); }
	$aMsgCds[] = 102;
	$_SESSION['temp'] = array();
}

if ($aMsgCds[0] == 102) { $sFile = "accept.php"; }
else { $sFile = "name.php"; }

$msg = "";
for ($i=0; $i<count($aMsgCds); $i++)
{
	$msg .= "&msg=". $aMsgCds[$i];
}

header("content-type: text/plain");
header("content-length: 0");

header("location: http://". $_SERVER['HTTP_HOST'] ."/pay/". $sFile ."?". session_name() ."=". session_id() . $msg);
?>