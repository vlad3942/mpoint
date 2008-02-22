<?php
/**
 * This files contains the Controller for sending an E-Mail Receipt to the customer.
 * The file will ensure that the customer's e-mail address is validated and the e-mail receipt is sent out.
 * 
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Shop
 * @subpackage Products
 * @version 1.0
 */

// Require Global Include File
require_once("../../inc/include.php");

// Require Business logic for the Order Overview Component
require_once(sCLASS_PATH ."/overview.php");
// Require Business logic for the Product List Component
require_once(sCLASS_PATH ."/products.php");

$_SESSION['temp'] = $_POST;

$obj_mPoint = new Products($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo']);

$aMsgCds = array();
	
if ($obj_mPoint->valPurchase($_POST['products']) != 10) { $aMsgCds[] = $obj_mPoint->valPurchase($_POST['products']) + 10; }

// Success: Input Valid
if (count($aMsgCds) == 0)
{
	$aProducts = array();
	foreach ($_POST['products'] as $id => $quantity)
	{
		if (intval($quantity) > 0) { $aProducts[$id] = $quantity; }
	}
	$obj_mPoint->logProducts($aProducts);
	$_SESSION['obj_Info']->setInfo("order_cost", $obj_mPoint->calcTotalOrder($aProducts) );
	unset($_SESSION['temp']);
	
	$aMsgCds[] = 100;
}

$msg = "&msg=". $aMsgCds[0];

header("content-type: text/plain");
header("content-length: 0");

if ($aMsgCds[0] == 100)
{
	$sFile = "delivery.php";
	$msg = "";
}
else { $sFile = "products.php"; }

header("location: http://". $_SERVER['HTTP_HOST'] ."/shop/". $sFile ."?". session_name() ."=". session_id() . $msg);
?>