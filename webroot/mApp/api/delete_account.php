<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:delete_account.php
 */


// Require Global Include File
require_once("../../inc/include.php");

// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH . "simpledom.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH . "/enduser_account.php");
// Require Business logic for the My Account component
require_once(sCLASS_PATH . "/my_account.php");
// Require data data class for Customer Information
require_once(sCLASS_PATH . "/customer_info.php");
// Require Data Class for Client Information
require_once(sCLASS_PATH . "/clientinfo.php");
// Require Business logic for the validating client Input
require_once(sCLASS_PATH . "/validate.php");


// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH));

/*$_SERVER['PHP_AUTH_USER'] = "CPMDemo";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<delete-account client-id="10007" account="100007">';
//$HTTP_RAW_POST_DATA .= '<euaid>433433</eauid>';
$HTTP_RAW_POST_DATA .= '<password>123456</password>';
//$HTTP_RAW_POST_DATA .= '<auth-token>test1234</auth-token>';
$HTTP_RAW_POST_DATA .= '<client-info platform="iOS" version="1.00" language="da">';
//$HTTP_RAW_POST_DATA .= '<customer-ref>ABC-123</customer-ref>';
$HTTP_RAW_POST_DATA .= '<mobile country-id="200" operator-id="20000">9876543210</mobile>';
$HTTP_RAW_POST_DATA .= '<email>sagar@cellpointmobile.com</email>';
$HTTP_RAW_POST_DATA .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
$HTTP_RAW_POST_DATA .= '</client-info>';
$HTTP_RAW_POST_DATA .= '</delete-account>';
$HTTP_RAW_POST_DATA .= '</root>';*/

$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);
if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true) {
    if (($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH . "mpoint.xsd") === true && count($obj_DOM->{'delete-account'}) > 0) {
        $obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);
        for ($i = 0; $i < count($obj_DOM->{'delete-account'}); $i++) {
            $clientId = (integer)$obj_DOM->{'delete-account'}[$i]["client-id"];
            $accountId = (integer)$obj_DOM->{'delete-account'}[$i]["account"];
            // Validate basic information
            $code = Validate::valBasic($_OBJ_DB, $clientId, $accountId);
            if ($code == 100) {
                $obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, $clientId, $accountId);
                if ($obj_ClientConfig->getUsername() == trim($_SERVER['PHP_AUTH_USER']) && $obj_ClientConfig->getPassword() == trim($_SERVER['PHP_AUTH_PW'])) {
                    $obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer)$obj_DOM->{'delete-account'}[$i]->{'client-info'}->mobile["country-id"]);
                    if (($obj_CountryConfig instanceof CountryConfig) === false || $obj_CountryConfig->getID() <= 0) {
                        $obj_CountryConfig = $obj_ClientConfig->getCountryConfig();
                    }

                    $obj_mPoint = new MyAccount($_OBJ_DB, $_OBJ_TXT, $obj_CountryConfig);
                    $obj_Validator = new Validate($obj_CountryConfig);
                    $aMsgCds = array();
                    $iAccountID = -1;
                    if (count($obj_DOM->{'delete-account'}[$i]->euaid) > 0)
                        $iAccountID = $obj_DOM->{'delete-account'}[$i]->euaid;
                    else
                        $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_CountryConfig, $obj_DOM->{'delete-account'}[$i]->{'client-info'}->{'customer-ref'}, $obj_DOM->{'delete-account'}[$i]->{'client-info'}->mobile, $obj_DOM->{'delete-account'}[$i]->{'client-info'}->email);

                    if($iAccountID != -1){
                    if (strlen((string)$obj_DOM->{'delete-account'}[$i]->password) > 1 && $obj_Validator->valPassword((string)$obj_DOM->{'delete-account'}[$i]->password) != 10) {
                        $aMsgCds[] = $obj_Validator->valPassword((string)$obj_DOM->{'delete-account'}[$i]->password) + 20;
                    }

                    if (count($aMsgCds) == 0) {
                        // Single Sign-On
                        if (count($obj_DOM->{'delete-account'}[$i]->{'auth-token'}) == 1
                            && (count($obj_DOM->{'delete-account'}[$i]->{'auth-url'}) == 1 || strlen($obj_ClientConfig->getAuthenticationURL()) > 0)) {
                            $code = 10;
                        } else {
                            $code = General::authToken($iAccountID, $obj_ClientConfig->getSecret(), $_COOKIE['token']);
                            // Generate new security token
                            if ($code == 11) {
                                setcookie("token", General::genToken($iAccountID, $obj_ClientConfig->getSecret()));
                            }
                        }
                        $code = 10;
                        // Authentication succeeded
                        if ($code >= 10) {
                            // Single Sign-On
                            if (count($obj_DOM->{'delete-account'}[$i]->{'auth-token'}) == 1
                                && (count($obj_DOM->{'delete-account'}[$i]->{'auth-url'}) == 1 || strlen($obj_ClientConfig->getAuthenticationURL()) > 0)) {
                                $url = $obj_ClientConfig->getAuthenticationURL();
                                if (count($obj_DOM->{'delete-account'}[$i]->{'auth-url'}) == 1) {
                                    $url = (string)$obj_DOM->{'delete-account'}[$i]->{'auth-url'};
                                }
                                if ($obj_Validator->valURL($url, $obj_ClientConfig->getAuthenticationURL()) == 10) {
                                    $obj_CustomerInfo = CustomerInfo::produceInfo($_OBJ_DB, $iAccountID);
                                    $obj_Customer = simplexml_load_string($obj_CustomerInfo->toXML());
                                    //for existing accounts
                                    if (empty($obj_Customer["customer-ref"]) === true && count($obj_DOM->{'delete-account'}[$i]->{'client-info'}->{'customer-ref'}) > 0) {
                                        $obj_Customer["customer-ref"] = (string) $obj_DOM->{'delete-account'}[$i]->{'client-info'}->{'customer-ref'};
                                    }

                                    $obj_CustomerInfo = CustomerInfo::produceInfo($obj_Customer);
                                    $code = $obj_mPoint->auth($obj_ClientConfig, $obj_CustomerInfo, trim($obj_DOM->{'delete-account'}[$i]->{'auth-token'}), intval($obj_DOM->{'delete-account'}[$i]["client-id"]));
                                } else {
                                    $code = 8;
                                }
                            } else {
                                $code = $obj_mPoint->auth($iAccountID, (string)$obj_DOM->{'delete-account'}[$i]->password);
                            }

                            if ($code == 10 || ($code == 11 && $obj_ClientConfig->smsReceiptEnabled() === false)) {
                                $_OBJ_DB->query("START TRANSACTION");
                                $obj = $obj_mPoint->delStoredCardAndDisableAccount($iAccountID);
                                $obj_MyAccount = $obj_mPoint;
                                // Success: Stored Card Deleted
                                if ($obj->status == 10) {
                                    // Success: Card saved
                                    if ($code > 0 && $obj_ClientConfig->getNotificationURL() != "") {
                                        $obj_mPoint = new EndUserAccount($_OBJ_DB, $_OBJ_TXT, $obj_ClientConfig);
                                        try {
                                            $obj_ClientInfo = ClientInfo::produceInfo($obj_DOM->{'delete-account'}[$i]->{'client-info'}, $obj_CountryConfig, @$_SERVER['HTTP_X_FORWARDED_FOR']);

                                            $aObj_XML = simplexml_load_string($obj_mPoint->getStoredCards($iAccountID, $obj_ClientConfig, true));
                                            $aObj_XML = $aObj_XML->xpath("/stored-cards/card[client/@id = " . $obj_ClientConfig->getID() . "]");

                                            $aURL_Info = parse_url($obj_ClientConfig->getNotificationURL());
                                            $aHTTP_CONN_INFO["mesb"]["protocol"] = $aURL_Info["scheme"];
                                            $aHTTP_CONN_INFO["mesb"]["host"] = $aURL_Info["host"];
                                            $aHTTP_CONN_INFO["mesb"]["port"] = $aURL_Info["port"];
                                            $aHTTP_CONN_INFO["mesb"]["path"] = $aURL_Info["path"];
                                            if (array_key_exists("query", $aURL_Info) === true) {
                                                $aHTTP_CONN_INFO["mesb"]["path"] .= "?" . $aURL_Info["query"];
                                            }
                                            $obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["mesb"]);

                                            switch ($obj_mPoint->notify($obj_ConnInfo, $obj_ClientInfo, $iAccountID, $obj_DOM->{'delete-account'}[$i]->{'auth-token'}, count($aObj_XML))) {
                                                case (1):    // Error: Unknown response from CRM System
                                                    // Abort transaction and rollback to previous state
                                                    $_OBJ_DB->query("ROLLBACK");
                                                    header("HTTP/1.1 502 Bad Gateway");

                                                    $xml = '<status code="98">Invalid response from CRM System</status>';
                                                    break;
                                                case (2):    // Error: Notification Rejected by CRM System
                                                    // Abort transaction and rollback to previous state
                                                    $_OBJ_DB->query("ROLLBACK");
                                                    header("HTTP/1.1 502 Bad Gateway");

                                                    $xml = '<status code="97">Notification rejected by CRM System</status>';
                                                    break;
                                                case (10):    // Success: Card successfully saved
                                                    // Commit Deleted Card
                                                    $_OBJ_DB->query("COMMIT");

                                                    $xml = '<status code="100">Card successfully deleted and CRM system notified</status>';
                                                    $xml .= '<card-tokens eua-id="'. intval($iAccountID) .'">';
                                                    foreach ($obj->tokens as $token)
                                                        $xml .= '<token>' . $token . '</token>';
                                                    $xml .= '</card-tokens>';
                                                    break;
                                                default:    // Error: Unknown response from CRM System
                                                    // Abort transaction and rollback to previous state
                                                    $_OBJ_DB->query("ROLLBACK");
                                                    header("HTTP/1.1 502 Bad Gateway");

                                                    $xml = '<status code="99">Unknown response from CRM System</status>';
                                                    break;
                                            }
                                        } // Error: Unable to connect to CRM System
                                        catch (HTTPConnectionException $e) {
                                            // Abort transaction and rollback to previous state
                                            $_OBJ_DB->query("ROLLBACK");
                                            header("HTTP/1.1 504 Gateway Timeout");

                                            $xml = '<?xml version="1.0" encoding="UTF-8"?>';
                                            $xml .= '<root>';
                                            $xml .= '<status code="91">Unable to connect to CRM System</status>';
                                            $xml .= '</root>';
                                        } // Error: No response received from CRM System
                                        catch (HTTPSendException $e) {
                                            // Abort transaction and rollback to previous state
                                            $_OBJ_DB->query("ROLLBACK");
                                            header("HTTP/1.1 504 Gateway Timeout");

                                            $xml = '<?xml version="1.0" encoding="UTF-8"?>';
                                            $xml .= '<root>';
                                            $xml .= '<status code="92">No response received from CRM System</status>';
                                            $xml .= '</root>';
                                        }
                                    } // Success: Card successfully saved
                                    else {
                                        // Commit Deleted Card
                                        $_OBJ_DB->query("COMMIT");

                                        $xml = '<status code="100">Card successfully deleted</status>';
                                        $xml .= '<card-tokens eua-id="'. intval($iAccountID) .'">';
                                        foreach ($obj->tokens as $token)
                                            $xml .= '<token>' . $token . '</token>';
                                        $xml .= '</card-tokens>';
                                    }
                                } else if ($obj->status == 1) {
                                    header("HTTP/1.1 403 Forbidden");

                                    $xml = '<status code="51">Cannot Disable account</status>';
                                } elseif ($obj->status == 3) {
                                    header("HTTP/1.1 500 Internal Server Error");

                                    $xml = '<status code="90">Unable to delete card</status>';
                                }
                            } // Authentication succeeded - But Mobile number not verified
                            elseif ($code == 11) {
                                header("HTTP/1.1 403 Forbidden");

                                $xml = '<status code="37">Mobile number not verified</status>';
                            } // Authentication failed
                            else {
                                header("HTTP/1.1 403 Forbidden");

                                $xml = '<status code="' . ($code + 30) . '">Authentication failed</status>';
                            }

                        } // Authentication failed
                        else {
                            header("HTTP/1.1 403 Forbidden");

                            if (strlen((string)$obj_DOM->{'delete-account'}[$i]->{'auth-token'}) > 0 && strlen((string)$obj_DOM->{'delete-account'}[$i]->{'auth-url'}) > 0) {
                                $xml = '<status code="38">Invalid Auth Token: ' . (string)$obj_DOM->{'delete-account'}[$i]->{'auth-token'} . '</status>';
                            } else {
                                $xml = '<status code="38">Invalid Security Token: ' . $_COOKIE['token'] . '</status>';
                            }
                        }

                    } else {
                        header("HTTP/1.1 400 Bad Request");

                        $message = 'Invalid card number';
                        if ($aMsgCds[0] > 20 && $aMsgCds[0] < 25) {
                            $message = 'Invalid Password.';
                        }

                        $xml = '<status code="' . $aMsgCds[0] . '" >' . $message . '</status>';
                    }
                }else {
                        header("HTTP/1.1 401 Unauthorized");

                        $xml = '<status code="401">EndUser acccount not found </status>';
                    }
                } else {
                    header("HTTP/1.1 401 Unauthorized");

                    $xml = '<status code="401">Username / Password doesn\'t match</status>';
                }
            } else {
                header("HTTP/1.1 400 Bad Request");

                $xml = '<status code="' . $code . '">Client ID / Account doesn\'t match</status>';
            }
        }
    }elseif (($obj_DOM instanceof SimpleDOMElement) === false) {
        header("HTTP/1.1 415 Unsupported Media Type");

        $xml = '<status code="415">Invalid XML Document</status>';
    } // Error: Wrong operation
    elseif (count($obj_DOM->{'delete-account'}) == 0) {
        header("HTTP/1.1 400 Bad Request");

        $xml = '';
        foreach ($obj_DOM->children() as $obj_Elem) {
            $xml .= '<status code="400">Wrong operation: ' . $obj_Elem->getName() . '</status>';
        }
    } // Error: Invalid Input
    else {
        header("HTTP/1.1 400 Bad Request");
        $aObj_Errs = libxml_get_errors();

        $xml = '';
        for ($i = 0; $i < count($aObj_Errs); $i++) {
            $xml .= '<status code="400">' . htmlspecialchars($aObj_Errs[$i]->message, ENT_NOQUOTES) . '</status>';
        }
    }

    // $obj_mPoint->newAuditMessage(Constants::iOPERATION_CARD_DELETED, $obj_DOM->{'delete-card'}[0]->{'client-info'}->mobile, $obj_DOM->{'delete-card'}[0]->{'client-info'}->email, $obj_DOM->{'delete-card'}[0]->{'client-info'}->{'customer-ref'}, $obj_XML->status["code"], (string) $obj_XML->status);
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