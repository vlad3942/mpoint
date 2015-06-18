<?php
/**
 * User: jot
 * Date: 24-03-15
 * Time: 19:46
 */
require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once sCLASS_PATH . '/status.php';

class ActiveTransactionsTest extends mPointBaseAPITest
{
	/**
	 * @var RDB
	 */
	private $_obj_DB;
	private $_obj_TXT;
	private $_aMPOINT_CONN_INFO;


	public function setUp()
	{
		parent::setUp();

		$this->_obj_DB = RDB::produceDatabase($this->mPointDBInfo);
		$this->_obj_TXT = new TranslateText(array(sLANGUAGE_PATH . sLANG ."/global.txt", sLANGUAGE_PATH . sLANG ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");
	}

    public function testTwoOutOfFourRelevantTransaction()
    {
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
		$now = time();
		$pspID = 2; //DIBS -- irrelevant for the test though
		$sCreated = date('Y-m-d H:i:s', $now-3600); // now minus 1 hour
		$sCreatedOld = date('Y-m-d H:i:s', $now-3600*24*10); // now minus 10 days

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'test', 'testtest')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Admin.User_Tbl (id, countryid, firstname, lastname, email, username, passwd) VALUES (1, 100, 'Test', 'TestTest', 'test@cellpointmobile.com', 'test', 'testtest')");
		$this->queryDB("INSERT INTO Admin.Access_Tbl (id, clientid, userid) VALUES (1, 113, 1)");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name, username, passwd) VALUES (1, 113, $pspID, '1', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (113, 17, $pspID)");
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, created, keywordid) VALUES (1001001, 100, 113, 1100, 100, $pspID, '1515', '1513-005', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, '". $sCreated. "', 1)");
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, created, keywordid) VALUES (1001002, 100, 113, 1100, 100, $pspID, '1515', '1513-005', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, '". $sCreated. "', 1)");
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, created, keywordid) VALUES (1001003, 100, 113, 1100, 100, $pspID, '1515', '1513-005', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, '". $sCreated. "', 1)");
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, created, keywordid) VALUES (1001004, 100, 113, 1100, 100, $pspID, '1515', '1513-005', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, '". $sCreatedOld. "', 1)");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001002, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001002, ". Constants::iPAYMENT_CAPTURED_STATE. ")");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001003, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001004, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");

		$obj_Status = new Status($this->_obj_DB, $this->_obj_TXT);
		$aTxns = $obj_Status->getActiveTransactions($now-3600*24*5, $now, true); // Get active transactions created in the period from NOW-5days to NOW

		$this->assertEquals(2, count($aTxns) );
		$aIDs = array();

		foreach ($aTxns as $txn)
		{
			$aIDs[] = intval($txn["ID"]);
		}

		$this->assertContains(1001001, $aIDs);
		$this->assertContains(1001003, $aIDs);
    }

	public function tearDown()
	{
		$this->_obj_DB->disConnect();
		parent::tearDown();
	}


}