<?php
/** 
 *
 * @author Rohit Malhotra
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package API
 * @subpackage MobileApp
 * @version 1.10
 */

// Require Global Include File
require_once("../../inc/include.php");

// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");

require_once(sCLASS_PATH ."/clientconfig.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );

/*
$_SERVER['PHP_AUTH_USER'] = "CPMDemo";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<get-client-configurations user-id="51302">';
$HTTP_RAW_POST_DATA .= '<client-id>10000</client-id>';
$HTTP_RAW_POST_DATA .= '<client-id>10007</client-id>';
$HTTP_RAW_POST_DATA .= '</get-client-configurations>';
$HTTP_RAW_POST_DATA .= '</root>';  
*/ 

$xml = '';

$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mconsole.xsd") === true && count($obj_DOM->{'get-client-configurations'}) > 0)
	{		
		$obj_val = new Validate();
		$userID = intval($obj_DOM->{'get-client-configurations'}['user-id']);
		$clientIDs = (array)$obj_DOM->{'get-client-configurations'}->{'client-id'};
				
		$valErrors = true;
		//Validating to see if the end user has access to the client.
		foreach ($clientIDs as $clientID)
		{
			$valErrors = ($valErrors && $obj_val->valUserAceesToClient($_OBJ_DB, $clientID, $userID));
		}
		
		if($valErrors)
		{			
			foreach ($clientIDs as $clientID)
			{
				$obj_mPointClient = ClientConfig::produceConfig($_OBJ_DB, $clientID);				
				$clientPSPConfig = $obj_mPointClient->getClientPSPConfigToXML($_OBJ_DB, $clientID);
				$clientCardAccess = $obj_mPointClient->getClientCardAccessToXML($_OBJ_DB, $clientID);
				$clientPaymentMethods = $obj_mPointClient->getClientAccountsToXML($_OBJ_DB, $clientID);
				$xml .= $obj_mPointClient->toFullXML($clientCardAccess, $clientPSPConfig, $clientPaymentMethods);
			}				
		}
		else
		{
			header("HTTP/1.1 400 Bad Request");
	
			$xml = '<status code="415">Invalid Customer or client ID  </status>';
		}
	}
	// Error: Invalid XML Document
	elseif ( ($obj_DOM instanceof SimpleDOMElement) === false)
	{
		header("HTTP/1.1 415 Unsupported Media Type");
		
		$xml = '<status code="415">Invalid XML Document</status>';
	}
	// Error: Wrong operation
	elseif (count($obj_DOM->{'save-client-configuration'}) == 0)
	{
		header("HTTP/1.1 400 Bad Request");
		
		$xml = '';
		foreach ($obj_DOM->children() as $obj_Elem)
		{
			$xml .= '<status code="400">Wrong operation: '. $obj_Elem->getName() .'</status>'; 
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
