<?php
/**
 * User: SAGAR BADAVE
 */

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class DCCInitTest extends baseAPITest
{
        protected $_aMPOINT_CONN_INFO;

    public function __construct()
    {
        parent::__construct();
        $this->constHTTPClient();
    }

    public function constHTTPClient()
    {
        global $aMPOINT_CONN_INFO;
        $aMPOINT_CONN_INFO['path'] = "/mApp/api/initialize.php";
        $aMPOINT_CONN_INFO["contenttype"] = "text/xml";
        $this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO) );
    }

    protected function getInitDoc($client, $account, $currecyid = null, $token=null, $amount = 200, $hmac=null, $email=null, $customerref=null, $mobile=null, $profileid=null, $sso_preference=null, $version="2.0",$exchangeinfoid=0)
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml .= '<root>';
		$xml .= '<initialize-payment client-id="'. $client .'" account="'. $account .'">';
		$xml .= '<transaction order-no="1234abc"';
        if($exchangeinfoid > 0)
            $xml .= ' exchangeserviceinfo-id="'.$exchangeinfoid.'"';
        $xml .= '>';
		$xml .= '<amount country-id="100"';
		if(isset($currecyid) === true)
		    $xml .= ' currency-id="'.$currecyid.'"';
		$xml .= '>'.$amount.'</amount>';
		$xml .= '<callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url>';
		if(isset($hmac)=== true) $xml .= '<hmac>'.$hmac.'</hmac>';
		$xml .= '</transaction>';
		if(isset($token) === true)
        {
		    $xml .= '<auth-token>'.$token.'</auth-token>';
        }

        if(isset($sso_preference) === true && ($sso_preference === 'STRICT'))
        {
        	if(isset($profileid) === true) {
				$xml .= '<client-info platform="iOS" version="'.$version.'" language="da" profileid= "'.$profileid.'">';
			} else {
				$xml .= '<client-info platform="iOS" version="'.$version.'" language="da" >';
			}


        	if(isset($mobile) === true) {
				$xml .= '<mobile country-id="100" operator-id="10000">'.$mobile.'</mobile>';
			}
			if(isset($email) === true) {
				$xml .= '<email>'.$email.'</email>';
			}
			if(isset($customerref) === true) {
				$xml .= '<customer-ref>'.$email.'</customer-ref>';
			}
        }
        else {
			$xml .= '<client-info platform="iOS" version="'.$version.'" language="da">';
			$xml .= '<mobile country-id="100" operator-id="10000">288828610</mobile>';
			if(isset($email) === true) {
				$xml .= '<email>'.$email.'</email>';
			} else {
				$xml .= '<email>jona@oismail.com</email>';
			}
        }

		$xml .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
		$xml .= '</client-info>';
		$xml .= '</initialize-payment>';
		$xml .= '</root>';

		return $xml;
	}

    public function testSuccessfulDCCInit()
    {

        $pspID = Constants::iWIRE_CARD_PSP;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd,salt) VALUES (10018, 1, 100, 'Test Client', 'Tuser', 'Tpass','23lkhfgjh24qsdfkjh')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10018, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10018, 'app')");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10018, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10018, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid,dccenabled) VALUES (10018, 8, $pspID,100,true)");
        $this->queryDB("INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid, enabled) VALUES (10018,100,840, true)");

        $xml = $this->getInitDoc(10018, 1100, 840, null, 1000,'ebed76a1736c4a755e0ed8ec38c58a0b7abb409cfb82bdb40bd3e9a63208b5016a5f68a8a01dbee6f2cc2dada268af743a7fc4ecc4208d912fd1915538a58c1a');

        $this->_httpClient->connect();
        $this->bIgnoreErrors = true; //User Warning Expected
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertStringContainsString('dcc="true"',$sReplyBody);
    }

    public function testSuccessfulDCCPresentmentInit()
    {
		$pspID = Constants::iWIRE_CARD_PSP;

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd,salt) VALUES (10018, 1, 100, 'Test Client', 'Tuser', 'Tpass','23lkhfgjh24qsdfkjh')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10018, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10018, 'app')");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10018, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10018, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid,dccenabled) VALUES (10018, 8, $pspID,100,true)");
		$this->queryDB("INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid, enabled) VALUES (10018,100,840, true)");
        $this->queryDB("INSERT INTO client.services_tbl (clientid, pcc_enabled,legacy_flow_enabled) VALUES(10018, true,true);");

		$this->queryDB("INSERT INTO client.pcc_config_tbl (pmId,clientId,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (8,10018,840,156,'true','true')");
		$this->queryDB("INSERT INTO client.pcc_config_tbl (pmId,clientId,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (8,10018,840,360,'true','true')");

		$xml = $this->getInitDoc(10018, 1100, 840, null, 1000, 'ebed76a1736c4a755e0ed8ec38c58a0b7abb409cfb82bdb40bd3e9a63208b5016a5f68a8a01dbee6f2cc2dada268af743a7fc4ecc4208d912fd1915538a58c1a');

		$this->_httpClient->connect();
		$this->bIgnoreErrors = true; // User Warning Expected
		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody ();
		$this->assertStringContainsString('dcc="true"', $sReplyBody );
		$this->assertStringContainsString('presentment-currency="true"', $sReplyBody );
		$this->assertStringContainsString('<settlement-currencies><settlement-currency><id>156</id></settlement-currency><settlement-currency><id>360</id></settlement-currency></settlement-currencies>', $sReplyBody);
	}

    public function testFailureDCCPresentmentInit()
    {

		$pspID = Constants::iWIRE_CARD_PSP;

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd,salt) VALUES (10018, 1, 100, 'Test Client', 'Tuser', 'Tpass','23lkhfgjh24qsdfkjh')" );
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10018, 4, 'http://mpoint.local.cellpointmobile.com:80/')" );
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10018, 'app')" );
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10018, 'CPM', true)" );
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10018, $pspID, '4216310')" );
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')" );
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid,dccenabled) VALUES (10018, 8, $pspID,100,true)" );
		$this->queryDB("INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid, enabled) VALUES (10018,100,840, true)" );

		$xml = $this->getInitDoc(10018, 1100, 840, null, 1000, 'ebed76a1736c4a755e0ed8ec38c58a0b7abb409cfb82bdb40bd3e9a63208b5016a5f68a8a01dbee6f2cc2dada268af743a7fc4ecc4208d912fd1915538a58c1a' );

		$this->_httpClient->connect ();
		$this->bIgnoreErrors = true; // User Warning Expected
		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass' ), $xml );
		$sReplyBody = $this->_httpClient->getReplyBody ();
		$this->assertStringContainsString('dcc="true"', $sReplyBody );
		$this->assertStringContainsString('presentment-currency="false"', $sReplyBody );
	}
}