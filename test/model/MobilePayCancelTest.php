<?php
/**
 * User: jot
 * Date: 24-03-15
 * Time: 19:46
 */
require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once sINTERFACE_PATH . '/cpm_psp.php';
require_once sCLASS_PATH . '/mobilepay.php';

class MobilePayCancelTest extends baseAPITest
{
	/**
	 * @var RDB
	 */
	private $_obj_DB;
	private $_obj_TXT;
	private $_aMobilePayConnInfo;
	private $_aMPOINT_CONN_INFO;


	public function setUp()
	{
		parent::setUp();
		global $aHTTP_CONN_INFO;
		global $aMPOINT_CONN_INFO;

		$this->_obj_DB = RDB::produceDatabase($this->mPointDBInfo);
		$this->_obj_TXT = new TranslateText(array(sLANGUAGE_PATH . sLANG ."/global.txt", sLANGUAGE_PATH . sLANG ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");
		$this->_aMobilePayConnInfo = $aHTTP_CONN_INFO["mobilepay"];
		$this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
	}

    public function testSuccessfulCancel()
    {
		$pspID = Constants::iMOBILEPAY_PSP;
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'test', 'testtest')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Admin.User_Tbl (id, countryid, firstname, lastname, email, username, passwd) VALUES (1, 100, 'Test', 'TestTest', 'test@cellpointmobile.com', 'test', 'testtest')");
		$this->queryDB("INSERT INTO Admin.Access_Tbl (id, clientid, userid) VALUES (1, 113, 1)");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name, username, passwd) VALUES (1, 113, $pspID, '1', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (113, 17, $pspID)"); //Mobilepay
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid) VALUES (1001001, 100, 113, 1100, 100, $pspID, '1515', '1513-005', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1)");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");

		$obj_TxnInfo = TxnInfo::produceInfo(1001001, $this->_obj_DB);

		$obj_PSP = new MobilePay($this->_obj_DB, $this->_obj_TXT, $obj_TxnInfo, $this->_aMobilePayConnInfo);
		$obj_PSP->cancel();

		$res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY created ASC");
		$this->assertTrue(is_resource($res) );

		$aStates = array();
		while ($row = pg_fetch_assoc($res) )
		{
			$aStates[] = $row["stateid"];
		}

		$this->assertEquals(5, count($aStates) );
		$this->assertEquals(Constants::iPAYMENT_ACCEPTED_STATE, $aStates[0]);
		$this->assertEquals(Constants::iPAYMENT_CANCELLED_STATE, $aStates[1]);
		$this->assertEquals(Constants::iCB_CONSTRUCTED_STATE, $aStates[2]);
		$this->assertEquals(Constants::iCB_CONNECTED_STATE, $aStates[3]);
		$this->assertEquals(Constants::iCB_ACCEPTED_STATE, $aStates[4]);
    }

	public function tearDown()
	{
		$this->_obj_DB->disConnect();
		parent::tearDown();
	}


}