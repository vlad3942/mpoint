<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anna Lagad
 * Copyright: Cellpoint Digital
 * Link: http://www.cellpointdigital.com
 * File Name:ValidateRuleTest.php
 */

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once sAPI_CLASS_PATH . 'simpledom.php';
require_once __DIR__ . '/../../api/classes/crs/ValidateRule.php';

class ValidateRuleTest extends baseAPITest
{

    private $_OBJ_DB;

    public function setUp() : void
    {
        parent::setUp(true);
        $this->_OBJ_DB = RDB::produceDatabase($this->mPointDBInfo);
    }

    public function testSuccessValidateRule()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 18, '-1')");
        $this->queryDB("INSERT INTO Client.Route_Tbl (id, clientid, providerid) VALUES (1, 10099, 18)");
        $this->queryDB("INSERT INTO Client.Routeconfig_Tbl (id, routeid, name, capturetype, mid, username, password) VALUES (1, 1, 'Wirecard_VISA', 2, 'TESTMID', 'username', 'password')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (1)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (1)");

        $aCards = array(7,8);
        $aCountries = array(200,603);
        $aCurrencies = array(840,208);
        $sRoutes = array(1);
        $aMissingRouteConfiguration = array();
        $iConfigCount = (count($aCards) * count($aCurrencies));
        $this->assertEquals($iConfigCount, 4);
        $xml ='';
        foreach ($sRoutes as $route){
            $obj_validateRule = ValidateRule::produceConfig($this->_OBJ_DB, $route, $aCards, $aCountries, $aCurrencies);
            if(empty($obj_validateRule) === false){
                if($obj_validateRule->getRouteConfigCount() < $iConfigCount){
                    $aMissingRouteConfiguration[] = $obj_validateRule->getRouteConfigId();
                }
            }
        }
        $xml .= $obj_validateRule->toXML($aMissingRouteConfiguration);
        $this->assertStringContainsString('<status><code>200</code><description>Success</description></status>', $xml);
    }

    public function testValidateRuleWithNegetiveScanario()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 18, '-1')");
        $this->queryDB("INSERT INTO Client.Route_Tbl (id, clientid, providerid) VALUES (1, 10099, 18)");
        $this->queryDB("INSERT INTO Client.Routeconfig_Tbl (id, routeid, name, capturetype, mid, username, password) VALUES (1, 1, 'Wirecard_VISA', 2, 'TESTMID', 'username', 'password')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (1)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (1)");

        $aCards = array(7,8);
        $aCountries = array(200,603);
        $aCurrencies = array(840,608);
        $sRoutes = array(1);
        $aMissingRouteConfiguration = array();
        $iConfigCount = (count($aCards) * count($aCurrencies));
        $this->assertEquals($iConfigCount, 4);
        $xml ='';
        foreach ($sRoutes as $route){
            $obj_validateRule = ValidateRule::produceConfig($this->_OBJ_DB, $route, $aCards, $aCountries, $aCurrencies);
            if(empty($obj_validateRule) === false){
                if($obj_validateRule->getRouteConfigCount() < $iConfigCount){
                    $aMissingRouteConfiguration[] = $obj_validateRule->getRouteConfigId();
                }
            }
        }
        $xml .= $obj_validateRule->toXML($aMissingRouteConfiguration);
        $this->assertStringContainsString('<status><code>404</code><description>Configuration Not Found For Route ID : 1</description></status>', $xml);
    }

    public function testEmptyCardValidateRule()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 18, '-1')");
        $this->queryDB("INSERT INTO Client.Route_Tbl (id, clientid, providerid) VALUES (1, 10099, 18)");
        $this->queryDB("INSERT INTO Client.Routeconfig_Tbl (id, routeid, name, capturetype, mid, username, password) VALUES (1, 1, 'Wirecard_VISA', 2, 'TESTMID', 'username', 'password')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (1)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (1)");

        $aCards = array();
        $aCountries = array(200,603);
        $aCurrencies = array(840,208);
        $sRoutes = array(1);
        $aMissingRouteConfiguration = array();
        $xml ='';
        foreach ($sRoutes as $route){
            $obj_validateRule = ValidateRule::produceConfig($this->_OBJ_DB, $route, $aCards, $aCountries, $aCurrencies);
            if(empty($obj_validateRule) === false){
                if($obj_validateRule->getRouteConfigCount() === 0){
                    $aMissingRouteConfiguration[] = $obj_validateRule->getRouteConfigId();
                }
            }
        }
        $xml .= $obj_validateRule->toXML($aMissingRouteConfiguration);
        $this->assertStringContainsString('<status><code>200</code><description>Success</description></status>', $xml);
    }


    public function tearDown() : void
    {
        $this->_OBJ_DB->disConnect();
        parent::tearDown();
    }


}
