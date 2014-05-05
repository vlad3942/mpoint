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
 * @subpackage PSPConfig
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
class PSPConfig extends BasicConfig
{
	/**
	 * The name of the Client's Merchant Account with the Payment Service Provider
	 *
	 * @var string
	 */
	private $_sMerchantAccount;
	/**
	 * The name of the Client's Merchant Sub Account with the Payment Service Provider
	 *
	 * @var string
	 */
	private $_sMerchantSubAccount;
	/**
	 * Client's Username for the Payment Service Provider
	 *
	 * @var string
	 */
	private $_sUsername;
	/**
	 * Client's Password for the Payment Service Provider
	 *
	 * @var string
	 */
	private $_sPassword;

	/**
	 * Default Constructor
	 *
	 * @param 	integer $id 	Unique ID for the Payment Service Provider in mPoint
	 * @param 	string $name	Payment Service Provider's name in mPoint
	 * @param 	string $ma 		The name of the Client's Merchant Account with the Payment Service Provider
	 * @param 	string $msa		The name of the Client's Merchant Sub Account with the Payment Service Provider
	 * @param 	string $un 		Client's Username for the Payment Service Provider
	 * @param 	string $pw 		Client's Password for the Payment Service Provider
	 */
	public function __construct($id, $name, $ma, $msa, $un, $pw)
	{
		parent::__construct($id, $name);

		$this->_sMerchantAccount = trim($ma);
		$this->_sMerchantSubAccount = trim($msa);
		$this->_sUsername = trim($un);
		$this->_sPassword = trim($pw);
	}

	/**
	 * Returns the name of the Client's Merchant Account with the Payment Service Provider
	 *
	 * @return 	string
	 */
	public function getMerchantAccount() { return $this->_sMerchantAccount; }
	/**
	 * Returns the name of the Client's Merchant Sub Account with the Payment Service Provider
	 *
	 * @return 	string
	 */
	public function getMerchantSubAccount() { return $this->_sMerchantSubAccount; }
	/**
	 * Returns the Client's Username for the Payment Service Provider
	 *
	 * @return 	string
	 */
	public function getUsername() { return $this->_sUsername; }
	/**
	 * Returns the Client's Password for the Payment Service Provider
	 *
	 * @return 	string
	 */
	public function getPassword() { return $this->_sPassword; }

	public function toXML()
	{
		$xml = '<psp-info id="'. $this->getID() .'">';
		$xml .= '<name>'. htmlspecialchars($this->getName(), ENT_NOQUOTES) .'</name>';
		$xml .= '<merchant-account>'. htmlspecialchars($this->_sMerchantAccount, ENT_NOQUOTES) .'</merchant-account>';
		$xml .= '<merchant-sub-account>'. htmlspecialchars($this->_sMerchantSubAccount, ENT_NOQUOTES) .'</merchant-sub-account>';
		$xml .= '<username>'. htmlspecialchars($this->_sUsername, ENT_NOQUOTES) .'</username>';
		$xml .= '<password>'. htmlspecialchars($this->_sPassword, ENT_NOQUOTES) .'</password>';
		$xml .= '</client-config>';

		return $xml;
	}

	/**
	 * Produces a new instance of a Payment Service Provider Configuration Object.
	 *
	 * @param 	RDB $oDB 		Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param 	integer $clid 	Unique ID for the Client performing the request
	 * @param 	integer $accid 	Unique ID for the Account-id performing the request
	 * @param 	integer $pspid 	Unique ID for the Payment Service Provider
	 * @return 	PSPConfig
	 */
	public static function produceConfig(RDB &$oDB, $clid, $accid, $pspid)
	{
		$sql = "SELECT DISTINCT PSP.id, PSP.name,
					MA.name AS ma, MA.username, MA.passwd AS password, MSA.name AS msa
				FROM System".sSCHEMA_POSTFIX.".PSP_Tbl PSP
				INNER JOIN Client".sSCHEMA_POSTFIX.".MerchantAccount_Tbl MA ON PSP.id = MA.pspid AND MA.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".Client_Tbl CL ON MA.clientid = CL.id AND CL.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".Account_Tbl Acc ON CL.id = Acc.clientid AND Acc.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".MerchantSubAccount_Tbl MSA ON Acc.id = MSA.accountid AND PSP.id = MSA.pspid AND MSA.enabled = '1'
				WHERE CL.id = ". intval($clid) ." AND PSP.id = ". intval($pspid) ." AND PSP.enabled = '1' AND Acc.id = ". intval($accid)." ";
//		echo $sql ."\n";
		$RS = $oDB->getName($sql);

		if (is_array($RS) === true && count($RS) > 1) {	return new PSPConfig($RS["ID"], $RS["NAME"], $RS["MA"], $RS["MSA"], $RS["USERNAME"], $RS["PASSWORD"]); }
		else { return null; }
	}
}
?>