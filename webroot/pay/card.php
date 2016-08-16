<?php
/**
 * This file contains the Controller for mPoint's Card Selection component.
 * The component will generate a page using the Client Configuration listing the credit cards available to the Customer.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Payment
 * @subpackage CreditCard
 * @version 1.10
 */

// Require Global Include File
require_once("../inc/include.php");

require_once(sAPI_CLASS_PATH ."simpledom.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require Business logic for the Select Credit Card component
require_once(sCLASS_PATH ."/credit_card.php");

// Require Business logic for the Payment Accepted component
require_once(sCLASS_PATH ."/accept.php");

$aWallets = array(Constants::iVISA_CHECKOUT_WALLET, Constants::iMASTER_PASS_WALLET, Constants::iAMEX_EXPRESS_CHECKOUT_WALLET);

try
{
	if($_SESSION['obj_TxnInfo'] === null)
	{
		trigger_error("Session expired.", E_USER_ERROR);
		header("location: ".$_SERVER['HTTP_REFERER']);
		exit;
	}
	
	// Instantiate main mPoint object for handling the component's functionality
	$obj_mPoint = new CreditCard($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo'], $_SESSION['obj_UA']);
	// Instantiate main special object in order to pass all relevant data for the Accept Payment page through DIBS: Custom Pages
	$obj_Accept = new Accept($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_UA']);
	
	
	$card_xml = $obj_mPoint->getCards($_SESSION['obj_TxnInfo']->getAmount() );
	
	$clientId = $_SESSION['obj_TxnInfo']->getClientConfig()->getAccountConfig()->getClientID();
	$accountId = $_SESSION['obj_TxnInfo']->getClientConfig()->getAccountConfig()->getID();
	
	$obj_CardXML = simplexml_load_string($card_xml );
	
	foreach($aWallets as $iWallet)
	{
		$obj_Elem = current($obj_CardXML->xpath("/cards/item[@id = ".$iWallet."]"));
		
		if(empty($obj_Elem) === false)
		{
			$b = '<?xml version="1.0" encoding="UTF-8"?>
					<root>
						<pay client-id="'.$clientId.'" account="'.$accountId.'">
							<transaction id="'.$_SESSION['obj_TxnInfo']->getID().'" store-card="false">
								<card type-id="'.$obj_Elem['type-id'].'">
									<amount country-id="'.$_SESSION['obj_TxnInfo']->getCountryConfig()->getID().'">'.$_SESSION['obj_TxnInfo']->getAmount().'</amount>
								</card>
							</transaction>
							<client-info language="'.sDEFAULT_LANGUAGE.'" version="1.20" platform="HTML5">
							</client-info>
						</pay>
					</root>';
			
			$aHTTP_CONN_INFO["mesb"]["path"] = "/mpoint/pay";
			$aHTTP_CONN_INFO["mesb"]["username"] = $_SESSION['obj_TxnInfo']->getClientConfig()->getUsername();
			$aHTTP_CONN_INFO["mesb"]["password"] = $_SESSION['obj_TxnInfo']->getClientConfig()->getPassword();
			
			$obj_Wallet_Response = getXMLResponse($b, $aHTTP_CONN_INFO);
			
			
			$sHead = str_replace("</script>","<\/script>",html_entity_decode($obj_Wallet_Response->{'psp-info'}->head));
			
			if($obj_Elem['type-id'] == 16)
			{
				$sHead = str_replace("{PAYMENT SUCCESS}", "var jsonObject = JSON.parse(JSON.stringify(payment));document.getElementById('walletform_16').elements.namedItem('token').value = jsonObject['callid'];document.getElementById('walletform_16').submit();", $sHead);
				
				$sHead = str_replace("{PAYMENT ERROR}", "", $sHead);
				$sHead = str_replace("{PAYMENT CANCEL}", "", $sHead);
			}
			
			if($obj_Elem['type-id'] == 23)
			{
				$sHead = str_replace("mpSuccessCallback", "function (data){ console.log(data); document.getElementById('walletform_23').elements.namedItem('token').value = data.oauth_token; document.getElementById('walletform_23').elements.namedItem('verifier').value = data.oauth_verifier; document.getElementById('walletform_23').elements.namedItem('checkouturl').value = data.checkout_resource_url; document.getElementById('walletform_23').submit();}", $sHead);
				$sHead = str_replace("mpFailureCallback", "function (data) { console.log('in FAILURE'); console.log(data); }", $sHead);
				$sHead = str_replace("mpCancelCallback", "function (data) { console.log('in CANCEL'); console.log(data); }", $sHead);		
			}
			
			$obj_Elem->head = NULL;			
			$obj_Elem1 = dom_import_simplexml($obj_Elem->head);
			$cdata = $obj_Elem1->ownerDocument->createCDataSection($sHead);
			$obj_Elem1->appendChild($cdata);
			
			$obj_Elem->body = NULL;
			$obj_Elem2 = dom_import_simplexml($obj_Elem->body);
			$cdata = $obj_Elem2->ownerDocument->createCDataSection(str_replace("</","<\/",html_entity_decode($obj_Wallet_Response->{'psp-info'}->body)));
			$obj_Elem2->appendChild($cdata);
			
		}
	
	}
	
	$xml = '<?xml version="1.0" encoding="UTF-8"?>';
	$xml .= '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/'. General::getMarkupLanguage($_SESSION['obj_UA'], $_SESSION['obj_TxnInfo']) .'/pay/card.xsl"?>';
	
	$xml .= '<root>
		<title>'.$_OBJ_TXT->_("Select Payment Method").'</title>';
	
	$xml .= $obj_mPoint->getSystemInfo();
	
	$xml .= $_SESSION['obj_TxnInfo']->getClientConfig()->getCountryConfig()->toXML();
	$xml .= $_SESSION['obj_TxnInfo']->getClientConfig()->getAccountConfig()->toXML();
	$xml .= $_SESSION['obj_TxnInfo']->getClientConfig()->toXML();
	
	$xml .= $_SESSION['obj_TxnInfo']->toXML($_SESSION['obj_UA']);
		
	$xml .= $_SESSION['obj_UA']->toXML();
		
	$xml .= '<labels>
			<progress>'.$_OBJ_TXT->_("Step 1 of 2").'</progress>
			<info>'.$_OBJ_TXT->_("Please select your Payment Method").'</info>
			<cancel>'.$_OBJ_TXT->_("Cancel Payment").'</cancel>
			<cardholdername>'.$_OBJ_TXT->_("Card Holder Name").'</cardholdername>
			<cardnumber>'.$_OBJ_TXT->_("Card Number").'</cardnumber>
			<expiry>'.$_OBJ_TXT->_("Expiry Date").'</expiry>
			<cvv>'.$_OBJ_TXT->_("CVV Code").'</cvv>
			<button>'.$_OBJ_TXT->_("Pay now").'</button>
			<paymentcard>'.$_OBJ_TXT->_("Payment card").'</paymentcard>
			<savecard>'.$_OBJ_TXT->_("Save card info").'</savecard>
			<cardholder>'.$_OBJ_TXT->_("Card holder").'</cardholder>
			<password>'. $_OBJ_TXT->_("Password") .'</password>
			<submit>'. $_OBJ_TXT->_("Complete Payment") .'</submit>
			<password>'. $_OBJ_TXT->_("Create Password - Help Checkout") .'</password>
			<new-password>'. $_OBJ_TXT->_("New Password") .'</new-password>
			<repeat-password>'. $_OBJ_TXT->_("Repeat Password") .'</repeat-password>
			<name>'. $_OBJ_TXT->_("Card Name") .'</name>
			<back-button>'. $_OBJ_TXT->_("Back button") .'</back-button>
		</labels>';
	
	//$xml .= $obj_mPoint->getCards($_SESSION['obj_TxnInfo']->getAmount() );
	
	$xml .= trim(str_replace('<?xml version="1.0"?>', '',$obj_CardXML->asXML()));
		
	$xml .= $obj_mPoint->getStoredCards($_SESSION['obj_TxnInfo']->getAccountID(), $_SESSION['obj_TxnInfo']->getClientConfig(), false, $_SESSION['obj_UA']);
	
	//DIBS Custom Pages: Payment Accepted
	$xml .= '<accept>';
	$xml .= $obj_Accept->getmPointLogoInfo();
	
	$xml .= $obj_Accept->getClientVars($_SESSION['obj_TxnInfo']->getID() );
	$xml .= '</accept>';
	
	$xml .= $obj_mPoint->getMessages("Select Card");
	
	// Current transaction is an Account Top-Up and a previous transaction is in progress
	if ($_SESSION['obj_TxnInfo']->getTypeID() >= 100 && $_SESSION['obj_TxnInfo']->getTypeID() <= 109 && array_key_exists("obj_OrgTxnInfo", $_SESSION) === true)
	{
		$xml .= '<original-transaction-id>'. $_SESSION['obj_OrgTxnInfo']->getID() .'</original-transaction-id>';
	}
	
	$xml .= '</root>';
	
	file_put_contents(sLOG_PATH ."/debug_". date("Y-m-d") .".log", $xml);
	
	echo $xml;
	exit;
}
catch(Exception $e)
{
	trigger_error($e->getMessage(), E_USER_ERROR);
	header("location: ".$_SERVER['HTTP_REFERER']);
	exit;	
}

function getXMLResponse($b, $aHTTP_CONN_INFO)
{
	try
		{
		$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["mesb"]);
			
		$h = "{METHOD} {PATH} HTTP/1.0" .HTTPClient::CRLF;
		$h .= "host: {HOST}" .HTTPClient::CRLF;
		$h .= "referer: {REFERER}" .HTTPClient::CRLF;
		$h .= "content-length: {CONTENTLENGTH}" .HTTPClient::CRLF;
		$h .= "content-type: {CONTENTTYPE}" .HTTPClient::CRLF;
		$h .= "user-agent: mPoint" .HTTPClient::CRLF;
		$h .= "Authorization: Basic ". base64_encode($aHTTP_CONN_INFO["mesb"]["username"] .":". $aHTTP_CONN_INFO["mesb"]["password"]) .HTTPClient::CRLF;
		
		
		$obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
		$obj_HTTP->connect();
		$code = $obj_HTTP->send($h, $b);
		$obj_HTTP->disconnect();
		
		if ($code == 200 && strlen($obj_HTTP->getReplyBody() ) > 0)
		{
			$obj_XML = simplexml_load_string($obj_HTTP->getReplyBody() );
		}
	}
	catch(Exception $e)
	{
		trigger_error($e->getMessage(), E_USER_ERROR);
		header("location: ".$_SERVER['HTTP_REFERER']);
		exit;
	}
	
	return $obj_XML;
}
?>