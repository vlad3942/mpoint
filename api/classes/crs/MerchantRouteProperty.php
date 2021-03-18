<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anna Lagad
 * Copyright: Cellpoint Digital
 * Link: http://www.cellpointdigital.com
 * Project: mPoint
 * File Name: MerchantRouteProperty.php
 */

class MerchantRouteProperty
{
    /**
     * Hold active DB connection
     * @var RDB
     */
    private $_objDB;

    /**
     * Hold an unique ID of the client
     * @var integer
     */
    private int $_iClientId;

    /**
     * Hold an unique ID of the route configuration
     * @var integer
     */
    private int $_iRouteConfigId;

    /**
     * Holds name of the merchant property key
     * @var string
     */
    private string $_sKey;

    /**
     * Holds name of the merchant property value
     * @var string
     */
    private ?string $_sValue;

    /**
     * Default Constructor
     *
     * @param 	RDB $oDB 		    Reference to the Database Object that holds the active connection to the mPoint Database
     *@param 	integer $clientId 	Unique ID for the Client performing the request
     * @param 	integer $routeConfigId	Holds unique id of the route configuration
     * @param   string $key             Hold additional property key
     * @param   string $value           Hold additional property value
     */
	public function __construct(RDB $_OBJ_DB, int $clientId, int $routeConfigId , string $key, string $value)
	{
        $this->_objDB = $_OBJ_DB;
        $this->_iClientId = $clientId;
        $this->_iRouteConfigId = $routeConfigId;
        $this->_sKey = $key;
        $this->_sValue = $value;
	}

    /**
     * @param array $response an array containing route feature configuration status
     * @return string XML playload structure of route additional property configuration status
     */
	public function processResponse(array $response): string
    {
        $xml = '<additional_property_response>';
        if($response['status'] === TRUE){
            $xml .= '<key>'.$this->_sKey.'</key>';
            $xml .= '<status>Success</status>';
            $xml .= '<message>Configuration Successfully Updated.</message>';
        }else{
            $xml .= '<status>Fail</status>';
            if($response['is_duplicate'] === TRUE){
                $xml .= '<message>Configuration Already Exist: '. "Key: ".$this->_sKey." value: ".$this->_sValue .'</message>';
            }else{
                $xml .= '<message>Fail To Configure Additinal Property. </message>';
            }
        }
        $xml .= '</additional_property_response>';
        return $xml;
    }

    /**
     * @return array  an array response of update merchant route property
     * @throws Exception
     */
	public function AddNewAdditionalMerchantProperty() : array
    {
        $response = array();
        $isRouteFeaturealreadyExist = $this->isAdditionalPropertyAlreadyExist();
        if($isRouteFeaturealreadyExist === false){
            $sql = "INSERT INTO Client" . sSCHEMA_POSTFIX . ".AdditionalProperty_Tbl
                (key, value, externalid, type)
                values ($1, $2, $3, $4)";

            $resource = $this->_objDB->prepare($sql);
            if (is_resource($resource) === true) {
                $aParam = array( $this->_sKey, $this->_sValue, $this->_iRouteConfigId, 'merchant');
                $result = $this->_objDB->execute($resource, $aParam);
                if ($result === false) {
                    throw new Exception("Unable to update route property", E_USER_ERROR);
                    $response['status'] = FALSE;
                }else{
                    $response['status'] = TRUE;
                }
            } else {
                trigger_error("Unable to build query for update route property", E_USER_WARNING);
                $response['status'] = FALSE;
            }
        }else{
            trigger_error('Configuration Already Exist For Route: '.$this->_iRouteConfigId , E_USER_NOTICE);
            $response['status'] = FALSE;
            $response['is_duplicate'] = $isRouteFeaturealreadyExist;
        }
        return $response;
    }

    /**
     * Function used to identify duplicate record
     * @return bool
     */
    private function isAdditionalPropertyAlreadyExist() :bool
    {
        $sql = "SELECT id
                    FROM Client" . sSCHEMA_POSTFIX . ".AdditionalProperty_Tbl
                    WHERE externalid = $this->_iRouteConfigId
                    AND lower(key) = '".strtolower($this->_sKey)."'
                    AND lower(value) = '".strtolower($this->_sValue)."' 
                    AND type = 'merchant'";
        try {
            $res = $this->_objDB->getName($sql);
            if (is_array($res) === true && count($res) > 0) {
                return true;
            }
        }catch (SQLQueryException $e){
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
        return false;
    }



}
?>