<?php
require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once(sAPI_CLASS_PATH ."simpledom.php");

use api\classes\merchantservices\MerchantConfigInfo;
use api\classes\merchantservices\Repositories\MerchantConfigRepository;
use api\classes\merchantservices\configuration\BaseConfig;
use api\classes\merchantservices\Services\ConfigurationService;
use api\classes\merchantservices\Controllers\ConfigurationController;
use api\classes\merchantservices\Repositories\ReadOnlyConfigRepository;
use api\classes\merchantservices\configuration\AddonServiceType;

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
        $this->queryDB("INSERT INTO client.mpi_config_tbl (clientid, pmid, providerid) VALUES(10099, 1, 17)");
        $this->queryDB("INSERT INTO client.mpi_property_tbl (clientid, version) VALUES(10099,'1.0')");
        $this->queryDB("INSERT INTO client.pcc_config_tbl (clientid, pmid, sale_currency_id, is_presentment, settlement_currency_id) VALUES(10099, 8, 608, true, 590)");
        $this->queryDB("INSERT INTO client.split_configuration_tbl (id, client_id, name, is_one_step_auth,type) VALUES(1, 10099, 'hybrid', true,'hybrid')");
        $this->queryDB("INSERT INTO client.split_configuration_tbl (id, client_id, name, is_one_step_auth,type) VALUES(2, 10099, 'cashless', false,'cashless')");
        $this->queryDB("INSERT INTO client.split_configuration_tbl (id, client_id, name, is_one_step_auth,type) VALUES(3, 10099, 'conventional', false,'conventional')");

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
        $xml = '<?xml version="1.0" encoding="UTF-8"?><addon_configuration_request><client_id>10099</client_id><dcc_config><addon_configurations><addon_configuration><pm_id>8</pm_id><currency_id>566</currency_id><country_id>200</country_id></addon_configuration></addon_configurations></dcc_config><mcp_config><addon_configurations><addon_configuration><pm_id>8</pm_id><currency_id>566</currency_id><country_id>200</country_id></addon_configuration></addon_configurations></mcp_config><pcc_config><addon_configurations><addon_configuration><pm_id>8</pm_id><currency_id>608</currency_id><settlement_currency_id>590</settlement_currency_id><is_presentment>true</is_presentment></addon_configuration></addon_configurations></pcc_config><mpi_config><addon_configurations><addon_configuration><pm_id>1</pm_id><provider_id>17</provider_id></addon_configuration></addon_configurations><version>1.0</version></mpi_config><fraud_configs><fraud_config><sub_type>pre_auth</sub_type><addon_configurations><addon_configuration><pm_id>8</pm_id><currency_id>654</currency_id><country_id>640</country_id><provider_id>15</provider_id></addon_configuration></addon_configurations><is_rollback>true</is_rollback></fraud_config><fraud_config><sub_type>post_auth</sub_type><addon_configurations><addon_configuration><pm_id>8</pm_id><currency_id>654</currency_id><country_id>640</country_id><provider_id>15</provider_id></addon_configuration></addon_configurations><is_rollback>true</is_rollback></fraud_config></fraud_configs><split_payment_configs><split_payment_config><sub_type>hybrid</sub_type><name>hybrid</name><addon_configurations><addon_configuration><sequence_no>1</sequence_no><payment_type_id>1</payment_type_id></addon_configuration><addon_configuration><sequence_no>2</sequence_no><payment_type_id>2</payment_type_id></addon_configuration></addon_configurations><is_rollback>true</is_rollback></split_payment_config><split_payment_config><sub_type>cashless</sub_type><name>cashless</name><addon_configurations><addon_configuration><sequence_no>1</sequence_no><payment_type_id>1</payment_type_id></addon_configuration><addon_configuration><sequence_no>2</sequence_no><payment_type_id>2</payment_type_id></addon_configuration></addon_configurations><is_rollback>true</is_rollback></split_payment_config><split_payment_config><sub_type>conventional</sub_type><name>conventional</name><addon_configurations><addon_configuration><sequence_no>1</sequence_no><payment_type_id>1</payment_type_id></addon_configuration><addon_configuration><sequence_no>2</sequence_no><payment_type_id>2</payment_type_id></addon_configuration></addon_configurations><is_rollback>true</is_rollback></split_payment_config></split_payment_configs><tokenization_config><addon_configurations><addon_configuration><pm_id>8</pm_id><currency_id>840</currency_id><country_id>200</country_id><provider_id>15</provider_id></addon_configuration></addon_configurations></tokenization_config></addon_configuration_request>';
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
        $res =  $this->queryDB("SELECT id FROM CLIENT.mpi_config_tbl where pmid = 1 AND clientid = 10099 AND providerid = 17");
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
        $this->queryDB("INSERT INTO client.mpi_config_tbl (clientid, pmid, providerid) VALUES(10099, 1, 17)");
        $this->queryDB("INSERT INTO client.mpi_property_tbl (clientid, version) VALUES(10099,'1.0')");
        $this->queryDB("INSERT INTO client.pcc_config_tbl (clientid, pmid, sale_currency_id, is_presentment, settlement_currency_id) VALUES(10099, 8, 608, true, 590)");
        $this->queryDB("INSERT INTO client.split_configuration_tbl (id, client_id, name, is_one_step_auth,type) VALUES(1, 10099, 'hybrid', true,'hybrid')");
        $this->queryDB("INSERT INTO client.split_configuration_tbl (id, client_id, name, is_one_step_auth,type) VALUES(2, 10099, 'cashless', false,'cashless')");
        $this->queryDB("INSERT INTO client.split_configuration_tbl (id, client_id, name, is_one_step_auth,type) VALUES(3, 10099, 'conventional', false,'conventional')");

        $this->queryDB("INSERT INTO client.split_combination_tbl (split_config_id, payment_type, sequence_no) VALUES(1, 1, 1)");
        $this->queryDB("INSERT INTO client.split_combination_tbl (split_config_id, payment_type, sequence_no) VALUES(1, 2, 2)");
        $this->queryDB("INSERT INTO client.split_combination_tbl (split_config_id, payment_type, sequence_no) VALUES(2, 1, 1)");
        $this->queryDB("INSERT INTO client.split_combination_tbl (split_config_id, payment_type, sequence_no) VALUES(2, 2, 2)");
        $this->queryDB("INSERT INTO client.split_combination_tbl (split_config_id, payment_type, sequence_no) VALUES(3, 1, 1)");
        $this->queryDB("INSERT INTO client.split_combination_tbl (split_config_id, payment_type, sequence_no) VALUES(3, 2, 2)");

        $this->queryDB("INSERT INTO client.fraud_property_tbl (clientid,is_rollback) VALUES(10099,true)");
        $this->queryDB("INSERT INTO client.split_property_tbl (clientid,is_rollback) VALUES(10099,true)");

        //<editor-fold desc="Request">
        $xml = '<?xml version="1.0" encoding="UTF-8"?><addon_configuration_request><client_id>10099</client_id><dcc_config><addon_configurations><addon_configuration><pm_id>8</pm_id><currency_id>566</currency_id><country_id>200</country_id></addon_configuration></addon_configurations></dcc_config><mcp_config><addon_configurations><addon_configuration><pm_id>8</pm_id><currency_id>566</currency_id><country_id>200</country_id></addon_configuration></addon_configurations></mcp_config><pcc_config><addon_configurations><addon_configuration><pm_id>8</pm_id><currency_id>608</currency_id><settlement_currency_id>590</settlement_currency_id><is_presentment>true</is_presentment></addon_configuration></addon_configurations></pcc_config><mpi_config><addon_configurations><addon_configuration><pm_id>1</pm_id><provider_id>17</provider_id></addon_configuration></addon_configurations><version>1.0</version></mpi_config><fraud_configs><fraud_config><sub_type>pre_auth</sub_type><addon_configurations><addon_configuration><pm_id>8</pm_id><currency_id>654</currency_id><country_id>640</country_id><provider_id>15</provider_id></addon_configuration></addon_configurations><is_rollback>true</is_rollback></fraud_config><fraud_config><sub_type>post_auth</sub_type><addon_configurations><addon_configuration><pm_id>8</pm_id><currency_id>654</currency_id><country_id>640</country_id><provider_id>15</provider_id></addon_configuration></addon_configurations><is_rollback>true</is_rollback></fraud_config></fraud_configs><split_payment_configs><split_payment_config><sub_type>hybrid</sub_type><name>hybrid</name><addon_configurations><addon_configuration><sequence_no>1</sequence_no><payment_type_id>1</payment_type_id></addon_configuration><addon_configuration><sequence_no>2</sequence_no><payment_type_id>2</payment_type_id></addon_configuration></addon_configurations><is_rollback>true</is_rollback></split_payment_config><split_payment_config><sub_type>cashless</sub_type><name>cashless</name><addon_configurations><addon_configuration><sequence_no>1</sequence_no><payment_type_id>1</payment_type_id></addon_configuration><addon_configuration><sequence_no>2</sequence_no><payment_type_id>2</payment_type_id></addon_configuration></addon_configurations><is_rollback>true</is_rollback></split_payment_config><split_payment_config><sub_type>conventional</sub_type><name>conventional</name><addon_configurations><addon_configuration><sequence_no>1</sequence_no><payment_type_id>1</payment_type_id></addon_configuration><addon_configuration><sequence_no>2</sequence_no><payment_type_id>2</payment_type_id></addon_configuration></addon_configurations><is_rollback>true</is_rollback></split_payment_config></split_payment_configs><tokenization_config><addon_configurations><addon_configuration><pm_id>8</pm_id><currency_id>840</currency_id><country_id>200</country_id><provider_id>15</provider_id></addon_configuration></addon_configurations></tokenization_config></addon_configuration_request>';
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
        $this->queryDB("INSERT INTO client.mpi_config_tbl (clientid, pmid, providerid) VALUES(10099, 1, 17)");
        $this->queryDB("INSERT INTO client.mpi_property_tbl (clientid, version) VALUES(10099,'1.0')");
        $this->queryDB("INSERT INTO client.pcc_config_tbl (clientid, pmid, sale_currency_id, is_presentment, settlement_currency_id) VALUES(10099, 8, 608, true, 590)");
        $this->queryDB("INSERT INTO client.split_configuration_tbl (id, client_id, name, is_one_step_auth,type) VALUES(1, 10099, 'hybrid', true,'hybrid')");
        $this->queryDB("INSERT INTO client.split_configuration_tbl (id, client_id, name, is_one_step_auth,type) VALUES(2, 10099, 'cashless', false,'cashless')");
        $this->queryDB("INSERT INTO client.split_configuration_tbl (id, client_id, name, is_one_step_auth,type) VALUES(3, 10099, 'conventional', false,'conventional')");

        $this->queryDB("INSERT INTO client.split_combination_tbl (split_config_id, payment_type, sequence_no) VALUES(1, 1, 1)");
        $this->queryDB("INSERT INTO client.split_combination_tbl (split_config_id, payment_type, sequence_no) VALUES(1, 2, 2)");
        $this->queryDB("INSERT INTO client.split_combination_tbl (split_config_id, payment_type, sequence_no) VALUES(2, 1, 1)");
        $this->queryDB("INSERT INTO client.split_combination_tbl (split_config_id, payment_type, sequence_no) VALUES(2, 2, 2)");
        $this->queryDB("INSERT INTO client.split_combination_tbl (split_config_id, payment_type, sequence_no) VALUES(3, 1, 1)");
        $this->queryDB("INSERT INTO client.split_combination_tbl (split_config_id, payment_type, sequence_no) VALUES(3, 2, 2)");

        $this->queryDB("INSERT INTO client.fraud_property_tbl (clientid,is_rollback) VALUES(10099,true)");
        $this->queryDB("INSERT INTO client.split_property_tbl (clientid,is_rollback) VALUES(10099,true)");


        $xml = '<?xml version="1.0" encoding="UTF-8"?><addon_configuration_request><client_id>10099</client_id><dcc_config><addon_configurations><addon_configuration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>654</currency_id><country_id>640</country_id></addon_configuration></addon_configurations></dcc_config><mcp_config><addon_configurations><addon_configuration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>654</currency_id><country_id>640</country_id></addon_configuration></addon_configurations></mcp_config><pcc_config><addon_configurations><addon_configuration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>654</currency_id><settlement_currency_id>590</settlement_currency_id><is_presentment>false</is_presentment></addon_configuration></addon_configurations></pcc_config><mpi_config><addon_configurations><addon_configuration><id>1</id><enabled>true</enabled><pm_id>1</pm_id><provider_id>1</provider_id></addon_configuration></addon_configurations><version>1.0</version></mpi_config><tokenization_config><addon_configurations><addon_configuration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>566</currency_id><country_id>200</country_id><provider_id>15</provider_id></addon_configuration></addon_configurations></tokenization_config><fraud_configs><fraud_config><sub_type>pre_auth</sub_type><addon_configurations><addon_configuration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>566</currency_id><country_id>200</country_id><provider_id>15</provider_id></addon_configuration></addon_configurations><is_rollback>true</is_rollback></fraud_config><fraud_config><sub_type>post_auth</sub_type><addon_configurations><addon_configuration><id>2</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>654</currency_id><country_id>640</country_id><provider_id>15</provider_id></addon_configuration></addon_configurations><is_rollback>true</is_rollback></fraud_config></fraud_configs><split_payment_configs><split_payment_config><sub_type>hybrid</sub_type><name>hybrid</name><addon_configurations><addon_configuration><id>1</id><enabled>false</enabled><sequence_no>3</sequence_no><payment_type_id>3</payment_type_id></addon_configuration><addon_configuration><id>2</id><enabled>false</enabled><sequence_no>4</sequence_no><payment_type_id>4</payment_type_id></addon_configuration></addon_configurations><is_rollback>true</is_rollback></split_payment_config><split_payment_config><sub_type>cashless</sub_type><name>cashless</name><addon_configurations><addon_configuration><id>3</id><enabled>false</enabled><sequence_no>3</sequence_no><payment_type_id>3</payment_type_id></addon_configuration><addon_configuration><id>4</id><enabled>false</enabled><sequence_no>4</sequence_no><payment_type_id>4</payment_type_id></addon_configuration></addon_configurations><is_rollback>true</is_rollback></split_payment_config><split_payment_config><sub_type>conventional</sub_type><name>conventional</name><addon_configurations><addon_configuration><id>5</id><enabled>false</enabled><sequence_no>3</sequence_no><payment_type_id>3</payment_type_id></addon_configuration><addon_configuration><id>6</id><enabled>false</enabled><sequence_no>4</sequence_no><payment_type_id>4</payment_type_id></addon_configuration></addon_configurations><is_rollback>true</is_rollback></split_payment_config></split_payment_configs></addon_configuration_request>';

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
        $res =  $this->queryDB("SELECT id FROM CLIENT.mpi_config_tbl where pmid = 1 AND clientid = 10099 AND providerid = 1");
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
        $this->queryDB("INSERT INTO client.mpi_config_tbl (clientid, pmid, providerid) VALUES(10099, 1, 17)");
        $this->queryDB("INSERT INTO client.mpi_property_tbl (clientid, version) VALUES(10099,'1.0')");
        $this->queryDB("INSERT INTO client.pcc_config_tbl (clientid, pmid, sale_currency_id, is_presentment, settlement_currency_id) VALUES(10099, 8, 608, true, 590)");
        $this->queryDB("INSERT INTO client.split_configuration_tbl (id, client_id, name, is_one_step_auth,type) VALUES(1, 10099, 'hybrid', true,'hybrid')");
        $this->queryDB("INSERT INTO client.split_configuration_tbl (id, client_id, name, is_one_step_auth,type) VALUES(2, 10099, 'cashless', false,'cashless')");
        $this->queryDB("INSERT INTO client.split_configuration_tbl (id, client_id, name, is_one_step_auth,type) VALUES(3, 10099, 'conventional', false,'conventional')");

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
        $res =  $this->queryDB("SELECT id FROM CLIENT.mpi_config_tbl where pmid = 1 AND clientid = 10099 AND providerid = 17");
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
        $this->queryDB("insert into Client.merchantaccount_tbl (clientid, pspid, name, username, passwd) values (10099, 52, 'TestPSPName','TestPSPUser','TestPSPPass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.psp_property_tbl (clientid,propertyid,value) VALUES ( 10099,(select ID from system.psp_property_tbl where name='FILE_EXPIRY' AND PSPID=52),'CPD_')");
        $this->queryDB("INSERT INTO Client.psp_property_tbl (clientid,propertyid,value) VALUES ( 10099,(select ID from system.psp_property_tbl where name='IS_TICKET_LEVEL_SETTLEMENT' AND PSPID=52),'true')");
        $this->queryDB("INSERT INTO Client.route_tbl (id, clientid, providerid) VALUES (1, 10099, 52)");
        $this->queryDB("insert into Client.providerpm_tbl (routeid, pmid) values (1, 1)");

        $this->_merchantConfigRepository = new MerchantConfigRepository($this->_OBJ_DB,10099);
        $this->_merchantAggregateRoot = new MerchantConfigInfo();  

        $arrPSPConfig = $this->_merchantAggregateRoot->getPropertyConfig($this->_merchantConfigRepository,'PSP','ALL',52);
        $arrResult = array_filter($arrPSPConfig['Technical'], function ($psp) {
            return (($psp->getValue() === 'CPD_' && $psp->getName() === 'FILE_EXPIRY') || ($psp->getValue() === 'true' && $psp->getName() === 'IS_TICKET_LEVEL_SETTLEMENT') );
        });
        $this->assertEquals(2, count($arrResult));

        $res =  $this->queryDB("SELECT id FROM CLIENT.providerpm_tbl where pmid in (1)" );
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));

        $res =  $this->queryDB("SELECT id FROM CLIENT.merchantaccount_tbl where clientid = 10099 AND pspid =  52 AND name = 'TestPSPName' AND username = 'TestPSPUser' AND passwd = 'TestPSPPass'" );
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));
    }

    public function testSuccessfulSavePSPProperty()
    {

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");


        $xml = '<?xml version="1.0" encoding="UTF-8"?><client_psp_configuration><client_id>10099</client_id><psp_id>52</psp_id><name>TestPSPName</name><credentials><username>TestPSPUser</username><password>TestPSPPass</password></credentials><properties><property><id>22</id><value>true</value></property><property><id>21</id><value>CPD_</value></property></properties><pm_configurations><pm_configuration><pm_id>1</pm_id></pm_configuration></pm_configurations></client_psp_configuration>';

        $obj_DOM = simpledom_load_string($xml);
        $objController = new ConfigurationController($this->_OBJ_DB,10099);
        $objController->savePSPConfig($obj_DOM);

        $res =  $this->queryDB("SELECT id FROM CLIENT.psp_property_tbl where value in ('CPD_','true')" );
        $this->assertIsResource($res);
        $this->assertEquals(2, pg_num_rows($res));

        $res =  $this->queryDB("SELECT id FROM CLIENT.providerpm_tbl where pmid in (1)" );
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));

        $res =  $this->queryDB("SELECT id FROM CLIENT.merchantaccount_tbl where clientid = 10099 AND pspid =  52 AND name = 'TestPSPName' AND username = 'TestPSPUser' AND passwd = 'TestPSPPass'" );
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));

    }

    public function testSuccessfulUpdatePSPProperty()
    {

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("insert into Client.merchantaccount_tbl (clientid, pspid, name, username, passwd) values (10099, 52, 'TestPSPName','TestPSPUser','TestPSPPass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.psp_property_tbl (clientid,propertyid,value) VALUES ( 10099,(select ID from system.psp_property_tbl where name='FILE_EXPIRY' AND PSPID=52),'CPD_')");
        $this->queryDB("INSERT INTO Client.psp_property_tbl (clientid,propertyid,value) VALUES ( 10099,(select ID from system.psp_property_tbl where name='IS_TICKET_LEVEL_SETTLEMENT' AND PSPID=52),'true')");
        $this->queryDB("INSERT INTO Client.route_tbl (id, clientid, providerid) VALUES (1, 10099, 52)");
        $this->queryDB("insert into Client.providerpm_tbl (routeid, pmid) values (1, 1)");


        $xml = '<?xml version="1.0" encoding="UTF-8"?><client_psp_configuration><client_id>10099</client_id><psp_id>52</psp_id><name>EFS10000114912</name><credentials><username>Paymaya ac1q2</username><password>sk-aXQdorOOF0zGMfyVAzTH9CbAFvqq1Oc7PAXcDlrz5z</password></credentials><properties><property><id>22</id><value>true</value><enabled>true</enabled></property><property><id>21</id><value>CPD_123</value><enabled>true</enabled></property></properties><pm_configurations><pm_configuration><pm_id>1</pm_id><enabled>false</enabled></pm_configuration></pm_configurations></client_psp_configuration>';

        $obj_DOM = simpledom_load_string($xml);
        $objController = new ConfigurationController($this->_OBJ_DB,10099);
        $objController->updatePSPConfig($obj_DOM);

        $res =  $this->queryDB("SELECT id FROM CLIENT.psp_property_tbl where value in ('CPD_123','true')" );
        $this->assertIsResource($res);
        $this->assertEquals(2, pg_num_rows($res));

        $res =  $this->queryDB("SELECT id FROM CLIENT.providerpm_tbl where pmid = 1" );
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));
/*
        $res =  $this->queryDB("SELECT id FROM CLIENT.merchantaccount_tbl where clientid = 10099 AND pspid =  52 AND name = 'EFS10000114912' AND username = 'Paymaya ac1q2' AND passwd = 'sk-aXQdorOOF0zGMfyVAzTH9CbAFvqq1Oc7PAXcDlrz5z'" );
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));
        */

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
        $this->queryDB("INSERT INTO Client.route_tbl (id, clientid, providerid) VALUES (1, 10099, 52)");
        $this->queryDB("insert into Client.providerpm_tbl (routeid, pmid) values (1, 2)");


        $this->_merchantConfigRepository = new MerchantConfigRepository($this->_OBJ_DB,10099);
        $this->_merchantAggregateRoot = new MerchantConfigInfo();

        $additionalParams = array(
            'client_id' => 10099,
            'p_id' => 21,
            'pm' => 2
        );

        $this->_merchantAggregateRoot->deletePropertyConfig($this->_merchantConfigRepository,'PSP',$additionalParams,52);
        $res =  $this->queryDB("SELECT id FROM CLIENT.psp_property_tbl where value in ('CPD_')" );
        $this->assertIsResource($res);
        $this->assertEquals(0, pg_num_rows($res));

        $res =  $this->queryDB("SELECT id FROM CLIENT.providerpm_tbl where pmid in (2)" );
        $this->assertIsResource($res);
        $this->assertEquals(0, pg_num_rows($res));

        $res =  $this->queryDB("SELECT id FROM CLIENT.merchantaccount_tbl where clientid = 10099 AND pspid =  52 AND name = 'TestPSPName' AND username = 'TestPSPUser' AND passwd = 'TestPSPPass'" );
        $this->assertIsResource($res);
        $this->assertEquals(0, pg_num_rows($res));
    }

    public function testSuccessfulDeleteAllPSPProperty()
    {

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.psp_property_tbl (clientid,propertyid,value) VALUES ( 10099,(select ID from system.psp_property_tbl where name='FILE_EXPIRY' AND PSPID=52),'CPD_')");
        $this->queryDB("INSERT INTO Client.psp_property_tbl (clientid,propertyid,value) VALUES ( 10099,(select ID from system.psp_property_tbl where name='IS_TICKET_LEVEL_SETTLEMENT' AND PSPID=52),'true')");
        $this->queryDB("INSERT INTO Client.route_tbl (id, clientid, providerid) VALUES (1, 10099, 52)");
        $this->queryDB("insert into Client.providerpm_tbl (routeid, pmid) values (1, 2)");


        $this->_merchantConfigRepository = new MerchantConfigRepository($this->_OBJ_DB,10099);
        $this->_merchantAggregateRoot = new MerchantConfigInfo();

        $additionalParams = array(
            'client_id' => 10099,
            'p_id' => -1,
            'pm' => -1
        );

        $this->_merchantAggregateRoot->deletePropertyConfig($this->_merchantConfigRepository,'PSP',$additionalParams,52);
        $res =  $this->queryDB("SELECT id FROM CLIENT.psp_property_tbl where clientid= 10099" );
        $this->assertIsResource($res);
        $this->assertEquals(0, pg_num_rows($res));

        $res =  $this->queryDB("SELECT id FROM CLIENT.providerpm_tbl where routeid = 1" );
        $this->assertIsResource($res);
        $this->assertEquals(0, pg_num_rows($res));

        $res =  $this->queryDB("SELECT id FROM CLIENT.merchantaccount_tbl where clientid = 10099 AND pspid =  52 AND name = 'TestPSPName' AND username = 'TestPSPUser' AND passwd = 'TestPSPPass'" );
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
        $this->queryDB("INSERT INTO client.routefeature_tbl (clientid,routeconfigid, featureid) VALUES (10099,1,1)");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid, countryid) VALUES (1,1)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid, currencyid) VALUES (1,1)");

        $this->_merchantConfigRepository = new MerchantConfigRepository($this->_OBJ_DB,10099);
        $this->_merchantAggregateRoot = new MerchantConfigInfo();
        $this->objConfigurationService = new ConfigurationService($this->_OBJ_DB,10099);

        $arrRouteConfig = $this->_merchantAggregateRoot->getPropertyConfig($this->_merchantConfigRepository,'ROUTE','ALL',1);
        $arrResult = array_filter($arrRouteConfig['Basic'], function ($psp) {
            return (($psp->getValue() === '1234' && $psp->getName() === 'CeptorAccessId') || ($psp->getValue() === '1233' && $psp->getName() === 'CeptorAccessKey') );
        });
        $this->assertEquals(2, count($arrResult));

        $aRouteInfo = $this->objConfigurationService->getRouteConfiguration(1, true);
        $this->assertEquals('TEST', $aRouteInfo->getName());
        $this->assertEquals('TESTMID', $aRouteInfo->getMid());
        $this->assertEquals('username', $aRouteInfo->getUserName());
        $this->assertEquals('password', $aRouteInfo->getPassword());
        $this->assertEquals('2', $aRouteInfo->getCaptureType());

        $aPM = $aRouteInfo->getPM("ROUTE", 1);
        $this->assertEquals(2, count($aPM));

        $aFeatures = $aRouteInfo->getFeatureId();
        $this->assertEquals(1, count($aFeatures));

        $aCountries = $aRouteInfo->getCountryIds();
        $this->assertEquals(1, count($aCountries));

        $aCurrencies = $aRouteInfo->getCurrencyIds();
        $this->assertEquals(1, count($aCurrencies));

    }

    public function testSuccessfulSaveRouteProperty()
    {

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        // $this->queryDB("INSERT INTO Client.route_tbl (id, clientid, providerid) VALUES (1, 10099, 50)");
        // $this->queryDB("INSERT INTO Client.routeconfig_tbl (id, routeid, name, capturetype, mid, username, password) VALUES (1, 1, 'TEST', 2, 'TESTMID', 'username', 'password')");

        $xml= '<?xml version="1.0" encoding="UTF-8"?><client_route_configuration><client_id>10099</client_id> <psp_id>50</psp_id><name>TEST</name><credentials><mid>TESTMID</mid><username>username</username><password>password</password><capture_type>2</capture_type></credentials><properties><property><id>41</id><value>1234</value></property><property><id>42</id><value>1233</value></property></properties><pm_configurations><pm_configuration><pm_id>8</pm_id></pm_configuration><pm_configuration><pm_id>7</pm_id></pm_configuration></pm_configurations><route_features><route_feature><id>1</id></route_feature></route_features><country_details><country_detail><id>1</id></country_detail></country_details><currency_details><currency_detail><id>1</id></currency_detail></currency_details></client_route_configuration>';

        $obj_DOM = simpledom_load_string($xml);
        $objController = new ConfigurationController($this->_OBJ_DB,10099);
        $objController->saveRouteConfig($obj_DOM);

        $res =  $this->queryDB("SELECT id FROM CLIENT.route_property_tbl where value in ('1234','1233')" );
        $this->assertIsResource($res);
        $this->assertEquals(2, pg_num_rows($res));

        $res =  $this->queryDB("SELECT id FROM CLIENT.routeconfig_tbl where id = 1 AND mid='TESTMID' AND username = 'username' AND password = 'password'" );
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));

        $res =  $this->queryDB("SELECT id FROM CLIENT.routepm_tbl where routeconfigid = 1" );
        $this->assertIsResource($res);
        $this->assertEquals(2, pg_num_rows($res));

        $res =  $this->queryDB("SELECT id FROM CLIENT.routefeature_tbl where routeconfigid = 1" );
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));

        $res =  $this->queryDB("SELECT id FROM CLIENT.routecurrency_tbl where routeconfigid = 1" );
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));

        $res =  $this->queryDB("SELECT id FROM CLIENT.routecountry_tbl where routeconfigid = 1" );
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));
    }

    public function testSuccessfulUpdateRouteProperty()
    {

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.route_tbl (id, clientid, providerid) VALUES (1, 10099, 50)");
        $this->queryDB("INSERT INTO Client.routeconfig_tbl (routeid, name, capturetype, mid, username, password) VALUES (1, 'TEST', 2, 'TESTMID', 'username', 'password')");

        $this->queryDB("INSERT INTO Client.route_property_tbl (propertyid,routeconfigid,value) VALUES ( (select ID from system.route_property_tbl where name='CeptorAccessId' AND PSPID=50),1,'1234')");
        $this->queryDB("INSERT INTO Client.route_property_tbl (propertyid,routeconfigid,value) VALUES ( (select ID from system.route_property_tbl where name='CeptorAccessKey' AND PSPID=50),1,'1233')");
        $this->queryDB("INSERT INTO client.routepm_tbl (routeconfigid, pmid) VALUES (1,8)");
        $this->queryDB("INSERT INTO client.routepm_tbl (routeconfigid, pmid) VALUES (1,7)");
        $this->queryDB("INSERT INTO client.routefeature_tbl (clientid,routeconfigid, featureid) VALUES (10099,1,1)");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid, countryid) VALUES (1,1)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid, currencyid) VALUES (1,1)");


        $xml= '<?xml version="1.0" encoding="UTF-8"?><route_configuration><client_id>10099</client_id> <psp_id>50</psp_id><name>TEST</name><mid>TESTMID</mid><username>username</username><password>password</password><capture_type>2</capture_type><properties><property><id>41</id><value>12345</value><enabled>true</enabled></property></properties><pm_configurations><pm_configuration><pm_id>8</pm_id><enabled>false</enabled></pm_configuration></pm_configurations><route_features><route_feature><id>1</id><enabled>false</enabled></route_feature></route_features><country_details><country_detail><id>1</id><enabled>false</enabled></country_detail></country_details><currency_details><currency_detail><id>1</id><enabled>false</enabled></currency_detail></currency_details></route_configuration>';

        $obj_DOM = simpledom_load_string($xml);
        $objController = new ConfigurationController($this->_OBJ_DB,10099);
        $objController->updateRouteConfig($obj_DOM);

        $res =  $this->queryDB("SELECT id FROM CLIENT.route_property_tbl where value in ('12345')" );
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));
        $res =  $this->queryDB("SELECT id FROM CLIENT.routepm_tbl WHERE pmid = 8 AND routeconfigid = 1" );
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));

        $res =  $this->queryDB("SELECT id FROM CLIENT.routefeature_tbl where routeconfigid = 1" );
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));

        $res =  $this->queryDB("SELECT id FROM CLIENT.routecurrency_tbl where routeconfigid = 1" );
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));

        $res =  $this->queryDB("SELECT id FROM CLIENT.routecountry_tbl where routeconfigid = 1" );
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));
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
        $this->queryDB("INSERT INTO client.routefeature_tbl (clientid,routeconfigid, featureid) VALUES (10099,1,1)");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid, countryid) VALUES (1,1)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid, currencyid) VALUES (1,1)");

        $xml= '';

        $obj_DOM = simpledom_load_string($xml);
        $objController = new ConfigurationController($this->_OBJ_DB,10099);
        $additionalParams = array(
            'client_id' => 10099,
            'route_conf_id' => 1,
            'p_id' => 41,
            'pm' => 8,
            'r_f' => 1,
            'country' => 1,
            'currency' => 1
        );
        $objController->deleteRouteConfig($obj_DOM, $additionalParams);

        $res =  $this->queryDB("SELECT id FROM CLIENT.route_property_tbl where value in ('1234')" );
        $this->assertIsResource($res);
        $this->assertEquals(0, pg_num_rows($res));

        $res =  $this->queryDB("SELECT id FROM CLIENT.routepm_tbl where routeconfigid = 1" );
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));

        $res =  $this->queryDB("SELECT id FROM CLIENT.routefeature_tbl where routeconfigid = 1" );
        $this->assertIsResource($res);
        $this->assertEquals(0, pg_num_rows($res));

        $res =  $this->queryDB("SELECT id FROM CLIENT.routecurrency_tbl where routeconfigid = 1" );
        $this->assertIsResource($res);
        $this->assertEquals(0, pg_num_rows($res));

        $res =  $this->queryDB("SELECT id FROM CLIENT.routecountry_tbl where routeconfigid = 1" );
        $this->assertIsResource($res);
        $this->assertEquals(0, pg_num_rows($res));

    }

    public function testSuccessfulDeleteRouteAllProperty()
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
        $this->queryDB("INSERT INTO client.routefeature_tbl (clientid,routeconfigid, featureid) VALUES (10099,1,1)");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid, countryid) VALUES (1,1)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid, currencyid) VALUES (1,1)");

        $xml= '';

        $obj_DOM = simpledom_load_string($xml);
        $objController = new ConfigurationController($this->_OBJ_DB,10099);
        $additionalParams = array(
            'client_id' => 10099,
            'route_conf_id' => 1,
            'p_id' => -1,
            'pm' => -1,
            'r_f' => -1,
            'country' => -1,
            'currency' => -1
        );
        $objController->deleteRouteConfig($obj_DOM, $additionalParams);

        $res =  $this->queryDB("SELECT id FROM CLIENT.route_property_tbl where routeconfigid = 1" );
        $this->assertIsResource($res);
        $this->assertEquals(0, pg_num_rows($res));

        $res =  $this->queryDB("SELECT id FROM CLIENT.routepm_tbl where routeconfigid = 1" );
        $this->assertIsResource($res);
        $this->assertEquals(0, pg_num_rows($res));

        $res =  $this->queryDB("SELECT id FROM CLIENT.routefeature_tbl where routeconfigid = 1" );
        $this->assertIsResource($res);
        $this->assertEquals(0, pg_num_rows($res));

        $res =  $this->queryDB("SELECT id FROM CLIENT.routecurrency_tbl where routeconfigid = 1" );
        $this->assertIsResource($res);
        $this->assertEquals(0, pg_num_rows($res));

        $res =  $this->queryDB("SELECT id FROM CLIENT.routecountry_tbl where routeconfigid = 1" );
        $this->assertIsResource($res);
        $this->assertEquals(0, pg_num_rows($res));

    }

    public function testSuccessfulGetSystemMetadata()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.route_tbl (id, clientid, providerid) VALUES (1, 10099, 50)");
        $this->queryDB("INSERT INTO Client.routeconfig_tbl (id, routeid, name, capturetype, mid, username, password) VALUES (1, 1, 'TEST', 2, 'TESTMID', 'username', 'password')");

        $this->_merchantConfigRepository = new MerchantConfigRepository($this->_OBJ_DB,10099);
        $aMetaData = $this->_merchantConfigRepository->getAllSystemMetaDataInfo();
        $aMetaDataEntities = array('psps','pm_types','country_details','currency_details','capture_types','client_urls','payment_processors','addon_types');
        $aData = array_diff($aMetaDataEntities, array_keys($aMetaData));
        $this->assertEquals(0, count($aData));
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

        $this->_merchantConfigRepository = new MerchantConfigRepository($this->_OBJ_DB,10099);
        $aMetaData = $this->_merchantConfigRepository->getAllPaymentMetaDataInfo();

        $aMetaDataEntities = array('pms','payment_providers','route_features','transaction_types','card_states','fx_service_types','versions');
        $aData = array_diff($aMetaDataEntities, array_keys($aMetaData));
        $this->assertEquals(0, count($aData));
    }

    public function testSuccessfulGetClientConfiguration()
    {

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd, cssurl, callbackurl) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass','https://devcpmassets.s3-ap-southeast-1.amazonaws.com', 'https://hpp2.sit-01.cellpoint.dev/views/callback.php')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");

        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");

        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");

        $this->queryDB("INSERT INTO client.services_tbl (clientid, dcc_enabled, mcp_enabled, pcc_enabled, fraud_enabled, tokenization_enabled, splitpayment_enabled, callback_enabled, void_enabled, enabled, legacy_flow_enabled) VALUES (10099, true, true, true, true, true, true, true, true, true, false);");
        $this->queryDB("INSERT INTO client.pm_tbl (clientid, pmid, enabled, created, modified) VALUES (10099::integer, 1::integer, DEFAULT, DEFAULT, DEFAULT);");
        $this->queryDB("INSERT INTO client.pm_tbl (clientid, pmid, enabled, created, modified) VALUES (10099::integer, 4::integer, DEFAULT, DEFAULT, DEFAULT);");
        $this->queryDB("INSERT INTO client.pm_tbl (clientid, pmid, enabled, created, modified) VALUES (10099::integer, 12::integer, DEFAULT, DEFAULT, DEFAULT);");

        $this->_merchantConfigRepository = new MerchantConfigRepository($this->_OBJ_DB,10099);
        $this->_merchantAggregateRoot = new MerchantConfigInfo();
        $this->objConfigurationService = new ConfigurationService($this->_OBJ_DB,10099);

        $aPM = $this->objConfigurationService->getClientPM();
        $this->assertEquals(0, count(array_diff(array(1,4,12),$aPM)));

        $aClientProperty = $this->objConfigurationService->getPropertyConfig("CLIENT","ALL");

        $this->assertGreaterThan(0, count($aClientProperty['HPP']));
        $this->assertGreaterThan(0, count($aClientProperty['Basic']));
        $this->assertGreaterThan(0, count($aClientProperty['Technical']));

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

    public function testSuccessfulPostClientConfiguration()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");

        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");

        # RQ Body
        $xml= '<?xml version="1.0" encoding="UTF-8"?> <client_configuration> <client_id>10099</client_id> <client_urls> <client_url> <id>1</id> <name>Single Sign-On Authentication</name> <type_id>15</type_id> <value>http://mpoint.local.cellpoint.dev/_test/simulators/login.php</value> </client_url> <client_url> <id>10077</id> <name>Callback URL</name> <type_id>7</type_id> <value>https://hpp2.local-01.cellpoint.dev/test.php</value> </client_url> </client_urls>
               <properties> <property> <id>60</id> <value>true</value> </property> <property> <id>61</id> <value>true</value> </property> </properties> </client_configuration>';

        $obj_DOM = simpledom_load_string($xml);
        $objController = new ConfigurationController($this->_OBJ_DB,10099);
        $objController->postClientConfig($obj_DOM);

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

    public function testSuccessfulPutClientConfiguration()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd, cssurl, callbackurl) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass','https://devcpmassets.s3-ap-southeast-1.amazonaws.com', 'https://hpp2.sit-01.cellpoint.dev/views/callback.php')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");

        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");

        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");

        # RQ Body
        $xml= '<?xml version="1.0" encoding="UTF-8"?> <client_configuration> <client_id>10099</client_id> <id>10077</id> <name>CEBU Pacific Air</name> </client_configuration>';

        $obj_DOM = simpledom_load_string($xml);
        $objController = new ConfigurationController($this->_OBJ_DB,10099);
        $objController->putClientConfig($obj_DOM);

        $res =  $this->queryDB("select id from client.Client_Tbl where name = 'CEBU Pacific Air'");
        # Test 1 : Client PM Table
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res), 'Error | Update Operation Failed for Payment method against client');

    }

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

        $xml = '';
        $obj_DOM = simpledom_load_string($xml);
        $objController = new ConfigurationController($this->_OBJ_DB,10099);
        $additionalParams = array(
            'pm' => '1,4'
        );
        $objController->deleteClientConfig($obj_DOM, $additionalParams);

        $res =  $this->queryDB("select * from client.pm_tbl where pmid in (1, 4)");
        # Test 1 : Client PM Table
        $this->assertIsResource($res);
        $this->assertEquals(0, pg_num_rows($res), 'Error | Delete Operation Failed for Payment method against client');
    }

    public function testSuccessfulDeleteAllClientConfiguration()
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

        $xml = '';
        $obj_DOM = simpledom_load_string($xml);
        $objController = new ConfigurationController($this->_OBJ_DB,10099);
        $additionalParams = array(
            'pm' => '-1',
        );
        $objController->deleteClientConfig($obj_DOM, $additionalParams);

        $res =  $this->queryDB("select * from client.pm_tbl where clientid = 10099 ");
        # Test 1 : Client PM Table
        $this->assertIsResource($res);
        $this->assertEquals(0, pg_num_rows($res), 'Error | Delete Operation Failed for Payment method against client');
    }

    public function  testSuccessfulReadOnlyAddonConfig()
    {
        $pspID = Constants::iWIRE_CARD_PSP;
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd,salt) VALUES (10018, 1, 100, 'Test Client', 'Tuser', 'Tpass','23lkhfgjh24qsdfkjh')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10018, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10018, 'app')");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10018, 'CPM', true)");

        $this->queryDB("INSERT INTO client.MerchantAccount_Tbl (id, clientid, pspid, name, enabled, username, passwd, supportedpartialoperations) VALUES (1, 10018, $pspID, 'Test 2c2p-alc', true, 'CELLPM', 'HC1XBPV0O4WLKZMG', 0)");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, 'Test Sub Merchant')");

        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid,dccenabled) VALUES (10018, 8, $pspID,100,true)");
        $this->queryDB("INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid, enabled) VALUES (10018,100,840, true)");
        $this->queryDB("INSERT INTO client.services_tbl (clientid, legacy_flow_enabled) VALUES(10018, true);");
        $this->queryDB("INSERT INTO client.pcc_config_tbl (clientid, pmid, sale_currency_id, is_presentment, settlement_currency_id) VALUES(10018, 8, 608, true, 590)");

        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (50011, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10018, 50011)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 50011, 8, $pspID, '501910******3742', '06/24', TRUE, 10018, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");

        # Transaction Related Entry
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10018, 1100, 208, 100, 4001, '1513-005', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,currencyid,euaid,convertedamount,convertedcurrencyid, routeconfigid) VALUES (1001012, 100, 10018, 1100, 100, $pspID, '1512', '1234abc', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,840,50011,5000,840, -1)");

        $iTxnID = 1001012;
        $obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $this->_OBJ_DB);
        $repository = new ReadOnlyConfigRepository($this->_OBJ_DB,$obj_TxnInfo);
        $aDCCPmid = array(8);
        $presentment = $repository->getAddonConfiguration(AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::ePCC),$aDCCPmid);
        $this->assertInstanceOf('api\classes\merchantservices\configuration\PCCConfig',$presentment);
    }

    public function  testSuccessfulReadOnlyAddonConfigProp()
    {
        $pspID = Constants::iWIRE_CARD_PSP;
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd,salt) VALUES (10018, 1, 100, 'Test Client', 'Tuser', 'Tpass','23lkhfgjh24qsdfkjh')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10018, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10018, 'app')");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10018, 'CPM', true)");

        $this->queryDB("INSERT INTO client.MerchantAccount_Tbl (id, clientid, pspid, name, enabled, username, passwd, supportedpartialoperations) VALUES (1, 10018, $pspID, 'Test 2c2p-alc', true, 'CELLPM', 'HC1XBPV0O4WLKZMG', 0)");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, 'Test Sub Merchant')");

        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid,dccenabled) VALUES (10018, 8, $pspID,100,true)");
        $this->queryDB("INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid, enabled) VALUES (10018,100,840, true)");
        $this->queryDB("INSERT INTO client.services_tbl (clientid, legacy_flow_enabled) VALUES(10018, true);");
        $this->queryDB("INSERT INTO client.pcc_config_tbl (clientid, pmid, sale_currency_id, is_presentment, settlement_currency_id) VALUES(10018, 8, 608, true, 590)");

        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (50011, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10018, 50011)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 50011, 8, $pspID, '501910******3742', '06/24', TRUE, 10018, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");

        # Transaction Related Entry
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10018, 1100, 208, 100, 4001, '1513-005', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,currencyid,euaid,convertedamount,convertedcurrencyid, routeconfigid) VALUES (1001012, 100, 10018, 1100, 100, $pspID, '1512', '1234abc', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,840,50011,5000,840, -1)");

        $iTxnID = 1001012;
        $obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $this->_OBJ_DB);
        $repository = new ReadOnlyConfigRepository($this->_OBJ_DB,$obj_TxnInfo);
        $splitPaymentAddOn = $repository->getAddonConfiguration(AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::eSPLIT_PAYMENT),array(),true);
        $this->assertInstanceOf('api\classes\merchantservices\configuration\Split_PaymentConfig',$splitPaymentAddOn);
    }

    public function testSuccessfulGetProviderConfig()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("insert into Client.merchantaccount_tbl (clientid, pspid, name, username, passwd) values (10099, 52, 'TestPSPName','TestPSPUser','TestPSPPass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");

        $this->objConfigurationService = new ConfigurationService($this->_OBJ_DB,10099);
        $aRS = $this->objConfigurationService->getAllPSPCredentials(-1,-1);

        $this->assertEquals(1, count($aRS));

        $res =  $this->queryDB("select pspid from Client.merchantaccount_tbl WHERE name IN ('TestPSPName') ");
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));
    }

    public function testSuccessfulUpdateProviderConfig()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("insert into Client.merchantaccount_tbl (clientid, pspid, name, username, passwd) values (10099, 52, 'TestPSPName','TestPSPUser','TestPSPPass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");

        $xml= '<?xml version="1.0" encoding="UTF-8"?><client_provider_configurations><client_provider_configuration><id>65</id><client_id>10099</client_id><name>CEBU-RMFSS</name><username>By9AjPV6j14jgb3DXRIpW0mInOfMEafS</username><password>E9NBawrSH6UAtw1v</password></client_provider_configuration><client_provider_configuration><id>64</id><client_id>10099</client_id><name>EFS10000114912</name><username>TEST</username><password>sk-aXQdorOOF0zGMfyVAzTH9CbAFvqq1Oc7PAXcDlrz5z2</password></client_provider_configuration></client_provider_configurations>';

        $obj_DOM = simpledom_load_string($xml);
        $objController = new ConfigurationController($this->_OBJ_DB,10099);
        $objController->updateProviderConfig($obj_DOM);

        $res =  $this->queryDB("select pspid from Client.merchantaccount_tbl WHERE name IN ('TestPSPName','CEBU-RMFSS','EFS10000114912') ");

        $this->assertIsResource($res);
        $this->assertEquals(3, pg_num_rows($res));

    }

    public function testSuccessfulDeleteProviderConfig()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("insert into Client.merchantaccount_tbl (clientid, pspid, name, username, passwd) values (10099, 52, 'TestPSPName','TestPSPUser','TestPSPPass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");

        $xml = '';
        $obj_DOM = simpledom_load_string($xml);
        $additionalParams = array(
        );

        $objController = new ConfigurationController($this->_OBJ_DB,10099);
        $objController->deleteProviderConfig($obj_DOM, $additionalParams);

        $res =  $this->queryDB("select pspid from Client.merchantaccount_tbl WHERE clientid = 10099 ");

        $this->assertIsResource($res);
        $this->assertEquals(0, pg_num_rows($res));
    }
}