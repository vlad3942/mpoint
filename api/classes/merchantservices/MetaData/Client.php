<?php
namespace api\classes\merchantservices\MetaData;

/**
   * Client
   * 
   * 
   * @package    Mechantservices
   * @subpackage Client Class
   * @author     Vikas Gupta <vikas.gupta@cellpointmobile.com>
 */
class Client
{

    /**
     * Id
     *
     * @var integer
     */
    private int $_Id;

    /**
     * Name
     *
     * @var string
     */
    private string $_name;

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
     * @return int
     */
    public function getId(): int
    {
        return $this->_Id;
    }

    /**
     * @param int $Id
     *
     * @return Client
     */
    public function setId(int $Id): Client
    {
        $this->_Id = $Id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->_name;
    }

    /**
     * @param string $name
     *
     * @return Client
     */
    public function setName(string $name): Client
    {
        $this->_name = $name;
        return $this;
    }

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
        $xml  = sprintf("<id>%s</id>",$this->getId());
        $xml .= sprintf("<name>%s</name>",$this->getName());
        $xml .= sprintf("<salt>%s</salt>",$this->getSalt());
        $xml .= sprintf("<max_amount>%s</max_amount>",$this->getMaxAmount());
        $xml .= sprintf("<country_id>%s</country_id>",$this->getCountryId());
        $xml .= sprintf("<email_notification>%s</email_notification>",$this->isEmailNotification());
        $xml .= sprintf("<sms_notification>%s</sms_notification>",$this->isSmsNotification);
        // TBD
        $xml .= sprintf("<timezone>%s</timezone>", '+05:30');
        $xml .= sprintf("<client_domain>%s</client_domain>",'1');
        return $xml;
    }
}

