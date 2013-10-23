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
$HTTP_RAW_POST_DATA .= '<search>';
$HTTP_RAW_POST_DATA .= '<clientid>10007</clientid>';
$HTTP_RAW_POST_DATA .= '<countryid>100</countryid>';
$HTTP_RAW_POST_DATA .= '<transactionno></transactionno>';
$HTTP_RAW_POST_DATA .= '<orderno></orderno>';
$HTTP_RAW_POST_DATA .= '<mobile>30206162</mobile>';
$HTTP_RAW_POST_DATA .= '<email></email>';
$HTTP_RAW_POST_DATA .= '<start-date>01/01/2012 19:00:02</start-date>';
$HTTP_RAW_POST_DATA .= '<end-date>06/01/2014 19:00:02</end-date>';
$HTTP_RAW_POST_DATA .= '</search>';
$HTTP_RAW_POST_DATA .= '</root>';

*/

$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA );

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate("http://". str_replace("mpoint", "mconsole", $_SERVER['HTTP_HOST']) ."/protocols/mconsole.xsd") === true && count($obj_DOM->search) > 0)
	{
		$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);
		
		$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->search->countryid);
		
		$obj_mPoint = new Home($_OBJ_DB, $_OBJ_TXT,$obj_CountryConfig);
		$xml = $obj_mPoint->searchTxnHistory( 
												(string) $obj_DOM->search->clientid,
												(string) $obj_DOM->search->transactionno,
												(string) $obj_DOM->search->orderno,
												(string) $obj_DOM->search->mobile,
												(string) $obj_DOM->search->email,
												(string) $obj_DOM->search->{'customer-reference'},
												(string) $obj_DOM->search->{'start-date'},
												(string) $obj_DOM->search->{'end-date'} );
		
		header("Content-Type: text/xml; charset=\"UTF-8\"");
		$xml =  utf8_encode ( $xml );
		echo $xml;
	
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
header("Content-Type: text/xml; charset=\"UTF-8\"");

echo $xml;
?>