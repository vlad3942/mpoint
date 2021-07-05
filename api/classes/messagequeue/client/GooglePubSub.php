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

        private ?object $_messageQueueClient = NULL;

        /**
         * @param string $data
         * @param array|null $filter
         * @return bool
         */
        public function publish(string $data, array $filter = null): bool
        {
            $topic = $this->getMessageQueueClient()->topic($this->getTopicName());
            $message = ['data' => $data];
            if($filter !== null)
            {
                $message['attributes'] = $filter;
            }
            $topic->publish($message);
            return TRUE;
        }

        /**
         * @return object
         */
        protected function getMessageQueueClient(): ?object
        {
            return $this->_messageQueueClient;
        }

        /**
         * @return bool
         */
        public function authenticate() : bool
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