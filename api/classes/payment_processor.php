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
class PaymentProcessorInitializeException extends mPointException
{
    private $subCode;
    function __construct($message = "", $code = 0, Throwable $previous = null, $subCode=0)
    {
        parent::__construct($message, $code, $previous);
        $this->subCode = $subCode;
    }

    function getSubcode()
    {
        return $this->subCode;
    }
}
/* ==================== Payment Processor Exception Classes End ==================== */


class PaymentProcessor
{
    private $_objPSPConfig;
    private $_objPSP;
    private $aConnInfo = array();
    private $aWalletCardScemes = array();

    private function _setConnInfo($aConnInfo, $iPSPID)
    {
        if(empty($aConnInfo[$iPSPID]) === false )
        {
            $this->aConnInfo = $aConnInfo[$iPSPID];
        }
    }

    public function __construct(RDB $oDB, api\classes\core\TranslateText $oTxt, TxnInfo $oTI, $iPSPID, $aConnInfo,$obj_ClientInfo=null)
    {
        $this->_setConnInfo($aConnInfo, $iPSPID);

        $this->_objPSPConfig = General::producePSPConfigObject($oDB, $oTI, $iPSPID );

        try {
            if (empty($this->aConnInfo) === true) {
                $this->_objPSP = Callback::producePSP($oDB, $oTxt, $oTI, $aConnInfo, $this->_objPSPConfig,$obj_ClientInfo);
            } else if (empty($this->aConnInfo) === false) {
                $this->_objPSP = new \api\classes\GenericPSP($oDB, $oTxt, $oTI, $this->aConnInfo, $this->_objPSPConfig, $obj_ClientInfo, $iPSPID);
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

    public static function produceConfig(RDB $oDB, api\classes\core\TranslateText $oTxt, TxnInfo $oTI, $iPSPID, $aConnInfo,$obj_ClientInfo=null)
    {
        return new PaymentProcessor($oDB, $oTxt, $oTI, $iPSPID, $aConnInfo,$obj_ClientInfo);
    }

    public function initialize($cardTypeId=-1, $cardToken='', $billingAddress = NULL, $clientInfo = NULL, $storeCard = FALSE, $authToken = NULL, $cardName='')
    {
        return $this->_objPSP->initialize($this->_objPSPConfig,$this->_objPSP->getTxnInfo()->getAccountID(), $storeCard, $cardTypeId, $cardToken, $billingAddress, $clientInfo, $authToken, $cardName, $this->getWalletCardSchemes());
    }

    public function authorize($obj_Elem, $obj_ClientInfo= null)
    {
        return $this->_objPSP->authorize($this->_objPSPConfig, $obj_Elem, $obj_ClientInfo);
    }

    public function authenticate($xml,$obj_Card, $obj_ClientInfo= null)
    {
            return $this->_objPSP->authenticate( $xml,$obj_Card, $obj_ClientInfo);
    }

    public function tokenize($aConnInfo, $obj_Elem)
    {
        return $this->_objPSP->tokenize($aConnInfo, $this->_objPSPConfig, $obj_Elem);
    }

    public function fraudCheck($obj_Elem)
    {
        return $this->_objPSP->fraudCheck($obj_Elem);
    }

    public function processCallback($obj_Elem)
    {
        return $this->_objPSP->processCallback($this->_objPSPConfig, $obj_Elem);
    }

    public function refund($iAmount=-1)
    {
        return $this->_objPSP->refund($iAmount);
    }

    public function capture($iAmount=-1)
    {
        return $this->_objPSP->capture($iAmount);
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

    public function cancel()
    {
        return $this->_objPSP->cancel();
    }

    public function getPaymentMethods()
    {
        return $this->_objPSP->getPaymentMethods($this->_objPSPConfig);
    }

    public function notifyClient(int $iStateId, array $vars, ?SurePayConfig $obj_SurePay=null)
    {
        return $this->_objPSP->notifyClient($iStateId,$vars,$obj_SurePay);
    }

    /**
     * Save Wallet Card Schemes
     * @param $aCardSchemes
     * @return void
     */
    public function setWalletCardSchemes(array $aCardSchemes = array())
    {
        $this->aWalletCardScemes = $aCardSchemes;
    }

    /**
     * Retrieve Wallet Card schemes
     * @return array
     */
    public function getWalletCardSchemes() : array
    {
        return $this->aWalletCardScemes;
    }

}
