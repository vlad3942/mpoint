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

require_once(sCLASS_PATH ."/admin.php");

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );
/*
$_SERVER['PHP_AUTH_USER'] = "CPMDemo";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<login>';
$HTTP_RAW_POST_DATA .= '<username>DSB</username>';
$HTTP_RAW_POST_DATA .= '<password>hdfy28abdl</password>';
$HTTP_RAW_POST_DATA .= '</login>';
$HTTP_RAW_POST_DATA .= '</root>';
*/
$obj_DOM = simpledom_load_string(file_get_contents('php://input'));

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate("http://". str_replace("mpoint", "mconsole", $_SERVER['HTTP_HOST']) ."/protocols/mconsole.xsd") === true && count($obj_DOM->login) > 0)
	{		
				$obj_mPoint = new Admin($_OBJ_DB, $_OBJ_TXT);
				$iUserID = -1;
				if ($obj_mPoint->auth($obj_DOM->login->username, $obj_DOM->login->password, $iUserID) === 10)
				{
					$xml = '<status code="100">Login successful</status>';
					$xml .= "<id>". $iUserID ."</id>";
					$xml .= "<username>".$obj_DOM->login->username."</username>";
					$xml .= "<password>". $obj_DOM->login->password."</password>";
				}
				else 
				{
 					header("HTTP/1.0 403 Forbidden");
 					$xml = "<status code=403>User not found or wrong Username/Password </status>";						
				}				
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

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<root>';
echo $xml;
echo '</root>';
?>