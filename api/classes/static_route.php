<?php

class StaticRoute extends Card
{
    private $_obj_TxnInfo = '';
    private $_iProcessorType = '';
    private $_iPSPId;
    private $_iStateId;
    private $_bPreferred;
    private $_iInstallment;
    private $_iCaptureType;
    private $_bCvcMandatory;
    private $_iWalletId;
    private $_bDccEnabled;
    private $_iWidth = 180; // Default logo width
    private $_iHeight = 115; // Default logo height

    /**
     * Default Constructor
     *
     * @param	TxnInfo $oTI 		Reference to the Data object with the Transaction Information
     * @param	CardPrefixConfig    $prefixes 	List of bin range for the card
     * @param 	array $aCard 	    Hold card configuration details
     * @param   integer $processorType    Unique psp type id
     */
    public function __construct(TxnInfo $oTI, RDB $oDB, array $prefixes, array $aCard, $processorType, $pspId, $stateId, $preferred, $installment, $captureType, $cvcMandatory, $walletId, $dccEnabled)
    {
        parent::__construct($aCard,$oDB, $prefixes);
        $this->_obj_TxnInfo = $oTI;
        $this->_iProcessorType = $processorType;
        $this->_iPSPId = $pspId;
        $this->_iStateId = $stateId;
        $this->_bPreferred = $preferred;
        $this->_iInstallment = $installment;
        $this->_iCaptureType = $captureType;
        $this->_bCvcMandatory = $cvcMandatory;
        $this->_iWalletId = $walletId;
        $this->_bDccEnabled = $dccEnabled;
    }

    public function getProcessorType() { return $this->_iProcessorType; }
    public function getPSPId() { return $this->_iPSPId; }
    public function getLogoWidth() { return $this->_iWidth; }
    public function getLogoHeight() { return $this->_iHeight; }
    public function getstateId() { return $this->_iStateId; }
    public function getPreferred() { return $this->_bPreferred; }
    public function getInstallment() { return $this->_iInstallment; }
    public function getCaptureType() { return $this->_iCaptureType; }
    public function getCvcMandatory() { return $this->_bCvcMandatory; }
    public function getWalletId() { return $this->_iWalletId; }
    public function getDccEnabled() { return $this->_bDccEnabled; }

    public function toXML()
    {
        $xml = '<item id="' . $this->getCardTypeId() . '" type-id="' . $this->getCardTypeId() . '" pspid="' . $this->getPSPId() . '" min-length="' . $this->getMinCardLength() . '" max-length="' . $this->getMaxCardLength() . '" cvc-length="' . $this->getCvcLength() . '" state-id="' . $this->getstateId() . '" payment-type="' . $this->getPaymentType() . '"' . ' preferred="' . General::bool2xml($this->getPreferred()) . '"' . ' enabled = "' . General::bool2xml(true) . '"' . ' processor-type = "' . $this->getProcessorType() . '" installment = "' . $this->getInstallment() . '" cvcmandatory = "' . General::bool2xml($this->getCvcMandatory()) . '" walletid = "' . $this->getWalletId() . '" dcc="' . General::bool2xml($this->getDccEnabled()) . '" >';
        $xml .= '<name>' . htmlspecialchars($this->getCardName(), ENT_NOQUOTES) . '</name>';
        $xml .= '<logo-width>' . $this->getLogoWidth() . '</logo-width>';
        $xml .= '<logo-height>' . $this->getLogoHeight() . '</logo-height>';
        $xml .= '<currency>' . $this->_obj_TxnInfo->getCurrencyConfig()->getID() . '</currency>';
        $xml .= '<capture_type>' . $this->getCaptureType() . '</capture_type>';
        if (count($this->getBinRange()) > 0)
        {
            $xml .= '<prefixes>';
            foreach ($this->getBinRange() as $obj_Prefix)
            {
                if(($obj_Prefix instanceof CardPrefixConfig) === true )
                {
                    $xml .= $obj_Prefix->toXML();
                }
            }
            $xml .= '</prefixes>';
        }
        else { $xml .= '<prefixes />'; }
        $xml .= '</item>';

        return $xml;
    }

    /**
     * Produces a new instance of a static Routes Configuration.
     *
     * @param 	RDB $oDB 		             Reference to the Database Object that holds the active connection to the mPoint Database
     * @param	TranslateText $oTxt 	     Text Translation Object for translating any text into a specific language
     * @param 	TxnInfo $oTI 			     Data object with the Transaction Information
     * @param 	integer $cardId 	         Unique id for the  card
     * @param   integer $pspType             Unique psp type id
     * @return 	object                       Instance of static Routes Configuration
     */
    public static function produceConfig(RDB &$oDB, TranslateText &$oTxt, TxnInfo &$oTI, $aPaymentMethodsConfig)
    {
        $aObj_Configurations = array();
        $cardIds = array_keys($aPaymentMethodsConfig);

        $sql = "SELECT DISTINCT ON (C.id) C.position, C.id, C.name, C.minlength, C.maxlength, C.cvclength, C.paymenttype, CA.psp_type AS processortype, CA.pspid,
                CA.stateid, CA.preferred, CA.installment, CA.capture_type, SRLC.cvcmandatory, CA.walletid, CA.dccEnabled
				FROM System" . sSCHEMA_POSTFIX . ".Card_Tbl C
				INNER JOIN Client".sSCHEMA_POSTFIX.".CardAccess_Tbl CA ON C.id = CA.cardid AND CA.clientid = ".$oTI->getClientConfig()->getID()."
				INNER JOIN System" . sSCHEMA_POSTFIX . ".CardPricing_Tbl CP ON C.id = CP.cardid
				INNER JOIN System" . sSCHEMA_POSTFIX . ".PricePoint_Tbl PP ON CP.pricepointid = PP.id AND PP.currencyid = " . $oTI->getCurrencyConfig()->getID() . " AND PP.amount = -1 AND PP.enabled = '1'
				LEFT OUTER JOIN Client" . sSCHEMA_POSTFIX . ".StaticRouteLevelConfiguration SRLC ON SRLC.cardaccessid = CA.id AND SRLC.enabled = '1'
				WHERE C.id IN (" . implode(',', $cardIds) . ")
				AND CA.psp_type NOT IN (".Constants::iPROCESSOR_TYPE_TOKENIZATION.",".Constants::iPROCESSOR_TYPE_PRE_FRAUD_GATEWAY. ",".Constants::iPROCESSOR_TYPE_POST_FRAUD_GATEWAY.")
                AND CA.walletid IS NULL
				AND C.enabled = '1'";

        $result = $oDB->getAllNames($sql);

        if (is_array($result) === true && count($result) > 0)
        {
            foreach ($result as $aRS) {
                // Set processor type and stateid given by CRS into resultset
                $aCardConfig = $aPaymentMethodsConfig[$aRS['ID']];
                $aRS['PROCESSORTYPE'] = $aCardConfig['psp_type'];
                $aRS['STATEID'] = $aCardConfig['state_id'];

                // Transaction instantiated via SMS or "Card" is NOT Premium SMS
                if ($oTI->getGoMobileID() > -1 || $aRS['ID'] != Constants::iPREMIUM_SMS) {

                    if ($aRS['ID'] == 11) {
                        if (($oTI->getClientConfig()->getStoreCard() & 1) == 1) {
                            $aRS['NAME'] = $oTxt->_("Stored Cards");
                        } else {
                            $aRS['NAME'] = str_replace("{CLIENT}", $oTI->getClientConfig()->getName(), $oTxt->_("My Account"));
                        }
                    }

                    $aPrefixes = CardPrefixConfig::produceConfigurations($oDB, $aRS['ID']);

                    $aObj_Configurations[] = new StaticRoute($oTI, $oDB, $aPrefixes, $aRS, $aRS['PROCESSORTYPE'], $aRS['PSPID'], $aRS['STATEID'], $aRS['PREFERRED'], $aRS['INSTALLMENT'], $aRS['CAPTURE_TYPE'], $aRS['CVCMANDATORY'], $aRS['WALLETID'], $aRS['DCCENABLED']);
                }
            }
        }
        return $aObj_Configurations;
    }

    /**
     * Produces a static Routes Configuration object.
     *
     * @param 	RDB $oDB 		             Reference to the Database Object that holds the active connection to the mPoint Database
     * @param	TranslateText $oTxt 	     Text Translation Object for translating any text into a specific language
     * @param 	TxnInfo $oTI 			     Data object with the Transaction Information
     * @param 	SimpleDOMElement $aObj_XML 	 List of payment methods
     * @return 	array $aObj_Configurations   Static Routes Configuration object
     */
    public static function produceConfigurations(RDB &$oDB, TranslateText &$oTxt, TxnInfo &$oTI, $aObj_PaymentMethods)
    {
        $paymentMethods = $aObj_PaymentMethods->payment_methods->payment_method;
        $aObj_Configurations = array();
        for ($i = 0; $i < count($paymentMethods); $i++) {
            $aObj_Configurations[] = self::produceConfig($oDB, $oTxt, $oTI, $paymentMethods[$i]->id, $paymentMethods[$i]->psp_type);
        }
        return $aObj_Configurations;
    }

}