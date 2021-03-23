<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anna Lagad
 * Copyright: Cellpoint Digital
 * Link: http://www.cellpointdigital.com
 * Project: mPoint
 * File Name:client_route_config.php
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
     * Set initialize class variable with appropriate value
     *
     * @param SimpleDOMElement $obj_DOM
     */
    public function setInputParams(RDB $_OBJ_DB, SimpleDOMElement $obj_DOM)
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

                $resource = $this->getDBConn()->prepare($sql);
                if (is_resource($resource) === true) {
                    $aParam = array( $this->_iRouteConfigId, $countryId );
                    $result = $this->getDBConn()->execute($resource, $aParam);
                    if ($result === false) {
                        throw new Exception("Unable to update route country", E_USER_ERROR);
                        return FALSE;
                    }
                } else {
                    trigger_error("Unable to build query for update route country", E_USER_WARNING);
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

                $resource = $this->getDBConn()->prepare($sql);
                if (is_resource($resource) === true) {
                    $aParam = array( $this->_iRouteConfigId, $currencyId );
                    $result = $this->getDBConn()->execute($resource, $aParam);
                    if ($result === false) {
                        throw new Exception("Unable to update route currecny", E_USER_ERROR);
                        return FALSE;
                    }
                } else {
                    trigger_error("Unable to build query for update route country", E_USER_WARNING);
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
     * Fucntion used identify wheter route already exist or not
     *
     * @return bool         Success/Failure status
     */
    private function isRouteConfigAlreadyExist() : bool
    {
        if(empty($this->_iRouteId) === false){
           $sql = "SELECT id
                FROM Client" . sSCHEMA_POSTFIX . ".RouteConfig_Tbl
                WHERE routeid = $this->_iRouteId 
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
     * Function used to add route confuguration
     * @return bool      Return final status of add route configuration
     */
    private function AddRouteConfig() : bool
    {
        $isDuplicateRouteConfig = $this->isRouteConfigAlreadyExist();
        if($isDuplicateRouteConfig === false){

            $sql = "INSERT INTO Client" . sSCHEMA_POSTFIX . ".RouteConfig_Tbl
                    (routeid, name, capturetype, mid, username, password)
                    values ($1, $2, $3, $4, $5, $6) RETURNING id";

            $resource = $this->getDBConn()->prepare($sql);
            if (is_resource($resource) === true) {

                $aParam = array(
                    $this->_iRouteId,
                    $this->_sRouteName,
                    $this->_iCaptureType,
                    $this->_sMID,
                    $this->_sUserName,
                    $this->_sPassword
                );

                $result = $this->getDBConn()->execute($resource, $aParam);

                if ($result === false) {
                    return FALSE;
                    throw new Exception("Unable to create route", E_USER_ERROR);
                } else {
                    $RS = $this->getDBConn()->fetchName($result);
                    $this->_iRouteConfigId = $RS["ID"];
                    return TRUE;
                }
            }
        }
        return FALSE;
    }

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
     * @return 	ClientRouteConfig
     */
    public static function produceConfig(RDB $oDB, $clientId) : array
    {
        $aObj_Configurations = array();

        $sql = "SELECT DISTINCT R.id, R.providerid, PSP.name AS providername
				FROM Client".sSCHEMA_POSTFIX.".Route_Tbl R
				INNER JOIN System".sSCHEMA_POSTFIX.".PSP_Tbl PSP ON PSP.id = R.providerid AND PSP.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".Client_Tbl CL ON R.clientid = CL.id AND CL.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".Account_Tbl Acc ON CL.id = Acc.clientid AND Acc.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".MerchantSubAccount_Tbl MSA ON Acc.id = MSA.accountid AND R.providerid = MSA.pspid AND MSA.enabled = '1'
				INNER JOIN SYSTEM".sSCHEMA_POSTFIX.".processortype_tbl PT ON PSP.system_type = PT.id	
				WHERE R.clientid = ". intval($clientId) ." AND R.enabled = '1'
				ORDER BY providername";

        try {
            $res = $oDB->query($sql);
            while ($RS = $oDB->fetchName($res)) {
                $sql = "SELECT RC.id AS routeid, RC.name AS routename
                    FROM Client" . sSCHEMA_POSTFIX . ".Routeconfig_Tbl RC
                    WHERE RC.routeid = " . $RS["ID"] . " AND RC.enabled = '1'
                    ORDER BY RC.id";

                $aRouteConfig = (array)$oDB->getAllNames($sql);
                $aObj_Configurations[] = new ClientRouteConfig ($RS["PROVIDERID"], $RS["PROVIDERNAME"], $aRouteConfig);
            }
        }catch (SQLQueryException $e){
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
        return $aObj_Configurations;
    }

}
?>