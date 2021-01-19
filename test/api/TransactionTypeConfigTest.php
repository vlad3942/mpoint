<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anna Lagad
 * Copyright: Cellpoint Digital
 * Link: http://www.cellpointdigital.com
 * Project: mPoint
 * File Name:TransactionTypeConfigTest.php
 */

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once sAPI_CLASS_PATH . 'simpledom.php';
require_once __DIR__ . '/../../api/classes/crs/TransactionTypeConfig.php';

class TransactionTypeConfigTest extends baseAPITest
{

    private $_OBJ_DB;
    protected $_aHTTP_CONN_INFO;

    public function setUp() : void
    {
        parent::setUp(true);
        global $aHTTP_CONN_INFO;
        $this->bIgnoreErrors = true;
        $this->_aHTTP_CONN_INFO = $aHTTP_CONN_INFO;
        $this->_OBJ_DB = RDB::produceDatabase($this->mPointDBInfo);
    }

    public function testSuccessGetTransactionType()
    {
        $obj_TransactionTypeConfig = TransactionTypeConfig::produceConfig($this->_OBJ_DB);
        $xml = '<transaction-types>';
        foreach ($obj_TransactionTypeConfig as $obj_TransactionType)
        {
            if ( ($obj_TransactionType instanceof TransactionTypeConfig) === true)
            {
                $xml .= $obj_TransactionType->toXML();
            }
        }
        $xml .= '</transaction-types>';

        $this->assertStringContainsString('<transaction-types><transaction-type  id="0" name="System Record" enabled="false" /><transaction-type  id="1" name="Shopping Online" enabled="true" /><transaction-type  id="2" name="Shopping Offline" enabled="true" /><transaction-type  id="3" name="Self Service Online" enabled="true" /><transaction-type  id="4" name="Self Service Offline" enabled="true" /><transaction-type  id="10" name="Call Centre Purchase" enabled="true" /><transaction-type  id="11" name="Call Centre Subscription" enabled="true" /><transaction-type  id="20" name="SMS Purchase" enabled="true" /><transaction-type  id="21" name="SMS Subscription" enabled="true" /><transaction-type  id="30" name="Web Purchase" enabled="true" /><transaction-type  id="31" name="Web Subscription" enabled="true" /><transaction-type  id="40" name="Mobile App. Purchase" enabled="true" /><transaction-type  id="41" name="Mobile App. Subscription" enabled="true" /><transaction-type  id="100" name="Top-Up Purchase" enabled="true" /><transaction-type  id="101" name="Top-Up Subscription" enabled="true" /><transaction-type  id="102" name="Points Top-Up Purchase" enabled="true" /><transaction-type  id="1000" name="E-Money Top-Up" enabled="true" /><transaction-type  id="1001" name="E-Money Purchase" enabled="true" /><transaction-type  id="1002" name="E-Money Transfer" enabled="true" /><transaction-type  id="1003" name="E-Money Withdrawal" enabled="true" /><transaction-type  id="1004" name="Points Top-Up" enabled="true" /><transaction-type  id="1005" name="Points Purchase" enabled="true" /><transaction-type  id="1007" name="Points Reward" enabled="true" /><transaction-type  id="1009" name="Card Purchase" enabled="true" /><transaction-type  id="10091" name="New Card Purchase" enabled="true" /></transaction-types>', $xml);
    }

    public function testSuccessGetTransactionTypeAsAttributelessXML()
    {
        $obj_TransactionTypeConfig = TransactionTypeConfig::produceConfig($this->_OBJ_DB);
        $xml = '<transaction_types>';
        foreach ($obj_TransactionTypeConfig as $obj_TransactionType)
        {
            if ( ($obj_TransactionType instanceof TransactionTypeConfig) === true)
            {
                $xml .= $obj_TransactionType->toAttributelessXML();
            }
        }
        $xml .= '</transaction_types>';

        $this->assertStringContainsString('<transaction_types><transaction_type><id>0</id><name>System Record</name><enabled>false</enabled></transaction_type><transaction_type><id>1</id><name>Shopping Online</name><enabled>true</enabled></transaction_type><transaction_type><id>2</id><name>Shopping Offline</name><enabled>true</enabled></transaction_type><transaction_type><id>3</id><name>Self Service Online</name><enabled>true</enabled></transaction_type><transaction_type><id>4</id><name>Self Service Offline</name><enabled>true</enabled></transaction_type><transaction_type><id>10</id><name>Call Centre Purchase</name><enabled>true</enabled></transaction_type><transaction_type><id>11</id><name>Call Centre Subscription</name><enabled>true</enabled></transaction_type><transaction_type><id>20</id><name>SMS Purchase</name><enabled>true</enabled></transaction_type><transaction_type><id>21</id><name>SMS Subscription</name><enabled>true</enabled></transaction_type><transaction_type><id>30</id><name>Web Purchase</name><enabled>true</enabled></transaction_type><transaction_type><id>31</id><name>Web Subscription</name><enabled>true</enabled></transaction_type><transaction_type><id>40</id><name>Mobile App. Purchase</name><enabled>true</enabled></transaction_type><transaction_type><id>41</id><name>Mobile App. Subscription</name><enabled>true</enabled></transaction_type><transaction_type><id>100</id><name>Top-Up Purchase</name><enabled>true</enabled></transaction_type><transaction_type><id>101</id><name>Top-Up Subscription</name><enabled>true</enabled></transaction_type><transaction_type><id>102</id><name>Points Top-Up Purchase</name><enabled>true</enabled></transaction_type><transaction_type><id>1000</id><name>E-Money Top-Up</name><enabled>true</enabled></transaction_type><transaction_type><id>1001</id><name>E-Money Purchase</name><enabled>true</enabled></transaction_type><transaction_type><id>1002</id><name>E-Money Transfer</name><enabled>true</enabled></transaction_type><transaction_type><id>1003</id><name>E-Money Withdrawal</name><enabled>true</enabled></transaction_type><transaction_type><id>1004</id><name>Points Top-Up</name><enabled>true</enabled></transaction_type><transaction_type><id>1005</id><name>Points Purchase</name><enabled>true</enabled></transaction_type><transaction_type><id>1007</id><name>Points Reward</name><enabled>true</enabled></transaction_type><transaction_type><id>1009</id><name>Card Purchase</name><enabled>true</enabled></transaction_type><transaction_type><id>10091</id><name>New Card Purchase</name><enabled>true</enabled></transaction_type></transaction_types>', $xml);
    }

    public function tearDown() : void
    {
        $this->_OBJ_DB->disConnect();
        parent::tearDown();
    }


}
