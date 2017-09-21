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
	 * The value of the System Type i.e if it is APM = 4,Wallet = 3,Bank = 2,PSP = 1 etc with the Payment Service Provider
	 *
	 * @var integer
	 */
	private $_iType;
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
	 * List of messages that are sent to the Payment Service Provider
	 *
	 * @var array
	 */
	private $_aMessages;


    /*
     * Array that hold the Addotional Data in
     * @var array
     */
    private $_aAdditionalProperties=array();

    /**
	 * Default Constructor
	 *
	 * @param 	integer $id 	Unique ID for the Payment Service Provider in mPoint
	 * @param 	string $name	Payment Service Provider's name in mPoint
	 * @param 	string $ma 		The name of the Client's Merchant Account with the Payment Service Provider
	 * @param 	string $msa		The name of the Client's Merchant Sub Account with the Payment Service Provider
	 * @param 	string $un 		Client's Username for the Payment Service Provider
	 * @param 	string $pw 		Client's Password for the Payment Service Provider
	 * @param 	array $aMsgs 	List of messages that are sent to the Payment Service Provider
	 */
	public function __construct($id, $name, $system_type, $ma, $msa, $un, $pw, array $aMsgs=array(),$aAdditionalProperties )
	{
		parent::__construct($id, $name);
		$this->_sMerchantAccount = trim($ma);
		$this->_sMerchantSubAccount = trim($msa);
		$this->_iType = intval($system_type);
		$this->_sUsername = trim($un);
		$this->_sPassword = trim($pw);
		$this->_aMessages = $aMsgs;
		$this->_aAdditionalProperties =$aAdditionalProperties;
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
	 * Returns the ID of System Type with the Payment Service Provider
	 *
	 * @return 	integer
	 */
	public function getProcessorType(){ return $this->_iType; }
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
	/**
	 * Returns the List of messages that are sent to the Payment Service Provider
	 *
	 * @return 	array
	 */
	public function getMessages() { return $this->_aMessages; }
	/**
	 * Returns the that is sent to the Payment Service Provider in the specified language
	 *
	 * @return 	string
	 */
	public function getMessage($lang) { return @$this->_aMessages[strtolower($lang)]; }

	public function toXML()
	{
		$xml  = '<psp-config id="'. $this->getID() .'" type="'. $this->getProcessorType().'">';
		$xml .= '<name>'. htmlspecialchars($this->getName(), ENT_NOQUOTES) .'</name>';
		$xml .= '<merchant-account>'. htmlspecialchars($this->_sMerchantAccount, ENT_NOQUOTES) .'</merchant-account>';
		$xml .= '<merchant-sub-account>'. htmlspecialchars($this->_sMerchantSubAccount, ENT_NOQUOTES) .'</merchant-sub-account>';
		$xml .= '<username>'. htmlspecialchars($this->_sUsername, ENT_NOQUOTES) .'</username>';
		$xml .= '<password>'. htmlspecialchars($this->_sPassword, ENT_NOQUOTES) .'</password>';
		$xml .= '<messages>';
		foreach ($this->_aMessages as $lang => $msg)
		{
			$xml .= '<message language="'. htmlspecialchars($lang, ENT_NOQUOTES) .'">'. htmlspecialchars($msg, ENT_NOQUOTES) .'</message>';
		}
		$xml .= '</messages>';
        $xml .= '<additional-config>';
        foreach ($this->_aAdditionalProperties as $aAdditionalProperty)
        {
            $xml .= '<property name="'.$aAdditionalProperty['key'].'">'.$aAdditionalProperty['value'].'</property>';
        }
        $xml .= '</additional-config>';
		$xml .= '</psp-config>';

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
		$sql = "SELECT DISTINCT PSP.id, PSP.name, PSP.system_type,
					MA.name AS ma, MA.username, MA.passwd AS password, MSA.name AS msa, MA.id as MerchantId
				FROM System".sSCHEMA_POSTFIX.".PSP_Tbl PSP
				INNER JOIN Client".sSCHEMA_POSTFIX.".MerchantAccount_Tbl MA ON PSP.id = MA.pspid AND MA.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".Client_Tbl CL ON MA.clientid = CL.id AND CL.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".Account_Tbl Acc ON CL.id = Acc.clientid AND Acc.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".MerchantSubAccount_Tbl MSA ON Acc.id = MSA.accountid AND PSP.id = MSA.pspid AND MSA.enabled = '1'
				INNER JOIN SYSTEM".sSCHEMA_POSTFIX.".processortype_tbl PT ON PSP.system_type = PT.id	
				WHERE CL.id = ". intval($clid) ." AND PSP.id = ". intval($pspid) ." AND PSP.enabled = '1' AND Acc.id = ". intval($accid) ." AND (MA.stored_card = '0' OR MA.stored_card IS NULL)";
//		echo $sql ."\n";
		$RS = $oDB->getName($sql);
		if (is_array($RS) === true && count($RS) > 1)
		{
			$sql = "SELECT I.language, I.text
					FROM Client".sSCHEMA_POSTFIX.".Info_Tbl I
					INNER JOIN Client".sSCHEMA_POSTFIX.".InfoType_Tbl IT ON I.infotypeid = IT.id AND IT.enabled = '1'
					WHERE I.clientid = ". intval($clid) ." AND IT.id = ". Constants::iPSP_MESSAGE_INFO ." AND (I.pspid = ". intval($pspid) ." OR I.pspid IS NULL)";
//			echo $sql ."\n";
			$aRS = $oDB->getAllNames($sql);
			$aMessages = array();
			if (is_array($aRS) === true)
			{
				for ($i=0; $i<count($aRS); $i++)
				{
					$aMessages[strtolower($aRS[$i]["LANGUAGE"])] = $aRS[$i]["TEXT"];
				}
			}

            $sql  = "SELECT key,value
					 FROM Client". sSCHEMA_POSTFIX .".AdditionalProperty_tbl
					 WHERE externalid = ". intval($RS["MERCHANTID"]) ." and type='merchant'" ;
            //		echo $sql ."\n";
            $aRS = $oDB->getAllNames($sql);
            $aAdditionalProperties = array();
            if (is_array($aRS) === true && count($aRS) > 0)
            {
                for ($i=0; $i<count($aRS); $i++)
                {
                    $aAdditionalProperties[$i]["key"] =$aRS[$i]["KEY"];
                    $aAdditionalProperties[$i]["value"] = $aRS[$i]["VALUE"];
                }
            }

			return new PSPConfig($RS["ID"], $RS["NAME"], $RS["SYSTEM_TYPE"], $RS["MA"], $RS["MSA"], $RS["USERNAME"], $RS["PASSWORD"], $aMessages,$aAdditionalProperties);
		}
		else
		{
			trigger_error("PSP Configuration not found using Client ID: ". $clid .", Account: ". $accid .", PSP ID: ". $pspid, E_USER_WARNING);
			return null;
		}
	}
}
?>