<?php
/**
 *
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Admin
 * @subpackage Transfer
 */

// Require include file for including all Shared and General APIs
require_once("../../inc/include.php");

// Require the PHP API for handling the connection to GoMobile
require_once(sAPI_CLASS_PATH ."/gomobile.php");

// Require Business logic for the E-Money Transfer component
require_once(sCLASS_PATH ."/transfer.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");

// Initialize Standard content Object
$obj_mPoint = new Transfer($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_CountryConfig']);
$obj_Validator = new Validate($_SESSION['obj_CountryConfig']);

// Add transfer specific constants used for Text Tag Replacement
$obj_AccountXML = simplexml_load_string($obj_mPoint->getAccountInfo($_SESSION['obj_Info']->getInfo("accountid") ) );
define("iACCOUNT_BALANCE", (integer) $obj_AccountXML->balance); 
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH,
							   "MIN TRANSFER" => $_SESSION['obj_CountryConfig']->getMinTransfer(), "ACCOUNT BALANCE" => iACCOUNT_BALANCE / 100) );

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
		case "amount":	// Validate Amount
			$aErrCd["amount"] = $obj_Validator->valAmount(iACCOUNT_BALANCE, (integer) $input);
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
		case "recipient":	// Validate recipient
			if ($obj_Validator->valCountry($_OBJ_DB, (integer) $obj_XML->countryid) == 10)
			{
				$oXML = simplexml_load_string($obj_mPoint->getCountries() );
				$oXML = $oXML->xpath("/countries/item[@id = ". $obj_XML->countryid ."]");
				$oXML = $oXML[0];

				$obj_CountryConfig = new CountryConfig($oXML["id"], (string) $oXML->name, (string) $oXML->currency, (string) $oXML->currency["symbol"], (integer) $oXML->maxbalance, (integer) $oXML->mintransfer, (float) $oXML->minmobile, (float) $oXML->maxmobile, (string) $oXML->channel, (string) $oXML->priceformat, (integer) $oXML->decimals, General::xml2bool( (string) $oXML->addresslookup), General::xml2bool( (string) $oXML->doubleoptin) );
				$obj_Validator = new Validate($obj_CountryConfig);
				
				$_OBJ_TXT->loadConstants(array("MIN MOBILE" => $obj_CountryConfig->getMinMobile(), "MAX MOBILE" => $obj_CountryConfig->getMaxMobile() ) );

				$aErrCd["recipient"] = $obj_Validator->valMobile( (string) $obj_XML->recipient);
				if ($aErrCd["recipient"] < 10 && floatval($obj_XML->recipient) == 0) { $aErrCd["recipient"] = $obj_Validator->valEMail( (string) $obj_XML->recipient) + 10; }
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
		
		$_OBJ_TXT->loadConstants(array("MIN MOBILE" => $obj_CountryConfig->getMinMobile(), "MAX MOBILE" => $obj_CountryConfig->getMaxMobile() ) );

		$aErrCd["recipient"] = $obj_Validator->valMobile( (string) $obj_XML->form->recipient);
		if ($aErrCd["recipient"] < 10 && floatval($obj_XML->recipient) == 0) { $aErrCd["recipient"] = $obj_Validator->valEMail( (string) $obj_XML->recipient) + 10; }
	}
	else { $aErrCd["countryid"] = 1; }
	$aErrCd["amount"] = $obj_Validator->valAmount(iACCOUNT_BALANCE, (integer) $obj_XML->form->amount);
	
	// Check return codes for errors
	while (list($tag, $code) = each($aErrCd) )
	{
		// Error found in Input
		if ($code < 10)
		{
			$xml .= '<'. $tag .' id="'. $code .'">'. htmlspecialchars($_OBJ_TXT->_($tag ." - code: ". $code), ENT_NOQUOTES) .'</'. $tag .'>';
		}
	}

	// recipient / Password validated
	if (empty($xml) === true)
	{
		// National Transfer
		if ($obj_CountryConfig->getID() == $_SESSION['obj_CountryConfig']->getID() )
		{
			$iAmountSent = intval($obj_XML->form->amount) * 100;
			$iAmountReceived = intval($obj_XML->form->amount) * 100;
		}
		// International Remittance
		else
		{
			$iAmountSent = intval($obj_XML->form->amount) * 100;
			$iAmountReceived = $obj_mPoint->convert($obj_CountryConfig, intval($obj_XML->form->amount) * 100);
		}
		
		$iAccountID = $obj_mPoint->getAccountID( (string) $obj_XML->form->recipient);
		// Currency conversion successful for Amount - Verify that recipient's balance doesn't exceed allowed amount
		if ($iAccountID > 0 && $iAmountReceived > 0)
		{
			$obj_XML = simplexml_load_string($obj_mPoint->getAccountInfo($iAccountID) );
			if (intval($obj_XML->balance) + $iAmountReceived > $obj_CountryConfig->getMaxBalance() )
			{
				$iAmountReceived = -4;
			}
		}
		
		// Currency conversion successful for Amount and recipient's balance doesn't exceed allowed amount
		if ($iAmountReceived > 0)
		{
			// Recipient doesn't have an account yet
			if ($iAccountID <= 0)
			{
				if ($obj_Validator->valMobile( (string) $obj_XML->form->recipient) == 10)
				{
					$mob = (string) $obj_XML->form->recipient;
					$email = "";
				}
				else
				{
					$mob = "";
					$email = (string) $obj_XML->form->recipient;
				}
				$iAccountID = $obj_mPoint->newAccount($obj_CountryConfig->getID(), $mob, "", $email);
				// Account successfully created - send notification SMS to recipient
				if ($iAccountID > 0 && empty($mob) === false)
				{
					$code = $obj_mPoint->sendNewAccountSMS(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO), $iAccountID, $obj_AccountXML, $iAmountReceived) + 1;
				}
				// Account successfully created - send notification E-Mail to recipient
				elseif ($iAccountID > 0)
				{
					$code = $obj_mPoint->sendNewAccountEMail($iAccountID, $obj_AccountXML, $iAmountReceived) + 2;
				}
				// Error: Unable to create new account
				else { $code = 1; }
			}
			else { $code = 10; }
			
			// Success: Make Transfer
			if ($code >= 10)
			{
				$code = $obj_mPoint->makeTransfer($iAccountID, $_SESSION['obj_Info']->getInfo("accountid"), $iAmountReceived, $iAmountSent) + $code - 10;
				
				// Transfer sucessful
				if ($code >= 10)
				{
					$sType = "multipart";
					$xml = '<document type="status">
								<form id="'. ($code + 90) .'" name="'. (string) $obj_XML->form['name'] .'">'. htmlspecialchars($_OBJ_TXT->_("transfer - code: ". ($code + 90) ), ENT_NOQUOTES) .'</form>
							</document>
							<document type="command">
							<recache>
							 	<url>/home/topmenu.php</url>
							 	<url>/home/transfer.php</url>
							</recache>
							</document>
							<document type="command">
								<redirect>
							 		<url>/home/topmenu.php</url>
							 	</redirect>
							 	<redirect>
							 		<url>/home/transfer.php</url>
							 	</redirect>
							</document>';
				}
				// Error during transfer, return status code and message
				else
				{
					$xml = '<form id="91" name="'. (string) $obj_XML->form['name'] .'">'. htmlspecialchars($_OBJ_TXT->_("transfer - code: 91"), ENT_NOQUOTES) .'</form>';
				}
			}
			// Error during account creation
			else { $xml .= '<form id="'. $code .'">'. htmlspecialchars($_OBJ_TXT->_("account - code: ". $code), ENT_NOQUOTES) .'</form>'; }
		}
		// Error: Unable to make currency conversion for Amount
		else { $xml .= '<amount id="'. abs($iAmountReceived) + 3 .'">'. htmlspecialchars($_OBJ_TXT->_("amount - code: ". abs($iAmountReceived) + 3), ENT_NOQUOTES) .'</amount>'; }
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