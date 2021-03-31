<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package:
 * File Name: callback
 */

require_once('../inc/include.php');

$_Request = $_REQUEST;

$isSessionCallback = array_key_exists('transaction-data', $_Request);

$cardNames = [];
if ($isSessionCallback === TRUE) {
    $txnIds = array_keys($_Request['transaction-data']);
    $cardIds = [];
    foreach ($txnIds as $txnId) {
        array_push($cardIds, (int)$_Request['transaction-data'][$txnId]['card-id']);
    }
    $cardNames = getCardNames($cardIds);

    foreach ($txnIds as $txnId) {
        $cardId = (int)$_Request['transaction-data'][$txnId]['card-id'];
        if (array_key_exists($cardId, $cardNames)) {
            $_Request['transaction-data'][$txnId]['card_name'] = $cardNames[$cardId];
        }
    }

    $cardId = $_Request['card-id'];
    if (array_key_exists($cardId, $cardNames)) {
        $_Request['card_name'] = $cardNames[$cardId];
    }
} else {
    $cardId = (int)$_Request['card-id'];
    $cardNames = getCardNames([$cardId]);
    if (array_key_exists($cardId, $cardNames)) {
        $_Request['card_name'] = $cardNames[$cardId];
    }
}

$url = $_Request["proxy_callback"];

unset($_Request["proxy_callback"]);


$genericCallabckRequest = http_build_query($_Request);
$genericCallabckRequest = urldecode($genericCallabckRequest);
$search =['&mpoint-id','&payment-method','&payment-type','&orderid','&status','&amount','&currency','&customer-country-id','&card-number','&expiry','&approval-code','&session-id','&pspid','&psp-name','&decimals','&exchange_rate','&billing_first_name','&billing_last_name','&billing_street_address','&billing_city','&billing_country','&billing_state','&billing_postal_code','&billing_email','&billing_mobile','&billing_idc','&sale_currency','&sale_amount','&sale_decimals',];
$replace = ['&transaction_id','&payment_method','&payment-type','&order_id','&state_id','&dcc_card_amount','&dcc_card_currency','&customer-country-id','&masked_card','&expiration_date','&approval_code','&session_id','&psp_ref_id','&psp_name','&dcc_card_decimals','&dcc_exchange_rate','&first_name','&last_name','&street_address','&city','&country','&province','&postal_code','&email','&mobile','&dialing_country_code','&currency','&amount','&decimals'];
$cebusCallabckRequest =  str_replace($search, $replace, $genericCallabckRequest);

sendCallback($url, $cebusCallabckRequest);

function getCardNames(array $cardIds): array
{
    global $_OBJ_DB;
    $sql = 'SELECT ID,NAME FROM SYSTEM.CARD_TBL WHERE ID IN (' . implode(',', $cardIds) . ')';
    $resultSet = $_OBJ_DB->getAllNames($sql);
    $cardNames = [];

    if (is_array($resultSet) === TRUE && count($resultSet) > 0) {
        foreach ($resultSet as $rs) {
            $cardNames[(int)$rs['ID']] = $rs['NAME'];
        }
    }
    return $cardNames;
}

function sendCallback(string $url, string $body)
{
    $aURLInfo = parse_url($url);

if (array_key_exists("port", $aURLInfo) === false)
    {
        if (array_key_exists("scheme", $aURLInfo) === true)
        {
            if ( $aURLInfo["scheme"] == "https") { $aURLInfo["port"] = 443; }
            else { $aURLInfo["port"] = 80; }
        }
        else { $aURLInfo["port"] = 80; }
    }
    if (array_key_exists("query", $aURLInfo) === true) { $aURLInfo["path"] .= "?". $aURLInfo["query"]; }

    $obj_ConnInfo = new HTTPConnInfo($aURLInfo["scheme"], $aURLInfo["host"], $aURLInfo["port"], 20, $aURLInfo["path"], "POST", "application/x-www-form-urlencoded");
    /* ========== Instantiate Connection Info End ========== */
    $obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);

    /* ========== Perform Callback Start ========== */

    try
    {
        $obj_HTTP->connect();
        // Send Callback data
        $iCode = $obj_HTTP->send(constHTTPHeaders(), $body);
        $obj_HTTP->disConnect();

        http_response_code($iCode);
        echo $obj_HTTP->getReplyBody();
    }
    // Error: Unable to establish Connection to Client
    catch (HTTPConnectionException | HTTPSendException $e)
    {
        trigger_error("mPoint Callback request failed Body: ". $body. ", Message: " . $e->getMessage(), E_USER_ERROR);
        http_response_code(500);
        echo ("Callback request failed please contact customer support");
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
		$h .= "user-agent: mPoint-{USER-AGENT}" .HTTPClient::CRLF;
		$h .= "X-CPM-Merchant-Domain: {X-CPM-MERCHANT-DOMAIN}" .HTTPClient::CRLF;
		/* ----- Construct HTTP Header End ----- */

		return $h;
	}
