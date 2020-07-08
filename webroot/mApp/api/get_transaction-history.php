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
// Require the PHP API for handling the connection to GoMobile
require_once(sAPI_CLASS_PATH ."/gomobile.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");

$aMsgCds = array();

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );
/*
$_SERVER['PHP_AUTH_USER'] = "CPMDemo";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<get-transaction-history client-id="10007" account="100007">';
$HTTP_RAW_POST_DATA .= '<results>';
$HTTP_RAW_POST_DATA .= '<offset>0</offset>';
$HTTP_RAW_POST_DATA .= '<limit>10</limit>';
$HTTP_RAW_POST_DATA .= '</results>';
$HTTP_RAW_POST_DATA .= '<client-info platform="iOS" version="1.00" language="da">';
$HTTP_RAW_POST_DATA .= '<mobile country-id="100" operator-id="10000">28882861</mobile>';
$HTTP_RAW_POST_DATA .= '<email>jona@oismail.com</email>';
$HTTP_RAW_POST_DATA .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
$HTTP_RAW_POST_DATA .= '</client-info>';
$HTTP_RAW_POST_DATA .= '</get-transaction-history>';
$HTTP_RAW_POST_DATA .= '</root>';
*/
$obj_DOM = simpledom_load_string(file_get_contents('php://input'));

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mpoint.xsd") === true && count($obj_DOM->{'get-transaction-history'}) > 0)
	{	
		$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);
		$obj_Validator = new Validate();
		$xml = '';
		
		for ($i=0; $i<count($obj_DOM->{'get-transaction-history'}); $i++)
		{
			// Set Global Defaults
			if (empty($obj_DOM->{'get-transaction-history'}[$i]["account"]) === true || intval($obj_DOM->{'get-transaction-history'}[$i]["account"]) < 1) { $obj_DOM->{'get-transaction-history'}[$i]["account"] = -1; }
		
			// Validate basic information
			$code = Validate::valBasic($_OBJ_DB, (integer) $obj_DOM->{'get-transaction-history'}[$i]["client-id"], (integer) $obj_DOM->{'get-transaction-history'}[$i]["account"]);
			if ($code == 100)
			{
				$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'get-transaction-history'}[$i]["client-id"], (integer) $obj_DOM->{'get-transaction-history'}[$i]["account"]);
				
				// Client successfully authenticated
				if ($obj_ClientConfig->getUsername() == trim($_SERVER['PHP_AUTH_USER']) && $obj_ClientConfig->getPassword() == trim($_SERVER['PHP_AUTH_PW']) )
				{
					$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'get-transaction-history'}[$i]->{'client-info'}->mobile["country-id"]);
					if ( ($obj_CountryConfig instanceof CountryConfig) === false) { $obj_CountryConfig = $obj_ClientConfig->getCountryConfig(); }
					
					$obj_mPoint = new Home($_OBJ_DB, $_OBJ_TXT, $obj_ClientConfig->getCountryConfig() );
					
					// Input valid
					if (count($aMsgCds) == 0)
					{
						$iAccountID = EndUserAccount::getAccountID_Static($_OBJ_DB, $obj_ClientConfig, $obj_DOM->{'get-transaction-history'}[$i]->{'client-info'}->mobile, $obj_CountryConfig);
						if ($iAccountID < 0 && count($obj_DOM->{'get-transaction-history'}[$i]->{'client-info'}->email) == 1) { $iAccountID = EndUserAccount::getAccountID_Static($_OBJ_DB, $obj_ClientConfig, $obj_DOM->{'get-transaction-history'}[$i]->{'client-info'}->email, $obj_CountryConfig); }
						if ($iAccountID < 0) { $iAccountID = $obj_mPoint->getAccountID($obj_CountryConfig, $obj_DOM->{'get-transaction-history'}[$i]->{'client-info'}->mobile); }
						if ($iAccountID < 0) { $iAccountID = $obj_mPoint->getAccountID($obj_CountryConfig, $obj_DOM->{'get-transaction-history'}[$i]->{'client-info'}->email); }
						
						$code = General::authToken($iAccountID, $obj_ClientConfig->getSecret(), $_COOKIE['token']);
						// Authentication succeeded
						if ($code >= 10)
						{
							// Generate new security token
							if ($code == 11) { setcookie("token", General::genToken($iAccountID, $obj_ClientConfig->getSecret() ) ); }
							if (count($obj_DOM->{'get-transaction-history'}[$i]->results) == 1)
							{
								if (count($obj_DOM->{'get-transaction-history'}[$i]->results->limit) == 1) { $obj_DOM->{'get-transaction-history'}[$i]->results->limit = -1; }
								if (count($obj_DOM->{'get-transaction-history'}[$i]->results->offset) == 1) { $obj_DOM->{'get-transaction-history'}[$i]->results->offset = -1; }
								$xml .= $obj_mPoint->getTxnHistory($iAccountID, intval($obj_DOM->{'get-transaction-history'}[$i]->results->limit), intval($obj_DOM->{'get-transaction-history'}[$i]->results->offset) );
							} 
							else { $xml .= $obj_mPoint->getTxnHistory($iAccountID); }
						}
						// Authentication failed
						else
						{
							header("HTTP/1.1 403 Forbidden");
								
							$xml = '<status code="38">Invalid Security Token: '. $_COOKIE['token'] .'</status>';
						}
					}
					// Error in Input
					else
					{
						header("HTTP/1.1 400 Bad Request");
					
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
	elseif (count($obj_DOM->{'get-transaction-history'}) == 0)
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