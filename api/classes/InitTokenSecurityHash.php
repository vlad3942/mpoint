<?php
/**
 * Created by IntelliJ IDEA.
 * User: Chaitenya Yadav
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:InitTokenSecurityHash.php
 */

namespace api\classes;

class InitTokenSecurityHash extends SecurityHash
{
    private string $_sUsername;
    private string $_sPassword;
    private string $_sAcceptUrl;
    
    /**
     * InitTokenSecurityHash constructor.
     *
     * @param int $clientId
     * @param string $nonce
     * @param string $username
     * @param string $password
     */
    public function __construct(int $clientId, string $nonce, string $username, string $password)
    {
        parent::__construct($clientId, $nonce);
        $this->_sUsername = $username;
        $this->_sPassword = $password;
    }

    /**
     * @param string $sAcceptUrl
     */
    public function setAcceptUrl($sAcceptUrl)
    {
        $this->_sAcceptUrl = trim($sAcceptUrl);
    }

    public function generate512Hash()
    {
        $this->_hashString = $this->generateInitToken();
        if($this->_hashString)
            return parent::generate512Hash();
    }

    public function generateInitToken()
	{
        $initToken = $this->_iClientID.$this->_sUsername.$this->_sPassword.$this->_sSalt.$this->_sAcceptUrl;
		return $initToken;
    }
}
