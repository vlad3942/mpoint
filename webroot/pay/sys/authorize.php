<?php
// Require Global Include File
require_once("../../inc/include.php");

require_once(sAPI_CLASS_PATH ."simpledom.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");

// Require Business logic for the Select Credit Card component
require_once(sCLASS_PATH ."/credit_card.php");

$aMsgCds = array();
$msg = "";
$sCardHolderName = "";

$obj_mPoint = new CreditCard($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo'], $_SESSION['obj_UA']);

$obj_Validator = new Validate($_SESSION['obj_TxnInfo']->getCountryConfig() );

if(isset($_POST['token']) == false)
{
	$givenDate = $_POST['expiry-month'].'/'.$_POST['expiry-year'];
	
	if(preg_match('/^\\d{2}\\/\\d{2}$/', $givenDate) == 0) 
	{  
		$aMsgCds[] = 20;
	}
	else 
	{
		$givenDateTimeStamp = strtotime(gmdate("Y-m-d H:i:sP", mktime(0, 0, 0, $_POST['expiry-month'], 01, $_POST['expiry-year'])));
		
		if ($givenDateTimeStamp < strtotime('today') ) { $aMsgCds[] = 20; }
	}
	
	if( (count($_POST['cardnumber']) == 0 || (count($_POST['cardnumber']) > 0 && $obj_Validator->valCardNumber($_POST['cardnumber']) != 10))
		) { $aMsgCds[] = 25; }
	
	if(empty($_POST['cardholdername']) == false)
	{
		$sCardHolderName = $_POST['cardholdername'];
	} else { $sCardHolderName = "John Doe"; }
}

if (count($aMsgCds) == 0)
{
	$msg = 99;
	
	try 
	{
	
		$aHTTP_CONN_INFO["mesb"]["path"] = "/mpoint/authorize-payment";
		$aHTTP_CONN_INFO["mesb"]["username"] = $_SESSION['obj_TxnInfo']->getClientConfig()->getUsername();
		$aHTTP_CONN_INFO["mesb"]["password"] = $_SESSION['obj_TxnInfo']->getClientConfig()->getPassword();
				
		$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["mesb"]);
			
		$h = "{METHOD} {PATH} HTTP/1.0" .HTTPClient::CRLF;
		$h .= "host: {HOST}" .HTTPClient::CRLF;
		$h .= "referer: {REFERER}" .HTTPClient::CRLF;
		$h .= "content-length: {CONTENTLENGTH}" .HTTPClient::CRLF;
		$h .= "content-type: {CONTENTTYPE}" .HTTPClient::CRLF;
		$h .= "user-agent: mPoint" .HTTPClient::CRLF;
		$h .= "Authorization: Basic ". base64_encode($aHTTP_CONN_INFO["mesb"]["username"] .":". $aHTTP_CONN_INFO["mesb"]["password"]) .HTTPClient::CRLF;
		
		
		$clientId = $_SESSION['obj_TxnInfo']->getClientConfig()->getAccountConfig()->getClientID();
		$accountId = $_SESSION['obj_TxnInfo']->getClientConfig()->getAccountConfig()->getID();
		
		if(isset($_POST['token']) == false)
		{
			$transactionType = Constants::iNEW_CARD_PURCHASE_TYPE;
			$cardDetails = ' <card-holder-name>'.$sCardHolderName.'</card-holder-name>
			         <card-number>'.$_POST['cardnumber'].'</card-number>
			         <expiry>'.$givenDate.'</expiry>
			         <cvc>'.$_POST['cvv'].'</cvc>';
		}
		else 
		{
			$transactionType = Constants::iCARD_PURCHASE_TYPE;
			$cardDetails = '<token>'.$_POST['token'].'</token>';
			
			if(intval($_POST['cardtype']) == 23)
			{
				$cardDetails .= '<verifier>'.$_POST['verifier'].'</verifier>';
				$cardDetails .= '<checkout-url>'.$_POST['checkouturl'].'</checkout-url>';
			}
		}
		
		
		$b = '<?xml version="1.0" encoding="UTF-8"?>
			<root>
			  <authorize-payment account="'.$accountId.'" client-id="'.$clientId.'">
			    <transaction type-id="'.$transactionType.'" id="'.$_SESSION['obj_TxnInfo']->getID().'">
			      <card type-id="'.intval($_POST['cardtype']).'">
			        <amount country-id="'.$_SESSION['obj_TxnInfo']->getCountryConfig()->getID().'">'.$_SESSION['obj_TxnInfo']->getAmount().'</amount>
			         '.$cardDetails.'
			      </card>
			    </transaction>
			    <client-info language="'.sDEFAULT_LANGUAGE.'" version="1.20" platform="HTML5">
			    </client-info>
			  </authorize-payment>
			</root>';
		
			
		$obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
		$obj_HTTP->connect();
		$code = $obj_HTTP->send($h, $b);
		$obj_HTTP->disconnect();
				
	}
	catch(Exception $e)
	{
		$msg = 59;
	}
	
	if ($code == 200)
	{
		$obj_XML = simplexml_load_string($obj_HTTP->getReplyBody() );
		
		$code = $obj_XML->status["code"];
		if($code == 100)
		{
			$url = "http://". $_SERVER['SERVER_NAME'] ."/pay/accept.php?mpoint-id=". $_SESSION['obj_TxnInfo']->getID() ."&". session_name() ."=". session_id();
		} 
		else 
		{
			$obj_mPoint->delMessage($_SESSION['obj_TxnInfo']->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);
			
			$url = "http://". $_SERVER['SERVER_NAME'] ."/pay/card.php?mpoint-id=". $_SESSION['obj_TxnInfo']->getID() ."&". session_name() ."=". session_id() ."&msg=".$code;
		}
	}
	else
	{
		$obj_mPoint->delMessage($_SESSION['obj_TxnInfo']->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);
		
		$url = "http://". $_SERVER['SERVER_NAME'] ."/pay/card.php?mpoint-id=". $_SESSION['obj_TxnInfo']->getID() ."&". session_name() ."=". session_id() ."&msg=".$msg;
	}
	
	header("location: ". $url);
	exit;
} 
else
{
		
	if (isset($sPath) === false) { $sPath = "pay/card.php?"; }
	for ($i=0; $i<count($aMsgCds); $i++)
	{
		$msg .= "&msg=". $aMsgCds[$i];
	}

	header("location: http://". $_SERVER['HTTP_HOST'] ."/". $sPath . session_name() ."=". session_id() . $msg);
	exit;
}