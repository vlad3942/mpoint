<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anna Lagad
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package:
 * File Name:routing_service_response.php
 */

class FraudResponse
{
    /**
     * Hold fraud type
     *
     * @var integer
     */
    private $_iFraudType;

    /**
     * Hold fraud status code
     *
     * @var integer
     */
    private $_iStatusCode;

    /**
     * Hold Description
     *
     * @var string
     */
    private $_sDesc;

    /**
     * Hold Description
     *
     * @var string
     */
    private $_sExternalRef;

    /**
     * Default Constructor
     *
     * @param integer $iFraudType 	Fraud Type
     * @param integer $iStatusCode  Fraud attempt status code
     * @param string  $sDesc
     * @param string  $sExternalRef external reference returned by Fraud Provider
     */
    public function __construct( $iFraudType,$iStatusCode, $sDesc, $sExternalRef )
    {
       $this->_iFraudType = $iFraudType;
       $this->_sDesc = $sDesc;
       $this->_sExternalRef = $sExternalRef;

        //Fraud Check endpoint will return result status code PRE or POST auth need be determine
        //30 for pre-auth and 31 for post-auth if service return 11 and $iFraudType id pre it become 3011 represents pre-auth fraud Accepted
        if($iFraudType === Constants::iPROCESSOR_TYPE_PRE_FRAUD_GATEWAY)
        {
            $this->_iStatusCode = (int) (substr((string)Constants::iPRE_FRAUD_CHECK_INITIATED_STATE,0,2).$iStatusCode);
        }
        else { $this->_iStatusCode = (int) (substr((string)Constants::iPOST_FRAUD_CHECK_INITIATED_STATE,0,2).$iStatusCode); }

    }
    public function getFraudType() { return $this->_iFraudType; }
    public function getStatusCode() { return $this->_iStatusCode; }
    public function getDescription() { return $this->_sDesc; }
    public function getExternalID() { return $this->_sExternalRef; }

    public static function produceInfoFromXML($iFraudType, SimpleXMLElement $obj_XML)
    {
        return new FraudResponse( $iFraudType, (int) $obj_XML->status["code"], (string)$obj_XML->status, (string)$obj_XML->externalId );
    }



}