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
	 * All the associated PSPs with the client account
	 *
	 * @var array
	 */
	private $_aClientMerchantSubAccounts;

	/**
	 * Default Constructor
	 *
	 * @param 	integer $id 	Unique ID for the Account
	 * @param 	integer $clid 	Unique ID for the Client to whom the Account belongs
	 * @param 	string $name 	Name of the Account
	 * @param 	string $mob 	Mobile Number (MSISDN) for the account holder.
	 * @param 	string $mrk 	String indicating the markup language used to render the payment pages
	 */
	public function __construct($id, $clid, $name, $mob, $mrk, $cmsaccts= array())
	{
		parent::__construct($id, $name);

		$this->_iClientID = (integer) $clid;
		$this->_sMobile = trim($mob);

		$this->_sMarkupLanguage = trim($mrk);
		
		$this->_aClientMerchantSubAccounts = $cmsaccts;
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
	
	public function getClientMerchantSubAccounts(){ return $this->_aClientMerchantSubAccounts; }
	
	public function getClientMerchantSubAccountsToXML()
	{ 
		$returnXML = '<payment-service-providers>';
		foreach($this->_aClientMerchantSubAccounts as $merchantSubAccount)
		{
			if($merchantSubAccount instanceof ClientMerchantSubAccountConfig)
			{
				$returnXML .= $merchantSubAccount->toFullXML();
			}
		} 
		$returnXML .= '</payment-service-providers>';
		return $returnXML;
	}
	
	
	public function toXML()
	{
		$xml = '<account-config id="'. $this->getID() .'" client-id="'. $this->_iClientID .'">';
		$xml .= '<name>'. htmlspecialchars($this->getName(), ENT_NOQUOTES) .'</name>';
		$xml .= '<mobile>'. $this->_sMobile .'</mobile>';
		$xml .= '</account-config>';

		return $xml;
	}
	
	public static function produceConfig(RDB $oDB, $accountid, $clientid)
	{
		$sql = "SELECT name, mobile, markup 
				FROM Client". sSCHEMA_POSTFIX .".Account_Tbl				
				WHERE id = ". intval($accountid) .";
		";	
		$RS = $oDB->getName($sql);
		
		if(!empty($RS))
		{	
			$clientMerchantSubAccounts = ClientMerchantSubAccountConfig::produceConfigurations($oDB, $accountid, $clientid);
			
			return new AccountConfig($accountid, $clientid, $RS['NAME'], $RS['MOBILE'], $RS['MARKUP'], $clientMerchantSubAccounts);
		}
		
	}
	
	public static function produceConfigurations(RDB $oDB, $clientid)
	{			
		$sql = "SELECT A.id AS accountid			
				FROM Client". sSCHEMA_POSTFIX .".Client_Tbl CL 
				INNER JOIN Client". sSCHEMA_POSTFIX .".Account_Tbl A ON CL.id = A.clientid 				
				WHERE CL.id = ". intval($clientid) ." AND CL.enabled = '1';
		";
		//echo $sql ."\n";
		$aConfigurations = array();
		$res = $oDB->query($sql);
		while ($RS = $oDB->fetchName($res))
		{
			if (!empty($RS) && $RS['ACCOUNTID'] > 0)
			{
				$aConfigurations[] = self::produceConfig($oDB, $RS['ACCOUNTID'], $clientid);
			}
		}
		
		return $aConfigurations;		
	}
	
	public function toFullXML()
	{
		$xml = '<account>';		
		$xml .= '<name>'. htmlspecialchars($this->getName(), ENT_NOQUOTES) .'</name>';
		$xml .= '<markup>'. htmlspecialchars($this->getMarkupLanguage(), ENT_NOQUOTES).'</markup>';				
		$xml .= $this->getClientMerchantSubAccountsToXML();
		$xml .= '</account>';		
				
		return $xml;
	}
}
?>