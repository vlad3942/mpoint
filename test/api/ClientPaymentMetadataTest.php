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
        $this->queryDB("INSERT INTO Client.Routeconfig_Tbl (id, routeid, name, capturetype, mid, username, password) VALUES (1, 1, 'Wirecard_VISA', 2, 'TESTMID', 'username', 'password')");

        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (1)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (1)");

        $this->queryDB("INSERT INTO Client.Routefeature_Tbl (id, clientid, routeconfigid, featureid) VALUES (1, 10099, 1,2)");
        $this->queryDB("INSERT INTO Client.Routefeature_Tbl (id, clientid, routeconfigid, featureid) VALUES (2, 10099, 1, 5)");

        $obj_Config = ClientPaymentMetadata::produceConfig($this->_OBJ_DB, 10099);
        $this->assertInstanceOf(ClientPaymentMetadata::class, $obj_Config);
        $xml = '';
        if ($obj_Config instanceof ClientPaymentMetadata)
        {
            $xml = $obj_Config->toXML();
        }

        $this->assertStringContainsString('<payment_metadata><payment_methods><payment_method><id>1</id><type_id>1</type_id><name>American Express</name></payment_method><payment_method><id>2</id><type_id>1</type_id><name>Dankort</name></payment_method><payment_method><id>3</id><type_id>1</type_id><name>Diners Club</name></payment_method><payment_method><id>5</id><type_id>1</type_id><name>JCB</name></payment_method><payment_method><id>6</id><type_id>1</type_id><name>Maestro</name></payment_method><payment_method><id>7</id><type_id>1</type_id><name>Master Card</name></payment_method><payment_method><id>8</id><type_id>1</type_id><name>VISA</name></payment_method><payment_method><id>9</id><type_id>1</type_id><name>VISA Electron</name></payment_method><payment_method><id>12</id><type_id>1</type_id><name>Switch</name></payment_method><payment_method><id>13</id><type_id>1</type_id><name>Solo</name></payment_method><payment_method><id>14</id><type_id>1</type_id><name>Delta</name></payment_method><payment_method><id>15</id><type_id>3</type_id><name>Apple Pay</name></payment_method><payment_method><id>16</id><type_id>3</type_id><name>VISA Checkout</name></payment_method><payment_method><id>22</id><type_id>1</type_id><name>Discover</name></payment_method><payment_method><id>23</id><type_id>3</type_id><name>Master Pass</name></payment_method><payment_method><id>25</id><type_id>3</type_id><name>AMEX Express Checkout</name></payment_method><payment_method><id>27</id><type_id>3</type_id><name>Android Pay</name></payment_method><payment_method><id>41</id><type_id>3</type_id><name>Google Pay</name></payment_method></payment_methods><payment_providers><payment_provider><id>1</id><name>Wire Card</name><route_configurations><route_configuration><id>1</id><route_name>Wirecard_VISA</route_name></route_configuration></route_configurations></payment_provider></payment_providers><payment_currencies></payment_currencies><payment_countries></payment_countries><route_features><route_feature><id>4</id><name>Partial Capture</name></route_feature><route_feature><id>5</id><name>Refund</name></route_feature><route_feature><id>6</id><name>Partial Refund</name></route_feature><route_feature><id>9</id><name>3DS</name></route_feature><route_feature><id>10</id><name>Installment</name></route_feature><route_feature><id>18</id><name>Cancel</name></route_feature><route_feature><id>19</id><name>Partial Cancel</name></route_feature><route_feature><id>20</id><name>MPI</name></route_feature></route_features><transaction_types><transaction_type><id>1</id><name>Shopping Online</name><enabled>true</enabled></transaction_type><transaction_type><id>2</id><name>Shopping Offline</name><enabled>true</enabled></transaction_type><transaction_type><id>3</id><name>Self Service Online</name><enabled>true</enabled></transaction_type><transaction_type><id>4</id><name>Self Service Offline</name><enabled>true</enabled></transaction_type><transaction_type><id>5</id><name>Self Service Online with additional rules on FOP</name><enabled>true</enabled></transaction_type><transaction_type><id>6</id><name>Payment Link Transaction</name><enabled>true</enabled></transaction_type><transaction_type><id>7</id><name>Telephone Order</name><enabled>true</enabled></transaction_type><transaction_type><id>8</id><name>Mail Order</name><enabled>true</enabled></transaction_type></transaction_types><card_states><card_state><id>1</id><name>Enabled</name><enabled>true</enabled></card_state><card_state><id>2</id><name>Disabled By Merchant</name><enabled>true</enabled></card_state><card_state><id>3</id><name>Disabled By PSP</name><enabled>true</enabled></card_state><card_state><id>4</id><name>Prerequisite not Met</name><enabled>true</enabled></card_state><card_state><id>5</id><name>Temporarily Unavailable</name><enabled>true</enabled></card_state><card_state><id>6</id><name>Disable Show</name><enabled>true</enabled></card_state></card_states><account_configurations><account_config><id>1100</id><client_id>10099</client_id><name></name><markup></markup><mobile></mobile></account_config></account_configurations><fx_service_types><fx_service_type><id>11</id><name>DCC Opt</name><enabled>true</enabled></fx_service_type><fx_service_type><id>12</id><name>DCC Not opt</name><enabled>true</enabled></fx_service_type><fx_service_type><id>21</id><name>MCP opt</name><enabled>true</enabled></fx_service_type><fx_service_type><id>22</id><name>MCP Not opt</name><enabled>true</enabled></fx_service_type><fx_service_type><id>31</id><name>External MCP opt</name><enabled>true</enabled></fx_service_type><fx_service_type><id>32</id><name>External MCP Not opt</name><enabled>true</enabled></fx_service_type><fx_service_type><id>41</id><name>PCC Opt</name><enabled>true</enabled></fx_service_type><fx_service_type><id>42</id><name>PCC Not opt</name><enabled>true</enabled></fx_service_type></fx_service_types></payment_metadata>', $xml);
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
        $this->assertStringContainsString('<payment_metadata><payment_currencies></payment_currencies><payment_countries></payment_countries><route_features><route_feature><id>4</id><name>Partial Capture</name></route_feature><route_feature><id>5</id><name>Refund</name></route_feature><route_feature><id>6</id><name>Partial Refund</name></route_feature><route_feature><id>9</id><name>3DS</name></route_feature><route_feature><id>10</id><name>Installment</name></route_feature><route_feature><id>18</id><name>Cancel</name></route_feature><route_feature><id>19</id><name>Partial Cancel</name></route_feature><route_feature><id>20</id><name>MPI</name></route_feature></route_features><transaction_types><transaction_type><id>1</id><name>Shopping Online</name><enabled>true</enabled></transaction_type><transaction_type><id>2</id><name>Shopping Offline</name><enabled>true</enabled></transaction_type><transaction_type><id>3</id><name>Self Service Online</name><enabled>true</enabled></transaction_type><transaction_type><id>4</id><name>Self Service Offline</name><enabled>true</enabled></transaction_type><transaction_type><id>5</id><name>Self Service Online with additional rules on FOP</name><enabled>true</enabled></transaction_type><transaction_type><id>6</id><name>Payment Link Transaction</name><enabled>true</enabled></transaction_type><transaction_type><id>7</id><name>Telephone Order</name><enabled>true</enabled></transaction_type><transaction_type><id>8</id><name>Mail Order</name><enabled>true</enabled></transaction_type></transaction_types><card_states><card_state><id>1</id><name>Enabled</name><enabled>true</enabled></card_state><card_state><id>2</id><name>Disabled By Merchant</name><enabled>true</enabled></card_state><card_state><id>3</id><name>Disabled By PSP</name><enabled>true</enabled></card_state><card_state><id>4</id><name>Prerequisite not Met</name><enabled>true</enabled></card_state><card_state><id>5</id><name>Temporarily Unavailable</name><enabled>true</enabled></card_state><card_state><id>6</id><name>Disable Show</name><enabled>true</enabled></card_state></card_states><account_configurations><account_config><id>1100</id><client_id>10099</client_id><name></name><markup></markup><mobile></mobile></account_config></account_configurations><fx_service_types><fx_service_type><id>11</id><name>DCC Opt</name><enabled>true</enabled></fx_service_type><fx_service_type><id>12</id><name>DCC Not opt</name><enabled>true</enabled></fx_service_type><fx_service_type><id>21</id><name>MCP opt</name><enabled>true</enabled></fx_service_type><fx_service_type><id>22</id><name>MCP Not opt</name><enabled>true</enabled></fx_service_type><fx_service_type><id>31</id><name>External MCP opt</name><enabled>true</enabled></fx_service_type><fx_service_type><id>32</id><name>External MCP Not opt</name><enabled>true</enabled></fx_service_type><fx_service_type><id>41</id><name>PCC Opt</name><enabled>true</enabled></fx_service_type><fx_service_type><id>42</id><name>PCC Not opt</name><enabled>true</enabled></fx_service_type></fx_service_types></payment_metadata>', $xml);
    }

    /**
     * Test case covered, ClientPaymentMeta with Restrict data as per request.
     * @throws \ErrorException
     */
    public function testSuccessGetClientPaymentMetadataWithRestrict()
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

        $this->queryDB("INSERT INTO Client.Routefeature_Tbl (id, clientid, routeconfigid, featureid) VALUES (1, 10099, 1,2)");
        $this->queryDB("INSERT INTO Client.Routefeature_Tbl (id, clientid, routeconfigid, featureid) VALUES (2, 10099, 1, 5)");

        $requestParam = array('method' => 'true');
        $obj_Config = ClientPaymentMetadata::produceConfig($this->_OBJ_DB, 10099, $requestParam);
        $this->assertInstanceOf(ClientPaymentMetadata::class, $obj_Config);
        $xml = '';
        if ($obj_Config instanceof ClientPaymentMetadata)
        {
            $xml = $obj_Config->toXML();
        }

        $this->assertStringContainsString('<payment_metadata><payment_methods><payment_method><id>1</id><type_id>1</type_id><name>American Express</name></payment_method><payment_method><id>2</id><type_id>1</type_id><name>Dankort</name></payment_method><payment_method><id>3</id><type_id>1</type_id><name>Diners Club</name></payment_method><payment_method><id>5</id><type_id>1</type_id><name>JCB</name></payment_method><payment_method><id>6</id><type_id>1</type_id><name>Maestro</name></payment_method><payment_method><id>7</id><type_id>1</type_id><name>Master Card</name></payment_method><payment_method><id>8</id><type_id>1</type_id><name>VISA</name></payment_method><payment_method><id>9</id><type_id>1</type_id><name>VISA Electron</name></payment_method><payment_method><id>12</id><type_id>1</type_id><name>Switch</name></payment_method><payment_method><id>13</id><type_id>1</type_id><name>Solo</name></payment_method><payment_method><id>14</id><type_id>1</type_id><name>Delta</name></payment_method><payment_method><id>15</id><type_id>3</type_id><name>Apple Pay</name></payment_method><payment_method><id>16</id><type_id>3</type_id><name>VISA Checkout</name></payment_method><payment_method><id>22</id><type_id>1</type_id><name>Discover</name></payment_method><payment_method><id>23</id><type_id>3</type_id><name>Master Pass</name></payment_method><payment_method><id>25</id><type_id>3</type_id><name>AMEX Express Checkout</name></payment_method><payment_method><id>27</id><type_id>3</type_id><name>Android Pay</name></payment_method><payment_method><id>41</id><type_id>3</type_id><name>Google Pay</name></payment_method></payment_methods></payment_metadata>', $xml);
    }

    public function testGetProducts()
    {
        $this->queryDB('INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, \'Test Client\', \'Tuser\', \'Tpass\')');
        $this->queryDB('INSERT INTO Client.Product_tbl (id, code, description, producttypeid, clientid, enabled) VALUES (1, \'P1\', \'Product1\', 100, 10099, true)');
        $this->queryDB('INSERT INTO Client.Product_tbl (id, code, description, producttypeid, clientid, enabled) VALUES (2, \'P2\', \'Product2\', 200, 10099, true)');
        $this->queryDB('INSERT INTO Client.Product_tbl (id, code, description, producttypeid, clientid, enabled) VALUES (3, \'P3\', \'Product2\', 200, 10099, false)');


        $requestParam = array('products' => 'true');
        $obj_Config = ClientPaymentMetadata::produceConfig($this->_OBJ_DB, 10099, $requestParam);
        $this->assertInstanceOf(ClientPaymentMetadata::class, $obj_Config);
        $xml = '';
        if ($obj_Config instanceof ClientPaymentMetadata)
        {
            $xml = $obj_Config->toXML();
        }

        $this->assertStringContainsString('<payment_metadata><products><product><id>1</id><code>P1</code><description>Product1</description><product_category_id>100</product_category_id><enabled>true</enabled></product><product><id>2</id><code>P2</code><description>Product2</description><product_category_id>200</product_category_id><enabled>true</enabled></product><product><id>3</id><code>P3</code><description>Product2</description><product_category_id>200</product_category_id><enabled>false</enabled></product></products></payment_metadata>', $xml);
    }

    public function tearDown() : void
    {
        $this->_OBJ_DB->disConnect();
        parent::tearDown();
    }


}
