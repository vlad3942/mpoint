<?php
/**
 * This files contains the Controller for mPoint's SMS Purchase API.
 * The Controller will receive an MO-SMS from GoMobile and check if the the End-User has accepted the purchase.
 * A callback is generated if the if the purchase is accepted and the amount charged to the End-User's prepaid E-Money Account.   
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Payment
 * @subpackage SMS
 * @version 1.00
 */

// Require Global Include File
require_once("../../inc/include.php");

// Require the PHP API for handling the connection to GoMobile
require_once(sAPI_CLASS_PATH ."/gomobile.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require Business logic for the Mobile Web module
require_once(sCLASS_PATH ."/mobile_web.php");
// Require Business logic for the SMS Purchase module
require_once(sCLASS_PATH ."/sms_purchase.php");

// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require general Business logic for the Cellpoint Mobile module
require_once(sCLASS_PATH ."/cpm.php");

header("content-type: text/plain");

// Parse received MO-SMS
$obj_MsgInfo = GoMobileMessage::produceMessage($HTTP_RAW_POST_DATA);

// Instantiate mPoint object to handle the transaction
$obj_mPoint = SMS_Purchase::produceSMS_Purchase($_OBJ_DB, $obj_MsgInfo);

$obj_TxnInfo = TxnInfo::produceInfo($obj_mPoint->findTxnIDFromSMS($obj_MsgInfo), $_OBJ_DB);

// Associate End-User Account (if exists) with Transaction
$iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_TxnInfo->getClientConfig(), $obj_TxnInfo->getMobile() );
if ($iAccountID == -1 && trim($obj_TxnInfo->getEMail() ) != "") { $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_TxnInfo->getEMail() ); }
$obj_TxnInfo->setAccountID($iAccountID);

// Transfer GoMobile Username / Password global array of GoMobile Connection Information
$aGM_CONN_INFO["username"] = $obj_TxnInfo->getClientConfig()->getUsername();
$aGM_CONN_INFO["password"] = $obj_TxnInfo->getClientConfig()->getPassword();
// Confirm to GoMobile that the MO-SMS has been received
$obj_ConnInfo = GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO);
$obj_GoMobile = new GoMobileClient($obj_ConnInfo);
$obj_GoMobile->communicate($obj_MsgInfo);

// Determine End-User Response
switch (true)
{
case (in_array(strtoupper($obj_MsgInfo->getBody() ), $aACCEPT_WORDS) ):	// Payment Accepted by End-User
	$obj_mPoint->purchase($obj_TxnInfo->getAccountID(), $obj_TxnInfo->getID(), $obj_TxnInfo->getAmount() );
	$obj_PSP = new CellpointMobile($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo);
	// Initialise Callback to Client
	$obj_PSP->initCallback(HTTPConnInfo::produceConnInfo($aCPM_CONN_INFO), Constants::iEMONEY_CARD, Constants::iPAYMENT_ACCEPTED_STATE);
	break;
case (in_array(strtoupper($obj_MsgInfo->getBody() ), $aREJECT_WORDS) ):	// Payment Rejected by End-User
	$obj_PSP = new CellpointMobile($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo);
	// Initialise Callback to Client
	$obj_PSP->initCallback(HTTPConnInfo::produceConnInfo($aCPM_CONN_INFO), Constants::iEMONEY_CARD, Constants::iPAYMENT_REJECTED_STATE);
	break;
default:	// Unknown Response
	$obj_MsgInfo = GoMobileMessage::produceMessage(Constants::iMT_SMS_TYPE, $obj_MsgInfo->getCountry(), $obj_MsgInfo->getOperator(), $obj_MsgInfo->getChannel(), $obj_MsgInfo->getKeyword(), Constants::iMT_PRICE, $obj_MsgInfo->getSender(), $_OBJ_TXT->_("SMS Purchase - Unknown Reponse"), $obj_MsgInfo->getGoMobileID() );
	$obj_MsgInfo->setDescription("mPoint - SMS Purchase");
	$obj_MsgInfo->enableConcatenation();
	$obj_GoMobile->communicate($obj_MsgInfo);
	break;
}
?>