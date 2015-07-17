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
	 * Card Holder's PSP ID.
	 *
	 * @var integer
	 */
	private $_iPSPID;	

	/**
	 * Default Constructor
	 *
	 * @param 	integer $id 			Card ID.
	 * @param 	integer $name	 		Card issuer name.
	 * @param 	integer $countryid	 	Card holder country ID.
	 * @param 	integer $pspid 			ID of the PSP.	 	
	 */
	public function __construct($id, $name, $countryid, $pspid)
	{
		parent::__construct($id, $name);
	
		$this->_sName = trim($name);
		$this->_iCountryID = $countryid;		
		$this->_iPSPID = $pspid;			
	}
	/**
	 * Returns the Card issuer name.
	 *
	 * @return 	string
	 */
	public function getCardName() { return $this->_sName; }
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
	
	
	public function toFullXML()
	{
		$xml .= '<card id="'.$this->getID().'" country-id="'.$this->getCountryID().'" psp-id="'.$this->getPSPID().'">'.htmlspecialchars($this->getCardName(), ENT_NOQUOTES).'</card>';

		return $xml;
	}
	
	public static function produceConfig(RDB $oDB, $cardid, $clientid)
	{
		$sql = "SELECT CA.id AS id, CA.name AS name, CL.countryid AS countryid, CCA.pspid AS pspid		
				FROM Client". sSCHEMA_POSTFIX .".Client_Tbl CL 
				INNER JOIN Client". sSCHEMA_POSTFIX .".CardAccess_Tbl CCA ON CL.id = CCA.clientid 
				INNER JOIN System.". sSCHEMA_POSTFIX ."Card_Tbl CA ON CCA.cardid = CA.id
				WHERE CL.id = ". intval($clientid) ." AND CCA.cardid = ". intval($cardid) ." AND CL.enabled = '1';
		";				
		$RS = $oDB->getName($sql);	
	
		if(!empty($RS))
		{		
			return new ClientCardConfig($RS['ID'],$RS['NAME'],$RS['COUNTRYID'], $RS['PSPID']);
		}
		
	}
	
	public static function produceConfigurations(RDB $oDB, $clientid)
	{			
		$sql = "SELECT CCA.cardid AS id, CL.countryid AS countryid
				FROM Client". sSCHEMA_POSTFIX .".Client_Tbl CL 
				INNER JOIN Client". sSCHEMA_POSTFIX .".CardAccess_Tbl CCA ON CL.id = CCA.clientid				
				WHERE CL.id = ". intval($clientid) ." AND CL.enabled = '1';
		";		
		$mConfigurations = array();
		$res = $oDB->query($sql);
		while ($RS = $oDB->fetchName($res))
		{
			if (!empty($RS['ID']))
			{
				$mConfigurations[] = self::produceConfig($oDB, $RS['ID'], $clientid);
			}
		}
		
		return $mConfigurations;		
	}	
}
?>