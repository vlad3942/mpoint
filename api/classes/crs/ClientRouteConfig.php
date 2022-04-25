<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anna Lagad
 * Copyright: Cellpoint Digital
 * Link: http://www.cellpointdigital.com
 * Project: mPoint
 * File Name:ClientRouteConfig.php
 */

class ClientRouteConfig
{

	/**
	 * Unique ID Of the payment service provider
	 *
	 * @var integer
	 */
	private ?int $_iProviderId;

    /**
     * The name of service provider
     *
     * @var string
     */
	private ?string $_sProviderName;

    /**
     * Hold list of route configurations
     *
     * @var array
     */
    private array $_aRouteConfig;

    private $_objDB;
    private int $_iClientId;
    private string $_sRouteName;
    private int $_iCaptureType;
    private string $_sMID;
    private string $_sUserName;
    private string $_sPassword;
    private int $_iRouteConfigId = -1;
    private int $_iRouteId;
    private array $_aCountryId = array(null);
    private array $_aCurrencyId = array(null);

    /**
     * Default Constructor
     *
     * @param 	integer $providerId 	Unique ID for the Payment Service Provider in mPoint
     * @param 	string $providerName	Payment Service Provider's name in mPoint
     * @param 	string $aRouteConfig 	Hold List of Route Configuration
     */
	public function __construct(?int $providerId = null, ?string $providerName = null, array $aRouteConfig = array())
	{
        $this->_iProviderId = $providerId;
        $this->_sProviderName = $providerName;
        $this->_aRouteConfig = $aRouteConfig;
	}

    /**
     * Hold unique id of the Payment Service Provider
     *
     * @return 	integer
     */
	public function getProviderId() : int
    {
        return $this->_iProviderId;
    }

    /**
     * Returns the name of Payment Service Provider
     *
     * @return 	string
     */
	public function getProviderName() : string
    {
        return $this->_sProviderName;
    }

    /**
     * Returns the active Database connection.
     *
     * @return RDB
     */
    protected function &getDBConn() { return $this->_objDB; }

    /**
     * Initialize class variable with appropriate value
     *
     * @param RDB $oDB 		    Reference to the Database Object that holds the active connection to the mPoint Database
     * @param SimpleDOMElement $obj_DOM
     */
    public function setInputParams(RDB $_OBJ_DB, SimpleDOMElement $obj_DOM) : void
    {
        $this->_objDB = $_OBJ_DB;
        if ( ($obj_DOM instanceof SimpleDOMElement) === true)
        {
            $this->_iClientId = (int)$obj_DOM->client_id;
            $this->_iRouteId = (int)$obj_DOM->route_id;
            $this->_sRouteName = (string)$obj_DOM->route_name;
            $this->_iCaptureType = (int)$obj_DOM->capture_type;
            $this->_sMID = (string)$obj_DOM->mid;
            $this->_sUserName = (string)$obj_DOM->username;
            $this->_sPassword = (string)$obj_DOM->password;
            if($obj_DOM->country_ids->country_id instanceof SimpleDOMElement) {
                $this->_aCountryId = (array)$obj_DOM->country_ids->country_id;
            }
            if($obj_DOM->currency_ids->currency_id instanceof SimpleDOMElement) {
                $this->_aCurrencyId = (array)$obj_DOM->currency_ids->currency_id;
            }
            if(empty($obj_DOM->id ) === false){
                $this->_iRouteConfigId = (int)$obj_DOM->id;
            }
        }
    }

    /**
     * Function used to process add route config response for end user
     *
     * @param array $response an array containing route configuration status
     * @return string XML playload structure of final response
     */
    public function processResponse(array $response):string
    {
        $xml = '';
        if($response['status'] === TRUE){
            $xml .= '<status>Success</status>';
            $xml .= '<route_config_id>'.$this->_iRouteConfigId.'</route_config_id>';
            $xml .= '<message>Route Configuration Created Successfully.</message>';
        }else{
            $xml .= '<status>Fail</status>';
            $xml .= '<route_config_id>'.$this->_iRouteConfigId.'</route_config_id>';
            $xml .= '<message>Unable to Create Route Configuration. </message>';
        }
        return $xml;
    }

    /**
     * Function used to add route configuration
     *
     * @return array              An array of final response with route configuration status
     * @throws SQLQueryException
     */
    public function AddRoute() : array
    {
        $this->getDBConn()->query('START TRANSACTION');
        $response['status'] = $this->AddRouteConfig();
        if($response['status'] === TRUE) {
            $addRouteCountryStatus = $this->addRouteCountry();
            $addRouteCurrencyStatus = $this->addRouteCurrency();
            if ($addRouteCountryStatus === TRUE && $addRouteCurrencyStatus === TRUE) {
                $this->getDBConn()->query('COMMIT');
                $response['status'] = TRUE;
            }else{
                $this->getDBConn()->getDBConn()->query('ROLLBACK');
                $response['status'] = FALSE;
            }
        }
        return $response;
    }

    /**
     * Function used to add route country
     *
     * @return bool        Success/Failure status
     * @throws Exception
     */
    private function addRouteCountry(): bool
    {
        if(empty($this->_iRouteConfigId) === FALSE) {
            foreach ($this->_aCountryId as $countryId){
                $sql = "INSERT INTO Client" . sSCHEMA_POSTFIX . ".RouteCountry_Tbl
                    (routeconfigid, countryid)
                    values ($1, $2)";

                $aParam = array( $this->_iRouteConfigId, $countryId );
                $result = $this->getDBConn()->executeQuery($sql, $aParam);
                if ($result === false) {
                    trigger_error("Unable to build query for update route country", E_USER_WARNING);
                    throw new Exception("Unable to update route country", E_USER_ERROR);
                    return FALSE;
                }

            }
            return TRUE;
        } else {
            trigger_error("RouteConfigId Not Found", E_USER_WARNING);
            return FALSE;
        }
    }

    /**
     * Function used to add route currency
     *
     * @return bool        Success/Failure status
     * @throws Exception
     */
    private function addRouteCurrency() : bool
    {
        if(empty($this->_iRouteConfigId) === FALSE) {
            foreach ($this->_aCurrencyId as $currencyId){
                $sql = "INSERT INTO Client" . sSCHEMA_POSTFIX . ".RouteCurrency_Tbl
                    (routeconfigid, currencyid)
                    values ($1, $2)";

                $aParam = array( $this->_iRouteConfigId, $currencyId );
                $result = $this->getDBConn()->executeQuery($sql, $aParam);
                if ($result === false) {
                    trigger_error("Unable to build query for update route country", E_USER_WARNING);
                    throw new Exception("Unable to update route currecny", E_USER_ERROR);
                    return FALSE;
                }

            }
            return TRUE;
        } else {
            trigger_error("RouteConfigId Not Found", E_USER_WARNING);
            return FALSE;
        }
    }

    /**
     * Fucntion used identify whether route configuration already exist or not
     *
     * @return bool         Success/Failure status
     */
    private function isRouteConfigAlreadyExist() : bool
    {
        if(empty($this->_iRouteId) === false){
           $sql = "SELECT id
                FROM Client" . sSCHEMA_POSTFIX . ".RouteConfig_Tbl
                WHERE routeid = $this->_iRouteId 
                AND name = '" . $this->_sRouteName . "'
                AND mid = '".$this->_sMID."'
                AND username = '".$this->_sUserName."'
                AND password = '".$this->_sPassword."'
                AND capturetype = $this->_iCaptureType";
            $res = $this->getDBConn()->getName($sql);
            if (is_array($res) === true && count($res) > 0) {
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * Function used to add route configuration
     * @return bool      Return final status of add route configuration
     */
    private function AddRouteConfig() : bool
    {
        $isDuplicateRouteConfig = $this->isRouteConfigAlreadyExist();
        if($isDuplicateRouteConfig === false){

            $sql = "INSERT INTO Client" . sSCHEMA_POSTFIX . ".RouteConfig_Tbl
                    (routeid, name, capturetype, mid, username, password)
                    values ($1, $2, $3, $4, $5, $6) RETURNING id";

            $aParam = array(
                $this->_iRouteId,
                $this->_sRouteName,
                $this->_iCaptureType,
                $this->_sMID,
                $this->_sUserName,
                $this->_sPassword
            );

            $result = $this->getDBConn()->executeQuery($sql, $aParam);

            if ($result === false) {
                return FALSE;
            } 

            $RS = $this->getDBConn()->fetchName($result);
            $this->_iRouteConfigId = $RS["ID"];
            return TRUE;

        }
        return FALSE;
    }

    /**
     * Produce route configuration response in the form of XML
     * @return string  XML playload structure of route feature configuration status
     */
    public function toXML() : string
    {
      $xml = '<payment_provider>';
      $xml .= '<id>'. $this->getProviderId() .'</id>';
      $xml .= '<name>'. $this->getProviderName() .'</name>';
      $xml .= '<route_configurations>';
      if(is_array($this->_aRouteConfig) && count($this->_aRouteConfig) > 0){
          foreach ($this->_aRouteConfig as $routeConfig){
              $xml .= '<route_configuration>';
              $xml .= '<id>'. $routeConfig['ROUTEID'] .'</id>';
              $xml .= '<route_name>'. $routeConfig['ROUTENAME'] .'</route_name>';
              $xml .= '</route_configuration>';
          }
      }
      $xml .= '</route_configurations>';
      $xml .= '</payment_provider>';
      return $xml;
    }

    /**
     * Produces a new instance of a Client Route Configuration Object.
     *
     * @param 	RDB $oDB 		    Reference to the Database Object that holds the active connection to the mPoint Database
     * @param 	integer $clientId 	Unique ID for the Client performing the request
     * @return 	ClientRouteConfig   An array of Client Route Configuration Object
     */
    public static function produceConfig(RDB $oDB, $clientId) : array
    {
        $aObj_Configurations = array();

        $sql = "SELECT DISTINCT R.id, R.providerid, PSP.name AS providername
				FROM Client".sSCHEMA_POSTFIX.".Route_Tbl R
				INNER JOIN System".sSCHEMA_POSTFIX.".PSP_Tbl PSP ON PSP.id = R.providerid AND PSP.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".Client_Tbl CL ON R.clientid = CL.id AND CL.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".Account_Tbl Acc ON CL.id = Acc.clientid AND Acc.enabled = '1'
				LEFT JOIN Client".sSCHEMA_POSTFIX.".MerchantSubAccount_Tbl MSA ON Acc.id = MSA.accountid AND R.providerid = MSA.pspid AND MSA.enabled = '1'
				INNER JOIN SYSTEM".sSCHEMA_POSTFIX.".processortype_tbl PT ON PSP.system_type = PT.id	
				WHERE R.clientid = ". intval($clientId) ." AND R.enabled = '1'
				ORDER BY providername";

        try {
            $res = $oDB->query($sql);
            while ($RS = $oDB->fetchName($res)) {
                $sql = "SELECT RC.id AS routeid, RC.name AS routename
                    FROM Client" . sSCHEMA_POSTFIX . ".Routeconfig_Tbl RC
                    WHERE RC.routeid = " . $RS["ID"] . " AND RC.enabled = '1' AND RC.isdeleted = '0'
                    ORDER BY RC.id";

                $aRouteConfig = (array)$oDB->getAllNames($sql);
                $aObj_Configurations[] = new ClientRouteConfig ($RS["ID"], $RS["PROVIDERNAME"], $aRouteConfig);
            }
        }catch (SQLQueryException $e){
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
        return $aObj_Configurations;
    }

    /**
     * Function used to update route configuration
     * @return bool   true/false as a update route configuration response
     */
    public function updateRoute() : bool
    {
        $this->getDBConn()->query('START TRANSACTION');
        $isDuplicateRouteConfig = $this->isRouteConfigAlreadyExist();
        $updateRouteConfigStatus = TRUE;
        if($isDuplicateRouteConfig === FALSE){
            $updateRouteConfigStatus = $this->updateRouteConfig();
        }
        if($updateRouteConfigStatus === TRUE)
        {
            $updateRouteCountryStatus = $this->updateRouteCountry();
            $updateRouteCurrencyStatus = $this->updateRouteCurrency();
            if ($updateRouteCountryStatus === TRUE && $updateRouteCurrencyStatus === TRUE) {
                $this->getDBConn()->query('COMMIT');
                return TRUE;
            }else{
                if($isDuplicateRouteConfig === FALSE){
                    $this->getDBConn()->getDBConn()->query('ROLLBACK');
                }
                trigger_error("Unable To Update Route Country/Currecny For The Route: ".$this->_iRouteConfigId, E_USER_WARNING);
                return FALSE;
            }
        } else {
            trigger_error("Unable To Update Route Configuration For The Route: ".$this->_iRouteConfigId, E_USER_WARNING);
            return FALSE;
        }
    }

    /**
     * Function used to update route configuration
     * @return bool  Return true upon successful query executation or else retrun false
     */
    private function updateRouteConfig() : bool
    {
        try {
            $sql = "UPDATE Client" . sSCHEMA_POSTFIX . ".Routeconfig_Tbl
            SET routeid = '" . $this->_iRouteId . "', name = '" . $this->_sRouteName . "', capturetype = '" . $this->_iCaptureType . "',
                mid = '" . $this->_sMID . "', username= '" . $this->_sUserName . "', password = '" . $this->_sPassword . "'
            WHERE id = " . $this->_iRouteConfigId;

            return is_resource($this->getDBConn()->query($sql));
        } catch (SQLQueryException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
            return false;
        }
    }

    /**
     * Function used to update the supported country for the route
     * @return bool   success / failure response
     * @throws Exception
     */
    private function updateRouteCountry() : bool
    {
        $aCountryId = array();
        $aObj_RouteCountry = ClientRouteCountry::produceConfig($this->_objDB, $this->_iRouteConfigId);
        if (empty($aObj_RouteCountry) === false) {
            foreach ($aObj_RouteCountry as $objCountry) {
                if ($objCountry instanceof ClientRouteCountry) {
                    $aCountryId[] =  $objCountry->getCountryID();
                }
            }
        }
        $obj_RouteCountry = new ClientRouteCountry ($this->_objDB);
        $aCountryIdToBeAdd  = array_diff($this->_aCountryId, $aCountryId);
        $aCountryIdToBeDelete = array_diff($aCountryId, $this->_aCountryId);
        $status = $obj_RouteCountry->addRouteCountry($this->_iRouteConfigId, $aCountryIdToBeAdd);
        if($status === true) {
            return $obj_RouteCountry->deleteRouteCountry($this->_iRouteConfigId, $aCountryIdToBeDelete);
        }
        return true;
    }

    /**
     * Function used to update the supported currency for the route
     * @return bool   success / failure response
     * @throws Exception
     */
    private function updateRouteCurrency() : bool
    {
        $aCurrencyId = array();
        $aObj_RouteCurrency = ClientRouteCurrency::produceConfig($this->_objDB, $this->_iRouteConfigId);
        if (empty($aObj_RouteCurrency) === false) {
            foreach ($aObj_RouteCurrency as $objCurrency) {
                if ($objCurrency instanceof ClientRouteCurrency) {
                    $aCurrencyId[] =  $objCurrency->getCurrencyID();
                }
            }
        }
        $obj_RouteCurrency = new ClientRouteCurrency ($this->_objDB);
        $aCurrencyIdToBeAdd  = array_diff($this->_aCurrencyId, $aCurrencyId);
        $aCurrencyIdToBeDelete = array_diff($aCurrencyId, $this->_aCurrencyId);
        $status = $obj_RouteCurrency->addRouteCurrency($this->_iRouteConfigId, $aCurrencyIdToBeAdd);
        if($status === true) {
            return $obj_RouteCurrency->deleteRouteCurrency($this->_iRouteConfigId, $aCurrencyIdToBeDelete);
        }
        return true;
    }

    /**
     * Function used to process update route config response
     *
     * @param bool $response  An array containing route configuration status
     * @return string         XML playload structure of final response
     */
    public function getUpdateRouteResponseAsXML(bool $response):string
    {
        $xml = '';
        if($response === TRUE){
            $xml .= '<status>Success</status>';
            $xml .= '<message>Route Configuration Updated Successfully.</message>';
        }else{
            $xml .= '<status>Fail</status>';
            $xml .= '<message>Unable To Update Route Configuration. </message>';
        }
        return $xml;
    }

}
?>