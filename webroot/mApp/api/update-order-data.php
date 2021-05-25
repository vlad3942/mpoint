<?php
/**
 * This files contains the Controller for mPoint's update-order-data API.
 * The Controller will ensure that all input from the client is validated prior to performing the capture.
 * Finally, assuming the Client Input is valid, the Controller will update order data.
 *
 * @author Kalpesh Parikh
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package API
 * @subpackage Capture
 * @version 1.11
 */

// Require Global Include File
require_once("../../inc/include.php");
// Require specific Business logic for the Capture component
require_once(sCLASS_PATH ."/capture.php");
// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for the CPM PSP component
require_once(sINTERFACE_PATH ."/cpm_psp.php");
// Require specific Business logic for the CPM ACQUIRER component
require_once(sINTERFACE_PATH ."/cpm_acquirer.php");
// Require specific Business logic for the CPM GATEWAY component
require_once(sINTERFACE_PATH ."/cpm_gateway.php");
// Require specific Business logic for the DIBS component
require_once(sCLASS_PATH ."/dibs.php");
// Require specific Business logic for the WorldPay component
require_once(sCLASS_PATH ."/worldpay.php");
// Require specific Business logic for the Netaxept component
require_once(sCLASS_PATH ."/netaxept.php");
// Require specific Business logic for the WannaFind component
require_once(sCLASS_PATH ."/wannafind.php");
// Require specific Business logic for the DSB PSP component
require_once(sCLASS_PATH ."/dsb.php");
if (function_exists("json_encode") === true && function_exists("curl_init") === true)
{
    // Require specific Business logic for the Stripe component
    require_once(sCLASS_PATH ."/stripe.php");
}
// Require specific Business logic for the MobilePay component
require_once(sCLASS_PATH ."/mobilepay.php");
// Require specific Business logic for the Adyen component
require_once(sCLASS_PATH ."/adyen.php");
// Require specific Business logic for the VISA checkout component
require_once(sCLASS_PATH ."/visacheckout.php");
// Require specific Business logic for the Data Cash component
require_once(sCLASS_PATH ."/datacash.php");
// Require specific Business logic for the Mada Mpgs component
require_once(sCLASS_PATH ."/mada_mpgs.php");
// Require specific Business logic for the Master Pass component
require_once(sCLASS_PATH ."/masterpass.php");
// Require specific Business logic for the AMEX Express Checkout component
require_once(sCLASS_PATH ."/amexexpresscheckout.php");
// Require specific Business logic for the WireCard component
require_once(sCLASS_PATH ."/wirecard.php");
// Require specific Business logic for the Global Collect component
require_once(sCLASS_PATH ."/globalcollect.php");
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
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");
// Require Business logic for General Administration of mPoint
require_once(sCLASS_PATH ."admin.php");
// Require specific Business logic for the Nets component
require_once(sCLASS_PATH ."/nets.php");
// Require Business logic for the mConsole Module
require_once(sCLASS_PATH ."/mConsole.php");
// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
// Require specific Business logic for the 2C2P ALC component
require_once(sCLASS_PATH ."/ccpp_alc.php");
// Require data data class for Customer Information
require_once(sCLASS_PATH ."/customer_info.php");
// Require specific Business logic for the chase component
require_once(sCLASS_PATH ."/chase.php");
// Require specific Business logic for the global payments component
require_once(sCLASS_PATH ."/global-payments.php");
// Require specific Business logic for the cybs component
require_once(sCLASS_PATH ."/cybersource.php");
// Require specific Business logic for the Cielo component
require_once(sCLASS_PATH ."/cielo.php");
// Require specific Business logic for the VeriTrans4G component
require_once(sCLASS_PATH ."/psp/veritrans4g.php");
// Require specific Business logic for the DragonPay component
require_once(sCLASS_PATH ."/aggregator/dragonpay.php");
// Require specific Business logic for the SWISH component
require_once(sCLASS_PATH ."/apm/swish.php");
require_once(sCLASS_PATH . '/txn_passbook.php');
require_once(sCLASS_PATH . '/passbookentry.php');
// Require specific Business logic for the Grab Pay component
require_once(sCLASS_PATH ."/grabpay.php");
// Require specific Business logic for the Paymaya component
require_once(sCLASS_PATH .'/apm/paymaya.php');
// Require specific Business logic for the CEBU Payment Center component
require_once(sCLASS_PATH .'/apm/CebuPaymentCenter.php');
//header("Content-Type: application/x-www-form-urlencoded");
// Require specific Business logic for the MPGS
require_once(sCLASS_PATH ."/MPGS.php");
// Require specific Business logic for the Paymaya-Acq component
require_once(sCLASS_PATH ."/Paymaya_Acq.php");

/*
 $HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>
<root>
    <update-order-data>
        <transaction id="12345" order-no="FIU9YA">
            <orders>
                <line-item>
                    <product order-ref="ABC1234" sku="product-ticket">
                        <type>100</type>
                        <name>ONE WAY</name>
                        <description>MNL-CEB</description>
                        <airline-data>
                            <profiles>
                                <profile>
                                    <seq>1</seq>
                                    <title>Mr</title>
                                    <first-name>dan</first-name>
                                    <last-name>dan</last-name>
                                    <type>ADT</type>
                                    <contact-info>
                                        <email>dan@dan.com</email>
                                        <mobile country-id="640">9187231231</mobile>
                                    </contact-info>
                                    <additional-data>
                                        <param name="loyality_id">345rtyu</param>
                                    </additional-data>
                                </profile>
                            </profiles>
                            <billing-summary>
                                <fare-detail>
                                    <fare>
                                        <type>1</type>
                                        <description>adult</description>
                                        <currency>PHP</currency>
                                        <amount>60</amount>
                                        <product-code>ABF</product-code>
                                        <product-category>FARE</product-category>
                                        <product-item>Base fare for adult</product-item>
                                    </fare>
                                </fare-detail>
                            </billing-summary>
                            <trips>
                                <trip tag="1" seq="1">
                                    <origin external-id="MNL" country-id="640" time-zone="+08:00" terminal="1">Ninoy Aquino International Airport</origin>
                                    <destination external-id="CEB" country-id="640" time-zone="+08:00" terminal="2">Mactan Cebu International Airport</destination>
                                    <departure-time>2021-03-07T19:35:00Z</departure-time>
                                    <arrival-time>2021-03-07T21:05:00Z</arrival-time>
                                    <booking-class>Z</booking-class>
                                    <service-level>Economy</service-level>
                                    <transportation code="5J" number="1">
                                        <carriers>
                                            <carrier code="5J" type-id="Aircraft Boeing-737-9">
                                                <number>563</number>
                                            </carrier>
                                        </carriers>
                                    </transportation>
                                    <additional-data>
                                        <param name="fare_basis">we543s3</param>
                                    </additional-data>
                                </trip>
                            </trips>
                        </airline-data>
                    </product>
                    <amount>125056</amount>
                    <quantity>1</quantity>
                    <additional-data>
                        <param name="key">value</param>
                    </additional-data>
                </line-item>
                <line-item>
                    <product order-ref="ABC1237" sku="product-ticket">
                        <type>200</type>
                        <name>ONE WAY</name>
                        <description>MNL-CEB</description>
                        <airline-data>
                            <profiles>
                                <profile>
                                    <seq>1</seq>
                                    <title>Mr</title>
                                    <first-name>dan</first-name>
                                    <last-name>dan</last-name>
                                    <type>ADT</type>
                                    <contact-info>
                                        <email>dan@dan.com</email>
                                        <mobile country-id="640">9187231231</mobile>
                                    </contact-info>
                                    <additional-data>
                                        <param name="loyality_id">345rtyu</param>
                                    </additional-data>
                                </profile>
                            </profiles>
                            <billing-summary>
                                <add-ons>
                                    <add-on>
                                        <profile-seq>1</profile-seq>
                                        <trip-tag>2</trip-tag>
                                        <trip-seq>2</trip-seq>
                                        <description>adult</description>
                                        <currency>PHP</currency>
                                        <amount>60</amount>
                                        <product-code>ABF</product-code>
                                        <product-category>FARE</product-category>
                                        <product-item>Base fare for adult</product-item>
                                    </add-on>
                                </add-ons>
                            </billing-summary>
                            <trips>
                                <trip tag="1" seq="1">
                                    <origin external-id="MNL" country-id="640" time-zone="+08:00" terminal="1">Ninoy Aquino International Airport</origin>
                                    <destination external-id="CEB" country-id="640" time-zone="+08:00" terminal="2">Mactan Cebu International Airport</destination>
                                    <departure-time>2021-03-07T19:35:00Z</departure-time>
                                    <arrival-time>2021-03-07T21:05:00Z</arrival-time>
                                    <booking-class>Z</booking-class>
                                    <service-level>Economy</service-level>
                                    <transportation code="5J" number="1">
                                        <carriers>
                                            <carrier code="5J" type-id="Aircraft Boeing-737-9">
                                                <number>563</number>
                                            </carrier>
                                        </carriers>
                                    </transportation>
                                    <additional-data>
                                        <param name="fare_basis">we543s3</param>
                                    </additional-data>
                                </trip>
                            </trips>
                        </airline-data>
                    </product>
                    <amount>125056</amount>
                    <quantity>1</quantity>
                    <additional-data>
                        <param name="key">value</param>
                    </additional-data>
                </line-item>
            </orders>
        </transaction>
    </initialize-payment>
</root>';
 */

header("HTTP/1.0 200 OK");

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH));

$obj_DOM = simpledom_load_string(file_get_contents("php://input"));
$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true) {

    if (($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH . "mpoint.xsd") === true && count($obj_DOM->{'update-order-data'}) > 0) {
        $obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, (integer)$obj_DOM->{'update-order-data'}["client-id"]);
        $_OBJ_DB->query("START TRANSACTION");
        try {
                $transaction = $obj_DOM->{'update-order-data'}->transaction;

                $obj_TxnInfo = TxnInfo::produceInfo(intval($transaction['id']), $_OBJ_DB);
                $obj_ClientConfig = $obj_TxnInfo->getClientConfig();
                $obj_CountryConfig = $obj_ClientConfig->getCountryConfig();
                echo trim($_SERVER['PHP_AUTH_USER']);
                if ($obj_ClientConfig->getUsername() == trim($_SERVER['PHP_AUTH_USER']) && $obj_ClientConfig->getPassword() == trim($_SERVER['PHP_AUTH_PW'])) {
                    $obj_DOMOrder = $transaction->orders;
                    if (!$obj_mPoint->saveOrderDetails($_OBJ_DB, $obj_TxnInfo, $obj_CountryConfig, $obj_DOMOrder)) {
                        throw new mPointSimpleControllerException(200, 99, "Operation Failed");
                    }
                } else {
                    $_OBJ_DB->query("ROLLBACK");
                    header("HTTP/1.0 401 Unauthorized");
                    throw new mPointSimpleControllerException(401, 401, "Username / Password doesn't match");
                }

                $_OBJ_DB->query("COMMIT");
            $xml = '<status code = "100">Operation Success</status>';
        } catch (mPointControllerException $e) {
            $_OBJ_DB->query("ROLLBACK");
            $xml = '<status code = "' . $e->getCode() . '">' . $e->getMessage() . '</status>';
        } catch (mPointException $e) {
            $_OBJ_DB->query("ROLLBACK");
            $xml = '<status code = "' . $e->getCode() . '">' . $e->getMessage() . '</status>';
        } catch (Exception $e) {
            $_OBJ_DB->query("ROLLBACK");
            $xml = '<status code = "' . $e->getCode() . '">' . $e->getMessage() . '</status>';
        }
    } elseif (($obj_DOM instanceof SimpleDOMElement) === false) {
        header("HTTP/1.1 415 Unsupported Media Type");

        $xml = '<status code="415">Invalid XML Document</status>';
    } // Error: Wrong operation
    elseif (count($obj_DOM->{'update-order-data'}) == 0) {
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
        for ($i = 0, $iMax = count($aObj_Errs); $i < $iMax; $i++) {
            $xml = '<status code="400">' . htmlspecialchars($aObj_Errs[$i]->message, ENT_NOQUOTES) . '</status>';
        }
    }
} else {
    header("HTTP/1.1 401 Unauthorized");

    $xml = '<status code="401">Authorization required</status>';
}

header("Content-Type: text/xml; charset=\"UTF-8\"");
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<root>';
echo $xml;
echo '</root>';

?>