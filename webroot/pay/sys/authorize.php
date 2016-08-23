<?php
// Require Global Include File
require_once("../../inc/include.php");

require_once(sAPI_CLASS_PATH ."simpledom.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");

// Require Data Class for Client Information
require_once(sCLASS_PATH ."/clientinfo.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");

// Require Business logic for the Select Credit Card component
require_once(sCLASS_PATH ."/credit_card.php");

$aMsgCds = array();

$msg = "";
$sCardHolderName = "";
$sCardName = "";
$sPassword = "";

if($_SESSION['obj_TxnInfo'] === null)
{
	trigger_error("Session expired.", E_USER_ERROR);
	header("location: ".$_SERVER['HTTP_REFERER']);
	exit;
}

$obj_mPoint = new CreditCard($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo'], $_SESSION['obj_UA']);

$obj_Validator = new Validate($_SESSION['obj_TxnInfo']->getCountryConfig() );

if (intval($_POST['cardtype']) > 0)
{
	if ($obj_Validator->valCardTypeID($_OBJ_DB, intval($_POST['cardtype']))  != 10) 
	{ 
		$aMsgCds[] = 3;
		$obj_mPoint->delMessage($_SESSION['obj_TxnInfo']->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE); 
	}
} else 
{ 
		$aMsgCds[] = 3;
		$obj_mPoint->delMessage($_SESSION['obj_TxnInfo']->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE); 
}

$cardTypeId = intval($_POST['cardtype']);

if(array_key_exists("store-card", $_POST) === true && $_POST['store-card'] == 'on')
{
	
	if ($obj_Validator->valName($_POST['cardname']) > 1 && $obj_Validator->valName($_POST['cardname']) != 10) 
	{ 
		$aMsgCds[] = $obj_Validator->valName($_POST['cardname']) + 33; 
		$obj_mPoint->delMessage($_SESSION['obj_TxnInfo']->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);
	}
	
	if(array_key_exists("new-password", $_POST) === true && array_key_exists("repeat-password", $_POST) === true)
	{
		if ($obj_Validator->valPassword($_POST['new-password']) != 10) { $aMsgCds[] = $obj_Validator->valPassword($_POST['new-password']) + 10; }
		else if ($obj_Validator->valPassword($_POST['repeat-password']) != 10) { $aMsgCds[] = $obj_Validator->valPassword($_POST['repeat-password']) + 20; }	
		else if (count($aMsgCds) == 0 && $_POST['new-password'] != $_POST['repeat-password']) { $aMsgCds[] = 31; }
		else { $sPassword = $_POST['new-password']; }
	}
	
	$sCardName = $_POST['cardname'];
}

if(isset($_POST['token']) == false)
{
	$givenDate = $_POST['expiry-month'].'/'.$_POST['expiry-year'];
	
	if(preg_match('/^\\d{2}\\/\\d{2}$/', $givenDate) == 0) 
	{  
		$aMsgCds[] = 19;
		$obj_mPoint->delMessage($_SESSION['obj_TxnInfo']->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);
	}
	else 
	{
		$givenDateTimeStamp = strtotime(gmdate("Y-m-d H:i:sP", mktime(0, 0, 0, $_POST['expiry-month'], 01, $_POST['expiry-year'])));
		
		if ($givenDateTimeStamp < strtotime('today') ) 
		{ 
			$aMsgCds[] = 20; 
			$obj_mPoint->delMessage($_SESSION['obj_TxnInfo']->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);
		}
	}
	
	if( (count($_POST['cardnumber']) == 0 || (count($_POST['cardnumber']) > 0 && $obj_Validator->valCardNumber($_POST['cardnumber']) != 10))
		) 
	{ 
		$aMsgCds[] = 25; 
		$obj_mPoint->delMessage($_SESSION['obj_TxnInfo']->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);
	}
	
	if(empty($_POST['cardholdername']) == false)
	{
		$sCardHolderName = $_POST['cardholdername'];
	} 
	else 
	{ 
		$aMsgCds[] = 25; 
		$obj_mPoint->delMessage($_SESSION['obj_TxnInfo']->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE); 
	}
}

if (count($aMsgCds) == 0)
{
	$msg = 99;
	
	try 
	{

		$clientId = $_SESSION['obj_TxnInfo']->getClientConfig()->getAccountConfig()->getClientID();
		$accountId = $_SESSION['obj_TxnInfo']->getClientConfig()->getAccountConfig()->getID();
		
		$payResponseCode = 200;
		
		if(isset($_POST['store-card']) && $_POST['store-card'] == 'on')
		{
			
			$payRequestBody = '<?xml version="1.0" encoding="UTF-8"?>
					<root>
						<pay account="'.$accountId.'" client-id="'.$clientId.'">
						    <transaction store-card="true" id="'.$_SESSION['obj_TxnInfo']->getID().'">
						       <card type-id="'.intval($cardTypeId).'">
			       					 <amount country-id="'.$_SESSION['obj_TxnInfo']->getCountryConfig()->getID().'">'.$_SESSION['obj_TxnInfo']->getAmount().'</amount>
						      </card>
						    </transaction>
						    <client-info language="da" version="1.20" platform="iOS/8.1.3">
						    </client-info>
						</pay>
					</root>';
			

			$aHTTP_CONN_INFO["mesb"]["path"] = "/mpoint/pay";
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
			
			$obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
			$obj_HTTP->connect();
			$payResponseCode = $obj_HTTP->send($h, $payRequestBody);
			$obj_HTTP->disconnect();
						
		}		
	
		if($payResponseCode == 200)
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
			
			
			if(isset($_POST['token']) == false)
			{
				$transactionType = Constants::iNEW_CARD_PURCHASE_TYPE;
				$cardDetails = ' <card-holder-name>'.$sCardHolderName.'</card-holder-name>
				         <card-number>'.preg_replace("/[^0-9]/", "", $_POST['cardnumber']).'</card-number>
				         <expiry>'.$givenDate.'</expiry>
				         <cvc>'.$_POST['cvv'].'</cvc>';
			}
			else 
			{
				$transactionType = Constants::iCARD_PURCHASE_TYPE;
				$cardDetails = '<token>'.$_POST['token'].'</token>';
				
				if(intval($cardTypeId) == 23)
				{
					$cardDetails .= '<verifier>'.$_POST['verifier'].'</verifier>';
					$cardDetails .= '<checkout-url>'.$_POST['checkouturl'].'</checkout-url>';
				}
			}
						
			$b = '<?xml version="1.0" encoding="UTF-8"?>
				<root>
				  <authorize-payment account="'.$accountId.'" client-id="'.$clientId.'">
				    <transaction type-id="'.$transactionType.'" id="'.$_SESSION['obj_TxnInfo']->getID().'">
				      <card type-id="'.intval($cardTypeId).'">
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
		else 
		{ 
			trigger_error("Not able to stored card.", E_USER_WARNING);
			throw new Exception("Not able to stored card.");
		}
				
	}
	catch(Exception $e)
	{
		$msg = 59;
	}
	
	if ($code == 200)
	{
		$obj_XML = simplexml_load_string($obj_HTTP->getReplyBody() );
		
		$code = $obj_XML->status["code"];
		
		if(array_key_exists("store-card", $_POST) === true && $_POST['store-card'] == 'on')
		{

			if($_SESSION['obj_TxnInfo']->getAccountID() == -1)
			{
				$iStatus = $obj_mPoint->savePassword($_SESSION['obj_TxnInfo']->getMobile(), $sPassword, $_SESSION['obj_TxnInfo']->getClientConfig()->getCountryConfig());
			}			
			
			$code = saveCardName($_OBJ_DB, $obj_mPoint, $_SESSION['obj_TxnInfo'], $cardTypeId, $sCardName);
			
			if($code == 2 or $code == 1) { $msg = 102; }
			
			$url = "http://". $_SERVER['SERVER_NAME'] ."/pay/accept.php?mpoint-id=". $_SESSION['obj_TxnInfo']->getID() ."&". session_name() ."=". session_id() ."&msg=". $msg;
			
		} 
		else 
		{
			
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

function saveCardName($_OBJ_DB, $obj_mPoint, $obj_TxnInfo, $cardid, $name)
{

	$iAccountID = -1;
	if ($obj_TxnInfo->getAccountID() > 0) { $iAccountID = $obj_TxnInfo->getAccountID(); }
	elseif (strlen($obj_TxnInfo->getCustomerRef() ) > 0) { $iAccountID = EndUserAccount::getAccountIDFromExternalID($_OBJ_DB, $obj_TxnInfo->getClientConfig(), $obj_TxnInfo->getCustomerRef() ); }
	if ($iAccountID == -1 && trim($obj_TxnInfo->getMobile() ) != "") { $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_TxnInfo->getClientConfig(), $obj_TxnInfo->getMobile() ); }
	if ($iAccountID == -1 && trim($obj_TxnInfo->getEMail() ) != "") { $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_TxnInfo->getClientConfig(), $obj_TxnInfo->getEMail() ); }
	// Client supports global storage of payment cards
	if ($iAccountID == -1 && $obj_TxnInfo->getClientConfig()->getStoreCard() > 3)
	{
		if (strlen($obj_TxnInfo->getCustomerRef() ) > 0) { $iAccountID = EndUserAccount::getAccountIDFromExternalID($_OBJ_DB, $obj_TxnInfo->getClientConfig(), $obj_TxnInfo->getCustomerRef(), false); }
		if ($iAccountID == -1 && trim($obj_TxnInfo->getMobile() ) != "") { $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_TxnInfo->getClientConfig(), $obj_TxnInfo->getCountryConfig(), $obj_TxnInfo->getMobile(), false); }
		if ($iAccountID == -1 && trim($obj_TxnInfo->getEMail() ) != "") { $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_TxnInfo->getClientConfig(), $obj_TxnInfo->getCountryConfig(), $obj_TxnInfo->getEMail(), false); }
	}
	$iStatus = $obj_mPoint->saveCardName($iAccountID, $cardid, $name);
	
	return $iStatus;
}