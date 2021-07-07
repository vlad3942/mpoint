<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name: GooglePubSubTest
 */

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

use api\classes\messagequeue\client\GooglePubSub;
use Google\Cloud\PubSub\PubSubClient;

class GooglePubSubTest extends baseAPITest
{
    protected $_aMessage_Queue_Provider_info;

    public function setUp() : void
    {
        parent::setUp(FALSE);
        global $aMessage_Queue_Provider_info;
        //$this->bIgnoreErrors = true;
        $this->_aMessage_Queue_Provider_info = $aMessage_Queue_Provider_info;
    }

    public function testSuccessfulPublish()
    {
        $keyFile = $this->_aMessage_Queue_Provider_info['keyfile'];
        $projectId = $this->_aMessage_Queue_Provider_info['projectid'];
        $topicName = $this->_aMessage_Queue_Provider_info['topicname'];
        $_mqClient = new GooglePubSub($keyFile, $projectId, $topicName);
        $_mqClient->authenticate();

        $getMockMethod = self::getReflectionMethod('api\classes\messagequeue\client\GooglePubSub', 'getMessageQueueClient');
        $getMessageQueueClientResponse = $getMockMethod->invoke($_mqClient);

        $this->assertInstanceOf(PubSubClient::class,$getMessageQueueClientResponse);

        try {
            $thisResponse =  $_mqClient->publish("Hello");
            $this->assertTrue($thisResponse);
        }
        catch (Exception $exception)
        {
            $this->assertTrue(false);
        }
    }

    public function testSuccessfulPublishWithAttribute()
    {
        $keyFile = $this->_aMessage_Queue_Provider_info['keyfile'];
        $projectId = $this->_aMessage_Queue_Provider_info['projectid'];
        $topicName = $this->_aMessage_Queue_Provider_info['topicname'];
        $_mqClient = new GooglePubSub($keyFile, $projectId, $topicName);
        $_mqClient->authenticate();

        $getMockMethod = self::getReflectionMethod('api\classes\messagequeue\client\GooglePubSub', 'getMessageQueueClient');
        $getMessageQueueClientResponse = $getMockMethod->invoke($_mqClient);

        $this->assertInstanceOf(PubSubClient::class,$getMessageQueueClientResponse);

        try {
            $sid = 2000;
            $thisResponse =  $_mqClient->publish("Hello", ['status_code' => (string)$sid]);
            $this->assertTrue($thisResponse);
        }
        catch (Exception $exception)
        {
            $this->assertTrue(false);
        }
    }

    public function testUnSuccessfulPublish()
    {
        $keyFile = $this->_aMessage_Queue_Provider_info['keyfile'];
        $projectId = $this->_aMessage_Queue_Provider_info['projectid'];
        $topicName = "WrongTopic";
        $_mqClient = new GooglePubSub($keyFile, $projectId, $topicName);
        $_mqClient->authenticate();

        $getMockMethod = self::getReflectionMethod('api\classes\messagequeue\client\GooglePubSub', 'getMessageQueueClient');
        $getMessageQueueClientResponse = $getMockMethod->invoke($_mqClient);

        $this->assertInstanceOf(PubSubClient::class,$getMessageQueueClientResponse);

        try {
            $thisResponse =  $_mqClient->publish("Hello");
            $this->assertFalse($thisResponse);
        }
        catch (Exception $exception)
        {
            $this->assertTrue(True);
        }


    }

    public function test__constructWithoutKeyFile()
    {

        $keyFile = NUll;
        $projectId = $this->_aMessage_Queue_Provider_info['projectid'];
        $topicName = $this->_aMessage_Queue_Provider_info['topicname'];
        $_mqClient = new GooglePubSub(null, $projectId, $topicName);
        $this->assertInstanceOf(GooglePubSub::class,$_mqClient);
    }
}