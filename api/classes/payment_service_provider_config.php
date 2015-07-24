<?php
/**
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package mConsole
 * @subpackage Config
 * @version 1.00
 */

/**
 * Data class for holding Payment Service Provider Configurations
 *
 */
class PaymentServiceProviderConfig extends BasicConfig
{
	/**
	 * List of Payment Methods (Cards) which are supported by the Payment Service Provider 
	 *
	 * @var array
	 */
	private $_aPaymentMethods = array();
	/**
	 * List of Currencies for each Country that is supported by the Payment Service Provider
	 *
	 * @var array
	 */
	private $_aCurrencies = array();

	/**
	 * Default constructor
	 * 
	 * @param integer $id			The unique ID for the Payment Service Provider
	 * @param string $name			The name of the Payment Service Provider
	 * @param array $pms			List of Payment Methods (Cards) which are supported by the Payment Service Provider
	 * @param array $currencies		List of Currencies for each Country that is supported by the Payment Service Provider
	 */
	public function __construct($id, $name, array $pms, array $currencies)
	{
		parent::__construct($id, $name);

		$this->_aPaymentMethods = $pms;
		$this->_aCurrencies = $currencies;
	}

	public function getPaymentMethods() { return $this->_aPaymentMethods; }
	public function getCurrencies() { return $this->_aCurrencies; }
	
	public function toXML()
	{
		$xml = '<payment-service-provider-config id="'. $this->getID() .'">';
		$xml .= '<name>'. htmlspecialchars($this->getName(), ENT_NOQUOTES) .'</name>';
		if (count($this->_aPaymentMethods) > 0)
		{
			$xml .= '<payment-methods>';
			foreach ($this->_aPaymentMethods as $id => $name)
			{
				$xml .= '<payment-method id="'. intval($id) .'">'. htmlspecialchars($name, ENT_NOQUOTES) .'</payment-method>';
			}
			$xml .= '</payment-methods>';
		}
		if (count($this->_aCurrencies) > 0)
		{
			$xml .= '<currencies>';
			foreach ($this->_aCurrencies as $obj_Currency)
			{
				$xml .= $obj_Currency->toXML();
			}
			$xml .= '</currencies>';
		}
		$xml .= '</payment-service-provider-config>';
		
		return $xml;
	}
	
	/**
	 * Creates a new instance for the specified ID from the database
	 *
	 * @param 	RDB $oDB 		Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param 	integer $id 	The unique ID for the Payment Service Provider that should be instantiated
	 * @return	PaymentServiceProviderConfig|NULL
	 */
	public static function produceConfig(RDB $oDB, $id)
	{
		$sql = "SELECT id, name
				FROM System". sSCHEMA_POSTFIX .".PSP_Tbl
				WHERE id = ". intval($id) ." AND enabled = '1'";
//		echo $sql ."\n";
		$RS = $oDB->getName($sql);
		
		if (is_array($RS) === true && $RS["ID"] > 0)
		{
			$aCurrencies = PSPCurrencyConfig::produceConfigurations($oDB, $id);
			$aPaymentMethods = array();
			$sql = "SELECT C.id, C.name
					FROM System". sSCHEMA_POSTFIX .".PSPCard_Tbl PC
					INNER JOIN System". sSCHEMA_POSTFIX .".Card_Tbl C ON PC.cardid = C.id AND C.enabled = '1'
					WHERE PC.pspid = ". intval($id) ." AND PC.enabled = '1'
					ORDER BY C.id ASC";
//			echo $sql ."\n";
			$aRS = $oDB->getAllNames($sql);
			if (is_array($aRS) === true && count($aRS) > 0)
			{
				for ($i=0; $i<count($aRS); $i++)
				{
					$aPaymentMethods[$aRS[$i]["ID"] ] = $aRS[$i]["NAME"];
				}
			}
			return new PaymentServiceProviderConfig($id, $RS["NAME"], $aPaymentMethods, $aCurrencies);
		}
		else { return null; }
	}
	
	/**
	 * Creates a list of all Payment Service Provider configuration instances that are enabled in the database 
	 * 
	 * @param	RDB $oDB 		Reference to the Database Object that holds the active connection to the mPoint Database
	 * @return	array			List of Payment Method Configurations
	 */
	public static function produceAll(RDB $oDB)
	{
		$sql = "SELECT id
				FROM System". sSCHEMA_POSTFIX .".PSP_Tbl
				WHERE enabled = '1'
				ORDER BY id ASC";
//		echo $sql ."\n";
		$res = $oDB->query($sql);
		$aObj_Configurations = array();
		while ($RS = $oDB->fetchName($res) )
		{
			$aObj_Configurations[] = self::produceConfig($oDB, $RS["ID"]); 
		}
		
		return $aObj_Configurations;
	}
}
?>