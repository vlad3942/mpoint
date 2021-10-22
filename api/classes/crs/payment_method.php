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

}

?>