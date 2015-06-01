<?php
/**
 *
 * @author Simon Boriis
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package mConsole
 * @version 1.00
 */

// Require Global Include File
require_once("../../inc/include.php");

// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");

require_once(sCLASS_PATH ."/admin.php");
// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );
/*
$_SERVER['PHP_AUTH_USER'] = "CPMDemo";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<save-cards user-id="3">';
$HTTP_RAW_POST_DATA .= '<cards>';
$HTTP_RAW_POST_DATA .= '<card id="149">false</card>';
$HTTP_RAW_POST_DATA .= '<card id="149">4</card>';
$HTTP_RAW_POST_DATA .= '<card id="149">false</card>';
$HTTP_RAW_POST_DATA .= '</cards>';
$HTTP_RAW_POST_DATA .= '</save-cards>';
$HTTP_RAW_POST_DATA .= '</root>';
*/
set_time_limit(0);

$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
	$url = $_SERVER['DOCUMENT_ROOT'] ."/protocols/mconsole.xsd";
	if (file_exists($url) === false) { $url = "http://". str_replace("mpoint", "mconsole", $_SERVER['HTTP_HOST']) ."/protocols/mconsole.xsd"; } 
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate($url) === true && count($obj_DOM->{'save-cards'}) > 0)
	{
		$obj_mPoint = new Admin($_OBJ_DB, $_OBJ_TXT);
		$xml ="<cards>";
		for ($i=0; $i<count($obj_DOM->{'save-cards'}->cards->card); $i++)
		{
			$xml .= $obj_mPoint->updateCardAccess( (integer) $obj_DOM->{'save-cards'}->cards->card[$i]["id"], $obj_DOM->{'save-cards'}->cards->card[$i], $obj_DOM->{'save-cards'}["user-id"]);
		}
		$xml .="</cards>";
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