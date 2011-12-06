<?php
/**
 * The Billing package provides features for charging the customer through alternatives to Credit Card such as Premium SMS and WAP Billing.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package General
 * @subpackage IPX
 * @version 1.00
 */

/**
 * Model Class containing all the Business Logic for handling identifying the end-user using Ericsson IPX's
 * WAP Identification API
 *
 */
class IPX
{
	private $_aCountries = array( 45 => 100,	// Denmark
								  46 => 101,	// Sweden
								  47 => 102,	// Norway
								  44 => 103,	// UK
								 358 => 104,	// Finland
								   1 => 200);	// US
	private $_sUsername;
	private $_sPassword;
	
	private $_obj_SOAP;
	
	public function __construct($un, $pw)
	{
		$this->_sUsername = trim($un);
		$this->_sPassword = trim($pw);
		
		$aParams = array("encoding" => "ISO-8859-1",
						 "exceptions" => true,
						 "trace" => true,
						 "connection_timeout" => 10,
						 "location" => "http://europe.ipx.com/api/services2/IdentificationApi30?wsdl",
						 "uri" => "http://www.ipx.com/api/services/identificationapi30");
		$this->_obj_SOAP = new SOAPClient("http://europe.ipx.com/api/services2/IdentificationApi30?wsdl", $aParams);
	}
	
	public function &start($url)
	{
		$aParams = array("correlationId" => date("YmdHis"),
						 "returnURL" => $url,
						 "contentName" => "mPoint",
						 "language" => "#NULL#",
						 "username" => $this->_sUsername,
						 "password" => $this->_sPassword);
		$this->_obj_SOAP->createSession($aParams);
		$obj_XML = simplexml_load_string($this->_obj_SOAP->__getLastResponse() );
		$obj_XML = $obj_XML->children("http://schemas.xmlsoap.org/soap/envelope/")->Body->children("http://www.ipx.com/api/services/identificationapi30/types")->CreateSessionResponse;
file_put_contents(sLOG_PATH ."/id.log", $this->_obj_SOAP->__getLastResponse(), FILE_APPEND);
		return $obj_XML;
	}
	
	public function &identify($id)
	{
		$aParams = array("correlationId" => date("YmdHis"),
						 "sessionId" => $id,
						 "username" => $this->_sUsername,
						 "password" => $this->_sPassword);
		$this->_obj_SOAP->checkStatus($aParams);
file_put_contents(sLOG_PATH ."/id.log", $this->_obj_SOAP->__getLastRequest() ."\n" ."-----" ."\n", FILE_APPEND);
file_put_contents(sLOG_PATH ."/id.log", $this->_obj_SOAP->__getLastResponse(), FILE_APPEND);
		$aParams = array("correlationId" => date("YmdHis"),
						 "sessionId" => $id,
						 "username" => $this->_sUsername,
						 "password" => $this->_sPassword);
		$this->_obj_SOAP->finalizeSession($aParams);
		$obj_XML = simplexml_load_string($this->_obj_SOAP->__getLastResponse() );
		$obj_XML = $obj_XML->children("http://schemas.xmlsoap.org/soap/envelope/")->Body->children("http://www.ipx.com/api/services/identificationapi30/types")->FinalizeSessionResponse;
file_put_contents(sLOG_PATH ."/id.log", $this->_obj_SOAP->__getLastRequest() ."\n" ."-----" ."\n", FILE_APPEND);
file_put_contents(sLOG_PATH ."/id.log", $this->_obj_SOAP->__getLastResponse(), FILE_APPEND);
		return $obj_XML;
	}
	
	public function getCountryID($msisdn)
	{
		for ($i=3; $i>0; $i--)
		{
			$idc = substr($msisdn, 0, $i);
			if (array_key_exists($idc, $this->_aCountries) === true)
			{
				$id = $this->_aCountries[$idc];
				$i = 0;
			}
		}
file_put_contents(sLOG_PATH ."/id.log", "getCountryID: ". $msisdn ." == ". $idc ." == ". $id ."\n", FILE_APPEND);
		return $id; 
	}
	
	public function getMobile($msisdn)
	{
		for ($i=3; $i>0; $i--)
		{
			$idc = substr($msisdn, 0, $i);
			if (array_key_exists($idc, $this->_aCountries) === true)
			{
				$i = 0;
			}
		}
file_put_contents(sLOG_PATH ."/id.log", "getMobile: ". $msisdn ." == ". $idc ." == ". substr($msisdn, strlen($idc) ) ."\n", FILE_APPEND);
		return substr($msisdn, strlen($idc) ); 
	}
}
?>