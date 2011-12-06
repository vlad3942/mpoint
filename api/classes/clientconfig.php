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
 * @subpackage ClientConfig
 * @version 1.10
 */

/**
 * Data class holding the Client Configuration as well as the client's default data fields including:
 * 	- logo-url
 * 	- css-url
 * 	- accept-url
 * 	- cancel-url
 * 	- callback-url
 *
 */
class ClientConfig extends BasicConfig
{
	/**
	 * ID of the Flow the Client's customers have to go through in order to complete the Payment Transaction
	 *
	 * @var integer
	 */
	private $_iFlowID;
	/**
	 * Configuration for the Account the Transaction will be associated with
	 *
	 * @var AccountConfig
	 */
	private $_obj_AccountConfig;
	/**
	 * Client's Username for GoMobile
	 *
	 * @var string
	 */
	private $_sUsername;
	/**
	 * Client's Password for GoMobile
	 *
	 * @var string
	 */
	private $_sPassword;
	/**
	 * Configuration for the Country the Client can process transactions in
	 *
	 * @var CountryConfig
	 */
	private $_obj_CountryConfig;
	/**
	 * Configuration for the Keyword the Client uses to send messages through
	 *
	 * @var KeywordConfig
	 */
	private $_obj_KeywordConfig;

	/**
	 * Absolute URL to the Client's Logo which will be displayed on all payment pages
	 *
	 * @var string
	 */
	private $_sLogoURL;
	/**
	 * Absolute URL to the CSS file that should be used to customising the payment pages
	 *
	 * @var string
	 */
	private $_sCSSURL;
	/**
	 * Absolute URL where the Customer should be returned to upon successfully completing the Transaction
	 *
	 * @var string
	 */
	private $_sAcceptURL;
	/**
	 * Absolute URL where the Customer should be returned to in case he / she cancels the Transaction midway
	 *
	 * @var string
	 */
	private $_sCancelURL;
	/**
	 * Absolute URL to the Client's Back Office where mPoint should send the Payment Status to
	 *
	 * @var string
	 */
	private $_sCallbackURL;
	/**
	 * Absolute URL to the Client's My Account Icon
	 *
	 * @var string
	 */
	private $_sIconURL;
	/**
	 * Max Amount an mPoint Transaction can cost the customer for the Client
	 *
	 * @var integer
	 */
	private $_iMaxAmount;
	/**
	 * The language that all payment pages should be rendered in by default for the Client
	 *
	 * @var string
	 */
	private $_sLanguage;
	/**
	 * Boolean Flag indicating whether mPoint should send out an SMS Receipt to the Customer by the Callback Module
	 * upon successful completion of the Payment
	 *
	 * @var boolean
	 */
	private $_bSMSReceipt;
	/**
	 * Boolean Flag indicating whether access to the E-Mail Receipt component should be enabled for customers
	 *
	 * @var boolean
	 */
	private $_bEmailReceipt;
	/**
	 * The method used by mPoint when performing a Callback to the Client.
	 * This can be one of the following:
	 * 	- mPoint, Callback is perfomed using mPoint's native protocol
	 * 	- PSP, Callback is performed using the PSP's protocol by re-constructing the request received from the PSP
	 *
	 * @see Callback::notifyClient()
	 *
	 * @var string
	 */
	private $_sMethod;

	/**
	 * Terms & Conditions for the Shop
	 *
	 * @var string
	 */
	private $_sTerms;
	/**
	 * Client mode in which all Transactions are Processed
	 * 	0. Production
	 * 	1. Test Mode with prefilled card Info
	 * 	2. Certification Mode
	 *
	 * @var integer
	 */
	private $_iMode;
	/**
	 * Boolean Flag indicating whether mPoint should use Auto Capture for the Client.
	 *
	 * @var boolean
	 */
	private $_bAutoCapture;
	/**
	 * Boolean Flag indicating whether mPoint should include the PSP's ID for the Payment in the Callback to the Client.
	 *
	 * @var boolean
	 */
	private $_bSendPSPID;
	/**
	 * Setting determining if / how the end-user's Card Info is stored:
	 * 	0. Off
	 * 	1. Client Only
	 * 	2. Global
	 *
	 * @var integer
	 */
	private $_iStoreCard;

	/**
	 * Default Constructor
	 *
	 * @param 	integer $id 		Unique ID for the Client in mPoint
	 * @param 	integer $fid 		ID of the Flow the Client's customers have to go through in order to complete the Payment Transaction
	 * @param 	string $name 		Client's name in mPoint
	 * @param 	AccountConfig $oAC 	Configuration for the Account the Transaction will be associated with
	 * @param 	string $un 			Client's Username for GoMobile
	 * @param 	string $pw 			Client's Password for GoMobile
	 * @param 	CountryConfig $oCC 	Configuration for the Country the Client can process transactions in
	 * @param 	KeywordConfig $oKC 	Configuration for the Keyword the Client uses to send messages through
	 * @param 	string $lurl 		Absolute URL to the Client's Logo which will be displayed on all payment pages
	 * @param 	string $cssurl 		Absolute URL to the CSS file that should be used to customising the payment pages
	 * @param 	string $aurl 		Absolute URL where the Customer should be returned to upon successfully completing the Transaction
	 * @param 	string $curl 		Absolute URL where the Customer should be returned to in case he / she cancels the Transaction midway
	 * @param 	string $cburl 		Absolute URL to the Client's Back Office where mPoint should send the Payment Status to
	 * @param 	string $iurl 		Absolute URL to the Client's My Account Icon
	 * @param 	string $ma 			Max Amount an mPoint Transaction can cost the customer for the Client
	 * @param 	string $l 			The language that all payment pages should be rendered in by default for the Client
	 * @param 	boolean $sms 		Boolean Flag indicating whether mPoint should send out an SMS Receipt to the Customer upon successful completion of the Payment
	 * @param 	boolean $email		Boolean Flag indicating whether access to the E-Mail Receipt component should be enabled for customers
	 * @param 	string $mtd			The method used by mPoint when performing a Callback to the Client
	 * @param 	string $terms 		Terms & Conditions for the Shop
	 * @param 	integer $m 			Client mode: 0 = Production, 1 = Test Mode with prefilled card Info, 2 = Certification Mode
	 * @param 	boolean $ac			Boolean Flag indicating whether Auto Capture should be used for the transactions
	 * @param 	boolean $sp			Boolean Flag indicating whether the PSP's ID for the Payment should be included in the Callback
	 */
	public function __construct($id, $name, $fid, AccountConfig &$oAC, $un, $pw, CountryConfig &$oCC, KeywordConfig &$oKC, $lurl, $cssurl, $aurl, $curl, $cburl, $iurl, $ma, $l, $sms, $email, $mtd, $terms, $m, $ac, $sp, $sc)
	{
		parent::__construct($id, $name);

		$this->_iFlowID = (integer) $fid;

		$this->_obj_AccountConfig = $oAC;
		$this->_sUsername = trim($un);
		$this->_sPassword = trim($pw);
		$this->_obj_CountryConfig = $oCC;
		$this->_obj_KeywordConfig = $oKC;

		$this->_sLogoURL = trim($lurl);
		$this->_sCSSURL = trim($cssurl);
		$this->_sAcceptURL = trim($aurl);
		$this->_sCancelURL = trim($curl);
		$this->_sCallbackURL = trim($cburl);
		$this->_sIconURL = trim($iurl);

		$this->_iMaxAmount = (integer) $ma;
		$this->_sLanguage = trim($l);

		$this->_bSMSReceipt = (bool) $sms;
		$this->_bEmailReceipt = (bool) $email;
		$this->_sMethod = $mtd;

		$this->_sTerms = trim($terms);
		$this->_iMode = (integer) $m;
		$this->_bAutoCapture = (bool) $ac;
		$this->_bSendPSPID = (bool) $sp;
		$this->_iStoreCard = (integer) $sc;
	}

	/**
	 * Returns the ID of the Flow the Client's customers have to go through in order to complete the Payment Transaction
	 *
	 * @return 	integer
	 */
	public function getFlowID() { return $this->_iFlowID; }
	/**
	 * Returns the Configuration for the Account the Transaction will be associated with
	 *
	 * @return 	AccountConfig
	 */
	public function getAccountConfig() { return $this->_obj_AccountConfig; }
	/**
	 * Returns the Client's Username for GoMobile
	 *
	 * @return 	string
	 */
	public function getUsername() { return $this->_sUsername; }
	/**
	 * Returns the Client's Password for GoMobile
	 *
	 * @return 	string
	 */
	public function getPassword() { return $this->_sPassword; }
	/**
	 * Returns the Configuration for the Country the Client can process transactions in
	 *
	 * @return 	CountryConfig
	 */
	public function getCountryConfig() { return $this->_obj_CountryConfig; }
	/**
	 * Returns the Configuration for the Keyword the Client uses to send messages through
	 *
	 * @return 	KeywordConfig
	 */
	public function getKeywordConfig() { return $this->_obj_KeywordConfig; }
	/**
	 * Returns the Absolute URL to the Client's Logo which will be displayed on all payment pages
	 *
	 * @return 	string
	 */
	public function getLogoURL() { return $this->_sLogoURL; }
	/**
	 * Returns the Absolute URL to the CSS file that should be used to customising the payment pages
	 *
	 * @return 	string
	 */
	public function getCSSURL() { return $this->_sCSSURL; }
	/**
	 * Returns the Absolute URL where the Customer should be returned to upon successfully completing the Transaction
	 *
	 * @return 	string
	 */
	public function getAcceptURL() { return $this->_sAcceptURL; }

	/**
	 * Returns the Absolute URL where the Customer should be returned to in case he / she cancels the Transaction midway
	 *
	 * @return 	string
	 */
	public function getCancelURL() { return $this->_sCancelURL; }
	/**
	 * Returns the Absolute URL to the Client's Back Office where mPoint should send the Payment Status to
	 *
	 * @return 	string
	 */
	public function getCallbackURL() { return $this->_sCallbackURL; }
	/**
	 * Returns the Absolute URL to the Client's Icon for My Account
	 *
	 * @return 	string
	 */
	public function getIconURL() { return $this->_sIconURL; }
	/**
	 * Returns the Max Amount an mPoint Transaction can cost the customer for the Client
	 *
	 * @return 	integer
	 */
	public function getMaxAmount() { return $this->_iMaxAmount; }
	/**
	 * Returns the language that all payment pages should be rendered in by default for the Client
	 *
	 * @return 	string
	 */
	public function getLanguage() { return $this->_sLanguage; }
	/**
	 * Returns the Boolean Flag that indicates whether mPoint should send out an SMS Receipt to the Customer by the Callback Module
	 * upon successful completion of the Payment
	 *
	 * @return 	boolean
	 */
	public function smsReceiptEnabled() { return $this->_bSMSReceipt; }
	/**
	 * Boolean Flag indicating whether access to the E-Mail Receipt component should be enabled for customers
	 *
	 * @return 	boolean
	 */
	public function emailReceiptEnabled() { return $this->_bEmailReceipt; }
	/**
	 * Returns the method that mPoint uses to perform a Callback to the Client.
	 * This can be one of the following:
	 * 	- mPoint, Callback is perfomed using mPoint's native protocol
	 * 	- PSP, Callback is performed using the PSP's protocol by re-constructing the request received from the PSP
	 *
	 * @see Callback::notifyClient()
	 *
	 * @return 	string
	 */
	public function getMethod() { return $this->_sMethod; }
	/**
	 * Returns the Terms & Conditions for the Shop
	 *
	 * @return 	string
	 */
	public function getTerms() { return $this->_sTerms; }
	/**
	 * Returns the Client Mode in which all Transactions are Processed
	 * 	0. Production
	 * 	1. Test Mode with prefilled card Info
	 * 	2. Certification Mode
	 *
	 * @return 	integer
	 */
	public function getMode() { return $this->_iMode; }
	/**
	 * Boolean Flag indicating whether mPoint should use Auto Capture for the Client.
	 *
	 * @return 	boolean
	 */
	public function useAutoCapture() { return $this->_bAutoCapture; }
	/**
	 * Boolean Flag indicating whether mPoint should include the PSP's ID for the Payment in the Callback to the Client.
	 *
	 * @return 	boolean
	 */
	public function sendPSPID() { return $this->_bSendPSPID; }
	/**
	 * Returns the setting determining if / how the end-user's Card Info is stored:
	 * 	0. OFF - Cards are not stored 
	 * 	1. INVALID!!!
	 * 	2. Stored cards are available only for the specific client
	 * 	3. Only use Stored Cards and only make the cards available for the specific client (e-money based prepaid account will be unavailable)
	 * 	4. Stored cards are globally available (mPoint must be hosted by a Master Merchant)
	 * 	5. Only use Stored Cards but make them globally available (mPoint must be hosted by a Master Merchant and e-money based prepaid account will be unavailable)
	 *
	 * @return 	integer
	 */
	public function getStoreCard() { return $this->_iStoreCard; }

	public function toXML()
	{
		$xml = '<client-config id="'. $this->getID() .'" flow-id="'. $this->_iFlowID .'" mode="'. $this->_iMode .'">';
		$xml .= '<name>'. htmlspecialchars($this->getName(), ENT_NOQUOTES) .'</name>';
		$xml .= '<username>'. htmlspecialchars($this->getUsername(), ENT_NOQUOTES) .'</username>';
		$xml .= '<logo-url>'. htmlspecialchars($this->getLogoURL(), ENT_NOQUOTES) .'</logo-url>';
		$xml .= '<css-url>'. htmlspecialchars($this->getCSSURL(), ENT_NOQUOTES) .'</css-url>';
		$xml .= '<accept-url>'. htmlspecialchars($this->getAcceptURL(), ENT_NOQUOTES) .'</accept-url>';
		$xml .= '<cancel-url>'. htmlspecialchars($this->getCancelURL(), ENT_NOQUOTES) .'</cancel-url>';
		$xml .= '<callback-url>'. htmlspecialchars($this->getCallbackURL(), ENT_NOQUOTES) .'</callback-url>';
		$xml .= '<icon-url>'. htmlspecialchars($this->getIconURL(), ENT_NOQUOTES) .'</icon-url>';
		$xml .= '<sms-receipt>'. General::bool2xml($this->_bSMSReceipt) .'</sms-receipt>';
		$xml .= '<email-receipt>'. General::bool2xml($this->_bEmailReceipt) .'</email-receipt>';
		$xml .= '<auto-capture>'. General::bool2xml($this->_bAutoCapture) .'</auto-capture>';
		$xml .= '<store-card>'. $this->_iStoreCard .'</store-card>';
		$xml .= '</client-config>';

		return $xml;
	}

	/**
	 * Produces a new instance of a Client Configuration Object.
	 *
	 * @param 	RDB $oDB 		Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param 	integer $id 	Unique ID for the Client performing the request
	 * @param 	integer $acc 	Unique Account ID or Account Number that the transaction should be associated with, set to -1 to use the default account
	 * @param 	integer $kw 	Unique ID for the Keyword that all messages sent to the customer should belong to, defaults to -1 for client's default keyword.
	 * @return 	ClientConfig
	 */
	public static function produceConfig(RDB &$oDB, $id, $acc, $kw=-1)
	{
		$acc = (integer) $acc;
		$sql = "SELECT Cl.id AS clientid, Cl.name AS client, Cl.flowid, Cl.username, Cl.passwd,
					Cl.logourl, Cl.cssurl, Cl.accepturl, Cl.cancelurl, Cl.callbackurl, Cl.iconurl,
					Cl.smsrcpt, Cl.emailrcpt, Cl.method,
					Cl.maxamount, Cl.lang, Cl.terms,
					Cl.mode, Cl.auto_capture, Cl.send_pspid, Cl.store_card,
					C.id AS countryid,
					Acc.id AS accountid, Acc.name AS account, Acc.mobile, Acc.markup,
					KW.id AS keywordid, KW.name AS keyword, Sum(P.price) AS price
				FROM Client.Client_Tbl Cl
				INNER JOIN System.Country_Tbl C ON Cl.countryid = C.id AND C.enabled = true
				INNER JOIN Client.Account_Tbl Acc ON Cl.id = Acc.clientid AND Acc.enabled = true
				INNER JOIN Client.Keyword_Tbl KW ON Cl.id = KW.clientid AND KW.enabled = true
				LEFT OUTER JOIN Client.Product_Tbl P ON KW.id = P.keywordid AND P.enabled = true
				WHERE Cl.id = ". intval($id) ." AND Cl.enabled = true";
		// Use Default Keyword
		if ($kw == -1)
		{
			$sql .= " AND KW.standard = true";
		}
		// Use specific Keyword
		else { $sql .= " AND KW.id = ". intval($kw); }
		$sql .= " {ACCOUNT CLAUSE}
				GROUP BY Cl.id, Cl.name, Cl.flowid, Cl.username, Cl.passwd,
					Cl.logourl, Cl.cssurl, Cl.accepturl, Cl.cancelurl, Cl.callbackurl, Cl.iconurl,
					Cl.smsrcpt, Cl.emailrcpt, Cl.method,
					Cl.maxamount, Cl.lang, Cl.terms,
					Cl.mode, Cl.auto_capture, Cl.send_pspid, Cl.store_card,
					C.id,
					Acc.id, Acc.name, Acc.mobile, Acc.markup,
					KW.id, KW.name";
		// Use Default Account
		if ($acc == -1)
		{
			$sql .= "
					ORDER BY Acc.id ASC, KW.id ASC
					LIMIT 1";
		}
		// Use Account Number
		elseif ($acc < 1000)
		{
			$sql .= "
					ORDER BY Acc.id ASC, KW.id ASC
					LIMIT 1 OFFSET ". $acc;
		}
		// Use Account ID
		else
		{
			$sql = str_replace("{ACCOUNT CLAUSE}", " AND Acc.id = ". $acc, $sql);
			$sql .= "
					ORDER BY KW.id ASC
					LIMIT 1";
		}
		// Remove Account clause if it hasn't been already
		$sql = str_replace("{ACCOUNT CLAUSE}", "", $sql);

//		echo $sql ."\n";
		$RS = $oDB->getName($sql);

		$obj_CountryConfig = CountryConfig::produceConfig($oDB, $RS["COUNTRYID"]);
		$obj_AccountConfig = new AccountConfig($RS["ACCOUNTID"], $RS["CLIENTID"], $RS["ACCOUNT"], $RS["MOBILE"], $RS["MARKUP"]);
		$obj_KeywordConfig = new KeywordConfig($RS["KEYWORDID"], $RS["CLIENTID"], $RS["KEYWORD"], $RS["PRICE"]);
		
		return new ClientConfig($RS["CLIENTID"], utf8_decode($RS["CLIENT"]), $RS["FLOWID"], $obj_AccountConfig, $RS["USERNAME"], $RS["PASSWD"], $obj_CountryConfig, $obj_KeywordConfig, $RS["LOGOURL"], $RS["CSSURL"], $RS["ACCEPTURL"], $RS["CANCELURL"], $RS["CALLBACKURL"], $RS["ICONURL"], $RS["MAXAMOUNT"], $RS["LANG"], $RS["SMSRCPT"], $RS["EMAILRCPT"], $RS["METHOD"], utf8_decode($RS["TERMS"]), $RS["MODE"], $RS["AUTO_CAPTURE"], $RS["SEND_PSPID"], $RS["STORE_CARD"]);
	}
}
?>