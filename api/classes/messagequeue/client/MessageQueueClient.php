<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:MessageQueueClient.php
 */

namespace api\classes\messagequeue\client {

    use mPointException;

    /**
     * Exception class for all MessageQueue exceptions
     */
    class MessageQueueClientException extends mPointException
    {
    }

    class MessageQueueClient
    {
        /**
         * @param array $providerInfo
         *
         * @throws \api\classes\messagequeue\client\MessageQueueClientException
         */
        public static function GetClient(array $providerInfo = [])
        {
            if (count($providerInfo) === 0) {
                global $aMessage_Queue_Provider_info;
                $providerInfo = $aMessage_Queue_Provider_info;
            }
            $provider = $providerInfo['provider'];
            if (empty($provider)) {
                trigger_error('Message Queue Provider : Provider information is missing', E_USER_ERROR);
                throw new MessageQueueClientException('Message Queue Provider : Provider information is missing');
            }

            $keyFile = NULL;
            if (array_key_exists('keyfile', $providerInfo) && $providerInfo['keyfile'] !== '') {
                $keyFile = $providerInfo['keyfile'];
            } elseif (array_key_exists('keyfilepath', $providerInfo) && $providerInfo['keyfilepath'] !== '') {
                $keyFile = readfile($providerInfo['keyfilepath']);
            }

            $projectId = $providerInfo['projectid'];
            if (empty($projectId)) {
                trigger_error('Message Queue Provider : Provider information is missing', E_USER_ERROR);
                throw new MessageQueueClientException('Message Queue Provider : Project id is missing');
            }

            $topicName = $providerInfo['topicname'];
            if (empty($topicName)) {
                trigger_error('Message Queue Provider : Provider information is missing', E_USER_ERROR);
                throw new MessageQueueClientException("Message Queue Provider : Topic Name is missing");
            }

            if ($provider == 'googlepubsub') {
                return new GooglePubSub($keyFile, $projectId, $topicName);
            } else {
                trigger_error('Message Queue Provider : Invalid Provider', E_USER_ERROR);
                throw new MessageQueueClientException('Message Queue Provider : Invalid Provider');
            }
        }
    }
}