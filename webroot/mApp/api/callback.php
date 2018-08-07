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
// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
// Require Processor Class for providing all Payment specific functionality.
require_once(sCLASS_PATH ."/payment_processor.php");
// Require specific Business logic for the CPM PSP component
require_once(sINTERFACE_PATH ."/cpm_psp.php");
// Require specific Business logic for the CPM ACQUIRER component
require_once(sINTERFACE_PATH ."/cpm_acquirer.php");
// Require specific Business logic for the CPM GATEWAY component
require_once(sINTERFACE_PATH ."/cpm_gateway.php");
// Require specific Business logic for the DIBS component
require_once(sCLASS_PATH ."/dibs.php");
// Require general Business logic for the Cellpoint Mobile module
require_once(sCLASS_PATH ."/cpm.php");
// Require specific Business logic for the WannaFind component
require_once(sCLASS_PATH ."/wannafind.php");
// Require specific Business logic for the NetAxept component
require_once(sCLASS_PATH ."/netaxept.php");
// Require specific Business logic for the WorldPay component
require_once(sCLASS_PATH ."/worldpay.php");
// Require specific Business logic for the Emirates' Corporate Payment Gateway (CPG) component
require_once(sCLASS_PATH ."/cpg.php");
// Require specific Business logic for the DSB PSP component
require_once(sCLASS_PATH ."/dsb.php");
// Require specific Business logic for the VISA checkout component
require_once(sCLASS_PATH ."/visacheckout.php");
if (function_exists("json_encode") === true && function_exists("curl_init") === true)
{
    // Require specific Business logic for the Stripe component
    require_once(sCLASS_PATH ."/stripe.php");
}
// Require specific Business logic for the Adyen component
require_once(sCLASS_PATH ."/adyen.php");
// Require specific Business logic for the Apple Pay component
require_once(sCLASS_PATH ."/applepay.php");
// Require specific Business logic for the Data Cash component
require_once(sCLASS_PATH ."/datacash.php");
// Require specific Business logic for the Master Pass component
require_once(sCLASS_PATH ."/masterpass.php");
// Require specific Business logic for the AMEX Express Checkout component
require_once(sCLASS_PATH ."/amexexpresscheckout.php");
// Require specific Business logic for the WireCard component
require_once(sCLASS_PATH ."/wirecard.php");
// Require specific Business logic for the Global Collect component
require_once(sCLASS_PATH ."/globalcollect.php");
// Require specific Business logic for the Android Pay component
require_once(sCLASS_PATH ."/androidpay.php");
// Require specific Business logic for the Secure Trading component
require_once(sCLASS_PATH ."/securetrading.php");
// Require specific Business logic for the PayFort component
require_once(sCLASS_PATH ."/payfort.php");
// Require specific Business logic for the PayPal component
require_once(sCLASS_PATH ."/paypal.php");
// Require specific Business logic for the CCAvenue component
require_once(sCLASS_PATH ."/ccavenue.php");
// Require specific Business logic for the 2C2P component
require_once(sCLASS_PATH ."/ccpp.php");
// Require specific Business logic for the MayBank component
require_once(sCLASS_PATH ."/maybank.php");
// Require specific Business logic for the PublicBank component
require_once(sCLASS_PATH ."/publicbank.php");
// Require specific Business logic for the MobilePay Online component
require_once(sCLASS_PATH ."/mobilepayonline.php");
// Require specific Business logic for the Klarna Online component
require_once(sCLASS_PATH ."/klarna.php");
// Require specific Business logic for the Nets component
require_once(sCLASS_PATH ."/nets.php");
// Require specific Business logic for the mVault component
require_once(sCLASS_PATH ."/mvault.php");
// Require specific Business logic for the 2c2p alc component
require_once(sCLASS_PATH ."/ccpp_alc.php");
// Require specific Business logic for the Google Pay component
require_once(sCLASS_PATH ."/googlepay.php");

// Require specific Business logic for the PPro component
require_once(sCLASS_PATH ."/ppro.php");
// Require specific Business logic for the Amex component
require_once(sCLASS_PATH ."/amex.php");
// Require specific Business logic for the CHUBB component
require_once(sCLASS_PATH ."/chubb.php");
// Require specific Business logic for the CHUBB component
require_once(sCLASS_PATH ."/payment_processor.php");
// Require specific Business logic for the UATP component
require_once(sCLASS_PATH . "/uatp.php");


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

try
{
    $obj_TxnInfo = TxnInfo::produceInfo($obj_DOM->{'transaction-reference'}, $_OBJ_DB);
	try
	{
		if(empty($obj_TxnInfo) === false)
        {
            $obj_PSP = PaymentProcessor::produceConfig($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, intval($obj_TxnInfo->getPSPID() ), $aHTTP_CONN_INFO);
            $obj_Elem = $obj_DOM->{'request-body'}->request;
            $code = $obj_PSP->processCallback($obj_Elem);
        }
	}
	catch (mPointException $e)
	{
		trigger_error($e, E_USER_WARNING);
		throw new mPointSimpleControllerException(HTTP::BAD_GATEWAY, $e->getCode(), $e->getMessage(), $e);
	}
	catch (Exception $e)
	{
		trigger_error($e, E_USER_ERROR);
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
