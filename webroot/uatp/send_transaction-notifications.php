<?php
/**
 * To send transaction notification
 *
 * @author Abhinav Shaha
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package API
 * File Name:send_transaction-notifications.php
 * x-www-form-urlencoded params :
 clientid:10069
 pspid:52
 cut-off-time:02:00
 */

// Require Global Include File
require_once("../inc/include.php");
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");
// Require API for txnpassbook
require_once sCLASS_PATH . '/txn_passbook.php';
require_once sCLASS_PATH . '/passbookentry.php';
// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require Business logic for the End-User Account Factory Provider
require_once(sCLASS_PATH ."/customer_info.php");
// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for Capture component (for use with auto-capture functionality)
require_once(sCLASS_PATH ."/capture.php");
// Require specific Business logic for the CPM PSP component
require_once(sINTERFACE_PATH ."/cpm_psp.php");
// Require specific Business logic for the CPM ACQUIRER component
require_once(sINTERFACE_PATH ."/cpm_acquirer.php");
// Require specific Business logic for the CPM GATEWAY component
require_once(sINTERFACE_PATH ."/cpm_gateway.php");
// Require specific Business logic for the UATP component
require_once(sCLASS_PATH . "/uatp_card_account.php");
// Require data class for Payment Service Provider Configurations
require_once(sCLASS_PATH ."/pspconfig.php");
// Require specific Business logic for the UATP component
require_once(sCLASS_PATH . "/uatp.php");
// Require specific Business logic for the eGHL FPX component
require_once(sCLASS_PATH ."/chase.php");


$aMsgCds = array();
ignore_user_abort(true);
set_time_limit(120);

$interval = '1 Day ';
$cutofftime = '00:00';
$inputPSPID = $_REQUEST['pspid'];
$pspid = 50;
$clientid = $_REQUEST['clientid'];
if(isset($_REQUEST['cut-off-time']))
{
	$cutofftime = $_REQUEST['cut-off-time'];
}

$query = "SELECT tt.id, tp.extref, tp.status, tp.performedopt
			FROM log.transaction_tbl tt
			INNER JOIN log.txnpassbook_tbl tp
			ON tp.clientid = tt.clientid
			AND tt.id = tp.transactionid
			WHERE tt.pspid = ".$inputPSPID."
			AND tt.clientid = ".$clientid."
			AND tp.performedopt IN ('".Constants::iPAYMENT_CAPTURED_STATE."','".Constants::iPAYMENT_CANCELLED_STATE."','".Constants::iPAYMENT_REFUNDED_STATE."')
			AND tp.status NOT IN ('".Constants::sPassbookStatusDone."','".Constants::sPassbookStatusInvalid."')
			AND tp.modified <= '".date('Y-m-d').' '.$cutofftime."'
			AND tp.modified >= (now() - INTERVAL '". $interval ."')
			GROUP BY tt.id, tp.extref, tp.status, tp.performedopt,tp.extref
			ORDER BY tt.id ASC";

//echo $query;die;
$resultObj = $_OBJ_DB->query($query);
$xml = '';

while ($RESULTSET = $_OBJ_DB->fetchName($resultObj))
{
	$aTicketNumbers = array();
	$txnid = $RESULTSET['ID'];
	$txnStateName = $RESULTSET['STATUS'];
	$iStateID = intval($RESULTSET['PERFORMEDOPT']);
	$exteRef = $RESULTSET['EXTREF'];
	$performedOptArray = array($iStateID);

	$subQuery = "SELECT tp.extref, tp.amount
			FROM log.txnpassbook_tbl tp
			WHERE tp.clientid = ".$clientid."
			AND tp.transactionid = ".$txnid."
			AND tp.id IN (".$exteRef.")
			ORDER BY tp.id ASC";

	//echo $subQuery;die;
	$queryResultObj = $_OBJ_DB->query($subQuery);
	while ($SUBRESULTSET = $_OBJ_DB->fetchName($queryResultObj))
	{
		$ticketNumbers = $SUBRESULTSET['EXTREF'];
		$iAmount = intval($SUBRESULTSET['AMOUNT']);
		$aTicketNumbers[$ticketNumbers] = $iAmount;
	}

	try
	{
		$obj_TxnInfo = TxnInfo::produceInfo($txnid, $_OBJ_DB);
		$_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH . $obj_TxnInfo->getLanguage() ."/global.txt", sLANGUAGE_PATH . $obj_TxnInfo->getLanguage() ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");
		$obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), intval($pspid));
		$obj_TxnInfo->produceOrderConfig($_OBJ_DB, $aTicketNumbers);

		$txnPassbookObj = TxnPassbook::Get($_OBJ_DB, $obj_TxnInfo->getID(),$obj_TxnInfo->getClientConfig()->getID());
		$obj_UATP = Callback::producePSP($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO, $obj_PSPConfig);
		$code = $obj_UATP->initCallback($obj_PSPConfig, $obj_TxnInfo, $iStateID, $txnStateName, $obj_TxnInfo->getCardID(), $performedOptArray, $txnPassbookObj);

		if($code === 1000)
		{
			$xml .= '<transaction id="'.$txnid.'"><status code="1000">Callback Success</status></transaction>';
		}
		else
		{
			$xml .= '<transaction id="'.$txnid.'"><status code="'.$code.'">Callback Failed</status></transaction>';
		}
	}
	catch (TxnInfoException $e)
	{
		$xml .= '<status code="'. $e->getCode() .'">'. htmlspecialchars($e->getMessage(), ENT_NOQUOTES). '</status>';
		trigger_error($e->getMessage() ."\n". $HTTP_RAW_POST_DATA, E_USER_WARNING);
	}
	catch (CallbackException $e)
	{
		$xml .= '<status code="'. $e->getCode() .'">'. htmlspecialchars($e->getMessage(), ENT_NOQUOTES). '</status>';
		trigger_error($e->getMessage() ."\n". $HTTP_RAW_POST_DATA, E_USER_WARNING);
	}
}

header("HTTP/1.1 200 Ok");
header("Content-Type: text/xml; charset=\"UTF-8\"");
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<root>';
echo '<transactions>';
echo $xml;
echo '</transactions>';
echo '</root>';
?>