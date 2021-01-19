<?php

if (PHP_SAPI == "cli") {
    $_SERVER['HTTP_HOST'] = getenv('MPOINT_HOST');
    $_SERVER['DOCUMENT_ROOT'] = '/opt/cpm/mPoint/webroot';
}
include $_SERVER['DOCUMENT_ROOT'].'/cron/cron-include.php';
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/include.php');

$sql = "SELECT sn.id, sn.amount
          FROM log" . sSCHEMA_POSTFIX . ".session_tbl sn
          WHERE sn.stateid = ".Constants::iSESSION_PARTIALLY_COMPLETED." AND sn.created >= (now() - interval '10 hour') AND sn.expire < now()";

$res = $_OBJ_DB->query($sql);

$results = array();

while ($RS = $_OBJ_DB->fetchName($res)) {
    $query = "SELECT  DISTINCT txn.id, txn.amount, txn.clientid, txn.accountid, txn.orderid, txn.countryid  
              FROM log" . sSCHEMA_POSTFIX . ".transaction_tbl txn 
                INNER JOIN log" . sSCHEMA_POSTFIX . ".message_tbl msg ON txn.id = msg.txnid 
              WHERE sessionid = " . $RS['ID'] . " 
                AND msg.stateid in (".Constants::iPAYMENT_ACCEPTED_STATE.",".Constants::iPAYMENT_CAPTURED_STATE.",".Constants::iPAYMENT_WITH_VOUCHER_STATE.")
                GROUP BY txn.id, msg.stateid";

    $resultObj = $_OBJ_DB->query($query);
    $amount = 0;
    $transactionIds = array();
    while ($RESULTSET = $_OBJ_DB->fetchName($resultObj)) {
        $amount = ($amount + intval($RESULTSET['AMOUNT']));
        $transactionIds[] = $RESULTSET;
    }
    $pendingAmount = $RS['AMOUNT'] - $amount;
    if ($pendingAmount != 0) {
        $results = array_merge($results, $transactionIds);
    }
}

// Call Void API for Cancel/Refund the transaction
foreach ($results as $result) {
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
}

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