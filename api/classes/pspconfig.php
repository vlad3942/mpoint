<?php

use api\classes\merchantservices\configuration\BaseConfig;
use api\classes\merchantservices\Repositories\ReadOnlyConfigRepository;

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

class PSPConfig extends BasicConfig
{
	/**
	 * The name of the Client's Merchant Account with the Payment Service Provider
	 *
	 * @var string
	 */
	private string $_sMerchantAccount;
	/**
	 * The name of the Client's Merchant Sub Account with the Payment Service Provider
	 *
	 * @var string
	 */
	private string $_sMerchantSubAccount;
	/**
	 * The value of the System Type i.e if it is APM = 4,Wallet = 3,Bank = 2,PSP = 1 etc with the Payment Service Provider
	 *
	 * @var integer
	 */
	private int $_iType;
	/**
	 * Client's Username for the Payment Service Provider
	 *
	 * @var string
	 */
	private string $_sUsername;
	/**
	 * Client's Password for the Payment Service Provider
	 *
	 * @var string
	 */
	private string $_sPassword;
	/**
	 * List of messages that are sent to the Payment Service Provider
	 *
	 * @var ?array
	 */
	private ?array $_aMessages;


    /*
     * Array that hold the Addotional Data in
     * @var ?array
     */
    private ?array $_aAdditionalProperties=array();

    /**
     * Hold uniquir ID of the route configuration
     * @var integer
     */
    private int $_iRouteConfigId;

    /**
     * Hold list of route features
     * @var ?array
     */
    private ?array $_aRouteFeature;

    /**
     * Hold the Client's MID for the Route
     * @var string
     */
    private string $_sRouteMid;

    /**
     * Hold the Client's Username for the Route
     * @var string
     */
    private string $_sRouteUseranme;

    /**
     * Hold the Client's Password for the Route
     * @var string
     */
    private string $_sRoutePassword;

    /**
	 * Default Constructor
	 *
	 * @param 	integer $id 	Unique ID for the Payment Service Provider in mPoint
	 * @param 	string $name	Payment Service Provider's name in mPoint
	 * @param 	int $system_type	System type
	 * @param 	string $ma 		The name of the Client's Merchant Account with the Payment Service Provider
	 * @param 	string $msa		The name of the Client's Merchant Sub Account with the Payment Service Provider
	 * @param 	string $un 		Client's Username for the Payment Service Provider
	 * @param 	string $pw 		Client's Password for the Payment Service Provider
	 * @param 	array $aMsgs 	List of messages that are sent to the Payment Service Provider
	 * @param 	?array $aAdditionalProperties Additional properties
	 * @param 	int $routeConfigId 	     Route config id
	 * @param 	?array $aRouteFeature     Route feature
	 */
	public function __construct(int $id, string $name, int $system_type, string $ma, string $msa, string $un, string $pw, array $aMsgs=array(), ?array $aAdditionalProperties=array(), int $routeConfigId = -1, ?array $aRouteFeature = array(), string $routeMID='', string $routeUsername='', string $routePassword='')
	{
		parent::__construct($id, $name);
		$this->_sMerchantAccount = trim($ma);
		$this->_sMerchantSubAccount = trim($msa);
		$this->_iType = $system_type;
		$this->_sUsername = trim($un);
		$this->_sPassword = trim($pw);
		$this->_aMessages = $aMsgs;
		$this->_aAdditionalProperties =$aAdditionalProperties;
        $this->_iRouteConfigId = $routeConfigId;
        $this->_aRouteFeature = $aRouteFeature;
        $this->_sRouteMid = $routeMID;
        $this->_sRouteUseranme = $routeUsername;
        $this->_sRoutePassword = $routePassword;
	}

	/**
	 * Returns the name of the Client's Merchant Account with the Payment Service Provider
	 *
	 * @return 	string
	 */
	public function getMerchantAccount(): ?string { return $this->_sMerchantAccount; }
	/**
	 * Returns the name of the Client's Merchant Sub Account with the Payment Service Provider
	 *
	 * @return 	string
	 */
	public function getMerchantSubAccount(): ?string { return $this->_sMerchantSubAccount; }
	/**
	 * Returns the ID of System Type with the Payment Service Provider
	 *
	 * @return 	integer
	 */
	public function getProcessorType(): int { return $this->_iType; }
	/**
	 * Returns the Client's Username for the Payment Service Provider
	 *
	 * @return 	string
	 */
	public function getUsername(): ?string { return $this->_sUsername; }
	/**
	 * Returns the Client's Password for the Payment Service Provider
	 *
	 * @return 	string
	 */
	public function getPassword(): ?string { return $this->_sPassword; }
	/**
	 * Returns the List of messages that are sent to the Payment Service Provider
	 *
	 * @return 	array
	 */
	public function getMessages(): ?array { return $this->_aMessages; }
	/**
	 * Returns the that is sent to the Payment Service Provider in the specified language
	 *
	 * @return 	string
	 */
	public function getMessage($lang) { return @$this->_aMessages[strtolower($lang)]; }

    /**
     * Returns unique route configuration ID
     *
     * @return 	integer
     */
	public function getRouteConfigId(): int { return $this->_iRouteConfigId; }

    /**
     * Set the Client's MID for the Route
     */
    public function getRouteMID() : string {  return $this->_sRouteMid; }

    /**
     * Set the Client's Username for the Route
     */
    public function getRouteUsername() : string {  return $this->_sRouteUseranme; }

    /**
     * Set the Client's Password for the Route
     */
    public function getRoutePassword() : string {  return $this->_sRoutePassword;  }

    public function isRouteFeatureEnabled(int $featureId) :bool
    {

        if(empty($this->_aRouteFeature) === false)
        {
            foreach ($this->_aRouteFeature as $feature)
            {
                if((int)$feature['ID'] === $featureId && $feature['ENABLED'] === true  ) return true;
            }
        }
        return false;
    }

	public function toXML(?int $propertyScope=2, array $aMerchantAccountDetails = array()): string
	{
		$xml  = '<psp-config id="'. $this->getID() .'" type="'. $this->getProcessorType().'">';
		$xml .= '<name>'. htmlspecialchars($this->getName(), ENT_NOQUOTES) .'</name>';
		if (count($aMerchantAccountDetails) > 0)        {
            $merchantaccount = $aMerchantAccountDetails['merchantaccount'];
            $username = $aMerchantAccountDetails['username'];
            $password = $aMerchantAccountDetails['password'];
            if (isset($merchantaccount) === false || $merchantaccount === false || $merchantaccount === '' ) {
                $merchantaccount = $this->_sMerchantAccount;
            }

            if (isset($username) === false || $username === false || $username === '') {
                $username = $this->_sUsername;
            }

            if (isset($password) === false || $password === false || $password === '') {
                $password = $this->_sPassword;
            }

            $xml .= '<merchant-account>' . htmlspecialchars($merchantaccount, ENT_NOQUOTES) . '</merchant-account>';
            $xml .= '<merchant-sub-account>'. htmlspecialchars($this->_sMerchantSubAccount, ENT_NOQUOTES) .'</merchant-sub-account>';
            $xml .= '<username>' . htmlspecialchars($username, ENT_NOQUOTES) . '</username>';
            $xml .= '<password>' . htmlspecialchars($password, ENT_NOQUOTES) . '</password>';
        }
		else {
            $xml .= '<merchant-account>' . htmlspecialchars($this->_sMerchantAccount, ENT_NOQUOTES) . '</merchant-account>';
            $xml .= '<merchant-sub-account>'. htmlspecialchars($this->_sMerchantSubAccount, ENT_NOQUOTES) .'</merchant-sub-account>';
            $xml .= '<username>' . htmlspecialchars($this->_sUsername, ENT_NOQUOTES) . '</username>';
            $xml .= '<password>' . htmlspecialchars($this->_sPassword, ENT_NOQUOTES) . '</password>';
        }
		$xml .= '<messages>';
		foreach ($this->_aMessages as $lang => $msg)
		{
			$xml .= '<message language="'. htmlspecialchars($lang, ENT_NOQUOTES) .'">'. htmlspecialchars($msg, ENT_NOQUOTES) .'</message>';
		}
		$xml .= '</messages>';
        $xml .= '<additional-config>';
        foreach ($this->getAdditionalProperties($propertyScope) as $aAdditionalProperty)
        {
             if (strpos($aAdditionalProperty['key'], 'rule') === false) {
                 $xml .= '<property name="' . $aAdditionalProperty['key'] . '">' . $aAdditionalProperty['value'] . '</property>';
             }
        }
        $xml .= '</additional-config>';
		$xml .= '</psp-config>';

		return $xml;
	}

    public function toAttributeLessXML(?int $propertyScope=2, array $aMerchantAccountDetails = array()): string
    {
        $xml  = '<pspConfig>';
        $xml .= '<id>'.$this->getID().'</id>';
        $xml .= '<type>'.$this->getProcessorType().'</type>';
        $xml .= '<name>'. htmlspecialchars($this->getName(), ENT_NOQUOTES) .'</name>';
        if (count($aMerchantAccountDetails) > 0)
        {
            $merchantaccount = $aMerchantAccountDetails['merchantaccount'];
            $username = $aMerchantAccountDetails['username'];
            $password = $aMerchantAccountDetails['password'];
            if (isset($merchantaccount) === false || $merchantaccount === false || $merchantaccount === '' ) {
                $merchantaccount = $this->_sMerchantAccount;
            }

            if (isset($username) === false || $username === false || $username === '') {
                $username = $this->_sUsername;
            }

            if (isset($password) === false || $password === false || $password === '') {
                $password = $this->_sPassword;
            }

            $xml .= '<merchantAccount>' . htmlspecialchars($merchantaccount, ENT_NOQUOTES) . '</merchantAccount>';
            $xml .= '<merchantSubAccount>'. htmlspecialchars($this->_sMerchantSubAccount, ENT_NOQUOTES) .'</merchantSubAccount>';
            $xml .= '<username>' . htmlspecialchars($username, ENT_NOQUOTES) . '</username>';
            $xml .= '<password>' . htmlspecialchars($password, ENT_NOQUOTES) . '</password>';
        }
        else
         {
            $xml .= '<merchantAccount>' . htmlspecialchars($this->_sMerchantAccount, ENT_NOQUOTES) . '</merchantAccount>';
            $xml .= '<merchantSubAccount>'. htmlspecialchars($this->_sMerchantSubAccount, ENT_NOQUOTES) .'</merchantSubAccount>';
            $xml .= '<username>' . htmlspecialchars($this->_sUsername, ENT_NOQUOTES) . '</username>';
            $xml .= '<password>' . htmlspecialchars($this->_sPassword, ENT_NOQUOTES) . '</password>';
        }
        $xml .= '<messages>';
        foreach ($this->_aMessages as $lang => $msg)
        {
            $xml .= '<message >';
            $xml .= '<language>'.htmlspecialchars($lang, ENT_NOQUOTES).'</language>';
            $xml .= '</message>';
        }
        $xml .= '</messages>';
        $xml .= '<additionalConfig>';
        foreach ($this->getAdditionalProperties($propertyScope) as $aAdditionalProperty)
        {
            if (strpos($aAdditionalProperty['key'], 'rule') === false)
            {
                $xml .= '<property>';
                $xml .=  '<name>'.$aAdditionalProperty['key'].'</name>';
                $xml .=  '<value>'.$aAdditionalProperty['value'].'</value>';
                $xml .= '</property>';
            }
        }
        $xml .= '</additionalConfig>';
        $xml .= '</pspConfig>';

        return $xml;
    }

    /**
     * Function return Route config XML
     * @return string
     */
    public function toRouteConfigXML(): string
    {
        $xml = '<route_configuration>';
        $xml .= '<id>'. $this->getRouteConfigId() .'</id>';
        $xml .= '<route_id>'. $this->getID() .'</route_id>';
        $xml .= '<name>'. $this->getName() .'</name>';
        $xml .= '<mid>'. $this->getRouteMID() .'</mid>';
        $xml .= '<username>'. htmlspecialchars($this->getRouteUsername(), ENT_NOQUOTES) .'</username>';
        $xml .= '<password>'. htmlspecialchars($this->getRoutePassword(), ENT_NOQUOTES) .'</password>';
        $xml .= '<route_features>';
        if(empty($this->_aRouteFeature) === false && count($this->_aRouteFeature) > 0)
        {
            foreach ($this->_aRouteFeature as $feature)
            {
                $xml .= '<route_feature>';
                $xml .= '<id>'. $feature['ID'] .'</id>';
                $xml .= '<name>'. $feature['FEATURENAME'] .'</name>';
                $xml .= '<value>'.  General::bool2xml($feature['ENABLED']) .'</value>';
				$xml .= '</route_feature>';
            }
        }
        $xml .= '</route_features>';
        $xml .= '</route_configuration>';

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
	public static function produceConfig(RDB $oDB, int $clid, int $accid, int $pspid): ?PSPConfig
	{
		$sql = "SELECT DISTINCT PSP.id, PSP.name, PSP.system_type,
					MA.name AS ma, MA.username, MA.passwd AS password, MSA.name AS msa, MA.id as MerchantId
				FROM System".sSCHEMA_POSTFIX.".PSP_Tbl PSP
				INNER JOIN Client".sSCHEMA_POSTFIX.".MerchantAccount_Tbl MA ON PSP.id = MA.pspid AND MA.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".Client_Tbl CL ON MA.clientid = CL.id AND CL.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".Account_Tbl Acc ON CL.id = Acc.clientid AND Acc.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".MerchantSubAccount_Tbl MSA ON Acc.id = MSA.accountid AND PSP.id = MSA.pspid AND MSA.enabled = '1'
				INNER JOIN SYSTEM".sSCHEMA_POSTFIX.".processortype_tbl PT ON PSP.system_type = PT.id	
				WHERE CL.id = ". $clid ." AND PSP.id = ". $pspid ." AND PSP.enabled = '1' AND Acc.id = ". $accid ." AND (MA.stored_card = '0' OR MA.stored_card IS NULL)";
//		echo $sql ."\n";
		$RS = $oDB->getName($sql);
		if (is_array($RS) === true && count($RS) > 1)
		{
			$sql = "SELECT I.language, I.text
					FROM Client".sSCHEMA_POSTFIX.".Info_Tbl I
					INNER JOIN Client".sSCHEMA_POSTFIX.".InfoType_Tbl IT ON I.infotypeid = IT.id AND IT.enabled = '1'
					WHERE I.clientid = ". $clid ." AND IT.id = ". Constants::iPSP_MESSAGE_INFO ." AND (I.pspid = ". $pspid ." OR I.pspid IS NULL)";
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

            $sql  = "SELECT key,value, scope
					 FROM Client". sSCHEMA_POSTFIX .".AdditionalProperty_tbl
					 WHERE externalid = ". (int)$RS["MERCHANTID"] ." and type='merchant' and enabled=true" ;
            //		echo $sql ."\n";
            $aRS = $oDB->getAllNames($sql);
            $aAdditionalProperties = array();
            if (is_array($aRS) === true && count($aRS) > 0)
            {
                $iConstOfRows = count($aRS);
                for ($i=0; $i<$iConstOfRows; $i++)
                {
                    $aAdditionalProperties[$i]["key"] =$aRS[$i]["KEY"];
                    $aAdditionalProperties[$i]["value"] = $aRS[$i]["VALUE"];
                    $aAdditionalProperties[$i]["scope"] = $aRS[$i]["SCOPE"];
                }
            }

			return new PSPConfig($RS["ID"], $RS["NAME"], (int) $RS["SYSTEM_TYPE"], $RS["MA"], $RS["MSA"], $RS["USERNAME"], $RS["PASSWORD"], $aMessages, $aAdditionalProperties);
		}
		else
		{
			trigger_error("PSP Configuration not found using Client ID: ". $clid .", Account: ". $accid .", PSP ID: ". $pspid, E_USER_WARNING);
			return null;
		}
	}

    /**
     * Produces a new instance of a Payment Service Provider Configuration Object For Non Legacy Flow.
     *
     * @param 	RDB $oDB 		Reference to the Database Object that holds the active connection to the mPoint Database
     * @param 	integer $clid 	Unique ID for the Client performing the request
     * @param 	integer $accid 	Unique ID for the Account-id performing the request
     * @param 	integer $pspid 	Unique ID for the Payment Service Provider
     * @return 	PSPConfig
     */
    public static function produceProviderConfig(RDB $oDB, int $pspid,TxnInfo &$oTI,BaseConfig $config=null): ?PSPConfig
    {
        $repository = new ReadOnlyConfigRepository($oDB,$oTI);
        return $repository->getProviderConfig($pspid,$config);
    }
    /*
	 * Get Additional properties
	 * If key is send as parameter then value of that key will return
	 * Otherwise all properties will return
	 *
     * @param int scope
	 * @param string key
	 *
	 * return string or array
	 */
    public function getAdditionalProperties(?int $scope, ?string $key = '')
    {
        $isAll = false;
        $returnProperties = [];
        if ($key === '')
        {
            $isAll = true;
        }

        foreach ($this->_aAdditionalProperties as $additionalProperty)
        {
            if ($isAll || $additionalProperty['key'] === $key)
            {
                $propertyScope = (integer)$additionalProperty['scope'];
                if($propertyScope >= $scope)
                {
                    if($isAll === false)
                    {
                        return $additionalProperty['value'];
                    }
                    array_push($returnProperties,$additionalProperty);
                }
            }
        }

        if ($isAll)
        {
            return $returnProperties;
        }

        return false;
    }

    /**
     * Produces a new instance of a Payment Service Provider Configuration Object.
     *
     * @param 	RDB $oDB 		Reference to the Database Object that holds the active connection to the mPoint Database
     * @param 	integer $clid 	Unique ID for the Client performing the request
     * @param 	integer $accid 	Unique ID for the Account-id performing the request
     * @param 	integer $pspid 	Unique ID for the Payment Service Provider
     * @param 	integer $routeconfigid 	Unique ID for the Route Config ID
     *
     * @return 	PSPConfig|null
     */
    public static function produceConfiguration(RDB $oDB, TxnInfo &$oTI, int $pspid, int $routeconfigid): ?PSPConfig
    {
        $repository = new ReadOnlyConfigRepository($oDB,$oTI);
        return $repository->getPSPConfig($pspid,$routeconfigid);
    }

    /**
     * Build a query which will select merchant account details
     *
     * @param 	integer $clid 	Unique ID for the Client performing the request
     * @param 	integer $accid 	Unique ID for the Account-id performing the request
     * @param 	integer $pspid 	Unique ID for the Payment Service Provider
     * @return 	string
     */
    private function getQuery(int $clid, int $accid, int $pspid): string
    {
        $sql = "SELECT DISTINCT  MA.name AS ma, MA.username, MA.passwd AS password
				FROM System".sSCHEMA_POSTFIX.".PSP_Tbl PSP
				INNER JOIN Client".sSCHEMA_POSTFIX.".MerchantAccount_Tbl MA ON PSP.id = MA.pspid AND MA.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".Client_Tbl CL ON MA.clientid = CL.id AND CL.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".Account_Tbl Acc ON CL.id = Acc.clientid AND Acc.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".MerchantSubAccount_Tbl MSA ON Acc.id = MSA.accountid AND PSP.id = MSA.pspid AND MSA.enabled = '1'
				WHERE CL.id = ". $clid ." AND PSP.id = ". $pspid ." AND PSP.enabled = '1' AND Acc.id = ". $accid ." AND (MA.stored_card = '0' OR MA.stored_card IS NULL)";
        return $sql;
    }

}
?>