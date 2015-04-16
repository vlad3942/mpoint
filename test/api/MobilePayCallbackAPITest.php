<?php

require_once __DIR__ . '/../inc/testinclude.php';

class MobilePayCallbackAPITest extends mPointBaseAPITest
{
    protected $_aMPOINT_CONN_INFO;

    public function __construct()
    {
        $this->constHTTPClient();
    }

    public function constHTTPClient()
    {
        global $aMPOINT_CONN_INFO;
        $aMPOINT_CONN_INFO['path'] = "/callback/mobilepay.php";
        $aMPOINT_CONN_INFO["contenttype"] = "text/xml";
        $this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO) );
    }

    public function testSuccessfulCallback()
    {
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
		$pspID = Constants::iMOBILEPAY_PSP;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name) VALUES (113, 1, 100, 'Test Client')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '1')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (113, 17, $pspID)"); //Mobilepay
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, pspid, callbackurl, amount, ip, enabled, keywordid) VALUES (1001001, '900-55150298', 100, 113, 1100, 100, $pspID, '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1)");

        $this->_httpClient->connect();

		/**
		 *	<root>
		 *		<callback merchant-id="">
		 *			<transaction id="" order-no="" external-id="">
		 *				<amount country-id="" currency="" symbol="" format=""></amount>
		 *				<status code="">message</status>
		 *			</transaction>
		 *		</callback>
		 *	</root>
		 */

		$xml  = '<root>';
		$xml .= '<callback>';
		$xml .= '<psp-config psp-id="'. Constants::iMOBILEPAY_PSP .'"></psp-config>';
		$xml .= '<transaction order-no="900-55150298" external-id="15469928">';
		$xml .= '<amount country-id="100" currency="DKK" symbol="kr." format="">10050</amount>';
		$xml .= '<status code="1000">Success</status>';
		$xml .= '</transaction>';
		$xml .= '</callback>';
		$xml .= '</root>';

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders(), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        //$this->assertEquals("msg=1000", $sReplyBody);

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

}
