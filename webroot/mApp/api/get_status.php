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
$_SERVER['PHP_AUTH_USER'] = "CPMTEST";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<get-status client-id="10024">';
$HTTP_RAW_POST_DATA .= '<transactions>';
$HTTP_RAW_POST_DATA .= '<transaction id="1813241" order-no="abc-123" />';
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

				$testingRequset = (boolean)$obj_DOM->{'get-status'}["test"];

				// Basic input valid
				if (count($aMsgCds) == 0)
				{
					foreach ($obj_DOM->{'get-status'}->transactions->transaction as $t)
					{
						try
						{
							//If order-no is supplied to API, use it in query for txninfo
							$misc = empty($t["order-no"]) === false ? array($t["order-no"]) : null;

							$obj_TxnInfo = TxnInfo::produceInfo( (integer) $t["id"], $_OBJ_DB, $misc);
							$aMessages = $obj_TxnInfo->getMessageHistory($_OBJ_DB,$testingRequset);
							$obj_CountryConfig = $obj_TxnInfo->getCountryConfig();

							$aCurrentState = @$aMessages[0];
							foreach ($aMessages as $m)
							{
								$iMessageID = (integer) $m["id"];
								$iStateID = (integer) $m["stateid"];
								// Marks the newest state >= iPAYMENT_ACCEPTED_STATE (2000) as the current state
								if (intval($aCurrentState["stateid"]) < Constants::iPAYMENT_ACCEPTED_STATE && $iStateID >= Constants::iPAYMENT_ACCEPTED_STATE)
								{
									$aCurrentState = $m;
								}

								$historyXml .= '<message id="'. $m["id"]. '" state-id="'. $m["stateid"]. '">';
								$historyXml .= '<timestamp>'. str_replace("T", " ", date("c", strtotime($m["created"]) ) ) .'</timestamp>';
                                if($testingRequset)
                                    $historyXml .= '<data>'. htmlspecialchars($m['data'], ENT_NOQUOTES) .'</data>';
								$historyXml .= '</message>';
							}

							$xml .= '<transaction id="'. $obj_TxnInfo->getID(). '" order-no="'. htmlspecialchars($obj_TxnInfo->getOrderID(), ENT_NOQUOTES) .'"  type-id="'. $obj_TxnInfo->getTypeID() .'" current-state="'. @$aCurrentState["id"] .'">';
							$xml .= '<amount country-id="'. $obj_CountryConfig->getID() .'" currency="'. $obj_CountryConfig->getCurrency() .'" symbol="'. $obj_CountryConfig->getSymbol() .'">'. $obj_TxnInfo->getAmount(). '</amount>';
							if ($obj_TxnInfo->getFee() > 0) { $xml .= '<fee country-id="'. $obj_CountryConfig->getID() .'" currency="'. $obj_CountryConfig->getCurrency() .'" symbol="'. $obj_CountryConfig->getSymbol() .'">'. $obj_TxnInfo->getFee() .'</fee>'; }
							if ($obj_TxnInfo->getCapturedAmount() > 0) { $xml .= '<captured country-id="'. $obj_CountryConfig->getID() .'" currency="'. $obj_CountryConfig->getCurrency() .'" symbol="'. $obj_CountryConfig->getSymbol() .'">'. $obj_TxnInfo->getCapturedAmount() .'</captured>'; }
							if ($obj_TxnInfo->getRefund() > 0) { $xml .= '<refunded country-id="'. $obj_CountryConfig->getID() .'" currency="'. $obj_CountryConfig->getCurrency() .'" symbol="'. $obj_CountryConfig->getSymbol() .'">' . $obj_TxnInfo->getRefund() .'</refunded>'; }

							if (count($aMessages) > 0)
							{
								$xml .= '<messages>';
								$xml .= $historyXml;
								$xml .= '</messages>';
							}
							else { $xml .= '<messages />'; }
							$xml .= '</transaction>';
						}
						catch (TxnInfoException $e)
						{
							header("HTTP/1.1 404 Not Found");
							
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