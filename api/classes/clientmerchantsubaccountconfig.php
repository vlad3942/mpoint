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
	 * Name of the PSP used by the client to make the payment.
	 *
	 * @var string
	 */
	private $_sPSPName;
	/**
	 * PSP object that is generated using the PSP id available in the merchant Sub account
	 *
	 * @var string
	 */
	private $_oPSPObj;

	/**
	 * Default Constructor
	 *
	 * @param 	integer $id 			For Merchant Sub Account.
	 * @param 	integer $acctid 		Parent account ID.
	 * @param 	integer $pspid 			ID of the PSP.
	 * @param 	string $pspname 		Name of the PSP.	
	 */
	public function __construct($id, $acctid, $pspid, $pspname, $objPSP)
	{
		parent::__construct($id, $pspname);

		$this->_iAccountID = (integer) $acctid;
		$this->_iPSPID = trim($pspid);
		$this->_sPSPName = trim($pspname);
		$this->_oPSPObj = $objPSP;		
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
	 * Returns the PSP name for the sub account.
	 *
	 * @return 	string
	 */
	public function getPSPName() { return $this->_sPSPName; }
/**
	 * Returns the PSP object for the sub account.
	 *
	 * @return 	PSPConfig
	 */
	public function getPSPObj() { return $this->_oPSPObj; }
	
	
	
	public function toFullXML()
	{
		$xml .= '<payment-service-provider id = "'.$this->getPSPObj()->getID().'">';			
		$xml .= '<name>'. htmlspecialchars($this->getPSPObj()->getName(), ENT_NOQUOTES) .'</name>';							
		$xml .= '</payment-service-provider>';

		return $xml;
	}
	
	public static function produceConfig(RDB $oDB, $msaid, $accountid, $clientid)
	{
		$sql = "SELECT MSA.id AS id, MSA.pspid AS pspid, MSA.name AS pspname 
				FROM Client". sSCHEMA_POSTFIX .".MerchantSubAccount_Tbl MSA				
				WHERE MSA.id = ". intval($msaid) ." AND MSA.accountid = ". intval($accountid) .";
		";					
		$RS = $oDB->getName($sql);		
		if(!empty($RS))
		{
			$obj_PSPConfig = PSPConfig::produceConfig($oDB, $clientid, $accountid, $RS['PSPID']);
			return new ClientMerchantSubAccountConfig($RS['id'], $accountid, $RS['PSPID'], $RS['PSPNAME'], $obj_PSPConfig);
		}
		
	}
	
	public static function produceConfigurations(RDB $oDB, $accountid, $clientid)
	{			
		$sql = "SELECT MSA.id AS id, MSA.accountid AS accountid, MSA.pspid AS pspid, MSA.name AS pspname		
				FROM Client". sSCHEMA_POSTFIX .".Client_Tbl CL 
				INNER JOIN Client". sSCHEMA_POSTFIX .".Account_Tbl A ON CL.id = A.clientid 
				INNER JOIN Client.MerchantSubAccount_Tbl MSA ON A.id = MSA.accountid				
				WHERE CL.id = ". intval($clientid) ." AND A.id = ".intval($accountid)." AND CL.enabled = '1';
		";
		//echo $sql ."\n";
		$msConfigurations = array();
		$res = $oDB->query($sql);
		while ($RS = $oDB->fetchName($res))
		{
			if (!empty($RS) && $RS['ACCOUNTID'] > 0)
			{
				$msConfigurations[] = self::produceConfig($oDB, $RS['ID'], $RS['ACCOUNTID'], $clientid);
			}
		}
		
		return $msConfigurations;		
	}	
}
?>