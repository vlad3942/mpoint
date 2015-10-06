<?php
/** 
 *
 * @author Abhishek Sawant
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package mConsole
 * @subpackage Statistics
 * @version 1.10
 */

// Require Global Include File
require_once("../../inc/include.php");

// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");
// Require Business logic for General Administration of mPoint
require_once(sCLASS_PATH ."admin.php");
// Require Business logic for the mConsole Module
require_once(sCLASS_PATH ."/mConsole.php");

// Require data data class for Transaction Statistics Information
require_once(sCLASS_PATH ."/transaction_statistics_info.php");

/*
$_SERVER['PHP_AUTH_USER'] = "CPMDemo";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '    <get-transaction-statistics psp-id="2" card-id="8">';
$HTTP_RAW_POST_DATA .= '       <clients>';
$HTTP_RAW_POST_DATA .= '              <client id="10014">';
$HTTP_RAW_POST_DATA .= '                     <accounts>';
$HTTP_RAW_POST_DATA .= '                            <account-id>100022</account-id>';
$HTTP_RAW_POST_DATA .= '                     </accounts>';
$HTTP_RAW_POST_DATA .= '              </client>';
$HTTP_RAW_POST_DATA .= '              <client id="10019">';
$HTTP_RAW_POST_DATA .= '                     <accounts>';
$HTTP_RAW_POST_DATA .= '                            <account-id>100026</account-id>';
$HTTP_RAW_POST_DATA .= '                     </accounts>';
$HTTP_RAW_POST_DATA .= '              </client>';
$HTTP_RAW_POST_DATA .= '       </clients>';
$HTTP_RAW_POST_DATA .= '       <start-date>2014-12-21T00:00:00</start-date>';
$HTTP_RAW_POST_DATA .= '       <end-date>2015-01-07T00:00:00</end-date>';
$HTTP_RAW_POST_DATA .= '    </get-transaction-statistics>';
$HTTP_RAW_POST_DATA .= '</root>'; 
*/

$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

$obj_mPoint = new mConsole($_OBJ_DB, $_OBJ_TXT);

$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mconsole.xsd") === true && count($obj_DOM->{'get-transaction-statistics'}) > 0)
	{
		$aClientIDs = array();
		$aAccountIDs = array();

		for ($i=0; $i<count($obj_DOM->{'get-transaction-statistics'}->clients->client); $i++)
		{
			if((int)$obj_DOM->{'get-transaction-statistics'}->clients->client[$i]->attributes()['id'] != 0)
			{
				$aClientIDs[] = (int)$obj_DOM->{'get-transaction-statistics'}->clients->client[$i]->attributes()['id'];
			}
			
			if($obj_DOM->{'get-transaction-statistics'}->clients->client[$i]->accounts instanceof SimpleDOMElement)
			{
				if($obj_DOM->{'get-transaction-statistics'}->clients->client[$i]->accounts->{'account-id'} != 0)
				{
					$aAccountIDs = array_merge($aAccountIDs, (array)$obj_DOM->{'get-transaction-statistics'}->clients->client[$i]->accounts->{'account-id'});
				}
			}
		}

		$aHTTP_CONN_INFO["mesb"]["path"] = Constants::sMCONSOLE_SINGLE_SIGN_ON_PATH;
		$aHTTP_CONN_INFO["mesb"]["username"] = trim($_SERVER['PHP_AUTH_USER']);
		$aHTTP_CONN_INFO["mesb"]["password"] = trim($_SERVER['PHP_AUTH_PW']);

		$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["mesb"]);

		$code = $obj_mPoint->singleSignOn($obj_ConnInfo, $_SERVER['HTTP_X_AUTH_TOKEN'], mConsole::sPERMISSION_GET_TRANSACTION_STATISTICS, $aClientIDs);
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
			header("HTTP/1.1 200 OK");
			
			$pspid = intval($obj_DOM->{'get-transaction-statistics'}->attributes()['psp-id']);
			$cardid = intval($obj_DOM->{'get-transaction-statistics'}->attributes()['card-id']);
			
			$obj_TransactionStats = $obj_mPoint->getTransactionStats($aClientIDs, str_replace("T", " ", $obj_DOM->{'get-transaction-statistics'}->{'start-date'}), str_replace("T", " ", $obj_DOM->{'get-transaction-statistics'}->{'end-date'}), $aAccountIDs, $pspid, $cardid);
			if($obj_TransactionStats instanceof TransactionStatisticsInfo)
			{
				$xml = $obj_TransactionStats->toXML();
			}
			else
			{
				header("HTTP/1.1 404 Not Found");
				
				$xml = '<status code="404">Transaction statistics did not found for given conditions </status>';
			}

			break;
		default:
			header("HTTP/1.1 500 Internal Server Error");
		
			$xml = '<status code="'. $code .'">Internal Error</status>';
			break;
		}
	}
	// Error: Invalid XML Document
	elseif ( ($obj_DOM instanceof SimpleDOMElement) === false)
	{
		header("HTTP/1.1 415 Unsupported Media Type");

		$xml = '<status code="415">Invalid XML Document</status>';
	}
	// Error: Wrong operation
	elseif (count($obj_DOM->{'get-transaction-statistics'}) == 0)
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
