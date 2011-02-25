<?php
/**
 * This files contains the Controller for mPoint's Surveillance API.
 * The Controller will ensure that all requests from the external surveillance system is authenticated and
 * appropriate data is returned to the caller.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package API
 * @subpackage Surveillance
 * @version 1.00
 */

// Require Global Include File
require_once("../inc/include.php");

// Require Business logic for Administrative functions
require_once(sCLASS_PATH ."/admin.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");

header("Content-Type: text/plain");

$aMsgCds = array();

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );

// Set Global Defaults
if (array_key_exists("clientid", $_REQUEST) === false) { $_REQUEST['clientid'] = -1; }

$aMsgCds = array();
$iUserID = -1;

$obj_mPoint = new Admin($_OBJ_DB, $_OBJ_TXT);
$obj_Validator = new Validate();

// Validate input
if ($obj_Validator->valUsername($_REQUEST['username']) != 10) { $aMsgCds[] = $obj_Validator->valUsername($_REQUEST['username']) + 10; }
if ($obj_Validator->valPassword($_REQUEST['password']) != 10) { $aMsgCds[] = $obj_Validator->valPassword($_REQUEST['password']) + 10; }

// Success: Input valid
if (count($aMsgCds) == 0)
{
	if ($obj_mPoint->auth($_REQUEST['username'], $_REQUEST['password'], $iUserID) === 10)
	{
		$aTxnInfo = $obj_mPoint->getLastTransaction($iUserID, $_REQUEST['clientid']);
		if (is_array($aTxnInfo) === true && count($aTxnInfo) == 2)
		{
			echo "id=". $aTxnInfo["id"] ."&timestamp=". urlencode($aTxnInfo["timestamp"]);
		}
		// Unable to find mPoint Transaction
		else { header("HTTP/1.0 206 Partial Content"); }
	}
	// Error: Unauthorized access
	else { header("HTTP/1.0 403 Forbidden"); }
}
// Error: Invalid input
else { header("HTTP/1.0 400 Bad Request"); }
?>