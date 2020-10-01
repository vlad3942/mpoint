<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name: MessageQueueClientTest
 */

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

use api\classes\messagequeue\client\GooglePubSub;
use api\classes\messagequeue\client\MessageQueueClient;
use api\classes\messagequeue\client\MessageQueueClientException;

class MessageQueueClientTest extends baseAPITest
{
    public function setUp()
    {
        parent::setUp(FALSE);
        $this->bIgnoreErrors = true;
    }

    public function testGetClientWithoutProviderInfo()
    {
        try {
            $client = MessageQueueClient::GetClient();
            $this->assertInstanceOf(GooglePubSub::class, $client);
        } catch (MessageQueueClientException $e) {
            $this->assertTrue(false);
        }
    }

    public function testGetClientWithoutProvider()
    {
        try {
            $aMessage_Queue_Provider_info['keyfile'] = 'samplefile';
            $aMessage_Queue_Provider_info['projectid'] = 'testproject';
            $aMessage_Queue_Provider_info['topicname'] = 'testtopic';
            $client = MessageQueueClient::GetClient($aMessage_Queue_Provider_info);
            $this->assertTrue(false);
        } catch (MessageQueueClientException $e) {
            $this->assertEquals('Message Queue Provider : Provider information is missing', $e->getMessage());
        }
    }

    public function testGetClientWithKeyFilePath()
    {
        try {
            $aMessage_Queue_Provider_info['provider'] = 'googlepubsub';
            $aMessage_Queue_Provider_info['keyfilepath'] = 'testData/testJsonFile.json';
            $aMessage_Queue_Provider_info['projectid'] = 'testproject';
            $aMessage_Queue_Provider_info['topicname'] = 'testtopic';
            $client = MessageQueueClient::GetClient($aMessage_Queue_Provider_info);
            $this->assertInstanceOf(GooglePubSub::class, $client);
        } catch (MessageQueueClientException $e) {
            $this->assertTrue(false);
        }
    }

    public function testGetClientWithoutKey()
    {
        try {
            $aMessage_Queue_Provider_info['provider'] = 'googlepubsub';
            $aMessage_Queue_Provider_info['projectid'] = 'testproject';
            $aMessage_Queue_Provider_info['topicname'] = 'testtopic';
            $client = MessageQueueClient::GetClient($aMessage_Queue_Provider_info);
            $this->assertInstanceOf(GooglePubSub::class, $client);
        } catch (MessageQueueClientException $e) {
            $this->assertTrue(false);
        }
    }

    public function testGetClientWithoutProjectId()
    {
        try {
            $aMessage_Queue_Provider_info['provider'] = 'googlepubsub';
            $aMessage_Queue_Provider_info['keyfile'] = 'samplefile';
            $aMessage_Queue_Provider_info['topicname'] = 'testtopic';
            $client = MessageQueueClient::GetClient($aMessage_Queue_Provider_info);
            $this->assertTrue(false);
        } catch (MessageQueueClientException $e) {
            $this->assertEquals('Message Queue Provider : Project id is missing', $e->getMessage());
        }
    }

    public function testGetClientWithoutTopicName()
    {
        try {
            $aMessage_Queue_Provider_info['provider'] = 'googlepubsub';
            $aMessage_Queue_Provider_info['keyfile'] = 'samplefile';
            $aMessage_Queue_Provider_info['projectid'] = 'testproject';
            $client = MessageQueueClient::GetClient($aMessage_Queue_Provider_info);
            $this->assertTrue(false);
        } catch (MessageQueueClientException $e) {
            $this->assertEquals('Message Queue Provider : Topic Name is missing', $e->getMessage());
        }
    }

    public function testGetClientWithInvalideProvider()
    {
        try {
            $aMessage_Queue_Provider_info['provider'] = 'invalidProvider';
            $aMessage_Queue_Provider_info['keyfile'] = 'samplefile';
            $aMessage_Queue_Provider_info['projectid'] = 'testproject';
            $aMessage_Queue_Provider_info['topicname'] = 'testtopic';
            $client = MessageQueueClient::GetClient($aMessage_Queue_Provider_info);
            $this->assertTrue(false);
        } catch (MessageQueueClientException $e) {
            $this->assertEquals('Message Queue Provider : Invalid Provider', $e->getMessage());
        }
    }

    public function testGetClient()
    {
        try {
            $aMessage_Queue_Provider_info['provider'] = 'googlepubsub';
            $aMessage_Queue_Provider_info['keyfile'] = 'samplefile';
            $aMessage_Queue_Provider_info['projectid'] = 'testproject';
            $aMessage_Queue_Provider_info['topicname'] = 'testtopic';
            $client = MessageQueueClient::GetClient($aMessage_Queue_Provider_info);
            $this->assertInstanceOf(GooglePubSub::class, $client);
        } catch (MessageQueueClientException $e) {
            $this->assertTrue(false);
        }
    }
}