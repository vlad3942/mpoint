<?php
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
define("sAPI_CLASS_PATH", substr(sSYSTEM_PATH, 0, strrpos(sSYSTEM_PATH, "/")) . "/php5api/classes/");
// Define path to the General API interfaces
define("sAPI_INTERFACE_PATH", substr(sSYSTEM_PATH, 0, strrpos(sSYSTEM_PATH, "/")) . "/php5api/interfaces/");
// Define path to the System Configuration
define("sCONF_PATH", sSYSTEM_PATH . "/conf/");
// Define path to the System classes
define("sCLASS_PATH", sSYSTEM_PATH . "/api/classes/");

require_once(sAPI_INTERFACE_PATH . "database.php");
require_once(sCLASS_PATH . "general.php");
require_once(sAPI_CLASS_PATH . "report.php");
require_once(sAPI_CLASS_PATH ."/template.php");
require_once(sAPI_CLASS_PATH ."/http_client.php");
require_once(sAPI_CLASS_PATH . "database.php");
require_once(sCONF_PATH . "global.php");

require_once(sCLASS_PATH ."/basicconfig.php");
require_once(sCLASS_PATH ."/countryconfig.php");
require_once(sCLASS_PATH ."/currencyconfig.php");
require_once(sCLASS_PATH ."/client_config.php");
require_once(sCLASS_PATH ."/account_config.php");
require_once(sCLASS_PATH ."/client_merchant_subaccount_config.php");
require_once(sCLASS_PATH ."/client_merchant_account_config.php");
require_once(sCLASS_PATH ."/client_payment_method_config.php");
require_once(sCLASS_PATH ."/client_url_config.php");
require_once(sCLASS_PATH ."/client_issuer_identifcation_number_range_config.php");
require_once(sCLASS_PATH ."/client_gomobile_config.php");
require_once(sCLASS_PATH ."/client_communication_channel_config.php");
require_once(sCLASS_PATH ."/keywordconfig.php");


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
    
    $obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, $result['CLIENTID'], $result['ACCOUNTID']);
    void($xml, $obj_ClientConfig);
}
//Performs a VOID (Refund or cancel) operation for the provided transaction.
function void($xml, $obj_ClientConfig) {
    try {
        $obj_ConnInfo = new HTTPConnInfo('http', 'mpoint.dev2.cellpointmobile.com', 80, 20, '/mApp/api/void.php', 'POST', 'text/xml', $obj_ClientConfig->getUsername(), $obj_ClientConfig->getPassword());

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