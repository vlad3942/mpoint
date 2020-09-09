<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

use api\classes\AdditionalData;

class AdditionalDataTest extends baseAPITest
{

    public function setUp()
    {
        parent::setUp(FALSE);
    }

    public function test__construct()
    {
        $additional_data = new AdditionalData('Name', 'Value');
        $json_string = json_encode($additional_data);
        $this->assertEquals('{"name":"Name","value":"Value"}', $json_string);
    }

    public function testEmpty__construct()
    {
        $additional_data = new AdditionalData(NULL, 'Value');
        $json_string = json_encode($additional_data);
        $this->assertEquals('[]', $json_string);
    }
}