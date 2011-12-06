<?php
/**
 *
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Admin
 * @subpackage CreateAccount
 */

// Require include file for including all Shared and General APIs
require_once("../../inc/include.php");

// Require the PHP API for handling the connection to GoMobile
require_once(sAPI_CLASS_PATH ."/gomobile.php");

// Require Business logic for the My Account component
require_once(sCLASS_PATH ."/my_account.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );

// Initialize Standard content Object
$obj_mPoint = new MyAccount($_OBJ_DB, $_OBJ_TXT);
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
		case "firstname":	// Validate First name
			$aErrCd["firstname"] = $obj_Validator->valName( (string) $input);
			break;
		case "lastname":	// Validate Last name
			$aErrCd["lastname"] = $obj_Validator->valName( (string) $input);
			break;
		case "password":	// Validate Password
			$aErrCd["password"] = $obj_Validator->valPassword( (string) $input);
			break;
		case "repeatpassword":	// Validate Repeated Password
			$aErrCd["repeatpassword"] = $obj_Validator->valPassword( (string) $input);
			break;
		case "checksum":		// Validate Transfer Code allowing it not be provided
			$aErrCd["checksum"] = $obj_Validator->valChecksum($_OBJ_DB, (string) $input);
			if ($aErrCd["checksum"] == 1) { $aErrCd["checksum"] = 10; }
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
	if ($obj_Validator->valCountry($_OBJ_DB, (integer) $obj_XML->countryid) == 10)
	{
		$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_XML->countryid);
		$obj_Validator = new Validate($obj_CountryConfig);
	
		// Re-Initialize Standard content Object
		$obj_mPoint = new MyAccount($_OBJ_DB, $_OBJ_TXT, $obj_CountryConfig);
		
		// Validate Input
		foreach ($obj_XML as $input)
		{
			switch ($input->getName() )
			{
			case "mobile":	// Validate Mobile Number
				$aErrCd["mobile"] = $obj_Validator->valMobile( (string) $input);
				if ($aErrCd["mobile"] == 10) { $aErrCd["mobile"] = $obj_mPoint->valMobile(-1, (string) $input) + 3; }
				if ( ($aErrCd["mobile"] == 5  || $aErrCd["mobile"] == 6) && $obj_Validator->valChecksum($_OBJ_DB, (string) $obj_XML->checksum) == 10)
				{
					list(, $id) = spliti("Z", (string) $obj_XML->checksum);
					$id = base_convert($id, 32, 10);
					// Mobile Number belongs to the same End-User Account as the Account ID for the Transfer Code
					if ($obj_mPoint->getAccountID($obj_CountryConfig, (string) $input) == $id)
					{
						$aErrCd["mobile"] = 10;
					}
				}
				// Mobile Number already belongs to an end-user account which has not yet been activated isn't considered an error at this point
				elseif ($aErrCd["mobile"] == 6) { $aErrCd["mobile"] = 10; }
				break;
			case "email":	// Validate E-Mail Address
				$aErrCd["email"] = $obj_Validator->valEMail( (string) $input);
				if ($aErrCd["email"] == 10) { $aErrCd["email"] = $obj_mPoint->valEMail(-1, (string) $input) + 5; }
				if ( ($aErrCd["email"] == 7 || $aErrCd["email"] == 8) && $obj_Validator->valChecksum($_OBJ_DB, (string) $obj_XML->checksum) == 10)
				{
					list(, $id) = spliti("Z", (string) $obj_XML->checksum);
					$id = base_convert($id, 32, 10);
					// E-Mail Address belongs to the same End-User Account as the Account ID for the Transfer Code
					if ($obj_mPoint->getAccountID($obj_CountryConfig, (string) $input) == $id)
					{
						$aErrCd["email"] = 10;
					}
				}
				// E-Mail Address already belongs to an end-user account which has not yet been activated isn't considered an error at this point
				elseif ($aErrCd["email"] == 8) { $aErrCd["email"] = 10; }
				break;
			default:			// Error: Unknown tag
				break;
			}
		}
	}
	else { $aErrCd["countryid"] = 1; }
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
	$aErrCd["firstname"] = $obj_Validator->valName( (string) $obj_XML->form->firstname);
	$aErrCd["lastname"] = $obj_Validator->valName( (string) $obj_XML->form->lastname);
	// Validate passwords checking they're the same
	$aErrCd["password"] = $obj_Validator->valPassword( (string) $obj_XML->form->password);
	$aErrCd["repeatpassword"] = $obj_Validator->valPassword( (string) $obj_XML->form->repeatpassword);
	if ($aErrCd["password"] == 10 && $aErrCd["repeatpassword"] == 10 && strval($obj_XML->form->password) != strval($obj_XML->form->repeatpassword) )
	{
		$aErrCd["password"] = 5;
		$aErrCd["repeatpassword"] = 5;
	}
	// Validate Mobile Number and E-Mail Address checking they aren't already registered to another account
	if ($aErrCd["countryid"] == 10)
	{
		$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_XML->form->countryid);
		$obj_Validator = new Validate($obj_CountryConfig);
		
		// Re-Initialize Standard content Object
		$obj_mPoint = new MyAccount($_OBJ_DB, $_OBJ_TXT, $obj_CountryConfig);
		
		$aErrCd["mobile"] = $obj_Validator->valMobile( (string) $obj_XML->form->mobile);
		if ($aErrCd["mobile"] == 10) { $aErrCd["mobile"] = $obj_mPoint->valMobile(-1, (string) $obj_XML->form->mobile) + 3; }
		$aErrCd["email"] = $obj_Validator->valEMail( (string) $obj_XML->form->email);
		if ($aErrCd["email"] == 10) { $aErrCd["email"] = $obj_mPoint->valEMail(-1, (string) $obj_XML->form->email) + 5; }
	}
	// Validate Transfer Code allowing it not be provided
	$aErrCd["checksum"] = $obj_Validator->valChecksum($_OBJ_DB, (string) $obj_XML->form->checksum);
	if ($aErrCd["checksum"] == 10)
	{
		$aErrCd["checksum"] = $obj_mPoint->valChecksum( (string) $obj_XML->form->checksum, (string) $obj_XML->form->mobile) + 4;
		if ($aErrCd["checksum"] == 4 || $aErrCd["checksum"] == 9) { $aErrCd["checksum"] = $obj_mPoint->valChecksum( (string) $obj_XML->form->checksum, (string) $obj_XML->form->email) + 4; }
		
		switch ($aErrCd["checksum"])
		{
		case (5):
			$aErrCd["mobile"] = 7;
			break;
		case (6):
			$aErrCd["email"] = 9;
			break;
		default:
			list(, $id) = spliti("Z", (string) $obj_XML->form->checksum);
			$id = base_convert($id, 32, 10);
			// Mobile Number belongs to the same End-User Account as the Account ID for the Transfer Code
			if ( ($aErrCd["mobile"] == 5 || $aErrCd["mobile"] == 6) && $obj_mPoint->getAccountID($obj_CountryConfig, (string) $obj_XML->form->mobile) == $id)
			{
				$aErrCd["mobile"] = 10;
			}
			// E-Mail Address belongs to the same End-User Account as the Account ID for the Transfer Code
			if ( ($aErrCd["email"] == 7 || $aErrCd["email"] == 8) && $obj_mPoint->getAccountID($obj_CountryConfig, (string) $obj_XML->form->email) == $id)
			{
				$aErrCd["email"] = 10;
			}
			break;
		}
	}
	elseif ($aErrCd["checksum"] == 1) { $aErrCd["checksum"] = 10; }

	// Check return codes for errors
	foreach ($aErrCd as $tag => $code)
	{
		// Error found in Input
		if ($code < 10)
		{
			$xml .= '<'. $tag .' id="'. $code .'">'. htmlspecialchars($_OBJ_TXT->_($tag ." - code: ". $code), ENT_NOQUOTES) .'</'. $tag .'>';
		}
	}
	
	// Account Data validated
	if (empty($xml) === true)
	{
		$_OBJ_DB->query("BEGIN");
		// Valid Transfer Code provided
		if (strval($obj_XML->form->checksum) != "")
		{	
			$aErrCd["password"] = $obj_mPoint->savePassword($id, (string) $obj_XML->form->password);
			$aErrCd["mobile"] = $obj_mPoint->saveMobile($id, null);
			$aErrCd["email"] = $obj_mPoint->saveEMail($id, null);
			// All Account Information saved
			if ($aErrCd["password"] === true && $aErrCd["mobile"] === true && $aErrCd["email"] == true)
			{
				$iAccountID = $id;
			}
			else { $iAccountID = -1; }
		}
		else { $iAccountID = $obj_mPoint->newAccount($obj_CountryConfig->getID(), "", (string) $obj_XML->form->password); }
		
		// Success: Account created
		if ($iAccountID > 0)
		{
			$code = $obj_mPoint->saveInfo($iAccountID, (string) $obj_XML->form->firstname, (string) $obj_XML->form->lastname);

			// Success: Account Info saved
			if ($code === true)
			{
				$aErrCd["mobile"] = $obj_mPoint->sendCode(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO), $iAccountID, (string) $obj_XML->form->mobile);
				$aErrCd["email"] = $obj_mPoint->sendLink($iAccountID, (string) $obj_XML->form->email);
				
				if ($aErrCd["mobile"] == 200 && $aErrCd["email"] === true)
				{
					$_OBJ_DB->query("COMMIT");
					$_SESSION['temp']['accountid'] = $iAccountID;
					$_SESSION['temp']['obj_CountryConfig'] = $obj_CountryConfig;
					$sType = "multipart";
					$xml = '<document type="status">
								<form id="100" name="'. (string) $obj_XML->form['name'] .'">'. htmlspecialchars($_OBJ_TXT->_("save - code: 100"), ENT_NOQUOTES) .'</form>
							</document>
							<document type="command">
								<redirect>
								 	<url>/new/step2.php</url>
								</redirect>
							</document>';
				}
				// Error while sending verification SMS / E-Mail
				else
				{
					$_OBJ_DB->query("ROLLBACK");
					$code = 0;
					if ($aErrCd["mobile"] != 200) { $code = 1; }
					if ($aErrCd["email"] !== true) { $code += 2; }
					$xml = '<form id="'. ($code + 92) .'" name="'. (string) $obj_XML->form['name'] .'">'. htmlspecialchars($_OBJ_TXT->_("save - code: ". ($code + 92) ), ENT_NOQUOTES) .'</form>';
				}
			}
			// Error while saving account information
			else
			{
				$_OBJ_DB->query("ROLLBACK");
				$xml = '<form id="92" name="'. (string) $obj_XML->form['name'] .'">'. htmlspecialchars($_OBJ_TXT->_("save - code: 92"), ENT_NOQUOTES) .'</form>';
			}
		}
		// Error during account creation
		else
		{
			$_OBJ_DB->query("ROLLBACK");
			$xml = '<form id="91" name="'. (string) $obj_XML->form['name'] .'">'. htmlspecialchars($_OBJ_TXT->_("save - code: 91"), ENT_NOQUOTES) .'</form>';
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