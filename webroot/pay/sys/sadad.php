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

$clientId = $_SESSION['obj_TxnInfo']->getClientConfig()->getAccountConfig()->getClientID();
$accountId = $_SESSION['obj_TxnInfo']->getClientConfig()->getAccountConfig()->getID();

$payResponseCode = 200;

$payRequestBody = '<?xml version="1.0" encoding="UTF-8"?>
				<root>
					<pay account="'.$accountId.'" client-id="'.$clientId.'">
					    <transaction store-card="false" id="'.$_SESSION['obj_TxnInfo']->getID().'">
					       <card type-id="31">
		       				   <amount country-id="'.$_SESSION['obj_TxnInfo']->getCountryConfig()->getID().'">'.$_SESSION['obj_TxnInfo']->getAmount().'</amount>
		       				   <token>'.$_REQUEST['sadad_payment_id'].'</token>
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

if ($payResponseCode == 200 && strlen($obj_HTTP->getReplyBody() ) > 0)
{
	
	$obj_Response = simplexml_load_string($obj_HTTP->getReplyBody() );
	
	if(count($obj_Response->{'psp-info'}->url) > 0)
	{
		$url = $obj_Response->{'psp-info'}->url;
	}
	
	$html = "";
	
	if(count($obj_Response->{'psp-info'}->{'hidden-fields'}) > 0)
	{
		$hidden_inputs = '';
	
		$hidden_fields = $obj_Response->{'psp-info'}->{'hidden-fields'}->children();
	
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