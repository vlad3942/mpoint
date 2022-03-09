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
define ('sAPI_PATH', sSYSTEM_PATH . '/vendor/cellpointmobile/php5api');
// Define path to the General API classes
define("sAPI_CLASS_PATH", sAPI_PATH . '/classes/');
// Define path to the General API interfaces
define("sAPI_INTERFACE_PATH", sAPI_PATH . '/interfaces/');
// Define path to the General API functions
define("sAPI_FUNCTION_PATH", sAPI_PATH . '/functions/');

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
// Define path to the Environment Configuration
define("sENV_PATH", sSYSTEM_PATH ."/env/");
// Define Language Path Constant
define("sLANGUAGE_PATH", sSYSTEM_PATH ."/webroot/text/");
require_once(sSYSTEM_PATH . '/vendor/autoload.php');
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
// Require API for Text Transalation // This functionality no longer required. To minimize the impact dummy class is created instead of removing code. Code refactoring is required in phase 2.
//require_once(sCLASS_PATH ."core/TranslateText.php");
use api\classes\core\TranslateText;
// Require API for controlling Output prior to sending it to the device
require_once(sAPI_CLASS_PATH ."output.php");
// Require API for handling resizing of images
require_once(sAPI_CLASS_PATH ."image.php");
// Require API for determining device capabilities via the User Agent Profile
require_once(sAPI_CLASS_PATH ."uaprofile.php");
// Require API containing generic validation functions
require_once(sAPI_CLASS_PATH ."validate_base.php");

// Require Global function file
require_once(sAPI_FUNCTION_PATH ."global.php");


// Require API for Web Session handling
require_once(sCLASS_PATH ."websession.php");
// Require Basic HTTP API and helper functions
require_once(sCLASS_PATH ."/http.php");
// Require general mPoint exceptions classes
require_once(sCLASS_PATH ."/exceptions.php");
// Require API for general functionality
require_once(sCLASS_PATH ."general.php");
// Require abstract class with system wide constants
require_once(sCLASS_PATH ."/constants.php");
// Require super class for all Configurations
require_once(sCLASS_PATH ."/basicconfig.php");
// Require data class for Country Configurations
require_once(sCLASS_PATH ."/countryconfig.php");
// Require data class for Currency Configurations
require_once(sCLASS_PATH ."/currencyconfig.php");
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
// Require data class for Client GoMobile Configurations
require_once(sCLASS_PATH ."/client_gomobile_config.php");
// Require data class for Client Communication Channels Configurations
require_once(sCLASS_PATH ."/client_communication_channel_config.php");
// Require data class for Keyword Configurations
require_once(sCLASS_PATH ."/keywordconfig.php");
// Require data class for Shop Configuration
require_once(sCLASS_PATH ."/shopconfig.php");
// Require data class for Payment Service Provider Configurations
require_once(sCLASS_PATH ."/pspconfig.php");
// Require data data class for Transaction Information
require_once(sCLASS_PATH ."/txninfo.php");
// Require data data class for Payment Session Information
require_once(sCLASS_PATH ."/paymentsession.php");
// Require data data class for Order/Cart Information
require_once(sCLASS_PATH ."/order_info.php");
// Require data data class for Flight Information
require_once(sCLASS_PATH ."/flight_info.php");
// Require data data class for Passenger Information
require_once(sCLASS_PATH ."/passenger_info.php");
// Require data data class for Address Information
require_once(sCLASS_PATH ."/address_info.php");
// Require Business logic for the End-User Administration Component
require_once(sCLASS_PATH ."/home.php");
// Require general Business logic for the SurePay module
require_once(sCLASS_PATH ."/surepayconfig.php");
// Require PSP functionality interfaces
require_once(sINTERFACE_PATH ."/captureable.php");
require_once(sINTERFACE_PATH ."/refundable.php");
require_once(sINTERFACE_PATH ."/voidable.php");
require_once(sINTERFACE_PATH ."/redeemable.php");
require_once(sINTERFACE_PATH ."/invoiceable.php");

require_once(sCLASS_PATH ."/crs/TransactionTypeConfig.php");

// Require specific Business logic for the Status component
require_once(sCLASS_PATH ."/status.php");

// Require global settings file
require_once(sCONF_PATH ."global.php");

// Set Custom Error & Exception handlers
new RemoteReport(HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["iemendo"]), iOUTPUT_METHOD, sERROR_LOG, iDEBUG_LEVEL);

// Instantiate connection to the Database
$_OBJ_DB = RDB::produceDatabase($aDB_CONN_INFO["mpoint"]);

// Payment link activated, use /overview.php og /shop/products.php through a rewrite rule defined by .htaccess
if (array_key_exists("checksum", $_GET) === true && $_SERVER['REQUEST_METHOD'] == "GET" && preg_match("/new/", $_SERVER['PHP_SELF']) == false)
{
	$_SESSION['obj_TxnInfo'] = General::produceTxnInfo($_OBJ_DB, $_GET['checksum']);
}
// Define language for page translations
define("sLANG", General::getLanguage() );

// Intialise Text Translation Object
$_OBJ_TXT = new api\classes\core\TranslateText(array(sLANGUAGE_PATH . sLANG ."/global.txt", sLANGUAGE_PATH . sLANG ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");

?>