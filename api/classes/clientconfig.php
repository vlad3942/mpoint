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
	 * Constants for each URL Type
	 *
	 * @var integer
	 */
	const iCUSTOMER_IMPORT_URL = 1;
	const iAUTHENTICATION_URL = 2;
	const iNOTIFICATION_URL = 3;
	const iMESB_URL = 4;
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
	 * Absolute URL to the external system where customer data may be imported from.
	 * This is generally an existing e-Commerce site or a CRM system.
	 *
	 * @var string
	 */
	private $_sCustomerImportURL;
	/**
	 * Absolute URL to the external system where customer authenticated.
	 * This is generally an existing e-Commerce site or a CRM system.
	 *
	 * @var string
	 */
	private $_sAuthenticationURL;
	/**
	 * Absolute URL to the external system that needs To by Notify When Stored Cards changes.
	 *
	 * @var string
	 */
	private $_sNotificationURL;
	/**
	 * Absolute URL to the Mobile Enterprise Servicebus (MESB)
	 *
	 * @var string
	 */
	private $_sMESBURL;
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
	 * List of IP white-listed by The System
	 *
	 * @var array
	 */
	private $_aIPList;

	/**
	 * Boolean Flag indicating whether to include disabled/expired cards; default is false
	 *
	 * @var boolean
	 */
	private $_bShowAllCards;
	/**
	 * Max Amount of cards for a user on the  Client
	 *
	 * @var integer
	 */
	private $_iMaxCards;
	/**
	 * Set of binary flags which specifies how customers may be identified
	 * 1. Only Customer Reference
	 * 2. Only Mobile Number
	 * 3. Identify using either Customer Reference or Mobile Number
	 * 4. Only E-Mail Address
	 * 5. Identify using either Customer Reference or E-Mail Address
	 * 6. Identify using either Mobile or E-Mail Address
	 * 7. Identify using either Customer Reference, Mobile Number or E-Mail Address
	 * 8. Both Mobile Number & E-Mail Address must match
	 *
	 * @var integer
	 */
	private $_iIdentification;	
	
	/**
	 * Transaction time to leave in seconds 
	 *
	 * @var integer
	 */
	private $_iTransactionTTL;
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
	 * @param 	string $accurl 		Absolute URL where the Customer should be returned to upon successfully completing the Transaction
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
	 * @param 	string $ciurl 		Absolute URL to the external system where customer data may be imported from. This is generally an existing e-Commerce site or a CRM system
	 * @param 	string $aurl		Absolute URL to the external system where a customer may be authenticated. This is generally an existing e-Commerce site or a CRM system
	 * @param 	string $nurl		Absolute URL to the external system that needs to by Notify when Stored Cards changes.
	 * @param	array $aIPs			List of Whitelisted IP addresses in mPoint, pass an empty array to disable IP Whitelisting
	 * @param 	boolean $dc			Boolean Flag indicating whether to include disabled/expired cards; default is false
	 * @param 	integer $mc			The max number of cards a user can have on the Client, set to -1 for inifite
	 * @param 	integer $ident		Set of binary flags which specifies how customers may be identified
	 * @param 	integer $transttl	Transaction time to live value
	 */
	public function __construct($id, $name, $fid, AccountConfig $oAC, $un, $pw, CountryConfig $oCC, KeywordConfig $oKC, $lurl, $cssurl, $accurl, $curl, $cburl, $iurl, $ma, $l, $sms, $email, $mtd, $terms, $m, $ac, $sp, $sc, $ciurl, $aurl, $nurl, $murl, $aIPs, $dc, $mc=-1, $ident=7,$transttl)
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
		$this->_sAcceptURL = trim($accurl);
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

		$this->_sCustomerImportURL = trim($ciurl);
		$this->_sAuthenticationURL = trim($aurl);
		$this->_sNotificationURL = trim($nurl);
		$this->_sMESBURL = trim($murl);
		$this->_aIPList = $aIPs;
		$this->_bShowAllCards = (bool) $dc;
		$this->_iMaxCards = (integer) $mc;
		$this->_iIdentification = (integer) $ident;
		$this->_iTransactionTTL = (integer)$transttl;
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
	 * Absolute URL to the external system where customer data may be imported from.
	 * This is generally an existing e-Commerce site or a CRM system.
	 *
	 * @return 	string
	 */
	public function getCustomerImportURL() { return $this->_sCustomerImportURL; }
	/**
	 * Absolute URL to the external system where customer may be authenticated.
	 * This is generally an existing e-Commerce site or a CRM system.
	 *
	 * @return 	string
	 */
	public function getAuthenticationURL() { return $this->_sAuthenticationURL; }
	/**
	 * Absolute URL to the external system that needs To by Notify When Stored Cards changes.
	 *
	 * @return 	string
	 */
	public function getNotificationURL() { return $this->_sNotificationURL; }
	/**
	 * Absolute URL to the Mobile Enterprise Servicebus (MESB)
	 *
	 * @return 	string
	 */
	public function getMESBURL() { return $this->_sMESBURL; }
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
	 * Returns an Array of the ip-list for the client
	 *
	 * @return 	array
	 */
	public function getIPList() { return $this->_aIPList; }
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
	 * Returns the max amount of cards a enduser can have on a Client of this value is not set for the client -1 will be returned
	 * 	-1. Allow an infinite number of cards to be stored
	 * 	1+. The max number of cards a user may have stored
	 *
	 * @return 	integer
	 */
	public function getMaxCards() { return $this->_iMaxCards; }
	/**
	 * Set of binary flags which specifies how customers may be identified
	 * 1. Only Customer Reference
	 * 2. Only Mobile Number
	 * 3. Identify using either Customer Reference or Mobile Number
	 * 4. Only E-Mail Address
	 * 5. Identify using either Customer Reference or E-Mail Address
	 * 6. Identify using either Mobile or E-Mail Address
	 * 7. Identify using either Customer Reference, Mobile Number or E-Mail Address
	 * 8. Both Mobile Number & E-Mail Address must match
	 *
	 * @return 	integer
	 */
	public function getIdentification() { return $this->_iIdentification; }
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
	public function getSecret() { return sha1($this->getID() . $this->_sPassword); }

	public function showAllCards() { return $this->_bShowAllCards; }
	
	/**
	 * Returns the trnasaction time to live in seconds.	 
	 *
	 * @return 	integer
	 */
	public function getTransactionTTL() { return $this->_iTransactionTTL; }

	public function toXML()
	{
		$xml = '<client-config id="'. $this->getID() .'" flow-id="'. $this->_iFlowID .'" mode="'. $this->_iMode .'" max-cards="'. $this->_iMaxCards .'" identification="'. $this->_iIdentification .'">';
		$xml .= '<name>'. htmlspecialchars($this->getName(), ENT_NOQUOTES) .'</name>';
		$xml .= '<username>'. htmlspecialchars($this->getUsername(), ENT_NOQUOTES) .'</username>';
		$xml .= '<logo-url>'. htmlspecialchars($this->getLogoURL(), ENT_NOQUOTES) .'</logo-url>';
		$xml .= '<css-url>'. htmlspecialchars($this->getCSSURL(), ENT_NOQUOTES) .'</css-url>';
		$xml .= '<accept-url>'. htmlspecialchars($this->getAcceptURL(), ENT_NOQUOTES) .'</accept-url>';
		$xml .= '<cancel-url>'. htmlspecialchars($this->getCancelURL(), ENT_NOQUOTES) .'</cancel-url>';
		$xml .= '<callback-url>'. htmlspecialchars($this->getCallbackURL(), ENT_NOQUOTES) .'</callback-url>';
		$xml .= '<icon-url>'. htmlspecialchars($this->getIconURL(), ENT_NOQUOTES) .'</icon-url>';
		$xml .= '<customer-import-url>'. htmlspecialchars($this->_sCustomerImportURL, ENT_NOQUOTES) .'</customer-import-url>';
		$xml .= '<authentication-url>'. htmlspecialchars($this->_sAuthenticationURL, ENT_NOQUOTES) .'</authentication-url>';
		$xml .= '<notification-url>'. htmlspecialchars($this->_sNotificationURL, ENT_NOQUOTES) .'</notification-url>';
		$xml .= '<sms-receipt>'. General::bool2xml($this->_bSMSReceipt) .'</sms-receipt>';
		$xml .= '<email-receipt>'. General::bool2xml($this->_bEmailReceipt) .'</email-receipt>';
		$xml .= '<auto-capture>'. General::bool2xml($this->_bAutoCapture) .'</auto-capture>';
		$xml .= '<store-card>'. $this->_iStoreCard .'</store-card>';
		$xml .= '<ip-list>';
		foreach($this->_aIPList as $value)
		{
			$xml .= '<ip>'.$value.'</ip>';
		}
		$xml .= '</ip-list>';
		$xml .= '<show-all-cards>'. $this->_bShowAllCards .'</show-all-cards>';
		$xml .= '</client-config>';

		return $xml;
	}
	
	public function toFullXML($clientCardAccess, $clientPSPConfig , $clientPaymentMethods)
	{
		
		$xml = '<client-config id="'. $this->getID() .'" auto-capture = "'. General::bool2xml($this->_bAutoCapture).'" country-id = "'.$this->getCountryConfig()->getID().'" language = "'.$this->_sLanguage.'" sms-receipt = "'.General::bool2xml($this->_bSMSReceipt).'" email-receipt = "'.General::bool2xml($this->_bEmailReceipt).'" mode="'. $this->_iMode .'">';
		$xml .= '<name>'. htmlspecialchars($this->getName(), ENT_NOQUOTES) .'</name>';
		$xml .= '<username>'. htmlspecialchars($this->getUsername(), ENT_NOQUOTES) .'</username>';
		$xml .= '<password>'. htmlspecialchars($this->getPassword(), ENT_NOQUOTES) .'</password>';
		$xml .= '<max-amount country-id = "'.$this->getCountryConfig()->getID().'">'. htmlspecialchars($this->getMaxAmount(), ENT_NOQUOTES) .'</max-amount>';
		$xml .= '<cards store-card="'.$this->_iStoreCard.'" show-all-cards="'.General::bool2xml($this->_bShowAllCards).'" max-stored-cards="'.$this->_iMaxCards.'">';
		$xml .= $clientCardAccess;
		$xml .= '</cards>';
		$xml .= $clientPSPConfig;
		$xml .= '<urls>';
			$xml .= '<url type-id = "'.self::iCUSTOMER_IMPORT_URL.'">'.htmlspecialchars($this->_sCustomerImportURL, ENT_NOQUOTES).'</url>';
			$xml .= '<url type-id = "'.self::iAUTHENTICATION_URL.'">'.htmlspecialchars($this->_sAuthenticationURL, ENT_NOQUOTES).'</url>';
			$xml .= '<url type-id = "'.self::iNOTIFICATION_URL.'">'.htmlspecialchars($this->_sNotificationURL, ENT_NOQUOTES).'</url>';
			$xml .= '<url type-id = "'.self::iMESB_URL.'">'.htmlspecialchars($this->_sMESBURL, ENT_NOQUOTES).'</url>';
		$xml .= '</urls>';
		$xml .= '<keyword>'.$this->getKeywordConfig()->getName().'</keyword>';		
		$xml .= $clientPaymentMethods;		
		$xml .= '<callback-protocol send-psp-id = "'.General::bool2xml($this->sendPSPID()).'">'. htmlspecialchars($this->getCallbackURL(), ENT_NOQUOTES) .'</callback-protocol>';
		$xml .= '<identification>'. $this->_iIdentification .'</identification>';
		$xml .= '<transaction-time-to-live>'. $this->getTransactionTTL() .'</transaction-time-to-live>';						
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
	public static function produceConfig(RDB $oDB, $id, $acc=-1, $kw=-1)
	{
		$acc = (integer) $acc;
		$sql = "SELECT CL.id AS clientid, CL.name AS client, CL.flowid, CL.username, CL.passwd,
					CL.logourl, CL.cssurl, CL.accepturl, CL.cancelurl, CL.callbackurl, CL.iconurl,
					CL.smsrcpt, CL.emailrcpt, CL.method,
					CL.maxamount, CL.lang, CL.terms,
					CL.\"mode\", CL.auto_capture, CL.send_pspid, CL.store_card, CL.show_all_cards, CL.max_cards,
					CL.identification, CL.transaction_ttl,
					C.id AS countryid,
					Acc.id AS accountid, Acc.name AS account, Acc.mobile, Acc.markup,
					KW.id AS keywordid, KW.name AS keyword, Sum(P.price) AS price,
					U1.url AS customerimporturl, U2.url AS authurl, U3.url AS notifyurl, U4.url AS mesburl
				FROM Client". sSCHEMA_POSTFIX .".Client_Tbl CL
				INNER JOIN System". sSCHEMA_POSTFIX .".Country_Tbl C ON CL.countryid = C.id AND C.enabled = '1'
				INNER JOIN Client". sSCHEMA_POSTFIX .".Account_Tbl Acc ON CL.id = Acc.clientid AND Acc.enabled = '1'
				INNER JOIN Client". sSCHEMA_POSTFIX .".Keyword_Tbl KW ON CL.id = KW.clientid AND KW.enabled = '1'
				LEFT OUTER JOIN Client". sSCHEMA_POSTFIX .".Product_Tbl P ON KW.id = P.keywordid AND P.enabled = '1'
				LEFT OUTER JOIN Client". sSCHEMA_POSTFIX .".URL_Tbl U1 ON CL.id = U1.clientid AND U1.urltypeid = ". self::iCUSTOMER_IMPORT_URL ." AND U1.enabled = '1'
				LEFT OUTER JOIN Client". sSCHEMA_POSTFIX .".URL_Tbl U2 ON CL.id = U2.clientid AND U2.urltypeid = ". self::iAUTHENTICATION_URL ." AND U2.enabled = '1'
				LEFT OUTER JOIN Client". sSCHEMA_POSTFIX .".URL_Tbl U3 ON CL.id = U3.clientid AND U3.urltypeid = ". self::iNOTIFICATION_URL ." AND U3.enabled = '1'
				LEFT OUTER JOIN Client". sSCHEMA_POSTFIX .".URL_Tbl U4 ON CL.id = U4.clientid AND U4.urltypeid = ". self::iMESB_URL ." AND U4.enabled = '1'
				WHERE CL.id = ". intval($id) ." AND CL.enabled = '1'";
		// Use Default Keyword
		if ($kw == -1)
		{
			$sql .= " AND KW.standard = '1'";
		}
		// Use specific Keyword
		else { $sql .= " AND KW.id = ". intval($kw); }
		$sql .= "{ACCOUNT CLAUSE}
				GROUP BY CL.id, CL.name, CL.flowid, CL.username, CL.passwd,
					CL.logourl, CL.cssurl, CL.accepturl, CL.cancelurl, CL.callbackurl, CL.iconurl,
					CL.smsrcpt, CL.emailrcpt, CL.method,
					CL.maxamount, CL.lang, CL.terms,
					CL.\"mode\", CL.auto_capture, CL.send_pspid, CL.store_card, CL.show_all_cards, CL.max_cards,
					CL.identification, CL.transaction_ttl,
					C.id,
					Acc.id, Acc.name, Acc.mobile, Acc.markup,
					KW.id, KW.name,
					U1.url, U2.url, U3.url, U4.url";
		// Use Default Account
		if ($acc == -1)
		{
			$sql .= "
					ORDER BY Acc.id ASC, KW.id ASC";
		}
		// Use Account Number (Not supported if running on Oracle)
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
					ORDER BY KW.id ASC";
		}
		// Remove Account clause if it hasn't been already
		$sql = str_replace("{ACCOUNT CLAUSE}", "", $sql);
//		echo $sql ."\n";
		$RS = $oDB->getName($sql);

		$obj_CountryConfig = CountryConfig::produceConfig($oDB, $RS["COUNTRYID"]);
		$obj_AccountConfig = new AccountConfig($RS["ACCOUNTID"], $RS["CLIENTID"], $RS["ACCOUNT"], $RS["MOBILE"], $RS["MARKUP"]);
		$obj_KeywordConfig = new KeywordConfig($RS["KEYWORDID"], $RS["CLIENTID"], $RS["KEYWORD"], $RS["PRICE"]);

		$sql  = "SELECT ipaddress
				 FROM Client". sSCHEMA_POSTFIX .".IPAddress_Tbl
				 WHERE clientid = ". intval($id) ."";
//		echo $sql ."\n";
		$aRS = $oDB->getAllNames($sql);
		$aIPs = array();
		if (is_array($aRS) === true && count($aRS) > 0)
		{
			for ($i=0; $i<count($aRS); $i++)
			{
				$aIPs[] = $aRS[$i]["IPADDRESS"];
			}
		}

		return new ClientConfig($RS["CLIENTID"], $RS["CLIENT"], $RS["FLOWID"], $obj_AccountConfig, $RS["USERNAME"], $RS["PASSWD"], $obj_CountryConfig, $obj_KeywordConfig, $RS["LOGOURL"], $RS["CSSURL"], $RS["ACCEPTURL"], $RS["CANCELURL"], $RS["CALLBACKURL"], $RS["ICONURL"], $RS["MAXAMOUNT"], $RS["LANG"], $RS["SMSRCPT"], $RS["EMAILRCPT"], $RS["METHOD"], utf8_decode($RS["TERMS"]), $RS["MODE"], $RS["AUTO_CAPTURE"], $RS["SEND_PSPID"], $RS["STORE_CARD"], $RS["CUSTOMERIMPORTURL"], $RS["AUTHURL"], $RS["NOTIFYURL"], $RS["MESBURL"], $aIPs, $RS["SHOW_ALL_CARDS"], $RS["MAX_CARDS"], $RS["IDENTIFICATION"], $RS["TRANSACTION_TTL"]);
	}

	/**
	 * Function to check for the IP whitelisting
	 *
	 * @param string $ip	the IP address as a string
	 * @return boolean
	 */
	public function hasAccess($ip)
	{
		if (count($this->_aIPList) == 0) { return true; }
		else { return in_array($ip, $this->_aIPList); }
	}
	
	public function getClientAccountsToXML(RDB $oDB, $clientid)
	{		
		$xml = '';
		$sql = "SELECT A.id AS accountid, A.name AS accountname, A.markup AS markup			
				FROM Client". sSCHEMA_POSTFIX .".Client_Tbl CL 
				INNER JOIN Client". sSCHEMA_POSTFIX .".Account_Tbl A ON CL.id = A.clientid 				
				WHERE CL.id = ". intval($clientid) ." AND CL.enabled = '1';
		";
		//echo $sql ."\n";
		$res = $oDB->query($sql);
//		var_dump($oDB->fetchName($res));die;		
		$xml = '<accounts>';
		while ($RS = $oDB->fetchName($res))
		{			
			$xml .= '<account>';
			if (!empty($RS) && $RS['ACCOUNTID'] > 0)
			{
				$xml .= '<name>'. htmlspecialchars($RS['ACCOUNTNAME'], ENT_NOQUOTES) .'</name>';
				$xml .= '<markup>'. htmlspecialchars($RS['MARKUP'], ENT_NOQUOTES).'</markup>';	
			}
			$xml .= $this->getClientMerchantSubAccountsToXML($oDB, $clientid, $RS['ACCOUNTID']);
			$xml .= '</account>';
		}
		$xml .= '</accounts>';		
		return $xml;
	}
	
	public function getClientMerchantSubAccountsToXML(RDB $oDB, $clientid, $accountid)
	{		
		$xml = '';
		$sql = "SELECT MSA.pspid AS pspid		
				FROM Client". sSCHEMA_POSTFIX .".Client_Tbl CL 
				INNER JOIN Client". sSCHEMA_POSTFIX .".Account_Tbl A ON CL.id = A.clientid 
				INNER JOIN Client.MerchantSubAccount_Tbl MSA ON A.id = MSA.accountid				
				WHERE CL.id = ". intval($clientid) ." AND A.id = ".intval($accountid)." AND CL.enabled = '1';
		";
		//echo $sql ."\n";
		$res = $oDB->query($sql);	
		
		$xml = '<payment-service-providers>';
		while ($RS = $oDB->fetchName($res))
		{			
			$obj_PSPConfig = PSPConfig::produceConfig($oDB, $clientid, $accountid, $RS['PSPID']);
			if(!empty($obj_PSPConfig)){
				$xml .= '<payment-service-provider id = "'.$obj_PSPConfig->getID().'">';			
				$xml .= '<name>'. htmlspecialchars($obj_PSPConfig->getName(), ENT_NOQUOTES) .'</name>';							
				$xml .= '</payment-service-provider>';
			}
		}
		$xml .= '</payment-service-providers>';		
		
		return $xml;
	}
	
	public function getClientCardAccessToXML(RDB $oDB, $clientid)
	{		
		$xml = '';
		$sql = "SELECT CA.id, CA.name, CL.countryid, CCA.pspid		
				FROM Client". sSCHEMA_POSTFIX .".Client_Tbl CL 
				INNER JOIN Client". sSCHEMA_POSTFIX .".CardAccess_Tbl CCA ON CL.id = CCA.clientid 
				INNER JOIN System.". sSCHEMA_POSTFIX ."Card_Tbl CA ON CCA.cardid = CA.id
				WHERE CL.id = ". intval($clientid) ." AND CL.enabled = '1';
		";		
		//echo $sql ."\n";
		$res = $oDB->query($sql);		
		
		while ($RS = $oDB->fetchName($res))
		{			
			if(!empty($RS) && $RS['ID'] > 0){										
				$xml .= '<card id="'.$RS['ID'].'" country-id="'.$RS['COUNTRYID'].'" psp-id="'.$RS['PSPID'].'">'.htmlspecialchars($RS['NAME'], ENT_NOQUOTES).'</card>';
			}
		}		
		return $xml;
	}
	
	public function getClientPSPConfigToXML(RDB $oDB, $clientid)
	{		
		$xml = '';
		$sql = "SELECT MA.name, MA.username, MA.passwd, MA.pspid		
				FROM Client". sSCHEMA_POSTFIX .".Client_Tbl CL 
				INNER JOIN Client". sSCHEMA_POSTFIX .".MerchantAccount_Tbl MA ON CL.id = MA.clientid 				
				WHERE CL.id = ". intval($clientid) ." AND CL.enabled = '1';
		";
		//echo $sql ."\n";
		$res = $oDB->query($sql);	
		
		$xml = '<payment-service-providers>';
		while ($RS = $oDB->fetchName($res))
		{			
			if(!empty($RS)){
				$xml .= '<payment-service-provider id = "'.$RS['PSPID'].'">';			
				$xml .= '<name>'. htmlspecialchars($RS['NAME'], ENT_NOQUOTES) .'</name>';
				$xml .= '<username>'. htmlspecialchars($RS['NAME'], ENT_NOQUOTES) .'</username>';
				$xml .= '<password>'. htmlspecialchars($RS['PASSWD'], ENT_NOQUOTES) .'</password>';							
				$xml .= '</payment-service-provider>';
			}
		}
		$xml .= '</payment-service-providers>';		
		
		return $xml;
	}
}
?>