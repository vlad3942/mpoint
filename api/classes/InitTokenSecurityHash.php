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

class InitTokenSecurityHash
{
    private string $_sNonce;
    private int $_iClientID;    
    private string $_sUsername;
    private string $_sPassword;
    private string $_sAlgo;
    
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
        $this->_iClientID = (integer) $clientId;
        $this->_sNonce = $nonce;
        $this->_sUsername = $username;
        $this->_sPassword = $password;
        $this->_sAlgo = "sha512";
    }

    public function generateInitToken()
	{
        $initToken = hash($this->_sAlgo, $this->_iClientID.$this->_sUsername.$this->_sPassword.$this->_sNonce);
		return $initToken;
    }
}
