<?php
/** 
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package mConsole
 * @subpackage Config
 * @version 1.01
 */

// Require Global Include File
require_once("../../inc/include.php");

// Require Business logic for General Administration of mPoint
require_once(sCLASS_PATH ."admin.php");
// Require Business logic for the mConsole Module
require_once(sCLASS_PATH ."/mConsole.php");
// Require data class for Payment Method Configurations
require_once(sCLASS_PATH ."/payment_method_config.php");
// Require data class for Card Prefix Configurations
require_once(sCLASS_PATH ."/card_prefix_config.php");

/*
$_SERVER['PHP_AUTH_USER'] = "CPMDemo";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2"; 
*/
$xml = '';

$obj_mPoint = new mConsole($_OBJ_DB, $_OBJ_TXT);

$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
	$aHTTP_CONN_INFO["mesb"]["path"] = Constants::sMCONSOLE_SINGLE_SIGN_ON_PATH;
	$aHTTP_CONN_INFO["mesb"]["username"] = trim($_SERVER['PHP_AUTH_USER']);
	$aHTTP_CONN_INFO["mesb"]["password"] = trim($_SERVER['PHP_AUTH_PW']);
	
	$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["mesb"]);
	
	$code = $obj_mPoint->singleSignOn($obj_ConnInfo, $_SERVER['HTTP_X_AUTH_TOKEN'], mConsole::sPERMISSION_GET_PAYMENT_METHODS);	
	switch ($code)
	{
	case (mConsole::iSERVICE_UNAVAILABLE_ERROR):
		header("HTTP/1.1 502 Bad Gateway");
		
		$xml = '<status code="'. $code .'">Single Sign-On Service is unavailable</status>';
		break;
	case (mConsole::iUNAUTHORIZED_USER_ACCESS_ERROR):
		header("HTTP/1.1 401 Unauthorized");
	
		$xml = '<status code="'. $code .'">Unauthorized User Access</status>';
		break;
	case (mConsole::iINSUFFICIENT_PERMISSIONS_ERROR):
		header("HTTP/1.1 403 Forbidden");
		
		$xml = '<status code="'. $code .'">Insufficient Permissions</status>';
		break;
	case (mConsole::iAUTHORIZATION_SUCCESSFUL):
		header("HTTP/1.1 200 OK");
		
		$aObj_Configurations = PaymentMethodConfig::produceAll($_OBJ_DB);
		foreach ($aObj_Configurations as $obj_Config)
		{
			$xml .= $obj_Config->toXML();
		}
		break;
	default:
		header("HTTP/1.1 500 Internal Server Error");
		
		$xml = '<status code="500">Unknown Error</status>';
		break;
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
