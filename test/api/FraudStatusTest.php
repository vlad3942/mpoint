<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once sAPI_CLASS_PATH .'simpledom.php';
require_once __DIR__ . '/../../api/classes/admin.php';
require_once __DIR__ . '/../../api/classes/mConsole.php';
require_once __DIR__ . '/../../api/classes/fraudStatus.php';

class FraudStatusTest extends baseAPITest
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
        $this->_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH . sLANG ."/global.txt", sLANGUAGE_PATH . sLANG ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");
    }

    public function testSuccessUpdateFraudStatus()
    {
        $pspID = 2;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 113, 'app')");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        //$this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 7)");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (113, 7, $pspID, true, 1)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 7, $pspID, '501910******3742', '06/24', TRUE, 113, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, auto_capture, enabled, currencyid, sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 113, 1100, 1,  $pspID, 5001, 100, '103-1418291', 'phpunit.com', 5000, '127.0.0.1', 1, TRUE,208, 1,5000,208)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_CAPTURED_STATE. ")");

        $iClientId = 113;
        $iTransactionId = 1001001;
        $iStatusId = Constants::iPRE_FRAUD_CHECK_REVIEW_SUCCESS_STATE;
        $sComment = 'phpunit test';
        $obj_mConsole = new mConsole($this->_OBJ_DB, $this->_OBJ_TXT);
        $obj_mPoint = new General($this->_OBJ_DB, $this->_OBJ_TXT);
        $objFraudStatus = new FraudStatus($this->_aHTTP_CONN_INFO, $this->_OBJ_DB, $obj_mPoint, $obj_mConsole, $iClientId, $iTransactionId, $iStatusId,  $sComment);

        //$code = $objFraudStatus->SSOCheck();
        //$this->assertEquals(mConsole::iAUTHORIZATION_SUCCESSFUL, $code);
        $xml = $objFraudStatus->updateFraudStatus();
        $this->assertStringContainsString('<status code="200">Operation Successful</status>', $xml);
    }

    public function testFailUpdateFraudStatus()
    {
        $pspID = 2;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 113, 'app')");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        //$this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 7)");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (113, 7, $pspID, true, 1)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 7, $pspID, '501910******3742', '06/24', TRUE, 113, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, auto_capture, enabled, currencyid, sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 113, 1100, 1,  $pspID, 5001, 100, '103-1418291', 'phpunit.com', 5000, '127.0.0.1', 1, TRUE,208, 1,5000,208)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_CAPTURED_STATE. ")");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPOST_FRAUD_CHECK_REVIEW_SUCCESS_STATE. ")");

        $iClientId = 113;
        $iTransactionId = 1001001;
        $iStatusId = Constants::iPRE_FRAUD_CHECK_REVIEW_SUCCESS_STATE;
        $sComment = 'phpunit test';
        $obj_mConsole = new mConsole($this->_OBJ_DB, $this->_OBJ_TXT);
        $obj_mPoint = new General($this->_OBJ_DB, $this->_OBJ_TXT);
        $objFraudStatus = new FraudStatus($this->_aHTTP_CONN_INFO, $this->_OBJ_DB, $obj_mPoint, $obj_mConsole, $iClientId, $iTransactionId, $iStatusId,  $sComment);

        //$code = $objFraudStatus->SSOCheck();
        //$this->assertEquals(mConsole::iAUTHORIZATION_SUCCESSFUL, $code);
        $xml = $objFraudStatus->updateFraudStatus();
        $this->assertStringContainsString('<status code="422">Invalid Operation</status>', $xml);
    }

    public function testRequestValidationErrors()
    {
        $iClientId = 113;
        $iTransactionId = 1001001;
        $iStatusId = Constants::iPRE_FRAUD_CHECK_REVIEW_SUCCESS_STATE;
        $sComment = 'phpunit test';
        $obj_mConsole = new mConsole($this->_OBJ_DB, $this->_OBJ_TXT);
        $obj_mPoint = new General($this->_OBJ_DB, $this->_OBJ_TXT);
        $objFraudStatus = new FraudStatus($this->_aHTTP_CONN_INFO, $this->_OBJ_DB, $obj_mPoint, $obj_mConsole, $iClientId, $iTransactionId, $iStatusId,  $sComment);
        $xml = '<xml></xml>';
        $obj_DOM = simpledom_load_string($xml);

        $xml = $objFraudStatus->getRequestValidationError($obj_DOM);
        $this->assertStringContainsString('<status code="400">Wrong operation empty request</status>', $xml);

        $xml = $objFraudStatus->getRequestValidationError(null);
        $this->assertStringContainsString('<status code="415">Invalid XML Document</status>', $xml);

        $xml = $this->getDoc();
        $obj_DOM = simpledom_load_string($xml);
        $xml = $objFraudStatus->getRequestValidationError($obj_DOM);
        $this->assertEmpty($xml);
    }

    protected function getDoc()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<root>';
        $xml .= '<update-fraud-status>';
        $xml .= '<client-id>113</client-id>';
        $xml .= '<transaction-id>1001001</transaction-id>';
        $xml .= '<status_id>3117</status_id>';
        $xml .= '<comment>Test comment</comment>';
        $xml .= '</update-fraud-status>';
        $xml .= '</root>';
        return $xml;
    }

    public function tearDown(): void
    {
        $this->_OBJ_DB->disConnect();
        parent::tearDown();
    }

}

