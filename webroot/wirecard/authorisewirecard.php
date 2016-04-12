<?php
// Require Global Include File
require_once("../inc/include.php");

require_once(sAPI_CLASS_PATH ."simpledom.php");


// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");

// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for the CPM PSP component
require_once(sINTERFACE_PATH ."/cpm_psp.php");
// Require specific Business logic for the wirecard component
require_once(sCLASS_PATH ."/wirecard.php");

// Require Business logic for the Select Credit Card component
require_once(sCLASS_PATH ."/credit_card.php");

$obj_mPoint = new WireCard($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo'], $aHTTP_CONN_INFO["wire-card"]);

if (strlen($_SESSION['obj_TxnInfo']->getOrderID() ) > 0 && $obj_mPoint->orderAlreadyAuthorized($_SESSION['obj_TxnInfo']->getOrderID() ) == false) 
{
	$b = 'merchant_account_id='.$_POST['merchant_account_id'];
	$b .= '&requested_amount='.$_POST['requested_amount'];
	$b .= '&request_id='.$_POST['request_id'];
	$b .= '&transaction_type='.$_POST['transaction_type'];
	$b .= '&requested_amount_currency='.$_POST['requested_amount_currency'];
	$b .= '&account_number='.$_POST['card-number'];
	$b .= '&expiration_month='.$_POST['emonth'];
	$b .= '&expiration_year='.$_POST['eyear'];
	$b .= '&card_security_code='.$_POST['cvc'];
	$b .= '&last_name='.$_POST['last_name'];
	$b .= '&field_name_1=exp_month';
	$b .= '&phone='.$_POST['phone'];
	$b .= '&field_name_2=exp_year';
	$b .= '&field_name_3=card_type_id';
	$b .= '&field_value_3='.$_POST['field_value_3'];//7
	$b .= '&field_value_1='.$_POST['emonth'];//01
	$b .= '&field_value_2='.substr($_POST['eyear'], -2);//19
	$b .= '&payment_ip_address='.$_POST['payment_ip_address'];
	$b .= '&email='.$_POST['email'];
	$b .= '&first_name='.$_POST['first_name'];
	$b .= '&notification_url_1='.$_POST['notification_url_1'];
	$b .= '&card_type='.$_POST['card_type'];
	
	$aHTTP_CONN_INFO["wire-card"]["protocol"] = "https";
	$aHTTP_CONN_INFO["wire-card"]["port"] = "443";
	$aHTTP_CONN_INFO["wire-card"]["host"] = "api-test.wirecard.com";
	$aHTTP_CONN_INFO["wire-card"]["path"] = "/engine/rest/payments/";
	$aHTTP_CONN_INFO["wire-card"]["method"] = "POST";
	$aHTTP_CONN_INFO["wire-card"]["contenttype"] = "application/x-www-form-urlencoded";
	$aHTTP_CONN_INFO["wire-card"]["username"] = $_SESSION['obj_XML_initialize']['user_name'];
	$aHTTP_CONN_INFO["wire-card"]["password"] = $_SESSION['obj_XML_initialize']['password'];
	
	unset($_SESSION['obj_XML_initialize']);
	
	$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["wire-card"]);
	
	$h = "{METHOD} {PATH} HTTP/1.0" .HTTPClient::CRLF;
	$h .= "host: {HOST}" .HTTPClient::CRLF;
	$h .= "referer: {REFERER}" .HTTPClient::CRLF;
	$h .= "content-length: {CONTENTLENGTH}" .HTTPClient::CRLF;
	$h .= "content-type: {CONTENTTYPE}" .HTTPClient::CRLF;
	$h .= "user-agent: mPoint" .HTTPClient::CRLF;
	$h .= "Authorization: Basic ". base64_encode($aHTTP_CONN_INFO["wire-card"]["username"] .":". $aHTTP_CONN_INFO["wire-card"]["password"]) .HTTPClient::CRLF;
	
	try
	{
		$obj_Client = new HTTPClient(new Template(), $obj_ConnInfo);
		$obj_Client->connect();
		$code = $obj_Client->send($h, $b);
		$obj_Client->disconnect();
		if ($code == 201 && strlen($obj_Client->getReplyBody() ) > 0)
		{
			$obj_XML = simplexml_load_string($obj_Client->getReplyBody() );
		}
		else
		{
			$url .= "http://". $_SERVER['SERVER_NAME'] ."/pay/card.php?mpoint-id=". $_SESSION['obj_TxnInfo']->getID() ."&". session_name() ."=". session_id() ."&msg=99";
			header("location: ". $url);
			exit;
		}
	}
	catch (Exception $e)
	{
		header("Content-Type: text/plain");
		var_dump($e);
		var_dump($obj_Client);
		die();
	}
}

// Order already completed (likely because the customer paid with a Stored Card)
if (strlen($_SESSION['obj_TxnInfo']->getOrderID() ) > 0 && $obj_mPoint->orderAlreadyAuthorized($_SESSION['obj_TxnInfo']->getOrderID() ) === true)
{
	$url = "/pay/accept.php?". session_name() ."=". session_id() ."&mpoint-id=". $_SESSION['obj_TxnInfo']->getID();
}

if (array_key_exists("store-card", $_POST) === true && General::xml2bool($_POST['store-card']) === true)
{
	$obj_mPoint->newMessage($_SESSION['obj_TxnInfo']->getID(), Constants::iTICKET_CREATED_STATE, "");
	if (strlen($obj_mPoint->getTxnInfo()->getAuthenticationURL() ) > 0 || $obj_mPoint->getTxnInfo()->getAccountID() > 0)
	{
		$url = "http://". $_SERVER['SERVER_NAME'] ."/pay/name.php?mpoint-id=". $_SESSION['obj_TxnInfo']->getID() ."&". session_name() ."=". session_id() ."&cardid=". $_POST['cardid'];
	}
	else { $url = "http://". $_SERVER['SERVER_NAME'] ."/pay/pwd.php?mpoint-id=". $_SESSION['obj_TxnInfo']->getID() ."&". session_name() ."=". session_id() ."&cardid=". $_POST['cardid']; }
}
else { $url = "http://". $_SERVER['SERVER_NAME'] ."/pay/accept.php?mpoint-id=". $_SESSION['obj_TxnInfo']->getID() ."&". session_name() ."=". session_id(); }

header("location: ". $url);
	