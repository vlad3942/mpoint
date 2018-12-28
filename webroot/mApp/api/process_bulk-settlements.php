<?php
/**
 * This files contains the Controller for mPoint's Bulk Capture API.
 *
 * @author Rohit Malhotra
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package API
 * @subpackage Capture
 * @version 1.11
 */

// Require Global Include File
require_once("../../inc/include.php");
// Require specific Business logic for the Capture component
require_once(sCLASS_PATH . "/capture.php");
// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH . "/enduser_account.php");
// Require general Business logic for the Callback module
require_once(sCLASS_PATH . "/callback.php");
// Require specific Business logic for the CPM PSP component
require_once(sINTERFACE_PATH . "/cpm_psp.php");
// Require specific Business logic for the CPM ACQUIRER component
require_once(sINTERFACE_PATH . "/cpm_acquirer.php");
// Require specific Business logic for the CPM GATEWAY component
require_once(sINTERFACE_PATH . "/cpm_gateway.php");
// Require specific Business logic for the DIBS component
require_once(sCLASS_PATH . "/dibs.php");
// Require specific Business logic for the WorldPay component
require_once(sCLASS_PATH . "/worldpay.php");
// Require specific Business logic for the Netaxept component
require_once(sCLASS_PATH . "/netaxept.php");
// Require specific Business logic for the WannaFind component
require_once(sCLASS_PATH . "/wannafind.php");
// Require specific Business logic for the DSB PSP component
require_once(sCLASS_PATH . "/dsb.php");
if (function_exists("json_encode") === true && function_exists("curl_init") === true) {
    // Require specific Business logic for the Stripe component
    require_once(sCLASS_PATH . "/stripe.php");
}
// Require specific Business logic for the MobilePay component
require_once(sCLASS_PATH . "/mobilepay.php");
// Require specific Business logic for the Adyen component
require_once(sCLASS_PATH . "/adyen.php");
// Require specific Business logic for the VISA checkout component
require_once(sCLASS_PATH . "/visacheckout.php");
// Require specific Business logic for the Data Cash component
require_once(sCLASS_PATH . "/datacash.php");
// Require specific Business logic for the Master Pass component
require_once(sCLASS_PATH . "/masterpass.php");
// Require specific Business logic for the AMEX Express Checkout component
require_once(sCLASS_PATH . "/amexexpresscheckout.php");
// Require specific Business logic for the WireCard component
require_once(sCLASS_PATH . "/wirecard.php");
// Require specific Business logic for the Global Collect component
require_once(sCLASS_PATH . "/globalcollect.php");
// Require specific Business logic for the Secure Trading component
require_once(sCLASS_PATH . "/securetrading.php");
// Require specific Business logic for the PayFort component
require_once(sCLASS_PATH . "/payfort.php");
// Require specific Business logic for the PayPal component
require_once(sCLASS_PATH . "/paypal.php");
// Require specific Business logic for the CCAvenue component
require_once(sCLASS_PATH . "/ccavenue.php");
// Require specific Business logic for the 2C2P component
require_once(sCLASS_PATH . "/ccpp.php");
// Require specific Business logic for the MayBank component
require_once(sCLASS_PATH . "/maybank.php");
// Require specific Business logic for the PublicBank component
require_once(sCLASS_PATH . "/publicbank.php");
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH . "simpledom.php");
// Require Business logic for General Administration of mPoint
require_once(sCLASS_PATH . "admin.php");
// Require specific Business logic for the Nets component
require_once(sCLASS_PATH . "/nets.php");
// Require Business logic for the mConsole Module
require_once(sCLASS_PATH . "/mConsole.php");
// Require Business logic for the validating client Input
require_once(sCLASS_PATH . "/validate.php");
// Require specific Business logic for the 2C2P ALC component
require_once(sCLASS_PATH . "/ccpp_alc.php");
// Require data data class for Customer Information
require_once(sCLASS_PATH . "/customer_info.php");
// Require specific Business logic for the chase component
require_once(sCLASS_PATH . "/chase.php");
// Require Processor Class for providing all Payment specific functionality.
require_once(sCLASS_PATH . "/payment_processor.php");

//header("Content-Type: application/x-www-form-urlencoded");

/*
 $_SERVER['PHP_AUTH_USER'] = "CPMDemo";
 $_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

 $HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>
<root>
	<bulk-capture client-id = "10007">
		<transactions>
			<transaction token = "165400018291651">
				<amount country-id="100">10000</amount>
			</transaction>
			<transaction  order-no="UAT-28577880" token = "165400018291651">
				<amount country-id="100">10000</amount>
			</transaction>
		</transactions>
	</bulk-capture>
</root>';
 */


// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH));

$obj_DOM = simpledom_load_string(file_get_contents("php://input"));

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
    $obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'bulk-capture'}["client-id"]);
    // Client successfully authenticated
    if ($obj_ClientConfig->getUsername() == trim($_SERVER['PHP_AUTH_USER']) && $obj_ClientConfig->getPassword() == trim($_SERVER['PHP_AUTH_PW']) ) {
        if (($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH . "mpoint.xsd") === true && count($obj_DOM->{'bulk-capture'}) > 0) {
            $xml = '<bulk-capture-response>';
            for ($i = 0; $i < count($obj_DOM->{'bulk-capture'}->transactions->transaction); $i++) {
                try {
                    try {
                        if (isset($obj_DOM->{'bulk-capture'}->transactions->transaction[$i]['id']) === true) {
                            $obj_TxnInfo = TxnInfo::produceInfo(intval($obj_DOM->{'bulk-capture'}->transactions->transaction[$i]['id']), $_OBJ_DB);
                        } else if (isset($obj_DOM->{'bulk-capture'}->transactions->transaction[$i]['order-no']) === true) {
                            $obj_TxnInfo = TxnInfo::produceInfoFromOrderNoAndMerchant($_OBJ_DB, $obj_DOM->{'bulk-capture'}->transactions->transaction[$i]['order-no']);
                        } else if (isset($obj_DOM->{'bulk-capture'}->transactions->transaction[$i]['token']) === true) {
                            $obj_TxnInfo = TxnInfo::produceTxnInfoFromToken($_OBJ_DB, $obj_DOM->{'bulk-capture'}->transactions->transaction[$i]['token']);
                        }

                        if (empty($obj_TxnInfo) === false) {
                            $obj_PSP = PaymentProcessor::produceConfig($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, intval($obj_TxnInfo->getPSPID()), $aHTTP_CONN_INFO);
                            $iAmount = 0;
                            for ($j = 0; $j < count($obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->amount); $j++) {
                                if($obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->amount[$j]['type'] == 'DB')
                                {
                                    $iAmount += intval($obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->amount[$j]);
                                }
                            }
                            $code = $obj_PSP->capture($iAmount);

                            $xml .= '<status id = "' . $obj_TxnInfo->getID() . '" code = "' . $code . '" />';
                        }

                    } catch (mPointException $e) {
                        trigger_error($e, E_USER_WARNING);
                        throw new mPointSimpleControllerException(HTTP::BAD_GATEWAY, $e->getCode(), $e->getMessage(), $e);
                    } catch (Exception $e) {
                        trigger_error($e, E_USER_ERROR);
                        throw new mPointSimpleControllerException(HTTP::INTERNAL_SERVER_ERROR, $e->getCode(), $e->getMessage(), $e);
                    }
                } catch (mPointControllerException $e) {
                    header(HTTP::getHTTPHeader($e->getHTTPCode()));
                    $xml .= '<status code = "' . $e->getCode() . '">' . $e->getMessage() . '</status>';
                }
            }
            $xml .= '</bulk-capture-response>';
        } elseif (($obj_DOM instanceof SimpleDOMElement) === false) {
            header("HTTP/1.1 415 Unsupported Media Type");

            $xml = '<status code="415">Invalid XML Document</status>';
        } // Error: Wrong operation
        elseif (count($obj_DOM->{'bulk-capture'}) == 0) {
            header("HTTP/1.1 400 Bad Request");

            $xml = '';
            foreach ($obj_DOM->children() as $obj_Elem) {
                $xml = '<status code="400">Wrong operation: ' . $obj_Elem->getName() . '</status>';
            }
        } // Error: Invalid Input
        else {
            header("HTTP/1.1 400 Bad Request");
            $aObj_Errs = libxml_get_errors();

            $xml = '';
            for ($i = 0; $i < count($aObj_Errs); $i++) {
                $xml = '<status code="400">' . htmlspecialchars($aObj_Errs[$i]->message, ENT_NOQUOTES) . '</status>';
            }
        }
    }
    else
    {
        header("HTTP/1.1 401 Unauthorized");

        $xml = '<status code="401">Username / Password doesn\'t match</status>';
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

?>