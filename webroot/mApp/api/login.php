<?php
/**
 * This files contains the Controller for mPoint's Mobile Web API.
 * The Controller will ensure that all input from a Mobile Internet Site or Mobile Application is validated and a new payment transaction is started.
 * Finally, assuming the Client Input is valid, the Controller will redirect the Customer to one of the following flow start pages:
 * 	- Payment Flow: /pay/card.php
 * 	- Shop Flow: /shop/delivery.php
 * If the input provided was determined to be invalid, an error page will be generated.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package API
 * @subpackage MobileApp
 * @version 1.10
 */

// Require Global Include File
require_once("../../inc/include.php");

// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");
// Require the PHP API for handling the connection to GoMobile
require_once(sAPI_CLASS_PATH ."/gomobile.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require data data class for Customer Information
require_once(sCLASS_PATH ."/customer_info.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");

$aMsgCds = array();

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );
/*
$_SERVER['PHP_AUTH_USER'] = "CPMDemo";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<login client-id="100">';
$HTTP_RAW_POST_DATA .= '<password>oisJona</password>';
//$HTTP_RAW_POST_DATA .= '<auth-token>test1234</auth-token>';
//$HTTP_RAW_POST_DATA .= '<auth-url>http://mpoint.test.cellpointmobile.com/_test/auth.php</auth-url>';
$HTTP_RAW_POST_DATA .= '<client-info platform="iOS" version="1.00" language="da">';
$HTTP_RAW_POST_DATA .= '<customer-ref>ABC-123</customer-ref>';
$HTTP_RAW_POST_DATA .= '<mobile country-id="100" operator-id="10000">28882861</mobile>';
$HTTP_RAW_POST_DATA .= '<email>jona@oismail.com</email>';
$HTTP_RAW_POST_DATA .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
$HTTP_RAW_POST_DATA .= '</client-info>';
$HTTP_RAW_POST_DATA .= '</login>';
$HTTP_RAW_POST_DATA .= '</root>';
*/
$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mpoint.xsd") === true && count($obj_DOM->login) > 0)
	{
		$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);
		$obj_Validator = new Validate();
		$xml = '';

		for ($i=0; $i<count($obj_DOM->login); $i++)
		{
			// Set Global Defaults
			if (empty($obj_DOM->login[$i]["account"]) === true || intval($obj_DOM->login[$i]["account"]) < 1) { $obj_DOM->login[$i]["account"] = -1; }

			// Validate basic information
			$code = Validate::valBasic($_OBJ_DB, (integer) $obj_DOM->login[$i]["client-id"], (integer) $obj_DOM->login[$i]["account"]);
			if ($code == 100)
			{
				$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->login[$i]["client-id"], (integer) $obj_DOM->login[$i]["account"]);

				// Client successfully authenticated
				if ($obj_ClientConfig->getUsername() == trim($_SERVER['PHP_AUTH_USER']) && $obj_ClientConfig->getPassword() == trim($_SERVER['PHP_AUTH_PW']) )
				{
					$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->login[$i]->{'client-info'}->mobile["country-id"]);
					if ( ($obj_CountryConfig instanceof CountryConfig) === false) { $obj_CountryConfig = $obj_ClientConfig->getCountryConfig(); }

					$obj_mPoint = new Home($_OBJ_DB, $_OBJ_TXT, $obj_ClientConfig->getCountryConfig() );
					if ( strlen((string) $obj_DOM->login[$i]->password) > 1 && $obj_Validator->valPassword( (string) $obj_DOM->login[$i]->password) < 10)
					{
						$aMsgCds["password"] = $obj_Validator->valPassword( (string) $obj_DOM->login[$i]->password) + 20;
					}

					// Input valid
					if (count($aMsgCds) == 0)
					{
						$iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_CountryConfig, $obj_DOM->login[$i]->{'client-info'}->{'customer-ref'}, $obj_DOM->login[$i]->{'client-info'}->mobile, $obj_DOM->login[$i]->{'client-info'}->email);

						if ($iAccountID > -1)
						{
                            if (count($obj_DOM->login[$i]->{'auth-token'}) == 1
                                && (count($obj_DOM->login[$i]->{'auth-url'}) == 1 || strlen($obj_ClientConfig->getAuthenticationURL()) > 0)
                            ) {
                                $url = $obj_ClientConfig->getAuthenticationURL();
                                if (count($obj_DOM->login[$i]->{'auth-url'}) == 1) {
                                    $url = (string)$obj_DOM->login[$i]->{'auth-url'};
                                }
                                if ($obj_Validator->valURL($url, $obj_ClientConfig->getAuthenticationURL()) == 10) {
                                    $code = $obj_mPoint->auth(HTTPConnInfo::produceConnInfo($url), CustomerInfo::produceInfo($_OBJ_DB, $iAccountID), trim($obj_DOM->login[$i]->{'auth-token'}), intval($obj_DOM->login[$i]["client-id"]));
                                } else {
                                    $code = 8;
                                }
                            } else {
                                $code = $obj_mPoint->auth($iAccountID, (string)$obj_DOM->login[$i]->password);
                            }

                            // Authentication succeeded
                            if ($code == 10 || ($code == 11 && $obj_ClientConfig->smsReceiptEnabled() === false)) {
                                $xml .= $obj_mPoint->getAccountInfo($iAccountID);
                                $aObj_XML = simplexml_load_string($obj_mPoint->getStoredCards($iAccountID, $obj_ClientConfig));
                                if ($obj_ClientConfig->getStoreCard() <= 3) {
                                    $aObj_XML = $aObj_XML->xpath("/stored-cards/card[client/@id = " . $obj_ClientConfig->getID() . "]");
                                } else {
                                    $aObj_XML = $aObj_XML->xpath("/stored-cards/card");
                                }
                                // End-User has Stored Cards available
                                if (is_array($aObj_XML) === true && count($aObj_XML) > 0) {
                                    $xml .= '<stored-cards>';
                                    for ($j = 0; $j < count($aObj_XML); $j++) {
                                        $xml .= '<card id="' . $aObj_XML[$j]["id"] . '" type-id="' . $aObj_XML[$j]->type["id"] . '" psp-id="' . $aObj_XML[$j]["pspid"] . '" preferred="' . $aObj_XML[$j]["preferred"] . '" charge-type-id="' . $aObj_XML[$j]["charge-type-id"] . '">';
                                        if (strlen($aObj_XML[$j]->name) > 0) {
                                            $xml .= $aObj_XML[$j]->name->asXML();
                                        }
                                        $xml .= '<card-number-mask>' . $aObj_XML[$j]->mask . '</card-number-mask>';
                                        $xml .= $aObj_XML[$j]->expiry->asXML();
                                        $xml .= '<token>' . htmlspecialchars((string)$aObj_XML[$j]->ticket, ENT_NOQUOTES) . '</token>';
                                        if (strlen($aObj_XML[$j]->{'card-holder-name'}) > 0) {
                                            $xml .= $aObj_XML[$j]->{'card-holder-name'}->asXML();
                                        }
                                        if (count($aObj_XML[$j]->address) == 1) {
                                            $xml .= $aObj_XML[$j]->address->asXML();
                                        }
                                        $xml .= '</card>';
                                    }
                                    $xml .= '</stored-cards>';
                                } else {
                                    $xml .= '<stored-cards />';
                                }
                                if ($obj_ClientConfig->getStoreCard() == 2 || $obj_ClientConfig->getStoreCard() == 4) {
                                    // Return last 5 transactions of each type
                                    $aTypes = array(Constants::iTOPUP_OF_EMONEY . " or @type-id = " . Constants::iTOPUP_OF_POINTS, Constants::iREWARD_OF_POINTS, Constants::iTRANSFER_OF_EMONEY);
                                    $obj_XML = simplexml_load_string($obj_mPoint->getTxnHistory($iAccountID, -1));
                                    $xml .= '<history account-id="' . $obj_XML["account-id"] . '">';
                                    foreach ($aTypes as $id) {
                                        $aObj_XML = $obj_XML->xpath("/history/transaction[@type-id = " . $id . "]");
                                        if (is_array($aObj_XML) === true && count($aObj_XML) > 0) {
                                            for ($j = 0; $j < count($aObj_XML) && $j < 5; $j++) {
                                                $xml .= $aObj_XML[$j]->asXML();
                                            }
                                        }
                                    }
                                    // Return last 5 purchase transactions
                                    $aObj_XML = $obj_XML->xpath("/history/transaction[@type-id != " . Constants::iTOPUP_OF_EMONEY . " and @type-id != " . Constants::iTOPUP_OF_POINTS . " and @type-id != " . Constants::iREWARD_OF_POINTS . " and @type-id != " . Constants::iTRANSFER_OF_EMONEY . "]");
                                    if (is_array($aObj_XML) === true && count($aObj_XML) > 0) {
                                        for ($j = 0; $j < count($aObj_XML) && $j < 5; $j++) {
                                            $xml .= $aObj_XML[$j]->asXML();
                                        }
                                    }
                                    $xml .= '</history>';
                                }
                                setcookie("token", General::genToken($iAccountID, $obj_ClientConfig->getSecret()));
                            } // Authentication succeeded - But Mobile number not verified
                            elseif ($code == 11) {
                                header("HTTP/1.1 403 Forbidden");

                                $xml = '<status code="37">Mobile number not verified</status>';
                            } // Authentication failed
                            else {
                                // Account disabled due to too many failed login attempts
                                if ($code == 3) {
                                    // Re-Intialise Text Translation Object based on transaction
                                    $_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH . $obj_DOM->login[$i]->{'client-info'}["language"] . "/global.txt", sLANGUAGE_PATH . $obj_DOM->login[$i]->{'client-info'}["language"] . "/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");
                                    $obj_mPoint = new EndUserAccount($_OBJ_DB, $_OBJ_TXT, $obj_ClientConfig);
                                    $obj_mPoint->sendAccountDisabledNotification(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO), $obj_DOM->login[$i]->{'client-info'}->mobile);
                                }

                                header("HTTP/1.1 403 Forbidden");

                                $xml = '<status code="' . ($code + 30) . '" />';
                            }
                        }
                        //Account not found
                        {
                            header("HTTP/1.1 403 Forbidden");
                            $xml = '<status code="35">Account not found</status>';
                        }
					}
					// Error in Input
					else
					{
						header("HTTP/1.1 400 Bad Request");

						foreach ($aMsgCds as $code)
						{
							$xml .= '<status code="'. $code .'" />';
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
				header("HTTP/1.1 400 Bad Request");

				$xml = '<status code="'. $code .'">Client ID / Account doesn\'t match</status>';
			}
		}
	}
	// Error: Invalid XML Document
	elseif ( ($obj_DOM instanceof SimpleDOMElement) === false)
	{
		header("HTTP/1.1 415 Unsupported Media Type");

		$xml = '<status code="415">Invalid XML Document</status>';
	}
	// Error: Wrong operation
	elseif (count($obj_DOM->login) == 0)
	{
		header("HTTP/1.1 400 Bad Request");

		$xml = '';
		foreach ($obj_DOM->children() as $obj_Elem)
		{
			$xml .= '<status code="400">Wrong operation: '. $obj_Elem->getName() .'</status>';
		}
	}
	// Error: Invalid Input
	else
	{
		header("HTTP/1.1 400 Bad Request");
		$aObj_Errs = libxml_get_errors();

		$xml = '';
		for ($i=0; $i<count($aObj_Errs); $i++)
		{
			$xml .= '<status code="400">'. htmlspecialchars($aObj_Errs[$i]->message, ENT_NOQUOTES) .'</status>';
		}
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

$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);
$obj_XML = simpledom_load_string('<root>'. $xml .'</root>');
if (count($obj_XML->status) > 0)
{
	$obj_mPoint->newAuditMessage(Constants::iOPERATION_LOGGED_IN, $obj_DOM->login[0]->{'client-info'}->mobile, $obj_DOM->login[0]->{'client-info'}->email, $obj_DOM->login[0]->{'client-info'}->{'customer-ref'}, intval($obj_XML->status["code"]), (string) $obj_XML->status);
}
else
{
	$obj_mPoint->newAuditMessage(Constants::iOPERATION_LOGGED_IN, $obj_DOM->login[0]->{'client-info'}->mobile, $obj_DOM->login[0]->{'client-info'}->email, $obj_DOM->login[0]->{'client-info'}->{'customer-ref'}, 100, 'Successful login');
}
?>