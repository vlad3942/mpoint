<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class DIBSCallbackAPITest extends baseAPITest
{
    protected $_aMPOINT_CONN_INFO;

    public function __construct()
    {
        $this->constHTTPClient();
    }

    public function constHTTPClient()
    {
        global $aMPOINT_CONN_INFO;
        $aMPOINT_CONN_INFO['path'] = "/callback/dibs.php";
        $aMPOINT_CONN_INFO["contenttype"] = "application/x-www-form-urlencoded";
        $this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO) );
    }

    public function testSuccessfulCallback()
    {
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
		$pspID = Constants::iDIBS_PSP;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '1')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (113, 17, $pspID)");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, mobile, pspid, extid, callbackurl, amount, ip, enabled, keywordid) VALUES (1001001, '900-55150298', ". Constants::iPURCHASE_VIA_APP .", 113, 1100, 100, '29612109', null, null, '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1)");

		trigger_error("mRetail expect external transaction id: 15469928");

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders(), "mpointid=1001001&language=da&transact=15469928&amount=5000&cardid=". Constants::iVISA_CARD);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);

        $res =  $this->queryDB("SELECT extid, stateid FROM Log.Message_Tbl m, Log.Transaction_Tbl t WHERE t.id = 1001001 AND m.txnid = t.id");
        $this->assertTrue(is_resource($res) );

        $aStates = array();
        while ($row = pg_fetch_assoc($res) )
        {
            $aStates[] = $row["stateid"];
			$this->assertEquals("15469928", $row["extid"]);
        }

        $this->assertTrue(is_int(array_search(Constants::iPAYMENT_ACCEPTED_STATE, $aStates) ) );
    }

	public function testSuccessfulAutoCapture()
	{
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
		$pspID = Constants::iDIBS_PSP;

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '1')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (113, 17, $pspID)");
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, mobile, pspid, extid, callbackurl, amount, ip, enabled, keywordid, auto_capture) VALUES (1001001, '900-55150298', ". Constants::iPURCHASE_VIA_APP .", 113, 1100, 100, '29612109', null, null, '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, TRUE)");

		trigger_error("mRetail expect external transaction id: 15469922");

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders(), "mpointid=1001001&language=da&transact=15469922&amount=5000&cardid=". Constants::iVISA_CARD);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);

		$res =  $this->queryDB("SELECT extid, stateid FROM Log.Message_Tbl m, Log.Transaction_Tbl t WHERE t.id = 1001001 AND m.txnid = t.id");
		$this->assertTrue(is_resource($res) );

		$aStates = array();
		while ($row = pg_fetch_assoc($res) )
		{
			$aStates[] = $row["stateid"];
			$this->assertEquals("15469922", $row["extid"]);
		}

		$this->assertTrue(is_int(array_search(Constants::iPAYMENT_ACCEPTED_STATE, $aStates) ) );
		$this->assertTrue(is_int(array_search(Constants::iPAYMENT_CAPTURED_STATE, $aStates) ) );
	}

}
