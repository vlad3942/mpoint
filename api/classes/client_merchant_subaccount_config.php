<?php 
class ClientMerchantSubAccountConfig extends BasicConfig
{
	/**
	 * The unique ID for the Sub-Account to which the Payment Service Provider configuration belongs
	 *
	 * @var integer
	 */
	private $_iAccountID;
	/**
	 * The unique ID of the Payment Service Provider that the configuration is valid for
	 *
	 * @var integer
	 */	
	private $_iPSPID;	
	/**
	 * The configuration for the Payment Service Provider
	 *
	 * @var string
	 */
	private $_obj_PSP;

	/**
	 * Default Constructor
	 *
	 * @param 	integer $id 		The unique ID for the client's Merchant Sub-Account configuration for the Payment Service Provider
	 * @param 	integer $accountid 	The unique ID for the Sub-Account to which the Payment Service Provider configuration belongs
	 * @param 	integer $pspid 		The unique ID of the Payment Service Provider that the configuration is valid for
	 * @param 	PSPConfig $obj_PSP 	The configuration for the Payment Service Provider
	 */
	public function __construct($id, $accountid, $pspid, $pspname)
	{
		parent::__construct($id, $pspname);

		$this->_iAccountID = (integer) $accountid;
		$this->_iPSPID = (integer) $pspid;	
				
	}
	public function getAccountID() { return $this->_iAccountID; }
	public function getPSPID() { return $this->_iPSPID; }	
	
	public function toXML()
	{
		$xml = '<payment-service-provider id = "' . $this->getID() . '" psp-id = "' . intval($this->_iPSPID) . '">';			
		$xml .= '<name>' . htmlspecialchars($this->getName(), ENT_NOQUOTES) . '</name>';							
		$xml .= '</payment-service-provider>';				
		
		return $xml;
	}
	
	public static function produceConfig(RDB $oDB, $id)
	{
		$sql = "SELECT MSA.id, MSA.pspid, MSA.name, A.id AS accountid, A.clientid 
				FROM Client". sSCHEMA_POSTFIX .".MerchantSubAccount_Tbl MSA
				INNER JOIN Client". sSCHEMA_POSTFIX .".Account_Tbl A ON MSA.accountid = A.id AND A.enabled = '1'			
				WHERE MSA.id = ". intval($id) ." AND MSA.enabled = '1'";
//		echo $sql ."\n";					
		$RS = $oDB->getName($sql);		
		if (is_array($RS) === true && count($RS) > 0)
		{			
			return new ClientMerchantSubAccountConfig($RS["ID"], $RS["ACCOUNTID"], $RS["PSPID"], $RS["NAME"]);
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
		while ($RS = $oDB->fetchName($res) )
		{
			$aObj_Configurations[] = self::produceConfig($oDB, $RS["ID"]);
		}
		
		return $aObj_Configurations;		
	}	
}
?>