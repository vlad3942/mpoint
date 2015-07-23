<?php 
class ClientMerchantAccountConfig extends BasicConfig
{	
	/**
	 * Client Merchant Account username used to communicate with the PSP
	 *
	 * @var string
	 */	
	private $_sUsername;
	/**
	 * Client Merchant Account password used to communicate with the PSP
	 *
	 * @var string
	 */
	private $_sPassword;
	/**
	 * PSP ID used for the transaction.
	 *
	 * @var integer
	 */
	private $_iPSPID;

	/**
	 * Default Constructor
	 *
	 * @param 	integer $id 			For Client Merchant Account.
	 * @param 	string $accname 		Merchant Account Name.
	 * @param 	string $username 		Merchant Account username.
	 * @param 	string $passwd	 		Merchant Account password.
	 * @param 	integer $pspid	 		ID of the PSP.	
	 */
	public function __construct($id, $accname, $username, $passwd, $pspid)
	{
		parent::__construct($id, $accname);
		
		$this->_sUsername = trim($username);
		$this->_sPassword = trim($passwd);
		$this->_iPSPID = (integer)$pspid;			
	}
	/**
	 * Returns the Client Merchant Account Username used to communicate with the PSP.
	 *
	 * @return 	string
	 */
	public function getUsername() { return $this->_sUsername; }
	/**
	 * Returns the Client Merchant Account Password used to communicate with the PSP.
	 *
	 * @return 	string
	 */
	public function getPassword() { return $this->_sPassword; }
	/**
	 * Returns the PSP ID used for the transaction.
	 *
	 * @return 	integer
	 */
	public function getPSPID() { return $this->_iPSPID; }	
	
	
	public function toXML()
	{
		$xml .= '<payment-service-provider id = "'.$this->getID().'" psp-id = "'.$this->getPSPID().'">';			
		$xml .= '<name>'. htmlspecialchars($this->getName(), ENT_NOQUOTES) .'</name>';
		$xml .= '<username>'. htmlspecialchars($this->getUsername(), ENT_NOQUOTES) .'</username>';
		$xml .= '<password>'. htmlspecialchars($this->getPassword(), ENT_NOQUOTES) .'</password>';							
		$xml .= '</payment-service-provider>';

		return $xml;
	}
	
	public static function produceConfig(RDB $oDB, $id)
	{
		$sql = "SELECT MA.id, MA.name, MA.username, MA.passwd, MA.pspid		
				FROM Client". sSCHEMA_POSTFIX .".MerchantAccount_Tbl MA  				
				WHERE MA.id = ". intval($id) .";
		";				
		$RS = $oDB->getName($sql);		
		if(is_array($RS) === true && count($RS) > 0)
		{		
			return new ClientMerchantAccountConfig($RS["ID"],$RS["NAME"],$RS["USERNAME"], $RS["PASSWD"], $RS["PSPID"]);
		}
		
	}
	
	public static function produceConfigurations(RDB $oDB, $clientid)
	{			
		$sql = "SELECT MA.id	
				FROM Client". sSCHEMA_POSTFIX .".Client_Tbl CL 
				INNER JOIN Client". sSCHEMA_POSTFIX .".MerchantAccount_Tbl MA ON CL.id = MA.clientid 				
				WHERE CL.id = ". intval($clientid) ." AND CL.enabled = '1';
		";
		//echo $sql ."\n";
		$aObj_Configurations = array();
		$res = $oDB->query($sql);
		while ($RS = $oDB->fetchName($res))
		{
			if ((is_array($RS) === true && count($RS) > 0) && (!empty($RS["ID"])))
			{
				$aObj_Configurations[] = self::produceConfig($oDB, $RS["ID"], $clientid);
			}
		}
		
		return $aObj_Configurations;		
	}	
}
?>