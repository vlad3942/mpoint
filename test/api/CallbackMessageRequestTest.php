<?php
use api\classes\Amount;
use api\classes\CallbackMessageRequest;
use api\classes\PSPData;
use api\classes\StateInfo;
use api\classes\TransactionData;
use api\classes\billingsummary\info\BillingSummaryAbstract;
use api\classes\billingsummary\info\FareInfo;
use api\classes\billingsummary\info\AddonInfo;
use api\classes\OrderData;
use api\classes\BillingSummaryData;

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once __DIR__ . '/../../api/classes/core/card.php';


class CallbackMessageRequestTest extends baseAPITest
{
    protected $_aMPOINT_CONN_INFO;
    private CallbackMessageRequest $callbackMessageRequest;
    public function __construct()
    {
        parent::__construct();
        $this->constHTTPClient();
    }
    public function constHTTPClient()
    {
        global $aMPOINT_CONN_INFO;
        $this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO) );
    }

    public function getOrderData()
    {
        $pAdata = [
            ['name' => 'name1',
                'value' => 'val1'
            ],
            ['name' => 'name2',
                'value' => 'val2'
            ]
        ];
        $obj_passengerInfo = new PassengerInfo(1, 'fName', 'lName', 'ADT', 'Title1', 'test@gmail.com', '8989898989', '130', '20000', '1', $pAdata);
        $obj_passengerInfo1 = new PassengerInfo(2, 'fName', 'lName', 'ADT', 'Title1', 'test@gmail.com', '8989898989', '130', '20000', '2');
        $aObj_passengerInfo = array($obj_passengerInfo, $obj_passengerInfo1);

        $obj_flightInfo = new FlightInfo(1, 'G', '962', 'DVO', 'MNL', '5J', '2022-05-21T01:20:00Z', '2022-05-20T23:20:00Z', '1', '1', '3', '130', '150', $pAdata, '+08:00', 'XYZ', '+08:00', '1', 'Francisco Bangoy International Airport', 'Ninoy Aquino International Airport', '1', 'testAT1', 'testDT1');
        $obj_flightInfo2 = new FlightInfo(2, 'G', '962', 'DVO', 'MNL', '5J', '2022-05-21T01:20:00Z', '2022-05-20T23:20:00Z', '2', '2', '3', '130', '150', $pAdata, '+08:00', 'XYZ', '+08:00', '1', 'Francisco Bangoy International Airport', 'Ninoy Aquino International Airport', '1', 'testAT1', 'testDT1');
        $aObj_flightInfo = array($obj_flightInfo, $obj_flightInfo2);

        $obj_fareDetails = new FareInfo(1, '', 'Fare', 'Localization Key - SEAT PRICE', '20000', 'PHP', '1', '1', '1', 'YSTR', 'TAX', 'Sales Tax Colombia');
        $obj_fareDetails1 = new FareInfo(2, '', 'Fare', 'Localization Key - SEAT PRICE', '20000', 'PHP', '2', '2', '2', 'YSTR', 'TAX', 'Sales Tax Colombia');
        $aObj_fareDetails = array($obj_fareDetails, $obj_fareDetails1);

        $obj_addonDetails = new AddonInfo(3, '', 'Add-on', 'Localization Key - SEAT PRICE', '20000', 'PHP', '1', '1', '1', 'YSTR', 'TAX', 'Sales Tax Colombia');
        $obj_addonDetails1 = new AddonInfo(4, '', 'Add-on', 'Localization Key - SEAT PRICE', '20000', 'PHP', '2', '2', '2', 'YSTR', 'TAX', 'Sales Tax Colombia');
        $aObj_addonDetails = array($obj_addonDetails, $obj_addonDetails1);

        $aObj_billingSummaryData = new BillingSummaryData($aObj_fareDetails, $aObj_addonDetails);
        $obj_orderData = new OrderData($aObj_passengerInfo, $aObj_flightInfo, $aObj_billingSummaryData);
        return $obj_orderData;
    }

    public function setUp() : void
    {
        parent::setUp(FALSE);
        $amount = new Amount(100, 840,2,"PHP", 1);
        $stateInfo = new StateInfo(2010, 20103, 'Transaction Failed');
        $pspData = new PSPData(18, 'Wirecard', '23b7d8c8-b2a6-4817-af0a-af24ab66fd83');
        $card = new Card(['ID' => 7, 'NAME'=>'Master Card','MASKEDCARDNUMBER' => '411111******1111', 'EXPIRY' => '01/21']);
        $custmoerInfo = new CustomerInfo(-1, 200, 9876543210, 'sagar@cellpointmobile.com', '', '', 'en');
        $custmoerInfo->setDeviceId('device-id');
        $custmoerInfo->setOperator(20000);
        $transactionData = new TransactionData(1, 'abc_1', 'CD', '1', $amount, $stateInfo, $pspData, $card, $custmoerInfo);
        $transactionData->setOrderData($this->getOrderData());
        $sale_amount = new Amount(100, 840, 2,'PHP',NULL);
        $obj_StateInfo = new StateInfo(2010, 20103, "Payment failed");
        $callback_url =  $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
        $this->callbackMessageRequest = new CallbackMessageRequest(10007, 100007, 1234, $sale_amount, $obj_StateInfo, [$transactionData],$callback_url);
        $this->assertStringContainsString('{"client_id":10007,"account_id":100007,"session_id":1234,"sale_amount":{"value":100,"currency_id":840,"decimals":2,"alpha3code":"PHP"},"status":{"code":2010,"sub_code":20103,"message":"Payment failed"}', json_encode($this->callbackMessageRequest));
        $this->assertStringContainsString("<session><client_id>10007</client_id><account_id>100007</account_id><session_id>1234</session_id><sale_amount><value>100</value><currency_id>840</currency_id><decimals>2</decimals><alpha3code>PHP</alpha3code></sale_amount><status><code>2010</code><sub_code>20103</sub_code><message>Payment failed</message></status><transactions><transaction><id>1</id><order_id>abc_1</order_id><payment_method>CD</payment_method><payment_type>1</payment_type><amount><value>100</value><currency_id>840</currency_id><decimals>2</decimals><alpha3code>PHP</alpha3code><conversion_rate>1</conversion_rate></amount><status><code>2010</code><sub_code>20103</sub_code><message>Transaction Failed</message></status><psp><id>18</id><name>Wirecard</name><external_id>23b7d8c8-b2a6-4817-af0a-af24ab66fd83</external_id></psp><card><id>7</id><card_name>Master Card</card_name><masked_card_number>411111******1111</masked_card_number><expiry>01/21</expiry></card><customer_info><language>en</language><email>sagar@cellpointmobile.com</email><country_id>200</country_id><mobile>9876543210</mobile><operator>20000</operator><device_id>device-id</device_id></customer_info><order_data></order_data></transaction></transactions><callback_url>http://mpoint.local.cellpointmobile.com/_test/simulators/mticket/callback.php</callback_url></session>", xml_encode($this->callbackMessageRequest));
        $this->assertStringContainsString('{"profiles":[{"id":1,"first_name":"fName","last_name":"lName","type":"ADT","additional_data":[{"name":"name1","value":"val1"},{"name":"name2","value":"val2"}],"title":"Title1","email":"test@gmail.com","mobile":"8989898989","country_id":"130","amount":"20000","seq":"1"},{"id":2,"first_name":"fName","last_name":"lName","type":"ADT","title":"Title1","email":"test@gmail.com","mobile":"8989898989","country_id":"130","amount":"20000","seq":"2"}],"trips":[{"id":1,"service_class":"G","departure_airport":"DVO","arrival_airport":"MNL","op_airline_code":"5J","mkt_airline_code":"1","arrival_date":"2022-05-21T01:20:00Z","departure_date":"2022-05-20T23:20:00Z","additional_data":[{"name":"name1","value":"val1"},{"name":"name2","value":"val2"}],"mkt_flight_number":"962","op_flight_number":"XYZ","tag":"1","trip_count":"1","service_level":"3","departure_country_id":"130","arrival_country_id":"150","dept_time_zone":"+08:00","arrival_time_zone":"+08:00","arrival_terminal":"testAT1","dept_terminal":"testDT1","dept_city":"Francisco Bangoy International Airport","arrival_city":"Ninoy Aquino International Airport","aircraft_type":"1"},{"id":2,"service_class":"G","departure_airport":"DVO","arrival_airport":"MNL","op_airline_code":"5J","mkt_airline_code":"1","arrival_date":"2022-05-21T01:20:00Z","departure_date":"2022-05-20T23:20:00Z","additional_data":[{"name":"name1","value":"val1"},{"name":"name2","value":"val2"}],"mkt_flight_number":"962","op_flight_number":"XYZ","tag":"2","trip_count":"2","service_level":"3","departure_country_id":"130","arrival_country_id":"150","dept_time_zone":"+08:00","arrival_time_zone":"+08:00","arrival_terminal":"testAT1","dept_terminal":"testDT1","dept_city":"Francisco Bangoy International Airport","arrival_city":"Ninoy Aquino International Airport","aircraft_type":"1"}],"billing_summary":{"fare_details":[{"id":1,"journey_ref":"","bill_type":"Fare","description":"Localization Key - SEAT PRICE","amount":"20000","currency":"PHP","profile_seq":"1","trip_tag":"1","trip_seq":"1","product_code":"YSTR","product_category":"TAX","product_item":"Sales Tax Colombia"},{"id":2,"journey_ref":"","bill_type":"Fare","description":"Localization Key - SEAT PRICE","amount":"20000","currency":"PHP","profile_seq":"2","trip_tag":"2","trip_seq":"2","product_code":"YSTR","product_category":"TAX","product_item":"Sales Tax Colombia"}],"add_on":[{"id":3,"journey_ref":"","bill_type":"Add-on","description":"Localization Key - SEAT PRICE","amount":"20000","currency":"PHP","profile_seq":"1","trip_tag":"1","trip_seq":"1","product_code":"YSTR","product_category":"TAX","product_item":"Sales Tax Colombia"},{"id":4,"journey_ref":"","bill_type":"Add-on","description":"Localization Key - SEAT PRICE","amount":"20000","currency":"PHP","profile_seq":"2","trip_tag":"2","trip_seq":"2","product_code":"YSTR","product_category":"TAX","product_item":"Sales Tax Colombia"}]}}', json_encode($this->callbackMessageRequest));
        $this->assertStringContainsString('{"id":1,"order_id":"abc_1","payment_method":"CD","payment_type":"1","amount":{"value":100,"currency_id":840,"decimals":2,"alpha3code":"PHP","conversion_rate":1},"status":{"code":2010,"sub_code":20103,"message":"Transaction Failed"},"psp":{"id":18,"name":"Wirecard","external_id":"23b7d8c8-b2a6-4817-af0a-af24ab66fd83"},"card":{"id":7,"name":"Master Card","masked_card_number":"411111******1111","expiry":"01\/21"},"customer_info":{"language":"en","email":"sagar@cellpointmobile.com","country_id":200,"mobile":9876543210,"operator":20000,"device_id":"device-id"}', json_encode($this->callbackMessageRequest));
    }

    public function testPendingAmt()
    {
        $pending_amt = new Amount(100, 840, 2,'PHP',NULL);
        $this->callbackMessageRequest->setPendingAmt($pending_amt) ;
        $this->assertStringContainsString('"pending_amount":{"value":100,"currency_id":840,"decimals":2,"alpha3code":"PHP"}}', json_encode($this->callbackMessageRequest));
        $this->assertStringContainsString("<pending_amount><value>100</value><currency_id>840</currency_id><decimals>2</decimals><alpha3code>PHP</alpha3code></pending_amount>", xml_encode($this->callbackMessageRequest));
    }
}