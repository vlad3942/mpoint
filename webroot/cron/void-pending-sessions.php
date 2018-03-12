<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);
if (isset($_SERVER['DOCUMENT_ROOT']) === true && empty($_SERVER['DOCUMENT_ROOT']) === false) {
    $_SERVER['DOCUMENT_ROOT'] = str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']);
    // Define system path constant
    define("sSYSTEM_PATH", substr($_SERVER['DOCUMENT_ROOT'], 0, strrpos($_SERVER['DOCUMENT_ROOT'], "/")));
}
// Command line
else {
    $aTemp = explode("/", str_replace("\\", "/", __FILE__));
    $sPath = "";
    for ($i = 0; $i < count($aTemp) - 3; $i++) {
        $sPath .= $aTemp[$i] . "/";
    }
    // Define system path constant
    define("sSYSTEM_PATH", substr($sPath, 0, strlen($sPath) - 1));
}
// Define path to the General API classes
define("sAPI_CLASS_PATH", substr(sSYSTEM_PATH, 0, strrpos(sSYSTEM_PATH, "/")) . "/../php5api/classes/");
// Define path to the General API interfaces
define("sAPI_INTERFACE_PATH", substr(sSYSTEM_PATH, 0, strrpos(sSYSTEM_PATH, "/")) . "/../php5api/interfaces/");
// Define path to the System Configuration
define("sCONF_PATH", sSYSTEM_PATH . "/../conf/");
// Define path to the System classes
define("sCLASS_PATH", sSYSTEM_PATH . "/../api/classes/");

require_once(sAPI_INTERFACE_PATH . "database.php");
require_once(sCLASS_PATH . "general.php");
require_once(sAPI_CLASS_PATH . "report.php");
require_once(sAPI_CLASS_PATH ."/template.php");
require_once(sAPI_CLASS_PATH ."/http_client.php");
require_once(sAPI_CLASS_PATH . "database.php");
require_once(sCONF_PATH . "global.php");

$_OBJ_DB = RDB::produceDatabase($aDB_CONN_INFO["mpoint"]);

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
    void($xml);
}

//Performs a VOID (Refund or cancel) operation for the provided transaction.
function void($xml) {
    try {
        $obj_ConnInfo = new HTTPConnInfo('http', $_SERVER['HTTP_HOST'], 80, 20, '/mApp/api/void.php', 'POST', 'text/xml', 'MalindoDemo', 'DEMOisNO_2');

        $obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
        $obj_HTTP->connect();
        $code = $obj_HTTP->send(constHTTPHeaders(), $xml);
        $obj_HTTP->disConnect();
    } catch (Exception $e) {
        trigger_error("Void of txn: " . $this->getTxnInfo()->getID() . " failed with code: " . $e->getCode() . " and message: " . $e->getMessage(), E_USER_ERROR);
    }
}

function constHTTPHeaders()
{
        /* ----- Construct HTTP Header Start ----- */
        $h = "{METHOD} {PATH} HTTP/1.0" .HTTPClient::CRLF;
        $h .= "host: {HOST}" .HTTPClient::CRLF;
        $h .= "referer: {REFERER}" .HTTPClient::CRLF;
        $h .= "content-length: {CONTENTLENGTH}" .HTTPClient::CRLF;
        $h .= "content-type: {CONTENTTYPE}; charset=UTF-8" .HTTPClient::CRLF;
        $h .= "user-agent: mPoint" .HTTPClient::CRLF;
        /* ----- Construct HTTP Header End ----- */
        return $h;
}