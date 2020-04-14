<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anna Lagad
 * Copyright: Cellpoint Digital
 * Link: https://cellpointdigital.com/
 * Project: server
 * Package:
 * File Name:get_routes_config.php
 */

class ClientRoutesConfig extends BasicConfig
{
    /**
     * The unique ID of the contry the configuration is valid in or -1 for "ALL"
     *
     * @var integer
     */
    private $_iCountryId;
    /**
     * The unique ID of the current Payment Method (Card) state for the client
     *
     * @var integer
     */
    private $_iStateID;
    /**
     * The unique ID for the Payment Method unique ID
     *
     * @var integer
     */
    private $_iPaymentMethodID;
    /**
     * The unique ID of the Payment Service Provider (PSP) that will process payments for this Payment Method (Card)
     *
     * @var integer
     */
    private $_iPSPID;
    /**
     * Flag indicating whether the routing configuration is currently active
     *
     * @var boolean
     */
    private $_bEnabled;
    /**
     * The unique ID for the Payment type
     *
     * @var integer
     */
    private $_iCardType;
    /**
     * The unique ID of the currency for Payment
     *
     * @var integer
     */
    private $_iCurrencyId;

    /**
     * Default Constructor
     *
     * @param 	integer $id 		The unique ID for the client's Payment Method (Card) configuration
     * @param 	integer $pmid 		The unique ID for the Payment Method (Card) type
     * @param 	integer $name	 	The name of the Payment Method (Card)
     * @param 	integer $countryid	The unique ID of the contry the configuration is valid in. Pass -1 for "ALL"
     * @param 	integer $stateid	The unique ID of the current Payment Method (Card) state for the client
     * @param 	integer $pspid 		The unique ID of the Payment Service Provider (PSP) that will process payments for this Payment Method (Card)
     * @param 	boolean $enabled 	Flag indicating whether the routing configuration is currently active
     * @param 	integer $cardtype 	The unique ID for the Payment type
     * @param 	integer $currencyid The unique ID of the currency for Payment
     */
    public function __construct($id, $pmid, $name, $countryid, $stateid, $pspid, $enabled, $cardtype, $currencyid)
    {
        parent::__construct($id, $name);
        $this->_iPaymentMethodID = (integer) $pmid;
        $this->_iCountryId = (integer) $countryid;
        $this->_iStateID = (integer) $stateid;
        $this->_iPSPID = (integer) $pspid;
        $this->_bEnabled = (bool) $enabled;
        $this->_iCardType = $cardtype;
        $this->_iCurrencyId = $currencyid;
    }

    public function getCountryID() { return $this->_iCountryId; }
    public function getCurrencyID() { return $this->_iCurrencyId; }
    public function getStateID() { return $this->_iStateID; }
    public function getPSPID() { return $this->_iPSPID; }
    public function getPaymentMethodID() { return $this->_iPaymentMethodID; }
    public function isEnabled() { return $this->_bEnabled; }
    public function getCardType() { return $this->_iCardType; }

    public function toXML()
    {
        $xml = '<route>';
        $xml .= '<card_type_id>'.$this->getPaymentMethodID().'</card_type_id>';
	    $xml .= '<country_id>'.$this->getCountryID().'</country_id>';
	    $xml .= '<currency_id>'.$this->getCurrencyID().'</currency_id>';
	    $xml .= '<psp_id>'.$this->getPSPID().'</psp_id>';
	    $xml .= '<enabled>'.General::bool2xml($this->isEnabled()).'</enabled>';
	    $xml .= '<psp_type>'.$this->getCardType().'</psp_type>';
        $xml .= '</route>';

        return $xml;
    }

    /**
     * Produces a new instance of a client Routes Configuration Object.
     *
     * @param 	RDB $oDB 		Reference to the Database Object that holds the active connection to the mPoint Database
     * @param 	integer $id 	Unique ID for the static route the request is performed in
     * @return 	clientRoutescyConfig
     */
    public static function produceConfig(RDB $oDB, $id)
    {
        $sql = "SELECT DISTINCT CA.id, Coalesce(CA.countryid, -1) AS countryid, CA.stateid, CA.pspid, CA.enabled, CA.cardid, C.name, CA.psp_type AS paymenttype, CC.currencyid		
				FROM Client". sSCHEMA_POSTFIX .".CardAccess_Tbl CA
				INNER JOIN System". sSCHEMA_POSTFIX .".Card_Tbl C ON CA.cardid = C.id
				LEFT JOIN Client". sSCHEMA_POSTFIX .".countrycurrency_tbl CC ON CA.countryid = CC.countryid
				WHERE CA.id = ". intval($id);

        $RS = $oDB->getName($sql);

        if (is_array($RS) === true && count($RS) > 0)
        {
            return new ClientRoutesConfig($RS["ID"], $RS["CARDID"], $RS["NAME"], $RS["COUNTRYID"], $RS["STATEID"], $RS["PSPID"], $RS["ENABLED"], $RS['PAYMENTTYPE'], $RS['CURRENCYID']);
        }
        else { return null; }
    }

    /**
     * Produces a new instance of a client Routes Configuration Object.
     *
     * @param 	RDB $oDB 		             Reference to the Database Object that holds the active connection to the mPoint Database
     * @param 	integer $clientid 	         Unique ID for the client
     * @return 	array $aObj_Configurations   Static Routes Configuration
     */
    public static function produceConfigurations(RDB $oDB, $clientid)
    {
        $sql = "SELECT DISTINCT CA.id
				FROM Client". sSCHEMA_POSTFIX .".CardAccess_Tbl CA
				WHERE CA.clientid = ". intval($clientid);

        $aObj_Configurations = array();
        $res = $oDB->query($sql);
        while ($RS = $oDB->fetchName($res) )
        {
            $aObj_Configurations[] = self::produceConfig($oDB, $RS["ID"]);
        }

        return $aObj_Configurations;
    }



}