<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:GooglePubSub.php
 */

namespace api\classes\messagequeue\client {

    use Google\Cloud\PubSub\PubSubClient;
    use api\interfaces\messagequeue\MessageQueueProvider;

    class GooglePubSub extends MessageQueueProvider
    {

        /**
         * @var object
         */
        private $_messageQueueClient = NULL;

        /**
         * @param string $message
         *
         * @return bool
         */
        public function publish($message)
        {
            $topic = $this->getMessageQueueClient()->topic($this->getTopicName());
            $topic->publish(['data' => $message]);
            return TRUE;
        }

        /**
         * @return object
         */
        protected function getMessageQueueClient()
        {
            return $this->_messageQueueClient;
        }

        /**
         * @return bool
         */
        public function authenticate()
        {
            $options = ['projectId' => $this->getProjectId()];
            $keyDetails = $this->getKeyFile();
            if(empty($keyDetails) == FALSE)
            {
                $options['keyFile'] = $keyDetails;
            }

            $pubSubClient = new PubSubClient($options);

            $this->_messageQueueClient = $pubSubClient;
            return TRUE;
        }
    }
}