<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anna Lagad
 * Copyright: Cellpoint Digtal
 * Link: http://www.cellpointdigital.com
 * Project: mPoint
 * File Name:StaticRouteTest.php
 */

use api\classes\merchantservices\Repositories\ReadOnlyConfigRepository;

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once sAPI_CLASS_PATH . 'simpledom.php';
require_once __DIR__ . '/../../api/classes/card_prefix_config.php';
require_once __DIR__ . '/../../api/classes/core/card.php';
require_once __DIR__ . '/../../api/classes/crs/payment_method.php';
require_once __DIR__ . '/../../api/classes/routing_service_response.php';

class PaymentMethodTest extends baseAPITest
{

    private $_OBJ_DB;
    protected $_aHTTP_CONN_INFO;

    public function setUp() : void
    {
        parent::setUp(TRUE);
        global $aHTTP_CONN_INFO;
        $this->bIgnoreErrors = true;
        $this->_aHTTP_CONN_INFO = $aHTTP_CONN_INFO;
        $this->_OBJ_DB = RDB::produceDatabase($this->mPointDBInfo);
        $this->_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH . sLANG ."/global.txt", sLANGUAGE_PATH . sLANG ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");
    }


    public function testSuccessGetPaymentMethodByOrder()
    {
        $pspID = Constants::iWIRE_CARD_PSP;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 7, $pspID, false, 2)");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 5, $pspID, false, 2)");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 11, $pspID, false, 2)");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 8, $pspID, false, 2)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (11, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, 1,  $pspID, 5001, 100, '103-1418291', 'www.celppointditial.com/callback', 5000, '127.0.0.1', TRUE,11,5000)");


        $iTxnID = 1001001;
        $obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $this->_OBJ_DB);
        $obj_XML = $this->getPaymentMethods(array(8,11,5,7));
        $obj_PaymentMethodResponse = RoutingServiceResponse::produceGetPaymentMethodResponse($obj_XML);
        $readOnlyRepo = new ReadOnlyConfigRepository($this->_OBJ_DB,$obj_TxnInfo);

        if($obj_PaymentMethodResponse instanceof RoutingServiceResponse)
        {
            $obj_PaymentMethods = $obj_PaymentMethodResponse->getPaymentMethods();
            $obj_SR = $readOnlyRepo->getCardConfigurationsByCardIds( $this->_OBJ_TXT, $obj_PaymentMethods);

            $this->assertEquals(8, $obj_SR[1]->getCardTypeId());
            $this->assertEquals(11, $obj_SR[2]->getCardTypeId());
            $this->assertEquals(5, $obj_SR[3]->getCardTypeId());
            $this->assertEquals(7, $obj_SR[4]->getCardTypeId());
        }
    }

    public function testSuccessGetPaymentMethodFailureScanario()
    {
        $pspID = Constants::iWIRE_CARD_PSP;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (11, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, 1,  $pspID, 5001, 100, '103-1418291', 'www.celppointditial.com/callback', 5000, '127.0.0.1', TRUE,11,5000)");


        $iTxnID = 1001001;
        $obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $this->_OBJ_DB);
        $obj_XML = $this->getPaymentMethods(array(111,222));
        $obj_PaymentMethodResponse = RoutingServiceResponse::produceGetPaymentMethodResponse($obj_XML);
        $readOnlyRepo = new ReadOnlyConfigRepository($this->_OBJ_DB,$obj_TxnInfo);
        if($obj_PaymentMethodResponse instanceof RoutingServiceResponse)
        {
            $obj_PaymentMethods = $obj_PaymentMethodResponse->getPaymentMethods();
            $obj_SR = $readOnlyRepo->getCardConfigurationsByCardIds( $this->_OBJ_TXT, $obj_PaymentMethods);
            $this->assertEmpty($obj_SR);
        }
    }

    protected function getPaymentMethods($aPaymentMethod)
    {
        $xml = '<payment_method_search_response>';
        $xml .= '<payment_methods>';
        if(empty($aPaymentMethod) === false && count($aPaymentMethod) > 0)
        {
            $preference =1;
            foreach ($aPaymentMethod as $paymentMethod)
            {
                $xml .= '<payment_method>';
                $xml .= '<id>'.$paymentMethod.'</id>';
                $xml .= '<preference>'.$preference.'</preference>';
                $xml .= '<state_id>1</state_id>';
                $xml .= '</payment_method>';
                $preference = $preference + 1;
            }
        }
        $xml .= '</payment_methods>';
        $xml .= '</payment_method_search_response>';

        return simplexml_load_string($xml);
    }


    public function tearDown() : void
    {
        $this->_OBJ_DB->disConnect();
        parent::tearDown();
    }


}
