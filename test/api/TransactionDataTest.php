<?php

use api\classes\AdditionalData;
use api\classes\Amount;
use api\classes\ProductInfo;
use api\classes\PSPData;
use api\classes\StateInfo;
use api\classes\TransactionData;
use api\classes\BillingAddress;

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once __DIR__ . '/../../api/classes/core/card.php';

class TransactionDataTest extends baseAPITest
{
    private TransactionData $transactionData;

    public function setUp() : void
    {
        parent::setUp(FALSE);
        $amount = new Amount(100, 840,2,"PHP",1);
        $stateInfo = new StateInfo(2010, 20103, 'Transaction Failed');
        $pspData = new PSPData(18, 'Wirecard', '23b7d8c8-b2a6-4817-af0a-af24ab66fd83');
        $card = new Card(['ID' => 7, 'MASKEDCARDNUMBER' => '411111******1111', 'EXPIRY' => '01/21']);
        $custmoerInfo = new CustomerInfo(-1, 200, 9876543210, 'sagar@cellpointmobile.com', '', '', 'en');
        $custmoerInfo->setDeviceId('device-id');
        $custmoerInfo->setOperator(20000);
        $this->transactionData = new TransactionData(1, 'abc_1', 'CD', '1', $amount, $stateInfo, $pspData, $card, $custmoerInfo);
        $this->assertEquals('{"id":1,"order_id":"abc_1","payment_method":"CD","payment_type":"1","amount":{"value":100,"currency_id":840,"decimals":2,"alpha3code":"PHP","conversion_rate":1},"status":{"code":2010,"sub_code":20103,"message":"Transaction Failed"},"psp":{"id":18,"name":"Wirecard","external_id":"23b7d8c8-b2a6-4817-af0a-af24ab66fd83"},"card":{"id":7,"masked_card_number":"411111******1111","expiry":"01\/21"},"customer_info":{"language":"en","email":"sagar@cellpointmobile.com","country_id":200,"mobile":9876543210,"operator":20000,"device_id":"device-id"}}', json_encode($this->transactionData));
        $this->assertStringContainsString('<transaction><id>1</id><order_id>abc_1</order_id><payment_method>CD</payment_method><payment_type>1</payment_type><amount><value>100</value><currency_id>840</currency_id><decimals>2</decimals><alpha3code>PHP</alpha3code><conversion_rate>1</conversion_rate></amount><status><code>2010</code><sub_code>20103</sub_code><message>Transaction Failed</message></status><psp><id>18</id><name>Wirecard</name><external_id>23b7d8c8-b2a6-4817-af0a-af24ab66fd83</external_id></psp><card><id>7</id><masked_card_number>411111******1111</masked_card_number><expiry>01/21</expiry></card><customer_info><language>en</language><email>sagar@cellpointmobile.com</email><country_id>200</country_id><mobile>9876543210</mobile><operator>20000</operator><device_id>device-id</device_id></customer_info></transaction>', xml_encode($this->transactionData));
    }

    
    public function testSetShortCode()
    {
        $this->transactionData->setShortCode('WRE');
        $this->assertStringContainsString('"short_code":"WRE"', json_encode($this->transactionData));
        $this->assertStringContainsString('<short_code>WRE', xml_encode($this->transactionData));
    }
    
    public function testSetDateTime()
    {
        $this->transactionData->setDateTime('2019-04-05T12:56:49+02:00');
        $this->assertStringContainsString('"date_time":"2019-04-05T12:56:49+02:00"', json_encode($this->transactionData));
        $this->assertStringContainsString('<date_time>2019-04-05T12:56:49+02:00', xml_encode($this->transactionData));
    }

    public function testSetLocalDateTime()
    {
        $this->transactionData->setLocalDateTime('2019-04-05T18:56:49+08:00');
        $this->assertStringContainsString('"local_date_time":"2019-04-05T18:56:49+08:00"', json_encode($this->transactionData));
        $this->assertStringContainsString('<local_date_time>2019-04-05T18:56:49+08:00', xml_encode($this->transactionData));
    }

    public function testSetFee()
    {
        $this->transactionData->setFee(100);
        $this->assertStringContainsString('"fee":100', json_encode($this->transactionData));
        $this->assertStringContainsString('<fee>100', xml_encode($this->transactionData));
    }

    public function testSetDescription()
    {
        $this->transactionData->setDescription('TestDescription');
        $this->assertStringContainsString('"description":"TestDescription"', json_encode($this->transactionData));
        $this->assertStringContainsString('<description>TestDescription', xml_encode($this->transactionData));
    }

    public function testSetForeignExchangeId()
    {
        $this->transactionData->setForeignExchangeId(111);
        $this->assertStringContainsString('"foreign_exchange_id":111', json_encode($this->transactionData));
        $this->assertStringContainsString('<foreign_exchange_id>111', xml_encode($this->transactionData));
    }

    public function testSetIssuingBank()
    {
        $this->transactionData->setIssuingBank('TestIssuingBank');
        $this->assertStringContainsString('"issuing_bank":"TestIssuingBank"', json_encode($this->transactionData));
        $this->assertStringContainsString('<issuing_bank>TestIssuingBank', xml_encode($this->transactionData));
    }

    public function testSetWalletId()
    {
        $this->transactionData->setWalletId(15);
        $this->assertStringContainsString('"wallet_id":15', json_encode($this->transactionData));
        $this->assertStringContainsString('<wallet_id>15', xml_encode($this->transactionData));
    }

    public function testSetApprovalCode()
    {
        $this->transactionData->setApprovalCode("123456");
        $this->assertStringContainsString('"approval_code":"123456"', json_encode($this->transactionData));
        $this->assertStringContainsString('<approval_code>123456', xml_encode($this->transactionData));
    }

    public function testSetHmac()
    {
        $this->transactionData->setHmac('f6100dd45e06767c78d7a5532057f4d1059318f463c0cf9d6e8e6c180bca4268e5e770f7a0f9dbd9a5b535dcc0521279dcb696e86d4d2546606f9d90bc96a1fe');
        $this->assertStringContainsString('"hmac":"f6100dd45e06767c78d7a5532057f4d1059318f463c0cf9d6e8e6c180bca4268e5e770f7a0f9dbd9a5b535dcc0521279dcb696e86d4d2546606f9d90bc96a1fe"', json_encode($this->transactionData));
        $this->assertStringContainsString('<hmac>f6100dd45e06767c78d7a5532057f4d1059318f463c0cf9d6e8e6c180bca4268e5e770f7a0f9dbd9a5b535dcc0521279dcb696e86d4d2546606f9d90bc96a1fe', xml_encode($this->transactionData));
    }

    public function testSetDeliveryInfo()
    {
        $additionalData = new AdditionalData('DeliveryInfo', 'Test');
        $this->transactionData->setDeliveryInfo([$additionalData]);
        trigger_error(xml_encode($this->transactionData));
        $this->assertStringContainsString('"delivery_info":[{"name":"DeliveryInfo","text":"Test"}]', json_encode($this->transactionData));
        $this->assertStringContainsString('<delivery_info><params><name>DeliveryInfo</name><text>Test</text></params></delivery_info>', xml_encode($this->transactionData));

    }

    public function testSetAdditionalData()
    {
        $additionalData = new AdditionalData('AdditionalData', 'Test');
        $this->transactionData->setAdditionalData([$additionalData]);
        $this->assertStringContainsString('"additional_data":[{"name":"AdditionalData","text":"Test"}]', json_encode($this->transactionData));
        $this->assertStringContainsString('<additional_data><params><name>AdditionalData</name><text>Test</text></params></additional_data>', xml_encode($this->transactionData));
    }

    public function testSetProductInfo()
    {
        $additionalData = new ProductInfo('Sample', 2,200);
        $this->transactionData->setProductInfo([$additionalData]);
        $this->assertStringContainsString('"product_info":[{"name":"Sample","quantity":2,"price":200}]', json_encode($this->transactionData));
        $this->assertStringContainsString('<ProductInfo><name>Sample</name><quantity>2</quantity><price>200</price></ProductInfo>', xml_encode($this->transactionData));
    }

    public function testSetShippingInfo()
    {
        $additionalData = new AdditionalData('ShippingInfo', 'Test');
        $this->transactionData->setShippingInfo([$additionalData]);
        $this->assertStringContainsString('"shipping_info":[{"name":"ShippingInfo","text":"Test"}]', json_encode($this->transactionData));
        $this->assertStringContainsString('<shipping_info><params><name>ShippingInfo</name><text>Test</text></params></shipping_info>', xml_encode($this->transactionData));
    }

    public function testSetClientData()
    {
        $additionalData = new AdditionalData('ClientData', 'Test');
        $this->transactionData->setClientData([$additionalData]);
        $this->assertStringContainsString('"client_data":[{"name":"ClientData","text":"Test"}]', json_encode($this->transactionData));
        $this->assertStringContainsString("<client_data><params><name>ClientData</name><text>Test</text></params></client_data>", xml_encode($this->transactionData));
    }

    public function testSetBillingAddress()
    {
        $billingAddr = new BillingAddress(['first_name' => 'test_first_name', 'last_name' => 'test_last_name']);
        $this->transactionData->setBillingAddress($billingAddr);
        $this->assertStringContainsString('"billing_address":{"first_name":"test_first_name","last_name":"test_last_name"}', json_encode($this->transactionData));
        $this->assertStringContainsString("<billing_address><first_name>test_first_name</first_name><last_name>test_last_name</last_name></billing_address>", xml_encode($this->transactionData));
    }

    public function testSetServiceTypeId()
    {
        $this->transactionData->setServiceTypeId(11);
        $this->assertStringContainsString('"service_type_id":11}', json_encode($this->transactionData));
        $this->assertStringContainsString('<service_type_id>11', xml_encode($this->transactionData));
    }

    public function testSetFraudStatusCode()
    {
        $this->transactionData->setFraudStatusCode("1123");
        $this->assertStringContainsString('"fraud_status_code":"1123"}', json_encode($this->transactionData));
        $this->assertStringContainsString('<fraud_status_code>1123', xml_encode($this->transactionData));
    }

    public function testSetFraudStatusDesc()
    {
        $this->transactionData->setFraudStatusDesc("reject");
        $this->assertStringContainsString('"fraud_status_desc":"reject"}', json_encode($this->transactionData));
        $this->assertStringContainsString('<fraud_status_desc>reject', xml_encode($this->transactionData));
    }

    public function testSetRouteConfigId()
    {
        $this->transactionData->setRouteConfigId(1);
        $this->assertStringContainsString('"route_config_id":1}', json_encode($this->transactionData));
        $this->assertStringContainsString('<route_config_id>1', xml_encode($this->transactionData));
    }

    public function testSetInstallment()
    {
        $this->transactionData->setInstallment(2);
        $this->assertStringContainsString('"installment":2}', json_encode($this->transactionData));
        $this->assertStringContainsString('<installment>2', xml_encode($this->transactionData));
    }
}