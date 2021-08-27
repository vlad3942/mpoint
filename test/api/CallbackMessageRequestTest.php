<?php
use api\classes\Amount;
use api\classes\CallbackMessageRequest;
use api\classes\PSPData;
use api\classes\StateInfo;
use api\classes\TransactionData;

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
        $sale_amount = new Amount(100, 840, 2,'PHP',NULL);
        $obj_StateInfo = new StateInfo(2010, 20103, "Payment failed");
        $callback_url =  $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
        $this->callbackMessageRequest = new CallbackMessageRequest(10007, 100007, 1234, $sale_amount, $obj_StateInfo, [$transactionData],$callback_url);
        $this->assertEquals('{"client_id":10007,"account_id":100007,"session_id":1234,"sale_amount":{"value":100,"currency_id":840,"decimals":2,"alpha3code":"PHP"},"status":{"code":2010,"sub_code":20103,"message":"Payment failed"},"transactions":[{"id":1,"order_id":"abc_1","payment_method":"CD","payment_type":"1","amount":{"value":100,"currency_id":840,"decimals":2,"alpha3code":"PHP","conversion_rate":1},"status":{"code":2010,"sub_code":20103,"message":"Transaction Failed"},"psp":{"id":18,"name":"Wirecard","external_id":"23b7d8c8-b2a6-4817-af0a-af24ab66fd83"},"card":{"id":7,"name":"Master Card","masked_card_number":"411111******1111","expiry":"01\/21"},"customer_info":{"language":"en","email":"sagar@cellpointmobile.com","country_id":200,"mobile":9876543210,"operator":20000,"device_id":"device-id"}}],"callback_url":"http:\/\/mpoint.local.cellpointmobile.com\/_test\/simulators\/mticket\/callback.php"}', json_encode($this->callbackMessageRequest));
        $this->assertStringContainsString("<session><client_id>10007</client_id><account_id>100007</account_id><session_id>1234</session_id><sale_amount><value>100</value><currency_id>840</currency_id><decimals>2</decimals><alpha3code>PHP</alpha3code></sale_amount><status><code>2010</code><sub_code>20103</sub_code><message>Payment failed</message></status><transactions><transaction><id>1</id><order_id>abc_1</order_id><payment_method>CD</payment_method><payment_type>1</payment_type><amount><value>100</value><currency_id>840</currency_id><decimals>2</decimals><alpha3code>PHP</alpha3code><conversion_rate>1</conversion_rate></amount><status><code>2010</code><sub_code>20103</sub_code><message>Transaction Failed</message></status><psp><id>18</id><name>Wirecard</name><external_id>23b7d8c8-b2a6-4817-af0a-af24ab66fd83</external_id></psp><card><id>7</id><masked_card_number>411111******1111</masked_card_number><expiry>01/21</expiry></card><customer_info><language>en</language><email>sagar@cellpointmobile.com</email><country_id>200</country_id><mobile>9876543210</mobile><operator>20000</operator><device_id>device-id</device_id></customer_info></transaction></transactions><callback_url>http://mpoint.local.cellpointmobile.com/_test/simulators/mticket/callback.php</callback_url></session>", xml_encode($this->callbackMessageRequest));
    }

    public function testPendingAmt()
    {
        $pending_amt = new Amount(100, 840, 2,'PHP',NULL);
        $this->callbackMessageRequest->setPendingAmt($pending_amt) ;
        $this->assertStringContainsString('"pending_amount":{"value":100,"currency_id":840,"decimals":2,"alpha3code":"PHP"}}', json_encode($this->callbackMessageRequest));
        $this->assertStringContainsString("<pending_amount><value>100</value><currency_id>840</currency_id><decimals>2</decimals><alpha3code>PHP</alpha3code></pending_amount>", xml_encode($this->callbackMessageRequest));
    }
}