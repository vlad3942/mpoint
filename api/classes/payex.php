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
			parent::notifyClient($sid, $_post["transact"], $this->getTxnInfo()->getAmount() );
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
	public function capture(HTTPConnInfo &$oCI, $an, $txn)
	{
		$obj_SOAP = new SOAPClient("https://". $oCI->getHost() . $oCI->getPath(), array("trace" => true,
																						"exceptions" => true) );
		
		$aParams = array("accountNumber" => $an,
						 "transactionNumber" => $txn,
						 "amount" => $this->getTxnInfo()->getAmount(),
						 "orderId" => $this->getTxnInfo()->getOrderID(),
						 "vatAmount" => 0,
						 "additionalValues" => "");
		$aParams["hash"] = md5($aParams["accountNumber"] . $aParams["transactionNumber"] . $aParams["amount"] . $aParams["orderId"] . $aParams["vatAmount"] . $aParams["additionalValues"] . $oCI->getPassword() );
		$obj_Std = $obj_SOAP->Capture5($aParams);
		$obj_XML = simplexml_load_string($obj_Std->Capture5Result);
		
		if ($obj_XML->status->errorCode == "OK")
		{
			$this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_CAPTURED_STATE, $obj_Std->Capture5Result);
			
			return 0;
		}
		else
		{
			$this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_DECLINED_STATE, $obj_Std->Capture5Result);
			trigger_error("Capture declined by PayEx for Transaction: ". $this->getTxnInfo()->getID() ."(". $txn ."), Result: ". $obj_XML->status->description ."(". $obj_XML->status->errorCode .")", E_USER_WARNING);
			
			return 1;
		}
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
		case "AMEX":	// American Express
			$id = 1; 
			break;
		case "DANKORT":	// Dankort
			$id = 2;
			break;
		case "DINERS":	// Diners Club
			$id = 3;
			break;
//		case "ECMC":	// EuroCard
//			$id = 4;
//			break;
		case "JCB":		// JCB
			$id = 5;
			break;
		case "MAESTRO":	// Maestro
			$id = 6;
			break;
		case "MC":	// MasterCard
			$id = 7;
			break;
		case "VISA":	// VISA
			$id = 8;
			break;
		case "VISA_ELECTRON":	// VISA Electron
			$id = 9;
			break;
		default:	// Unknown
			break;
		}
		
		return $id;
	}

	public function initialize(HTTPConnInfo &$oCI, $an, $currency)
	{
		$obj_SOAP = new SOAPClient("https://". $oCI->getHost() . $oCI->getPath(), array("trace" => true,
																						"exceptions" => true) );
		switch (sLANG)
		{
		case "da":	// Danish
			$lang = "da-DK";
			break;
		case "gb":	// British English
			$lang = "en-US";
			break;
		case "us":	// American English
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
						 "priceArgList" => "",
						 "currency" => $currency,
						 "vat" => 0,
						 "orderID" => $this->getTxnInfo()->getOrderID(),
						 "productNumber" => $this->getTxnInfo()->getID(),
						 "description" => "mPoint ID: ". $this->getTxnInfo()->getID() ." for Order No.:". $this->getTxnInfo()->getOrderID(),
						 "clientIPAddress" => $_SERVER['REMOTE_ADDR'],
						 "clientIdentifier" => "USERAGENT=". $_SERVER['HTTP_USER_AGENT'],
						 "additionalValues" => "",
						 "externalID" => "",
						 "returnUrl" => "http://". $_SERVER['HTTP_HOST'] ."/pay/accept.php?mpoint-id=". $this->getTxnInfo()->getID(),
						 "view" => "CREDITCARD",
						 "agreementRef" => "",
						 "cancelUrl" => $this->getTxnInfo()->getCancelURL(),
						 "clientLanguage" => $lang);
		$aParams["hash"] = md5($aParams["accountNumber"] . $aParams["purchaseOperation"] . $aParams["price"] . $aParams["priceArgList"] . $aParams["currency"] . $aParams["vat"]. $aParams["orderID"] . $aParams["productNumber"] . $aParams["description"] . $aParams["clientIPAddress"] . $aParams["clientIdentifier"] . $aParams["additionalValues"] . $aParams["externalID"] . $aParams["returnUrl"] . $aParams["view"] . $aParams["agreementRef"] . $aParams["cancelUrl"] . $aParams["clientLanguage"] . $oCI->getPassword() );
		$obj_Std = $obj_SOAP->Initialize8($aParams);
		$obj_XML = simplexml_load_string($obj_Std->Initialize8Result);
		
		if ($obj_XML->status->errorCode == "OK")
		{
			$sql = "UPDATE Log.Transaction_Tbl
					SET pspid = ". Constants::iPAYEX_PSP .", extid = '". $this->getDBConn()->escStr($obj_XML->orderRef) ."'
					WHERE id = ". $this->getTxnInfo()->getID();
//			echo $sql ."\n";
			$this->getDBConn()->query($sql);
			
			/* ----- Construct HTTP Header Start ----- */
			$h = "GET {PATH} HTTP/1.0" .HTTPClient::CRLF;
			$h .= "host: {HOST}" .HTTPClient::CRLF;
			$h .= "referer: {REFERER}" .HTTPClient::CRLF;
			$h .= "content-length: {CONTENTLENGTH}" .HTTPClient::CRLF;
			$h .= "user-agent: ". $_SERVER['HTTP_USER_AGENT'] .HTTPClient::CRLF;
			/* ----- Construct HTTP Header End ----- */
			$obj_ConnInfo = HTTPConnInfo::produceConnInfo( (string) $obj_XML->redirectUrl);
			$obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
			$obj_HTTP->connect();
			$code = $obj_HTTP->send($h);
			$obj_HTTP->disConnect();
			
			$sCookies = "";
			$aHiddenFields = array();
			$sURL = "";
			$sCardNo = "";
			$sCVC = "";
			$sExpiryYear = "";
			$sExpiryMonth = "";
			// Parse HTTP Response Headers
			$a = explode(HTTPClient::CRLF, $obj_HTTP->getReplyHeader() );
			foreach ($a as $str)
			{
				$pos = strpos($str, ":");
				// HTTP Header
				if ($pos > 0)
				{
					$name = substr($str, 0, $pos);
					if (strtolower($name) == "set-cookie")
					{
						$value = trim(substr($str, $pos+1) ); 
						$pos = strpos($value, ";");
						if ($pos < 0) { $pos = strlen($value); }
						$sCookies = trim(substr($value, 0, $pos) );
					}
				}
			}
			// Parse HTTP Response Body
			$obj_DOM = DOMDocument::loadXML($obj_HTTP->getReplyBody() );
			$aObj_Elems = array();
			$obj_NodeList = $obj_DOM->getElementsByTagName("input");
			foreach ($obj_NodeList as $obj_Elem)
			{
				$aObj_Elems[] = $obj_Elem;
			}
			$obj_NodeList = $obj_DOM->getElementsByTagName("select");
			foreach ($obj_NodeList as $obj_Elem)
			{
				$obj_Elem->setAttribute("type", "select");
				$aObj_Elems[] = $obj_Elem;
			}
			$obj_NodeList = $obj_DOM->getElementsByTagName("form");
			foreach ($obj_NodeList as $obj_Elem)
			{
				$obj_Elem->setAttribute("type", "form");
				$aObj_Elems[] = $obj_Elem;
			}
			foreach ($aObj_Elems as $obj_Elem)
			{
				$type = "";
				$name = "";
				$value = "";
				for ($i=0; $i<$obj_Elem->attributes->length; $i++)
				{
					switch (strtolower($obj_Elem->attributes->item($i)->nodeName) )
					{
					case "type":
						$type = strtolower($obj_Elem->attributes->item($i)->nodeValue);
						break;
					case "name":
						$name = $obj_Elem->attributes->item($i)->nodeValue;
						break;
					case "value":
					case "action":
						$value = $obj_Elem->attributes->item($i)->nodeValue;
						break;
					}
				}
				if (empty($type) === false && (empty($name) === false || empty($value) === false) )
				{
					switch ($type)
					{
					case "hidden":
					case "submit":
					case "button":
						$aHiddenFields[$name] = $value;
						break;
					case "text":
					case "number":
					case "tel":
					case "select":
						if (stristr($name, "CardNumber") == true) { $sCardNo = $name; }
						elseif (stristr($name, "CVCCode") == true) { $sCVC = $name; }
						elseif (stristr($name, "ExpireMonth") == true) { $sExpiryMonth = $name; }
						elseif (stristr($name, "ExpireYear") == true) { $sExpiryYear = $name; }
						break;
					case "form":
						if (empty($sURL) === true) { $sURL = $value; }
						break;
					default:	// Unsupported input type
						break;
					}
				}
			}
			$xml = '<?xml version="1.0" encoding="UTF-8"?>';
			$xml .= '<root>';
			$xml .= '<url method="post" content-type="application/x-www-form-urlencoded">https://'. $obj_ConnInfo->getHost() . $sURL .'</url>';
			$xml .= '<card-number>'. htmlspecialchars($sCardNo, ENT_NOQUOTES) .'</card-number>';
			$xml .= '<expiry-month>'. htmlspecialchars($sExpiryMonth, ENT_NOQUOTES) .'</expiry-month>';
			$xml .= '<expiry-year>'. htmlspecialchars($sExpiryYear, ENT_NOQUOTES) .'</expiry-year>';
			$xml .= '<cvc>'. htmlspecialchars($sCVC, ENT_NOQUOTES) .'</cvc>';
			$xml .= '<cookies>'. htmlspecialchars($sCookies, ENT_NOQUOTES) .'</cookies>';
			$xml .= '<hidden-fields>';
			foreach ($aHiddenFields as $name => $value)
			{
				$xml .= '<'. str_replace("$", "-DOLLARSIGN-", $name) .'>'. $value .'</'. str_replace("$", "-DOLLARSIGN-", $name) .'>';
			}
			$xml .= '</hidden-fields>';
			$xml .= '</root>';
			$obj_XML = simplexml_load_string($xml);	
		}
		else
		{
			throw new mPointException("Unable to initialize payment using PayEx. Error: ". $obj_XML->status->description ."(". $obj_XML->status->errorCode .")");
		}
		
		return $obj_XML;
	}
	
	public static function getIDFromExternalID(RDB &$oDB, $orderref)
	{
		$sql = "SELECT id
				FROM Log.Transaction_Tbl
				WHERE pspid = ". Constants::iPAYEX_PSP ." AND extid = '". $oDB->escStr($orderref) ."'";
//		echo $sql ."\n";
		$RS = $oDB->getName($sql);
		
		return is_array($RS) === true ? $RS["ID"] : -1;
	}
	
	public function complete(HTTPConnInfo &$oCI, $an, $or)
	{
		$obj_SOAP = new SOAPClient("https://". $oCI->getHost() . $oCI->getPath(), array("trace" => true,
																						"exceptions" => true) );
		
		$aParams = array("accountNumber" => $an,
						 "orderRef" => $or);
		$aParams["hash"] = md5($aParams["accountNumber"] . $aParams["orderRef"] . $oCI->getPassword() );
		
		$obj_Std = $obj_SOAP->Complete($aParams);
		$obj_XML = simplexml_load_string($obj_Std->CompleteResult);
		
		if ($obj_XML->status->errorCode == "OK")
		{
			// Payment Captured
			if ($obj_XML->transactionStatus == 6)
			{
				$obj_XML->status["code"] = Constants::iPAYMENT_CAPTURED_STATE;
			}
			else
			{
				$sql = "UPDATE Log.Transaction_Tbl
						SET extid = NULL
						WHERE id = ". $this->getTxnInfo()->getID();
//				echo $sql ."\n";
				$this->getDBConn()->query($sql);
				$obj_XML->status["code"] = $this->completeTransaction(Constants::iPAYEX_PSP, $obj_XML->transactionNumber, $this->getCardID($obj_XML->paymentMethod), Constants::iPAYMENT_ACCEPTED_STATE, array("result" => $obj_Std->CompleteResult) );
			}
		}
		else
		{
			$obj_XML->status["code"] = $this->completeTransaction(Constants::iPAYEX_PSP, $or, $this->getCardID($obj_XML->paymentMethod), Constants::iPAYMENT_DECLINED_STATE, array("result" => $obj_Std->CompleteResult) );
		}
		
		return $obj_XML;
	}
}
?>