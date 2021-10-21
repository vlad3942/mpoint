<?php
namespace api\classes\merchantservices\Repositories;


use AddonServiceTypeIndex;
use api\classes\merchantservices\configuration\AddonServiceType;
use api\classes\merchantservices\configuration\BaseConfig;
use api\classes\merchantservices\configuration\ServiceConfig;
use CardPrefixConfig;
use ClientConfig;
use Constants;
use PaymentMethod;
use RDB;
use TranslateText;
use TxnInfo;

class ReadOnlyConfigRepository
{
    private MerchantConfigRepository $_merchantConfRepo;
    private RDB $_conn;

    public function __construct(RDB  &$conn,ClientConfig  $clientConfig)
    {
        $this->_conn = $conn;
        $this->_merchantConfRepo = new MerchantConfigRepository($conn,$clientConfig->getID(),$clientConfig);
    }
    private function getDBConn():RDB { return $this->_conn;}


    private function getMerchantConfigRepo():MerchantConfigRepository { return $this->_merchantConfRepo; }

    public function getAddonConfiguration(AddonServiceType $addonServiceType,TxnInfo &$oTI,array $aPmId) : BaseConfig
    {
        $sWhereCls = '';
        if($addonServiceType->getID() === AddonServiceTypeIndex::ePCC)
        {
            $sWhereCls = " sale_currency_id = ".$oTI->getCurrencyConfig()->getID();
        }
       $sWhereCls .= " and pmid in(".implode(',', $aPmId).")";
       return $this->getMerchantConfigRepo()->getAddonConfig($addonServiceType,$sWhereCls);
    }

    public function getCardConfigurationsByCardIds(TxnInfo &$oTI,TranslateText &$oTxt,  $aObj_PaymentMethods) :array
    {
        $paymentMethods = $aObj_PaymentMethods->payment_methods->payment_method;
        $aPaymentMethodsConfig = array();
        for ($i = 0, $iMax = count($paymentMethods); $i < $iMax; $i++) {

            $aPaymentMethodsConfig[$paymentMethods[$i]->id] = array(
                'state_id' => $paymentMethods[$i]->state_id,
                'preference' => $paymentMethods[$i]->preference
            );
        }
        $aPmId = array_keys($aPaymentMethodsConfig);
        $sql = "SELECT  C.position, C.id, C.name, C.minlength, C.maxlength, C.cvclength, C.paymenttype, C.paymenttype AS processortype, '-1' AS pspid,
                false AS preferred, 0 AS installment, false as cvcmandatory,'' AS walletid, coalesce(dcc.enabled,false)  as dccenabled
				FROM System" . sSCHEMA_POSTFIX . ".Card_Tbl C LEFT JOIN CLIENT".sSCHEMA_POSTFIX.".DCC_config_tbl dcc ON c.id = dcc.pmid AND dcc.enabled = true
				and dcc.clientid = ".$this->_merchantConfRepo->getClientInfo()->getID()." AND dcc.countryid = ".$oTI->getCountryConfig()->getID()." AND dcc.currencyid=".$oTI->getCurrencyConfig()->getID()."
				WHERE C.id IN (" . implode(',', $aPmId) . ") AND C.enabled = '1'";
        $result = $this->getDBConn()->getAllNames($sql);

        if (is_array($result) === true && count($result) > 0)
        {
            foreach ($result as $aRS)
            {
                // Set processor type and stateid given by CRS into resultset
                $aCardConfig = $aPaymentMethodsConfig[$aRS['ID']];
                $aRS['STATEID'] = $aCardConfig['state_id'];
                $preference = $aCardConfig['preference'];

                // Transaction instantiated via SMS or "Card" is NOT Premium SMS
                if ($oTI->getGoMobileID() > -1 || $aRS['ID'] != Constants::iPREMIUM_SMS)
                {

                    if ($aRS['ID'] == 11)
                    {
                        if (($oTI->getClientConfig()->getStoreCard() & 1) == 1) $aRS['NAME'] = $oTxt->_("Stored Cards");
                        else $aRS['NAME'] = str_replace("{CLIENT}", $oTI->getClientConfig()->getName(), $oTxt->_("My Account"));
                    }


                    $aPrefixes = CardPrefixConfig::produceConfigurations($this->getDBConn(), $aRS['ID']);

                    $aObj_Configurations[$preference] = new PaymentMethod($oTI, $this->_conn, $aPrefixes, $aRS, $aRS['PROCESSORTYPE'], $aRS['PSPID'], $aRS['STATEID'], $aRS['PREFERRED'], $aRS['INSTALLMENT'], $aRS['CAPTURE_TYPE'], $aRS['CVCMANDATORY'], $aRS['WALLETID'], $aRS['DCCENABLED']);
                }
            }
        }
        return $aObj_Configurations;

    }
}