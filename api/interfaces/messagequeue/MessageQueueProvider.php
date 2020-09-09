<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:MessageQueueProvider.php
 */

namespace api\interfaces\messagequeue {
    abstract class MessageQueueProvider
    {
        private $_keyFile;

        private $_projectId;

        private $_topicName;

        /**
         * MessageQueueProvider constructor.
         *
         * @param string $keyFile
         * @param string $projectId
         * @param string $topicName
         */
        public function __construct(string $keyFile, string $projectId, string $topicName)
        {
            $this->_keyFile = json_decode($keyFile, true);
            $this->_projectId = $projectId;
            $this->_topicName = $topicName;
        }

        /**
         * @param string $message
         *
         * @return bool
         */
        abstract public function publish($message);

        /**
         * @return string
         */
        protected function getKeyFile()
        {
            return $this->_keyFile;
        }

        /**
         * @return string
         */
        protected function getProjectId()
        {
            return $this->_projectId;
        }

        /**
         * @return string
         */
        protected function getTopicName()
        {
            return $this->_topicName;
        }

        /**
         * @return bool
         */
        abstract public function authenticate();

        abstract protected function getMessageQueueClient();
    }
}