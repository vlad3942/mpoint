<?php
/* ==================== Callback Exception Classes Start ==================== */

use api\classes\merchantservices\configuration\AddonServiceType;
use api\classes\merchantservices\Repositories\ReadOnlyConfigRepository;

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
    public function __construct(RDB $oDB, api\classes\core\TranslateText $oTxt, TxnInfo $oTI, ?array $aConnInfo)
    {
        $this->_obj_TxnInfo = $oTI;
        $this->_oDB = $oDB;
        $iFSPID = $this->getFSPID();
        if(empty($aConnInfo) === false )
        {
            $this->aCONN_INFO = $aConnInfo;
        }
        else
        {
            throw new CPMFraudEXCEPTION("Connection Configuration not found for the given FSP ID ". $iFSPID);
        }

        $this->_obj_PSPConfig = $oPSPConfig = PSPConfig::produceProviderConfig($oDB,   $iFSPID,$oTI);
        $this->_obj_mPoint = new General($oDB, $oTxt);
    }

    abstract protected function getFSPID();
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

    /**
     * Reference to the Database Object that holds the active connection to the mPoint Database
     *
     * @return RDB
     */
    public function getDBConn() { return $this->_oDB; }


    /**
     * Factory Returns fraud object
     * @param RDB $obj_DB Reference to the Database Object that holds the active connection to the mPoint Database
     * @param TranslateText $obj_Txt Text Translation Object for translating any text into a specific language
     * @param TxnInfo $obj_TxnInfo Data object with the Transaction Information
     * @param array $aConnInfo Connection Information
     * @param integer $iFSPID FSP id for Fraud Service Provider
     * @return CyberSourceFSP|EZY
     * @throws CPMFraudEXCEPTION
     * @throws CallbackException
     */
    public static function produceFSP(RDB &$obj_DB, api\classes\core\TranslateText &$obj_Txt, TxnInfo &$obj_TxnInfo, array $aConnInfo, $iFSPID)
    {
        if (empty($aConnInfo) === false) {
            return new \api\classes\GenericFSP($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo,$iFSPID);
        } else {
            throw new CallbackException("Could not construct PSP object for the given Fraud PSPID ".$iFSPID );
        }
    }
    /**
     * Handles fraud check
     *
     * @param	SimpleDOMElement $obj_Card	Card Information
     * @param	RDB $obj_DB	Reference to the Database Object that holds the active connection to the mPoint Database
     * @param	ClientInfo $obj_ClientInfo		The Client Information from which fields such as the customer's mobile & email is retrieved
     * @param	TranslateText $obj_Txt 	Text Translation Object for translating any text into a specific language
     * @param 	TxnInfo $obj_TxnInfo 			Data object with the Transaction Information
     * @param 	PSPConfig $oPSPConfig 	Configuration object with the PSP Information
     * @param 	array $aConnInfo 	Connection Information
     * @param 	CreditCard $obj_mCard	CreditCard obj used to fetch routes
     * @param 	integer $cardTypeId	Card Type
     * @param 	integer $iFraudType	Fraud Check Type
     * @param  null $authToken
     * @return FraudResult
     */
    public static function attemptFraudCheckIfRoutePresent($obj_Card,RDB &$obj_DB, ?ClientInfo $clientInfo, api\classes\core\TranslateText &$obj_Txt, TxnInfo &$obj_TxnInfo, array $aConnInfo,CreditCard &$obj_mCard,$cardTypeId,$iFraudType = Constants::iPROCESSOR_TYPE_PRE_FRAUD_GATEWAY,$authToken=null)
    {
        $repository = new ReadOnlyConfigRepository($obj_DB,$obj_TxnInfo);
        $subType ='pre_auth';
        if($iFraudType === Constants::iPROCESSOR_TYPE_POST_FRAUD_GATEWAY) { $subType ='post_auth'; }

        $fraudAddon = $repository->getAddonConfiguration(AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::eFraud,$subType));

        $aFSPStatus = array();
        $fraudCheckResponse = new FraudResult();
        if($obj_TxnInfo->getClientConfig()->getClientServices()->isFraud() === true)
        {
            foreach ($fraudAddon->getConfiguration() as $config)
            {
                if(CPMFRAUD::hasFraudPassed($aFSPStatus) === true || empty($aFSPStatus)  === true )
                {
                    $obj_FSP = CPMFRAUD::produceFSP($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo, $config->getProviderId());
                    $iFSPCode = $obj_FSP->initiateFraudCheck($obj_DB,$obj_Card,$clientInfo,$iFraudType,$authToken);
                    $fraudCheckResponse->setFraudCheckAttempted(true);
                    array_push($aFSPStatus, $iFSPCode);
                }
            }
        }

        $fraudCheckResponse->setFraudCheckResult(CPMFRAUD::hasFraudPassed($aFSPStatus));
        return $fraudCheckResponse;
    }

    public static function attemptFraudInitCallback($iStateId,$sStateName,RDB &$obj_DB, api\classes\core\TranslateText &$obj_Txt, TxnInfo &$obj_TxnInfo, array $aConnInfo,$cardTypeId,$iFraudType = Constants::iPROCESSOR_TYPE_PRE_FRAUD_GATEWAY)
    {
        $repository = new ReadOnlyConfigRepository($obj_DB,$obj_TxnInfo);
        $subType ='pre_auth';
        if($iFraudType === Constants::iPROCESSOR_TYPE_PRE_FRAUD_GATEWAY) { $subType ='post_auth'; }

        $fraudAddon = $repository->getAddonConfiguration(AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::eFraud,$subType));

        foreach ($fraudAddon->getConfiguration() as $config)
        {
            $obj_FSP = CPMFRAUD::produceFSP($obj_DB, $obj_Txt, $obj_TxnInfo, $aConnInfo, $config->getProviderId());
            $obj_FSP->initCallback($iStateId,$sStateName,$cardTypeId,$iFraudType);
        }
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
                case Constants::iPRE_FRAUD_CHECK_UNKNOWN_STATE:
                case Constants::iPOST_FRAUD_CHECK_UNKNOWN_STATE:
                case Constants::iPOST_FRAUD_CHECK_TECH_ERROR_STATE:
                case Constants::iPRE_FRAUD_CHECK_TECH_ERROR_STATE:
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

    /**
     * Initiate Request to Fraud Service Provider
     *
     * @param	SimpleDOMElement $obj_Card	Card Information
     * @param	RDB $obj_DB	Reference to the Database Object that holds the active connection to the mPoint Database
     * @param	ClientInfo $obj_ClientInfo		The Client Information from which fields such as the customer's mobile & email is retrieved
     * @param 	integer $iFraudType	Fraud Check Type
     * @param null $authToken
     * @return integer $iStatusCode
     */
    public function initiateFraudCheck(RDB $obj_DB,$obj_Card, ClientInfo $clientInfo = null,$iFraudType = Constants::iPROCESSOR_TYPE_PRE_FRAUD_GATEWAY,$authToken=null)
    {
        if($obj_Card === null)
        {
            $response = new FraudResponse($iFraudType,13,'','','');
            $this->_obj_mPoint->newMessage($this->getTxnInfo()->getID(), $response->getStatusCode(), 'No card details passed');
            return $response->getStatusCode();
        }
        try
        {
            $mask =NULL;
            if(isset($obj_Card->{'card-number'}))
            {
                $mask = General::getMaskCardNumber($obj_Card->{'card-number'});
            }
            else if(isset($obj_Card->mask) && empty($obj_Card->mask) === false)
            {
                $mask=str_replace(" ", "", $obj_Card->mask);
            }
            //In case of wallet payment flow mPoint get real card and card id in authorization
            $this->getTxnInfo()->updateCardDetails($this->getDBConn(), $obj_Card['type-id'], $mask, $obj_Card->expiry);
        }
        catch (Exception $e)
        {
            trigger_error("Failed to update card details", E_USER_ERROR);
        }
        $this->_obj_TxnInfo = TxnInfo::produceInfo( $this->_obj_TxnInfo->getID(), $this->getDBConn());

        $this->getTxnInfo()->produceOrderConfig($this->getDBConn());
        $aMerchantAccountDetails = $this->genMerchantAccountDetails();
        $iStatusCode = 0;
        $b  = '<?xml version="1.0" encoding="UTF-8"?>';
        $b .= '<root>';
        $b .= '<fraudCheck>';
        $b .= '<type>'.$iFraudType.'</type>';
        $b .= '<fail-txn-count>'.FailedPaymentMethodConfig::getFailedFraudTxnCount($obj_DB, $this->_obj_TxnInfo->getSessionId(), $this->getTxnInfo()->getClientConfig()->getID(),$this->getFSPID()).'</fail-txn-count>';
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
        if($authToken !== null) {
            $b .= '<auth-token>' . $authToken . '</auth-token>';
        }
        $b .= $this->getPSPConfig()->toAttributeLessXML(Constants::iPrivateProperty, $aMerchantAccountDetails);
        $b .= $this->getTxnInfo()->getPaymentSession()->toXML();
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
                $externalID = $response->getExternalID();
                $externalActionCode = $response->getExternalActionCode();
                $additionalTxnData = [];
                $i = 0;
                $preFix = "pre_auth";
                if($iFraudType === Constants::iPROCESSOR_TYPE_POST_FRAUD_GATEWAY) { $preFix = "post_auth"; }

                if(empty($externalID) === false)
                {
                    $additionalTxnData[$i]['name'] = $preFix.'_ext_id';
                    $additionalTxnData[$i]['value'] = $externalID;
                    $additionalTxnData[$i]['type'] = 'Transaction';
                    $i++;
                }
                if(empty($externalActionCode) === false)
                {
                    $additionalTxnData[$i]['name'] = $preFix.'_ext_status_code';
                    $additionalTxnData[$i]['value'] = $externalActionCode;
                    $additionalTxnData[$i]['type'] = 'Transaction';
                }

                $this->getTxnInfo()->setAdditionalDetails($this->getDBConn(),$additionalTxnData,$this->getTxnInfo()->getID());

                $this->_obj_mPoint->newMessage($this->getTxnInfo()->getID(), $iStatusCode, utf8_encode($obj_HTTP->getReplyBody() ));
                return $iStatusCode;
            }
            else
            {
                if($iFraudType === Constants::iPROCESSOR_TYPE_PRE_FRAUD_GATEWAY) { $iStatusCode = Constants::iPRE_FRAUD_CHECK_UNKNOWN_STATE; }
                else { $iStatusCode = Constants::iPOST_FRAUD_CHECK_UNKNOWN_STATE; }
                $this->_obj_mPoint->newMessage($this->getTxnInfo()->getID(), $iStatusCode, "Fraud Check failed with FPS: ". $this->getPSPConfig()->getID()." responded with HTTP status code: ". $code. " and header: ". utf8_encode($obj_HTTP->getReplyHeader() ));

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

            trigger_error("Fraud request failed for Transaction: ". $this->_obj_TxnInfo->getID(), E_USER_WARNING);
            $this->_obj_mPoint->newMessage($this->getTxnInfo()->getID(), $iStatusCode, $e->getMessage() ."(". $e->getCode() .")");

        }

        return $iStatusCode;
    }


    protected function genMerchantAccountDetails()
    {
        $context = '<root>';
        $context .= $this->getPSPConfig()->toXML(Constants::iInternalProperty);

        $context .= str_replace('<?xml version="1.0"?>', '', $this->getTxnInfo()->getClientConfig()->toXML(Constants::iPrivateProperty));
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
        if (!empty($obj_Card->card_name)) {
            $b .= '<name>' . $obj_Card->card_name . '</name>';
        }
        if(count($obj_Card->{'card-holder-name'}) > 0) { $b .= '<card-holder-name>'. $obj_Card->{'card-holder-name'} .'</card-holder-name>'; }

        $b .= '<card-number>'. $obj_Card->{'card-number'} .'</card-number>';
        $b .= '<expiry-month>'. $expiry_month .'</expiry-month>';
        $b .= '<expiry-year>'. $expiry_year .'</expiry-year>';

        if(count($obj_Card->{'valid-from'}) > 0)
        {
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
            if(empty($obj_Card->address->{'full-name'}) === false)
            {
                $pos = strrpos($obj_Card->address->{'full-name'}, " ");
                if ($pos > 0)
                {
                    $obj_Card->address->{'first-name'} = trim(substr($obj_Card->address->{'full-name'}, 0, $pos) );
                    $obj_Card->address->{'last-name'} = trim(substr($obj_Card->address->{'full-name'}, $pos) );
                }
                else { $obj_Card->address->{'first-name'} = trim($obj_Card->address->{'full-name'}); }
            }

            $b .= $obj_Card->address->asXML();
        }

        return $b;
    }

    public function initCallback($iStateID, $sStateName, $iCardid,$iFraudType)
    {
        if(empty($this->aCONN_INFO["paths"]["callback"]))  return;
        $obj_TxnInfo = $this->getTxnInfo();
        $obj_PSPConfig = $this->getPSPConfig();
        $aMerchantAccountDetails = $this->genMerchantAccountDetails();
        $code = 0;
        $xml  = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<root>';
        $xml .= '<callback>';
        $xml .= '<type>'.$iFraudType.'</type>';
        $xml .= $obj_PSPConfig->toXML(Constants::iPrivateProperty, $aMerchantAccountDetails);
        $xml .= '<transaction>';
        $xml .= '<id>'. $obj_TxnInfo->getID() .'</id> <order_no>'. $obj_TxnInfo->getOrderID() .'</order_no>';
        $xml .= '<amount_info>';
        $xml .= '<country_id>'. $obj_TxnInfo->getCountryConfig()->getID() .'</country_id>';
        $xml .= '<currency_id>'. $obj_TxnInfo->getCurrencyConfig()->getID() .'</currency_id>';
        $xml .= '<currency>'. $obj_TxnInfo->getCurrencyConfig()->getCode() .'</currency>';
        $xml .= '<decimals>'. $obj_TxnInfo->getCurrencyConfig()->getDecimals() .'</decimals>';
        $xml .= '<alpha2code>'. $obj_TxnInfo->getCountryConfig()->getAlpha2code() .'</alpha2code>';
        $xml .= '<alpha3code>'. $obj_TxnInfo->getCountryConfig()->getAlpha3code() .'</alpha3code>';
        $xml .= '<code>'. $obj_TxnInfo->getCountryConfig()->getNumericCode() .'</code>';
        $xml .= '<amount>'. $iAmount .'</amount>';
        $xml .= '</amount_info>';
        $xml .= '<card>';
        $xml .= '<type_id>'.$iCardid.'</type_id><psp_id>'. $obj_TxnInfo->getPSPID() .'</psp_id>';
        $xml .= '</card>';
        if($this->getTxnInfo()->getAdditionalData() != null)
        {
            $xml .= "<additional_data>";
            foreach ($this->getTxnInfo()->getAdditionalData() as $key=>$value)
            {
                if (strpos($key, 'rule') === false)
                {
                    $xml .= '<param> <name>'. $key . '</name>' ;
                    $xml .= '<value>'. $value . '</value> </param>';
                }
            }
            $xml .="</additional_data>";
        }
        $xml .= '<description>'. $this->getTxnInfo()->getDescription() .'</description>';
        $xml .= '</transaction>';
        $xml .= '<status code="'. $iStateID .'">'. $sStateName .'</status>';
        $xml .= '</callback>';
        $xml .= '</root>';

        try
        {
            $obj_ConnInfo = $this->_constConnInfo($this->aCONN_INFO["paths"]["callback"]);

            $obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
            $obj_HTTP->connect();
            $code = $obj_HTTP->send($this->constHTTPHeaders(), $xml);
            $obj_HTTP->disConnect();

            if($code == 200)
            {
                $obj_XML = simplexml_load_string($obj_HTTP->getReplyBody());
                $response = FraudResponse::produceInfoFromXML($iFraudType,$obj_XML);
                $this->_obj_mPoint->newMessage($this->getTxnInfo()->getID(), $iStateID, $obj_HTTP->getReplyBody());
              /*  $args = array('amount'=>$this->getTxnInfo()->getAmount(),
                    'transact'=>$this->getTxnInfo()->getExternalID(),
                    'cardid'=>$this->getTxnInfo()->getCardID());
                $this->_obj_mPoint->notifyClient(Constants::iPAYMENT_PENDING_STATE, $args, $this->getTxnInfo()->getClientConfig()->getSurePayConfig($this->getDBConn()));*/
            }
            else if ($code == 200)
            {
                $obj_XML = simplexml_load_string($obj_HTTP->getReplyBody() );
                if (isset($obj_XML->status["code"]) === true && strlen($obj_XML->status["code"]) > 0) { $code = $obj_XML->status["code"]; }
                else { throw new mPointException("Invalid response from callback controller: ". $this->getPSPConfig()->getName() .", Body: ". $obj_HTTP->getReplyBody(), $code); }
            }
            else { throw new mPointException("Callback to mPoint callback controller: ". $this->getPSPConfig()->getName() ." responded with HTTP status code: ". $code. " and body: ". $obj_HTTP->getReplyBody(), $code ); }
        }
        catch (mPointException $e)
        {
            trigger_error("Callback to mPoint for txn: ". $this->getTxnInfo()->getID(). " failed with code: ". $e->getCode(). " and message: ". $e->getMessage(), E_USER_ERROR);
            $code = -1*abs($code);
        }
        return $code;
    }

    protected function _constConnInfo($path)
    {
        $aCI = $this->aCONN_INFO;
        $aURLInfo = parse_url($this->getTxnInfo()->getClientConfig()->getMESBURL() );
        if(isset($aURLInfo['port']) === false)
        {
            $aURLInfo['port'] = $aURLInfo["scheme"] === 'https' ? 443 : 80;
        }
        return new HTTPConnInfo($aURLInfo["scheme"], $aURLInfo["host"], $aURLInfo["port"], $aCI["timeout"], $path, $aCI["method"], $aCI["contenttype"], $this->getTxnInfo()->getClientConfig()->getUsername(), $this->getTxnInfo()->getClientConfig()->getPassword() );
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