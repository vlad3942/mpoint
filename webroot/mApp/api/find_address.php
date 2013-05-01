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

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
// Require Data Class for Client Information
require_once(sCLASS_PATH ."/clientinfo.php");

// Initialize Standard content Object
$obj_mRetail = new mOrder($_OBJ_DB, $_OBJ_TXT);
/*
$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<address country-id="100">';
$HTTP_RAW_POST_DATA .= '<full-name>Jonatan Buus</full-name>';
//$HTTP_RAW_POST_DATA .= '<first-name>Jonatan Evald</first-name>';
//$HTTP_RAW_POST_DATA .= '<last-name>Buus</last-name>';
$HTTP_RAW_POST_DATA .= '<company>Buus</company>';
$HTTP_RAW_POST_DATA .= '<street>Dexter Gordons Vej 3, 6.th</street>';
$HTTP_RAW_POST_DATA .= '<postal-code>2450</postal-code>';
$HTTP_RAW_POST_DATA .= '<city>'. utf8_encode("København SV") .'</city>';
//$HTTP_RAW_POST_DATA .= '<state>N/A</state>';
$HTTP_RAW_POST_DATA .= '<client-info app-id="3" platform="iOS" version="2.10" language="gb">';
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
	for ($i=0; $i<count($obj_DOM->{'find-address'}); $i++)
	{
		$aErrCd = array();
		if (count($obj_DOM->{'find-address'}[$i]->{'client-info'}->mobile) == 1)
		{
			$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'find-address'}[$i]->{'client-info'}->mobile["country-id"]);
			if (is_null($obj_CountryConfig) === true) { $aErrCd[] = 81; }
		}
		else { $obj_CountryConfig = $obj_mRetail->getClientConfig()->getCountryConfig(); }
		$obj_Validator = new Validate($obj_CountryConfig);
		// Validate Mobile Number
		if (is_null($obj_CountryConfig) === false && count($obj_DOM->{'find-address'}[$i]->{'client-info'}->mobile) == 1)
		{
			if ($obj_Validator->valMobile( (float) $obj_DOM->{'find-address'}[$i]->{'client-info'}->mobile) < 10) { $aErrCd[] = $obj_Validator->valMobile( (float) $obj_DOM->{'find-address'}[$i]->{'client-info'}->mobile) + 81; }
		}
		
		// Validate Phone Number
		$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'find-address'}[$i]->{'phone-number'}["country-id"]);
		if ( ($obj_CountryConfig instanceof CountryConfig) === true) 
		{
			$obj_Validator = new Validate($obj_CountryConfig);
			if ($obj_Validator->valMobile( (float) $obj_DOM->{'find-loyalty-cards'}[$i]->{'phone-number'}) < 10) { $aErrCd[] = $obj_Validator->valMobile( (float) $obj_DOM->{'find-loyalty-cards'}[$i]->{'phone-number'}) + 11; }
		}
		else { $aErrCd[] = 11; }
		
		// Success: Input valid
		if (count($aErrCd) == 0)
		{
			// Construct Client Info
			$obj_ClientInfo = ClientInfo::produceInfo($obj_DOM->{'find-address'}[$i]->{'client-info'}, $obj_CountryConfig, @$_SERVER['HTTP_X_FORWARDED_FOR']);
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