<?php
/* ========== Define System path Start ========== */
// HTTP Request
if(isset($_SERVER['DOCUMENT_ROOT']) === true && empty($_SERVER['DOCUMENT_ROOT']) === false)
{
	$_SERVER['DOCUMENT_ROOT'] = str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']);
	// Define system path constant
	define("sSYSTEM_PATH", substr($_SERVER['DOCUMENT_ROOT'], 0, strrpos($_SERVER['DOCUMENT_ROOT'], "/") ) );
}
// Command line
else
{
	$aTemp = explode("/", str_replace("\\", "/", __FILE__) );
	$sPath = "";
	for($i=0; $i<count($aTemp)-3; $i++)
	{
		$sPath .= $aTemp[$i] ."/";
	}
	// Define system path constant
	define("sSYSTEM_PATH", substr($sPath, 0, strlen($sPath)-1) );
}
/* ========== Define System path End ========== */

// Define path to the General API classes
define("sAPI_CLASS_PATH", substr(sSYSTEM_PATH, 0, strrpos(sSYSTEM_PATH, "/") ) ."/php5api/classes/");
// Define path to the General API interfaces
define("sAPI_INTERFACE_PATH", substr(sSYSTEM_PATH, 0, strrpos(sSYSTEM_PATH, "/") ) ."/php5api/interfaces/");

// Require API for defining the Database interface
require_once(sAPI_INTERFACE_PATH ."database.php");

// Require API for handling and reporting errors
require_once(sAPI_CLASS_PATH ."report.php");
// Require Database Abstraction API
require_once(sAPI_CLASS_PATH ."database.php");

define("sLOG_PATH", "../../log/");
set_time_limit(0);
header("Content-Type: text/plain");
/**
 * Database settings for the MEP database
 */
$aDB_CONN_INFO["host"] = "localhost";
$aDB_CONN_INFO["port"] = 5000;
$aDB_CONN_INFO["path"] = "comm";
$aDB_CONN_INFO["username"] = "jona";
$aDB_CONN_INFO["password"] = "oisJona";
$aDB_CONN_INFO["timeout"] = 10;
$aDB_CONN_INFO["charset"] = "ISO8859_1";
$aDB_CONN_INFO["class"] = "PostGreSQL";
$aDB_CONN_INFO["connmode"] = "normal";
$aDB_CONN_INFO["errorpath"] = sLOG_PATH ."db_error_". date("Y-m-d") .".log";
$aDB_CONN_INFO["errorhandling"] = 3;
$aDB_CONN_INFO["exectime"] = 0.3;
$aDB_CONN_INFO["execpath"] = sLOG_PATH ."db_exectime_". date("Y-m-d") .".log";
$aDB_CONN_INFO["keycase"] = CASE_UPPER;
$aDB_CONN_INFO["debuglevel"] = 2;
$aDB_CONN_INFO["method"] = 3;

$obj_DB = RDB::produceDatabase($aDB_CONN_INFO);

$sql = "SELECT cntid, min, max, country
		FROM  System".sSCHEMA_POSTFIX."IPRange_Tbl
		WHERE id > 0
		ORDER BY country ASC";
$res = $obj_DB->query($sql);
while ($RS = $obj_DB->fetchName($res) )
{
	switch ($RS["COUNTRY"])
	{
	case "DENMARK":
		$iCountryID = 100;
		break;
	case "SWEDEN":
		$iCountryID = 101;
		break;
	case "UNITED STATES":
		$iCountryID = 200;
		break;
	default:
		$iCountryID = 0;
		break;
	}
	echo "INSERT INTO System".sSCHEMA_POSTFIX.".IPRange_Tbl (countryid, min, max, country) VALUES (". $iCountryID .", ". $RS["MIN"] .", ". $RS["MAX"] .", '". $obj_DB->escStr($RS["COUNTRY"]) ."');";
	echo "\n";
}
?>