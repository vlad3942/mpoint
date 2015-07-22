<?php
/**
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package mConsole
 * @subpackage Config
 * @version 1.0
 */

/**
 * Data class for holding Payment Method Configurations
 *
 */
class PaymentMethodConfig extends BasicConfig
{
	/**
	 * The position at which the Payment Method (Card) should be placed
	 *
	 * @var integer
	 */
	private $_iPosition;
	/**
	 * The minimum length of a valid number for the Payment Method (Card)
	 *
	 * @var integer
	 */
	private $_iMinLength;
	/**
	 * The maximum length of a valid number for the Payment Method (Card)
	 *
	 * @var integer
	 */
	private $_iMaxLength;
	/**
	 * The length of a CVC number for the Payment Method (Card)
	 *
	 * @var integer
	 */
	private $_iCVCLength;
	/**
	 * List of card number prefixes which may be used to automatically detect the Payment Method (Card)
	 *
	 * @var array
	 */
	private $_aPrefixes = array();
	/**
	 * List of Payment Service Providers, which supports the Payment Method (Card)
	 *
	 * @var array
	 */
	private $_aPSPs = array();
	/**
	 * List of Countries in which the Payment Method (Card) is supported
	 *
	 * @var array
	 */
	private $_aCountries = array();

	/**
	 * Default constructor
	 * 
	 * @param integer $id			The unique ID for the Payment Method (Card)
	 * @param string $name			The name of the Payment Method (Card)
	 * @param integer $pos			The position at which the Payment Method (Card) should be placed
	 * @param integer $minlength	The minimum length of a valid number for the Payment Method (Card)
	 * @param integer $maxlength	The maximum length of a valid number for the Payment Method (Card)
	 * @param integer $cvclength	The length of a CVC number for the Payment Method (Card)
	 * @param array $prefixes		List of card number prefixes which may be used to automatically detect the Payment Method (Card)
	 * @param array $psps			List of Payment Service Providers, which supports the Payment Method (Card)
	 * @param array $countries		List of Countries in which the Payment Method (Card) is supported
	 */
	public function __construct($id, $name, $pos, $minlength, $maxlength, $cvclength, array $prefixes, array $psps, array $countries)
	{
		parent::__construct($id, $name);

		$this->_iPosition = (integer) $pos;
		$this->_iMinLength = (integer) $minlength;
		$this->_iMaxLength = (integer) $maxlength;
		$this->_iCVCLength = (integer) $cvclength;
		$this->_aPrefixes = $prefixes;
		$this->_aPSPs = $psps;
		$this->_aCountries = $countries;
		
		// Normalize Defaults
		if ($this->_iMinLength <= 0) { $this->_iMinLength = -1; }
		if ($this->_iMaxLength <= 0) { $this->_iMaxLength = -1; }
		if ($this->_iCVCLength <= 0) { $this->_iCVCLength = -1; }
	}

	public function getPosition() { return $this->_iPosition; }
	public function getMinLength() { return $this->_iMinLength; }
	public function getMaxLength() { return $this->_iMaxLength; }
	public function getCVCLength() { return $this->_iCVCLength; }
	public function getPrefixes() { return $this->_aPrefixes; }
	public function getPSPs() { return $this->_aPSPs; }
	public function getCountries() { return $this->_aCountries; }
	
	public function toXML()
	{
		$xml = '<payment-method-config id="'. $this->getID() .'" position="'. $this->_iPosition .'">';
		$xml .= '<name>'. htmlspecialchars($this->getName(), ENT_NOQUOTES) .'</name>';
		$xml .= '<min-length>'. $this->_iMinLength .'</min-length>';
		$xml .= '<max-length>'. $this->_iMaxLength .'</max-length>';
		$xml .= '<cvc-length>'. $this->_iCVCLength .'</cvc-length>';
		if (count($this->_aPrefixes) > 0)
		{
			$xml .= '<prefixes>';
			foreach ($this->_aPrefixes as $obj_Prefix)
			{
				$xml .= $obj_Prefix->toXML();
			}
			$xml .= '</prefixes>';
		}
		if (count($this->_aPSPs) > 0)
		{
			$xml .= '<payment-service-providers>';
			foreach ($this->_aPSPs as $id => $name)
			{
				$xml .= '<payment-service-provider id="'. intval($id) .'">'. htmlspecialchars($name, ENT_NOQUOTES) .'</payment-service-provider>';
			}
			$xml .= '</payment-service-providers>';
		}
		if (count($this->_aCountries) > 0)
		{
			$xml .= '<countries>';
			foreach ($this->_aCountries as $id => $name)
			{
				$xml .= '<country id="'. intval($id) .'">'. htmlspecialchars($name, ENT_NOQUOTES) .'</country>';
			}
			$xml .= '</countries>';
		}
		$xml .= '</payment-method-config>';
		
		return $xml;
	}
	
	/**
	 * Creates a new instance for the specified ID from the database
	 *
	 * @param 	RDB $oDB 		Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param 	integer $id 	The unique ID for the Payment Method that should be instantiated
	 * @return	PaymentMethodConfig|NULL
	 */
	public static function produceConfig(RDB $oDB, $id)
	{
		$sql = "SELECT id, name, position, Coalesce(minlength, -1) AS minlength, Coalesce(maxlength, -1) AS maxlength, Coalesce(cvclength, -1) AS cvclength
				FROM System". sSCHEMA_POSTFIX .".Card_Tbl
				WHERE id = ". intval($id) ." AND enabled = '1'
				ORDER BY id ASC";
//		echo $sql ."\n";
		$RS = $oDB->getName($sql);
		
		if (is_array($RS) === true && $RS["ID"] > 0)
		{
			$aPrefixes = PrefixConfig::produceConfigurations($oDB, $id);

			$aPSPs = array();
			$sql = "SELECT PSP.id, PSP.name
					FROM System". sSCHEMA_POSTFIX .".PSPCard_Tbl PC
					INNER JOIN System". sSCHEMA_POSTFIX .".PSP_Tbl PSP ON PC.pspid = PSP.id AND PSP.enabled = '1'
					WHERE PC.cardid = ". intval($id) ." AND PC.enabled = '1'";
//			echo $sql ."\n";
			$aRS = $oDB->getAllNames($sql);
			if (is_array($aRS) === true && count($aRS) > 0)
			{
				for ($i=0; $i<count($aRS); $i++)
				{
					$aPSPs[$aRS[$i]["ID"] ] = $aRS[$i]["NAME"];
				}
			}
			$aCountries = array();
			$sql = "SELECT C.id, C.name
					FROM System". sSCHEMA_POSTFIX .".CardPricing_Tbl CP
					INNER JOIN System". sSCHEMA_POSTFIX .".PricePoint_Tbl PP ON CP.pricepointid = PP.id AND PP.enabled = '1'
					INNER JOIN System". sSCHEMA_POSTFIX .".Country_Tbl C ON PP.countryid = C.id AND C.enabled = '1'
					WHERE CP.cardid = ". intval($id) ." AND CP.enabled = '1'";
//			echo $sql ."\n";
			$aRS = $oDB->getAllNames($sql);
			if (is_array($aRS) === true && count($aRS) > 0)
			{
				for ($i=0; $i<count($aRS); $i++)
				{
					$aCountries[$aRS[$i]["ID"] ] = $aRS[$i]["NAME"];
				}
			}
			return new PaymentMethodConfig($id, $RS["NAME"], $RS["POSITION"], $RS["MINLENGTH"], $RS["MAXLENGTH"], $RS["CVCLENGTH"], $aPrefixes, $aPSPs, $aCountries);
		}
		else { return null; }
	}
	
	/**
	 * Creates a list of al payment method configuration instances that are enabled in the database 
	 * 
	 * @param	RDB $oDB 		Reference to the Database Object that holds the active connection to the mPoint Database
	 * @return	array			List of Payment Method Configurations
	 */
	public static function produceAll(RDB $oDB)
	{
		$sql = "SELECT id
				FROM System". sSCHEMA_POSTFIX .".Card_Tbl
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