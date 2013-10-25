<?php
/**
 * This file contains the Controller for mPoint's Administration API.
 * The Controller will ensure that all input is validated and the desired country is updated.
 * If the input provided was determined to be invalid, an error status will be returned.
 *
 * @author Tomas Kraina
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package API
 * @subpackage Admin
 * @version 1.10
 */

// Require Global Include File
require_once("../../inc/include.php");

// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );

/*
$_SERVER['PHP_AUTH_USER'] = "CPMDemo";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<save-country-configuration>';
$HTTP_RAW_POST_DATA .= '<countries>';

$HTTP_RAW_POST_DATA .= '<country id="100" address-lookup="true">';
$HTTP_RAW_POST_DATA .= '<name>Denmark</name>';
$HTTP_RAW_POST_DATA .= '<min-mobile>10000000</min-mobile>';
$HTTP_RAW_POST_DATA .= '<max-mobile>99999999</max-mobile>';
$HTTP_RAW_POST_DATA .= '<currency>DKK</currency>';
$HTTP_RAW_POST_DATA .= '<symbol>kr</symbol>';
$HTTP_RAW_POST_DATA .= '<price-format>{PRICE} {CURRENCY}</price-format>';
$HTTP_RAW_POST_DATA .= '</country>';

$HTTP_RAW_POST_DATA .= '<country id="100" address-lookup="true">';
$HTTP_RAW_POST_DATA .= '<name>Denmark</name>';
$HTTP_RAW_POST_DATA .= '<min-mobile>10000000</min-mobile>';
$HTTP_RAW_POST_DATA .= '<max-mobile>99999999</max-mobile>';
$HTTP_RAW_POST_DATA .= '<currency>DKK</currency>';
$HTTP_RAW_POST_DATA .= '<symbol>kr</symbol>';
$HTTP_RAW_POST_DATA .= '<price-format>{PRICE} {CURRENCY}</price-format>';
$HTTP_RAW_POST_DATA .= '</country>';

$HTTP_RAW_POST_DATA .= '</countries>';
$HTTP_RAW_POST_DATA .= '</save-country-configuration>';
$HTTP_RAW_POST_DATA .= '</root>';
*/

$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
    if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mpoint.xsd") === true && count($obj_DOM->{'save-country-configuration'}->countries) > 0)
	{	
		$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);
        $obj_Validator = new Validate();
        $aMsgCds = array();
		$xml = '';
        
		for ($i=0; $i<count($obj_DOM->{'save-country-configuration'}->countries->country); $i++)
        {
            // Set Global Defaults
//			if (empty($obj_DOM->{'save-settings'}[$i]["account"]) === true || intval($obj_DOM->{'save-settings'}[$i]["account"]) < 1) { $obj_DOM->{'save-settings'}[$i]["account"] = -1; }
			
			// Validate basic information
//			$code = Validate::valBasic($_OBJ_DB, (integer) $obj_DOM->{'save-settings'}[$i]["client-id"], (integer) $obj_DOM->{'save-settings'}[$i]["account"]);
//			if ($code == 100)
//			{
//				$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'save-settings'}[$i]["client-id"], (integer) $obj_DOM->{'save-settings'}[$i]["account"]);
//	
//				// Client successfully authenticated
//				if ($obj_ClientConfig->getUsername() == trim($_SERVER['PHP_AUTH_USER']) && $obj_ClientConfig->getPassword() == trim($_SERVER['PHP_AUTH_PW']) )
//				{
					$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'save-country-configuration'}->countries->country[$i]['id']);
					if ( ($obj_CountryConfig instanceof CountryConfig) === false) { $aMsgCds[] = -1; }
                    
                    // TODO: Do more input check?
//                    $obj_mPoint = new MyAccount($_OBJ_DB, $_OBJ_TXT, $obj_CountryConfig);
//					if ($obj_Validator->valPassword( (string) $obj_DOM->{'save-settings'}[$i]->password) != 10) { $aMsgCds[] = $obj_Validator->valPassword( (string) $obj_DOM->{'save-settings'}[$i]->password) + 20; }
                
                    // Success: Input valid
					if (count($aMsgCds) == 0)
					{
                        $country = $obj_DOM->{'save-country-configuration'}->countries->country[$i];
                        $id = (integer) $obj_DOM->{'save-country-configuration'}->countries->country[$i]['id'];
                        $name = (string) $obj_DOM->{'save-country-configuration'}->countries->country[$i]->name;
                        $currency = (string) $obj_DOM->{'save-country-configuration'}->countries->country[$i]->currency;
                        $sym = (string) $obj_DOM->{'save-country-configuration'}->countries->countryies[$i]->symbol;
                        $pf = (string) $obj_DOM->{'save-country-configuration'}->countries->country[$i]->{'price-format'};
                        $minmob = (string) $obj_DOM->{'save-country-configuration'}->countries->country[$i]->{'min-mobile'};
                        $maxmob = (string) $obj_DOM->{'save-country-configuration'}->countries->country[$i]->{'max-mobile'};
                        if ( (string) $obj_DOM->{'save-country-configuration'}->countries->country[$i]['country-id'] == 'true') { $al = true; }
                        else { $al = false; }
                        
                        if (CountryConfig::updateConfig($_OBJ_DB, $id, $name, $currency, $sym, $pf, $al, $minmob, $maxmob) == true)
                        {
                            $xml .= '<status code="100" country-id="'. $obj_DOM->{'save-country-configuration'}->countries->country[$i]['id'] .'">Country successfully updated</status>';
                        }
                        else
                        {
                            header("HTTP/1.1 500 Internal Server Error");
                            $xml .= '<status code="90" country-id="'. $obj_DOM->{'save-country-configuration'}->countries->country[$i]['id'] .'">Unable to update country</status>';
                        }
                    }
                    // Error: Invalid Input
					else
					{
						header("HTTP/1.1 400 Bad Request");
					
						foreach ($aMsgCds as $code)
						{
							$xml .= '<status code="'. $code .'" country-id="'. $obj_DOM->{'save-country-configuration'}->countries->country[$i]['id'] .'" />';
						}
					}
//                }
//				else
//				{
//					header("HTTP/1.1 401 Unauthorized");
//						
//					$xml = '<status code="401">Username / Password doesn\'t match</status>';
//				}
//            }
//			else
//			{
//				header("HTTP/1.1 400 Bad Request");
//			
//				$xml = '<status code="'. $code .'">Client ID / Account doesn\'t match</status>';
//			}
        }
    }
	// Error: Invalid XML Document
	elseif ( ($obj_DOM instanceof SimpleDOMElement) === false)
	{
		header("HTTP/1.1 415 Unsupported Media Type");
		
		$xml = '<status code="415">Invalid XML Document</status>';
	}
	// Error: Wrong operation
	elseif (count($obj_DOM->pay) == 0)
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