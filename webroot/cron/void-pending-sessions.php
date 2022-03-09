<?php

if (PHP_SAPI == "cli") {
    $_SERVER['HTTP_HOST'] = getenv('MPOINT_HOST');
    $_SERVER['DOCUMENT_ROOT'] = '/opt/cpm/mPoint/webroot';
}
include $_SERVER['DOCUMENT_ROOT'].'/cron/cron-include.php';
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/include.php');
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");

// Require data class for Payment Service Provider Configurations
require_once(sCLASS_PATH ."/pspconfig.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require Business logic for the Mobile Web module
require_once(sCLASS_PATH ."/mobile_web.php");
// Require Business logic for the Select Credit Card component
require_once(sCLASS_PATH ."/credit_card.php");

// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for the CPM PSP component
require_once(sINTERFACE_PATH ."/cpm_psp.php");
// Require specific Business logic for the CPM ACQUIRER component
require_once(sINTERFACE_PATH ."/cpm_acquirer.php");
// Require specific Business logic for the CPM GATEWAY component
require_once(sINTERFACE_PATH ."/cpm_gateway.php");
// Require specific Business logic for the DIBS component
require_once(sCLASS_PATH ."/dibs.php");
// Require specific Business logic for the PayEx component
require_once(sCLASS_PATH ."/payex.php");
// Require specific Business logic for the NetAxept component
require_once(sCLASS_PATH ."/netaxept.php");
// Require specific Business logic for the MobilePay component
require_once(sCLASS_PATH ."/mobilepay.php");
// Require specific Business logic for the WireCard component
require_once(sCLASS_PATH ."/wirecard.php");
// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
// Require specific Business logic for the Nets component
require_once(sCLASS_PATH ."/nets.php");
// Require specific Business logic for the mVault component
require_once(sCLASS_PATH ."/mvault.php");
// Require specific Business logic for the Amex component
require_once(sCLASS_PATH ."/amex.php");
// Require specific Business logic for the CHUBB component
require_once(sCLASS_PATH ."/chubb.php");
// Require Data Class for Client Information
require_once(sCLASS_PATH ."/clientinfo.php");
// Require specific Business logic for the UATP component
require_once(sCLASS_PATH . "/uatp.php");
// Require specific Business logic for the Chase component
require_once(sCLASS_PATH ."/chase.php");
require_once(sCLASS_PATH ."/payment_processor.php");
require_once(sCLASS_PATH ."/wallet_processor.php");
require_once(sCLASS_PATH ."/voucher/TravelFund.php");

$sql = "SELECT sn.id, sn.amount FROM log" . sSCHEMA_POSTFIX . ".session_tbl sn
          WHERE sn.stateid not in (".Constants::iSESSION_COMPLETED.", ".Constants::iSESSION_EXPIRED.",".Constants::iSESSION_FAILED.") AND sn.created >= (now() - interval '10 hour') AND sn.expire < now()";

$res = $_OBJ_DB->query($sql);


while ($RS = $_OBJ_DB->fetchName($res))
{
    $query = "SELECT  t.id,pspid FROM log" . sSCHEMA_POSTFIX . ".transaction_tbl t 
        JOIN system" . sSCHEMA_POSTFIX . ". card_tbl c ON t.cardid = c.id 
        WHERE sessionid = " . $RS['ID'] ." and pspid > 0 and c.paymenttype != 8 Limit 1" ;
    $RSTxn = $_OBJ_DB->getName ( $query );
    if(is_array($RSTxn) === true)
    {
        $obj_TxnInfo = TxnInfo::produceInfo( $RSTxn["ID"], $_OBJ_DB);
        $obj_Processor = PaymentProcessor::produceConfig($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, intval($RSTxn['PSPID']), $aHTTP_CONN_INFO);
        $obj_Processor->getPSPInfo()->updateSessionState(-1, $obj_TxnInfo->getExternalID(), $obj_TxnInfo->getAmount(), $obj_TxnInfo->getCardMask(), $obj_TxnInfo->getCardID(), $obj_TxnInfo->getCardExpiry(), "", $obj_TxnInfo->getClientConfig()->getSurePayConfig($_OBJ_DB));
    }
}

/*// Call Void API for Cancel/Refund the transaction
foreach ($results as $result)
{
    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= '<void-payments-request>';
    $xml .= '<transactions client-id="'.$result['CLIENTID'].'">';
    $xml .= '<transaction id="'.$result['ID'].'" order-no="'.$result['ORDERID'].'">';
    $xml .= '<amount country-id="'.$result['COUNTRYID'].'">'.$result['AMOUNT'].'</amount>';
    $xml .= '</transaction>';
    $xml .= '</transactions>';
    $xml .= '</void-payments-request>';
    $obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, $result['CLIENTID'], $result['ACCOUNTID']);
    void($xml, $obj_ClientConfig);
}

//Performs a VOID (Refund or cancel) operation for the provided transaction.
function void($xml, $obj_ClientConfig) {
    try {
        $aURLInfo = parse_url($obj_ClientConfig->getMESBURL() );
        $obj_ConnInfo = new HTTPConnInfo('http', $aURLInfo["host"], 10080, 20, '/mpoint/mconsole/void', 'POST', 'application/xml', $obj_ClientConfig->getUsername(), $obj_ClientConfig->getPassword());
        $obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
        $obj_HTTP->connect();
        $code = $obj_HTTP->send(constHTTPHeaders($obj_ClientConfig), $xml);
        $obj_HTTP->disConnect();
        if ($code == 200) {
            trigger_error('Rollback Success:' . $code);
        } else {
            trigger_error('Rollback Failed:' . $code);
        }
    } catch (Exception $e) {
        trigger_error("Void of txn: failed with code: " . $e->getCode() . " and message: " . $e->getMessage(), E_USER_ERROR);
    }
}*/

function constHTTPHeaders($obj_ClientConfig)
{
    /* ----- Construct HTTP Header Start ----- */
    $h = "{METHOD} {PATH} HTTP/1.0" .HTTPClient::CRLF;
    $h .= "host: {HOST}" .HTTPClient::CRLF;
    $h .= "referer: {REFERER}" .HTTPClient::CRLF;
    $h .= "content-length: {CONTENTLENGTH}" .HTTPClient::CRLF;
    $h .= "content-type: {CONTENTTYPE}; charset=UTF-8" .HTTPClient::CRLF;
    $h .= "Authorization: Basic ". base64_encode($obj_ClientConfig->getUsername() .":". $obj_ClientConfig->getPassword()) .HTTPClient::CRLF;
    $h .= "user-agent: mPoint" .HTTPClient::CRLF;
    /* ----- Construct HTTP Header End ----- */
    return $h;
}