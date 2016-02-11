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

// Require the PHP API for handling the connection to GoMobile
require_once(sAPI_CLASS_PATH ."/gomobile.php");
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require Data Class for Client Information
require_once(sCLASS_PATH ."/clientinfo.php");

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );
/*
$_SERVER['PHP_AUTH_USER'] = "CPMDemo";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<get-account client-id="10007" account="100006">';
$HTTP_RAW_POST_DATA .= '<mobile country-id="610" operator-id="61000">3138544000</mobile>';
//$HTTP_RAW_POST_DATA .= '<email>asd@as.com</email>';
$HTTP_RAW_POST_DATA .= '<client-info platform="iOS" version="1.00" language="da">';
$HTTP_RAW_POST_DATA .= '<mobile country-id="100" operator-id="10000">28882861</mobile>';
$HTTP_RAW_POST_DATA .= '<email>asd@as.com</email>';
$HTTP_RAW_POST_DATA .= '<device-id>85ce3843c0a068fb5cb1e76156fdd719</device-id>';
$HTTP_RAW_POST_DATA .= '</client-info>';
$HTTP_RAW_POST_DATA .= '</get-account>';
$HTTP_RAW_POST_DATA .= '</root>';
*/
$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mpoint.xsd") === true && count($obj_DOM->{'get-account'}) > 0)
	{	
		$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);
		
		for ($i=0; $i<count($obj_DOM->{'get-account'}); $i++)
		{
			// Set Global Defaults
			if (empty($obj_DOM->{'get-account'}[$i]["account"]) === true || intval($obj_DOM->{'get-account'}[$i]["account"]) < 1) { $obj_DOM->{'get-account'}[$i]["account"] = -1; }
		
			// Validate basic information
			$code = Validate::valBasic($_OBJ_DB, (integer) $obj_DOM->{'get-account'}[$i]["client-id"], (integer) $obj_DOM->{'get-account'}[$i]["account"]);
			if ($code == 100)
			{
				$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'get-account'}[$i]["client-id"], (integer) $obj_DOM->{'get-account'}[$i]["account"]);
				
				// Client successfully authenticated
				if ($obj_ClientConfig->getUsername() == trim($_SERVER['PHP_AUTH_USER']) && $obj_ClientConfig->getPassword() == trim($_SERVER['PHP_AUTH_PW']) )
				{
					// Re-Intialise Text Translation Object based on transaction
					$_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH . $obj_DOM->{'get-account'}[$i]->{'client-info'}["language"] ."/global.txt", sLANGUAGE_PATH . $obj_DOM->{'get-account'}[$i]->{'client-info'}["language"] ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");
					
					$obj_mPoint = new EndUserAccount($_OBJ_DB, $_OBJ_TXT, $obj_ClientConfig);
					$obj_Validator = new Validate($obj_ClientConfig->getCountryConfig() );
					$aMsgCds = array();
										
					// Success: Input Valid
					if (count($aMsgCds) == 0)
					{
						$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'get-account'}[$i]->{'client-info'}->mobile["country-id"]);
						
						$iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_CountryConfig, $obj_DOM->{'get-account'}[$i]->{'client-info'}->{'customer-ref'}, $obj_DOM->{'get-account'}[$i]->{'client-info'}->mobile, $obj_DOM->{'get-account'}[$i]->{'client-info'}->email);
						
						
						$obj_XML = simplexml_load_string($obj_mPoint->getAccountInfo($iAccountID) );
						$xml = '<account id="'. $obj_XML["id"] .'">';
						$xml .= $obj_XML->{'first-name'}->asXML();
						$xml .= $obj_XML->{'last-name'}->asXML();
						$xml .= $obj_XML->mobile->asXML();
						$xml .= $obj_XML->email->asXML();
						$xml .= $obj_XML->created->asXML();
						$xml .= '</account>';
					}
					else
					{
						header("HTTP/1.1 400 Bad Request");
						
						$xml = '<status code="'. $aMsgCds[0] .'" />';
					}
				}
				else
				{
					header("HTTP/1.1 401 Unauthorized");
					
					$xml = '<status code="401">Username / Password doesn\'t match</status>';
				}
			}
			else
			{
				header("HTTP/1.1 400 Bad Request");
				
				$xml = '<status code="'. $code .'">Client ID / Account doesn\'t match</status>';
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
	elseif (count($obj_DOM->{'get-account'}) == 0)
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