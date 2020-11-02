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

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>
<root>
	<get-transaction-statistics-by-filter client-id = "10007">
		<filters>
			<filter>
				<key>to</key>
				<value>2016-09-09 24:00:00</value>
			</filter>
			<filter>
				<key>from</key>
				<value>2016-09-09 00:00:00</value>
			</filter>
			<filter>
				<key>stateid</key>
				<value>1009</value>
			</filter>
			<filter>
				<key>stateid</key>
				<value>2000</value>
			</filter>
			<filter>
				<key>stateid</key>
				<value>2001</value>
			</filter>
			<filter>
				<key>paymenttype</key>
				<value>1</value>
			</filter>
		</filters>
		<aggregations>
			<aggregation>stateid</aggregation>
			<aggregation>hour</aggregation>
		</aggregations>
		<columns>
			<column>count</column>
			<column>stateid</column>
			<column>hour</column>
		</columns>
	</get-transaction-statistics-by-filter>
</root>';
*/

$obj_DOM = simpledom_load_string(file_get_contents('php://input'));

$obj_mPoint = new mConsole($_OBJ_DB, $_OBJ_TXT);

$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mconsole.xsd") === true && count($obj_DOM->{'get-transaction-statistics-by-filter'}) > 0)
	{
		$iClientID = 0;
		$iClientID = intval($obj_DOM->{'get-transaction-statistics-by-filter'}['client-id']);

		$aHTTP_CONN_INFO["mesb"]["path"] = Constants::sMCONSOLE_SINGLE_SIGN_ON_PATH;
		$aHTTP_CONN_INFO["mesb"]["username"] = trim($_SERVER['PHP_AUTH_USER']);
		$aHTTP_CONN_INFO["mesb"]["password"] = trim($_SERVER['PHP_AUTH_PW']);

		$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["mesb"]);

		$aClientIDs[] = array();
        $aClientIDs[0] = $iClientID;

		$code = $obj_mPoint->singleSignOn($obj_ConnInfo, $_SERVER['HTTP_X_AUTH_TOKEN'], mConsole::sPERMISSION_VISION_DASHBOARDS, $aClientIDs, $_SERVER['HTTP_VERSION']);

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

			$aFilter = array();
			$aAggregations = array();
			$aColumns = array();
			$limit = $obj_DOM->{'get-transaction-statistics-by-filter'}->limit;
            $sTimeZoneOffset = "0";
            if(count($obj_DOM->{'get-transaction-statistics-by-filter'}->UTCOffset)>0){ $sTimeZoneOffset = $obj_DOM->{'get-transaction-statistics-by-filter'}->UTCOffset;}
			$orderby = array();
			$orderby[(string)$obj_DOM->{'get-transaction-statistics-by-filter'}->orderby->key] = (string)$obj_DOM->{'get-transaction-statistics-by-filter'}->orderby->value;

            for ($i=0; $i<count($obj_DOM->{'get-transaction-statistics-by-filter'}->filters->filter); $i++)
            {
                $key = (string) $obj_DOM->{'get-transaction-statistics-by-filter'}->filters->filter[$i]->key;
                $value = (string) $obj_DOM->{'get-transaction-statistics-by-filter'}->filters->filter[$i]->value;
                if(strtolower($key) == 'state' || strtolower($key) == 'paymenttypeid' || strtolower($key) == 'cardid' || strtolower($key) == 'currency_id' | strtolower($key) == 'country_id')
                {
                    $aFilter[$key][] = $value;
                }
                else
                {
                    $aFilter[$key] = $value;
                }

            }

            for ($i=0; $i<count($obj_DOM->{'get-transaction-statistics-by-filter'}->aggregations->aggregation); $i++)
            {
                $value = (string) $obj_DOM->{'get-transaction-statistics-by-filter'}->aggregations->aggregation[$i];
                $aAggregations[] = $value;
            }

            for ($i=0; $i<count($obj_DOM->{'get-transaction-statistics-by-filter'}->columns->column); $i++)
            {
                $value = (string) $obj_DOM->{'get-transaction-statistics-by-filter'}->columns->column[$i];
                $aColumns[] = $value;
            }

			$sTransactionStats = $obj_mPoint->getTransactionStatsByFilter($iClientID, $aFilter, $aAggregations, $aColumns,$limit,$orderby,$sTimeZoneOffset );
			if(empty($sTransactionStats) === false)
			{
				$xml = $sTransactionStats;
			}
			else
			{
				header("HTTP/1.1 404 Not Found");
				
				$xml = '<status code="404">Transaction statistics could not be retrieved for given conditions </status>';
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
	elseif (count($obj_DOM->{'get-transaction-statistics-by-filter'}) == 0)
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
