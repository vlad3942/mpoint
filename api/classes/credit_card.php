<?php
/**
 * The Credit Card sub-package provides methods for retrieving credit card data
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Payment
 * @subpackage CreditCard
 * @version 1.0
 */

/**
 * 
 *
 */
class CreditCard extends General
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
	public function __construct(RDB &$oDB, TranslateText &$oTxt, TxnInfo &$oTI, UAProfile &$oUA)
	{
		parent::__construct($oDB, $oTxt);
		
		$this->_obj_TxnInfo = $oTI;
		$this->_obj_UA = $oUA;

	}
	
	/**
	 * Fetch meta-data for all Credit Cards that are available to the Client.
	 * The card data is returned as an XML Document in the following format:
	 * 	<cards>
	 * 		<item id="{UNIQUE ID FOR THE CARD}" pspid="{UNIQUE ID FOR THE PSP THAT WILL AUTHORISE THE PAYMENT}">
	 *			<name>{CREDIT CARD NAME}</name>
	 *			<width>{CALCUALTED WIDTH FOR THE LOGO}</width>
	 *			<height>{CALCUALTED HEIGHT FOR THE LOGO}</height>
	 *			<account>{PSPS ACCOUNT}</account>
	 *			<subaccount>{PSP SUBACCOUNT}</subaccount>
	 *			<currency>{PSP'S CURRENCY THAT THE TRANSACTION WILL BE CHARGED IN}</currency>
	 *		</item>
	 *		<item id="{UNIQUE ID FOR THE CARD}" pspid="{UNIQUE ID FOR THE PSP THAT WILL AUTHORISE THE PAYMENT}">
	 *			<name>{CREDIT CARD NAME}</name>
	 *			<width>{CALCUALTED WIDTH FOR THE LOGO}</width>
	 *			<height>{CALCUALTED HEIGHT FOR THE LOGO}</height>
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
	public function getCards()
	{
		/* ========== Calculate Logo Dimensions Start ========== */
		$iWidth = $this->_obj_UA->getWidth() * iCARD_LOGO_SCALE / 100;
		$iHeight = $this->_obj_UA->getHeight() * iCARD_LOGO_SCALE / 100;
		
		if ($iWidth / 180 > $iHeight / 115) { $fScale = $iHeight / 115; }
		else { $fScale = $iWidth / 180; }
		
		$iWidth = intval($fScale * 180);
		$iHeight = intval($fScale * 115);
		/* ========== Calculate Logo Dimensions End ========== */
		
		$sql = "SELECT C.id, C.name,
					PSP.id AS pspid, MA.name AS account, MSA.name AS subaccount, PC.name AS currency
				FROM System.Card_Tbl C
				INNER JOIN Client.CardAccess_Tbl CA ON C.id = CA.cardid
				INNER JOIN Client.MerchantAccount_Tbl MA ON CA.clientid = MA.clientid
				INNER JOIN Client.Account_Tbl A ON CA.clientid = A.clientid AND A.enabled = true
				INNER JOIN Client.MerchantSubAccount_Tbl MSA ON A.id = MSA.accountid
				INNER JOIN System.PSP_Tbl PSP ON MA.pspid = PSP.id AND MSA.pspid = PSP.id AND PSP.enabled = true
				INNER JOIN System.PSPCurrency_Tbl PC ON PSP.id = PC.pspid
				WHERE CA.clientid = ". $this->_obj_TxnInfo->getClientConfig()->getID() ."
					AND A.id = ". $this->_obj_TxnInfo->getClientConfig()->getAccountConfig()->getID() ."
					AND PC.countryid = ". $this->_obj_TxnInfo->getClientConfig()->getCountryConfig()->getID() ."
					AND C.enabled = true
				ORDER BY C.position ASC, C.name ASC";
//		echo $sql ."\n";
		$res = $this->getDBConn()->query($sql);
		
		$xml = '<cards>';
		while ($RS = $this->getDBConn()->fetchName($res) )
		{
			// Construct XML Document with card data
			$xml .= '<item id="'. $RS["ID"] .'" pspid="'. $RS["PSPID"] .'">';
			$xml .= '<name>'. $RS["NAME"] .'</name>';
			$xml .= '<width>'. $iWidth .'</width>';
			$xml .= '<height>'. $iHeight .'</height>';
			$xml .= '<account>'. $RS["ACCOUNT"] .'</account>';
			$xml .= '<subaccount>'. $RS["SUBACCOUNT"] .'</subaccount>';
			$xml .= '<currency>'. $RS["CURRENCY"] .'</currency>';
			$xml .= '</item>';
		}
		$xml .= '</cards>';
		
		return $xml;
	}
}
?>