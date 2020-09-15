<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Badave
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package:
 * File Name:fraud_response.php
 */
class PaymentSecureInfo
{
    /**
     * Holds authentication status
     * in case of modirum this is referred as mdStatus
     * in case of First Data this is referred as Secure3DResponse
     * @var integer
     */
    private $_iStatus;

    /**
     * Holds authentication Message
     *
     * @var string
     */
    private $_sMsg;

    /**
     * Holds Verify Enrollment Response enrolled status
     *
     * @var string
     */
    private $_sVeresEnrolledStatus;

    /**
     * Holds Payer Authentication Response status
     *
     * @var string
     */
    private $_sParestxstatus;

    /**
     * Holds eci value returned by MPI/wallet/PSP/Acquirer
     *
     * @var int
     */
    private $_iEci;
    /**
     * Holds CAVV(Cardholder Authentication Verification Value) value returned by MPI/wallet/PSP/Acquirer
     *
     * @var string
     */
    private $_sCavv;
    /**
     * Holds algorithm used to generate the CAVV value
     *
     * @var string
     */
    private $_iCavvAlgorithm;
    /**
     * Unique ID for the The PSP used for the transaction
     *
     * @var integer
     */
    private $_iPSPID ;
    /**
     * Unique ID for the Transaction
     *
     * @var integer
     */
    private $_iTxnID;
    /**
     * Holds Protocol
     *
     * @var integer
     */
    private $_sProtocol;


    /**
     * Default Constructor
     *
     * @param integer $iTxnId 	Unique ID for the Transaction
     * @param integer $iPSPID   Unique ID for the The PSP used for the transaction
     * @param string  $iStatus  authentication status
     * @param string  $sMsg     authentication Message
     * @param string  $sVeresEnrolledStatus Holds Verify Enrollment Response enrolled status
     * @param string  $sParestxstatus Payer Authentication Response status
     * @param integer  $iEci eci value returned by MPI/wallet/PSP/Acquirer
     * @param string  $sCavv CAVV(Cardholder Authentication Verification Value) value returned by MPI/wallet/PSP/Acquirer
     * @param integer  $iCavvAlgorithm algorithm used to generate the CAVV value
     */
    public function __construct( $iTxnId,$iPSPID, $iStatus, $sMsg, $sVeresEnrolledStatus,$sParestxstatus,$iEci,$sCavv,$iCavvAlgorithm,$sProtocol)
    {
        $this->_iTxnID = $iTxnId;
        $this->_iPSPID = $iPSPID;
        $this->_iStatus = $iStatus;
        $this->_sMsg = $sMsg;
        $this->_sVeresEnrolledStatus = $sVeresEnrolledStatus;
        $this->_sParestxstatus = $sParestxstatus;
        $this->_iEci = $iEci;
        $this->_sCavv = $sCavv;
        $this->_iCavvAlgorithm = $iCavvAlgorithm;
        $this->_sProtocol = $sProtocol;

    }
    public function getTransactionID() { return $this->_iTxnID; }
    public function getPSPID() { return $this->_iPSPID; }
    public function getStatus() { return $this->_iStatus; }
    public function getMsg() { return $this->_sMsg; }
    public function getVeresEnrolledStatus() { return $this->_sVeresEnrolledStatus; }
    public function getParestxstatus() { return $this->_sParestxstatus; }
    public function getECI() { return $this->_iEci; }
    public function getCAVV() { return $this->_sCavv; }
    public function getCavvAlgorithm() { return $this->_iCavvAlgorithm; }
    public function getProtocol() { return $this->_sProtocol; }

    private static function _produceInfoFromXML(SimpleXMLElement $obj_XML,$iPSPID,$iTxnID)
    {
        $aPaymentSecureData = array();

        if($obj_XML->{'cryptogram'})
        {
            $aPaymentSecureData['eci'] = (integer)$obj_XML->{'cryptogram'}["eci"];
            $aPaymentSecureData['cavv'] = (string)$obj_XML->{'cryptogram'};
            $aPaymentSecureData['cavvAlgorithm'] =(integer) $obj_XML->{'cryptogram'}["algorithm-id"];
        }

        for ($j=0; $j<count($obj_XML->{'additional-data'}->param); $j++ )
        {
            $sKey = (string) $obj_XML->{'additional-data'}->param[$j]['name'];
            $sValue = (string) $obj_XML->{'additional-data'}->param[$j];
            $aPaymentSecureData[$sKey] = $sValue;
        }
        if(empty($aPaymentSecureData) === false)
        {
            return new PaymentSecureInfo( $iTxnID, $iPSPID, $aPaymentSecureData['status'], $aPaymentSecureData['msg'] ,$aPaymentSecureData['veresEnrolledStatus'],$aPaymentSecureData['paresTxStatus'],(int)$aPaymentSecureData['eci'],$aPaymentSecureData['cavv'],(int)$aPaymentSecureData['cavvAlgorithm'],$aPaymentSecureData['protocol']);
        }
        else { return null; }
    }
    private static function _produceInfoFromDatabase(RDB &$oDB, $iTxnId)
    {
        $sql = "SELECT  txnid, pspid, status, msg, veresEnrolledStatus, paresTxStatus,eci,cavv,cavvAlgorithm, protocol 
        FROM LOG".sSCHEMA_POSTFIX.".paymentsecureinfo_tbl WHERE txnid=".$iTxnId;
        $RS = $oDB->getName($sql);

        if (is_array($RS) === true && count($RS) > 0)
        {
            return new PaymentSecureInfo($iTxnId, $RS["PSPID"], $RS["STATUS"], $RS["MSG"],$RS["VERESENROLLEDSTATUS"],$RS["PARESTXSTATUS"],$RS["ECI"],$RS["CAVV"],$RS["CAVVALGORITHM"],$RS["PROTOCOL"]);
        }
        else { return null; }
    }

    public static function produceInfo()
    {
        $aArgs = func_get_args();
        switch (count($aArgs) )
        {
            case (2):
                return self::_produceInfoFromDatabase($aArgs[0], $aArgs[1]);
                break;
            case (3):
                return self::_produceInfoFromXML($aArgs[0], $aArgs[1], $aArgs[2]);
                break;
            default:
                return null;
                break;
        }
    }

    public function attachPaymentSecureNode(SimpleXMLElement &$obj_XML)
    {
        $obj_XML->addChild('info-3d-secure');
        $obj_XML->{'info-3d-secure'}->addChild('cryptogram',$this->getCAVV());
        $obj_XML->{'info-3d-secure'}->{'cryptogram'}->addAttribute('eci',$this->getECI());
        $obj_XML->{'info-3d-secure'}->{'cryptogram'}->addAttribute('algorithm-id',$this->getCavvAlgorithm());
        $obj_XML->{'info-3d-secure'}->addChild('additional-data');
        $obj_XML->{'info-3d-secure'}->{'additional-data'}->addChild('param',$this->getStatus())->addAttribute('name','status');
        $obj_XML->{'info-3d-secure'}->{'additional-data'}->addChild('param',$this->getMsg())->addAttribute('name','msg');
        $obj_XML->{'info-3d-secure'}->{'additional-data'}->addChild('param',$this->getVeresEnrolledStatus())->addAttribute('name','veresEnrolledStatus');
        $obj_XML->{'info-3d-secure'}->{'additional-data'}->addChild('param',$this->getParestxstatus())->addAttribute('name','paresTxStatus');
        $obj_XML->{'info-3d-secure'}->{'additional-data'}->addChild('param',$this->getECI())->addAttribute('name','eci');
        $obj_XML->{'info-3d-secure'}->{'additional-data'}->addChild('param',$this->getCAVV())->addAttribute('name','cavv');
        $obj_XML->{'info-3d-secure'}->{'additional-data'}->addChild('param',$this->getCavvAlgorithm())->addAttribute('name','cavvAlgorithm');
        $obj_XML->{'info-3d-secure'}->{'additional-data'}->addChild('param',$this->getProtocol())->addAttribute('name','protocol');
    }
}