<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anna Lagad
 * Copyright: Cellpoint Digital
 * Link: http://www.cellpointdigital.com
 * Project: mPoint
 * File Name:FlightInfoTest.php
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
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 18, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, 1,  18, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");
        $this->queryDB("INSERT INTO Log.Order_Tbl (id, txnid, countryid, amount, productsku, productname, productdescription, productimageurl, points, reward, quantity, orderref, fees) VALUES (10, 1001001, 100, 5000, 'PR-RAEV-21',  'return journey', 'return journey', 'https://www.cpm.com', 300, 1, 1, 'SOCGN6', 0)");
        $this->queryDB("INSERT INTO log.additional_data_tbl(name, value, type, externalid) VALUES('FCTxnID', '243001', 'Transaction','1001001')");
        $this->queryDB("INSERT INTO Log.Flight_Tbl (id, service_class, departure_airport, arrival_airport, op_airline_code, order_id, arrival_date, departure_date, tag, trip_count, service_level, departure_countryid, arrival_countryid) VALUES (10, 'X', 'CEB', 'MNL', 'PR', 10,  '2020-05-23 13:55:00', '2020-05-23 12:40:00', 2, 2, 3, 640, 640)");
        $GLOBALS['oldOrderXml'] = true;

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
        $this->assertStringContainsString('<flight-detail tag="2" trip-count="2" service-level="3"><service-class>X</service-class><flight-number></flight-number><departure-airport>CEB</departure-airport><arrival-airport>MNL</arrival-airport><airline-code>PR</airline-code><departure-date>2020-05-23 12:40:00</departure-date><arrival-date>2020-05-23 13:55:00</arrival-date><departure-country>640</departure-country><arrival-country>640</arrival-country><time-zone></time-zone></flight-detail>', $xml);
    }

    public function testEmptyGetFlightInfo()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 18, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, 1,  18, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");
        $this->queryDB("INSERT INTO Log.Order_Tbl (id, txnid, countryid, amount, productsku, productname, productdescription, productimageurl, points, reward, quantity, orderref, fees) VALUES (10, 1001001, 100, 5000, 'PR-RAEV-21',  'return journey', 'return journey', 'https://www.cpm.com', 300, 1, 1, 'SOCGN6', 0)");
        $this->queryDB("INSERT INTO log.additional_data_tbl(name, value, type, externalid) VALUES('FCTxnID', '243001', 'Transaction','1001001')");
        $this->queryDB("INSERT INTO Log.Flight_Tbl (id, service_class, departure_airport, arrival_airport, op_airline_code, order_id, arrival_date, departure_date, tag, trip_count, service_level, departure_countryid, arrival_countryid) VALUES (10, 'X', 'CEB', 'MNL', 'PR', 10,  '2020-05-23 13:55:00', '2020-05-23 12:40:00', 2, 2, 3, 640, 640)");

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
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 18, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, 1,  18, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");
        $this->queryDB("INSERT INTO Log.Order_Tbl (id, txnid, countryid, amount, productsku, productname, productdescription, productimageurl, points, reward, quantity, orderref, fees) VALUES (10, 1001001, 100, 5000, 'PR-RAEV-21',  'return journey', 'return journey', 'https://www.cpm.com', 300, 1, 1, 'SOCGN6', 0)");
        $this->queryDB("INSERT INTO log.additional_data_tbl(name, value, type, externalid) VALUES('FCTxnID', '243001', 'Transaction','1001001')");
        $this->queryDB("INSERT INTO Log.Flight_Tbl (id, service_class, departure_airport, arrival_airport, op_airline_code, order_id, arrival_date, departure_date, tag, trip_count, service_level, departure_countryid, arrival_countryid) VALUES (10, 'X', 'MNL', 'CEB', 'PR', 10,  '2020-05-16 19:45:00', '2020-05-16 18:55:00', 1, 1, 3, 640, 640)");
        $this->queryDB("INSERT INTO Log.Flight_Tbl (id, service_class, departure_airport, arrival_airport, op_airline_code, order_id, arrival_date, departure_date, tag, trip_count, service_level, departure_countryid, arrival_countryid) VALUES (11, 'X', 'CEB', 'BCD', 'PR', 10,  '2020-05-16 12:00:00', '2020-05-16 10:45:00', 1, 2, 3, 640, 640)");
        $GLOBALS['oldOrderXml'] = true;

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
        $this->assertCount(2, $objFlightData);
        $this->assertStringContainsString('<flight-detail tag="1" trip-count="1" service-level="3"><service-class>X</service-class><flight-number></flight-number><departure-airport>MNL</departure-airport><arrival-airport>CEB</arrival-airport><airline-code>PR</airline-code><departure-date>2020-05-16 18:55:00</departure-date><arrival-date>2020-05-16 19:45:00</arrival-date><departure-country>640</departure-country><arrival-country>640</arrival-country><time-zone></time-zone></flight-detail>', $xml);
        $this->assertStringContainsString('<flight-detail tag="1" trip-count="2" service-level="3"><service-class>X</service-class><flight-number></flight-number><departure-airport>CEB</departure-airport><arrival-airport>BCD</arrival-airport><airline-code>PR</airline-code><departure-date>2020-05-16 10:45:00</departure-date><arrival-date>2020-05-16 12:00:00</arrival-date><departure-country>640</departure-country><arrival-country>640</arrival-country><time-zone></time-zone></flight-detail>', $xml);
    }

    public function testSuccessSetFlightDetails()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 18, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO Log.Session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, 1,  18, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");
        $this->queryDB("INSERT INTO log.additional_data_tbl(name, value, type, externalid) VALUES('FCTxnID', '243001', 'Transaction','1001001')");
        $this->queryDB("INSERT INTO Log.Order_Tbl (id, orderref, txnid, countryid, amount, quantity, productsku, productname, productdescription, productimageurl, points, reward,fees) VALUES (10, 'SOCGN6', 1001001, 100, 100, 1, '103-1418291', 'return journey', 'return journey', 'https://www.cpm.com', 300, 1, 0);");

        $data = array();
        $data['flights']['service_class'] = 'X';
        $data['flights']['departure_airport'] = 'CEB';
        $data['flights']['arrival_airport']= 'BCD';
        $data['flights']['op_airline_code']= 'PR';
        $data['flights']['departure_date']= '2020-05-16 18:55:00';
        $data['flights']['arrival_date']= '2020-05-16 19:45:00';
        $data['flights']['mkt_flight_number']= '1850';
        $data['flights']['departure_country'] = 200;
        $data['flights']['arrival_country'] = 200;
        $data['flights']['departure_timezone']= '+08:30';
        $data['flights']['order_id'] = 10;
        $data['flights']['tag'] = '2';
        $data['flights']['trip_count'] = '2';
        $data['flights']['service_level'] = '3';
        $data['additional'] = array();

        $iTxnID = 1001001;
        $obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $this->_OBJ_DB);
        $flight = $obj_TxnInfo->setFlightDetails($this->_OBJ_DB, $data['flights'], $data['additional']);
        $this->assertEquals(1, $flight);
    }

    public function testSetFlightDetailsNegetiveScenario()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 18, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO Log.Session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, 1,  18, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");
        $this->queryDB("INSERT INTO log.additional_data_tbl(name, value, type, externalid) VALUES('FCTxnID', '243001', 'Transaction','1001001')");
        $this->queryDB("INSERT INTO Log.Order_Tbl (id, orderref, txnid, countryid, amount, quantity, productsku, productname, productdescription, productimageurl, points, reward,fees) VALUES (10, 'SOCGN6', 1001001, 100, 100, 1, '103-1418291', 'return journey', 'return journey', 'https://www.cpm.com', 300, 1, 0);");

        $data = array();
        $data['flights']['service_class'] = 'X';
        $data['flights']['departure_airport'] = 'CEB';
        $data['flights']['arrival_airport']= 'BCD';
        $data['flights']['op_airline_code']= 'PR';
        $data['flights']['departure_date']= '2020-05-16 18:55:00';
        $data['flights']['arrival_date']= '2020-05-16 19:45:00';
        $data['flights']['mkt_flight_number']= '1850';
        $data['flights']['departure_country'] = 2000;
        $data['flights']['arrival_country'] = 2000;
        $data['flights']['departure_timezone']= '+08:30';
        $data['flights']['order_id'] = 10;
        $data['flights']['tag'] = '2';
        $data['flights']['trip_count'] = '2';
        $data['flights']['service_level'] = '3';
        $data['additional'] = array();

        $iTxnID = 1001001;
        $obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $this->_OBJ_DB);
        $flight = $obj_TxnInfo->setFlightDetails($this->_OBJ_DB, $data['flights'], $data['additional']);
        $this->assertEmpty($flight);
    }

    public function testSuccessGetFlightDetails()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 18, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO Log.Session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, 1,  18, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");
        $this->queryDB("INSERT INTO log.additional_data_tbl(name, value, type, externalid) VALUES('FCTxnID', '243001', 'Transaction','1001001')");
        $this->queryDB("INSERT INTO Log.Order_Tbl (id, orderref, txnid, countryid, amount, quantity, productsku, productname, productdescription, productimageurl, points, reward,fees) VALUES (10, 'SOCGN6', 1001001, 100, 100, 1, '103-1418291', 'return journey', 'return journey', 'https://www.cpm.com', 300, 1, 0);");
        $this->queryDB("INSERT INTO Log.Flight_Tbl (id, service_class,mkt_flight_number, departure_airport, arrival_airport, op_airline_code, order_id, arrival_date, departure_date, tag, trip_count, service_level, departure_countryid, arrival_countryid, departure_timezone) VALUES (10, 'X', '1850', 'CEB', 'MNL', 'PR', '10', '2020-05-23 13:55:00', '2020-05-23 12:40:00', '1', '2', '3', 200, 200, '+08:30')");
        $GLOBALS['oldOrderXml'] = true;

        $xml = "";
        $flightdata = FlightInfo::produceConfigurations($this->_OBJ_DB, 10);
        if(count($flightdata) > 0 ) {
            $xml .= '<airline-data>';
            foreach ($flightdata as $flight_Obj) {
                if (($flight_Obj instanceof FlightInfo) === TRUE) {
                    $xml .= $flight_Obj->toXML();
                }
            }
            $xml .= '</airline-data>';
        }
        $this->assertStringContainsString('<airline-data><flight-detail tag="1" trip-count="2" service-level="3"><service-class>X</service-class><flight-number>1850</flight-number><departure-airport>CEB</departure-airport><arrival-airport>MNL</arrival-airport><airline-code>PR</airline-code><departure-date>2020-05-23 12:40:00</departure-date><arrival-date>2020-05-23 13:55:00</arrival-date><departure-country>200</departure-country><arrival-country>200</arrival-country><time-zone>+08:30</time-zone></flight-detail></airline-data>', $xml);
    }

    public function testGetFlightDetailsNegetiveScenario()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 18, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO Log.Session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, 1,  18, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");
        $this->queryDB("INSERT INTO log.additional_data_tbl(name, value, type, externalid) VALUES('FCTxnID', '243001', 'Transaction','1001001')");
        $this->queryDB("INSERT INTO Log.Order_Tbl (id, orderref, txnid, countryid, amount, quantity, productsku, productname, productdescription, productimageurl, points, reward,fees) VALUES (10, 'SOCGN6', 1001001, 100, 100, 1, '103-1418291', 'return journey', 'return journey', 'https://www.cpm.com', 300, 1, 0);");
        $this->queryDB("INSERT INTO Log.Flight_Tbl (id, service_class,mkt_flight_number, departure_airport, arrival_airport, op_airline_code, order_id, arrival_date, departure_date, tag, trip_count, service_level, departure_countryid, arrival_countryid, departure_timezone) VALUES (10, 'X', '1850', 'CEB', 'MNL', 'PR', '10', '2020-05-23 13:55:00', '2020-05-23 12:40:00', '1', '2', '3', 200, 200, '+08:30')");

        $xml = null;
        $flightdata = FlightInfo::produceConfigurations($this->_OBJ_DB, 101);
        if(count($flightdata) > 0 ) {
            $xml .= '<airline-data>';
            foreach ($flightdata as $flight_Obj) {
                if (($flight_Obj instanceof FlightInfo) === TRUE) {
                    $xml .= $flight_Obj->toXML();
                }
            }
            $xml .= '</airline-data>';
        }
        $this->assertNull($xml);
    }

    public function testSuccessGetFlightDetailsWithAdditionalData()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 18, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO Log.Session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, 1,  18, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");
        $this->queryDB("INSERT INTO log.additional_data_tbl(name, value, type, externalid) VALUES('FCTxnID', '243001', 'Flight','10')");
        $this->queryDB("INSERT INTO Log.Order_Tbl (id, orderref, txnid, countryid, amount, quantity, productsku, productname, productdescription, productimageurl, points, reward,fees) VALUES (10, 'SOCGN6', 1001001, 100, 100, 1, '103-1418291', 'return journey', 'return journey', 'https://www.cpm.com', 300, 1, 0);");
        $this->queryDB("INSERT INTO Log.Flight_Tbl (id, service_class,mkt_flight_number, departure_airport, arrival_airport, op_airline_code, order_id, arrival_date, departure_date, tag, trip_count, service_level, departure_countryid, arrival_countryid, departure_timezone) VALUES (10, 'X', '1850', 'CEB', 'MNL', 'PR', '10', '2020-05-23 13:55:00', '2020-05-23 12:40:00', '1', '2', '3', 200, 200, '+08:30')");
        $GLOBALS['oldOrderXml'] = true;

        $xml = null;
        $flightdata = FlightInfo::produceConfigurations($this->_OBJ_DB, 10);
        if(count($flightdata) > 0 ) {
            $xml .= '<airline-data>';
            foreach ($flightdata as $flight_Obj) {
                if (($flight_Obj instanceof FlightInfo) === TRUE) {
                    $xml .= $flight_Obj->toXML();
                }
            }
            $xml .= '</airline-data>';
        }
        $this->assertStringContainsString('<airline-data><flight-detail tag="1" trip-count="2" service-level="3"><service-class>X</service-class><flight-number>1850</flight-number><departure-airport>CEB</departure-airport><arrival-airport>MNL</arrival-airport><airline-code>PR</airline-code><departure-date>2020-05-23 12:40:00</departure-date><arrival-date>2020-05-23 13:55:00</arrival-date><departure-country>200</departure-country><arrival-country>200</arrival-country><time-zone>+08:30</time-zone><additional-data><param name="FCTxnID">243001</param></additional-data></flight-detail></airline-data>', $xml);

    }

    public function testFlightInfoNewToXML()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 18, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, 1,  18, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");
        $this->queryDB("INSERT INTO log.order_tbl (id, txnid, countryid, amount, productsku, productname, productdescription, productimageurl, points, reward, quantity, created, modified, enabled, orderref, fees) VALUES (24, 1001001, 640, 125056, 'product-ticket', 'ONE WAY', 'MNL-CEB', '', 0, 0, 1, '2021-04-09 10:25:06.395114', '2021-04-09 10:25:06.395114', true, 'FIU9YAN', 0)");
        $this->queryDB("INSERT INTO log.flight_tbl (id, service_class, departure_airport, arrival_airport, op_airline_code, order_id, arrival_date, departure_date, created, modified, mkt_flight_number, tag, trip_count, service_level, departure_countryid, arrival_countryid, departure_timezone, op_flight_number, arrival_timezone, mkt_airline_code, departure_city, arrival_city, aircraft_type, arrival_terminal, departure_terminal) VALUES (22, 'Z', 'MNL', 'CEB', '5J', 24, '2021-03-07 21:05:00.000000', '2021-03-07 19:35:00.120000', '2021-04-09 10:25:06.513775', '2021-04-09 10:25:06.513775', '563', '1', '1', '3', 640, 640, '+08:00', '1', '+08:00', '5J', 'Ninoy Aquino International Airport', 'Mactan Cebu International Airport', 'Aircraft Boeing-737-9', '2', '1')");
        $this->queryDB("INSERT INTO log.additional_data_tbl (id, name, value, type, created, modified, externalid) VALUES (10, 'fare_basis', 'we543s3', 'Flight', '2021-04-06 09:18:21.094984', '2021-04-06 09:18:21.094984', 22)");

        $flightObj = FlightInfo::produceConfigurations($this->_OBJ_DB, 24);
        // new xml
        $GLOBALS['oldOrderXml'] = false;
        $xml = $flightObj[0]->toXML();
        $this->assertEquals('<trip tag="1" seq="1"><origin external-id="MNL" country-id="640" time-zone="+08:00" terminal="1">Ninoy Aquino International Airport</origin><destination external-id="CEB" country-id="640" time-zone="+08:00" terminal="2">Mactan Cebu International Airport</destination><departure-time>2021-03-07T19:35:00Z</departure-time><arrival-time>2021-03-07T21:05:00Z</arrival-time><departure-time-without-timezone>2021-03-07 19:35:00.12</departure-time-without-timezone><arrival-time-without-timezone>2021-03-07 21:05:00</arrival-time-without-timezone><booking-class>Z</booking-class><service-level id="3">Economy</service-level><transportation code="5J" number="1"><carriers><carrier code="5J" type-id="Aircraft Boeing-737-9"><number>563</number></carrier></carriers></transportation><additional-data><param name="fare_basis">we543s3</param></additional-data></trip>', $xml);

        //old xml
        $GLOBALS['oldOrderXml'] = true;
        $xml = $flightObj[0]->toXML();
        $this->assertEquals('<flight-detail tag="1" trip-count="1" service-level="3"><service-class>Z</service-class><flight-number>563</flight-number><departure-airport>MNL</departure-airport><arrival-airport>CEB</arrival-airport><airline-code>5J</airline-code><departure-date>2021-03-07 19:35:00.12</departure-date><arrival-date>2021-03-07 21:05:00</arrival-date><departure-country>640</departure-country><arrival-country>640</arrival-country><time-zone>+08:00</time-zone><additional-data><param name="fare_basis">we543s3</param></additional-data></flight-detail>', $xml);

    }

    public function tearDown() : void
    {
        $GLOBALS['oldOrderXml'] = false;
        $this->_OBJ_DB->disConnect();
        parent::tearDown();
    }


}
