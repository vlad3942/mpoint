<?php
/**
 * Created by IntelliJ IDEA.
 * User: rohit
 * Date: 11-03-2019
 * Time: 18:02
 */

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class ClientConfigUnitTest extends baseAPITest
{

    private function getClientConfigtoXML()
    { return '<client-config id="113" flow-id="1" mode="0" max-cards="-1" identification="7" masked-digits="4"><name>Test Client</name><username>Tuser</username><logo-url>https://s3-ap-southeast-1.amazonaws.com/cpmassets/marchant/mpl/logo.png</logo-url><css-url>https://s3-ap-southeast-1.amazonaws.com/cpmassets/marchant/mpl/logo.png</css-url><accept-url>https://s3-ap-southeast-1.amazonaws.com/cpmassets/marchant/mpl/logo.png</accept-url><app-url></app-url><base-image-url></base-image-url><cancel-url>https://s3-ap-southeast-1.amazonaws.com/cpmassets/marchant/mpl/logo.png</cancel-url><decline-url></decline-url><callback-url>https://s3-ap-southeast-1.amazonaws.com/cpmassets/marchant/mpl/logo.png</callback-url><icon-url></icon-url><customer-import-url></customer-import-url><authentication-url></authentication-url><notification-url></notification-url><sms-receipt>false</sms-receipt><email-receipt>false</email-receipt><auto-capture>true</auto-capture><store-card>0</store-card><salt></salt><secret-key></secret-key><ip-list></ip-list><additional-config></additional-config><show-all-cards></show-all-cards></client-config>';}

    public function testClientConfigXML($cardID = 8, $pspID = 18)
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd, logourl, cssurl, callbackurl, accepturl,cancelurl, maxamount, lang, smsrcpt, emailrcpt, method, created, modified ) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass', 'https://s3-ap-southeast-1.amazonaws.com/cpmassets/marchant/mpl/logo.png', 'https://s3-ap-southeast-1.amazonaws.com/cpmassets/marchant/mpl/logo.png', 'https://s3-ap-southeast-1.amazonaws.com/cpmassets/marchant/mpl/logo.png', 'https://s3-ap-southeast-1.amazonaws.com/cpmassets/marchant/mpl/logo.png', 'https://s3-ap-southeast-1.amazonaws.com/cpmassets/marchant/mpl/logo.png', 10000, 'gb', FALSE, FALSE, 'mPoint', '2019-03-18T23:54:53+00:00', '2019-03-18T23:54:53+00:00')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name, created, modified) VALUES (1100, $pspID, '-1', '2019-03-18T23:54:53+00:00', '2019-03-18T23:54:53+00:00')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid) VALUES (113, $cardID, $pspID, 100)");
        $objCC = ClientConfig::produceConfig($this->getDBObj(), 113);
        $sReturnXML = $objCC->toXML();
        $this->assertEquals($this->getClientConfigtoXML(), $sReturnXML);
    }
}