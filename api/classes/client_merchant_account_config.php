<?php 
class ClientMerchantAccountConfig extends BasicConfig
{	
	/**
	 * Username for the client's Payment Service Provider configuration
	 *
	 * @var string
	 */	
	private $_sUsername;
	/**
	 * Password for the client's Payment Service Provider configuration
	 *
	 * @var string
	 */
	private $_sPassword;
	/**
	 * The unique ID of the Payment Service Provider that the configuration is valid for
	 *
	 * @var integer
	 */
	private $_iPSPID;
	/**
	 * Boolean flag indicating whether the configuration is only valid for processing payments made with a stored card
	 *
	 * @var boolean
	 */
	private $_bStoredCard;

	/**
	 * Default Constructor
	 *
	 * @param 	integer $id 			The unique ID for the client's Merchant Account configuration for the Payment Service Provider
	 * @param 	string $name 			Name (Merchant Code) for the client's Payment Service Provider configuration
	 * @param 	string $username 		Username for the client's Payment Service Provider configuration
	 * @param 	string $passwd	 		Password for the client's Payment Service Provider configuration
	 * @param 	integer $pspid	 		The unique ID of the Payment Service Provider that the configuration is valid for	
	 * @param 	integer $storedcard		Boolean flag indicating whether the configuration is only valid for processing payments made with a stored card
	 */
	public function __construct($id, $name, $username, $passwd, $pspid, $storedcard)
	{
		parent::__construct($id, $name);
		
		$this->_sUsername = trim($username);
		$this->_sPassword = trim($passwd);
		$this->_iPSPID = (integer) $pspid;
		$this->_bStoredCard = (boolean) $storedcard;			
	}
	public function getUsername() { return $this->_sUsername; }
	public function getPassword() { return $this->_sPassword; }
	public function getPSPID() { return $this->_iPSPID; }	
	public function isStoredCard() { return $this->_bStoredCard; }	
	
	public function toXML()
	{
		$xml = '<payment-service-provider id="' . $this->getID() . '" psp-id="' . $this->_iPSPID . '" stored-card="'. General::bool2xml($this->_bStoredCard) .'">';			
		$xml .= '<name>' . htmlspecialchars($this->getName(), ENT_NOQUOTES) . '</name>';
		if (strlen($this->_sUsername) > 0) { $xml .= '<username>' . htmlspecialchars($this->_sUsername, ENT_NOQUOTES) . '</username>'; }
		if (strlen($this->_sPassword) > 0) { $xml .= '<password>' . htmlspecialchars($this->_sPassword, ENT_NOQUOTES) . '</password>'; }							
		$xml .= '</payment-service-provider>';

		return $xml;
	}
	
	public static function produceConfig(RDB $oDB, $id)
	{
		$sql = "SELECT MA.id, MA.name, MA.username, MA.passwd, MA.pspid, MA.stored_card	
				FROM Client". sSCHEMA_POSTFIX .".MerchantAccount_Tbl MA  				
				WHERE MA.id = ". intval($id) ." AND MA.enabled = '1'";
		//echo $sql ."\n";				
		$RS = $oDB->getName($sql);		
		if(is_array($RS) === true && count($RS) > 0)
		{		
			return new ClientMerchantAccountConfig($RS["ID"], $RS["NAME"], $RS["USERNAME"], $RS["PASSWD"], $RS["PSPID"], $RS['STORED_CARD']);
		}
		else { return null; }
	}
	
	public static function produceConfigurations(RDB $oDB, $id)
	{			
		$sql = "SELECT MA.id	
				FROM Client". sSCHEMA_POSTFIX .".Client_Tbl CL 
				INNER JOIN Client". sSCHEMA_POSTFIX .".MerchantAccount_Tbl MA ON CL.id = MA.clientid 				
				WHERE CL.id = ". intval($id) ." AND CL.enabled = '1'";
		//echo $sql ."\n";
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