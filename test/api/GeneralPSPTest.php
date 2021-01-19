<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package:
 * File Name: GeneralPSPTest
 */

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once __DIR__ . '/../../api/classes/general.php';
require_once __DIR__ . '/../../api/classes/home.php';
require_once __DIR__ . '/../../api/classes/enduser_account.php';
require_once __DIR__ . '/../../api/classes/callback.php';
require_once __DIR__ . '/../../api/interfaces/cpm_psp.php';
require_once __DIR__ . '/../../api/interfaces/cpm_acquirer.php';
require_once __DIR__ . '/../../api/classes/GeneralPSP.php';
require_once __DIR__ . '/../../api/classes/txn_passbook.php';
require_once __DIR__ . '/../../api/classes/passbookentry.php';
require_once __DIR__ . '/../../api/classes/wirecard.php';
require_once __DIR__ . '/../../api/classes/refund.php';

class GeneralPSPTest extends baseAPITest
{
    private $_obj_TXT;
    private GeneralPSP $_obj_PSP;
    private TxnInfo $_obj_txnInfo;
    private RDB $_obj_DB;
    private SimpleXMLElement $_simpleXMLElement;

    public function setUp($isDBSetupRequired = TRUE): void
    {
        parent::setUp($isDBSetupRequired); // TODO: Change the autogenerated stub
        $this->setupDB();
        $this->_simpleXMLElement = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><root></root>');
    }

    private function setupDB()
    {
        global $aMPOINT_CONN_INFO;

        $pspID = 18;
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, " . $pspID . ", '1')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10099, 8, $pspID)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4030, '900-55150298', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, pspid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1001001, '900-55150298', 100, 10099, 1100, 100, $pspID, '', 5000, '127.0.0.1', TRUE, 1, 1,5000)");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, pspid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1001002, '900-55150298', 100, 10099, 1100, 100, $pspID, '', 5000, '127.0.0.1', TRUE, 1, 1,5000)");

        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_CAPTURED_STATE. ")");

		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001001, 5000,208,". Constants::iInitializeRequested. ",NULL,'done',10099)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001001, 5000,208,NULL,". Constants::iINPUT_VALID_STATE. ",'done',100,10099)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (102,1001001, 5000,208,". Constants::iAuthorizeRequested. ",NULL,'done',10099)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (103,1001001, 5000,208,NULL,". Constants::iPAYMENT_ACCEPTED_STATE. ",'done',102,10099)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (104,1001001, 5000,208,". Constants::iCaptureRequested. ",NULL,'done',10099)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (105,1001001, 5000,208,NULL,". Constants::iPAYMENT_CAPTURED_STATE. ",'done',104,10099)");


        $this->_obj_DB = RDB::produceDatabase($this->mPointDBInfo);
        $this->_obj_txnInfo = TxnInfo::produceInfo(1001001, $this->_obj_DB);

        $this->_obj_TXT = new TranslateText([sLANGUAGE_PATH . sLANG . "/global.txt", sLANGUAGE_PATH . sLANG . "/custom.txt"], sSYSTEM_PATH, 0, "UTF-8");
        $this->_obj_PSP = new GeneralPSP($this->_obj_DB, $this->_obj_TXT, $this->_obj_txnInfo, $aMPOINT_CONN_INFO, NULL, NULL);
    }

    public function testInvoice()
    {
        try {
            $this->_obj_PSP->invoice('', -1);
            $this->assertTrue(FALSE);
        }
        catch (BadMethodCallException $e) {
            $this->assertEquals('Method invoice is not supported by GeneralPSP class', $e->getMessage());
        }
    }

    public function testStatus()
    {
        try {
            $this->_obj_PSP->status();
            $this->assertTrue(FALSE);
        }
        catch (BadMethodCallException $e) {
            $this->assertEquals('Method status is not supported by GeneralPSP class', $e->getMessage());
        }
    }

    public function testGetExternalPaymentMethods()
    {
        try {
            $this->_obj_PSP->getExternalPaymentMethods($this->_simpleXMLElement);
            $this->assertTrue(FALSE);
        }
        catch (BadMethodCallException $e) {
            $this->assertEquals('Method getExternalPaymentMethods is not supported by GeneralPSP class', $e->getMessage());
        }
    }

    public function testGetPaymentMethods()
    {
        try {
            $this->_obj_PSP->getPaymentMethods($this->_obj_PSP->getPSPConfig());
            $this->assertTrue(FALSE);
        }
        catch (BadMethodCallException $e) {
            $this->assertEquals('Method getPaymentMethods is not supported by GeneralPSP class', $e->getMessage());
        }
    }

    public function testTokenize()
    {
        try {
            $this->_obj_PSP->tokenize([], $this->_obj_PSP->getPSPConfig(), $this->_simpleXMLElement);
            $this->assertTrue(FALSE);
        }
        catch (BadMethodCallException $e) {
            $this->assertEquals('Method tokenize is not supported by GeneralPSP class', $e->getMessage());
        }
    }

    public function testRedeem()
    {
        try {
            $this->_obj_PSP->redeem(1,-1);
            $this->assertTrue(FALSE);
        }
        catch (BadMethodCallException $e) {
            $this->assertEquals('Method redeem is not supported by GeneralPSP class', $e->getMessage());
        }
    }

    public function testCancel()
    {
        try {
            $this->_obj_PSP->cancel(-1);
            $this->assertTrue(FALSE);
        }
        catch (BadMethodCallException $e) {
            $this->assertEquals('Method cancel is not supported by GeneralPSP class', $e->getMessage());
        }
    }

    public function testCapture()
    {
        try {
            $this->_obj_PSP->capture(-1);
            $this->assertTrue(FALSE);
        }
        catch (BadMethodCallException $e) {
            $this->assertEquals('Method capture is not supported by GeneralPSP class', $e->getMessage());
        }
    }

    public function testGetPaymentData()
    {
        try {
            $this->_obj_PSP->getPaymentData($this->_obj_PSP->getPSPConfig(), $this->_simpleXMLElement);
            $this->assertTrue(FALSE);
        }
        catch (BadMethodCallException $e) {
            $this->assertEquals('Method getPaymentData is not supported by GeneralPSP class', $e->getMessage());
        }
    }

    public function testVoid()
    {
        try {
            $this->_obj_PSP->void(-1);
            $this->assertTrue(FALSE);
        }
        catch (BadMethodCallException $e) {
            $this->assertEquals('Method void is not supported by GeneralPSP class', $e->getMessage());
        }
    }

    public function testInitCallback()
    {
        try {
            $this->_obj_PSP->initCallback($this->_obj_PSP->getPSPConfig(), $this->_obj_txnInfo, 2000, 'Auth', 1);
            $this->assertTrue(FALSE);
        }
        catch (BadMethodCallException $e) {
            $this->assertEquals('Method initCallback is not supported by GeneralPSP class', $e->getMessage());
        }
    }

    public function testRefund()
    {
        try {
            $this->_obj_PSP->refund(-1);
            $this->assertTrue(FALSE);
        }
        catch (BadMethodCallException $e) {
            $this->assertEquals('Method refund is not supported by GeneralPSP class', $e->getMessage());
        }
    }

    public function testAuthenticate()
    {
        try {
            $this->_obj_PSP->authenticate('<?xml version="1.0" encoding="UTF-8"?><root></root>', new stdClass());
            $this->assertTrue(FALSE);
        }
        catch (BadMethodCallException $e) {
            $this->assertEquals('Method authenticate is not supported by GeneralPSP class', $e->getMessage());
        }
    }

    public function testVoidTransaction()
    {
        global $aHTTP_CONN_INFO;
        unset($aHTTP_CONN_INFO["wire-card"]["paths"]["status"]);
        $aHTTP_CONN_INFO["wire-card"]["paths"]["refund"] = "/_test/simulators/mobilepay/refund.php";
        $this->_obj_PSP->setTxnInfo(1001001);
        $response = $this->_obj_PSP->voidTransaction(5000);
        $this->assertIsArray($response);
        $this->assertArrayHasKey(1000,$response);
        $this->assertTrue( $this->_obj_PSP->getTxnInfo()->hasEitherState($this->_obj_DB, Constants::iPAYMENT_REFUNDED_STATE));
    }

    public function testVoidTransactionDecline()
    {
        global $aHTTP_CONN_INFO;
        $this->bIgnoreErrors = TRUE; //endpoint URL is invalid so that mpoint will generate error
        unset($aHTTP_CONN_INFO["wire-card"]["paths"]["status"]);
        $aHTTP_CONN_INFO["wire-card"]["paths"]["refund"] = '/_test/simulators/mobilepay/refund_not_found.php';
        $this->_obj_PSP->setTxnInfo(1001001);
        $response = $this->_obj_PSP->voidTransaction(5000);
        $this->assertIsArray($response);
        $this->assertArrayHasKey(999,$response);
    }

    public function testSetTxnInfo()
    {
        $this->_obj_PSP->setTxnInfo(1001002);
        $this->assertEquals(1001002, $this->_obj_PSP->getTxnInfo()->getID());
    }

    public function testGetPSPID()
    {
        $this->assertEquals(-1, $this->_obj_PSP->getPSPID());
    }

    public function testNotifyClient()
    {
        try {
            $this->_obj_PSP->notifyClient(2000, [], new SurePayConfig(1, 2));
            $this->assertTrue(FALSE);
        }
        catch (BadMethodCallException $e) {
            $this->assertEquals('Method notifyClient is not supported by GeneralPSP class', $e->getMessage());
        }
    }

    public function testAuthorize()
    {
        try {
            $this->_obj_PSP->authorize($this->_obj_PSP->getPSPConfig(), new stdClass());
            $this->assertTrue(FALSE);
        }
        catch (BadMethodCallException $e) {
            $this->assertEquals('Method authorize is not supported by GeneralPSP class', $e->getMessage());
        }
    }

    public function testProcessCallback()
    {
        try {
            $this->_obj_PSP->processCallback($this->_obj_PSP->getPSPConfig(), $this->_simpleXMLElement);
            $this->assertTrue(FALSE);
        }
        catch (BadMethodCallException $e) {
            $this->assertEquals('Method processCallback is not supported by GeneralPSP class', $e->getMessage());
        }
    }

    public function testInitialize()
    {
        try {
            $this->_obj_PSP->initialize($this->_obj_PSP->getPSPConfig(), $this->_simpleXMLElement);
            $this->assertTrue(FALSE);
        }
        catch (BadMethodCallException $e) {
            $this->assertEquals('Method initialize is not supported by GeneralPSP class', $e->getMessage());
        }
    }

    public function testCallback()
    {
        try {
            $this->_obj_PSP->callback($this->_obj_PSP->getPSPConfig(), $this->_simpleXMLElement, $this->_simpleXMLElement);
            $this->assertTrue(FALSE);
        }
        catch (BadMethodCallException $e) {
            $this->assertEquals('Method callback is not supported by GeneralPSP class', $e->getMessage());
        }
    }

    public function testPostStatus()
    {
        try {
            $this->_obj_PSP->postStatus(new stdClass());
            $this->assertTrue(FALSE);
        }
        catch (BadMethodCallException $e) {
            $this->assertEquals('Method postStatus is not supported by GeneralPSP class', $e->getMessage());
        }
    }

}