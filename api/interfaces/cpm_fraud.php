<?php
/* ==================== Callback Exception Classes Start ==================== */
/**
 * Exception class for all Callback exceptions
 */
class CPMFraudEXCEPTION extends mPointException { }
/* ==================== Callback Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for handling the Callback request from the Payment Service Provider (PSP).
 * The class contains methods that completes the transaction log with information received from the PSP, notifies the Client
 * and sends out an SMS Receipt to the Customer.
 *
 */
abstract class CPMFRAUD
{

    /**
     * Data object with the Transaction Information
     *
     * @var TxnInfo
     */
    private $_obj_TxnInfo;

    /**
     * Data array with Connection Information for the specific PSP
     *
     * @var array
     */
    protected $aCONN_INFO;

    /**
     * Data object with PSP configuration Information
     *
     * @var PSPConfig
     */
    private $_obj_PSPConfig;

    private $_obj_mPoint;

    private $_oDB;


    /**
     * Default Constructor.
     *
     * @param	RDB $oDB				Reference to the Database Object that holds the active connection to the mPoint Database
     * @param	TranslateText $oTxt 	Text Translation Object for translating any text into a specific language
     * @param 	TxnInfo $oTI 			Data object with the Transaction Information
     * @param 	PSPConfig $oPSPConfig 	Configuration object with the PSP Information
     */
    public function __construct(RDB $oDB, TranslateText $oTxt, TxnInfo $oTI, array $aConnInfo)
    {
        $this->_obj_TxnInfo = $oTI;
        $this->_oDB = $oDB;
        $iFSPID = $this->getFPSID();
        if(empty($aConnInfo) === false )
        {
            $this->aCONN_INFO = $aConnInfo;
        }
        else
        {
            throw new CPMFraudEXCEPTION("Connection Configuration not found for the given FSP ID ". $iFSPID);
        }

        $this->_obj_PSPConfig = $oPSPConfig = PSPConfig::produceConfig($oDB, $oTI->getClientConfig()->getID(), $oTI->getClientConfig()->getAccountConfig()->getID(), $iFSPID);
        $this->_obj_mPoint = new General($oDB, $oTxt);
    }

    abstract protected function getFPSID();
    /**
     * Returns the Data object with the Transaction Information.
     *
     * @return TxnInfo
     */
    public function getTxnInfo() { return $this->_obj_TxnInfo; }


    /**
     * Returns the Configuration object with the PSP Information.
     *
     * @return PSPConfig
     */
    public function getPSPConfig() { return $this->_obj_PSPConfig; }
    public function getDBConn() { return $this->_oDB; }



    public static function produceFSP(RDB &$obj_DB, TranslateText &$obj_Txt, TxnInfo &$obj_TxnInfo, array $aConnInfo, $iFSPID)
    {
        switch ($iFSPID)
        {
             case (Constants::iEZY_PSP):
                return new EZY($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo["ezy"]);
            default:
                throw new CallbackException("Unknown Fraud Service Provider: ". $obj_TxnInfo->getPSPID() ." for transaction: ". $obj_TxnInfo->getID(), 1001);
        }
    }

    public static function attemptFraudCheckIfRoutePresent($obj_Card,RDB &$obj_DB,ClientInfo &$clientInfo, TranslateText &$obj_Txt, TxnInfo &$obj_TxnInfo, array $aConnInfo,CreditCard &$obj_mCard,$cardTypeId,$iFraudType = Constants::iPROCESSOR_TYPE_PRE_FRAUD_GATEWAY)
    {
        $iFSPRoutes = $obj_mCard->getFraudCheckRoute($cardTypeId) ;
        $aFSPStatus = array();

        while ($RS = $obj_DB->fetchName($iFSPRoutes) )
        {
            if(CPMFRAUD::hasFraudPassed($aFSPStatus) === true || empty($aFSPStatus)  === true )
            {
                $obj_FSP = CPMFRAUD::produceFSP($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo, (int)$RS['PSPID']);
                $iFSPCode = $obj_FSP->initiateFraudCheck($obj_Card,$clientInfo,$iFraudType);
                array_push($aFSPStatus, $iFSPCode);
            }
        }

        return CPMFRAUD::hasFraudPassed($aFSPStatus);
    }


    public static function hasFraudPassed($aFSPStatus = array())
    {
        $bFraudPass = false;
        foreach ($aFSPStatus as $iFSPStatus)
        {
            switch ($iFSPStatus)
            {
                case Constants::iPRE_FRAUD_CHECK_ACCEPTED_STATE:
                case Constants::iPOST_FRAUD_CHECK_ACCEPTED_STATE:
                case Constants::iPRE_FRAUD_CHECK_UNAVAILABLE_STATE:
                case Constants::iPOST_FRAUD_CHECK_UNAVAILABLE_STATE:
                case Constants::iPRE_FRAUD_CHECK_CONNECTION_FAILED_STATE:
                case Constants::iPOST_FRAUD_CHECK_CONNECTION_FAILED_STATE:
                case Constants::iPRE_FRAUD_CHECK_REVIEW_STATE:
                case Constants::iPOST_FRAUD_CHECK_REVIEW_STATE:
                 $bFraudPass = true;
                 break;
                case Constants::iPRE_FRAUD_CHECK_REJECTED_STATE:
                case Constants::iPOST_FRAUD_CHECK_REJECTED_STATE:
                 $bFraudPass = false;
                break;
            }
        }

        return $bFraudPass;
    }


    public function initiateFraudCheck($obj_Card, ClientInfo $clientInfo = null,$iFraudType = Constants::iPROCESSOR_TYPE_PRE_FRAUD_GATEWAY)
    {

        $aMerchantAccountDetails = $this->genMerchantAccountDetails($clientInfo);
        $iStatusCode = 0;
        $b  = '<?xml version="1.0" encoding="UTF-8"?>';
        $b .= '<root>';
        $b .= '<fraudCheck>';
        $b .= '<type>'.$iFraudType.'</type>';
        $b .= '<client-config>';
        $b .=  '<clientId>'.$this->getTxnInfo()->getClientConfig()->getID().'</clientId>';
        $b .=  '<account>'.$this->getTxnInfo()->getClientConfig()->getAccountConfig()->getID().'</account>';
        $b .= '<additionalConfig>';
        foreach ($this->getTxnInfo()->getClientConfig()->getAdditionalProperties(Constants::iPrivateProperty) as $aAdditionalProperty)
        {
            $b .= '<property>';
            $b .=  '<name>'.$aAdditionalProperty['key'].'</name>';
            $b .=  '<value>'.$aAdditionalProperty['value'].'</value>';
            $b .= '</property>';
        }
        $b .= '</additionalConfig>';

        $b .= '</client-config>';

        $b .= $this->getPSPConfig()->toAttributeLessXML(Constants::iPrivateProperty, $aMerchantAccountDetails);

        $b .= $this->getTxnInfo()->toAttributeLessXML();

        $b .=  $this->_constNewCardAuthorizationRequest($obj_Card);

        if($clientInfo !== null && $clientInfo instanceof ClientInfo)
        {
            $b .= $clientInfo->toXML();
        }
        $b .= '</fraudCheck>';
        $b .= '</root>';

        try
        {
            if($iFraudType === Constants::iPROCESSOR_TYPE_PRE_FRAUD_GATEWAY) { $this->_obj_mPoint->newMessage($this->getTxnInfo()->getID(), Constants::iPRE_FRAUD_CHECK_INITIATED_STATE, ''); }
            else { $this->_obj_mPoint->newMessage($this->getTxnInfo()->getID(), Constants::iPOST_FRAUD_CHECK_INITIATED_STATE, ''); }

            $obj_ConnInfo = $this->_constConnInfo($this->aCONN_INFO["paths"]["fraud-check"]);

            $obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
            $obj_HTTP->connect();
            $code = $obj_HTTP->send($this->constHTTPHeaders(), $b);
            $obj_HTTP->disConnect();
            if ($code == 200 )
            {

                $obj_XML = simplexml_load_string($obj_HTTP->getReplyBody());
                $response = FraudResponse::produceInfoFromXML($iFraudType,$obj_XML);
                $iStatusCode = $response->getStatusCode();
                $this->_obj_mPoint->newMessage($this->getTxnInfo()->getID(), $iStatusCode, utf8_encode($obj_HTTP->getReplyBody() ));
                return $iStatusCode;
            }
            else
            {
                if($iFraudType === Constants::iPROCESSOR_TYPE_PRE_FRAUD_GATEWAY) { $iStatusCode = Constants::iPRE_FRAUD_CHECK_UNKNOWN_STATE; }
                else { $iStatusCode = Constants::iPOST_FRAUD_CHECK_UNKNOWN_STATE; }
                $this->_obj_mPoint->newMessage($this->getTxnInfo()->getID(), Constants::iPRE_FRAUD_CHECK_INITIATED_STATE, '');

            }


        }
        catch (mPointException $e)
        {
            trigger_error("Fraud Check failed of txn: ". $this->getTxnInfo()->getID(). " failed with code: ". $e->getCode(). " and message: ". $e->getMessage(), E_USER_ERROR);
        }
        catch (HTTPException $e)
        {

            if($iFraudType === Constants::iPROCESSOR_TYPE_PRE_FRAUD_GATEWAY) { $iStatusCode = Constants::iPRE_FRAUD_CHECK_CONNECTION_FAILED_STATE; }
            else { $iStatusCode = Constants::iPOST_FRAUD_CHECK_CONNECTION_FAILED_STATE; }
            $this->_obj_mPoint->newMessage($this->getTxnInfo()->getID(), Constants::iPRE_FRAUD_CHECK_INITIATED_STATE, '');

        }

        return $iStatusCode;
    }


    protected function genMerchantAccountDetails(ClientInfo $clientInfo)
    {
        $context = '<root>';
        $context .= $this->getPSPConfig()->toXML(Constants::iInternalProperty);

        $context .= str_replace('<?xml version="1.0"?>', '', $clientInfo->toXML(Constants::iPrivateProperty));
        $context .= $this->getTxnInfo()->toAttributeLessXML();
        $context .= '</root>';
        $parser = new  \mPoint\Core\Parser();
        $parser->setContext($context);

        $rules = $this->getPSPConfig()->getAdditionalProperties(Constants::iInternalProperty);

        foreach ($rules as $value) {
            if ($value['scope'] == 0 && strpos($value['key'], 'rule') !== false) {
                $parser->setRules($value['value']);
            }
        }

        $parser->parse();
        $merchantaccount = $parser->getValue('merchantaccount');
        $username = $parser->getValue('username');
        $password = $parser->getValue('password');
        $aMerchantAccountDetails = array();
        if(isset($merchantaccount) && $merchantaccount !== false && $merchantaccount !== '')
        {
            $aMerchantAccountDetails['merchantaccount'] = $merchantaccount;
        }
        if(isset($username) && $username !== false && $username !== '')
        {
            $aMerchantAccountDetails['username'] = $username;
        }
        if(isset($password) && $password !== false && $password !== '')
        {
            $aMerchantAccountDetails['password'] = $password;
        }

        return $aMerchantAccountDetails;

    }

    protected function _constNewCardAuthorizationRequest($obj_Card)
    {

        list($expiry_month, $expiry_year) = explode("/", $obj_Card->expiry);

        $expiry_year = substr_replace(date('Y'), $expiry_year, -2);

        $b = '<card type-id="'.intval($obj_Card['type-id']).'">';

        if(count($obj_Card->{'card-holder-name'}) > 0) { $b .= '<card-holder-name>'. $obj_Card->{'card-holder-name'} .'</card-holder-name>'; }

        $b .= '<card-number>'. $obj_Card->{'card-number'} .'</card-number>';
        $b .= '<expiry-month>'. $expiry_month .'</expiry-month>';
        $b .= '<expiry-year>'. $expiry_year .'</expiry-year>';

        if(count($obj_Card->{'valid-from'}) > 0) {
            list($valid_from_month, $valid_from_year) = explode("/", $obj_Card->{'valid-from'});
            $valid_from_year = substr_replace(date('Y'), $valid_from_year, -2);
            $b .= '<valid-from-month>'. $valid_from_month .'</valid-from-month>';
            $b .= '<valid-from-year>'. $valid_from_year .'</valid-from-year>';
        }

        if(count($obj_Card->cvc) > 0) { $b .= '<cvc>'. $obj_Card->cvc .'</cvc>'; }

        if(count($obj_Card->{'info-3d-secure'}) > 0)
        {
            $b .= $obj_Card->{'info-3d-secure'}->asXML();
        }

        $b .= '</card>';

        if(count($obj_Card->address) > 0)
        {
            //Produce Country config based on the country id
            CountryConfig::setISO3166Attributes($obj_Card->address, $this->getDBConn(), (int)$obj_Card->address["country-id"]);
            $b .= $obj_Card->address->asXML();
        }

        return $b;
    }
    protected function _constConnInfo($path)
    {
        $aCI = $this->aCONN_INFO;
        $aURLInfo = parse_url($this->getTxnInfo()->getClientConfig()->getMESBURL() );

        return new HTTPConnInfo($aCI["protocol"], $aURLInfo["host"], $aCI["port"], $aCI["timeout"], $path, $aCI["method"], $aCI["contenttype"], $this->getTxnInfo()->getClientConfig()->getUsername(), $this->getTxnInfo()->getClientConfig()->getPassword() );
    }

    /**
     * Construct standard mPoint HTTP Headers for notifying the Client via HTTP.
     *
     * @return string
     */
    public function constHTTPHeaders()
    {
        /* ----- Construct HTTP Header Start ----- */
        $h = "{METHOD} {PATH} HTTP/1.0" .HTTPClient::CRLF;
        $h .= "host: {HOST}" .HTTPClient::CRLF;
        $h .= "referer: {REFERER}" .HTTPClient::CRLF;
        $h .= "content-length: {CONTENTLENGTH}" .HTTPClient::CRLF;
        $h .= "content-type: {CONTENTTYPE}; charset=UTF-8" .HTTPClient::CRLF;
        $h .= "user-agent: mPoint-{USER-AGENT}" .HTTPClient::CRLF;
        /* ----- Construct HTTP Header End ----- */

        return $h;
    }

}