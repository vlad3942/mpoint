<?php
/**
 *
 * @author Rohit Malhotra
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package mConsole
 * @subpackage Config
 * @version 1.10
 */

// Require Global Include File
require_once("../../inc/include.php");

// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");
// Require Business logic for General Administration of mPoint
require_once(sCLASS_PATH ."admin.php");
// Require Business logic for the mConsole Module
require_once(sCLASS_PATH ."/mConsole.php");
// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );

/*
$_SERVER['PHP_AUTH_USER'] = "CPMDemo";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<save-client-configuration>  ';
$HTTP_RAW_POST_DATA .= '<client-config id="10001" auto-capture="true" country-id="100" language="gb" sms-receipt="true" email-receipt="true" mode="0">';
$HTTP_RAW_POST_DATA .= '<name>Emirates - IBE</name>';
$HTTP_RAW_POST_DATA .= '<username>10000000</username>';
$HTTP_RAW_POST_DATA .= '<password>99999999</password>';
$HTTP_RAW_POST_DATA .= '<max-amount country-id="100">123400</max-amount>';
$HTTP_RAW_POST_DATA .= '<urls>';  
$HTTP_RAW_POST_DATA .= '<url id="36" type-id="1">http://mpoint.test.cellpointmobile.com/home/accept.php</url>';  
$HTTP_RAW_POST_DATA .= '<url id="37" type-id="2">http://mpoint.test.cellpointmobile.com/_test/auth.php</url>                      ';  
$HTTP_RAW_POST_DATA .= '</urls>';  
$HTTP_RAW_POST_DATA .= '<keyword id="46">EK</keyword>';  
$HTTP_RAW_POST_DATA .= '<payment-methods store-card="5" show-all-cards="true" max-stored-cards="3">';  
$HTTP_RAW_POST_DATA .= '<payment-method id="338" type-id="6" state-id = "3" country-id="100" psp-id="7">VISA</payment-method>';  
$HTTP_RAW_POST_DATA .= '<payment-method id="339" type-id="7" state-id = "1" country-id="100" psp-id="7">MasterCard</payment-method>';  
$HTTP_RAW_POST_DATA .= '</payment-methods>';  
$HTTP_RAW_POST_DATA .= '<payment-service-providers>';  
$HTTP_RAW_POST_DATA .= '<payment-service-provider id="106" psp-id="7" stored-card = "true">';  
$HTTP_RAW_POST_DATA .= '<name>IBE</name>';  
$HTTP_RAW_POST_DATA .= '<username>IBE</username>';  
$HTTP_RAW_POST_DATA .= '<password>IBE</password>';  
$HTTP_RAW_POST_DATA .= '</payment-service-provider>';  
$HTTP_RAW_POST_DATA .= '</payment-service-providers>';  
$HTTP_RAW_POST_DATA .= '<account-configurations>';  
$HTTP_RAW_POST_DATA .= '<account-config id="100071">';  
$HTTP_RAW_POST_DATA .= '<name>Web</name>';  
$HTTP_RAW_POST_DATA .= '<markup>app</markup>';  
$HTTP_RAW_POST_DATA .= '<payment-service-providers>';  
$HTTP_RAW_POST_DATA .= '<payment-service-provider id="118" psp-id="7">';  
$HTTP_RAW_POST_DATA .= '<name>IBE</name>';  
$HTTP_RAW_POST_DATA .= '</payment-service-provider>';  
$HTTP_RAW_POST_DATA .= '</payment-service-providers>';  
$HTTP_RAW_POST_DATA .= '</account-config>';  
$HTTP_RAW_POST_DATA .= '</account-configurations>';  
$HTTP_RAW_POST_DATA .= '<callback-protocol send-psp-id="true">mPoint</callback-protocol>';  
$HTTP_RAW_POST_DATA .= '<identification>7</identification>';  
$HTTP_RAW_POST_DATA .= '<transaction-time-to-live>0</transaction-time-to-live>';  
$HTTP_RAW_POST_DATA .= '<issuer-identification-number-ranges>';  
$HTTP_RAW_POST_DATA .= '<issuer-identification-number-range id="2" action-id="1">';  
$HTTP_RAW_POST_DATA .= '<min>501999</min>';  
$HTTP_RAW_POST_DATA .= '<max>501999</max>';  
$HTTP_RAW_POST_DATA .= '</issuer-identification-number-range>';  
$HTTP_RAW_POST_DATA .= '</issuer-identification-number-ranges>';  
$HTTP_RAW_POST_DATA .= '<communication-channels>';
$HTTP_RAW_POST_DATA .= '<channel type = "1" />';
$HTTP_RAW_POST_DATA .= '<channel type = "5" />';
$HTTP_RAW_POST_DATA .= '</communication-channels>';
$HTTP_RAW_POST_DATA .= '</client-config>';
$HTTP_RAW_POST_DATA .= '</save-client-configuration>';  
$HTTP_RAW_POST_DATA .= '</root>';  
*/

$xml = '';

$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mconsole.xsd") === true && count($obj_DOM->{'save-client-configuration'}) > 0)
	{	
		$obj_mPoint = new mConsole($_OBJ_DB, $_OBJ_TXT);		
		$bSingleSignOnSuccess = false;
				
		//Start Single sign on Validation.	
		$aClientIDs = array();
		for ($i=0; $i<count($obj_DOM->{'save-client-configuration'}->{'client-config'}); $i++)
		{
			$aClientIDs[] = (integer) $obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]["id"];
		}
		$aHTTP_CONN_INFO["mesb"]["path"] = Constants::sMCONSOLE_SINGLE_SIGN_ON_PATH;
		$aHTTP_CONN_INFO["mesb"]["username"] = trim($_SERVER['PHP_AUTH_USER']);
		$aHTTP_CONN_INFO["mesb"]["password"] = trim($_SERVER['PHP_AUTH_PW']);
		
		$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["mesb"]);
				
		$code = $obj_mPoint->singleSignOn($obj_ConnInfo, $_SERVER['HTTP_X_AUTH_TOKEN'], mConsole::sPERMISSION_SAVE_CLIENT, $aClientIDs, $aClientIDs,$_SERVER['HTTP_VERSION']);
		
		switch ($code)
		{
		case (mConsole::iSERVICE_CONNECTION_TIMEOUT_ERROR):
			header("HTTP/1.1 504 Gateway Timeout");
		
			$xml = '<status code="'. $code .'">Single Sign-On Service is unreachable</status>';
			break;
		case (mConsole::iSERVICE_READ_TIMEOUT_ERROR):
			header("HTTP/1.1 502 Bad Gateway");
			
			$xml = '<status code="'. $code .'">Single Sign-On Service is unavailable</status>';
			break;
		case (mConsole::iUNAUTHORIZED_USER_ACCESS_ERROR):
			header("HTTP/1.1 401 Unauthorized");
		
			$xml = '<status code="'. $code .'">Unauthorized User Access</status>';
			break;
		case (mConsole::iINSUFFICIENT_USER_PERMISSIONS_ERROR):
			header("HTTP/1.1 403 Forbidden");
			
			$xml = '<status code="'. $code .'">Insufficient User Permissions</status>';
			break;
		case (mConsole::iINSUFFICIENT_CLIENT_LICENSE_ERROR):
			header("HTTP/1.1 402 Payment Required");
		
			$xml = '<status code="'. $code .'">Insufficient Client License</status>';
			break;
		case (mConsole::iAUTHORIZATION_SUCCESSFUL):
			header("HTTP/1.1 200 OK");			
			break;
		default:
			header("HTTP/1.1 500 Internal Server Error");
	
			$xml = '<status code="500">Unknown Error</status>';
			break;
		}
		
		if ($code == mConsole::iAUTHORIZATION_SUCCESSFUL)
		{				
			/* ========== INPUT VALIDATION START ========== */
			$obj_Validate = new Validate();
			$aMsgCodes = array();		
			for ($i=0; $i<count($obj_DOM->{'save-client-configuration'}->{'client-config'}); $i++) {
                $iClientID = intval($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]["id"]);
                $code = $obj_Validate->valBasic($_OBJ_DB, $iClientID, -1);
                if (in_array($code, array(4, 14, 100)) === false) {
                    $aMsgCodes[$iClientID][] = new BasicConfig($code + 10, "Validation of Client : " . $iClientID . " failed");
                }

                // Validate Account Configurations
                for ($j = 0; $j < count($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'account-configurations'}->{'account-config'}); $j++) {
                    $iAccountID = intval($obj_DOM->{'save-client-configuration'}->{'client-config'}->{'account-configurations'}->{'account-config'}[$j]["id"]);
                    $code = $obj_Validate->valBasic($_OBJ_DB, $iClientID, $iAccountID);
                    if (in_array($code, array(14, 100, 11, 12)) === true) {
                        // Validate Merchant Sub-Accounts
                        for ($k = 0; $k < count($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'account-configurations'}->{'account-config'}[$j]->{'payment-service-providers'}->{'payment-service-provider'}); $k++) {
                            $id = intval($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'account-configurations'}->{'account-config'}[$j]->{'payment-service-providers'}->{'payment-service-provider'}[$k]["id"]);
                            $code = $obj_Validate->valMerchantSubAccountID($_OBJ_DB, $id, $iAccountID);
                            if (1 < $code && $code < 10) {
                                $aMsgCodes[$iClientID][] = new BasicConfig($code + 30, "Validation of Merchant Sub Account : " . $id . " failed");
                            }
                        }
                    } else {
                        $aMsgCodes[$iClientID][] = new BasicConfig($code + 20, "Validation of Account : " . $iAccountID . " failed");
                    }
                }
                // Validate Merchant Accounts
                for ($j = 0; $j < count($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'payment-service-providers'}->{'payment-service-provider'}); $j++) {
                    $id = $obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'payment-service-providers'}->{'payment-service-provider'}[$j]["id"];
                    $code = $obj_Validate->valMerchantAccountID($_OBJ_DB, $id, $iClientID);
                    if (1 < $code && $code < 10) {
                        $aMsgCodes[$iClientID][] = new BasicConfig($code + 40, "Validation of Merchant Account : " . $id . " failed");
                    }
                }
                // Validate Payment Methods
                for ($j = 0; $j < count($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'payment-methods'}->{'payment-method'}); $j++) {
                    $id = $obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'payment-methods'}->{'payment-method'}[$j]["id"];
                    $code = $obj_Validate->valCardAccessID($_OBJ_DB, $id, $iClientID);
                    if (1 < $code && $code < 10) {
                        $aMsgCodes[$iClientID][] = new BasicConfig($code + 50, "Validation of Payment Method : " . $id . " failed");
                    }
                }
                // Validate Issuer Identification Number Ranges
                for ($j = 0; $j < count($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'issuer-identification-number-ranges'}->{'issuer-identification-number-range'}); $j++) {
                    $id = $obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'issuer-identification-number-ranges'}->{'issuer-identification-number-range'}[$j]["id"];
                    $code = $obj_Validate->valIINRangeID($_OBJ_DB, $id, $iClientID);
                    if (1 < $code && $code < 10) {
                        $aMsgCodes[$iClientID][] = new BasicConfig($code + 60, "Validation of Client IIN Range : " . $id . " failed");
                    }
                }
                //Summing Up communication channel values for the given client
                $sumChannels = 0;
                for ($j = 0; $j < count($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'communication-channels'}->{'channel'}); $j++) {
                	$sumChannels += intval($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'communication-channels'}->{'channel'}[$j]["type"] );
				}
				
			}
			/* ========== INPUT VALIDATION END ========== */
			
			// Success: Input Valid
			if (count($aMsgCodes) == 0)
			{			
				for ($i=0; $i<count($obj_DOM->{'save-client-configuration'}->{'client-config'}); $i++)
				{
					$_OBJ_DB->query("START TRANSACTION");  // START TRANSACTION does not work with Oracle db
											
					try
					{
						$iClientID = $obj_mPoint->saveClient( (integer) $obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]["country-id"],
															 (integer) $obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]["store-card"],
															 General::xml2bool($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]["auto-capture"]),
															 trim($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->name),
															 trim($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->username),
															 trim($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->password),
															 trim($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'max-amount'}),
															 trim($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]["language"]),
															 General::xml2bool($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]["sms-receipt"]),
															 General::xml2bool($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]["email-receipt"]),
															 (integer) $obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]["mode"],
															 trim($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'callback-protocol'}),
															 General::xml2bool($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'callback-protocol'}["send-psp-id"]),
															 (integer) $obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->identification,
															 (integer) $obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'transaction-time-to-live'},
															 trim($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'salt'}),
															 $sumChannels,
															 (integer) $obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]["id"] );
						// Success
						if ($iClientID > 0)
						{
							if (count($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'account-configurations'}) == 1 )
							{							
								$aAccountIds = array();
								
								for ($j=0; $j<count($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'account-configurations'}->{'account-config'}); $j++)
								{
									$iAccountID = $obj_mPoint->saveAccount( $iClientID,
																		   trim($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'account-configurations'}->{'account-config'}[$j]->name),
																		   trim($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'account-configurations'}->{'account-config'}[$j]->markup),
																		   (integer) $obj_DOM->{'save-client-configuration'}->{'client-config'}->{'account-configurations'}->{'account-config'}[$j]["id"]);
									
									// Success
									if ($iAccountID > 0)
									{
										$aAccountIds[$j] = $iAccountID;
										
										$aMerchantSubAccountIds = array();
										
										for ($k=0; $k<count($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'account-configurations'}->{'account-config'}[$j]->{'payment-service-providers'}->{'payment-service-provider'}); $k++)
										{
											$iMSAID = $obj_mPoint->saveMerchantSubAccount( $iAccountID,
																						  (integer) $obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'account-configurations'}->{'account-config'}[$j]->{'payment-service-providers'}->{'payment-service-provider'}[$k]["psp-id"],
																						  trim($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'account-configurations'}->{'account-config'}[$j]->{'payment-service-providers'}->{'payment-service-provider'}[$k]->name),
																						  (integer) $obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'account-configurations'}->{'account-config'}[$j]->{'payment-service-providers'}->{'payment-service-provider'}[$k]["id"]);
											
											// Error: Break out of loop
											if ($iMSAID < 0) { throw new mConsoleSaveMerchantSubAccountFailedException("Error during save payment service provider: ". $iMSAID ." for account: ". $iAccountID); }
											
											$aMerchantSubAccountIds[$k] = $iMSAID;
										}
										
										$bDisableMSA = $obj_mPoint->disableMerchantSubAccounts($iAccountID, $aMerchantSubAccountIds);
										
										if($bDisableMSA === false) { throw new mConsoleDisableMerchantSubAccountFailedException("Error during disable payment service providers for account: ". $iAccountID); }
										
									}
									// Error: Break out of loop
									else { throw new mConsoleSaveAccountFailedException("Error during Save Account: ". $iAccountID ." for client: ". $iClientID); }							
								
								}
								
								
								//Disbale all accounts linked to the client.
								$bDisableAccounts = $obj_mPoint->disableAccounts($iClientID, $aAccountIds);
									
								if($bDisableAccounts === false) {throw new mConsoleDisableAccountFailedException("Error during disable Accounts for client: ". $iClientID); }
								
							}
							
							//Disable Merchant Accounts
							$bDisableMA = $obj_mPoint->disableMerchantAccounts($iClientID);
							
							if($bDisableMA === false) { throw new mConsoleDisableMerchantAccountFailedException("Error during disable payment service providers for client: ". $iClientID); }
							
							//Save Merchant Account Details.
							for ($j=0; $j<count($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'payment-service-providers'}->{'payment-service-provider'}); $j++)
							{
								
								 $iMAID = $obj_mPoint->saveMerchantAccount( $iClientID,																	 
																		 (integer) $obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'payment-service-providers'}->{'payment-service-provider'}[$j]["psp-id"],
																		 trim($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'payment-service-providers'}->{'payment-service-provider'}[$j]->name),
																		 trim($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'payment-service-providers'}->{'payment-service-provider'}[$j]->username),
																		 trim($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'payment-service-providers'}->{'payment-service-provider'}[$j]->password),
																		 General::xml2bool($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'payment-service-providers'}->{'payment-service-provider'}[$j]['stored-card']),
																		 (integer) $obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'payment-service-providers'}->{'payment-service-provider'}[$j]["id"]);
								// Error: Break out of loop
								if ($iMAID < 0) { throw new mConsoleSaveMerchantAccountFailedException("Error during save payment service provider: ". $iMAID ." for client: ". $iClientID); }
							}

							if (count($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'payment-methods'}) == 1 )
							{
								//Disable all card access to the client
								$bDisableCardAccess = $obj_mPoint->disableAllCardAccess($iClientID);
								
								if($bDisableCardAccess === false) { throw new mConsoleDisableCardAccessFailedException("Error during disable all card access for client: ". $iClientID); }
								
								//Save the data in Client table for all the card related data.
								$isClientCardError = $obj_mPoint->saveClientCardData( $iClientID, 
																					(integer) $obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'payment-methods'}["store-card"], 
																					(integer) General::xml2bool($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'payment-methods'}["show-all-cards"]),
																					(integer) $obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'payment-methods'}["max-stored-cards"]);
																					
								if ($isClientCardError == true)
								{
									for ($j=0; $j<count($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'payment-methods'}->{'payment-method'}); $j++)											
									{
										$enabled = $obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'payment-methods'}->{'payment-method'}[$j]["enabled"];
										if(is_null($enabled) == true)
										{
											$enabled = 'true';
										}
										
										//Save card access data.
										$iPMID = $obj_mPoint->saveStaticRoute($iClientID,
																			 (integer) $obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'payment-methods'}->{'payment-method'}[$j]["type-id"],
																			 (integer) $obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'payment-methods'}->{'payment-method'}[$j]["psp-id"],
																			 (integer) $obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'payment-methods'}->{'payment-method'}[$j]["state-id"],
																			 (integer) $obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'payment-methods'}->{'payment-method'}[$j]["country-id"],
																			 (integer) $obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'payment-methods'}->{'payment-method'}[$j]["id"],
																			 $enabled);
										// Error: Break out of loop
										if ($iPMID < 0) { throw new mConsoleSaveCardAccessFailedException("Error during save card access: ". $iPMID ." for client: ". $iClientID); }
									}
								}
							}							
							
							if (count($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->keyword ) == 1 )
							{
								//Disable all keywords.
								$bDisableKeyword = $obj_mPoint->disableKeyword($iClientID);
								
								if($bDisableKeyword === false) { throw new mConsoleDisableKeywordFailedException("Error during save payment service provider: ". $iMSAID ." for account: ". $iAccountID); }
								
								$iKeyID = $obj_mPoint->saveKeyword(																
																	$iClientID, 
																	trim($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->keyword),
																	(integer) $obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->keyword["id"]
																	);
																	
								// Error: Break out of loop
								if ($iKeyID < 0) { throw new mConsoleSaveKeywordFailedException("Error during save keywords: ". $iKeyID ." for client: ". $iClientID); }
							}
							
							if (count($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->urls ) == 1 )
							{
										
								//Disable Client URLs
								//$bDisabeURLs = $obj_mPoint->disableURLs($iClientID);
								
								if($bDisableKeyword === false) { throw new mConsoleDisableKeywordFailedException("Error during disable keyword for client: ". $iClientID ); }
															
								//Save Client URL data.
								for ($j = 0; $j<count($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->urls->url); $j++)
								{
							
									$iURLID = $obj_mPoint->saveURL(														
																	$iClientID,
																	(integer) $obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->urls->url[$j]["type-id"],
																	trim($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->urls->url[$j]),
																	(integer) $obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->urls->url[$j]["id"]
																	);
									// Error: Break out of loop
									if ($iURLID < 0) { throw new mConsoleSaveURLFailedException("Error during save URL: ". $iURLID ." for client: ". $iClientID); }
								}
							}
							
							if (count($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'issuer-identification-number-ranges'} ) == 1 )
							{								
								//Disable IIN Ranges for the Client.							
								$bDisableIIN = $obj_mPoint->disableIINRanges($iClientID);
								
								if($bDisableIIN === false) { throw new mConsoleDisableIINRangeFailedException("Error during disable IIN range for client: ". $iClientID ); }
								
								//Save Client IIN range data.
								for ($j = 0; $j<count($obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'issuer-identification-number-ranges'}->{'issuer-identification-number-range'}); $j++)
								{
							
									$iIINRangeID = $obj_mPoint->saveIINRange(																			
																			$iClientID,
																			(integer) $obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'issuer-identification-number-ranges'}->{'issuer-identification-number-range'}[$j]["action-id"],
																			(integer) $obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'issuer-identification-number-ranges'}->{'issuer-identification-number-range'}[$j]->min,
																			(integer) $obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'issuer-identification-number-ranges'}->{'issuer-identification-number-range'}[$j]->max,
																			(integer) $obj_DOM->{'save-client-configuration'}->{'client-config'}[$i]->{'issuer-identification-number-ranges'}->{'issuer-identification-number-range'}[$j]["id"]
																			);
									// Error: Break out of loop
									if ($iURLID < 0) { throw new mConsoleSaveIINRangeFailedException("Error during save IIN Range: ". $iIINRangeID ." for client: ". $iClientID); }
								}
							}

							//If all Success then commit the DB transaction.
							$_OBJ_DB->query("COMMIT");
                            $xml .= '<status code="100" client-id="'. $iClientID .'">OK</status>';
													
						}
						else { throw new mConsoleSaveClientFailedException("Error during save client: ". $iClientID);  }
						
						
					}
					catch (Exception $e)
					{
						$_OBJ_DB->query("ROLLBACK");
						header("HTTP/1.1 500 Internal Server Error");
						
                        $xml .= '<status code="500">'. $e->getMessage() .'</status>';
					}	
				}
			}
			else
			{
				header("HTTP/1.1 400 Bad Request");
		
				foreach ($aMsgCodes as $iClientID => $obj_MessageContainer)
				{					
					foreach($obj_MessageContainer as $obj_Message)
					{
						$xml .= '<status code="'. $obj_Message->getID() .'">'. $obj_Message->getName() .' for Client '. $iClientID .'</status>' ;
					}				
				}			
			}
		}
		
	}
	// Error: Invalid XML Document
	elseif ( ($obj_DOM instanceof SimpleDOMElement) === false)
	{
		header("HTTP/1.1 415 Unsupported Media Type");
		
		$xml .= '<status code="415">Invalid XML Document</status>';
	}
	// Error: Wrong operation
	elseif (count($obj_DOM->{'save-client-configuration'}) == 0)
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
