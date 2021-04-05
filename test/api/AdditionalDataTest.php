<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

use api\classes\AdditionalData;

class AdditionalDataTest extends baseAPITest
{

    public function setUp() : void
    {
        parent::setUp(FALSE);
    }

    public function test__construct()
    {
        $additional_data = new AdditionalData('Name', 'Value');
        $json_string = json_encode($additional_data);
        $this->assertEquals('{"key":"Name","value":"Value"}', $json_string);
    }
}