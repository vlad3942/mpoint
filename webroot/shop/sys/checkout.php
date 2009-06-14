<?php
/**
 * This files contains the Controller for performing a checkout of the purchased producs
 * The file will ensure that the Data object containing the Transaction Information is updated with the
 * following:
 * 	- Calculated total amount for the Transaction based on ordered products and calculated shipping cost
 * 	- Language based on the customer's device settings
 * Additionally the selected shipping info will be saved to the database
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Shop
 * @subpackage Checkout
 * @version 1.01
 */

// Require Global Include File
require_once("../../inc/include.php");

// Require Business logic for the Shipping Component
require_once(sCLASS_PATH ."/shipping.php");

$obj_mPoint = new Shipping($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo'], $_SESSION['obj_ShopConfig']);
/*
 * Re-Instantiate Data object containing the Transaction Information to:
 * 	- Calculate total amount for the Transaction based on ordered products and calculated shipping cost
 * 	- Set the language based on the customer's device settings
 */
$aTxnInfo = array("amount" => $_SESSION['obj_Info']->getInfo("order_cost") + $_GET['cost'], "language" => sLANG);
$_SESSION['obj_TxnInfo'] = TxnInfo::produceInfo($_SESSION['obj_TxnInfo']->getID(), $_SESSION['obj_TxnInfo'], $aTxnInfo);

// Re-Instantiate the Object with Business Logic for Shipping Information so it uses the re-instantiated Data Object with Transaction Information
$obj_mPoint = new Shipping($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo'], $_SESSION['obj_ShopConfig']);

$aMsgCds = array();

// Success: Input Valid
if (count($aMsgCds) == 0)
{
	$obj_mPoint->logShippingInfo($_GET['id'], $_GET['cost']);
	$obj_mPoint->logTransaction($_SESSION['obj_TxnInfo']);

	$aMsgCds[] = 100;
}

$msg = "&msg=". $aMsgCds[0];

header("content-type: text/plain");
header("content-length: 0");

if ($aMsgCds[0] == 100)
{
	$sPath = "/overview.php";
	$msg = "";
}
else { $sPath = "/shop/shipping.php"; }

header("location: http://". $_SERVER['HTTP_HOST'] . $sPath ."?". session_name() ."=". session_id() . $msg);
?>