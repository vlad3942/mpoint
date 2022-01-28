<?php
/**
 * SDK sends in post-status request. This endpoint after detection will invoke general.php with specific error code.
 *
 * @author Arvind Halgekar
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package API
 */

// Require Global Include File
require_once("../../inc/include.php");

// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");
// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for the CPM PSP component
require_once(sINTERFACE_PATH ."/cpm_psp.php");
// Require specific Business logic for the CPM ACQUIRER component
require_once(sINTERFACE_PATH ."/cpm_acquirer.php");
// Require specific Business logic for the CPM GATEWAY component
require_once(sINTERFACE_PATH ."/cpm_gateway.php");
// Require specific Business logic for the CPM FRAUD GATEWAY component
require_once(sINTERFACE_PATH ."/cpm_fraud.php");
// Require specific Business logic for the DIBS component
require_once(sCLASS_PATH ."/dibs.php");
// Require general Business logic for the Cellpoint Mobile module
require_once(sCLASS_PATH ."/cpm.php");
// Require specific Business logic for the WannaFind component
require_once(sCLASS_PATH ."/wannafind.php");
// Require specific Business logic for the NetAxept component
require_once(sCLASS_PATH ."/netaxept.php");
// Require specific Business logic for the WorldPay component
require_once(sCLASS_PATH ."/worldpay.php");
// Require specific Business logic for the Emirates' Corporate Payment Gateway (CPG) component
require_once(sCLASS_PATH ."/cpg.php");
// Require specific Business logic for the DSB PSP component
require_once(sCLASS_PATH ."/dsb.php");
// Require specific Business logic for the VISA checkout component
require_once(sCLASS_PATH ."/visacheckout.php");
// Require specific Business logic for the Stripe component
require_once(sCLASS_PATH ."/stripe.php");
// Require specific Business logic for the Adyen component
require_once(sCLASS_PATH ."/adyen.php");
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
// Require specific Business logic for the Global Collect component
require_once(sCLASS_PATH ."/globalcollect.php");
// Require specific Business logic for the Android Pay component
require_once(sCLASS_PATH ."/androidpay.php");
// Require specific Business logic for the Secure Trading component
require_once(sCLASS_PATH ."/securetrading.php");
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
// Require specific Business logic for the MobilePay Online component
require_once(sCLASS_PATH ."/mobilepayonline.php");
// Require specific Business logic for the Klarna Online component
require_once(sCLASS_PATH ."/klarna.php");
// Require Data Class for Client Information
require_once(sCLASS_PATH ."/clientinfo.php");
// Require specific Business logic for the Nets component
require_once(sCLASS_PATH ."/nets.php");
// Require specific Business logic for the mVault component
require_once(sCLASS_PATH ."/mvault.php");
// Require specific Business logic for the 2c2p alc component
require_once(sCLASS_PATH ."/ccpp_alc.php");
// Require specific Business logic for the Google Pay component
// Require specific Business logic for the PPro component
require_once(sCLASS_PATH ."/ppro.php");
// Require specific Business logic for the Amex component
require_once(sCLASS_PATH ."/amex.php");
// Require specific Business logic for the CHUBB component
require_once(sCLASS_PATH ."/chubb.php");
// Require specific Business logic for the CHUBB component
require_once(sCLASS_PATH ."/payment_processor.php");
// Require specific Business logic for the UATP component
require_once(sCLASS_PATH . "/uatp.php");
// Require specific Business logic for the UATP Card Account services
require_once(sCLASS_PATH . "/uatp_card_account.php");
// Require specific Business logic for the chase component
require_once(sCLASS_PATH ."/chase.php");
// Require specific Business logic for the PayU component
require_once(sCLASS_PATH ."/payu.php");
// Require specific Business logic for the Cielo component
require_once(sCLASS_PATH ."/cielo.php");
// Require specific Business logic for the cellulant component
require_once(sCLASS_PATH ."/cellulant.php");
require_once(sCLASS_PATH ."/wallet_processor.php");
// Require specific Business logic for the Global payments component
require_once(sCLASS_PATH ."/global-payments.php");
// Require specific Business logic for the cybs component
require_once(sCLASS_PATH ."/cybersource.php");
// Require specific Business logic for the VeriTrans4G component
require_once(sCLASS_PATH ."/psp/veritrans4g.php");
// Require specific Business logic for the DragonPay component
require_once(sCLASS_PATH ."/aggregator/dragonpay.php");
// Require specific Business logic for the SWISH component
require_once(sCLASS_PATH ."/apm/swish.php");
// Require specific Business logic for the FirstData component
require_once(sCLASS_PATH ."/first-data.php");
require_once sCLASS_PATH . '/txn_passbook.php';
require_once sCLASS_PATH . '/passbookentry.php';
require_once(sCLASS_PATH ."/fraud/provider/ezy.php");
require_once(sCLASS_PATH ."/fraud/provider/cyberSourceFsp.php");
require_once(sCLASS_PATH ."/fraud/provider/cebuRmfss.php");
require_once(sCLASS_PATH ."/core/card.php");
require_once(sCLASS_PATH ."/validation/cardvalidator.php");
require_once sCLASS_PATH . '/routing_service.php';
require_once sCLASS_PATH . '/routing_service_response.php';
require_once sCLASS_PATH . '/fraud/fraud_response.php';
require_once sCLASS_PATH . '/fraud/fraudResult.php';
require_once(sCLASS_PATH . '/payment_route.php');
require_once(sCLASS_PATH .'/apm/paymaya.php');
require_once(sCLASS_PATH . '/paymentSecureInfo.php');
// Require specific Business logic for the MPGS
require_once(sCLASS_PATH ."/MPGS.php");
require_once(sCLASS_PATH . '/Route.php');
require_once(sCLASS_PATH ."/voucher/TravelFund.php");
// Require specific Business logic for the Paymaya-Acq component
require_once(sCLASS_PATH ."/Paymaya_Acq.php");
// Require specific Business logic for the Nmi-Credomatic component
require_once(sCLASS_PATH ."/nmi_credomatic.php");
$aMsgCds = array();
/*
$_SERVER['PHP_AUTH_USER'] = "MalindoDemo";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA = '<root>';
$HTTP_RAW_POST_DATA = '<callback>';
$HTTP_RAW_POST_DATA = '<psp-config id="25" >';
$HTTP_RAW_POST_DATA = '<name>wirecard</name>';
$HTTP_RAW_POST_DATA = '<transaction id="1986311" order-no="HYHJXT">';
$HTTP_RAW_POST_DATA = '<amount country-id="603" currency="INR" />';
$HTTP_RAW_POST_DATA = '<card type-id="8" />';
$HTTP_RAW_POST_DATA = '</transaction>';
$HTTP_RAW_POST_DATA = '<status code="20109" />';
$HTTP_RAW_POST_DATA = '</callback>';
$HTTP_RAW_POST_DATA = '</root>';
*/
$obj_DOM = simpledom_load_string(file_get_contents('php://input'));
if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mpoint.xsd") === true && count($obj_DOM->{'callback'}) > 0)
	{
		$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);
		$xml = '';

		for ($i=0; $i<count($obj_DOM->{'callback'}); $i++)
		{
            $obj_Elem = $obj_DOM->{'callback'}[$i];
            if (intval($obj_Elem->{'psp-config'}['id']) > 0) {
                $obj_TxnInfo = TxnInfo::produceInfo($obj_Elem->transaction["id"], $_OBJ_DB);
                $obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), intval($obj_Elem->{'psp-config'}['id']));
                $obj_PSP = Callback::producePSP($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO, $obj_PSPConfig);
                $code = $obj_PSP->postStatus($obj_Elem);
            } else if (intval($obj_Elem->{'session'}['id']) > 0) {

                $query = "SELECT  id,pspid FROM log" . sSCHEMA_POSTFIX . ".transaction_tbl WHERE sessionid = " . $obj_Elem->{'session'}['id'] ." and pspid>0 Limit 1" ;
                $RSTxn = $_OBJ_DB->getName ( $query );
                if(is_array($RSTxn) === true)
                {
                    $obj_TxnInfo = TxnInfo::produceInfo( $RSTxn["ID"], $_OBJ_DB);
                    $obj_Processor = PaymentProcessor::produceConfig($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, intval($RSTxn['PSPID']), $aHTTP_CONN_INFO);
                    $obj_Processor->getPSPInfo()->updateSessionState(-1, $obj_TxnInfo->getExternalID(), $obj_TxnInfo->getAmount(), $obj_TxnInfo->getCardMask(), $obj_TxnInfo->getCardID(), $obj_TxnInfo->getCardExpiry(), "", $obj_TxnInfo->getClientConfig()->getSurePayConfig($_OBJ_DB), 0, $obj_Elem->{'status'}['code']);

                }
            }
		}
	}
	// Error: Invalid XML Document
	elseif ( ($obj_DOM instanceof SimpleDOMElement) === false)
	{
		header("HTTP/1.1 415 Unsupported Media Type");
		
		$xml = '<status code="415">Invalid XML Document</status>';
	}
	// Error: Wrong operation
	elseif (count($obj_DOM->{'callback'}) == 0)
	{
		header("HTTP/1.1 400 Bad Request");
	
		$xml = '';
		foreach ($obj_DOM->children() as $obj_Elem)
		{
			$xml .= '<status code="400">Wrong operation: '. $obj_Elem->getName() .'</status>'; 
		}
	}
	// Error: Invalid Input
	else
	{
		header("HTTP/1.1 400 Bad Request");
		$aObj_Errs = libxml_get_errors();
		
		$xml = '';
		for ($i=0; $i<count($aObj_Errs); $i++)
		{
			$xml .= '<status code="400">'. htmlspecialchars($aObj_Errs[$i]->message, ENT_NOQUOTES) .'</status>';
		}
	}
}
else
{
	header("HTTP/1.1 401 Unauthorized");
	
	$xml = '<status code="401">Authorization required</status>';
}
header("Content-Type: text/xml; charset=\"UTF-8\"");

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<root>';
echo $xml;
echo '</root>';
?>