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

require_once(sCLASS_PATH ."/admin.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );

/*
$_SERVER['PHP_AUTH_USER'] = "CPMDemo";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";
 
$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .=  '<save-client-configuration>';

$HTTP_RAW_POST_DATA .=   '<client-config id="10025" store-card="3" auto-capture="true" country-id="100">';
$HTTP_RAW_POST_DATA .=    '<name>Emirates - IBE</name>';
$HTTP_RAW_POST_DATA .=    '<username>10000000</username>';
$HTTP_RAW_POST_DATA .=    '<password>99999999</password>';
$HTTP_RAW_POST_DATA .=    '<urls>';
$HTTP_RAW_POST_DATA .=     '<url type-id="1">http://mpoint.test.cellpointmobile.com/home/accept.php</url>';
$HTTP_RAW_POST_DATA .=     '<url type-id="2">http://mpoint.test.cellpointmobile.com/_test/auth.php</url>';
$HTTP_RAW_POST_DATA .=    '</urls>';
$HTTP_RAW_POST_DATA .=    '<keyword>EK</keyword>';
$HTTP_RAW_POST_DATA .=    '<cards>';
$HTTP_RAW_POST_DATA .=     '<card id="6" psp-id="7" country-id="100">VISA</card>';
$HTTP_RAW_POST_DATA .=     '<card id="7" psp-id="7" country-id="100">MasterCard</card>';
$HTTP_RAW_POST_DATA .=    '</cards>';
$HTTP_RAW_POST_DATA .=    '<payment-service-providers>';
$HTTP_RAW_POST_DATA .=     '<payment-service-provider id="7">';
$HTTP_RAW_POST_DATA .=      '<name>IBE</name>';
$HTTP_RAW_POST_DATA .=      '<username>IBE</username>';
$HTTP_RAW_POST_DATA .=      '<password>IBE</password>';
$HTTP_RAW_POST_DATA .=     '</payment-service-provider>';
$HTTP_RAW_POST_DATA .=    '</payment-service-providers>';
$HTTP_RAW_POST_DATA .=    '<accounts>';
$HTTP_RAW_POST_DATA .=     '<account id = "1">';
$HTTP_RAW_POST_DATA .=      '<name>Web</name>';
$HTTP_RAW_POST_DATA .=      '<markup>App</markup>';
$HTTP_RAW_POST_DATA .=      '<payment-service-providers>';
$HTTP_RAW_POST_DATA .=       '<payment-service-provider id="7">';
$HTTP_RAW_POST_DATA .=        '<name>IBE</name>';
$HTTP_RAW_POST_DATA .=       '</payment-service-provider>';
$HTTP_RAW_POST_DATA .=      '</payment-service-providers>';
$HTTP_RAW_POST_DATA .=     '</account>';
$HTTP_RAW_POST_DATA .=    '</accounts>';
$HTTP_RAW_POST_DATA .=   '</client-config>';

$HTTP_RAW_POST_DATA .=   '<client-config store-card="3" auto-capture="true" country-id="100">';
$HTTP_RAW_POST_DATA .=    '<name>Emirates - IBE</name>';
$HTTP_RAW_POST_DATA .=    '<username>user</username>';
$HTTP_RAW_POST_DATA .=    '<password>pass</password>';
$HTTP_RAW_POST_DATA .=    '<urls>';
$HTTP_RAW_POST_DATA .=     '<url type-id="1">http://mpoint.test.cellpointmobile.com/home/accept.php</url>';
$HTTP_RAW_POST_DATA .=    '</urls>';
$HTTP_RAW_POST_DATA .=    '<keyword>EK</keyword>';
$HTTP_RAW_POST_DATA .=    '<cards>';
$HTTP_RAW_POST_DATA .=     '<card id="5" psp-id="7" country-id="100">VISA</card>';
$HTTP_RAW_POST_DATA .=     '<card id="6" psp-id="7" country-id="100">VISA</card>';
$HTTP_RAW_POST_DATA .=     '<card id="7" psp-id="7" country-id="100">MasterCard</card>';
$HTTP_RAW_POST_DATA .=    '</cards>';
$HTTP_RAW_POST_DATA .=    '<payment-service-providers>';
$HTTP_RAW_POST_DATA .=     '<payment-service-provider id="7">';
$HTTP_RAW_POST_DATA .=      '<name>IBE</name>';
$HTTP_RAW_POST_DATA .=      '<username>IBE</username>';
$HTTP_RAW_POST_DATA .=      '<password>IBE</password>';
$HTTP_RAW_POST_DATA .=     '</payment-service-provider>';
$HTTP_RAW_POST_DATA .=     '<payment-service-provider id="8">';
$HTTP_RAW_POST_DATA .=      '<name>IBE2</name>';
$HTTP_RAW_POST_DATA .=      '<username>IBE2</username>';
$HTTP_RAW_POST_DATA .=      '<password>IBE2</password>';
$HTTP_RAW_POST_DATA .=     '</payment-service-provider>';
$HTTP_RAW_POST_DATA .=    '</payment-service-providers>';
$HTTP_RAW_POST_DATA .=    '<accounts>';
$HTTP_RAW_POST_DATA .=     '<account id="12">';
$HTTP_RAW_POST_DATA .=      '<name>Web</name>';
$HTTP_RAW_POST_DATA .=      '<markup>App</markup>';
$HTTP_RAW_POST_DATA .=      '<payment-service-providers>';
$HTTP_RAW_POST_DATA .=       '<payment-service-provider id="7">';
$HTTP_RAW_POST_DATA .=        '<name>IBE</name>';
$HTTP_RAW_POST_DATA .=       '</payment-service-provider>';
$HTTP_RAW_POST_DATA .=      '</payment-service-providers>';
$HTTP_RAW_POST_DATA .=     '</account>';
$HTTP_RAW_POST_DATA .=     '<account id="1">';
$HTTP_RAW_POST_DATA .=      '<name>Web2</name>';
$HTTP_RAW_POST_DATA .=      '<markup>App</markup>';
$HTTP_RAW_POST_DATA .=      '<payment-service-providers>';
$HTTP_RAW_POST_DATA .=       '<payment-service-provider id="7">';
$HTTP_RAW_POST_DATA .=        '<name>IBE</name>';
$HTTP_RAW_POST_DATA .=       '</payment-service-provider>';
$HTTP_RAW_POST_DATA .=       '<payment-service-provider id="8">';
$HTTP_RAW_POST_DATA .=        '<name>IBE3</name>';
$HTTP_RAW_POST_DATA .=       '</payment-service-provider>';
$HTTP_RAW_POST_DATA .=      '</payment-service-providers>';
$HTTP_RAW_POST_DATA .=     '</account>';
$HTTP_RAW_POST_DATA .=    '</accounts>';
$HTTP_RAW_POST_DATA .=   '</client-config>';

$HTTP_RAW_POST_DATA .=  '</save-client-configuration>';
$HTTP_RAW_POST_DATA .= '</root>';
*/

$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mpoint.xsd") === true && count($obj_DOM->{'save-client-configuration'}) > 0)
	{	
		$obj_mPoint = new Admin($_OBJ_DB, $_OBJ_TXT);
		$obj_val = new Validate();
		$valErros = 0;
		//Validating of account and clinent 
		for ($i=0; $i<count($obj_DOM->{'save-client-configuration'}); $i++)
		{
			for ($j=0; $j<count($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}); $j++)
			{										
				if ($obj_val->valBasic($_OBJ_DB, $obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]["id"], -1) == 2 ){ $valErros += 2;  }
				for($a=0; $a<count($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}->accounts->account); $a++)
				{
					if ($obj_val->valBasic($_OBJ_DB, $obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]["id"], $obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}->accounts->account[$a]["id"]) == 12 ){$valErros += 12; }
				}
			}
		}
		if($valErros == 0)
		{			
			for ($i=0; $i<count($obj_DOM->{'save-client-configuration'}); $i++)
			{
				for ($j=0; $j<count($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}); $j++)
				{
					$_OBJ_DB->query("START TRANSACTION");  // START TRANSACTION does not work with Oracle db
					if ($obj_val->valBasic($_OBJ_DB, $obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]["id"], -1) == 100 )
					{
						$clientid = $obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]["id"];	
					}			
					try
					{
						$iErrors = $obj_mPoint->saveClient($clientid,
															$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]["country-id"],
															$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]["store-card"],
													 		$obj_mPoint->xml2bool($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]["auto-capture"]),
													 		$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->name,
									 				 		$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->username,
													 		$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->password);
						
						
						if ($iErrors == false )
						{
							$_OBJ_DB->query("ROLLBACK");
							header("HTTP/1.1 500 internal server error");
                            $xml = '<status code="500">Error during Save Client</status>';
							break;
						}
						else
						{
							$iErrors =	$obj_mPoint->deleteAccount($clientid);
							if ($iErrors == false )
							{							
								$_OBJ_DB->query("ROLLBACK");
								header("HTTP/1.1 500 internal server error");
                                $xml = '<status code="500"></status>';
							}
							else
							{
								for($a=0; $a<count($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->accounts->account); $a++)
								{
                                    $accountid = -1;
									if ($obj_val->valBasic($_OBJ_DB, $obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]["id"], $obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}->accounts->account[$a]["id"]) == 100 )
                                    {
										$accountid = $obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}->accounts->account[$a]["id"];
									}
									$iErrors = $obj_mPoint->saveAccount($accountid,
																	 $clientid,
																	 $obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->accounts->account[$a]->name,
																	 $obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->accounts->account[$a]->markup);
									if ($iErrors == false )
									{
										$_OBJ_DB->query("ROLLBACK");
										header("HTTP/1.1 500 internal server error");
                                        $xml = '<status code="500">Error during Save Account</status>';
									}
									else
									{
										$iErrors = $obj_mPoint->deleteMerchantSubAccount($accountid);
									
										if ($iErrors == false )
										{
											$_OBJ_DB->query("ROLLBACK");
											header("HTTP/1.1 500 internal server error");
                                            $xml = '<status code="500"></status>';
										}
										else
										{
											for($k=0; $k<count($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->accounts->account[$a]->{'payment-service-providers'}->{'payment-service-provider'}); $k++)
											{ 
												$iErrors = $obj_mPoint->saveMerchantSubAccount($accountid,
														$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->accounts->account[$a]->{'payment-service-providers'}->{'payment-service-provider'}[$k]["id"],
														$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->accounts->account[$a]->{'payment-service-providers'}->{'payment-service-provider'}[$k]->name);
										
												if ($iErrors == false )
												{								
													$_OBJ_DB->query("ROLLBACK");
													header("HTTP/1.1 500 internal server error");
                                                    $xml = '<status code="500">Error during save payment service providers for account</status>';
												}
												else{}
											}
										}
									}
								}
								$iErrors = $obj_mPoint->deleteMerchantAccount($clientid);
								if ($iErrors == false )
								{	
									$_OBJ_DB->query("ROLLBACK");
									header("HTTP/1.1 500 internal server error");
                                    $xml = '<status code="500"></status>';
								}
								else
								{
									for($p=0; $p<count($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'payment-service-providers'}->{'payment-service-provider'}); $p++)
									{
										$iErrors = $obj_mPoint->saveMerchantAccount($clientid,
																			 $obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'payment-service-providers'}->{'payment-service-provider'}[$p]["id"],
																			 $obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'payment-service-providers'}->{'payment-service-provider'}[$p]->name,
																			 $obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'payment-service-providers'}->{'payment-service-provider'}[$p]->username,
																			 $obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'payment-service-providers'}->{'payment-service-provider'}[$p]->password);
									}
									if ($iErrors == false )
									{
										$_OBJ_DB->query("ROLLBACK");
										header("HTTP/1.1 500 internal server error");
                                        $xml = '<status code="500">Error during save payment service providers</status>';
									}
									else
									{	
										$iErrors = $obj_mPoint->deleteCardAccess($clientid);
									
										if ($iErrors == false )
										{
											$_OBJ_DB->query("ROLLBACK");
											header("HTTP/1.1 500 internal server error");
                                            $xml = '<status code="500">Delete card error</status>';
										}
										else
										{
											for($c=0; $c<count($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->cards->card); $c++)
											{
												$iErrors = $obj_mPoint->saveCardAccess($clientid,
																					$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->cards->card[$c]["id"],
																					$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->cards->card[$c]["psp-id"],
																					$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->cards->card[$c]["country-id"]);
											}
											if ($iErrors == false )
											{
												$_OBJ_DB->query("ROLLBACK");
												header("HTTP/1.1 500 internal server error");
                                                $xml = '<status code="500">Error during save card access</status>';
											}
											else
											{
												$iErrors = $obj_mPoint->deleteKeyWord($clientid);
											
												if ($iErrors == false )
												{
													$_OBJ_DB->query("ROLLBACK");
													header("HTTP/1.1 500 internal server error");
                                                    $xml = '<status code="500">Error during delete keyword</status>';
												}
												else
												{
													$iErrors = $obj_mPoint->saveKeyWord($clientid, $obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->keyword);
												
													if ($iErrors == false )
													{
														$_OBJ_DB->query("ROLLBACK");
														header("HTTP/1.1 500 internal server error");
                                                        $xml = '<status code="500">Error during save keywords</status>';
													}
													else
													{
														$iErrors = $obj_mPoint->deleteURL($clientid);
													
														if ($iErrors == false )
														{
															$_OBJ_DB->query("ROLLBACK");
															header("HTTP/1.1 500 internal server error");
                                                            $xml = '<status code="500">Error during delete url</status>';
														}
														else
														{
															for($u = 0; $u<count($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->urls->url); $u++)
															{
														
																$iErrors = $obj_mPoint->saveURL($clientid,
																							$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->urls->url[$u]["type-id"],
																							$obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->urls->url[$u]);
															}
															if ($iErrors == false )
															{
																$_OBJ_DB->query("ROLLBACK");
																header("HTTP/1.1 500 internal server error");
																$xml = '<status code="500">Error during save url</status>';
															}
															else
															{
																$_OBJ_DB->query("COMMIT");
                                                                $xml = '<status code="100" client-id="'. $clientid .'">OK</status>';
															}									
														}	
													}
												}
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
                        $xml = '<status code="500">'. $e->getMessage() .'</status>';
					}	
				}
			}
		}
		else
		{
			header("HTTP/1.1 400 Bad Request");
	
			$xml = '<status code="415">Invalid Customer or client ID  </status>';
		}
	}
	// Error: Invalid XML Document
	elseif ( ($obj_DOM instanceof SimpleDOMElement) === false)
	{
		header("HTTP/1.1 415 Unsupported Media Type");
		
		$xml = '<status code="415">Invalid XML Document</status>';
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
