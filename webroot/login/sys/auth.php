<?php
/**
 *
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Admin
 * @subpackage Login
 */

// Require include file for including all Shared and General APIs
require_once("../../inc/include.php");

// Require the PHP API for handling the connection to GoMobile
require_once(sAPI_CLASS_PATH ."/gomobile.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );

// Initialize Standard content Object
$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);
$obj_Validator = new Validate();

$obj_XML = simplexml_load_string(trim($HTTP_RAW_POST_DATA) );

$xml = '';
$sType = "status";
// List of Error Codes
$aErrCd = array();

switch ($obj_XML["type"])
{
case "input":
	// Validate Input
	foreach ($obj_XML as $input)
	{
		switch ($input->getName() )
		{
		case "countryid":	// Validate Country
			$aErrCd["countryid"] = $obj_Validator->valCountry($_OBJ_DB, (integer) $input);
			break;
		case "password":	// Validate password
			$aErrCd["password"] = $obj_Validator->valPassword( (string) $input);
			break;
		case "otp":	// Validate One Time Password
			$aErrCd["otp"] = $obj_Validator->valCode( (integer) $input);
			break;
		default:			// Error: Unknown tag
			$aErrCd["internal"] = 2;
			break;
		}
	}
	// Check return codes for errors
	foreach ($aErrCd as $tag => $code)
	{
		// Error found in Input
		if ($code < 10)
		{
			$xml .= '<'. $tag .' id="'. $code .'">'. htmlspecialchars($_OBJ_TXT->_($tag ." - code: ". $code), ENT_NOQUOTES) .'</'. $tag .'>';
		}
	}
	break;
case "linked":
	// Validate Input
	foreach ($obj_XML as $input)
	{
		switch ($input->getName() )
		{
		case "username":	// Validate username
			if ($obj_Validator->valCountry($_OBJ_DB, (integer) $obj_XML->countryid) == 10)
			{
				$oXML = simplexml_load_string($obj_mPoint->getCountries() );
				$oXML = $oXML->xpath("/countries/item[@id = ". $obj_XML->countryid ."]");
				$oXML = $oXML[0];

				$obj_CountryConfig = new CountryConfig($oXML["id"], (string) $oXML->name, (string) $oXML->currency, (string) $oXML->currency["symbol"], (integer) $oXML->maxbalance, (integer) $oXML->mintransfer, (float) $oXML->minmobile, (float) $oXML->maxmobile, (string) $oXML->channel, (string) $oXML->priceformat, (integer) $oXML->decimals, General::xml2bool( (string) $oXML->addresslookup), General::xml2bool( (string) $oXML->doubleoptin) );
				$obj_Validator = new Validate($obj_CountryConfig);

				$aErrCd["username"] = $obj_Validator->valMobile( (string) $obj_XML->username);
				if ($aErrCd["username"] < 10 && floatval($obj_XML->username) == 0) { $aErrCd["username"] = $obj_Validator->valEMail( (string) $obj_XML->username) + 10; }
			}
			else { $aErrCd["countryid"] = 1; }
			break;
		default:			// Error: Unknown tag
			break;
		}
	}
	// Check return codes for errors
	foreach ($aErrCd as $tag => $code)
	{
		// Error found in Input
		if ($code < 10)
		{
			$xml .= '<'. $tag .' id="'. $code .'">'. htmlspecialchars($_OBJ_TXT->_($tag ." - code: ". $code), ENT_NOQUOTES) .'</'. $tag .'>';
		}
	}
	break;
case "form":
	// Validate Input
	$aErrCd["countryid"] = $obj_Validator->valCountry($_OBJ_DB, (integer) $obj_XML->form->countryid);
	if ($aErrCd["countryid"] == 10)
	{
		$oXML = simplexml_load_string($obj_mPoint->getCountries() );
		$oXML = $oXML->xpath("/countries/item[@id = ". $obj_XML->form->countryid ."]");
		$oXML = $oXML[0];

		$obj_CountryConfig = new CountryConfig($oXML["id"], (string) $oXML->name, (string) $oXML->currency, (string) $oXML->currency["symbol"], (integer) $oXML->maxbalance, (integer) $oXML->mintransfer, (float) $oXML->minmobile, (float) $oXML->maxmobile, (string) $oXML->channel, (string) $oXML->priceformat, (integer) $oXML->decimals, General::xml2bool( (string) $oXML->addresslookup), General::xml2bool( (string) $oXML->doubleoptin) );
		$obj_Validator = new Validate($obj_CountryConfig);

		$aErrCd["username"] = $obj_Validator->valMobile( (string) $obj_XML->form->username);
		if ($aErrCd["username"] < 10 && floatval($obj_XML->username) == 0) { $aErrCd["username"] = $obj_Validator->valEMail( (string) $obj_XML->username) + 10; }
	}
	else { $aErrCd["countryid"] = 1; }
	$aErrCd["password"] = $obj_Validator->valPassword( (string) $obj_XML->form->password);
	if (count($obj_XML->form->otp) > 0)
	{
		$aErrCd["otp"] = $obj_Validator->valCode( (integer) $obj_XML->form->otp);
	}
	
	// Check return codes for errors
	foreach ($aErrCd as $tag => $code)
	{
		// Error found in Input
		if ($code < 10)
		{
			$xml .= '<'. $tag .' id="'. $code .'">'. htmlspecialchars($_OBJ_TXT->_($tag ." - code: ". $code), ENT_NOQUOTES) .'</'. $tag .'>';
		}
	}

	// Username / Password validated
	if (empty($xml) === true)
	{
		// Re-Initialize Standard content Object
		$obj_mPoint = new Home($_OBJ_DB, $_OBJ_TXT, $obj_CountryConfig);
		$iAccountID = $obj_mPoint->getAccountID($obj_CountryConfig, (string) $obj_XML->form->username);
		$code = $obj_mPoint->auth($iAccountID, (string) $obj_XML->form->password);
		
		// Authentication successful
		if ($code == 10)
		{
			$obj_AccountXML = simplexml_load_string($obj_mPoint->getAccountInfo($iAccountID) );
			if (floatval($obj_AccountXML->mobile) > $obj_CountryConfig->getMinMobile() )
			{
				// One Time Password supplied and validated
				if (count($obj_XML->form->otp) > 0)
				{
					$aErrCd["otp"] = $obj_mPoint->activateCode($iAccountID, (integer) $obj_XML->form->otp) + 3;
					// One Time Password validated
					if ($aErrCd["otp"] >= 10)
					{
						$_SESSION['obj_Info']->setInfo("accountid", $iAccountID);
						$_SESSION['obj_CountryConfig'] = $obj_CountryConfig;
						
						$sType = "multipart";
						$xml = '<document type="status">
									<form id="100" name="'. $obj_XML["name"] .'">'. htmlspecialchars($_OBJ_TXT->_("auth - code: 100"), ENT_NOQUOTES) .'</form>
								</document>
								<document type="command">
									'. Home::getRecacheLogin() .'
								</document>
								<document type="command">
									<close>
										<popup>one-time-password</popup>
									</close>
								</document>
								<document type="command" msg="status">
									<redirect>
								 		<url>/home/default.php</url>
								 	</redirect>
								</document>';
						// Workaround to ensure that the database session isn't accidentally overwritten by an Input Validation request
						sleep(2);
					}
					// Error: Unable to consume One Time Password
					else
					{
						$xml .= '<otp id="'. $aErrCd["otp"] .'">'. htmlspecialchars($_OBJ_TXT->_("otp - code: ". $aErrCd["otp"]), ENT_NOQUOTES) .'</otp>';
					}
				}
				// Send One Time Password
				else
				{
					$code = $obj_mPoint->sendOneTimePassword(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO), $iAccountID, (string) $obj_AccountXML->mobile);
					
					// One Time Password sent
					if ($code == 200)
					{
						$sType = "multipart";
						$xml = '<document type="status">
									<form id="100" name="'. $obj_XML["name"] .'">'. htmlspecialchars($_OBJ_TXT->_("auth - code: 100"), ENT_NOQUOTES) .'</form>
								</document>
								<document type="popup">
									<popup>
										<name>one-time-password</name>
										<parent>left-menu</parent>
										<url>/login/otp.php</url>
								 		<css>one-time-password</css>
								 	</popup>
								</document>';
					}
					// Error: Unable to send One Time Password
					else
					{
						$xml = '<form id="92" name="'. $obj_XML["name"] .'">'. htmlspecialchars($_OBJ_TXT->_("auth - code: 92"), ENT_NOQUOTES) .'</form>';
					}
				}
			}
			// Mobile Number not registered
			else
			{
				$_SESSION['obj_Info']->setInfo("accountid", $iAccountID);
				$_SESSION['obj_CountryConfig'] = $obj_CountryConfig;
				$sType = "multipart";
				$xml = '<document type="command">
							'. Home::getRecacheLogin() .'
						</document>
						<document type="status">
							<form id="102" name="'. $obj_XML["name"] .'">'. htmlspecialchars($_OBJ_TXT->_("auth - code: 102"), ENT_NOQUOTES) .'</form>
						</document>
						<document type="command" msg="status">
							<redirect>
						 		<url>/home/default.php</url>
						 	</redirect>
						</document>';
			}
		}
		// Error in authentication, return status code and message
		else
		{
			$xml = '<form id="'. ($code + 90) .'" name="'. $obj_XML["name"] .'">'. htmlspecialchars($_OBJ_TXT->_("auth - code: ". ($code + 90) ), ENT_NOQUOTES) .'</form>';
		}
	}
	break;
default:
	$xml = '<internal id="1">'. htmlspecialchars($_OBJ_TXT->_("internal - code: 2"), ENT_NOQUOTES) .'</internal>';
	break;
}

echo '<?xml version="1.0" encoding="UTF-8"?>'
?>
<root type="<?= $sType; ?>">
	<?= $xml; ?>
</root>