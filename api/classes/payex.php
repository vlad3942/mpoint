<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol.
 * The PayEx subpackage is a specific implementation capable of imitating PayEx's own protocol.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage PayEx
 * @version 1.00
 */

/**
 * Model Class containing all the Business Logic for handling interaction with PayEx
 *
 */
class PayEx extends Callback
{
	/**
	 * Notifies the Client of the Payment Status by performing a callback via HTTP.
	 * The method will re-construct the data received from DIBS after having removed the following mPoint specific fields:
	 * 	- width
	 * 	- height
	 * 	- format
	 * 	- PHPSESSID (found using PHP's session_name() function)
	 * 	- language
	 * 	- cardid
	 * Additionally the method will add mPoint's Unique ID for the Transaction.
	 *
	 * @see 	Callback::notifyClient()
	 * @see 	Callback::send()
	 * @see 	Callback::getVariables()
	 *
	 * @param 	integer $sid 	Unique ID of the State that the Transaction terminated in
	 * @param 	array $_post 	Array of data received from DIBS via HTTP POST
	 */
	public function notifyClient($sid, array $_post)
	{
		// Client is configured to use mPoint's protocol
		if ($this->getTxnInfo()->getClientConfig()->getMethod() == "mPoint")
		{
			parent::notifyClient($sid, $_post["transact"]);
		}
		// Client is configured to use DIBS' protocol
		else
		{
			// Remove mPoint specific data fields from Callback request
			unset($_post["width"], $_post["height"], $_post["format"], $_post[session_name()], $_post["language"], $_post["cardid"]);
			// Replace data fields previously overwritten by mPoint
			$_post["orderid"] = $this->getTxnInfo()->getOrderID();
			$_post["callbackurl"] = $this->getTxnInfo()->getCallbackURL();
			$_post["accepturl"] = $this->getTxnInfo()->getAcceptURL();
			// Re-Construct DIBS request
			$sBody = "mpoint-id=". $this->getTxnInfo()->getID();
			foreach ($_post as $key => $val)
			{
				$sBody .= "&". $key ."=". urlencode($val);
			}
			// Append Custom Client Variables and Customer Input
			$sBody .= "&". $this->getVariables();

			$this->performCallback($sBody);
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see api/classes/EndUserAccount#delTicket($pspid, $ticket)
	 */
	public function delTicket($ticket)
	{
		$h = $this->constHTTPHeaders();
//		$h .= "authorization: Basic ". base64_encode($this->_obj_ConnInfo->getUsername() .":". $this->_obj_ConnInfo->getPassword() ) .HTTPClient::CRLF;
		$b = "merchant=". $this->getMerchantAccount($this->getTxnInfo()->getClientConfig()->getID(), Constants::iDIBS_PSP). "&ticket=". $ticket;

//		parent::send("https://payment.architrade.com/cgi-adm/delticket.cgi", $h, $b);
	}

	/**
	 * Authorises a payment with WorldPay for the transaction using the provided ticket.
	 * The ticket represents a previously stored card.
	 * The method will return WorldPay' transaction ID if the authorisation is accepted or one of the following status codes if the authorisation is declined:
	 *  
	 * @link	
	 *  
	 * @param 	integer $ticket		Valid ticket which references a previously stored card 
	 * @return 	integer
	 * @throws	E_USER_WARNING
	 */
	public function authTicket($ticket)
	{
	}

	/**
	 * Performs a capture operation with WorldPay for the provided transaction.
	 * The method will log one the following status codes from WorldPay:
	 * 
	 * @link	
	 * 
	 * @param 	integer $txn	Transaction ID previously returned by WorldPay during authorisation
	 * @return	integer
	 * @throws	E_USER_WARNING
	 */
	public function capture($txn)
	{
	}
	
	public function getCardName($id)
	{
		switch ($id)
		{
		case (1):	// American Express
			$name = "AMEX-SSL"; 
			break;
		case (2):	// Dankort
			$name = "DANKORT-SSL";
			break;
		case (3):	// Diners Club
			$name = "DINERS-SSL";
			break;
		case (4):	// EuroCard
			$name = "ECMC-SSL";
			break;
		case (5):	// JCB
			$name = "JCB-SSL";
			break;
		case (6):	// Maestro
			$name = "MAESTRO-SSL";
			break;
		case (7):	// MasterCard
			$name = "ECMC-SSL";
			break;
		case (8):	// VISA
			$name = "VISA-SSL";
			break;
		case (9):	// VISA Electron
			$name = "VISA_ELECTRON-SSL";
			break;
		default:	// Unknown
			break;
		}
		
		return $name;
	}
	public function getCardID($name)
	{
		switch ($name)
		{
		case "AMEX-SSL":	// American Express
			$id = 1; 
			break;
		case "DANKORT-SSL":	// Dankort
			$id = 2;
			break;
		case "DINERS-SSL":	// Diners Club
			$id = 3;
			break;
//		case "ECMC-SSL":	// EuroCard
//			$id = 4;
//			break;
		case "JCB-SSL":		// JCB
			$id = 5;
			break;
		case "MAESTRO-SSL":	// Maestro
			$id = 6;
			break;
		case "ECMC-SSL":	// MasterCard
			$id = 7;
			break;
		case "VISA-SSL":	// VISA
			$id = 8;
			break;
		case "VISA_ELECTRON-SSL":	// VISA Electron
			$id = 9;
			break;
		default:	// Unknown
			break;
		}
		
		return $id;
	}

	/**
	 * Initialises Callback to the Client.
	 *
	 * @param 	HTTPConnInfo $oCI 	Connection Info required to communicate with the Callback component for Cellpoint Mobile
	 * @param 	integer $cardid		Unique ID of the Card Type that was used in the payment transaction
	 * @param 	integer $txnid		Transaction ID from WorldPay returned in the "transact" parameter
	 */
	public function initialize(SOAPConnInfo &$oCI, $an, $currency)
	{
		$obj_SOAP = new MSSoapClient($oCI->getURL(), $oCI->getOptions() );
		switch (sLANG)
		{
		case "da":	// Danish
			$lang = "da-DK";
			break;
		case "gb":	// British English
			$lang = "en-US";
			break;
		case "no":	// Norwegian
			$lang = "nb-NO";
			break;
		case "sv":	// Swedish
			$lang = "sv-SE";
			break;
		case "cz":	// Czech
			$lang = "cs-CZ";
			break;
		case "de":	// German
		case "es":	// Spanish
		case "fi":	// Finnish
		case "hu":	// Hungarian
		case "pl":	// 
		default:
			$lang = sLANG ."-". strtoupper(sLANG);
			break;
		}
		
		$aParams = array("accountNumber" => $an,
						 "purchaseOperation" => "AUTHORIZATION",
						 "price" => $this->getTxnInfo()->getAmount(),
						 "currency" => $currency,
						 "vat" => 0,
						 "orderID" => $this->getTxnInfo()->getID(),
						 "productNumber" => $this->getTxnInfo()->getOrderID(),
						 "description" => "",
						 "clientIPAddress" => $_SERVER['REMOTE_ADDR'],
						 "returnURL" => "http://". $_SERVER['HTTP_HOST'] ."/pay/accept.php?". session_name() ."=". session_id(),
						 "clientLanguage" => $lang,
						 "hash" => md5($an . "AUTHORIZATION" . $this->getTxnInfo()->getAmount() . $currency . 0 . $this->getTxnInfo()->getID() . $this->getTxnInfo()->getOrderID() . $_SERVER['REMOTE_ADDR'] ."http://". $_SERVER['HTTP_HOST'] ."/pay/accept.php?". session_name() ."=". session_id() . $lang . $oCI->getPassword() ) );
		$obj_SOAP->Initialize7($aParams);
		echo $obj_SOAP->__getLastRequest();
		$obj_XML = simplexml_load_string($obj_SOAP->__getLastResponse() );
		$obj_XML = $obj_XML->children("http://schemas.xmlsoap.org/soap/envelope/")->Body->children("http://external.payex.com/PxOrder/")->Initialize7Response;
		$obj_XML = simplexml_load_string(htmlspecialchars_decode( (string) $obj_XML->Initialize7Result, ENT_NOQUOTES) );
		
		return $obj_XML;
	}
}
?>