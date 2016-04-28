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
// Require specific Business logic for the datacash component
require_once(sCLASS_PATH ."/datacash.php");

// Require Business logic for the Select Credit Card component
require_once(sCLASS_PATH ."/credit_card.php");

$obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $_SESSION['obj_TxnInfo']->getClientConfig()->getAccountConfig()->getClientID(), 
$_SESSION['obj_TxnInfo']->getClientConfig()->getAccountConfig()->getID(), intval($_POST['pspid']));

$obj_mPoint = Callback::producePSP($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo'], $aHTTP_CONN_INFO, $obj_PSPConfig);

if (strlen($_SESSION['obj_TxnInfo']->getOrderID() ) > 0 && $obj_mPoint->orderAlreadyAuthorized($_SESSION['obj_TxnInfo']->getOrderID() ) == false) 
{
	switch(intval($_POST['pspid']))
	{
		case (Constants::iWIRE_CARD_PSP):
			
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
			
			$obj_Client = new HTTPClient(new Template(), $obj_ConnInfo);
			$obj_Client->connect();
			$code = $obj_Client->send($h, $b);
			$obj_Client->disconnect();
			
			if($code == 201)
			{
				$code = 2000;
			}
		break;
			
		case (Constants::iDATA_CASH_PSP):
			
			$b = "gatewayReturnURL=".$_POST['gatewayReturnURL'];
			$b .= "&gatewayCardNumber=".$_POST['card-number'];
			$b .= "&gatewayCardExpiryDateMonth=".$_POST['emonth'];
			$b .= "&gatewayCardExpiryDateYear=".substr($_POST['eyear'], -2);
			$b .= "&gatewayCardSecurityCode=".$_POST['cvc'];
			$b .= "&merchant=".$_POST['merchant'];
			$b .= "&order.id=".$_POST['orderid'];
			$b .= "&order.amount=".$_POST['orderamount'];
			$b .= "&order.currency=GBP";
			$b .= "&session.id=".$_POST['sessionid'];
			$b .= "&transaction.id=".$_POST['transactionid'];
			$b .= "&sourceOfFunds.type=CARD";
			$b .= "&mpoint-id=".$_POST['mpoint-id'];
			$b .= "&store-card=".(!empty($_POST['store-card'])?true:false);
			
			file_put_contents(sLOG_PATH ."/debug_". date("Y-m-d") ."_datacash.log", "FirstRequest : ".$b."\n\n", FILE_APPEND);
									
			$aHTTP_CONN_INFO["data-cash"]["protocol"] = "https";
			$aHTTP_CONN_INFO["data-cash"]["port"] = "443";
			$aHTTP_CONN_INFO["data-cash"]["host"] = "test-gateway.mastercard.com";
			$aHTTP_CONN_INFO["data-cash"]["path"] = "/form/".$_POST['sessionid'];
			$aHTTP_CONN_INFO["data-cash"]["method"] = "POST";
			$aHTTP_CONN_INFO["data-cash"]["contenttype"] = "application/x-www-form-urlencoded";
							
			$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["data-cash"]);
				
			$h = "{METHOD} {PATH} HTTP/1.0" .HTTPClient::CRLF;
			$h .= "host: {HOST}" .HTTPClient::CRLF;
			$h .= "referer: {REFERER}" .HTTPClient::CRLF;
			$h .= "content-length: {CONTENTLENGTH}" .HTTPClient::CRLF;
			$h .= "content-type: {CONTENTTYPE}" .HTTPClient::CRLF;
			$h .= "user-agent: mPoint" .HTTPClient::CRLF;
										
			$obj_Client = new HTTPClient(new Template(), $obj_ConnInfo);
			$obj_Client->connect();
			$code = $obj_Client->send($h, $b);
			$obj_Client->disconnect();
			
						
			//if (strlen($obj_Client->getReplyBody() ) > 0)
			{
				$obj_XML = $obj_Client->getReplyBody();
				
				$dom = new DOMDocument();
				
				$dom->loadHTML($obj_XML);
				
				$requiredData = current(element_to_obj($dom->documentElement)['children'][1]['children'])['children'];
				
				$dataToSend = array();
				
				foreach($requiredData as $data)
				{
					if(isset($data['name']) == true)
					{
						$dataToSend[$data['name']] = $data['value'];
					}
				
				}
								
				$b = http_build_query($dataToSend);
				
				file_put_contents(sLOG_PATH ."/debug_". date("Y-m-d") ."_datacash.log", "second request : ".$b."\n\n", FILE_APPEND);
				
				$urlData = parse_url($dataToSend['gatewayReturnURL']);
				
				$aHTTP_CONN_INFO["data-cash"]["protocol"] = $urlData['scheme'];
				
				if(isset($urlData['port']) == true)
				{
					$aHTTP_CONN_INFO["data-cash"]["port"] = $urlData['port'];
				} else { $aHTTP_CONN_INFO["data-cash"]["port"] = 10080; }			
				
				$aHTTP_CONN_INFO["data-cash"]["host"] = $urlData['host'];
				$aHTTP_CONN_INFO["data-cash"]["path"] = $urlData['path'];
				$aHTTP_CONN_INFO["data-cash"]["method"] = "POST";
				$aHTTP_CONN_INFO["data-cash"]["contenttype"] = "application/x-www-form-urlencoded";
				$aHTTP_CONN_INFO["data-cash"]["username"] = $_SESSION['obj_XML_initialize']['user_name'];
				$aHTTP_CONN_INFO["data-cash"]["password"] = $_SESSION['obj_XML_initialize']['password'];
				
				file_put_contents(sLOG_PATH ."/debug_". date("Y-m-d") ."_datacash.log", "Second Request Details : ".var_export($aHTTP_CONN_INFO, true)."\n\n", FILE_APPEND);
								
				unset($_SESSION['obj_XML_initialize']);				
				
				$h = "{METHOD} {PATH} HTTP/1.0" .HTTPClient::CRLF;
				$h .= "host: {HOST}" .HTTPClient::CRLF;
				$h .= "referer: {REFERER}" .HTTPClient::CRLF;
				$h .= "content-length: {CONTENTLENGTH}" .HTTPClient::CRLF;
				$h .= "content-type: {CONTENTTYPE}" .HTTPClient::CRLF;
				$h .= "user-agent: mPoint" .HTTPClient::CRLF;
				$h .= "Authorization: Basic ". base64_encode($aHTTP_CONN_INFO["data-cash"]["username"] .":". $aHTTP_CONN_INFO["data-cash"]["password"]) .HTTPClient::CRLF;
				
				$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["data-cash"]);
					
				$obj_Client = new HTTPClient(new Template(), $obj_ConnInfo);
				$obj_Client->connect();
				$code = $obj_Client->send($h, $b);
				$obj_Client->disconnect();	
				
				file_put_contents(sLOG_PATH ."/debug_". date("Y-m-d") ."_datacash.log", "Final Response : ".var_export($obj_Client->getReplyBody(), true)."\n", FILE_APPEND);
	
				if($code == 200)
				{
					$code = 2000;
				}
			} /* else {
				$code = 502;
			} */
			
			break;	
	}
	try
	{
	
		if ($code == 2000)
		{
			$obj_mPoint->newMessage($_SESSION['obj_TxnInfo']->getID(), Constants::iPAYMENT_ACCEPTED_STATE, $obj_Client->getReplyBody());
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
	if (strlen($obj_mPoint->getTxnInfo()->getAuthenticationURL() ) > 0 || $obj_mPoint->getTxnInfo()->getAccountID() > 0)
	{
		$url = "http://". $_SERVER['SERVER_NAME'] ."/pay/name.php?mpoint-id=". $_SESSION['obj_TxnInfo']->getID() ."&". session_name() ."=". session_id() ."&cardid=". $_POST['cardid'];
	}
	else { $url = "http://". $_SERVER['SERVER_NAME'] ."/pay/pwd.php?mpoint-id=". $_SESSION['obj_TxnInfo']->getID() ."&". session_name() ."=". session_id() ."&cardid=". $_POST['cardid']; }
}
else { $url = "http://". $_SERVER['SERVER_NAME'] ."/pay/accept.php?mpoint-id=". $_SESSION['obj_TxnInfo']->getID() ."&". session_name() ."=". session_id(); }

header("location: ". $url);

function element_to_obj($element) {

	$obj = array( "tag" => $element->tagName );
	foreach ($element->attributes as $attribute) {
		$obj[$attribute->name] = $attribute->value;
	}

	foreach ($element->childNodes as $subElement) {
		if ($subElement->nodeType != XML_TEXT_NODE) {
			$obj["children"][] = element_to_obj($subElement);
		}
	}
	return $obj;
}