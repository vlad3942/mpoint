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
$HTTP_RAW_POST_DATA .= '<search user-id="1" client-id="10007">';
$HTTP_RAW_POST_DATA .= '<countryid>100</countryid>';
$HTTP_RAW_POST_DATA .= '<transaction-number></transaction>';
$HTTP_RAW_POST_DATA .= '<order-number></order-number>';
$HTTP_RAW_POST_DATA .= '<mobile country-id="100">28882861</mobile>';
$HTTP_RAW_POST_DATA .= '<email></email>';
$HTTP_RAW_POST_DATA .= '<start-date>2012-01-01T09:00:00</start-date>';
$HTTP_RAW_POST_DATA .= '<end-date>2014-06-01T09:00:00</end-date>';
$HTTP_RAW_POST_DATA .= '</search>';
$HTTP_RAW_POST_DATA .= '</root>';
*/


$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA );

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate("http://". str_replace("mpoint", "mconsole", $_SERVER['HTTP_HOST']) ."/protocols/mconsole.xsd") === true && count($obj_DOM->search) > 0)
	{
		$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);
		
		$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->search->mobile["country-id"]);
		
		$obj_mPoint = new Home($_OBJ_DB, $_OBJ_TXT,$obj_CountryConfig);
		$xml = $obj_mPoint->searchTxnHistory( (integer) $obj_DOM->search["user-id"],  (integer) $obj_DOM->search["client-id"], (string) $obj_DOM->search->{'transaction-number'}, (string) $obj_DOM->search->{'order-number'}, (float) $obj_DOM->search->mobile, (string) $obj_DOM->search->email, (string) $obj_DOM->search->{'customer-ref'}, (string) $obj_DOM->search->{'start-date'}, (string) $obj_DOM->search->{'end-date'}, str_replace("T", " ", $obj_DOM->search->{'start-date'}), str_replace("T", " ", $obj_DOM->search->{'end-date'}) );
		
		$obj_mPoint = new Home($_OBJ_DB, $_OBJ_TXT);
		$xml .= $obj_mPoint->getAuditLog($obj_DOM->search->mobile, $obj_DOM->search->email, $obj_DOM->search->{'customer-ref'}, str_replace("T", " ", $obj_DOM->search->{'start-date'}), str_replace("T", " ", $obj_DOM->search->{'end-date'}) );
		
		$xml = utf8_encode($xml);

	}
	// Error: Invalid XML Document
	elseif ( ($obj_DOM instanceof SimpleDOMElement) === false)
	{
		header("HTTP/1.1 415 Unsupported Media Type");
		
		$xml = '<status code="415">Invalid XML Document</status>';
	}
	// Error: Wrong operation
	elseif (count($obj_DOM->search) == 0)
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
header("Content-Type: text/xml; charset=\"UTF-8\"");

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<root>';
echo $xml;
echo '</root>';
?>