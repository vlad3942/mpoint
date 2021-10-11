<?php
namespace api\classes\merchantservices\MetaData;

use api\classes\merchantservices\commons\BaseInfo;

/**
   * Client
   * 
   * 
   * @package    Mechantservices
   * @subpackage Client Class
   * @author     Vikas Gupta <vikas.gupta@cellpointmobile.com>
 */
class Client extends BaseInfo
{

    /**
     * Username
     *
     * @var string
     */
    private string $_username;
    /**
     * Salt
     *
     * @var string
     */
    private string $_salt;

    /**
     * Max Amount
     *
     * @var int
     */
    private int $_maxAmount;

    /**
     * Country ID
     *
     * @var int
     */
    private int $_countryId;

    /**
     * Email Notification
     * @var bool
     */
    private bool $_emailNotification;

    /**
     * SMS Notification
     * @var bool
     */
    private bool $_smsNotification;

    /**
     * @return string
     */
    public function getSalt(): string
    {
        return $this->_salt;
    }

    /**
     * @param string $salt
     *
     * @return Client
     */
    public function setSalt(string $salt): Client
    {
        $this->_salt = $salt;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxAmount(): int
    {
        return $this->_maxAmount;
    }

    /**
     * @param int $maxAmount
     *
     * @return Client
     */
    public function setMaxAmount(int $maxAmount): Client
    {
        $this->_maxAmount = $maxAmount;
        return $this;
    }

    /**
     * @return int
     */
    public function getCountryId(): int
    {
        return $this->_countryId;
    }

    /**
     * @param int $countryId
     *
     * @return Client
     */
    public function setCountryId(int $countryId): Client
    {
        $this->_countryId = $countryId;
        return $this;
    }

    /**
     * @return bool
     */
    public function isEmailNotification(): bool
    {
        return $this->_emailNotification;
    }

    /**
     * @param bool $emailNotification
     *
     * @return Client
     */
    public function setEmailNotification(bool $emailNotification): Client
    {
        $this->_emailNotification = (bool)$emailNotification;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSmsNotification(): bool
    {
        return $this->_smsNotification;
    }

    /**
     * @param bool $smsNotification
     *
     * @return Client
     */
    public function setSmsNotification(bool $smsNotification): Client
    {
        $this->_smsNotification = $smsNotification;
        return $this;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->_username;
    }

    /**
     * @param string $username
     *
     * @return Client
     */
    public function setUsername(string $username): Client
    {
        $this->_username = $username;
        return $this;
    }

    // Return in String::XML
    public function toXML() : string
    {
        $xml = parent::toXML();
        $xml .= sprintf("<salt>%s</salt>",$this->getSalt());
        $xml .= sprintf("<max_amount>%s</max_amount>",$this->getMaxAmount());
        $xml .= sprintf("<country_id>%s</country_id>",$this->getCountryId());
        $xml .= sprintf("<email_notification>%s</email_notification>",\General::bool2xml($this->isEmailNotification()));
        $xml .= sprintf("<sms_notification>%s</sms_notification>", \General::bool2xml($this->isSmsNotification()));
        // TBD
        $xml .= sprintf("<timezone>%s</timezone>", '+05:30');
        $xml .= sprintf("<client_domain>%s</client_domain>",'1');
        return $xml;
    }

    /**
     * Static Method to assign property to Class member variable.
     *
     * @param array $client
     *
     * @return \api\classes\merchantservices\MetaData\Client
     */
    public static function produceFromResultSet(array $client): Client
    {
        $ClientInfo = new Client();
        $ClientInfo->setId($client["ID"]);
        $ClientInfo->setName($client['NAME']);
        $ClientInfo->setUsername($client['USERNAME']);
        $ClientInfo->setSalt($client['SALT']);
        $ClientInfo->setMaxAmount($client['MAXAMOUNT']);
        $ClientInfo->setCountryId($client['COUNTRYID']);
        $ClientInfo->setEmailNotification($client['EMAILRCPT']);
        $ClientInfo->setSmsNotification($client['SMSRCPT']);
        return $ClientInfo;
    }

}