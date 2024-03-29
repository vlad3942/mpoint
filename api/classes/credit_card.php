<?php
/**
 * The Credit Card sub-package provides methods for retrieving credit card data
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Payment
 * @subpackage CreditCard
 * @version 1.10
 */

/**
 *
 *
 */
class CreditCard extends EndUserAccount
{
	/**
	 * Data object with the Transaction InformaStion
	 *
	 * @var TxnInfo
	 */
	private $_obj_TxnInfo;
	/**
	 * Data object with the User Agent Profile for the customer's mobile device.
	 *
	 * @var UAProfile
	 */
	private $_obj_UA;

	/**
	 * Default Constructor
	 *
	 * @param	RDB $oDB			Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param	TranslateText $oDB 	Reference to the Text Translation Object for translating any text into a specific language
	 * @param	TxnInfo $oTI 		Reference to the Data object with the Transaction Information
	 * @param	UAProfile $oUA 		Reference to the data object with the User Agent Profile for the customer's mobile device
	 */
	public function __construct(RDB &$oDB, api\classes\core\TranslateText &$oTxt, TxnInfo &$oTI, UAProfile &$oUA=null)
	{
		parent::__construct($oDB, $oTxt, $oTI->getClientConfig() );

		$this->_obj_TxnInfo = $oTI;
		$this->_obj_UA = $oUA;

	}

	/**
	 * Fetch meta-data for all Credit Cards that are available to the Client.
	 * The card data is returned as an XML Document in the following format:
	 * 	<cards accountid="{UNIQUE ID FOR THE END-USER'S ACCOUNT}>
	 * 		<item id="{UNIQUE ID FOR THE CARD}" pspid="{UNIQUE ID FOR THE PSP THAT WILL AUTHORISE THE PAYMENT}">
	 *			<name>{CREDIT CARD NAME}</name>
	 *			<logo-width>{CALCUALTED WIDTH FOR THE LOGO}</logo-width>
	 *			<logo-height>{CALCUALTED HEIGHT FOR THE LOGO}</logo-height>
	 *			<account>{PSPS ACCOUNT}</account>
	 *			<subaccount>{PSP SUBACCOUNT}</subaccount>
	 *			<currency>{PSP'S CURRENCY THAT THE TRANSACTION WILL BE CHARGED IN}</currency>
	 *		</item>
	 *		<item id="{UNIQUE ID FOR THE CARD}" pspid="{UNIQUE ID FOR THE PSP THAT WILL AUTHORISE THE PAYMENT}">
	 *			<name>{CREDIT CARD NAME}</name>
	 *			<logo-width>{CALCUALTED WIDTH FOR THE LOGO}</logo-width>
	 *			<logo-height>{CALCUALTED HEIGHT FOR THE LOGO}</logo-height>
	 *			<account>{PSPS ACCOUNT}</account>
	 *			<subaccount>{PSP SUBACCOUNT}</subaccount>
	 *			<currency>{PSP'S CURRENCY THAT THE TRANSACTION WILL BE CHARGED IN}</currency>
	 *		</item>
	 * 		...
	 * 	</cards>
	 * Please note that if the Payment Service Provider (PSP) does not support sub-accounts or no sub-account has been
	 * configured for the PSP, the subaccount tag will contain -1.
	 *
	 * @see 	iCARD_LOGO_SCALE
	 *
	 * @param 	integer $id 	Unique Card ID that should be fetched
	 * @return 	Image
	 */
	public function getCards($amount, $aDiabledPMs = array(),$iRoute = null)
	{
		/* ========== Calculate Logo Dimensions Start ========== */
		if ( ($this->_obj_UA instanceof UAProfile) === true)
		{
			$iWidth = $this->_obj_UA->getWidth() * iCARD_LOGO_SCALE / 100;
			$iHeight = $this->_obj_UA->getHeight() * iCARD_LOGO_SCALE / 100;

			if ($iWidth / 180 > $iHeight / 115) { $fScale = $iHeight / 115; }
			else { $fScale = $iWidth / 180; }

			$iWidth = intval($fScale * 180);
			$iHeight = intval($fScale * 115);
		}
		else
		{
			$iWidth = 180;
			$iHeight = 115;
		}
		/* ========== Calculate Logo Dimensions End ========== */



		$res = $this->getCardsQuery($amount);

		$xml = '<cards accountid="'. $this->_obj_TxnInfo->getAccountID() .'">';
		while ($RS = $this->getDBConn()->fetchName($res) )
		{
			$aRS = array();
			// Transaction instantiated via SMS or "Card" is NOT Premium SMS
			if ($this->_obj_TxnInfo->getGoMobileID() > -1 || $RS["ID"] != 10)
			{
				// My Account
				if ($RS["ID"] == 11)
				{
					// Only use Stored Cards (e-money based prepaid account will be unavailable)
					if ( ($this->_obj_TxnInfo->getClientConfig()->getStoreCard()&1) == 1)
					{
						$sName = $this->getText()->_("Stored Cards");
					}
					else { $sName = str_replace("{CLIENT}", $this->_obj_TxnInfo->getClientConfig()->getName(), $this->getText()->_("My Account") ); }
				}
				else
				{
					$sName = $RS["NAME"];

					$sql = "SELECT min, \"max\"
							FROM System".sSCHEMA_POSTFIX.".CardPrefix_Tbl
							WHERE cardid = ". $RS["ID"];
//					echo $sql ."\n";
					$aRS = $this->getDBConn()->getAllNames($sql);
				}
				
				$pspId = '';
				
				if (is_null ( $iRoute )) {
					$pspId = $RS ["PSPID"];
				} else {
					$pspId = $iRoute;
				}
				// Construct XML Document with card data
                $enabled = true;
				if(in_array($RS['ID'], $aDiabledPMs) === true ) { $enabled = false; }
				// Construct XML Document with card data
				$xml .= '<item id="'. $RS["ID"] .'" type-id="'. $RS["ID"] .'" pspid="'.$pspId.'" min-length="'. $RS["MINLENGTH"] .'" max-length="'. $RS["MAXLENGTH"] .'" cvc-length="'. $RS["CVCLENGTH"] .'" state-id="'. $RS["STATEID"] .'" payment-type="'.$RS['PAYMENTTYPE'].'"' .' preferred="'.General::bool2xml($RS['PREFERRED']).'"'. ' enabled = "'.General::bool2xml($enabled).'"'. ' processor-type = "'. $RS['PSP_TYPE'].'" installment = "'. $RS['INSTALLMENT'].'" cvcmandatory = "'. General::bool2xml($RS['CVCMANDATORY']).'" walletid = "'. $RS['WALLETID'].'" dcc="'. var_export($RS["DCCENABLED"], true).'" >';
				$xml .= '<name>'. htmlspecialchars($sName, ENT_NOQUOTES) .'</name>';
				$xml .= '<logo-width>'. $iWidth .'</logo-width>';
				$xml .= '<logo-height>'. $iHeight .'</logo-height>';
				$xml .= '<account>'. $RS["ACCOUNT"] .'</account>';
				$xml .= '<subaccount>'. $RS["SUBACCOUNT"] .'</subaccount>';
				$xml .= '<currency>'. $RS["CURRENCY"] .'</currency>';
				$xml .= '<capture_type>'. $RS["CAPTURE_TYPE"] .'</capture_type>';
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
		}
		$xml .= '</cards>';

		return $xml;
	}

	public function getCardsQuery($amount, $typeid = null, $stateid = null, $walletid = null)
    {
        	$sql = 'SELECT DISTINCT C.position, C.id, C.name, C.minlength, C.maxlength, C.cvclength,
					PSP.id AS pspid, MA.name AS account, MSA.name AS subaccount, PC.name AS currency,
					CA.stateid, CA.position AS client_position, C.paymenttype, CA.preferred, CA.psp_type, CA.installment, CA.capture_type, SRLC.cvcmandatory, CA.walletid,CA.dccEnabled
				FROM ' . $this->_constDataSourceQuery() . '
				WHERE CA.clientid = ' . $this->_obj_TxnInfo->getClientConfig()->getID() . '
					AND A.id = ' . $this->_obj_TxnInfo->getClientConfig()->getAccountConfig()->getID() . '
					AND PC.currencyid = ' . $this->_obj_TxnInfo->getCurrencyConfig()->getID(). '
					AND PP.currencyid = ' . $this->_obj_TxnInfo->getCurrencyConfig()->getID(). '
					AND PP.amount IN (-1, ' . (int)$amount .")
					AND C.enabled = '1' AND (MA.stored_card = '0' OR MA.stored_card IS NULL)
					AND (CA.countryid = ". $this->_obj_TxnInfo->getCountryConfig()->getID() ." OR CA.countryid IS NULL) AND CA.enabled = '1'
					AND PSP.system_type NOT IN (".Constants::iPROCESSOR_TYPE_TOKENIZATION.",".Constants::iPROCESSOR_TYPE_PRE_FRAUD_GATEWAY. ",".Constants::iPROCESSOR_TYPE_POST_FRAUD_GATEWAY.')';
					if($typeid !== null)
					{
					    $sql .= ' AND C.ID =' . $typeid ;
					}
					if($stateid !== null)
					{
					    $sql .= ' AND stateid = ' . $stateid ;
					}
					if($walletid !== null)
					{
                        $sql .= ' AND coalesce(walletid,-1) = '. $walletid;
                    }
				$sql .= ' ORDER BY CA.position ASC NULLS LAST, C.position ASC, C.name ASC';

		$res = $this->getDBConn()->query($sql);
		return $res;

    }

    public function getCardObject($amount, $typeid = null, $stateid = null, $walletid = null)
    {
        $result = $this->getCardsQuery($amount, $typeid, $stateid, $walletid );
        $resultSet = $this->getDBConn()->fetchName($result);
        return $resultSet;
    }


    /*Fetches the tokenization configuration set for a Client and card type
    * @param	integer $iCardID 	Unique ID of the CardTypeUsed
    * @return 	string
   */

    public function getTokenizationRoute($iCardID)
    {
        $sql = "SELECT DISTINCT PSP.id AS pspid FROM ". $this->_constDataSourceQuery() .
				"WHERE CA.clientid = ". $this->_obj_TxnInfo->getClientConfig()->getID() ."
					AND A.id = ". $this->_obj_TxnInfo->getClientConfig()->getAccountConfig()->getID() ."
					AND PC.currencyid = ". $this->_obj_TxnInfo->getCurrencyConfig()->getID()."
					AND PP.currencyid = ". $this->_obj_TxnInfo->getCurrencyConfig()->getID()."					
					AND C.enabled = '1' 
					AND CA.countryid = ". $this->_obj_TxnInfo->getCountryConfig()->getID() ." AND CA.enabled = '1'
					AND CA.cardid = ".$iCardID."
					AND CA.psp_type = ". Constants::iPROCESSOR_TYPE_TOKENIZATION;

        //echo $sql ."\n";
        $RS = $this->getDBConn()->getName($sql);
        return $RS['PSPID'];
    }

    /*Fetches the fraud check configuration set for a Client and card type
    * @param	integer $iCardID 	Unique ID of the CardTypeUsed
    * @return 	string
   */
    public function getFraudCheckRoute($iCardID,$iFraudType = Constants::iPROCESSOR_TYPE_PRE_FRAUD_GATEWAY)
    {

        $sql = "SELECT DISTINCT PSP.id AS pspid,CA.POSITION FROM ". $this->_constDataSourceQuery() .
            "WHERE CA.clientid = ". $this->_obj_TxnInfo->getClientConfig()->getID() ."
					AND A.id = ". $this->_obj_TxnInfo->getClientConfig()->getAccountConfig()->getID() ."
					AND PC.currencyid = ". $this->_obj_TxnInfo->getCurrencyConfig()->getID()."
					AND PP.currencyid = ". $this->_obj_TxnInfo->getCurrencyConfig()->getID()."					
					AND C.enabled = '1' 
					AND (CA.countryid = ". $this->_obj_TxnInfo->getCountryConfig()->getID() ." OR CA.countryid IS null ) AND CA.enabled = '1'
					AND (CA.cardid = ".$iCardID." OR CA.cardid = 0)
					AND CA.psp_type = ". $iFraudType." order by CA.POSITION" ;

        //echo $sql ."\n";
        $res = $this->getDBConn()->query($sql);
        return $res;
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
				INNER JOIN System".sSCHEMA_POSTFIX.".PricePoint_Tbl PP ON CP.pricepointid = PP.id AND PC.currencyid = PP.currencyid AND PP.enabled = '1'
				LEFT OUTER JOIN Client".sSCHEMA_POSTFIX.".StaticRouteLevelConfiguration SRLC ON SRLC.cardaccessid = CA.id AND SRLC.enabled = '1'";
    }


//TODO Remove
    public function getCardConfigurationObject($amount, $cardTypeId, $routeId)
    {
       $sql = "SELECT DISTINCT C.position, C.id, C.name, C.minlength, C.maxlength, C.cvclength, R.providerid AS pspid, RC.capturetype as capture_type, RC.mid AS account, MSA.name AS subaccount, PC.name AS currency,
					C.paymenttype, SRLC.cvcmandatory, CA.dccenabled
                FROM Client".sSCHEMA_POSTFIX.".Routeconfig_Tbl RC
                    INNER JOIN Client".sSCHEMA_POSTFIX.".RouteCountry_Tbl RCON ON RC.id = RCON.routeconfigid AND RCON.enabled = '1'
                    INNER JOIN Client".sSCHEMA_POSTFIX.".RouteCurrency_Tbl RCUR ON RC.id = RCUR.routeconfigid AND RCUR.enabled = '1' 
                    INNER JOIN Client".sSCHEMA_POSTFIX.".Route_Tbl R ON RC.routeid = R.id AND R.clientid = ".$this->_obj_TxnInfo->getClientConfig()->getID()." AND R.enabled = '1'
                    INNER JOIN Client".sSCHEMA_POSTFIX.".Account_Tbl A ON R.clientid = A.clientid AND A.id = " . $this->_obj_TxnInfo->getClientConfig()->getAccountConfig()->getID() . " AND A.enabled = '1'
                    INNER JOIN Client".sSCHEMA_POSTFIX.".MerchantSubAccount_Tbl MSA ON A.id = MSA.accountid AND MSA.enabled = '1'
                    INNER JOIN System".sSCHEMA_POSTFIX.".PSP_Tbl PSP ON R.providerid = PSP.id AND MSA.pspid = PSP.id AND PSP.system_type NOT IN (".Constants::iPROCESSOR_TYPE_TOKENIZATION.",".Constants::iPROCESSOR_TYPE_PRE_FRAUD_GATEWAY.",".Constants::iPROCESSOR_TYPE_POST_FRAUD_GATEWAY.") AND PSP.enabled = '1'
                    INNER JOIN System".sSCHEMA_POSTFIX.".PSPCurrency_Tbl PC ON PSP.id = PC.pspid AND PC.currencyid = " . $this->_obj_TxnInfo->getCurrencyConfig()->getID(). " AND PC.enabled = '1'
                    INNER JOIN System".sSCHEMA_POSTFIX.".PSPCard_Tbl PCD ON PSP.id = PCD.pspid AND PCD.enabled = '1'
                    INNER JOIN System".sSCHEMA_POSTFIX.".Card_Tbl C ON C.id = PCD.cardid AND C.id = ".$cardTypeId." AND C.enabled = '1'
                    INNER JOIN System".sSCHEMA_POSTFIX.".CardPricing_Tbl CP ON C.id = CP.cardid AND CP.enabled = '1'
                    INNER JOIN System".sSCHEMA_POSTFIX.".PricePoint_Tbl PP ON CP.pricepointid = PP.id AND PC.currencyid = PP.currencyid AND PP.currencyid = " . $this->_obj_TxnInfo->getCurrencyConfig()->getID(). " AND PP.amount IN (-1, ".(int)$amount.") AND PP.enabled = '1'
                    LEFT OUTER JOIN Client".sSCHEMA_POSTFIX.".CardAccess_Tbl CA ON CA.cardid = C.id AND CA.clientid = ".$this->_obj_TxnInfo->getClientConfig()->getID()."
                    LEFT OUTER JOIN Client" . sSCHEMA_POSTFIX . ".StaticRouteLevelConfiguration SRLC ON SRLC.cardaccessid = CA.id AND SRLC.enabled = '1'
                WHERE RC.id = ".$routeId."
                AND (RCON.countryid = ".$this->_obj_TxnInfo->getCountryConfig()->getID()." OR RCON.countryid IS NULL)
                AND (RCUR.currencyid =".$this->_obj_TxnInfo->getCurrencyConfig()->getID()." OR RCUR.currencyid IS NULL)
                AND RC.enabled = '1'
                ORDER BY C.position ASC, C.name ASC";

                $result = $this->getDBConn()->getName($sql);
                return $result;
    }

    public function getCardConfigurationXML($RS)
    {

        $xml = '<cards accountid="'. $this->_obj_TxnInfo->getAccountID() .'">';
        if (is_array($RS) === true && count($RS) > 0) {
            $aRS = array();
            // Transaction instantiated via SMS or "Card" is NOT Premium SMS
            if ($this->_obj_TxnInfo->getGoMobileID() > -1 || $RS["ID"] != 10) {
                // My Account
                if ($RS["ID"] == 11) {
                    // Only use Stored Cards (e-money based prepaid account will be unavailable)
                    if (($this->_obj_TxnInfo->getClientConfig()->getStoreCard() & 1) == 1) {
                        $sName = $this->getText()->_("Stored Cards");
                    } else {
                        $sName = str_replace("{CLIENT}", $this->_obj_TxnInfo->getClientConfig()->getName(), $this->getText()->_("My Account"));
                    }
                } else {
                    $sql = "SELECT min, \"max\"
                        FROM System" . sSCHEMA_POSTFIX . ".CardPrefix_Tbl
                        WHERE cardid = " . $RS["ID"];
                    $aRS = $this->getDBConn()->getAllNames($sql);
                }

                if (empty($iProviderId) === true) {
                    $iProviderId = $RS ["PSPID"];
                }

                // Construct XML Document with card data
                $xml .= '<item id="' . $RS["ID"] . '" type-id="' . $RS["ID"] . '" pspid="' . $iProviderId . '" min-length="' . $RS["MINLENGTH"] . '" max-length="' . $RS["MAXLENGTH"] . '" cvc-length="' . $RS["CVCLENGTH"] . '" enabled = "' . General::bool2xml(true) . '"' . ' cvcmandatory = "' . General::bool2xml($RS['CVCMANDATORY']) . '" dcc="' . General::bool2xml($RS["DCCENABLED"]) . '" >';
                $xml .= '<name>' . htmlspecialchars($RS["NAME"], ENT_NOQUOTES) . '</name>';
                $xml .= '<account>' . $RS["ACCOUNT"] . '</account>';
                $xml .= '<subaccount>' . $RS["SUBACCOUNT"] . '</subaccount>';
                $xml .= '<currency>' . $RS["CURRENCY"] . '</currency>';
                $xml .= '<capture_type>'. $RS["CAPTURE_TYPE"] .'</capture_type>';
                if (is_array($aRS) === true && count($aRS) > 0) {
                    $xml .= '<prefixes>';
                    for ($i = 0; $i < count($aRS); $i++) {
                        $xml .= '<prefix>';
                        $xml .= '<min>' . $aRS[$i]["MIN"] . '</min>';
                        $xml .= '<max>' . $aRS[$i]["MAX"] . '</max>';
                        $xml .= '</prefix>';
                    }
                    $xml .= '</prefixes>';
                } else {
                    $xml .= '<prefixes />';
                }
                $xml .= '</item>';
            }
        }
        $xml .= '</cards>';

        return $xml;
    }

}
?>