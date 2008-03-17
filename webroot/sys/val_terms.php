<?php
/**
 * This files contains the Controller for validating that the Customer has accepted the Terms & Conditions
 * 
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Payment
 * @subpackage Terms
 * @version 1.0
 */

// Require Global Include File
require_once("../inc/include.php");

$_SESSION['temp'] = $_POST;

header("content-type: text/plain");
header("content-length: 0");

$msg = "";
if (array_key_exists("terms", $_POST) === true)
{
	if ($_SESSION['obj_TxnInfo']->getClientConfig()->emailReceiptEnabled() === true)
	{
		$sPath = "pay/email.php";
	}
	else { $sPath = "pay/card.php"; }
}
else
{
	$sPath = "overview.php";
	$msg = "&msg=11";
}

header("location: http://". $_SERVER['HTTP_HOST'] ."/". $sPath ."?". session_name() ."=". session_id() . $msg);
?>