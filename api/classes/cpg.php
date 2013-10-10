<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol.
 * The PayEx subpackage is a specific implementation capable of imitating CPG's own protocol.
 *
 * @author Jonatan Evald Buus, Tomas Kraina
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage CPG
 * @version 1.00
 */

/**
 * Model Class containing all the Business Logic for handling interaction with CPG
 *
 */
class CPG extends Callback
{
	
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

	public function initialize(HTTPConnInfo &$oCI, $currency, $shortCode, $description, $shippingInfo)
	{
        
        $orderContent = NULL;
        $shopperIPAddress = NULL;
        $authenticatedShopperID = NULL;
        $orderCode = NULL;
        $exponent = 2;
        $debitCardIndication = 'credit';
        $cardId = 8;
        $oldOrder = NULL;
        $description = "mPoint ID: ". $this->getTxnInfo()->getID() ." for Order No.:". $this->getTxnInfo()->getOrderID();
        $creditCardInfoAvailable = FALSE;
        $billingAddressAvailable = FALSE;
        $nominalAuth = NO;
        
		$b = '<?xml version="1.0" encoding="UTF-8"?>';
        $b.= '<submit>';
        $b.= ' <shortCode>'. $shortCode .'</shortCode>'; // Short code of the Storefront application 
        $b.= ' <order orderCode="'. $orderCode .'">'; // mandatory, needs to be unique
        if (strlen($oldOrder) > 0)
        {
            $b.= '  <oldOrder></oldOrder>'; // Optional
        }
        if ($nominalAuth === YES)
        {
            $b.= '  <nominalAuth>Y</nominalAuth>'; // Optional, only storefronts that can accept nominalAuth need to send this element    
        }
        $b.= '  <description>'. htmlspecialchars($description, ENT_NOQUOTES) .'</description>'; // Mandatory, maxlenght=50, simple one line description of the order
        $b.= '  <amount value="'. $this->getTxnInfo()->getAmount() .'" curencyCode="'. $currency .'" exponent="'. $exponent .'" debitCardIndication="'. $debitCardIndication .'"/>'; // Mandatory, decimal based on exponent, code uppercase ISO4217, indicator should be always 'credit'
        $b.= '  <orderContent>';
        $b.= '   <![CDATA]['. htmlspecialchars($orderContent, ENT_NOQUOTES) .']]>'; // don't use <html><body> tags
        $b.= '  </orderContent>';
        if ($billingAddressAvailable === TRUE)
        {
            $b.= '  <paymentDetails>';
            if ($creditCardInfoAvailable)
            {
                $b.= '   <'. $this->getCardName($cardId) .'>';
                $b.= '    <cardNumber>4444333322221111</cardNumber>'; // mandatory, 0-20
                $b.= '    <expiryDate>';
                $b.= '     <date month="09" year="2003" />'; // mandatory
                $b.= '    </expiryDate>';
                $b.= '    <cardHolderName>J. Doe</cardHolderName>'; // mandatory
            }
            $b.= '    <cardAddress>';
            $b.= '     <address>';
            $b.= '      <firstName>'. htmlspecialchars($this->getTxnInfo()->getClientConfig()->getName(), ENT_NOQUOTES) .'</firstName>'; // mandatory, 0-40 chars
            $b.= '      <lastName>'. htmlspecialchars($this->getTxnInfo()->getClientConfig()->getName(), ENT_NOQUOTES) .'</lastName>'; // mandatory, 0-40 chars
            $b.= '      <street>11 Hereortherestreet</street>'; // mandatory, 0-100 chars
            $b.= '      <postalCode>1234KL</postalCode>'; // optional, 0-20 chars
            $b.= '      <city>Somewhereorother</city>'; // mandatory, 0-50 chars
            $b.= '      <countryCode>TP</countryCode>'; // mandatory, 2-2 chars
            $b.= '      <telephoneNumber>0123456789</telephoneNumber>'; // optional
            $b.= '     </address>';
            $b.= '    </cardAddress>';
            if ($creditCardInfoAvailable)
            {
                $b.= '   </.'. $this->getCardName($cardId) .'>';
            }
            $b.= '  </paymentDetails>';
        }
        $b.= '  <shopper>';
        $b.= '   <shopperIPAddress>'. $_SERVER['REMOTE_ADDR'] .'</shopperIPAddress>'; // mandatory
        $b.= '   <shopperEmailAddress>'. htmlspecialchars($this->getTxnInfo()->getEMail(), ENT_NOQUOTES) .'</shopperEmailAddress>'; // optional, 0-50 chars
        $b.= '   <authenticatedShopperID>1234567890</authenticatedShopperID>'; // optional, applicable to BIBIT, 0-20 chars
        $b.= '  </shopper>';
        $b.= '  <shippingAddress>';
        $b.= '   <address>';
        $b.= '    <firstName>Joh</firstName>'; // mandatory, 0-40 chars
        $b.= '    <lastName>Doe</lastName>'; // mandatory, 0-40 chars
        $b.= '    <street>11 Hereortherestreet</street>'; // mandatory, 0-100 chars
        $b.= '    <postalCode>1234KL</postalCode>'; // optional, 0-20 chars
        $b.= '    <city>Somewhereorother</city>'; // mandatory, 0-50 chars
        $b.= '    <countryCode>TP</countryCode>'; // mandatory, 2-2 chars
        $b.= '    <telephoneNumber>0123456789</telephoneNumber>'; // optional
        $b.= '   </address>';
        $b.= '  </shippingAddress>';
        $b.= ' </order>';
        $b.= ' <returnURL>'. "http://". $_SERVER['HTTP_HOST'] ."/pay/accept.php?mpoint-id=". $this->getTxnInfo()->getID() .'</returnURL>';
//        $b.= ' <teaLeafGuid>452ef50f-68ef-4677-9b19-d7140c444d19</teaLeafGuid>'; // ???
        $b.= '</submit>';
        
		$aParams = array(
                         "accountNumber" => $an,
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
			$sql = "UPDATE Log".sSCHEMA_POSTFIX.".Transaction_Tbl
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
			$sCardHolder = "";
			
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
						elseif (stristr($name, "CardHolderName") == true) { $sCardHolder = $name; }
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
			if (empty($sCardHolder) === false) { $xml .= '<name>'. htmlspecialchars($sCardHolder, ENT_NOQUOTES) .'</name>'; }
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
				FROM Log".sSCHEMA_POSTFIX.".Transaction_Tbl
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
				// Payment Authorized
				if ($obj_XML->transactionStatus == 3) { $sid = Constants::iPAYMENT_ACCEPTED_STATE; }
				else { $sid = Constants::iPAYMENT_REJECTED_STATE; }
				
				$sql = "UPDATE Log".sSCHEMA_POSTFIX.".Transaction_Tbl
						SET extid = NULL
						WHERE id = ". $this->getTxnInfo()->getID();
//				echo $sql ."\n";
				$this->getDBConn()->query($sql);
				$obj_XML->status["code"] = $this->completeTransaction(Constants::iPAYEX_PSP, $obj_XML->transactionNumber, $this->getCardID($obj_XML->paymentMethod), $sid, array("result" => $obj_Std->CompleteResult) );
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