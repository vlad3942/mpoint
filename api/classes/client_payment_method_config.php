<?php 
class ClientPaymentMethodConfig extends BasicConfig
{
	/**
	 * The unique ID of the contry the configuration is valid in or -1 for "ALL"
	 *
	 * @var integer
	 */	
	private $_iCountryID;
	/**
	 * The unique ID of the current Payment Method (Card) state for the client
	 *
	 * @var integer
	 */	
	private $_iStateID;
	/**
	 * The unique ID for the Payment Method (Card) type
	 *
	 * @var integer
	 */
	private $_iPaymentMethodID;
	/**
	 * The unique ID of the Payment Service Provider (PSP) that will process payments for this Payment Method (Card)
	 *
	 * @var integer
	 */
	private $_iPSPID;
	/**
	 * Flag indicating whether the routing configuration is currently active
	 *
	 * @var boolean
	 */
	private $_bEnabled;	

	/**
	 * Default Constructor
	 *
	 * @param 	integer $id 		The unique ID for the client's Payment Method (Card) configuration
	 * @param 	integer $pmid 		The unique ID for the Payment Method (Card) type
	 * @param 	integer $name	 	The name of the Payment Method (Card)
	 * @param 	integer $countryid	The unique ID of the contry the configuration is valid in. Pass -1 for "ALL"
	 * @param 	integer $stateid	The unique ID of the current Payment Method (Card) state for the client
	 * @param 	integer $pspid 		The unique ID of the Payment Service Provider (PSP) that will process payments for this Payment Method (Card) 	 	
	 */
	public function __construct($id, $pmid, $name, $countryid, $stateid, $pspid, $enabled)
	{
		parent::__construct($id, $name);	
		$this->_iPaymentMethodID = (integer) $pmid;
		$this->_iCountryID = (integer) $countryid;	
		$this->_iStateID = (integer) $stateid;
		$this->_iPSPID = (integer) $pspid;
		$this->_bEnabled = (bool) $enabled;
		// Set Defaults
		if ($this->_iCountryID <= 0) { $this->_iCountryID = -1; }
	}
	
	public function getCountryID() { return $this->_iCountryID; }	
	public function getStateID() { return $this->_iStateID; }	
	public function getPSPID() { return $this->_iPSPID; }	
	public function getPaymentMethodID() { return $this->_iPaymentMethodID; }
	public function isEnabled() { return $this->_bEnabled; }
	
	public function toXML()
	{
		$xml = '<payment-method id="'. $this->getID() .'" type-id="'. $this->_iPaymentMethodID .'" state-id="'. $this->_iStateID .'" country-id="'. $this->_iCountryID .'" psp-id="'. $this->_iPSPID .'" enabled="'. General::bool2xml($this->_bEnabled) .'">';
		$xml .= htmlspecialchars($this->getName(), ENT_NOQUOTES); 
		$xml .= '</payment-method>';

		return $xml;
	}
	
	public static function produceConfig(RDB $oDB, $id)
	{
		$sql = "SELECT DISTINCT CA.id, Coalesce(CA.countryid, -1) AS countryid, CA.stateid, CA.pspid, CA.enabled, C.id AS cardid, C.name		
				FROM Client". sSCHEMA_POSTFIX .".CardAccess_Tbl CA
				INNER JOIN System". sSCHEMA_POSTFIX .".Card_Tbl C ON CA.cardid = C.id AND C.enabled = '1'
				INNER JOIN Client". sSCHEMA_POSTFIX .".MerchantAccount_Tbl MA ON MA.clientid = CA.clientid AND MA.pspid = CA.pspid AND MA.enabled = '1'
				WHERE CA.id = ". intval($id);
//		echo $sql .'\n';				
		$RS = $oDB->getName($sql);	
	
		if (is_array($RS) === true && count($RS) > 0)
		{		
			return new ClientPaymentMethodConfig($RS["ID"], $RS["CARDID"], $RS["NAME"], $RS["COUNTRYID"], $RS["STATEID"], $RS["PSPID"], $RS["ENABLED"]);
		}
		else { return null; }
	}
	
	public static function produceConfigurations(RDB $oDB, $clientid)
	{			
		$sql = "SELECT DISTINCT CA.id
				FROM Client". sSCHEMA_POSTFIX .".CardAccess_Tbl CA
				INNER JOIN System". sSCHEMA_POSTFIX .".Card_Tbl C ON CA.cardid = C.id AND C.enabled = '1'
				INNER JOIN Client". sSCHEMA_POSTFIX .".MerchantAccount_Tbl MA ON MA.clientid = CA.clientid AND MA.pspid = CA.pspid AND MA.enabled = '1'
				WHERE CA.clientid = ". intval($clientid);
//		echo $sql .'\n';			
		$aObj_Configurations = array();
		$res = $oDB->query($sql);
		while ($RS = $oDB->fetchName($res) )
		{
			$aObj_Configurations[] = self::produceConfig($oDB, $RS["ID"]);
		}
		
		return $aObj_Configurations;		
	}	
	
	// public function getCards($amount, $aDiabledPMs = array(),$iRoute = null)
	public static function getClientAccountPaymentMethods(RDB $oDB, $obj_ClientConfig, $obj_CountryConfig, $amount)
	{
		/* ========== Logo Dimensions Start ========== */
		$iWidth = 180;
		$iHeight = 115;
		/* ========== Logo Dimensions End ========== */
		$iRoute = null;

		$sql = "SELECT DISTINCT C.position, C.id, C.name, C.minlength, C.maxlength, C.cvclength,
					PSP.id AS pspid, MA.name AS account, MSA.name AS subaccount, PC.name AS currency,
					CA.stateid, CA.position AS client_position, C.paymenttype, CA.preferred, CA.psp_type, CA.installment 
				FROM " . self::_constDataSourceQuery(). "
				WHERE CA.clientid = ". $obj_ClientConfig->getID() ."
					AND A.id = ". $obj_ClientConfig->getAccountConfig()->getID() ."
					AND PC.currencyid = ". $obj_CountryConfig->getCurrencyConfig()->getID()."
					AND PP.currencyid = ". $obj_CountryConfig->getCurrencyConfig()->getID()."
					AND PP.amount IN (-1, ". intval($amount) .")
					AND C.enabled = '1' AND (MA.stored_card = '0' OR MA.stored_card IS NULL)
					AND (CA.countryid = ". $obj_CountryConfig->getID() ." OR CA.countryid IS NULL) AND CA.enabled = '1'
					AND PSP.system_type <> ".Constants::iPROCESSOR_TYPE_TOKENIZATION."
				ORDER BY CA.position ASC NULLS LAST, C.position ASC, C.name ASC";
//		echo $sql ."\n";
		$res = $oDB->query($sql);

		$xml = '<cards accountid="'. $obj_ClientConfig->getAccountConfig()->getID() .'">';
		while ($RS = $oDB->fetchName($res) )
		{
			$aRS = array();
				
			$sName = $RS["NAME"];

			$sql = "SELECT min, \"max\"
					FROM System".sSCHEMA_POSTFIX.".CardPrefix_Tbl
					WHERE cardid = ". $RS["ID"];
//					echo $sql ."\n";
			$aRS = $oDB->getAllNames($sql);

			$pspId = '';
			if (is_null ( $iRoute )) {
				$pspId = $RS ["PSPID"];
			} else {
				$pspId = $iRoute;
			}
			
			// Construct XML Document with card data
			$enabled = true;
			//if(in_array($RS['ID'], $aDiabledPMs) === true ) { $enabled = false; }
			// Construct XML Document with card data
			$xml .= '<item id="'. $RS["ID"] .'" type-id="'. $RS["ID"] .'" pspid="'.$pspId.'" min-length="'. $RS["MINLENGTH"] .'" max-length="'. $RS["MAXLENGTH"] .'" cvc-length="'. $RS["CVCLENGTH"] .'" state-id="'. $RS["STATEID"] .'" payment-type="'.$RS['PAYMENTTYPE'].'"' .' preferred="'.General::bool2xml($RS['PREFERRED']).'"'. ' enabled = "'.General::bool2xml($enabled).'"'. ' processor-type = "'. $RS['PSP_TYPE'].'" installment = "'. $RS['INSTALLMENT'].'" >';
			$xml .= '<name>'. htmlspecialchars($sName, ENT_NOQUOTES) .'</name>';
			$xml .= '<logo-width>'. $iWidth .'</logo-width>';
			$xml .= '<logo-height>'. $iHeight .'</logo-height>';
			$xml .= '<account>'. $RS["ACCOUNT"] .'</account>';
			$xml .= '<subaccount>'. $RS["SUBACCOUNT"] .'</subaccount>';
			$xml .= '<currency>'. $RS["CURRENCY"] .'</currency>';
			if (is_array($aRS) === true && count($aRS) > 0)
			{
				$xml .= '<prefixes>';
				for ($i=0; $i<count($aRS); $i++)
				{
					$xml .= '<prefix>';
					$xml .= '<min>'. $aRS[$i]["MIN"] .'</min>';
					$xml .= '<max>'. $aRS[$i]["MAX"] .'</max>';
					$xml .= '</prefix>';
				}
				$xml .= '</prefixes>';
			}
			else { $xml .= '<prefixes />'; }
			$xml .= '</item>';
		}
		$xml .= '</cards>';
		return $xml;
	}

	private function _constDataSourceQuery()
    {
        return "System".sSCHEMA_POSTFIX.".Card_Tbl C
				INNER JOIN Client".sSCHEMA_POSTFIX.".CardAccess_Tbl CA ON C.id = CA.cardid 
				INNER JOIN Client".sSCHEMA_POSTFIX.".MerchantAccount_Tbl MA ON CA.clientid = MA.clientid
				INNER JOIN Client".sSCHEMA_POSTFIX.".Account_Tbl A ON CA.clientid = A.clientid AND A.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".MerchantSubAccount_Tbl MSA ON A.id = MSA.accountid
				INNER JOIN System".sSCHEMA_POSTFIX.".PSP_Tbl PSP ON MA.pspid = PSP.id AND MSA.pspid = PSP.id AND CA.pspid = PSP.id AND PSP.enabled = '1'
				INNER JOIN System".sSCHEMA_POSTFIX.".PSPCurrency_Tbl PC ON PSP.id = PC.pspid
				INNER JOIN System".sSCHEMA_POSTFIX.".PSPCard_Tbl PCD ON PSP.id = PCD.pspid AND C.id = PCD.cardid
				INNER JOIN System".sSCHEMA_POSTFIX.".CardPricing_Tbl CP ON C.id = CP.cardid
				INNER JOIN System".sSCHEMA_POSTFIX.".PricePoint_Tbl PP ON CP.pricepointid = PP.id AND PC.currencyid = PP.currencyid AND PP.enabled = '1'";
    }
}
?>