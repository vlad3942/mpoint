<?php
require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class UpdateOrderDataAPIValidationTest extends baseAPITest
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
        $aMPOINT_CONN_INFO['path'] = "/mApp/api/update-order-data.php";
        $aMPOINT_CONN_INFO["contenttype"] = "text/xml";
        $this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO) );
    }

    protected function getInitDoc($txnId, $orderRef, $orderXml='')
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml .= '<root>';
		$xml .= '<update-order-data>';
		$xml .= '<transaction id="' .$txnId. '" order-no="' .$orderRef. '">';
		$xml .= $orderXml;
        $xml .= '</transaction>';
        $xml .= '</update-order-data>';
		$xml .= '</root>';

		return $xml;
	}

	public function testUnauthorized()
	{
		$xml = $this->getInitDoc(1, 1);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders(), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(401, $iStatus);
		$this->assertStringContainsString('<status code="401">Authorization required</status>', $sReplyBody);
	}

	public function testWrongUsernamePassword()
	{
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
        $pspID = Constants::iWIRE_CARD_PSP;
		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10078, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (100780, 10078)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10078, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10078, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100780, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10078, 2, $pspID, true, 1)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (3, 10078, 100780, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, countryid, orderid, callbackurl, amount, ip, auto_capture, enabled, currencyid, sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10078, 100780, 1,  2, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', 1, TRUE, 208,3,5000,208)");

        $orderXml = '<orders> <line-item> <product order-ref="ABC1234" sku="product-ticket"> <type>100</type> <name>ONE WAY</name> <description>MNL-CEB</description> <airline-data> <profiles> <profile> <seq>1</seq> <title>Mr</title> <first-name>dan</first-name> <last-name>dan</last-name> <type>ADT</type> <contact-info> <email>dan@dan.com</email> <mobile country-id="640">9187231231</mobile> </contact-info> <additional-data> <param name="loyality_id">345rtyu</param> </additional-data> </profile> </profiles> <billing-summary> <fare-detail> <fare> <profile-seq>1</profile-seq> <description>adult</description> <currency>PHP</currency> <amount>60</amount> <product-code>ABF</product-code> <product-category>FARE</product-category> <product-item>Base fare for adult</product-item> </fare> </fare-detail> </billing-summary> <trips> <trip tag="1" seq="1"> <origin external-id="MNL" country-id="640" time-zone="+08:00" terminal="1">Ninoy Aquino International Airport</origin> <destination external-id="CEB" country-id="640" time-zone="+08:00" terminal="2">Mactan Cebu International Airport</destination> <departure-time>2021-03-07T19:35:00Z</departure-time> <arrival-time>2021-03-07T21:05:00Z</arrival-time> <booking-class>Z</booking-class> <service-level>Economy</service-level> <transportation code="5J" number="1"> <carriers> <carrier code="5J" type-id="Aircraft Boeing-737-9"> <number>563</number> </carrier> </carriers> </transportation> <additional-data> <param name="fare_basis">we543s3</param> </additional-data> </trip> </trips> </airline-data> </product> <amount>125056</amount> <quantity>1</quantity> <additional-data> <param name="key">value</param> </additional-data> </line-item>  </orders>';
		$xml = $this->getInitDoc(1001001, 1100, $orderXml);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Twrong'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(401, $iStatus);
		$this->assertStringContainsString('<status code = "401">Username / Password doesn\'t match</status></root>', $sReplyBody);
	}

    public function testUpdateAID()
    {

        /**   AIR LINE DATA XML
        <?xml version="1.0" encoding="UTF-8"?>
        <root>
        <initialize-payment account="100770" client-id="10077">
        <transaction order-no="TESTABHINAVV1" type-id="1">
        <amount country-id="640">100000</amount>
        <hmac></hmac>
        <orders>
        <line-item>
        <product order-ref="ABC1234" sku="product-ticket">
        <type>100</type>
        <name>ONE WAY</name>
        <description>MNL-CEB</description>
        <airline-data>
        <profiles>
        <profile>
        <seq>1</seq>
        <title>Mr</title>
        <first-name>dan</first-name>
        <last-name>dan</last-name>
        <type>ADT</type>
        <contact-info>
        <email>dan@dan.com</email>
        <mobile country-id="640">9187231231</mobile>
        </contact-info>
        <additional-data>
        <param name="loyality_id">345rtyu</param>
        </additional-data>
        </profile>
        </profiles>
        <billing-summary>
        <fare-detail>
        <fare>
        <profile-seq>1</profile-seq>
        <description>adult</description>
        <currency>PHP</currency>
        <amount>60</amount>
        <product-code>ABF</product-code>
        <product-category>FARE</product-category>
        <product-item>Base fare for adult</product-item>
        </fare>
        </fare-detail>
        </billing-summary>
        <trips>
        <trip tag="1" seq="1">
        <origin external-id="MNL" country-id="640" time-zone="+08:00" terminal="1">Ninoy Aquino International Airport</origin>
        <destination external-id="CEB" country-id="640" time-zone="+08:00" terminal="2">Mactan Cebu International Airport</destination>
        <departure-time>2021-03-07T19:35:00Z</departure-time>
        <arrival-time>2021-03-07T21:05:00Z</arrival-time>
        <booking-class>Z</booking-class>
        <service-level>Economy</service-level>
        <transportation code="5J" number="1">
        <carriers>
        <carrier code="5J" type-id="Aircraft Boeing-737-9">
        <number>563</number>
        </carrier>
        </carriers>
        </transportation>
        <additional-data>
        <param name="fare_basis">we543s3</param>
        </additional-data>
        </trip>
        </trips>
        </airline-data>
        </product>
        <amount>125056</amount>
        <quantity>1</quantity>
        <additional-data>
        <param name="key">value</param>
        </additional-data>
        </line-item>
        </orders>
        </transaction>
        </update-order-data>
        </root>*/
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
        $pspID = Constants::iWIRE_CARD_PSP;
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10078, 1, 640, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10078, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (100780, 10078)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10078, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10078, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100780, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10078, 2, $pspID, true, 1)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (3, 10078, 100780, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, countryid, orderid, callbackurl, amount, ip, auto_capture, enabled, currencyid, sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10078, 100780, 1,  2, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', 1, TRUE, 208,3,5000,208)");

        $orderXml = '<orders> <line-item> <product order-ref="ABC1234" sku="product-ticket"> <type>100</type> <name>ONE WAY</name> <description>MNL-CEB</description> <airline-data> <profiles> <profile> <seq>1</seq> <title>Mr</title> <first-name>dan</first-name> <last-name>dan</last-name> <type>ADT</type> <contact-info> <email>dan@dan.com</email> <mobile country-id="640">9187231231</mobile> </contact-info> <additional-data> <param name="loyality_id">345rtyu</param> </additional-data> </profile> </profiles> <billing-summary> <fare-detail> <fare> <profile-seq>1</profile-seq> <description>adult</description> <currency>PHP</currency> <amount>60</amount> <product-code>ABF</product-code> <product-category>FARE</product-category> <product-item>Base fare for adult</product-item> </fare> </fare-detail> </billing-summary> <trips> <trip tag="1" seq="1"> <origin external-id="MNL" country-id="640" time-zone="+08:00" terminal="1">Ninoy Aquino International Airport</origin> <destination external-id="CEB" country-id="640" time-zone="+08:00" terminal="2">Mactan Cebu International Airport</destination> <departure-time>2021-03-07T19:35:00Z</departure-time> <arrival-time>2021-03-07T21:05:00Z</arrival-time> <booking-class>Z</booking-class> <service-level>Economy</service-level> <transportation code="5J" number="1"> <carriers> <carrier code="5J" type-id="Aircraft Boeing-737-9"> <number>563</number> </carrier> </carriers> </transportation> <additional-data> <param name="fare_basis">we543s3</param> </additional-data> </trip> </trips> </airline-data> </product> <amount>125056</amount> <quantity>1</quantity> <additional-data> <param name="key">value</param> </additional-data> </line-item>  </orders>';

        $xml = $this->getInitDoc(1001001, "ref123",  $orderXml);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertStringContainsString('<status code = "100">Operation Success</status>', $sReplyBody);

        //Check passenger_tbl entry
        $res =  $this->queryDB("SELECT seq from Log.Order_Tbl ot join Log.passenger_tbl pt on ot.id = pt.order_id WHERE ot.orderref='ABC1234'");

        $this->assertTrue(is_resource($res) );

        while ($row = pg_fetch_assoc($res) )
        {
            $seq = (int)$row["seq"];
        }
        $this->assertEquals(1, $seq);

        //Check billing_summary_tbl entry
        $res =  $this->queryDB("SELECT profile_seq, trip_tag, trip_seq, product_code, product_category, product_item from Log.Order_Tbl ot join Log.billing_summary_tbl bst on ot.id = bst.order_id WHERE ot.orderref='ABC1234' and bst.bill_type='Fare'");

        $this->assertTrue(is_resource($res) );

        while ($row = pg_fetch_assoc($res) )
        {
            $profileSeq = (int) $row['profile_seq'];
            $tripTag = (int) $row['trip_tag'];
            $tripSeq = (int) $row['trip_seq'];
            $productCode = $row["product_code"];
            $productCat = $row['product_category'];
            $productItem = $row['product_item'];

        }
        $this->assertEquals(1, $profileSeq);
        $this->assertEquals(0, $tripTag);
        $this->assertEquals(0, $tripSeq);
        $this->assertEquals('ABF', $productCode);
        $this->assertEquals('FARE', $productCat);
        $this->assertEquals('Base fare for adult', $productItem);

        //Check flight_tbl entry
        $res =  $this->queryDB("SELECT op_flight_number, arrival_timezone, mkt_airline_code, departure_city, arrival_city, aircraft_type, arrival_terminal, departure_terminal from Log.Order_Tbl ot join Log.flight_tbl ft on ot.id = ft.order_id WHERE ot.orderref='ABC1234'");

        $this->assertTrue(is_resource($res) );

        while ($row = pg_fetch_assoc($res) )
        {
            $opFlightNumber = $row["op_flight_number"];
            $arrivalTz = $row["arrival_timezone"];
            $mktAirlineCode = $row["mkt_airline_code"];
            $deptCity = $row["departure_city"];
            $arrCity = $row["arrival_city"];
            $aircraftType = $row["aircraft_type"];
            $arrivalTerminal = $row["arrival_terminal"];
            $deptTerminal = $row["departure_terminal"];

        }
        $this->assertEquals('1', $opFlightNumber);
        $this->assertEquals('+08:00', $arrivalTz);
        $this->assertEquals('5J', $mktAirlineCode);
        $this->assertEquals('Ninoy Aquino International Airport', $deptCity);
        $this->assertEquals('Mactan Cebu International Airport', $arrCity);
        $this->assertEquals('Aircraft Boeing-737-9', $aircraftType);
        $this->assertEquals('2', $arrivalTerminal);
        $this->assertEquals('1', $deptTerminal);

        //Check order_tbl entry
        $res =  $this->queryDB("SELECT orderref, type from Log.Order_Tbl ot WHERE ot.orderref='ABC1234'");

        $this->assertTrue(is_resource($res) );

        while ($row = pg_fetch_assoc($res) )
        {
            $orderref = $row["orderref"];
            $type = $row["type"];
        }
        $this->assertEquals('ABC1234', $orderref);
        $this->assertEquals(100, $type);

    }

    public function testUpdateExistingAID()
    {
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
        $pspID = Constants::iWIRE_CARD_PSP;
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10078, 1, 640, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10078, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (100780, 10078)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10078, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10078, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100780, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10078, 2, $pspID, true, 1)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10078, 100780, 208, 100, 4001, '1513-005', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1001001, 100, 10078, 100780, 100, $pspID, '1512', '1513-005', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,5000)");
        $this->queryDB("INSERT INTO log.additional_data_tbl(name, value, type, externalid) VALUES('FCTxnID', '243001', 'Transaction','1001001')");
        $this->queryDB("INSERT INTO Log.Order_Tbl (id, orderref, txnid, countryid, amount, quantity, productsku, productname, productdescription, productimageurl, points, reward,fees) VALUES (10, 'SOCGN6', 1001001, 100, 100, 1, '103-1418291', 'return journey', 'return journey', 'https://www.cpm.com', 300, 1, 0);");
        $this->queryDB("INSERT INTO Log.Flight_Tbl (id, service_class,mkt_flight_number, departure_airport, arrival_airport, op_airline_code, order_id, arrival_date, departure_date, tag, trip_count, service_level, departure_countryid, arrival_countryid, departure_timezone) VALUES (10, 'X', '1850', 'CEB', 'MNL', 'PR', '10', '2020-05-23 13:55:00', '2020-05-23 12:40:00', '1', '2', '3', 200, 200, '+08:30')");
        $this->queryDB("INSERT INTO log.passenger_tbl (id, first_name, last_name, type, order_id, created, modified, title, email, mobile, country_id, amount, seq) VALUES (24, 'dan', 'dan', 'ADT', 10, '2021-04-09 13:06:23.420245', '2021-04-09 13:06:23.420245', 'Mr', 'dan@dan.com', '9187231231', '640', 0, 1)");
        $this->queryDB("INSERT INTO log.additional_data_tbl (id, name, value, type, created, modified, externalid) VALUES (109, 'loyality_id', '345rtyu', 'Passenger', '2021-04-09 13:06:23.406019', '2021-04-09 13:06:23.406019', 24);");
        $this->queryDB("INSERT INTO log.billing_summary_tbl (id, order_id, journey_ref, bill_type, description, amount, currency, created, modified, profile_seq, trip_tag, trip_seq, product_code, product_category, product_item) VALUES (45, 10, '', 'Fare', 'adult', '60', 'PHP', '2021-04-09 13:06:23.336965', '2021-04-09 13:06:23.336965', 1, 0, 0, 'ABF', 'FARE', 'Base fare for adult')");
        $this->queryDB("INSERT INTO log.billing_summary_tbl (id, order_id, journey_ref, bill_type, description, amount, currency, created, modified, profile_seq, trip_tag, trip_seq, product_code, product_category, product_item) VALUES (46, 10, '', 'Add-on', 'adult', '60', 'PHP', '2021-04-09 13:06:23.353398', '2021-04-09 13:06:23.353398', 1, 2, 2, 'ABF', 'FARE', 'Base fare for adult')");

        $orderXml = '<orders> <line-item> <product order-ref="SOCGN6" sku="product-ticket"> <type>200</type> <name>ONE WAY</name> <description>MNL-CEB</description> <airline-data> <profiles> <profile> <seq>2</seq> <title>Mr</title> <first-name>dan</first-name> <last-name>dan</last-name> <type>ADT</type> <contact-info> <email>dan@dan.com</email> <mobile country-id="640">9187231231</mobile> </contact-info> <additional-data> <param name="loyality_id">345rtyu</param> </additional-data> </profile> </profiles> <billing-summary> <fare-detail> <fare> <profile-seq>1</profile-seq> <description>adult</description> <currency>PHP</currency> <amount>60</amount> <product-code>ABF</product-code> <product-category>FARE</product-category> <product-item>Base fare for adult</product-item> </fare> </fare-detail> </billing-summary> <trips> <trip tag="1" seq="1"> <origin external-id="MNL" country-id="640" time-zone="+08:00" terminal="1">Ninoy Aquino International Airport</origin> <destination external-id="CEB" country-id="640" time-zone="+08:00" terminal="2">Mactan Cebu International Airport</destination> <departure-time>2021-03-07T19:35:00Z</departure-time> <arrival-time>2021-03-07T21:05:00Z</arrival-time> <booking-class>Z</booking-class> <service-level>Economy</service-level> <transportation code="5J" number="1"> <carriers> <carrier code="5J" type-id="Aircraft Boeing-737-9"> <number>563</number> </carrier> </carriers> </transportation> <additional-data> <param name="fare_basis">we543s3</param> </additional-data> </trip> </trips> </airline-data> </product> <amount>125056</amount> <quantity>1</quantity> <additional-data> <param name="key">value</param> </additional-data> </line-item>  </orders>';

        $xml = $this->getInitDoc(1001001, 100780, $orderXml);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);

        $this->assertEquals(200, $iStatus);

        //Check passenger_tbl entry
        $res =  $this->queryDB("SELECT seq from Log.Order_Tbl ot join Log.passenger_tbl pt on ot.id = pt.order_id WHERE ot.orderref='SOCGN6'");

        $this->assertTrue(is_resource($res) );

        while ($row = pg_fetch_assoc($res) )
        {
            $seq = (int)$row["seq"];
        }
        $this->assertNotEquals(2, $seq);

        //Check order_tbl entry
        $res =  $this->queryDB("SELECT orderref, type from Log.Order_Tbl ot WHERE ot.orderref='SOCGN6'");

        $this->assertTrue(is_resource($res) );

        while ($row = pg_fetch_assoc($res) )
        {
            $orderref = $row["orderref"];
            $type = $row["type"];
        }
        $this->assertEquals('SOCGN6', $orderref);
        $this->assertNotEquals(200, $type);

    }

    public function testIncorrectTxnId()
    {
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
        $pspID = Constants::iWIRE_CARD_PSP;
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10078, 1, 640, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10078, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (100780, 10078)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10078, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10078, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100780, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10078, 2, $pspID, true, 1)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10078, 100780, 208, 100, 4001, '1513-005', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1001001, 100, 10078, 100780, 100, $pspID, '1512', '1513-005', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,5000)");

        $orderXml = '<orders> <line-item> <product order-ref="SOCGN6" sku="product-ticket"> <type>200</type> <name>ONE WAY</name> <description>MNL-CEB</description> <airline-data> <profiles> <profile> <seq>2</seq> <title>Mr</title> <first-name>dan</first-name> <last-name>dan</last-name> <type>ADT</type> <contact-info> <email>dan@dan.com</email> <mobile country-id="640">9187231231</mobile> </contact-info> <additional-data> <param name="loyality_id">345rtyu</param> </additional-data> </profile> </profiles> <billing-summary> <fare-detail> <fare> <profile-seq>1</profile-seq> <description>adult</description> <currency>PHP</currency> <amount>60</amount> <product-code>ABF</product-code> <product-category>FARE</product-category> <product-item>Base fare for adult</product-item> </fare> </fare-detail> </billing-summary> <trips> <trip tag="1" seq="1"> <origin external-id="MNL" country-id="640" time-zone="+08:00" terminal="1">Ninoy Aquino International Airport</origin> <destination external-id="CEB" country-id="640" time-zone="+08:00" terminal="2">Mactan Cebu International Airport</destination> <departure-time>2021-03-07T19:35:00Z</departure-time> <arrival-time>2021-03-07T21:05:00Z</arrival-time> <booking-class>Z</booking-class> <service-level>Economy</service-level> <transportation code="5J" number="1"> <carriers> <carrier code="5J" type-id="Aircraft Boeing-737-9"> <number>563</number> </carrier> </carriers> </transportation> <additional-data> <param name="fare_basis">we543s3</param> </additional-data> </trip> </trips> </airline-data> </product> <amount>125056</amount> <quantity>1</quantity> <additional-data> <param name="key">value</param> </additional-data> </line-item>  </orders>';

        $xml = $this->getInitDoc(1001002, 100780, $orderXml);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertStringContainsString('<status code = "1001">Transaction with ID: 1001002 not found</status>', $sReplyBody);

    }
}
