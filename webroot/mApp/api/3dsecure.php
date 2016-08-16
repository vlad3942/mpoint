<?php
/**
 * This files contains the Controller for mPoint's Mobile Web API.
 * The Controller will collect and try to parse the input 3dsecure challenge using dedicated endpoints for challenge parsing, based on provider
 *
 * @author Johan Thomsen
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package API
 * @subpackage MobileApp
 * @version 1.99
 */

// Require Global Include File
require_once("../../inc/include.php");

// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");
// Require data class for Payment Service Provider Configurations
require_once(sCLASS_PATH ."/pspconfig.php");
// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for the CPM PSP component
require_once(sINTERFACE_PATH ."/cpm_psp.php");
// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
// Require Business logic for the 3D Secure model operation
require_once(sCLASS_PATH ."/threedsecure.php");

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );
/*
	$_SERVER['PHP_AUTH_USER'] = "CPMDemo";
	$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

	$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
	$HTTP_RAW_POST_DATA .= '<root>';
	$HTTP_RAW_POST_DATA .= '<request-3dsecure client-id="10001" account="10022">';
	$HTTP_RAW_POST_DATA .= '<transaction id="1002469">800-123456</transaction>';
	$HTTP_RAW_POST_DATA .= '<challenge content-type="text/html" url="http://acs4.3dsecure.no/mdpayacs/pareq">
	&lt;html lang=&quot;en&quot;&gt;
	&lt;head&gt;
		&lt;title&gt;Autentisering&lt;/title&gt;
		&lt;meta content=&quot;text/html; charset=utf-8&quot; http-equiv=&quot;Content-Type&quot;&gt;
		&lt;link rel=&quot;stylesheet&quot; type=&quot;text/css&quot; href=&quot;content/040/screen.css&quot;&gt;
		&lt;link rel=&quot;stylesheet&quot; type=&quot;text/css&quot; href=&quot;content/040/dk/gh-buttons.css&quot;&gt;
		&lt;script src=&quot;content/commons.js&quot;&gt;&lt;/script&gt;
		&lt;script src=&quot;content/040/js/jquery-1.9.1.min.js&quot;&gt;&lt;/script&gt;
		&lt;script type=&quot;text/javascript&quot;&gt;
	... </challenge>';
	$HTTP_RAW_POST_DATA .= '<client-info platform="iOS" version="1.00" language="da">';
	$HTTP_RAW_POST_DATA .= '<mobile country-id="100" operator-id="10000">28882861</mobile>';
	$HTTP_RAW_POST_DATA .= '<email>jona@oismail.com</email>';
	$HTTP_RAW_POST_DATA .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
	$HTTP_RAW_POST_DATA .= '</client-info>';
	$HTTP_RAW_POST_DATA .= '</request-3dsecure>';
	$HTTP_RAW_POST_DATA .= '</root>';
*/
$obj_DOM = simpledom_load_string(file_get_contents("php://input") );
$aMsgCds = array();
try
{
	if (isset($_SERVER["PHP_AUTH_USER"]) === false || isset($_SERVER["PHP_AUTH_PW"]) === false) { throw new mPointSecurityException(mPointSecurityException::UNAUTHORIZED); }

	// Validate basic information
	$obj_3DSecure = Validate::valRequestFormat($obj_DOM, "mpoint.xsd", "request-3dsecure");

	$code = Validate::valBasic($_OBJ_DB, (integer) $obj_3DSecure["client-id"], (integer) $obj_3DSecure["account"]);
	if ($code != 100) { throw new mPointSecurityException($code); }

	$obj_ClientConfig = ClientConfig::authenticate($_OBJ_DB, (integer) $obj_3DSecure["client-id"], (integer) $obj_3DSecure["account"], $_SERVER["PHP_AUTH_USER"], $_SERVER["PHP_AUTH_PW"], $_SERVER["REMOTE_ADDR"]);

	// Begin validations specific to this controller
	$obj_Validator = new Validate($obj_ClientConfig->getCountryConfig() );

	$iValResult = $obj_Validator->valOrderID($_OBJ_DB, (string) $obj_3DSecure->transaction, (integer) $obj_3DSecure->transaction["id"]);
	if ($iValResult != 10) { $aMsgCds[$iValResult + 20] = "Transaction and Order ID doesn't match. mPoint ID: ". $obj_3DSecure->transaction["id"] ." Order ID: ". $obj_3DSecure->transaction; }
	$iValResult = $obj_Validator->valmPointID($_OBJ_DB, (integer) $obj_3DSecure->transaction["id"], $obj_ClientConfig->getID() );
	if ($iValResult != 3) { $aMsgCds[$iValResult + 30] = "Transaction not in right state. mPoint ID: ". $obj_3DSecure->transaction["id"] ." Client ID: ". $obj_ClientConfig->getID(); }
	$iValResult = $obj_Validator->valChallenge($obj_3DSecure->challenge);
	if ($iValResult != 10) { $aMsgCds[$iValResult + 40] = "Challenge invalid. Content-Type: ". $obj_3DSecure->challenge["content-type"] ." URL: ". $obj_3DSecure->challenge["url"]; }

	// Validation errors have occurred
	if (count($aMsgCds) > 0) { throw new mPointCustomValidationException($aMsgCds); }
	// 3dsecure is not configured for client
	if ( ($obj_ClientConfig->getParse3DSecureChallengeURLConfig() instanceof ClientURLConfig) === false) { throw new mPointSimpleControllerException(HTTP::METHOD_NOT_ALLOWED, 51, "Mobile Optimized 3D secure not configured for client: ". $obj_ClientConfig->getID() );	}

	try
	{
		$obj_TxnInfo = TxnInfo::produceInfo($obj_3DSecure->transaction["id"], $_OBJ_DB);
		$obj_3DSecure = new ThreeDSecure($_OBJ_DB, $_OBJ_TXT, $obj_ClientConfig);
		$obj_HTTP = $obj_3DSecure->parse3DSecureChallenge($obj_TxnInfo, $obj_3DSecure->challenge);

		// Forward external response directly to client
		header(HTTP::getHTTPHeader($obj_HTTP->getReturnCode() ) );
		$xml = $obj_HTTP->getReplyBody();
	}
	catch (mPointException $e)
	{
		trigger_error($e->getMessage(), E_USER_WARNING);
		throw new mPointSimpleControllerException(HTTP::BAD_GATEWAY, $e->getCode(), $e->getMessage(), $e);
	}
	catch (Exception $e)
	{
		trigger_error($e->getMessage(), E_USER_ERROR);
		throw new mPointSimpleControllerException(HTTP::INTERNAL_SERVER_ERROR, $e->getCode(), $e->getMessage(), $e);
	}
}
catch (mPointControllerException $e)
{
	header(HTTP::getHTTPHeader($e->getHTTPCode() ) );
	
	$xml = '<?xml version="1.0" encoding="UTF-8"?>';
	$xml .= '<root>'. $e->getResponseXML() .'</root>';
}

header("Content-Type: text/xml; charset=\"UTF-8\"");
echo $xml;
