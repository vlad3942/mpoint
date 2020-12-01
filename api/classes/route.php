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
    /**
     * Handles the active database connection
     * @var RDB
     */
    private $objDB;

    /**
     * Data object with the Transaction Information
     * @var TxnInfo
     */
    private $_obj_TxnInfo;

    /**
     * Hold Unique client ID
     * @var integer
     */
    private $_iClientId;

    /**
     * Default Constructor
     *
     * @param	RDB $oDB			Reference to the Database Object that holds the active connection to the mPoint Database
     * @param	TranslateText $oDB 	Text Translation Object for translating any text into a specific language
     * @param   integer $clientId   Hold unique client ID
     */
    public function __construct(RDB $oDB, TxnInfo &$oTI, $clientId)
    {
        $this->objDB = $oDB;
        $this->_obj_TxnInfo = $oTI;
        $this->_iClientId = $clientId;
    }

    /**
     * Function to get route config ID
     * @param integer $providerId
     * @return int|mixed
     */
    public function getRouteID($providerId = null)
    {
        if(empty($this->_iClientId) === false && empty($providerId) === false)
        {
            $sql = "SELECT RC.id  FROM Client".sSCHEMA_POSTFIX.".Route_Tbl R
                    INNER JOIN Client".sSCHEMA_POSTFIX.".Routeconfig_Tbl RC  ON RC.routeid = R.id
                    WHERE R.providerid = ".$providerId." 
                    AND R.clientid = ". $this->_iClientId ."
                    AND ( RC.countryid = ". $this->_obj_TxnInfo->getCountryConfig()->getID()." OR RC.countryid IS NULL )
                    AND ( RC.currencyid = ".$this->_obj_TxnInfo->getCurrencyConfig()->getID()." OR RC.currencyid IS NULL)";

            $result = $this->objDB->getName($sql);

            if (is_array($result)) {
                return $result['ID'];
            }
        }
        return -1;
    }

}