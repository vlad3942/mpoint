<?php
/**
 * Created by IntelliJ IDEA.
 * User: Chaitenya Yadav
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:SecurityHash.php
 */

namespace api\classes;

class SecurityHash
{

    public int $_iClientID;    
    public string $_sSalt;
    public string $_hashString;
    
    /**
     * SecurityHash constructor.
     *
     * @param int $clientId
     * @param string $salt
     */
    public function __construct(int $clientId, string $salt)
    {
        $this->_iClientID = (integer) $clientId;
        $this->_sSalt = $salt;
    }

    public function generate512Hash()
	{
        return hash('sha512', $this->_hashString);
    }
}
