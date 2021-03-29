<?php
/**
 * Created by VS code
 * User: Chaitenya Yadav
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package:
 * File Name:HomeTest.php
 */

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once sAPI_CLASS_PATH . 'simpledom.php';


class HomeTest extends baseAPITest
{

    private $_OBJ_DB;
    private $_OBJ_TXT;
    protected $_aHTTP_CONN_INFO;

    public function setUp():void
    {
        parent::setUp(TRUE);
        global $aHTTP_CONN_INFO;
        $this->bIgnoreErrors = true;
        $this->_aHTTP_CONN_INFO = $aHTTP_CONN_INFO;
        $this->_OBJ_DB = RDB::produceDatabase($this->mPointDBInfo);
        $this->_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH . sLANG ."/global.txt", sLANGUAGE_PATH . sLANG ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");
    }

    protected function getInitDoc($txnid, $client, $mode)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<root>';
        $xml .= '<get-transaction-status>';
        $xml .= '<client-id>'.$client.'</client-id>';
        $xml .= '<transactions>';
        $xml .= '<transaction-id mode="'.$mode.'">'.$txnid.'</transaction-id>';
        $xml .= '</transaction>';
        $xml .= '</get-transaction-status>';
        $xml .= '</root>';
        return $xml;
    }

   public function testGetTxnStatus()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, 18, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 18, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 2, 18, true, 1)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 18, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount,cardid) VALUES (1001001, 100, 10099, 1100, 1,  18, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000,8)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_INIT_WITH_PSP_STATE .")");


        $iTxnID = 1001001;
        $iClientID = 10099;
        $iMode = 1;

        $obj_mPoint = new Home($this->_OBJ_DB, $this->_OBJ_TXT );
        $getTxnStatusResponse = $obj_mPoint->getTxnStatus((int)$iTxnID,(int)$iClientID,(int)$iMode);

        $this->assertStringContainsString('mpoint-id="1001001"', $getTxnStatusResponse);
        $this->assertStringContainsString('accoutid="1100" clientid="10099"', $getTxnStatusResponse);
        $this->assertStringContainsString('country-id="100" currency="208"', $getTxnStatusResponse);
    }


    public function testGetTxnStatusInvalidClientId()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, 18, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 18, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 2, 18, true, 1)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 18, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, 1,  18, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_INIT_WITH_PSP_STATE .")");

        $iTxnID = 1001001;
        $iClientID = 114;   // Invalid client ID
        $iMode = 1;

        $obj_mPoint = new Home($this->_OBJ_DB, $this->_OBJ_TXT );
        $getTxnStatusResponse = $obj_mPoint->getTxnStatus((int)$iTxnID,(int)$iClientID,(int)$iMode);

        $this->assertEmpty($getTxnStatusResponse);
    }

    public function testGetTxnStatusInvalidTxnId()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, 18, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 18, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 2, 18, true, 1)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 18, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, 1,  18, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_INIT_WITH_PSP_STATE .")");


        $iTxnID = 1001002;  // Invalid txn ID
        $iClientID = 10099;
        $iMode = 1;

        $obj_mPoint = new Home($this->_OBJ_DB, $this->_OBJ_TXT );
        $getTxnStatusResponse = $obj_mPoint->getTxnStatus((int)$iTxnID,(int)$iClientID,(int)$iMode);

        $this->assertEmpty($getTxnStatusResponse);
    }

    public function testGetOrphanAuthorizedTransactionList(): void
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client1', 'Tusername1', 'Tpassword1')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4030, '900-55150298', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, pspid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1001001, '900-55150298', 100, 10099, 1100, 100, 18, '', 5000, '127.0.0.1', TRUE, 1, 1,5000)");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, pspid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1001002, '900-55150298', 100, 10099, 1100, 100, 18, '', 5000, '127.0.0.1', TRUE, 1, 1,5000)");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, pspid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1001003, '900-55150298', 100, 10099, 1100, 100, 4, '', 5000, '127.0.0.1', TRUE, 1, 1,5000)");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, pspid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1001004, '900-55150298', 100, 10099, 1100, 100, 18, '', 5000, '127.0.0.1', TRUE, 1, 1,5000)");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, pspid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1001005, '900-55150298', 100, 10099, 1100, 100, 18, '', 5000, '127.0.0.1', TRUE, 1, 1,5000)");

        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001003, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001004, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");

		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (1001001, 5000,208,". Constants::iInitializeRequested. ",NULL,'done',10099)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (1001001, 5000,208,NULL,". Constants::iINPUT_VALID_STATE. ",'done',100,10099)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (1001001, 5000,208,". Constants::iAuthorizeRequested. ",NULL,'done',10099)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (1001001, 5000,208,NULL,". Constants::iPAYMENT_ACCEPTED_STATE. ",'done',102,10099)");

		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (1001002, 5000,208,". Constants::iInitializeRequested. ",NULL,'done',10099)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (1001003, 5000,208,NULL,". Constants::iINPUT_VALID_STATE. ",'done',100,10099)");

		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (1001003, 5000,208,". Constants::iInitializeRequested. ",NULL,'done',10099)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (1001003, 5000,208,NULL,". Constants::iINPUT_VALID_STATE. ",'done',100,10099)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (1001003, 5000,208,". Constants::iAuthorizeRequested. ",NULL,'done',10099)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (1001003, 5000,208,NULL,". Constants::iPAYMENT_ACCEPTED_STATE. ",'done',102,10099)");

		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (1001004, 5000,208,". Constants::iInitializeRequested. ",NULL,'done',10099)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (1001004, 5000,208,NULL,". Constants::iINPUT_VALID_STATE. ",'done',100,10099)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (1001004, 5000,208,". Constants::iAuthorizeRequested. ",NULL,'done',10099)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (1001004, 5000,208,NULL,". Constants::iPAYMENT_ACCEPTED_STATE. ",'done',102,10099)");

		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (1001005, 5000,208,". Constants::iInitializeRequested. ",NULL,'done',10099)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (1001005, 5000,208,NULL,". Constants::iINPUT_VALID_STATE. ",'done',100,10099)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (1001005, 5000,208,". Constants::iAuthorizeRequested. ",NULL,'done',10099)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (1001005, 5000,208,NULL,". Constants::iPAYMENT_ACCEPTED_STATE. ",'done',102,10099)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (1001005, 5000,208,". Constants::iCaptureRequested. ",NULL,'done',10099)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (1001005, 5000,208,NULL,". Constants::iPAYMENT_CAPTURED_STATE. ",'done',104,10099)");

         $obj_mPoint = new Home($this->_OBJ_DB, $this->_OBJ_TXT );
         sleep(2);
         $result = $obj_mPoint->getOrphanAuthorizedTransactionList(10099, '1 Second');

         $this->assertIsArray($result);
         $this->assertCount(3,$result);

         $result = $obj_mPoint->getOrphanAuthorizedTransactionList(10099, '5 minute');

         $this->assertIsArray($result);
         $this->assertCount(0,$result);

         $result = $obj_mPoint->getOrphanAuthorizedTransactionList(10099, '1 Second', 4);

         $this->assertIsArray($result);
         $this->assertCount(1,$result);

    }

    public function testGetAutoVoidConfig(): void
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client1', 'Tuser1', 'Tpass1')");
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10077, 1, 100, 'Test Client2', 'Tuser2', 'Tpass2')");
        $this->queryDB("INSERT INTO Client.AUTOVOIDCONFIG_TBL (id, clientid, pspid, expiry) VALUES (1, 10099, 18, '1 DAY')");
        $this->queryDB("INSERT INTO Client.AUTOVOIDCONFIG_TBL (id, clientid, pspid, expiry) VALUES (2, 10077, 18, '2 DAY')");
        $this->queryDB("INSERT INTO Client.AUTOVOIDCONFIG_TBL (id, clientid, pspid, expiry) VALUES (3, 10099, 4, '3 DAY')");

        $obj_mPoint = new Home($this->_OBJ_DB, $this->_OBJ_TXT );

        $result = $obj_mPoint->getAutoVoidConfig();
        $this->assertIsArray($result);
        $this->assertCount(3,$result);

        $result = $obj_mPoint->getAutoVoidConfig(10099);
        $this->assertIsArray($result);
        $this->assertCount(2,$result);

        $result = $obj_mPoint->getAutoVoidConfig(10077, 18);
        $this->assertIsArray($result);
        $this->assertCount(1,$result);
        $this->assertEquals(10077, $result[0]['CLIENTID']);
        $this->assertEquals(18, $result[0]['PSPID']);
        $this->assertEquals('2 DAY', $result[0]['EXPIRY']);

        $result = $obj_mPoint->getAutoVoidConfig(10077, 4);
        $this->assertIsArray($result);
        $this->assertCount(0,$result);

    }


    public function tearDown():void
    {
        $this->_OBJ_DB->disConnect();
        parent::tearDown();
    }

}
