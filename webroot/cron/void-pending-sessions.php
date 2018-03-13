<?php
require_once("../inc/include.php");

$sql = "SELECT sn.id, sn.amount
          FROM log" . sSCHEMA_POSTFIX . ".session_tbl sn
          WHERE sn.stateid not in (4030) AND sn.created >= (now() - interval '10 hour') AND sn.expire > now()";

$res = $_OBJ_DB->query($sql);
$results = array();
while ($RS = $_OBJ_DB->fetchName($res)) {
    $query = "SELECT  DISTINCT txn.id, txn.amount, txn.clientid, txn.accountid, txn.orderid, txn.countryid  
              FROM log" . sSCHEMA_POSTFIX . ".transaction_tbl txn 
                INNER JOIN log" . sSCHEMA_POSTFIX . ".message_tbl msg ON txn.id = msg.txnid 
              WHERE sessionid = " . $RS['ID'] . " 
                AND msg.stateid in (2000,2001,2007) GROUP BY txn.id, msg.stateid";

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
    $xml .= '<root>';
    $xml .= '<void client-id="'.$result['CLIENTID'].'" account="'.$result['ACCOUNTID'].'">';
    $xml .= '<transaction id="'.$result['ID'].'" order-no="'.$result['ORDERID'].'">';
    $xml .= '<amount country-id="'.$result['COUNTRYID'].'">'.$result['AMOUNT'].'</amount>';
    $xml .= '</transaction>';
    $xml .= '</void>';
    $xml .= '</root>';
    
    $obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, $result['CLIENTID'], $result['ACCOUNTID']);
    void($xml, $obj_ClientConfig);
}
//Performs a VOID (Refund or cancel) operation for the provided transaction.
function void($xml, $obj_ClientConfig) {
    try {
        $obj_ConnInfo = new HTTPConnInfo('http', $_SERVER['HTTP_HOST'], 80, 20, '/mApp/api/void.php', 'POST', 'text/xml', $obj_ClientConfig->getUsername(), $obj_ClientConfig->getPassword());

        $obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
        $obj_HTTP->connect();
        $code = $obj_HTTP->send(constHTTPHeaders($obj_ClientConfig), $xml);
        $obj_HTTP->disConnect();
        trigger_error('Success:'. $code);
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