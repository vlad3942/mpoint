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
require_once("../../include.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require Business logic for the E-Mail Receipt Component
require_once(sCLASS_PATH ."/email_receipt.php");

$_SESSION['temp'] = $_POST;

$aMsgCds = array();
$obj_Validator = new Validate($_SESSION['obj_TxnInfo']->getClientConfig()->getCountryConfig() );
$obj_mPoint = new EMailReceipt($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo']);

if ($obj_Validator->valEMail($_POST['email']) != 10) { $aMsgCds[] = $obj_Validator->valEMail($_POST['email']) + 10; }

// Success: Input Valid
if (count($aMsgCds) == 0)
{
	$_SESSION['obj_TxnInfo']->setEmail($_POST['email']);
	$obj_mPoint->logTransaction($_SESSION['obj_TxnInfo']);
	$obj_mPoint->saveEmail($_SESSION['obj_TxnInfo']->getMobile(), $_POST['email']);
	if ($obj_mPoint->sendReceipt($_POST['email']) === true) { $msg = 100; }
	else { $msg = 91; }
}
else { $msg = $aMsgCds[0]; }

header("content-type: text/plain");
header("content-length: 0");

if ($msg == 100) { $sFile = "accept.php"; }
else { $sFile = "email.php"; }

header("location: http://". $_SERVER['HTTP_HOST'] ."/pay/". $sFile ."?". session_name() ."=". session_id() ."&msg=". $msg);
?>