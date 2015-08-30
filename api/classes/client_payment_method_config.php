<?php 
class ClientPaymentMethodConfig extends BasicConfig
{
	/**
	 * The unique ID of the contry the configuration is valid in or -1 for "ALL"
	 *
	 * @var integer
	 */	
	private $_iCountryID;
	/**
	 * The unique ID of the current Payment Method (Card) state for the client
	 *
	 * @var integer
	 */	
	private $_iStateID;
	/**
	 * The unique ID for the Payment Method (Card) type
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
	 * Default Constructor
	 *
	 * @param 	integer $id 		The unique ID for the client's Payment Method (Card) configuration
	 * @param 	integer $pmid 		The unique ID for the Payment Method (Card) type
	 * @param 	integer $name	 	The name of the Payment Method (Card)
	 * @param 	integer $countryid	The unique ID of the contry the configuration is valid in. Pass -1 for "ALL"
	 * @param 	integer $stateid	The unique ID of the current Payment Method (Card) state for the client
	 * @param 	integer $pspid 		The unique ID of the Payment Service Provider (PSP) that will process payments for this Payment Method (Card) 	 	
	 */
	public function __construct($id, $pmid, $name, $countryid, $stateid, $pspid, $enabled)
	{
		parent::__construct($id, $name);	
		$this->_iPaymentMethodID = (integer) $pmid;
		$this->_iCountryID = (integer) $countryid;	
		$this->_iStateID = (integer) $stateid;
		$this->_iPSPID = (integer) $pspid;
		$this->_bEnabled = (bool) $enabled;
		// Set Defaults
		if ($this->_iCountryID <= 0) { $this->_iCountryID = -1; }
	}
	
	public function getCountryID() { return $this->_iCountryID; }	
	public function getStateID() { return $this->_iStateID; }	
	public function getPSPID() { return $this->_iPSPID; }	
	public function getPaymentMethodID() { return $this->_iPaymentMethodID; }
	public function isEnabled() { return $this->_bEnabled; }
	
	public function toXML()
	{
		$xml = '<payment-method id="'. $this->getID() .'" type-id="'. $this->_iPaymentMethodID .'" state-id="'. $this->_iStateID .'" country-id="'. $this->_iCountryID .'" psp-id="'. $this->_iPSPID .'" enabled="'. General::bool2xml($this->_bEnabled) .'">';
		$xml .= htmlspecialchars($this->getName(), ENT_NOQUOTES); 
		$xml .= '</payment-method>';

		return $xml;
	}
	
	public static function produceConfig(RDB $oDB, $id)
	{
		$sql = "SELECT DISTINCT CA.id, Coalesce(CA.countryid, -1) AS countryid, CA.stateid, CA.pspid, CA.enabled, C.id AS cardid, C.name		
				FROM Client". sSCHEMA_POSTFIX .".CardAccess_Tbl CA
				INNER JOIN System.". sSCHEMA_POSTFIX ."Card_Tbl C ON CA.cardid = C.id AND C.enabled = '1'
				INNER JOIN Client.". sSCHEMA_POSTFIX ."MerchantAccount_Tbl MA ON MA.clientid = CA.clientid AND MA.pspid = CA.pspid AND MA.enabled = '1'
				WHERE CA.id = ". intval($id);
//		echo $sql .'\n';				
		$RS = $oDB->getName($sql);	
	
		if (is_array($RS) === true && count($RS) > 0)
		{		
			return new ClientPaymentMethodConfig($RS["ID"], $RS["CARDID"], $RS["NAME"], $RS["COUNTRYID"], $RS["STATEID"], $RS["PSPID"], $RS["ENABLED"]);
		}
		else { return null; }
	}
	
	public static function produceConfigurations(RDB $oDB, $clientid)
	{			
		$sql = "SELECT DISTINCT CA.id
				FROM Client". sSCHEMA_POSTFIX .".CardAccess_Tbl CA
				INNER JOIN System.". sSCHEMA_POSTFIX ."Card_Tbl C ON CA.cardid = C.id AND C.enabled = '1'
				INNER JOIN Client.". sSCHEMA_POSTFIX ."MerchantAccount_Tbl MA ON MA.clientid = CA.clientid AND MA.pspid = CA.pspid AND MA.enabled = '1'
				WHERE CA.clientid = ". intval($clientid);
//		echo $sql .'\n';			
		$aObj_Configurations = array();
		$res = $oDB->query($sql);
		while ($RS = $oDB->fetchName($res) )
		{
			$aObj_Configurations[] = self::produceConfig($oDB, $RS["ID"]);
		}
		
		return $aObj_Configurations;		
	}	
}
?>