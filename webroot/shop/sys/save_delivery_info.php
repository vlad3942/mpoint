<?php
/**
 * This files contains the Controller for doing validating and saving the provided Address
 * The file will ensure that each of the address fields are valid.
 * 
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Shop
 * @subpackage Delivery
 * @version 1.0
 */


// Require Global Include File
require_once("../../inc/include.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
// Require Business logic for the Delivery Component
require_once(sCLASS_PATH ."/delivery.php");

$_SESSION['temp'] = $_POST;

$obj_mPoint = new Delivery($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo']);
$obj_Validator = new Validate($_SESSION['obj_TxnInfo']->getClientConfig() );

$aMsgCds = array();
	
if ($obj_Validator->valName($_POST['name']) != 10) { $aMsgCds[] = $obj_Validator->valName($_POST['name']) + 20; }
$iCode = $obj_Validator->valName($_POST['company']);
if ($iCode != 10 && $iCode != 1) { $aMsgCds[] = $iCode + 30; }
if ($obj_Validator->valName($_POST['street']) != 10) { $aMsgCds[] = $obj_Validator->valName($_POST['street']) + 40; }
if ($obj_Validator->valZipCode($_POST['zipcode']) != 10) { $aMsgCds[] = $obj_Validator->valZipCode($_POST['zipcode']) + 50; }
if ($obj_Validator->valName($_POST['city']) != 10) { $aMsgCds[] = $obj_Validator->valName($_POST['city']) + 60; }
if ($_SESSION['obj_ShopConfig']->useDeliveryDate() === true && $obj_Validator->valDeliveryDate($_POST['year'], $_POST['month'], $_POST['day']) != 10)
{
	$aMsgCds[] = $obj_Validator->valDeliveryDate($_POST['year'], $_POST['month'], $_POST['day']) + 70;
}

// Success: Input Valid
if (count($aMsgCds) == 0)
{
	if ($_SESSION['obj_ShopConfig']->useDeliveryDate() === true)
	{
		$_POST['delivery-date'] = $_POST['year'] ."-". $_POST['month'] ."-". $_POST['day'];
		unset($_POST['year'], $_POST['month'], $_POST['day']);
	}
	$obj_mPoint->logDeliveryInfo($_POST);
	unset($_SESSION['temp']);
	
	$aMsgCds[] = 100;
}

$msg = "";
for ($i=0; $i<count($aMsgCds); $i++)
{
	$msg .= "&msg=". $aMsgCds[$i];
}

header("content-type: text/plain");
header("content-length: 0");

if ($aMsgCds[0] == 100)
{
	$sFile = "shipping.php";
	$msg = "";
}
else { $sFile = "delivery.php"; }

header("location: http://". $_SERVER['HTTP_HOST'] ."/shop/". $sFile ."?". session_name() ."=". session_id() . $msg);
?>