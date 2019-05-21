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

/* ==================== Payment Processor Exception Classes Start ==================== */

/**
 * Exception class for all Payment Processor exceptions
 */
class PaymentProcessorException extends mPointException {}

/* ==================== Payment Processor Exception Classes End ==================== */


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
        try {
            if (empty($this->aConnInfo) === true) {
                $this->_objPSP = Callback::producePSP($oDB, $oTxt, $oTI, $aConnInfo, $this->_objPSPConfig);
            } else if (class_exists($sPSPClassName) === true && empty($this->aConnInfo) === false) {
                $this->_objPSP = new $sPSPClassName($oDB, $oTxt, $oTI, $this->aConnInfo);
            } else {
                throw new PaymentProcessorException("Could not construct PSP object for the given PSPID ".$iPSPID );
            }
        }
        catch (PaymentProcessorException $e)
        {
            trigger_error($e->getMessage(), E_USER_ERROR);
            throw $e;
        }
        catch (CallbackException $e)
        {
            throw new mPointException($e->getMessage() );
        }
    }

    public function getPSPConfig() { return $this->_objPSPConfig; }
    public function getPSPInfo()  { return $this->_objPSP; }

    public static function produceConfig(RDB $oDB, TranslateText $oTxt, TxnInfo $oTI, $iPSPID, $aConnInfo = array())
    {
        return new PaymentProcessor($oDB, $oTxt, $oTI, $iPSPID, $aConnInfo);
    }

    public function initialize($cardTypeId=-1, $cardToken='', $billingAddress = NULL, $clientInfo = NULL)
    {
        return $this->_objPSP->initialize($this->_objPSPConfig,$this->_objPSP->getTxnInfo()->getAccountID(), false, $cardTypeId, $cardToken, $billingAddress, $clientInfo);
    }

    public function authorize($obj_Elem, $obj_ClientInfo= null)
    {
        return $this->_objPSP->authorize($this->_objPSPConfig, $obj_Elem, $obj_ClientInfo);
    }

    public function authenticate($obj_Elem)
    {
            return $this->_objPSP->authenticate( $obj_Elem);
    }

    public function tokenize($aConnInfo, $obj_Elem)
    {
        return $this->_objPSP->tokenize($aConnInfo, $this->_objPSPConfig, $obj_Elem);
    }

    public function processCallback($obj_Elem)
    {
        return $this->_objPSP->processCallback($this->_objPSPConfig, $obj_Elem);
    }

    public function refund($iAmount=-1)
    {
        return $this->_objPSP->refund($iAmount);
    }

    public function getPaymentData($obj_Elem, $mode = null)
    {
        if ($mode != null) {
            $paymentData = $this->_objPSP->getPaymentData($this->_objPSPConfig, $obj_Elem, $mode);
        } else {
            $paymentData = $this->_objPSP->getPaymentData($this->_objPSPConfig, $obj_Elem);
        }
        return $paymentData;
    }

    public function getPSPConfigForRoute($obj_Elem, $b)
    {
        return $this->_objPSP->getPSPConfigForRoute($obj_Elem, $b);
    }

    public function status()
    {
        return $this->_objPSP->status();
    }
    public function getPaymentMethods()
    {
        return $this->_objPSP->getPaymentMethods($this->_objPSPConfig);
    }
}
