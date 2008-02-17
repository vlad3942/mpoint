<?php
/**
 * This files contains the Controller for mPoint's SMS API.
 * The Controller will receive an MO-SMS from GoMobile, construct a payment link for mPoint and send it to the customer
 * as an MT-WAP Push or an MT-SMS depending on the Mobile Network Operator
 * 
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package SMS_Purchase
 * @subpackage Buy
 * @version 1.0
 */

// Require Global Include File
require_once("../inc/include.php");

// Require the PHP API for handling the connection to GoMobile
require_once(sAPI_CLASS_PATH ."/gomobile.php");

// Require Business logic for the Mobile Web module
require_once(sCLASS_PATH ."/mobile_web.php");
// Require Business logic for the SMS Purchase module
require_once(sCLASS_PATH ."/sms_purchase.php");

header("content-type: text/plain");

// Parse received MO-SMS
$obj_MsgInfo = GoMobileMessage::produceMessage($HTTP_RAW_POST_DATA);

// Instantiate mPoint object to handle the transaction
$obj_mPoint = SMS_Purchase::produceSMS_Purchase($_OBJ_DB, $_OBJ_TXT, $obj_MsgInfo);

$iTxnID = $obj_mPoint->newTransaction(Constants::iSMS_PURCHASE_TYPE);
// Update Transaction Log
$obj_TxnInfo = new TxnInfo($iTxnID, Constants::iSMS_PURCHASE_TYPE, $obj_mPoint->getClientConfig(), $obj_mPoint->getClientConfig()->getKeywordConfig()->getPrice(), -1, $obj_MsgInfo->getAddress(), $obj_MsgInfo->getOperator(), $obj_mPoint->getClientConfig()->getLogoURL(), $obj_mPoint->getClientConfig()->getCSSURL(), $obj_mPoint->getClientConfig()->getAcceptURL(), $obj_mPoint->getClientConfig()->getCancelURL(), $obj_mPoint->getClientConfig()->getCallbackURL(), $obj_mPoint->getClientConfig()->getLanguage() );
$obj_mPoint->logTransaction($obj_TxnInfo);
// Log additional data
$obj_mPoint->logProducts();

// Transafer GoMobile Username / Password global array of GoMobile Connection Information
$aGM_CONN_INFO["username"] = $obj_TxnInfo->getClientConfig()->getUsername();
$aGM_CONN_INFO["password"] = $obj_TxnInfo->getClientConfig()->getPassword();
// Confirm to GoMobile that the MO-SMS has been received
$obj_ConnInfo = GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO);
$obj_GoMobile = new GoMobileClient($obj_ConnInfo);
$obj_GoMobile->communicate($obj_MsgInfo);

// Construct & Send mPoint Payment link to the customer
$sURL = $obj_mPoint->constLink($obj_MsgInfo->getOperator() );
$obj_mPoint->sendLink(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO), $obj_TxnInfo, $sURL);
?>