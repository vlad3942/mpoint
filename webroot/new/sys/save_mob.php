<?php
/**
 *
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Admin
 * @subpackage MyAccount
 */

// Require include file for including all Shared and General APIs
require_once("../../inc/include.php");

// Require Business logic for the My Account component
require_once(sCLASS_PATH ."/my_account.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );

// Initialize Standard content Object
$obj_mPoint = new MyAccount($_OBJ_DB, $_OBJ_TXT);
$obj_Validator = new Validate();

$_SESSION['temp'] = array_merge($_POST, $_SESSION['temp']);

$aMsgCds = array();

// Validate Input
if ($obj_Validator->valCode($_POST['code']) == 10)
{
	$code = $obj_mPoint->activateCode($_SESSION['temp']['accountid'], $_POST['code']);
	if ($code != 10) { $aMsgCds[] = $code + 73; }
}	
else { $aMsgCds[] = $obj_Validator->valCode($_POST['code']) + 70; }

// Activation Code validated and consumed
if (count($aMsgCds) == 0)
{
	$mob = $obj_mPoint->getActivationAddress($_SESSION['temp']['accountid'], $_POST['code']);
	$code = $obj_mPoint->saveMobile($_SESSION['temp']['accountid'], $mob);
	
	// Mobile Number saved to Account
	if ($code == 10)
	{
		// Account created as part of a payment transaction
		if (array_key_exists("obj_TxnInfo", $_SESSION) === true)
		{
			$_SESSION['obj_TxnInfo']->setAccountID($_SESSION['temp']['accountid']);
			// Update Transaction Log
			$obj_mPoint->logTransaction($_SESSION['obj_TxnInfo']);
		}
		$_SESSION['temp'] = array("countryid" => $_SESSION['temp']['obj_CountryConfig']->getID(),
								  "username" => $mob);
		$aMsgCds[] = 101;
	}
	else { $aMsgCds[] = 94; }
}

if ($aMsgCds[0] == 101) { $sFile = "step3.php"; }
else { $sFile = "step2.php"; }

$msg = "";
for ($i=0; $i<count($aMsgCds); $i++)
{
	$msg .= "&msg=". $aMsgCds[$i];
}

header("content-type: text/plain");
header("content-length: 0");

header("location: http://". $_SERVER['HTTP_HOST'] ."/new/". $sFile ."?". session_name() ."=". session_id() . $msg);
?>