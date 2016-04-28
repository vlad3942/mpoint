<?php
/**
 * This files contains the Controller for initializing a payment through WannaFindt and presenting the
 * customer with the payment form
 * The file will construct the HTML form used to authorize the payment by enabling the customer to enter his / her credit card details.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Payment
 * @subpackage WannaFind
 * @version 1.00
 */

// Require Global Include File
require_once("../inc/include.php");

// Require data class for Payment Service Provider Configurations
require_once(sCLASS_PATH ."/pspconfig.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");

// Require Business logic for the Select Credit Card component
require_once(sCLASS_PATH ."/credit_card.php");

// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for the CPM PSP component
require_once(sINTERFACE_PATH ."/cpm_psp.php");
// Require specific Business logic for the wirecard component
require_once(sCLASS_PATH ."/wirecard.php");
// Require specific Business logic for the datacash component
require_once(sCLASS_PATH ."/datacash.php");

// Instantiate main mPoint object for handling the component's functionality
$obj_mPoint = new CreditCard($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo'], $_SESSION['obj_UA']);
$obj_XML = simplexml_load_string($obj_mPoint->getCards($_SESSION['obj_TxnInfo']->getAmount() ) );
$obj_XML = $obj_XML->xpath("item[@id = ". intval($_POST['cardid']) ." and @pspid = ".intval($_POST['pspid'])."]");
$obj_XML = $obj_XML[0];


$obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $_SESSION['obj_TxnInfo']->getClientConfig()->getAccountConfig()->getClientID(), 
$_SESSION['obj_TxnInfo']->getClientConfig()->getAccountConfig()->getID(), intval($_POST['pspid']));

$obj_mPoint = Callback::producePSP($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo'], $aHTTP_CONN_INFO, $obj_PSPConfig);

$obj_XML_initialize = $obj_mPoint->initialize($obj_PSPConfig, $_SESSION['obj_TxnInfo']->getAccountID(), false, intval($_POST['cardid']) );

$checkUserName = $obj_XML_initialize->username->__toString();

if(empty($checkUserName) == true)
{
	$_SESSION['obj_XML_initialize']['user_name'] = htmlspecialchars($_SESSION['obj_TxnInfo']->getClientConfig()->getUsername(), ENT_NOQUOTES);
	$_SESSION['obj_XML_initialize']['password'] = htmlspecialchars($_SESSION['obj_TxnInfo']->getClientConfig()->getPassword(), ENT_NOQUOTES);
}
else 
{
	$_SESSION['obj_XML_initialize']['user_name'] = $obj_XML_initialize->username->__toString();
	$_SESSION['obj_XML_initialize']['password'] = $obj_XML_initialize->password->__toString();
}

$xml = '<?xml version="1.0" encoding="UTF-8"?>';
$xml .= '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/'. General::getMarkupLanguage($_SESSION['obj_UA'], $_SESSION['obj_TxnInfo']) .'/wirecard/postform.xsl"?>';

$xml .= '<root>';
$xml .= '<title>Card Info</title>';

	$xml .= $obj_mPoint->getSystemInfo();
	$xml .= $_SESSION['obj_TxnInfo']->getClientConfig()->toXML();
	$xml .= str_replace('psp-id="-1" card-id="-1"', 'psp-id="'.intval($_POST['pspid']).'" card-id="'.intval($_POST['cardid']).'"', $_SESSION['obj_TxnInfo']->toXML($_SESSION['obj_UA']));
	

$itemFields = "";

foreach($obj_XML_initialize as $key => $data)
{
	
	if($data instanceof SimpleXMLElement && count($data) > 1)
	{
		$itemFields .= '<'.$key.'>';
		$itemFields .= "\n\r";
		foreach($data as $innerKey => $innerValue)
		{
			$itemFields .= '<'.$innerKey.'>'.$innerValue.'</'.$innerKey.'>';
			$itemFields .= "\n\r";
		}		
		$itemFields .= '</'.$key.'>';
		
	} else {
		$itemFields .= '<'.$key.'>'.$data.'</'.$key.'>';
		$itemFields .= "\n\r";
	}	
}

/* $initObject = $obj_XML_initialize->{'hidden-fields'};
$request_time_stamp = gmdate('YmdHis');
$request_id = $initObject->request_id;
$merchant_account_id = $initObject->merchant_account_id;
$transaction_type = $initObject->transaction_type;
$requested_amount = $initObject->requested_amount;
$request_amount_currency = $initObject->request_amount_currency;
$redirect_url = "https://sandbox-engine.thesolution.com/shop/success.html";
$cancel_redirect_url = "https://sandbox-engine.thesolution.com/shop/cancel.html";
$success_redirect_url = "https://sandbox-engine.thesolution.com/shop/success.html";
$ip_address = $initObject->payment_ip_address;
$secret_key = "d1efed51-4cb9-46a5-ba7b-0fdc87a66544";

$sha256 = trim(
		$request_time_stamp
		.$request_id
		.$merchant_account_id
		.$transaction_type
		.$requested_amount
		.$request_amount_currency
		.$redirect_url
		.$ip_address
		.$secret_key
		);

$request_signature = hash('sha256', $sha256);

$itemFields .= '<request_signature>'.$request_signature.'</request_signature>';
$itemFields .= '<request_time_stamp>'.$request_time_stamp.'</request_time_stamp>'; */

$xml .= '<labels>';
$xml .= '<progress>Step 2 of 2</progress>';
$xml .= '	<info>Please enter your card information below</info>';
$xml .= '	<selected-card>Selected Card</selected-card>';
$xml .= '	<price>Price</price>';
$xml .= '	<first-name>First Name</first-name>';
$xml .= '	<last-name>Last Name</last-name>';
$xml .= '		<card-number>Card Number</card-number>';
$xml .= '		<expiry-date>Expiry Date</expiry-date>';
$xml .= '		<expiry-month>mm</expiry-month>';
$xml .= '		<expiry-year>yyyy</expiry-year>';
$xml .= '		<cvc>CVC / CVS</cvc>';
$xml .= '		<cvc-3-help>3 digits (printed on the back of the card)</cvc-3-help>';
$xml .= '		<cvc-4-help>4 digits (printed on the back of the card)</cvc-4-help>';
$xml .= '		<store-card>Save Card Info</store-card>';
$xml .= '		<submit>Complete Payment</submit>';
$xml .= '</labels>';
	
$xml .= '<item merchant-account="'.htmlspecialchars($obj_XML->account, ENT_NOQUOTES).'">
			'.$itemFields.'
		</item>';
	
$xml .= '<card id="'.intval($obj_XML["id"]).'">
			<name>'.htmlspecialchars($obj_XML->name, ENT_NOQUOTES).'</name>
			<width>'.intval($obj_XML->{'logo-width'}).'</width>
			<height>'.intval($obj_XML->{'logo-height'}).'</height>
			<currency>'.intval($obj_XML->currency).'</currency>
		</card>';
$xml .= '</root>';

file_put_contents(sLOG_PATH ."/debug_". date("Y-m-d") ."_postform.log", $xml, FILE_APPEND);

echo $xml;
exit;
?>