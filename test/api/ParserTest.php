<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package:
 * File Name:ParserTest.php
 */

include __DIR__ . '/../../api/classes/Parser.php';

class ParserTest extends PHPUnit_Framework_TestCase
{

    private $parser = null;

    public function testSetRule()
    {
        $this->parser->setRule('storefront', '(markup-language)=="app"="app":"NMA"');
        $rules = $this->parser->getRules();
        $this->assertArrayHasKey('storefront', $rules);
        $this->assertEquals('(markup-language)=="app"="app":"NMA"', $rules['storefront']);
    }

    /*public function testXMLParseException()
    {
        $tempParser = new \mPoint\Core\Parser();

        $context = 'Invalid XML<?xml version="1.0" encoding="utf-8"?><root><authorize client-id="10007" account="100007"><psp-config id="21" type="1"><name>GlobalCollect</name><merchant-account>337</merchant-account><merchant-sub-account>-1</merchant-sub-account><username>d05f1d86bf1611f7</username><password>t91nuhCNWxVgRJNQK21CXRNAueGjkQT4zuiGPKF/opo=</password><messages></messages><additional-config></additional-config></psp-config><transaction id="1844619" type="30" gmid="-1" mode="0" eua-id="52365" attempt="0" psp-id="21" card-id="8" wallet-id="0" product-type="100" external-id=""><captured-amount country-id="604" currency="KWD" symbol="" format="" alpha2code="KW" alpha3code="KWT" code="414">0</captured-amount><fee country-id="604" currency="KWD" symbol="" format="">0</fee><price /><points country-id="0" currency="points" symbol="points" format="{PRICE} {CURRENCY}">-1</points><reward country-id="0" currency="points" symbol="points" format="{PRICE} {CURRENCY}">-1</reward><refund country-id="604" currency="KWD" symbol="" format="">0</refund><orderid>NYSI0O</orderid><mobile country-id="200" country-code="965">9876543210</mobile><operator>20000</operator><email>sagar@cellpointmobile.com</email><device-id /><logo><url>https://hpp-dev2.cellpointmobile.com/css/img/logo.jpg</url><width>100%</width><height>20%</height></logo><css-url>https://s3-ap-southeast-1.amazonaws.com/cpmassets/marchant/mpl/style.css</css-url><accept-url>http://dev2.cellpointmobile.com:8989/booking-confirmation</accept-url><cancel-url>http://dev2.cellpointmobile.com:8989/booking-confirmation</cancel-url><decline-url /><callback-url>https://webhook.site/f4f16c2f-9a98-4241-98ba-5fcc16092231</callback-url><icon-url /><auth-url>http://localhost:10081/mprofile/login</auth-url><language>us</language><auto-capture>false</auto-capture><auto-store-card>false</auto-store-card><markup-language>html5</markup-language><customer-ref /><description /><ip>::1</ip><hmac>df1c34c3ce2b5617a8f3eb7ed1f3145b55fc5e68</hmac><created-date>20190304</created-date><created-time>194856</created-time><authorized-amount country-id="604" currency-id="414" currency="KWD" decimals="3" symbol="" format="" alpha2code="KW" alpha3code="KWT" code="414">100</authorized-amount></transaction><order-attempt>1</order-attempt><card type-id="8"><card-holder-name>GC Test</card-holder-name><card-number>4567350000427977</card-number><expiry-month>12</expiry-month><expiry-year>2020</expiry-year><cvc>123</cvc></card><address country-id="640" alpha2code="PH" alpha3code="PHL" code="608"><full-name>First Last</full-name><company>Cellpoint Mobile</company><street>Place Street 2</street><postal-code>23456789</postal-code><city>Town City</city><state>Place State</state></address></authorize></root>';
        $tempParser->setContext($context);
        $tempParser->parse();
        $this->expectException();
    }*/

    public function testSetRules()
    {
        $rulesString = 'orderid ::= (transaction.orderid)
                        transactionid ::= (transaction.@id)
                        attempt ::= (@attempt)
                        invoiceid ::= <orderid>"CPM"<transactionid><attempt>';
        $this->parser->setRules($rulesString);
        $rules = $this->parser->getRules();
        $this->assertArrayHasKey('orderid', $rules);
        $this->assertEquals('(transaction.orderid)', $rules['orderid']);
        $this->assertArrayHasKey('transactionid', $rules);
        $this->assertEquals('(transaction.@id)', $rules['transactionid']);
        $this->assertArrayHasKey('attempt', $rules);
        $this->assertEquals('(@attempt)', $rules['attempt']);
        $this->assertArrayHasKey('invoiceid', $rules);
        $this->assertEquals('<orderid>"CPM"<transactionid><attempt>', $rules['invoiceid']);
    }

    /**
     * @depends testSetRule
     * @depends testSetRules
     */
    public function testGetValue()
    {
        $rulesString = 'orderid ::= (transaction.orderid)
                        transactionid ::= (transaction.@id)
                        attempt ::= (@attempt)
                        invoiceid ::= <orderid>"CPM"<transactionid><attempt>';
        $this->parser->setRules($rulesString);

        $this->parser->parse();

        $orderIdOutput = $this->parser->getValue('orderid');
        $this->assertEquals('NYSI0O', $orderIdOutput);

        $invoiceIdOutput = $this->parser->getValue('invoiceid');
        $this->assertEquals('NYSI0OCPM18446190', $invoiceIdOutput);
    }

    public function testParse()
    {
        $rulesString = 'orderid ::= (transaction.orderid)
                        transactionid ::= (transaction.@id)
                        attempt ::= (@attempt)
                        invoiceid ::= <orderid>"CPM"<transactionid><attempt>';
        $this->parser->setRules($rulesString);
        $parseOutput = $this->parser->parse();
        $this->assertEquals('NYSI0OCPM18446190', $parseOutput);
    }

    public function testConstantsInRule()
    {
        $rulesString = 'orderid ::= "ConstantTest"';
        $this->parser->setRules($rulesString);
        $parseOutput = $this->parser->parse();
        $this->assertEquals('ConstantTest', $parseOutput);
    }

    public function testXPathInRule()
    {
        $rulesString = 'orderid ::= (transaction.orderid)
                         transactionid ::= (transaction.@id)';
        $this->parser->setRules($rulesString);
        $this->parser->parse();

        $nodeOutput = $this->parser->getValue('orderid');
        $this->assertEquals('NYSI0O', $nodeOutput);

        $attributeOutput = $this->parser->getValue('transactionid');
        $this->assertEquals('1844619', $attributeOutput);
    }

    public function testVariableInRule()
    {
        $rulesString = 'invoiceid ::=  <storefront>(orderid)(@attempt)
                          storefront ::=  "NMA"';
        $this->parser->setRules($rulesString);
        $output = $this->parser->parse();

        $this->assertEquals('NMANYSI0O0', $output);
    }

    public function testConditionInRule()
    {
        $rulesString = 'invoiceid ::=  <storefront>(orderid)(@attempt)
                          storefront ::=  (markup-language)=="html5"="web":"app"';
        $this->parser->setRules($rulesString);
        $output = $this->parser->parse();

        $this->assertEquals('webNYSI0O0', $output);
    }

    public function testVariableInXPathAndFuction()
    {
        $rulesString = 'invoiceid ::=  (orderid)(<attempt>){date."dmY"}{memory_get_usage}
                        attempt ::= "@attempt"';
        $this->parser->setRules($rulesString);
        $output = $this->parser->parse();

        $this->assertStringStartsWith('NYSI0O0'.date('dmY'), $output);
    }


    public function testAppleRuleOnSecondContext()
    {
        $rulesString = "invoiceid ::=  (property[@name='AIRLINE_CODE'])";

        $this->parser->setRules($rulesString);
        $output = $this->parser->parse();

        $this->assertEquals('6S', $output);
    }

    public function testInvalidXPath()
    {
        $rulesString = "invoiceid ::=  ([@name='AIRLINE_CODE'])";

        $this->parser->setRules($rulesString);
        $output = $this->parser->parse();

        $this->assertEquals('', $output);
    }

    public function testFunctionInRule()
    {
        $date = new DateTime();
        $timestamp = $date->getTimestamp();
        $rulesString = 'invoiceid ::=  <storefront>(orderid){date.dmY}(@attempt)
                          storefront ::=  (markup-language)=="html5"="web":"app"';
        $this->parser->setRules($rulesString);
        $this->parser->setRule('timestamp', '{date.dmY.' . $timestamp . '}');
        $output = $this->parser->parse();
        $this->assertEquals('webNYSI0O' . date("dmY") . '0', $output);
        $timestampOutput = $this->parser->getValue('timestamp');
        $this->assertEquals(date('dmY', $timestamp), $timestampOutput);
    }

    public function testOrOperatorInRule()
    {
        $rulesString = 'invoiceid ::= (@attempt)=="0"OR(transaction.@id)=="1844619"="pass"
                        invoiceid1 ::= (@attempt)=="1"OR(transaction.@id)=="1844619"="pass"
                        invoiceid2 ::= (@attempt)==0OR(transaction.@id)=="18446191"="pass"
                        invoiceid3 ::= (@attempt)=="1"OR(transaction.@id)=="18446191"="pass""
                        invoiceid4 ::= (@attempt)=="1"OR(transaction.@id)=="18446191"="pass":"fail"';
        $this->parser->setRules($rulesString);
        $this->parser->parse();

        $invoiceid = $this->parser->getValue('invoiceid');
        $this->assertEquals('pass', $invoiceid);
        $invoiceid1 = $this->parser->getValue('invoiceid1');
        $this->assertEquals('pass', $invoiceid1);
        $invoiceid2 = $this->parser->getValue('invoiceid2');
        $this->assertEquals('pass', $invoiceid2);
        $invoiceid3 = $this->parser->getValue('invoiceid3');
        $this->assertEquals(false, $invoiceid3);
        $invoiceid4 = $this->parser->getValue('invoiceid4');
        $this->assertEquals('fail', $invoiceid4);
    }

    public function testAndOperatorInRule()
    {
        $rulesString = 'invoiceid ::= (@attempt)=="0"AND(transaction.@id)=="1844619"="pass"
                        invoiceid1 ::= (@attempt)=="1"AND(transaction.@id)=="1844619"="pass":"fail"
                        invoiceid2 ::= (@attempt)==0AND(transaction.@id)=="18446191"="pass":"fail"
                        invoiceid3 ::= (@attempt)=="1"AND(transaction.@id)=="18446191"="pass""
                        invoiceid4 ::= (@attempt)=="1"AND(transaction.@id)=="18446191"="pass":"fail"';
        $this->parser->setRules($rulesString);
        $this->parser->parse();

        $invoiceid = $this->parser->getValue('invoiceid');
        $this->assertEquals('pass', $invoiceid);
        $invoiceid1 = $this->parser->getValue('invoiceid1');
        $this->assertEquals('fail', $invoiceid1);
        $invoiceid2 = $this->parser->getValue('invoiceid2');
        $this->assertEquals('fail', $invoiceid2);
        $invoiceid3 = $this->parser->getValue('invoiceid3');
        $this->assertEquals(false, $invoiceid3);
        $invoiceid4 = $this->parser->getValue('invoiceid4');
        $this->assertEquals('fail', $invoiceid4);
    }

    public function testUndefinedVariable()
    {
        $rulesString = 'invoiceid ::=  <storefront>(orderid)(@attempt)
                          storefront ::=  (markup-language)=="html5"="web":"app"';
        $this->parser->setRules($rulesString);
        $this->parser->parse();
        $output = $this->parser->getValue('undefinedvar');
        $this->assertEquals(null, $output);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->parser = new \mPoint\Core\Parser();

        $context1 = '<?xml version="1.0" encoding="utf-8"?><root><authorize client-id="10007" account="100007"><psp-config id="21" type="1"><name>GlobalCollect</name><merchant-account>337</merchant-account><merchant-sub-account>-1</merchant-sub-account><username>d05f1d86bf1611f7</username><password>t91nuhCNWxVgRJNQK21CXRNAueGjkQT4zuiGPKF/opo=</password><messages></messages><additional-config></additional-config></psp-config><transaction id="1844619" type="30" gmid="-1" mode="0" eua-id="52365" attempt="0" psp-id="21" card-id="8" wallet-id="0" product-type="100" external-id=""><captured-amount country-id="604" currency="KWD" symbol="" format="" alpha2code="KW" alpha3code="KWT" code="414">0</captured-amount><fee country-id="604" currency="KWD" symbol="" format="">0</fee><price /><points country-id="0" currency="points" symbol="points" format="{PRICE} {CURRENCY}">-1</points><reward country-id="0" currency="points" symbol="points" format="{PRICE} {CURRENCY}">-1</reward><refund country-id="604" currency="KWD" symbol="" format="">0</refund><orderid>NYSI0O</orderid><mobile country-id="200" country-code="965">9876543210</mobile><operator>20000</operator><email>sagar@cellpointmobile.com</email><device-id /><logo><url>https://hpp-dev2.cellpointmobile.com/css/img/logo.jpg</url><width>100%</width><height>20%</height></logo><css-url>https://s3-ap-southeast-1.amazonaws.com/cpmassets/marchant/mpl/style.css</css-url><accept-url>http://dev2.cellpointmobile.com:8989/booking-confirmation</accept-url><cancel-url>http://dev2.cellpointmobile.com:8989/booking-confirmation</cancel-url><decline-url /><callback-url>https://webhook.site/f4f16c2f-9a98-4241-98ba-5fcc16092231</callback-url><icon-url /><auth-url>http://localhost:10081/mprofile/login</auth-url><language>us</language><auto-capture>false</auto-capture><auto-store-card>false</auto-store-card><markup-language>html5</markup-language><customer-ref /><description /><ip>::1</ip><hmac>df1c34c3ce2b5617a8f3eb7ed1f3145b55fc5e68</hmac><created-date>20190304</created-date><created-time>194856</created-time><authorized-amount country-id="604" currency-id="414" currency="KWD" decimals="3" symbol="" format="" alpha2code="KW" alpha3code="KWT" code="414">100</authorized-amount></transaction><order-attempt>1</order-attempt><card type-id="8"><card-holder-name>GC Test</card-holder-name><card-number>4567350000427977</card-number><expiry-month>12</expiry-month><expiry-year>2020</expiry-year><cvc>123</cvc></card><address country-id="640" alpha2code="PH" alpha3code="PHL" code="608"><full-name>First Last</full-name><company>Cellpoint Mobile</company><street>Place Street 2</street><postal-code>23456789</postal-code><city>Town City</city><state>Place State</state></address></authorize></root>';
        $this->parser->setContext($context1);
        $context2 = '<client-config><additional-config><property name="AIRLINE_CODE">6S</property><property name="CARRIER_NAME">SAUDI GULF AIRLINES</property><property name="TICKET_ISSUE_CITY">DEIRA</property><property name="TIMEZONE">Asia/Kuala_Lumpur</property><property name="AIRLINE_NUMRIC_CODE">571</property></additional-config></client-config>';
        $this->parser->setContext($context2);
    }

}
