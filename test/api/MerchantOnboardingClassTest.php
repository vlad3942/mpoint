<?php
require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once(sAPI_CLASS_PATH ."simpledom.php");

use api\classes\merchantservices\MerchantConfigInfo;
use api\classes\merchantservices\Repositories\MerchantConfigRepository;
use api\classes\merchantservices\configuration\BaseConfig;
use api\classes\merchantservices\configuration\PropertyInfo;

class MerchantOnboardingClassTest extends baseAPITest
{
    protected $_aMPOINT_CONN_INFO;
    private $_OBJ_DB;
    private $_merchantConfigRepository;
    private $_merchantAggregateRoot;

    public function __construct()
    {
        parent::__construct();

    }

    public function setUp() : void
    {
        parent::setUp(true);
        $this->_OBJ_DB = RDB::produceDatabase($this->mPointDBInfo);
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
    
    public function testMerchantConfigInfoGetAllAddonConfig()
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

        $this->_merchantConfigRepository = new MerchantConfigRepository($this->_OBJ_DB,10099);
        $this->_merchantAggregateRoot = new MerchantConfigInfo();

        $aAddonConf = $this->_merchantAggregateRoot->getAllAddonConfig($this->_merchantConfigRepository);

        $aClassSet = array(
            'DCCConfig' => 1 , 'MCPConfig' => 1 , 'PCCConfig' => 1 ,'FraudConfig' => 1 , 'MPIConfig' => 1 , 'Split_PaymentConfig' => 1 , 'TokenizationConfig' => 1 
        );

        $sprevBaseClass = '';
        foreach($aAddonConf as $config)        
        {
            $baseClass = substr(strrchr('\\'.get_class($config), '\\'), 1);
            if(isset($aClassSet[$baseClass]))
            {                
                unset($aClassSet[$baseClass]);
            } else if($sprevBaseClass !== $baseClass){
                $aClassSet['Unkown'] = 1;
            }
            $sprevBaseClass = $baseClass;            
        }
        $this->assertEquals(0, count($aClassSet));

    }

    public function testMerchantConfigInfoSaveAddonConfig()
    {

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");        

        //<editor-fold desc="Request">
        $xml = '<?xml version="1.0" encoding="UTF-8"?><addon_configuration_request><client_id>10099</client_id><dcc_config><addon_configurations><addon_confguration><pm_id>8</pm_id><currency_id>566</currency_id><country_id>200</country_id></addon_confguration></addon_configurations></dcc_config><mcp_config><addon_configurations><addon_confguration><pm_id>8</pm_id><currency_id>566</currency_id><country_id>200</country_id></addon_confguration></addon_configurations></mcp_config><pcc_config><addon_configurations><addon_confguration><pm_id>8</pm_id><currency_id>608</currency_id><settlement_currency_id>590</settlement_currency_id><is_presentment>true</is_presentment></addon_confguration></addon_configurations></pcc_config><mpi_config><addon_configurations><addon_confguration><pm_id>1</pm_id><provider_id>17</provider_id><version>1.0</version></addon_confguration></addon_configurations></mpi_config><fraud_configs><fraud_config><sub_type>pre_auth</sub_type><addon_configurations><addon_confguration><pm_id>8</pm_id><currency_id>654</currency_id><country_id>640</country_id><provider_id>15</provider_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></fraud_config><fraud_config><sub_type>post_auth</sub_type><addon_configurations><addon_confguration><pm_id>8</pm_id><currency_id>654</currency_id><country_id>640</country_id><provider_id>15</provider_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></fraud_config></fraud_configs><split_payment_configs><split_payment_config><sub_type>hybrid</sub_type><addon_configurations><addon_confguration><sequence_no>1</sequence_no><payment_type_id>1</payment_type_id></addon_confguration><addon_confguration><sequence_no>2</sequence_no><payment_type_id>2</payment_type_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></split_payment_config><split_payment_config><sub_type>cashless</sub_type><addon_configurations><addon_confguration><sequence_no>1</sequence_no><payment_type_id>1</payment_type_id></addon_confguration><addon_confguration><sequence_no>2</sequence_no><payment_type_id>2</payment_type_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></split_payment_config><split_payment_config><sub_type>conventional</sub_type><addon_configurations><addon_confguration><sequence_no>1</sequence_no><payment_type_id>1</payment_type_id></addon_confguration><addon_confguration><sequence_no>2</sequence_no><payment_type_id>2</payment_type_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></split_payment_config></split_payment_configs><tokenization_config><addon_configurations><addon_confguration><pm_id>8</pm_id><currency_id>840</currency_id><country_id>200</country_id><provider_id>15</provider_id></addon_confguration></addon_configurations></tokenization_config></addon_configuration_request>';
        //</editor-fold>

        $obj_DOM = simpledom_load_string($xml);
        $this->_merchantConfigRepository = new MerchantConfigRepository($this->_OBJ_DB,10099);
        $this->_merchantAggregateRoot = new MerchantConfigInfo();

        unset($obj_DOM->client_id);
        $addOnConfig = BaseConfig::produceFromXML($obj_DOM);
        
        $this->_merchantAggregateRoot->saveAddonConfig($this->_merchantConfigRepository,$addOnConfig);

        // DCC
        $res =  $this->queryDB("SELECT id FROM CLIENT.dcc_config_tbl where pmid = 8 AND clientid = 10099 AND countryid = 200 AND currencyid = 566" );
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));

        // MCP
        $res =  $this->queryDB("SELECT id FROM CLIENT.mcp_config_tbl where pmid = 8 AND clientid = 10099 AND countryid = 200 AND currencyid = 566" );
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));

        // Fraud        
        $res =  $this->queryDB("SELECT id FROM CLIENT.fraud_config_tbl where pmid = 8 AND clientid = 10099 AND countryid = 640 AND currencyid = 654 AND providerid = 15 AND  typeoffraud = 1");
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));

        // Tokenization
        $res =  $this->queryDB("SELECT id FROM CLIENT.tokenization_config_tbl where pmid = 8 AND clientid = 10099 AND countryid = 200 AND currencyid = 840 AND providerid = 15");
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));

        // PCC
        $res =  $this->queryDB("SELECT id FROM CLIENT.pcc_config_tbl where pmid = 8 AND clientid = 10099 AND settlement_currency_id = 590 AND sale_currency_id = 608 AND is_presentment = true");
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));

        // MPI
        $res =  $this->queryDB("SELECT id FROM CLIENT.mpi_config_tbl where pmid = 1 AND clientid = 10099 AND providerid = 17 AND \"version\" = '1.0'");
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));

        // Split Configuration
        $res =  $this->queryDB("SELECT split_config_id FROM CLIENT.split_combination_tbl where (split_config_id = 1  AND payment_type = 1 AND sequence_no = 1) OR (split_config_id = 1  AND payment_type = 2 AND sequence_no = 2) OR (split_config_id = 2  AND payment_type = 1 AND sequence_no = 1) OR (split_config_id = 2  AND payment_type = 2 AND sequence_no = 2) OR (split_config_id = 3  AND payment_type = 1 AND sequence_no = 1) OR (split_config_id = 3  AND payment_type = 2 AND sequence_no = 2)");
        $this->assertIsResource($res);
        $this->assertEquals(6, pg_num_rows($res));

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

        //<editor-fold desc="Request">
        $xml = '<?xml version="1.0" encoding="UTF-8"?><addon_configuration_request><client_id>10099</client_id><dcc_config><addon_configurations><addon_confguration><pm_id>8</pm_id><currency_id>567</currency_id><country_id>200</country_id></addon_confguration></addon_configurations></dcc_config><mcp_config><addon_configurations><addon_confguration><pm_id>8</pm_id><currency_id>566</currency_id><country_id>200</country_id></addon_confguration></addon_configurations></mcp_config><pcc_config><addon_configurations><addon_confguration><pm_id>8</pm_id><currency_id>608</currency_id><settlement_currency_id>590</settlement_currency_id><is_presentment>true</is_presentment></addon_confguration></addon_configurations></pcc_config><mpi_config><addon_configurations><addon_confguration><pm_id>1</pm_id><provider_id>17</provider_id><version>1.0</version></addon_confguration></addon_configurations></mpi_config><fraud_configs><fraud_config><sub_type>pre_auth</sub_type><addon_configurations><addon_confguration><pm_id>8</pm_id><currency_id>654</currency_id><country_id>640</country_id><provider_id>15</provider_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></fraud_config><fraud_config><sub_type>post_auth</sub_type><addon_configurations><addon_confguration><pm_id>8</pm_id><currency_id>654</currency_id><country_id>640</country_id><provider_id>15</provider_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></fraud_config></fraud_configs><split_payment_configs><split_payment_config><sub_type>hybrid</sub_type><addon_configurations><addon_confguration><sequence_no>1</sequence_no><payment_type_id>1</payment_type_id></addon_confguration><addon_confguration><sequence_no>2</sequence_no><payment_type_id>2</payment_type_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></split_payment_config><split_payment_config><sub_type>cashless</sub_type><addon_configurations><addon_confguration><sequence_no>1</sequence_no><payment_type_id>1</payment_type_id></addon_confguration><addon_confguration><sequence_no>2</sequence_no><payment_type_id>2</payment_type_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></split_payment_config><split_payment_config><sub_type>conventional</sub_type><addon_configurations><addon_confguration><sequence_no>1</sequence_no><payment_type_id>1</payment_type_id></addon_confguration><addon_confguration><sequence_no>2</sequence_no><payment_type_id>2</payment_type_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></split_payment_config></split_payment_configs><tokenization_config><addon_configurations><addon_confguration><pm_id>8</pm_id><currency_id>840</currency_id><country_id>200</country_id><provider_id>15</provider_id></addon_confguration></addon_configurations></tokenization_config></addon_configuration_request>';
        //</editor-fold>

        $obj_DOM = simpledom_load_string($xml);
        $this->_merchantConfigRepository = new MerchantConfigRepository($this->_OBJ_DB,10099);
        $this->_merchantAggregateRoot = new MerchantConfigInfo();

        unset($obj_DOM->client_id);
        $addOnConfig = BaseConfig::produceFromXML($obj_DOM);
        
        try{
            $this->_merchantAggregateRoot->saveAddonConfig($this->_merchantConfigRepository, $addOnConfig);
        } catch (Exception $e){
            
        }

        // DCC
        $res =  $this->queryDB("SELECT id FROM CLIENT.dcc_config_tbl where pmid = 8 AND clientid = 10099 AND countryid = 200 AND currencyid = 567" );
        $this->assertIsResource($res);
        $this->assertEquals(0, pg_num_rows($res));
    
    }


    public function testSuccessfulUpdateAddOnConfig()
    {

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO client.dcc_config_tbl (pmid, clientid, countryid, currencyid) VALUES(8, 10099, 200, 566)");
        $this->queryDB("INSERT INTO client.mcp_config_tbl (pmid, clientid, countryid, currencyid) VALUES(8, 10099, 200, 566)");
        $this->queryDB("INSERT INTO client.fraud_config_tbl (clientid, pmid, providerid, countryid, currencyid, typeoffraud) VALUES(10099, 8, 15, 640, 654, 1),(10099, 8, 15, 640, 654, 2)");
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


        $xml = '<?xml version="1.0" encoding="UTF-8"?><addon_configuration_request><client_id>10099</client_id><dcc_config><addon_configurations><addon_confguration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>654</currency_id><country_id>640</country_id></addon_confguration></addon_configurations></dcc_config><mcp_config><addon_configurations><addon_confguration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>654</currency_id><country_id>640</country_id></addon_confguration></addon_configurations></mcp_config><pcc_config><addon_configurations><addon_confguration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>654</currency_id><settlement_currency_id>590</settlement_currency_id><is_presentment>false</is_presentment></addon_confguration></addon_configurations></pcc_config><mpi_config><addon_configurations><addon_confguration><id>1</id><enabled>true</enabled><pm_id>1</pm_id><provider_id>1</provider_id><version>1.0</version></addon_confguration></addon_configurations></mpi_config><tokenization_config><addon_configurations><addon_confguration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>566</currency_id><country_id>200</country_id><provider_id>15</provider_id></addon_confguration></addon_configurations></tokenization_config><fraud_configs><fraud_config><sub_type>pre_auth</sub_type><addon_configurations><addon_confguration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>566</currency_id><country_id>200</country_id><provider_id>15</provider_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></fraud_config><fraud_config><sub_type>post_auth</sub_type><addon_configurations><addon_confguration><id>2</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>654</currency_id><country_id>640</country_id><provider_id>15</provider_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></fraud_config></fraud_configs><split_payment_configs><split_payment_config><sub_type>hybrid</sub_type><addon_configurations><addon_confguration><id>1</id><enabled>false</enabled><sequence_no>3</sequence_no><payment_type_id>3</payment_type_id></addon_confguration><addon_confguration><id>2</id><enabled>false</enabled><sequence_no>4</sequence_no><payment_type_id>4</payment_type_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></split_payment_config><split_payment_config><sub_type>cashless</sub_type><addon_configurations><addon_confguration><id>3</id><enabled>false</enabled><sequence_no>3</sequence_no><payment_type_id>3</payment_type_id></addon_confguration><addon_confguration><id>4</id><enabled>false</enabled><sequence_no>4</sequence_no><payment_type_id>4</payment_type_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></split_payment_config><split_payment_config><sub_type>conventional</sub_type><addon_configurations><addon_confguration><id>5</id><enabled>false</enabled><sequence_no>3</sequence_no><payment_type_id>3</payment_type_id></addon_confguration><addon_confguration><id>6</id><enabled>false</enabled><sequence_no>4</sequence_no><payment_type_id>4</payment_type_id></addon_confguration></addon_configurations><is_rollback>true</is_rollback></split_payment_config></split_payment_configs></addon_configuration_request>';

        $obj_DOM = simpledom_load_string($xml);
        $this->_merchantConfigRepository = new MerchantConfigRepository($this->_OBJ_DB,10099);
        $this->_merchantAggregateRoot = new MerchantConfigInfo();

        unset($obj_DOM->client_id);
        $addOnConfig = BaseConfig::produceFromXML($obj_DOM);
        
        $this->_merchantAggregateRoot->updateAddonConfig($this->_merchantConfigRepository,$addOnConfig);

        // DCC
        $res =  $this->queryDB("SELECT id FROM CLIENT.dcc_config_tbl where pmid = 8 AND clientid = 10099 AND countryid = 640 AND currencyid = 654" );
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));

        // MCP
        $res =  $this->queryDB("SELECT id FROM CLIENT.mcp_config_tbl where pmid = 8 AND clientid = 10099 AND countryid = 640 AND currencyid = 654");
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));

        // PCC
        $res =  $this->queryDB("SELECT id FROM CLIENT.pcc_config_tbl where pmid = 8 AND clientid = 10099 AND settlement_currency_id = 590 AND sale_currency_id = 654 AND is_presentment = false");
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));

        // Fraud        
        $res =  $this->queryDB("SELECT id FROM CLIENT.fraud_config_tbl where pmid = 8 AND clientid = 10099 AND countryid = 200 AND currencyid = 566 AND providerid = 15 AND  typeoffraud = 1");
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));

        // Tokenization
        $res =  $this->queryDB("SELECT id FROM CLIENT.tokenization_config_tbl where pmid = 8 AND clientid = 10099 AND countryid = 200 AND currencyid = 566 AND providerid = 15");
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));


        // MPI
        $res =  $this->queryDB("SELECT id FROM CLIENT.mpi_config_tbl where pmid = 1 AND clientid = 10099 AND providerid = 1 AND \"version\" = '1.0'");
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));

        
        // Split Configuration
        $res =  $this->queryDB("SELECT split_config_id FROM CLIENT.split_combination_tbl where (split_config_id = 1  AND payment_type = 3 AND sequence_no = 3) OR (split_config_id = 1  AND payment_type = 4 AND sequence_no = 4) OR (split_config_id = 2  AND payment_type = 3 AND sequence_no = 3) OR (split_config_id = 2  AND payment_type = 4 AND sequence_no = 4) OR (split_config_id = 3  AND payment_type = 3 AND sequence_no = 3) OR (split_config_id = 3  AND payment_type = 4 AND sequence_no = 4)");
        $this->assertIsResource($res);
        $this->assertEquals(6, pg_num_rows($res));


    }

    public function testSuccessfulDeleteAddOnConfig()
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
        
        $additionalParams = array(
            'client_id' => 10099,
            'dcc' => 1,
            'mcp' => 1,
            'pcc' => 1,
            'mpi' => 1,
            'tokenization' => 1,
            'fraud' => 1,
            'split_payment' => '1,2,3,4,5,6'
        );

        $this->_merchantConfigRepository = new MerchantConfigRepository($this->_OBJ_DB,10099);
        $this->_merchantAggregateRoot = new MerchantConfigInfo();        
        
        $this->_merchantAggregateRoot->deleteAddonConfig($this->_merchantConfigRepository,$additionalParams);

        // DCC
        $res =  $this->queryDB("SELECT id FROM CLIENT.dcc_config_tbl where pmid = 8 AND clientid = 10099 AND countryid = 200 AND currencyid = 566" );
        $this->assertIsResource($res);
        $this->assertEquals(0, pg_num_rows($res));

        // MCP
        $res =  $this->queryDB("SELECT id FROM CLIENT.mcp_config_tbl where pmid = 8 AND clientid = 10099 AND countryid = 200 AND currencyid = 566" );
        $this->assertIsResource($res);
        $this->assertEquals(0, pg_num_rows($res));

        // Fraud        
        $res =  $this->queryDB("SELECT id FROM CLIENT.fraud_config_tbl where pmid = 8 AND clientid = 10099 AND countryid = 640 AND currencyid = 654 AND providerid = 15 AND  typeoffraud = 1");
        $this->assertIsResource($res);
        $this->assertEquals(0, pg_num_rows($res));

        // Tokenization
        $res =  $this->queryDB("SELECT id FROM CLIENT.tokenization_config_tbl where pmid = 8 AND clientid = 10099 AND countryid = 200 AND currencyid = 840 AND providerid = 15");
        $this->assertIsResource($res);
        $this->assertEquals(0, pg_num_rows($res));

        // PCC
        $res =  $this->queryDB("SELECT id FROM CLIENT.pcc_config_tbl where pmid = 8 AND clientid = 10099 AND settlement_currency_id = 590 AND sale_currency_id = 608 AND is_presentment = true");
        $this->assertIsResource($res);
        $this->assertEquals(0, pg_num_rows($res));

        // MPI
        $res =  $this->queryDB("SELECT id FROM CLIENT.mpi_config_tbl where pmid = 1 AND clientid = 10099 AND providerid = 17 AND \"version\" = '1.0'");
        $this->assertIsResource($res);
        $this->assertEquals(0, pg_num_rows($res));

        // Split Configuration
        $res =  $this->queryDB("SELECT split_config_id FROM CLIENT.split_combination_tbl where (split_config_id = 1  AND payment_type = 1 AND sequence_no = 1) OR (split_config_id = 1  AND payment_type = 2 AND sequence_no = 2) OR (split_config_id = 2  AND payment_type = 1 AND sequence_no = 1) OR (split_config_id = 2  AND payment_type = 2 AND sequence_no = 2) OR (split_config_id = 3  AND payment_type = 1 AND sequence_no = 1) OR (split_config_id = 3  AND payment_type = 2 AND sequence_no = 2)");
        $this->assertIsResource($res);
        $this->assertEquals(0, pg_num_rows($res));

    }
    
    public function testSuccessfulGetPSPProperty()
    {

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.psp_property_tbl (clientid,propertyid,value) VALUES ( 10099,(select ID from system.psp_property_tbl where name='FILE_EXPIRY' AND PSPID=52),'CPD_')");
        $this->queryDB("INSERT INTO Client.psp_property_tbl (clientid,propertyid,value) VALUES ( 10099,(select ID from system.psp_property_tbl where name='IS_TICKET_LEVEL_SETTLEMENT' AND PSPID=52),'true')");

        $this->_merchantConfigRepository = new MerchantConfigRepository($this->_OBJ_DB,10099);
        $this->_merchantAggregateRoot = new MerchantConfigInfo();  

        $arrPSPConfig = $this->_merchantAggregateRoot->getPropertyConfig($this->_merchantConfigRepository,'PSP','ALL',52);
        $arrResult = array_filter($arrPSPConfig['Technical'], function ($psp) {
            return (($psp->getValue() === 'CPD_' && $psp->getName() === 'FILE_EXPIRY') || ($psp->getValue() === 'true' && $psp->getName() === 'IS_TICKET_LEVEL_SETTLEMENT') );
        });

        $this->assertEquals(2, count($arrResult));

    }

    public function testSuccessfulSavePSPProperty()
    {

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");

        $this->_merchantConfigRepository = new MerchantConfigRepository($this->_OBJ_DB,10099);
        $this->_merchantAggregateRoot = new MerchantConfigInfo();          

        $xml = '<?xml version="1.0" encoding="UTF-8"?><client_psp_configuration><client_id>10099</client_id><psp_id>52</psp_id><properties><property><id>22</id><value>true</value></property><property><id>21</id><value>CPD_</value></property></properties></client_psp_configuration>';

        $obj_DOM = simpledom_load_string($xml);
        $aPropertyInfo = [];

        foreach ($obj_DOM->properties->property as $property)  array_push($aPropertyInfo,PropertyInfo::produceFromXML($property));

        $this->_merchantAggregateRoot->savePropertyConfig($this->_merchantConfigRepository, 'PSP',$aPropertyInfo,52);

        $res =  $this->queryDB("SELECT id FROM CLIENT.psp_property_tbl where value in ('CPD_','true')" );
        $this->assertIsResource($res);
        $this->assertEquals(2, pg_num_rows($res));

    }

    public function testSuccessfulUpdatePSPProperty()
    {

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.psp_property_tbl (clientid,propertyid,value) VALUES ( 10099,(select ID from system.psp_property_tbl where name='FILE_EXPIRY' AND PSPID=52),'CPD_')");
        $this->queryDB("INSERT INTO Client.psp_property_tbl (clientid,propertyid,value) VALUES ( 10099,(select ID from system.psp_property_tbl where name='IS_TICKET_LEVEL_SETTLEMENT' AND PSPID=52),'true')");

        $this->_merchantConfigRepository = new MerchantConfigRepository($this->_OBJ_DB,10099);
        $this->_merchantAggregateRoot = new MerchantConfigInfo();    
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?><client_psp_configuration><client_id>10099</client_id><psp_id>52</psp_id><properties><property><id>22</id><value>true</value><enabled>true</enabled></property><property><id>21</id><value>CPD_123</value><enabled>true</enabled></property></properties></client_psp_configuration>';

        $obj_DOM = simpledom_load_string($xml);
        $aPropertyInfo = [];

        foreach ($obj_DOM->properties->property as $property)  array_push($aPropertyInfo,PropertyInfo::produceFromXML($property));        

        $this->_merchantAggregateRoot->updatePropertyConfig($this->_merchantConfigRepository, 'PSP',$aPropertyInfo,52);        
        $res =  $this->queryDB("SELECT id FROM CLIENT.psp_property_tbl where value in ('CPD_123','true')" );
        $this->assertIsResource($res);
        $this->assertEquals(2, pg_num_rows($res));

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

        $this->_merchantConfigRepository = new MerchantConfigRepository($this->_OBJ_DB,10099);
        $this->_merchantAggregateRoot = new MerchantConfigInfo();

        $additionalParams = array(
            'client_id' => 10099,
            'p_id' => 21
        );

        $this->_merchantAggregateRoot->deletePropertyConfig($this->_merchantConfigRepository,'PSP',$additionalParams,52);
        $res =  $this->queryDB("SELECT id FROM CLIENT.psp_property_tbl where value in ('CPD_')" );
        $this->assertIsResource($res);
        $this->assertEquals(0, pg_num_rows($res));
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

        $this->_merchantConfigRepository = new MerchantConfigRepository($this->_OBJ_DB,10099);
        $this->_merchantAggregateRoot = new MerchantConfigInfo();  

        $arrRouteConfig = $this->_merchantAggregateRoot->getPropertyConfig($this->_merchantConfigRepository,'ROUTE','ALL',1);
        $arrResult = array_filter($arrRouteConfig['Basic'], function ($psp) {
            return (($psp->getValue() === '1234' && $psp->getName() === 'CeptorAccessId') || ($psp->getValue() === '1233' && $psp->getName() === 'CeptorAccessKey') );
        });

        $this->assertEquals(2, count($arrResult));
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

        $this->_merchantConfigRepository = new MerchantConfigRepository($this->_OBJ_DB,10099);
        $this->_merchantAggregateRoot = new MerchantConfigInfo();  

        $xml= '<?xml version="1.0" encoding="UTF-8"?><client_route_configuration><client_id>10099</client_id><route_config_id>1</route_config_id><properties><property><id>41</id><value>1234</value></property><property><id>42</id><value>1233</value></property></properties><pm_configurations><pm_configuration><pm_id>8</pm_id></pm_configuration><pm_configuration><pm_id>7</pm_id></pm_configuration></pm_configurations></client_route_configuration>';

        $obj_DOM = simpledom_load_string($xml);
        $aPropertyInfo = [];
        $aPMIds = [];


        foreach ($obj_DOM->properties->property as $property)
        {
            array_push($aPropertyInfo,PropertyInfo::produceFromXML($property));
        }
        $aPMIds = array();
        if(count($obj_DOM->pm_configurations)>0)
        {
            foreach ($obj_DOM->pm_configurations->pm_configuration as $pm_configuration)
            {
                array_push($aPMIds, (int)$pm_configuration->pm_id);
            }
        }

        $this->_merchantAggregateRoot->savePropertyConfig($this->_merchantConfigRepository, 'ROUTE',$aPropertyInfo,1,$aPMIds);
        
        $res =  $this->queryDB("SELECT id FROM CLIENT.route_property_tbl where value in ('1234','1233')" );
        $this->assertIsResource($res);
        $this->assertEquals(2, pg_num_rows($res));
        $res =  $this->queryDB("SELECT id FROM CLIENT.routepm_tbl" );
        $this->assertIsResource($res);
        $this->assertEquals(2, pg_num_rows($res));
    }

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

        $xml= '<?xml version="1.0" encoding="UTF-8"?><client_route_configuration><client_id>10099</client_id><route_config_id>1</route_config_id><properties><property><id>41</id><value>1231</value><enabled>true</enabled></property><property><id>42</id><value>1232</value><enabled>true</enabled></property></properties><pm_configurations><pm_configuration><pm_id>8</pm_id><enabled>true</enabled></pm_configuration><pm_configuration><pm_id>7</pm_id><enabled>true</enabled></pm_configuration></pm_configurations></client_route_configuration>';

        $this->_merchantConfigRepository = new MerchantConfigRepository($this->_OBJ_DB,10099);
        $this->_merchantAggregateRoot = new MerchantConfigInfo(); 
        $obj_DOM = simpledom_load_string($xml);
        $aPropertyInfo = [];
        $aPMIds = [];


        foreach ($obj_DOM->properties->property as $property)
        {
            array_push($aPropertyInfo,PropertyInfo::produceFromXML($property));
        }
        $aPMIds = array();
        foreach ($obj_DOM->pm_configurations->pm_configuration as $pm_configuration)
        {
            array_push($aPMIds,array((int)$pm_configuration->pm_id,(string)$pm_configuration->enabled));
        }

        $this->_merchantAggregateRoot->updatePropertyConfig($this->_merchantConfigRepository, 'ROUTE',$aPropertyInfo,1,$aPMIds);        

        $res =  $this->queryDB("SELECT id FROM CLIENT.route_property_tbl where value in ('1232','1231')" );
        $this->assertIsResource($res);
        $this->assertEquals(2, pg_num_rows($res));
        $res =  $this->queryDB("SELECT id FROM CLIENT.routepm_tbl" );
        $this->assertIsResource($res);
        $this->assertEquals(2, pg_num_rows($res));

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

        $this->_merchantConfigRepository = new MerchantConfigRepository($this->_OBJ_DB,10099);
        $this->_merchantAggregateRoot = new MerchantConfigInfo();  
        $additionalParams = array(
            'client_id' => 10099,
            'p_id' => 41
        );

        $this->_merchantAggregateRoot->deletePropertyConfig($this->_merchantConfigRepository, 'ROUTE',$additionalParams,1);
        $res =  $this->queryDB("SELECT id FROM CLIENT.route_property_tbl where value in ('1234')" );
        $this->assertIsResource($res);
        $this->assertEquals(0, pg_num_rows($res));

    }
    
}