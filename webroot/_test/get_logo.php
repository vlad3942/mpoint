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

/**
 * Database settings for mPoint's database
 */
$aDB_CONN_INFO["host"] = "localhost";
$aDB_CONN_INFO["port"] = 5000;
$aDB_CONN_INFO["path"] = "mpoint";
$aDB_CONN_INFO["username"] = "mpoint";
$aDB_CONN_INFO["password"] = "hspzr735abl";
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

//echo "INSERT INTO System.Card_Tbl (name, position, logo) VALUES ('My Account', -1, '". $obj_DB->escBin(file_get_contents("../img/my_account.png") ) ."');" ."\n";
$sql = "UPDATE System".sSCHEMA_POSTFIX.".Card_Tbl SET logo = '". $obj_DB->escBin(file_get_contents("../img/dankort.gif") ) ."' WHERE id = 2;" ."\n";
$obj_DB->query($sql);
?>