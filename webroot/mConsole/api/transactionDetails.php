<?php
/**
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package mConsole
 * @version 1.10
 */

// Require Global Include File
require_once("../../inc/include.php");

// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );

$_SERVER['PHP_AUTH_USER'] = "CPMDemo";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<transactiondetails>';
$HTTP_RAW_POST_DATA .= '<userid>12312321</userid>';
$HTTP_RAW_POST_DATA .= '<clientid>3</clientid>';
$HTTP_RAW_POST_DATA .= '</transactiondetails>';
$HTTP_RAW_POST_DATA .= '</root>';

$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate("http://". str_replace("mpoint", "mconsole", $_SERVER['HTTP_HOST']) ."/protocols/mconsole.xsd") === true && count($obj_DOM->transactiondetails) > 0)
	{
		
			$xml = '
<transaction id="79299" mpoint-id="1756709" psp-id="710382481" order-no="906-29987547" status="">
	<client-info app-id="4" platform="iOS/6.1.3" version="1.11" language="da">
	<device-id>07677455385448515b0ef3e82a303440</device-id>
	</client-info>
		<amount country-id="100" currency="kr." symbol="kr" format="{PRICE} {CURRENCY}">35000</amount>
		<refund country-id="100" currency="kr." symbol="kr" format="{PRICE} {CURRENCY}">0</refund>
		<client id="10013" refund-order="true" resend-receipt="true">
		<name>Wallet.dk</name>
	
	</client>
		<customer id="49941">
		<name>Simon Boriis</name>
		<mobile country-id="100">30206162</mobile>
		<email>simon@cellpointmobile.com</email>
	</customer>
	
	<sales-date>05/04-13 15:14:50</sales-date>
	<authorized epoch="1365174939.65089">05/04-13 17:15:39</authorized>
	<captured>05/04-13 17:15:48</captured>
	<refunded></refunded>
	<refund-confirmed></refund-confirmed>
	<transferred></transferred>
	<transfer-confirmed></transfer-confirmed>

	 <products>
		<product id="16554" type-id="15">
			<name>Wallet to Wallet</name>
		</product>
	</products>

<wallet-to-wallet>
	<from account-id="49941">
		<name>Simon Boriis</name>
	</from>
	<to account-id="49731">
		<name>Jona </name>
   </to>
	<message> hey payback </message>
</wallet-to-wallet>
<notes></notes>
</transaction>';	
	
	}
	// Error: Invalid XML Document
	elseif ( ($obj_DOM instanceof SimpleDOMElement) === false)
	{
		header("HTTP/1.1 415 Unsupported Media Type");
		
		$xml = '<status code="415">Invalid XML Document</status>';
	}
	// Error: Wrong operation
	elseif (count($obj_DOM->login) == 0)
	{
		header("HTTP/1.1 400 Bad Request");
	
		$xml = '';
		foreach ($obj_DOM->children() as $obj_Elem)
		{
			$xml .= '<status code="400">Wrong operation: '. $obj_Elem->getName() .'</status>'; 
		}
	}
	// Error: Invalid Input
	else
	{
		header("HTTP/1.1 400 Bad Request");
		$aObj_Errs = libxml_get_errors();
		
		$xml = '';
		for ($i=0; $i<count($aObj_Errs); $i++)
		{
			$xml .= '<status code="400">'. htmlspecialchars($aObj_Errs[$i]->message, ENT_NOQUOTES) .'</status>';
		}
	}
}
else
{
	header("HTTP/1.1 401 Unauthorized");
	
	$xml = '<status code="401">Authorization required</status>';
}
header("Content-Type: text/xml; charset=\"UTF-8\"");

echo $xml;
?>