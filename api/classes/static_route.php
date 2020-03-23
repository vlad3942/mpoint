<?php

class StaticRoute extends Card
{
    private $_obj_TxnInfo = '';
    private $_aPrefixes = array();
    private $iProcessorType = '';
    private $iWidth = 180; // Default logo width
    private $iHeight = 115; // Default logo height

    /**
     * Default Constructor
     *
     * @param	TxnInfo $oTI 		Reference to the Data object with the Transaction Information
     * @param	array $prefixes 	List of bin range for the card
     * @param 	array $aCard 	    Hold card configuration details
     * @param   integer $pspType    Unique psp type id
     */
    public function __construct(TxnInfo $oTI, $prefixes, $aCard, $processorType)
    {
        parent::__construct($aCard);
        $this->_obj_TxnInfo = $oTI;
        $this->_aPrefixes = $prefixes;
        $this->iProcessorType = $processorType;
    }

    public function getProcessorType() { return $this->iProcessorType; }
    public function getLogoWidth() { return $this->iWidth; }
    public function getLogoHeight() { return $this->iHeight; }

    public function toXML()
    {
        $xml = '<item id="' . $this->getCardTypeId() . '" type-id="' . $this->getCardTypeId() . '" min-length="' . $this->getMinCardLength() . '" max-length="' . $this->getMaxCardLength() . '" cvc-length="' . $this->getCvcLength() . '" payment-type="' . $this->getPaymentType() . '"' . ' enabled = "' . General::bool2xml(true) . '"' . ' processor-type = "' . $this->getProcessorType() . '" >';
        $xml .= '<name>' . htmlspecialchars($this->getCardName(), ENT_NOQUOTES) . '</name>';
        $xml .= '<logo-width>' . $this->getLogoWidth() . '</logo-width>';
        $xml .= '<logo-height>' . $this->getLogoHeight() . '</logo-height>';
        $xml .= '<currency>' . $this->_obj_TxnInfo->getCurrencyConfig()->getID() . '</currency>';
        if (count($this->_aPrefixes) > 0)
        {
            $xml .= '<prefixes>';
            foreach ($this->_aPrefixes as $obj_Prefix)
            {
                $xml .= $obj_Prefix->toXML();
            }
            $xml .= '</prefixes>';
        }
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
    public static function produceConfig(RDB &$oDB, TranslateText &$oTxt, TxnInfo &$oTI, $cardId, $pspType)
    {
        $sql = "SELECT DISTINCT C.position, C.id, C.name, C.minlength, C.maxlength, C.cvclength, C.paymenttype, $pspType AS processortype
				FROM System" . sSCHEMA_POSTFIX . ".Card_Tbl C
				INNER JOIN System" . sSCHEMA_POSTFIX . ".CardPricing_Tbl CP ON C.id = CP.cardid
				INNER JOIN System" . sSCHEMA_POSTFIX . ".PricePoint_Tbl PP ON CP.pricepointid = PP.id AND PP.currencyid = " . $oTI->getCurrencyConfig()->getID() . " AND PP.amount = -1 AND PP.enabled = '1'
				WHERE C.id = " . $cardId . "
				AND C.enabled = '1'
				ORDER BY C.name ASC";

        $aRS = $oDB->getName($sql);

        if (is_array($aRS) === true && count($aRS) > 0)
        {
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

                return new StaticRoute($oTI, $aPrefixes, $aRS, $aRS['PROCESSORTYPE']);
            }
        }
        return null;
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
    public static function produceConfigurations(RDB &$oDB, TranslateText &$oTxt, TxnInfo &$oTI, SimpleDOMElement $aObj_XML)
    {
        $paymentMethods = $aObj_XML->payment_methods->payment_method;
        $aObj_Configurations = array();
        for ($i = 0; $i < count($paymentMethods); $i++) {
            $aObj_Configurations[] = self::produceConfig($oDB, $oTxt, $oTI, $paymentMethods[$i]->id, $paymentMethods[$i]->psp_type);
        }
        return $aObj_Configurations;
    }

}