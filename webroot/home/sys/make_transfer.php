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

// Error: Unauthorized access
if (General::val() != 1000)
{
?>
	<root type="command">
		<redirect>
			<url>/internal/unauthorized.php?code=<?= General::val(); ?></url>
		</redirect>
	</root>
<?php
}
// Success: Access granted
else
{
	// Initialize Standard content Object
	$obj_mPoint = new Transfer($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_CountryConfig']);
	$obj_Validator = new Validate($_SESSION['obj_CountryConfig']);
	
	// Add transfer specific constants used for Text Tag Replacement
	$obj_AccountXML = simplexml_load_string($obj_mPoint->getAccountInfo($_SESSION['obj_Info']->getInfo("accountid") ) );
	define("iACCOUNT_BALANCE", (integer) $obj_AccountXML->balance); 
	$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH,
								   "MIN TRANSFER" => General::formatAmount($_SESSION['obj_CountryConfig'], $_SESSION['obj_CountryConfig']->getMinTransfer() ), "ACCOUNT BALANCE" => General::formatAmount($_SESSION['obj_CountryConfig'], iACCOUNT_BALANCE) ) );
	
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
			case "code":	// Validate Confirmation Code (OTP)
				$aErrCd["code"] = $obj_Validator->valCode( (integer) $input);
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
			
			$_OBJ_TXT->loadConstants(array("MIN MOBILE" => $obj_CountryConfig->getMinMobile(), "MAX MOBILE" => $obj_CountryConfig->getMaxMobile() ) );
			
			// Validate Input
			foreach ($obj_XML as $input)
			{
				switch ($input->getName() )
				{
				case "recipient":	// Validate recipient
					$aErrCd["recipient"] = $obj_Validator->valMobile( (string) $obj_XML->recipient);
					if ($aErrCd["recipient"] < 10 && floatval($obj_XML->recipient) == 0) { $aErrCd["recipient"] = $obj_Validator->valEMail( (string) $obj_XML->recipient) + 3; }
					break;
				case "amount":	// Validate Amount
					$oXML = simplexml_load_string($obj_mPoint->getFees(Constants::iTRANSFER_FEE, $_SESSION['obj_CountryConfig']->getID() ) );
					$oXML = $oXML->xpath("/fees/item[@toid = ". $obj_CountryConfig->getID() ."]");
					$oXML = $oXML[0];
					if (intval($oXML->basefee) + intval($obj_XML->amount) * floatval($oXML->share) > intval($oXML->minfee) ) { $iFee = intval($oXML->basefee) + intval($obj_XML->amount) * floatval($oXML->share); }
					else { $iFee = (integer) $oXML->minfee / 100; }
					$_OBJ_TXT->loadConstants(array("ACCOUNT BALANCE" => General::formatAmount($_SESSION['obj_CountryConfig'], iACCOUNT_BALANCE - $iFee * 100) ) );
					
					$aErrCd["amount"] = $obj_Validator->valAmount(iACCOUNT_BALANCE, intval($obj_XML->amount) + $iFee);
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
		if ($aErrCd["countryid"] == 10)
		{
			$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_XML->form->countryid);
			$obj_Validator = new Validate($obj_CountryConfig);
			
			$_OBJ_TXT->loadConstants(array("MIN MOBILE" => $obj_CountryConfig->getMinMobile(), "MAX MOBILE" => $obj_CountryConfig->getMaxMobile() ) );
	
			$aErrCd["recipient"] = $obj_Validator->valMobile( (string) $obj_XML->form->recipient);
			if ($aErrCd["recipient"] < 10 && floatval($obj_XML->recipient) == 0) { $aErrCd["recipient"] = $obj_Validator->valEMail( (string) $obj_XML->recipient) + 10; }
			
			$oXML = simplexml_load_string($obj_mPoint->getFees(Constants::iTRANSFER_FEE, $_SESSION['obj_CountryConfig']->getID() ) );
			$oXML = $oXML->xpath("/fees/item[@toid = ". $obj_CountryConfig->getID() ."]");
			$oXML = $oXML[0];
			if (intval($oXML->basefee) + intval($obj_XML->form->amount) * floatval($oXML->share) > intval($oXML->minfee) ) { $iFee = intval($oXML->basefee) + intval($obj_XML->form->amount) * floatval($oXML->share); }
			else { $iFee = (integer) $oXML->minfee / 100; }
			$_OBJ_TXT->loadConstants(array("ACCOUNT BALANCE" => General::formatAmount($_SESSION['obj_CountryConfig'], iACCOUNT_BALANCE + $iFee) ) );
			
			$aErrCd["amount"] = $obj_Validator->valAmount(iACCOUNT_BALANCE, intval($obj_XML->form->amount) + $iFee);
			
		}
		else { $aErrCd["countryid"] = 1; }
		// Password provided
		if (count($obj_XML->form->password) > 0)
		{
			$aErrCd["password"] = $obj_Validator->valPassword( (string) $obj_XML->form->password);
		}
		// Confirmation Code (OTP) provided
		if (count($obj_XML->form->code) > 0)
		{
			$aErrCd["code"] = $obj_Validator->valCode( (integer) $obj_XML->form->code);
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
	
		// Transfer Data validated
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
			
			$iAccountID = $obj_mPoint->getAccountID($obj_CountryConfig, (string) $obj_XML->form->recipient);
			// Currency conversion successful for Amount - Verify that recipient's balance doesn't exceed allowed amount
			if ($iAccountID > 0 && $iAmountReceived > 0)
			{
				$obj_AccountXML = simplexml_load_string($obj_mPoint->getAccountInfo($iAccountID) );
				if (intval($obj_AccountXML->balance) + $iAmountReceived > $obj_CountryConfig->getMaxBalance() )
				{
					$iAmountReceived = -4;
				}
			}
			
			// Currency conversion successful for Amount and recipient's balance doesn't exceed allowed amount
			if ($iAmountReceived > 0)
			{
				// Fetch sender's account info
				$obj_AccountXML = simplexml_load_string($obj_mPoint->getAccountInfo($_SESSION['obj_Info']->getInfo("accountid") ) );
				
				// Both Password has been and either no mobile number is registered for the account or a Confirmation Code (OTP) has been provided as well
				if (count($obj_XML->form->password) > 0 && (floatval($obj_AccountXML->mobile) < $_SESSION['obj_CountryConfig']->getMinMobile() || count($obj_XML->form->code) > 0) )
				{
					// Start database transaction
					$_OBJ_DB->query("BEGIN");
					
					// Authenticate sender
					$aErrCd["password"] = $obj_mPoint->auth($_SESSION['obj_Info']->getInfo("accountid"), (string) $obj_XML->form->password) + 3;
					if (count($obj_XML->form->code) > 0) { $aErrCd["code"] = $obj_mPoint->activateCode($_SESSION['obj_Info']->getInfo("accountid"), (integer) $obj_XML->form->code) + 3; }
					else { $aErrCd["code"] = 10; }
					
					// Authentication successful
					if ($aErrCd["password"] >= 10 && $aErrCd["code"] >= 10)
					{
						// Recipient doesn't have an account yet
						if ($iAccountID <= 0)
						{
							// Recipient's Mobile Number provided by sender
							if ($obj_Validator->valMobile( (string) $obj_XML->form->recipient) == 10)
							{
								$mob = (string) $obj_XML->form->recipient;
								$email = "";
							}
							// Recipient's E-Mail Address provided by sender							
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
							$code = $obj_mPoint->makeTransfer($iAccountID, $_SESSION['obj_Info']->getInfo("accountid"), $iAmountReceived, $iAmountSent, $iFee * 100) + $code - 10;
							
							// Transfer sucessful
							if ($code >= 10)
							{
								// Commit database transaction
								$_OBJ_DB->query("COMMIT");
								
								$sType = "multipart";
								$xml = '<document type="status">
											<form id="'. ($code + 90) .'" name="'. (string) $obj_XML->form['name'] .'">'. htmlspecialchars($_OBJ_TXT->_("transfer - code: ". ($code + 90) ), ENT_NOQUOTES) .'</form>
										</document>
										<document type="command">
											<recache>
											 	<url>/home/topmenu.php</url>
											 	<url>/home/transfer.php</url>
											 	<url>/home/topup.php</url>
											 	<url>/home/history.php</url>
											</recache>
										</document>
										<document type="command">
											<close>
												<popup>confirm-transfer</popup>
											</close>
										</document>
										<document type="command" msg="status">
											<redirect>
										 		<url>/home/topmenu.php</url>
										 		<url>/home/transfer.php</url>
										 	</redirect>
										</document>';
							}
							// Error during transfer, return status code and message
							else
							{
								// Abort database transaction and rollback to previous state
								$_OBJ_DB->query("ROLLBACK");
								$xml = '<form id="91" name="'. (string) $obj_XML->form['name'] .'">'. htmlspecialchars($_OBJ_TXT->_("transfer - code: 91"), ENT_NOQUOTES) .'</form>';
							}
						}
						// Error during account creation
						else
						{
							// Abort database transaction and rollback to previous state
							$_OBJ_DB->query("ROLLBACK");
							$xml .= '<form id="'. $code .'">'. htmlspecialchars($_OBJ_TXT->_("account - code: ". $code), ENT_NOQUOTES) .'</form>';
						}
					}
					// Error during authentication
					else
					{
						// Abort database transaction and rollback to previous state
						$_OBJ_DB->query("ROLLBACK");
						
						// Check return codes for errors
						foreach ($aErrCd as $tag => $code)
						{
							// Error found in Input
							if ($code < 10)
							{
								$xml .= '<'. $tag .' id="'. $code .'">'. htmlspecialchars($_OBJ_TXT->_($tag ." - code: ". $code), ENT_NOQUOTES) .'</'. $tag .'>';
							}
						}
					}
				}
				// Send Confirmation Code (OTP)
				else
				{
					if (floatval($obj_AccountXML->mobile) < $_SESSION['obj_CountryConfig']->getMinMobile() ) { $code = 199; }
					else { $code = $obj_mPoint->sendConfirmationCode(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO), $_SESSION['obj_Info']->getInfo("accountid"), (string) $obj_AccountXML->mobile); }
					
					// Confirmation Code (OTP) sent
					if ($code == 200 || $code == 199)
					{
						$sType = "multipart";
						$xml = '<document type="status">
									<form id="'. $code .'" name="make-transfer">'. htmlspecialchars($_OBJ_TXT->_("transfer - code: ". $code), ENT_NOQUOTES) .'</form>
								</document>
								<document type="popup">
									<popup>
										<name>confirm-transfer</name>
										<parent>left-menu</parent>
										<url>/home/confirm.php</url>
								 		<css>confirm-transfer</css>
								 	</popup>
								</document>';
					}
					// Error: Unable to send Confirmation Code (OTP)
					else
					{
						$xml = '<form id="92" name="'. $obj_XML["name"] .'">'. htmlspecialchars($_OBJ_TXT->_("transfer - code: 92"), ENT_NOQUOTES) .'</form>';
					}
				}
			}
			// Error: Unable to make currency conversion for Amount
			else { $xml .= '<amount id="'. (abs($iAmountReceived) + 3) .'">'. htmlspecialchars($_OBJ_TXT->_("amount - code: ". (abs($iAmountReceived) + 3) ), ENT_NOQUOTES) .'</amount>'; }
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
<?php
}	// Access validation end
?>