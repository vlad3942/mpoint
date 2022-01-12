<?php
/**
 * Created by IntelliJ IDEA.
 * User: Chaitenya Yadav
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:HmacSecurityHash.php
 */

namespace api\classes;

class HmacSecurityHash extends SecurityHash
{

    private string $_sHmacType;
    private string $_sOrderID;
    private int $_lAmount;
    private int $_iCountryID;
    private string $_lMobile;
    private int $_iMobileCountryID;
    private string $_sEMail;
    private string $_sDeviceID;
    private string $_lSaleAmount;
    private string $_iSaleCurrency;
    private string $_iCfxID;
    
    /**
     * HmacSecurityHash constructor.
     *
     * @param int $clientId
     * @param string $orderId
     * @param int $amount
     * @param int $countryid
     * @param string $string
     */
    public function __construct(int $clientId, string $orderId, int $amount, int $countryid, string $salt)
    {
        parent::__construct($clientId, $salt);
        $this->_sOrderID = $orderId;
        $this->_lAmount = $amount;
        $this->_iCountryID = $countryid;
    }

    /**
     * @param string $_sHmacType
     */
    public function setHmacType($sHmacType)
    {
            $this->_sHmacType = trim($sHmacType);
    }

    /**
     * @param string $lMobile
     */
    public function setMobile($lMobile)
    {
        $this->_lMobile = $lMobile;
        
    }
    
    /**
     * @param int $iMobileCountry
     */
    public function setMobileCountry($iMobileCountry)
    {
        $this->_iMobileCountryID = $iMobileCountry;
    }

    /**
     * @param string $sEMail
     */
    public function setEMail($sEMail)
    {
        $this->_sEMail = trim($sEMail);
    }

    /**
     * @param string $sDeviceId
     */
    public function setDeviceId($sDeviceId)
    {
        $this->_sDeviceID = $sDeviceId;
    }
    
    /**
     * @param int $lSaleAmount
     */
    public function setSaleAmount($lSaleAmount)
    {
        $this->_lSaleAmount = $lSaleAmount;
    }
    
    /**
     * @param int $iSaleCurrency
     */
    public function setSaleCurrency($iSaleCurrency)
    {
        $this->_iSaleCurrency = $iSaleCurrency;
    }

    /**
     * @param int $iCfxID
     */
    public function setCfxID($iCfxID)
    {
        $this->_iCfxID = $iCfxID;
    }

    public function generate512Hash()
    {
        $this->_hashString = $this->generateHmac();
        if($this->_hashString)
            return parent::generate512Hash();
    }

    public function generateHmac()
	{
        switch ($this->_sHmacType)
		{
		case ('FX'):
			$hmac  = $this->_fxHmac();
			break;
		default:
            $hmac  = $this->_regularHmac();
			break;
		}
		return $hmac;
    }
    
    private function _regularHmac()
	{
        $hmacString = $this->_iClientID.$this->_sOrderID.$this->_lAmount.$this->_iCountryID.$this->_lMobile.$this->_iMobileCountryID.$this->_sEMail.$this->_sDeviceID.$this->_sSalt;
		return $hmacString;
	}
    
    private function _fxHmac()
	{
        $hmacString = $this->_iClientID.$this->_sOrderID.$this->_lAmount.$this->_iCountryID.$this->_lMobile.$this->_iMobileCountryID.$this->_sEMail.$this->_sDeviceID.$this->_sSalt.$this->_lSaleAmount.$this->_iSaleCurrency.$this->_iCfxID;
		return $hmacString;
	}

}
