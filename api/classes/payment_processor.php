<?php
/**
 * Abstraction Class that creates the basic functions for abstracting the functionality of PSP authorizations
 *
 * @author Rohit Malhotra
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage Visa Checkout
 * @version 1.00
 */
class PaymentProcessor
{
    private $_objPSPConfig;
    private $_objPSP;
    private $aConnInfo = array();

    private function _setConnInfo($aConnInfo, $iPSPID)
    {
        if(empty($aConnInfo[$iPSPID]) === false )
        {
            $this->aConnInfo = $aConnInfo[$iPSPID];
        }
    }

    public function __construct(RDB $oDB, TranslateText $oTxt, TxnInfo $oTI, $iPSPID, $aConnInfo)
    {
        $this->_objPSPConfig = PSPConfig::produceConfig($oDB, $oTI->getClientConfig()->getID(), $oTI->getClientConfig()->getAccountConfig()->getID(), $iPSPID);
        $sPSPClassName = $this->_objPSPConfig->getName();
        $this->_setConnInfo($aConnInfo, $iPSPID);
        if (empty($this->aConnInfo) === true)
        {
            $this->_objPSP = Callback::producePSP($oDB, $oTxt, $oTI, $aConnInfo, $this->_objPSPConfig);
        }
        else
        {
            $this->_objPSP = new $sPSPClassName($oDB, $oTxt, $oTI, $this->aConnInfo);
        }
    }

    public static function produceConfig(RDB $oDB, TranslateText $oTxt, TxnInfo $oTI, $iPSPID, $aConnInfo)
    {
        return new PaymentProcessor($oDB, $oTxt, $oTI, $iPSPID, $aConnInfo);
    }

    public function authorize($obj_Elem)
    {
        return $this->_objPSP->authorize($this->_objPSPConfig, $obj_Elem);
    }

    public function tokenize($obj_Elem)
    {
        return $this->_objPSP->tokenize($this->_objPSPConfig, $obj_Elem);
    }

    public function processCallback($obj_Elem)
    {
        return $this->_objPSP->processCallback($this->_objPSPConfig, $obj_Elem);
    }
}
