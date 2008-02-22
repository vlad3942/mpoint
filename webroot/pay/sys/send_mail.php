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
// Require Business logic for the E-Mail Receipt Component
require_once(sCLASS_PATH ."/email_receipt.php");

$_SESSION['temp'] = $_POST;

$aMsgCds = array();
$obj_Validator = new Validate($_SESSION['obj_TxnInfo']->getClientConfig() );
	
if ($obj_Validator->valEMail($_POST['email']) != 10) { $aMsgCds[] = $obj_Validator->valEMail($_POST['email']) + 10; }

// Success: Input Valid
if (count($aMsgCds) == 0)
{
	$obj_mPoint = new EMailReceipt($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo']);
	if (mail($_POST['email'], $obj_mPoint->constSubject(), $obj_mPoint->constBody(), $obj_mPoint->constHeaders($_POST['email']) ) === true)
	{
		$aMsgCds[] = 100;
		unset($_SESSION['temp']);
	}
	else { $aMsgCds[] = 91; }
}

$msg = "msg=". $aMsgCds[0];

header("content-type: text/plain");
header("content-length: 0");

if ($aMsgCds[0] == 100) { $sFile = "accept.php"; }
else { $sFile = "email.php"; }

header("location: http://". $_SERVER['HTTP_HOST'] ."/pay/". $sFile ."?". session_name() ."=". session_id() ."&". $msg);
?>