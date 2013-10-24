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
 * @version 1.21
 */

// Require Global Include File
require_once("../../inc/include.php");

// Require the PHP API for handling the connection to GoMobile
require_once(sAPI_CLASS_PATH ."/gomobile.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");

// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require general Business logic for the Cellpoint Mobile module
require_once(sCLASS_PATH ."/cpm.php");
// Require specific Business logic for the DIBS component
require_once(sCLASS_PATH ."/dibs.php");
// Require specific Business logic for the WannaFind component
require_once(sCLASS_PATH ."/wannafind.php");
// Require specific Business logic for the WorldPay component
require_once(sCLASS_PATH ."/worldpay.php");

ignore_user_abort(true);
set_time_limit(0);
set_time_limit(120);
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
		if ($obj_Validator->valAccount($_OBJ_DB, $_POST['euaid'], $_SESSION['obj_TxnInfo']->getAmount() ) != 10) { $aMsgCds[] = $obj_Validator->valAccount($_OBJ_DB, $_POST['euaid'], $_SESSION['obj_TxnInfo']->getAmount() ) + 11; }
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

if ($_SESSION['obj_TxnInfo']->getAmount() > $_SESSION['obj_TxnInfo']->getClientConfig()->getCountryConfig()->getMaxPSMSAmount()
	&& ($_SESSION['obj_Info']->getInfo("auth-token") === false || strlen($_SESSION['obj_TxnInfo']->getAuthenticationURL() ) == 0) )
{	
	if ($obj_Validator->valPassword($_POST['pwd']) != 10) { $aMsgCds[] = $obj_Validator->valPassword($_POST['pwd']) + 30; }
}

// Success: Input Valid
if (count($aMsgCds) == 0)
{
	if ($_SESSION['obj_TxnInfo']->getAmount() > $_SESSION['obj_TxnInfo']->getClientConfig()->getCountryConfig()->getMaxPSMSAmount() )
	{
		if ($_SESSION['obj_Info']->getInfo("auth-token") === false || strlen($_SESSION['obj_TxnInfo']->getAuthenticationURL() ) == 0)
		{
			$msg = $obj_mPoint->auth($_POST['euaid'], $_POST['pwd']);
		}
		else { $msg = $obj_mPoint->auth(HTTPConnInfo::produceConnInfo($_SESSION['obj_TxnInfo']->getAuthenticationURL() ), $_SESSION['obj_TxnInfo']->getCustomerRef(), $_SESSION['obj_Info']->getInfo("auth-token") ); }
	}
	else { $msg = 10; }
	if ($msg >= 10)
	{
		// Payment has not previously been attempted for transaction
		$_OBJ_DB->query("BEGIN");
		if (count($obj_mPoint->getMessageData($_SESSION['obj_TxnInfo']->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE, true) ) == 0)
		{
			// Add control state and immediately commit database transaction
			$obj_mPoint->newMessage($_SESSION['obj_TxnInfo']->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE, serialize(array("cardid" => $_POST['cardid']) ) );
			$_OBJ_DB->query("COMMIT");
			
			// Pay using the End-User's e-money based prepaid account.
			if ($_POST['cardid'] == -1)
			{
				// End-User has an e-money based prepaid account but no link between the Account and the Client exists
				if ($_SESSION['obj_TxnInfo']->getAccountID() <= 0)
				{
					// Find the unique ID for the End-User's Account
					$obj_Home = new Home($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo']->getClientConfig()->getCountryConfig() );
					$iAccountID = $obj_Home->getAccountID($_SESSION['obj_TxnInfo']->getClientConfig()->getCountryConfig(), $_SESSION['obj_TxnInfo']->getMobile() );
					if ($iAccountID == -1 && trim($_SESSION['obj_TxnInfo']->getEMail() ) != "") { $iAccountID = $obj_Home->getAccountID($_SESSION['obj_TxnInfo']->getClientConfig()->getCountryConfig(), $_SESSION['obj_TxnInfo']->getEMail() ); }
					$_SESSION['obj_TxnInfo']->setAccountID($iAccountID);
					// Create a link between the End-User Account and the Client
					$obj_mPoint->link($iAccountID);
				}
				// Complete the e-money based purchase
				$obj_mPoint->purchase($_SESSION['obj_TxnInfo']->getAccountID(), Constants::iPURCHASE_USING_EMONEY, $_SESSION['obj_TxnInfo']->getID(), $_SESSION['obj_TxnInfo']->getAmount() );
				$obj_PSP = new CellpointMobile($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo']);
				// Initialise Callback to Client
				$obj_PSP->initCallback(HTTPConnInfo::produceConnInfo($aCPM_CONN_INFO), Constants::iEMONEY_CARD, Constants::iPAYMENT_ACCEPTED_STATE);
				$aMsgCds[] = 100;
			}
			// Pay using a Stored Payment Card
			else
			{
				$obj_XML = simplexml_load_string($obj_mPoint->getStoredCards($_SESSION['obj_TxnInfo']->getAccountID(), $_SESSION['obj_TxnInfo']->getClientConfig()->showAllCards() ) );
				$obj_XML = $obj_XML->xpath("/stored-cards/card[@id = ". $_POST['cardid'] ."]");
				$obj_XML = $obj_XML[0];
	
				switch (intval($obj_XML["pspid"]) )
				{
				case (Constants::iDIBS_PSP):	// DIBS
					// Authorise payment with PSP based on Ticket
					$obj_PSP = new DIBS($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo']);
					$iTxnID = $obj_PSP->authTicket( (integer) $obj_XML->ticket);
					// Authorization succeeded
					if ($iTxnID > 0)
					{
						// Initialise Callback to Client
						$aCPM_CONN_INFO["path"] = "/callback/dibs.php";
						$obj_PSP->initCallback(HTTPConnInfo::produceConnInfo($aCPM_CONN_INFO), intval($obj_XML->type["id"]), $iTxnID, (string) $obj_XML->mask, (string) $obj_XML->expiry);
						$aMsgCds[] = 100;
					}
					else
					{
						$obj_mPoint->delMessage($_SESSION['obj_TxnInfo']->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);
						$aMsgCds[] = 51;
					}
					break;
				case (Constants::iWANNAFIND_PSP):	// WannaFind
					// Authorise payment with PSP based on Ticket
					$obj_PSP = new WannaFind($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo']);
					$iTxnID = $obj_PSP->authTicket( (integer) $obj_XML->ticket);
					// Authorization succeeded
					if ($iTxnID > 0)
					{
						// Initialise Callback to Client
						$aCPM_CONN_INFO["path"] = "/callback/wannafind.php";
						$obj_PSP->initCallback(HTTPConnInfo::produceConnInfo($aCPM_CONN_INFO), intval($obj_XML->type["id"]), $iTxnID);
						$aMsgCds[] = 100;
					}
					else
					{
						$obj_mPoint->delMessage($_SESSION['obj_TxnInfo']->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);
						$aMsgCds[] = 51;
					}
					break;
				case (Constants::iWORLDPAY_PSP):	// WorldPay
					// Authorise payment with PSP based on Ticket
					$obj_PSP = new WorldPay($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo']);
					if ($_SESSION['obj_TxnInfo']->getMode() > 0) { $aHTTP_CONN_INFO["worldpay"]["host"] = str_replace("secure.", "secure-test.", $aHTTP_CONN_INFO["worldpay"]["host"]); }
					$aLogin = $obj_PSP->getMerchantLogin($_SESSION['obj_TxnInfo']->getClientConfig()->getID(), Constants::iWORLDPAY_PSP, true);
					$aHTTP_CONN_INFO["worldpay"]["username"] = $aLogin["username"];
					$aHTTP_CONN_INFO["worldpay"]["password"] = $aLogin["password"];
					
					$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["worldpay"]);
					$obj_XML = $obj_PSP->authTicket($obj_ConnInfo, (string) $obj_XML->ticket);
					// Authorization succeeded
					if (is_null($obj_XML) === false && ($obj_XML instanceof SimpleXMLElement) === true && intval($obj_XML["code"]) == Constants::iPAYMENT_ACCEPTED_STATE)
					{
						// Initialise Callback to Client
						$aCPM_CONN_INFO["path"] = "/callback/worldpay.php";
						$aCPM_CONN_INFO["contenttype"] = "text/xml";
						$obj_PSP->initCallback(HTTPConnInfo::produceConnInfo($aCPM_CONN_INFO), $obj_XML);
						$aMsgCds[] = 100;
					}
					else
					{
						$obj_mPoint->delMessage($_SESSION['obj_TxnInfo']->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);
						$aMsgCds[] = 51;
					}
					break;
				default:	// Unkown Error
					$obj_mPoint->delMessage($_SESSION['obj_TxnInfo']->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);
					$aMsgCds[] = 59;
					break;
				}
			}
		}
		else
		{
			$_OBJ_DB->query("COMMIT");
			$aMsgCds[] = 100;
		}
	}
	else
	{
		// Account disabled due to too many failed login attempts
		if ($msg == 3)
		{
			$obj_mPoint->sendAccountDisabledNotification(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO), $_SESSION['obj_TxnInfo']->getMobile() );
			$_SESSION['obj_TxnInfo']->setAccountID(-1);
			$_SESSION['temp'] = array();
			$sPath = "pay/card.php?";
		}
		$aMsgCds[] = $msg + 40;
	}
}

$msg = "";
if ($aMsgCds[0] == 100) { $sPath = "pay/accept.php?mpoint-id=". $_SESSION['obj_TxnInfo']->getID() ."&"; }
else
{
	if (isset($sPath) === false) { $sPath = "cpm/payment.php?"; }
	for ($i=0; $i<count($aMsgCds); $i++)
	{
		$msg .= "&msg=". $aMsgCds[$i];
	}
}

header("location: http://". $_SERVER['HTTP_HOST'] ."/". $sPath . session_name() ."=". session_id() . $msg);
?>