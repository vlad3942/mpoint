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
	 * The modified date is updated whenever any changes in record happens in merchantsubaccount_tbl.
	 * @var unknown
	 */
	
	private $_sModifiedDate;

	/**
	 * Default Constructor
	 *
	 * @param 	integer $id 		The unique ID for the client's Merchant Sub-Account configuration for the Payment Service Provider
	 * @param 	integer $accountid 	The unique ID for the Sub-Account to which the Payment Service Provider configuration belongs
	 * @param 	integer $pspid 		The unique ID of the Payment Service Provider that the configuration is valid for
	 * @param 	PSPConfig $obj_PSP 	The configuration for the Payment Service Provider
	 */
	public function __construct($id, $accountid, $pspid, $pspname, $modifiedDate)
	{
		parent::__construct($id, $pspname);

		$this->_iAccountID = (integer) $accountid;
		$this->_iPSPID = (integer) $pspid;
		$this->_sModifiedDate = $modifiedDate;
				
	}
	public function getAccountID() { return $this->_iAccountID; }
	public function getPSPID() { return $this->_iPSPID; }
	public function getModifiedDate() { return $this->_sModifiedDate; }
	
	public function toXML()
	{
		$xml = '<payment-service-provider id = "' . $this->getID() . '" psp-id = "' . intval($this->_iPSPID) . '">';			
		$xml .= '<name>' . htmlspecialchars($this->getName(), ENT_NOQUOTES) . '</name>';
		$xml .= '<modified-date>' . htmlspecialchars(str_replace(" ", "T", $this->_sModifiedDate), ENT_NOQUOTES) . '</modified-date>';
		$xml .= '</payment-service-provider>';				
		
		return $xml;
	}
	

	
	public static function produceConfigurations(RDB $oDB, $accountid)
	{
        $sql = "SELECT MSA.id, MSA.pspid, MSA.name, MSA.accountid, MSA.modified
				FROM Client". sSCHEMA_POSTFIX .".MerchantSubAccount_Tbl MSA
				WHERE accountid = ". (int)$accountid ." AND MSA.enabled = '1' ORDER BY modified DESC";
//		echo $sql ."\n";
        $aRS = $oDB->getAllNames($sql);
        $aObj_Configurations = array();

        if (is_array($aRS) === true && count($aRS) > 0)
        {
            foreach ($aRS as $RS)
            {
                $aObj_Configurations[] = new ClientMerchantSubAccountConfig($RS["ID"], $RS["ACCOUNTID"], $RS["PSPID"], $RS["NAME"], gmdate("Y-m-d H:i:sP", strtotime(substr($RS['MODIFIED'], 0, strpos($RS['MODIFIED'], ".") ) ) ));
            }
        }

		
		return $aObj_Configurations;		
	}	
}
?>