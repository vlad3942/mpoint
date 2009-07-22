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
 * @version 1.0
 */

// Require Global Include File
require_once("../../inc/include.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");

// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for the DIBS component
require_once(sCLASS_PATH ."/dibs.php");
// Require general Business logic for the Cellpoint Mobile module
require_once(sCLASS_PATH ."/cpm.php");

ignore_user_abort(true);
set_time_limit(0);

header("content-type: text/plain");

$_SESSION['temp'] = $_POST;

$aMsgCds = array();
$obj_Validator = new Validate($_SESSION['obj_TxnInfo']->getClientConfig()->getCountryConfig() );
$obj_mPoint = new EndUserAccount($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo']->getClientConfig() );

if (array_key_exists("prepaid", $_POST) === true && $_POST['prepaid'] == "true")
{
	switch ($_POST['cardid'])
	{
	case (-1):	// Pre-Paid account selected
	case "-1":
		if ($obj_Validator->valAccount($_OBJ_DB, $_SESSION['obj_TxnInfo']->getAccountID(), $_SESSION['obj_TxnInfo']->getAmount() ) != 10) { $aMsgCds[] = $obj_Validator->valAccount($_OBJ_DB, $_SESSION['obj_TxnInfo']->getAccountID(), $_SESSION['obj_TxnInfo']->getAmount() ) + 11; }
		break;
	case (0):	// Account available but nothing has been selected
	case "0":
		$aMsgCds[] = 11;
		break;
	default:	// Stored Card selected
		if ($obj_Validator->valStoredCard($_OBJ_DB, $_SESSION['obj_TxnInfo']->getAccountID(), $_POST['cardid']) != 10) { $aMsgCds[] = $obj_Validator->valStoredCard($_OBJ_DB, $_SESSION['obj_TxnInfo']->getAccountID(), $_POST['cardid']) + 20; }
		break;
	}
}
elseif ($obj_Validator->valStoredCard($_OBJ_DB, $_SESSION['obj_TxnInfo']->getAccountID(), $_POST['cardid']) != 10) { $aMsgCds[] = $obj_Validator->valStoredCard($_OBJ_DB, $_SESSION['obj_TxnInfo']->getAccountID(), $_POST['cardid']) + 20; }
if ($obj_Validator->valPassword($_POST['pwd']) != 10) { $aMsgCds[] = $obj_Validator->valPassword($_POST['pwd']) + 30; }

// Success: Input Valid
if (count($aMsgCds) == 0)
{
	$msg = $obj_mPoint->auth($_SESSION['obj_TxnInfo']->getAccountID(), $_POST['pwd']);
	if ($msg == 10)
	{
		if ($_POST['cardid'] == -1)
		{
			$obj_mPoint->purchase($_SESSION['obj_TxnInfo']->getAccountID(), $_SESSION['obj_TxnInfo']->getID(), $_SESSION['obj_TxnInfo']->getAmount() );
			$obj_PSP = new CellpointMobile($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo']);
			// Initialise Callback to Client
			$obj_PSP->initCallback(HTTPConnInfo::produceConnInfo($aCPM_CONN_INFO), Constants::iEMONEY_CARD, Constants::iPAYMENT_ACCEPTED_STATE);
			$aMsgCds[] = 100;
		}
		else
		{
			$obj_XML = simplexml_load_string($obj_mPoint->getStoredCards($_SESSION['obj_TxnInfo']->getAccountID() ) );
			$obj_XML = $obj_XML->xpath("/stored-cards/card[@id = ". $_POST['cardid'] ."]");
			$obj_XML = $obj_XML[0];

			switch (intval($obj_XML["pspid"]) )
			{
			case (Constants::iDIBS_PSP):	// DIBS
				// Authorise payment with PSP based on Ticket
				$obj_PSP = new DIBS($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo']);
				$iTxnID = $obj_PSP->authTicket( (integer) $obj_XML->ticket);
				if ($iTxnID > 0)
				{
					// Initialise Callback to Client
					$aCPM_CONN_INFO["path"] = "/callback/dibs.php";
					$obj_PSP->initCallback(HTTPConnInfo::produceConnInfo($aCPM_CONN_INFO), intval($obj_XML->type["id"]), $iTxnID);
					$aMsgCds[] = 100;
				}
				else { $aMsgCds[] = 51; }
				break;
			default:	// Unkown Error
				$aMsgCds[] = 59;
				break;
			}
		}
	}
	else { $aMsgCds[] = $msg + 40; }
}

$msg = "";
if ($aMsgCds[0] == 100) { $sPath = "pay/accept.php"; }
else
{
	$sPath = "cpm/payment.php";
	for ($i=0; $i<count($aMsgCds); $i++)
	{
		$msg .= "&msg=". $aMsgCds[$i];
	}
}

header("location: http://". $_SERVER['HTTP_HOST'] ."/". $sPath ."?". session_name() ."=". session_id() . $msg);
?>