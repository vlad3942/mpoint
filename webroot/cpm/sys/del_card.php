<?php
/**
 * This files contains the Controller for sending an E-Mail Receipt to the customer.
 * The file will ensure that the customer's e-mail address is validated and the e-mail receipt is sent out.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Payment
 * @subpackage EndUserAccount
 * @version 1.00
 */

// Require Global Include File
require_once("../../inc/include.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require Business logic for the My Account component
require_once(sCLASS_PATH ."/my_account.php");

header("content-type: text/plain");

$_SESSION['temp'] = $_POST;

$aMsgCds = array();
$obj_Validator = new Validate($_SESSION['obj_TxnInfo']->getClientConfig()->getCountryConfig() );
$obj_mPoint = new MyAccount($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo']->getClientConfig()->getCountryConfig() );

if ($obj_Validator->valStoredCard($_OBJ_DB, $_SESSION['obj_TxnInfo']->getAccountID(), $_POST['cardid']) != 10) { $aMsgCds[] = $obj_Validator->valStoredCard($_OBJ_DB, $_SESSION['obj_TxnInfo']->getAccountID(), $_POST['cardid']) + 20; }
if ($_SESSION['obj_Info']->getInfo("auth-token") === false || strlen($_SESSION['obj_TxnInfo']->getAuthenticationURL() ) == 0)
{
	if ($obj_Validator->valPassword($_POST['pwd']) != 10) { $aMsgCds[] = $obj_Validator->valPassword($_POST['pwd']) + 30; }
}

// Success: Input Valid
if (count($aMsgCds) == 0)
{
	if ($_SESSION['obj_Info']->getInfo("auth-token") === false || strlen($_SESSION['obj_TxnInfo']->getAuthenticationURL() ) == 0)
	{
		$msg = $obj_mPoint->auth($_SESSION['obj_TxnInfo']->getAccountID(), $_POST['pwd'], false);
	}
	else { $msg = $obj_mPoint->auth(HTTPConnInfo::produceConnInfo($_SESSION['obj_TxnInfo']->getAuthenticationURL() ), $_SESSION['obj_TxnInfo']->getCustomerRef(), $_SESSION['obj_Info']->getInfo("auth-token") ); }
	if ($msg >= 10)
	{
		if ($obj_mPoint->delStoredCard($_SESSION['obj_TxnInfo']->getAccountID(), $_POST['cardid']) === true)
		{
			$aMsgCds[] = 100;
		}
		else { $aMsgCds[] = 99; }
	}
	else { $aMsgCds[] = $msg + 40; }
}

$msg = "";
for ($i=0; $i<count($aMsgCds); $i++)
{
	$msg .= "&msg=". $aMsgCds[$i];
}


header("location: http://". $_SERVER['HTTP_HOST'] ."/cpm/payment.php?" . session_name() ."=". session_id() . $msg);
?>