<?php
/**
 * This files contains the Controller for mPoint's Controller to save order data for set of transactions
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
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH . "simpledom.php");

// Require specific Business logic for the Capture component

/*
 $_SERVER['PHP_AUTH_USER'] = "CPMDemo";
 $_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

 $HTTP_RAW_POST_DATA = 'Unable to update Transaction';
 */


// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH));

$obj_DOM = simpledom_load_string(file_get_contents("php://input"));

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true) {

    if (($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH . "mpoint.xsd") === true && count($obj_DOM->{'save-order-data'}) > 0) {
        $obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, (integer)$obj_DOM->{'save-order-data'}["client-id"]);
        // Client successfully authenticated
        if ($obj_ClientConfig->getUsername() == trim($_SERVER['PHP_AUTH_USER']) && $obj_ClientConfig->getPassword() == trim($_SERVER['PHP_AUTH_PW'])) {
            $xml = '<save-order-data-response>';
            for ($i = 0, $iMax = count($obj_DOM->{'save-order-data'}->transactions->transaction); $i < $iMax; $i++) {
                try {
                    try {
                        $sToken = '';
                        if (isset($obj_DOM->{'save-order-data'}->transactions->transaction[$i]['id']) === true) {
                            $sToken = $obj_DOM->{'save-order-data'}->transactions->transaction[$i]['id'];
                            $obj_TxnInfo = TxnInfo::produceInfo(intval($obj_DOM->{'save-order-data'}->transactions->transaction[$i]['id']), $_OBJ_DB);
                        } else if (isset($obj_DOM->{'save-order-data'}->transactions->transaction[$i]['order-no']) === true) {
                            $sToken = $obj_DOM->{'save-order-data'}->transactions->transaction[$i]['order-no'];
                            $obj_TxnInfo = TxnInfo::produceInfoFromOrderNoAndMerchant($_OBJ_DB, $obj_DOM->{'save-order-data'}->transactions->transaction[$i]['order-no']);
                        }
                        if (empty($obj_TxnInfo) === false) {

                            if (count($obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders) === 1 && count($obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->children()) > 0) {
                                $aResponse = array();
                                for ($j = 0, $jMax = count($obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}); $j < $jMax; $j++) {
                                    if (count($obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}) > 0) {

                                        if ($obj_TxnInfo->getCurrencyConfig()->getID() !== (int)$obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->amount['currency-id']) {
                                            throw new mPointException("Currency mismatch for token : " . $sToken . " expected " . $obj_TxnInfo->getCurrencyConfig()->getID(), 999);
                                        }
                                        $data['orders'] = array();
                                        $data['orders'][0]['product-sku'] = (string)$obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product["sku"];
                                        $data['orders'][0]['orderref'] = (string)$obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->{'additional-data'}->xpath("./param[@name='TDNR']");
                                        $data['orders'][0]['product-name'] = (string)$obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->name;
                                        $data['orders'][0]['product-description'] = (string)$obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->description;
                                        $data['orders'][0]['product-image-url'] = (string)$obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'image-url'};
                                        $data['orders'][0]['amount'] = (float)$obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->amount;
                                        $collectiveFees = 0;
                                        if (count($obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->fees->fee) > 0) {
                                            for ($k = 0; $k < count($obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->fees->fee); $k++) {
                                                $collectiveFees += $obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->fees->fee[$k];
                                            }
                                        }
                                        $data['orders'][0]['fees'] = (float)$collectiveFees;
                                        $data['orders'][0]['country-id'] = $obj_TxnInfo->getCountryConfig()->getID();
                                        $data['orders'][0]['points'] = (float)$obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->points;
                                        $data['orders'][0]['reward'] = (float)$obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->reward;
                                        $data['orders'][0]['quantity'] = (float)$obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->quantity;

                                        if (isset($obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->{'additional-data'})) {
                                            for ($k = 0, $kMax = count($obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->{'additional-data'}->children()); $k < $kMax; $k++) {
                                                $data['orders'][0]['additionaldata'][$k]['name'] = (string)$obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->{'additional-data'}->param[$k]['name'];
                                                $data['orders'][0]['additionaldata'][$k]['value'] = (string)$obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->{'additional-data'}->param[$k];
                                                $data['orders'][0]['additionaldata'][$k]['type'] = (string)'Order';
                                            }
                                        }
                                        $order_id = $obj_TxnInfo->setOrderDetails($_OBJ_DB, $data['orders']);
                                    }

                                    if (count($obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}) > 0) {
                                        for ($k = 0, $kMax = count($obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}); $k < $kMax; $k++) {
                                            $data['flights'] = array();
                                            $data['additional'] = array();
                                            $data['flights']['service_class'] = (string)$obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]->{'service-class'};
                                            $data['flights']['departure_airport'] = (string)$obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]->{'departure-airport'};
                                            $data['flights']['arrival_airport'] = (string)$obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]->{'arrival-airport'};
                                            $data['flights']['airline_code'] = (string)$obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]->{'airline-code'};
                                            $data['flights']['arrival_date'] = (string)date('Y-m-d H:i:s', strtotime($obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]->{'arrival-date'}));
                                            $data['flights']['departure_date'] = (string)$obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]->{'departure-date'};
                                            $data['flights']['order_id'] = $order_id;
                                            $data['flights']['tag'] = (string)$obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]['tag'];
                                            $data['flights']['trip_count'] = (string)$obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]['trip-count'];
                                            $data['flights']['service_level'] = (string)$obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]['service-level'];
                                            if (count($obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]->{'flight-number'}) > 0) {
                                                $data['flights']['flight_number'] = (string)$obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]->{'flight-number'};
                                            }


                                            if (count($obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]->{'additional-data'}) > 0) {
                                                for ($l = 0; $l < count($obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]->{'additional-data'}->children()); $l++) {
                                                    $data['additional'][$l]['name'] = (string)$obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]->{'additional-data'}->param[$l]['name'];
                                                    $data['additional'][$l]['value'] = (string)$obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]->{'additional-data'}->param[$l];
                                                    $data['additional'][$l]['type'] = (string)"Flight";
                                                }
                                            } else {
                                                $data['additional'] = array();
                                            }

                                            $flight = $obj_TxnInfo->setFlightDetails($_OBJ_DB, $data['flights'], $data['additional']);
                                        }
                                    }

                                    if (count($obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'passenger-detail'}) > 0) {
                                        for ($k = 0, $kMax = count($obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'passenger-detail'}); $k < $kMax; $k++) {
                                            $data['passenger'] = array();
                                            $data['additionalp'] = array();
                                            $data['passenger']['first_name'] = (string)$obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'passenger-detail'}[$k]->{'first-name'};
                                            $data['passenger']['last_name'] = (string)$obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'passenger-detail'}[$k]->{'last-name'};
                                            $data['passenger']['type'] = (string)$obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'passenger-detail'}[$k]->{'type'};
                                            $data['passenger']['order_id'] = $order_id;
                                            $data['passenger']['title'] = (string)$obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'passenger-detail'}[$k]->{'title'};
                                            $data['passenger']['email'] = (string)$obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'passenger-detail'}[$k]->{'contact-info'}->email;
                                            $data['passenger']['mobile'] = (string)$obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'passenger-detail'}[$k]->{'contact-info'}->mobile;
                                            $data['passenger']['country_id'] = (string)$obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'passenger-detail'}[$k]->{'contact-info'}->mobile["country-id"];

                                            if (count($obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'passenger-detail'}[$k]->{'additional-data'}) > 0) {
                                                for ($l = 0; $l < count($obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'passenger-detail'}[$k]->{'additional-data'}->children()); $l++) {
                                                    $data['additionalp'][$l]['name'] = (string)$obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'passenger-detail'}[$k]->{'additional-data'}->param[$l]['name'];
                                                    $data['additionalp'][$l]['value'] = (string)$obj_DOM->{'save-order-data'}->transactions->transaction[$i]->orders->{'line-item'}[$j]->product->{'airline-data'}->{'passenger-detail'}[$k]->{'additional-data'}->param[$l];
                                                    $data['additionalp'][$l]['type'] = (string)"Passenger";
                                                }
                                            } else {
                                                $data['additionalp'] = array();
                                            }
                                            $passenger = $obj_TxnInfo->setPassengerDetails($_OBJ_DB, $data['passenger'], $data['additionalp']);
                                        }
                                    }

                                }
                                $xml .= '<status id = "' . $sToken . '" code = "100">OK</status>';
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
                    $xml .= '<status id = "' . $sToken . '" code = "' . $e->getCode() . '">' . $e->getMessage() . '</status>';
                }
            }
            $xml .= '</save-order-data-response>';
        } else {
            header("HTTP/1.1 401 Unauthorized");

            $xml = '<status code="401">Username / Password doesn\'t match</status>';
        }
    } elseif (($obj_DOM instanceof SimpleDOMElement) === false) {
        header("HTTP/1.1 415 Unsupported Media Type");

        $xml = '<status code="415">Invalid XML Document</status>';
    } // Error: Wrong operation
    elseif (count($obj_DOM->{'save-order-data'}) == 0) {
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

header("HTTP/1.0 200 OK");
header("Content-Type: text/xml; charset=\"UTF-8\"");
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<root>';
echo $xml;
echo '</root>';

?>