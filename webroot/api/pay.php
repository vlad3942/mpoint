<?php
/**
 * This files contains the Controller for mPoint's Mobile Web API.
 * The Controller will ensure that all input from a Mobile Internet Site or Mobile Application is validated and a new payment transaction is started.
 * Finally, assuming the Client Input is valid, the Controller will redirect the Customer to one of the following flow start pages:
 * 	- Payment Flow: /pay/card.php
 * 	- Shop Flow: /shop/delivery.php
 * If the input provided was determined to be invalid, an error page will be generated.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package API
 * @subpackage MobileApp
 * @version 1.10
 */

// Require Global Include File
require_once("../inc/include.php");

// Require the PHP API for handling the connection to GoMobile
require_once(sAPI_CLASS_PATH ."/gomobile.php");

// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require Business logic for the Mobile Web module
require_once(sCLASS_PATH ."/mobile_web.php");
// Require Business logic for the Select Credit Card component
require_once(sCLASS_PATH ."/credit_card.php");

// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for the DIBS component
require_once(sCLASS_PATH ."/dibs.php");
// Require specific Business logic for the WorldPay component
require_once(sCLASS_PATH ."/worldpay.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");

$aMsgCds = array();

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<client-id>10002</client-id>';
//$HTTP_RAW_POST_DATA .= '<account></account>';
$HTTP_RAW_POST_DATA .= '<amount>100</amount>';
//$HTTP_RAW_POST_DATA .= '<operator></operator>';
$HTTP_RAW_POST_DATA .= '<mobile country-id="100">28882861</mobile>';
$HTTP_RAW_POST_DATA .= '<email>jona@oismail.com</email>';
$HTTP_RAW_POST_DATA .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
$HTTP_RAW_POST_DATA .= '<callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url>';
$HTTP_RAW_POST_DATA .= '</root>';

$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mpoint.xsd") === true)
{
	// Set Global Defaults
	if (count($obj_DOM->account) == 0 || intval($obj_DOM->account) < 0) { $obj_DOM->account = -1; }
	
	$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);
	
	// Validate basic information
	if (Validate::valBasic($_OBJ_DB, (integer) $obj_DOM->{'client-id'}, (integer) $obj_DOM->account) == 100)
	{
		$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'client-id'}, (integer) $obj_DOM->account);
		$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->mobile["country-id"]);
		if (is_null($obj_CountryConfig) === true) { $obj_CountryConfig = $obj_ClientConfig->getCountryConfig(); }
		
		// Set Client Defaults
		if (count($obj_DOM->operator) == 0) { $obj_DOM->operator = $obj_CountryConfig->getID() * 100; }
		if (count($obj_DOM->{'callback-url'}) == 0) { $obj_DOM->{'callback-url'} = $obj_ClientConfig->getCallbackURL(); }
		
		$obj_mPoint = new MobileWeb($_OBJ_DB, $_OBJ_TXT, $obj_ClientConfig);
		$iTxnID = $obj_mPoint->newTransaction(Constants::iAPP_PURCHASE_TYPE);
	
		/* ========== Input Validation Start ========== */
		$obj_Validator = new Validate($obj_CountryConfig);
	
		if ($obj_Validator->valMobile( (float) $obj_DOM->mobile) != 10 && $obj_ClientConfig->smsReceiptEnabled() === true) { $aMsgCds[$obj_Validator->valMobile( (float) $obj_DOM->mobile) + 30] = (string) $obj_DOM->mobile; }
		if ($obj_Validator->valOperator( (integer) $obj_DOM->operator) != 10) { $aMsgCds[$obj_Validator->valOperator( (integer) $obj_DOM->operator) + 40] =  (string) $obj_DOM->operator; }
		if ($obj_Validator->valPrice($obj_ClientConfig->getMaxAmount(),  (integer) $obj_DOM->amount) != 10) { $aMsgCds[$obj_Validator->valPrice($obj_ClientConfig->getMaxAmount(), (integer) $obj_DOM->amount) + 50] = (string) $obj_DOM->amount; }
		// Validate URLs
		if ($obj_Validator->valURL( (string) $obj_DOM->{'callback-url'}) != 10) { $aMsgCds[$obj_Validator->valURL( (string) $obj_DOM->{'callback-url'}) + 110] = (string) $obj_DOM->{'callback-url'}; }
		if ($obj_Validator->valEMail( (string) $obj_DOM->email) != 1 && $obj_Validator->valEMail( (string) $obj_DOM->email) != 10) { $aMsgCds[$obj_Validator->valEMail( (string) $obj_DOM->email) + 140] = (string) $obj_DOM->email; }
		/* ========== Input Validation End ========== */
		
		// Success: Input Valid
		if (count($aMsgCds) == 0)
		{
			try
			{
				// Update Transaction State
				$obj_mPoint->newMessage($iTxnID, Constants::iINPUT_VALID_STATE, var_export($obj_DOM->asXML(), true) );
	
				$data['typeid'] = Constants::iAPP_PURCHASE_TYPE;
				$data['amount'] = (integer) $obj_DOM->amount;
				$data['gomobileid'] = -1;
				$data['orderid'] = (string) $obj_DOM->{'order-no'};
				$data['mobile'] = (float) $obj_DOM->mobile;
				$data['operator'] = (float) $obj_DOM->operator;
				$data['email'] = (string) $obj_DOM->email;
				$data['logo-url'] = "";
				$data['css-url'] = "";
				$data['accept-url'] = "";
				$data['cancel-url'] = "";
				$data['callback-url'] = (string) $obj_DOM->{'callback-url'};
				$data['icon-url'] = "";
				$data['language'] = "";
				$data['accountid'] = "";
				$data['language'] = "";
				$obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $obj_ClientConfig, $data);
				// Associate End-User Account (if exists) with Transaction
				$iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_TxnInfo->getMobile(), false);
				if ($iAccountID == -1 && trim($obj_TxnInfo->getEMail() ) != "") { $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_TxnInfo->getEMail(), false); }
				$obj_TxnInfo->setAccountID($iAccountID);
				
				// Update Transaction Log
				$obj_mPoint->logTransaction($obj_TxnInfo);
				if (count($obj_DOM->{'client-variables'}) == 1 && count($obj_DOM->{'client-variables'}->children() ) > 0)
				{
					$aVars = array();
					foreach ($obj_DOM->{'client-variables'}->children() as $obj_Var)
					{
						if (substr($obj_Var->getName(), 0, 4) == "var_")
						{
							$aVars[$obj_Var->getName()] = (string) $obj_Var;
						}
						else { $aVars["var_". $obj_Var->getName()] = (string) $obj_Var; } 
					}
					// Log additional data
					$obj_mPoint->logClientVars($aVars);
				}
	
				$aMsgCds[1000] = "Success";
			}
			// Internal Error
			catch (mPointException $e)
			{
				$aMsgCds[$e->getCode()] = $e->getMessage();
			}
		}
		// Error: Invalid Input
		else
		{
			// Log Errors
			foreach ($aMsgCds as $state => $debug)
			{
				$obj_mPoint->newMessage($iTxnID, $state, $debug);
			}
		}
	}
	// Error: Basic information is invalid
	else
	{
		$aMsgCds[Validate::valBasic($_OBJ_DB, (integer) $obj_DOM->{'client-id'}, (integer) $obj_DOM->account)+10] = "Client: ". $obj_DOM->{'client-id'} .", Account: ". $obj_DOM->account;
	}
	
	// Instantiate data object with the User Agent Profile for the customer's mobile device.
	//$obj_UA = UAProfile::produceUAProfile(HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["iemendo"]) );
	
	// Success
	if (array_key_exists(1000, $aMsgCds) === true)
	{
		$iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_TxnInfo->getMobile() );
		if ($iAccountID == -1 && trim($obj_TxnInfo->getEMail() ) != "") { $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_TxnInfo->getEMail() ); }
		
		$obj_mPoint = new CreditCard($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo);
		$xml = $obj_mPoint->getCards($obj_TxnInfo->getAmount() );
		$obj_DOM = simpledom_load_string($xml);
		$aPSPIDs = array();
		for ($i=0; $i<count($obj_DOM->item); $i++)
		{
			$pspid = (integer) $obj_DOM->item[$i]["pspid"];
			if (in_array($pspid, $aPSPIDs) === false)
			{
				$aPSPIDs[] = $pspid;
				switch ($pspid)
				{
				case (Constants::iDIBS_PSP):
					break;
				case (Constants::iWORLDPAY_PSP):
					$obj_mPoint = new WorldPay($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo);

					if ($obj_TxnInfo->getMode() > 0) { $aHTTP_CONN_INFO["worldpay"]["host"] = str_replace("secure.", "secure-test.", $aHTTP_CONN_INFO["worldpay"]["host"]); }
					$aHTTP_CONN_INFO["worldpay"]["username"] = (string) $obj_DOM->item[$i]->account; 
					
					$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["worldpay"]);
					$obj_XML = $obj_mPoint->initialize($obj_ConnInfo, (string) $obj_DOM->item[$i]->account, (string) $obj_DOM->item[$i]->currency, $obj_mPoint->getCardName(7) );
					$url = $obj_XML->reply->orderStatus->reference ."&preferredPaymentMethod=". $obj_mPoint->getCardName(7) ."&language=". sLANG;
					$url .= "&successURL=". urlencode("http://". $_SERVER['HTTP_HOST'] ."/pay/accept.php");
file_put_contents(sLOG_PATH ."/jona.log", $url); die();
					$obj_ConnInfo = HTTPConnInfo::produceConnInfo($url);
					$obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
					$obj_HTTP->connect();
					$code = $obj_HTTP->send($obj_mPoint->constHTTPHeaders() );
					// HTTP OK or HTTP Moved temporarily
					if ($code == 200 || $code == 302)
					{
						$aMatches = array();
						preg_match('/<meta http-equiv="refresh" content="(.*)" \/>/', $obj_HTTP->getReplyBody(), $aMatches);
						$url = substr($aMatches[1], strpos(strtolower($aMatches[1]), "url=") + 4);
file_put_contents(sLOG_PATH ."/jona.log", $url);
						$obj_HTTP->disConnect();
						$obj_ConnInfo = HTTPConnInfo::produceConnInfo($url);
						$obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
						$obj_HTTP->connect();
						// HTTP OK
						if ($obj_HTTP->send($obj_mPoint->constHTTPHeaders() ) == 200)
						{
							$xml .= '<psp-info id="'. $pspid .'">';
							$xml .= '<url method="post" content-type="application/x-www-form-urlencoded">'. $aHTTP_CONN_INFO["worldpay"]["protocol"] .'://'. $aHTTP_CONN_INFO["worldpay"]["host"] .'/wcc/card</url>';
							$xml .= '<card-number>cardNoInput</card-number>';
							$xml .= '<expiry-month>cardExp.month</expiry-month>';
							$xml .= '<expiry-year>cardExp.year</expiry-year>';
							$xml .= '<cvc>cardCVV</cvc>';
							$xml .= '<name>name</name>';
							$xml .= '<hidden-fields>';
							preg_match('/<INPUT TYPE=hidden NAME=PaymentID VALUE="(.*)" \/>/', $obj_HTTP->getReplyBody(), $aMatches);
							$xml .= '<PaymentID>'. $aMatches[1] .'</PaymentID>';
							$xml .= '<cardExp.day>32</cardExp.day>';
							$xml .= '<cardExp.time>23:59:59</cardExp.time>';
							$xml .= '<cardNoJS />';
							$xml .= '<cardNoHidden />';
							$xml .= '</hidden-fields>';
							$xml .= '</psp-info>';
						}
						$obj_HTTP->disConnect();
					}
					else { $obj_HTTP->disConnect(); }
					break;
				}
			}
		}
		// End-User already has an account that is linked to the Client
		if ($iAccountID > 0) { $xml .= $obj_mPoint->getStoredCards($obj_TxnInfo->getAccountID() ); }
		$xml = str_replace("<item", "<card", $xml);
		$xml = str_replace("</item>", "</card>", $xml);
		$xml .= $obj_TxnInfo->toXML();
		$xml .= $obj_TxnInfo->getClientConfig()->toXML();
	}
	// Error: Construct Status Page
	else
	{
		$xml = '';
		foreach ($aMsgCds as $code => $data)
		{
			$xml .= '<status code="'. $code .'">'. htmlspecialchars($data, ENT_NOQUOTES) .'</status>';
		}
	}
}
// Error: Invalid XML Document
elseif ( ($obj_DOM instanceof SimpleDOMElement) === false)
{
	header("HTTP/1.1 415 Unsupported Media Type");
	
	$xml = '<status code="415">Invalid XML Document</status>';
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
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<root>';
echo $xml;
echo '</root>';
?>