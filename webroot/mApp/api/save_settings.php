<?php
/**
 *
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package profile
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

set_time_limit(600);
/*
$_SERVER['PHP_AUTH_USER'] = "CPMDemo";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<save-settings client-id="10007" account="100007">';
$HTTP_RAW_POST_DATA .= '<settings>';
$HTTP_RAW_POST_DATA .= '<auto-top-up>';
$HTTP_RAW_POST_DATA .= '<threshold country-id="100">10000</threshold>';
$HTTP_RAW_POST_DATA .= '<amount country-id="100">5000</amount>';
$HTTP_RAW_POST_DATA .= '</auto-top-up>';
$HTTP_RAW_POST_DATA .= '</settings>';
$HTTP_RAW_POST_DATA .= '<password>oisJona1</password>';
$HTTP_RAW_POST_DATA .= '<client-info app-id="4" platform="iOS" version="1.00" language="gb">';
$HTTP_RAW_POST_DATA .= '<mobile country-id="100">28882861</mobile>';
$HTTP_RAW_POST_DATA .= '<email>jona@oismail.com</email>';
$HTTP_RAW_POST_DATA .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
$HTTP_RAW_POST_DATA .= '</client-info>';
$HTTP_RAW_POST_DATA .= '</save-settings>';
$HTTP_RAW_POST_DATA .= '</root>';
*/
$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mpoint.xsd") === true && count($obj_DOM->{'save-settings'}) > 0)
	{
		$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);
		$obj_Validator = new Validate();
		$xml = '';
		$aMsgCds = array();
		for ($i=0; $i<count($obj_DOM->{'save-settings'}); $i++)
		{
			// Set Global Defaults
			if (empty($obj_DOM->{'save-settings'}[$i]["account"]) === true || intval($obj_DOM->{'save-settings'}[$i]["account"]) < 1) { $obj_DOM->{'save-settings'}[$i]["account"] = -1; }

			// Validate basic information
			$code = Validate::valBasic($_OBJ_DB, (integer) $obj_DOM->{'save-settings'}[$i]["client-id"], (integer) $obj_DOM->{'save-settings'}[$i]["account"]);
			if ($code == 100)
			{
				$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'save-settings'}[$i]["client-id"], (integer) $obj_DOM->{'save-settings'}[$i]["account"]);

				// Client successfully authenticated
				if ($obj_ClientConfig->getUsername() == trim($_SERVER['PHP_AUTH_USER']) && $obj_ClientConfig->getPassword() == trim($_SERVER['PHP_AUTH_PW']) )
				{
					$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'save-settings'}[$i]->{'client-info'}->mobile["country-id"]);
					if ( ($obj_CountryConfig instanceof CountryConfig) === false) { $obj_CountryConfig = $obj_ClientConfig->getCountryConfig(); }

					$obj_mPoint = new MyAccount($_OBJ_DB, $_OBJ_TXT, $obj_CountryConfig);
					if ($obj_Validator->valPassword( (string) $obj_DOM->{'save-settings'}[$i]->password) != 10) { $aMsgCds[] = $obj_Validator->valPassword( (string) $obj_DOM->{'save-settings'}[$i]->password) + 20; }
					if (intval($obj_DOM->{'save-settings'}[$i]->settings->{'auto-top-up'}->threshold) < 0) { $aMsgCds[] = 41; }
					if (intval($obj_DOM->{'save-settings'}[$i]->settings->{'auto-top-up'}->amount) > 0 && intval($obj_DOM->{'save-settings'}[$i]->settings->{'auto-top-up'}->amount) <= 1000) { $aMsgCds[] = 42; }

					// Success: Input valid
					if (count($aMsgCds) == 0)
					{
						$iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_CountryConfig, $obj_DOM->{'save-settings'}[$i]->{'client-info'}->{'customer-ref'}, $obj_DOM->{'save-settings'}[$i]->{'client-info'}->mobile, $obj_DOM->{'save-settings'}[$i]->{'client-info'}->email);
						$code = General::authToken($iAccountID, $obj_ClientConfig->getSecret(), $_COOKIE['token']);
						// Authentication succeeded
						if ($code >= 10)
						{
							// Generate new security token
							if ($code == 11) { setcookie("token", General::genToken($iAccountID, $obj_ClientConfig->getSecret() ) ); }
							$code = $obj_mPoint->auth($iAccountID, (string) $obj_DOM->{'save-settings'}[$i]->password, false);
							// Authentication not required or Authentication succeeded
							if ($code == 10 || ($code == 11 && $obj_ClientConfig->smsReceiptEnabled() === false) )
							{
// TODO
//								$obj_mPoint->saveSettings($iAccountID, (integer) $obj_DOM->{'save-settings'}[$i]->settings->{'auto-top-up'}->threshold, (integer) $obj_DOM->{'save-settings'}[$i]->settings->{'auto-top-up'}->amount);
								$xml = '<status code="100">Settings saved</status>';
							}
							// Authentication succeeded - But Mobile number not verified
							elseif ($code == 11)
							{
								header("HTTP/1.1 403 Forbidden");

								$xml = '<status code="37">Mobile number not verified</status>';
							}
							// Authentication failed
							else
							{
								header("HTTP/1.1 403 Forbidden");

								$xml = '<status code="'. ($code+30) .'" />';
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
	elseif (count($obj_DOM->{'save-settings'}) == 0)
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