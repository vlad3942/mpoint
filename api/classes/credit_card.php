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
	public function __construct(RDB &$oDB, TranslateText &$oTxt, TxnInfo &$oTI, UAProfile &$oUA=null)
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
	public function getCards($amount)
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

		$sql = "SELECT C.id, C.name,
					PSP.id AS pspid, MA.name AS account, MSA.name AS subaccount, PC.name AS currency
				FROM System.Card_Tbl C
				INNER JOIN Client.CardAccess_Tbl CA ON C.id = CA.cardid
				INNER JOIN Client.MerchantAccount_Tbl MA ON CA.clientid = MA.clientid
				INNER JOIN Client.Account_Tbl A ON CA.clientid = A.clientid AND A.enabled = true
				INNER JOIN Client.MerchantSubAccount_Tbl MSA ON A.id = MSA.accountid
				INNER JOIN System.PSP_Tbl PSP ON MA.pspid = PSP.id AND MSA.pspid = PSP.id AND CA.pspid = PSP.id AND PSP.enabled = true
				INNER JOIN System.PSPCurrency_Tbl PC ON PSP.id = PC.pspid
				INNER JOIN System.PSPCard_Tbl PCD ON PSP.id = PCD.pspid AND C.id = PCD.cardid
				INNER JOIN System.CardPricing_Tbl CP ON C.id = CP.cardid
				INNER JOIN System.PricePoint_Tbl PP ON CP.pricepointid = PP.id AND PC.countryid = PP.countryid AND PP.enabled = true
				WHERE CA.clientid = ". $this->_obj_TxnInfo->getClientConfig()->getID() ."
					AND A.id = ". $this->_obj_TxnInfo->getClientConfig()->getAccountConfig()->getID() ."
					AND PC.countryid = ". $this->_obj_TxnInfo->getClientConfig()->getCountryConfig()->getID() ."
					AND PP.countryid = ". $this->_obj_TxnInfo->getClientConfig()->getCountryConfig()->getID() ."
					AND PP.amount IN (-1, ". intval($amount) .")
					AND C.enabled = true
				ORDER BY C.position ASC, C.name ASC";
//		echo $sql ."\n";
		$res = $this->getDBConn()->query($sql);
		
		$xml = '<cards accountid="'. $this->_obj_TxnInfo->getAccountID() .'">';
		while ($RS = $this->getDBConn()->fetchName($res) )
		{
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
				else { $sName = $RS["NAME"]; }
				// Construct XML Document with card data
				$xml .= '<item id="'. $RS["ID"] .'" pspid="'. $RS["PSPID"] .'">';
				$xml .= '<name>'. htmlspecialchars($sName, ENT_NOQUOTES) .'</name>';
				$xml .= '<logo-width>'. $iWidth .'</logo-width>';
				$xml .= '<logo-height>'. $iHeight .'</logo-height>';
				$xml .= '<account>'. $RS["ACCOUNT"] .'</account>';
				$xml .= '<subaccount>'. $RS["SUBACCOUNT"] .'</subaccount>';
				$xml .= '<currency>'. $RS["CURRENCY"] .'</currency>';
				$xml .= '</item>';
			}
		}
		$xml .= '</cards>';

		return $xml;
	}
}
?>