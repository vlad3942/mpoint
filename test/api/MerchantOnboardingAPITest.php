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
        $this->queryDB("INSERT INTO client.fraud_config_tbl (clientid, pmid, providerid, countryid, currencyid, \"typeOfFraud\") VALUES(10099, 8, 15, 640, 654, 1)");
        $this->queryDB("INSERT INTO client.mpi_config_tbl (clientid, pmid, providerid, \"version\") VALUES(10099, 1, 17, '1.0')");
        $this->queryDB("INSERT INTO client.pcc_config_tbl (clientid, pmid, sale_currency_id, is_presentment, settlement_currency_id) VALUES(10099, 8, 608, true, 590)");



        $this->constHTTPClient("/merchantservices/api/Onboarding.php?service=addonconfig&params=client_id/10099");

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'));
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><addon_config_details><addon_config_detail><addon_type>FX</addon_type><addon_subtype>DCC</addon_subtype><addon_configurations><addon_configuration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>566</currency_id><country_id>200</country_id></addon_configuration></addon_configurations></addon_config_detail><addon_config_detail><addon_type>FX</addon_type><addon_subtype>MCP</addon_subtype><addon_configurations><addon_configuration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>566</currency_id><country_id>200</country_id></addon_configuration></addon_configurations></addon_config_detail><addon_config_detail><addon_type>FX</addon_type><addon_subtype>PCC</addon_subtype><addon_configurations><addon_configuration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>608</currency_id><settlement_currency_id>590</settlement_currency_id><is_presentment>true</is_presentment></addon_configuration></addon_configurations></addon_config_detail><addon_config_detail><addon_type>FRAUD</addon_type><addon_subtype>FRAUD</addon_subtype><addon_configurations><addon_configuration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>654</currency_id><country_id>640</country_id><provider_id>15</provider_id><type>1</type></addon_configuration></addon_configurations></addon_config_detail><addon_config_detail><addon_type>FRAUD</addon_type><addon_subtype>MPI</addon_subtype><addon_configurations><addon_configuration><id>1</id><enabled>true</enabled><pm_id>1</pm_id><provider_id>17</provider_id><version>1.0</version></addon_configuration></addon_configurations></addon_config_detail></addon_config_details>', $sReplyBody);

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
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
<addon_config_details>
    <addon_config_detail>
        <addon_type>FX</addon_type>
        <addon_subtype>DCC</addon_subtype>
        <addon_configurations>
            <addon_configuration>
                <enabled>true</enabled>
                <pm_id>8</pm_id>
                <currency_id>608</currency_id>
                <country_id>200</country_id>
            </addon_configuration>
            
        </addon_configurations>
    </addon_config_detail>
    <addon_config_detail>
        <addon_type>FX</addon_type>
        <addon_subtype>MCP</addon_subtype>
        <addon_configurations>
            <addon_configuration>
                <enabled>true</enabled>
                <pm_id>8</pm_id>
                <currency_id>608</currency_id>
                <country_id>200</country_id>
            </addon_configuration>
            
        </addon_configurations>
    </addon_config_detail>
    <addon_config_detail>
        <addon_type>FX</addon_type>
        <addon_subtype>PCC</addon_subtype>
        <addon_configurations>
            <addon_configuration>                
                <enabled>true</enabled>
                <pm_id>8</pm_id>
                <currency_id>608</currency_id>
                <settlement_currency_id>604</settlement_currency_id>
                <is_presentment>true</is_presentment>
            </addon_configuration>           
        </addon_configurations>
    </addon_config_detail>
    <addon_config_detail>
        <addon_type>FRAUD</addon_type>
        <addon_subtype>FRAUD</addon_subtype>
        <addon_configurations>
            <addon_configuration>
                <enabled>true</enabled>
                <pm_id>8</pm_id>
                <currency_id>608</currency_id>
                <country_id>640</country_id>
                <provider_id>15</provider_id>
                <type>1</type>
            </addon_configuration>             
        </addon_configurations>
        <properties>
            <property>
                <name>ISROLLBACK</name>
                <value>1</value>
            </property>
        </properties>
    </addon_config_detail>
    <addon_config_detail>
        <addon_type>FRAUD</addon_type>
        <addon_subtype>MPI</addon_subtype>
        <addon_configurations>
            <addon_configuration>
                <id>1</id>
                <enabled>true</enabled>
                <pm_id>8</pm_id>
                <provider_id>17</provider_id>
                <version>2.0</version>
            </addon_configuration>
        </addon_configurations>
    </addon_config_detail>
</addon_config_details>';
        //</editor-fold>

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'),$xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(201, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><addon_config_details><addon_config_detail><addon_type>FX</addon_type><addon_subtype>DCC</addon_subtype><addon_configurations><addon_configuration><id>-1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>608</currency_id><country_id>200</country_id><status>Successful</status></addon_configuration></addon_configurations></addon_config_detail><addon_config_detail><addon_type>FX</addon_type><addon_subtype>MCP</addon_subtype><addon_configurations><addon_configuration><id>-1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>608</currency_id><country_id>200</country_id><status>Successful</status></addon_configuration></addon_configurations></addon_config_detail><addon_config_detail><addon_type>FX</addon_type><addon_subtype>PCC</addon_subtype><addon_configurations><addon_configuration><id>-1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>608</currency_id><settlement_currency_id>604</settlement_currency_id><is_presentment>true</is_presentment><status>Successful</status></addon_configuration></addon_configurations></addon_config_detail><addon_config_detail><addon_type>FRAUD</addon_type><addon_subtype>FRAUD</addon_subtype><addon_configurations><addon_configuration><id>-1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>608</currency_id><country_id>640</country_id><provider_id>15</provider_id><type>1</type><status>Successful</status></addon_configuration></addon_configurations></addon_config_detail><addon_config_detail><addon_type>FRAUD</addon_type><addon_subtype>MPI</addon_subtype><addon_configurations><addon_configuration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><provider_id>17</provider_id><version>2.0</version><status>Successful</status></addon_configuration></addon_configurations></addon_config_detail></addon_config_details>', $sReplyBody);

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

        $this->queryDB("INSERT INTO client.dcc_config_tbl (pmid, clientid, countryid, currencyid) VALUES(8, 10099, 200, 608)");
        $this->queryDB("INSERT INTO client.mcp_config_tbl (pmid, clientid, countryid, currencyid) VALUES(8, 10099, 200, 608)");
        $this->queryDB("INSERT INTO client.fraud_config_tbl (clientid, pmid, providerid, countryid, currencyid, \"typeOfFraud\") VALUES(10099, 8, 15, 200, 608, 1)");
        $this->queryDB("INSERT INTO client.mpi_config_tbl (clientid, pmid, providerid, \"version\") VALUES(10099, 8, 17, '2.0')");
        $this->queryDB("INSERT INTO client.pcc_config_tbl (clientid, pmid, sale_currency_id, is_presentment, settlement_currency_id) VALUES(10099, 8, 608, true, 590)");



        $this->constHTTPClient("/merchantservices/api/Onboarding.php?service=addonconfig&params=client_id/10099",'post');

        $this->_httpClient->connect();
        //<editor-fold desc="Request">
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
<addon_config_details>
    <addon_config_detail>
        <addon_type>FX</addon_type>
        <addon_subtype>DCC</addon_subtype>
        <addon_configurations>
            <addon_configuration>
                <enabled>true</enabled>
                <pm_id>8</pm_id>
                <currency_id>608</currency_id>
                <country_id>200</country_id>
            </addon_configuration>
            
        </addon_configurations>
    </addon_config_detail>
    <addon_config_detail>
        <addon_type>FX</addon_type>
        <addon_subtype>MCP</addon_subtype>
        <addon_configurations>
            <addon_configuration>
                <enabled>true</enabled>
                <pm_id>8</pm_id>
                <currency_id>608</currency_id>
                <country_id>200</country_id>
            </addon_configuration>
            
        </addon_configurations>
    </addon_config_detail>
    <addon_config_detail>
        <addon_type>FRAUD</addon_type>
        <addon_subtype>FRAUD</addon_subtype>
        <addon_configurations>
            <addon_configuration>
                <enabled>true</enabled>
                <pm_id>8</pm_id>
                <currency_id>608</currency_id>
                <country_id>200</country_id>
                <provider_id>15</provider_id>
                <type>1</type>
            </addon_configuration>             
        </addon_configurations>
        <properties>
            <property>
                <name>ISROLLBACK</name>
                <value>1</value>
            </property>
        </properties>
    </addon_config_detail>
    <addon_config_detail>
        <addon_type>FRAUD</addon_type>
        <addon_subtype>MPI</addon_subtype>
        <addon_configurations>
            <addon_configuration>
                <id>1</id>
                <enabled>true</enabled>
                <pm_id>8</pm_id>
                <provider_id>17</provider_id>
                <version>2.0</version>
            </addon_configuration>
        </addon_configurations>
    </addon_config_detail>
</addon_config_details>';
        //</editor-fold>

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'),$xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(207, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><addon_config_details><addon_config_detail><addon_type>FX</addon_type><addon_subtype>DCC</addon_subtype><addon_configurations><addon_configuration><id>-1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>608</currency_id><country_id>200</country_id><status>Duplicated</status></addon_configuration></addon_configurations></addon_config_detail><addon_config_detail><addon_type>FX</addon_type><addon_subtype>MCP</addon_subtype><addon_configurations><addon_configuration><id>-1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>608</currency_id><country_id>200</country_id><status>Duplicated</status></addon_configuration></addon_configurations></addon_config_detail><addon_config_detail><addon_type>FRAUD</addon_type><addon_subtype>FRAUD</addon_subtype><addon_configurations><addon_configuration><id>-1</id><enabled>true</enabled><pm_id>8</pm_id><currency_id>608</currency_id><country_id>200</country_id><provider_id>15</provider_id><type>1</type><status>Duplicated</status></addon_configuration></addon_configurations></addon_config_detail><addon_config_detail><addon_type>FRAUD</addon_type><addon_subtype>MPI</addon_subtype><addon_configurations><addon_configuration><id>1</id><enabled>true</enabled><pm_id>8</pm_id><provider_id>17</provider_id><version>2.0</version><status>Duplicated</status></addon_configuration></addon_configurations></addon_config_detail></addon_config_details>', $sReplyBody);

    }

}