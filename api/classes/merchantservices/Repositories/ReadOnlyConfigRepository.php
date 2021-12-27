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
use PSPConfig;
use RDB;
use TranslateText;
use TxnInfo;
/**
 * Readonly Configuration Repository
 *
 *
 * @package    Mechantservices
 * @subpackage DB Services
 */

class ReadOnlyConfigRepository
{
    /**
     * @var MerchantConfigRepository
     */
    private MerchantConfigRepository $_merchantConfRepo;

    /**
     * @var RDB
     */
    private RDB $_conn;

    /**
     * @var TxnInfo|null
     */
    private TxnInfo $_oTI;

    /**
     * @param RDB $conn
     * @param TxnInfo|null $oTI
     */
    public function __construct(RDB  &$conn,?TxnInfo  &$oTI)
    {
        $this->_conn = $conn;
        $clientConfig = $oTI->getClientConfig();
        $this->_oTI = $oTI;
        $this->_merchantConfRepo = new MerchantConfigRepository($conn,$oTI->getClientConfig()->getID(), $clientConfig);
    }

    /**
     * @return RDB
     */
    private function getDBConn():RDB { return $this->_conn;}

    /**
     * @return TxnInfo
     */
    private function getTxnInfo():TxnInfo { return $this->_oTI;}

    /**
     * @return MerchantConfigRepository
     */
    private function getMerchantConfigRepo():MerchantConfigRepository { return $this->_merchantConfRepo; }

    /**
     * @param AddonServiceType $addonServiceType
     * @param array $aPmId
     * @param bool $isPropertyOnly
     * @return BaseConfig
     */
    public function getAddonConfiguration(AddonServiceType $addonServiceType,array $aPmId=array(),bool $isPropertyOnly = false) : BaseConfig
    {
        $aWhereCls = array();
        if($isPropertyOnly === false)
        {
            if($addonServiceType->getID() === AddonServiceTypeIndex::ePCC)
            {
                array_push($aWhereCls,"sale_currency_id = ".$this->_oTI->getCurrencyConfig()->getID());
            }
            else if($addonServiceType->getID() !== AddonServiceTypeIndex::eMPI)
            {
                array_push($aWhereCls,"(currencyid = ".$this->_oTI->getCurrencyConfig()->getID()." OR currencyid=0) AND (countryid = ".$this->_oTI->getCountryConfig()->getID()." OR countryid=0) ");
            }
        }

        if(empty($aPmId) === false) array_push ($aWhereCls , "pmid in(".implode(',', $aPmId).")");
        else { array_push ($aWhereCls , "pmid in(".$this->_oTI->getCardID().")");   }

       return $this->getMerchantConfigRepo()->getAddonConfig($addonServiceType,$aWhereCls,$isPropertyOnly);
    }

    /**
     * @param TranslateText $oTxt
     * @param $aObj_PaymentMethods
     * @return array
     */
    public function getCardConfigurationsByCardIds(TranslateText &$oTxt,  $aObj_PaymentMethods) :array
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


        $result = $this->getResultSetCardConfigurationsByCardIds($aPmId);
        $aObj_Configurations = array();
        if (is_array($result) === true && count($result) > 0)
        {
            foreach ($result as $aRS)
            {
                // Set processor type and stateid given by CRS into resultset
                $aCardConfig = $aPaymentMethodsConfig[$aRS['ID']];
                $aRS['STATEID'] = $aCardConfig['state_id'];
                $preference = $aCardConfig['preference'];

                // Transaction instantiated via SMS or "Card" is NOT Premium SMS
                if ($this->_oTI->getGoMobileID() > -1 || $aRS['ID'] != Constants::iPREMIUM_SMS)
                {

                    if ($aRS['ID'] === 11)
                    {
                        if (($this->_oTI->getClientConfig()->getStoreCard() & 1) === 1) {$aRS['NAME'] = $oTxt->_("Stored Cards"); }
                        else { $aRS['NAME'] = str_replace("{CLIENT}", $this->_oTI->getClientConfig()->getName(), $oTxt->_("My Account")); }
                    }


                    $aPrefixes = CardPrefixConfig::produceConfigurations($this->getDBConn(), $aRS['ID']);

                    $aObj_Configurations[$preference] = new PaymentMethod($this->_oTI, $this->_conn, $aPrefixes, $aRS, $aRS['PROCESSORTYPE'], $aRS['PSPID'], $aRS['STATEID'], $aRS['PREFERRED'], $aRS['INSTALLMENT'], $aRS['CAPTURE_TYPE'], $aRS['CVCMANDATORY'], $aRS['WALLETID'], $aRS['DCCENABLED']);
                }
            }
        }
        return $aObj_Configurations;

    }

    /**
     * @param array $aPmId
     * @param int $routeId
     * @return array|false
     */
    public function getResultSetCardConfigurationsByCardIds(array $aPmId,int $routeId = 0)
    {
        $sJoins = "";
        $sColumns = "";
        if($this->_oTI->getClientConfig()->getClientServices()->isDcc() === true)
        {
            $sJoins = "LEFT JOIN CLIENT".sSCHEMA_POSTFIX.".DCC_config_tbl dcc ON c.id = dcc.pmid AND dcc.enabled = true
				and dcc.clientid = ".$this->_oTI->getClientConfig()->getID()." AND (dcc.countryid = ".$this->_oTI->getCountryConfig()->getID()." OR dcc.countryid = 0) AND (dcc.currencyid=".$this->_oTI->getCurrencyConfig()->getID()." OR dcc.currencyid=0)";
            $sColumns = "coalesce(dcc.enabled,false)  as dccenabled";
        }
        else
        {
            $sColumns = "false  as dccenabled";
        }


        $sql = "SELECT  C.position, C.id, C.name, C.minlength, C.maxlength, C.cvclength, C.paymenttype, C.paymenttype AS processortype, '-1' AS pspid,
                false AS preferred, 0 AS installment, false as cvcmandatory,'' AS walletid, ".$sColumns."
				FROM System" . sSCHEMA_POSTFIX . ".Card_Tbl C 
				".$sJoins."
				WHERE C.id IN (" . implode(',', $aPmId) . ") AND C.enabled = '1'";

        if($routeId !== 0)
        {
            $RS = $this->getDBConn()->getName($sql);

            if(is_array($RS) === true && count($RS) > 0)
            {
                $routeSQL = "SELECT  R.providerid AS pspid, RC.capturetype as capture_type, RC.mid AS account FROM Client".sSCHEMA_POSTFIX.".Routeconfig_Tbl RC  
                   INNER JOIN Client".sSCHEMA_POSTFIX.".Route_Tbl R ON RC.routeid = R.id AND R.clientid = ".$this->_oTI->getClientConfig()->getID()." AND R.enabled = '1'
                    WHERE RC.id = ".$routeId;
                $resultSet = $this->getDBConn()->getName($routeSQL);
                if(is_array($resultSet) === true && count($resultSet) > 0)
                {
                    return array_merge($RS,$resultSet);
                }
                else
                {
                    return array();
                }
            }
        }
        return $this->getDBConn()->getAllNames($sql);
    }

    /**
     * @param int $pspid
     * @param int $routeconfigid
     * @return PSPConfig|null
     */
    public function getPSPConfig(int $pspid, int $routeconfigid): ?PSPConfig
    {
        $sql = "SELECT PSP.id, PSP.name, PSP.system_type, RC.mid, RC.username, RC.password, R.id as MerchantId, RC.id AS routeconfigid
				FROM System".sSCHEMA_POSTFIX.".PSP_Tbl PSP
				INNER JOIN Client".sSCHEMA_POSTFIX.".Route_Tbl R ON PSP.id = R.providerid AND R.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".Routeconfig_Tbl RC ON R.id = RC.routeid AND RC.enabled = '1'
            	WHERE R.clientid = ". $this->_oTI->getClientConfig()->getID() ." AND PSP.enabled = '1' 
				AND RC.id = ". $routeconfigid;

        $RS = $this->getDBConn()->getName($sql);

        if (is_array($RS) === true && count($RS) > 1)
        {
            $sql = "SELECT I.language, I.text
					FROM Client".sSCHEMA_POSTFIX.".Info_Tbl I
					INNER JOIN Client".sSCHEMA_POSTFIX.".InfoType_Tbl IT ON I.infotypeid = IT.id AND IT.enabled = '1'
					WHERE I.clientid = ". $this->_oTI->getClientConfig()->getID() ." AND IT.id = ". Constants::iPSP_MESSAGE_INFO ." AND (I.pspid = ". $pspid ." OR I.pspid IS NULL)";

            $aRS = $this->getDBConn()->getAllNames($sql);
            $aMessages = array();
            if (is_array($aRS) === true)
            {
                for ($i=0; $i<count($aRS); $i++)
                {
                    $aMessages[strtolower($aRS[$i]["LANGUAGE"])] = $aRS[$i]["TEXT"];
                }
            }

            $aPSPProperty  =  $this->getMerchantConfigRepo()->getPropertyConfig("PSP","CLIENT",$pspid,array(),false);
            $aRouteProperty  =  $this->getMerchantConfigRepo()->getPropertyConfig("ROUTE","CLIENT",$pspid,array(),false);
            $aProperty = array_merge($aPSPProperty,$aRouteProperty);

            $aAdditionalProperties = array();
            if (is_array($aProperty) === true && count($aProperty) > 0)
            {
                $iConstOfRows = count($aProperty);
                for ($i=0; $i<$iConstOfRows; $i++)
                {
                    $aAdditionalProperties[$i]["key"] =$aProperty[$i]->getName();
                    $aAdditionalProperties[$i]["value"] = $aProperty[$i]->getValue();
                    $aAdditionalProperties[$i]["scope"] = $aProperty[$i]->getScope();
                }
            }

            //Get route feature
            $sql  = "SELECT SRF.id, CRF.enabled, SRF.featurename
					 FROM Client". sSCHEMA_POSTFIX .".RouteFeature_Tbl CRF
					 INNER JOIN System". sSCHEMA_POSTFIX .".RouteFeature_Tbl SRF ON CRF.featureid = SRF.id AND SRF.enabled = '1'
					 WHERE routeconfigid = ". (int)$RS["ROUTECONFIGID"];

            $aRS = $this->getDBConn()->getAllNames($sql);
            $aRouteFeature = array();
            if (is_array($aRS) === true) {
                $aRouteFeature = $aRS;
                unset($aRS);
            }



            return  new PSPConfig($RS["ID"], $RS["NAME"], (int)$RS["SYSTEM_TYPE"], $RS["MID"], "",  $RS["USERNAME"], $RS["PASSWORD"], $aMessages,$aAdditionalProperties, $RS["ROUTECONFIGID"], $aRouteFeature, $RS["MID"], $RS["USERNAME"], $RS["PASSWORD"]);
        }
        else
        {
            trigger_error("PSP Configuration not found using Client ID: ". $this->_oTI->getClientConfig()->getID() .", Account: ". $this->_oTI->getClientConfig()->getAccountConfig()->getID() .", PSP ID: ". $pspid .", Route Config ID: ". $routeconfigid, E_USER_WARNING);
            return null;
        }
    }


    /**
     * @param int $pspid
     * @param int $routeconfigid
     * @return PSPConfig|null
     */
    public function getProviderConfig(int $pspid,BaseConfig $addonConfig=null): ?PSPConfig
    {
        $sql = "SELECT DISTINCT PSP.id, PSP.name, PSP.system_type,
					MA.name AS ma, MA.username, MA.passwd AS password, '' AS msa, MA.id as MerchantId
				FROM System".sSCHEMA_POSTFIX.".PSP_Tbl PSP
				INNER JOIN Client".sSCHEMA_POSTFIX.".MerchantAccount_Tbl MA ON PSP.id = MA.pspid AND MA.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".Client_Tbl CL ON MA.clientid = CL.id AND CL.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".Account_Tbl Acc ON CL.id = Acc.clientid AND Acc.enabled = '1'
				INNER JOIN SYSTEM".sSCHEMA_POSTFIX.".processortype_tbl PT ON PSP.system_type = PT.id	
				WHERE CL.id = ". $this->getTxnInfo()->getClientConfig()->getID() ." AND PSP.id = ". $pspid ." AND PSP.enabled = '1' AND Acc.id = ". $this->getTxnInfo()->getClientConfig()->getAccountConfig()->getID() ." AND (MA.stored_card = '0' OR MA.stored_card IS NULL)";
//		echo $sql ."\n";
        $RS = $this->getDBConn()->getName($sql);
        if (is_array($RS) === true && count($RS) > 1)
        {
            $sql = "SELECT I.language, I.text
					FROM Client".sSCHEMA_POSTFIX.".Info_Tbl I
					INNER JOIN Client".sSCHEMA_POSTFIX.".InfoType_Tbl IT ON I.infotypeid = IT.id AND IT.enabled = '1'
					WHERE I.clientid = ". $this->getTxnInfo()->getClientConfig()->getID() ." AND IT.id = ". Constants::iPSP_MESSAGE_INFO ." AND (I.pspid = ". $pspid ." OR I.pspid IS NULL)";
//			echo $sql ."\n";
            $aRS = $this->getDBConn()->getAllNames($sql);
            $aMessages = array();
            if (is_array($aRS) === true)
            {
                for ($i=0; $i<count($aRS); $i++)
                {
                    $aMessages[strtolower($aRS[$i]["LANGUAGE"])] = $aRS[$i]["TEXT"];
                }
            }

            $aPSPProperty  =  $this->getMerchantConfigRepo()->getPropertyConfig("PSP","CLIENT",$pspid,array(),false);
            $aProperty = $aPSPProperty;
            $i=0;
            $aAdditionalProperties = array();
            if (is_array($aProperty) === true && count($aProperty) > 0)
            {
                $iConstOfRows = count($aProperty);
                for (; $i<$iConstOfRows; $i++)
                {
                    $aAdditionalProperties[$i]["key"] =$aProperty[$i]->getName();
                    $aAdditionalProperties[$i]["value"] = $aProperty[$i]->getValue();
                    $aAdditionalProperties[$i]["scope"] = $aProperty[$i]->getScope();
                }
            }
            if($addonConfig !== null)
            {
                foreach ($addonConfig->getProperties() as $key => $value)
                {
                    $aAdditionalProperties[$i]["key"] =$key;
                    $aAdditionalProperties[$i]["value"] = $value;
                    $aAdditionalProperties[$i]["scope"] = Constants::iPrivateProperty;
                }

            }

            return new PSPConfig($RS["ID"], $RS["NAME"], (int) $RS["SYSTEM_TYPE"], $RS["MA"], $RS["MSA"], $RS["USERNAME"], $RS["PASSWORD"], $aMessages, $aAdditionalProperties);
        }
        else
        {
            trigger_error("PSP Configuration not found using Client ID: ". $this->getTxnInfo()->getClientConfig()->getID() .", Account: ". $this->getTxnInfo()->getClientConfig()->getAccountConfig()->getID() .", PSP ID: ". $pspid, E_USER_WARNING);
            return null;
        }
    }

}