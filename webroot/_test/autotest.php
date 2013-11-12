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
//$aHTTP_CONN_INFO["mesb"]["host"] = "192.168.1.12"; // Simon
$aHTTP_CONN_INFO["mesb"]["host"] = "10.150.242.42";
//$aHTTP_CONN_INFO["mesb"]["host"] = $_SERVER['HTTP_HOST'];
//$aHTTP_CONN_INFO["mesb"]["port"] = 10080; // Local MESB
//$aHTTP_CONN_INFO["mesb"]["port"] = 80; // mPoint
$aHTTP_CONN_INFO["mesb"]["port"] = 9000; // MESB
$aHTTP_CONN_INFO["mesb"]["timeout"] = 120;
$aHTTP_CONN_INFO["mesb"]["method"] = "POST";
$aHTTP_CONN_INFO["mesb"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["mesb"]["username"] = "IBE";
$aHTTP_CONN_INFO["mesb"]["password"] = "kjsg5Ahf_1";
//$aHTTP_CONN_INFO["mesb"]["username"] = "CPMDemo"; // Local username
//$aHTTP_CONN_INFO["mesb"]["password"] = "DEMOisNO_2"; // Local password

//$h .= "user-agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0" .HTTPClient::CRLF;
//$h .= "accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8" .HTTPClient::CRLF;
//$h .= "accept-language: en-US,en;q=0.5" .HTTPClient::CRLF;

header("Content-Type: text/html; charset=\"utf-8\"");

class AutoTest
{
	const sSTATUS_SUCCESS = '<span class="success">Passed</span>';
	const sSTATUS_WARNING = '<span class="warning">Failed</span>';
	const sSTATUS_FAILED = '<span class="error">Failed</span>';
	
	protected $_aConnInfo = array();
	protected $_obj_Client = null;
	protected $_sDebug;
	
	protected $_iClientID;
	protected $_iAccount;
	protected $_sCustomerRef;
	protected $_lMobile;
	protected $_sEMail;
	
	public function __construct(array &$aCI, $clid, $acc, $cr, $mob, $email)
	{
		$this->_aConnInfo = $aCI; 
		$this->_iClientID = (integer) $clid;;
		$this->_iAccount = $acc;
		$this->_sCustomerRef = trim($cr);
		$this->_lMobile = floatval($mob);
		$this->_sEMail = trim($email);
	}
	
	protected function _constHeaders()
	{
		$h = "{METHOD} {PATH} HTTP/1.0" .HTTPClient::CRLF;
		$h .= "host: {HOST}" .HTTPClient::CRLF;
		$h .= "referer: {REFERER}" .HTTPClient::CRLF;
		$h .= "content-length: {CONTENTLENGTH}" .HTTPClient::CRLF;
		$h .= "content-type: {CONTENTTYPE}" .HTTPClient::CRLF;
		$h .= "user-agent: mPoint" .HTTPClient::CRLF;
		
		return $h;
	}
	protected function _constmPointHeaders()
	{
		$h = trim($this->_constHeaders() );
		$h .= HTTPClient::CRLF;
		$h .= "authorization: Basic ". base64_encode($this->_aConnInfo["username"] .":". $this->_aConnInfo["password"]) . HTTPClient::CRLF;
		
		return $h;
	}
	
	protected function _constClientInfo()
	{
		$xml = '<client-info platform="iOS" version="5.1.1" language="da">';
		$xml .= '<customer-ref>'. htmlspecialchars($this->_sCustomerRef, ENT_NOQUOTES) .'</customer-ref>';
		$xml .= '<mobile country-id="100">'. $this->_lMobile .'</mobile>';
		$xml .= '<email>'. $this->_sEMail .'</email>';
		$xml .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
		$xml .= '</client-info>';
	
		return $xml;
	}
	
	protected function _initialize($cv="")
	{
		$b = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<initialize-payment client-id="'. $this->_iClientID .'" account="'. $this->_iAccount .'">';
		$b .= '<transaction order-no="EST/NGPN4N/07NOV2013/1507-'. time() .'">';
		$b .= '<amount country-id="AE">147500</amount>';
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
			$b .= '<card type-id="VISADANKORT">';
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
			$this->_aConnInfo["path"] = "/mApp/api/search.php";
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
			if (count($obj_XML->{'audit-logs'}->{'audit-log'}) > 0)
			{
				return self::sSTATUS_SUCCESS;
			}
			else
			{
				$this->_sDebug = "No Audit Log Entries Found";
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
	public function saveCardNameTest()
	{
		$b = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<save-card client-id="'. $this->_iClientID .'" account="'. $this->_iAccount .'">';
		$b .= '<card type-id="MAESTRO" preferred="true">';
		$b .= '<name>My VISA</name>';
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
			return self::sSTATUS_FAILED;
		}
	}
	
	public function saveMaskedCardTest()
	{
		$b = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<save-card client-id="'. $this->_iClientID .'" account="'. $this->_iAccount .'">';
		$b .= '<card psp-id="9" type-id="VISA" preferred="true">';
		$b .= '<name>My VISA</name>';
		$b .= '<card-number-mask>4444********3333</card-number-mask>';
		$b .= '<expiry-month>07</expiry-month>';
		$b .= '<expiry-year>17</expiry-year>';
		$b .= '<token>123470-ABCD</token>';
		$b .= '<card-holder-name>mohamedgiya ulhak</card-holder-name>';
		$b .= '<address country-id="AE">';
		$b .= '<first-name>Mohamedgiya</first-name>';
		$b .= '<last-name>Ulhak</last-name>';
		$b .= '<street>Anna nagar</street>';
		$b .= '<postal-code>600408</postal-code>';
		$b .= '<city>Chennai</city>';
		$b .= '<state>N/A</state>';
		$b .= '</address>';
		$b .= '</card>';
		$b .= '<password>oisJona1</password>';
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
			if ($obj_XML->status["code"] >= 100) { return self::sSTATUS_SUCCESS; }
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

	public function savePreferenceInCRIS($at)
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
			if ($obj_XML->status["code"] == '200')
			{
				$this->_sDebug = $obj_XML->{'status'};
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
	
	public function savePreferenceInCRISFail()
	{
		$b = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<notify>';
		$b .= '<auth-token>D7E4B0E705A967076482D4D1BB0716714FF195144BB27A1E425E6F2420457CEA6C1F5E5B1CCD22D3</auth-token>';
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
		if ($code == 403)
		{
			$obj_XML = simplexml_load_string($this->_obj_Client->getReplyBody() );
			if ($obj_XML->status["code"] == '403')
			{
				$this->_sDebug = $obj_XML->{'status'};
				return self::sSTATUS_SUCCESS;
			}
			else
			{
				$this->_sDebug = $obj_XML->{'status'};
				return self::sSTATUS_WARNING;
			}
		}
		else
		{
			$this->_sDebug = $this->_obj_Client->getReplyBody();
			return self::sSTATUS_FAILED;
		}
	}
	
	public function deletePreferenceInCRIS($at)
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
		$b .= '<stored-cards>0</stored-cards>';
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
			if ($obj_XML->status["code"] == '200')
			{
				$this->_sDebug = $obj_XML->{'status'};
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
}

class AutotestPayEx extends AutoTest
{
	
	private function constClientInfo()
	{
		$xml = '<client-info platform="iOS" version="5.1.1" language="da">';
		$xml .= '<customer-ref>'. htmlspecialchars($this->_sCustomerRef, ENT_NOQUOTES) .'</customer-ref>';
		$xml .= '<mobile country-id="100">'. $this->_lMobile .'</mobile>';
		$xml .= '<email>'. $this->_sEMail .'</email>';
		$xml .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
		$xml .= '</client-info>';
	
		return $xml;
	}
	

	private function initialize($cv="")
	{
		$b = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<initialize-payment client-id="'. $this->_iClientID .'" account="'. $this->_iAccount .'">';
		$b .= '<transaction order-no="EST/NGPN4N/07NOV2013/1507-'. time() .'">';
		$b .= '<amount country-id="100">147500</amount>';
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
		$b .= $this->constClientInfo();
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
	
	public function payViaPayExTest()
	{
		$this->_sDebug = "";
		// Call Initialize
		$obj_XML = $this->initialize();
		
		if ( ($obj_XML instanceof SimpleXMLElement) === true)
		{
			file_put_contents(sLOG_PATH ."/error.log", "\n". "obj_XML: ".var_export($obj_XML, true), FILE_APPEND  | LOCK_EX );
			
			$b = '<?xml version="1.0" encoding="UTF-8"?>';
			$b .= '<root>';
			$b .= '<pay client-id="'. $this->_iClientID .'" account="'. $this->_iAccount .'">';
			$b .= '<transaction id="'. $obj_XML->transaction["id"] .'" store-card="false">';
			$b .= '<card type-id="5">';
			$b .= '<amount country-id="'. $obj_XML->transaction->amount["country-id"] .'">'. $obj_XML->transaction->amount .'</amount>';
			$b .= '</card>';
			$b .= '</transaction>';
			$b .= $this->_constClientInfo();
			$b .= '</pay>';
			$b .= '</root>';
				
			if ($this->_aConnInfo["port"] == 80 || $this->_aConnInfo["port"] == 443)
			{
				$this->_aConnInfo["path"] = "/mApp/api/pay.php";
			}
			
			// Call Pay
			$obj_ConnInfo = HTTPConnInfo::produceConnInfo($this->_aConnInfo);
			$this->_obj_Client = new HTTPClient(new Template, $obj_ConnInfo);
			$this->_obj_Client->connect();
			$code = $this->_obj_Client->send($this->_constmPointHeaders(), $b);
			$this->_obj_Client->disconnect();
			
			$obj_XML = simplexml_load_string($this->_obj_Client->getReplyBody() );
			$obj_ConnInfo = HTTPConnInfo::produceConnInfo($obj_XML->{'psp-info'}->url);
			$obj_Client = new HTTPClient(new Template, $obj_ConnInfo);
			$b = $obj_XML->{'psp-info'}->{'card-number'} ."=". urlencode("4581090329655682") ."&";
			$b .= $obj_XML->{'psp-info'}->{'expiry-month'} ."=". urlencode("2") ."&";
			$b .= $obj_XML->{'psp-info'}->{'expiry-year'} ."=". urlencode("2014") ."&";
			$b .= $obj_XML->{'psp-info'}->cvc ."=". urlencode("210") ."&";
			
			file_put_contents(sLOG_PATH ."/error.log", "\n". "B1: ".var_export($b, true), FILE_APPEND  | LOCK_EX );
							
			foreach ($obj_XML->{'psp-info'}->{'hidden-fields'}->children() as $obj_Field)
			{
				$b .= str_replace("-DOLLARSIGN-", "$", $obj_Field->getName() ) ."=". urlencode($obj_Field) ."&";
			}

			$b = substr($b, 0, strlen($b) - 1);
			$obj_Client->connect();
			$code = -1;
			$h = str_replace("{REFERER}", (string) $obj_XML->{'psp-info'}->url, $this->_constmPointHeaders() );;
			if (empty($obj_XML->{'psp-info'}->cookies) === false)
			{
				$s = $h ."cookie: ". $obj_XML->{'psp-info'}->cookies .HTTPClient::CRLF;
				file_put_contents(sLOG_PATH ."/error.log", "\n". "S: ".var_export($s, true), FILE_APPEND  | LOCK_EX );
				file_put_contents(sLOG_PATH ."/error.log", "\n". "B: ".var_export($b, true), FILE_APPEND  | LOCK_EX );
				file_put_contents(sLOG_PATH ."/error.log", "\n". "H: ".var_export($h, true), FILE_APPEND  | LOCK_EX );
				file_put_contents(sLOG_PATH ."/error.log", "\n". "obj Client: ".var_export($obj_Client, true), FILE_APPEND  | LOCK_EX );
				$code = $obj_Client->send($h ."cookie: ". $obj_XML->{'psp-info'}->cookies .HTTPClient::CRLF, $b);
			}
			else { $code = $obj_Client->send($h, $b); }
			$obj_Client->disconnect();
			file_put_contents(sLOG_PATH ."/error.log", "\n". "Code: ".var_export($code, true), FILE_APPEND  | LOCK_EX );
			if ($code == 200)
			{
				$this->_sDebug = $obj_Client->getReplyBody();
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
								$this->_sDebug =  $value ."\r\n";
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
					$this->_sDebug = $obj_Client->getReplyBody();
				}
				else { var_dump($obj_Client); die(); }
			}
			else { var_dump($obj_Client); die(); }
			
			if ($code == 200)
			{
				$this->_sDebug = $this->_obj_Client->getReplyBody();
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
$sCustomerRef = "00777415236";
$iMobile = "28882861";
$sEMail = "jona@oismail.com";

$obj_AutoTest = new AutoTest($aHTTP_CONN_INFO["mesb"], $iClientID, $iAccount, $sCustomerRef, $iMobile, $sEMail);

$obj_AutoTestPayEx = new AutotestPayEx($aHTTP_CONN_INFO["mesb"], 10013, 100032, $sCustomerRef, $iMobile, $sEMail);
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
		<td class="name">Save Masked Card</td>
		<td><?//= $obj_AutoTest->saveMaskedCardTest(); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Save Masked Card 2</td>
		<td><?//= $obj_AutoTest->saveMaskedCardTest2(); ?></td>
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
		<td><?//= $obj_AutoTest->authorizeStoredCardUsingSSOTest($obj_AutoTest->getCRISAuthToken() ); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Authorize Stored Card with Instalments using Single Sign-On</td>
		<td><?//= $obj_AutoTest->authorizeStoredCardWithInstalmentsUsingSSOTest($obj_AutoTest->getCRISAuthToken() ); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Save Card Name</td>
		<td><?//= $obj_AutoTest->saveCardNameTest(); ?></td>
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
		<td><?//= $obj_AutoTest->loginUsingSSOTest($obj_AutoTest->getCRISAuthToken() ); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Login using Single Sign-On 2</td>
		<td><?//= $obj_AutoTest->loginUsingSSOTest2($obj_AutoTest->getCRISAuthToken() ); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Delete Card using Single Sign-On</td>
		<td><?//= $obj_AutoTest->deleteCardUsingSSOTest($obj_AutoTest->getCRISAuthToken() ); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Save Preferences in CRIS</td>
		<td><?//= $obj_AutoTest->savePreferenceInCRIS($obj_AutoTest->getCRISAuthToken() ); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Save Preferences in CRIS Fail</td>
		<td><?//= $obj_AutoTest->savePreferenceInCRISFail(); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Delete Preferences in CRIS</td>
		<td><?//= $obj_AutoTest->deletePreferenceInCRIS($obj_AutoTest->getCRISAuthToken() ); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">PayEx Test</td>
		<td><?= $obj_AutoTestPayEx->payViaPayExTest(); ?></td>
		<td><?= htmlspecialchars($obj_AutoTestPayEx->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	</table>
</body>
</html>