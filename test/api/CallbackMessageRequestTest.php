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
    }

    public function test__construct()
    {
        $amount = new Amount(100, 840,2,"PHP", 1);
        $stateInfo = new StateInfo(2010, 20103, 'Transaction Failed');
        $pspData = new PSPData(18, 'Wirecard', '23b7d8c8-b2a6-4817-af0a-af24ab66fd83');
        $card = new Card(['ID' => 7, 'MASKEDCARDNUMBER' => '411111******1111', 'EXPIRY' => '01/21']);
        $custmoerInfo = new CustomerInfo(-1, 200, 9876543210, 'sagar@cellpointmobile.com', '', '', 'en');
        $custmoerInfo->setDeviceId('device-id');
        $custmoerInfo->setOperator(20000);
        $transactionData = new TransactionData(1, 'abc_1', 'CD', '1', $amount, $stateInfo, $pspData, $card, $custmoerInfo);
        $sale_amount = new Amount(100, 840, 2,'PHP',NULL);
        $obj_StateInfo = new StateInfo(2010, 20103, "Payment failed");
        $callback_url =  $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
        $callbackMessageRequest = new CallbackMessageRequest(10007, 100007, 1234, $sale_amount, $obj_StateInfo, [$transactionData],$callback_url);
        $this->assertEquals('{"client_id":10007,"account_id":100007,"session_id":1234,"sale_amount":{"value":100,"currency_id":840,"decimals":2,"alpha3code":"PHP"},"status":{"code":2010,"sub_code":20103,"message":"Payment failed"},"transactions":[{"id":1,"order_id":"abc_1","payment_method":"CD","payment_type":"1","amount":{"value":100,"currency_id":840,"decimals":2,"alpha3code":"PHP","conversion_rate":1},"status":{"code":2010,"sub_code":20103,"message":"Transaction Failed"},"psp":{"id":18,"name":"Wirecard","external_id":"23b7d8c8-b2a6-4817-af0a-af24ab66fd83"},"card":{"id":7,"masked_card_number":"411111******1111","expiry":"01\/21"},"customer_info":{"language":"en","email":"sagar@cellpointmobile.com","country_id":200,"mobile":9876543210,"operator":20000,"device_id":"device-id"}}],"callback_url":"http:\/\/mpoint.local.cellpointmobile.com\/_test\/simulators\/mticket\/callback.php"}', json_encode($callbackMessageRequest));
    }
}