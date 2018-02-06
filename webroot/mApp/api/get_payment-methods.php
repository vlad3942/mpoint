<?php
/**
 * This files contains the Controller for mPoint's Mobile Web API.
 * The Controller will ensure that all input from a Mobile Internet Site or Mobile Application is validated and shows all payment methods for the client.
 * If the input provided was determined to be invalid, an error page will be generated.
 *
 * @author Manish Diwani
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

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require Business logic for the Mobile Web module
require_once(sCLASS_PATH ."/mobile_web.php");
// Require Business logic for the Payment Method component
require_once(sCLASS_PATH ."/credit_card.php");
// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for the CPM PSP component
require_once(sINTERFACE_PATH ."/cpm_psp.php");
// Require specific Business logic for the CPM ACQUIRER component
require_once(sINTERFACE_PATH ."/cpm_acquirer.php");
// Require specific Business logic for the DSB PSP component
require_once(sCLASS_PATH ."/dsb.php");
// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
// Require Data Class for Client Information
require_once(sCLASS_PATH ."/clientinfo.php");
// Require Data Class for Client Account Information
require_once(sCLASS_PATH ."/account_config.php");

$aMsgCds = array();

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );
/*
$_SERVER['PHP_AUTH_USER'] = "1415";
$_SERVER['PHP_AUTH_PW'] = "Ghdy4_ah1G";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<get-payment-methods client-id="10019" account="100026">';
$HTTP_RAW_POST_DATA .= '<transaction">';
$HTTP_RAW_POST_DATA .= '<amount country-id="100">2400</amount>';
$HTTP_RAW_POST_DATA .= '</transaction>';
$HTTP_RAW_POST_DATA .= '</get-payment-methods>';
$HTTP_RAW_POST_DATA .= '</root>';
*/
$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mpoint.xsd") === true && count($obj_DOM->{'get-payment-methods'}) > 0)
	{
		$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);

		for ($i=0; $i<count($obj_DOM->{'get-payment-methods'}); $i++)
		{

			// Validate basic information
			$code = Validate::valClient($_OBJ_DB, (integer) $obj_DOM->{'get-payment-methods'}[$i]["client-id"]);
			
			if ($code == 100)
			{
                if (isset($obj_DOM->{'get-payment-methods'}[$i]->transaction["id"]) === true)
                {
                    $iTxnID = intval($obj_DOM->{'get-payment-methods'}[$i]->transaction["id"]);
                }
                $obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $_OBJ_DB);
				$obj_ClientConfig = $obj_TxnInfo->getClientConfig();
				$obj_ClientAccountsConfig = AccountConfig::produceConfigurations($_OBJ_DB, $obj_ClientConfig->getID());
				if ($obj_ClientConfig->getUsername() == trim($_SERVER['PHP_AUTH_USER']) && $obj_ClientConfig->getPassword() == trim($_SERVER['PHP_AUTH_PW'])
					&& $obj_ClientConfig->hasAccess($_SERVER['REMOTE_ADDR']) === true)
				{
					$obj_CountryConfig = $obj_TxnInfo->getCountryConfig();
					if ( ($obj_CountryConfig instanceof CountryConfig) === false || $obj_CountryConfig->getID() < 1) { $obj_CountryConfig = $obj_ClientConfig->getCountryConfig(); }
					
					$obj_Validator = new Validate($obj_ClientConfig->getCountryConfig() );

					$iValResult = $obj_Validator->valPrice($obj_ClientConfig->getMaxAmount(), (integer) $obj_DOM->{'get-payment-methods'}[$i]->transaction->amount);
					if ($obj_ClientConfig->getMaxAmount() > 0 && $iValResult != 10) { $aMsgCds[$iValResult + 50] = (string) $obj_DOM->{'get-payment-methods'}[$i]->transaction->amount; }
					// Success: Input Valid
					if (count($aMsgCds) == 0)
					{
                        try {
                            $sOrderXML = '';
                            $aPSPs = array();
                            $sOrderID = $obj_TxnInfo->getOrderID();
                            if (empty($sOrderID) === false)
                            {
                                $aObj_OrderInfoConfigs = OrderInfo::produceConfigurationsFromOrderID($_OBJ_DB, $obj_TxnInfo->getOrderID());
                                if (count($aObj_OrderInfoConfigs) > 0)
                                {
                                    $sOrderXML .= '<orders>';
                                    foreach ($aObj_OrderInfoConfigs as $obj_OrderInfo) {
                                        $sOrderXML .= $obj_OrderInfo->toXML();
                                    }
                                    $sOrderXML .= '</orders>';
                                }
                            }

						    $obj_XML = simplexml_load_string($obj_TxnInfo->toXML(), "SimpleXMLElement", LIBXML_COMPACT);
                            $xml .= '';
                            $xml .= '<client-config id="'. $obj_ClientConfig->getID() .'" account="'. $obj_ClientConfig->getAccountConfig()->getID() .'" store-card="'. $obj_ClientConfig->getStoreCard() .'" auto-capture="'. General::bool2xml($obj_ClientConfig->useAutoCapture() ) .'" mode="'. $obj_ClientConfig->getMode() .'">';
                            $xml .= '<name>'. htmlspecialchars($obj_ClientConfig->getName(), ENT_NOQUOTES) .'</name>';
                            $xml .= '<callback-url>'. htmlspecialchars($obj_ClientConfig->getCallbackURL(), ENT_NOQUOTES) .'</callback-url>';
                            $xml .= '<accept-url>'. htmlspecialchars($obj_ClientConfig->getAcceptURL(), ENT_NOQUOTES) .'</accept-url>';
                            $xml .= '<cancel-url>'. htmlspecialchars($obj_ClientConfig->getCancelURL(), ENT_NOQUOTES) .'</cancel-url>';
                            $xml .= '<app-url>'. htmlspecialchars($obj_ClientConfig->getAppURL(), ENT_NOQUOTES) .'</app-url>';
                            $xml .= '<css-url>'. htmlspecialchars($obj_ClientConfig->getCSSURL(), ENT_NOQUOTES) .'</css-url>';
                            $xml .= '<logo-url>'. htmlspecialchars($obj_ClientConfig->getLogoURL(), ENT_NOQUOTES) .'</logo-url>';
                            $xml .= '<base-image-url>'. htmlspecialchars($obj_ClientConfig->getBaseImageURL(), ENT_NOQUOTES) .'</base-image-url>';
                            $xml .= '<additional-config>';
                            foreach ($obj_ClientConfig->getAdditionalProperties() as $aAdditionalProperty)
                            {
                                $xml .= '<property name="'.$aAdditionalProperty['key'].'">'.$aAdditionalProperty['value'].'</property>';
                            }
                            $xml .= '</additional-config>';
							$xml .= '<accounts>';
                            foreach ($obj_ClientAccountsConfig as $obj_AccountConfig)
                            {
                                $xml .= '<account id= "'. $obj_AccountConfig->getID() .'" markup= "'. $obj_AccountConfig->getMarkupLanguage() .'" />';
                            }
                            $xml .= '</accounts>'; 
                            $xml .= '</client-config>';
                            $xml .= '<transaction id="'. $obj_TxnInfo->getID() .'" order-no="'. htmlspecialchars($obj_TxnInfo->getOrderID(), ENT_NOQUOTES) .'" type-id="'. $obj_TxnInfo->getTypeID() .'" eua-id="'. $obj_TxnInfo->getAccountID() .'" language="'. $obj_TxnInfo->getLanguage() .'" auto-capture="'. General::bool2xml($obj_TxnInfo->useAutoCapture() ) .'" mode="'. $obj_TxnInfo->getMode() .'">';
                            $xml .= $obj_XML->amount->asXML();
                            if (empty($sOrderXML) === false )  { $xml .= $sOrderXML; }
                            if ($obj_TxnInfo->getPoints() > 0) { $xml .= $obj_XML->points->asXML(); }
                            if ($obj_TxnInfo->getReward() > 0) { $xml .= $obj_XML->reward->asXML(); }
                            $xml .= '<mobile country-id="'. $obj_CountryConfig->getID() .'" operator-id="'. $obj_TxnInfo->getOperator() .'">'. floatval($obj_TxnInfo->getMobile() ) .'</mobile>';
                            if (trim($obj_TxnInfo->getEMail() ) != "") { $xml .= $obj_XML->email->asXML(); }
                            $xml .= $obj_XML->{'callback-url'}->asXML();
                            $xml .= $obj_XML->{'accept-url'}->asXML();
                            $xml .= '</transaction>';
						    $obj_mPoint = new CreditCard($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo);
							$obj_XML = simplexml_load_string($obj_mPoint->getCards($obj_TxnInfo->getAmount() ), "SimpleXMLElement", LIBXML_COMPACT);
							$cardsXML = '<cards>';
							for ($j=0; $j<count($obj_XML->item); $j++)
							{
								// Card does not represent "My Account" or the End-User has an acccount with Stored Cards or Stored Value Account is available
								if ($obj_XML->item[$j]["type-id"] != 11
									|| ($obj_TxnInfo->getAccountID() > 0 && (count($aObj_XML) > 0 || $obj_ClientConfig->getStoreCard() == 2) ) )
								{
									if (in_array((integer) $obj_XML->item[$j]["pspid"], $aPSPs) === false) { $aPSPs[] = intval($obj_XML->item[$j]["pspid"] ); }
									$cardsXML .= '<card id="'. $obj_XML->item[$j]["id"] .'" type-id="'. $obj_XML->item[$j]["type-id"] .'" psp-id="'. $obj_XML->item[$j]["pspid"] .'" min-length="'. $obj_XML->item[$j]["min-length"] .'" max-length="'. $obj_XML->item[$j]["max-length"] .'" cvc-length="'. $obj_XML->item[$j]["cvc-length"] .'" state-id="'. $obj_XML->item[$j]["state-id"] .'"  payment-type="'. $obj_XML->item[$j]["payment-type"].'">';
									$cardsXML .= '<name>'. htmlspecialchars($obj_XML->item[$j]->name, ENT_NOQUOTES) .'</name>';
									$cardsXML .= $obj_XML->item[$j]->prefixes->asXML();
									$cardsXML .= htmlspecialchars($obj_XML->item[$j]->name, ENT_NOQUOTES);	// Backward compatibility
									$cardsXML .= '</card>';
								}
							}
							$cardsXML .= '</cards>';
							$xml .= $cardsXML;

							if(count($obj_DOM->{'get-payment-methods'}[$i]->{'client-info'}) > 0 )
                            {
                                $iAccountID = -1;
                                if(intval($obj_TxnInfo->getAccountID()) > 0)
                                {
                                    $iAccountID = $obj_TxnInfo->getAccountID();
                                }
                                else
                                {
                                    if (count($obj_DOM->{'get-payment-methods'}[$i]->{'client-info'}->{'customer-ref'}) == 1) { $iAccountID = EndUserAccount::getAccountIDFromExternalID($_OBJ_DB, $obj_ClientConfig, $obj_DOM->{'get-payment-methods'}[$i]->{'client-info'}->{'customer-ref'}, ($obj_ClientConfig->getStoreCard() <= 3) ); }
                                    if ($iAccountID < 0 && count($obj_DOM->{'get-payment-methods'}[$i]->{'client-info'}->mobile) == 1) { $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_DOM->{'get-payment-methods'}[$i]->{'client-info'}->mobile, $obj_CountryConfig, ($obj_ClientConfig->getStoreCard() <= 3) ); }
                                    if ($iAccountID < 0 && count($obj_DOM->{'get-payment-methods'}[$i]->{'client-info'}->email) == 1) { $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_DOM->{'get-payment-methods'}[$i]->{'client-info'}->email, $obj_CountryConfig, ($obj_ClientConfig->getStoreCard() <= 3) ); }
                                    if ($iAccountID < 0) { $iAccountID = $obj_mPoint->getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_DOM->{'get-payment-methods'}[$i]->{'client-info'}->mobile, $obj_CountryConfig); }
                                    if ($iAccountID < 0) { $iAccountID = $obj_mPoint->getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_DOM->{'get-payment-methods'}[$i]->{'client-info'}->email, $obj_CountryConfig); }
                                }

                                if($iAccountID > 0 && count($obj_XML->xpath("/cards/item[@type-id = 11]") ) == 1)
                                {
                                    $aObj_XML = simplexml_load_string($obj_mPoint->getStoredCards($iAccountID, $obj_ClientConfig), "SimpleXMLElement", LIBXML_COMPACT);
                                    if ($obj_ClientConfig->getStoreCard() <= 3) { $aObj_XML = $aObj_XML->xpath("/stored-cards/card[client/@id = ". $obj_ClientConfig->getID() ."]"); }
                                    else { $aObj_XML = $aObj_XML->xpath("/stored-cards/card"); }

                                    if (is_array($aObj_XML) === true && count($aObj_XML) > 0)
                                    {
                                        $xml .= '<stored-cards>';
                                        for ($j=0; $j<count($aObj_XML); $j++)
                                        {
                                            $xml .= '<card id="'. $aObj_XML[$j]["id"] .'" type-id="'. $aObj_XML[$j]->type["id"] .'" psp-id="'. $aObj_XML[$j]["pspid"] .'" preferred="'. $aObj_XML[$j]["preferred"] .'" state-id="'. $aObj_XML[$j]["state-id"] .'" charge-type-id="'. $aObj_XML[$j]["charge-type-id"] .'">';
                                            if (strlen($aObj_XML[$j]->name) > 0) { $xml .= $aObj_XML[$j]->name->asXML(); }
                                            $xml .= '<card-number-mask>'. $aObj_XML[$j]->mask .'</card-number-mask>';
                                            $xml .= $aObj_XML[$j]->expiry->asXML();
                                            if (strlen($aObj_XML[$j]->{'card-holder-name'}) > 0) { $xml .= $aObj_XML[$j]->{'card-holder-name'}->asXML(); }
                                            if (count($aObj_XML[$j]->address) == 1) { $xml .= $aObj_XML[$j]->address->asXML(); }
                                            $xml .= '</card>';
                                        }
                                        $xml .= '</stored-cards>';
                                    }
                                }
                            }


                        }
						// Internal Error
						catch (mPointException $e)
						{
							trigger_error("Unknown error: ". $e->getMessage() ."(". $e->getCode() .")" ."\n". $e->getTrace(), E_USER_WARNING);
	
							header("HTTP/1.1 500 Internal Server Error");
	
							$xml = '<status code="'. $e->getCode() .'">'. htmlspecialchars($e->getMessage(), ENT_NOQUOTES) .'</status>';
						}
					}
					// Error: Invalid Input
					else
					{
						header("HTTP/1.1 400 Bad Request");
					
						foreach ($aMsgCds as $code => $data)
						{
							$xml .= '<status code="'. $code .'">'. htmlspecialchars($data, ENT_NOQUOTES) .'</status>';
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
	elseif (count($obj_DOM->{'get-payment-methods'}) == 0)
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
echo preg_replace('~\s*(<([^>]*)>[^<]*</\2>|<[^>]*>)\s*~', '$1', $xml);
echo '</root>';
?>