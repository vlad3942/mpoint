<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anna Lagad
 * Copyright: Cellpoint Digital
 * Link: http://www.cellpointdigital.com
 * Project: mPoint
 * File Name:client_country_currency_config.php
 */

class ClientCountryCurrencyConfig
{
    /**
     * Hold an array of client supported country
     * @var array
     */
	private array $_aCountry;

    /**
     * Hold an array of client supported currency
     * @var array
     */
	private array $_aCurrency;

    /**
     * Default Constructor
     *
     * @param array $aCountryConfig
     * @param array $aCurrencyConfig
     */
	public function __construct(array $aCountryConfig, array $aCurrencyConfig)
	{
        $this->_aCountry = $aCountryConfig;
        $this->_aCurrency = $aCurrencyConfig;
	}

	public function toCountryAsXML() : string
	{
        $xml = '';
		if(empty($this->_aCountry) == false && count($this->_aCountry) > 0){
            $countryArr =  array_unique($this->_aCountry, SORT_REGULAR);
            foreach ($countryArr as $country ){
                $xml .= '<payment_country>';
                $xml .= '<id>'. $country['COUNTRYID'] .'</id>';
			    $xml .= '<name>'.$country['COUNTRYNAME'] .'</name>';
                $xml .= '<iso_code>'.$country['ISO_CODE'] .'</iso_code>';
                $xml .= '<country_calling_code>'.$country['COUNTRY_CALLING_CODE'] .'</country_calling_code>';
                $xml .= '</payment_country>';
            }
        }
		return $xml;
	}

    public function toCurrencyAsXML() : string
    {
        $xml = '';
        if(empty($this->_aCurrency) == false && count($this->_aCurrency) > 0){
            $currencyArr =  array_unique($this->_aCurrency, SORT_REGULAR);
            foreach ($currencyArr as $currency){
                $xml .= '<payment_currency>';
                $xml .= '<id>'. $currency['CURRENCYID'] .'</id>';
                $xml .= '<name>'.$currency['CURRENCYCODE'] .'</name>';
                $xml .= '<decimals>'.$currency['DECIMALS'] .'</decimals>';
                $xml .= '</payment_currency>';
            }
        }

        return $xml;
    }

    /**
     * Produces a new instance of a Country Configuration Object.
     *
     * @param RDB $oDB Reference to the Database Object that holds the active connection to the mPoint Database
     * @param integer $clientId Unique ID for the Country the request is performed in
     * @return array
     */
	public static function produceConfig(RDB $oDB, int $clientId) : array
	{
        $aObj_Configurations = array();
        $aCountryConfig = array();
        $aCurrencyConfig = array();
        $aCurrencyArr = array();
        $aCountryArr = array();

		$sql = "SELECT DISTINCT ON (CCT.countryid, CCT.currencyid) CCT.countryid, CCT.currencyid, CNT.name as countryname, CUR.code AS currencycode, CUR.decimals as decimals, CNT.code as iso_code, CNT.country_calling_code as country_calling_code
				FROM Client".sSCHEMA_POSTFIX.".Countrycurrency_Tbl CCT
				INNER JOIN System".sSCHEMA_POSTFIX.".Country_tbl CNT ON CCT.countryid = CNT.id AND CNT.enabled = '1'
				INNER JOIN System".sSCHEMA_POSTFIX.".Currency_Tbl CUR ON CCT.currencyid = CUR.id AND CUR.enabled = '1'
				WHERE CCT.clientid = ". $clientId ." 
				AND CCT.enabled = '1'";
        try {
            $res = $oDB->query($sql);
            while ($RS = $oDB->fetchName($res)) {
                $aCountryArr['COUNTRYID']      = $RS['COUNTRYID'];
                $aCountryArr['COUNTRYNAME']    = $RS['COUNTRYNAME'];
                $aCountryArr['ISO_CODE']       = $RS['ISO_CODE'];
                $aCountryArr['COUNTRY_CALLING_CODE']  = $RS['COUNTRY_CALLING_CODE'];
                $aCurrencyArr['CURRENCYID']    = $RS['CURRENCYID'];
                $aCurrencyArr['CURRENCYCODE']  = $RS['CURRENCYCODE'];
                $aCurrencyArr['DECIMALS']      = $RS['DECIMALS'];
                $aCurrencyConfig[]             = $aCurrencyArr;
                $aCountryConfig[]              = $aCountryArr;
            }
            $aObj_Configurations[] = new ClientCountryCurrencyConfig($aCountryConfig, $aCurrencyConfig);
        }catch (SQLQueryException $e){
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
        return $aObj_Configurations;
	}
	

}
?>