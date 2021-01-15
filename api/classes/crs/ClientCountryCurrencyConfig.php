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
     * @var integer
     */
	private $_aCountry;

    /**
     * Hold an array of client supported currency
     * @var integer
     */
	private $_aCurrency;

	/**
	 * Default Constructor
	 *
	 * @param 	integer $countryId 		Unique ID for the Country
	 * @param 	string $currencyId 		Unique ID for the Country currency
	 * @param 	string $countryName 	Hold client supported country name
	 * @param	integer $currencyCode	Hold client supported currency code
	 */
	public function __construct(?array $aCountryConfig = null, ?array $aCurrencyConfig = null)
	{
        $this->_aCountry = $aCountryConfig;
        $this->_aCurrency = $aCurrencyConfig;
	}

	public function toCountryAsXML() : string
	{
        $xml = '';
		if(empty($this->_aCountry) == false && count($this->_aCountry) > 0){
		    foreach ($this->_aCountry as $countryid => $name){
                $xml .= '<payment_country>';
                $xml .= '<id>'. $countryid .'</id>';
			    $xml .= '<name>'.$name .'</name>';
                $xml .= '</payment_country>';
            }
        }
		return $xml;
	}

    public function toCurrencyAsXML() : string
    {
        $xml = '';
        if(empty($this->_aCurrency) == false && count($this->_aCurrency) > 0){
            foreach ($this->_aCurrency as $currencyid => $currencyCode){
                $xml .= '<payment_currency>';
                $xml .= '<id>'. $currencyid .'</id>';
                $xml .= '<name>'.$currencyCode .'</name>';
                $xml .= '</payment_currency>';
            }
        }

        return $xml;
    }
	
	/**
	 * Produces a new instance of a Country Configuration Object.
	 *
	 * @param 	RDB $oDB 		Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param 	integer $id 	Unique ID for the Country the request is performed in
	 * @return 	ClientCountryCurrencyConfig
	 */
	public static function produceConfig(RDB &$oDB, $clientId) : array
	{
        $aObj_Configurations = array();
        $aCountryConfig = array();
        $aCurrencyConfig = array();

		$sql = "SELECT DISTINCT ON (CCT.countryid, CCT.currencyid) CCT.countryid, CCT.currencyid, CNT.name as countryname, CUR.code AS currencycode
				FROM Client".sSCHEMA_POSTFIX.".Countrycurrency_Tbl CCT
				INNER JOIN System".sSCHEMA_POSTFIX.".Country_tbl CNT ON CCT.countryid = CNT.id AND CNT.enabled = '1'
				INNER JOIN System".sSCHEMA_POSTFIX.".Currency_Tbl CUR ON CCT.currencyid = CUR.id AND CUR.enabled = '1'
				WHERE CCT.clientid = ". $clientId ." 
				AND CCT.enabled = '1'";
        try {
            $res = $oDB->query($sql);
            while ($RS = $oDB->fetchName($res)) {
                $aCountryConfig[$RS['COUNTRYID']] = $RS['COUNTRYNAME'];
                $aCurrencyConfig[$RS['CURRENCYID']] = $RS['CURRENCYCODE'];

            }
            $aObj_Configurations[] = new ClientCountryCurrencyConfig($aCountryConfig, $aCurrencyConfig);
        }catch (SQLQueryException $e){
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
        return $aObj_Configurations;
	}
	

}
?>