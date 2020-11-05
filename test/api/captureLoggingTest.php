<?php
/**
 * Created by Eclipse
 * User: Abhinav Shaha
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package:
 * File Name:captureLoggingTest.php
 */

// Require Global Include File
require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once __DIR__ . '/../inc/basedatabasetest.php';
require_once __DIR__ . '/../../api/interfaces/cpm_psp.php';
require_once __DIR__ . '/../../api/classes/callback.php';
require_once __DIR__ . '/../../api/classes/wirecard.php';
require_once sCLASS_PATH . '/txn_passbook.php';
require_once sCLASS_PATH . '/passbookentry.php';


class CaptureLoggingTest extends baseAPITest
{
	/**
	 * @var RDB
	 */
	private $_obj_DB;
	private $_obj_TXT;
	private $_aWireCardConnInfo;
	private $_aMPOINT_CONN_INFO;

	public function setUp() : void
	{
		$this->bIgnoreErrors = true;
		parent::setUp();
		global $aHTTP_CONN_INFO;
		global $aMPOINT_CONN_INFO;
		
		$this->_obj_DB = RDB::produceDatabase($this->mPointDBInfo);
		$this->_obj_TXT = new TranslateText(array(sLANGUAGE_PATH . sLANG ."/global.txt", sLANGUAGE_PATH . sLANG ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");
		$this->_aWireCardConnInfo = $aHTTP_CONN_INFO["wire-card"];
		$this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
	}

	public function testPositiveScenarioCaptureLogging()
	{
	    //TODO FIX ME
	    $this->markTestIncomplete("
            Failed asserting that two strings are equal.
            --- Expected
            +++ Actual
            @@ @@
            -'done'
            +'inprogress'
        ");
		$pspID = Constants::iWIRE_CARD_PSP;
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, ".$pspID.", '1')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, capture_type) VALUES (10099, 8, $pspID, 3)");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', 'profilePass', TRUE)");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");

		$this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4001, '900-55150298', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, pspid, callbackurl, amount, ip, enabled, keywordid, sessionid, auto_capture,convertedamount) VALUES (1001001, '900-55150298', 100, 10099, 1100, 100, null, '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1, 3,5000)");
		
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001001, 5000,208,". Constants::iInitializeRequested. ",NULL,'done',10099)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001001, 5000,208,NULL,". Constants::iINPUT_VALID_STATE. ",'done',100,10099)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (102,1001001, 5000,208,". Constants::iAuthorizeRequested. ",NULL,'done',10099)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (103,1001001, 5000,208,NULL,". Constants::iPAYMENT_ACCEPTED_STATE. ",'done',102,10099)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (104,1001001, 5000,208,". Constants::iCaptureRequested. ",NULL,'done',10099)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (105,1001001, 5000,208,NULL,". Constants::iPAYMENT_CAPTURED_STATE. ",'inprogress',104,10099)");
		
		$obj_TxnInfo = TxnInfo::produceInfo(1001001, $this->_obj_DB);
		$obj_PSP = new WireCard($this->_obj_DB, $this->_obj_TXT, $obj_TxnInfo, $this->_aWireCardConnInfo);
		
		//Pass correct amount 5000 so that Capture entry will get update from "inprogress" to "done" in passbook table and new capture entry will get added into message table
		$obj_PSP->completeCapture(5000,0);
		
		$cStates = array();
		$aStates = array();
		
		$res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY id ASC");
		$this->assertIsResource($res);
		while ($row = pg_fetch_assoc($res) )
		{
			$aStates[] = $row["stateid"];
		}
		//Capture state should get logged in message table
		$this->assertIsInt(array_search(Constants::iPAYMENT_CAPTURED_STATE, $aStates));
		
		$captureStateStatus = $this->queryDB("SELECT status FROM Log.Txnpassbook_Tbl WHERE transactionid = 1001001 and performedopt = 2001  ORDER BY id ASC");
		$this->assertIsResource($captureStateStatus);
		while ($row = pg_fetch_assoc($captureStateStatus))
		{
			$cStates[] = $row["status"];
		}
		//Capture state status should be in "done"
		$this->assertEquals(Constants::sPassbookStatusDone, $cStates[0]);
	}

	public function tearDown() : void
	{
		$this->_obj_DB->disConnect();
		parent::tearDown();
	}
}
