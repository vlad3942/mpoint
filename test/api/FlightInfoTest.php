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

    private $_OBJ_DB;
    protected $_aHTTP_CONN_INFO;

    public function setUp() : void
    {
        parent::setUp(true);
        global $aHTTP_CONN_INFO;
        $this->bIgnoreErrors = true;
        $this->_aHTTP_CONN_INFO = $aHTTP_CONN_INFO;
        $this->_OBJ_DB = RDB::produceDatabase($this->mPointDBInfo);
    }


    public function testSuccessGetFlightInfo()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 18, '5019********3742', '06/24', TRUE, 113, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 2)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 113, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 113, 1100, 1,  18, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");
        $this->queryDB("INSERT INTO Log.Order_Tbl (id, txnid, countryid, amount, productsku, productname, productdescription, productimageurl, points, reward, quantity, orderref, fees) VALUES (10, 1001001, 100, 5000, 'PR-RAEV-21',  'return journey', 'return journey', 'https://www.cpm.com', 300, 1, 1, 'SOCGN6', 0)");
        $this->queryDB("INSERT INTO log.additional_data_tbl(name, value, type, externalid) VALUES('FCTxnID', '243001', 'Transaction','1001001')");
        $this->queryDB("INSERT INTO Log.Flight_Tbl (id, service_class, departure_airport, arrival_airport, airline_code, order_id, arrival_date, departure_date, tag, trip_count, service_level, departure_countryid, arrival_countryid, additional_data_ref) VALUES (10, 'X', 'CEB', 'MNL', 'PR', 10,  '2020-05-23 13:55:00', '2020-05-23 12:40:00', 2, 2, 3, 640, 640, 1)");

        $id = 10;
        $objFlightData = FlightInfo::produceConfigurations($this->_OBJ_DB, $id);
        $xml = '';
        if(count($objFlightData) > 0 )
        {
            foreach ($objFlightData as $flight_Obj)
            {
                if (($flight_Obj instanceof FlightInfo) === TRUE)
                {
                    $xml .= $flight_Obj->toXML();
                }
            }
        }
        $this->assertEquals(1, count($objFlightData));
        $this->assertStringContainsString('<flight-detail tag="2" trip-count="2" service-level="3"><service-class>X</service-class><flight-number></flight-number><departure-airport>CEB</departure-airport><arrival-airport>MNL</arrival-airport><airline-code>PR</airline-code><departure-date>2020-05-23 12:40:00</departure-date><arrival-date>2020-05-23 13:55:00</arrival-date><departure-country>640</departure-country><arrival-country>640</arrival-country></flight-detail>', $xml);
    }

    public function testEmptyGetFlightInfo()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 18, '5019********3742', '06/24', TRUE, 113, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 2)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 113, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 113, 1100, 1,  18, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");
        $this->queryDB("INSERT INTO Log.Order_Tbl (id, txnid, countryid, amount, productsku, productname, productdescription, productimageurl, points, reward, quantity, orderref, fees) VALUES (10, 1001001, 100, 5000, 'PR-RAEV-21',  'return journey', 'return journey', 'https://www.cpm.com', 300, 1, 1, 'SOCGN6', 0)");
        $this->queryDB("INSERT INTO log.additional_data_tbl(name, value, type, externalid) VALUES('FCTxnID', '243001', 'Transaction','1001001')");
        $this->queryDB("INSERT INTO Log.Flight_Tbl (id, service_class, departure_airport, arrival_airport, airline_code, order_id, arrival_date, departure_date, tag, trip_count, service_level, departure_countryid, arrival_countryid, additional_data_ref) VALUES (10, 'X', 'CEB', 'MNL', 'PR', 10,  '2020-05-23 13:55:00', '2020-05-23 12:40:00', 2, 2, 3, 640, 640, 1)");

        $id = 100;
        $objFlightData = FlightInfo::produceConfigurations($this->_OBJ_DB, $id);
        $xml = '';
        if(count($objFlightData) > 0 )
        {
            foreach ($objFlightData as $flight_Obj)
            {
                if (($flight_Obj instanceof FlightInfo) === TRUE)
                {
                    $xml .= $flight_Obj->toXML();
                }
            }
        }
        $this->assertEquals(0, count($objFlightData));
        $this->assertEmpty($objFlightData);
    }

    public function testSuccessStopoverJourney()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 18, '5019********3742', '06/24', TRUE, 113, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 2)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 113, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 113, 1100, 1,  18, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");
        $this->queryDB("INSERT INTO Log.Order_Tbl (id, txnid, countryid, amount, productsku, productname, productdescription, productimageurl, points, reward, quantity, orderref, fees) VALUES (10, 1001001, 100, 5000, 'PR-RAEV-21',  'return journey', 'return journey', 'https://www.cpm.com', 300, 1, 1, 'SOCGN6', 0)");
        $this->queryDB("INSERT INTO log.additional_data_tbl(name, value, type, externalid) VALUES('FCTxnID', '243001', 'Transaction','1001001')");
        $this->queryDB("INSERT INTO Log.Flight_Tbl (id, service_class, departure_airport, arrival_airport, airline_code, order_id, arrival_date, departure_date, tag, trip_count, service_level, departure_countryid, arrival_countryid, additional_data_ref) VALUES (10, 'X', 'MNL', 'CEB', 'PR', 10,  '2020-05-16 19:45:00', '2020-05-16 18:55:00', 1, 1, 3, 640, 640, 1)");
        $this->queryDB("INSERT INTO Log.Flight_Tbl (id, service_class, departure_airport, arrival_airport, airline_code, order_id, arrival_date, departure_date, tag, trip_count, service_level, departure_countryid, arrival_countryid, additional_data_ref) VALUES (11, 'X', 'CEB', 'BCD', 'PR', 10,  '2020-05-16 12:00:00', '2020-05-16 10:45:00', 1, 2, 3, 640, 640, 1)");

        $id = 10;
        $objFlightData = FlightInfo::produceConfigurations($this->_OBJ_DB, $id);
        $xml = '';
        if(count($objFlightData) > 0 )
        {
            foreach ($objFlightData as $flight_Obj)
            {
                if (($flight_Obj instanceof FlightInfo) === TRUE)
                {
                    $xml .= $flight_Obj->toXML();
                }
            }
        }
        $this->assertEquals(2, count($objFlightData));
        $this->assertStringContainsString('<flight-detail tag="1" trip-count="1" service-level="3"><service-class>X</service-class><flight-number></flight-number><departure-airport>MNL</departure-airport><arrival-airport>CEB</arrival-airport><airline-code>PR</airline-code><departure-date>2020-05-16 18:55:00</departure-date><arrival-date>2020-05-16 19:45:00</arrival-date><departure-country>640</departure-country><arrival-country>640</arrival-country></flight-detail><flight-detail tag="1" trip-count="2" service-level="3"><service-class>X</service-class><flight-number></flight-number><departure-airport>CEB</departure-airport><arrival-airport>BCD</arrival-airport><airline-code>PR</airline-code><departure-date>2020-05-16 10:45:00</departure-date><arrival-date>2020-05-16 12:00:00</arrival-date><departure-country>640</departure-country><arrival-country>640</arrival-country></flight-detail>', $xml);
    }


    public function tearDown() : void
    {
        $this->_OBJ_DB->disConnect();
        parent::tearDown();
    }


}
