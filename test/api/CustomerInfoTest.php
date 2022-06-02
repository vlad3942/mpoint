<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anna Lagad
 * Copyright: Cellpoint Digital
 * Link: http://www.cellpointdigital.com
 * Project: mPoint
 * File Name:CustomerInfoTest.php
 */

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once sAPI_CLASS_PATH . 'simpledom.php';
require_once __DIR__ . '/../../api/classes/customer_info.php';
require_once __DIR__ . '/../../api/classes/clientinfo.php';

class CustomerInfoTest extends baseAPITest
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
        $this->_obj_TXT = new api\classes\core\TranslateText(array(sLANGUAGE_PATH . sLANG ."/global.txt", sLANGUAGE_PATH . sLANG ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");
    }

    public function testSuccessGetCustomerType()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1103, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.URL_Tbl (urltypeid, clientid, url) VALUES (2, 10099, 'http://mpoint.local.cellpointmobile.com/_test/simulators/auth.php')");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1103, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1103, 1,  18, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");

        $obj_mPoint = new Home($this->_OBJ_DB, $this->_obj_TXT);
        $obj_CountryConfig = CountryConfig::produceConfig($this->_OBJ_DB, 100);
        $obj_ClientConfig = ClientConfig::produceConfig($this->_OBJ_DB, 10099, 1103);
        $obj_TxnInfo = TxnInfo::produceInfo(1001001, $this->_OBJ_DB);
        $obj_CustomerInfo = CustomerInfo::produceInfo($this->_OBJ_DB, $obj_TxnInfo->getAccountID() );
        if (is_object($obj_CustomerInfo)) {
            $obj_Customer = simplexml_load_string($obj_CustomerInfo->toXML());
            if (strlen($obj_TxnInfo->getCustomerRef()) > 0) {
                $obj_Customer["customer-ref"] = $obj_TxnInfo->getCustomerRef();
            }
            if ((int)$obj_TxnInfo->getMobile() > 0) {
                $obj_Customer->mobile = $obj_TxnInfo->getMobile();
                $obj_Customer->mobile["country-id"] = intval($obj_CountryConfig->getID());
                $obj_Customer->mobile["operator-id"] = $obj_TxnInfo->getOperator();
            }
            if (strlen($obj_TxnInfo->getEMail()) > 0) {
                $obj_Customer->email = $obj_TxnInfo->getEMail();
            }
            $obj_CustomerInfo = CustomerInfo::produceInfo($obj_Customer);

            $code = $obj_mPoint->auth($obj_ClientConfig, $obj_CustomerInfo, 'success', 10099);
            $profileTypeId = $obj_CustomerInfo->getProfileTypeID();
            $this->assertEquals(10, $code);
            $this->assertEquals(1, $profileTypeId);
        }
    }

    public function testGetCustomerTypeFailureScenario()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1101, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.URL_Tbl (urltypeid, clientid, url) VALUES (2, 10099, 'http://mpoint.local.cellpointmobile.com/_test/simulators/auth-error.php')");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1101, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1101, 1,  18, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");

        $obj_mPoint = new Home($this->_OBJ_DB, $this->_obj_TXT);
        $obj_CountryConfig = CountryConfig::produceConfig($this->_OBJ_DB, 100);
        $obj_ClientConfig = ClientConfig::produceConfig($this->_OBJ_DB, 10099, 1101);
        $obj_TxnInfo = TxnInfo::produceInfo(1001001, $this->_OBJ_DB);
        $obj_CustomerInfo = CustomerInfo::produceInfo($this->_OBJ_DB, $obj_TxnInfo->getAccountID() );
        if (is_object($obj_CustomerInfo)) {
            $obj_Customer = simplexml_load_string($obj_CustomerInfo->toXML());
            if (strlen($obj_TxnInfo->getCustomerRef()) > 0) {
                $obj_Customer["customer-ref"] = $obj_TxnInfo->getCustomerRef();
            }
            if ((int)$obj_TxnInfo->getMobile() > 0) {
                $obj_Customer->mobile = $obj_TxnInfo->getMobile();
                $obj_Customer->mobile["country-id"] = intval($obj_CountryConfig->getID());
                $obj_Customer->mobile["operator-id"] = $obj_TxnInfo->getOperator();
            }
            if (strlen($obj_TxnInfo->getEMail()) > 0) {
                $obj_Customer->email = $obj_TxnInfo->getEMail();
            }
            $obj_CustomerInfo = CustomerInfo::produceInfo($obj_Customer);

            $code = $obj_mPoint->auth($obj_ClientConfig, $obj_CustomerInfo, 'fail', 10099);
            $this->assertEquals(1, $code);
        }
    }


    public function testWrongAuthUrl()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1102, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1102, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1102, 1,  18, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");

        $obj_mPoint = new Home($this->_OBJ_DB, $this->_obj_TXT);
        $obj_CountryConfig = CountryConfig::produceConfig($this->_OBJ_DB, 100);
        $obj_ClientConfig = ClientConfig::produceConfig($this->_OBJ_DB, 10099, 1102);
        $obj_TxnInfo = TxnInfo::produceInfo(1001001, $this->_OBJ_DB);
        $obj_CustomerInfo = CustomerInfo::produceInfo($this->_OBJ_DB, $obj_TxnInfo->getAccountID() );
        if (is_object($obj_CustomerInfo)) {
            $obj_Customer = simplexml_load_string($obj_CustomerInfo->toXML());
            if (strlen($obj_TxnInfo->getCustomerRef()) > 0) {
                $obj_Customer["customer-ref"] = $obj_TxnInfo->getCustomerRef();
            }
            if ((int)$obj_TxnInfo->getMobile() > 0) {
                $obj_Customer->mobile = $obj_TxnInfo->getMobile();
                $obj_Customer->mobile["country-id"] = intval($obj_CountryConfig->getID());
                $obj_Customer->mobile["operator-id"] = $obj_TxnInfo->getOperator();
            }
            if (strlen($obj_TxnInfo->getEMail()) > 0) {
                $obj_Customer->email = $obj_TxnInfo->getEMail();
            }
            $obj_CustomerInfo = CustomerInfo::produceInfo($obj_Customer);

            $code = $obj_mPoint->auth($obj_ClientConfig, $obj_CustomerInfo, 'fail', 10099);
            $this->assertEquals(6, $code);
        }
    }

    public function tearDown() : void
    {
        $this->_OBJ_DB->disConnect();
        parent::tearDown();
    }

}
