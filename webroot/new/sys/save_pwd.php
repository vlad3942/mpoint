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
 * @version 1.10
 */

// Require Global Include File
require_once("../../inc/include.php");

// Require the PHP API for handling the connection to GoMobile
require_once(sAPI_CLASS_PATH ."/gomobile.php");

// Require Business logic for the My Account component
require_once(sCLASS_PATH ."/my_account.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");

$_SESSION['temp'] = $_POST;

$aMsgCds = array();

// Initialize Standard content Object
$obj_mPoint = new MyAccount($_OBJ_DB, $_OBJ_TXT);
$obj_Validator = new Validate();

if ($obj_Validator->valPassword($_POST['pwd']) != 10) { $aMsgCds[] = $obj_Validator->valPassword($_POST['pwd']) + 10; }
if ($obj_Validator->valPassword($_POST['rpt']) != 10) { $aMsgCds[] = $obj_Validator->valPassword($_POST['rpt']) + 20; }
if (count($aMsgCds) == 0 && $_POST['pwd'] != $_POST['rpt']) { $aMsgCds[] = 31; }
// Previous automatic detection of Country failed
if ($_SESSION['obj_Info']->getInfo("countryid") < 100)
{
	$code = $obj_Validator->valCountry($_OBJ_DB, $_POST['countryid']);
	if ($code == 10) { $obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, $_POST['countryid'], -1); }
	else { $aMsgCds[] = $code + 40; }
}
// Country was automatically detected
else
{
	$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB,$_SESSION['obj_Info']->getInfo("countryid"), -1);
	$_POST['countryid'] = $_SESSION['obj_Info']->getInfo("countryid");
}
// Previous automatic detection of Mobile Number failed
if ($_SESSION['obj_Info']->getInfo("mobile") < 100)
{
	// Country Valid
	if ($obj_Validator->valCountry($_OBJ_DB, $_POST['countryid']) == 10)
	{
		// Re-instatiate objects containing validation login
		$obj_mPoint = new MyAccount($_OBJ_DB, $_OBJ_TXT, $obj_ClientConfig->getCountryConfig() );
		$obj_Validator = new Validate($obj_ClientConfig->getCountryConfig() );
		
		// Mobile Number appears to be valid
		if ($obj_Validator->valMobile($_POST['mobile']) == 10)
		{
			$code = $obj_mPoint->valMobile(-1, $_POST['mobile']);
			
			if ($code != 10 && $code != 3) { $aMsgCds[] = $code + 53; }
		}
		else { $aMsgCds[] = $obj_Validator->valMobile($_POST['mobile']) + 50; }
	}
}
// Mobile Number was automatically detected
else { $_POST['mobile'] = $_SESSION['obj_Info']->getInfo("mobile"); }
// Validate Transfer Code allowing it not be provided
$code = $obj_Validator->valChecksum($_OBJ_DB, $_POST['chk']);
if ($code == 10)
{
	// Validate Transfer Code against Mobile Number
	if ($obj_mPoint->valChecksum($_POST['chk'], $_POST['mobile']) != 10)
	{
		$aMsgCds[] = $obj_mPoint->valChecksum($_POST['chk'], $_POST['mobile']) + 64;
	}	
}
elseif ($code > 1) { $aMsgCds[] = $code + 60; }

// Success: Input Valid
if (count($aMsgCds) == 0)
{
	// Start database transaction
	$_OBJ_DB->query("BEGIN");
	
	$iAccountID = $obj_mPoint->getAccountID($obj_ClientConfig->getCountryConfig(), $_POST['mobile']);
	if ($iAccountID < 0) { $iAccountID = $obj_mPoint->newAccount($obj_ClientConfig->getCountryConfig()->getID(), "", $_POST['pwd']); }
	
	// Save Password
	if ($obj_mPoint->savePassword($iAccountID, $_POST['pwd']) === true)
	{
		// Mobile Number was automatically detected
		if ($_SESSION['obj_Info']->getInfo("mobile") > 100)
		{
			// Commit database transaction
			$_OBJ_DB->query("COMMIT");
			$_SESSION['obj_Info']->setInfo("accountid", $iAccountID);
			$_SESSION['obj_CountryConfig'] = $obj_ClientConfig->getCountryConfig();
			$aMsgCds[] = 101;
		}
		// Previous automatic detection of Mobile Number failed
		else
		{
			// Clear Mobile Number
			if ($obj_mPoint->saveMobile($iAccountID, null) === true)
			{
				// Send Activation Code
				if ($obj_mPoint->sendCode(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO), $iAccountID, $_POST['mobile']) == 200)
				{
					// Commit database transaction
					$_OBJ_DB->query("COMMIT");
					$_SESSION['temp']['accountid'] = $iAccountID;
					$_SESSION['temp']['obj_CountryConfig'] = $obj_ClientConfig->getCountryConfig();
					$aMsgCds[] = 102;
				}
				// Error: Unable to Send Activation Code
				else
				{
					// Abort database transaction and rollback to previous state
					$_OBJ_DB->query("ROLLBACK");
					$aMsgCds[] = 93;
				}
			}
			// Error: Unable to Clear Mobile Number
			else
			{
				// Abort database transaction and rollback to previous state
				$_OBJ_DB->query("ROLLBACK");
				$aMsgCds[] = 92;
			}
		}
	}
	// Error: Unable to Save Password
	else
	{
		// Abort database transaction and rollback to previous state
		$_OBJ_DB->query("ROLLBACK");
		$aMsgCds[] = 91;
	}
}

if ($aMsgCds[0] == 101) { $sFile = "step3.php"; }
elseif ($aMsgCds[0] == 102) { $sFile = "step2.php"; }
else { $sFile = "step1.php"; }

$msg = "";
for ($i=0; $i<count($aMsgCds); $i++)
{
	$msg .= "&msg=". $aMsgCds[$i];
}

header("content-type: text/plain");
header("content-length: 0");

header("location: http://". $_SERVER['HTTP_HOST'] ."/new/". $sFile ."?". session_name() ."=". session_id() . $msg);
?>