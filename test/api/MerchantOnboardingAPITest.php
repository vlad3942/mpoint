<?php
require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class MerchantOnboardingAPITest extends baseAPITest
{
    protected $_aMPOINT_CONN_INFO;

    public function __construct()
    {
        parent::__construct();
    }

    public function constHTTPClient($path,$method='GET')
    {
        global $aMPOINT_CONN_INFO;
        $aMPOINT_CONN_INFO['path'] = $path;
        $aMPOINT_CONN_INFO["contenttype"] = "text/xml";
        $aMPOINT_CONN_INFO["method"] = $method;
        $this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO) );
    }
    public function testSuccessfulGetAddOnConfig()
    {

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO client.dcc_config_tbl (pmid, clientid, countryid, currencyid) VALUES(8, 10099, 200, 566)");
        $this->queryDB("INSERT INTO client.mcp_config_tbl (pmid, clientid, countryid, currencyid) VALUES(8, 10099, 200, 566)");
        $this->queryDB("INSERT INTO client.fraud_config_tbl (clientid, pmid, providerid, countryid, currencyid, typeoffraud) VALUES(10099, 8, 15, 640, 654, 1)");
        $this->queryDB("INSERT INTO client.mpi_config_tbl (clientid, pmid, providerid, \"version\") VALUES(10099, 1, 17, '1.0')");
        $this->queryDB("INSERT INTO client.pcc_config_tbl (clientid, pmid, sale_currency_id, is_presentment, settlement_currency_id) VALUES(10099, 8, 608, true, 590)");
        $this->queryDB("INSERT INTO client.split_configuration_tbl (id, client_id, name, is_one_step_auth) VALUES(1, 10099, 'hybrid', true)");
        $this->queryDB("INSERT INTO client.split_configuration_tbl (id, client_id, name, is_one_step_auth) VALUES(2, 10099, 'cashless', false)");
        $this->queryDB("INSERT INTO client.split_configuration_tbl (id, client_id, name, is_one_step_auth) VALUES(3, 10099, 'conventional', false)");

        $this->queryDB("INSERT INTO client.split_combination_tbl (split_config_id, payment_type, sequence_no) VALUES(1, 1, 1)");
        $this->queryDB("INSERT INTO client.split_combination_tbl (split_config_id, payment_type, sequence_no) VALUES(1, 2, 2)");
        $this->queryDB("INSERT INTO client.split_combination_tbl (split_config_id, payment_type, sequence_no) VALUES(2, 1, 1)");
        $this->queryDB("INSERT INTO client.split_combination_tbl (split_config_id, payment_type, sequence_no) VALUES(2, 2, 2)");
        $this->queryDB("INSERT INTO client.split_combination_tbl (split_config_id, payment_type, sequence_no) VALUES(3, 1, 1)");
        $this->queryDB("INSERT INTO client.split_combination_tbl (split_config_id, payment_type, sequence_no) VALUES(3, 2, 2)");

        $this->queryDB("INSERT INTO client.fraud_property_tbl (clientid,is_rollback) VALUES(10099,true)");
        $this->queryDB("INSERT INTO client.split_property_tbl (clientid,is_rollback) VALUES(10099,true)");




        $this->constHTTPClient("/merchantservices/api/Onboarding.php?service=addonconfig&params=client_id/10099");

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'));
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><addon_configuration_response><dcc_config><addon_configurations><addon_confguration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>566</currency_id><country_id>200</country_id></addon_confguration></addon_configurations></dcc_config><mcp_config><addon_configurations><addon_confguration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>566</currency_id><country_id>200</country_id></addon_confguration></addon_configurations></mcp_config><pcc_config><addon_configurations><addon_confguration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>608</currency_id><settlement_currency_id>590</settlement_currency_id><is_presentment>true</is_presentment></addon_confguration></addon_configurations></pcc_config><mpi_config><addon_configurations><addon_confguration><id>1</id><enabled>true</enabled><pm_id>1</pm_id><provider_id>17</provider_id><version>1.0</version></addon_confguration></addon_configurations></mpi_config><fraud_configs><fraud_config><sub_type>pre_auth</sub_type><addon_configurations><addon_confguration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>654</currency_id><country_id>640</country_id><provider_id>15</provider_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></fraud_config><fraud_config><sub_type>post_auth</sub_type><addon_configurations></addon_configurations><is_rollback>true</is_rollback></fraud_config></fraud_configs><split_payment_configs><split_payment_config><sub_type>hybrid</sub_type><addon_configurations><addon_confguration><id>1</id><enabled>false</enabled><sequence_no>1</sequence_no><payment_type_id>1</payment_type_id></addon_confguration><addon_confguration><id>2</id><enabled>false</enabled><sequence_no>2</sequence_no><payment_type_id>2</payment_type_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></split_payment_config><split_payment_config><sub_type>cashless</sub_type><addon_configurations><addon_confguration><id>3</id><enabled>false</enabled><sequence_no>1</sequence_no><payment_type_id>1</payment_type_id></addon_confguration><addon_confguration><id>4</id><enabled>false</enabled><sequence_no>2</sequence_no><payment_type_id>2</payment_type_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></split_payment_config><split_payment_config><sub_type>conventional</sub_type><addon_configurations><addon_confguration><id>5</id><enabled>false</enabled><sequence_no>1</sequence_no><payment_type_id>1</payment_type_id></addon_confguration><addon_confguration><id>6</id><enabled>false</enabled><sequence_no>2</sequence_no><payment_type_id>2</payment_type_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></split_payment_config></split_payment_configs></addon_configuration_response>', $sReplyBody);

    }

    public function testSuccessfulSaveAddOnConfig()
    {

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");



        $this->constHTTPClient("/merchantservices/api/Onboarding.php?service=addonconfig&params=client_id/10099",'post');

        $this->_httpClient->connect();
        //<editor-fold desc="Request">
        $xml = '<?xml version="1.0" encoding="UTF-8"?><addon_configuration_response><dcc_config><addon_configurations><addon_confguration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>566</currency_id><country_id>200</country_id></addon_confguration></addon_configurations></dcc_config><mcp_config><addon_configurations><addon_confguration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>566</currency_id><country_id>200</country_id></addon_confguration></addon_configurations></mcp_config><pcc_config><addon_configurations><addon_confguration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>608</currency_id><settlement_currency_id>590</settlement_currency_id><is_presentment>true</is_presentment></addon_confguration></addon_configurations></pcc_config><mpi_config><addon_configurations><addon_confguration><id>1</id><enabled>true</enabled><pm_id>1</pm_id><provider_id>17</provider_id><version>1.0</version></addon_confguration></addon_configurations></mpi_config><fraud_configs><fraud_config><sub_type>pre_auth</sub_type><addon_configurations><addon_confguration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>654</currency_id><country_id>640</country_id><provider_id>15</provider_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></fraud_config><fraud_config><sub_type>post_auth</sub_type><addon_configurations></addon_configurations><is_rollback>true</is_rollback></fraud_config></fraud_configs><split_payment_configs><split_payment_config><sub_type>hybrid</sub_type><addon_configurations><addon_confguration><id>1</id><enabled>false</enabled><sequence_no>1</sequence_no><payment_type_id>1</payment_type_id></addon_confguration><addon_confguration><id>2</id><enabled>false</enabled><sequence_no>2</sequence_no><payment_type_id>2</payment_type_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></split_payment_config><split_payment_config><sub_type>cashless</sub_type><addon_configurations><addon_confguration><id>3</id><enabled>false</enabled><sequence_no>1</sequence_no><payment_type_id>1</payment_type_id></addon_confguration><addon_confguration><id>4</id><enabled>false</enabled><sequence_no>2</sequence_no><payment_type_id>2</payment_type_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></split_payment_config><split_payment_config><sub_type>conventional</sub_type><addon_configurations><addon_confguration><id>5</id><enabled>false</enabled><sequence_no>1</sequence_no><payment_type_id>1</payment_type_id></addon_confguration><addon_confguration><id>6</id><enabled>false</enabled><sequence_no>2</sequence_no><payment_type_id>2</payment_type_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></split_payment_config></split_payment_configs></addon_configuration_response>';
        //</editor-fold>

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'),$xml);
        $this->assertEquals(200, $iStatus);

    }

    public function testALLDuplicateSaveAddOnConfig()
    {
        //enable due to sql will trow uniue constraint violation error
        $this->bIgnoreErrors = true;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO client.dcc_config_tbl (pmid, clientid, countryid, currencyid) VALUES(8, 10099, 200, 566)");
        $this->queryDB("INSERT INTO client.mcp_config_tbl (pmid, clientid, countryid, currencyid) VALUES(8, 10099, 200, 566)");
        $this->queryDB("INSERT INTO client.fraud_config_tbl (clientid, pmid, providerid, countryid, currencyid, typeoffraud) VALUES(10099, 8, 15, 640, 654, 1)");
        $this->queryDB("INSERT INTO client.mpi_config_tbl (clientid, pmid, providerid, \"version\") VALUES(10099, 1, 17, '1.0')");
        $this->queryDB("INSERT INTO client.pcc_config_tbl (clientid, pmid, sale_currency_id, is_presentment, settlement_currency_id) VALUES(10099, 8, 608, true, 590)");
        $this->queryDB("INSERT INTO client.split_configuration_tbl (id, client_id, name, is_one_step_auth) VALUES(1, 10099, 'hybrid', true)");
        $this->queryDB("INSERT INTO client.split_configuration_tbl (id, client_id, name, is_one_step_auth) VALUES(2, 10099, 'cashless', false)");
        $this->queryDB("INSERT INTO client.split_configuration_tbl (id, client_id, name, is_one_step_auth) VALUES(3, 10099, 'conventional', false)");

        $this->queryDB("INSERT INTO client.split_combination_tbl (split_config_id, payment_type, sequence_no) VALUES(1, 1, 1)");
        $this->queryDB("INSERT INTO client.split_combination_tbl (split_config_id, payment_type, sequence_no) VALUES(1, 2, 2)");
        $this->queryDB("INSERT INTO client.split_combination_tbl (split_config_id, payment_type, sequence_no) VALUES(2, 1, 1)");
        $this->queryDB("INSERT INTO client.split_combination_tbl (split_config_id, payment_type, sequence_no) VALUES(2, 2, 2)");
        $this->queryDB("INSERT INTO client.split_combination_tbl (split_config_id, payment_type, sequence_no) VALUES(3, 1, 1)");
        $this->queryDB("INSERT INTO client.split_combination_tbl (split_config_id, payment_type, sequence_no) VALUES(3, 2, 2)");

        $this->queryDB("INSERT INTO client.fraud_property_tbl (clientid,is_rollback) VALUES(10099,true)");
        $this->queryDB("INSERT INTO client.split_property_tbl (clientid,is_rollback) VALUES(10099,true)");

        $this->constHTTPClient("/merchantservices/api/Onboarding.php?service=addonconfig&params=client_id/10099",'post');

        $this->_httpClient->connect();
        //<editor-fold desc="Request">
        $xml = '<?xml version="1.0" encoding="UTF-8"?><addon_configuration_response><dcc_config><addon_configurations><addon_confguration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>566</currency_id><country_id>200</country_id></addon_confguration></addon_configurations></dcc_config><mcp_config><addon_configurations><addon_confguration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>566</currency_id><country_id>200</country_id></addon_confguration></addon_configurations></mcp_config><pcc_config><addon_configurations><addon_confguration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>608</currency_id><settlement_currency_id>590</settlement_currency_id><is_presentment>true</is_presentment></addon_confguration></addon_configurations></pcc_config><mpi_config><addon_configurations><addon_confguration><id>1</id><enabled>true</enabled><pm_id>1</pm_id><provider_id>17</provider_id><version>1.0</version></addon_confguration></addon_configurations></mpi_config><fraud_configs><fraud_config><sub_type>pre_auth</sub_type><addon_configurations><addon_confguration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>654</currency_id><country_id>640</country_id><provider_id>15</provider_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></fraud_config><fraud_config><sub_type>post_auth</sub_type><addon_configurations></addon_configurations><is_rollback>true</is_rollback></fraud_config></fraud_configs><split_payment_configs><split_payment_config><sub_type>hybrid</sub_type><addon_configurations><addon_confguration><id>1</id><enabled>false</enabled><sequence_no>1</sequence_no><payment_type_id>1</payment_type_id></addon_confguration><addon_confguration><id>2</id><enabled>false</enabled><sequence_no>2</sequence_no><payment_type_id>2</payment_type_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></split_payment_config><split_payment_config><sub_type>cashless</sub_type><addon_configurations><addon_confguration><id>3</id><enabled>false</enabled><sequence_no>1</sequence_no><payment_type_id>1</payment_type_id></addon_confguration><addon_confguration><id>4</id><enabled>false</enabled><sequence_no>2</sequence_no><payment_type_id>2</payment_type_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></split_payment_config><split_payment_config><sub_type>conventional</sub_type><addon_configurations><addon_confguration><id>5</id><enabled>false</enabled><sequence_no>1</sequence_no><payment_type_id>1</payment_type_id></addon_confguration><addon_confguration><id>6</id><enabled>false</enabled><sequence_no>2</sequence_no><payment_type_id>2</payment_type_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></split_payment_config></split_payment_configs></addon_configuration_response>';
        //</editor-fold>

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'),$xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(500, $iStatus);
        $this->assertStringContainsString('<code>101</code>',$sReplyBody);

    }

}