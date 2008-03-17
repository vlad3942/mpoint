<?php
/**
 * This files contains the Controller for sending an E-Mail Receipt to the customer.
 * The file will ensure that the customer's e-mail address is validated and the e-mail receipt is sent out.
 * 
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Payment
 * @subpackage Receipt
 * @version 1.0
 */

// Require Global Include File
require_once("../../inc/include.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");

$_SESSION['temp'] = $_POST;

$aMsgCds = array();
$obj_Validator = new Validate($_SESSION['obj_TxnInfo']->getClientConfig() );
	
if ($obj_Validator->valEMail($_POST['email']) != 10) { $aMsgCds[] = $obj_Validator->valEMail($_POST['email']) + 10; }

// Success: Input Valid
if (count($aMsgCds) == 0)
{
	$_SESSION['obj_TxnInfo']->setEmail($_POST['email']);
}

$msg = "msg=". $aMsgCds[0];

header("content-type: text/plain");
header("content-length: 0");

if ($aMsgCds[0] == 100) { $sFile = "card.php"; }
else { $sFile = "email.php"; }

header("location: http://". $_SERVER['HTTP_HOST'] ."/pay/". $sFile ."?". session_name() ."=". session_id() ."&". $msg);
?>