<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anna Lagad
 * Copyright: Cellpoint Digital
 * Link: http://www.cellpointdigital.com
 * Project: server
 * File Name:route.php
 */


class Route
{
    private $objDB;
    private $_iClientId;

    public function __construct(RDB $oDB, $clientId)
    {
        $this->objDB = $oDB;
        $this->_iClientId = $clientId;
    }

    public function getProviderID($routeId = null)
    {
        if(empty($this->_iClientId) === false && empty($routeId) === false)
        {
            $sql = "SELECT providerid  FROM Client".sSCHEMA_POSTFIX.".Route_Tbl WHERE id = ".$routeId." and clientid = $this->_iClientId";
            $result =$this->objDB->getName($sql);
            if (is_array($result)) {
                return $result['PROVIDERID'];
            }
        }
        return -1;
    }

    public function getRouteID($providerId = null)
    {
        if(empty($this->_iClientId) === false && empty($providerId) === false)
        {
            $sql = "SELECT id  FROM Client".sSCHEMA_POSTFIX.".Route_Tbl WHERE providerid = ".$providerId." and clientid = $this->_iClientId";
            $result =$this->objDB->getName($sql);
            if (is_array($result)) {
                return $result['ID'];
            }
        }
        return -1;
    }

}