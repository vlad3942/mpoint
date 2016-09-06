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

$aWallets = array(Constants::iVISA_CHECKOUT_WALLET, Constants::iMASTER_PASS_WALLET);

$xmlData = "";

try
{
	if($_SESSION['obj_TxnInfo'] === null)
	{
		trigger_error("Session expired.", E_USER_ERROR);
		$_GET['msg'] = 0;
	}
	else 
	{
		
		// Instantiate main mPoint object for handling the component's functionality
		$obj_mPoint = new CreditCard($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo'], $_SESSION['obj_UA']);
		
		if(isset($_REQUEST['mpoint-id']) && $_REQUEST['mpoint-id'] > 0)
		{
			$obj_mPoint->delMessage($_SESSION['obj_TxnInfo']->getID(), Constants::iPAYMENT_WITH_ACCOUNT_STATE);
		}
		
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
				
				if(count($obj_Wallet_Response->{'psp-info'}->head) > 0 && count($obj_Wallet_Response->{'psp-info'}->body) > 0)
				{
					$sHead = str_replace("</script>","<\/script>",html_entity_decode($obj_Wallet_Response->{'psp-info'}->head));
										
					switch($obj_Elem['type-id'])
					{
						case Constants::iMASTER_PASS_WALLET :
							$sHead = str_replace("mpSuccessCallback", "function (data){ document.getElementById('walletform_23').elements.namedItem('token').value = data.oauth_token; document.getElementById('walletform_23').elements.namedItem('verifier').value = data.oauth_verifier; document.getElementById('walletform_23').elements.namedItem('checkouturl').value = data.checkout_resource_url; document.getElementById('walletform_23').submit();}", $sHead);
							$sHead = str_replace("mpFailureCallback", "function (data) { console.log('in FAILURE'); console.log(data); }", $sHead);
							$sHead = str_replace("mpCancelCallback", "function (data) { console.log('in CANCEL'); console.log(data); }", $sHead);
							
							$sHead = str_replace("<script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'><\/script>", " ", $sHead);
						break;
						
						case Constants::iVISA_CHECKOUT_WALLET :
							$sHead = str_replace("{PAYMENT SUCCESS}", "var jsonObject = JSON.parse(JSON.stringify(payment));document.getElementById('walletform_16').elements.namedItem('token').value = jsonObject['callid'];document.getElementById('walletform_16').submit();", $sHead);
							
							$sHead = str_replace("{PAYMENT ERROR}", "", $sHead);
							$sHead = str_replace("{PAYMENT CANCEL}", "", $sHead);
						break;
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
				else
				{
					$dom = dom_import_simplexml($obj_Elem);
					$dom->parentNode->removeChild($dom);
					unset($dom);
				}
				
			}
		
		}	
		
		$xmlData = '<title>'.$_OBJ_TXT->_("Select Payment Method").'</title>';
		
		$xmlData .= $obj_mPoint->getSystemInfo();
		
		$xmlData .= $_SESSION['obj_TxnInfo']->getClientConfig()->getCountryConfig()->toXML();
		$xmlData .= $_SESSION['obj_TxnInfo']->getClientConfig()->getAccountConfig()->toXML();
		$xmlData .= $_SESSION['obj_TxnInfo']->getClientConfig()->toXML();
		
		$xmlData .= $_SESSION['obj_TxnInfo']->toXML($_SESSION['obj_UA']);
		
		$xmlData .= $_SESSION['obj_UA']->toXML();
		
		$xmlData .= '<labels>
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
			<delete-card>'.$_OBJ_TXT->_("Delete Card").'</delete-card>
		</labels>';
		
		$xmlData .= trim(str_replace('<?xml version="1.0"?>', '',$obj_CardXML->asXML()));
		
		$xmlData .= $obj_mPoint->getStoredCards($_SESSION['obj_TxnInfo']->getAccountID(), $_SESSION['obj_TxnInfo']->getClientConfig(), false, $_SESSION['obj_UA']);
		
		//DIBS Custom Pages: Payment Accepted
		$xmlData .= '<accept>';
		$xmlData .= $obj_Accept->getmPointLogoInfo();
		
		$xmlData .= $obj_Accept->getClientVars($_SESSION['obj_TxnInfo']->getID() );
		$xmlData .= '</accept>';
				
		// Current transaction is an Account Top-Up and a previous transaction is in progress
		if ($_SESSION['obj_TxnInfo']->getTypeID() >= 100 && $_SESSION['obj_TxnInfo']->getTypeID() <= 109 && array_key_exists("obj_OrgTxnInfo", $_SESSION) === true)
		{
			$xmlData .= '<original-transaction-id>'. $_SESSION['obj_OrgTxnInfo']->getID() .'</original-transaction-id>';
		}		
	}	
	
}
catch(Exception $e)
{
	trigger_error($e->getMessage(), E_USER_ERROR);
	$_GET['msg'] = 1;
}

$xml = '<?xml version="1.0" encoding="UTF-8"?>';
$xml .= '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/html5/pay/card.xsl"?>';

$xml .= '<root>';

$xml .= $xmlData;

$xml .= $obj_mPoint->getMessages("Select Card");

$xml .= '</root>';

//file_put_contents(sLOG_PATH ."/debug_". date("Y-m-d") .".log", $xml);

echo $xml;
exit;

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
		
		return $obj_XML;
	}
	catch(Exception $e)
	{
		trigger_error($e->getMessage(), E_USER_ERROR);
		$_GET['msg'] = 1;
	}
}
?>