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
define("sAPI_CLASS_PATH", substr(sSYSTEM_PATH, 0, strrpos(sSYSTEM_PATH, "/") ) ."/../php5api/classes/");
// Define path to the General API interfaces
define("sAPI_INTERFACE_PATH", substr(sSYSTEM_PATH, 0, strrpos(sSYSTEM_PATH, "/") ) ."/../php5api/interfaces/");
// Define path to the General API functions
define("sAPI_FUNCTION_PATH", substr(sSYSTEM_PATH, 0, strrpos(sSYSTEM_PATH, "/") ) ."/../php5api/functions/");

// Define path to the System classes
define("sCLASS_PATH", sSYSTEM_PATH ."/api/classes/");
// Define path to the System interfaces
define("sINTERFACE_PATH", sSYSTEM_PATH ."/api/interfaces/");
// Define path to the System functions
define("sFUNCTION_PATH", sSYSTEM_PATH ."/api/functions/");
// Define path to PHP libraries used for the system
define("sLIB_PATH", sSYSTEM_PATH ."/api/lib/");
// Define path to the System Configuration
define("sCONF_PATH", sSYSTEM_PATH ."/conf/");
// Define Language Path Constant
define("sLANGUAGE_PATH", sSYSTEM_PATH ."/webroot/text/");

// Require API for defining the Database interface
require_once(sAPI_INTERFACE_PATH ."database.php");

// Require API for handling and reporting errors
require_once(sAPI_CLASS_PATH ."report.php");
// Require API for parsing Templates with text tags: {TEXT_TAG}
require_once(sAPI_CLASS_PATH ."template.php");
// Require API for handling the connection to a remote webserver using HTTP
require_once(sAPI_CLASS_PATH ."http_client.php");
// Require API for handling and reporting errors to a remote host
require_once(sAPI_CLASS_PATH ."remote_report.php");
// Require Database Abstraction API
require_once(sAPI_CLASS_PATH ."database.php");
// Require API for Custom User Session handling
require_once(sAPI_CLASS_PATH ."session.php");
// Require API for Text Transalation
require_once(sAPI_CLASS_PATH ."text.php");
// Require API for controlling Output prior to sending it to the device
require_once(sAPI_CLASS_PATH ."output.php");
// Require API for handling resizing of images
require_once(sAPI_CLASS_PATH ."image.php");
// Require API for determining device capabilities via the User Agent Profile
require_once(sAPI_CLASS_PATH ."uaprofile.php");

// Require Global function file
require_once(sAPI_FUNCTION_PATH ."global.php");


// Require API for Web Session handling
require_once(sCLASS_PATH ."websession.php");
// Require API for general functionality
require_once(sCLASS_PATH ."general.php");
// Require abstract class with system wide constants
require_once(sCLASS_PATH ."/constants.php");
// Require super class for all Configurations
require_once(sCLASS_PATH ."/basicconfig.php");
// Require data class for Country Configurations
require_once(sCLASS_PATH ."/countryconfig.php");
// Require data class for Client Configurations
require_once(sCLASS_PATH ."/client_config.php");
// Require data class for Account Configurations
require_once(sCLASS_PATH ."/account_config.php");
// Require data class for Merchant Sub Account config
require_once(sCLASS_PATH ."/client_merchant_subaccount_config.php");
// Require data class for Merchant Account config
require_once(sCLASS_PATH ."/client_merchant_account_config.php");
// Require data class for Client card config
require_once(sCLASS_PATH ."/client_payment_method_config.php");
// Require data class for Client URL config
require_once(sCLASS_PATH ."/client_url_config.php");
// Require data class for Client URL config
require_once(sCLASS_PATH ."/client_issuer_identifcation_number_range_config.php");
// Require data class for Keyword Configurations
require_once(sCLASS_PATH ."/keywordconfig.php");
// Require data class for Shop Configuration
require_once(sCLASS_PATH ."/shopconfig.php");
// Require data class for Payment Service Provider Configurations
require_once(sCLASS_PATH ."/pspconfig.php");
// Require data data class for Transaction Information
require_once(sCLASS_PATH ."/txninfo.php");
// Require Business logic for the End-User Administration Component
require_once(sCLASS_PATH ."/home.php");
// Require PSP functionality interfaces
require_once(sINTERFACE_PATH ."/captureable.php");
require_once(sINTERFACE_PATH ."/refundable.php");
require_once(sINTERFACE_PATH ."/Voiadable.php");

// Require specific Business logic for the Status component
require_once(sCLASS_PATH ."/status.php");

// Require global settings file
require_once(sCONF_PATH ."global.php");

// Set Custom Error & Exception handlers
new RemoteReport(HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["iemendo"]), iOUTPUT_METHOD, sERROR_LOG, iDEBUG_LEVEL);

// Web Request
if ( (eregi("/buy/", $_SERVER['PHP_SELF']) == false || eregi("/buy/web.php", $_SERVER['PHP_SELF']) == true || eregi("/buy/topup.php", $_SERVER['PHP_SELF']) == true)
	&& eregi("/subscr/", $_SERVER['PHP_SELF']) == false && eregi("/callback/", $_SERVER['PHP_SELF']) == false
	&& eregi("/surepay/", $_SERVER['PHP_SELF']) == false && empty($_SERVER['DOCUMENT_ROOT']) === false
	&& eregi("/pay/sys/sms.php", $_SERVER['PHP_SELF']) == false && eregi("/api/", $_SERVER['PHP_SELF']) == false)
{
	// Start user session
	new Session($aDB_CONN_INFO["session"], iOUTPUT_METHOD, sERROR_LOG);

	// Session object not initialized
	if (isset($_SESSION['obj_Info']) === false)
	{
		$_SESSION['obj_Info'] = new WebSession();
	}

	// Not fetching an Image or performing a back-end process and accessing the mobile website
	if (eregi("/img/", $_SERVER['PHP_SELF']) == false && eregi("/sys/", $_SERVER['PHP_SELF']) == false
		&& (eregi("/pay/", $_SERVER['PHP_SELF']) == true || eregi("/shop/", $_SERVER['PHP_SELF']) == true
			|| eregi("/anet/", $_SERVER['PHP_SELF']) == true || eregi("/wannafind/", $_SERVER['PHP_SELF']) == true
			|| $_SERVER['PHP_SELF'] == "/overview.php" || $_SERVER['PHP_SELF'] == "/terms.php"
			|| (eregi("/new/", $_SERVER['PHP_SELF']) == true && General::getBrowserType() == "mobile") ) )
	{
		// User Agent Profile not instantiated
		if (array_key_exists("obj_UA", $_SESSION) === false)
		{	
			// Instantiate data object with the User Agent Profile for the customer's mobile device.
			$_SESSION['obj_UA'] = UAProfile::produceUAProfile(HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["iemendo"]) );
		}
		/*
		 * Use Output buffering to "magically" transform the XML via XSL behind the scene
		 * This means that all PHP scripts must output a wellformed XML document.
		 * The XML in turn must refer to an XSL Stylesheet by using the xml-stylesheet tag
		 */
		ob_start(array(new Output("all", false, $_SESSION['obj_UA']), "transform") );
	}
	else { header('Content-Type: text/xml; charset="UTF-8"'); }
}

// Instantiate connection to the Database
$_OBJ_DB = RDB::produceDatabase($aDB_CONN_INFO["mpoint"]);

// Payment link activated, use /overview.php og /shop/products.php through a rewrite rule defined by .htaccess
if (array_key_exists("checksum", $_GET) === true && $_SERVER['REQUEST_METHOD'] == "GET" && eregi("/new/", $_SERVER['PHP_SELF']) == false)
{
	$_SESSION['obj_TxnInfo'] = General::produceTxnInfo($_OBJ_DB, $_GET['checksum']);
}
// Define language for page translations
define("sLANG", General::getLanguage() );

// Intialise Text Translation Object
$_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH . sLANG ."/global.txt", sLANGUAGE_PATH . sLANG ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");
?>