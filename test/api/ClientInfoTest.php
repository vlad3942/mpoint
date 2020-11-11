<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anna Lagad
 * Copyright: Cellpoint Digital
 * Link: http://www.cellpointdigital.com
 * Project: mPoint
 * File Name:ClientInfoTest.php
 */

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once sAPI_CLASS_PATH . 'simpledom.php';
require_once __DIR__ . '/../../api/classes/clientinfo.php';

class ClientInfoTest extends baseAPITest
{

    private $_OBJ_DB;
    protected $_aHTTP_CONN_INFO;

    public function setUp() : void
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
        $xml .= '<client-info platform="iOS" version="1.00" language="da" sdk-version="2.0" app-version="2.0" app-id="2">';
        $xml .= '<mobile country-id="100" operator-id="10000">28882861</mobile>';
        $xml .= '<email>jona@oismail.com</email>';
        $xml .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
        $xml .= '</client-info>';
        $xml .= '</initialize-payment>';
        $xml .= '</root>';

        return $xml;
    }

    public function testAttributeLessXML()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 18, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, 1,  18, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");

        $xml = $this->getInitDoc(10099, 1100, 100, 208);
        $obj_DOM = simpledom_load_string($xml);

        $profileTypeId = 3;
        $_SERVER['HTTP_X_FORWARDED_FOR'] = "127.0.0.1, 192.168.2.7";
        $obj_ClientInfo = ClientInfo::produceInfo($obj_DOM->{'initialize-payment'}->{'client-info'}, CountryConfig::produceConfig($this->_OBJ_DB, (integer) $obj_DOM->{'initialize-payment'}->{'client-info'}->mobile["country-id"]), $_SERVER['HTTP_X_FORWARDED_FOR'], $profileTypeId);
        $attributeless_xml = $obj_ClientInfo->toAttributeLessXML();
        $this->assertEquals('<platform>iOS</platform><language>da</language><version>1.00</version><sdk-version>2.0</sdk-version><app-version>2.0</app-version><app_id>2</app_id><mobile><mobile>28882861</mobile><mobile_type>MobileEnriched</mobile_type><country_id>100</country_id><validated>true</validated></mobile><email><email>jona@oismail.com</email><email_type>EmailEnriched</email_type><validated>true</validated></email><device_id>23lkhfgjh24qsdfkjh</device_id><ip>127.0.0.1</ip><customer_type>3</customer_type>', $attributeless_xml);
    }

    public function testAttributeLessXMLWithoutCustomerType()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 18, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, 1,  18, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");

        $xml = $this->getInitDoc(10099, 1100, 100, 208);
        $obj_DOM = simpledom_load_string($xml);

         $_SERVER['HTTP_X_FORWARDED_FOR'] = "127.0.0.1, 192.168.2.7";
        $obj_ClientInfo = ClientInfo::produceInfo($obj_DOM->{'initialize-payment'}->{'client-info'}, CountryConfig::produceConfig($this->_OBJ_DB, (integer) $obj_DOM->{'initialize-payment'}->{'client-info'}->mobile["country-id"]), $_SERVER['HTTP_X_FORWARDED_FOR']);
        $attributeless_xml = $obj_ClientInfo->toAttributeLessXML();
        $this->assertEquals('<platform>iOS</platform><language>da</language><version>1.00</version><sdk-version>2.0</sdk-version><app-version>2.0</app-version><app_id>2</app_id><mobile><mobile>28882861</mobile><mobile_type>MobileEnriched</mobile_type><country_id>100</country_id><validated>true</validated></mobile><email><email>jona@oismail.com</email><email_type>EmailEnriched</email_type><validated>true</validated></email><device_id>23lkhfgjh24qsdfkjh</device_id><ip>127.0.0.1</ip>', $attributeless_xml);
    }

    public function tearDown() : void
    {
        $this->_OBJ_DB->disConnect();
        parent::tearDown();
    }

}
