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
// Require specific Business logic for the CPM GATEWAY component
require_once(sINTERFACE_PATH ."/cpm_gateway.php");
// Require specific Business logic for the DSB PSP component
require_once(sCLASS_PATH ."/dsb.php");
// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
// Require Data Class for Client Information
require_once(sCLASS_PATH ."/clientinfo.php");
// Require Data Class for Client Account Information
require_once(sCLASS_PATH ."/account_config.php");
// Require Data class for Payment processor
require_once(sCLASS_PATH ."/payment_processor.php");
// Require Data class for Wallet processor
require_once(sCLASS_PATH ."/wallet_processor.php");
// Require specific Business logic for the Google Pay component
require_once(sCLASS_PATH ."/googlepay.php");
$aMsgCds = array();

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );
/*
$_SERVER['PHP_AUTH_USER'] = "1415";
$_SERVER['PHP_AUTH_PW'] = "Ghdy4_ah1G";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<get-payment-methods>';
$HTTP_RAW_POST_DATA .= '<client-id>10019</client-id>';
$HTTP_RAW_POST_DATA .= '<account>100026</account>';
$HTTP_RAW_POST_DATA .= '<transaction">';
$HTTP_RAW_POST_DATA .= '<country-id>100</country-id>';
$HTTP_RAW_POST_DATA .= '<amount>100</amount>';
$HTTP_RAW_POST_DATA .= '</transaction>';
$HTTP_RAW_POST_DATA .= '</get-payment-methods>';
$HTTP_RAW_POST_DATA .= '</root>';
*/
$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);
// print_r($obj_DOM);exit;

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."get_payment_methods.xsd") === true && count($obj_DOM->{'get-payment-methods'}) > 0)
	{
        $obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);
        
		for ($i=0; $i<count($obj_DOM->{'get-payment-methods'}); $i++)
		{
            // Validate basic information
			$code = Validate::valBasic($_OBJ_DB, (integer) $obj_DOM->{'get-payment-methods'}[$i]->{'client-id'}, (integer) $obj_DOM->{'get-payment-methods'}[$i]->{'account'});
			if ($code == 100)
			{
                $obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'get-payment-methods'}[$i]->{"client-id"});
   
                if ($obj_ClientConfig->getUsername() == trim($_SERVER['PHP_AUTH_USER']) && $obj_ClientConfig->getPassword() == trim($_SERVER['PHP_AUTH_PW']) && $obj_ClientConfig->hasAccess($_SERVER['REMOTE_ADDR']) === true)
				{

					$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'get-payment-methods'}[$i]->transaction->{'country-id'});

                    $obj_Validator = new Validate($obj_ClientConfig->getCountryConfig() );
                    $iValResult = $obj_Validator->valPrice($obj_ClientConfig->getMaxAmount(), (integer) $obj_DOM->{'get-payment-methods'}[$i]->transaction->amount);
					if ($obj_ClientConfig->getMaxAmount() > 0 && $iValResult != 10) { $aMsgCds[$iValResult + 50] = (string) $obj_DOM->{'get-payment-methods'}[$i]->transaction->amount; }

					// Validate currency if explicitly passed in request, which defer from default currency of the country
					if(intval($obj_DOM->{'get-payment-methods'}[$i]->transaction->{"currency-id"}) > 0){
						if($obj_Validator->valCurrency($_OBJ_DB, intval($obj_DOM->{'get-payment-methods'}[$i]->transaction->{"currency-id"}) ,$obj_CountryConfig, intval( $obj_DOM->{'get-payment-methods'}[$i]->{"client-id"})) != 10 ){
							$aMsgCds[56] = "Invalid Currency:".intval($obj_DOM->{'get-payment-methods'}[$i]->transaction->{"currency-id"}) ;
						}
					}

					// Success: Input Valid
					if (count($aMsgCds) == 0)
					{
                        try {

                            $xml .= '<payment_methods>';

                            $objXml = ClientPaymentMethodConfig::getClientAccountPaymentMethods($_OBJ_DB, $obj_ClientConfig, $obj_CountryConfig, (integer) $obj_DOM->{'get-payment-methods'}[$i]->transaction->amount);
                            $obj_XML = simplexml_load_string($objXml, "SimpleXMLElement", LIBXML_COMPACT);

                            $cardsXML = '<cards>';
							for ($j=0; $j<count($obj_XML->item); $j++)
							{
								// Only payments methods will be returned.
								if ($obj_XML->item[$j]["type-id"] != 11)
								{
                                    $cardsXML .= '<card>';
                                    $cardsXML .= '<id>'. $obj_XML->item[$j]["id"] .'</id>';
                                    $cardsXML .= '<type-id>'. $obj_XML->item[$j]["type-id"] .'</type-id>';
                                    $cardsXML .= '<psp-id>'. $obj_XML->item[$j]["pspid"] .'</psp-id>';
                                    $cardsXML .= '<min-length>'. $obj_XML->item[$j]["min-length"] .'</min-length>';
                                    $cardsXML .= '<max-length>'. $obj_XML->item[$j]["max-length"] .'</max-length>';
                                    $cardsXML .= '<cvc-length>'. $obj_XML->item[$j]["cvc-length"] .'</cvc-length>';
                                    $cardsXML .= '<state-id>'. $obj_XML->item[$j]["state-id"] .'</state-id>';
                                    $cardsXML .= '<payment-type>'. $obj_XML->item[$j]["payment-type"] .'</payment-type>';
									$cardsXML .= '<name>'. htmlspecialchars($obj_XML->item[$j]->name, ENT_NOQUOTES) .'</name>';
									$cardsXML .= $obj_XML->item[$j]->prefixes->asXML();
									$cardsXML .= htmlspecialchars($obj_XML->item[$j]->name, ENT_NOQUOTES);	// Backward compatibility
									$cardsXML .= '</card>';
								}
							}
							$cardsXML .= '</cards>';
                            $xml .= $cardsXML;
                            $xml .='</payment_methods>';
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