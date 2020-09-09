<?php

use api\classes\AdditionalData;
use api\classes\Amount;
use api\classes\ProductInfo;
use api\classes\PSPData;
use api\classes\StateInfo;
use api\classes\TransactionData;

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once __DIR__ . '/../../api/classes/core/card.php';

class TransactionDataTest extends baseAPITest
{
    private $transactionData;

    public function setUp()
    {
        parent::setUp(FALSE);
        $amount = new Amount(100, 840, 1);
        $stateInfo = new StateInfo(2010, 20103, 'Transaction Failed');
        $pspData = new PSPData(18, 'Wirecard', '23b7d8c8-b2a6-4817-af0a-af24ab66fd83');
        $card = new Card(['ID' => 7, 'MASKCARDNUMBER' => '411111******1111', 'EXPIRY' => '01/21']);
        $custmoerInfo = new CustomerInfo(-1, 200, 9876543210, 'sagar@cellpointmobile.com', '', '', 'en');
        $custmoerInfo->setDeviceId('device-id');
        $custmoerInfo->setOperator(20000);
        $this->transactionData = new TransactionData(1, 'abc_1', 'CD', '1', $amount, $stateInfo, $pspData, $card, $custmoerInfo);
        $this->assertEquals('{"id":1,"order_id":"abc_1","payment_method":"CD","payment_type":"1","amount":{"value":100,"currency_id":840,"conversion_rate":1},"status":{"code":2010,"sub_code":20103,"message":"Transaction Failed"},"psp":{"id":18,"name":"Wirecard","external_id":"23b7d8c8-b2a6-4817-af0a-af24ab66fd83"},"card":{"id":7,"card_mask_number":"411111******1111","expiry":"01\/21"},"customer_info":{"language":"en","email":"sagar@cellpointmobile.com","country_id":200,"mobile":9876543210,"operator":20000,"device_id":"device-id"}}', json_encode($this->transactionData));
    }

    
    public function testSetShortCode()
    {
        $this->transactionData->setShortCode('WRE');
        $this->assertContains('"short_code":"WRE"', json_encode($this->transactionData));
    }
    
    public function testSetDateTime()
    {
        $this->transactionData->setDateTime('2019-04-05T12:56:49+02:00');
        $this->assertContains('"date_time":"2019-04-05T12:56:49+02:00"', json_encode($this->transactionData));
    }

    public function testSetLocalDateTime()
    {
        $this->transactionData->setLocalDateTime('2019-04-05T18:56:49+08:00');
        $this->assertContains('"local_date_time":"2019-04-05T18:56:49+08:00"', json_encode($this->transactionData));
    }

    public function testSetFee()
    {
        $this->transactionData->setFee(100);
        $this->assertContains('"fee":100', json_encode($this->transactionData));
    }

    public function testSetDescription()
    {
        $this->transactionData->setDescription('TestDescription');
        $this->assertContains('"description":"TestDescription"', json_encode($this->transactionData));
    }

    public function testSetForeignExchangeId()
    {
        $this->transactionData->setForeignExchangeId(111);
        $this->assertContains('"foreign_exchange_id":111', json_encode($this->transactionData));
    }

    public function testSetIssuingBank()
    {
        $this->transactionData->setIssuingBank('TestIssuingBank');
        $this->assertContains('"issuing_bank":"TestIssuingBank"', json_encode($this->transactionData));
    }

    public function testSetWalletId()
    {
        $this->transactionData->setWalletId(15);
        $this->assertContains('"wallet_id":15', json_encode($this->transactionData));
    }

    public function testSetApprovalCode()
    {
        $this->transactionData->setApprovalCode("123456");
        $this->assertContains('"approval_code":"123456"', json_encode($this->transactionData));
    }

    public function testSetHmac()
    {
        $this->transactionData->setHmac('f6100dd45e06767c78d7a5532057f4d1059318f463c0cf9d6e8e6c180bca4268e5e770f7a0f9dbd9a5b535dcc0521279dcb696e86d4d2546606f9d90bc96a1fe');
        $this->assertContains('"hmac":"f6100dd45e06767c78d7a5532057f4d1059318f463c0cf9d6e8e6c180bca4268e5e770f7a0f9dbd9a5b535dcc0521279dcb696e86d4d2546606f9d90bc96a1fe"', json_encode($this->transactionData));
    }

    public function testSetDeliveryInfo()
    {
        $additionalData = new AdditionalData('DeliveryInfo', 'Test');
        $this->transactionData->setDeliveryInfo([$additionalData]);
        $this->assertContains('"delivery_info":[{"key":"DeliveryInfo","value":"Test"}]', json_encode($this->transactionData));

    }

    public function testSetAdditionalData()
    {
        $additionalData = new AdditionalData('AdditionalData', 'Test');
        $this->transactionData->setAdditionalData([$additionalData]);
        $this->assertContains('"additional_data":[{"key":"AdditionalData","value":"Test"}]', json_encode($this->transactionData));
    }

    public function testSetProductInfo()
    {
        $additionalData = new ProductInfo('Sample', 2,200);
        $this->transactionData->setProductInfo([$additionalData]);
        $this->assertContains('"product_info":[{"name":"Sample","quantity":2,"price":200}]', json_encode($this->transactionData));
    }

    public function testSetShippingInfo()
    {
        $additionalData = new AdditionalData('ShippingInfo', 'Test');
        $this->transactionData->setShippingInfo([$additionalData]);
        $this->assertContains('"shipping_info":[{"key":"ShippingInfo","value":"Test"}]', json_encode($this->transactionData));
    }

    public function testSetClientData()
    {
        $additionalData = new AdditionalData('ClientData', 'Test');
        $this->transactionData->setClientData([$additionalData]);
        $this->assertContains('"client_data":[{"key":"ClientData","value":"Test"}]', json_encode($this->transactionData));
    }

    public function testSetBillingAddress()
    {
        $additionalData = new AdditionalData('BillingAddress', 'Test');
        $this->transactionData->setBillingAddress([$additionalData]);
        $this->assertContains('"billing_address":[{"key":"BillingAddress","value":"Test"}]', json_encode($this->transactionData));
    }
}