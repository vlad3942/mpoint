<?php
define("sAPI_CLASS_PATH", "D:/Development/php5api/classes");
/**
 * Path to Log Files directory
 */
define("sLOG_PATH", "D:/Development/mPoint/log/");

// Require the PHP API for handling the connection to the Mobile Enterprise Service Bus
require_once(sAPI_CLASS_PATH ."/template.php");
// Require the PHP API for handling the connection to Mobile Enterprise Service Bus
require_once(sAPI_CLASS_PATH ."/http_client.php");


/**
 * Connection info for sending error reports to a remote host
 */
$aHTTP_CONN_INFO["mesb"]["protocol"] = "http";
//$aHTTP_CONN_INFO["mesb"]["host"] = "mpoint.test.cellpointmobile.com";
$aHTTP_CONN_INFO["mesb"]["host"] = $_SERVER['HTTP_HOST'];
$aHTTP_CONN_INFO["mesb"]["port"] = 80;
$aHTTP_CONN_INFO["mesb"]["timeout"] = 120;
//$aHTTP_CONN_INFO["mesb"]["path"] = "/mticket/startup";
$aHTTP_CONN_INFO["mesb"]["method"] = "POST";
$aHTTP_CONN_INFO["mesb"]["contenttype"] = "text/xml";
$aHTTP_CONN_INFO["mesb"]["username"] = "CPMDemo";
$aHTTP_CONN_INFO["mesb"]["password"] = "DEMOisNO_2";

//$h .= "user-agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0" .HTTPClient::CRLF;
//$h .= "accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8" .HTTPClient::CRLF;
//$h .= "accept-language: en-US,en;q=0.5" .HTTPClient::CRLF;

header("Content-Type: text/html; charset=\"utf-8\"");

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
	private $_lMobile;
	private $_sEMail;
	
	public function __construct(array &$aCI, $clid, $acc, $cr, $mob, $email)
	{
		$this->_aConnInfo = $aCI; 
		$this->_iClientID = (integer) $clid;;
		$this->_iAccount = $acc;
		$this->_sCustomerRef = trim($cr);
		$this->_lMobile = floatval($mob);
		$this->_sEMail = trim($email);
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
		$xml .= '<mobile country-id="100" operator-id="10000">'. $this->_lMobile .'</mobile>';
		$xml .= '<email>'. $this->_sEMail .'</email>';
		$xml .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
		$xml .= '</client-info>';
	
		return $xml;
	}
	
	private function _initialize()
	{
		$b = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<initialize-payment client-id="'. $this->_iClientID .'" account="'. $$this->_iAccount .'">';
		$b .= '<transaction order-no="1234abc">';
		$b .= '<amount country-id="100">1000</amount>';
		$b .= '<auth-url>http://mesb.localhost:10080/mpoint/emirates/cris/authenticate</auth-url>';
		$b .= '<description>
				<![CDATA[
				<center><table>
				<tr><td bgcolor="#ffff00">Your Internet Order:</td><td colspan="2"
				bgcolor="#ffff00" align="right">IBE/845</td></tr>
				<tr><td bgcolor="#ffff00">Description:</td><td>14 Tulip bulbs</td><td
				align="right">1,00</td></tr>
				<tr><td colspan="2">Subtotal:</td><td align="right">14,00</td></tr>
				<tr><td colspan="2">VAT: 13%</td><td align="right">1,82</td></tr>
				<tr><td colspan="2">Shipping and Handling:</td><td align="right">4,00</td></tr>
				<tr><td colspan="2" bgcolor="#c0c0c0">Total cost:</td><td bgcolor="#c0c0c0" align="right">Euro 19,82</td></tr>
				<tr><td colspan="3">&nbsp;</td></tr>
				<tr><td bgcolor="#ffff00" colspan="3">Your billing address:</td></tr>
				<tr><td colspan="3">Mr. Doe,<br>11 Hereortherestreet,<br>1234 KL
				Somewhereorother,<br>Thisplace.</td></tr>
				<tr><td colspan="3">&nbsp;</td></tr>
				<tr><td bgcolor="#ffff00" colspan="3">Your shipping address:</td></tr>
				<tr><td colspan="3">Mr. Doe,<br>11 Hereortherestreet,<br>1234 KL
				Somewhereorother,<br>Thisplace.</td></tr>
				<tr><td colspan="3">&nbsp;</td></tr>
				<tr><td bgcolor="#ffff00" colspan="3">Our contact information:</td></tr>
				<tr><td colspan="3">Emirates Airlines<br>P.O. Box 686<br>Dubai,<br>UAE<br><br>payment@emirates.com<br>971 4-3167530 </td></tr>
				<tr><td colspan="3">&nbsp;</td></tr>
				<tr><td bgcolor="#c0c0c0" colspan="3">Billing notice:</td></tr>
				<tr><td colspan="3">Your payment will be handled by Bibit Global Payments
				Services<br>This name may appear on your bank
				statement<br>http://www.bibit.com</td></tr>
				</table></center>
				]]>
			   </description>';
		$b .= '<callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url>';
		$b .= '</transaction>';
		$b .= $this->_constClientInfo();
		$b .= '</initialize-payment>';
		$b .= '</root>';
		
		$this->_aConnInfo["path"]= "/mApp/api/initialize.php";
		
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
	/* ========== Automatic Payment Tests Start ========== */
	public function initializePaymentTest()
	{
		$this->_sDebug = "";
		if ( ($this->_initialize() instanceof SimpleXMLElement) === true)
		{
			return self::sSTATUS_SUCCESS;
		}
		else
		{
			$this->_sDebug = $this->_obj_Client->getReplyBody();
			return self::sSTATUS_WARNING;
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
			$b .= '<card type-id="2">';
			$b .= '<amount country-id="'. $obj_XML->transaction->amount["country-id"] .'">'. $obj_XML->transaction->amount .'</amount>';
			$b .= '</card>';
			$b .= '</transaction>';
			$b .= $this->_constClientInfo();
			$b .= '</pay>';
			$b .= '</root>';
			
			$this->_aConnInfo["path"]= "/mApp/api/pay.php";
			
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
	public function authorizePaymentWithStoredCardTest()
	{
		$this->_sDebug = "";
		$obj_XML = $this->_initialize();
		if ( ($obj_XML instanceof SimpleXMLElement) === true)
		{
			$b = '<?xml version="1.0" encoding="UTF-8"?>';
			$b .= '<root>';
			$b .= '<authorize-payment client-id="'. $this->_iClientID .'" account="'. $this->_iAccount .'">';
			$b .= '<transaction id="'. $obj_XML->transaction["id"] .'">';
			$b .= '<card id="'. $obj_XML->{'stored-cards'}->card["id"] .'" type-id="'. $obj_XML->{'stored-cards'}->card["type-id"] .'">';
			$b .= '<amount country-id="'. $obj_XML->transaction->amount["country-id"] .'">'. $obj_XML->transaction->amount .'</amount>';
			$b .= '</card>';
			$b .= '</transaction>';
			$b .= '<password>oisJona</password>';
			$b .= $this->_constClientInfo();
			$b .= '</authorize-payment>';
			$b .= '</root>';
				
			$this->_aConnInfo["path"]= "/mApp/api/authorize.php";
				
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
			return self::sSTATUS_WARNING;
		}
	}
	/* ========== Automatic Payment Tests End ========== */
	
	/* ========== Automatic Account Management Tests Start ========== */
	public function saveCardNameTest()
	{
		$b = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<save-card client-id="'. $this->_iClientID .'" account="'. $this->_iAccount .'">';
		$b .= '<card type-id="6" preferred="true">';
		$b .= '<name>My VISA</name>';
		$b .= '</card>';
		$b .= $this->_constClientInfo();
		$b .= '</save-card>';
		$b .= '</root>';
		
		$this->_sDebug = "";
		$this->_aConnInfo["path"]= "/mApp/api/save_card.php";
		
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
	public function saveMaskedCardTest()
	{
		$b = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<save-card client-id="'. $this->_iClientID .'" account="'. $this->_iAccount .'">';
		$b .= '<card psp-id="4" type-id="6" preferred="true">';
		$b .= '<name>My VISA</name>';
		$b .= '<card-number-mask>540287******1244</card-number-mask>';
		$b .= '<expiry-month>10</expiry-month>';
		$b .= '<expiry-year>14</expiry-year>';
		$b .= '<token>123456-ABCD</token>';
		$b .= '<card-holder-name>Jonatan Evad Buus</card-holder-name>';
		$b .= '<address country-id="100">';
		$b .= '<first-name>Jonatan Evald</first-name>';
		$b .= '<last-name>Buus</last-name>';
		$b .= '<street>Dexter Gordons Vej 3, 6.tv</street>';
		$b .= '<postal-code>2450</postal-code>';
		$b .= '<city>'. utf8_encode("KÝbenhavn SV") .'</city>';
		$b .= '<state>N/A</state>';
		$b .= '</address>';
		$b .= '</card>';
		$b .= $this->_constClientInfo();
		$b .= '</save-card>';
		$b .= '</root>';
		
		$this->_sDebug = "";
		$this->_aConnInfo["path"]= "/mApp/api/save_card.php";
		
		$obj_ConnInfo = HTTPConnInfo::produceConnInfo($this->_aConnInfo);
		$this->_obj_Client = new HTTPClient(new Template, $obj_ConnInfo);
		$this->_obj_Client->connect();
		$code = $this->_obj_Client->send($this->_constmPointHeaders(), $b);
		$this->_obj_Client->disconnect();
		if ($code == 200)
		{
			return self::sSTATUS_SUCCESS;
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

$iClientID = 10012;
$iAccount = 100012;
$sCustomerRef = "ABC-123";
$iMobile = "28882861";
$sEMail = "jona@oismail.com";

$obj_AutoTest = new AutoTest($aHTTP_CONN_INFO["mesb"], $iClientID, $iAccount, $sCustomerRef, $iMobile, $sEMail);
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
			color: yellow;
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
		<td class="name">Initialize Payment</td>
		<td><?//= $obj_AutoTest->initializePaymentTest(); ?></td>
		<td><?//= $obj_AutoTest->getDebug(); ?></td>
	</tr>
	<tr>
		<td class="name">Pay</td>
		<td><?//= $obj_AutoTest->payTest(); ?></td>
		<td><?//= $obj_AutoTest->getDebug(); ?></td>
	</tr>
	<tr>
		<td class="name">Authorize with Stored Card</td>
		<td><?//= $obj_AutoTest->authorizePaymentWithStoredCardTest(); ?></td>
		<td><?//= $obj_AutoTest->getDebug(); ?></td>
	</tr>
	<tr>
		<td class="name">Save Card Name</td>
		<td><?//= $obj_AutoTest->saveCardNameTest(); ?></td>
		<td><?//= $obj_AutoTest->getDebug(); ?></td>
	</tr>
	<tr>
		<td class="name">Save Masked Card</td>
		<td><?= $obj_AutoTest->saveMaskedCardTest(); ?></td>
		<td><?= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	</table>
</body>
</html>