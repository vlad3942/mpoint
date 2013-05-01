<?php
/**
 *
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package mOrder
 * @subpackage API
 */

// Require include file for including all Shared and General APIs
require_once("../../inc/include.php");

// Require Business logic for the User Administration System
require_once(sCLASS_PATH ."/mLookup.php");
// Require Business logic for the mCoupon module
require_once(sCLASS_PATH ."mCoupon.php");
// Require Business logic for the mOrder module
require_once(sCLASS_PATH ."mOrder.php");
// Require Business logic for the mProfile module
require_once(sCLASS_PATH ."/profile.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
// Require Data Class for Client Information
require_once(sCLASS_PATH ."/clientinfo.php");

// Initialize Standard content Object
$obj_mRetail = new Profile($_OBJ_DB, $_OBJ_TXT);
/*
$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<address country-id="100">';
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

if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."morder.xsd") === true)
{
	for ($i=0; $i<count($obj_DOM->address); $i++)
	{
		$aErrCd = array();
		if (count($obj_DOM->address[$i]->{'client-info'}->mobile) == 1)
		{
			$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->address[$i]->{'client-info'}->mobile["country-id"]);
			if (is_null($obj_CountryConfig) === true) { $aErrCd[] = 81; }
		}
		else { $obj_CountryConfig = $obj_mRetail->getClientConfig()->getCountryConfig(); }
		$obj_Validator = new Validate($obj_CountryConfig);
		// Validate Mobile Number
		if (is_null($obj_CountryConfig) === false && count($obj_DOM->address[$i]->{'client-info'}->mobile) == 1)
		{
			if ($obj_Validator->valMobile( (float) $obj_DOM->address[$i]->{'client-info'}->mobile) < 10) { $aErrCd[] = $obj_Validator->valMobile( (float) $obj_DOM->address[$i]->{'client-info'}->mobile) + 81; }
		}
		if ($obj_DOM->address[$i]["country-id"] != $obj_CountryConfig->getID() )
		{
			$obj_Validator = new Validate(CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->address[$i]["country-id"]) );
		}
		
		// Set Defaults
		if (empty($obj_DOM->address[$i]["save"]) === true) { $obj_DOM->address[$i]["save"] = "true"; }
		if (count($obj_DOM->address[$i]->state) == 0) { $obj_DOM->address[$i]->state = "N/A"; }

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
			if ($obj_Validator->valName( (string) $obj_DOM->address[$i]->{'first-name'}) < 10) { $aErrCd[] = $obj_Validator->valName( (string) $obj_DOM->address[$i]->{'first-name'}) + 10; }
		}
		// Validate Last Name
		if (count($obj_DOM->address[$i]->{'last-name'}) == 1)
		{
			if ($obj_Validator->valName( (string) $obj_DOM->address[$i]->{'last-name'}) < 10) { $aErrCd[] = $obj_Validator->valName( (string) $obj_DOM->address[$i]->{'last-name'}) + 20; }
		}
		// Validate required input
		if ($obj_Validator->valName( (string) $obj_DOM->address[$i]->street) < 10) { $aErrCd[] = $obj_Validator->valName( (string) $obj_DOM->address[$i]->street) + 30; }
		if ($obj_Validator->valState($_OBJ_DB,  (string) $obj_DOM->address[$i]->state) == 10)
		{
			if ($obj_Validator->valPostalCode($_OBJ_DB, (string) $obj_DOM->address[$i]->{'postal-code'}, (string) $obj_DOM->address[$i]->state) < 10) { $aErrCd[] = $obj_Validator->valPostalCode($_OBJ_DB, (string) $obj_DOM->address[$i]->{'postal-code'}, (string) $obj_DOM->address[$i]->state) + 40; }
		}
		else
		{
			if ($obj_Validator->valPostalCode($_OBJ_DB, (string) $obj_DOM->address[$i]->{'postal-code'}) < 10) { $aErrCd[] = $obj_Validator->valPostalCode($_OBJ_DB, (string) $obj_DOM->address[$i]->{'postal-code'}) + 40; }
			$aErrCd[] = $obj_Validator->valState($_OBJ_DB,  (string) $obj_DOM->address[$i]->state) + 60;
		}
		if ($obj_Validator->valName( (string) $obj_DOM->address[$i]->city) < 10) { $aErrCd[] = $obj_Validator->valName( (string) $obj_DOM->address[$i]->city) + 50; }
		// Validate Order
		if (count($obj_DOM->address[$i]->order) == 1)
		{
			if ($obj_Validator->valOrder($_OBJ_DB, (integer) $obj_DOM->address[$i]->order["id"], $obj_mRetail->getClientConfig()->getID(), (string) $obj_DOM->address[$i]->order) < 10) { $aErrCd[] = $obj_Validator->valOrder($_OBJ_DB, (integer) $obj_DOM->address[$i]->order["id"], $obj_mRetail->getClientConfig()->getID(), (string) $obj_DOM->address[$i]->order) + 60; }
		}
		
		// Success: Input valid
		if (count($aErrCd) == 0)
		{
			// Construct Client Info
			$obj_ClientInfo = ClientInfo::produceInfo($obj_DOM->address[$i]->{'client-info'}, $obj_CountryConfig, @$_SERVER['HTTP_X_FORWARDED_FOR']);
			$iCustomerID = $obj_mRetail->getCustomerIDFromMobile($obj_ClientInfo->getCountryConfig()->getID(), $obj_ClientInfo->getMobile() );
			
			if (count($obj_DOM->address[$i]->order) == 0 || $obj_DOM->address[$i]["save"] == "true")
			{
				$bNewAddress = true;
				$obj_XML = simplexml_load_string($obj_mRetail->getAddresses($iCustomerID) );
				
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
				// Address doesn't exist, add to profile
				if ($bExists === false)
				{
					$code = $obj_mRetail->saveAddress($iCustomerID, (integer) $obj_DOM->address[$i]["country-id"], (string) $obj_DOM->address[$i]->{'first-name'}, (string) $obj_DOM->address[$i]->{'last-name'}, (string) $obj_DOM->address[$i]->company, (string) $obj_DOM->address[$i]->street, (string) $obj_DOM->address[$i]->{'postal-code'}, (string) $obj_DOM->address[$i]->city, (string) $obj_DOM->address[$i]->state);
					if ($code >= 10) { $code++; } 
				}
				else { $code = 10; }
			}
			else { $code = 10; }
			
			if ($code >= 10)
			{
				if (count($obj_DOM->address[$i]->order) == 1)
				{
					$iTypeID = (integer) $obj_DOM->address[$i]["type-id"];
					if ( ($iTypeID&mOrder::iBILLING_ADDRESS) == mOrder::iBILLING_ADDRESS)
					{
						$c = $obj_mRetail->saveAddressForOrder( (integer) $obj_DOM->address[$i]->order["id"], mOrder::iBILLING_ADDRESS, (integer) $obj_DOM->address[$i]["country-id"], (string) $obj_DOM->address[$i]->{'first-name'}, (string) $obj_DOM->address[$i]->{'last-name'}, (string) $obj_DOM->address[$i]->company, (string) $obj_DOM->address[$i]->street, (string) $obj_DOM->address[$i]->{'postal-code'}, (string) $obj_DOM->address[$i]->city, (string) $obj_DOM->address[$i]->state);
						if ($c < 10) { $code = $c + 1; }
						else { $code += 2 + $c - 10; }
					}
					if ( ($iTypeID&mOrder::iSHIPPING_ADDRESS) == mOrder::iSHIPPING_ADDRESS)
					{
						$c = $obj_mRetail->saveAddressForOrder( (integer) $obj_DOM->address[$i]->order["id"], mOrder::iSHIPPING_ADDRESS, (integer) $obj_DOM->address[$i]["country-id"], (string) $obj_DOM->address[$i]->{'first-name'}, (string) $obj_DOM->address[$i]->{'last-name'}, (string) $obj_DOM->address[$i]->company, (string) $obj_DOM->address[$i]->street, (string) $obj_DOM->address[$i]->{'postal-code'}, (string) $obj_DOM->address[$i]->city, (string) $obj_DOM->address[$i]->state);
						if ($c < 10) { $code = $c + 1; }
						elseif ( ($iTypeID&mOrder::iBILLING_ADDRESS) != mOrder::iBILLING_ADDRESS) { $code += 2 + $c - 10; }
					}
				}
				// Customer Data should be saved in Client System
				if ($code >= 10 && $obj_mRetail->getClientConfig()->getCustomerExportURL() != "")
				{
					$aURL_Info = parse_url($obj_mRetail->getClientConfig()->getCustomerExportURL() );
					$aHTTP_CONN_INFO["mesb"]["protocol"] = $aURL_Info["scheme"];
					$aHTTP_CONN_INFO["mesb"]["host"] = $aURL_Info["host"];
					$aHTTP_CONN_INFO["mesb"]["port"] = $aURL_Info["port"];
					$aHTTP_CONN_INFO["mesb"]["path"] = $aURL_Info["path"];
					if (array_key_exists("query", $aURL_Info) === true) { $aHTTP_CONN_INFO["mesb"]["path"] .= "?". $aURL_Info["query"]; }
					$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["mesb"]);
					try 
					{
						// Save Customer Data in External system
						if ($obj_mRetail->export($obj_ConnInfo, $obj_ClientInfo, $iCustomerID, (integer) $obj_DOM->address[$i]["country-id"], (string) $obj_DOM->address[$i]->{'first-name'}, (string) $obj_DOM->address[$i]->{'last-name'}, (string) $obj_DOM->address[$i]->company, (string) $obj_DOM->address[$i]->street, (string) $obj_DOM->address[$i]->{'postal-code'}, (string) $obj_DOM->address[$i]->city, (string) $obj_DOM->address[$i]->state) == 10)
						{
							$xml .= str_replace('<?xml version="1.0"?>', '', $obj_DOM->asXML() );
						}
						// Error: Invalid Response received from External System
						else { $code = 8; }
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
				switch ($code)
				{
				case (6):
					header("HTTP/1.1 504 Gateway Timeout");
						
					$xml = '<status code="'. ($code+90) .'">Unable to connect to External System</status>';
					break;
				case (7):
					header("HTTP/1.1 504 Gateway Timeout");
						
					$xml = '<status code="'. ($code+90) .'">No response received from External System</status>';
					break;
				case (8):
					header("HTTP/1.1 502 Bad Gateway");
						
					$xml = '<status code="'. ($code+90) .'">Invalid Response received from External System</status>';
					break;
				case (10):
					$xml = '<status code="'. ($code+90) .'">Nothing to do</status>';
					break;
				case (11):
					$xml = '<status code="'. ($code+90) .'">Address successfully saved</status>';
					break;
				case (12):
					$xml = '<status code="'.  ($code+90) .'">Address successfully added to order</status>';
					break;
				case (13):
					$xml = '<status code="'.  ($code+90) .'">Address saved and added to order</status>';
					break;
				default:	// Error: Unable to add address to order
					header("HTTP/1.1 500 Internal Server Error");
					
					$xml = '<status code="'.  ($code+90) .'">Unable to add address to order</status>';
					break;
				}
			}
			// Error: Unable to save address
			else
			{
				header("HTTP/1.1 500 Internal Server Error");
				
				$xml = '<status code="'.  ($code+90) .'">Unable to save address</status>';
			}
		}
		// Error: Invalid Input
		else
		{
			header("HTTP/1.1 400 Bad Request");
		
			$xml = '';
			foreach ($aErrCd as $code)
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