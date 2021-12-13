<?php
namespace api\classes\merchantservices\configuration;

use AddonServiceTypeIndex;
use api\classes\merchantservices\OperationStatus;
use api\classes\merchantservices\SQLOperation;
use General;

/**
 *
 * @package    Mechantservices
 * @subpackage Service Config
 */
class ServiceConfig
{
    /**
     * @var int
     */
    private int $_id = -1;

    /**
     * @var int
     */
    private int $_iPMId = -1;

    /**
     * @var int
     */
    private int $_iCountryId = -1;

    /**
     * @var int
     */
    private int $_iCurrencyId = -1;

    /**
     * @var int
     */
    private int $_iSettlementCurrencyId = -1;

    /**
     * @var bool
     */
    private bool $_bPresentment;

    /**
     * @var string
     */
    private string $_dCreated;

    /**
     * @var string
     */
    private string $_dModified;

    /**
     * @var bool
     */
    private bool $_bEnabled = false;

    /**
     * @var int
     */
    private int $_iProviderId = -1;

    /**
     * @var string
     */
    private string $_sVersion = '' ;

    /**
     * @var int
     */
    private int $_iPaymentType = -1 ;

    /**
     * @var int
     */
    private int $_iSequenceNo = -1 ;


    /**
     * Default Constructor
     */
    public function __construct() {}


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->_id;
    }

    /**
     * @return int
     */
    public function getPaymentMethodId(): int
    {
        return $this->_iPMId;
    }

    /**
     * @return int
     */
    public function getCountryId(): int
    {
        return $this->_iCountryId;
    }

    /**
     * @return int
     */
    public function getCurrencyId(): int
    {
        return $this->_iCurrencyId;
    }

    /**
     * @return int
     */
    public function getSettlementCurrencyId(): int
    {
        return $this->_iSettlementCurrencyId;
    }

    /**
     * @return bool
     */
    public function isPresentment(): bool
    {
        return $this->_bPresentment;
    }

    /**
     * @return string
     */
    public function getCreated(): string
    {
        return $this->_dCreated;
    }

    /**
     * @return string
     */
    public function getModified(): string
    {
        return $this->_dModified;
    }

    /**
     * @return bool
     */
    public function getEnabled(): bool
    {
        return $this->_bEnabled;
    }

    /**
     * @return int
     */
    public function getProviderId(): int
    {
        return $this->_iProviderId;
    }



    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->_sVersion;
    }

    /**
     * @return int
     */
    public function getPaymentType(): int
    {
        return $this->_iPaymentType;
    }

    /**
     * @return int
     */
    public function getSequenceNo(): int
    {
        return $this->_iSequenceNo;
    }

    /**
     * @param int $id
     * @return ServiceConfig
     */
    public function setId(int $id): ServiceConfig
    {
        $this->_id = $id;
        return $this;
    }

    /**
     * @param int $iCardId
     * @return ServiceConfig
     */
    public function setPaymentMethodId(int $iPMId): ServiceConfig
    {
        $this->_iPMId = $iPMId;
        return $this;
    }

    /**
     * @param int $iCountryId
     * @return ServiceConfig
     */
    public function setCountryId(int $iCountryId): ServiceConfig
    {
        $this->_iCountryId = $iCountryId;
        return $this;
    }

    /**
     * @param int $iCurrencyId
     * @return ServiceConfig
     */
    public function setCurrencyId(int $iCurrencyId): ServiceConfig
    {
        $this->_iCurrencyId = $iCurrencyId;
        return $this;
    }

    /**
     * @param int $iSettlementCurrencyId
     * @return ServiceConfig
     */
    public function setSettlementCurrencyId(int $iSettlementCurrencyId): ServiceConfig
    {
        $this->_iSettlementCurrencyId = $iSettlementCurrencyId;
        return $this;
    }

    /**
     * @param bool $bPresentment
     * @return ServiceConfig
     */
    public function setPresentment(bool $bPresentment): ServiceConfig
    {
        $this->_bPresentment = $bPresentment;
        return $this;
    }

    /**
     * @param string $dCreated
     * @return ServiceConfig
     */
    public function setCreated(string $dCreated): ServiceConfig
    {
        $this->_dCreated = $dCreated;
        return $this;
    }

    /**
     * @param string $dModified
     * @return ServiceConfig
     */
    public function setModified(string $dModified): ServiceConfig
    {
        $this->_dModified = $dModified;
        return $this;
    }

    /**
     * @param string $dEnabled
     * @return ServiceConfig
     */
    public function setEnabled(string $dEnabled): ServiceConfig
    {
        $this->_bEnabled = $dEnabled;
        return $this;
    }

    /**
     * @param int $iProviderId
     * @return ServiceConfig
     */
    public function setProviderId(int $iProviderId): ServiceConfig
    {
        $this->_iProviderId = $iProviderId;
        return $this;
    }



    /**
     * @param string $sVersion
     * @return ServiceConfig
     */
    public function setVersion(string $sVersion): ServiceConfig
    {
        $this->_sVersion = $sVersion;
        return $this;
    }

    /**
     * @param int $sVersion
     * @return ServiceConfig
     */
    public function setPaymentType(int $iPaymentType): ServiceConfig
    {
        $this->_iPaymentType = $iPaymentType;
        return $this;
    }

    /**
     * @param int $sVersion
     * @return ServiceConfig
     */
    public function setSequenceNo(int $iSequenceNo): ServiceConfig
    {
        $this->_iSequenceNo = $iSequenceNo;
        return $this;
    }

    /**
     * @param AddonServiceType $addonServiceType
     * @return string
     */
    public  function getUpdateSQL(AddonServiceType $addonServiceType):string
    {
        $sql = "UPDATE CLIENT". sSCHEMA_POSTFIX .".%s Set %s where id=".$this->getId();

        $parms = " enabled=".General::bool2xml($this->getEnabled());

        if($this->getPaymentMethodId()>-1) $parms .= ",pmid=".$this->getPaymentMethodId();
        if($this->getCurrencyId()>-1)
        {
            if($addonServiceType->getID() === AddonServiceTypeIndex::ePCC)  $parms .= ",sale_currency_id=".$this->getCurrencyId();
            else  $parms .= ",currencyid=".$this->getCurrencyId();

        }
        if($this->getCountryId()>-1)  $parms .= ",countryid=".$this->getCountryId();
        if($this->getPaymentType()>-1)  $parms .= ",payment_type=".$this->getPaymentType();
        if($this->getSequenceNo()>-1)  $parms .= ",sequence_no=".$this->getSequenceNo();
        if($this->getSettlementCurrencyId()>-1)
        {
            $parms .= ",settlement_currency_id=".$this->getSettlementCurrencyId();
            $parms .= ",is_presentment=".General::bool2xml($this->isPresentment());
        }

        if($this->getProviderId()>-1) $parms .= ",providerid=".$this->getProviderId();
        if($addonServiceType->getID() === AddonServiceTypeIndex::eFraud)
        {
            $type = 1;
            if($addonServiceType->getSubType() === 'post_auth') $type = 2;

            $parms .= ',typeoffraud='.$type;
        }

        if(empty($this->getVersion()) === false) $parms .= ',"version"=\''.$this->getVersion().'\'';
      return  sprintf($sql,$addonServiceType->getTableName(),$parms);
    }

    /**
     * @param AddonServiceType $addonServiceType
     * @return string
     */
    public static function getInsertSQL(AddonServiceType $addonServiceType):string
    {
        $sql = "INSERT INTO CLIENT". sSCHEMA_POSTFIX .".%s (%s) values (%s)";
        if ($addonServiceType->getID() === AddonServiceTypeIndex::eMCP || $addonServiceType->getID() === AddonServiceTypeIndex::eDCC)
        {
            $sTableName = $addonServiceType->getTableName();
            return sprintf($sql,$sTableName,"clientid,pmid,countryid,currencyid","$1,$2,$3,$4");
        }
        else if ($addonServiceType->getID() === AddonServiceTypeIndex::ePCC)
        {
            return sprintf($sql,$addonServiceType->getTableName(),"clientid,pmid,sale_currency_id,is_presentment,settlement_currency_id","$1,$2,$3,$4,$5");
        }
        else if ($addonServiceType->getID() === AddonServiceTypeIndex::eFraud)
        {
            return sprintf($sql,$addonServiceType->getTableName(),'clientid, pmid, providerid, countryid, currencyid,typeoffraud ',"$1,$2,$3,$4,$5,$6");
        }
        else if ($addonServiceType->getID() === AddonServiceTypeIndex::eMPI)
        {
            return sprintf($sql,$addonServiceType->getTableName(),'clientid, pmid, providerid, "version" ',"$1,$2,$3,$4");
        }
        else if ($addonServiceType->getID() === AddonServiceTypeIndex::eSPLIT_PAYMENT)
        {
            return sprintf($sql,$addonServiceType->getTableName(),'split_config_id, payment_type, sequence_no',"$1,$2,$3");
        }
        else if ($addonServiceType->getID() === AddonServiceTypeIndex::eTOKENIZATION)
        {
            return sprintf($sql,$addonServiceType->getTableName(),'clientid, pmid, providerid, countryid, currencyid ',"$1,$2,$3,$4,$5");
        }
        else
        return "";
    }

    /**
     * @param AddonServiceType $addonServiceType
     * @param int $iId
     * @return array|int[]
     */
    public function getParam(AddonServiceType $addonServiceType,int $iId):array
    {
        if($addonServiceType->getID() === AddonServiceTypeIndex::eMCP || $addonServiceType->getID() === AddonServiceTypeIndex::eDCC)
        {
            return array($iId,$this->getPaymentMethodId(),$this->getCountryId(),$this->getCurrencyId());

        }else  if($addonServiceType->getID() === AddonServiceTypeIndex::ePCC)
        {
            return array($iId,$this->getPaymentMethodId(),$this->getCurrencyId(),$this->isPresentment()?'true':'false',$this->getSettlementCurrencyId());

        }
        else  if($addonServiceType->getID() === AddonServiceTypeIndex::eMPI)
        {
            return array($iId,$this->getPaymentMethodId(),$this->getProviderId(),$this->getVersion());
        }
        else  if($addonServiceType->getID() === AddonServiceTypeIndex::eFraud)
        {
            $type = 1;
            if($addonServiceType->getSubType() === 'post_auth') {
                $type = 2;
            }

            return array($iId,$this->getPaymentMethodId(),$this->getProviderId(),$this->getCountryId(),$this->getCurrencyId(),$type);
        }
        else  if($addonServiceType->getID() === AddonServiceTypeIndex::eSPLIT_PAYMENT)
        {
           return array($iId,$this->getPaymentType(),$this->getSequenceNo());
        }
        else  if($addonServiceType->getID() === AddonServiceTypeIndex::eTOKENIZATION)
        {
           return array($iId,$this->getPaymentMethodId(),$this->getProviderId(),$this->getCountryId(),$this->getCurrencyId());
        }
        else return array();

    }

    /**
     * @return string
     */
    public function toXML():string
    {
        $xml = "<addon_configuration>";
        $xml .= sprintf("<id>%s</id>",$this->getId());
        $xml .= sprintf("<enabled>%s</enabled>",General::bool2xml($this->getEnabled()));
        if($this->getPaymentMethodId()>-1) {
            $xml .= sprintf("<pm_id>%s</pm_id>",$this->getPaymentMethodId());
        }
        if($this->getCurrencyId()>-1) {
            $xml .=  sprintf("<currency_id>%s</currency_id>",$this->getCurrencyId());
        }
        if($this->getSequenceNo()>-1) {
            $xml .= sprintf("<sequence_no>%s</sequence_no>",$this->getSequenceNo());
        }
        if($this->getPaymentType()>-1) {
            $xml .= sprintf("<payment_type_id>%s</payment_type_id>",$this->getPaymentType());
        }
        if($this->getCountryId()>-1) {
            $xml .= sprintf("<country_id>%s</country_id>",$this->getCountryId());
        }
        if($this->getSettlementCurrencyId()>-1)
        {
            $xml .= sprintf("<settlement_currency_id>%s</settlement_currency_id>",$this->getSettlementCurrencyId());
            $xml .= sprintf("<is_presentment>%s</is_presentment>",General::bool2xml($this->isPresentment()));

        }
        if($this->getProviderId()>-1) {
            $xml .= sprintf("<provider_id>%s</provider_id>",$this->getProviderId());
        }
        if(empty($this->getVersion()) === false) {
            $xml .= sprintf("<version>%s</version>",$this->getVersion());
        }
        $xml .= "</addon_configuration>";

        return $xml;
    }

    /**
     * @param $oXML
     * @return ServiceConfig
     */
    public static function produceFromXML( &$oXML):ServiceConfig
    {
        $serviceConf = new ServiceConfig();
        if(count($oXML->id)>0) {
            $serviceConf->setId((int)$oXML->id);
        }
        if(count($oXML->enabled)>0) {
            $serviceConf->setEnabled(General::xml2bool($oXML->enabled));
        }
        if(count($oXML->pm_id)>0) {
            $serviceConf->setPaymentMethodId((int)$oXML->pm_id);
        }
        if(count($oXML->currency_id)>0) {
            $serviceConf->setCurrencyId((int)$oXML->currency_id);
        }
        if(count($oXML->country_id)>0) {
            $serviceConf->setCountryId((int)$oXML->country_id);
        }
        if(count($oXML->settlement_currency_id)>0) {
            $serviceConf->setSettlementCurrencyId((int)$oXML->settlement_currency_id);
        }
        if(count($oXML->is_presentment)>0) {
            $serviceConf->setPresentment(General::xml2bool($oXML->is_presentment));
        }
        if(count($oXML->provider_id)>0) {
            $serviceConf->setProviderId((int)$oXML->provider_id);
        }
        if(count($oXML->version)>0) {
            $serviceConf->setVersion($oXML->version);
        }
        if(count($oXML->payment_type_id)>0) {
            $serviceConf->setPaymentType((int)$oXML->payment_type_id);
        }
        if(count($oXML->sequence_no)>0) {
            $serviceConf->setSequenceNo((int)$oXML->sequence_no);
        }

        return $serviceConf;
    }

    /**
     * @param $rs
     * @return ServiceConfig
     */
    public static function produceFromResultSet($rs):ServiceConfig
    {
        $serviceConf = new ServiceConfig();
        if(isset($rs["ID"])) {
            $serviceConf->setId($rs["ID"]);
        }
        if(isset($rs['ENABLED'])) {
            $serviceConf->setEnabled($rs['ENABLED']);
        }
        if(isset($rs["PMID"])) {
            $serviceConf->setPaymentMethodId($rs["PMID"]);
        }
        if(isset($rs["CURRENCYID"])) {
            $serviceConf->setCurrencyId($rs["CURRENCYID"]);
        }
        if(isset($rs["SALE_CURRENCY_ID"])) {
            $serviceConf->setCurrencyId($rs["SALE_CURRENCY_ID"]);
        }
        if(isset($rs["COUNTRYID"])) {
            $serviceConf->setCountryId($rs["COUNTRYID"]);
        }
        if(isset($rs['CREATED'])) {
            $serviceConf->setCreated($rs['CREATED']);
        }
        if(isset($rs['MODIFIED'])) {
            $serviceConf->setModified($rs["MODIFIED"]);
        }
        if(isset($rs["SETTLEMENT_CURRENCY_ID"])) {
            $serviceConf->setSettlementCurrencyId($rs["SETTLEMENT_CURRENCY_ID"]);
        }
        if(isset($rs['IS_PRESENTMENT'])) {
            $serviceConf->setPresentment($rs['IS_PRESENTMENT']);
        }
        if(isset($rs["PROVIDERID"])) {
            $serviceConf->setProviderId($rs["PROVIDERID"]);
        }
        if(isset($rs["VERSION"])) {
            $serviceConf->setVersion($rs["VERSION"]);
        }
        if(isset($rs["PAYMENT_TYPE"])) {
            $serviceConf->setPaymentType($rs["PAYMENT_TYPE"]);
        }
        if(isset($rs["SEQUENCE_NO"])) {
            $serviceConf->setSequenceNo($rs["SEQUENCE_NO"]);
        }

        return $serviceConf;
    }

    /**
     * @return string
     */
    public function toString():string
    {
        $aString = array();
        if ($this->getId()>-1) {
            array_push($aString,"id=".$this->getId());
        }
        array_push($aString,"enabled=".General::bool2xml($this->getEnabled()));
        if($this->getPaymentMethodId()>-1) {
            array_push($aString,"pm_id=".$this->getPaymentMethodId());
        }
        if($this->getCurrencyId()>-1) {
            array_push($aString,"currency_id=".$this->getCurrencyId());
        }
        if($this->getCountryId()>-1) {
            array_push($aString,"country_id=".$this->getCountryId());
        }
        if($this->getSettlementCurrencyId()>-1)
        {
            array_push($aString,"settlement_currency_id=".$this->getSettlementCurrencyId());
            array_push($aString,"is_presentment=".General::bool2xml($this->isPresentment()));
        }
        if($this->getProviderId()>-1) {
            array_push($aString,"provider_id=".$this->getProviderId());
        }
        if($this->getPaymentType()>-1) {
            array_push($aString,"payment_type_id=".$this->getPaymentType());
        }
        if($this->getSequenceNo()>-1) {
            array_push($aString,"sequence_no=".$this->getSequenceNo());
        }
        if(empty($this->getVersion()) === false) {
            array_push($aString,"version=".$this->getVersion());
        }
        return '{'.implode(', ', $aString).'}';
    }

}