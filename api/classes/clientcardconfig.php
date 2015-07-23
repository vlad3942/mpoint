<?php 
class ClientCardConfig extends BasicConfig
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
	 * @param 	integer $pspid 			ID of the PSP.	 	
	 */
	public function __construct($accessid, $cardid, $name, $countryid, $pspid)
	{
		parent::__construct($accessid, $name);	
		
		$this->_iCountryID = (integer)$countryid;	
		$this->_iCardID = (integer)$cardid;	
		$this->_iPSPID = (integer)$pspid;			
	}
	
	/**
	 * Returns the Card Holder's country ID.
	 *
	 * @return 	string
	 */
	public function getCountryID() { return $this->_iCountryID; }	
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
		$xml .= '<payment-method id="'.$this->getID().'" type-id="'.$this->getCardID().'" country-id="'.$this->getCountryID().'" psp-id="'.$this->getPSPID().'">'.htmlspecialchars($this->getName(), ENT_NOQUOTES).'</payment-method>';

		return $xml;
	}
	
	public static function produceConfig(RDB $oDB, $cardid, $clientid)
	{
		$sql = "SELECT CCA.id AS cardaccessid, CA.id AS id, CA.name AS name, CL.countryid AS countryid, CCA.pspid AS pspid		
				FROM Client". sSCHEMA_POSTFIX .".Client_Tbl CL 
				INNER JOIN Client". sSCHEMA_POSTFIX .".CardAccess_Tbl CCA ON CL.id = CCA.clientid 
				INNER JOIN System.". sSCHEMA_POSTFIX ."Card_Tbl CA ON CCA.cardid = CA.id
				WHERE CL.id = ". intval($clientid) ." AND CCA.cardid = ". intval($cardid) ." AND CL.enabled = '1';
		";				
		$RS = $oDB->getName($sql);	
	
		if(is_array($RS) === true && count($RS) > 0)
		{		
			return new ClientCardConfig($RS["CARDACCESSID"], $RS["ID"],$RS["NAME"],$RS["COUNTRYID"], $RS["PSPID"]);
		}
		
	}
	
	public static function produceConfigurations(RDB $oDB, $clientid)
	{			
		$sql = "SELECT CCA.cardid AS id, CL.countryid AS countryid
				FROM Client". sSCHEMA_POSTFIX .".Client_Tbl CL 
				INNER JOIN Client". sSCHEMA_POSTFIX .".CardAccess_Tbl CCA ON CL.id = CCA.clientid				
				WHERE CL.id = ". intval($clientid) ." AND CL.enabled = '1';
		";		
		$aObj_Configurations = array();
		$res = $oDB->query($sql);
		while ($RS = $oDB->fetchName($res))
		{
			if ((is_array($RS) === true && count($RS) > 0) && !empty($RS["ID"]))
			{
				$aObj_Configurations[] = self::produceConfig($oDB, $RS["ID"], $clientid);
			}
		}
		
		return $aObj_Configurations;		
	}	
}
?>