<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:CardValidatorTest.php
 */

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once sAPI_CLASS_PATH . 'simpledom.php';
require_once __DIR__ . '/../../api/classes/core/card.php';
require_once __DIR__ . '/../../api/classes/validation/cardvalidator.php';

class CardValidatorTest extends baseAPITest
{
    private $cardValidator;
    private $_OBJ_DB;

    public function setUp()
    {
        parent::setUp(TRUE);

        $cardXML = '<card  type-id="7">
                        <amount country-id="638" currency-id="458" >31000</amount>
                        <cvc>003</cvc>
                        <card-holder-name>test</card-holder-name>
                        <card-number>5555555555554444</card-number>
                        <expiry>01/21</expiry>				
                    </card>';

        $this->_OBJ_DB = RDB::produceDatabase($this->mPointDBInfo);

        $cardSimpleXMLDom = simpledom_load_string($cardXML);
        $objCard = new Card($cardSimpleXMLDom, $this->_OBJ_DB);
        $this->cardValidator = new CardValidator($objCard);
    }

    public function testValCardNumber()
    {
        $this->assertEquals(720, $this->cardValidator->valCardNumber());
    }

    public function tearDown()
    {
        $this->_OBJ_DB->disConnect();
        parent::tearDown();
    }

    public function testValCardFullName()
    {
        $this->assertEquals(730, $this->cardValidator->valCardFullName());
    }

    public function testValidateCVC()
    {
        $this->assertEquals(710, $this->cardValidator->validateCVC());
    }

    public function testNegativeScenario1()
    {

        $cardXML = '<card  type-id="7">
                        <amount country-id="638" currency-id="458" >31000</amount>
                        <cvc>03</cvc>
                        <card-holder-name>test888</card-holder-name>
                        <card-number>555555555555444</card-number>
                        <expiry>01/21</expiry>				
                    </card>';

        $cardSimpleXMLDom = simpledom_load_string($cardXML);
        $objCard = new Card($cardSimpleXMLDom, $this->_OBJ_DB);
        $cardValidator = new CardValidator($objCard);
        $this->assertEquals(711, $cardValidator->validateCVC());
        $this->assertEquals(722, $cardValidator->valCardNumber());
        $this->assertEquals(731, $cardValidator->valCardFullName());
    }
}
