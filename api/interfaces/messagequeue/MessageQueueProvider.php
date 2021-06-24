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
        private ?array $_keyFile;

        private string $_projectId;

        private string $_topicName;

        /**
         * MessageQueueProvider constructor.
         *
         * @param string|null $keyFile
         * @param string $projectId
         * @param string $topicName
         */
        public function __construct(?string $keyFile, string $projectId, string $topicName)
        {
            $this->_keyFile = empty($keyFile) === FALSE ? json_decode($keyFile, TRUE) : [];
            $this->_projectId = $projectId;
            $this->_topicName = $topicName;
        }

        /**
         * @param string $data
         * @param array|null $filter
         * @return bool
         */
        abstract public function publish(string $data, array $filter = null): bool;

        /**
         * @return array|null
         */
        protected function getKeyFile() : ?array
        {
            return $this->_keyFile;
        }

        /**
         * @return string
         */
        protected function getProjectId() : string
        {
            return $this->_projectId;
        }

        /**
         * @return string
         */
        protected function getTopicName() : string
        {
            return $this->_topicName;
        }

        /**
         * @return bool
         */
        abstract public function authenticate() : bool;

        /**
         * @return object
         */
        abstract protected function getMessageQueueClient() : ?object;
    }
}