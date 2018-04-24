<?php

define("sGOMOBILE_CONF_PATH", "conf");
define("sGOMOBILE_API_PATH", "conf/lib/gomobile");

// HTTP Request
if (isset ( $_SERVER ['DOCUMENT_ROOT'] ) === true && empty ( $_SERVER ['DOCUMENT_ROOT'] ) === false) {
	$_SERVER ['DOCUMENT_ROOT'] = str_replace ( "\\", "/", $_SERVER ['DOCUMENT_ROOT'] );
	// Define system path constant
	define ( "sSYSTEM_PATH", substr ( $_SERVER ['DOCUMENT_ROOT'], 0, strrpos ( $_SERVER ['DOCUMENT_ROOT'], "/" ) ) );
} // Command line
else {
	$aTemp = explode ( "/", str_replace ( "\\", "/", __FILE__ ) );
	$sPath = "";
	for($i = 0; $i < count ( $aTemp ) - 3; $i ++) {
		$sPath .= $aTemp [$i] . "/";
	}
	// Define system path constant
	define ( "sSYSTEM_PATH", substr ( $sPath, 0, strlen ( $sPath ) - 1 ) );
}
/* ========== Define System path End ========== */

// Define path to the General API classes
define ( "sAPI_CLASS_PATH", substr ( sSYSTEM_PATH, 0, strrpos ( sSYSTEM_PATH, "/" ) ) . "/../php5api/classes/" );
// Define path to the General API interfaces
define ( "sAPI_INTERFACE_PATH", substr ( sSYSTEM_PATH, 0, strrpos ( sSYSTEM_PATH, "/" ) ) . "/../php5api/interfaces/" );
// Define path to the System classes
define ( "sCLASS_PATH", sSYSTEM_PATH . "/api/classes/" );
// Define path to the System Configuration
define ( "sCONF_PATH", sSYSTEM_PATH . "/conf/" );

// Require API for handling and reporting errors
require_once (sAPI_CLASS_PATH . "report.php");
// Require API for defining the Database interface
require_once (sAPI_INTERFACE_PATH . "database.php");
// Require Database Abstraction API
require_once (sAPI_CLASS_PATH . "/database.php");
// Require API for parsing HTTP Header Template with text tags: {TEXT_TAG}
require_once(sAPI_CLASS_PATH ."/template.php");
// Require API for handling the connection to a remote webserver using HTTP
require_once(sAPI_CLASS_PATH ."/http_client.php");
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."/simpledom.php");

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

// Require global settings file
require_once (sCONF_PATH . "global.php");

// Require the PHP API for handling the connection to GoMobile
require_once(sGOMOBILE_API_PATH ."/gomobile.php");

// Require the PHP API for handling the connection to SMTP server
require_once(sAPI_CLASS_PATH ."/smtp.php");

// Require global configuration file
require_once(sGOMOBILE_CONF_PATH ."/gomobile.php");

// Instantiate connection to the Database
$_OBJ_DB = RDB::produceDatabase($aDB_CONN_INFO["mpoint"]);

// Local mBE classes
require_once ("classes/enduseraccount.php");
require_once ("classes/chat.php");
require_once ("classes/transaction.php");

?>