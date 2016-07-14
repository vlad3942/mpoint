<?php 

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
define("sAPI_CLASS_PATH", substr(sSYSTEM_PATH, 0, strrpos(sSYSTEM_PATH, "/") ) ."/../php5api/classes/");
// Define path to the General API interfaces
define("sAPI_INTERFACE_PATH", substr(sSYSTEM_PATH, 0, strrpos(sSYSTEM_PATH, "/") ) ."/../php5api/interfaces/");
// Define path to the System classes
define("sCLASS_PATH", sSYSTEM_PATH ."/api/classes/");
// Define path to the System Configuration
define("sCONF_PATH", sSYSTEM_PATH ."/conf/");
// Define Language Path Constant
define("sLANGUAGE_PATH", sSYSTEM_PATH ."/webroot/text/");

// Require API for handling and reporting errors
require_once(sAPI_CLASS_PATH ."report.php");
require_once(sAPI_CLASS_PATH ."text.php");
// Require API for defining the Database interface
require_once(sAPI_INTERFACE_PATH ."database.php");
// Require Database Abstraction API
require_once(sAPI_CLASS_PATH ."/database.php");

// Require API for general functionality
require_once(sCLASS_PATH ."general.php");
require_once(sCLASS_PATH ."home.php");
require_once(sCLASS_PATH ."my_account.php");
require_once(sCLASS_PATH ."basicconfig.php");
require_once(sCLASS_PATH ."countryconfig.php");

// Require global settings file
require_once(sCONF_PATH ."global.php");

// Instantiate connection to the Database
$_OBJ_DB = RDB::produceDatabase($aDB_CONN_INFO["mpoint"]);

// Define language for page translations
define("sLANG", General::getLanguage() );

// Intialise Text Translation Object
$_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH . sLANG ."/global.txt", sLANGUAGE_PATH . sLANG ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");
?>