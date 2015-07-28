<?php 
class ClientMerchantSubAccountConfig extends BasicConfig
{
	/**
	 * Client Account ID
	 *
	 * @var integer
	 */
	private $_iAccountID;
	/**
	 * Client ID for the transaction
	 *
	 * @var integer
	 */	
	private $_iPSPID;	
	/**
	 * PSP object that is generated using the PSP id available in the merchant Sub account
	 *
	 * @var string
	 */
	private $_obj_PSP;

	/**
	 * Default Constructor
	 *
	 * @param 	integer $id 			For Merchant Sub Account.
	 * @param 	integer $acctid 		Parent account ID.
	 * @param 	integer $pspid 			ID of the PSP.
	 * @param 	PSPConfig $objPSP 		PSP Object that holds the PSP data of the merchnt sub account.	
	 */
	public function __construct($id, $acctid, $pspid, $pspname, $objPSP)
	{
		parent::__construct($id, $pspname);

		$this->_iAccountID = (integer) $acctid;
		$this->_iPSPID = trim($pspid);		
		$this->_obj_PSP = $objPSP;		
	}
	/**
	 * Returns the Parent account ID to which the PSP belongs to.
	 *
	 * @return 	integer
	 */
	public function getAccountID() { return $this->_iAccountID; }
	/**
	 * Returns the PSP ID for the sub account.
	 *
	 * @return 	integer
	 */
	public function getPSPID() { return $this->_iPSPID; }	
	/**
	 * Returns the PSP object for the sub account.
	 *
	 * @return 	PSPConfig
	 */
	public function getPSPConfig() { return $this->_obj_PSP; }
	
	public function toXML()
	{
		$xml  = '';
		if ( ($this->getPSPConfig() instanceof PSPConfig) == true)
		{
			$xml .= '<payment-service-provider id = "' . $this->getID() . '" psp-id = "' . $this->getPSPConfig()->getID() . '">';			
			$xml .= '<name>' . htmlspecialchars($this->getPSPConfig()->getName(), ENT_NOQUOTES) . '</name>';							
			$xml .= '</payment-service-provider>';				
		}
		
		return $xml;
	}
	
	public static function produceConfig(RDB $oDB, $id)
	{
		$sql = "SELECT MSA.id, MSA.pspid, MSA.name, A.id AS accountid, A.clientid 
				FROM Client". sSCHEMA_POSTFIX .".MerchantSubAccount_Tbl MSA
				INNER JOIN Client". sSCHEMA_POSTFIX .".Account_Tbl A ON MSA.accountid = A.id				
				WHERE MSA.id = ". intval($id) ." AND MSA.enabled = '1'";
//		echo $sql ."\n";					
		$RS = $oDB->getName($sql);		
		if (is_array($RS) === true && count($RS) > 0)
		{
			$obj_PSPConfig = PSPConfig::produceConfig($oDB, $RS["CLIENTID"], $RS["ACCOUNTID"], $RS["PSPID"]);
			return new ClientMerchantSubAccountConfig($RS["ID"], $RS["ACCOUNTID"], $RS["PSPID"], $RS["NAME"], $obj_PSPConfig);
		}
		else { return null; }
	}
	
	public static function produceConfigurations(RDB $oDB, $accountid)
	{			
		$sql = "SELECT id	
				FROM Client". sSCHEMA_POSTFIX .".MerchantSubAccount_Tbl				
				WHERE accountid = ". intval($accountid) ." AND enabled = '1'";		
//		echo $sql ."\n";
		$aObj_Configurations = array();
		$res = $oDB->query($sql);
		while ($RS = $oDB->fetchName($res))
		{
			if (is_array($RS) === true && count($RS) > 0 && $RS["ID"] > 0)
			{
				$aObj_Configurations[] = self::produceConfig($oDB, $RS["ID"]);
			}
		}
		
		return $aObj_Configurations;		
	}	
}
?>