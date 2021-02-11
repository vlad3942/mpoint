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
     * Hold uniquir ID of the route configuration
     * @var integer
     */
    private $_iRouteConfigId;

    /**
     * Hold list of route features
     * @var array
     */
    private $_aRouteFeature;

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
	public function __construct($id, $name, $system_type, $ma, $msa, $un, $pw, array $aMsgs=array(),$aAdditionalProperties=array(), $routeConfigId = -1,  $aRouteFeature = array())
	{
		parent::__construct($id, $name);
		$this->_sMerchantAccount = trim($ma);
		$this->_sMerchantSubAccount = trim($msa);
		$this->_iType = intval($system_type);
		$this->_sUsername = trim($un);
		$this->_sPassword = trim($pw);
		$this->_aMessages = $aMsgs;
		$this->_aAdditionalProperties =$aAdditionalProperties;
        $this->_iRouteConfigId = $routeConfigId;
        $this->_aRouteFeature = $aRouteFeature;
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

    /**
     * Returns unique route configuration ID
     *
     * @return 	integer
     */
	public function getRouteConfigId() { return $this->_iRouteConfigId; }

	public function toXML($propertyScope=2, $aMerchantAccountDetails = array())
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

    public function toAttributeLessXML($propertyScope=2, $aMerchantAccountDetails = array())
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

    public function toRouteConfigXML()
    {
        $xml = '<route_configuration>';
        $xml .= '<id>'. $this->getRouteConfigId() .'</id>';
        $xml .= '<route_id>'. $this->getID() .'</route_id>';
        $xml .= '<name>'. $this->getName() .'</name>';
        $xml .= '<mid>'. $this->getMerchantAccount() .'</mid>';
        $xml .= '<username>'. $this->getUsername() .'</username>';
        $xml .= '<password>'. $this->getPassword() .'</password>';
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

            $sql  = "SELECT key,value, scope
					 FROM Client". sSCHEMA_POSTFIX .".AdditionalProperty_tbl
					 WHERE externalid = ". intval($RS["MERCHANTID"]) ." and type='merchant' and enabled=true" ;
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

			return new PSPConfig($RS["ID"], $RS["NAME"], $RS["SYSTEM_TYPE"], $RS["MA"], $RS["MSA"], $RS["USERNAME"], $RS["PASSWORD"], $aMessages,$aAdditionalProperties);
		}
		else
		{
			trigger_error("PSP Configuration not found using Client ID: ". $clid .", Account: ". $accid .", PSP ID: ". $pspid, E_USER_WARNING);
			return null;
		}
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
    public function getAdditionalProperties($scope, $key = '')
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
     * @return 	PSPConfig
     */
    public static function produceConfiguration(RDB $oDB, $clid, $accid, $pspid, $routeconfigid)
    {
        $sql = "SELECT DISTINCT PSP.id, PSP.name, PSP.system_type, RC.mid AS ma, RC.username, RC.password, MSA.name AS msa, R.id as MerchantId, RC.id AS routeconfigid
				FROM System".sSCHEMA_POSTFIX.".PSP_Tbl PSP
				INNER JOIN Client".sSCHEMA_POSTFIX.".Route_Tbl R ON PSP.id = R.providerid AND R.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".Routeconfig_Tbl RC ON R.id = RC.routeid AND RC.enabled = '1'
                INNER JOIN Client".sSCHEMA_POSTFIX.".RouteCountry_Tbl RCON ON RC.id = RCON.routeconfigid AND RCON.enabled = '1'
                INNER JOIN Client".sSCHEMA_POSTFIX.".RouteCurrency_Tbl RCUR ON RC.id = RCUR.routeconfigid AND RCUR.enabled = '1'				
				INNER JOIN Client".sSCHEMA_POSTFIX.".Client_Tbl CL ON R.clientid = CL.id AND CL.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".Account_Tbl Acc ON CL.id = Acc.clientid AND Acc.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".MerchantSubAccount_Tbl MSA ON Acc.id = MSA.accountid AND PSP.id = MSA.pspid AND MSA.enabled = '1'
				INNER JOIN SYSTEM".sSCHEMA_POSTFIX.".processortype_tbl PT ON PSP.system_type = PT.id
				WHERE CL.id = ". (int)$clid ." AND PSP.enabled = '1' 
				    AND Acc.id = ". (int)$accid ." AND RC.id = ". (int)$routeconfigid;

        $RS = $oDB->getName($sql);

        if (is_array($RS) === true && count($RS) > 1)
        {
            $sql = "SELECT I.language, I.text
					FROM Client".sSCHEMA_POSTFIX.".Info_Tbl I
					INNER JOIN Client".sSCHEMA_POSTFIX.".InfoType_Tbl IT ON I.infotypeid = IT.id AND IT.enabled = '1'
					WHERE I.clientid = ". intval($clid) ." AND IT.id = ". Constants::iPSP_MESSAGE_INFO ." AND (I.pspid = ". intval($pspid) ." OR I.pspid IS NULL)";

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
					 WHERE externalid = ". intval($RS["MERCHANTID"]) ." and type='merchant' and enabled=true" ;

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

            //Get route feature
            $sql  = "SELECT CRF.id, CRF.enabled, SRF.featurename
					 FROM Client". sSCHEMA_POSTFIX .".RouteFeature_Tbl CRF
					 INNER JOIN System". sSCHEMA_POSTFIX .".RouteFeature_Tbl SRF ON CRF.featureid = SRF.id AND SRF.enabled = '1'
					 WHERE routeconfigid = ". intval($RS["ROUTECONFIGID"]);

            $aRouteFeature = $oDB->getAllNames($sql);
            return new PSPConfig($RS["ID"], $RS["NAME"], $RS["SYSTEM_TYPE"], $RS["MA"], $RS["MSA"], $RS["USERNAME"], $RS["PASSWORD"], $aMessages,$aAdditionalProperties, $RS["ROUTECONFIGID"], $aRouteFeature);
        }
        else
        {
            trigger_error("PSP Configuration not found using Client ID: ". $clid .", Account: ". $accid .", PSP ID: ". $pspid .", Route Config ID: ". $routeconfigid, E_USER_WARNING);
            return null;
        }
    }

}
?>