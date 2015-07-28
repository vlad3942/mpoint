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
				
		$code = $obj_mPoint->singleSignOn($obj_ConnInfo, $_SERVER['HTTP_X_AUTH_TOKEN'], mConsole::sPERMISSION_SAVE_CLIENT, $aClientIDs);
		switch ($code)
		{
		case (mConsole::iSERVICE_UNAVAILABLE_ERROR):
			header("HTTP/1.1 502 Bad Gateway");
	
			$xml = '<status code="'. $code .'">Single Sign-On Service is unavailable</status>';
			break;
		case (mConsole::iUNAUTHORIZED_USER_ACCESS_ERROR):
			header("HTTP/1.1 401 Unauthorized");
	
			$xml = '<status code="'. $code .'">Unauthorized User Access</status>';
			break;
		case (mConsole::iINSUFFICIENT_PERMISSIONS_ERROR):
			header("HTTP/1.1 403 Forbidden");
	
			$xml = '<status code="'. $code .'">Insufficient Permissions</status>';
			break;
		case (mConsole::iAUTHORIZATION_SUCCESSFUL):
			header("HTTP/1.1 200 OK");	
			$bSingleSignOnSuccess = true;
			break;
		default:
			header("HTTP/1.1 500 Internal Server Error");
	
			$xml = '<status code="500">Unknown Error</status>';
			break;
		}
		
		if($bSingleSignOnSuccess === true)
		{				
			//Validating of account and client
			$obj_val = new Validate();
			$aMsgCodes = array();		
			for ($j=0; $j<count($obj_DOM->{'save-client-configuration'}->{'client-config'}); $j++)
			{										
				$iClientID = intval($obj_DOM->{'save-client-configuration'}->{'client-config'}[$j]["id"]);			
				$iClientCode = $obj_val->valBasic($_OBJ_DB, $iClientID, -1);
				if($iClientCode == 100 || $iClientCode == 14 || $iClientCode == 4)
				{
					for($a=0; $a<count($obj_DOM->{'save-client-configuration'}->{'client-config'}[$j]->accounts->account); $a++)
					{
						$iAccountID = intval($obj_DOM->{'save-client-configuration'}->{'client-config'}->accounts->account[$a]["id"]);
						$iAccountCode = $obj_val->valBasic($_OBJ_DB, $iClientID, $iAccountID);
						if($iAccountCode == 100 || $iAccountCode == 14)
						{
							for($k=0; $k<count($obj_DOM->{'save-client-configuration'}->{'client-config'}[$j]->accounts->account[$a]->{'payment-service-providers'}->{'payment-service-provider'}); $k++)
							{ 										
								$iMerchantSubAccountID = intval($obj_DOM->{'save-client-configuration'}->{'client-config'}[$j]->accounts->account[$a]->{'payment-service-providers'}->{'payment-service-provider'}[$k]["id"]);
								$iMerchantSubAccountCode = $obj_val->valMerchantSubAccountID($_OBJ_DB, $iMerchantSubAccountID, $iAccountID);
								if($iMerchantSubAccountCode == 1 || $iMerchantSubAccountCode == 2 || $iMerchantSubAccountCode == 3 || $iMerchantSubAccountCode == 4)
								{
									$aMsgCodes[$iClientID]['merchantsubaccount '.$iMerchantSubAccountID] = $iMerchantSubAccountCode;
								}																									
							}
						}
						else 
						{
							$aMsgCodes[$iClientID]['account '.$iAccountID] = $iAccountCode;
						}
					}
					//for client merchant accounts.
					for($p=0; $p<count($obj_DOM->{'save-client-configuration'}->{'client-config'}[$j]->{'payment-service-providers'}->{'payment-service-providers'}); $p++)
					{
						$iMerchantAccountID = $obj_DOM->{'save-client-configuration'}->{'client-config'}[$j]->{'payment-service-providers'}->{'payment-service-provider'}[$p]["id"];
						$iMerchantAccountCode = $obj_val->valMerchantAccountID($_OBJ_DB, $iMerchantAccountID, $iClientID);
						if($iMerchantAccountCode == 1 || $iMerchantAccountCode == 2 || $iMerchantAccountCode == 3 || $iMerchantAccountCode == 4)
						{
							$aMsgCodes[$iClientID]['merchantaccount '.$iMerchantAccountID] = $iMerchantAccountCode;
						}
					}
					//for client payment methods.
					for($c=0; $c<count($obj_DOM->{'save-client-configuration'}->{'client-config'}[$j]->{'payment-methods'}->{'payment-method'}); $c++)											
					{
						$iCardID = $obj_DOM->{'save-client-configuration'}->{'client-config'}[$j]->{'payment-methods'}->{'payment-method'}[$c]["id"];
						$iCardCode = $obj_val->valCardAccessID($_OBJ_DB, $iCardID, $iClientID);
						if($iCardCode == 1 || $iCardCode == 2 || $iCardCode == 3 || $iCardCode == 4)
						{
							$aMsgCodes[$iClientID]['paymentmethod '.$iCardID] = $iCardCode;
						}						
					}					
					//for IIN ranges
					for($in = 0; $in<count($obj_DOM->{'save-client-configuration'}->{'client-config'}[$j]->{'issuer-identification-number-ranges'}->{'issuer-identification-number-range'}); $in++)
					{
						$iIINRangeID = $obj_DOM->{'save-client-configuration'}->{'client-config'}[$j]->{'issuer-identification-number-ranges'}->{'issuer-identification-number-range'}[$in]["id"];
						$iIINRangeCode = $obj_val->valIINRangeID($_OBJ_DB, $iIINRangeID, $iClientID);
						if($iIINRangeCode == 1 || $iIINRangeCode == 2 || $iIINRangeCode == 3 || $iIINRangeCode == 4)
						{
							$aMsgCodes[$iClientID]['iinrange '.$iIINRangeID] = $iIINRangeCode;
						}					
						
					}					
					
				}
				else
				{
					$aMsgCodes[$iClientID]['clientid'] = $iClientCode;	
				}				
			}
			
			if(count($aMsgCodes) == 0)
			{			
				for ($i=0; $i<count($obj_DOM->{'save-client-configuration'}); $i++)
				{
					for ($j=0; $j<count($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}); $j++)
					{
						$_OBJ_DB->query("START TRANSACTION");  // START TRANSACTION does not work with Oracle db
												
						$clientid = $obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]["id"];	
									
						try
						{
							$iErrors = $obj_mPoint->saveClient($clientid,
																$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]["country-id"],
																$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]["store-card"],
														 		General::xml2bool($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]["auto-capture"]),
														 		$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->name,
										 				 		$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->username,
														 		$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->password,
														 		$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]["language"],
														 		General::xml2bool($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]["sms-receipt"]),
														 		General::xml2bool($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]["email-receipt"]),
														 		$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]["mode"],
														 		$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'callback-protocol'},
														 		General::xml2bool($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'callback-protocol'}["send-psp-id"]),
														 		$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->identification,
														 		$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'transaction-time-to-live'}				 		
														 		);						
							
							if ($iErrors == false )
							{
								$_OBJ_DB->query("ROLLBACK");
								header("HTTP/1.1 500 internal server error");
	                            $xml .= '<status code="500">Error during Save Client</status>';
								break;
							}
							else
							{	
								
								$aAccountIDs = array();
								for($a=0; $a<count($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->accounts->account); $a++)
								{	                                
									$accountid = $obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}->accounts->account[$a]["id"];									
									
									$iErrors = $obj_mPoint->saveAccount($accountid,
																	 $clientid,
																	 $obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->accounts->account[$a]->name,
																	 $obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->accounts->account[$a]->markup);
									
									$aAccountIDs[] = $accountid;
																	 
									if ($iErrors == false )
									{
										$_OBJ_DB->query("ROLLBACK");
										header("HTTP/1.1 500 internal server error");
	                                    $xml .= '<status code="500">Error during Save Account</status>';
									}														
									else
									{				
										$obj_mPoint->disableMerchantSubAccounts($accountid);
										
										for($k=0; $k<count($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->accounts->account[$a]->{'payment-service-providers'}->{'payment-service-provider'}); $k++)
										{ 										
											$iErrors = $obj_mPoint->saveMerchantSubAccount(
													$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->accounts->account[$a]->{'payment-service-providers'}->{'payment-service-provider'}[$k]["id"],
													$accountid,												
													$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->accounts->account[$a]->{'payment-service-providers'}->{'payment-service-provider'}[$k]["psp-id"],
													$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->accounts->account[$a]->{'payment-service-providers'}->{'payment-service-provider'}[$k]->name);
									
											if ($iErrors == false )
											{								
												$_OBJ_DB->query("ROLLBACK");
												header("HTTP/1.1 500 internal server error");
	                                            $xml .= '<status code="500">Error during save payment service providers for account</status>';
											}										
										}
									}
									
								}
								
								/*Disable all accounts associated with the client that are  not passed in the request.*/
								
								$obj_mPoint->disableAccounts($clientid, $aAccountIDs);
								
								$obj_mPoint->disableMerchantAccounts($clientid);
								
								for($p=0; $p<count($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'payment-service-providers'}->{'payment-service-provider'}); $p++)
								{
									
									$iErrors = ($iErrors && $obj_mPoint->saveMerchantAccount(
																		 $obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'payment-service-providers'}->{'payment-service-provider'}[$p]["id"],
																		 $clientid,																	 
																		 $obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'payment-service-providers'}->{'payment-service-provider'}[$p]["psp-id"],
																		 $obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'payment-service-providers'}->{'payment-service-provider'}[$p]->name,
																		 $obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'payment-service-providers'}->{'payment-service-provider'}[$p]->username,
																		 $obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'payment-service-providers'}->{'payment-service-provider'}[$p]->password,
																		 General::xml2bool($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'payment-service-providers'}->{'payment-service-provider'}[$p]['stored-card'])																	 
																		 ));
								}
								if ($iErrors == false )
								{
									$_OBJ_DB->query("ROLLBACK");
									header("HTTP/1.1 500 internal server error");
	                                        $xml .= '<status code="500">Error during save payment service providers</status>';
								}
										
								else
								{
									$obj_mPoint->disableAllCardAccess($clientid);
									
									$isClientCardError = $obj_mPoint->saveClientCardData($clientid, 
																						$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'payment-methods'}["store-card"], 
																						General::xml2bool($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'payment-methods'}["show-all-cards"]),
																						$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'payment-methods'}["max-stored-cards"]);
																																
									if($isClientCardError == true)
									{
										for($c=0; $c<count($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'payment-methods'}->{'payment-method'}); $c++)											
										{
											$iErrors = ($iErrors && $obj_mPoint->saveCardAccess(
																				$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'payment-methods'}->{'payment-method'}[$c]["id"],
																				$clientid,
																				$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'payment-methods'}->{'payment-method'}[$c]["type-id"],
																				$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'payment-methods'}->{'payment-method'}[$c]["psp-id"],
																				$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'payment-methods'}->{'payment-method'}[$c]["country-id"],
																				$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'payment-methods'}->{'payment-method'}[$c]["state-id"]
																				));
										}
									}
									if (($isClientCardError && $iErrors) == false )
									{
										$_OBJ_DB->query("ROLLBACK");
										header("HTTP/1.1 500 internal server error");
		                                $xml .= '<status code="500">Error during save card access</status>';
									}								
									else
									{
										$obj_mPoint->disableKeyword($clientid);
										
										$iErrors = $obj_mPoint->saveKeyword(
																			$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->keyword["id"],
																			$clientid, 
																			$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->keyword
																			);
									
										if ($iErrors == false )
										{
											$_OBJ_DB->query("ROLLBACK");
											header("HTTP/1.1 500 internal server error");
	                                        $xml .= '<status code="500">Error during save keywords</status>';
										}									
										else
										{
											$obj_mPoint->disableURLs($clientid);
											
											for($u = 0; $u<count($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->urls->url); $u++)
											{
										
												$iErrors = ($iErrors && $obj_mPoint->saveURL(
																			$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->urls->url[$u]["id"],
																			$clientid,
																			$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->urls->url[$u]["type-id"],
																			$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->urls->url[$u]));
											}
											if ($iErrors == false )
											{
												$_OBJ_DB->query("ROLLBACK");
												header("HTTP/1.1 500 internal server error");
												$xml .= '<status code="500">Error during save url</status>';
											}
											else
											{
												$obj_mPoint->disableIINRanges($clientid);
												
												for($in = 0; $in<count($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'issuer-identification-number-ranges'}->{'issuer-identification-number-range'}); $in++)
												{
											
													$iErrors = ($iErrors && $obj_mPoint->saveIINRange(
																								$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'issuer-identification-number-ranges'}->{'issuer-identification-number-range'}[$in]["id"],
																								$clientid,
																								$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'issuer-identification-number-ranges'}->{'issuer-identification-number-range'}[$in]["action-id"],
																								$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'issuer-identification-number-ranges'}->{'issuer-identification-number-range'}[$in]->min,
																								$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'issuer-identification-number-ranges'}->{'issuer-identification-number-range'}[$in]->max
																								));
												}
												if ($iErrors == false )
												{
													$_OBJ_DB->query("ROLLBACK");
													header("HTTP/1.1 500 internal server error");
													$xml .= '<status code="500">Error during save IIN Range</status>';
												}
												else
												{
													$_OBJ_DB->query("COMMIT");
		                                            $xml .= '<status code="100" client-id="'. $clientid .'">OK</status>';
												}
											}
																				
										}	
										
									}
									
								}						
							}
						}
						catch (Exception $e)
						{
							$_OBJ_DB->query("ROLLBACK");
							header("HTTP/1.1 500 internal server error");	
	                        $xml .= '<status code="500">'. $e->getMessage() .'</status>';
						}	
					}
				}
			}
			else
			{
				header("HTTP/1.1 400 Bad Request");
		
				foreach ($aMsgCodes as $clientid => $codeset)
				{
					foreach($codeset as $type => $code)
					{
						$xml .= '<status code="'. $code .'" > For Client '. $clientid .' and '. $type .'</status>' ;
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
