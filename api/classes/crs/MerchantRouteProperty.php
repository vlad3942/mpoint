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
    private ?string $_sKey;

    /**
     * Holds name of the merchant property value
     * @var string
     */
    private ?string $_sValue;

    /**
     * Holds scope of the merchant property
     * @var integer
     */
    private ?int $_iScope;

    /**
     * Default Constructor
     *
     * @param 	RDB $oDB 		    Reference to the Database Object that holds the active connection to the mPoint Database
     *@param 	integer $clientId 	Unique ID for the Client performing the request
     * @param 	integer $routeConfigId	Holds unique id of the route configuration
     * @param   string $key             Hold additional property key
     * @param   string $value           Hold additional property value
     * @param   string $scope           Hold additional property scope
     */
	public function __construct(RDB $_OBJ_DB, int $clientId, int $routeConfigId , ?string $key = null, ?string $value = null, int $scope = Constants::iPrivateProperty)
	{
        $this->_objDB = $_OBJ_DB;
        $this->_iClientId = $clientId;
        $this->_iRouteConfigId = $routeConfigId;
        $this->_sKey = $key;
        $this->_sValue = html_entity_decode($value);
        $this->_iScope = $scope;
	}

    /**
     * Function used to process add additional property response
     *
     * @param array $response an array containing route feature configuration status
     * @return string XML playload structure of route additional property configuration status
     */
	public function processResponse(array $response): string
    {
        $xml = '<param>';
        $xml .= '<key>'.$this->_sKey.'</key>';
        if($response['status'] === TRUE){
            $xml .= '<status>Success</status>';
            $xml .= '<message>Configuration Successfully Updated.</message>';
        }else{
            $xml .= '<status>Fail</status>';
            $xml .= '<message>Fail To Configure Additional Property. </message>';
        }
        $xml .= '</param>';
        return $xml;
    }

    /**
     * Function used to add additional merchant property for the route
     *
     * @return array  an array response of update merchant route property
     * @throws Exception
     */
	public function AddAdditionalMerchantProperty() : bool
    {
        try {
            $sql = "INSERT INTO Client" . sSCHEMA_POSTFIX . ".AdditionalProperty_Tbl
                    (key, value, externalid, type, scope)
                    VALUES ('" . $this->_sKey . "', '" . $this->_sValue . "', '" . $this->_iRouteConfigId . "', 'merchant', $this->_iScope)";

            $res = $this->_objDB->query($sql);
            if (is_resource($res) === false) {
                throw new Exception("Unable to update route property", E_USER_ERROR);
                return FALSE;
            } else {
                return TRUE;
            }
        }catch (SQLQueryException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
            return false;
        }
    }

    /**
     * Function used to update additional merchant property for the route
     *
     * @return bool     true/false based on record updated
     * @throws Exception
     */
    private function updateMerchantAdditionalProperty():bool
    {
        try {
            $sql = "UPDATE Client" . sSCHEMA_POSTFIX . ".AdditionalProperty_Tbl
                SET value = '" . $this->_sValue . "',
                    scope = $this->_iScope
                WHERE externalid = $this->_iRouteConfigId AND key = '".$this->_sKey."'";

            $res = $this->_objDB->query($sql);
            if(is_resource($res) === true && $this->_objDB->countAffectedRows($res) === 1) {
                return true;
            }else{
                trigger_error("No Record Updated For The Route Config ID: ".$this->_iRouteConfigId, E_USER_WARNING);
                return false;
            }
        } catch (SQLQueryException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
            return false;
        }
    }

    /**
     * Function is used to get additional route property configuration
     * @return array  An array of additional route property
     */
    private function getAdditionalPropertyByRouteConfigId() : array
    {
        $aAdditionalProperties = array();
        $sql  = "SELECT key, value
					 FROM Client". sSCHEMA_POSTFIX .".AdditionalProperty_tbl
					 WHERE externalid = ". $this->_iRouteConfigId ." AND type='merchant' " ;
        try {
            $aRS = $this->_objDB->getAllNames($sql);
            if (is_array($aRS) === true && count($aRS) > 0) {
                foreach ($aRS as $rs) {
                    $aAdditionalProperties[$rs["KEY"]] = $rs["VALUE"];
                }
            }
        }catch (SQLQueryException $e){
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
        return $aAdditionalProperties;
    }

    /**
     * Function is used to update additional merchant property for given the route
     * @param array $aAdditionalProperty  Hold route additional property configuration
     * @return bool     Return true/false as a response
     * @throws Exception
     */
    public function updateAdditionalMerchantProperty(array $aAdditionalProperty)
    {
        $aExistingAdditionalProperty = MerchantRouteProperty::getAdditionalPropertyByRouteConfigId();
        if(empty($aAdditionalProperty) === false){
            foreach ($aAdditionalProperty as $key => $value){
                if(strlen($key) > 0 && empty($value) === false) {
                    $this->_sKey = $key;
                    $this->_sValue = $value['value'];
                    $this->_iScope = $value['scope'];
                    if(array_key_exists($key,$aExistingAdditionalProperty)) {
                        $states = $this->updateMerchantAdditionalProperty();
                    }else{
                        $states = $this->AddAdditionalMerchantProperty();
                    }
                    if ($states === FALSE) {
                        return FALSE;
                    }
                }else{
                    trigger_error("Found Empty Additional Property", E_USER_WARNING);
                }
            }
        }
        $aAdditionalPropertyToBeDelete = array_diff_key($aExistingAdditionalProperty, $aAdditionalProperty);
        return $this->deleteAdditionalMerchantProperty($aAdditionalPropertyToBeDelete);
    }

    /**
     * Function help to delete merchant additional property specific to the route
     * @param array $aAdditionalPropertyToBeDelete  Hold list of merchant additional property which needs to be remove
     * @return bool  Return true/false as a response
     */
    private function deleteAdditionalMerchantProperty(array $aAdditionalPropertyToBeDelete) : bool
    {
        if(empty($aAdditionalPropertyToBeDelete) === false) {
            if(empty($this->_iRouteConfigId) === false) {
                try {
                    $sql = "DELETE FROM Client".sSCHEMA_POSTFIX.".AdditionalProperty_tbl
                            WHERE externalid = ". $this->_iRouteConfigId ." 
                            AND type='merchant'
                            AND key IN  ('" . implode("','", array_keys($aAdditionalPropertyToBeDelete)) . "')";
                    return is_resource($this->_objDB->query($sql) );
                } catch (SQLQueryException $e) {
                    trigger_error($e->getMessage(), E_USER_ERROR);
                }
            }else {
                trigger_error("RouteConfigId is Missing", E_USER_WARNING);
                return false;
            }
        }
        return true;
    }

    /**
     * Function used to process add additional property response
     *
     * @param bool $response  true/flase as a update route feature configuration status
     * @return string XML playload structure of route additional property configuration status
     */
    public function getUpdateAdditionalPropertyResponseAsXML(bool $response): string
    {
        $xml = '';
        if($response === TRUE){
            $xml .= '<status>Success</status>';
            $xml .= '<message>Additional Property Updated Successfully</message>';
        }else{
            $xml .= '<status>Fail</status>';
            $xml .= '<message>Unable To Update Additional Property</message>';
        }
        return $xml;
    }

}
?>