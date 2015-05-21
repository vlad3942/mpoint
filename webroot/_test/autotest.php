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
//$aHTTP_CONN_INFO["mesb"]["host"] = "10.150.242.42";	// EK: Dev
//$aHTTP_CONN_INFO["mesb"]["host"] = "10.50.245.137";	// EK: Pre-Prod 1
//$aHTTP_CONN_INFO["mesb"]["host"] = "10.150.242.41";	// EK: Pre-Prod 2
//$aHTTP_CONN_INFO["mesb"]["host"] = "localhost";			//
$aHTTP_CONN_INFO["mesb"]["host"] = "1415.mesb.test.cellpointmobile.com";
//$aHTTP_CONN_INFO["mesb"]["host"] = "mpoint.localhost";			//
//$aHTTP_CONN_INFO["mesb"]["port"] = 9000; 				// EK MESB
$aHTTP_CONN_INFO["mesb"]["port"] = 10080; 				// MESB
//$aHTTP_CONN_INFO["mesb"]["port"] = 80; 				// mPoint
$aHTTP_CONN_INFO["mesb"]["timeout"] = 120;
$aHTTP_CONN_INFO["mesb"]["method"] = "POST";
$aHTTP_CONN_INFO["mesb"]["contenttype"] = "text/xml";
//$aHTTP_CONN_INFO["mesb"]["username"] = "IBE";
//$aHTTP_CONN_INFO["mesb"]["password"] = "kjsg5Ahf_1";
//$aHTTP_CONN_INFO["mesb"]["username"] = "EasyMARS";
//$aHTTP_CONN_INFO["mesb"]["password"] = 'EZM$PC_UAT';
//$aHTTP_CONN_INFO["mesb"]["username"] = "MobileWeb";
//$aHTTP_CONN_INFO["mesb"]["password"] = "hgUd_36cAd";
//$aHTTP_CONN_INFO["mesb"]["username"] = "iPad";
//$aHTTP_CONN_INFO["mesb"]["password"] = "FgDH_as6ap";
$aHTTP_CONN_INFO["mesb"]["username"] = "1415";
$aHTTP_CONN_INFO["mesb"]["password"] = "Ghdy4_ah1G";

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
		$b .= '<amount country-id="'. $this->_country .'">10000</amount>';
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
				<enchancedData code="EK"><airline code="EK" /><passenger type = "ADT" passengerID="300003524" paxClass="" productName="FLIGHT" productCode="SME Revenue">Alalawi/Iman</passenger><pnr code="NGPN4N"><flight carrierCode="EK0600"><departureAirport>DXB</departureAirport><arrivalAirport>KHI</arrivalAirport><departureDate><depdate day = "15" month="12" year="2013" /></departureDate><arrivalDate><arrdate day = "15" month="12" year="2013" /></arrivalDate><jrnyType>Return</jrnyType><fare class="Y" /></flight><flight carrierCode="EK0605"><departureAirport>KHI</departureAirport><arrivalAirport>DXB</arrivalAirport><departureDate><depdate day = "21" month="12" year="2013" /></departureDate><arrivalDate><arrdate day = "21" month="12" year="2013" /></arrivalDate><jrnyType>Return</jrnyType><fare class="Y" /></flight></pnr><devSessionID>15547305</devSessionID><merchData2>Y</merchData2><bookingType>SME Revenue</bookingType><merchData1></merchData1><merchData3>SME BOOKING</merchData3><merchData4>IMAN ALALAWI</merchData4><merchData5>IMAN ALALAWI</merchData5><merchData6>EK 0600</merchData6><merchData7>15 Dec 13</merchData7><merchData8>NGPN4N</merchData8><thirdParty>false</thirdParty><deptTime>08:00</deptTime><agent code="" /><redemptionTicket></redemptionTicket><ticketOption>ETKT</ticketOption><promotionalCode></promotionalCode><skywardsNumber>300003524</skywardsNumber><productCode>SME Revenue</productCode><bkgChannel>WEB</bkgChannel><tax currencyCode="VND" exponent="0" value="147500"></tax></enchancedData>
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
	public function pay2()
	{
		$b = '<?xml version="1.0" encoding="UTF-8"?>
<root><initialize-payment account="100026" client-id="10019"><transaction order-no="800-48127208" type-id="40"><amount country-id="115">35000</amount><callback-url>http://1415.mretail.cellpointmobile.com/mOrder/sys/mpoint.php</callback-url></transaction><client-info language="da" version="1.20" platform="iOS/7.0.4"><mobile operator-id="11500" country-id="115">1733060076</mobile><email>mmille44@csc.com</email><device-id>B58BB3D2-D52D-4D15-B06A-CC9C1BC99629</device-id></client-info></initialize-payment></root>';
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
			$b = '<?xml version="1.0" encoding="UTF-8"?><root><pay account="100026" client-id="10019"><transaction store-card="true" id="'. $obj_XML->transaction["id"] .'"><card type-id="7"><amount country-id="'. $obj_XML->transaction->amount["country-id"] .'">35000</amount></card></transaction><client-info language="da" version="1.20" platform="iOS/7.0.4"><mobile operator-id="11500" country-id="115">1733060076</mobile><email>mmille44@csc.com</email><device-id>B58BB3D2-D52D-4D15-B06A-CC9C1BC99629</device-id></client-info></pay></root>';
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

			echo $this->_obj_Client->getReplyBody();
		}
		else { $obj_XML = null; }
		$this->_obj_Client->disconnect();
	}
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
	public function authorizeApplePayTest()
	{
		$this->_sDebug = "";
		$obj_XML = $this->_initialize();
		if ( ($obj_XML instanceof SimpleXMLElement) === true)
		{
			$b = '<?xml version="1.0" encoding="UTF-8"?>';
			$b .= '<root>';
			$b .= '<authorize-payment client-id="'. $this->_iClientID .'" account="'. $this->_iAccount .'">';
			$b .= '<transaction id="'. $obj_XML->transaction["id"] .'">';
			$b .= '<card network="Visa" type-id="15">';
			$b .= '<amount country-id="'. $obj_XML->transaction->amount["country-id"] .'">'. $obj_XML->transaction->amount .'</amount>';
//			$b .= '<token>eyJ2ZXJzaW9uIjoiRUNfdjEiLCJkYXRhIjoiMzFoMU8zdUZNUk9ETS9HRmxMeWNSMVRoaml6Y0pIdkZMNlRBaTFXZmVmSlpHUEplWC94ekVIMmx3c3lBODU4M3VFTnR1cmJZbmZZS0EwOXRVT1B5aWROOFJicEsrdzVWK1d1SDFiak5ndFNudHRlclJvU2ROYVFReHViS3NCaEVSUWVwYUdyRkRWWG41b3VjSi9rWDNuK2xMeWNZaWNFN2U5T2xkc3l3T2I0RDRMT2FsaUorczR5U2dSU0JHUWIwV3ZoMDJaaVdESTdHT0EyVG85d3o1OGdjQWpkV3dmem1WYUFCcGRoK21hemlneGhaZ1Qzek1uZUdwTWo5bkluaVY1blBjUVBQejlPaHJMZE9BYmgxWm1mY1p2OTczUWNqNDZqT01EamR0dGhNTVlHNDhuUFRmQXdmNFA2TE9lUlI2UlkyVFpWbVVpb1dUU3NhL1J5NldldlpWdnlYYzFTditldzFnTm1Nam9HQWRuZTdFREpBWFpPOEFZbzg1WTlaTlU5UkpNTTBhT3V2S2dja21nPT0iLCJzaWduYXR1cmUiOiJNSUFHQ1NxR1NJYjNEUUVIQXFDQU1JQUNBUUV4RHpBTkJnbGdoa2dCWlFNRUFnRUZBRENBQmdrcWhraUc5dzBCQndFQUFLQ0FNSUlENGpDQ0E0aWdBd0lCQWdJSUpFUHlxQWFkOVhjd0NnWUlLb1pJemowRUF3SXdlakV1TUN3R0ExVUVBd3dsUVhCd2JHVWdRWEJ3YkdsallYUnBiMjRnU1c1MFpXZHlZWFJwYjI0Z1EwRWdMU0JITXpFbU1DUUdBMVVFQ3d3ZFFYQndiR1VnUTJWeWRHbG1hV05oZEdsdmJpQkJkWFJvYjNKcGRIa3hFekFSQmdOVkJBb01Da0Z3Y0d4bElFbHVZeTR4Q3pBSkJnTlZCQVlUQWxWVE1CNFhEVEUwTURreU5USXlNRFl4TVZvWERURTVNRGt5TkRJeU1EWXhNVm93WHpFbE1DTUdBMVVFQXd3Y1pXTmpMWE50Y0MxaWNtOXJaWEl0YzJsbmJsOVZRelF0VUZKUFJERVVNQklHQTFVRUN3d0xhVTlUSUZONWMzUmxiWE14RXpBUkJnTlZCQW9NQ2tGd2NHeGxJRWx1WXk0eEN6QUpCZ05WQkFZVEFsVlRNRmt3RXdZSEtvWkl6ajBDQVFZSUtvWkl6ajBEQVFjRFFnQUV3aFYzN2V2V3g3SWhqMmpkY0pDaElZM0hzTDF2TENnOWhHQ1YyVXIwcFVFYmcwSU8yQkh6UUg2RE14OGNWTVAzNnpJZzFyclYxTy8wa29tSlBud1BFNk9DQWhFd2dnSU5NRVVHQ0NzR0FRVUZCd0VCQkRrd056QTFCZ2dyQmdFRkJRY3dBWVlwYUhSMGNEb3ZMMjlqYzNBdVlYQndiR1V1WTI5dEwyOWpjM0F3TkMxaGNIQnNaV0ZwWTJFek1ERXdIUVlEVlIwT0JCWUVGSlJYMjIvVmRJR0dpWWwyTDM1WGhRZm5tMWdrTUF3R0ExVWRFd0VCL3dRQ01BQXdId1lEVlIwakJCZ3dGb0FVSS9KSnhFK1Q1TzhuNXNUMktHdy9vcnY5TGtzd2dnRWRCZ05WSFNBRWdnRVVNSUlCRURDQ0FRd0dDU3FHU0liM1kyUUZBVENCL2pDQnd3WUlLd1lCQlFVSEFnSXdnYllNZ2JOU1pXeHBZVzVqWlNCdmJpQjBhR2x6SUdObGNuUnBabWxqWVhSbElHSjVJR0Z1ZVNCd1lYSjBlU0JoYzNOMWJXVnpJR0ZqWTJWd2RHRnVZMlVnYjJZZ2RHaGxJSFJvWlc0Z1lYQndiR2xqWVdKc1pTQnpkR0Z1WkdGeVpDQjBaWEp0Y3lCaGJtUWdZMjl1WkdsMGFXOXVjeUJ2WmlCMWMyVXNJR05sY25ScFptbGpZWFJsSUhCdmJHbGplU0JoYm1RZ1kyVnlkR2xtYVdOaGRHbHZiaUJ3Y21GamRHbGpaU0J6ZEdGMFpXMWxiblJ6TGpBMkJnZ3JCZ0VGQlFjQ0FSWXFhSFIwY0RvdkwzZDNkeTVoY0hCc1pTNWpiMjB2WTJWeWRHbG1hV05oZEdWaGRYUm9iM0pwZEhrdk1EUUdBMVVkSHdRdE1Dc3dLYUFub0NXR0kyaDBkSEE2THk5amNtd3VZWEJ3YkdVdVkyOXRMMkZ3Y0d4bFlXbGpZVE11WTNKc01BNEdBMVVkRHdFQi93UUVBd0lIZ0RBUEJna3Foa2lHOTJOa0JoMEVBZ1VBTUFvR0NDcUdTTTQ5QkFNQ0EwZ0FNRVVDSUhLS253K1NveXE1bVhRcjFWNjJjMEJYS3BhSG9kWXU5VFdYRVBVV1BwYnBBaUVBa1RlY2ZXNitXNWwwcjBBRGZ6VENQcTJZdGJTMzl3MDFYSWF5cUJOeThiRXdnZ0x1TUlJQ2RhQURBZ0VDQWdoSmJTKy9PcGphbHpBS0JnZ3Foa2pPUFFRREFqQm5NUnN3R1FZRFZRUUREQkpCY0hCc1pTQlNiMjkwSUVOQklDMGdSek14SmpBa0JnTlZCQXNNSFVGd2NHeGxJRU5sY25ScFptbGpZWFJwYjI0Z1FYVjBhRzl5YVhSNU1STXdFUVlEVlFRS0RBcEJjSEJzWlNCSmJtTXVNUXN3Q1FZRFZRUUdFd0pWVXpBZUZ3MHhOREExTURZeU16UTJNekJhRncweU9UQTFNRFl5TXpRMk16QmFNSG94TGpBc0JnTlZCQU1NSlVGd2NHeGxJRUZ3Y0d4cFkyRjBhVzl1SUVsdWRHVm5jbUYwYVc5dUlFTkJJQzBnUnpNeEpqQWtCZ05WQkFzTUhVRndjR3hsSUVObGNuUnBabWxqWVhScGIyNGdRWFYwYUc5eWFYUjVNUk13RVFZRFZRUUtEQXBCY0hCc1pTQkpibU11TVFzd0NRWURWUVFHRXdKVlV6QlpNQk1HQnlxR1NNNDlBZ0VHQ0NxR1NNNDlBd0VIQTBJQUJQQVhFWVFaMTJTRjFScGVKWUVIZHVpQW91L2VlNjVONEkzOFM1UGhNMWJWWmxzMXJpTFFsM1lOSWs1N3VnajlkaGZPaU10MnUyWnd2c2pvS1lUL1ZFV2pnZmN3Z2ZRd1JnWUlLd1lCQlFVSEFRRUVPakE0TURZR0NDc0dBUVVGQnpBQmhpcG9kSFJ3T2k4dmIyTnpjQzVoY0hCc1pTNWpiMjB2YjJOemNEQTBMV0Z3Y0d4bGNtOXZkR05oWnpNd0hRWURWUjBPQkJZRUZDUHlTY1JQaytUdkorYkU5aWhzUDZLNy9TNUxNQThHQTFVZEV3RUIvd1FGTUFNQkFmOHdId1lEVlIwakJCZ3dGb0FVdTdEZW9WZ3ppSnFraXBuZXZyM3JyOXJMSktzd053WURWUjBmQkRBd0xqQXNvQ3FnS0lZbWFIUjBjRG92TDJOeWJDNWhjSEJzWlM1amIyMHZZWEJ3YkdWeWIyOTBZMkZuTXk1amNtd3dEZ1lEVlIwUEFRSC9CQVFEQWdFR01CQUdDaXFHU0liM1kyUUdBZzRFQWdVQU1Bb0dDQ3FHU000OUJBTUNBMmNBTUdRQ01EclBjb05SRnBteGh2czF3MWJLWXIvMEYrM1pEM1ZOb282KzhaeUJYa0szaWZpWTk1dFpuNWpWUVEyUG5lbkMvZ0l3TWkzVlJDR3dvd1YzYkYzek9EdVFaLzBYZkN3aGJaWlB4bkpwZ2hKdlZQaDZmUnVaeTVzSmlTRmhCcGtQQ1pJZEFBQXhnZ0ZmTUlJQld3SUJBVENCaGpCNk1TNHdMQVlEVlFRRERDVkJjSEJzWlNCQmNIQnNhV05oZEdsdmJpQkpiblJsWjNKaGRHbHZiaUJEUVNBdElFY3pNU1l3SkFZRFZRUUxEQjFCY0hCc1pTQkRaWEowYVdacFkyRjBhVzl1SUVGMWRHaHZjbWwwZVRFVE1CRUdBMVVFQ2d3S1FYQndiR1VnU1c1akxqRUxNQWtHQTFVRUJoTUNWVk1DQ0NSRDhxZ0duZlYzTUEwR0NXQ0dTQUZsQXdRQ0FRVUFvR2t3R0FZSktvWklodmNOQVFrRE1Rc0dDU3FHU0liM0RRRUhBVEFjQmdrcWhraUc5dzBCQ1FVeER4Y05NVFV3TkRJM01EWXhOakExV2pBdkJna3Foa2lHOXcwQkNRUXhJZ1FnMzJNNmpXM3NJVnU3MnpZckRWK3JTQzYvaXlpbm00QVNtM2VtMXUxaVQ2WXdDZ1lJS29aSXpqMEVBd0lFUnpCRkFpRUEzellXbGZpdW9LTUtOa0Frd1p3Y3VaOWxNeGNWUVNlOGxXdko1Tjc5VnBvQ0lFNWg0OStMR2FJakQvMlcxdllMUE5JMTVERk42Y0lRQW9xdWxWWXN6L1dxQUFBQUFBQUEiLCJoZWFkZXIiOnsiYXBwbGljYXRpb25EYXRhIjoiZTNiMGM0NDI5OGZjMWMxNDlhZmJmNGM4OTk2ZmI5MjQyN2FlNDFlNDY0OWI5MzRjYTQ5NTk5MWI3ODUyYjg1NSIsImVwaGVtZXJhbFB1YmxpY0tleSI6Ik1Ga3dFd1lIS29aSXpqMENBUVlJS29aSXpqMERBUWNEUWdBRVZwREJLaUt3V25oN1lmeDV3VFJXWlVoM2lrbEM3WFJhdmpOS3daV051QnppNXRwZERnUnRYTHZOcTRBYkc3MC9TOGRUWHdyRUIzVThEWHpWTXhsYi9RPT0iLCJ0cmFuc2FjdGlvbklkIjoiZDZkZGY5OWYxMTJkOWM5MmUxOWFhM2RlOTY0ZTA0N2I5N2Q5NTAzZDRmOTVlNzA4ZDRiNmUzMDkwZGIyNGJjNCIsInB1YmxpY0tleUhhc2giOiJiUVZBMEQxUXErSEtqMkRIbUl3am9NZFVSVEJaSXhWR2M1d21Jc1d6TjZFPSJ9fQ==</token>';
			$b .= '<token>eyJ2ZXJzaW9uIjoiRUNfdjEiLCJkYXRhIjoiWGVaSE9LVTlvemVzWmFGSDF5Y3hRMVl6TWtCNlk1WW00NjVRN3VGVDNCS0E5VEtBVUgzYlZRMmNIUHIwcG1PYmNYTWoxSG1Oc29XNmtqQkxOODNkdFJYOTM3eDNGbCtmcjNMNU5XbWZ5cGpXdGN2SUphTG55QWY1dk9ld09mVUFkL096U3VFYUZhczVlMm8zUkNaa1N1cm9JaDZkYWhBZVZ0RUhJVndHZ1JibkZVbjU0WGl2bVBEaUJyekhNbFFvTXdGTTRTdUo1MWVrTjBSVGtJUzdlb3FmNHQ1Q0J5cGFIcTQ1WFQ0eldBd3cvOG1WazhPbXc0ekVIT0h3RURvb3FKcHgrUkc0M1ozS3h0UmFFb25OTUtFYS9yR0JwRkI4WnJtME1WVXRLUTRYZVpwS0V5Q3RWMVRsSnlQdGErNzBvb0ZSbnJuSnBVVzhZVWtVWXpuaERhM0tQUEh0ajhNVXVyYTd6ektHZjRGQ1RkOEl5eXVQWHZVWXQ5TFoxNXJmdmNTdVpDWXdFZVpNRmUxSE9PdEJFV2JHWjlOVmJhK2hZN094UVVlOHZTbDMiLCJzaWduYXR1cmUiOiJNSUFHQ1NxR1NJYjNEUUVIQXFDQU1JQUNBUUV4RHpBTkJnbGdoa2dCWlFNRUFnRUZBRENBQmdrcWhraUc5dzBCQndFQUFLQ0FNSUlENGpDQ0E0aWdBd0lCQWdJSUpFUHlxQWFkOVhjd0NnWUlLb1pJemowRUF3SXdlakV1TUN3R0ExVUVBd3dsUVhCd2JHVWdRWEJ3YkdsallYUnBiMjRnU1c1MFpXZHlZWFJwYjI0Z1EwRWdMU0JITXpFbU1DUUdBMVVFQ3d3ZFFYQndiR1VnUTJWeWRHbG1hV05oZEdsdmJpQkJkWFJvYjNKcGRIa3hFekFSQmdOVkJBb01Da0Z3Y0d4bElFbHVZeTR4Q3pBSkJnTlZCQVlUQWxWVE1CNFhEVEUwTURreU5USXlNRFl4TVZvWERURTVNRGt5TkRJeU1EWXhNVm93WHpFbE1DTUdBMVVFQXd3Y1pXTmpMWE50Y0MxaWNtOXJaWEl0YzJsbmJsOVZRelF0VUZKUFJERVVNQklHQTFVRUN3d0xhVTlUSUZONWMzUmxiWE14RXpBUkJnTlZCQW9NQ2tGd2NHeGxJRWx1WXk0eEN6QUpCZ05WQkFZVEFsVlRNRmt3RXdZSEtvWkl6ajBDQVFZSUtvWkl6ajBEQVFjRFFnQUV3aFYzN2V2V3g3SWhqMmpkY0pDaElZM0hzTDF2TENnOWhHQ1YyVXIwcFVFYmcwSU8yQkh6UUg2RE14OGNWTVAzNnpJZzFyclYxTy8wa29tSlBud1BFNk9DQWhFd2dnSU5NRVVHQ0NzR0FRVUZCd0VCQkRrd056QTFCZ2dyQmdFRkJRY3dBWVlwYUhSMGNEb3ZMMjlqYzNBdVlYQndiR1V1WTI5dEwyOWpjM0F3TkMxaGNIQnNaV0ZwWTJFek1ERXdIUVlEVlIwT0JCWUVGSlJYMjIvVmRJR0dpWWwyTDM1WGhRZm5tMWdrTUF3R0ExVWRFd0VCL3dRQ01BQXdId1lEVlIwakJCZ3dGb0FVSS9KSnhFK1Q1TzhuNXNUMktHdy9vcnY5TGtzd2dnRWRCZ05WSFNBRWdnRVVNSUlCRURDQ0FRd0dDU3FHU0liM1kyUUZBVENCL2pDQnd3WUlLd1lCQlFVSEFnSXdnYllNZ2JOU1pXeHBZVzVqWlNCdmJpQjBhR2x6SUdObGNuUnBabWxqWVhSbElHSjVJR0Z1ZVNCd1lYSjBlU0JoYzNOMWJXVnpJR0ZqWTJWd2RHRnVZMlVnYjJZZ2RHaGxJSFJvWlc0Z1lYQndiR2xqWVdKc1pTQnpkR0Z1WkdGeVpDQjBaWEp0Y3lCaGJtUWdZMjl1WkdsMGFXOXVjeUJ2WmlCMWMyVXNJR05sY25ScFptbGpZWFJsSUhCdmJHbGplU0JoYm1RZ1kyVnlkR2xtYVdOaGRHbHZiaUJ3Y21GamRHbGpaU0J6ZEdGMFpXMWxiblJ6TGpBMkJnZ3JCZ0VGQlFjQ0FSWXFhSFIwY0RvdkwzZDNkeTVoY0hCc1pTNWpiMjB2WTJWeWRHbG1hV05oZEdWaGRYUm9iM0pwZEhrdk1EUUdBMVVkSHdRdE1Dc3dLYUFub0NXR0kyaDBkSEE2THk5amNtd3VZWEJ3YkdVdVkyOXRMMkZ3Y0d4bFlXbGpZVE11WTNKc01BNEdBMVVkRHdFQi93UUVBd0lIZ0RBUEJna3Foa2lHOTJOa0JoMEVBZ1VBTUFvR0NDcUdTTTQ5QkFNQ0EwZ0FNRVVDSUhLS253K1NveXE1bVhRcjFWNjJjMEJYS3BhSG9kWXU5VFdYRVBVV1BwYnBBaUVBa1RlY2ZXNitXNWwwcjBBRGZ6VENQcTJZdGJTMzl3MDFYSWF5cUJOeThiRXdnZ0x1TUlJQ2RhQURBZ0VDQWdoSmJTKy9PcGphbHpBS0JnZ3Foa2pPUFFRREFqQm5NUnN3R1FZRFZRUUREQkpCY0hCc1pTQlNiMjkwSUVOQklDMGdSek14SmpBa0JnTlZCQXNNSFVGd2NHeGxJRU5sY25ScFptbGpZWFJwYjI0Z1FYVjBhRzl5YVhSNU1STXdFUVlEVlFRS0RBcEJjSEJzWlNCSmJtTXVNUXN3Q1FZRFZRUUdFd0pWVXpBZUZ3MHhOREExTURZeU16UTJNekJhRncweU9UQTFNRFl5TXpRMk16QmFNSG94TGpBc0JnTlZCQU1NSlVGd2NHeGxJRUZ3Y0d4cFkyRjBhVzl1SUVsdWRHVm5jbUYwYVc5dUlFTkJJQzBnUnpNeEpqQWtCZ05WQkFzTUhVRndjR3hsSUVObGNuUnBabWxqWVhScGIyNGdRWFYwYUc5eWFYUjVNUk13RVFZRFZRUUtEQXBCY0hCc1pTQkpibU11TVFzd0NRWURWUVFHRXdKVlV6QlpNQk1HQnlxR1NNNDlBZ0VHQ0NxR1NNNDlBd0VIQTBJQUJQQVhFWVFaMTJTRjFScGVKWUVIZHVpQW91L2VlNjVONEkzOFM1UGhNMWJWWmxzMXJpTFFsM1lOSWs1N3VnajlkaGZPaU10MnUyWnd2c2pvS1lUL1ZFV2pnZmN3Z2ZRd1JnWUlLd1lCQlFVSEFRRUVPakE0TURZR0NDc0dBUVVGQnpBQmhpcG9kSFJ3T2k4dmIyTnpjQzVoY0hCc1pTNWpiMjB2YjJOemNEQTBMV0Z3Y0d4bGNtOXZkR05oWnpNd0hRWURWUjBPQkJZRUZDUHlTY1JQaytUdkorYkU5aWhzUDZLNy9TNUxNQThHQTFVZEV3RUIvd1FGTUFNQkFmOHdId1lEVlIwakJCZ3dGb0FVdTdEZW9WZ3ppSnFraXBuZXZyM3JyOXJMSktzd053WURWUjBmQkRBd0xqQXNvQ3FnS0lZbWFIUjBjRG92TDJOeWJDNWhjSEJzWlM1amIyMHZZWEJ3YkdWeWIyOTBZMkZuTXk1amNtd3dEZ1lEVlIwUEFRSC9CQVFEQWdFR01CQUdDaXFHU0liM1kyUUdBZzRFQWdVQU1Bb0dDQ3FHU000OUJBTUNBMmNBTUdRQ01EclBjb05SRnBteGh2czF3MWJLWXIvMEYrM1pEM1ZOb282KzhaeUJYa0szaWZpWTk1dFpuNWpWUVEyUG5lbkMvZ0l3TWkzVlJDR3dvd1YzYkYzek9EdVFaLzBYZkN3aGJaWlB4bkpwZ2hKdlZQaDZmUnVaeTVzSmlTRmhCcGtQQ1pJZEFBQXhnZ0ZmTUlJQld3SUJBVENCaGpCNk1TNHdMQVlEVlFRRERDVkJjSEJzWlNCQmNIQnNhV05oZEdsdmJpQkpiblJsWjNKaGRHbHZiaUJEUVNBdElFY3pNU1l3SkFZRFZRUUxEQjFCY0hCc1pTQkRaWEowYVdacFkyRjBhVzl1SUVGMWRHaHZjbWwwZVRFVE1CRUdBMVVFQ2d3S1FYQndiR1VnU1c1akxqRUxNQWtHQTFVRUJoTUNWVk1DQ0NSRDhxZ0duZlYzTUEwR0NXQ0dTQUZsQXdRQ0FRVUFvR2t3R0FZSktvWklodmNOQVFrRE1Rc0dDU3FHU0liM0RRRUhBVEFjQmdrcWhraUc5dzBCQ1FVeER4Y05NVFV3TlRBMk1URXdOalV3V2pBdkJna3Foa2lHOXcwQkNRUXhJZ1FnNkt4UUN6Vld0Wndkb2dPaVltTmtLaVp6bXFtOXZhYWlmbXV6OVR3QlA0OHdDZ1lJS29aSXpqMEVBd0lFUnpCRkFpQi8zajEzYkErbTJTQ29ZRW9YYnRSVEdrOThtbS9STjN0L0E0bTd3MFBrL1FJaEFQbGpUSkVZak5WZi80TDhHK1RiTEJUSW5tVEpVS2ZwSXV6Qm1LbzlZYk02QUFBQUFBQUEiLCJoZWFkZXIiOnsiYXBwbGljYXRpb25EYXRhIjoiZTNiMGM0NDI5OGZjMWMxNDlhZmJmNGM4OTk2ZmI5MjQyN2FlNDFlNDY0OWI5MzRjYTQ5NTk5MWI3ODUyYjg1NSIsImVwaGVtZXJhbFB1YmxpY0tleSI6Ik1Ga3dFd1lIS29aSXpqMENBUVlJS29aSXpqMERBUWNEUWdBRW9yTHFMYUJJSHZwdDdQWU00WGRCQTJCNVA3UU1SR0xlN1dWYmNNbDlXY0dveWE2M3YrT29iYWJ4cEdoS1hFTUZZY2FVT0NKK1p3M0RTYjBvUzdKNm1RPT0iLCJ0cmFuc2FjdGlvbklkIjoiODMwZmU3MzVmYmJhZDAzNWViNGEwZDBmNDFkOTA5Y2U1ZjQ1OTM3Y2NjMDllM2FlYzdkMWNmN2ExMmNjZTQwZSIsInB1YmxpY0tleUhhc2giOiJiUVZBMEQxUXErSEtqMkRIbUl3am9NZFVSVEJaSXhWR2M1d21Jc1d6TjZFPSJ9fQ==</token>';
			$b .= '</card>';
			$b .= '</transaction>';
			$b .= '<password>oisJona</password>';
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
		else
		{
			$this->_sDebug = $this->_obj_Client->getReplyBody();
			return self::sSTATUS_WARNING;
		}
	
		return $this->_authorize($at, $b);
	}
	public function auth2($at)
	{
		$b = '<?xml version="1.0" encoding="UTF-8"?><submit><shortCode>ESU</shortCode><order orderCode="ESU/MWC7D6/26JAN2014/1222"><description>Emirates Airline Ticket Purchase MWC7D6</description><amount value="277000" currencyCode="VND" exponent="0" debitCreditIndicator="credit" /><tax value="0" currencyCode="VND" exponent="0" /><orderContent>&lt;center&gt;&lt;table&gt;&lt;tr&gt;&lt;td bgcolor="#ffff00"&gt;Your Internet Order:&lt;/td&gt;&lt;td colspan="2" bgcolor="#ffff00" align="right"&gt;ESU/MWC7D6/26JAN2014/1222&lt;/td&gt;&lt;/tr&gt;&lt;tr&gt;&lt;td bgcolor="#ffff00"&gt;Description:&lt;/td&gt;&lt;td&gt;EK Internet Booking Engine&lt;/td&gt;&lt;td align="right"&gt;1.00&lt;/td&gt;&lt;/tr&gt;&lt;tr&gt;&lt;td colspan="2"&gt;Subtotal:&lt;/td&gt;&lt;td align="right"&gt;2770&lt;/td&gt;&lt;/tr&gt;&lt;tr&gt;&lt;td colspan="2" bgcolor="#c0c0c0"&gt;Total cost:&lt;/td&gt;&lt;td bgcolor="#c0c0c0" align="right"&gt;2770&lt;/td&gt;&lt;/tr&gt;&lt;tr&gt;&lt;td colspan="3"&gt;&amp;nbsp;&lt;/td&gt;&lt;/tr&gt;&lt;tr&gt;&lt;td bgcolor="#ffff00" colspan="3"&gt;Your billing address:&lt;/td&gt;&lt;/tr&gt;&lt;tr&gt;&lt;td colspan="3"&gt;&lt;br&gt;Address Object contains :
        address1:dubai
        address2:
        address2:
        city: dubai
        region:
        country: AE
        postalcode:
        contactType: BIL&lt;/td&gt;&lt;/tr&gt;&lt;tr&gt;&lt;td colspan="3"&gt;&amp;nbsp;&lt;/td&gt;&lt;/tr&gt;&lt;tr&gt;&lt;td bgcolor="#ffff00" colspan="3"&gt;Your shipping address:&lt;/td&gt;&lt;/tr&gt;&lt;tr&gt;&lt;td colspan="3"&gt;&lt;br&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr&gt;&lt;td colspan="3"&gt;&amp;nbsp;&lt;/td&gt;&lt;/tr&gt;&lt;tr&gt;&lt;td bgcolor="#ffff00" colspan="3"&gt;Our contact information:&lt;/td&gt;&lt;/tr&gt;&lt;tr&gt;&lt;td colspan="3"&gt;Emirates Airlines,&lt;br&gt;P.O.Box No 686,&lt;br&gt;1255 KZ Dubai,&lt;br&gt;UAE.&lt;br&gt;&lt;br&gt;payment@emirates.com&lt;br&gt;971 4-7035726&lt;/td&gt;&lt;/tr&gt;&lt;/table&gt;&lt;/center&gt;</orderContent><paymentDetails><VISA-SSL><CCRKey>9444400377251111</CCRKey><cvc>***</cvc><cardNumber></cardNumber><storeCardFlag>N</storeCardFlag><expiryDate><date month="03" year="2016" /></expiryDate><cardHolderName>Zing Marley</cardHolderName><paymentCountryCode>AE</paymentCountryCode><cardAddress><address><firstName>Zing</firstName><lastName>Marley</lastName><street>dubai</street><postalCode>0</postalCode><city>dubai</city><state>N/A</state><countryCode>AE</countryCode></address></cardAddress></VISA-SSL></paymentDetails><shopper><shopperIPAddress>10.38.72.181</shopperIPAddress><shopperEmailAddress>S725704@EMIRATES.COM</shopperEmailAddress><authenticatedShopperID>237145930</authenticatedShopperID></shopper><shippingAddress><address><firstName>Zing</firstName><lastName>Marley</lastName><street>dubai</street><postalCode>0</postalCode><city>dubai</city><state>N/A</state><countryCode>AE</countryCode></address></shippingAddress><enchancedData code="EK"><airline code="EK" /><passenger type = "ADT" passengerID= "EK237145930" paxProgramName= "EK" paxClass= "SKYWARDS" productName= "FLIGHT" productCode= "HOLD BOOKING FULFILMENT">MARLEY/ZINGY</passenger><pnr code="MWC7D6"><flight carrierCode="EK071"><departureAirport>DXB</departureAirport><arrivalAirport>CDG</arrivalAirport><departureDate><depdate day = "22" month="2" year="2014" time="03:20" /></departureDate><arrivalDate><arrdate day = "22" month="2" year="2014" time="07:50" /></arrivalDate><jrnyType></jrnyType><fare  bookingClass="B" cabinClass="Y" /></flight></pnr><devSessionID>122214134</devSessionID><bookingType>Standard Revenue</bookingType><merchData1>EK Host</merchData1><merchData2>Y</merchData2><merchData3>BLUE</merchData3><merchData4>Zing Marley</merchData4><merchData5>ZINGY MARLEY</merchData5><merchData6>EK071</merchData6><merchData7>22 Feb 14</merchData7><merchData8>MWC7D6</merchData8><thirdParty>N</thirdParty><merchData21>ETKT</merchData21><merchData22></merchData22><merchData23></merchData23><merchData24></merchData24><merchData25></merchData25><merchData26>Y</merchData26><merchData27></merchData27><merchData28></merchData28><merchData29></merchData29><merchData30></merchData30><merchData31>N</merchData31><merchData32>8589024</merchData32><merchData33></merchData33><merchData34>N</merchData34><merchData35>N</merchData35><merchData36>N</merchData36><merchData37>N</merchData37><merchData38>N</merchData38><merchData39></merchData39><merchData40></merchData40><deptTime>03:20</deptTime><agent code="" /><redemptionTicket>N</redemptionTicket><ticketOption>Standard Revenue</ticketOption><promotionalCode></promotionalCode><skywardsNumber></skywardsNumber><productCode>HOLD BOOKING FULFILMENT</productCode><bkgChannel>WEB MYB</bkgChannel><tax currencyCode="VND" exponent="0" value="0"></tax></enchancedData></order><returnURL></returnURL></submit>';
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

	public function saveMaskedCardTest($at, $type="", $name="", $state="")
	{
		if (empty($type) === true) { $type = $this->_card; }
		switch ($type)
		{
		case (1):
		case "AMEX":
			$cardno = "3456XXXXXX34564";
			$token = "9345600161194564";
			break;
		case (3):
		case "DINERS":
			$cardno = "364073XXXX0569";
			$token = "8364000357540569";
			break;
		case (8):
		case "VISA":
			$cardno = "444433XXXXXX1111";
			$token = "9444400377251111";
			break;
		default:
			$cardno = "4444********3333";
			$token = "123470-ABCD";
			break;
		}

		$b = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<save-card client-id="'. $this->_iClientID .'" account="'. $this->_iAccount .'">';
		$b .= '<card psp-id="9" type-id="'. htmlspecialchars($type, ENT_NOQUOTES) .'" preferred="true">';
		if (empty($name) === false) { $b .= '<name>'. htmlspecialchars($name, ENT_NOQUOTES) .'</name>'; }
		$b .= '<card-number-mask>'. $cardno .'</card-number-mask>';
		$b .= '<expiry-month>05</expiry-month>';
		$b .= '<expiry-year>2017</expiry-year>';
		$b .= '<token>'. $token .'</token>';
		$b .= '<card-holder-name>mohamedgiya ulhak</card-holder-name>';
		$b .= '<address country-id="'. $this->_country .'">';
		$b .= '<first-name>Mohamedgiya</first-name>';
		$b .= '<last-name>Ulhak</last-name>';
		$b .= '<street>Anna nagar</street>';
		$b .= '<postal-code>600408</postal-code>';
		$b .= '<city>Chennai</city>';
		if (empty($state) === false) { $b .= '<state>'. htmlspecialchars(utf8_encode($state), ENT_NOQUOTES) .'</state>'; }
		elseif ($this->_country == "IN") { $b .= '<state>TR</state>'; }
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
	private function _loginUsingPasswordTest($pwd)
	{
		$b = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<login client-id="'. $this->_iClientID .'" account="'. $this->_iAccount .'">';
		$b .= '<password>'. htmlspecialchars($pwd, ENT_NOQUOTES) .'</password>';
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
				// EZM requires card token to be returned
				if ($this->_aConnInfo["username"] == "EasyMARS")
				{
					if (count($obj_XML->{'stored-cards'}->card->token) == 1)
					{
						return self::sSTATUS_SUCCESS;
					}
					else
					{
						$this->_sDebug = "Card Token not returned";
						return self::sSTATUS_FAILED;
					}
				}
				// Card Token may not be returned for other Clients
				elseif (count($obj_XML->{'stored-cards'}->card->token) == 0) { return self::sSTATUS_SUCCESS; }
				else
				{
					$this->_sDebug = "Card Token may only be returned for EZM";
					return self::sSTATUS_FAILED;
				}
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
	public function deleteCardUsingPasswordTest()
	{
		$obj_XML = $this->_loginUsingPasswordTest("oisJona");
		if ( ($obj_XML instanceof SimpleXMLElement) === true)
		{
			if (count($obj_XML->{'stored-cards'}->card) > 0)
			{
				$b = '<?xml version="1.0" encoding="UTF-8"?>';
				$b .= '<root>';
				$b .= '<delete-card client-id="'. $this->_iClientID .'" account="'. $this->_iAccount .'">';
				$b .= '<card>'. $obj_XML->{'stored-cards'}->card[0]["id"] .'</card>';
				$b .= '<password>oisJona</password>';
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
		$obj_XML = $this->_loginUsingPasswordTest("oisJona");
		if ( ($obj_XML instanceof SimpleXMLElement) === true)
		{
			if (count($obj_XML->{'stored-cards'}->card->address) == 1)
			{
				// EZM requires card token to be returned
				if ($this->_aConnInfo["username"] == "EasyMARS")
				{
					if (count($obj_XML->{'stored-cards'}->card->token) == 1)
					{
						return self::sSTATUS_SUCCESS;
					}
					else
					{
						$this->_sDebug = "Card Token not returned";
						return self::sSTATUS_FAILED;
					}
				}
				// Card Token may not be returned for other Clients
				elseif (count($obj_XML->{'stored-cards'}->card->token) == 0) { return self::sSTATUS_SUCCESS; }
				else
				{
					$this->_sDebug = "Card Token may only be returned for EZM";
					return self::sSTATUS_FAILED;
				}
			}
			else
			{
				$this->_sDebug = "No address returned";
				return self::sSTATUS_FAILED;
			}
		}
		else { return self::sSTATUS_FAILED; }
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

$iClientID = 10019;
$iAccount = 100026;
//$sCustomerRef = "100119331";
//$sCustomerRef = "263771465";
//$sCustomerRef = "900005702";
$sCustomerRef = "100119342";
$country = "100";
$iMobile = "28882861";
$sEMail = "jona@oismail.com";
$card = "MAST";
$obj_AutoTest = new AutoTest($aHTTP_CONN_INFO["mesb"], $iClientID, $iAccount, $sCustomerRef, $country, $iMobile, $sEMail, $card);

//$sAuthToken = $obj_AutoTest->getCRISAuthToken();
//$sAuthToken = "801FA7C68510F21F3609CD34C5630393AC213A88B55A6A78AE774E0DA79B0949B72C2F87E594D0EF";
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
		<td><?//= $obj_AutoTest->saveMaskedCardTest($sAuthToken, "AMEX", "My AMEX"); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
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
		<td class="name">Save Masked MasterCard with Name and Unknown State</td>
		<td><?//= $obj_AutoTest->saveMaskedCardTest($sAuthToken, "MASTER", "My MasterCard", "ÆØ"); ?></td>
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
		<td><?//= $obj_AutoTest->authorizeStoredCardWithMultiCurrencyUsingSSOTest($sAuthToken); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Authorize Apple Pay</td>
		<td><?= $obj_AutoTest->authorizeApplePayTest(); ?></td>
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
		<td><?//= $obj_AutoTest->deleteCardUsingSSOTest($sAuthToken); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
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
		<td><?//= $obj_AutoTest->saveMaskedCardWithoutPSPIDTest($sAuthToken); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Auth 2</td>
		<td><?//= $obj_AutoTest->auth2($sAuthToken); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	<tr>
		<td class="name">Delete Card using Password</td>
		<td><?//= $obj_AutoTest->deleteCardUsingPasswordTest(); ?></td>
		<td><?//= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	</table>
</body>
</html>