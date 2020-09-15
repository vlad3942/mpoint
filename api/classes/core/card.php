<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:Card.php
 */

class Card
{
    private $sCvc = '';
    private $sCardNumber = '';
    private $sExpiry = '';
    private $sValidFrom = '';
    private $sCardHolderName = '';
    private $sCryptogram = '';
    private $sCryptogramType = ''; //emv, 3ds
    private $iEci = -1;
    private $sToken = '';
    private $sNetwork = '';
    private $sXid = '';
    private $iCardTypeId = -1;

    private $isAdditionalCardDetailsFetched = FALSE;
    private $iMinCardLength = -1;
    private $iMaxCardLength = -1;
    private $iCvcLength = -1;
    private $aBinRange = [];

    private $sCardName = '';
    private $iPaymentType = '';
    private $iPosition = '';

    private $objDB;

    public function __construct($obj_Card, RDB &$oDB = NULL, array $prefixes = NULL)
    {
        if ( ($obj_Card instanceof SimpleDOMElement) === true)
        {
            $this->initializePropFromXML($obj_Card);
        }
        else
        {
            $this->initializePropFromArray($obj_Card);
        }
        $this->objDB = $oDB;
        $this->aBinRange = $prefixes;
    }

    /**
     * @return string
     */
    public function getCvc()
    {
        return $this->sCvc;
    }

    /**
     * @return string
     */
    public function getCardNumber()
    {
        return $this->sCardNumber;
    }

    /**
     * @return string
     */
    public function getExpiry()
    {
        return $this->sExpiry;
    }

    /**
     * @return string
     */
    public function getValidFrom()
    {
        return $this->sValidFrom;
    }

    /**
     * @return string
     */
    public function getCardHolderName()
    {
        return $this->sCardHolderName;
    }

    /**
     * @return string
     */
    public function getCryptogram()
    {
        return $this->sCryptogram;
    }

    /**
     * @return string
     */
    public function getCryptogramType()
    {
        return $this->sCryptogramType;
    }

    /**
     * @return int
     */
    public function getEci()
    {
        return $this->iEci;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->sToken;
    }

    /**
     * @return string
     */
    public function getNetwork()
    {
        return $this->sNetwork;
    }

    /**
     * @return string
     */
    public function getXid()
    {
        return $this->sXid;
    }

    /**
     * @param \RDB|null $oRDB
     *
     * @return int
     */
    public function getMinCardLength(RDB $oRDB = NULL)
    {
        $this->getAdditionalCardDetails($oRDB);
        return $this->iMinCardLength;
    }

    /**
     * @param \RDB|NULL $oRDB
     */
    private function getAdditionalCardDetails(RDB $oRDB = NULL)
    {
        if ($this->isAdditionalCardDetailsFetched === FALSE) {
            $oDB = $this->objDB;
            if ($oRDB instanceof RDB) {
                $oDB = $oRDB;
            }
            if ($oDB === NULL) {
                trigger_error('Database objected is not passed', E_USER_ERROR);
            } else {
                $sql = "SELECT name, minlength, maxlength, cvclength, paymenttype FROM system.card_tbl WHERE enabled = true and id = $this->iCardTypeId;";
                $resultSet = $oDB->getName($sql);
                if (is_array($resultSet)) {
                    $this->iCvcLength = (int)$resultSet['CVCLENGTH'];
                    $this->iMinCardLength = (int)$resultSet['MINLENGTH'];
                    $this->iMaxCardLength = (int)$resultSet['MAXLENGTH'];
                    $this->iPaymentType   = (int)$resultSet['PAYMENTTYPE'];
                    $this->sCardName = (string)$resultSet['NAME'];
                    $this->isAdditionalCardDetailsFetched = TRUE;
                }
            }
        }
    }

    /**
     * @param int $iMinCardLength
     */
    private function setMinCardLength($iMinCardLength)
    {
        $this->iMinCardLength = $iMinCardLength;
    }

    /**
     * @param \RDB|null $oRDB
     *
     * @return int
     */
    public function getMaxCardLength(RDB $oRDB = NULL)
    {
        $this->getAdditionalCardDetails($oRDB);
        return $this->iMaxCardLength;
    }

    /**
     * @param int $iMaxCardLength
     */
    private function setMaxCardLength($iMaxCardLength)
    {
        $this->iMaxCardLength = $iMaxCardLength;
    }

    /**
     * @param \RDB|null $oRDB
     *
     * @return int
     */
    public function getCvcLength(RDB $oRDB = NULL)
    {
        $this->getAdditionalCardDetails($oRDB);
        return $this->iCvcLength;
    }

    /**
     * @param int $iCvcLength
     */
    private function setCvcLength($iCvcLength)
    {
        $this->iCvcLength = $iCvcLength;
    }

    /**
     * @return string
     */
    public function getCardTypeId()
    {
        return $this->iCardTypeId;
    }

    /**
     * @return string
     */
    public function getCardName(RDB $oRDB = NULL)
    {
        $this->getAdditionalCardDetails($oRDB);
        return $this->sCardName;
    }

    /**
     * @return integer
     */
    public function getPaymentType(RDB $oRDB = NULL)
    {
        $this->getAdditionalCardDetails($oRDB);
        return $this->iPaymentType;
    }

    /**
     * @param \RDB|null $oRDB
     *
     * @return array
     */
    public function getBinRange(RDB $oRDB = NULL)
    {
        if (empty($this->aBinRange)) {
            $oDB = $this->objDB;
            if ($oRDB instanceof RDB) {
                $oDB = $oRDB;
            }
            if ($oDB === NULL) {
                trigger_error('Database objected is not passed', E_USER_ERROR);
            } else {
                $sql = "SELECT min, max FROM system.cardprefix_tbl WHERE enabled = true and cardid = $this->iCardTypeId;";
                while ($resultSet = $oDB->getName($sql))
                    array_push($this->aBinRange, '{"min": "' . (int)$resultSet['MIN'] . '" , "max": "' . (int)$resultSet['MAX'] . '"}');
            }
        }
        return $this->aBinRange;
    }

    private function initializePropFromXML($obj_Card)
    {
        if (empty($obj_Card->{'expiry'}) === FALSE) {
            $this->sExpiry = (string)$obj_Card->{'expiry'};
        }
        if (empty($obj_Card->{'card-holder-name'}) === FALSE) {
            $this->sCardHolderName = (string)$obj_Card->{'card-holder-name'};
        }
        if (empty($obj_Card->{'card-number'}) === FALSE) {
            $this->sCardNumber = (string)$obj_Card->{'card-number'};
        }
        if (empty($obj_Card->{'valid-from'}) === FALSE) {
            $this->sValidFrom = (string)$obj_Card->{'valid-from'};
        }
        if (empty($obj_Card->{'cvc'}) === FALSE) {
            $this->sCvc = (string)$obj_Card->{'cvc'};
        }
        if (empty($obj_Card->{'token'}) === FALSE) {
            $this->sToken = (string)$obj_Card->{'token'};
        }
        if (empty($obj_Card->{'info-3d-secure'}->{'cryptogram'}) === FALSE) {
            $this->sCryptogram = (string)$obj_Card->{'info-3d-secure'}->{'cryptogram'};
        }
        if (empty($obj_Card->{'info-3d-secure'}->{'cryptogram'}["type"]) === FALSE) {
            $this->sCryptogramType = (string)$obj_Card->{'info-3d-secure'}->{'cryptogram'}["type"];
        }
        if (empty($obj_Card->{'info-3d-secure'}->{'cryptogram'}["eci"]) === FALSE) {
            $this->iEci = (int)$obj_Card->{'info-3d-secure'}->{'cryptogram'}["eci"];
        }
        if (empty($obj_Card->{'info-3d-secure'}->cryptogram["xid"]) === FALSE) {
            $this->sXid = (string)$obj_Card->{'info-3d-secure'}->cryptogram["xid"];
        }
        if (empty($obj_Card["network"]) === FALSE) {
            $this->sNetwork = (string)$obj_Card["network"];
        }
        if (empty($obj_Card["type-id"]) === FALSE) {
            $this->iCardTypeId = $obj_Card["type-id"];
        }
    }

    private function initializePropFromArray($aCard)
    {
        if(empty($aCard['ID']) === FALSE)
        {
            $this->iCardTypeId = $aCard['ID'];
        }
        if(empty($aCard['NAME']) === FALSE)
        {
            $this->sCardName = $aCard['NAME'];
        }
        if(empty($aCard['POSITION']) === FALSE)
        {
            $this->iPosition = $aCard['POSITION'];
        }
        if(empty($aCard['MINLENGTH']) === FALSE)
        {
            $this->iMinCardLength = $aCard['MINLENGTH'];
        }
        if(empty($aCard['MAXLENGTH']) === FALSE)
        {
            $this->iMaxCardLength = $aCard['MAXLENGTH'];
        }
        if(empty($aCard['CVCLENGTH']) === FALSE)
        {
            $this->iCvcLength = $aCard['CVCLENGTH'];
        }
        if(empty($aCard['PAYMENTTYPE']) === FALSE)
        {
            $this->iPaymentType = $aCard['PAYMENTTYPE'];
        }
    }

}