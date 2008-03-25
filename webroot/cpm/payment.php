<?php
/**
 * This files contains the Controller for mPoint's implementation of Premium SMS Billing.
 * The Controller will construct a Premium MT-SMS and send it to the customer.
 * 
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Payment
 * @subpackage CellpointMobile
 * @version 1.0
 */

// Require Global Include File
require_once("../inc/include.php");

// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require general Business logic for the Cellpoint Mobile module
require_once(sCLASS_PATH ."/cpm.php");

// Require the PHP API for handling the connection to GoMobile
require_once(sAPI_CLASS_PATH ."/gomobile.php");

header("content-type: text/plain");

$obj_mPoint = new CellpointMobile($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo']);

try
{
	// Send Billing SMS through GoMobile
	$obj_MsgInfo = $obj_mPoint->sendBillingSMS(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO) );

	ignore_user_abort(true);
	// Re-Direct customer
	header("Content-Length: 0");
	header("location: http://". $_SERVER['HTTP_HOST'] ."/pay/accept.php?". session_name() ."=". session_id() );
	header("Connection: close");
	flush();

	// Initialise Callback to Client
	$obj_mPoint->initCallback(HTTPConnInfo::produceConnInfo($aCPM_CONN_INFO), $obj_MsgInfo);
}
// Error: Billing SMS rejected by GoMobile
catch (mPointException  $e)
{
	header("location: http://". $_SERVER['HTTP_HOST'] ."/pay/card.php?". session_name() ."=". session_id() ."&msg=99");
}
?>