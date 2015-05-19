<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol.
 * The WorldPay subpackage is a specific implementation capable of imitating WorldPay's own protocol.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage WorldPay
 * @version 1.00
 */

/**
 * Model Class containing all the Business Logic for handling interaction with WorldPay
 *
 */
class WorldPay extends Callback
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
	 * @param 	integer $sid 				Unique ID of the State that the Transaction terminated in
	 * @param 	SimpleXMLElement $obj_XML 	XML Document received from WorldPay via HTTP POST
	 */
	public function notifyClient($sid, SimpleXMLElement &$obj_XML, SurePayConfig &$obj_SurePay=null)
	{
		// Client is configured to use mPoint's protocol
		if ($this->getTxnInfo()->getClientConfig()->getMethod() == "mPoint")
		{
			if (count($obj_XML->notify->orderStatusEvent->payment->cardNumber) == 1) { $sMask = $obj_XML->notify->orderStatusEvent->payment->cardNumber; }
			else { $sMask = $obj_XML->notify->orderStatusEvent->payment->paymentMethodDetail->card["number"]; }
			parent::notifyClient($sid, -1, $this->getTxnInfo()->getAmount(), $this->getCardID($obj_XML->notify->orderStatusEvent->payment->paymentMethod), $sMask, $obj_SurePay);
		}
		// Client is configured to use WorldPay's protocol
		else
		{
			$obj_XML->notify->orderStatusEvent["orderCode"] = $this->getTxnInfo()->getOrderID();
			$obj_XML->notify->orderStatusEvent["mpoint-id"] = $this->getTxnInfo()->getID();

			$this->performCallback($obj_XML->asXML(), $obj_SurePay);
		}
	}
	/* Initialises Callback to the Client.
	*
	* @param 	HTTPConnInfo $oCI 	Connection Info required to communicate with the Callback component for Cellpoint Mobile
	* @param 	integer $cardid		Unique ID of the Card Type that was used in the payment transaction
	* @param 	integer $txnid		Transaction ID from WorldPay returned in the "transact" parameter
	*/
	public function auth(HTTPConnInfo &$oCI, $merchantcode, $currency, $cardid, $storecard)
	{		
		if (empty($oc) === true) { $oc = $this->getTxnInfo()->getID(); }
		
		$sql = "UPDATE Log".sSCHEMA_POSTFIX.".Transaction_Tbl
				SET pspid = ". Constants::iWORLDPAY_PSP ."
				WHERE id = ". $this->getTxnInfo()->getID();

//		echo $sql ."\n";
		$this->getDBConn()->query($sql);

		if ($cardid === Constants::iAPPLE_PAY)
		{
			$xml .= '<url method="app" />';
		}
		else 
		{
			$card = $this->getCardName($cardid);
			$url = "https://" . $oCI->getHost() . $oCI->getPath();
			if ($storecard === true ) { $url .= "&preferredPaymentMethod=". $card ."&language=". $this->getTxnInfo()->getLanguage(); }
			$xml = '<url method="post" content-type="text/xml">'. htmlspecialchars( $url, ENT_NOQUOTES) .'</url>';
	
			$oc = htmlspecialchars($this->getTxnInfo()->getOrderID(), ENT_NOQUOTES);
			if (empty($oc) === true) { $oc = $this->getTxnInfo()->getID(); }
			$xml .= '<body>';
			$b .= '<?xml version="1.0" encoding="UTF-8"?>';
			$b .= '<!DOCTYPE paymentService PUBLIC "-//WorldPay//DTD WorldPay PaymentService v1//EN" "http://dtd.worldpay.com/paymentService_v1.dtd">';
			$b .= '<paymentService version="1.4" merchantCode="'. $merchantcode .'">';
			$b .= '<submit>';
			$b .= '<order orderCode="'. $oc .'">';
			$b .= '<description>Order: '. $oc .' from: '. htmlspecialchars($this->getTxnInfo()->getClientConfig()->getName(), ENT_NOQUOTES) .'</description>';
			$b .= '<amount currencyCode="'. $currency .'" exponent="2"  value="'. $this->getTxnInfo()->getAmount() .'"/>';
			$b .= '<paymentDetails>';
			$b .= '<'. $card .'>';
			$b .='<cardNumber>_CARD_NUMBER_</cardNumber>';
			$b .='<expiryDate>';
			$b .='<date month="_MONTH_" year="_YEAR_"/>';
			$b .='</expiryDate>';
			$b .='<cardHolderName>_CARD_HOLDER_NAME_</cardHolderName>';
			$b .='<cvc>_CVC_</cvc>';
			$b .= '</'. $card .'>';
			$headers = apache_request_headers();
			$b .='<session shopperIPAddress="'. $headers["X-Forwarded-For"] .'" id="'. $this->getTxnInfo()->getID() .'" />';
			$b .= '</paymentDetails>';
	
			$b .= '<shopper>';
			if (strlen($this->getTxnInfo()->getEMail() ) > 0)
			{
				$b .= '<shopperEmailAddress>'. htmlspecialchars($this->getTxnInfo()->getEMail(), ENT_NOQUOTES) .'</shopperEmailAddress>';
			}
			$b .= '<browser>';
			$b .= '<acceptHeader>_ACCEPT_HEADER_</acceptHeader>';
			$b .= '<userAgentHeader>_USER_AGENT_HEADER_</userAgentHeader>';
			$b .= '</browser>';
			$b .= '</shopper>';

			$b .= '<statementNarrative>Order: '. $oc .' from: '. htmlspecialchars($this->getTxnInfo()->getClientConfig()->getName(), ENT_NOQUOTES) .'</statementNarrative>';
			$b .= '</order>';
			$b .= '</submit>';
			$b .= '</paymentService>';
			$xml .= htmlspecialchars($b, ENT_NOQUOTES);
			$xml .= '</body>';
		}

		$this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_INIT_WITH_PSP_STATE, $xml);
		
		return $xml;
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
	 * Authorises a payment with WorldPay for the transaction using the provided ticket using either WorldPay's "Pay as Order" or "Direct XML" API.
	 * The XML element represents a previously stored card or the details for a 3D Secure cryptogram.
	 * The method will return WorldPay' transaction ID if the authorisation is accepted or one of the following status codes if the authorisation is declined.
	 *
	 * @link
	 *
	 * @param 	SimpleXMLElement $obj_Card	Details for the previously stored card or 3D Secure cryptogram that is used for the authorization
	 * @return 	SimpleXMLElement
	 * @throws	E_USER_WARNING
	 */
	public function authTicket(HTTPConnInfo &$oCI, SimpleXMLElement $obj_Card)
	{
		$sql = "UPDATE Log".sSCHEMA_POSTFIX.".Transaction_Tbl
				SET pspid = ". Constants::iWORLDPAY_PSP ."
				WHERE id = ". $this->getTxnInfo()->getID();
//		echo $sql ."\n";
		$this->getDBConn()->query($sql);
		
		$oc = htmlspecialchars($this->getTxnInfo()->getOrderID(), ENT_NOQUOTES);
		if (empty($oc) === true) { $oc = $this->getTxnInfo()->getID(); }
		// Tokenized Card Details which may be authorized using WorldPay's "Pay As Order" API
		if (count($obj_Card->ticket) == 1)
		{
			$b = $this->_constPayAsOrderRequest($oCI, $obj_Card->ticket, $oc);
		}
		// Other Type of token which may be authorized using WorldPay's "Direct XML" API
		else { $b = $this->_constDirectXMLRequest($oCI, $obj_Card, $oc); }
		
		$obj_HTTP = new HTTPClient(new Template(), $oCI);
		$obj_HTTP->connect();
		$code = $obj_HTTP->send($this->constHTTPHeaders(), $b);
		$obj_HTTP->disConnect();
		$obj_XML = null;
		if ($code == 200)
		{
			$obj_XML = simplexml_load_string($obj_HTTP->getReplyBody() );
			if (strval(@$obj_XML->reply->orderStatus->payment->lastEvent) == "AUTHORISED" || strval(@$obj_XML->reply->orderStatus->payment->lastEvent) == "CAPTURED")
			{
				$obj_XML["code"] = Constants::iPAYMENT_ACCEPTED_STATE;
				$this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_INIT_WITH_PSP_STATE, $obj_XML->asXML() );
			}
			// Error: Unable to initialize payment transaction
			else
			{
				$obj_XML["code"] = Constants::iPAYMENT_DECLINED_STATE;
				trigger_error("Unable to initialize payment with WorldPay for transaction: ". $this->getTxnInfo()->getID() .", error code: ". $obj_XML->reply->error["code"] ."\n". $obj_XML->reply->error->asXML(), E_USER_WARNING);
			}
		}
		// Error: Unable to initialize payment transaction
		else
		{
			trigger_error("Unable to initialize payment with WorldPay for transaction: ". $this->getTxnInfo()->getID() .". HTTP Response Code: ". $code ."\n". var_export($obj_HTTP, true), E_USER_WARNING);
		}
		return $obj_XML;
	}
	/**
	 * Constructs the request for authorizing a payment using previously stored card details through WorldPay's "Pay As Order" API.
	 *
	 * @param 	string $ticket		Valid ticket which references a previously stored card
	 * @return 	string
	 */
	private function _constPayAsOrderRequest(HTTPConnInfo &$oCI, $ticket, $oc)
	{
		list($orderno, $merchantcode, $amount, $currency) = explode(" ### ", $ticket);

		$b = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<!DOCTYPE paymentService PUBLIC "-//WorldPay/DTD WorldPay PaymentService v1//EN" "http://dtd.worldpay.com/paymentService_v1.dtd">';
		$b .= '<paymentService version="1.4" merchantCode="'. htmlspecialchars($this->getMerchantAccount($this->getTxnInfo()->getClientConfig()->getID(), Constants::iWORLDPAY_PSP, true), ENT_NOQUOTES) .'">';
		$b .= '<submit>';
		$b .= '<order orderCode="'. $oc .'">';
		$b .= '<description>Order: '. $oc .' from: '. htmlspecialchars($this->getTxnInfo()->getClientConfig()->getName(), ENT_NOQUOTES) .'</description>';
		$b .= '<amount value="'. $this->getTxnInfo()->getAmount() .'" currencyCode="'. htmlspecialchars(trim($currency), ENT_NOQUOTES) .'" exponent="2" />';
		$b .= '<payAsOrder orderCode="'. htmlspecialchars(trim($orderno), ENT_NOQUOTES) .'" merchantCode="'. htmlspecialchars(trim($merchantcode), ENT_NOQUOTES) .'">';
		$b .= '<amount value="'. intval(trim($amount) ) .'" currencyCode="'. htmlspecialchars(trim($currency), ENT_NOQUOTES) .'" exponent="2" />';
		$b .= '</payAsOrder>';
		$b .= '</order>';
		$b .= '</submit>';
		$b .= '</paymentService>';

		return $b;
	}
	/**
	 * Constructs the request for authorizing a payment using 3D Secure cryptogram through WorldPay's "Direct XML" API.
	 *
	 * @param 	SimpleXMLElement $obj_Card	Details for the 3D Secure cryptogram that is used for the authorization
	 * @return 	string
	 */
	private function _constDirectXMLRequest(HTTPConnInfo &$oCI, SimpleXMLElement $obj_Card, $oc)
	{
		$b = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<!DOCTYPE paymentService PUBLIC "-//WorldPay/DTD WorldPay PaymentService v1//EN" "http://dtd.worldpay.com/paymentService_v1.dtd">';
		$b .= '<paymentService version="1.4" merchantCode="'. htmlspecialchars($this->getMerchantAccount($this->getTxnInfo()->getClientConfig()->getID(), Constants::iWORLDPAY_PSP, true), ENT_NOQUOTES) .'">';
		$b .= '<submit>';
		$b .= '<order orderCode="'. $oc .'">';
		$b .= '<description>Order: '. $oc .' from: '. htmlspecialchars($this->getTxnInfo()->getClientConfig()->getName(), ENT_NOQUOTES) .'</description>';
		$b .= '<amount value="'. $this->getTxnInfo()->getAmount() .'" currencyCode="'. htmlspecialchars(trim($this->getTxnInfo()->getCountryConfig()->getCurrency() ), ENT_NOQUOTES) .'" exponent="2" />';
		$b .= '<paymentDetails>';
		$b .= '<'. $this->getCardName(intval($obj_Card["id"]) ) .'>';
		$b .= '<cardNumber>'. htmlspecialchars($obj_Card->{'card-number'}, ENT_NOQUOTES) .'</cardNumber>';
		$b .= '<expiryDate>';
		$b .= '<date month="'. substr($obj_Card->expiry, 0, 2) .'" year="20'. substr($obj_Card->expiry, -2) .'"/>';
		$b .= '</expiryDate>';
		if (count($obj_Card->{'card-holder-name'}) == 1) { $b .= '<cardHolderName>'. htmlspecialchars($obj_Card->{'card-holder-name'}, ENT_NOQUOTES) .'</cardHolderName>'; }
		else { $b .= '<cardHolderName>John Doe</cardHolderName>'; }
		$b .= '</'. $this->getCardName(intval($obj_Card["id"]) ) .'>';
		$ip = $_SERVER['REMOTE_ADDR'];
		if (array_key_exists("X_FORWARDED_FOR", $_SERVER) === true) { $ip = $_SERVER['X_FORWARDED_FOR']; }
		$b .= '<session shopperIPAddress="'. $ip .'" id="'. $this->getTxnInfo()->getID() .'"/>';
		$b .= '<info3DSecure>';
		$b .= '<xid />';
		$b .= '<cavv>'. htmlspecialchars($obj_Card->cryptogram, ENT_NOQUOTES) .'</cavv>';
		if (strlen($obj_Card->cryptogram["eci"]) > 0)
		{
			$eci = (integer) $obj_Card->cryptogram["eci"];
			$b .= '<eci>'. ($eci < 10 ? "0". $eci : $eci) .'</eci>';
		}
		else { $b .= '<eci />'; }
		$b .= '</info3DSecure>';
		$b .= '</paymentDetails>';
		$b .= '<shopper>';
		$b .= '<browser>';
		$b .= '<acceptHeader>text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8</acceptHeader>';
		$b .= '<userAgentHeader>Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.9.1.5) Gecko/20091102 Firefox/3.5.5 (.NET CLR 3.5.30729)</userAgentHeader>';
		$b .= '</browser>';
		$b .= '</shopper>';
		$b .= '</order>';
		$b .= '</submit>';
		$b .= '</paymentService>';
		
		return $b;
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
		case (12):	// Switch
			$name = "SWITCH-SSL";
			break;
		case (13):	// Solo
			$name = "SOLO_GB-SSL";
			break;
		case (14):	// Delta
			$name = "DELTA-SSL";
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
		case "ECMC_DEBIT-SSL":
		case "ECMC_CREDIT-SSL":
			$id = 7;
			break;
		case "VISA-SSL":	// VISA
		case "VISA_DEBIT-SSL":
		case "VISA_CREDIT-SSL":
			$id = 8;
			break;
		case "VISA_ELECTRON-SSL":	// VISA Electron
			$id = 9;
			break;
		case "SWITCH-SSL":	// Switch
			$id = 12;
			break;
		case "SOLO_GB-SSL":	// Solo
			$id = 13;
			break;
		case "DELTA-SSL":	// Delta
			$id = 14;
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
	public function initialize(HTTPConnInfo &$oCI, $merchantcode, $installationid, $currency, array &$cards)
	{
		$sql = "SELECT data
				FROM Log".sSCHEMA_POSTFIX.".Message_Tbl
				WHERE txnid = ". $this->getTxnInfo()->getID() ." AND stateid = ". Constants::iPAYMENT_INIT_WITH_PSP_STATE ."
				ORDER BY id DESC";
//		echo $sql ."\n";
		$aRS = $this->getDBConn()->getAllNames($sql);
		$url = "";
		if (is_array($aRS) === true)
		{
			for ($i=0; $i<count($aRS); $i++)
			{
				$data = @unserialize($aRS[$i]["DATA"]);
				if (is_array($data) === true && $data["psp-id"] == Constants::iWORLDPAY_PSP)
				{
					$url = $data["url"];
					$i = count($aRS);
				}
			}
		}
		// Payment Transaction not previously initialized with WorldPay
		if (empty($url) === true)
		{
			$oc = htmlspecialchars($this->getTxnInfo()->getOrderID(), ENT_NOQUOTES);
			if (empty($oc) === true) { $oc = $this->getTxnInfo()->getID(); }
			$b = '<?xml version="1.0" encoding="UTF-8"?>';
			$b .= '<!DOCTYPE paymentService PUBLIC "-//WorldPay/DTD WorldPay PaymentService v1//EN" "http://dtd.worldpay.com/paymentService_v1.dtd">';
			$b .= '<paymentService version="1.4" merchantCode="'. $merchantcode .'">';
			$b .= '<submit>';
			$b .= '<order orderCode="'. $oc .'" installationId="'. $installationid .'">';
			$b .= '<description>Order: '. $oc .' from: '. htmlspecialchars($this->getTxnInfo()->getClientConfig()->getName(), ENT_NOQUOTES) .'</description>';
			$b .= '<amount value="'. $this->getTxnInfo()->getAmount() .'" currencyCode="'. $currency .'" exponent="2"/>';
			$b .= '<paymentMethodMask>';
			foreach ($cards as $id)
			{
				$n = $this->getCardName($id);
				// Filter out payment methods that are not supported by WorldPay's Redirect XML API such as Apple Pay
				if (empty($n) === false) { $b .= '<include code="'. $n .'"/>'; }
			}
			$b .= '</paymentMethodMask>';
			if (strlen($this->getTxnInfo()->getEMail() ) > 0)
			{
				$b .= '<shopper>';
				$b .= '<shopperEmailAddress>'. htmlspecialchars($this->getTxnInfo()->getEMail(), ENT_NOQUOTES) .'</shopperEmailAddress>';
				$b .= '</shopper>';
			}
			$b .= '</order>';
			$b .= '</submit>';
			$b .= '</paymentService>';
			
			$obj_HTTP = new HTTPClient(new Template(), $oCI);
			$obj_HTTP->connect();
			$code = $obj_HTTP->send($this->constHTTPHeaders(), $b);
			$obj_HTTP->disConnect();
			if ($code == 200)
			{
				$obj_XML = simplexml_load_string($obj_HTTP->getReplyBody() );

				if (floatval($obj_XML->reply->orderStatus->reference["id"]) > 0)
				{
					$data = array("psp-id" => Constants::iWORLDPAY_PSP,
								  "url" => strval($obj_XML->reply->orderStatus->reference) );
					$this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_INIT_WITH_PSP_STATE, serialize($data) );

					$sql = "UPDATE Log".sSCHEMA_POSTFIX.".Transaction_Tbl
							SET pspid = ". Constants::iWORLDPAY_PSP .", extid = '". $this->getDBConn()->escStr($obj_XML->reply->orderStatus->reference["id"]) ."'
							WHERE id = ". $this->getTxnInfo()->getID();
//					echo $sql ."\n";
					$this->getDBConn()->query($sql);
					$url = $obj_XML->reply->orderStatus->reference;
				}
				// Error: Unable to initialize payment transaction
				else
				{
					trigger_error("Unable to initialize payment transaction with WorldPay, error code: ". $obj_XML->reply->error["code"] ."\n". $obj_XML->reply->error->asXML(), E_USER_WARNING);

					throw new mPointException("WorldPay returned Error: ". $obj_XML->reply->error ." (". $obj_XML->reply->error["code"] .")", 1101);
				}
			}
			// Error: Unable to initialize payment transaction
			else
			{
				trigger_error("Unable to initialize payment transaction with WorldPay. HTTP Response Code: ". $code ."\n". var_export($obj_HTTP, true), E_USER_WARNING);

				throw new mPointException("WorldPay returned HTTP Code: ". $code, 1100);
			}
		}

		return $url;
	}

	/**
	 * Initialises Callback to the Client.
	 *
	 * @param 	HTTPConnInfo $oCI 		Connection Info required to communicate with the Callback component for Cellpoint Mobile
	 * @param	SimpleXMLElement $oXML	The XML response received from WorldPay
	 */
	public function initCallback(HTTPConnInfo &$oCI, SimpleXMLElement &$oXML)
	{
		$b = str_replace("reply>", "notify>", $oXML->asXML() );
		$b = str_replace("<orderStatus", "<orderStatusEvent", $b);
		$b = str_replace("</orderStatus>", "</orderStatusEvent>", $b);
		$obj_HTTP = new HTTPClient(new Template(), $oCI);
		$obj_HTTP->connect();
		$obj_HTTP->send($this->constHTTPHeaders(), $b);
		$obj_HTTP->disConnect();
	}
}
?>