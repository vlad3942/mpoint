<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once sAPI_CLASS_PATH . 'simpledom.php';
require_once __DIR__ . '/../../api/classes/clientinfo.php';
require_once __DIR__ . '/../../api/classes/core/card.php';
require_once __DIR__ . '/../../api/classes/routing_service.php';
require_once __DIR__ . '/../../api/classes/static_route.php';


class RoutingServiceValidationTest extends baseAPITest
{

    private $_OBJ_DB;
    protected $_aHTTP_CONN_INFO;

    public function setUp()
    {
        parent::setUp(TRUE);
        global $aHTTP_CONN_INFO;
        $this->bIgnoreErrors = true;
        $this->_aHTTP_CONN_INFO = $aHTTP_CONN_INFO;
        $this->_OBJ_DB = RDB::produceDatabase($this->mPointDBInfo);
    }

    protected function getInitDoc($client, $account, $country, $currency = null)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<root>';
        $xml .= '<initialize-payment client-id="'. $client .'" account="'. $account .'">';
        $xml .= '<transaction order-no="1234abc" type-id="30">';
        $xml .= '<amount country-id="'.$country.'" currency-id ="'.$currency.'">200</amount>';
        $xml .= '</transaction>';
        $xml .= $this->getClientInfo();
        $xml .= '</initialize-payment>';
        $xml .= '</root>';

        return $xml;
    }

    protected function getPayDoc($client, $account, $country, $currency = null)
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml .= '<root>';
		$xml .= '<pay client-id="'. $client .'" account="'. $account .'">';
		$xml .= '<transaction id="1001001" store-card="false">';
		$xml .= '<card type-id="7">';
		$xml .= '<amount country-id="'.$country.'" currency-id ="'.$currency.'">200</amount>';
		$xml .= '</card>';
		$xml .= '</transaction>';
		$xml .= $this->getClientInfo();
		$xml .= '</pay>';
		$xml .= '</root>';

		return $xml;
	}

	protected function getClientInfo()
    {
        $xml  = '<client-info platform="iOS" version="1.00" language="da">';
        $xml .= '<mobile country-id="100" operator-id="10000">28882861</mobile>';
        $xml .= '<email>jona@oismail.com</email>';
        $xml .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
        $xml .= '</client-info>';

        return $xml;
    }

    public function testSuccessRoutingServiceGetPaymentMethods()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");

        $xml = $this->getInitDoc(113, 1100, 100, 208);
        $obj_DOM = simpledom_load_string($xml);

        $obj_ClientConfig = ClientConfig::produceConfig($this->_OBJ_DB, $obj_DOM->{'initialize-payment'}["client-id"], $obj_DOM->{'initialize-payment'}["account"]);
        $obj_ClientInfo = ClientInfo::produceInfo($obj_DOM->pay->{'client-info'}, CountryConfig::produceConfig($this->_OBJ_DB, (integer) $obj_DOM->pay->{'client-info'}->mobile["country-id"]), $_SERVER['HTTP_X_FORWARDED_FOR']);
        $obj_RS = new RoutingService($obj_ClientConfig, $obj_ClientInfo, $this->_aHTTP_CONN_INFO['routing-service'], $obj_DOM->{'initialize-payment'}["client-id"], $obj_DOM->{'initialize-payment'}->transaction->amount["country-id"], $obj_DOM->{'initialize-payment'}->transaction->amount["currency-id"], $obj_DOM->{'initialize-payment'}->transaction->amount);
        $aPaymentMethods = $obj_RS->getPaymentMethods();


        $this->assertEquals(2, count($aPaymentMethods->payment_methods->payment_method) );

        $aPSPID = array();
        $aPSPType = array();
        foreach ($aPaymentMethods->payment_methods->payment_method as $paymentMethod)
        {
            $aPSPID[] = $paymentMethod->id;
            $aPSPType[] = $paymentMethod->psp_type;
        }

        $this->assertContains(17, $aPSPID);
        $this->assertContains(18, $aPSPID);
        $this->assertContains(1, $aPSPType);
        $this->assertContains(2, $aPSPType);
    }

    public function testEmptyRoutingServiceGetPaymentMethods()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");

        $xml = $this->getInitDoc(113, 1100);
        $obj_DOM = simpledom_load_string($xml);

        $obj_ClientConfig = ClientConfig::produceConfig($this->_OBJ_DB, $obj_DOM->{'initialize-payment'}["client-id"], $obj_DOM->{'initialize-payment'}["account"]);
        $obj_ClientInfo = ClientInfo::produceInfo($obj_DOM->pay->{'client-info'}, CountryConfig::produceConfig($this->_OBJ_DB, (integer) $obj_DOM->pay->{'client-info'}->mobile["country-id"]), $_SERVER['HTTP_X_FORWARDED_FOR']);
        $obj_RS = new RoutingService($obj_ClientConfig, $obj_ClientInfo, $this->_aHTTP_CONN_INFO['routing-service'], $obj_DOM->{'initialize-payment'}["client-id"], $obj_DOM->{'initialize-payment'}->transaction->amount["country-id"], $obj_DOM->{'initialize-payment'}->transaction->amount["currency-id"], $obj_DOM->{'initialize-payment'}->transaction->amount);
        $aPaymentMethods = $obj_RS->getPaymentMethods();

        $this->assertEquals(0, count($aPaymentMethods->payment_methods->payment_method) );
    }

    public function testSuccessRoutingServiceGetRoutes()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");

        $xml = $this->getPayDoc(113, 1100, 100, 208);
        $obj_DOM = simpledom_load_string($xml);

        $obj_ClientConfig = ClientConfig::produceConfig($this->_OBJ_DB, $obj_DOM->pay["client-id"], $obj_DOM->pay["account"]);
        $obj_ClientInfo = ClientInfo::produceInfo($obj_DOM->pay->{'client-info'}, CountryConfig::produceConfig($this->_OBJ_DB, (integer) $obj_DOM->pay->{'client-info'}->mobile["country-id"]), $_SERVER['HTTP_X_FORWARDED_FOR']);
        $obj_RS = new RoutingService($obj_ClientConfig, $obj_ClientInfo, $this->_aHTTP_CONN_INFO['routing-service'], $obj_DOM->pay["client-id"], $obj_DOM->pay->transaction->card->amount["country-id"], $obj_DOM->pay->transaction->card->amount["currency-id"], $obj_DOM->pay->transaction->card->amount, $obj_DOM->pay->transaction["id"], $obj_DOM->pay->transaction->card["type-id"], 100);
        $aRoutes = $obj_RS->getRoute();

        $this->assertContains('<payment_route_search_response><psps><psp><id>18</id><preference>1</preference></psp><psp><id>17</id><preference>1</preference></psp></psps></payment_route_search_response>', $aRoutes);
    }



    public function testEmptyRoutingServiceGetRoutes()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");

        $xml = $this->getPayDoc(113, 1100);
        $obj_DOM = simpledom_load_string($xml);

        $obj_ClientConfig = ClientConfig::produceConfig($this->_OBJ_DB, $obj_DOM->pay["client-id"], $obj_DOM->pay["account"]);
        $obj_ClientInfo = ClientInfo::produceInfo($obj_DOM->pay->{'client-info'}, CountryConfig::produceConfig($this->_OBJ_DB, (integer) $obj_DOM->pay->{'client-info'}->mobile["country-id"]), $_SERVER['HTTP_X_FORWARDED_FOR']);
        $obj_RS = new RoutingService($obj_ClientConfig, $obj_ClientInfo, $this->_aHTTP_CONN_INFO['routing-service'], $obj_DOM->pay["client-id"], $obj_DOM->pay->transaction->card->amount["country-id"], $obj_DOM->pay->transaction->card->amount["currency-id"], $obj_DOM->pay->transaction->card->amount, $obj_DOM->pay->transaction["id"], $obj_DOM->pay->transaction->card["type-id"], 100);
        $aRoutes = $obj_RS->getRoute();

        $this->assertContains('<?xml version="1.0" encoding="UTF-8"?><root></root>', $aRoutes);
    }

    public function tearDown()
    {
        $this->_OBJ_DB->disConnect();
        parent::tearDown();
    }

}
