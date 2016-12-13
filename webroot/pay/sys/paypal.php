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

// Require data data class for Customer Information
require_once(sCLASS_PATH ."/customer_info.php");

// Require Business logic for the Select Credit Card component
require_once(sCLASS_PATH ."/credit_card.php");

header("Content-Type: text/html; charset=\"utf-8\"");

if(array_key_exists('transactionid', $_REQUEST) == true)
{
	$_SESSION['obj_UA'] = UAProfile::produceUAProfile(HTTPConnInfo::produceConnInfo($aUA_CONN_INFO) );
	$_SESSION['obj_TxnInfo'] = TxnInfo::produceInfo($_REQUEST['transactionid'], $_OBJ_DB);
}

$obj_Validator = new Validate($_SESSION['obj_TxnInfo']->getCountryConfig() );

if (intval($_REQUEST['cardtype']) > 0)
{
	if ($obj_Validator->valCardTypeID($_OBJ_DB, intval($_REQUEST['cardtype']))  != 10)
	{
		$aMsgCds[] = 3;

	}
}
else
{
	$aMsgCds[] = 3;

}

$cardTypeId = intval($_REQUEST['cardtype']);

$bNewAccount = false;

if(array_key_exists("store-card", $_REQUEST) === true && $_REQUEST['store-card'] == 'on')
{

	if ($obj_Validator->valName($_REQUEST['cardname']) > 1 && $obj_Validator->valName($_REQUEST['cardname']) != 10)
	{
		$aMsgCds[] = $obj_Validator->valName($_REQUEST['cardname']) + 33;
	}

	if(array_key_exists("new-password", $_REQUEST) === true && array_key_exists("repeat-password", $_REQUEST) === true)
	{
		if ($obj_Validator->valPassword($_REQUEST['new-password']) != 10) { $aMsgCds[] = $obj_Validator->valPassword($_REQUEST['new-password']) + 10; }
		else if ($obj_Validator->valPassword($_REQUEST['repeat-password']) != 10) { $aMsgCds[] = $obj_Validator->valPassword($_REQUEST['repeat-password']) + 20; }
		else if (count($aMsgCds) == 0 && $_REQUEST['new-password'] != $_REQUEST['repeat-password']) { $aMsgCds[] = 31; }
		else { $sPassword = $_REQUEST['new-password']; $bNewAccount = true; }
	}

	$sCardName = $_REQUEST['cardname'];
}

$clientId = $_SESSION['obj_TxnInfo']->getClientConfig()->getAccountConfig()->getClientID();
$accountId = $_SESSION['obj_TxnInfo']->getClientConfig()->getAccountConfig()->getID();

$payResponseCode = 200;

$bStoredCard = 'false';

if(isset($_REQUEST['store-card']) && $_REQUEST['store-card'] == 'on')
{
	$bStoredCard = 'true';
}
		
$payRequestBody = '<?xml version="1.0" encoding="UTF-8"?>
				<root>
					<pay account="'.$accountId.'" client-id="'.$clientId.'">
					    <transaction store-card="'.$bStoredCard.'" id="'.$_SESSION['obj_TxnInfo']->getID().'">
					       <card type-id="'.intval($cardTypeId).'">
		       					 <amount country-id="'.$_SESSION['obj_TxnInfo']->getCountryConfig()->getID().'">'.$_SESSION['obj_TxnInfo']->getAmount().'</amount>
					      </card>
					    </transaction>
					    <client-info language="da" version="1.20" platform="iOS/8.1.3">
		       				<customer-ref>'.$_SESSION['obj_TxnInfo']->getCustomerRef().'</customer-ref>
		       				<mobile country-id="'.$_SESSION['obj_TxnInfo']->getCountryConfig()->getID().'">'.$_SESSION['obj_TxnInfo']->getMobile().'</mobile>
							<email>'.$_SESSION['obj_TxnInfo']->getEMail().'</email>
						    <ip>'.$_SESSION['obj_TxnInfo']->getIP().'</ip>
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

if ($payResponseCode == 200 && strlen($obj_HTTP->getReplyBody() ) > 0)
{
	$obj_Wallet_Response = simplexml_load_string($obj_HTTP->getReplyBody() );

	if($bStoredCard == true)
	{
		$obj_TxnInfo = TxnInfo::produceInfo($_SESSION['obj_TxnInfo']->getID(), $_OBJ_DB);
		
		$obj_mPoint = new CreditCard($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo'], $_SESSION['obj_UA']);
		
		if($obj_TxnInfo->getAccountID() != -1)
		{
			if($bNewAccount == true)
			{
				$obj_mPoint->saveCustomerReference($obj_TxnInfo->getAccountID(), $obj_TxnInfo->getCustomerRef());
					
				$mobile = $obj_TxnInfo->getMobile();
					
				if(empty($mobile) == false)
				{
					$obj_mPoint->saveMobile($obj_TxnInfo->getAccountID(), $mobile);
				}
					
				$email = $obj_TxnInfo->getEMail();
					
				if(empty($email) == false)
				{
					$obj_mPoint->saveEmail($obj_TxnInfo->getMobile(), $obj_TxnInfo->getEMail(), $obj_TxnInfo->getCountryConfig());
				}
		
				$obj_mPoint->savePassword($obj_TxnInfo->getMobile(), $sPassword, $obj_TxnInfo->getCountryConfig());
		
			}
		
			$saveCardCode = $obj_mPoint->saveCardName($obj_TxnInfo->getAccountID(), $cardTypeId, (string) $sCardName);
				
			if($saveCardCode == 2 or $saveCardCode == 1) { $saveCardCode = 102; }
				
		}
	}
	
	if(count($obj_Wallet_Response->{'psp-info'}->url) > 0)
	{
		$url = $obj_Wallet_Response->{'psp-info'}->url;
	}
	
	$html = "";
	
	if(count($obj_Wallet_Response->{'psp-info'}->{'hidden-fields'}) > 0)
	{
		$hidden_inputs = '';
	
		$hidden_fields = $obj_Wallet_Response->{'psp-info'}->{'hidden-fields'}->children();
	
	
		$timestamp = date("YmdHis");
	
		$html .= "<body onload='submitForm();' >";
		$html .= "<form name='secure_page_".$timestamp."' id='secure_page_".$timestamp."' action='".$url."' method='POST'>";
	
		foreach($hidden_fields as $hidden_field)
		{
			$hidden_inputs .= '<input type="hidden" name="'.$hidden_field->getName().'" value="'.$hidden_field.'" /> ';
		}
	
	
		$html .= $hidden_inputs;
	
		$html .= '</form>';
	
		$html .= '<script type="text/javascript">
	
				function submitForm()
				{
					document.getElementById("secure_page_'.$timestamp.'").submit();
				}
	</script></body>';
	}
	$obj_HTTP->disconnect();
	echo $html;
	exit;
}

