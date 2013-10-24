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

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );
/*
$_SERVER['PHP_AUTH_USER'] = "CPMDemo";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .=  '<save-client-configuration>';
<!-- Save in Client.Client_Tbl -->
$HTTP_RAW_POST_DATA .=   '<client-config store-card="3" auto-capture="true" id="10001">';
$HTTP_RAW_POST_DATA .=    '<name>Emirates - IBE</name>';
$HTTP_RAW_POST_DATA .=    '<username>10000000</username>';
$HTTP_RAW_POST_DATA .=    '<password>99999999</password>';
<!-- Store in Client.URL_Tbl or URL fields in Client.Client_Tbl -->
$HTTP_RAW_POST_DATA .=    '<urls>';
$HTTP_RAW_POST_DATA .=     '<url type-id="1">http://mpoint.test.cellpointmobile.com/home/accept.php</url>';
$HTTP_RAW_POST_DATA .=     '<url type-id="2">http://mpoint.test.cellpointmobile.com/_test/auth.php</url>';
$HTTP_RAW_POST_DATA .=    '</urls>';
<!-- Save in Client.Keyword_Tbl -->
$HTTP_RAW_POST_DATA .=    '<keyword>EK</keyword>';
<!-- Save in Client.CardAccess_Tbl -->
$HTTP_RAW_POST_DATA .=    '<cards>';
$HTTP_RAW_POST_DATA .=     '<card id="6" psp-id="7" country-id="100">VISA</card>';
$HTTP_RAW_POST_DATA .=     '<card id="7" psp-id="7" country-id="100">MasterCard</card>';
$HTTP_RAW_POST_DATA .=    '</cards>';
<!-- Save in Client.MerchantAccount_Tbl -->
$HTTP_RAW_POST_DATA .=    '<payment-service-providers>';
$HTTP_RAW_POST_DATA .=     '<payment-service-provider id="7">';
$HTTP_RAW_POST_DATA .=      '<name>IBE</name>';
$HTTP_RAW_POST_DATA .=      '<username>IBE</username>';
$HTTP_RAW_POST_DATA .=      '<password>IBE</password>';
$HTTP_RAW_POST_DATA .=     '</payment-service-provider>';
$HTTP_RAW_POST_DATA .=    '</payment-service-providers>';
<!-- Save in Client.Account_Tbl -->
$HTTP_RAW_POST_DATA .=    '<accounts>';
$HTTP_RAW_POST_DATA .=     '<account id="100010">';
$HTTP_RAW_POST_DATA .=      '<name>Web</name>';
$HTTP_RAW_POST_DATA .=      '<markup>App</markup>';
<!-- Save in Client.MerchantSubAccount_Tbl -->
$HTTP_RAW_POST_DATA .=      '<payment-service-providers>';
$HTTP_RAW_POST_DATA .=       '<payment-service-provider id="7">';
$HTTP_RAW_POST_DATA .=        '<name>IBE</name>';
$HTTP_RAW_POST_DATA .=       '</payment-service-provider>';
$HTTP_RAW_POST_DATA .=      '</payment-service-providers>';
$HTTP_RAW_POST_DATA .=     '</account>';
$HTTP_RAW_POST_DATA .=    '</accounts>';
$HTTP_RAW_POST_DATA .=   '</client-config>';
$HTTP_RAW_POST_DATA .=  '</save-client-configuration>';
$HTTP_RAW_POST_DATA .= '</root>';
*/
$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mpoint.xsd") === true && count($obj_DOM->{'save-client-configuration'}) > 0)
	{					
		for ($i=0; $i<count($obj_DOM->{'save-client-configuration'}); $i++)
		{
			for ($j=0; $j<count($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}); $j++)
			{										
				if (empty($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]["store-card"]) === false)
				{
				   	is_int($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]["store-card"]);
				}
				if (empty($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]["auto-capture"]) === false)
				{
					is_bool($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]["auto-capture"]);
				}
				if (empty($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]["id"]) === false)
				{
					is_int($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]["id"]);
				}
				if (empty($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->name) === false)
				{
					is_string($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->name);
				}
				if (empty($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->username) === false)
				{
					is_string($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->username);
				}
				if (empty($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->password) === false)
				{
					is_string($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->password);
				}
				
				   		//TODO update/insert 
						//<!-- Save in Client.Client_Tbl -->
				
				if (empty($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->urls) === false)
				{
					for ($k=0; $k<count($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->urls->url); $k++)
					{
						if (empty($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->urls->url[$k]) === false)
						{
							
							// TODO update/insert
							//<!-- Store in Client.URL_Tbl or URL fields in Client.Client_Tbl -->
						}
					}
				}
				if (empty($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->keyword) === false)
				{
					//<!-- Save in Client.Keyword_Tbl -->
				}
				if (empty($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->cards) === false)
				{
					for ($l=0; $l<count($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->cards->card); $l++)
					{
						if (empty($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->cards->card[$l]) === false)
						{
						    
							if (empty($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->cards->card[$l]["id"]) === false)
							{
								// TODO : Validate input
							}
							if (empty($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->cards->card[$l]["psp-id"]) === false)
							{
								// TODO : Validate input
							}
							if (empty($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->cards->card[$l]["country-id"]) === false)
							{
								// TODO : Validate input
							}	
							
								//TODO update/insert
						    	//<!-- Save in Client.CardAccess_Tbl -->
						    
						}
					}
				}
				if (empty($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'payment-service-providers'}) === false)
				{
					for ($m=0; $m<count($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'payment-service-providers'}->{'payment-service-provider'}); $m++)
					{
						if (empty($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'payment-service-providers'}->{'payment-service-provider'}[$m]) === false)
						{
							if (empty($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'payment-service-providers'}->{'payment-service-provider'}[$m]->name) === false)
							{
								// TODO : Validate input
							}
							if (empty($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'payment-service-providers'}->{'payment-service-provider'}[$m]->username) === false)
							{
								// TODO : Validate input
							}
							if (empty($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'payment-service-providers'}->{'payment-service-provider'}[$m]->password) === false)
							{
								// TODO : Validate input
							}
							
								//TODO update/insert
								//<!-- Save in Client.MerchantAccount_Tbl -->
						}
					}
				}
				if (empty($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'accounts'}) === false)
				{
					for ($a=0; $a<count($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'accounts'}->{'account'}); $a++)
					{
						if (empty($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'accounts'}->{'account'}[$a]) === false)
						{
							if (empty($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'accounts'}->{'account'}[$a]["id"]) === false)
							{
								// TODO : Validate input
							}
							if (empty($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'accounts'}->{'account'}[$a]->name) === false)
							{
								// TODO : Validate input
							}
							if (empty($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'accounts'}->{'account'}[$a]->markup) === false)
							{
								// TODO : Validate input
							}
								//TODO update/insert
								//<!-- Save in Client.Account_Tbl -->

							if (empty($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'accounts'}->{'account'}[$a]->{'payment-service-providers'}) === false)
							{
								for ($n=0; $n<count($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'accounts'}->{'account'}[$a]->{'payment-service-providers'}->{'payment-service-provider'}); $n++)
								{
									if (empty($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'accounts'}->{'account'}[$a]->{'payment-service-providers'}->{'payment-service-provider'}[$n]) === false)
									{
										if (empty($obj_DOM->{'save-client-configuration'}[$i]->{'client-config'}[$j]->{'accounts'}->{'account'}[$a]->{'payment-service-providers'}->{'payment-service-provider'}[$n]->name) === false)
										{
											//TODO update/insert
											//<!-- Save in Client.MerchantSubAccount_Tbl -->
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