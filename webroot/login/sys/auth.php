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
		default:			// Error: Unknown tag
			$aErrCd["internal"] = 2;
			break;
		}
	}
	// Check return codes for errors
	while (list($tag, $code) = each($aErrCd) )
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
	while (list($tag, $code) = each($aErrCd) )
	{
		// Error found in Input
		if ($code != 10)
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

	// Check return codes for errors
	while (list($tag, $code) = each($aErrCd) )
	{
		// Error found in Input
		if ($code != 10)
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

		// Authentication succesful, return URLs for fetching next page
		if ($code == 10)
		{
			$_SESSION['obj_Info']->setInfo("accountid", $iAccountID);
			$_SESSION['obj_CountryConfig'] = $obj_CountryConfig;
			$sType = "multipart";
			$xml = '<document type="command">
						'. Home::getRecacheLogin() .'
					</document>
					<document type="status">
						<form id="'. ($code + 90) .'" name="'. (string) $obj_XML->form['name'] .'">'. htmlspecialchars($_OBJ_TXT->_("auth - code: 10"), ENT_NOQUOTES) .'</form>
					</document>
					<document type="command">
						<redirect>
					 		<url>/home/default.php</url>
					 	</redirect>
					</document>';
			// Workaround to ensure that the database session isn't accidentally overwritten by an Input Validation request
			sleep(2);
		}
		// Error in authentication, return status code and message
		else
		{
			$xml = '<form id="'. $code .'" name="'. (string) $obj_XML->form['name'] .'">'. htmlspecialchars($_OBJ_TXT->_("auth - code: ". $code), ENT_NOQUOTES) .'</form>';
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