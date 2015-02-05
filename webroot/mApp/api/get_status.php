<?php
/**
 * This files contains the Controller for mPoint's Mobile Web API.
 * The Controller will ensure that all input from a Mobile Internet Site or Mobile Application is validated and a new payment transaction is started.
 * Finally, assuming the Client Input is valid, the Controller will redirect the Customer to one of the following flow start pages:
 * 	- Payment Flow: /pay/card.php
 * 	- Shop Flow: /shop/delivery.php
 * If the input provided was determined to be invalid, an error page will be generated.
 *
 * @author Johan Thomsen
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package API
 * @subpackage MobileApp
 */

// Require Global Include File
require_once("../../inc/include.php");

// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");
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
$HTTP_RAW_POST_DATA .= '<get-status client-id="10007">';
$HTTP_RAW_POST_DATA .= '<transactions>';
$HTTP_RAW_POST_DATA .= '<transaction>123456</transaction>';
$HTTP_RAW_POST_DATA .= '</transactions>';
$HTTP_RAW_POST_DATA .= '<client-info platform="iOS" version="1.00" language="da">';
$HTTP_RAW_POST_DATA .= '<mobile country-id="100" operator-id="10000">28882861</mobile>';
$HTTP_RAW_POST_DATA .= '<email>jona@oismail.com</email>';
$HTTP_RAW_POST_DATA .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
$HTTP_RAW_POST_DATA .= '</client-info>';
$HTTP_RAW_POST_DATA .= '</get-status>';
$HTTP_RAW_POST_DATA .= '</root>';
*/
$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mpoint.xsd") === true && count($obj_DOM->{'get-status'}) > 0)
	{	
		$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);
		$obj_Validator = new Validate();
		$xml = '';
		
		// Set Global Defaults
		if (empty($obj_DOM->{'get-status'}["account"]) === true || intval($obj_DOM->{'get-status'}["account"]) < 1) { $obj_DOM->{'get-status'}["account"] = -1; }
	
		// Validate basic information
		$code = Validate::valBasic($_OBJ_DB, (integer) $obj_DOM->{'get-status'}["client-id"], (integer) $obj_DOM->{'get-status'}["account"]);
		if ($code == 100)
		{
			$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'get-status'}["client-id"], (integer) $obj_DOM->{'get-status'}["account"]);
			
			// Client successfully authenticated
			if ($obj_ClientConfig->getUsername() == trim($_SERVER['PHP_AUTH_USER']) && $obj_ClientConfig->getPassword() == trim($_SERVER['PHP_AUTH_PW']) )
			{
				$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'get-status'}->{'client-info'}->mobile["country-id"]);
				if ( ($obj_CountryConfig instanceof CountryConfig) === false) { $obj_CountryConfig = $obj_ClientConfig->getCountryConfig(); }
				
				$obj_mPoint = new Home($_OBJ_DB, $_OBJ_TXT, $obj_CountryConfig);

				// Basic input valid
				if (count($aMsgCds) == 0)
				{
					foreach ($obj_DOM->{'get-status'}->transactions as $t)
					{
						try
						{
							$obj_txnInfo = TxnInfo::produceInfo( (integer)$t->transaction, $_OBJ_DB);
							$aMessages = $obj_txnInfo->getMessageHistory($_OBJ_DB);
							$obj_CountryConfig = $obj_txnInfo->getCountryConfig();

							$aCurrentState = @$aMessages[0];
							foreach ($aMessages as $m)
							{
								$iMessageID = (integer)$m["ID"];
								$iStateID = (integer)$m["STATEID"];
								// Marks the newest state >= iPAYMENT_ACCEPTED_STATE (2000) as the current state
								if ( (integer)$aCurrentState["STATEID"] < Constants::iPAYMENT_ACCEPTED_STATE &&
											  $iStateID >= Constants::iPAYMENT_ACCEPTED_STATE) { $aCurrentState = $m; }

								$historyXml .= '<message id="'. (integer)$m["ID"]. '" state="'. (integer)$m["STATEID"]. '">';
								$historyXml .= '<timestamp>'. date('c', strtotime($m["CREATED"]) ). '</timestamp>';
								$historyXml .= '</message>';
							}

							$xml .= '<transactionHistory mpoint-id="'. $obj_txnInfo->getID(). '" type="'. $obj_txnInfo->getTypeID() .'" currentState="'. @$aCurrentState["ID"] .'">';
							$xml .= '<amount currency="'. $obj_CountryConfig->getCurrency() .'" symbol="'. $obj_CountryConfig->getSymbol() .'">'. $obj_txnInfo->getAmount(). '</amount>';
							if ($obj_txnInfo->getFee() > 0) { $xml .= '<fee currency="'. $obj_CountryConfig->getCurrency() .'" symbol="'. $obj_CountryConfig->getSymbol() .'">'. $obj_txnInfo->getFee() .'</fee>'; }
							if ($obj_txnInfo->getCapturedAmount() > 0) { $xml .= '<captured currency="'. $obj_CountryConfig->getCurrency() .'" symbol="'. $obj_CountryConfig->getSymbol() .'">'. $obj_txnInfo->getCapturedAmount() .'</captured>'; }
							if ($obj_txnInfo->getRefund() > 0) { $xml .= '<refunded currency="'. $obj_CountryConfig->getCurrency() .'" symbol="'. $obj_CountryConfig->getSymbol() .'">' . $obj_txnInfo->getRefund() .'</refunded>'; }

							if (count($aMessages) > 0)
							{
								$xml .= '<history>';
								$xml .= $historyXml;
								$xml .= '</history>';
							} else { $xml .= '<history />'; }
							$xml .= '</transactionHistory>';
						}
						catch (TxnInfoException $e)
						{
							$xml .= '<status code="404">'. htmlspecialchars($e->getMessage() ). '</status>';
						}
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
	// Error: Invalid XML Document
	elseif ( ($obj_DOM instanceof SimpleDOMElement) === false)
	{
		header("HTTP/1.1 415 Unsupported Media Type");
		
		$xml = '<status code="415">Invalid XML Document</status>';
	}
	// Error: Wrong operation
	elseif (count($obj_DOM->{'get-status'}) == 0)
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