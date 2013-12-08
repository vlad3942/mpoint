<?php
/**
 * Path to Log Files directory
 */
/**
 * Define path to the directory which holds the different API class files
 *
 */
require_once("../inc/include.php");
require_once(sAPI_CLASS_PATH ."/template.php");
require_once(sAPI_CLASS_PATH ."/http_client.php");
/**
 * Connection info for sending error reports to a remote host
 */
$aHTTP_CONN_INFO["mesb"]["protocol"] = "http";
$aHTTP_CONN_INFO["mesb"]["host"] = "10.150.242.42";		// EK: Dev
//$aHTTP_CONN_INFO["mesb"]["host"] = "10.50.245.137";		// EK: Pre-Prod 1
//$aHTTP_CONN_INFO["mesb"]["host"] = "10.150.242.41";		// EK: Pre-Prod 2
//$aHTTP_CONN_INFO["mesb"]["host"] = "localhost";			
$aHTTP_CONN_INFO["mesb"]["port"] = 9000; 				// EK MESB
//$aHTTP_CONN_INFO["mesb"]["host"] = "dsb.mesb.test.cellpointmobile.com";
//$aHTTP_CONN_INFO["mesb"]["port"] = 10080; // Local MESB
//$aHTTP_CONN_INFO["mesb"]["host"] = $_SERVER['HTTP_HOST'];
//$aHTTP_CONN_INFO["mesb"]["port"] = 80; // mPoint
$aHTTP_CONN_INFO["mesb"]["timeout"] = 120;
$aHTTP_CONN_INFO["mesb"]["method"] = "POST";
$aHTTP_CONN_INFO["mesb"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["mesb"]["username"] = "IBE";
$aHTTP_CONN_INFO["mesb"]["password"] = "kjsg5Ahf_1";
//$aHTTP_CONN_INFO["mesb"]["username"] = "DSB"; // Local username
//$aHTTP_CONN_INFO["mesb"]["password"] = "hdfy28abdl"; // Local password

//$h .= "user-agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0" .HTTPClient::CRLF;
//$h .= "accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8" .HTTPClient::CRLF;
//$h .= "accept-language: en-US,en;q=0.5" .HTTPClient::CRLF;

header("Content-Type: text/html; charset=\"utf-8\"");
set_time_limit(0);

class AutoTest
{
	const sSTATUS_SUCCESS = '<span class="success">Passed</span>';
	const sSTATUS_WARNING = '<span class="warning">Failed</span>';
	const sSTATUS_FAILED = '<span class="error">Failed</span>';
	
	private $_aConnInfo = array();
	private $_obj_Client = null;
	private $_sDebug;
	
	private $_iClientID;
	private $_iAccount;
	private $_sCustomerRef;
	private $_country;
	private $_lMobile;
	private $_sEMail;
	private $_card;
	
	public function __construct(array &$aCI, $clid, $acc, $cr, $cnt, $mob, $email, $crd)
	{
		$this->_aConnInfo = $aCI; 
		$this->_iClientID = (integer) $clid;
		$this->_iAccount = $acc;
		$this->_sCustomerRef = trim($cr);
		$this->_country = trim($cnt);
		$this->_lMobile = floatval($mob);
		$this->_sEMail = trim($email);
		$this->_card = trim($crd);
	}
	
	private function _constHeaders()
	{
		$h = "{METHOD} {PATH} HTTP/1.0" .HTTPClient::CRLF;
		$h .= "host: {HOST}" .HTTPClient::CRLF;
		$h .= "referer: {REFERER}" .HTTPClient::CRLF;
		$h .= "content-length: {CONTENTLENGTH}" .HTTPClient::CRLF;
		$h .= "content-type: {CONTENTTYPE}" .HTTPClient::CRLF;
		$h .= "user-agent: mPoint" .HTTPClient::CRLF;
		
		return $h;
	}
	private function _constmPointHeaders()
	{
		$h = trim($this->_constHeaders() );
		$h .= HTTPClient::CRLF;
		$h .= "authorization: Basic ". base64_encode($this->_aConnInfo["username"] .":". $this->_aConnInfo["password"]) . HTTPClient::CRLF;
		
		return $h;
	}
	
	private function _constClientInfo()
	{
		$xml = '<client-info platform="iOS" version="5.1.1" language="da">';
		$xml .= '<customer-ref>'. htmlspecialchars($this->_sCustomerRef, ENT_NOQUOTES) .'</customer-ref>';
		$xml .= '<mobile country-id="'. $this->_country .'">'. $this->_lMobile .'</mobile>';
		$xml .= '<email>'. $this->_sEMail .'</email>';
		$xml .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
		$xml .= '</client-info>';
	
		return $xml;
	}
	
	private function _initialize($cv="")
	{
		$b = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<initialize-payment client-id="'. $this->_iClientID .'" account="'. $this->_iAccount .'">';
		$b .= '<transaction order-no="EST/NGPN4N/07NOV2013/1507-'. time() .'">';
		$b .= '<amount country-id="'. $this->_country .'">147500</amount>';
//		$b .= '<auth-url>http://localhost/_test/auth.php</auth-url>';
//		$b .= '<callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url>';
		$b .= '<description>
				<![CDATA[
				<center><table><tr><td bgcolor="#ffff00">Your Internet Order:</td><td colspan="2" bgcolor="#ffff00" align="right">EST/NGPN4N/29OCT2013/1507</td></tr><tr><td bgcolor="#ffff00">Description:</td><td>EK Internet Booking Engine</td><td align="right">1.00</td></tr><tr><td colspan="2">Subtotal:</td><td align="right">1475.0</td></tr><tr><td colspan="2" bgcolor="#c0c0c0">Total cost:</td><td bgcolor="#c0c0c0" align="right">1475.0</td></tr><tr><td colspan="3">&nbsp;</td></tr><tr><td bgcolor="#ffff00" colspan="3">Your billing address:</td></tr><tr><td colspan="3"><br>Address Object contains :
				address1:dsfdsf
				address2: 
				address2: 
				city: sdfsdf
				region: 
				country: AZ
				postalcode: 
				contactType: </td></tr><tr><td colspan="3">&nbsp;</td></tr><tr><td bgcolor="#ffff00" colspan="3">Your shipping address:</td></tr><tr><td colspan="3"><br>Address Object contains :
				address1:
				address2: 
				address2: 
				city: 
				region: 
				country: 
				postalcode: 
				contactType: </td></tr><tr><td colspan="3">&nbsp;</td></tr><tr><td bgcolor="#ffff00" colspan="3">Our contact information:</td></tr><tr><td colspan="3">Emirates Airlines,<br>P.O.Box No 686,<br>1255 KZ Dubai,<br>UAE.<br><br>payment@emirates.com<br>971 4-7035726</td></tr></table></center>
				]]>
			   </description>';
		$b .= '<custom-variables>';
		$b .= '<tax>33500</tax>';
		$b .= '<enhanced-data>
				<![CDATA[
				<enchancedData code="EK"><airline code="EK" /><passenger type = "ADT" passengerID="300003524" paxClass="" productName="FLIGHT" productCode="SME Revenue">Alalawi/Iman</passenger><pnr code="NGPN4N"><flight carrierCode="EK0600"><departureAirport>DXB</departureAirport><arrivalAirport>KHI</arrivalAirport><departureDate><depdate day = "15" month="12" year="2013" /></departureDate><arrivalDate><arrdate day = "15" month="12" year="2013" /></arrivalDate><jrnyType>Return</jrnyType><fare class="Y" /></flight><flight carrierCode="EK0605"><departureAirport>KHI</departureAirport><arrivalAirport>DXB</arrivalAirport><departureDate><depdate day = "21" month="12" year="2013" /></departureDate><arrivalDate><arrdate day = "21" month="12" year="2013" /></arrivalDate><jrnyType>Return</jrnyType><fare class="Y" /></flight></pnr><devSessionID>15547305</devSessionID><merchData2>Y</merchData2><bookingType>SME Revenue</bookingType><merchData1></merchData1><merchData3>SME BOOKING</merchData3><merchData4>IMAN ALALAWI</merchData4><merchData5>IMAN ALALAWI</merchData5><merchData6>EK 0600</merchData6><merchData7>15 Dec 13</merchData7><merchData8>NGPN4N</merchData8><thirdParty>false</thirdParty><deptTime>08:00</deptTime><agent code="" /><redemptionTicket></redemptionTicket><ticketOption>ETKT</ticketOption><promotionalCode></promotionalCode><skywardsNumber>300003524</skywardsNumber><productCode>SME Revenue</productCode><bkgChannel>WEB</bkgChannel><tax currencyCode="AED" exponent="2" value="147500"></tax></enchancedData>
				]]>
			   </enhanced-data>';
		$b .= $cv;
		$b .= '</custom-variables>';
		$b .= '</transaction>';
		$b .= $this->_constClientInfo();
		$b .= '</initialize-payment>';
		$b .= '</root>';
		
		// mPoint
		if ($this->_aConnInfo["port"] == 80 || $this->_aConnInfo["port"] == 443)
		{ 
			$this->_aConnInfo["path"] = "/mApp/api/initialize.php";
		}
		// Mobile Enterprise Service Bus
		else { $this->_aConnInfo["path"] = "/mpoint/initialize-payment"; }
		
		$obj_ConnInfo = HTTPConnInfo::produceConnInfo($this->_aConnInfo);
		$this->_obj_Client = new HTTPClient(new Template, $obj_ConnInfo);
		$this->_obj_Client->connect();
		$code = $this->_obj_Client->send($this->_constmPointHeaders(), $b);
		if ($code == 200)
		{
			$obj_XML = simplexml_load_string($this->_obj_Client->getReplyBody() );
		}
		else { $obj_XML = null; }
		$this->_obj_Client->disconnect();
		
		return $obj_XML;
	}
	
	public function getClient() { return $this->_obj_Client; }
	public function getDebug() { return $this->_sDebug; }
	
	public function getCRISAuthToken()
	{
		$b = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<login>';
		$b .= '<username>00777415236</username>';
		$b .= '<password>a1111111</password>';
		$b .= '</login>';
		$b .= '</root>';
		
		$this->_aConnInfo["path"] = "/mpoint/emirates/cris/get-member";
		
		$obj_ConnInfo = HTTPConnInfo::produceConnInfo($this->_aConnInfo);
		$this->_obj_Client = new HTTPClient(new Template, $obj_ConnInfo);
		$this->_obj_Client->connect();
		$code = $this->_obj_Client->send($this->_constmPointHeaders(), $b);
		$this->_obj_Client->disconnect();
		
		if ($code == 200)
		{
			$obj_XML = simplexml_load_string($this->_obj_Client->getReplyBody() );
			
			return $obj_XML->{'auth-token'};
		}
		else { return ""; }
	}
	
	/* ========== Automatic Payment Tests Start ========== */
	public function initializePaymentWithEnhancedDataTest()
	{
		$this->_sDebug = "";
		if ( ($this->_initialize() instanceof SimpleXMLElement) === true)
		{
			return self::sSTATUS_SUCCESS;
		}
		else
		{
			$this->_sDebug = $this->_obj_Client->getReplyBody();
			return self::sSTATUS_FAILED;
		}
	}
	public function initializePaymentWithInstalmentsTest()
	{
		$b = '<fiscal-number>1234567890</fiscal-number>';
		$b .= '<payment-country-code>AE</payment-country-code>';
		$b .= '<number-of-instalments>2</number-of-instalments>';
		$this->_sDebug = "";
		if ( ($this->_initialize($b) instanceof SimpleXMLElement) === true)
		{
			return self::sSTATUS_SUCCESS;
		}
		else
		{
			$this->_sDebug = $this->_obj_Client->getReplyBody();
			return self::sSTATUS_FAILED;
		}
	}
	public function payTest()
	{
		$this->_sDebug = "";
		$obj_XML = $this->_initialize();
		if ( ($obj_XML instanceof SimpleXMLElement) === true)
		{
			$b = '<?xml version="1.0" encoding="UTF-8"?>';
			$b .= '<root>';
			$b .= '<pay client-id="'. $this->_iClientID .'" account="'. $this->_iAccount .'">';
			$b .= '<transaction id="'. $obj_XML->transaction["id"] .'" store-card="false">';
			$b .= '<card type-id="'. $this->_card .'">';
			$b .= '<amount country-id="'. $obj_XML->transaction->amount["country-id"] .'">'. $obj_XML->transaction->amount .'</amount>';
			$b .= '</card>';
			$b .= '</transaction>';
			$b .= $this->_constClientInfo();
			$b .= '</pay>';
			$b .= '</root>';
			
			// mPoint
			if ($this->_aConnInfo["port"] == 80 || $this->_aConnInfo["port"] == 443)
			{
				$this->_aConnInfo["path"] = "/mApp/api/pay.php";
			}
			// Mobile Enterprise Service Bus
			else { $this->_aConnInfo["path"] = "/mpoint/pay"; }
			
			$obj_ConnInfo = HTTPConnInfo::produceConnInfo($this->_aConnInfo);
			$this->_obj_Client = new HTTPClient(new Template, $obj_ConnInfo);
			$this->_obj_Client->connect();
			$code = $this->_obj_Client->send($this->_constmPointHeaders(), $b);
			$this->_obj_Client->disconnect();
			if ($code == 200)
			{
				return self::sSTATUS_SUCCESS;
			}
			else
			{
				$this->_sDebug = $this->_obj_Client->getReplyBody();
				return self::sSTATUS_FAILED;
			}
		}
		else
		{
			$this->_sDebug = $this->_obj_Client->getReplyBody();
			return self::sSTATUS_FAILED;
		}
	}
	private function _authorize($at, $cv="")
	{
		$this->_sDebug = "";
		$obj_XML = $this->_initialize($cv);
		if ( ($obj_XML instanceof SimpleXMLElement) === true && count($obj_XML->{'stored-cards'}) == 1)
		{
			$b = '<?xml version="1.0" encoding="UTF-8"?>';
			$b .= '<root>';
			$b .= '<authorize-payment client-id="'. $this->_iClientID .'" account="'. $this->_iAccount .'">';
			$b .= '<transaction id="'. $obj_XML->transaction["id"] .'">';
			$b .= '<card id="'. $obj_XML->{'stored-cards'}->card["id"] .'" type-id="'. $obj_XML->{'stored-cards'}->card["type-id"] .'">';
			$b .= '<amount country-id="'. $obj_XML->transaction->amount["country-id"] .'">'. $obj_XML->transaction->amount .'</amount>';
			$b .= '<cvc>123</cvc>';
			$b .= '</card>';
			$b .= '</transaction>';
			$b .= '<auth-token>'. htmlspecialchars($at, ENT_NOQUOTES) .'</auth-token>';
			$b .= $this->_constClientInfo();
			$b .= '</authorize-payment>';
			$b .= '</root>';

			// mPoint
			if ($this->_aConnInfo["port"] == 80 || $this->_aConnInfo["port"] == 443)
			{
				$this->_aConnInfo["path"] = "/mApp/api/authorize.php";
			}
			// Mobile Enterprise Service Bus
			else { $this->_aConnInfo["path"] = "/mpoint/authorize-payment"; }
		
			$obj_ConnInfo = HTTPConnInfo::produceConnInfo($this->_aConnInfo);
			$this->_obj_Client = new HTTPClient(new Template, $obj_ConnInfo);
			$this->_obj_Client->connect();
			$code = $this->_obj_Client->send($this->_constmPointHeaders(), $b);
			$this->_obj_Client->disconnect();
			if ($code == 200)
			{
				$obj_XML = simplexml_load_string($this->_obj_Client->getReplyBody() );
				if ($obj_XML->status["code"] >= 100)
				{
					if (count($obj_XML->status->url) == 0)
					{
						$this->_sDebug = "Redirect URL not returned";
						return self::sSTATUS_WARNING;
					}
					else
					{
						$this->_sDebug = $this->_obj_Client->getReplyBody();
						return self::sSTATUS_SUCCESS;
					}
				}
				else
				{
					$this->_sDebug = $this->_obj_Client->getReplyBody();
					return self::sSTATUS_FAILED;
				}
			}
			else
			{
				$this->_sDebug = $this->_obj_Client->getReplyBody();
				return self::sSTATUS_FAILED;
			}
		}
		elseif ( ($obj_XML instanceof SimpleXMLElement) === true)
		{
			$this->_sDebug = "No Stored Cards Found for End-User: ". $obj_XML->transaction["eua-id"];
			return self::sSTATUS_WARNING;
		}
		else
		{
			$this->_sDebug = $this->_obj_Client->getReplyBody();
			return self::sSTATUS_WARNING;
		}
	}
	public function authorizeStoredCardUsingSSOTest($at)
	{
		return $this->_authorize($at);
	}
	public function authorizeStoredCardWithInstalmentsUsingSSOTest($at)
	{
		$b = '<fiscal-number>1234567890</fiscal-number>';
		$b .= '<payment-country-code>AE</payment-country-code>';
		$b .= '<number-of-instalments>2</number-of-instalments>';
		return $this->_authorize($at, $b);
	}
	public function authorizeStoredCardWithMultiCurrencyUsingSSOTest($at)
	{
		$b = '<mcp>
				<![CDATA[
				<MCP><amount currencyCode="JPY" value="8551200" exponent="2" /><effectiveRate>5908173168</effectiveRate><mcpOption>Y</mcpOption></MCP>
				]]>
			  </mcp>';
		
		return $this->_authorize($at, $b);
	}
	public function auth2($at)
	{
		$b = '<?xml version="1.0" encoding="ISO-8859-1"?><root><authorize-payment account="100010" client-id="10001"><transaction id="354"><card type-id="MAST" id="205"><amount country-id="BH">52</amount><cvc>123</cvc></card></transaction><auth-token>'. $at .'</auth-token><client-info language="gb" version="1.2" platform="Web"><customer-ref>00100119331</customer-ref><email>ravi.gupta@emirates.com</email><device-id>145031385</device-id><ip>127.0.0.1</ip></client-info></authorize-payment></root>';
		// mPoint
		if ($this->_aConnInfo["port"] == 80 || $this->_aConnInfo["port"] == 443)
		{
			$this->_aConnInfo["path"] = "/mApp/api/authorize.php";
		}
		// Mobile Enterprise Service Bus
		else { $this->_aConnInfo["path"] = "/mpoint/authorize-payment"; }
		
		$obj_ConnInfo = HTTPConnInfo::produceConnInfo($this->_aConnInfo);
		$this->_obj_Client = new HTTPClient(new Template, $obj_ConnInfo);
		$this->_obj_Client->connect();
		$code = $this->_obj_Client->send($this->_constmPointHeaders(), $b);
		$this->_obj_Client->disconnect();
		if ($code == 200)
		{
			$obj_XML = simplexml_load_string($this->_obj_Client->getReplyBody() );
			if ($obj_XML->status["code"] >= 100)
			{
				if (count($obj_XML->status->url) == 0)
				{
					$this->_sDebug = $this->_obj_Client->getReplyBody();
					return self::sSTATUS_WARNING;
				}
				else
				{
					$this->_sDebug = $this->_obj_Client->getReplyBody();
					return self::sSTATUS_SUCCESS;
				}
			}
			else
			{
				$this->_sDebug = $this->_obj_Client->getReplyBody();
				return self::sSTATUS_FAILED;
			}
		}
		else
		{
			$this->_sDebug = $this->_obj_Client->getReplyBody();
			return self::sSTATUS_FAILED;
		}
	}
	/* ========== Automatic mConsole Tests Start ========== */
	
	private function _mConsoleSearchTest($mobile=0, $email="", $cr="", $start="", $end="")
	{
		$this->_sDebug = "";
		
		$b = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<search client-id="'. $this->_iClientID .'">';
		if (floatval($mobile) > 0) { $b .= '<mobile country-id="DK">'. floatval($mobile) .'</mobile>'; }
		if (empty($email) === false) { $b .= '<email>'. htmlspecialchars($email, ENT_NOQUOTES) .'</email>'; }
		if (empty($cr) === false) { $b .= '<customer-ref>'. htmlspecialchars($cr, ENT_NOQUOTES) .'</customer-ref>'; }
		if (empty($start) === false && empty($end) === false)
		{
			$b .= '<start-date>'. htmlspecialchars($start, ENT_NOQUOTES) .'</start-date>';
			$b .= '<end-date>'. htmlspecialchars($end, ENT_NOQUOTES) .'</end-date>';
		}
		$b .= '</search>';
		$b .= '</root>';
		
		// mPoint
		if ($this->_aConnInfo["port"] == 80 || $this->_aConnInfo["port"] == 443)
		{
			$this->_aConnInfo["path"] = "/mConsole/api/search.php";
		}
		// Mobile Enterprise Service Bus
		else { $this->_aConnInfo["path"] = "/mpoint/mconsole/search"; }
		
		$obj_ConnInfo = HTTPConnInfo::produceConnInfo($this->_aConnInfo);
		$this->_obj_Client = new HTTPClient(new Template, $obj_ConnInfo);
		$this->_obj_Client->connect();
		$code = $this->_obj_Client->send($this->_constmPointHeaders(), $b);
		$this->_obj_Client->disconnect();
		if ($code == 200)
		{
			$obj_XML = simplexml_load_string($this->_obj_Client->getReplyBody() );
			if (count($obj_XML->{'audit-logs'}->{'audit-log'}) > 0 && count($obj_XML->transactions->transaction) > 0)
			{
				$this->_sDebug = $obj_XML->transactions->transaction->asXML();
				return self::sSTATUS_SUCCESS;
			}
			elseif (count($obj_XML->{'audit-logs'}->{'audit-log'}) > 0)
			{
				$this->_sDebug = "No Transaction Log Entries Found";
				return self::sSTATUS_WARNING;
			}
			elseif (count($obj_XML->transactions->transaction) > 0)
			{
				$this->_sDebug = "No Audit Log Entries Found";
				return self::sSTATUS_WARNING;
			}
			else
			{
				$this->_sDebug = "No Logs Found";
				return self::sSTATUS_FAILED;
			}
		}
		else
		{
			$this->_sDebug = $this->_obj_Client->getReplyBody();
			return self::sSTATUS_FAILED;
		}
	}
	public function mConsoleSearchUsingMobileTest()
	{
		return $this->_mConsoleSearchTest($this->_lMobile);
	}
	public function mConsoleSearchUsingEMailTest()
	{
		return $this->_mConsoleSearchTest(0, $this->_sEMail);
	}
	public function mConsoleSearchUsingCustomerRefTest()
	{
		return $this->_mConsoleSearchTest(0, "", $this->_sCustomerRef);
	}
	public function mConsoleSearchUsingPeriodTest()
	{
		return $this->_mConsoleSearchTest(0, "", "", "2013-10-01T00:00:00+00:00", "2013-12-01T00:00:00+00:00");
	}
	/* ========== Automatic mConsole Tests End ========== */
	
	/* ========== Automatic Account Management Tests Start ========== */
	public function saveCardNameUsingSSOTest($at)
	{
		$obj_XML = $this->_loginUsingSSOTest($at);
		if ( ($obj_XML instanceof SimpleXMLElement) === true)
		{
			if (count($obj_XML->{'stored-cards'}->card) > 0)
			{
				$id = $obj_XML->{'stored-cards'}->card[0]["id"];
				$name = "My VISA";
				$b = '<?xml version="1.0" encoding="UTF-8"?>';
				$b .= '<root>';
				$b .= '<save-card client-id="'. $this->_iClientID .'" account="'. $this->_iAccount .'">';
				$b .= '<card id="'. $id .'" preferred="false">';
				$b .= '<name>'. $name .'</name>';
				$b .= '</card>';
				$b .= $this->_constClientInfo();
				$b .= '</save-card>';
				$b .= '</root>';
				
				$this->_sDebug = "";
				// mPoint
				if ($this->_aConnInfo["port"] == 80 || $this->_aConnInfo["port"] == 443)
				{
					$this->_aConnInfo["path"] = "/mApp/api/save_card.php";
				}
				// Mobile Enterprise Service Bus
				else { $this->_aConnInfo["path"] = "/mpoint/save-card"; }
				
				$obj_ConnInfo = HTTPConnInfo::produceConnInfo($this->_aConnInfo);
				$this->_obj_Client = new HTTPClient(new Template, $obj_ConnInfo);
				$this->_obj_Client->connect();
				$code = $this->_obj_Client->send($this->_constmPointHeaders(), $b);
				$this->_obj_Client->disconnect();
				if ($code == 200)
				{
					$obj_XML = simplexml_load_string($this->_obj_Client->getReplyBody() );
					if ($obj_XML->status["code"] >= 100)
					{
						$obj_XML = $this->_loginUsingSSOTest($at);
						$aObj_XML = $obj_XML->xpath("/root/stored-cards/card[@id = ". $id ."]");
						if (count($aObj_XML) == 1 && $aObj_XML[0]->name == $name && $aObj_XML[0]["preferred"] == "false")
						{
							$this->_sDebug = "Card ID: ". $id;
							return self::sSTATUS_SUCCESS;
						}
						elseif (is_array($aObj_XML) === true && count($aObj_XML) == 1)
						{
							$this->_sDebug = "Wrong Card name: ". $aObj_XML[0]->name ." expected: ". $name ." or preferred: ". $aObj_XML[0]["preferred"] ." not false";
							return self::sSTATUS_FAILED;
						}
						else
						{
							$this->_sDebug = "Card not found";
							return self::sSTATUS_FAILED;
						}
					}
					else
					{
						$this->_sDebug = $this->_obj_Client->getReplyBody();
						return self::sSTATUS_FAILED;
					}
				}
				else
				{
					$this->_sDebug = $this->_obj_Client->getReplyBody();
					return self::sSTATUS_FAILED;
				}
			}
			else
			{
				$this->_sDebug = "No Stored Cards returned";
				return self::sSTATUS_WARNING;
			}
		}
		else
		{
			$this->_sDebug = $this->_obj_Client->getReplyBody();
			return self::sSTATUS_WARNING;
		}
	}
	
	public function saveMaskedCardTest($at, $type="", $name="")
	{
		if (empty($type) === true) { $type = $this->_card; }
		$b = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<save-card client-id="'. $this->_iClientID .'" account="'. $this->_iAccount .'">';
		$b .= '<card psp-id="9" type-id="'. htmlspecialchars($type, ENT_NOQUOTES) .'" preferred="true">';
		if (empty($name) === false) { $b .= '<name>'. htmlspecialchars($name, ENT_NOQUOTES) .'</name>'; }
		$b .= '<card-number-mask>4444********3333</card-number-mask>';
		$b .= '<expiry-month>07</expiry-month>';
		$b .= '<expiry-year>17</expiry-year>';
		$b .= '<token>123470-ABCD</token>';
		$b .= '<card-holder-name>mohamedgiya ulhak</card-holder-name>';
		$b .= '<address country-id="'. $this->_country .'">';
		$b .= '<first-name>Mohamedgiya</first-name>';
		$b .= '<last-name>Ulhak</last-name>';
		$b .= '<street>Anna nagar</street>';
		$b .= '<postal-code>600408</postal-code>';
		$b .= '<city>Chennai</city>';
		if ($this->_country == "IN") { $b .= '<state>TR</state>'; }
		$b .= '</address>';
		$b .= '</card>';
		$b .= '<password>oisJona1</password>';
		$b .= '<auth-token>'. htmlspecialchars($at, ENT_NOQUOTES) .'</auth-token>';
		$b .= $this->_constClientInfo();
		$b .= '</save-card>';
		$b .= '</root>';
		
		$this->_sDebug = "";
		// mPoint
		if ($this->_aConnInfo["port"] == 80 || $this->_aConnInfo["port"] == 443)
		{
			$this->_aConnInfo["path"] = "/mApp/api/save_card.php";
		}
		// Mobile Enterprise Service Bus
		else { $this->_aConnInfo["path"] = "/mpoint/save-card"; }
		
		$obj_ConnInfo = HTTPConnInfo::produceConnInfo($this->_aConnInfo);
		$this->_obj_Client = new HTTPClient(new Template, $obj_ConnInfo);
		$this->_obj_Client->connect();
		$code = $this->_obj_Client->send($this->_constmPointHeaders(), $b);
		$this->_obj_Client->disconnect();
		
		if ($code == 200)
		{
			$obj_XML = simplexml_load_string($this->_obj_Client->getReplyBody() );
			if ($obj_XML->status["code"] >= 100 && $obj_XML->status["card-id"] > 0)
			{
				$id = $obj_XML->status["card-id"];
				$obj_XML = $this->_loginUsingSSOTest($at);
				if ( ($obj_XML instanceof SimpleXMLElement) === true)
				{
					if (count($obj_XML->{'stored-cards'}->card) > 0)
					{
						$aObj_XML = $obj_XML->xpath("/root/stored-cards/card[@id = ". $id ."]");
						$type = str_replace("CARTEBLEUEVISA", "CARTEBLEUE", $type);
						$type = str_replace("POSTEPAY", "", $type);
						if (count($aObj_XML) == 1 && $aObj_XML[0]["type-id"] == $type)
						{
							$this->_sDebug = "Card ID: ". $id;
							return self::sSTATUS_SUCCESS;
						}
						elseif (is_array($aObj_XML) === true && count($aObj_XML) == 1)
						{
							$this->_sDebug = "Wrong Card Type: ". $aObj_XML[0]["type-id"] ." expected: ". $type;
							return self::sSTATUS_FAILED;
						}
						else
						{
							$this->_sDebug = "Card not found";
							return self::sSTATUS_FAILED;
						}
					}
					else
					{
						$this->_sDebug = "No Stored Cards returned";
						return self::sSTATUS_WARNING;
					}
				}
				else
				{
					$this->_sDebug = "Login failed";
					return self::sSTATUS_WARNING;
				}
			}
			elseif ($obj_XML->status["code"] >= 100)
			{
				$this->_sDebug = "Card ID not returned for Saved Card";
				return self::sSTATUS_WARNING;
			}
			else
			{
				$this->_sDebug = $this->_obj_Client->getReplyBody();
				return self::sSTATUS_FAILED;
			}
		}
		elseif ($code == 401 || $code == 403)
		{
			$this->_sDebug = $this->_obj_Client->getReplyBody();
			return self::sSTATUS_WARNING;
		}
		else
		{
			$this->_sDebug = $this->_obj_Client->getReplyBody();
			return self::sSTATUS_FAILED;
		}
	}
	
	public function loginUsingPasswordAddressReturnTest()
	{
		$b = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<login client-id="'. $this->_iClientID .'" account="'. $this->_iAccount .'">';
		$b .= '<password>oisJona1</password>';
		$b .= $this->_constClientInfo();
		$b .= '</login>';
		$b .= '</root>';
	
		$this->_sDebug = "";
		// mPoint
		if ($this->_aConnInfo["port"] == 80 || $this->_aConnInfo["port"] == 443)
		{
			$this->_aConnInfo["path"] = "/mApp/api/login.php";
		}
		// Mobile Enterprise Service Bus
		else { $this->_aConnInfo["path"] = "/mpoint/login"; }
	
		$obj_ConnInfo = HTTPConnInfo::produceConnInfo($this->_aConnInfo);
		$this->_obj_Client = new HTTPClient(new Template, $obj_ConnInfo);
		$this->_obj_Client->connect();
		$code = $this->_obj_Client->send($this->_constmPointHeaders(), $b);
		$this->_obj_Client->disconnect();
		if ($code == 200)
		{
			$obj_XML = simplexml_load_string($this->_obj_Client->getReplyBody() );
			if (count($obj_XML->{'stored-cards'}->card->address) == 1)
			{
				return self::sSTATUS_SUCCESS;
			}
			else
			{
				$this->_sDebug = "No address returned";
				return self::sSTATUS_FAILED;
			}
		}
		elseif ($code == 401 || $code == 403)
		{
			$this->_sDebug = $this->_obj_Client->getReplyBody();
			return self::sSTATUS_WARNING;
		}
		else
		{
			$this->_sDebug = $this->_obj_Client->getReplyBody();
			return self::sSTATUS_FAILED;
		}
	}
	
	private function _loginUsingSSOTest($at)
	{
		$b = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<login client-id="'. $this->_iClientID .'" account="'. $this->_iAccount .'">';
		$b .= '<auth-token>'. htmlspecialchars($at, ENT_NOQUOTES) .'</auth-token>';
		$b .= $this->_constClientInfo();
		$b .= '</login>';
		$b .= '</root>';
		
		$this->_sDebug = "";
		// mPoint
		if ($this->_aConnInfo["port"] == 80 || $this->_aConnInfo["port"] == 443)
		{
			$this->_aConnInfo["path"] = "/mApp/api/login.php";
		}
		// Mobile Enterprise Service Bus
		else { $this->_aConnInfo["path"] = "/mpoint/login"; }
		
		$obj_ConnInfo = HTTPConnInfo::produceConnInfo($this->_aConnInfo);
		$this->_obj_Client = new HTTPClient(new Template, $obj_ConnInfo);
		$this->_obj_Client->connect();
		$code = $this->_obj_Client->send($this->_constmPointHeaders(), $b);
		$this->_obj_Client->disconnect();
		
		if ($code == 200)
		{
			return simplexml_load_string($this->_obj_Client->getReplyBody() );
		}
		elseif ($code == 401 || $code == 403)
		{
			$this->_sDebug = $this->_obj_Client->getReplyBody();
			return self::sSTATUS_WARNING;
		}
		else
		{
			$this->_sDebug = $this->_obj_Client->getReplyBody();
			return self::sSTATUS_FAILED;
		}
	}
	
	public function loginUsingSSOTest($at)
	{
		$obj_XML = $this->_loginUsingSSOTest($at);
		if ( ($obj_XML instanceof SimpleXMLElement) === true)
		{
			if (count($obj_XML->{'stored-cards'}->card->address) == 1)
			{
				return self::sSTATUS_SUCCESS;
			}
			else
			{
				$this->_sDebug = "No address returned";
				return self::sSTATUS_FAILED;
			}
		}
		else { return self::sSTATUS_FAILED; }
	}
	
	public function deleteCardUsingSSOTest($at)
	{
		$obj_XML = $this->_loginUsingSSOTest($at);
		if ( ($obj_XML instanceof SimpleXMLElement) === true)
		{
			if (count($obj_XML->{'stored-cards'}->card) > 0)
			{
				$b = '<?xml version="1.0" encoding="UTF-8"?>';
				$b .= '<root>';
				$b .= '<delete-card client-id="'. $this->_iClientID .'" account="'. $this->_iAccount .'">';
				$b .= '<card>'. $obj_XML->{'stored-cards'}->card[0]["id"] .'</card>';
				$b .= '<auth-token>'. htmlspecialchars($at, ENT_NOQUOTES) .'</auth-token>';
				$b .= $this->_constClientInfo();
				$b .= '</delete-card>';
				$b .= '</root>';
			
				$this->_sDebug = "";
				// mPoint
				if ($this->_aConnInfo["port"] == 80 || $this->_aConnInfo["port"] == 443)
				{
					$this->_aConnInfo["path"] = "/mApp/api/del_card.php";
				}
				// Mobile Enterprise Service Bus
				else { $this->_aConnInfo["path"] = "/mpoint/delete-card"; }
			
				$obj_ConnInfo = HTTPConnInfo::produceConnInfo($this->_aConnInfo);
				$this->_obj_Client = new HTTPClient(new Template, $obj_ConnInfo);
				$this->_obj_Client->connect();
				$code = $this->_obj_Client->send($this->_constmPointHeaders(), $b);
				$this->_obj_Client->disconnect();
				
				if ($code == 200)
				{
					$obj_XML = simplexml_load_string($this->_obj_Client->getReplyBody() );
					if ($obj_XML->status["code"] >= 100) { return self::sSTATUS_SUCCESS; }
					else
					{
						$this->_sDebug = $this->_obj_Client->getReplyBody();
						return self::sSTATUS_FAILED;
					}
				}
				else
				{
					$this->_sDebug = $this->_obj_Client->getReplyBody();
					return self::sSTATUS_WARNING;
				}
			}
			else
			{
				$this->_sDebug = "No Stored Cards returned";
				return self::sSTATUS_WARNING;
			}
		}
		else { return self::sSTATUS_WARNING; }
	}

	public function loginUsingPassword()
	{
		$b = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<login client-id="'. $this->_iClientID .'" account="'. $this->_iAccount .'">';
		$b .= '<password>oisJona1</password>';
		$b .= $this->_constClientInfo();
		$b .= '</login>';
		$b .= '</root>';
	
	
		$this->_sDebug = "";
		// Using login.php because it has a call to getStoredCards()
		// mPoint
		if ($this->_aConnInfo["port"] == 80 || $this->_aConnInfo["port"] == 443)
		{
			$this->_aConnInfo["path"] = "/mApp/api/login.php";
		}
		// Mobile Enterprise Service Bus
		else { $this->_aConnInfo["path"] = "/mpoint/login"; }
	
		$obj_ConnInfo = HTTPConnInfo::produceConnInfo($this->_aConnInfo);
		$this->_obj_Client = new HTTPClient(new Template, $obj_ConnInfo);
		$this->_obj_Client->connect();
		$code = $this->_obj_Client->send($this->_constmPointHeaders(), $b);
		$this->_obj_Client->disconnect();
		if ($code == 200)
		{
			$obj_XML = simplexml_load_string($this->_obj_Client->getReplyBody() );
			$c = $obj_XML->{'stored-cards'};
			if (count($obj_XML->{'stored-cards'}->card) == 1)
			{
				return self::sSTATUS_SUCCESS;
			}
			else if (count($obj_XML->{'stored-cards'}->card) == 0)
			{
				$this->_sDebug = "No cards found";
				return self::sSTATUS_FAILED;
			}
			else
			{
				$this->_sDebug = "Expired and disabled cards must not be retrieved";
				return self::sSTATUS_FAILED;
			}
		}
		elseif ($code == 401 || $code == 403)
		{
			$this->_sDebug = $this->_obj_Client->getReplyBody();
			return self::sSTATUS_WARNING;
		}
		else
		{
			$this->_sDebug = $this->_obj_Client->getReplyBody();
			return self::sSTATUS_FAILED;
		}
	}
	

	public function savePreferenceInCRISSuccessTest($at)
	{
		$b = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<notify>';
		$b .= '<auth-token>'. $at .'</auth-token>';
		$b .= $this->_constClientInfo();
		$b .= '<preference>';
		$b .= '<category-id>9</category-id>';
		$b .= '<category-description>Lifestyle</category-description>';
		$b .= '<preference-type-code>SOCCER</preference-type-code>';
		$b .= '<preference-type-description>Soccer</preference-type-description>';
		$b .= '<parent-card-no></parent-card-no>';
		$b .= '</preference>';
		$b .= '<customer>';
		$b .= '<stored-cards>2</stored-cards>';
		$b .= '</customer>';
		$b .= '</notify>';
		$b .= '</root>';
	
		$this->_aConnInfo["path"] = "/mpoint/emirates/cris/save-preferences";
	
		$obj_ConnInfo = HTTPConnInfo::produceConnInfo($this->_aConnInfo);
		$this->_obj_Client = new HTTPClient(new Template, $obj_ConnInfo);
		$this->_obj_Client->connect();
		$code = $this->_obj_Client->send($this->_constmPointHeaders(), $b);
		$this->_obj_Client->disconnect();
		if ($code == 200)
		{
			$obj_XML = simplexml_load_string($this->_obj_Client->getReplyBody() );
			$c = $obj_XML->{'stored-cards'};
			if ($obj_XML->{'status'}->code === "200")
			{
				return self::sSTATUS_SUCCESS;
			}
			else
			{
				$this->_sDebug = $obj_XML->{'status'};
				return self::sSTATUS_FAILED;
			}
		}
		elseif ($code == 401 || $code == 403)
		{
			$this->_sDebug = $this->_obj_Client->getReplyBody();
			return self::sSTATUS_WARNING;
		}
		else
		{
			$this->_sDebug = $this->_obj_Client->getReplyBody();
			return self::sSTATUS_FAILED;
		}
	}
	
	public function savePreferenceInCRISFailureTest($at)
	{
	
		$b = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<notify>';
		$b .= '<auth-token>'. $at .'</auth-token>';
		$b .= $this->_constClientInfo();
		$b .= '<preference>';
		$b .= '<category-id>9</category-id>';
		$b .= '<category-description>Lifestyle</category-description>';
		$b .= '<preference-type-code>SOCCER</preference-type-code>';
		$b .= '<preference-type-description>Soccer</preference-type-description>';
		$b .= '<parent-card-no></parent-card-no>';
		$b .= '</preference>';
		$b .= '<customer>';
		$b .= '<stored-cards>2</stored-cards>';
		$b .= '</customer>';
		$b .= '</notify>';
		$b .= '</root>';
	
		$this->_aConnInfo["path"] = "/mpoint/emirates/cris/save-preferences";
	
		$obj_ConnInfo = HTTPConnInfo::produceConnInfo($this->_aConnInfo);
		$this->_obj_Client = new HTTPClient(new Template, $obj_ConnInfo);
		$this->_obj_Client->connect();
		$code = $this->_obj_Client->send($this->_constmPointHeaders(), $b);
		$this->_obj_Client->disconnect();
		if ($code == 200)
		{
			$obj_XML = simplexml_load_string($this->_obj_Client->getReplyBody() );
			$c = $obj_XML->{'stored-cards'};
			if ($obj_XML->{'status'}->code === "200")
			{
				return self::sSTATUS_SUCCESS;
			}
			else
			{
				$this->_sDebug = $obj_XML->{'status'};
				return self::sSTATUS_FAILED;
			}
		}
		elseif ($code == 401 || $code == 403)
		{
			$this->_sDebug = $this->_obj_Client->getReplyBody();
			return self::sSTATUS_WARNING;
		}
		else
		{
			$this->_sDebug = $this->_obj_Client->getReplyBody();
			return self::sSTATUS_FAILED;
		}
	}
	/* ========== Automatic Account Management Tests End ========== */
	public function saveMaskedCardWithoutPSPIDTest($at, $type="", $name="" )
	{
		if (empty($type) === true) { $type = $this->_card; }
		$b = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<save-card client-id="'. $this->_iClientID .'" account="'. $this->_iAccount .'">';
		$b .= '<card type-id="'. htmlspecialchars($type, ENT_NOQUOTES) .'" preferred="true">';
		if (empty($name) === false) { $b .= '<name>'. htmlspecialchars($name, ENT_NOQUOTES) .'</name>'; }
		$b .= '<card-number-mask>4444********3333</card-number-mask>';
		$b .= '<expiry-month>07</expiry-month>';
		$b .= '<expiry-year>17</expiry-year>';
		$b .= '<token>123470-ABCD</token>';
		$b .= '<card-holder-name>mohamedgiya ulhak</card-holder-name>';
		$b .= '<address country-id="'. $this->_country .'">';
		$b .= '<first-name>Mohamedgiya</first-name>';
		$b .= '<last-name>Ulhak</last-name>';
		$b .= '<street>Anna nagar</street>';
		$b .= '<postal-code>600408</postal-code>';
		$b .= '<city>Chennai</city>';
		if ($this->_country == "IN") { $b .= '<state>TR</state>'; }
		$b .= '</address>';
		$b .= '</card>';
		$b .= '<password>oisJona1</password>';
		$b .= '<auth-token>'. htmlspecialchars($at, ENT_NOQUOTES) .'</auth-token>';
		$b .= $this->_constClientInfo();
		$b .= '</save-card>';
		$b .= '</root>';
	
		$this->_sDebug = "";
		// mPoint
		if ($this->_aConnInfo["port"] == 80 || $this->_aConnInfo["port"] == 443)
		{
			$this->_aConnInfo["path"] = "/mApp/api/save_card.php";
		}
		// Mobile Enterprise Service Bus
		else { $this->_aConnInfo["path"] = "/mpoint/save-card"; }
	
		$obj_ConnInfo = HTTPConnInfo::produceConnInfo($this->_aConnInfo);
		$this->_obj_Client = new HTTPClient(new Template, $obj_ConnInfo);
		$this->_obj_Client->connect();
		$code = $this->_obj_Client->send($this->_constmPointHeaders(), $b);
		$this->_obj_Client->disconnect();
		
		if ($code == 400)
		{
			$obj_XML = simplexml_load_string($this->_obj_Client->getReplyBody() );
							
			if ($obj_XML->status["code"] == 61 || $obj_XML->status["code"] == 63)
			{
				return self::sSTATUS_SUCCESS;
			}
			else
			{
				$this->_sDebug = $obj_XML->status->asXML();
				return self::sSTATUS_FAILED;
			}
		}
		elseif ($code == 401 || $code == 403)
		{
			$this->_sDebug = $this->_obj_Client->getReplyBody();
			return self::sSTATUS_WARNING;
		}
		else
		{
			$this->_sDebug = $this->_obj_Client->getReplyBody();
			return self::sSTATUS_FAILED;
		}
	}
}

/*
$obj_ConnInfo = HTTPConnInfo::produceConnInfo($obj_XML->{'psp-info'}->url);
$obj_Client = new HTTPClient(new Template, $obj_ConnInfo);

$b = $obj_XML->{'psp-info'}->{'card-number'} ."=". urlencode("5019010000000007") ."&";
$b .= $obj_XML->{'psp-info'}->{'expiry-month'} ."=". urlencode("2") ."&";
$b .= $obj_XML->{'psp-info'}->{'expiry-year'} ."=". urlencode("2014") ."&";
$b .= $obj_XML->{'psp-info'}->cvc ."=". urlencode("210") ."&";

$b = $obj_XML->{'psp-info'}->{'card-number'} ."=". urlencode("1234567890123456") ."&";
$b .= $obj_XML->{'psp-info'}->{'expiry-month'} ."=". urlencode("2") ."&";
$b .= $obj_XML->{'psp-info'}->{'expiry-year'} ."=". urlencode("2014") ."&";
$b .= $obj_XML->{'psp-info'}->cvc ."=". urlencode("210") ."&";

$b = $obj_XML->{'psp-info'}->{'card-number'} ."=&";
$b .= $obj_XML->{'psp-info'}->{'expiry-month'} ."=01&";
$b .= $obj_XML->{'psp-info'}->{'expiry-year'} ."=13&";
$b .= $obj_XML->{'psp-info'}->cvc ."=&";

foreach ($obj_XML->{'psp-info'}->{'hidden-fields'}->children() as $obj_Field)
{
	$b .= str_replace("-DOLLARSIGN-", "$", $obj_Field->getName() ) ."=". urlencode($obj_Field) ."&";
}
$b = substr($b, 0, strlen($b) - 1);
$obj_Client->connect();
$code = -1;
//$h = str_replace("{REFERER}", $obj_XML->{'psp-info'}->url, $h);
if (empty($obj_XML->{'psp-info'}->cookies) === false)
{
	$code = $obj_Client->send($h ."cookie: ". $obj_XML->{'psp-info'}->cookies .HTTPClient::CRLF, $b);
}
else { $code = $obj_Client->send($h, $b); }
$obj_Client->disconnect();
if ($code == 200)
{
	echo $obj_Client->getReplyBody();
}
elseif ($code == 302)
{
	while ($code == 302)
	{
		// Parse HTTP Response Headers
		$a = explode(HTTPClient::CRLF, $obj_Client->getReplyHeader() );
		foreach ($a as $str)
		{
			$pos = strpos($str, ":");
			if ($pos > 0)
			{
				$name = substr($str, 0, $pos);
				if (strtolower($name) == "location")
				{
					$value = trim(substr($str, $pos+1) );
					echo $value ."\r\n"; 
					$obj_ConnInfo = HTTPConnInfo::produceConnInfo(trim($value) );
					$obj_Client = new HTTPClient(new Template, $obj_ConnInfo);
					$obj_Client->connect();
					$code = $obj_Client->send($h);
					$obj_Client->disconnect();
				}
			}
		}
	}
	if ($code == 200)
	{
		echo $obj_Client->getReplyBody();
	}
	else { var_dump($obj_Client); die(); }
}
else { var_dump($obj_Client); die(); }
*/




$iClientID = 10001;
$iAccount = 100010;
$sCustomerRef = "00100119331";
$country = "100";
$iMobile = "28882861";
$sEMail = "jona@oismail.com";
$card = "6";
//00777415236

//00123
$obj_AutoTest = new AutoTest($aHTTP_CONN_INFO["mesb"], $iClientID, $iAccount, $sCustomerRef, $country, $iMobile, $sEMail, $card);

$sAuthToken = $obj_AutoTest->getCRISAuthToken();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<title>mPoint Automatic Tests</title>
	<meta http-equiv="content-type" content="application/xhtml+xml; charset=ISO-8859-1"/>
	<style>
		table#tests tr td
		{
			padding-left: 10px;
		}
		.caption
		{
			font-size: 110%;
			font-weight: bold;
			white-space: nowrap;
			text-align: center;
		}
		.label
		{
			font-weight: bold;
		}
		.success
		{
			font-weight: bold;
			color: green;
		}
		.error
		{
			font-weight: bold;
			color: red;
		}
		.warning
		{
			font-weight: bold;
			color: orange;
		}
		.debug
		{
			white-space: pre;
		}
		.info
		{
			font-style: italic;
		}
		.name
		{
			white-space: nowrap;
		}
	</style>
</head>
<body>
	<table id="tests" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="caption">Test Case</td>
		<td class="caption">Result</td>
		<td class="caption">Debug</td>
	</tr>
	<tr>
		<td class="name">Save Masked American Express with Name</td>
		<td><?= $obj_AutoTest->saveMaskedCardTest($sAuthToken, "AMEX", "My AMEX"); ?></td>
		<td><?= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Save Masked CarteBleue with Name</td>
		<td><?//= $obj_AutoTest->saveMaskedCardTest($sAuthToken, "CARTEBLEUE", "My CarteBleue"); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Save Masked CarteBleue VISA with Name</td>
		<td><?//= $obj_AutoTest->saveMaskedCardTest($sAuthToken, "CARTEBLEUEVISA", "My CarteBleue VISA"); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Save Masked Dankort with Name</td>
		<td><?//= $obj_AutoTest->saveMaskedCardTest($sAuthToken, "VISADANKORT", "My Dankort"); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Save Masked Diners Club with Name</td>
		<td><?//= $obj_AutoTest->saveMaskedCardTest($sAuthToken, "DINERS", "My Diners Club"); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Save Masked JCB with Name</td>
		<td><?//= $obj_AutoTest->saveMaskedCardTest($sAuthToken, "JCB", "My JCB"); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Save Masked Maestro with Name</td>
		<td><?//= $obj_AutoTest->saveMaskedCardTest($sAuthToken, "MAESTRO", "My Maestro"); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Save Masked MasterCard with Name</td>
		<td><?//= $obj_AutoTest->saveMaskedCardTest($sAuthToken, "MAST", "My MasterCard"); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Save Masked Postpay MasterCard with Name</td>
		<td><?//= $obj_AutoTest->saveMaskedCardTest($sAuthToken, "POSTEPAYMAST", "My PostPay MasterCard"); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Save Masked Postpay VISA Card with Name</td>
		<td><?//= $obj_AutoTest->saveMaskedCardTest($sAuthToken, "POSTEPAYVISA", "My PostPay VISA"); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Save Masked SOLO Card with Name</td>
		<td><?//= $obj_AutoTest->saveMaskedCardTest($sAuthToken, "SOLO", "My SOLO"); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Save Masked VISA Card with Name</td>
		<td><?//= $obj_AutoTest->saveMaskedCardTest($sAuthToken, "VISA", "My VISA"); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Save Masked VISA Card without Name</td>
		<td><?//= $obj_AutoTest->saveMaskedCardTest($sAuthToken, "VISA", ""); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Save Masked VISA Electron Card with Name</td>
		<td><?//= $obj_AutoTest->saveMaskedCardTest($sAuthToken, "VISAELEC", "My VISA Electron"); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Initialize Payment with Enhanced Data</td>
		<td><?//= $obj_AutoTest->initializePaymentWithEnhancedDataTest(); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Initialize Payment with Instalments</td>
		<td><?//= $obj_AutoTest->initializePaymentWithInstalmentsTest(); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Pay</td>
		<td><?//= $obj_AutoTest->payTest(); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Authorize Stored Card using Single Sign-On</td>
		<td><?//= $obj_AutoTest->authorizeStoredCardUsingSSOTest($sAuthToken); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Authorize Stored Card with Instalments using Single Sign-On</td>
		<td><?//= $obj_AutoTest->authorizeStoredCardWithInstalmentsUsingSSOTest($sAuthToken); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Authorize Stored Card with Multi-Currency Payment using Single Sign-On</td>
		<td><?= $obj_AutoTest->authorizeStoredCardWithMultiCurrencyUsingSSOTest($sAuthToken); ?></td>
		<td><?= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Save Card Name using Single Sign-On</td>
		<td><?//= $obj_AutoTest->saveCardNameUsingSSOTest($sAuthToken); ?></td>
		<td><?//= $obj_AutoTest->getDebug(); ?></td>
	</tr>
	<tr>
		<td class="name">mConsole Search using Mobile</td>
		<td><?//= $obj_AutoTest->mConsoleSearchUsingMobileTest(); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">mConsole Search using E-Mail</td>
		<td><?//= $obj_AutoTest->mConsoleSearchUsingEMailTest(); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">mConsole Search using Customer Reference</td>
		<td><?//= $obj_AutoTest->mConsoleSearchUsingCustomerRefTest(); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">mConsole Search using Period</td>
		<td><?//= $obj_AutoTest->mConsoleSearchUsingPeriodTest(); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Login using Password</td>
		<td><?//= $obj_AutoTest->loginUsingPassword(); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Login using Password with Billing Address Returned</td>
		<td><?//= $obj_AutoTest->loginUsingPasswordAddressReturnTest(); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Login using Single Sign-On</td>
		<td><?//= $obj_AutoTest->loginUsingSSOTest($sAuthToken); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Delete Card using Single Sign-On</td>
		<td><?= $obj_AutoTest->deleteCardUsingSSOTest($sAuthToken); ?></td>
		<td><?= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Save Preferences in CRIS Success</td>
		<td><?//= $obj_AutoTest->savePreferenceInCRISSuccessTest($sAuthToken); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Save Preferences in CRIS Fail</td>
		<td><?//= $obj_AutoTest->savePreferenceInCRISFailureTest($sAuthToken); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
		<tr>
		<td class="name">Save Masked Card without PSP ID</td>
		<td><?= $obj_AutoTest->saveMaskedCardWithoutPSPIDTest($sAuthToken); ?></td>
		<td><?= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	</table>
</body>
</html>