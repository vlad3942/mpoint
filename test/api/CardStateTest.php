<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anna Lagad
 * Copyright: Cellpoint Digital
 * Link: http://www.cellpointdigital.com
 * Project: mPoint
 * File Name:CardStateTest.php
 */

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once sAPI_CLASS_PATH . 'simpledom.php';
require_once __DIR__ . '/../../api/classes/crs/CardState.php';

class CardStateTest extends baseAPITest
{

    private $_OBJ_DB;

    public function setUp() : void
    {
        parent::setUp(true);
        $this->_OBJ_DB = RDB::produceDatabase($this->mPointDBInfo);
    }

    public function testSuccessGetCardState()
    {
        $aObj_CardStateConfig = CardState::produceConfig($this->_OBJ_DB);
        $xml = '<card_states>';
        foreach ($aObj_CardStateConfig as $obj_CardState)
        {
            $this->assertInstanceOf(CardState::class, $obj_CardState);

            if ( ($obj_CardState instanceof CardState) === true)
            {
                $xml .= $obj_CardState->toXML();
            }
        }
        $xml .= '</card_states>';

        $this->assertStringContainsString('<card_states><card_state><id>1</id><name>Enabled</name><enabled>true</enabled></card_state><card_state><id>2</id><name>Disabled By Merchant</name><enabled>true</enabled></card_state><card_state><id>3</id><name>Disabled By PSP</name><enabled>true</enabled></card_state><card_state><id>4</id><name>Prerequisite not Met</name><enabled>true</enabled></card_state><card_state><id>5</id><name>Temporarily Unavailable</name><enabled>true</enabled></card_state><card_state><id>6</id><name>Disable Show</name><enabled>true</enabled></card_state></card_states>', $xml);
    }

    public function tearDown() : void
    {
        $this->_OBJ_DB->disConnect();
        parent::tearDown();
    }


}
