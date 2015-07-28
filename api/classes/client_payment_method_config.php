<?php 
class ClientPaymentMethodConfig extends BasicConfig
{
	/**
	 * The unique ID of the contry the configuration is valid in
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
	 * Default Constructor
	 *
	 * @param 	integer $id 		The unique ID for the client's Payment Method (Card) configuration
	 * @param 	integer $pmid 		The unique ID for the Payment Method (Card) type
	 * @param 	integer $name	 	The name of the Payment Method (Card)
	 * @param 	integer $countryid	The unique ID of the contry the configuration is valid in
	 * @param 	integer $stateid	The unique ID of the current Payment Method (Card) state for the client
	 * @param 	integer $pspid 		The unique ID of the Payment Service Provider (PSP) that will process payments for this Payment Method (Card) 	 	
	 */
	public function __construct($id, $pmid, $name, $countryid, $stateid, $pspid)
	{
		parent::__construct($id, $name);	
		$this->_iPaymentMethodID = (integer) $pmid;
		$this->_iCountryID = (integer) $countryid;	
		$this->_iStateID = (integer) $stateid;
		$this->_iPSPID = (integer) $pspid;			
	}
	
	public function getCountryID() { return $this->_iCountryID; }	
	public function getStateID() { return $this->_iStateID; }	
	public function getPSPID() { return $this->_iPSPID; }	
	public function getPaymentMethodID() { return $this->_iPaymentMethodID; }	
	
	public function toXML()
	{
		$xml = '<payment-method id="' . $this->getID() . '" type-id="' . $this->_iPaymentMethodID . '" state-id="' . $this->_iStateID .'"';
		if ($this->_iCountryID > 0) { $xml .= ' country-id="' . $this->_iCountryID . '"'; }
		$xml .= ' psp-id="' . $this->_iPSPID . '">';
		$xml .= htmlspecialchars($this->getName(), ENT_NOQUOTES); 
		$xml .= '</payment-method>';

		return $xml;
	}
	
	public static function produceConfig(RDB $oDB, $id)
	{
		$sql = "SELECT CA.id, CA.countryid, CA.stateid, CA.pspid, C.id AS cardid, C.name		
				FROM Client". sSCHEMA_POSTFIX .".CardAccess_Tbl CA
				INNER JOIN System.". sSCHEMA_POSTFIX ."Card_Tbl C ON CA.cardid = C.id AND C.enabled = '1'
				WHERE CA.id = ". intval($id) ." AND CA.enabled = '1'";
//		echo $sql .'\n';				
		$RS = $oDB->getName($sql);	
	
		if (is_array($RS) === true && count($RS) > 0)
		{		
			return new ClientPaymentMethodConfig($RS["ID"], $RS["CARDID"], $RS["NAME"], $RS["COUNTRYID"], $RS["STATEID"], $RS["PSPID"]);
		}
		else { return null; }
	}
	
	public static function produceConfigurations(RDB $oDB, $clientid)
	{			
		$sql = "SELECT id
				FROM Client". sSCHEMA_POSTFIX .".CardAccess_Tbl				
				WHERE clientid = ". intval($clientid) ." AND enabled = '1'";
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