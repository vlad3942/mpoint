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
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");
/**
 * Connection info for sending error reports to a remote host
 */
$aHTTP_CONN_INFO["mesb"]["protocol"] = "http";
//$aHTTP_CONN_INFO["mesb"]["host"] = "10.150.242.42";
$aHTTP_CONN_INFO["mesb"]["host"] = $_SERVER['HTTP_HOST'];
$aHTTP_CONN_INFO["mesb"]["port"] = 80; // mPoint
//$aHTTP_CONN_INFO["mesb"]["port"] = 9000; // MESB
$aHTTP_CONN_INFO["mesb"]["timeout"] = 120;
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
	
	
	public function getClient() { return $this->_obj_Client; }
	public function getDebug() { return $this->_sDebug; }
    
    /* ========== Administration Tests Start ========== */
    
    public function validateClientConfigSaved(RDB &$oDB, SimpleDOMElement $obj_XML, $aInsertedIDs = NULL)
    {
        $aUnvalidatedIDs = $aInsertedIDs;
        $aMsgs = array();
        
        for ($iClient = 0; $iClient < count($obj_XML->{'save-client-configuration'}->{'client-config'}); $iClient++)
        {
            if (empty($obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]['id']) )
            {
                $obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]['id'] = array_pop($aUnvalidatedIDs);
            }
            
            $obj_ClientConfig = ClientConfig::produceConfig($oDB, intval($obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]['id']));
            if ( $obj_ClientConfig->getStoreCard() != $obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]['store-card'] )
            {
                $aMsgs[] = "Client wasn't save properly - store-card mismatch";
            }
            if ($obj_ClientConfig->getName() != $obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]->name)
            {
                $aMsgs[] = "Client wasn't save properly - name mismatch";
            }
            if ($obj_ClientConfig->getUsername() != $obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]->username)
            {
                $aMsgs[] = "Client wasn't save properly - username mismatch";
            }
            if ($obj_ClientConfig->getPassword() != $obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]->password)
            {
                $aMsgs[] = "Client wasn't save properly - password mismatch";
            }

            if ($obj_ClientConfig->getCountryConfig()->getID() != intval($obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]['country-id']))
            {
                $aMsgs[] = "Client wasn't save properly - country-id mismatch";
            }
            if ($obj_ClientConfig->getKeywordConfig()->getName() != $obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]->keyword)
            {
                $aMsgs[] = "Client wasn't save properly - keyword mismatch";
            }

            // Cards section
            $aRS = $oDB->getName("SELECT COUNT(*) AS count FROM Client.CardAccess_Tbl WHERE clientid = ". intval($obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]['id']) );
            if (intval($aRS['COUNT']) != count($obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]->cards->card) )
            {
                $aMsgs[] = "number of Cards mismatch";
            }
            else
            {
                for ($i = 0; $i < count($obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]->cards->card); $i++)
                {
                    $aRS = $oDB->getName('SELECT id FROM Client.CardAccess_Tbl'
                    .' WHERE clientid = '. intval($obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]['id'])
                    .' AND cardid = '. intval($obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]->cards->card[$i]['id'])
                    .' AND pspid = '. intval($obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]->cards->card[$i]['psp-id'])
                    .' AND countryid = '. intval($obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]->cards->card[$i]['country-id']) );

                    if (empty($aRS))
                    {
                        $aMsgs[] = "One or more of the cards mismatch";
                    }
                }
            }

            // URL section
            $aRS = $oDB->getName("SELECT COUNT(*) AS count FROM Client.Url_Tbl WHERE clientid = ". intval($obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]['id']) );
            if (intval($aRS['COUNT']) != count($obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]->urls->url) )
            {
                $aMsgs[] = "number of URLs mismatch";
            }
            else
            {
                for ($i = 0; $i < count($obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]->urls->url); $i++)
                {
                    $aRS = $oDB->getName('SELECT id FROM Client.Url_Tbl'
                    .' WHERE clientid = '. intval($obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]['id'])
                    .' AND urltypeid = '. intval($obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]->urls->url[$i]['type-id'])
                    ." AND url = '". $oDB->escStr($obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]->urls->url[$i]) ."'" );

                    if (empty($aRS))
                    {
                        $aMsgs[] = "One or more of the URLs mismatch";
                    }
                }
            }

            // MerchanAccount (PSP) section
            $aRS = $oDB->getName("SELECT COUNT(*) AS count FROM Client.MerchantAccount_Tbl WHERE clientid = ". intval($obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]['id']) );
            if (intval($aRS['COUNT']) != count($obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]->{'payment-service-providers'}->{'payment-service-provider'}) )
            {
                $aMsgs[] = "number of MerchantAccounts mismatch";
            }
            else
            {
                for ($i = 0; $i < count($obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]->{'payment-service-providers'}->{'payment-service-provider'}); $i++)
                {
                    $aRS = $oDB->getName('SELECT id FROM Client.MerchantAccount_Tbl'
                    .' WHERE clientid = '. intval($obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]['id'])
                    ." AND name = '". $oDB->escStr($obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]->{'payment-service-providers'}->{'payment-service-provider'}[$i]->name) ."'"
                    .' AND pspid = '. intval($obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]->{'payment-service-providers'}->{'payment-service-provider'}[$i]['id'])
                    ." AND username = '". $oDB->escStr($obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]->{'payment-service-providers'}->{'payment-service-provider'}[$i]->username) ."'"
                    ." AND passwd = '". $oDB->escStr($obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]->{'payment-service-providers'}->{'payment-service-provider'}[$i]->password) ."'");

                    if (empty($aRS))
                    {
                        $aMsgs[] = "One or more of the MerchantAccounts mismatch";
                    }
                }
            }

            // Account & MerchantSubAccount section
            $aRS = $oDB->getName("SELECT COUNT(*) AS count FROM Client.Account_Tbl WHERE clientid = ". intval($obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]['id']) );
            if (intval($aRS['COUNT']) != count($obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]->accounts->account))
            {
                $aMsgs[] = "number of Accounts mismatch: db=". intval($aRS['COUNT']) ." VS xml=".  count($obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]->accounts->account);
            }
            else
            {
                for ($i = 0; $i < count($obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]->accounts->account); $i++)
                {
                    $aRS = $oDB->getName('SELECT id, name, markup FROM Client.Account_tbl WHERE clientid = '. intval($obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]['id'])
                    ." AND name = '". $oDB->escStr($obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]->accounts->account[$i]->name) ."'"
                    ." AND markup = '". $oDB->escStr($obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]->accounts->account[$i]->markup) ."'");
                    if (empty($aRS))
                    {
                        $aMsgs[] = "One or more of the Accounts mismatch";
                    }
                    else
                    {
                        $aRS_MSA = $oDB->getName("SELECT COUNT(*) AS count FROM Client.MerchantSubAccount_Tbl WHERE accountid = ". intval($aRS['ID']) );
                        if (intval($aRS_MSA['COUNT']) != count($obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]->accounts->account[$i]->{'payment-service-providers'}->{'payment-service-provider'}))
                        {
                            $aMsgs[] = "number of MerchantSubAccounts mismatch";
                        }
                        else
                        {
                            for ($j = 0; $j < count($obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]->accounts->account[$i]->{'payment-service-providers'}->{'payment-service-provider'}); $j++)
                            {
                                $aRS_MSA = $oDB->getName('SELECT id, accountid, name, pspid FROM Client.MerchantSubAccount_Tbl'
                                        .' WHERE accountid = '. intval($aRS['ID'])
                                        ." AND name = '". $oDB->escStr($obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]->accounts->account[$i]->{'payment-service-providers'}->{'payment-service-provider'}[$j]->name) ."'"
                                        .' AND pspid = '. intval($obj_XML->{'save-client-configuration'}->{'client-config'}[$iClient]->accounts->account[$i]->{'payment-service-providers'}->{'payment-service-provider'}[$j]['id']) );
                                if (empty($aRS_MSA) )
                                {
                                    $aMsgs[] = "One or more of the MerchantSubAccounts mismatch";
                                }
                            }
                        }
                    }
                }
            }
        }
        return $aMsgs;
    }
    
    public function validateCountryConfigSaved(RDB &$oDB, SimpleDOMElement $obj_XML)
    {
        $aValidatedIDs = array();
        $aMsgs = array();
        
        // iterate backwards
        for ($i = count($obj_XML->{'save-country-configuration'}->{'coutries'}->{'country'}) - 1; $i >= 0; $i--)
        {
            if (in_array(intval($obj_XML->{'save-country-configuration'}->{'coutries'}->{'country'}[$i]['id']), $aValidatedIDs) == FALSE)
            {
                $aRS = $oDB->getName("SELECT id FROM System".sSCHEMA_POSTFIX.".Country_Tbl"
                        ." WHERE id = ". intval($obj_XML->{'save-country-configuration'}->{'coutries'}->{'country'}[$i]['id'])
                        ." AND addr_lookup = ". General::xml2bool($obj_XML->{'save-country-configuration'}->{'coutries'}->{'country'}[$i]['address-lookup'])
                        ." AND name = '". $oDB->escStr($obj_XML->{'save-country-configuration'}->{'coutries'}->{'country'}[$i]->name) ."'"
                        ." AND minmob = '". $oDB->escStr($obj_XML->{'save-country-configuration'}->{'coutries'}->{'country'}[$i]->{'min-mobile'}) ."'"
                        ." AND maxmob = '". $oDB->escStr($obj_XML->{'save-country-configuration'}->{'coutries'}->{'country'}[$i]->{'max-mobile'}) ."'"
                        ." AND currency = '". $oDB->escStr($obj_XML->{'save-country-configuration'}->{'coutries'}->{'country'}[$i]->currency) ."'"
                        ." AND symbol = '". $oDB->escStr($obj_XML->{'save-country-configuration'}->{'coutries'}->{'country'}[$i]->symbol) ."'"
                        ." AND priceformat = '". $oDB->escStr($obj_XML->{'save-country-configuration'}->{'coutries'}->{'country'}[$i]->{'price-format'}) ."'");

                if (empty($aRS) )
                {
                    $aMsgs[] = 'Country with id='. intval($obj_XML->{'save-country-configuration'}->{'coutries'}->{'country'}[$i]['id']). "wasn't updated properly";
                }
                else
                {
                    $aValidatedIDs[] = intval($obj_XML->{'save-country-configuration'}->{'coutries'}->{'country'}[$i]['id']);
                }
            }
        }
        
        return $aMsgs;
    }
    
    public function saveCountryConfigTest(RDB &$oDB)
    {
        $iCountryID = 100; // keep 0 to let the test to insert test data into database or set value to use specific id
        
        $this->_sDebug = "";
        if ($iCountryID == 0)
        {
            $sInsertSql = "INSERT INTO System".sSCHEMA_POSTFIX.".Country_Tbl (name, minmob, maxmob, currency, symbol, priceformat, addr_lookup)"
                    . " VALUES ('Autotest Country', '10000000', '99999999', 'CCC', '$$$', '{PRICE} {CURRENCY}', TRUE)";
            if (is_resource($oDB->query($sInsertSql) ) === TRUE )
            {
                $aRS = $oDB->getIndex("SELECT MAX(id) AS id FROM System".sSCHEMA_POSTFIX.".Country_Tbl");
                $iCountryID = intval($aRS["ID"]);
                $bDeleteInsertedData = TRUE;
            }
            else
            {
                $this->_sDebug .= "Can't insert test data, the test will fail. ";
            }
        }
        
		$b = '<?xml version="1.0" encoding="UTF-8"?>';
        $b .= '<root>';
        $b .= '<save-country-configuration>';
        $b .= '<countries>';

        $b .= '<country id="'.$iCountryID.'" address-lookup="true">';
        $b .= '<name>Denmark</name>';
        $b .= '<min-mobile>10000000</min-mobile>';
        $b .= '<max-mobile>99999999</max-mobile>';
        $b .= '<currency>DKK</currency>';
        $b .= '<symbol>kr</symbol>';
        $b .= '<price-format>{PRICE} {CURRENCY}</price-format>';
        $b .= '</country>';

        $b .= '<country id="'.$iCountryID.'" address-lookup="true">';
        $b .= '<name>Denmark</name>';
        $b .= '<min-mobile>10000000</min-mobile>';
        $b .= '<max-mobile>99999999</max-mobile>';
        $b .= '<currency>DKK</currency>';
        $b .= '<symbol>kr</symbol>';
        $b .= '<price-format>{PRICE} {CURRENCY}</price-format>';
        $b .= '</country>';

        $b .= '</countries>';
        $b .= '</save-country-configuration>';
        $b .= '</root>';
        
		// mPoint
		if ($this->_aConnInfo["port"] == 80 || $this->_aConnInfo["port"] == 443)
		{
			$this->_aConnInfo["path"] = "/admin/api/save_country_config.php";
		}
		// Mobile Enterprise Service Bus
		else {
            $this->_aConnInfo["path"] = "/mpoint/save-country-config";
        }
		
		$obj_ConnInfo = HTTPConnInfo::produceConnInfo($this->_aConnInfo);
		$this->_obj_Client = new HTTPClient(new Template, $obj_ConnInfo);
		$this->_obj_Client->connect();
		$code = $this->_obj_Client->send($this->_constmPointHeaders(), $b);		
		$this->_obj_Client->disconnect();
        
		if ($code == 200)
		{
            $this->_sDebug .= $this->_obj_Client->getReplyBody();
            
            // White box testing
            $obj_XML = simpledom_load_string($b);
            $aMsgs = $this->validateCountryConfigSaved($oDB, $obj_XML);
            
            if (empty($aMsgs) == TRUE)
            {
                return self::sSTATUS_SUCCESS;
            }
            else 
            {
                $this->_sDebug .= join(", ", $aMsgs);
                return self::sSTATUS_WARNING;
            }
		}
		elseif ($code == 401 || $code == 403)
		{
			$this->_sDebug .= $this->_obj_Client->getReplyBody();
			return self::sSTATUS_WARNING;
		}
		else
		{
			$this->_sDebug .= $this->_obj_Client->getReplyBody();
			return self::sSTATUS_FAILED;
		}
        
        if ($bDeleteInsertedData)
        {
            if (is_resource($oDB->query("DELETE System".sSCHEMA_POSTFIX.".Country_Tbl WHERE id = ". $iCountryID)) == FALSE)
            {
                $this->_sDebug .= "Can't delete inserted test data ";
            }
        }
    }
    
    public function saveClientConfigInsertTest(RDB &$oDB)
    {
        $b = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .=  '<save-client-configuration>';
        $b .=    '<client-config store-card="3" country-id="100">';
        $b .=    '<name>Emirates - IBE</name>';
        $b .=    '<username>user</username>';
        $b .=    '<password>pass</password>';
        $b .=    '<urls>';
        $b .=     '<url type-id="1">http://mpoint.test.cellpointmobile.com/home/accept.php</url>';
        $b .=    '</urls>';
        $b .=    '<keyword>EK</keyword>';
        $b .=    '<cards>';
        $b .=     '<card id="5" psp-id="7" country-id="100">VISA</card>';
        $b .=     '<card id="6" psp-id="7" country-id="100">VISA</card>';
        $b .=     '<card id="7" psp-id="7" country-id="100">Mastercard</card>';
        $b .=    '</cards>';
        $b .=    '<payment-service-providers>';
        $b .=     '<payment-service-provider id="7">';
        $b .=      '<name>IBE</name>';
        $b .=      '<username>IBE</username>';
        $b .=      '<password>IBE</password>';
        $b .=     '</payment-service-provider>';
        $b .=     '<payment-service-provider id="8">';
        $b .=      '<name>IBE2</name>';
        $b .=      '<username>IBE2</username>';
        $b .=      '<password>IBE2</password>';
        $b .=     '</payment-service-provider>';
        $b .=    '</payment-service-providers>';
        $b .=    '<accounts>';
        $b .=     '<account>';
        $b .=      '<name>Web</name>';
        $b .=      '<markup>App</markup>';
        $b .=      '<payment-service-providers>';
        $b .=       '<payment-service-provider id="7">';
        $b .=        '<name>IBE</name>';
        $b .=       '</payment-service-provider>';
        $b .=      '</payment-service-providers>';
        $b .=     '</account>';
        $b .=     '<account>';
        $b .=      '<name>Web2</name>';
        $b .=      '<markup>App</markup>';
        $b .=      '<payment-service-providers>';
        $b .=       '<payment-service-provider id="7">';
        $b .=        '<name>IBE</name>';
        $b .=       '</payment-service-provider>';
        $b .=       '<payment-service-provider id="8">';
        $b .=        '<name>IBE3</name>';
        $b .=       '</payment-service-provider>';
        $b .=      '</payment-service-providers>';
        $b .=     '</account>';

        $b .=    '</accounts>';
        $b .=   '</client-config>';
        $b .=  '</save-client-configuration>';
		$b .= '</root>';
        
        $this->_sDebug = "";
		// mPoint
		if ($this->_aConnInfo["port"] == 80 || $this->_aConnInfo["port"] == 443)
		{
			$this->_aConnInfo["path"] = "/admin/api/save_client_config.php";
		}
		// Mobile Enterprise Service Bus
		else { $this->_aConnInfo["path"] = "/mpoint/save-client-config"; }
		
		$obj_ConnInfo = HTTPConnInfo::produceConnInfo($this->_aConnInfo);
		$this->_obj_Client = new HTTPClient(new Template, $obj_ConnInfo);
		$this->_obj_Client->connect();
		$code = $this->_obj_Client->send($this->_constmPointHeaders(), $b);
		$this->_obj_Client->disconnect();
        
        // TODO: delete the inserted data?
		if ($code == 200)
		{
			$this->_sDebug = $this->_obj_Client->getReplyBody();
            
            // White box testing
            $obj_XML = simpledom_load_string($b);
            
            // TODO: get the inserted ID from the response
            $aRS = $oDB->getName("SELECT MAX(id) AS id FROM Client.Client_Tbl");
            $aMsgs = $this->validateClientConfigSaved($oDB, $obj_XML, array(intval($aRS['ID'])));
            
            if (empty($aMsgs) == TRUE)
            {
                return self::sSTATUS_SUCCESS;
            }
            else 
            {
                $this->_sDebug .= join(", ", $aMsgs);
                return self::sSTATUS_WARNING;
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
    
    public function saveClientConfigUpdateTest(RDB &$oDB)
    {
        $b = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .=  '<save-client-configuration>';
        $b .=   '<client-config id="10025" store-card="3" country-id="100">';
        $b .=    '<name>Emirates - IBE</name>';
        $b .=    '<username>10000000</username>';
        $b .=    '<password>99999999</password>';
        $b .=    '<urls>';
        $b .=     '<url type-id="1">http://mpoint.test.cellpointmobile.com/home/accept.php</url>';
        $b .=     '<url type-id="2">http://mpoint.test.cellpointmobile.com/_test/auth.php</url>';
        $b .=    '</urls>';
        $b .=    '<keyword>EK</keyword>';
        $b .=    '<cards>';
        $b .=     '<card id="6" psp-id="7" country-id="100">VISA</card>';
        $b .=     '<card id="7" psp-id="7" country-id="100">Mastercard</card>';
        $b .=    '</cards>';
        $b .=    '<payment-service-providers>';
        $b .=     '<payment-service-provider id="7">';
        $b .=      '<name>IBE</name>';
        $b .=      '<username>IBE</username>';
        $b .=      '<password>IBE</password>';
        $b .=     '</payment-service-provider>';
        $b .=    '</payment-service-providers>';
        $b .=    '<accounts>';
        $b .=     '<account>';
        $b .=      '<name>Web</name>';
        $b .=      '<markup>App</markup>';
        $b .=      '<payment-service-providers>';
        $b .=       '<payment-service-provider id="7">';
        $b .=        '<name>IBE</name>';
        $b .=       '</payment-service-provider>';
        $b .=      '</payment-service-providers>';
        $b .=     '</account>';
        $b .=    '</accounts>';
        $b .=   '</client-config>';
        $b .=  '</save-client-configuration>';
		$b .= '</root>';
        
        $this->_sDebug = "";
		// mPoint
		if ($this->_aConnInfo["port"] == 80 || $this->_aConnInfo["port"] == 443)
		{
			$this->_aConnInfo["path"] = "/admin/api/save_client_config.php";
		}
		// Mobile Enterprise Service Bus
		else { $this->_aConnInfo["path"] = "/mpoint/save-client-config"; }
		
		$obj_ConnInfo = HTTPConnInfo::produceConnInfo($this->_aConnInfo);
		$this->_obj_Client = new HTTPClient(new Template, $obj_ConnInfo);
		$this->_obj_Client->connect();
		$code = $this->_obj_Client->send($this->_constmPointHeaders(), $b);		
		$this->_obj_Client->disconnect();
        
		if ($code == 200)
		{
            $this->_sDebug = $this->_obj_Client->getReplyBody();
            
            // White box testing
            $obj_XML = simpledom_load_string($b);
            $aMsgs = $this->validateClientConfigSaved($oDB, $obj_XML);
            
            if (empty($aMsgs) == TRUE)
            {
                return self::sSTATUS_SUCCESS;
            }
            else 
            {
                $this->_sDebug .= join(", ", $aMsgs);
                return self::sSTATUS_WARNING;
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
    
    /* ========== Administration Tests End ========== */
}

$iClientID = 10001;
$iAccount = 100010;
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
		<td class="name">Save Client Config (insert) Test</td>
        <td><?= $obj_AutoTest->saveClientConfigInsertTest($_OBJ_DB); ?></td>
		<td><?= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
    <tr>
		<td class="name">Save Client Config (update) Test</td>
        <td><?= $obj_AutoTest->saveClientConfigUpdateTest($_OBJ_DB); ?></td>
		<td><?= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
    <tr>
		<td class="name">Save Country Config Test</td>
        <td><?= $obj_AutoTest->saveCountryConfigTest($_OBJ_DB); ?></td>
		<td><?= htmlspecialchars($obj_AutoTest->getDebug(), ENT_NOQUOTES); ?></td>
	</tr>
	</table>
    <p>Running against <?php if ($aHTTP_CONN_INFO["mesb"]["port"] == 80 || $aHTTP_CONN_INFO["mesb"]["port"] == 443) { echo 'mPoint'; } else { echo 'MESB'; } ?> (<?= $aHTTP_CONN_INFO["mesb"]["host"] ?>:<?= $aHTTP_CONN_INFO["mesb"]["port"] ?>) </p>
</body>
</html>
