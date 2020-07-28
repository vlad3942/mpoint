<?php
/**
 * User: Abhinav Shaha
 * Date: 27-07-20
 * Time: 06:00
 */
require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once sAPI_CLASS_PATH . 'simpledom.php';
require_once __DIR__ . '/../../api/classes/txn_passbook.php';
require_once __DIR__ . '/../../api/classes/passbookentry.php';

class TxnPassbookEntriesToXMLFunctionTest extends baseAPITest
{
	private $_OBJ_DB;
	protected $_aHTTP_CONN_INFO;

	public function setUp()
	{
		parent::setUp(TRUE);
		global $aHTTP_CONN_INFO;
		$this->bIgnoreErrors = true;
		$this->_aHTTP_CONN_INFO = $aHTTP_CONN_INFO;
		$this->_OBJ_DB = RDB::produceDatabase($this->mPointDBInfo);
	}

	public function testTxnPassbookEntriesToXMLFunction()
	{
		$pspID = Constants::iWIRE_CARD_PSP;

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Admin.User_Tbl (id, countryid, firstname, lastname, email, username, passwd) VALUES (1, 100, 'Test', 'TestTest', 'test@cellpointmobile.com', 'test', 'testtest')");
		$this->queryDB("INSERT INTO Admin.Access_Tbl (id, clientid, userid) VALUES (1, 113, 1)");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name, username, passwd) VALUES (1, 113, $pspID, '1', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (113, 17, $pspID)");
		$this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '1513-005', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid) VALUES (1001010, 100, 113, 1100, 100, $pspID, '1515', '1513-005', 'test.com', 5000, '127.0.0.1', TRUE, 1, 1)");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001010, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");

		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001010, 5000,208,". Constants::iInitializeRequested. ",NULL,'done',113)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001010, 5000,208,NULL,". Constants::iINPUT_VALID_STATE. ",'done',100,113)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (102,1001010, 5000,208,". Constants::iAuthorizeRequested. ",NULL,'done',113)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (103,1001010, 5000,208,NULL,". Constants::iPAYMENT_ACCEPTED_STATE. ",'done',102,113)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid,extref) VALUES (104,1001010, 5000,208,". Constants::iCaptureRequested. ",NULL,'done',113, 5802341953600)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (105,1001010, 5000,208,NULL,". Constants::iPAYMENT_CAPTURED_STATE. ",'done',104,113)");

		$performedOptArray = array(Constants::iPAYMENT_CAPTURED_STATE);

		$obj_TxnInfo = TxnInfo::produceInfo(1001010, $this->_OBJ_DB);
		$txnPassbookObj = TxnPassbook::Get($this->_OBJ_DB, $obj_TxnInfo->getID(),$obj_TxnInfo->getClientConfig()->getID());
		$xml = $txnPassbookObj->toXML($performedOptArray);

		$obj_DOM = simpledom_load_string($xml);

		$this->assertEquals(105, (int)$obj_DOM->{'performedopt'}->id);
		$this->assertEquals(2001, (int)$obj_DOM->{'performedopt'}->code);
		$this->assertEquals('done', $obj_DOM->{'performedopt'}->status);
		$this->assertEquals(5000, (int)$obj_DOM->{'performedopt'}->amount);
		$this->assertEquals(208, (int)$obj_DOM->{'performedopt'}->amount['currency-id']);
		$this->assertEquals(104, (int)$obj_DOM->{'performedopt'}->extref);
		$this->assertEquals(1, count($obj_DOM->{'performedopt'}->created));
		$this->assertEquals(1, count($obj_DOM->{'performedopt'}->modified));
		$this->assertNotContains('', $obj_DOM->{'performedopt'}->created);
		$this->assertNotContains('', $obj_DOM->{'performedopt'}->modified);

		$this->assertEquals(104, (int)$obj_DOM->{'performedopt'}->{'requestedopt'}->id);
		$this->assertEquals(5011, (int)$obj_DOM->{'performedopt'}->{'requestedopt'}->code);
		$this->assertEquals('done', $obj_DOM->{'performedopt'}->{'requestedopt'}->status);
		$this->assertEquals(5000, (int)$obj_DOM->{'performedopt'}->{'requestedopt'}->amount);
		$this->assertEquals(208, (int)$obj_DOM->{'performedopt'}->{'requestedopt'}->amount['currency-id']);
		$this->assertEquals(5802341953600, (int)$obj_DOM->{'performedopt'}->{'requestedopt'}->extref);
		$this->assertEquals(1, count($obj_DOM->{'performedopt'}->{'requestedopt'}->created));
		$this->assertEquals(1, count($obj_DOM->{'performedopt'}->{'requestedopt'}->modified));
		$this->assertNotContains('', $obj_DOM->{'performedopt'}->{'requestedopt'}->created);
		$this->assertNotContains('', $obj_DOM->{'performedopt'}->{'requestedopt'}->modified);
	}

	public function tearDown()
	{
		$this->_OBJ_DB->disConnect();
		parent::tearDown();
	}
}