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

        $this->assertStringContainsString('<transaction-types><transaction-type  id="1" name="Shopping Online" enabled="true" /><transaction-type  id="2" name="Shopping Offline" enabled="true" /><transaction-type  id="3" name="Self Service Online" enabled="true" /><transaction-type  id="4" name="Self Service Offline" enabled="true" /><transaction-type  id="5" name="Self Service Online with additional rules on FOP" enabled="true" /><transaction-type  id="6" name="Payment Link Transaction" enabled="true" /><transaction-type  id="7" name="Call Center Purchase" enabled="true" /></transaction-types>', $xml);
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

        $this->assertStringContainsString('<transaction_types><transaction_type><id>1</id><name>Shopping Online</name><enabled>true</enabled></transaction_type><transaction_type><id>2</id><name>Shopping Offline</name><enabled>true</enabled></transaction_type><transaction_type><id>3</id><name>Self Service Online</name><enabled>true</enabled></transaction_type><transaction_type><id>4</id><name>Self Service Offline</name><enabled>true</enabled></transaction_type><transaction_type><id>5</id><name>Self Service Online with additional rules on FOP</name><enabled>true</enabled></transaction_type><transaction_type><id>6</id><name>Payment Link Transaction</name><enabled>true</enabled></transaction_type><transaction_type><id>7</id><name>Call Center Purchase</name><enabled>true</enabled></transaction_type></transaction_types>', $xml);
    }

    public function tearDown() : void
    {
        $this->_OBJ_DB->disConnect();
        parent::tearDown();
    }


}
