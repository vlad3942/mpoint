<?php
/**
 * User: jot
 * Date: 24-03-15
 * Time: 19:46
 */

require_once __DIR__. '/authorizeAPITest.php';

class GlobalCollectAuthorizeAPITest extends AuthorizeAPITest
{
		
    public function testSuccessfulAuthorize()
    {
    	$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
    	
    	//$this->queryDB("INSERT INTO System.PSP_Tbl (id, name) VALUES (20, 'GlobalCollect')");
    	/* $this->queryDB("INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) SELECT countryid, 20, name FROM System.PSPCurrency_Tbl WHERE pspid = 4");
    	$this->queryDB("INSERT INTO System.PSPCard_Tbl (cardid, pspid) SELECT cardid, 20 FROM System.PSPCard_Tbl WHERE pspid = 4"); */
    	$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
    	$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
    	$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
    	$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (113, 20, '337', '35e849953d7a4b5e', 'zGMYB+75ieEbehRxAF89Pnuek3mDp3xAd0/VofCIDIc=')");
    	$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 20, '-1')");
    	$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled) VALUES (113, 8, 20, true)"); //Authorize must be possible even with disabled cardac
    	$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '30206172', TRUE, 'profilePass', TRUE)");
    	$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");
    	$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 8, 20, '501910******3742', '06/24', TRUE, 113, NULL, '33713514-1fc9-4b8e-b1c8-dbe759242bde', NULL)");
    	$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled) VALUES (1001001, 100, 113, 1100, 1,  20, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE)");
    	
    	//$xml = $this->getAuthDoc(113, 1100, 1001001, 100, 'profilePass');
    	
    	$xml = '<?xml version="1.0" encoding="UTF-8"?>
				<root>
				  <authorize-payment account="1100" client-id="113">
				    <transaction type-id="1009" id="1001001">
				      <card type-id="8" id="61775">
				        <amount country-id="100">10025</amount>
				        <cvc>218</cvc>
				      </card>
				    </transaction>
				    <password>profilePass</password>
				    <client-info language="da" version="1.20" platform="iOS/8.1.3">
				      <mobile operator-id="10000" country-id="100">30206172</mobile>
				      <device-id>32E475F7295C488EBEA2C0FAF455915D14298774</device-id>
				    </client-info>
				  </authorize-payment>
				</root>
    			';
    	
    	
    	$this->_httpClient->connect();
    	
    	$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
    	$sReplyBody = $this->_httpClient->getReplyBody();
    	
    	$this->assertEquals(200, $iStatus);
    	$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="100">Payment Authorized using Stored Card</status></root>', $sReplyBody);
    	
    	$res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY ID ASC");
    	$this->assertTrue(is_resource($res) );
    	
    	$aStates = array();
    	while ($row = pg_fetch_assoc($res) )
    	{
    		$aStates[] = $row["stateid"];
    	}
    	
    	//TODO: Rewrite test so it supports both Netaxept and DIBS. Netaxept completes the txn within the callback, DIBS does it during the authorize API flow
    	$this->assertEquals(5, count($aStates) );
    	    	
    	$s = 0;
    	//TODO: Rewrite test so it supports both Netaxept and DIBS. Netaxept completes the txn within the callback, DIBS does it during the authorize API flow
    	$this->assertEquals(Constants::iPAYMENT_WITH_ACCOUNT_STATE, $aStates[$s++]);
    	$this->assertEquals(Constants::iPAYMENT_ACCEPTED_STATE, $aStates[$s++]);
    	$this->assertEquals(Constants::iCB_CONSTRUCTED_STATE, $aStates[$s++]);
    	$this->assertEquals(Constants::iCB_CONNECTED_STATE, $aStates[$s++]);
    	$this->assertEquals(Constants::iCB_ACCEPTED_STATE, $aStates[$s++]);
    	
    	
    	/* Test that euaid has been set on txn */
    	$res =  $this->queryDB("SELECT t.euaid, et.accountid FROM Log.Transaction_Tbl t LEFT JOIN Enduser.Transaction_Tbl et ON et.txnid = t.id WHERE t.id = 1001001");
    	$this->assertTrue(is_resource($res) );
    	$row = pg_fetch_assoc($res);
    	
    	$this->assertEquals(5001, $row["euaid"]);
    	//TODO: Rewrite test so it supports both Netaxept and DIBS. Netaxept completes the txn within the callback, DIBS does it during the authorize API flow
    	$this->assertEquals(5001, $row["accountid"]);
    }

	public function testSuccessfulAuthorizeIncludingAutoCapture()
	{
		//TODO: Implement test case
		$this->assertTrue(true);
	}

}