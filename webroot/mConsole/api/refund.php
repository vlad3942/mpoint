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
/*
$_SERVER['PHP_AUTH_USER'] = "CPMDemo";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<refund>';
$HTTP_RAW_POST_DATA .= '<txnid>'. htmlspecialchars($tid, ENT_NOQUOTES) .'</txnid>';
$HTTP_RAW_POST_DATA .= '<mpointid>'. htmlspecialchars($mid, ENT_NOQUOTES) .'</mpointid>';
$HTTP_RAW_POST_DATA .= '<orderid>'. htmlspecialchars($oid, ENT_NOQUOTES) .'</orderid>';
$HTTP_RAW_POST_DATA .= '<amount>'. htmlspecialchars($amount, ENT_NOQUOTES) .'</amount>';
$HTTP_RAW_POST_DATA .= '</refund>';
$HTTP_RAW_POST_DATA .= '</root>';
*/
$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{	
	if ( ($obj_DOM instanceof SimpleDOMElement) === true &&  $obj_DOM->validate("http://". str_replace("mpoint", "mconsole", $_SERVER['HTTP_HOST']) ."/protocols/mconsole.xsd") === true && count($obj_DOM->refund) > 0)
	{
		header("Content-Type: text/xml; charset=\"UTF-8\"");
		
		$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);
		
		$h = "POST {PATH} HTTP/1.0" .HTTPClient::CRLF;
		$h .= "host: {HOST}" .HTTPClient::CRLF;
		$h .= "referer: {REFERER}" .HTTPClient::CRLF;
		$h .= "content-length: {CONTENTLENGTH}" .HTTPClient::CRLF;
		$h .= "Content-Type: application/x-www-form-urlencoded" .HTTPClient::CRLF;
		
		$obj_Client = new HTTPClient(new Template, HTTPConnInfo::produceConnInfo("http://". $_SERVER["HTTP_HOST"] ."/buy/refund.php") );
		
		$obj_Client->connect();
		
		$b = "clientid=". intval($obj_DOM->refund->clientid) ."&username=". urlencode( $obj_DOM->refund->username ) ."&password=". urlencode( $obj_DOM->refund->password ) ."&mpointid=". intval($obj_DOM->refund->mpointid) ."&orderid=". urlencode($obj_DOM->refund->orderid) ."&amount=". intval($obj_DOM->refund->amount) ;
		
		$code = $obj_Client->send($h, $b);			
		if ($code != 200)
		{
			// Order already refunded
			if (strstr($obj_Client->getReplyBody(), "msg=177") == true) 
			{  
				$xml = '<status code="177"> Order already refunded </status>
						<code>177</code>'; 
			}
			else { trigger_error("Unable to perform refund for Order No.: ". $obj_DOM->refund->orderid ." using mPointID: ". intval($obj_DOM->refund->mpointid) .". mPoint returned: ". t ."\n". "Request Body: ". $b ."\n". "Response Body: ". $obj_Client->getReplyBody() ); }
		}
		else 
		{
			$xml = '<status code="200">Successfully Refunded  </status>
					<code>200</code>';
		}
		$obj_Client->disconnect();
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
			$xml = '<status code="400">Wrong operation: '. $obj_Elem->getName() .'</status>'; 
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
			$xml = '<status code="400">'. htmlspecialchars($aObj_Errs[$i]->message, ENT_NOQUOTES) .'</status>';
		}
	}
}
else
{
	header("HTTP/1.1 401 Unauthorized");
	
	$xml = '<status code="401">Authorization required</status>';
}
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<root>';
echo $xml;
echo '</root>';
?>