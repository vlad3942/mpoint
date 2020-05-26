<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anna Lagad
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package:
 * File Name:RoutingServiceTest.php
 */

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once sAPI_CLASS_PATH . 'simpledom.php';
require_once __DIR__ . '/../../api/classes/flight_info.php';

class FlightInfoTest extends baseAPITest
{

    protected $_aMPOINT_CONN_INFO;

    public function __construct()
    {
        parent::__construct();
        $this->constHTTPClient();
    }

    public function constHTTPClient()
    {
        global $aMPOINT_CONN_INFO;
        $aMPOINT_CONN_INFO['path'] = "/mApp/api/initialize.php";
        $aMPOINT_CONN_INFO["contenttype"] = "text/xml";
        $this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO) );
    }

    protected function getInitDoc($client, $account, $departure_country = null, $arival_country = null)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<root>';
        $xml .= '<initialize-payment client-id="'. $client .'" account="'. $account .'">';
        $xml .= '<transaction order-no="1234abc" type-id="30">';
        $xml .= '<amount country-id="100" currency-id ="208">200</amount>';
        $xml .= $this->getAirlineData($departure_country, $arival_country);
        $xml .= '</transaction>';
        $xml .= '<client-info platform="iOS" version="5.1.1" language="da">';
        $xml .= '<mobile country-id="100" operator-id="10000">288828610</mobile>';
        $xml .= '<email>jona@oismail.com</email>';
        $xml .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
        $xml .= '</client-info>';
        $xml .= '</initialize-payment>';
        $xml .= '</root>';

        return $xml;
    }

    protected function getAirlineData($departure_country, $arival_country)
    {
        $xml = '<orders>';
        $xml .= '<line-item>';
        $xml .= '<product sku="PR-RAEV-21">';
        $xml .= '<name>return journey</name>';
        $xml .= '<description>return journey</description>';
        $xml .= '<image-url>https://www.cpm.com</image-url>';
        $xml .= '<airline-data>';
        $xml .= '<flight-detail service-level="3" trip-count="1" tag="1">';
        $xml .= '<service-class>X</service-class>';
        $xml .= '<flight-number>1849</flight-number>';
        $xml .= '<departure-airport>MNL</departure-airport>';
        $xml .= '<arrival-airport>CEB</arrival-airport>';
        $xml .= '<airline-code>PR</airline-code>';
        $xml .= '<departure-date>2020-05-16 10:45:00</departure-date>';
        $xml .= '<arrival-date>2020-05-16 12:00:00</arrival-date>';
        if(empty($departure_country) === false){
            $xml .= '<departure-country>'.$departure_country.'</departure-country>';
        }
        if(empty($arival_country) === false){
            $xml .= '<arrival-country>'.$arival_country.'</arrival-country>';
        }
        $xml .= '</flight-detail>';
        $xml .= '<flight-detail service-level="3" trip-count="2" tag="1">';
        $xml .= '<service-class>X</service-class>';
        $xml .= '<flight-number>2289</flight-number>';
        $xml .= '<departure-airport>CEB</departure-airport>';
        $xml .= '<arrival-airport>BCD</arrival-airport>';
        $xml .= '<airline-code>PR</airline-code>';
        $xml .= '<departure-date>2020-05-16 18:55:00</departure-date>';
        $xml .= '<arrival-date>2020-05-16 19:45:00</arrival-date>';
        if(empty($departure_country) === false){
            $xml .= '<departure-country>'.$departure_country.'</departure-country>';
        }
        if(empty($arival_country) === false){
            $xml .= '<arrival-country>'.$arival_country.'</arrival-country>';
        }
        $xml .= '</flight-detail>';
        $xml .= '<passenger-detail>';
        $xml .= '<title>MR</title>';
        $xml .= '<first-name>Langley</first-name>';
        $xml .= '<last-name>Ballam</last-name>';
        $xml .= '<type>ADULT</type>';
        $xml .= '<contact-info>';
        $xml .= '<email>firstname.lastname@example.com</email>';
        $xml .= '<mobile country-id="641">4883048</mobile>';
        $xml .= '</contact-info>';
        $xml .= '</passenger-detail>';
        $xml .= '</airline-data>';
        $xml .= '</product>';
        $xml .= '<amount>1565700</amount>';
        $xml .= '<points>300</points>';
        $xml .= '<reward>1</reward>';
        $xml .= '<quantity>1</quantity>';
        $xml .= '</line-item>';
        $xml .= '</orders>';
      return $xml;
    }

    public function testSuccessStopoverDetails()
    {
        $pspID = 2;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (113, 2, $pspID, true, 2)");
        $this->queryDB("INSERT INTO client.staticroutelevelconfiguration (cardaccessid, cvcmandatory) VALUES (1, true);");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '5019********3742', '06/24', TRUE, 113, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 2)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 113, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 113, 1100, 1,  $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE,10,5000)");

        $xml = $this->getInitDoc(113, 1100, 640, 640);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->bIgnoreErrors = true;
        $this->assertEquals(200, $iStatus);
        $this->assertContains('<orders><line-item><product sku="PR-RAEV-21"><name>return journey</name><description>return journey</description><image-url>https://www.cpm.com</image-url><airline-data><flight-detail service-level="3" trip-count="1" tag="1"><service-class>X</service-class><flight-number>1849</flight-number><departure-airport>MNL</departure-airport><arrival-airport>CEB</arrival-airport><airline-code>PR</airline-code><departure-date>2020-05-16 10:45:00</departure-date><arrival-date>2020-05-16 12:00:00</arrival-date><departure-country>640</departure-country><arrival-country>640</arrival-country></flight-detail><flight-detail service-level="3" trip-count="2" tag="1"><service-class>X</service-class><flight-number>2289</flight-number><departure-airport>CEB</departure-airport><arrival-airport>BCD</arrival-airport><airline-code>PR</airline-code><departure-date>2020-05-16 18:55:00</departure-date><arrival-date>2020-05-16 19:45:00</arrival-date><departure-country>640</departure-country><arrival-country>640</arrival-country></flight-detail><passenger-detail><title>MR</title><first-name>Langley</first-name><last-name>Ballam</last-name><type>ADULT</type><contact-info><email>firstname.lastname@example.com</email><mobile country-id="641">4883048</mobile></contact-info></passenger-detail></airline-data></product><amount>1565700</amount><points>300</points><reward>1</reward><quantity>1</quantity></line-item></orders>', $sReplyBody);
    }

    public function testStopoverDetailsNotExist()
    {
        $pspID = 2;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (113, 2, $pspID, true, 2)");
        $this->queryDB("INSERT INTO client.staticroutelevelconfiguration (cardaccessid, cvcmandatory) VALUES (1, true);");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '5019********3742', '06/24', TRUE, 113, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 2)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 113, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 113, 1100, 1,  $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE,10,5000)");

        $xml = $this->getInitDoc(113, 1100);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->bIgnoreErrors = true;
        $this->assertEquals(200, $iStatus);
        $this->assertContains('<orders><line-item><product sku="PR-RAEV-21"><name>return journey</name><description>return journey</description><image-url>https://www.cpm.com</image-url><airline-data><flight-detail service-level="3" trip-count="1" tag="1"><service-class>X</service-class><flight-number>1849</flight-number><departure-airport>MNL</departure-airport><arrival-airport>CEB</arrival-airport><airline-code>PR</airline-code><departure-date>2020-05-16 10:45:00</departure-date><arrival-date>2020-05-16 12:00:00</arrival-date></flight-detail><flight-detail service-level="3" trip-count="2" tag="1"><service-class>X</service-class><flight-number>2289</flight-number><departure-airport>CEB</departure-airport><arrival-airport>BCD</arrival-airport><airline-code>PR</airline-code><departure-date>2020-05-16 18:55:00</departure-date><arrival-date>2020-05-16 19:45:00</arrival-date></flight-detail><passenger-detail><title>MR</title><first-name>Langley</first-name><last-name>Ballam</last-name><type>ADULT</type><contact-info><email>firstname.lastname@example.com</email><mobile country-id="641">4883048</mobile></contact-info></passenger-detail></airline-data></product><amount>1565700</amount><points>300</points><reward>1</reward><quantity>1</quantity></line-item></orders>', $sReplyBody);
    }


}
