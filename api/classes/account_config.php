<?php
/**
 * The Configuration package contains various data classes holding information such as:
 * 	- Configuration for the Country the transaction is processed in
 * 	- Configuration for the Client on whose behalf mPoint is processing the transaction
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Config
 * @subpackage AccountConfig
 * @version 1.0
 */

/**
 * Data class for hold the configuration for the Account a Transaction is associated with
 *
 */
class AccountConfig extends BasicConfig
{
	/**
	 * Unique ID for the Client to whom the Account belongs
	 *
	 * @var integer
	 */
	private $_iClientID;
	/**
	 * The Mobile Number (MSISDN) for the account holder.
	 *
	 * @var string
	 */
	private $_sMobile;
	/**
	 * String indicating the markup language used to render the payment pages.
	 * The value must match a folder in /templates/[TEMPLATE NAME]/
	 *
	 * @var string
	 */
	private $_sMarkupLanguage;
	/**
	 * List of sub-account configurations for each Payment Service Provider
	 *
	 * @var array
	 */
	private $_aObj_MerchantSubAccounts;

	/**
	 * Default Constructor
	 *
	 * @param 	integer $id 		Unique ID for the Account
	 * @param 	integer $clid 		Unique ID for the Client to whom the Account belongs
	 * @param 	string $name 		Name of the Account
	 * @param 	string $mob 		Mobile Number (MSISDN) for the account holder.
	 * @param 	string $mrk 		String indicating the markup language used to render the payment pages
	 * @param	array $aObj_MSAs	List of sub-account configurations for each Payment Service Provider
	 */
	public function __construct($id, $clid, $name, $mob, $mrk, $aObj_MSAs=array() )
	{
		parent::__construct($id, $name);

		$this->_iClientID = (integer) $clid;
		$this->_sMobile = trim($mob);

		$this->_sMarkupLanguage = trim($mrk);
		
		$this->_aObj_MerchantSubAccounts = $aObj_MSAs;
	}
	/**
	 * Returns the Unique ID for the Client to whom the Account belongs
	 *
	 * @return 	integer
	 */
	public function getClientID() { return $this->_iClientID; }
	/**
	 * Returns the Mobile Number (MSISDN) for the account holder.
	 *
	 * @return 	string
	 */
	public function getMobile() { return $this->_sMobile; }
	
	/**
	 * Returns the the markup language used to render the payment pages.
	 * The value must match a folder in /templates/[TEMPLATE NAME]/
	 *
	 * @return 	string
	 */
	public function getMarkupLanguage() { return $this->_sMarkupLanguage; }
	
	public function getMerchantSubAccounts(){ return $this->_aObj_MerchantSubAccounts; }
	
	public function toXML()
	{
		$xml = '<account-config id="'. $this->getID() .'" client-id="'. $this->_iClientID .'">';
		$xml .= '<name>'. htmlspecialchars($this->getName(), ENT_NOQUOTES) .'</name>';
		$xml .= '<mobile>'. $this->_sMobile .'</mobile>';
		$xml .= '</account-config>';

		return $xml;
	}
	
	public static function produceConfig(RDB $oDB, $id)
	{
		$sql = "SELECT clientid, name, mobile, markup 
				FROM Client". sSCHEMA_POSTFIX .".Account_Tbl				
				WHERE id = ". intval($id);
//		echo $sql ."\n";	
		$RS = $oDB->getName($sql);
		
		if (is_array($RS) === true && $RS["CLIENTID"] > 0)
		{	
			$aObj_MerchantSubAccounts = ClientMerchantSubAccountConfig::produceConfigurations($oDB, $id);
			
			return new AccountConfig($id, $RS["CLIENTID"], $RS["NAME"], $RS["MOBILE"], $RS["MARKUP"], $aObj_MerchantSubAccounts);
		}
		else { return null; }
	}
	
	public static function produceConfigurations(RDB $oDB, $id)
	{			
		$sql = "SELECT id			
				FROM Client". sSCHEMA_POSTFIX .".Account_Tbl 				
				WHERE clientid = ". intval($id) ." AND enabled = '1'";
//		echo $sql ."\n";
		$aConfigurations = array();
		$res = $oDB->query($sql);
		while ($RS = $oDB->fetchName($res) )
		{
			$aConfigurations[] = self::produceConfig($oDB, $RS["ID"]);
		}
		
		return $aConfigurations;		
	}
	
	public function toFullXML()
	{
		$xml = '<account-config id = "'. $this->getID().'">';
		$xml .= '<name>'. htmlspecialchars($this->getName(), ENT_NOQUOTES) .'</name>';
		$xml .= '<markup>'. htmlspecialchars($this->getMarkupLanguage(), ENT_NOQUOTES).'</markup>';
		$xml .= '<payment-service-providers>';
		foreach ($this->_aObj_MerchantSubAccounts as $obj_MerchantSubAccount)
		{
			if ( ($obj_MerchantSubAccount instanceof ClientMerchantSubAccountConfig) == true)
			{
				$xml .= $obj_MerchantSubAccount->toXML();
			}
		}
		$xml .= '</payment-service-providers>';
		$xml .= '</account-config>';
	
		return $xml;
	}
}
?>