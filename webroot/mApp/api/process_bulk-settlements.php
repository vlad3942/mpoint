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
// Require specific Business logic for the UATP Rails component
require_once(sCLASS_PATH . "/uatp_card_account.php");
// Require Processor Class for providing all Payment specific functionality.
require_once(sCLASS_PATH . "/payment_processor.php");
// Require specific Business logic for the Mada Mpgs component
require_once(sCLASS_PATH ."/mada_mpgs.php");
// Require specific Business logic for the Cielo component
require_once(sCLASS_PATH ."/cielo.php");

//header("Content-Type: application/x-www-form-urlencoded");

/*
 $_SERVER['PHP_AUTH_USER'] = "CPMDemo";
 $_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

 $HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>
<?xml version="1.0" encoding="UTF-8"?>
<root>
	<bulk-capture client-id="10007">
		<transactions>
			<transaction token="165400018651748">
				<orders>
					<line-item>
						<amount country-id="200" type="DB">20000</amount>
						<product sku="12345678920   ">
							<airline-data>
								<flight-detail>
									<departure-date />
									<additional-data>
										<param name="TDNR">35412345678920</param>
										<param name="CCAC">165400018651748</param>
										<param name="CINN">3543201811</param>
										<param name="SQNR">00000016</param>
										<param name="FPAM">00000000020000</param>
										<param name="CUTP">USD2</param>
										<param name="DBCR">DB</param>
									</additional-data>
								</flight-detail>
								<passenger-detail>
									<title />
									<first-name>Mejra</first-name>
									<last-name>Causevic</last-name>
								</passenger-detail>
							</airline-data>
						</product>
					</line-item>
				</orders>
			</transaction>
			<transaction token="165400018653587">
				<orders>
					<line-item>
						<amount country-id="200" type="DB">500000</amount>
						<product sku="35420180509A  ">
							<airline-data>
								<flight-detail>
									<departure-date />
									<additional-data>
										<param name="TDNR">35412345678900</param>
										<param name="CCAC">165400018653587</param>
										<param name="CINN">3543201811</param>
										<param name="SQNR">00000017</param>
										<param name="FPAM">00000000500000</param>
										<param name="CUTP">USD2</param>
										<param name="DBCR">DB</param>
									</additional-data>
								</flight-detail>
								<passenger-detail>
									<title />
									<first-name>Jack</first-name>
									<last-name>Frieh</last-name>
								</passenger-detail>
							</airline-data>
						</product>
					</line-item>
				</orders>
			</transaction>
		</transactions>
	</bulk-capture>
</root>';
 */


// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH));

$obj_DOM = simpledom_load_string(file_get_contents("php://input"));

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true) {

    if (($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH . "mpoint.xsd") === true && count($obj_DOM->{'bulk-capture'}) > 0) {
        $obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, (integer)$obj_DOM->{'bulk-capture'}["client-id"]);
        // Client successfully authenticated
        if ($obj_ClientConfig->getUsername() == trim($_SERVER['PHP_AUTH_USER']) && $obj_ClientConfig->getPassword() == trim($_SERVER['PHP_AUTH_PW'])) {
            $xml = '<bulk-capture-response>';
            for ($i = 0, $iMax = count($obj_DOM->{'bulk-capture'}->transactions->transaction); $i < $iMax; $i++) {
                try {
                    try {
                        $sToken = '';
                        if (isset($obj_DOM->{'bulk-capture'}->transactions->transaction[$i]['id']) === true) {
                            $sToken = $obj_DOM->{'bulk-capture'}->transactions->transaction[$i]['id'];
                            $obj_TxnInfo = TxnInfo::produceInfo(intval($obj_DOM->{'bulk-capture'}->transactions->transaction[$i]['id']), $_OBJ_DB);
                        } else if (isset($obj_DOM->{'bulk-capture'}->transactions->transaction[$i]['order-no']) === true) {
                            $sToken = $obj_DOM->{'bulk-capture'}->transactions->transaction[$i]['order-no'];
                            $obj_TxnInfo = TxnInfo::produceInfoFromOrderNoAndMerchant($_OBJ_DB, $obj_DOM->{'bulk-capture'}->transactions->transaction[$i]['order-no']);
                        } else if (isset($obj_DOM->{'bulk-capture'}->transactions->transaction[$i]['token']) === true) {
                            $sToken = $obj_DOM->{'bulk-capture'}->transactions->transaction[$i]['token'];
                            $obj_TxnInfo = TxnInfo::produceTxnInfoFromExternalRef($_OBJ_DB, $obj_DOM->{'bulk-capture'}->transactions->transaction[$i]['token']);
                        }

                        $isAIDAlreadyUpdated = false;
                        if($obj_TxnInfo->hasEitherState($_OBJ_DB, array(Constants::iPAYMENT_CAPTURE_INITIATED_STATE,Constants::iPAYMENT_CANCELLED_STATE,Constants::iPAYMENT_CAPTURED_STATE, Constants::iPAYMENT_REFUNDED_STATE, Constants::iPAYMENT_REFUND_INITIATED_STATE)))
                        {
                            $isAIDAlreadyUpdated=  true;
                        }

                        if (empty($obj_TxnInfo) === false) {
                            $obj_PSP = PaymentProcessor::produceConfig($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, intval($obj_TxnInfo->getPSPID()), $aHTTP_CONN_INFO);
                            $iAmount = 0;
                            $iDBAmount = 0;
                            $iCRAmount = 0;
                            if (count($obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders) === 1 && count($obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->children()) > 0) {
                                for ($j = 0, $jMax = count($obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}); $j < $jMax; $j++) {
                                    if (count($obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}) > 0) {
                                        if($isAIDAlreadyUpdated === false) {
                                            $data['orders'] = array();
                                            $data['orders'][0]['product-sku'] = (string)$obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product["sku"];
                                            $data['orders'][0]['product-name'] = (string)$obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->name;
                                            $data['orders'][0]['product-description'] = (string)$obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->description;
                                            $data['orders'][0]['product-image-url'] = (string)$obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'image-url'};
                                            $data['orders'][0]['amount'] = (float)$obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->amount;
                                            $data['orders'][0]['country-id'] = $obj_TxnInfo->getCountryConfig()->getID();
                                            $data['orders'][0]['points'] = (float)$obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->points;
                                            $data['orders'][0]['reward'] = (float)$obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->reward;
                                            $data['orders'][0]['quantity'] = (float)$obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->quantity;

                                             if (isset($obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->{'additional-data'})) {
                                                for ($k = 0, $kMax = count($obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->{'additional-data'}->children()); $k < $kMax; $k++) {
                                                    $data['orders'][0]['additionaldata'][$k]['name'] = (string)$obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->{'additional-data'}->param[$k]['name'];
                                                    $data['orders'][0]['additionaldata'][$k]['value'] = (string)$obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->{'additional-data'}->param[$k];
                                                    $data['orders'][0]['additionaldata'][$k]['type'] = (string)'Order';
                                                }
                                             }
                                             $order_id = $obj_TxnInfo->setOrderDetails($_OBJ_DB, $data['orders']);
                                        }
                                        if ($obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->amount['type'] == 'DB') {
                                                $iDBAmount += intval($obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->amount);
                                                $iAmount += intval($obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->amount);
                                            } elseif ($obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->amount['type'] == 'CR') {
                                                $iCRAmount += intval($obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->amount);
                                                $iAmount += intval($obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->amount);
                                            }
                                    }
                                    if ($isAIDAlreadyUpdated === false &&  count($obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}) > 0) {
                                        for ($k = 0, $kMax = count($obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}); $k < $kMax; $k++) {
                                            $data['flights'] = array();
                                            $data['additional'] = array();
                                            $data['flights']['service_class'] = (string)$obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]->{'service-class'};
                                            $data['flights']['departure_airport'] = (string)$obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]->{'departure-airport'};
                                            $data['flights']['arrival_airport'] = (string)$obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]->{'arrival-airport'};
                                            $data['flights']['airline_code'] = (string)$obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]->{'airline-code'};
                                            $data['flights']['arrival_date'] = (string)date('Y-m-d H:i:s', strtotime($obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]->{'arrival-date'}));
                                            $data['flights']['departure_date'] = (string)date('Y-m-d H:i:s', strtotime($obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]->{'departure-date'}));
                                            $data['flights']['order_id'] = $order_id;
                                            $data['flights']['tag'] = (string)$obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]['tag'];
                                            $data['flights']['trip_count'] = (string)$obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]['trip-count'];
                                            $data['flights']['service_level'] = (string)$obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]['service-level'];
                                            if(count($obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]->{'flight-number'}) > 0)
                                            {
                                                $data['flights']['flight_number'] = (string)$obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]->{'flight-number'};
                                            }


                                            if (count($obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]->{'additional-data'}) > 0) {
                                                for ($l = 0; $l < count($obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]->{'additional-data'}->children()); $l++) {
                                                    $data['additional'][$l]['name'] = (string)$obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]->{'additional-data'}->param[$l]['name'];
                                                    $data['additional'][$l]['value'] = (string)$obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]->{'additional-data'}->param[$l];
                                                    $data['additional'][$l]['type'] = (string)"Flight";
                                                }
                                            } else {
                                                $data['additional'] = array();
                                            }

                                            $flight = $obj_TxnInfo->setFlightDetails($_OBJ_DB, $data['flights'], $data['additional']);
                                        }
                                    }

                                    if ($isAIDAlreadyUpdated === false && count($obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'passenger-detail'}) > 0) {
                                        for ($k = 0, $kMax = count($obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'passenger-detail'}); $k < $kMax; $k++) {
                                            $data['passenger'] = array();
                                            $data['additionalp'] = array();
                                            $data['passenger']['first_name'] = (string)$obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'passenger-detail'}[$k]->{'first-name'};
                                            $data['passenger']['last_name'] = (string)$obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'passenger-detail'}[$k]->{'last-name'};
                                            $data['passenger']['type'] = (string)$obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'passenger-detail'}[$k]->{'type'};
                                            $data['passenger']['order_id'] = $order_id;
                                            $data['passenger']['title'] = (string)$obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'passenger-detail'}[$k]->{'title'};
                                            $data['passenger']['email'] = (string)$obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'passenger-detail'}[$k]->{'contact-info'}->email;
                                            $data['passenger']['mobile'] = (string)$obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'passenger-detail'}[$k]->{'contact-info'}->mobile;
                                            $data['passenger']['country_id'] = (string)$obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'passenger-detail'}[$k]->{'contact-info'}->mobile["country-id"];

                                            if (count($obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'passenger-detail'}[$k]->{'additional-data'}) > 0) {
                                                for ($l = 0; $l < count($obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'passenger-detail'}[$k]->{'additional-data'}->children()); $l++) {
                                                    $data['additionalp'][$l]['name'] = (string)$obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'passenger-detail'}[$k]->{'additional-data'}->param[$l]['name'];
                                                    $data['additionalp'][$l]['value'] = (string)$obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'passenger-detail'}[$k]->{'additional-data'}->param[$l];
                                                    $data['additionalp'][$l]['type'] = (string)"Passenger";
                                                }
                                            } else {
                                                $data['additionalp'] = array();
                                            }
                                            $passenger = $obj_TxnInfo->setPassengerDetails($_OBJ_DB, $data['passenger'], $data['additionalp']);
                                        }
                                    }

                                }
                            }
                            $sMessage = '';
                            //AS Discussion with UATP for now scope is full capture and cancel
                            if(((int)$obj_TxnInfo->getAmount() === (int)$iDBAmount) && ((int)$iDBAmount === (int)$iCRAmount)) {

                                $code = $obj_PSP->refund($iCRAmount);
                                $sMessage = "PSP returned code ".$code;
                            }
                            else if((int)$obj_TxnInfo->getAmount() === (int)$iDBAmount) {

                                if ($obj_TxnInfo->hasEitherState($_OBJ_DB, array(Constants::iPAYMENT_CAPTURE_INITIATED_STATE)) === false) {
                                    $code = $obj_PSP->capture($iDBAmount);
                                }

                                $sMessage = "PSP returned code ".$code;
                            }

                            elseif ((int)$obj_TxnInfo->getAmount() === (int)$iCRAmount )
                            {
                                $code = $obj_PSP->refund($iCRAmount);
                                $sMessage = "PSP returned code ".$code;
                            }
                            else
                            {
                                $sMessage = 'Amount mismatch';
                            }

                            if (intval($code) == 1000)
                            {
                                $xml .= '<status id = "' . $sToken . '" code = "' . $code . '" >Settlement Initialized, '.$sMessage
                                    . $obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->asXML()
                                    . $obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->amount->asXML()
                                    . '</status>';
                            }
                            else
                            {
                                $xml .= '<status id = "' . $sToken . '" code = "999" >Settlement Failed, '.$sMessage
                                    . $obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->asXML()
                                    . $obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->amount->asXML()
                                    . '</status>';
                            }
                        }

                    } catch (mPointException $e) {
                        trigger_error($e, E_USER_WARNING);
                        throw new mPointSimpleControllerException(HTTP::BAD_GATEWAY, $e->getCode(), $e->getMessage(), $e);
                    } catch (Exception $e) {
                        trigger_error($e, E_USER_ERROR);
                        throw new mPointSimpleControllerException(HTTP::INTERNAL_SERVER_ERROR, $e->getCode(), $e->getMessage(), $e);
                    }
                } catch (mPointControllerException $e) {
                    $xml .= '<status id = "' . $sToken . '" code = "' . $e->getCode() . '">' . $e->getMessage()
                        . $obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->orders->asXML()
                        . $obj_DOM->{'bulk-capture'}->transactions->transaction[$i]->amount->asXML()
                        . '</status>';
                }
            }
            $xml .= '</bulk-capture-response>';
        }
        else
        {
            header("HTTP/1.1 401 Unauthorized");

            $xml = '<status code="401">Username / Password doesn\'t match</status>';
        }
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
        for ($i = 0, $iMax = count($aObj_Errs); $i < $iMax; $i++) {
            $xml = '<status code="400">' . htmlspecialchars($aObj_Errs[$i]->message, ENT_NOQUOTES) . '</status>';
        }
    }
}
else
{
    header("HTTP/1.1 401 Unauthorized");

    $xml = '<status code="401">Authorization required</status>';
}

header("HTTP/1.0 200 OK");
header("Content-Type: text/xml; charset=\"UTF-8\"");
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<root>';
echo $xml;
echo '</root>';

?>