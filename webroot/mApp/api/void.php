<?php
/** 
 *
 * @author Manish S Dewani
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package mPoint
 * @version 1.10
 */

// Require Global Include File
require_once("../../inc/include.php");


// Require data class for Payment Service Provider Configurations
require_once(sCLASS_PATH ."/pspconfig.php");
// Require specific Business logic for the Refund component
require_once(sCLASS_PATH ."/refund.php");
// Require Business logic for Administrative functions
require_once(sCLASS_PATH ."/admin.php");
// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for the CPM PSP component
require_once(sINTERFACE_PATH ."/cpm_psp.php");
// Require specific Business logic for the CPM PSP component
require_once(sINTERFACE_PATH ."/cpm_gateway.php");
// Require specific Business logic for the DIBS component
require_once(sCLASS_PATH ."/dibs.php");
// Require specific Business logic for the WorldPay component
require_once(sCLASS_PATH ."/worldpay.php");
// Require specific Business logic for the NetAxept component
require_once(sCLASS_PATH ."/netaxept.php");
// Require specific Business logic for the DSB PSP component
require_once(sCLASS_PATH ."/dsb.php");
// Require specific customer info
require_once(sCLASS_PATH ."/customer_info.php");

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
// Require specific Business logic for the PublciBank component
require_once(sCLASS_PATH ."/publicbank.php");
// Require specific Business logic for the AliPay component
require_once(sCLASS_PATH ."/alipay.php");
require_once(sCLASS_PATH ."/alipay_chinese.php");
// Require specific Business logic for the Qiwi component
require_once(sCLASS_PATH ."/qiwi.php");
// Require specific Business logic for the Klarna component
require_once(sCLASS_PATH ."/klarna.php");
// Require specific Business logic for the 2C2P ALC component
require_once(sCLASS_PATH ."/ccpp_alc.php");
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");
// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
// Require specific Business logic for the PPRO component
require_once(sCLASS_PATH ."/ppro.php");
// Require specific Business logic for the Citcon Wechat component
require_once(sCLASS_PATH ."/citcon.php");
// Require specific Business logic for the Paytabs component
require_once(sCLASS_PATH ."/paytabs.php");
// Require specific Business logic for the eGHL FPX component
require_once(sCLASS_PATH ."/eghl.php");
// Require specific Business logic for the PayU component
require_once(sCLASS_PATH ."/payu.php");
// Require specific Business logic for the Cielo component
require_once(sCLASS_PATH ."/cielo.php");
// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );
 
/*
$_SERVER['PHP_AUTH_USER'] = "CPMDemo";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<void client-id="10007" account = "100006">';
$HTTP_RAW_POST_DATA .= '<transaction id="5045166" order-no="UAT-71081237">';
$HTTP_RAW_POST_DATA .= '<amount country-id="100">10000</amount>';
$HTTP_RAW_POST_DATA .= '</transaction>';
$HTTP_RAW_POST_DATA .= '</void>';
$HTTP_RAW_POST_DATA .= '</root>';
*/

$xml = '';

$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);
for ($i=0; $i<count($obj_DOM->void); $i++)
	{
					
		$clientID=$obj_DOM->void[$i]["client-id"];
		$account=$obj_DOM->void[$i]["account"];
		$orderno=$obj_DOM->void[$i]->transaction["order-no"];
		$transactionID=$obj_DOM->void[$i]->transaction["id"];
		$amount=$obj_DOM->void[$i]->transaction->amount;
		$country=$obj_DOM->void[$i]->transaction->amount["country-id"];
			
		$xml .= '<transactions client-id="'. intval($clientID) .'">';
		$xml .= '<transaction id="'. intval($transactionID) .'" order-no="'. htmlspecialchars($orderno) .'">';	
						

			if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
			{	
				if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mpoint.xsd") === true && count($obj_DOM->{'void'}) > 0)
				{
					
						/* ========== INPUT VALIDATION START ========== */
						$obj_Validate = new Validate();
						$aMsgCodes = array();		
																
						if ($account <= 0) { $account = -1; }		
						$code = $obj_Validate->valBasic($_OBJ_DB, $clientID, $account);							
						if ($code < 10) { $aMsgCodes[$clientID][] = new BasicConfig($code + 10, "Validation of Client : ". $clientID ." failed"); }
						elseif ($code < 20) { $aMsgCodes[$clientID][] = new BasicConfig($code + 10, "Validation of Account : ". $account ." failed"); }						
						
						/* ========== INPUT VALIDATION END ========== */
						
						if (count($aMsgCodes) == 0 )
						{	
							$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, (integer) $clientID);
										
							set_time_limit(120);
							$aMsgCds = array();
							// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
							$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );

							// Set Global Defaults
							if ($account == "") { $account = -1; }

							$obj_mPoint = new Admin($_OBJ_DB, $_OBJ_TXT);

							// Validate basic information
							if (Validate::valBasic($_OBJ_DB, $clientID, $account) == 100)
							{
								$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, $clientID, $account);

								// Set Client Defaults
								
								/* ========== Input Validation Start ========== */
								$obj_Validator = new Validate($obj_ClientConfig->getCountryConfig() );
								$usernm=$obj_ClientConfig->getUsername();
								$passw=$obj_ClientConfig->getPassword();
								// Validate input
								if ($obj_Validator->valUsername($usernm) != 10) { $aMsgCds[$obj_Validator->valUsername($usernm) + 20] = $usernm; }
								if ($obj_Validator->valPassword($passw) != 10) { $aMsgCds[$obj_Validator->valPassword($passw) + 30] = $passw; }
								$code = $obj_Validator->valmPointID($_OBJ_DB, $transactionID, $obj_ClientConfig->getID() );
								if ($code != 6 && $code != 10)
								{
									$aMsgCds[$code + 170] = $transactionID;
								}
								//if ($obj_Validator->valOrderID($_OBJ_DB, $orderno, $transactionID) > 1 && $obj_Validator->valOrderID($_OBJ_DB, $orderno, $transactionID) < 10) { $aMsgCds[$obj_Validator->valOrderID($_OBJ_DB, $orderno, $transactionID) + 180] = $orderno; }
								/* ========== Input Validation End ========== */
								// Success: Input Valid
								if (count($aMsgCds) == 0)
								{
									$obj_TxnInfo = TxnInfo::produceInfo($transactionID, $_OBJ_DB);
									$obj_TxnInfo->produceOrderConfig($_OBJ_DB);
									
									
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
									if ($obj_Validator->valPrice($obj_TxnInfo->getAmount(), $amount) != 10) { $aMsgCds[$obj_Validator->valPrice($obj_TxnInfo->getAmount(), $amount) + 50] = $amount; }
									/* ========== Input Validation End ========== */
									
									// Success: Input Valid
									if (count($aMsgCds) == 0)
									{
										$iUserID = -1;
										if (strtolower($obj_ClientConfig->getUsername() ) == strtolower($usernm) && $obj_ClientConfig->getPassword() == $passw)
										{	
											try
											{
											
												$obj_PSP = Callback::producePSP($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO);
												$obj_mPoint = new Refund($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $obj_PSP);

												// Refund operation succeeded
												$code = $obj_mPoint->refund($amount);
												
											
												if ($code == 1000 || $code == 1001)
												{
													header("HTTP/1.0 200 OK");
													$xml .= '<status code="1000"></status>';
													$aMsgCds[$code] = "Success";
													// Perform callback to Client
													if (strlen($obj_TxnInfo->getCallbackURL() ) > 0 && $obj_TxnInfo->hasEitherState($_OBJ_DB, Constants::iPAYMENT_REFUNDED_STATE) === true)
													{
														$args = array("transact" => $obj_TxnInfo->getExternalID(),
																	  "amount" => $amount);
														$obj_mPoint->getPSP()->notifyClient(Constants::iPAYMENT_REFUNDED_STATE, $args);
													}
												}
												//Request send for refund the transaction,
                                                //Once callback is receive 2003 state will update against transaction in general.php
												else if($code == 1100) //Refund initiated
                                                {
                                                    header("HTTP/1.0 200 OK");
													$xml .= '<status code="1000"></status>';
													$aMsgCds[$code] = "Success";
                                                }
												else
												{
													header("HTTP/1.0 502 Bad Gateway");
													$xml .= '<status code="999"></status>';
													$aMsgCds[999] = "Declined";
												}						
											}
											catch (HTTPException $e)
											{
												header("HTTP/1.0 502 Bad Gateway");
												$xml .= '<status code="998"></status>';
												$aMsgCds[998] = "Error while communicating with PSP";
											}
											// Internal Error
											catch (mPointException $e)
											{
												header("HTTP/1.0 500 Internal Error");
												$xml .= '<status code="'.$e->getMessage().'"></status>';
												$aMsgCds[$e->getCode()] = $e->getMessage();
											}
										}
										// Error: Unauthorized access
										else { header("HTTP/1.0 403 Forbidden"); $xml .= '<status code="403"></status>';}
									}
									// Error: Invalid Input
									else
									{
										header("HTTP/1.0 400 Bad Request");
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
												$xml .= '<status code="400"></status>';
												$obj_mPoint->newMessage($transactionID, $state, $debug);
											}
											else
											{
												// Transaction not found for mPoint ID
												if ($state == 173 && count($aMsgCds) == 1)
												{
													$xml .= '<status code="173"></status>';
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
											$xml .= '<status code="400"></status>';
											$obj_mPoint->newMessage($transactionID, $state, $debug);
										}
										else
										{
											// Transaction not found for mPoint ID
											if ($state == 173 && count($aMsgCds) == 1)
											{
												$xml .= '<status code="173"></status>';
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
								header("HTTP/1.0 400 Bad Request");	
								$xml .='<status code="400"></status>';
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
				// Error: Invalid XML Document
				elseif ( ($obj_DOM instanceof SimpleDOMElement) === false)
				{
					header("HTTP/1.1 415 Unsupported Media Type");
					
					$xml = '<status code="415">Invalid XML Document</status>';
				}
				// Error: Wrong operation
				elseif (count($obj_DOM->{'void'} ) == 0)
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
$xml .= '</transaction>';
$xml .= '</transactions>';	
	}
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<root>';
echo $xml;
echo '</root>';
?>