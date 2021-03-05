<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anna Lagad
 * Copyright: Cellpoint Digital
 * Link: http://www.cellpointdigital.com
 * Project: mPoint
 * File Name:ClientPaymentMetadataTest.php
 */

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once sAPI_CLASS_PATH . 'simpledom.php';
require_once __DIR__ . '/../../api/classes/crs/ClientPaymentMetadata.php';
require_once __DIR__ . '/../../api/classes/crs/ClientRouteConfig.php';
require_once __DIR__ . '/../../api/classes/crs/ClientCountryCurrencyConfig.php';
require_once __DIR__ . '/../../api/classes/crs/RouteFeature.php';
require_once __DIR__ . '/../../api/classes/crs/TransactionTypeConfig.php';
require_once __DIR__ . '/../../api/classes/crs/CardState.php';
require_once __DIR__ . '/../../api/classes/crs/FxServiceType.php';

class ClientPaymentMetadataTest extends baseAPITest
{

    private $_OBJ_DB;

    public function setUp() : void
    {
        parent::setUp(true);
        $this->_OBJ_DB = RDB::produceDatabase($this->mPointDBInfo);
    }

    public function testSuccessGetClientPaymentMetadata()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 18, '-1')");
        $this->queryDB("INSERT INTO Client.Route_Tbl (id, clientid, providerid) VALUES (1, 10099, 18)");
        $this->queryDB("INSERT INTO Client.Routeconfig_Tbl (id, routeid, name, capturetype, mid, countryid, currencyid, username, password) VALUES (1, 1, 'Wirecard_VISA', 2, 'TESTMID', null, null, 'username', 'password')");
        $this->queryDB("INSERT INTO Client.Routefeature_Tbl (id, clientid, routeconfigid, featureid) VALUES (1, 10099, 1,2)");
        $this->queryDB("INSERT INTO Client.Routefeature_Tbl (id, clientid, routeconfigid, featureid) VALUES (2, 10099, 1, 5)");

        $obj_Config = ClientPaymentMetadata::produceConfig($this->_OBJ_DB, 10099);
        $this->assertInstanceOf(ClientPaymentMetadata::class, $obj_Config);
        $xml = '';
        if ($obj_Config instanceof ClientPaymentMetadata)
        {
            $xml = $obj_Config->toXML();
        }

        $this->assertStringContainsString('<payment_metadata><payment_methods><payment_method><id>1</id><type_id>1</type_id><name>American Express</name></payment_method><payment_method><id>2</id><type_id>1</type_id><name>Dankort</name></payment_method><payment_method><id>3</id><type_id>1</type_id><name>Diners Club</name></payment_method><payment_method><id>5</id><type_id>1</type_id><name>JCB</name></payment_method><payment_method><id>6</id><type_id>1</type_id><name>Maestro</name></payment_method><payment_method><id>7</id><type_id>1</type_id><name>Master Card</name></payment_method><payment_method><id>8</id><type_id>1</type_id><name>VISA</name></payment_method><payment_method><id>9</id><type_id>1</type_id><name>VISA Electron</name></payment_method><payment_method><id>12</id><type_id>1</type_id><name>Switch</name></payment_method><payment_method><id>13</id><type_id>1</type_id><name>Solo</name></payment_method><payment_method><id>14</id><type_id>1</type_id><name>Delta</name></payment_method><payment_method><id>15</id><type_id>3</type_id><name>Apple Pay</name></payment_method><payment_method><id>16</id><type_id>3</type_id><name>VISA Checkout</name></payment_method><payment_method><id>22</id><type_id>1</type_id><name>Discover</name></payment_method><payment_method><id>23</id><type_id>3</type_id><name>Master Pass</name></payment_method><payment_method><id>25</id><type_id>3</type_id><name>AMEX Express Checkout</name></payment_method><payment_method><id>27</id><type_id>3</type_id><name>Android Pay</name></payment_method><payment_method><id>41</id><type_id>3</type_id><name>Google Pay</name></payment_method></payment_methods><payment_providers><payment_provider><id>18</id><name>Wire Card</name><route_configurations><route_configuration><id>1</id><route_name>Wirecard_VISA</route_name></route_configuration></route_configurations></payment_provider></payment_providers><payment_currencies></payment_currencies><payment_countries></payment_countries><route_features><route_feature><id>2</id><name>Delayed Capture</name></route_feature><route_feature><id>5</id><name>Refund</name></route_feature></route_features><transaction_types><transaction_type><id>0</id><name>System Record</name><enabled>false</enabled></transaction_type><transaction_type><id>1</id><name>Shopping Online</name><enabled>true</enabled></transaction_type><transaction_type><id>2</id><name>Shopping Offline</name><enabled>true</enabled></transaction_type><transaction_type><id>3</id><name>Self Service Online</name><enabled>true</enabled></transaction_type><transaction_type><id>4</id><name>Self Service Offline</name><enabled>true</enabled></transaction_type><transaction_type><id>10</id><name>Call Centre Purchase</name><enabled>true</enabled></transaction_type><transaction_type><id>11</id><name>Call Centre Subscription</name><enabled>true</enabled></transaction_type><transaction_type><id>20</id><name>SMS Purchase</name><enabled>true</enabled></transaction_type><transaction_type><id>21</id><name>SMS Subscription</name><enabled>true</enabled></transaction_type><transaction_type><id>30</id><name>Web Purchase</name><enabled>true</enabled></transaction_type><transaction_type><id>31</id><name>Web Subscription</name><enabled>true</enabled></transaction_type><transaction_type><id>40</id><name>Mobile App. Purchase</name><enabled>true</enabled></transaction_type><transaction_type><id>41</id><name>Mobile App. Subscription</name><enabled>true</enabled></transaction_type><transaction_type><id>100</id><name>Top-Up Purchase</name><enabled>true</enabled></transaction_type><transaction_type><id>101</id><name>Top-Up Subscription</name><enabled>true</enabled></transaction_type><transaction_type><id>102</id><name>Points Top-Up Purchase</name><enabled>true</enabled></transaction_type><transaction_type><id>1000</id><name>E-Money Top-Up</name><enabled>true</enabled></transaction_type><transaction_type><id>1001</id><name>E-Money Purchase</name><enabled>true</enabled></transaction_type><transaction_type><id>1002</id><name>E-Money Transfer</name><enabled>true</enabled></transaction_type><transaction_type><id>1003</id><name>E-Money Withdrawal</name><enabled>true</enabled></transaction_type><transaction_type><id>1004</id><name>Points Top-Up</name><enabled>true</enabled></transaction_type><transaction_type><id>1005</id><name>Points Purchase</name><enabled>true</enabled></transaction_type><transaction_type><id>1007</id><name>Points Reward</name><enabled>true</enabled></transaction_type><transaction_type><id>1009</id><name>Card Purchase</name><enabled>true</enabled></transaction_type><transaction_type><id>10091</id><name>New Card Purchase</name><enabled>true</enabled></transaction_type></transaction_types><card_states><card_state><id>1</id><name>Enabled</name><enabled>true</enabled></card_state><card_state><id>2</id><name>Disabled By Merchant</name><enabled>true</enabled></card_state><card_state><id>3</id><name>Disabled By PSP</name><enabled>true</enabled></card_state><card_state><id>4</id><name>Prerequisite not Met</name><enabled>true</enabled></card_state><card_state><id>5</id><name>Temporarily Unavailable</name><enabled>true</enabled></card_state><card_state><id>6</id><name>Disable Show</name><enabled>true</enabled></card_state></card_states><account_configurations><account_config><id>1100</id><client_id>10099</client_id><name></name><markup></markup><mobile></mobile></account_config></account_configurations><fx_service_types><fx_service_type><id>11</id><name>DCC Opt</name><enabled>true</enabled></fx_service_type><fx_service_type><id>12</id><name>DCC Not opt</name><enabled>true</enabled></fx_service_type><fx_service_type><id>21</id><name>MCP opt</name><enabled>true</enabled></fx_service_type><fx_service_type><id>22</id><name>MCP Not opt</name><enabled>true</enabled></fx_service_type><fx_service_type><id>31</id><name>External MCP opt</name><enabled>true</enabled></fx_service_type><fx_service_type><id>32</id><name>External MCP Not opt</name><enabled>true</enabled></fx_service_type><fx_service_type><id>41</id><name>PCC Opt</name><enabled>true</enabled></fx_service_type><fx_service_type><id>42</id><name>PCC Not opt</name><enabled>true</enabled></fx_service_type></fx_service_types></payment_metadata>', $xml);
    }

    public function testInvalidGetClientPaymentMetadata()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 18, '-1')");

        $obj_Config = ClientPaymentMetadata::produceConfig($this->_OBJ_DB, 10099);
        $this->assertInstanceOf(ClientPaymentMetadata::class, $obj_Config);
        $xml = '';
        if ($obj_Config instanceof ClientPaymentMetadata)
        {
            $xml = $obj_Config->toXML();
        }

        $this->assertStringContainsString('<payment_metadata><payment_methods></payment_methods><payment_providers></payment_providers><payment_currencies></payment_currencies><payment_countries></payment_countries><route_features></route_features><transaction_types><transaction_type><id>0</id><name>System Record</name><enabled>false</enabled></transaction_type><transaction_type><id>1</id><name>Shopping Online</name><enabled>true</enabled></transaction_type><transaction_type><id>2</id><name>Shopping Offline</name><enabled>true</enabled></transaction_type><transaction_type><id>3</id><name>Self Service Online</name><enabled>true</enabled></transaction_type><transaction_type><id>4</id><name>Self Service Offline</name><enabled>true</enabled></transaction_type><transaction_type><id>10</id><name>Call Centre Purchase</name><enabled>true</enabled></transaction_type><transaction_type><id>11</id><name>Call Centre Subscription</name><enabled>true</enabled></transaction_type><transaction_type><id>20</id><name>SMS Purchase</name><enabled>true</enabled></transaction_type><transaction_type><id>21</id><name>SMS Subscription</name><enabled>true</enabled></transaction_type><transaction_type><id>30</id><name>Web Purchase</name><enabled>true</enabled></transaction_type><transaction_type><id>31</id><name>Web Subscription</name><enabled>true</enabled></transaction_type><transaction_type><id>40</id><name>Mobile App. Purchase</name><enabled>true</enabled></transaction_type><transaction_type><id>41</id><name>Mobile App. Subscription</name><enabled>true</enabled></transaction_type><transaction_type><id>100</id><name>Top-Up Purchase</name><enabled>true</enabled></transaction_type><transaction_type><id>101</id><name>Top-Up Subscription</name><enabled>true</enabled></transaction_type><transaction_type><id>102</id><name>Points Top-Up Purchase</name><enabled>true</enabled></transaction_type><transaction_type><id>1000</id><name>E-Money Top-Up</name><enabled>true</enabled></transaction_type><transaction_type><id>1001</id><name>E-Money Purchase</name><enabled>true</enabled></transaction_type><transaction_type><id>1002</id><name>E-Money Transfer</name><enabled>true</enabled></transaction_type><transaction_type><id>1003</id><name>E-Money Withdrawal</name><enabled>true</enabled></transaction_type><transaction_type><id>1004</id><name>Points Top-Up</name><enabled>true</enabled></transaction_type><transaction_type><id>1005</id><name>Points Purchase</name><enabled>true</enabled></transaction_type><transaction_type><id>1007</id><name>Points Reward</name><enabled>true</enabled></transaction_type><transaction_type><id>1009</id><name>Card Purchase</name><enabled>true</enabled></transaction_type><transaction_type><id>10091</id><name>New Card Purchase</name><enabled>true</enabled></transaction_type></transaction_types><card_states><card_state><id>1</id><name>Enabled</name><enabled>true</enabled></card_state><card_state><id>2</id><name>Disabled By Merchant</name><enabled>true</enabled></card_state><card_state><id>3</id><name>Disabled By PSP</name><enabled>true</enabled></card_state><card_state><id>4</id><name>Prerequisite not Met</name><enabled>true</enabled></card_state><card_state><id>5</id><name>Temporarily Unavailable</name><enabled>true</enabled></card_state><card_state><id>6</id><name>Disable Show</name><enabled>true</enabled></card_state></card_states><account_configurations><account_config><id>1100</id><client_id>10099</client_id><name></name><markup></markup><mobile></mobile></account_config></account_configurations><fx_service_types><fx_service_type><id>11</id><name>DCC Opt</name><enabled>true</enabled></fx_service_type><fx_service_type><id>12</id><name>DCC Not opt</name><enabled>true</enabled></fx_service_type><fx_service_type><id>21</id><name>MCP opt</name><enabled>true</enabled></fx_service_type><fx_service_type><id>22</id><name>MCP Not opt</name><enabled>true</enabled></fx_service_type><fx_service_type><id>31</id><name>External MCP opt</name><enabled>true</enabled></fx_service_type><fx_service_type><id>32</id><name>External MCP Not opt</name><enabled>true</enabled></fx_service_type><fx_service_type><id>41</id><name>PCC Opt</name><enabled>true</enabled></fx_service_type><fx_service_type><id>42</id><name>PCC Not opt</name><enabled>true</enabled></fx_service_type></fx_service_types></payment_metadata>', $xml);
    }



    public function tearDown() : void
    {
        $this->_OBJ_DB->disConnect();
        parent::tearDown();
    }


}
