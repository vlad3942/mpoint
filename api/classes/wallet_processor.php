<?php
/**
 * Abstraction Class that creates the basic functions for abstracting the functionality of PSP authorizations
 *
 * @author Arvind Halgekar
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @version 1.00
 */
class WalletProcessor
{
    private $_objPSPConfig;
    private $_objPSP;
    private $aConnInfo;
    public static $aWalletConstants = array(Constants::iAPPLE_PAY => Constants::iAPPLE_PAY_PSP,
        Constants::iVISA_CHECKOUT_WALLET => Constants::iVISA_CHECKOUT_PSP,
        Constants::iMASTER_PASS_WALLET => Constants::iMASTER_PASS_PSP,
        Constants::iAMEX_EXPRESS_CHECKOUT_WALLET => Constants::iAMEX_EXPRESS_CHECKOUT_PSP,
        Constants::iANDROID_PAY_WALLET => Constants::iANDROID_PAY_PSP,
        Constants::iGOOGLE_PAY_WALLET => Constants::iGOOGLE_PAY_PSP,
        Constants::iMVAULT_PSP => Constants::iMVAULT_PSP);

    public static $aWalletConnInfo = array(Constants::iAPPLE_PAY => 'apple-pay',
        Constants::iVISA_CHECKOUT_WALLET => Constants::iVISA_CHECKOUT_PSP,
        Constants::iMASTER_PASS_WALLET => 'masterpass',
        Constants::iAMEX_EXPRESS_CHECKOUT_WALLET => 'amex-express-checkout',
        Constants::iANDROID_PAY_WALLET => 'android-pay',
        Constants::iGOOGLE_PAY_WALLET => 'google-pay',
        Constants::iMVAULT_PSP => 'mvault');

    private function _setConnInfo($aConnInfo)
    {
        if(empty($aConnInfo) === false )
        {
            $this->aConnInfo = $aConnInfo;
        }
        else { throw new mPointException("Connection Info not found for the wallet :". $aConnInfo); }
    }

    public function __construct(RDB $oDB, TranslateText $oTxt, TxnInfo $oTI, $iTypeId, $aConnInfo)
    {
        $this->_objPSPConfig = PSPConfig::produceConfig($oDB, $oTI->getClientConfig()->getID(), $oTI->getClientConfig()->getAccountConfig()->getID(), self::$aWalletConstants[$iTypeId]);
        $this->_setConnInfo($aConnInfo[self::$aWalletConnInfo[$iTypeId]]);
        $sPSPClassName = $this->_objPSPConfig->getName();
        $this->_objPSP = new $sPSPClassName($oDB, $oTxt, $oTI, $this->aConnInfo);
    }

    public static function produceConfig(RDB $oDB, TranslateText $oTxt, TxnInfo $oTI, $iTypeId, $aConnInfo, $card_psp_id = NULL)
    {
        if (empty($card_psp_id) === false && $card_psp_id == Constants::iMVAULT_PSP) {
            $iTypeId = $card_psp_id;
        }
        if (empty(self::$aWalletConstants[$iTypeId]) === false) {
            return new WalletProcessor($oDB, $oTxt, $oTI, $iTypeId, $aConnInfo);
        } else {
            return false;
        }
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
}
