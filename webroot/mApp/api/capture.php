<?php
/**
 * This files contains the Controller for mPoint's Capture API.
* The Controller will ensure that all input from the client is validated prior to performing the capture.
* Finally, assuming the Client Input is valid, the Controller will contact the Payment Service Provider to perform the Capture.
*
* @author Manish S Dewani
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
// Require specific Business logic for the CCAvenue component
require_once(sCLASS_PATH ."/ccavenue.php");
// Require specific Business logic for the 2C2P component
require_once(sCLASS_PATH ."/ccpp.php");
// Require specific Business logic for the MayBank component
require_once(sCLASS_PATH ."/maybank.php");

// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");
// Require Business logic for General Administration of mPoint
require_once(sCLASS_PATH ."admin.php");

// Require Business logic for the mConsole Module
require_once(sCLASS_PATH ."/mConsole.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
//header("Content-Type: application/x-www-form-urlencoded");

/*
 $_SERVER['PHP_AUTH_USER'] = "CPMDemo";
 $_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

 $HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
 $HTTP_RAW_POST_DATA .= '<root>';
 $HTTP_RAW_POST_DATA .= '<capture client-id="10007" account="100007">';
 $HTTP_RAW_POST_DATA .= '<transaction id="1812096" order-no="UAT-28577880">';
 $HTTP_RAW_POST_DATA .= '<amount country-id="100">10000</amount>';
 $HTTP_RAW_POST_DATA .= '</transaction>';
 $HTTP_RAW_POST_DATA .= '</capture>';
 $HTTP_RAW_POST_DATA .= '</root>';
 */
set_time_limit(120);

$aMsgCds = array();

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );

$xml = '';

$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);
for ($i=0; $i<count($obj_DOM->capture); $i++)
{
	$clientID=$obj_DOM->capture[$i]["client-id"];
	$account=$obj_DOM->capture[$i]["account"];
	$orderno=$obj_DOM->capture[$i]->transaction["order-no"];
	$transactionID=$obj_DOM->capture[$i]->transaction["id"];
	$amount=$obj_DOM->capture[$i]->transaction->amount;
	$country=$obj_DOM->capture[$i]->transaction->amount["country-id"];
	
	$xml .= '<transactions client-id = "'. intval($clientID) .'" >';	
	$xml .= '<transaction id="'. intval($transactionID) .'" order-no="'. htmlspecialchars($orderno) .'">';

	if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
	{
		if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mpoint.xsd") === true && count($obj_DOM->capture) > 0)
		{
			
				/* ========== INPUT VALIDATION START ========== */
				$obj_Validate = new Validate();
				$aMsgCodes = array();

				if ($account <= 0) { $account = -1; }
				$code = $obj_Validate->valBasic($_OBJ_DB, $clientID, $account);
				if ($code < 10) { $aMsgCodes[$clientID][] = new BasicConfig($code + 10, "Validation of Client : ". $clientID ." failed"); }
				elseif ($code < 20) { $aMsgCodes[$clientID][] = new BasicConfig($code + 10, "Validation of Account : ". $account ." failed"); }
					
				/* ========== INPUT VALIDATION END ========== */
					
				if (count($aMsgCodes) == 0)
				{
					// Set Global Defaults
					if ($account=="") { $_REQUEST['account'] = -1; }
					$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);
					
					// Validate basic information
					if (Validate::valBasic($_OBJ_DB, $clientID, $account) == 100)
					{
						$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, $clientID, $account);

						// Set Client Defaults

						/* ========== Input Validation Start ========== */
						$obj_Validator = new Validate($obj_ClientConfig->getCountryConfig() );

					    if ($obj_Validator->valmPointID($_OBJ_DB, $transactionID, $obj_ClientConfig->getID() ) != 10) { $aMsgCds[$obj_Validator->valmPointID($_OBJ_DB, $transactionID, $obj_ClientConfig->getID() ) + 170] = $obj_DOM->capture[$i]->transaction["id"]; }
						//if ($obj_Validator->valOrderID($_OBJ_DB, $orderno, $transactionID) > 1 && $obj_Validator->valOrderID($_OBJ_DB, $orderno, $transactionID) < 10) { $aMsgCds[$obj_Validator->valOrderID($_OBJ_DB, $orderno, $transactionID) + 180] = $obj_DOM->capture[$i]->transaction["order-no"]; }
						/* ========== Input Validation End ========== */

						// Success: Input Valid
						if (count($aMsgCds) == 0)
						{
							$obj_TxnInfo = TxnInfo::produceInfo($transactionID, $_OBJ_DB);
							if (array_key_exists("HTTP_X_AUTH_TOKEN", $_SERVER) === true)
							{
								$obj_CustomerInfo = CustomerInfo::produceInfo($_OBJ_DB, $obj_TxnInfo->getAccountID() );
								$obj_Customer = simplexml_load_string($obj_CustomerInfo->toXML() );
								if (strlen($obj_TxnInfo->getCustomerRef() ) > 0) { $obj_Customer["customer-ref"] = $obj_TxnInfo->getCustomerRef(); }
								if (floatval($obj_TxnInfo->getMobile() ) > 0)
								{
									$obj_Customer->mobile = $obj_TxnInfo->getMobile();
									$obj_Customer->mobile["country-id"] = intval($obj_TxnInfo->getOperator() / 100);
									$obj_Customer->mobile["operator-id"] = $obj_TxnInfo->getOperator();
								}
								if (strlen($obj_TxnInfo->getEMail() ) > 0) { $obj_Customer->email = $obj_TxnInfo->getEMail(); }
								$obj_CustomerInfo = CustomerInfo::produceInfo($obj_Customer);
								$code = $obj_mPoint->auth(HTTPConnInfo::produceConnInfo($obj_TxnInfo->getAuthenticationURL() ), $obj_CustomerInfo, trim($_SERVER['HTTP_X_AUTH_TOKEN']) );
							}
							/* ========== Input Validation Start ========== */
							$code = $obj_Validator->valPrice($obj_TxnInfo->getAmount(), $amount) ;
							if ($code != 10) 
							{ 
							  $aMsgCds[$code + 50] = $obj_DOM->capture[$i]->transaction->amount;
							}
							/* ========== Input Validation End ========== */
							// Success: Input Valid
							if (count($aMsgCds) == 0)
							{
								try
								{
									$obj_PSP = Callback::producePSP($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO);
									$obj_mPoint = new Capture($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $obj_PSP);
									$code = $obj_mPoint->capture( (integer) $amount);
									// Refresh transactioninfo object once the capture is performed
									$obj_TxnInfo = TxnInfo::produceInfo($obj_TxnInfo->getID(), $_OBJ_DB);

									// Capture operation succeeded
									if ($code == 1000)
									{
										header("HTTP/1.0 200 OK");
											
										$aMsgCds[1000] = "Success";
										$xml .= '<status code="1000" ></status>';
										// Perform callback to Client
										if (strlen($obj_TxnInfo->getCallbackURL() ) > 0)
										{
											$args = array("transact" => $obj_TxnInfo->getExternalID(),
													"amount" => $amount,
													"fee" => $obj_TxnInfo->getFee() );
											$obj_mPoint->getPSP()->notifyClient(Constants::iPAYMENT_CAPTURED_STATE, $args);
										}
									}
									else
									{
										header("HTTP/1.0 502 Bad Gateway");
											
										$aMsgCds[999] = "Declined";
										$xml .= '<status code="999" ></status>';
										// Perform callback to Client
										if (strlen($obj_TxnInfo->getCallbackURL() ) > 0)
										{
											$args = array("transact" => $obj_TxnInfo->getExternalID(),
													"amount" => $amount);
											$obj_mPoint->getPSP()->notifyClient(Constants::iPAYMENT_DECLINED_STATE, $args);
										}
									}
								}
								catch (BadMethodCallException $e)
								{
									header("HTTP/1.0 405 Method Not Allowed");
									$xml .= '<status code="997" ></status>';
									$aMsgCds[997] = "Capture not supported by PSP";
									trigger_error("Capture not supported by PSP" ."\n". var_export($e, true), E_USER_WARNING);
								}
								catch (HTTPException $e)
								{
									header("HTTP/1.0 502 Bad Gateway");
									$xml .= '<status code="998" ></status>';
									$aMsgCds[998] = "Error while communicating with PSP";
									trigger_error("Error while communicating with PSP" ."\n". var_export($e, true), E_USER_WARNING);
								}
								// Internal Error
								catch (mPointException $e)
								{
									header("HTTP/1.0 500 Internal Error");
									$xml .= '<status code="'.$e->getMessage().'" ></status>';
									$aMsgCds[$e->getCode()] = $e->getMessage();
									trigger_error("Internal Error" ."\n". var_export($e, true), E_USER_WARNING);
								}
							}
							// Error: Invalid Input
							else
							{
								header("HTTP/1.0 400 Bad Request");
								$xml .= '<status code="400" ></status>';
								// Log Errors
								foreach ($aMsgCds as $state => $debug)
								{
									/*
									 * Method: valmPointID has not returned one of the following states:
									 * 	 1. Undefined mPoint ID
									 * 	 2. Invalid mPoint ID
									 * 	 3. Transaction not found for mPoint ID
									 */
									if (array_key_exists(171, $aMsgCds) === false && array_key_exists(172, $aMsgCds) === false && array_key_exists(173, $aMsgCds) === false)
									{
										$obj_mPoint->newMessage($transactionID, $state, $debug);
									}
									else
									{
										// Transaction not found for mPoint ID
										if ($state == 173 && count($aMsgCds) == 1)
										{
											header("HTTP/1.0 404 Not Found");
										}
										trigger_error("Unable to log invalid input: ". $debug ." for state: ". $state .". No associated transaction found for mPoint ID: ". @$transactionID, E_USER_NOTICE);
									}
								}
							}
						}
						// Error: Invalid Input
						else
						{
							header("HTTP/1.0 400 Bad Request");
							$xml .= '<status code="400" ></status>';
							// Log Errors
							foreach ($aMsgCds as $state => $debug)
							{
								/*
								 * Method: valmPointID has not returned one of the following states:
								 * 	 1. Undefined mPoint ID
								 * 	 2. Invalid mPoint ID
								 * 	 3. Transaction not found for mPoint ID
								 */
								if (array_key_exists(171, $aMsgCds) === false && array_key_exists(172, $aMsgCds) === false && array_key_exists(173, $aMsgCds) === false)
								{
									$obj_mPoint->newMessage($transactionID, $state, $debug);
								}
								else
								{
									// Transaction not found for mPoint ID
									if ($state == 173 && count($aMsgCds) == 1)
									{
										header("HTTP/1.0 404 Not Found");
									}
									trigger_error("Unable to log invalid input: ". $debug ." for state: ". $state .". No associated transaction found for mPoint ID: ". @$transactionID, E_USER_NOTICE);
								}
									
							}
						}
					}
					// Error: Basic information is invalid
					else
					{
						$xml .='<status code="400"></status>';
						header("HTTP/1.0 400 Bad Request");

						$aMsgCds[Validate::valBasic($_OBJ_DB, $clientID, $account)+10] = "Client: ". $clientID .", Account: ". $account;
					}

				}
				else
				{
					header("HTTP/1.1 400 Bad Request");

					foreach ($aMsgCodes as $iClientID => $aObj_Messages)
					{
						foreach($aObj_Messages as $obj_Message)
						{
							$xml .= '<status code="'. $obj_Message->getID() .'">'. $obj_Message->getName() .' for Client '. $iClientID .'</status>' ;
						}
					}
				}

		}
		elseif ( ($obj_DOM instanceof SimpleDOMElement) === false)
		{
			header("HTTP/1.1 415 Unsupported Media Type");

			$xml = '<status code="415">Invalid XML Document</status>';
		}
		// Error: Wrong operation
		elseif (count($obj_DOM->capture) == 0)
		{
			header("HTTP/1.1 400 Bad Request");

			$xml = '';
			foreach ($obj_DOM->children() as $obj_Elem)
			{
				$xml = '<status code="400">Wrong operation: '. $obj_Elem->getName() .'</status>';
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
				$xml = '<status code="400">'. htmlspecialchars($aObj_Errs[$i]->message, ENT_NOQUOTES) .'</status>';
			}
		}

	}
	else
	{
		header("HTTP/1.1 401 Unauthorized");

		$xml = '<status code="401">Authorization required</status>';
	}

	$xml .='</transaction>';
	$xml .='</transactions>';

}
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<root>';
echo $xml;
echo '</root>';

?>