<?php 
class ClientPaymentMethodConfig extends BasicConfig
{
	/**
	 * Card provider name.
	 *
	 * @var string
	 */
	private $_sName;
	/**
	 * Card Holder's country ID.
	 *
	 * @var integer
	 */	
	private $_iCountryID;
	/**
	 * Card Holder's state ID.
	 *
	 * @var integer
	 */	
	private $_iStateID;
	/**
	 * Client's access ID to the client.
	 *
	 * @var integer
	 */
	private $_iCardID;
	/**
	 * Card Holder's PSP ID.
	 *
	 * @var integer
	 */
	private $_iPSPID;	

	/**
	 * Default Constructor
	 *
	 * @param 	integer $accessid 		Clients Card access ID.
	 * @param 	integer $cardid 		Card ID.
	 * @param 	integer $name	 		Card issuer name.
	 * @param 	integer $countryid	 	Card holder country ID.
	 * @param 	integer $stateid	 	Card holder state ID.
	 * @param 	integer $pspid 			ID of the PSP.	 	
	 */
	public function __construct($accessid, $cardid, $name, $countryid, $stateid, $pspid)
	{
		parent::__construct($accessid, $name);	
		
		$this->_iCountryID = (integer)$countryid;	
		$this->_iStateID = (integer)$stateid;
		$this->_iCardID = (integer)$cardid;	
		$this->_iPSPID = (integer)$pspid;			
	}
	
	/**
	 * Returns the Card Holder's country ID.
	 *
	 * @return 	integer
	 */
	public function getCountryID() { return $this->_iCountryID; }	
	/**
	 * Returns the Card Holder's state ID.
	 *
	 * @return 	integer
	 */
	public function getStateID() { return $this->_iStateID; }	
	/**
	 * Returns the PSP ID used for the transaction.
	 *
	 * @return 	integer
	 */
	public function getPSPID() { return $this->_iPSPID; }	
	/**
	 * Returns the Card ID used for the transaction.
	 *
	 * @return 	integer
	 */
	public function getCardID() { return $this->_iCardID; }	
	
	
	
	public function toXML()
	{
		$xml = '';
		$xml .= '<payment-method id="' . $this->getID() . '" type-id="' . $this->getCardID() . '" state-id="' . $this->getStateID() . '" country-id="' . $this->getCountryID() . '" psp-id="' . $this->getPSPID() . '">';
		$xml .= htmlspecialchars($this->getName(), ENT_NOQUOTES); 
		$xml .= '</payment-method>';

		return $xml;
	}
	
	public static function produceConfig(RDB $oDB, $cardid, $clientid)
	{
		$sql = "SELECT CCA.id AS cardaccessid, CA.id AS id, CA.name AS name, CCA.countryid AS countryid, CCA.stateid AS stateid, CCA.pspid AS pspid		
				FROM Client". sSCHEMA_POSTFIX .".CardAccess_Tbl CCA
				INNER JOIN System.". sSCHEMA_POSTFIX ."Card_Tbl CA ON CCA.cardid = CA.id
				WHERE CCA.clientid = ". intval($clientid) ." AND CCA.cardid = ". intval($cardid) ." AND CA.enabled = '1'";
		//echo $sql .'\n';				
		$RS = $oDB->getName($sql);	
	
		if(is_array($RS) === true && count($RS) > 0)
		{		
			return new ClientPaymentMethodConfig($RS["CARDACCESSID"], $RS["ID"], $RS["NAME"], $RS["COUNTRYID"], $RS["STATEID"], $RS["PSPID"]);
		}
		
	}
	
	public static function produceConfigurations(RDB $oDB, $id)
	{			
		$sql = "SELECT CCA.cardid AS id
				FROM Client". sSCHEMA_POSTFIX .".Client_Tbl CL 
				INNER JOIN Client". sSCHEMA_POSTFIX .".CardAccess_Tbl CCA ON CL.id = CCA.clientid				
				WHERE CL.id = ". intval($id) ." AND CL.enabled = '1'";
		//echo $sql .'\n';			
		$aObj_Configurations = array();
		$res = $oDB->query($sql);
		while ($RS = $oDB->fetchName($res))
		{
			if (is_array($RS) === true && count($RS) > 0 && $RS["ID"] > 0)
			{
				$aObj_Configurations[] = self::produceConfig($oDB, $RS["ID"], $id);
			}
		}
		
		return $aObj_Configurations;		
	}	
}
?>