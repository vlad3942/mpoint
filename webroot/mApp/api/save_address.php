<?php
/**
 *
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package mApp
 * @subpackage API
 */

// Require include file for including all Shared and General APIs
require_once("../../inc/include.php");

// Require the PHP API for handling the connection to GoMobile
require_once(sAPI_CLASS_PATH ."/gomobile.php");
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require Business logic for the My Account component
require_once(sCLASS_PATH ."/my_account.php");
// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");

// Initialize Standard content Object
$obj_mPoint = new MyAccount($_OBJ_DB, $_OBJ_TXT);
/*
$_SERVER['PHP_AUTH_USER'] = "CPMDemo";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<address client-id="10007" account="100007" country-id="100">';
$HTTP_RAW_POST_DATA .= '<full-name>Jonatan Buus</full-name>';
//$HTTP_RAW_POST_DATA .= '<first-name>Jonatan Evald</first-name>';
//$HTTP_RAW_POST_DATA .= '<last-name>Buus</last-name>';
$HTTP_RAW_POST_DATA .= '<company>CellPoint Mobile</company>';
$HTTP_RAW_POST_DATA .= '<street>Dexter Gordons Vej 3, 6.th</street>';
$HTTP_RAW_POST_DATA .= '<postal-code>2450</postal-code>';
$HTTP_RAW_POST_DATA .= '<city>'. utf8_encode("København SV") .'</city>';
//$HTTP_RAW_POST_DATA .= '<state>N/A</state>';
$HTTP_RAW_POST_DATA .= '<client-info app-id="4" platform="iOS" version="1.00" language="gb">';
$HTTP_RAW_POST_DATA .= '<mobile country-id="100">28882861</mobile>';
$HTTP_RAW_POST_DATA .= '<email>jona@oismail.com</email>';
$HTTP_RAW_POST_DATA .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
$HTTP_RAW_POST_DATA .= '</client-info>';
$HTTP_RAW_POST_DATA .= '</address>';
$HTTP_RAW_POST_DATA .= '</root>';
*/
$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mpoint.xsd") === true)
{
	for ($i=0; $i<count($obj_DOM->address); $i++)
	{
		// Set Global Defaults
		if (empty($obj_DOM->address[$i]["account"]) === true || intval($obj_DOM->address[$i]["account"]) < 1) { $obj_DOM->address[$i]["account"] = -1; }
		
		// Validate basic information
		$code = Validate::valBasic($_OBJ_DB, (integer) $obj_DOM->address[$i]["client-id"], (integer) $obj_DOM->address[$i]["account"]);
		if ($code == 100)
		{
			$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->address[$i]["client-id"], (integer) $obj_DOM->address[$i]["account"]);

			// Client successfully authenticated
			if ($obj_ClientConfig->getUsername() == trim($_SERVER['PHP_AUTH_USER']) && $obj_ClientConfig->getPassword() == trim($_SERVER['PHP_AUTH_PW']) )
			{
				$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->address[$i]->{'client-info'}->mobile["country-id"]);
				if ( ($obj_CountryConfig instanceof CountryConfig) === false) { $obj_CountryConfig = $obj_ClientConfig->getCountryConfig(); }
				if (count($obj_DOM->address[$i]->{'client-info'}->mobile) == 1)
				{
					$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->address[$i]->{'client-info'}->mobile["country-id"]);
					if (is_null($obj_CountryConfig) === true) { $aMsgCds[] = 81; }
				}
				else { $obj_CountryConfig = $obj_mPoint->getClientConfig()->getCountryConfig(); }
				$obj_Validator = new Validate($obj_CountryConfig);
				// Validate Mobile Number
				if (is_null($obj_CountryConfig) === false && count($obj_DOM->address[$i]->{'client-info'}->mobile) == 1)
				{
					if ($obj_Validator->valMobile( (float) $obj_DOM->address[$i]->{'client-info'}->mobile) < 10) { $aMsgCds[] = $obj_Validator->valMobile( (float) $obj_DOM->address[$i]->{'client-info'}->mobile) + 81; }
				}
				if ($obj_DOM->address[$i]["country-id"] != $obj_CountryConfig->getID() )
				{
					$obj_Validator = new Validate(CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->address[$i]["country-id"]) );
				}
				$obj_mPoint = new MyAccount($_OBJ_DB, $_OBJ_TXT, $obj_CountryConfig);
				// Seperate Full Name into First- and Last Name
				if (count($obj_DOM->address[$i]->{'full-name'}) == 1)
				{
					$obj_DOM->address[$i]->{'full-name'} = trim($obj_DOM->address[$i]->{'full-name'});
					$pos = strrpos($obj_DOM->address[$i]->{'full-name'}, " ");
					if ($pos === false) { $pos = strlen($obj_DOM->address[$i]->{'full-name'}); }
					else { $obj_DOM->address[$i]->{'last-name'} = substr($obj_DOM->address[$i]->{'full-name'}, $pos + 1); }
					$obj_DOM->address[$i]->{'first-name'} = substr($obj_DOM->address[$i]->{'full-name'}, 0 , $pos);
				}
				// Validate First Name
				if (count($obj_DOM->address[$i]->{'first-name'}) == 1)
				{
					if ($obj_Validator->valName( (string) $obj_DOM->address[$i]->{'first-name'}) < 10) { $aMsgCds[] = $obj_Validator->valName( (string) $obj_DOM->address[$i]->{'first-name'}) + 20; }
				}
				// Validate Last Name
				if (count($obj_DOM->address[$i]->{'last-name'}) == 1)
				{
					if ($obj_Validator->valName( (string) $obj_DOM->address[$i]->{'last-name'}) < 10) { $aMsgCds[] = $obj_Validator->valName( (string) $obj_DOM->address[$i]->{'last-name'}) + 40; }
				}
				// Validate required input
				if ($obj_Validator->valName( (string) $obj_DOM->address[$i]->street) < 10) { $aMsgCds[] = $obj_Validator->valName( (string) $obj_DOM->address[$i]->street) + 50; }
				if ($obj_Validator->valState($_OBJ_DB,  (string) $obj_DOM->address[$i]->state) == 10)
				{
					if ($obj_Validator->valPostalCode($_OBJ_DB, (string) $obj_DOM->address[$i]->{'postal-code'}, (string) $obj_DOM->address[$i]->state) < 10) { $aMsgCds[] = $obj_Validator->valPostalCode($_OBJ_DB, (string) $obj_DOM->address[$i]->{'postal-code'}, (string) $obj_DOM->address[$i]->state) + 60; }
				}
				else
				{
					if ($obj_Validator->valPostalCode($_OBJ_DB, (string) $obj_DOM->address[$i]->{'postal-code'}) < 10) { $aMsgCds[] = $obj_Validator->valPostalCode($_OBJ_DB, (string) $obj_DOM->address[$i]->{'postal-code'}) + 60; }
					$aMsgCds[] = $obj_Validator->valState($_OBJ_DB,  (string) $obj_DOM->address[$i]->state) + 70;
				}
				if ($obj_Validator->valName( (string) $obj_DOM->address[$i]->city) < 10) { $aMsgCds[] = $obj_Validator->valName( (string) $obj_DOM->address[$i]->city) + 80; }
				
				
				// Success: Input valid
				if (count($aMsgCds) == 0)
				{
					$iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_DOM->address[$i]->{'client-info'}->mobile, $obj_CountryConfig);
					if ($iAccountID < 0 && count($obj_DOM->address[$i]->{'client-info'}->email) == 1) { $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_DOM->address[$i]->{'client-info'}->email, $obj_CountryConfig); }
					if ($iAccountID < 0) { $iAccountID = $obj_mPoint->getAccountID($obj_CountryConfig, $obj_DOM->address[$i]->{'client-info'}->mobile); }
					if ($iAccountID < 0 && count($obj_DOM->address[$i]->{'client-info'}->email) == 1) { $iAccountID = $obj_mPoint->getAccountID($obj_CountryConfig, $obj_DOM->address[$i]->{'client-info'}->email); }
					$code = General::authToken($iAccountID, $obj_ClientConfig->getSecret(), $_COOKIE['token']);
					// Authentication succeeded
					if ($code >= 10)
					{
						// Generate new security token
						if ($code == 11) { setcookie("token", General::genToken($iAccountID, $obj_ClientConfig->getSecret() ) ); }
						/*
						 * TODO
						 $iCustomerID = $obj_mPoint->getCustomerIDFromMobile($obj_ClientInfo->getCountryConfig()->getID(), $obj_ClientInfo->getMobile() );
							
						$bNewAddress = true;
						$obj_XML = simplexml_load_string($obj_mPoint->getAddresses($iCustomerID) );
							
						// Assume address doesn't exist
						$bExists = false;
						for ($j=0; $j<count($obj_XML->address); $j++)
						{
						// Check whether address already exists
						if (intval($obj_DOM->address[$i]["country-id"]) == intval($obj_XML->address[$j]->country["id"])
								&& strtolower($obj_DOM->address[$i]->{'first-name'}) == strtolower($obj_XML->address[$j]->{'first-name'})
								&& strtolower($obj_DOM->address[$i]->{'last-name'}) == strtolower($obj_XML->address[$j]->{'last-name'})
								&& strtolower($obj_DOM->address[$i]->street) == strtolower($obj_XML->address[$j]->street)
								&& strtolower($obj_DOM->address[$i]->{'postal-code'}) == strtolower($obj_XML->address[$j]->{'postal-code'})
								&& strtolower($obj_DOM->address[$i]->city) == strtolower($obj_XML->address[$j]->city)
								&& strtolower($obj_DOM->address[$i]->state) == strtolower($obj_XML->address[$j]->state) )
						{
						$bExists = true;
						// Break out of loop as match has been found
						$j = count($obj_XML->address);
						}
						}
						*/
						// Address doesn't exist, add to profile
						if ($bExists === false)
						{
							$code = $obj_mPoint->saveAddress($iAccountID, (integer) $obj_DOM->address[$i]["country-id"], (string) $obj_DOM->address[$i]->{'first-name'}, (string) $obj_DOM->address[$i]->{'last-name'}, (string) $obj_DOM->address[$i]->company, (string) $obj_DOM->address[$i]->street, (string) $obj_DOM->address[$i]->{'postal-code'}, (string) $obj_DOM->address[$i]->city, (string) $obj_DOM->address[$i]->state);
							if ($code >= 10) { $code++; }
						}
						else { $code = 10; }
							
						switch ($code)
						{
						case (10):
							$xml = '<status code="'. ($code+90) .'">Nothing to do</status>';
							break;
						case (11):
							$xml = '<status code="'. ($code+90) .'">Address successfully saved</status>';
							break;
						default:	// Error: Unable to save address
							header("HTTP/1.1 500 Internal Server Error");
								
							$xml = '<status code="'.  ($code+90) .'">Unable to save address</status>';
							break;
						}
					}
					// Authentication failed
					else
					{
						header("HTTP/1.1 403 Forbidden");
							
						$xml = '<status code="38">Invalid Security Token: '. $_COOKIE['token'] .'</status>';
					}
				}
				// Error: Invalid Input
				else
				{
					header("HTTP/1.1 400 Bad Request");
				
					$xml = '';
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
		// Error: Invalid Input
		else
		{
			header("HTTP/1.1 400 Bad Request");
		
			$xml = '';
			foreach ($aMsgCds as $code)
			{
				$xml .= '<status code="'. $code .'" />';
			}
		}
	}
	$xml = '<?xml version="1.0" encoding="UTF-8"?><root>'. $xml .'</root>';
}
// Error: Invalid XML Document
elseif ( ($obj_DOM instanceof SimpleDOMElement) === false)
{
	header("HTTP/1.1 415 Unsupported Media Type");
	
	$xml = '<?xml version="1.0" encoding="UTF-8"?>';
	$xml .= '<root>';
	$xml .= '<status code="415">Invalid XML Document</status>';
	$xml .= '</root>';
}
// Error: Invalid Input
else
{
	header("HTTP/1.1 400 Bad Request");
	$aObj_Errs = libxml_get_errors();
	
	$xml = '<?xml version="1.0" encoding="UTF-8"?>';
	$xml .= '<root>';
	for ($i=0; $i<count($aObj_Errs); $i++)
	{
		$xml .= '<status code="400">'. htmlspecialchars($aObj_Errs[$i]->message, ENT_NOQUOTES) .'</status>';
	}
	$xml .= '</root>';
}

header("Content-Type: text/xml; charset=UTF-8");
header("Content-Length: ". strlen($xml) );
header("Connection: close");

echo $xml;
?>