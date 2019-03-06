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

// Require the PHP API for handling the connection to GoMobile
require_once(sAPI_CLASS_PATH ."/gomobile.php");
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require Data Class for Client Information
require_once(sCLASS_PATH ."/clientinfo.php");
// Require data data class for Customer Information
require_once(sCLASS_PATH ."/customer_info.php");

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );
/*
$_SERVER['PHP_AUTH_USER'] = "CPMDemo";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<save-account client-id="10007" account="100006">';
$HTTP_RAW_POST_DATA .= '<password>oisJona</password>';
$HTTP_RAW_POST_DATA .= '<confirm-password>oisJona</confirm-password>';
$HTTP_RAW_POST_DATA .= '<social-security-number>3008990017</social-security-number>';
$HTTP_RAW_POST_DATA .= '<full-name>Jonatan Evald Buus</full-name>';
$HTTP_RAW_POST_DATA .= '<card type-id="2">My Card</card>';
$HTTP_RAW_POST_DATA .= '<client-info platform="iOS" version="1.00" language="da">';
$HTTP_RAW_POST_DATA .= '<mobile country-id="610" operator-id="61000">3138544000</mobile>';
$HTTP_RAW_POST_DATA .= '<email>asd@as.com</email>';
$HTTP_RAW_POST_DATA .= '<device-id>85ce3843c0a068fb5cb1e76156fdd719</device-id>';
$HTTP_RAW_POST_DATA .= '</client-info>';
$HTTP_RAW_POST_DATA .= '</save-account>';
$HTTP_RAW_POST_DATA .= '</root>';
*/
$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mpoint.xsd") === true && count($obj_DOM->{'save-account'}) > 0)
	{
		$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);

		for ($i=0; $i<count($obj_DOM->{'save-account'}); $i++)
		{
			// Set Global Defaults
			if (empty($obj_DOM->{'save-account'}[$i]["account"]) === true || intval($obj_DOM->{'save-account'}[$i]["account"]) < 1) { $obj_DOM->{'save-account'}[$i]["account"] = -1; }

			// Validate basic information
			$code = Validate::valBasic($_OBJ_DB, (integer) $obj_DOM->{'save-account'}[$i]["client-id"], (integer) $obj_DOM->{'save-account'}[$i]["account"]);
			if ($code == 100)
			{
				$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'save-account'}[$i]["client-id"], (integer) $obj_DOM->{'save-account'}[$i]["account"]);

				// Client successfully authenticated
				if ($obj_ClientConfig->getUsername() == trim($_SERVER['PHP_AUTH_USER']) && $obj_ClientConfig->getPassword() == trim($_SERVER['PHP_AUTH_PW']) )
				{
					// Re-Intialise Text Translation Object based on transaction
					$_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH . $obj_DOM->{'save-account'}[$i]->{'client-info'}["language"] ."/global.txt", sLANGUAGE_PATH . $obj_DOM->{'save-account'}[$i]->{'client-info'}["language"] ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");

					$obj_mPoint = new EndUserAccount($_OBJ_DB, $_OBJ_TXT, $obj_ClientConfig);
					$obj_Validator = new Validate($obj_ClientConfig->getCountryConfig() );
					$aMsgCds = array();

					//validate password if present in the request
                    if ( count($obj_DOM->{'save-account'}[$i]->password) == 1)
                    {
                        if ($obj_Validator->valPassword((string)$obj_DOM->{'save-account'}[$i]->password) != 10) {
                            $aMsgCds[] = $obj_Validator->valPassword((string)$obj_DOM->{'save-account'}[$i]->password) + 20;
                        }
                        if ($obj_Validator->valPassword((string)$obj_DOM->{'save-account'}[$i]->{'confirm-password'}) != 10) {
                            $aMsgCds[] = $obj_Validator->valPassword((string)$obj_DOM->{'save-account'}[$i]->{'confirm-password'}) + 30;
                        }
                        if (count($aMsgCds) == 0 && strval($obj_DOM->{'save-account'}[$i]->password) != strval($obj_DOM->{'save-account'}[$i]->{'confirm-password'})) {
                            $aMsgCds[] = 41;
                        }
                    }
                    if (count($obj_DOM->{'save-account'}[$i]->card) == 1 ) {
                        $name = "";
                        if (count($obj_DOM->{'save-account'}[$i]->{'card'}->name) > 0)
                            $name = (string)$obj_DOM->{'save-account'}[$i]->{'card'}->name;
                        else
                            $name = (string)$obj_DOM->{'save-account'}[$i]->card;
                        if($obj_Validator->valName($name) != 10)
                        $aMsgCds[] = $obj_Validator->valName($name) + 50;
                    }

					// Seperate Full Name into First- and Last Name
					if (count($obj_DOM->{'save-account'}[$i]->{'full-name'}) == 1)
					{
						$obj_DOM->{'save-account'}[$i]->{'full-name'} = trim($obj_DOM->{'save-account'}[$i]->{'full-name'});
						$pos = strrpos($obj_DOM->{'save-account'}[$i]->{'full-name'}, " ");
						if ($pos === false) { $pos = strlen($obj_DOM->{'save-account'}[$i]->{'full-name'}); }
						else { $obj_DOM->{'save-account'}[$i]->{'last-name'} = substr($obj_DOM->{'save-account'}[$i]->{'full-name'}, $pos + 1); }
						$obj_DOM->{'save-account'}[$i]->{'first-name'} = substr($obj_DOM->{'save-account'}[$i]->{'full-name'}, 0 , $pos);
					}
					// Validate First Name
                    $chkFirstName = 10;
					if (count($obj_DOM->{'save-account'}[$i]->{'first-name'}) == 1)
					{
                        $chkFirstName = $obj_Validator->valCardFullname( (string) $obj_DOM->{'save-account'}[$i]->{'first-name'});
					}
					// Validate Last Name
                    $chkLastName = 10;
					if (count($obj_DOM->{'save-account'}[$i]->{'last-name'}) == 1)
					{
                        $chkLastName = $obj_Validator->valCardFullname( (string) $obj_DOM->{'save-account'}[$i]->{'last-name'});
					}

                    if($chkFirstName != 10 || $chkLastName != 10){
                        $aMsgCds[] = 62;
                    }

					// Success: Input Valid
					if (count($aMsgCds) == 0) {
                        if (count($obj_DOM->{'save-account'}[$i]->{'client-info'}->mobile) == 1)
                        {
                            $obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer)$obj_DOM->{'save-account'}[$i]->{'client-info'}->mobile["country-id"]);
                        } else {
                            $obj_CountryConfig = $obj_ClientConfig->getCountryConfig();
                        }
						// Construct Client Info
						$obj_ClientInfo = ClientInfo::produceInfo($obj_DOM->{'save-account'}[$i]->{'client-info'}, $obj_CountryConfig, @$_SERVER['HTTP_X_FORWARDED_FOR']);

						//check if account already exists, and auth-token is present
                        $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_CountryConfig, $obj_DOM->{'save-account'}[$i]->{'client-info'}->{'customer-ref'}, $obj_DOM->{'save-account'}[$i]->{'client-info'}->mobile, $obj_DOM->{'save-account'}[$i]->{'client-info'}->email);

                        if($iAccountID < 0) {
                            //account needs to be enabled
                            $result = EndUserAccount::enableAccountID($_OBJ_DB, $obj_ClientConfig, $obj_CountryConfig, $obj_DOM->{'save-account'}[$i]->{'client-info'}->{'customer-ref'}, $obj_DOM->{'save-account'}[$i]->{'client-info'}->mobile, $obj_DOM->{'save-account'}[$i]->{'client-info'}->email);
                            if($result === true){
                                $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_CountryConfig, $obj_DOM->{'save-account'}[$i]->{'client-info'}->{'customer-ref'}, $obj_DOM->{'save-account'}[$i]->{'client-info'}->mobile, $obj_DOM->{'save-account'}[$i]->{'client-info'}->email);
                            }
                        }


                        if ($iAccountID > -1) {
                            if (count($obj_DOM->{'save-account'}[$i]->{'auth-token'}) == 1
                                && (count($obj_DOM->{'save-account'}[$i]->{'auth-url'}) == 1 || strlen($obj_ClientConfig->getAuthenticationURL()) > 0)
                            ) {
                                $url = $obj_ClientConfig->getAuthenticationURL();
                                if (count($obj_DOM->{'save-account'}[$i]->{'auth-url'}) == 1) {
                                    $url = (string)$obj_DOM->{'save-account'}[$i]->{'auth-url'};
                                }
                                if ($obj_Validator->valURL($url, $obj_ClientConfig->getAuthenticationURL()) == 10) {
                                    $obj_CustomerInfo = CustomerInfo::produceInfo($_OBJ_DB, $iAccountID);
                                    $obj_Customer = simplexml_load_string($obj_CustomerInfo->toXML());
                                    //for existing accounts
                                    if (empty($obj_Customer["customer-ref"]) === true && count($obj_DOM->{'save-account'}[$i]->{'client-info'}->{'customer-ref'}) > 0) {
                                        $obj_Customer["customer-ref"] = (string) $obj_DOM->{'save-account'}[$i]->{'client-info'}->{'customer-ref'};
                                    }

                                    $obj_CustomerInfo = CustomerInfo::produceInfo($obj_Customer);
                                    $code = $obj_mPoint->auth($obj_ClientConfig, $obj_CustomerInfo, trim($obj_DOM->{'save-account'}[$i]->{'auth-token'}), intval($obj_DOM->{'save-account'}[$i]["client-id"]));
                                } else {
                                    $code = -1;
                                }
                            }
                        }

                        if ($code > 0)
                        {
                            //update or create new account
                            $code = $obj_mPoint->savePassword((float)$obj_DOM->{'save-account'}[$i]->{'client-info'}->mobile, (string)$obj_DOM->{'save-account'}[$i]->password, $obj_CountryConfig);
                            //get the account id if new account was created
                            if($iAccountID < 0)
                            {
                                $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_CountryConfig, $obj_DOM->{'save-account'}[$i]->{'client-info'}->{'customer-ref'}, $obj_DOM->{'save-account'}[$i]->{'client-info'}->mobile, $obj_DOM->{'save-account'}[$i]->{'client-info'}->email);
                            }
                        }
						// New Account automatically created when Password was saved
						if ($code == 1 && $obj_ClientConfig->smsReceiptEnabled() === true)
						{
//							$obj_mPoint->sendAccountInfo(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO), $_SESSION['obj_TxnInfo']);
						}


                        if (count($obj_DOM->{'save-account'}[$i]->card) == 1 && count($obj_DOM->{'save-account'}[$i]->{'card'}->name) > 0)
                        {
                            $obj_mPoint->saveCardName($iAccountID, $obj_DOM->{'save-account'}[$i]->card["type-id"], (string)$obj_DOM->{'save-account'}[$i]->{'card'}->name, true);
                        }
                        else
                        {
                            $obj_mPoint->saveCardName($iAccountID, $obj_DOM->{'save-account'}[$i]->card["type-id"], (string)$obj_DOM->{'save-account'}[$i]->card, true);
                        }

						// Success: Account Information Saved
						if ($code >= 0)
						{
							if (count($obj_DOM->{'save-account'}[$i]->{'first-name'}) == 1 || count($obj_DOM->{'save-account'}[$i]->{'last-name'}) == 1)
							{
								$obj_mPoint->saveInfo($iAccountID, (string) $obj_DOM->{'save-account'}[$i]->{'first-name'}, (string) $obj_DOM->{'save-account'}[$i]->{'last-name'});
							}
							if (count($obj_DOM->{'save-account'}[$i]->{'client-info'}->email) == 1)
							{
								$obj_mPoint->saveEmail($obj_DOM->{'save-account'}[$i]->{'client-info'}->mobile, $obj_DOM->{'save-account'}[$i]->{'client-info'}->email, $obj_CountryConfig);
							}

							// Customer Data should be imported from Client System
							if ($obj_ClientConfig->getCustomerImportURL() != "")
							{
								$aURL_Info = parse_url($obj_ClientConfig->getCustomerImportURL() );
								$aHTTP_CONN_INFO["mesb"]["protocol"] = $aURL_Info["scheme"];
								$aHTTP_CONN_INFO["mesb"]["host"] = $aURL_Info["host"];
								$aHTTP_CONN_INFO["mesb"]["port"] = $aURL_Info["port"];
								$aHTTP_CONN_INFO["mesb"]["path"] = $aURL_Info["path"];
								if (array_key_exists("query", $aURL_Info) === true) { $aHTTP_CONN_INFO["mesb"]["path"] .= "?". $aURL_Info["query"]; }
								$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["mesb"]);
								try
								{
									$obj_mPoint->import($obj_ConnInfo, $obj_ClientInfo, $iAccountID, (float) $obj_DOM->{'save-account'}[$i]->{'social-security-number'});
								}
								// Error: No response received from External System
								catch (HTTPSendException $e)
								{
									$code = 7;
								}
								// Error: Unable to connect to External System
								catch (HTTPConnectionException $e)
								{
									$code = 6;
								}
							}
							if ($obj_mPoint->getClientConfig()->smsReceiptEnabled() === true)
							{
                                try
                                {
                                    // One Time Password sent
                                    if ($obj_mPoint->sendOneTimePassword(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO), $iAccountID, $obj_CountryConfig, (float) $obj_DOM->{'save-account'}[$i]->{'client-info'}->mobile) == 200)
                                    {
                                        $xml = '<status code="'. ($code+110) .'" eua-id="'. intval($iAccountID) .'">Account information successfully saved and OTP sent</status>';
                                    } else {
                                        header("HTTP/1.1 500 Internal Server Error");
                                        $xml = '<status code="91">Unable to send One Time Password</status>';
                                    }
                                } // Error: No response received from External System
                                catch (HTTPSendException $e)
                                {
                                    header("HTTP/1.1 500 Internal Server Error");

                                    $xml = '<status code="91">Unable to send One Time Password</status>';
                                } // Error: Unable to connect to External System
                                catch (HTTPConnectionException $e)
                                {
                                    header("HTTP/1.1 500 Internal Server Error");

                                    $xml = '<status code="91">Unable to send One Time Password</status>';
                                }
							}
							else { $xml = '<status code="'. ($code+100) .'" eua-id="'. intval($iAccountID) .'">Account information successfully saved</status>'; }
						}
						else
						{
							header("HTTP/1.1 500 Internal Server Error");

							$xml = '<status code="90">Unable to save Account information</status>';
						}
					}
					else
					{
						header("HTTP/1.1 400 Bad Request");

						$xml = '<status code="'. $aMsgCds[0] .'" />';
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
	elseif (count($obj_DOM->{'save-account'}) == 0)
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
?>