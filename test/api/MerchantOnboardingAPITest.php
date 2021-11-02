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
        $this->queryDB("INSERT INTO client.tokenization_config_tbl (clientid, pmid, providerid, countryid, currencyid) VALUES(10099, 8, 15, 640, 654)");
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
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><addon_configuration_response><dcc_config><addon_configurations><addon_confguration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>566</currency_id><country_id>200</country_id></addon_confguration></addon_configurations></dcc_config><mcp_config><addon_configurations><addon_confguration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>566</currency_id><country_id>200</country_id></addon_confguration></addon_configurations></mcp_config><pcc_config><addon_configurations><addon_confguration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>608</currency_id><settlement_currency_id>590</settlement_currency_id><is_presentment>true</is_presentment></addon_confguration></addon_configurations></pcc_config><mpi_config><addon_configurations><addon_confguration><id>1</id><enabled>true</enabled><pm_id>1</pm_id><provider_id>17</provider_id><version>1.0</version></addon_confguration></addon_configurations></mpi_config><tokenization_config><addon_configurations><addon_confguration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>654</currency_id><country_id>640</country_id><provider_id>15</provider_id></addon_confguration></addon_configurations></tokenization_config><fraud_configs><fraud_config><sub_type>pre_auth</sub_type><addon_configurations><addon_confguration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>654</currency_id><country_id>640</country_id><provider_id>15</provider_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></fraud_config><fraud_config><sub_type>post_auth</sub_type><addon_configurations></addon_configurations><is_rollback>true</is_rollback></fraud_config></fraud_configs><split_payment_configs><split_payment_config><sub_type>hybrid</sub_type><addon_configurations><addon_confguration><id>1</id><enabled>true</enabled><sequence_no>1</sequence_no><payment_type_id>1</payment_type_id></addon_confguration><addon_confguration><id>2</id><enabled>true</enabled><sequence_no>2</sequence_no><payment_type_id>2</payment_type_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></split_payment_config><split_payment_config><sub_type>cashless</sub_type><addon_configurations><addon_confguration><id>3</id><enabled>true</enabled><sequence_no>1</sequence_no><payment_type_id>1</payment_type_id></addon_confguration><addon_confguration><id>4</id><enabled>true</enabled><sequence_no>2</sequence_no><payment_type_id>2</payment_type_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></split_payment_config><split_payment_config><sub_type>conventional</sub_type><addon_configurations><addon_confguration><id>5</id><enabled>true</enabled><sequence_no>1</sequence_no><payment_type_id>1</payment_type_id></addon_confguration><addon_confguration><id>6</id><enabled>true</enabled><sequence_no>2</sequence_no><payment_type_id>2</payment_type_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></split_payment_config></split_payment_configs></addon_configuration_response>', $sReplyBody);

    }

    public function testSuccessfulSaveAddOnConfig()
    {

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");



        $this->constHTTPClient("/merchantservices/api/Onboarding.php?service=addonconfig",'post');

        $this->_httpClient->connect();
        //<editor-fold desc="Request">
        $xml = '<?xml version="1.0" encoding="UTF-8"?><addon_configuration_request><client_id>10099</client_id><dcc_config><addon_configurations><addon_confguration><pm_id>8</pm_id><currency_id>566</currency_id><country_id>200</country_id></addon_confguration></addon_configurations></dcc_config><mcp_config><addon_configurations><addon_confguration><pm_id>8</pm_id><currency_id>566</currency_id><country_id>200</country_id></addon_confguration></addon_configurations></mcp_config><pcc_config><addon_configurations><addon_confguration><pm_id>8</pm_id><currency_id>608</currency_id><settlement_currency_id>590</settlement_currency_id><is_presentment>true</is_presentment></addon_confguration></addon_configurations></pcc_config><mpi_config><addon_configurations><addon_confguration><pm_id>1</pm_id><provider_id>17</provider_id><version>1.0</version></addon_confguration></addon_configurations></mpi_config><fraud_configs><fraud_config><sub_type>pre_auth</sub_type><addon_configurations><addon_confguration><pm_id>8</pm_id><currency_id>654</currency_id><country_id>640</country_id><provider_id>15</provider_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></fraud_config><fraud_config><sub_type>post_auth</sub_type><addon_configurations><addon_confguration><pm_id>8</pm_id><currency_id>654</currency_id><country_id>640</country_id><provider_id>15</provider_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></fraud_config></fraud_configs><split_payment_configs><split_payment_config><sub_type>hybrid</sub_type><addon_configurations><addon_confguration><sequence_no>1</sequence_no><payment_type_id>1</payment_type_id></addon_confguration><addon_confguration><sequence_no>2</sequence_no><payment_type_id>2</payment_type_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></split_payment_config><split_payment_config><sub_type>cashless</sub_type><addon_configurations><addon_confguration><sequence_no>1</sequence_no><payment_type_id>1</payment_type_id></addon_confguration><addon_confguration><sequence_no>2</sequence_no><payment_type_id>2</payment_type_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></split_payment_config><split_payment_config><sub_type>conventional</sub_type><addon_configurations><addon_confguration><sequence_no>1</sequence_no><payment_type_id>1</payment_type_id></addon_confguration><addon_confguration><sequence_no>2</sequence_no><payment_type_id>2</payment_type_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></split_payment_config></split_payment_configs><tokenization_config><addon_configurations><addon_confguration><pm_id>8</pm_id><currency_id>840</currency_id><country_id>200</country_id><provider_id>15</provider_id></addon_confguration></addon_configurations></tokenization_config></addon_configuration_request>';
        //</editor-fold>

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'),$xml);
        $this->assertEquals(200, $iStatus);

    }

    public function testDuplicateSaveAddOnConfig()
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
        $xml = '<?xml version="1.0" encoding="UTF-8"?><addon_configuration_request><client_id>10099</client_id><dcc_config><addon_configurations><addon_confguration><pm_id>8</pm_id><currency_id>566</currency_id><country_id>200</country_id></addon_confguration></addon_configurations></dcc_config><mcp_config><addon_configurations><addon_confguration><pm_id>8</pm_id><currency_id>566</currency_id><country_id>200</country_id></addon_confguration></addon_configurations></mcp_config><pcc_config><addon_configurations><addon_confguration><pm_id>8</pm_id><currency_id>608</currency_id><settlement_currency_id>590</settlement_currency_id><is_presentment>true</is_presentment></addon_confguration></addon_configurations></pcc_config><mpi_config><addon_configurations><addon_confguration><pm_id>1</pm_id><provider_id>17</provider_id><version>1.0</version></addon_confguration></addon_configurations></mpi_config><fraud_configs><fraud_config><sub_type>pre_auth</sub_type><addon_configurations><addon_confguration><pm_id>8</pm_id><currency_id>654</currency_id><country_id>640</country_id><provider_id>15</provider_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></fraud_config><fraud_config><sub_type>post_auth</sub_type><addon_configurations><addon_confguration><pm_id>8</pm_id><currency_id>654</currency_id><country_id>640</country_id><provider_id>15</provider_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></fraud_config></fraud_configs><split_payment_configs><split_payment_config><sub_type>hybrid</sub_type><addon_configurations><addon_confguration><sequence_no>1</sequence_no><payment_type_id>1</payment_type_id></addon_confguration><addon_confguration><sequence_no>2</sequence_no><payment_type_id>2</payment_type_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></split_payment_config><split_payment_config><sub_type>cashless</sub_type><addon_configurations><addon_confguration><sequence_no>1</sequence_no><payment_type_id>1</payment_type_id></addon_confguration><addon_confguration><sequence_no>2</sequence_no><payment_type_id>2</payment_type_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></split_payment_config><split_payment_config><sub_type>conventional</sub_type><addon_configurations><addon_confguration><sequence_no>1</sequence_no><payment_type_id>1</payment_type_id></addon_confguration><addon_confguration><sequence_no>2</sequence_no><payment_type_id>2</payment_type_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></split_payment_config></split_payment_configs><tokenization_config><addon_configurations><addon_confguration><pm_id>8</pm_id><currency_id>840</currency_id><country_id>200</country_id><provider_id>15</provider_id></addon_confguration></addon_configurations></tokenization_config></addon_configuration_request>';
        //</editor-fold>

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'),$xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(500, $iStatus);
        $this->assertStringContainsString('<code>101</code>',$sReplyBody);

    }


    /* public function testSuccessfulUpdateAddOnConfig()
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


        $req = '<?xml version="1.0" encoding="UTF-8"?><addon_configuration_request><client_id>10077</client_id><dcc_config><addon_configurations><addon_confguration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>566</currency_id><country_id>200</country_id></addon_confguration></addon_configurations></dcc_config><mcp_config><addon_configurations><addon_confguration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>566</currency_id><country_id>200</country_id></addon_confguration></addon_configurations></mcp_config><pcc_config><addon_configurations><addon_confguration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>608</currency_id><settlement_currency_id>590</settlement_currency_id><is_presentment>true</is_presentment></addon_confguration></addon_configurations></pcc_config><mpi_config><addon_configurations><addon_confguration><id>1</id><enabled>true</enabled><pm_id>1</pm_id><provider_id>17</provider_id><version>1.0</version></addon_confguration></addon_configurations></mpi_config><tokenization_config><addon_configurations><addon_confguration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>840</currency_id><country_id>200</country_id><provider_id>15</provider_id></addon_confguration></addon_configurations></tokenization_config><fraud_configs><fraud_config><sub_type>pre_auth</sub_type><addon_configurations><addon_confguration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>654</currency_id><country_id>640</country_id><provider_id>15</provider_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></fraud_config><fraud_config><sub_type>post_auth</sub_type><addon_configurations><addon_confguration><id>2</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>654</currency_id><country_id>640</country_id><provider_id>15</provider_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></fraud_config></fraud_configs><split_payment_configs><split_payment_config><sub_type>hybrid</sub_type><addon_configurations><addon_confguration><id>1</id><enabled>false</enabled><sequence_no>1</sequence_no><payment_type_id>1</payment_type_id></addon_confguration><addon_confguration><id>2</id><enabled>false</enabled><sequence_no>2</sequence_no><payment_type_id>2</payment_type_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></split_payment_config><split_payment_config><sub_type>cashless</sub_type><addon_configurations><addon_confguration><id>3</id><enabled>false</enabled><sequence_no>1</sequence_no><payment_type_id>1</payment_type_id></addon_confguration><addon_confguration><id>4</id><enabled>false</enabled><sequence_no>2</sequence_no><payment_type_id>2</payment_type_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></split_payment_config><split_payment_config><sub_type>conventional</sub_type><addon_configurations><addon_confguration><id>5</id><enabled>false</enabled><sequence_no>1</sequence_no><payment_type_id>1</payment_type_id></addon_confguration><addon_confguration><id>6</id><enabled>false</enabled><sequence_no>2</sequence_no><payment_type_id>2</payment_type_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></split_payment_config></split_payment_configs></addon_configuration_request>';

        $this->constHTTPClient("/merchantservices/api/Onboarding.php?service=addonconfig&params=client_id/10099",'put');

        $this->_httpClient->connect();


        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'),$req);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);

    }*/

   /* public function testSuccessfulDeleteAddOnConfig()
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


        $this->constHTTPClient("/merchantservices/api/Onboarding.php?service=addonconfig&params=client_id/10077/dcc/1/mcp/1/pcc/1/mpi/1/tokenization/1/fraud/1/split_payment/1,2,3",'DELETE');

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'));
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);

    }*/

    public function testSuccessfulGetPSPProperty()
    {

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.psp_property_tbl (clientid,propertyid,value) VALUES ( 10099,(select ID from system.psp_property_tbl where name='FILE_EXPIRY' AND PSPID=52),'CPD_')");
        $this->queryDB("INSERT INTO Client.psp_property_tbl (clientid,propertyid,value) VALUES ( 10099,(select ID from system.psp_property_tbl where name='IS_TICKET_LEVEL_SETTLEMENT' AND PSPID=52),'true')");

        $this->constHTTPClient("/merchantservices/api/Onboarding.php?service=pspconfig&params=client_id/10099/psp_id/52");

        $this->_httpClient->connect();
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'));
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><client_psp_configuration><property_details><property_detail><property_sub_category>Technical</property_sub_category><properties><property><id>25</id><name>CHASE_FILE_PREFIX</name><data_type>3</data_type><enabled>true</enabled><mandatory>true</mandatory></property><property><id>21</id><name>FILE_EXPIRY</name><value>CPD_</value><data_type>2</data_type><enabled>true</enabled><mandatory>true</mandatory></property><property><id>22</id><name>IS_TICKET_LEVEL_SETTLEMENT</name><value>true</value><data_type>1</data_type><enabled>true</enabled><mandatory>true</mandatory></property><property><id>30</id><name>MAX_DOWNLOAD_FILE_LIMIT</name><data_type>2</data_type><enabled>true</enabled><mandatory>true</mandatory></property><property><id>23</id><name>MVAULT_BATCH_SIZE</name><data_type>2</data_type><enabled>true</enabled><mandatory>true</mandatory></property><property><id>29</id><name>SETTLEMENT_BATCH_LIMIT</name><data_type>2</data_type><enabled>true</enabled><mandatory>true</mandatory></property><property><id>28</id><name>debug</name><data_type>1</data_type><enabled>true</enabled><mandatory>true</mandatory></property></properties></property_detail><property_detail><property_sub_category>Basic</property_sub_category><properties><property><id>26</id><name>CHASE_SFTP_PASSWORD</name><data_type>3</data_type><enabled>true</enabled><mandatory>true</mandatory></property><property><id>27</id><name>CHASE_SFTP_USERNAME</name><data_type>3</data_type><enabled>true</enabled><mandatory>true</mandatory></property><property><id>24</id><name>MERCHANT.CITY</name><data_type>3</data_type><enabled>true</enabled><mandatory>true</mandatory></property></properties></property_detail></property_details></client_psp_configuration>', $sReplyBody);

    }

    public function testSuccessfulSavePSPProperty()
    {

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");


        $this->constHTTPClient("/merchantservices/api/Onboarding.php?service=pspconfig",'POST');
        $xml = '<?xml version="1.0" encoding="UTF-8"?><client_psp_configuration><client_id>10099</client_id><psp_id>52</psp_id><properties><property><id>22</id><value>true</value></property><property><id>21</id><value>CPD_</value></property></properties></client_psp_configuration>';
        $this->_httpClient->connect();
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'),$xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(200, $iStatus);
        $res =  $this->queryDB("SELECT id FROM CLIENT.psp_property_tbl where value in ('CPD_','true')" );
        $this->assertIsResource($res);
        $this->assertEquals(2, pg_num_rows($res));

    }

    public function testAlreadySavedSuccessSavePSPProperty()
    {
        //enable due to sql will trow uniue constraint violation error
        $this->bIgnoreErrors = true;
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.psp_property_tbl (clientid,propertyid,value) VALUES ( 10099,(select ID from system.psp_property_tbl where name='FILE_EXPIRY' AND PSPID=52),'CPD_')");
        $this->queryDB("INSERT INTO Client.psp_property_tbl (clientid,propertyid,value) VALUES ( 10099,(select ID from system.psp_property_tbl where name='IS_TICKET_LEVEL_SETTLEMENT' AND PSPID=52),'true')");

        $this->constHTTPClient("/merchantservices/api/Onboarding.php?service=pspconfig",'POST');
        $xml = '<?xml version="1.0" encoding="UTF-8"?><client_psp_configuration><client_id>10099</client_id><psp_id>52</psp_id><properties><property><id>22</id><value>true</value></property><property><id>21</id><value>CPD_</value></property></properties></client_psp_configuration>';
        $this->_httpClient->connect();
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'),$xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(500, $iStatus);
        $this->assertStringContainsString('<code>101</code>',$sReplyBody);


    }

    /*
    public function testSuccessfulUpdatePSPProperty()
    {

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.psp_property_tbl (clientid,propertyid,value) VALUES ( 10099,(select ID from system.psp_property_tbl where name='FILE_EXPIRY' AND PSPID=52),'CPD_')");
        $this->queryDB("INSERT INTO Client.psp_property_tbl (clientid,propertyid,value) VALUES ( 10099,(select ID from system.psp_property_tbl where name='IS_TICKET_LEVEL_SETTLEMENT' AND PSPID=52),'true')");

        $this->constHTTPClient("/merchantservices/api/Onboarding.php?service=pspconfig",'PUT');
        $xml = '<?xml version="1.0" encoding="UTF-8"?><client_psp_configuration><client_id>10099</client_id><psp_id>52</psp_id><properties><property><id>22</id><value>true</value></property><property><id>21</id><value>CPD_123</value></property></properties></client_psp_configuration>';
        $this->_httpClient->connect();
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'),$xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(200, $iStatus);
    }

    public function testSuccessfulDeletePSPProperty()
    {

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.psp_property_tbl (clientid,propertyid,value) VALUES ( 10099,(select ID from system.psp_property_tbl where name='FILE_EXPIRY' AND PSPID=52),'CPD_')");
        $this->queryDB("INSERT INTO Client.psp_property_tbl (clientid,propertyid,value) VALUES ( 10099,(select ID from system.psp_property_tbl where name='IS_TICKET_LEVEL_SETTLEMENT' AND PSPID=52),'true')");

        $this->constHTTPClient("/merchantservices/api/Onboarding.php?service=pspconfig&params=client_id/10099/psp_id/52",'DELETE');

        $this->_httpClient->connect();
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'));
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);

    }

    */

    public function testWrongPSPSavePSPProperty()
    {
        //enable due to sql will trow uniue constraint violation error
        $this->bIgnoreErrors = true;
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");

        $this->constHTTPClient("/merchantservices/api/Onboarding.php?service=pspconfig",'POST');
        $xml = '<?xml version="1.0" encoding="UTF-8"?><client_psp_configuration><client_id>10099</client_id><psp_id>50</psp_id><properties><property><id>22</id><value>true</value></property><property><id>21</id><value>CPD_</value></property></properties></client_psp_configuration>';
        $this->_httpClient->connect();
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'),$xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(500, $iStatus);
        $this->assertStringContainsString('<code>100</code>',$sReplyBody);
    }

    public function testSuccessfulGetRouteProperty()
    {

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.route_tbl (id, clientid, providerid) VALUES (1, 10099, 50)");
        $this->queryDB("INSERT INTO Client.routeconfig_tbl (id, routeid, name, capturetype, mid, username, password) VALUES (1, 1, 'TEST', 2, 'TESTMID', 'username', 'password')");
        $this->queryDB("INSERT INTO Client.route_property_tbl (propertyid,routeconfigid,value) VALUES ( (select ID from system.route_property_tbl where name='CeptorAccessId' AND PSPID=50),1,'1234')");
        $this->queryDB("INSERT INTO Client.route_property_tbl (propertyid,routeconfigid,value) VALUES ( (select ID from system.route_property_tbl where name='CeptorAccessKey' AND PSPID=50),1,'1233')");
        $this->queryDB("INSERT INTO client.routepm_tbl (routeconfigid, pmid) VALUES (1,8)");
        $this->queryDB("INSERT INTO client.routepm_tbl (routeconfigid, pmid) VALUES (1,7)");

        $this->constHTTPClient("/merchantservices/api/Onboarding.php?service=routeconfig&params=client_id/10099/route_conf_id/1");

        $this->_httpClient->connect();
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'));
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><client_route_configuration><property_details><property_detail><property_sub_category>Basic</property_sub_category><properties><property><id>41</id><name>CeptorAccessId</name><value>1234</value><data_type>3</data_type><enabled>true</enabled><mandatory>true</mandatory></property><property><id>42</id><name>CeptorAccessKey</name><value>1233</value><data_type>3</data_type><enabled>true</enabled><mandatory>true</mandatory></property></properties></property_detail></property_details><pm_configurations><pm_configuration><pm_id>8</pm_id><enabled>true</enabled></pm_configuration><pm_configuration><pm_id>7</pm_id><enabled>true</enabled></pm_configuration></pm_configurations></client_route_configuration>', $sReplyBody);

    }

    public function testSuccessfulSaveRouteProperty()
    {

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.route_tbl (id, clientid, providerid) VALUES (1, 10099, 50)");
        $this->queryDB("INSERT INTO Client.routeconfig_tbl (id, routeid, name, capturetype, mid, username, password) VALUES (1, 1, 'TEST', 2, 'TESTMID', 'username', 'password')");

        $this->constHTTPClient("/merchantservices/api/Onboarding.php?service=routeconfig",'POST');
        $xml= '<?xml version="1.0" encoding="UTF-8"?><client_route_configuration><client_id>10099</client_id><route_config_id>1</route_config_id><properties><property><id>41</id><value>1234</value></property><property><id>42</id><value>1233</value></property></properties><pm_configurations><pm_configuration><pm_id>8</pm_id></pm_configuration><pm_configuration><pm_id>7</pm_id></pm_configuration></pm_configurations></client_route_configuration>';
        $this->_httpClient->connect();
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'),$xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(200, $iStatus);
        $res =  $this->queryDB("SELECT id FROM CLIENT.route_property_tbl where value in ('1234','1233')" );
        $this->assertIsResource($res);
        $this->assertEquals(2, pg_num_rows($res));
        $res =  $this->queryDB("SELECT id FROM CLIENT.routepm_tbl" );
        $this->assertIsResource($res);
        $this->assertEquals(2, pg_num_rows($res));
    }

    public function testAlreadySavedSuccessSaveRouteProperty()
    {
        //enable due to sql will trow uniue constraint violation error
        $this->bIgnoreErrors = true;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.route_tbl (id, clientid, providerid) VALUES (1, 10099, 50)");
        $this->queryDB("INSERT INTO Client.routeconfig_tbl (id, routeid, name, capturetype, mid, username, password) VALUES (1, 1, 'TEST', 2, 'TESTMID', 'username', 'password')");
        $this->queryDB("INSERT INTO Client.route_property_tbl (propertyid,routeconfigid,value) VALUES ( (select ID from system.route_property_tbl where name='CeptorAccessId' AND PSPID=50),1,'1234')");
        $this->queryDB("INSERT INTO Client.route_property_tbl (propertyid,routeconfigid,value) VALUES ( (select ID from system.route_property_tbl where name='CeptorAccessKey' AND PSPID=50),1,'1233')");
        $this->queryDB("INSERT INTO client.routepm_tbl (routeconfigid, pmid) VALUES (1,8)");
        $this->queryDB("INSERT INTO client.routepm_tbl (routeconfigid, pmid) VALUES (1,7)");

       $this->constHTTPClient("/merchantservices/api/Onboarding.php?service=routeconfig",'POST');
        $xml= '<?xml version="1.0" encoding="UTF-8"?><client_route_configuration><client_id>10099</client_id><route_config_id>1</route_config_id><properties><property><id>41</id><value>1234</value></property><property><id>42</id><value>1233</value></property></properties><pm_configurations><pm_configuration><pm_id>8</pm_id></pm_configuration><pm_configuration><pm_id>7</pm_id></pm_configuration></pm_configurations></client_route_configuration>';

        $this->_httpClient->connect();
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'),$xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(500, $iStatus);
        $this->assertStringContainsString('<code>101</code>',$sReplyBody);

    }

    public function testWrongSaveRouteProperty()
    {
        //enable due to sql will trow uniue constraint violation error
        $this->bIgnoreErrors = true;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.route_tbl (id, clientid, providerid) VALUES (1, 10099, 50)");
        $this->queryDB("INSERT INTO Client.routeconfig_tbl (id, routeid, name, capturetype, mid, username, password) VALUES (1, 1, 'TEST', 2, 'TESTMID', 'username', 'password')");
        $this->queryDB("INSERT INTO Client.route_property_tbl (propertyid,routeconfigid,value) VALUES ( (select ID from system.route_property_tbl where name='CeptorAccessId' AND PSPID=50),1,'1234')");
        $this->queryDB("INSERT INTO Client.route_property_tbl (propertyid,routeconfigid,value) VALUES ( (select ID from system.route_property_tbl where name='CeptorAccessKey' AND PSPID=50),1,'1233')");
        $this->queryDB("INSERT INTO client.routepm_tbl (routeconfigid, pmid) VALUES (1,8)");
        $this->queryDB("INSERT INTO client.routepm_tbl (routeconfigid, pmid) VALUES (1,7)");

       $this->constHTTPClient("/merchantservices/api/Onboarding.php?service=routeconfig",'POST');
        $xml= '<?xml version="1.0" encoding="UTF-8"?><client_route_configuration><client_id>10099</client_id><route_config_id>1</route_config_id><properties><property><id>41</id><value>1234</value></property><property><id>42</id><value>1233</value></property></properties><pm_configurations><pm_configuration><pm_id>10</pm_id></pm_configuration><pm_configuration><pm_id>9</pm_id></pm_configuration></pm_configurations></client_route_configuration>';

        $this->_httpClient->connect();
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'),$xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(500, $iStatus);
        $this->assertStringContainsString('<code>101</code>',$sReplyBody);

    }

    /*

    public function testSuccessfulUpdateRouteProperty()
    {

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.route_tbl (id, clientid, providerid) VALUES (1, 10099, 50)");
        $this->queryDB("INSERT INTO Client.routeconfig_tbl (id, routeid, name, capturetype, mid, username, password) VALUES (1, 1, 'TEST', 2, 'TESTMID', 'username', 'password')");
        $this->queryDB("INSERT INTO Client.route_property_tbl (propertyid,routeconfigid,value) VALUES ( (select ID from system.route_property_tbl where name='CeptorAccessId' AND PSPID=50),1,'1234')");
        $this->queryDB("INSERT INTO Client.route_property_tbl (propertyid,routeconfigid,value) VALUES ( (select ID from system.route_property_tbl where name='CeptorAccessKey' AND PSPID=50),1,'1233')");
        $this->queryDB("INSERT INTO client.routepm_tbl (routeconfigid, pmid) VALUES (1,8)");
        $this->queryDB("INSERT INTO client.routepm_tbl (routeconfigid, pmid) VALUES (1,7)");

        $this->constHTTPClient("/merchantservices/api/Onboarding.php?service=routeconfig",'PUT');

        $xml= '<?xml version="1.0" encoding="UTF-8"?><client_route_configuration><client_id>10099</client_id><route_config_id>1</route_config_id><properties><property><id>41</id><value>12345</value></property><property><id>42</id><value>12335</value></property></properties><pm_configurations><pm_configuration><pm_id>8</pm_id></pm_configuration><pm_configuration><pm_id>7</pm_id></pm_configuration></pm_configurations></client_route_configuration>';
        $this->_httpClient->connect();
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'),$xml);

        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(200, $iStatus);

    }

    public function testSuccessfulDeleteRouteProperty()
    {

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.route_tbl (id, clientid, providerid) VALUES (1, 10099, 50)");
        $this->queryDB("INSERT INTO Client.routeconfig_tbl (id, routeid, name, capturetype, mid, username, password) VALUES (1, 1, 'TEST', 2, 'TESTMID', 'username', 'password')");
        $this->queryDB("INSERT INTO Client.route_property_tbl (propertyid,routeconfigid,value) VALUES ( (select ID from system.route_property_tbl where name='CeptorAccessId' AND PSPID=50),1,'1234')");
        $this->queryDB("INSERT INTO Client.route_property_tbl (propertyid,routeconfigid,value) VALUES ( (select ID from system.route_property_tbl where name='CeptorAccessKey' AND PSPID=50),1,'1233')");
        $this->queryDB("INSERT INTO client.routepm_tbl (routeconfigid, pmid) VALUES (1,8)");
        $this->queryDB("INSERT INTO client.routepm_tbl (routeconfigid, pmid) VALUES (1,7)");

        $this->constHTTPClient("/merchantservices/api/Onboarding.php?service=routeconfig&params=client_id/10099/route_conf_id/1", 'DELETE');

        $this->_httpClient->connect();
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'));
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(200, $iStatus);

    }

    */

    public function testSuccessfulGetSystemMetadata()
    {

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.route_tbl (id, clientid, providerid) VALUES (1, 10099, 50)");
        $this->queryDB("INSERT INTO Client.routeconfig_tbl (id, routeid, name, capturetype, mid, username, password) VALUES (1, 1, 'TEST', 2, 'TESTMID', 'username', 'password')");

        $this->constHTTPClient("/merchantservices/api/Onboarding.php?service=system_metadata&params=client_id/10099");

        $this->_httpClient->connect();        
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'));
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(200, $iStatus);

        $this->assertStringContainsString('<psp><id>1</id><name>Cellpoint Mobile</name><type_id>1</type_id></psp>',$sReplyBody);
        $this->assertStringContainsString('<pm_type><id>1</id><name>Card</name></pm_type>',$sReplyBody);
        $this->assertStringContainsString('<country_detail><id>310</id><name>Central African Republic</name></country_detail>',$sReplyBody);
        $this->assertStringContainsString('<currency_detail><id>12</id><name>Algerian Dinar</name>',$sReplyBody);
        $this->assertStringContainsString('<capture_type><id>1</id><name>Manual Capture</name></capture_type>',$sReplyBody);
        $this->assertStringContainsString('<id>1</id><name>Import Customer Data</name></client_url>',$sReplyBody);
        $this->assertStringContainsString('<payment_processor><id>1</id><name>PSP</name></payment_processor>',$sReplyBody);
        $this->assertStringContainsString('<addon_type><id>1</id><name>FX</name><addon_subtypes><addon_subtype><id>1</id><name>DCC</name></addon_subtype><addon_subtype><id>2</id><name>MCP</name></addon_subtype><addon_subtype><id>3</id><name>PCC</name></addon_subtype></addon_subtypes></addon_type>',$sReplyBody);

    }

    public function testSuccessfulGetPaymentMetadata()
    {

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.route_tbl (id, clientid, providerid) VALUES (1, 10099, 50)");
        $this->queryDB("INSERT INTO Client.routeconfig_tbl (id, routeid, name, capturetype, mid, username, password) VALUES (1, 1, 'TEST', 2, 'TESTMID', 'username', 'password')");

        $this->constHTTPClient("/merchantservices/api/Onboarding.php?service=payment_metadata&params=client_id/10099");

        $this->_httpClient->connect();        
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'));
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(200, $iStatus);

        $this->assertStringContainsString('<pm><id>10</id><name>SMS</name><type_id>1</type_id></pm>',$sReplyBody);
        $this->assertStringContainsString('<payment_provider><id>1</id><name>UATP CardAccount</name><route_configurations><route_configuration><id>1</id><name>TEST</name></route_configuration></route_configurations></payment_provider>',$sReplyBody);
        $this->assertStringContainsString('<route_feature><id>1</id><name>Pre-Auth</name></route_feature>',$sReplyBody);
        $this->assertStringContainsString('<transaction_type><id>10</id><name>Call Centre Purchase</name></transaction_type>',$sReplyBody);
        $this->assertStringContainsString('<card_state><id>1</id><name>Enabled</name></card_state>',$sReplyBody);
        $this->assertStringContainsString('<fx_service_type><id>11</id><name>DCC Opt</name></fx_service_type>',$sReplyBody);
                  
    }

    public function testInvalidXMLSaveAddOnConfig()
    {

        $this->bIgnoreErrors = true;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");



        $this->constHTTPClient("/merchantservices/api/Onboarding.php?service=addonconfig",'post');

        $this->_httpClient->connect();
        $xml = '<?xml version="1.0" encoding="UTF-8"?><addon_configuration_request><client_id>10099</client_id><dcc_config><addon_configurations><addon_confguration><pm_id>8</pm_id><currency_id>566</currency_id><country_id>200</country_id></addon_confguration></addon_configurations></dcc_config><mcp_config><addon_configurations><addon_confguration><currency_id>566</currency_id><country_id>200</country_id></addon_confguration></addon_configurations></mcp_config><pcc_config><addon_configurations><addon_confguration><pm_id>8</pm_id><currency_id>608</currency_id><settlement_currency_id>590</settlement_currency_id><is_presentment>true</is_presentment></addon_confguration></addon_configurations></pcc_config><mpi_config><addon_configurations><addon_confguration><pm_id>1</pm_id><provider_id>17</provider_id><version>1.0</version></addon_confguration></addon_configurations></mpi_config><fraud_configs><fraud_config><sub_type>pre_auth</sub_type><addon_configurations><addon_confguration><pm_id>8</pm_id><currency_id>654</currency_id><country_id>640</country_id><provider_id>15</provider_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></fraud_config><fraud_config><sub_type>post_auth</sub_type><addon_configurations><addon_confguration><pm_id>8</pm_id><currency_id>654</currency_id><country_id>640</country_id><provider_id>15</provider_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></fraud_config></fraud_configs><split_payment_configs><split_payment_config><sub_type>hybrid</sub_type><addon_configurations><addon_confguration><sequence_no>1</sequence_no><payment_type_id>1</payment_type_id></addon_confguration><addon_confguration><sequence_no>2</sequence_no><payment_type_id>2</payment_type_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></split_payment_config><split_payment_config><sub_type>cashless</sub_type><addon_configurations><addon_confguration><sequence_no>1</sequence_no><payment_type_id>1</payment_type_id></addon_confguration><addon_confguration><sequence_no>2</sequence_no><payment_type_id>2</payment_type_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></split_payment_config><split_payment_config><sub_type>conventional</sub_type><addon_configurations><addon_confguration><sequence_no>1</sequence_no><payment_type_id>1</payment_type_id></addon_confguration><addon_confguration><sequence_no>2</sequence_no><payment_type_id>2</payment_type_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></split_payment_config></split_payment_configs><tokenization_config><addon_configurations><addon_confguration><pm_id>8</pm_id><currency_id>840</currency_id><country_id>200</country_id><provider_id>15</provider_id></addon_confguration></addon_configurations></tokenization_config></addon_configuration_request>';

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'),$xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(400, $iStatus);
        $this->assertStringContainsString('<code>400</code>',$sReplyBody);


    }

    /**
     * Test GET :: ClientConfiguration | Success
     *
     * @throws \ErrorException
     * @throws \HTTPConnectionException
     * @throws \HTTPSendException
     */
    public function testSuccessfulGetClientConfiguration()
    {

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd, cssurl, callbackurl) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass','https://devcpmassets.s3-ap-southeast-1.amazonaws.com', 'https://hpp2.sit-01.cellpoint.dev/views/callback.php')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");

        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");

        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");

        $this->queryDB("INSERT INTO client.services_tbl (clientid, dcc_enabled, mcp_enabled, pcc_enabled, fraud_enabled, tokenization_enabled, splitpayment_enabled, callback_enabled, void_enabled, enabled, created, modified,legacy_flow_enabled) VALUES (10099::integer, DEFAULT, true::boolean, true::boolean, true::boolean, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT,FALSE);");
        $this->queryDB("INSERT INTO client.pm_tbl (clientid, pmid, enabled, created, modified) VALUES (10099::integer, 1::integer, DEFAULT, DEFAULT, DEFAULT);");
        $this->queryDB("INSERT INTO client.pm_tbl (clientid, pmid, enabled, created, modified) VALUES (10099::integer, 4::integer, DEFAULT, DEFAULT, DEFAULT);");
        $this->queryDB("INSERT INTO client.pm_tbl (clientid, pmid, enabled, created, modified) VALUES (10099::integer, 12::integer, DEFAULT, DEFAULT, DEFAULT);");

        $this->constHTTPClient("/merchantservices/api/Onboarding.php?service=clientconfig&params=client_id/10099");

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'));
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><client_configuration><id>10099</id><name>Test Client</name><salt></salt><max_amount>0</max_amount><country_id>100</country_id><email_notification>true</email_notification><sms_notification>false</sms_notification><client_urls><client_url><id>1</id><name>Mobile Enterprise Servicebus</name><type_id>4</type_id><value>http://mpoint.local.cellpointmobile.com/</value></client_url></client_urls><merchant_urls><client_url><id>10099</id><name>Callback URL</name><type_id>7</type_id><value>https://hpp2.sit-01.cellpoint.dev/views/callback.php</value></client_url></merchant_urls><hpp_urls><client_url><id>10099</id><name>CSS URL</name><type_id>6</type_id><value>https://devcpmassets.s3-ap-southeast-1.amazonaws.com</value></client_url></hpp_urls><services><dcc>true</dcc><fraud>true</fraud><callback>true</callback><mcp>true</mcp><pcc>true</pcc><split_payment>true</split_payment><tokenization>true</tokenization><void>true</void></services><account_configurations><account_config><id>1100</id><client_id>10099</client_id><name></name><markup></markup><mobile></mobile></account_config></account_configurations><pm_configurations><pm_configuration><pm_id>1</pm_id><enabled>true</enabled></pm_configuration><pm_configuration><pm_id>4</pm_id><enabled>true</enabled></pm_configuration><pm_configuration><pm_id>12</pm_id><enabled>true</enabled></pm_configuration></pm_configurations><property_details><property_detail><property_sub_category>HPP</property_sub_category><properties><property><id>48</id><name>ADOBE_TARGET_SCRIPT</name><data_type>1</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>49</id><name>ADOBE_TARGET_SCRIPT_PATH</name><data_type>3</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>50</id><name>CYBS_DM_MID</name><data_type>3</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>51</id><name>CYBS_DM_ORGID</name><data_type>3</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>52</id><name>DFP_GEN</name><data_type>1</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>56</id><name>HPP_SHOW_CONTACT_DETAILS</name><data_type>1</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>62</id><name>SWISH_HPP_ECOMMERCE_QRCODE_ENABLE</name><data_type>1</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>53</id><name>enableHppAuthentication</name><data_type>1</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>54</id><name>getTxnStatusPollingTimeOut</name><data_type>2</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>55</id><name>googleAnalyticsId</name><data_type>3</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>57</id><name>isAutoRedirect</name><data_type>1</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>58</id><name>mandateBillingDetails</name><data_type>1</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>59</id><name>maxPollingInterval</name><data_type>2</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>60</id><name>minPollingInterval</name><data_type>2</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>61</id><name>showBillingDetails</name><data_type>1</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>63</id><name>webSessionTimeout</name><data_type>2</data_type><enabled>true</enabled><mandatory>false</mandatory></property></properties></property_detail><property_detail><property_sub_category>Basic</property_sub_category><properties><property><id>1</id><name>AIRLINE_CODE</name><data_type>3</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>2</id><name>AIRLINE_NUMRIC_CODE</name><data_type>2</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>3</id><name>APPLEPAY_JS_URL</name><data_type>3</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>4</id><name>CARD_ACCEPTOR_ADDRESS</name><data_type>3</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>5</id><name>CARD_ACCEPTOR_BUSINESS_CODE</name><data_type>3</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>6</id><name>CARD_ACCEPTOR_CITY</name><data_type>3</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>7</id><name>CARD_ACCEPTOR_COUNTRY</name><data_type>3</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>8</id><name>CARD_ACCEPTOR_IDENTIFICATION_CODE</name><data_type>2</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>9</id><name>CARD_ACCEPTOR_NAME</name><data_type>3</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>10</id><name>CARD_ACCEPTOR_REGION</name><data_type>3</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>11</id><name>CARD_ACCEPTOR_ZIP</name><data_type>2</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>12</id><name>CARRIER_NAME</name><data_type>3</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>13</id><name>ENABLE_PROFILE_ANONYMIZATION</name><data_type>1</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>14</id><name>GOOGLE_PAY_JS_URL</name><data_type>3</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>15</id><name>HPP_HOST_URL</name><data_type>3</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>16</id><name>InstitutionIdentificationCode</name><data_type>2</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>18</id><name>PROFILE_EXPIRY</name><data_type>2</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>19</id><name>PROFILE_TOKEN</name><data_type>3</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>20</id><name>TICKET_ISSUE_CITY</name><data_type>3</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>21</id><name>TIMEZONE</name><data_type>3</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>17</id><name>invoiceidrules</name><data_type>3</data_type><enabled>true</enabled><mandatory>false</mandatory></property></properties></property_detail><property_detail><property_sub_category>Technical</property_sub_category><properties><property><id>27</id><name>DR_SERVICE</name><data_type>1</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>37</id><name>GOMOBILE_EMAIL_SENDER</name><data_type>3</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>38</id><name>GOMOBILE_PUSH_CHANNEL</name><data_type>2</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>39</id><name>GOMOBILE_PUSH_KEYWORD</name><data_type>3</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>40</id><name>GOMOBILE_SMS_CHANNEL</name><data_type>2</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>41</id><name>GOMOBILE_SMS_KEYWORD</name><data_type>3</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>42</id><name>GOMOBILE_SMS_PASSWORD</name><data_type>3</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>43</id><name>GOMOBILE_SMS_PRICE</name><data_type>2</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>44</id><name>GOMOBILE_SMS_USERNAME</name><data_type>3</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>28</id><name>IS_LEGACY</name><data_type>1</data_type><enabled>true</enabled><mandatory>true</mandatory></property><property><id>29</id><name>IS_LEGACY_CALLBACK_FLOW</name><data_type>1</data_type><enabled>true</enabled><mandatory>true</mandatory></property><property><id>32</id><name>IS_STORE_BILLING_ADDRS</name><data_type>1</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>22</id><name>PROXY_CALLBACK</name><data_type>3</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>25</id><name>SSO_PREFERENCE</name><data_type>3</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>47</id><name>UATP_SETTLEMENT_FILE_NAME</name><data_type>3</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>33</id><name>autoFetchBalance</name><data_type>1</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>34</id><name>cumulativesettlement</name><data_type>1</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>26</id><name>debug</name><data_type>1</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>35</id><name>fetchBalancePaymentMethods</name><data_type>3</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>36</id><name>fetchBalanceUserType</name><data_type>3</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>45</id><name>ismutualexclusive</name><data_type>1</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>24</id><name>isnewcardconfig</name><data_type>1</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>30</id><name>max_session_retry_count</name><data_type>2</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>31</id><name>mvault</name><data_type>1</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>46</id><name>preferredvoidoperation</name><data_type>1</data_type><enabled>true</enabled><mandatory>false</mandatory></property><property><id>23</id><name>sessiontype</name><data_type>2</data_type><enabled>true</enabled><mandatory>false</mandatory></property></properties></property_detail></property_details></client_configuration>', $sReplyBody);
    }

    /***
     *
     * @throws \ErrorException
     * @throws \HTTPConnectionException
     * @throws \HTTPSendException
     */
    public function testSuccessfulPostClientConfiguration()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");

        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");

        # RQ Body
        $xml= '<?xml version="1.0" encoding="UTF-8"?> <client_configuration>     <client_id>10099</client_id> <client_urls> <client_url> <id>1</id> <name>Single Sign-On Authentication</name> <type_id>15</type_id> <value>http://mpoint.local.cellpoint.dev/_test/simulators/login.php</value> </client_url> </client_urls><merchant_urls> <client_url> <id>10077</id> <name>Callback URL</name> <type_id>7</type_id> <value>https://hpp2.local-01.cellpoint.dev/test.php</value> </client_url> </merchant_urls><properties> <property> <id>60</id> <value>true</value> </property> <property> <id>61</id> <value>true</value> </property> </properties></client_configuration>';

        # External Call
        $this->constHTTPClient("/merchantservices/api/Onboarding.php?service=clientconfig&params=client_id/10099",'POST');
        $this->_httpClient->connect();
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(200, $iStatus);

        # Test 1 : Merchant/HPP url
        $res =  $this->queryDB("SELECT callbackurl, cssurl FROM CLIENT.Client_tbl where id = 10099 and callbackurl='https://hpp2.local-01.cellpoint.dev/test.php'" );
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res), 'Error|Merchant/HPP url');

        # Test 2 : Client URL
        $res =  $this->queryDB("select * from client.url_tbl where urltypeid = 15 and clientid = 10099 and url= 'http://mpoint.local.cellpoint.dev/_test/simulators/login.php'" );
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res), 'Error|Client url');

        # Test 2 : Client URL
        $res =  $this->queryDB("select * from client.client_property_tbl where clientid = 10099");
        $this->assertIsResource($res);
        $this->assertEquals(2, pg_num_rows($res), 'Error|Client Property Break');
    }


    /***
     *
     * @api PUT : ClientConfiguration
     *
     * @throws \ErrorException
     * @throws \HTTPConnectionException
     * @throws \HTTPSendException
     */
    public function testSuccessfulPutClientConfiguration()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd, cssurl, callbackurl) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass','https://devcpmassets.s3-ap-southeast-1.amazonaws.com', 'https://hpp2.sit-01.cellpoint.dev/views/callback.php')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");

        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");

        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");

        # RQ Body
        $xml= '<?xml version="1.0" encoding="UTF-8"?> <client_configuration> <client_id>10099</client_id> <id>10077</id> <name>CEBU Pacific Air</name> </client_configuration>';

        # External Call
        $this->constHTTPClient("/merchantservices/api/Onboarding.php?service=clientconfig&params=client_id/10099",'PUT');
        $this->_httpClient->connect();
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $this->assertEquals(200, $iStatus);
    }

    /***
     *
     * @api DELETE : ClientConfiguration
     *
     * @throws \ErrorException
     * @throws \HTTPConnectionException
     * @throws \HTTPSendException
     */
    public function testSuccessfulDeleteClientConfiguration()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd, cssurl, callbackurl) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass','https://devcpmassets.s3-ap-southeast-1.amazonaws.com', 'https://hpp2.sit-01.cellpoint.dev/views/callback.php')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");

        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");

        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");

        $this->queryDB("INSERT INTO client.services_tbl (clientid, dcc_enabled, mcp_enabled, pcc_enabled, fraud_enabled, tokenization_enabled, splitpayment_enabled, callback_enabled, void_enabled, enabled, created, modified) VALUES (10099::integer, DEFAULT, true::boolean, true::boolean, true::boolean, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT);");
        $this->queryDB("INSERT INTO client.pm_tbl (clientid, pmid, enabled, created, modified) VALUES (10099::integer, 1::integer, DEFAULT, DEFAULT, DEFAULT);");
        $this->queryDB("INSERT INTO client.pm_tbl (clientid, pmid, enabled, created, modified) VALUES (10099::integer, 4::integer, DEFAULT, DEFAULT, DEFAULT);");
        $this->queryDB("INSERT INTO client.pm_tbl (clientid, pmid, enabled, created, modified) VALUES (10099::integer, 12::integer, DEFAULT, DEFAULT, DEFAULT);");

        # External Call
        $this->constHTTPClient("/merchantservices/api/Onboarding.php?service=clientconfig&params=client_id/10099/pm/1,4",'DELETE');
        $this->_httpClient->connect();
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'));
        $this->assertEquals(200, $iStatus);

        $res =  $this->queryDB("select * from client.pm_tbl where pmid in (1, 4)");
        # Test 1 : Client PM Table
        $this->assertIsResource($res);
        $this->assertEquals(0, pg_num_rows($res), 'Error | Delete Operation Failed for Payment method against client');
    }
    /***
     *
     * @api DELETE : ClientConfiguration | FAILED
     *
     * @throws \ErrorException
     * @throws \HTTPConnectionException
     * @throws \HTTPSendException
     */
    public function testDeleteClientConfigurationFail()
    {
        $this->bIgnoreErrors = true; //User Error, If Expected entries not found.
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd, cssurl, callbackurl) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass','https://devcpmassets.s3-ap-southeast-1.amazonaws.com', 'https://hpp2.sit-01.cellpoint.dev/views/callback.php')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");

        $this->queryDB("INSERT INTO client.services_tbl (clientid, dcc_enabled, mcp_enabled, pcc_enabled, fraud_enabled, tokenization_enabled, splitpayment_enabled, callback_enabled, void_enabled, enabled, created, modified) VALUES (10099::integer, DEFAULT, true::boolean, true::boolean, true::boolean, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT);");
        $this->queryDB("INSERT INTO client.pm_tbl (clientid, pmid, enabled, created, modified) VALUES (10099::integer, 1::integer, DEFAULT, DEFAULT, DEFAULT);");

        # External Call
        $this->constHTTPClient("/merchantservices/api/Onboarding.php?service=clientconfig&params=client_id/10099/pm/4",'DELETE');
        $this->_httpClient->connect();
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'));
        $this->assertEquals(500, $iStatus);

        # Test 1 : Client PM Table
        $res =  $this->queryDB("select * from client.pm_tbl where pmid in (4)");
        $this->assertIsResource($res);
        $this->assertEquals(0, pg_num_rows($res), 'Error | Failure Delete Operation Failed for Payment method against client');
    }
}