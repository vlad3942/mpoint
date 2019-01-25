<?php
/**
 * The mConsole API to get hourly sales count of captured transactions.
 *
 * @author Deblina Das
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package mConsole
 * @version 2.07
 */
// Require Global Include File
require_once ("../../inc/include.php");

// Require API for Simple DOM manipulation
require_once (sAPI_CLASS_PATH . "simpledom.php");
// Require Business logic for General Administration of mPoint
require_once (sCLASS_PATH . "admin.php");
// Require Business logic for the mConsole Module
require_once (sCLASS_PATH . "/mConsole.php");


/*
 * $_SERVER['PHP_AUTH_USER'] = "CPMDemo";
 * $_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";
 *
 * $HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
 * $HTTP_RAW_POST_DATA .= '<root>';
 * $HTTP_RAW_POST_DATA .= ' <get-hourly-transaction-data client-id="10018">';
 * $HTTP_RAW_POST_DATA .= ' <state-id>2001</state-id>';
 * $HTTP_RAW_POST_DATA .= ' <start-date>2014-12-21T00:00:00</start-date>';
 * $HTTP_RAW_POST_DATA .= ' <end-date>2015-01-07T00:00:00</end-date>';
 * $HTTP_RAW_POST_DATA .= ' </get-hourly-transaction-data>';
 * $HTTP_RAW_POST_DATA .= '</root>';
 */

$obj_DOM = simpledom_load_string ( $HTTP_RAW_POST_DATA );

$obj_mPoint = new mConsole ( $_OBJ_DB, $_OBJ_TXT );

$_OBJ_TXT->loadConstants ( array (
		"AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH,
		"AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH 
) );

if (array_key_exists ( "PHP_AUTH_USER", $_SERVER ) === true && array_key_exists ( "PHP_AUTH_PW", $_SERVER ) === true) {
	
	$aHTTP_CONN_INFO["mesb"]["path"] = Constants::sMCONSOLE_SINGLE_SIGN_ON_PATH;
	$aHTTP_CONN_INFO["mesb"]["username"] = trim($_SERVER['PHP_AUTH_USER']);
	$aHTTP_CONN_INFO["mesb"]["password"] = trim($_SERVER['PHP_AUTH_PW']);
	
	$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["mesb"]);
	
	$iClientID = intval ( $obj_DOM->{'get-hourly-transaction-data'} ['client-id'] );
	
	$aClientIDs=array($iClientID);
	
	$code = $obj_mPoint->singleSignOn($obj_ConnInfo, $_SERVER['HTTP_X_AUTH_TOKEN'], mConsole::sPERMISSION_SEARCH_TRANSACTION_LOGS, $aClientIDs);
	
	switch ($code)
	{
		case (mConsole::iSERVICE_CONNECTION_TIMEOUT_ERROR):
			header("HTTP/1.1 504 Gateway Timeout");
			
			$xml = '<status code="'. $code .'">Single Sign-On Service is unreachable</status>';
			break;
		case (mConsole::iSERVICE_READ_TIMEOUT_ERROR):
			header("HTTP/1.1 502 Bad Gateway");
			
			$xml = '<status code="'. $code .'">Single Sign-On Service is unavailable</status>';
			break;
		case (mConsole::iUNAUTHORIZED_USER_ACCESS_ERROR):
			header("HTTP/1.1 401 Unauthorized");
			
			$xml = '<status code="'. $code .'">Unauthorized User Access</status>';
			break;
		case (mConsole::iINSUFFICIENT_USER_PERMISSIONS_ERROR):
			header("HTTP/1.1 403 Forbidden");
			
			$xml = '<status code="'. $code .'">Insufficient User Permissions</status>';
			break;
		case (mConsole::iINSUFFICIENT_CLIENT_LICENSE_ERROR):
			header("HTTP/1.1 402 Payment Required");
			
			$xml = '<status code="'. $code .'">Insufficient Client License</status>';
			break;
		case (mConsole::iAUTHORIZATION_SUCCESSFUL):
			
			$start_date = $obj_DOM->{'get-hourly-transaction-data'}->{'start-date'} ;
			$end_date =  $obj_DOM->{'get-hourly-transaction-data'}->{'end-date'} ;
			$stateId = $obj_DOM->{'get-hourly-transaction-data'}->{'state-id'} ;
			
			$xml = $obj_mPoint->getHourlyTxnData($iClientID, $start_date, $end_date ,$stateId);
			
			break;
		default:
			header("HTTP/1.1 500 Internal Server Error");
			
			$xml = '<status code="'. $code .'">Internal Error</status>';
			break;
	}
	
	
} else {
	header ( "HTTP/1.1 401 Unauthorized" );
	$xml = '<status code="401">Authorization required</status>';
}

header ( "Content-Type: text/xml; charset=\"UTF-8\"" );

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<root>';
echo $xml;
echo '</root>';
