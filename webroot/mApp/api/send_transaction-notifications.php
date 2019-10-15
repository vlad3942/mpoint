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
 	pspid:50
 	cut-off-time:23:59 [HH:MM]
 */

// Require Global Include File
require_once("../../inc/include.php");
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
// Require Business logic for the Mobile Web module
require_once(sCLASS_PATH ."/mobile_web.php");
// Require Business logic for the Select Credit Card component
require_once(sCLASS_PATH ."/credit_card.php");
// Require specific Business logic for the DIBS component
require_once(sCLASS_PATH ."/dibs.php");
// Require specific Business logic for the WorldPay component
require_once(sCLASS_PATH ."/worldpay.php");
// Require specific Business logic for the PayEx component
require_once(sCLASS_PATH ."/payex.php");
// Require specific Business logic for the NetAxept component
require_once(sCLASS_PATH ."/netaxept.php");
// Require specific Business logic for the Stripe component
if (function_exists("json_encode") === true && function_exists("curl_init") === true)
{
	require_once(sCLASS_PATH ."/stripe.php");
}
// Require specific Business logic for the MobilePay component
require_once(sCLASS_PATH ."/mobilepay.php");
// Require specific Business logic for the Adyen component
require_once(sCLASS_PATH ."/adyen.php");
// Require specific Business logic for the VISA checkout component
require_once(sCLASS_PATH ."/visacheckout.php");
// Require specific Business logic for the Apple Pay component
require_once(sCLASS_PATH ."/applepay.php");
// Require specific Business logic for the Data Cash component
require_once(sCLASS_PATH ."/datacash.php");
// Require specific Business logic for the Mada Mpgs component
require_once(sCLASS_PATH ."/mada_mpgs.php");
// Require specific Business logic for the Master Pass component
require_once(sCLASS_PATH ."/masterpass.php");
// Require specific Business logic for the AMEX Express Checkout component
require_once(sCLASS_PATH ."/amexexpresscheckout.php");
// Require specific Business logic for the WireCard component
require_once(sCLASS_PATH ."/wirecard.php");
// Require specific Business logic for the GlobalCollect component
require_once(sCLASS_PATH ."/globalcollect.php");
// Require specific Business logic for the Secure Trading component
require_once(sCLASS_PATH ."/securetrading.php");
// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
// Require specific Business logic for the PayFort component
require_once(sCLASS_PATH ."/payfort.php");
// Require specific Business logic for the PayPal component
require_once(sCLASS_PATH ."/paypal.php");
// Require specific Business logic for the CCAvenue component
require_once(sCLASS_PATH ."/ccavenue.php");
// Require specific Business logic for the 2C2P component
require_once(sCLASS_PATH ."/ccpp.php");
// Require specific Business logic for the MayBank component
require_once(sCLASS_PATH ."/maybank.php");
// Require specific Business logic for the PublicBank component
require_once(sCLASS_PATH ."/publicbank.php");
// Require specific Business logic for the AliPay component
require_once(sCLASS_PATH ."/alipay.php");
require_once(sCLASS_PATH ."/alipay_chinese.php");
// Require specific Business logic for the POLi component
require_once(sCLASS_PATH ."/poli.php");
// Require specific Business logic for the Qiwi component
require_once(sCLASS_PATH ."/qiwi.php");
// Require specific Business logic for the Klarna component
require_once(sCLASS_PATH ."/klarna.php");
// Require specific Business logic for the MobilePay Online component
require_once(sCLASS_PATH ."/mobilepayonline.php");
// Require specific Business logic for the Nets component
require_once(sCLASS_PATH ."/nets.php");
// Require specific Business logic for the mVault component
require_once(sCLASS_PATH ."/mvault.php");
// Require specific Business logic for the Trustly component
require_once(sCLASS_PATH ."/trustly.php");
// Require specific Business logic for the PayTabs component
require_once(sCLASS_PATH ."/paytabs.php");
// Require specific Business logic for the 2C2P ALC component
require_once(sCLASS_PATH ."/ccpp_alc.php");
// Require specific Business logic for the Citcon component
require_once(sCLASS_PATH ."/citcon.php");
// Require specific Business logic for the PPRO component
require_once(sCLASS_PATH ."/ppro.php");
require_once(sCLASS_PATH ."/bre.php");
// Require specific Business logic for the Amex component
require_once(sCLASS_PATH ."/amex.php");
// Require specific Business logic for the CHUBB component
require_once(sCLASS_PATH ."/chubb.php");
// Require Data Class for Client Information
require_once(sCLASS_PATH ."/clientinfo.php");
// Require specific Business logic for the Google Pay component
require_once(sCLASS_PATH ."/googlepay.php");
// Require specific Business logic for the UATP component
require_once(sCLASS_PATH . "/uatp.php");
// Require specific Business logic for the eGHL FPX component
require_once(sCLASS_PATH . "/eghl.php");
// Require specific Business logic for the Chase component
require_once(sCLASS_PATH ."/chase.php");
require_once(sCLASS_PATH ."/payment_processor.php");
require_once(sCLASS_PATH ."/wallet_processor.php");
require_once(sCLASS_PATH ."/post_auth_action.php");
// Require specific Business logic for the Cielo component
require_once(sCLASS_PATH ."/cielo.php");


$aMsgCds = array();
ignore_user_abort(true);
set_time_limit(120);

$interval = '1 Day ';
$cutofftime = '00:00';
$pspid = $_REQUEST['pspid'];
$clientid = $_REQUEST['clientid'];

if(isset($_REQUEST['cut-off-time']))
{
	$cutofftime = $_REQUEST['cut-off-time'];
}

$query = "SELECT tt.id, tp.extref, tp.status, tp.performedopt
			FROM log.transaction_tbl tt
			INNER JOIN log.txnpassbook_tbl tp
			ON tt.id = tp.transactionid
			WHERE tt.pspid = ".$pspid."
			AND tt.clientid = ".$clientid."
			AND tp.performedopt IN ('".Constants::iPAYMENT_CAPTURED_STATE."','".Constants::iPAYMENT_CANCELLED_STATE."','".Constants::iPAYMENT_REFUNDED_STATE."')
			AND tp.status NOT IN ('".Constants::sPassbookStatusDone."')
			AND tp.created <= '".date('Y-m-d').' '.$cutofftime."'
			AND tp.created >= (now() - INTERVAL '". $interval ."')
			GROUP BY tt.id, tp.extref, tp.status, tp.performedopt
			ORDER BY tt.id ASC";

//echo $query;die;
$resultObj = $_OBJ_DB->query($query);
$xml = '';

while ($RESULTSET = $_OBJ_DB->fetchName($resultObj))
{
	$id = $RESULTSET['ID'];
	$ticketNumbers = $RESULTSET['EXTREF'];
	$txnStateName = $RESULTSET['STATUS'];
	$iStateID = $RESULTSET['PERFORMEDOPT'];
	$aTicketNumbers = explode(',',$ticketNumbers);
	try
	{
		$obj_TxnInfo = TxnInfo::produceInfo($id, $_OBJ_DB);
		$_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH . $obj_TxnInfo->getLanguage() ."/global.txt", sLANGUAGE_PATH . $obj_TxnInfo->getLanguage() ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");
		$obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), intval($pspid));

		$obj_TxnInfo->produceOrderConfig($_OBJ_DB, $aTicketNumbers);
		$aMessages = $obj_TxnInfo->getMessageHistory($_OBJ_DB);
		$createdtimestamp = null;
		foreach ($aMessages as $m) {
			$iMessageID = (integer)$m["id"];
			$iStateId = (integer)$m["stateid"];
			if($iStateId === $iStateID)
			{
				$createdtimestamp = $m["created"];
				break;
			}
		}
		$obj_UATP = Callback::producePSP($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO, $obj_PSPConfig);
		$code = $obj_UATP->initCallback($obj_PSPConfig, $obj_TxnInfo, $iStateID, $txnStateName, $obj_TxnInfo->getCardID(),$createdtimestamp);

		if($code === 1000)
		{
			$xml .= '<transaction id="'.$id.'"><status code="1000">Callback Success</status></transaction>';
		}
		else
		{
			$xml .= '<transaction id="'.$id.'"><status code="'.$code.'">Callback Failed</status></transaction>';
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