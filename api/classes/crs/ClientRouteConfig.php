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
	private int $_iProviderId;

    /**
     * The name of service provider
     *
     * @var string
     */
	private string $_sProviderName;

    /**
     * Hold list of route configurations
     *
     * @var array
     */
    private array $_aRouteConfig;

    /**
     * Default Constructor
     *
     * @param 	integer $providerId 	Unique ID for the Payment Service Provider in mPoint
     * @param 	string $providerName	Payment Service Provider's name in mPoint
     * @param 	string $aRouteConfig 	Hold List of Route Configuration
     */
	public function __construct(int $providerId, string $providerName, array $aRouteConfig)
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

        $sql = "SELECT R.id, R.providerid, PSP.name AS providername
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