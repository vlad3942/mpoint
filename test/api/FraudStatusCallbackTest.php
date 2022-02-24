<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class FraudStatusCallbackTest extends baseAPITest
{


    protected $_aHTTP_CONN_INFO;

    public function __construct()
    {
        parent::__construct();
        $this->constHTTPClient();
    }

    public function constHTTPClient()
    {
        global $aMPOINT_CONN_INFO;
        $aMPOINT_CONN_INFO['path'] = "/callback/update_fraud_status.php";
        $aMPOINT_CONN_INFO["contenttype"] = "text/xml";
        $this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO) );
    }

    public function testSuccessUpdateFraudStatus()
    {
        $pspID = 2;
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] . "://" . $this->_aMPOINT_CONN_INFO["host"] . "/_test/simulators/mticket/callback.php";

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 113, 'app')");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('IS_LEGACY_CALLBACK_FLOW', 'true', 113, 'client', 0);");
        $this->queryDB("INSERT INTO client.services_tbl (clientid, legacy_flow_enabled) VALUES(113, true);");
        //$this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 7)");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (113, 7, $pspID, true, 1)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 7, $pspID, '501910******3742', '06/24', TRUE, 113, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, auto_capture, enabled, currencyid, sessionid,convertedamount,convertedcurrencyid,cardid) VALUES (1001001, 100, 113, 1100, 1,  $pspID, 5001, 100, '103-1418291','".$sCallbackURL."', 5000, '127.0.0.1', 1, TRUE,208, 1,5000,208,8)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_CAPTURED_STATE. ")");
        $this->queryDB("INSERT INTO log.additional_data_tbl (name, value,type,externalid) VALUES ('pre_auth_ext_id','6208016239346599404008','Transaction',1001001)");

        $xml = $this->getDoc(141,'6208016239346599404008','103-1418291');
        $this->_httpClient->connect();
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tusername', 'Tpassword'),$xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $res = $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001  ORDER BY id ASC");
        $this->assertIsResource($res);

        $aStates = [];
        while ($row = pg_fetch_assoc($res)) {
            $aStates[] = $row["stateid"];
        }
        $this->assertEquals(6, count($aStates) );

        $s = 0;
        $this->assertEquals(Constants::iPAYMENT_ACCEPTED_STATE, $aStates[$s++]);
        $this->assertEquals(Constants::iPAYMENT_CAPTURED_STATE, $aStates[$s++]);
        $this->assertEquals(Constants::iPRE_FRAUD_CHECK_REVIEW_SUCCESS_STATE, $aStates[$s++]);
        $this->assertEquals(Constants::iCB_CONSTRUCTED_STATE, $aStates[$s++]);
        $this->assertEquals(Constants::iCB_CONNECTED_STATE, $aStates[$s++]);
        $this->assertEquals(Constants::iCB_ACCEPTED_STATE, $aStates[$s++]);
    }



    protected function getDoc($stateId,$externalId,$orderNo)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<update_fraud_status>';
        $xml .= '<status_id>'.$stateId.'</status_id>';
        $xml .= '<comments>Unit Test</comments>';
        $xml .= '<order_no>'.$orderNo.'</order_no>';
        $xml .= '<externalId>'.$externalId.'</externalId>';
        $xml .= '</update_fraud_status>';
        return $xml;
    }


}

