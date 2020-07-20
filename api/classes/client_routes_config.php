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

class ClientRoutesConfig
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
     * Default Constructor
     *
     * @param 	integer $pmid 		The unique ID for the Payment Method (Card) type
     * @param 	integer $countryid	The unique ID of the contry the configuration is valid in. Pass -1 for "ALL"
     * @param 	integer $stateid	The unique ID of the current Payment Method (Card) state for the client
     * @param 	integer $pspid 		The unique ID of the Payment Service Provider (PSP) that will process payments for this Payment Method (Card)
     * @param 	boolean $enabled 	Flag indicating whether the routing configuration is currently active
     * @param 	integer $cardtype 	The unique ID for the Payment type
     */
    public function __construct($pmid, $countryid, $stateid, $pspid, $enabled, $cardtype)
    {
        $this->_iPaymentMethodID = (integer) $pmid;
        $this->_iCountryId = (integer) $countryid;
        $this->_iStateID = (integer) $stateid;
        $this->_iPSPID = (integer) $pspid;
        $this->_bEnabled = (bool) $enabled;
        $this->_iCardType = $cardtype;
    }

    public function getCountryID() { return $this->_iCountryId; }
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
	    $xml .= '<psp_id>'.$this->getPSPID().'</psp_id>';
	    $xml .= '<enabled>'.General::bool2xml($this->isEnabled()).'</enabled>';
	    $xml .= '<psp_type>'.$this->getCardType().'</psp_type>';
        $xml .= '</route>';

        return $xml;
    }

    /**
     * Produces a new instance of a client Routes Configuration Object.
     *
     * @param 	RDB $oDB 		             Reference to the Database Object that holds the active connection to the mPoint Database
     * @param 	integer $clientid 	         Unique ID for the client
     * @return 	array $aObj_Configurations   Static Routes Configuration
     */
    public static function produceConfig(RDB $oDB, $clientid)
    {
        $sql = "SELECT Coalesce(CA.countryid, 0) AS countryid, CA.stateid, CA.pspid, CA.enabled, CA.cardid, CA.psp_type AS paymenttype		
				FROM Client". sSCHEMA_POSTFIX .".CardAccess_Tbl CA
				WHERE CA.clientid = ". intval($clientid);

        $res = $oDB->query($sql);
        $aObj_Configurations = array();
        while ($RS = $oDB->fetchName($res) )
        {
            $aObj_Configurations[] = new ClientRoutesConfig($RS["CARDID"], $RS["COUNTRYID"], $RS["STATEID"], $RS["PSPID"], $RS["ENABLED"], $RS['PAYMENTTYPE']);
        }

        return $aObj_Configurations;
    }



}