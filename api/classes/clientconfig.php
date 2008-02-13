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
 * @version 1.0
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
	 * Default Constructor
	 *
	 * @param 	integer $id 		Unique ID for the Client in mPoint
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
	 * @param 	string $ma 			Max Amount an mPoint Transaction can cost the customer for the Client
	 * @param 	string $l 			The language that all payment pages should be rendered in by default for the Client
	 */
	public function __construct($id, $name, AccountConfig &$oAC, $un, $pw, CountryConfig &$oCC, KeywordConfig &$oKC, $lurl, $cssurl, $aurl, $curl, $cburl, $ma, $l)
	{
		parent::__construct($id, $name);
		
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
		
		$this->_iMaxAmount = (integer) $ma;
		$this->_sLanguage = trim($l);
	}
	
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
	
	public function toXML()
	{
		$xml = '<client-config id="'. $this->getID() .'">';
		$xml .= '<name>'. htmlspecialchars($this->getName(), ENT_NOQUOTES) .'</name>';
		$xml .= '<username>'. htmlspecialchars($this->getUsername(), ENT_NOQUOTES) .'</username>';
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
		$sql = "SELECT Cl.id AS clientid, Cl.name AS client, Cl.username, Cl.passwd,
					Cl.logourl, Cl.cssurl, Cl.accepturl, Cl.cancelurl, Cl.callbackurl,
					Cl.maxamount, Cl.lang,
					C.id AS countryid, C.name AS country, C.currency, C.minmob, C.maxmob, C.channel, C.priceformat,
					Acc.id AS accountid, Acc.name AS account, Acc.address,
					KW.id AS keywordid, KW.name AS keyword, KW.price
				FROM Client.Client_Tbl Cl
				INNER JOIN System.Country_Tbl C ON Cl.countryid = C.id AND C.enabled = true
				INNER JOIN Client.Account_Tbl Acc ON Cl.id = Acc.clientid AND Acc.enabled = true
				INNER JOIN Client.Keyword_Tbl KW ON Cl.id = KW.clientid AND KW.enabled = true
				WHERE Cl.id = ". intval($id) ." AND Cl.enabled = true";
		// Use Default Keyword
		if ($kw == -1)
		{
			$sql .= " AND KW.price = -1";
		}
		// Use specific Keyword
		else { $sql .= " AND KW.id = ". intval($kw); }
		
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
			$sql .= " AND Acc.id = ". $acc ."
					ORDER BY KW.id ASC
					LIMIT 1";
		}
//		echo $sql ."\n";
		$RS = $oDB->getName($sql);
		
		$obj_CountryConfig = new CountryConfig($RS["COUNTRYID"], $RS["COUNTRY"], $RS["CURRENCY"], $RS["MINMOB"], $RS["MAXMOB"], $RS["CHANNEL"], $RS["PRICEFORMAT"]);
		$obj_AccountConfig = new AccountConfig($RS["ACCOUNTID"], $RS["CLIENTID"], $RS["ACCOUNT"], $RS["ADDRESS"]);
		$obj_KeywordConfig = new KeywordConfig($RS["KEYWORDID"], $RS["CLIENTID"], $RS["KEYWORD"], $RS["PRICE"]);
		
		return new ClientConfig($RS["CLIENTID"], $RS["CLIENT"], $obj_AccountConfig, $RS["USERNAME"], $RS["PASSWD"], $obj_CountryConfig, $obj_KeywordConfig, $RS["LOGOURL"], $RS["CSSURL"], $RS["ACCEPTURL"], $RS["CANCELURL"], $RS["CALLBACKURL"], $RS["MAXAMOUNT"], $RS["LANG"]);
	}
}
?>