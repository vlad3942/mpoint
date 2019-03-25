<?php
/**
 * Created by IntelliJ IDEA.
 * User: rohit
 * Date: 11-03-2019
 * Time: 18:02
 */

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class TxnInfoUnitTest extends baseAPITest
{

    private function _getTxnInfotoXML()
    { return '<transaction id="1001001" type="100" gmid="-1" mode="0" eua-id="5001" attempt="0" psp-id="18" card-id="0" wallet-id="0" product-type="100" external-id=""><captured-amount country-id="100" currency="DKK" symbol="" format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">0</captured-amount><amount country-id="100" currency-id="208" currency="DKK" decimals="2" symbol="" format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">5000</amount><fee country-id="100" currency="DKK" symbol="" format="{PRICE} {CURRENCY}">0</fee><price>50,00 </price><points country-id="0" currency="points" symbol="points" format="{PRICE} {CURRENCY}">-1</points><reward country-id="0" currency="points" symbol="points" format="{PRICE} {CURRENCY}">-1</reward><refund country-id="100" currency="DKK" symbol="" format="{PRICE} {CURRENCY}">0</refund><orderid>103-1418291</orderid><mobile country-id="0" country-code="45"></mobile><operator>0</operator><email></email><device-id></device-id><logo><url></url><width>100%</width><height>20%</height></logo><css-url></css-url><accept-url></accept-url><cancel-url></cancel-url><decline-url></decline-url><callback-url>https://s3-ap-southeast-1.amazonaws.com/cpmassets/marchant/mpl/logo.png</callback-url><icon-url></icon-url><auth-url></auth-url><language>gb</language><auto-capture>true</auto-capture><auto-store-card>false</auto-store-card><markup-language></markup-language><customer-ref></customer-ref><description></description><ip>127.0.0.1</ip><hmac>c54ea47b904c8772985c00e430f007a90cb01b79</hmac><created-date>20190312</created-date><created-time>021846</created-time></transaction>';}

    public function testTxnInfotoXML()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd, logourl, cssurl, callbackurl, accepturl,cancelurl, maxamount, lang, smsrcpt, emailrcpt, method, created, modified ) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass', 'https://s3-ap-southeast-1.amazonaws.com/cpmassets/marchant/mpl/logo.png', 'https://s3-ap-southeast-1.amazonaws.com/cpmassets/marchant/mpl/logo.png', 'https://s3-ap-southeast-1.amazonaws.com/cpmassets/marchant/mpl/logo.png', 'https://s3-ap-southeast-1.amazonaws.com/cpmassets/marchant/mpl/logo.png', 'https://s3-ap-southeast-1.amazonaws.com/cpmassets/marchant/mpl/logo.png', 10000, 'gb', FALSE, FALSE, 'mPoint', '2019-03-18T23:54:53+00:00', '2019-03-18T23:54:53+00:00')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, 18, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name, created, modified) VALUES (1100, 18, '-1', '2019-03-18T23:54:53+00:00', '2019-03-18T23:54:53+00:00')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid) VALUES (113, 8, 18, 100)");

        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 8, 18, '501910******3742', '06/24', TRUE, 113, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");

        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, auto_capture, enabled, currencyid, sessionid, producttype, created) VALUES (1001001, 100, 113, 1100, 1,  18, 5001, 100, '103-1418291', 'https://s3-ap-southeast-1.amazonaws.com/cpmassets/marchant/mpl/logo.png', 5000, '127.0.0.1', TRUE, TRUE,208, 1, 100, '2019-03-12 02:18:46.82603')");
        $this->queryDB("INSERT INTO Log.ExternalReference_Tbl (id, txnid, externalid, pspid) VALUES (1, 1001001, 1111001001, 18)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_CAPTURED_STATE. ")");

        $ObjTxnInfo = TxnInfo::produceInfo(1001001, $this->getDBObj());
        $sReturnXML = $ObjTxnInfo->toXML();
        $this->assertEquals($this->_getTxnInfotoXML(), $sReturnXML);

        $iCount = 0;
        $iCount = $ObjTxnInfo->hasEitherState($this->getDBObj(), Constants::iPAYMENT_ACCEPTED_STATE );
        $this->assertEquals($iCount, 1);

        $iCount = $ObjTxnInfo->hasEitherState($this->getDBObj(), Constants::iPAYMENT_CAPTURED_STATE );
        $this->assertEquals($iCount, 2);

        $aMessageStates = $ObjTxnInfo->getMessageHistory($this->getDBObj() );
        $i = 0;
        $this->assertEquals($aMessageStates[$i]["stateid"], Constants::iPAYMENT_CAPTURED_STATE);
        $this->assertEquals($aMessageStates[++$i]["stateid"], Constants::iPAYMENT_ACCEPTED_STATE);

        $ObjTxnInfoTwo = TxnInfo::produceInfoFromOrderNoAndMerchant($this->getDBObj(), '103-1418291', '4216310');
        $sReturnXMLTwo = $ObjTxnInfoTwo->toXML();
        $this->assertEquals($this->_getTxnInfotoXML(), $sReturnXMLTwo);

        $ObjTxnInfoThree = TxnInfo::produceTxnInfoFromExternalRef($this->getDBObj(), '1111001001');
        $sReturnXMLThree = $ObjTxnInfoThree->toXML();
        $this->assertEquals($this->_getTxnInfotoXML(), $sReturnXMLThree);
    }
}