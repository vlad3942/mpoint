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

header("Content-Type: text/plain");

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );

// Initialize Standard content Object
$obj_mPoint = new MyAccount($_OBJ_DB, $_OBJ_TXT);
$obj_Validator = new Validate();

$aErrCds = array();
if ($obj_Validator->valAccount($_OBJ_DB, $_GET['id'], -1) < 10) { $aErrCds[] = $obj_Validator->valAccount($_OBJ_DB, $_GET['id'], -1) + 10; }
if ($obj_Validator->valCode($_GET['c']) < 10) { $aErrCds[] = $obj_Validator->valCode($_GET['c']) + 20; }

if (count($aErrCds) == 0)
{
	$email = $obj_mPoint->getActivationAddress($_GET['id'], $_GET['c']);
	if ($_GET['chk'] = md5($_GET['id'] . $_GET['c'] . $email) )
	{
		$code = $obj_mPoint->activateCode($_GET['id'], $_GET['c']);
		if ($code == 10)
		{
			if ($obj_mPoint->saveEmail($_GET['id'], $email) === true)
			{
				$aErrCds[] = 100;
			}
			else { $aErrCds[] = 32; }
		}
		else { $aErrCds[] = $code + 23; }
	}
	else { $aErrCds[] = 31; }
}

$msg = "msg=". $aErrCds[0];
for ($i=1; $i<count($aErrCds); $i++)
{
	$msg .= "&msg=". $aErrCds[$i];
}

header("Location: /?url=home/email.php?". urlencode($msg) );
?>