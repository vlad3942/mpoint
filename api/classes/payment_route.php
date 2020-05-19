<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anna Lagad
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:txnroute.php
 */

class PaymentRoute
{

    /**
     * Database Object that holds the active connection to the mPoint Database
     *
     * @var object
     */
    private $_obj_DB;

    /**
     * Hold payment session id
     *
     * @var integer
     */
    private $_iSessionId;

    /**
     * Holds list of alternate payment routes
     *
     * @var array
     */
    private $_aRoutes = [];

    /**
     * Default Constructor
     *
     * @param	RDB $oDB 		Reference to the Database Object that holds the active connection to the mPoint Database
     * @param	TxnInfo $oTI 	Reference to the Data object with the Transaction Information
     */
    public function __construct(RDB &$oDB, $sessionId)
    {
        $this->_obj_DB = $oDB;
        $this->iSessionId = $sessionId;
    }

    /**
     * @return mixed
     */
    private function getDBConn()
    {
        return $this->_obj_DB;
    }

    /**
     * Store alternate payment routes return by CRS
     *
     * @param 	integer $pspid 	         Alternate payment route return by CRS
     * @param   integer $preference      Alternate payment route preference return by CRS
     * @return 	boolean
     */
    public function setAlternateRoute($aAlternateRoutes)
    {
        if(is_array($aAlternateRoutes) === true)
        {
            foreach ($aAlternateRoutes as $aRoute)
            {
                $sql = 'INSERT INTO Log' . sSCHEMA_POSTFIX . '.PaymentRoute_tbl 
                    (sessionid, pspid, preference)                                                         
                VALUES 
                    ($1, $2, $3)';

                $res = $this->getDBConn()->prepare($sql);
                if (is_resource($res) === TRUE) {
                    $aParams = array(
                        $this->iSessionId,
                        $aRoute['id'],
                        $aRoute['preference']
                    );

                    $result = $this->getDBConn()->execute($res, $aParams);

                    if ($result === false) {
                        trigger_error('Fail to store route for session ' . $this->iSessionId, E_USER_ERROR);
                        return false;
                    }
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Get alternate route to process payment
     *
     * @param   integer $preference    Preferred alternate route
     * @return 	integer                Alternate payment route id
     */
    public function getAlternateRoute($preference = Constants::iSECOND_ALTERNATE_ROUTE)
    {
        if(empty($this->_aRoutes)===true) {
            $this->getRoutes();
        }

        return (empty($this->_aRoutes[$preference]) === false)?$this->_aRoutes[$preference]:0;
    }

    /**
     * Get all alternate route for a specific payment session
     * @return List of all preferred routes
     */
    private function getRoutes()
    {
        $sql = 'SELECT pspid, preference FROM Log.' . sSCHEMA_POSTFIX . 'PaymentRoute_tbl WHERE sessionid = $1';
        $res = $this->getDBConn()->prepare($sql);
        if (is_resource($res) === TRUE) {
            $aParams = array(
                $this->iSessionId
            );

            $result = $this->getDBConn()->execute($res, $aParams);

            if (is_resource($result) === TRUE) {
                while ($RS = $this->getDBConn()->fetchName($result)) {
                    $this->_aRoutes[$RS['PREFERENCE']] = $RS['PSPID'];
                }
            }
        }else{
            trigger_error('Fail to fetch route for session : ' . $this->iSessionId, E_USER_ERROR);
            return false;
        }
    }


}