<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anna Lagad
 * Copyright: Cellpoint Digital
 * Link: http://www.cellpointdigital.com
 * Project: server
 * File Name:payment_method.php
 */

class PaymentMethod extends Card
{
    private $_obj_TxnInfo = '';
    private $_iProcessorType = '';
    private int $_iPSPId;
    private int $_iStateId;
    private bool $_bPreferred;
    private int $_iInstallment;
    private ?int $_iCaptureType;
    private ?bool $_bCvcMandatory;
    private ?string $_iWalletId;
    private ?bool $_bDccEnabled;
    private int $_iWidth = 180; // Default logo width
    private int $_iHeight = 115; // Default logo height

    /***
     * PaymentMethod constructor.
     *
     * @param TxnInfo $oTI              Reference to the Data object with the Transaction Information
     * @param RDB $oDB                  Reference to the Database
     * @param array $prefixes           List of bin range for the card
     * @param array $aCard              Hold card configuration details
     * @param string $processorType     Hold psp type id
     * @param int $pspId                Hold psp id
     * @param int $stateId              Hold state id
     * @param bool $preferred           Hold Preferred
     * @param int $installment          Hold Installment value
     * @param int|null $captureType     Hold Capture type
     * @param bool|null $cvcMandatory   Hold CVV mandatory
     * @param string|null $walletId     Hold Wallet ID
     * @param bool|null $dccEnabled     Hold DCC enabled
     */
    public function __construct(TxnInfo $oTI, RDB $oDB, array $prefixes, array $aCard, string $processorType,int $pspId, int $stateId, bool $preferred, int $installment, ?int $captureType, ?bool $cvcMandatory, ?string $walletId, ?bool $dccEnabled)
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

    public function getProcessorType():string { return $this->_iProcessorType; }
    public function getPSPId(): int { return $this->_iPSPId; }
    public function getLogoWidth(): int  { return $this->_iWidth; }
    public function getLogoHeight(): int { return $this->_iHeight; }
    public function getstateId(): int { return $this->_iStateId; }
    public function getPreferred(): ?bool { return $this->_bPreferred; }
    public function getInstallment(): int { return $this->_iInstallment; }
    public function getCaptureType(): ?int { return $this->_iCaptureType; }
    public function getCvcMandatory(): ?bool { return $this->_bCvcMandatory; }
    public function getWalletId(): ?string { return $this->_iWalletId; }
    public function getDccEnabled(): ?bool { return $this->_bDccEnabled; }

    public function toXML(): string
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
     * @param 	array $aPaymentMethodsConfig 	     Payment Methods Config
     * @return 	?array                       Instance of static Routes Configuration
     */
    public static function produceConfig(RDB $oDB, TranslateText $oTxt, TxnInfo $oTI, $aPaymentMethodsConfig,int $fraudDettectedForPMType = -1): ?array
    {
        $aObj_Configurations = array();
        $cardIds = array_keys($aPaymentMethodsConfig);

        $sql = "SELECT DISTINCT ON (C.id) C.position, C.id, C.name, C.minlength, C.maxlength, C.cvclength, C.paymenttype, C.paymenttype AS processortype, '-1' AS pspid,
                false AS preferred, 0 AS installment, SRLC.cvcmandatory, '' AS walletid, CA.dccenabled
				FROM System" . sSCHEMA_POSTFIX . ".Card_Tbl C
				INNER JOIN System" . sSCHEMA_POSTFIX . ".CardPricing_Tbl CP ON C.id = CP.cardid
				INNER JOIN System" . sSCHEMA_POSTFIX . ".PricePoint_Tbl PP ON CP.pricepointid = PP.id AND PP.currencyid = " . $oTI->getCurrencyConfig()->getID() . " AND PP.amount = -1 AND PP.enabled = '1'
				LEFT OUTER JOIN Client".sSCHEMA_POSTFIX.".CardAccess_Tbl CA ON CA.cardid = C.id AND CA.clientid = ".$oTI->getClientConfig()->getID()."
				LEFT OUTER JOIN Client" . sSCHEMA_POSTFIX . ".StaticRouteLevelConfiguration SRLC ON SRLC.cardaccessid = CA.id AND SRLC.enabled = '1'
				WHERE C.id IN (" . implode(',', $cardIds) . ")
				AND C.enabled = '1'";

        $result = $oDB->getAllNames($sql);

        if (is_array($result) === true && count($result) > 0)
        {
            foreach ($result as $aRS) {
                // Set processor type and stateid given by CRS into resultset
                $aCardConfig = $aPaymentMethodsConfig[$aRS['ID']];
                $aRS['STATEID'] = $aCardConfig['state_id'];
                if($aRS['PAYMENTTYPE'] == $fraudDettectedForPMType)
                {
                    $aRS['STATEID'] = Constants::iCARD_DISABLED;
                }
                $preference = $aCardConfig['preference'];

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

                    $aObj_Configurations[$preference] = new PaymentMethod($oTI, $oDB, $aPrefixes, $aRS, $aRS['PROCESSORTYPE'], $aRS['PSPID'], $aRS['STATEID'], $aRS['PREFERRED'], $aRS['INSTALLMENT'], $aRS['CAPTURE_TYPE'], $aRS['CVCMANDATORY'], $aRS['WALLETID'], $aRS['DCCENABLED']);
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
    public static function produceConfigurations(RDB &$oDB, TranslateText &$oTxt, TxnInfo &$oTI, $aObj_PaymentMethods,int $fraudDettectedForPMType = -1 )
    {
        $paymentMethods = $aObj_PaymentMethods->payment_methods->payment_method;
        $aPaymentMethodsConfig = array();
        for ($i = 0, $iMax = count($paymentMethods); $i < $iMax; $i++) {

            $aPaymentMethodsConfig[$paymentMethods[$i]->id] = array(
                'state_id' => $paymentMethods[$i]->state_id,
                'preference' => $paymentMethods[$i]->preference
            );
        }
        return self::produceConfig($oDB, $oTxt, $oTI, $aPaymentMethodsConfig,$fraudDettectedForPMType);
    }

}

?>