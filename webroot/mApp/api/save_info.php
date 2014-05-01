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
$HTTP_RAW_POST_DATA .= '<personal-info client-id="10007" account="100007">';
$HTTP_RAW_POST_DATA .= '<full-name>Jonatan Buus</full-name>';
//$HTTP_RAW_POST_DATA .= '<last-name>Buus</last-name>';
$HTTP_RAW_POST_DATA .= '<mobile country-id="100">28882861</mobile>';
//$HTTP_RAW_POST_DATA .= '<mobile country-id="100">28882861</mobile>';
$HTTP_RAW_POST_DATA .= '<email>jona@oismail.com</email>';
$HTTP_RAW_POST_DATA .= '<password>oisJona</password>';
$HTTP_RAW_POST_DATA .= '<new-password>oisJona</new-password>';
$HTTP_RAW_POST_DATA .= '<repeat-password>oisJona</repeat-password>';
$HTTP_RAW_POST_DATA .= '<client-info app-id="3" platform="iOS" version="2.10" language="gb">';
//$HTTP_RAW_POST_DATA .= '<mobile country-id="100">28880019</mobile>';
$HTTP_RAW_POST_DATA .= '<mobile country-id="100">28882861</mobile>';
$HTTP_RAW_POST_DATA .= '<email>jona@oismail.com</email>';
$HTTP_RAW_POST_DATA .= '<device-id>bfe5bb405cae88542e47d38d51e1e849</device-id>';
$HTTP_RAW_POST_DATA .= '</client-info>';
$HTTP_RAW_POST_DATA .= '</personal-info>';
$HTTP_RAW_POST_DATA .= '</root>';
*/
$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mpoint.xsd") === true && count($obj_DOM->{'personal-info'}) > 0)
	{
		$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);
		$obj_Validator = new Validate();
		$xml = '';
		$aMsgCds = array();
		for ($i=0; $i<count($obj_DOM->{'personal-info'}); $i++)
		{
			// Set Global Defaults
			if (empty($obj_DOM->{'personal-info'}[$i]["account"]) === true || intval($obj_DOM->{'personal-info'}[$i]["account"]) < 1) { $obj_DOM->{'personal-info'}[$i]["account"] = -1; }

			// Validate basic information
			$code = Validate::valBasic($_OBJ_DB, (integer) $obj_DOM->{'personal-info'}[$i]["client-id"], (integer) $obj_DOM->{'personal-info'}[$i]["account"]);
			if ($code == 100)
			{
				$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'personal-info'}[$i]["client-id"], (integer) $obj_DOM->{'personal-info'}[$i]["account"]);

				// Client successfully authenticated
				if ($obj_ClientConfig->getUsername() == trim($_SERVER['PHP_AUTH_USER']) && $obj_ClientConfig->getPassword() == trim($_SERVER['PHP_AUTH_PW']) )
				{
					$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'personal-info'}[$i]->{'client-info'}->mobile["country-id"]);
					if ( ($obj_CountryConfig instanceof CountryConfig) === false) { $obj_CountryConfig = $obj_ClientConfig->getCountryConfig(); }

					$obj_mPoint = new MyAccount($_OBJ_DB, $_OBJ_TXT, $obj_CountryConfig);
					if (count($obj_DOM->{'personal-info'}[$i]->password) == 1 || count($obj_DOM->{'personal-info'}[$i]->{'new-password'}) == 1 || count($obj_DOM->{'personal-info'}[$i]->{'repeat-password'}) == 1)
					{
						if ($obj_Validator->valPassword( (string) $obj_DOM->{'personal-info'}[$i]->password) != 10) { $aMsgCds[] = $obj_Validator->valPassword( (string) $obj_DOM->{'personal-info'}[$i]->password) + 20; }
						if ($obj_Validator->valPassword( (string) $obj_DOM->{'personal-info'}[$i]->{'new-password'}) != 10) { $aMsgCds[] = $obj_Validator->valPassword( (string) $obj_DOM->{'personal-info'}[$i]->{'new-password'} ) + 40; }
						if ($obj_Validator->valPassword( (string) $obj_DOM->{'personal-info'}[$i]->{'repeat-password'}) != 10) { $aMsgCds[] = $obj_Validator->valPassword( (string) $obj_DOM->{'personal-info'}[$i]->{'repeat-password'} ) + 44; }
						if (count($aMsgCds) == 0 && strval($obj_DOM->{'personal-info'}[$i]->{'new-password'}) != strval($obj_DOM->{'personal-info'}[$i]->{'repeat-password'}) ) { $aMsgCds[] = 49; }
					}

					// Seperate Full Name into First- and Last Name
					if (count($obj_DOM->{'personal-info'}[$i]->{'full-name'}) == 1)
					{
						$obj_DOM->{'personal-info'}[$i]->{'full-name'} = trim($obj_DOM->{'personal-info'}[$i]->{'full-name'});
						$pos = strrpos($obj_DOM->{'personal-info'}[$i]->{'full-name'}, " ");
						if ($pos === false) { $pos = strlen($obj_DOM->{'personal-info'}[$i]->{'full-name'}); }
						else { $obj_DOM->{'personal-info'}[$i]->{'last-name'} = substr($obj_DOM->{'personal-info'}[$i]->{'full-name'}, $pos + 1); }
						$obj_DOM->{'personal-info'}[$i]->{'first-name'} = substr($obj_DOM->{'personal-info'}[$i]->{'full-name'}, 0 , $pos);
					}
					// Validate First Name
					if (count($obj_DOM->{'personal-info'}[$i]->{'first-name'}) == 1)
					{
						if ($obj_Validator->valName( (string) $obj_DOM->{'personal-info'}[$i]->{'first-name'}) < 10) { $aMsgCds[] = $obj_Validator->valName( (string) $obj_DOM->{'personal-info'}[$i]->{'first-name'}) + 50; }
					}
					// Validate Last Name
					if (count($obj_DOM->{'personal-info'}[$i]->{'last-name'}) == 1)
					{
						if ($obj_Validator->valName( (string) $obj_DOM->{'personal-info'}[$i]->{'last-name'}) < 10) { $aMsgCds[] = $obj_Validator->valName( (string) $obj_DOM->{'personal-info'}[$i]->{'last-name'}) + 60; }
					}
					// Validate Mobile Number
					if (count($obj_DOM->{'personal-info'}[$i]->mobile) == 1)
					{
						$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'personal-info'}[$i]->mobile["country-id"]);
						if ( ($obj_CountryConfig instanceof CountryConfig) === true)
						{
							$obj_Validator = new Validate($obj_CountryConfig);
							if ($obj_Validator->valMobile( (float) $obj_DOM->{'personal-info'}[$i]->mobile) < 10) { $aMsgCds[] = $obj_Validator->valMobile( (float) $obj_DOM->{'personal-info'}[$i]->mobile) + 71; }
						}
						else { $aMsgCds[] = 71; }
					}
					// Error: Mobile Number not provided
					else if (count($obj_DOM->{'personal-info'}[$i]->{'client-info'}->mobile) == 0) { $aMsgCds[] = 79; }
					// Validate E-Mail Address
					if (count($obj_DOM->{'personal-info'}[$i]->email) == 1)
					{
						if ($obj_Validator->valEMail( (string) $obj_DOM->{'personal-info'}[$i]->email) < 10) { $aMsgCds[] = $obj_Validator->valEMail( (string) $obj_DOM->{'personal-info'}[$i]->email) + 80; }
					}

					// Success: Input valid
					if (count($aMsgCds) == 0)
					{
						$iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_CountryConfig, $obj_DOM->{'personal-info'}[$i]->{'client-info'}->{'customer-ref'}, $obj_DOM->{'personal-info'}[$i]->{'client-info'}->mobile, $obj_DOM->{'personal-info'}[$i]->{'client-info'}->email);
						$code = General::authToken($iAccountID, $obj_ClientConfig->getSecret(), $_COOKIE['token']);
						// Authentication succeeded
						if ($code >= 10)
						{
							// Generate new security token
							if ($code == 11) { setcookie("token", General::genToken($iAccountID, $obj_ClientConfig->getSecret() ) ); }
							if (count($obj_DOM->{'personal-info'}[$i]->password) == 1) { $code = $obj_mPoint->auth($iAccountID, (string) $obj_DOM->{'personal-info'}[$i]->password); }
							// Authentication not required or Authentication succeeded
							if (count($obj_DOM->{'personal-info'}[$i]->password) == 0 || $code == 10 || ($code == 11 && $obj_ClientConfig->smsReceiptEnabled() === false) )
							{
								if (count($obj_DOM->{'personal-info'}[$i]->{'first-name'}) == 1 || count($obj_DOM->{'personal-info'}[$i]->{'last-name'}) == 1)
								{
									$obj_mPoint->saveInfo($iAccountID, (string) $obj_DOM->{'personal-info'}[$i]->{'first-name'}, (string)  $obj_DOM->{'personal-info'}[$i]->{'last-name'});
								}
								if (count($obj_DOM->{'personal-info'}[$i]->mobile) == 1)
								{
									if (floatval($obj_DOM->{'personal-info'}[$i]->mobile) == floatval($obj_DOM->{'personal-info'}[$i]->{'client-info'}->mobile) )
									{
										$obj_mPoint->saveMobile($iAccountID, (float) $obj_DOM->{'personal-info'}[$i]->mobile, true);
									}
									else
									{
										$obj_mPoint->saveMobile($iAccountID, (float) $obj_DOM->{'personal-info'}[$i]->mobile, false);
										// One Time Password sent
										if ($obj_mPoint->sendOneTimePassword(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO), $iAccountID, $obj_CountryConfig, (float) $obj_DOM->{'personal-info'}[$i]->mobile) == 200)
										{
											$xml = '<status code="110">Account information successfully saved and OTP sent</status>';
										}
										else
										{
											header("HTTP/1.1 500 Internal Server Error");

											$xml = '<status code="91">Unable to send One Time Password</status>';
										}
									}
								}
								if (count($obj_DOM->{'personal-info'}[$i]->email) == 1)
								{
									$obj_mPoint->saveEmail($iAccountID, (string) $obj_DOM->{'personal-info'}[$i]->email);
								}
								if (count($obj_DOM->{'personal-info'}[$i]->{'new-password'}) == 1)
								{
									$obj_mPoint->savePassword($iAccountID, (string) $obj_DOM->{'personal-info'}[$i]->{'new-password'});
								}
								if (empty($xml) === true) { $xml = '<status code="100">Profile Information Saved</status>'; }
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
								// Account disabled due to too many failed login attempts
								if ($code == 3)
								{
									// Re-Intialise Text Translation Object based on transaction
									$_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH . $obj_DOM->{'personal-info'}[$i]->{'client-info'}["language"] ."/global.txt", sLANGUAGE_PATH . $obj_DOM->{'personal-info'}[$i]->{'client-info'}["language"] ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");
									$obj_mPoint = new EndUserAccount($_OBJ_DB, $_OBJ_TXT, $obj_ClientConfig);
									$obj_mPoint->sendAccountDisabledNotification(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO), $obj_DOM->{'personal-info'}[$i]->{'client-info'}->mobile);
								}

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
	elseif (count($obj_DOM->{'personal-info'}) == 0)
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