<?php

class mProfileException extends mPointException{}

class mProfile extends General
{
    const sHTTP_METHOD = 'POST';
    const sHTTP_TIMEOUT = 120;
    const sHTTP_CONTENT_TYPE = 'text/xml';
    const sX_CPM_TOKEN = 'eyJhbGciOiJIUzI1NiJ9.eyJleHAiOjQ2NjEzOTU4MDEsImlhdCI6MTUwNTcyMjIwMSwiaXNzIjoiQ1BNIiwidHlwZSI6MiwiY2xpZW50aWQiOiIxMDAxOCIsInNlc3Npb25JZCI6MX0.GbnU1gTFPAY8jgJWsLJBXDxG8_0Rvazx69MP53hRL1w';
    const sGET_PROFILE_SERVICE_URL = '/mprofile/get-profile';

    private $_obj_ClientConfig;

    private $_obj_CustomerInfo;

    private $_sDeviceID;

    private $_sPushID;

    private $_iPlatformID = -1;

    private $_bIsGuest;

    private $_iMProfileID;


    public function __construct(RDB $oDB, TranslateText $oTxt, ClientConfig $obj_CC, CustomerInfo $obj_CI, $sDeviceID)
    {
        parent::__construct($oDB, $oTxt);
        $this->_obj_ClientConfig = $obj_CC;
        $this->_obj_CustomerInfo = $obj_CI;
        $this->_sDeviceID = $sDeviceID;
    }

    private function _setIsGuestFlag($bIsGuest)
    {
        $this->_bIsGuest = $bIsGuest;
    }

    private function _setDeviceID($sDeviceID)
    {
        $this->_sDeviceID = $sDeviceID;
    }

    private function _setMProfileID($iID)
    {
        $this->_iMProfileID = $iID;
    }

    private function _setPushID($iID)
    {
        $this->_sPushID = $iID;
    }

    private function _setPlatformID($iID)
    {
        $this->_iPlatformID = $iID;
    }

    /**
     * @return CustomerInfo
     */
    public function getObjCustomerInfo()
    {
        return $this->_obj_CustomerInfo;
    }

    /**
     * @param CustomerInfo $obj_CustomerInfo
     */
    private function setObjCustomerInfo($obj_CustomerInfo)
    {
        $this->_obj_CustomerInfo = $obj_CustomerInfo;
    }

    public function getPushID()     { return $this->_sPushID; }
    public function getDeviceID()   { return $this->_sDeviceID; }
    public function getGuestFlag()  { return $this->_bIsGuest; }
    public function getMProfileID() { return $this->_iMProfileID; }
    public function getPlatformID() { return $this->_iPlatformID; }


    protected function _constConnInfo($path)
    {
        $aURLInfo = parse_url($this->_obj_ClientConfig->getAuthenticationURL() );

        return new HTTPConnInfo($aURLInfo["scheme"], $aURLInfo["host"], $aURLInfo["port"], self::sHTTP_TIMEOUT,
            $path, self::sHTTP_METHOD, self::sHTTP_CONTENT_TYPE, $this->_obj_ClientConfig->getUsername(),
            $this->_obj_ClientConfig->getPassword() );
    }

    public static function produceConfiguration(RDB $oDB, TranslateText $oTxt, $iClientID, CustomerInfo $oCI, $sDeviceID)
    {
        $oCC = ClientConfig::produceConfig($oDB, $iClientID);
        return new mProfile($oDB, $oTxt, $oCC, $oCI, $sDeviceID);
    }

    public function getProfile()
    {
        if ($this->_obj_ClientConfig->getAdditionalProperties(Constants::iInternalProperty, "ENABLE_PROFILE_ANONYMIZATION") == "true" && $this->_obj_CustomerInfo->getProfileID() > 0) {
            $b = '<?xml version="1.0" encoding="UTF-8"?>
                <root>
                    <get-profile id = "' . $this->_obj_CustomerInfo->getProfileID() . '" />
                </root>';
        } else {
            $b = '<?xml version="1.0" encoding="UTF-8"?>
                <root>
                    <get-profile id = "' . $this->_obj_CustomerInfo->getCustomerRef() . '" />
                </root>';
        }

        try
        {
            $code = -1;
            $oCI = $this->_constConnInfo(self::sGET_PROFILE_SERVICE_URL);
            $h = trim($this->constHTTPHeaders() ) .HTTPClient::CRLF;
            $h .= "x-cpm-Token: ". self::sX_CPM_TOKEN .HTTPClient::CRLF;
            $h .= "X-CPM-client-id: ". $this->_obj_ClientConfig->getID(). HTTPClient::CRLF;
            if (empty($this->_obj_CustomerInfo->getMobile()) === false and $this->_obj_CustomerInfo->getMobile() > 0) {
                $h .= "x-cpm-mobile: " . $this->_obj_CustomerInfo->getMobile() . HTTPClient::CRLF;
            }
            $h .= "x-cpm-country-id: ". $this->_obj_CustomerInfo->getCountryID() .HTTPClient::CRLF;
            if (empty($this->getDeviceID()) === false) {
                $h .= "x-cpm-device-id: " . $this->_sDeviceID . HTTPClient::CRLF;
            }
            $obj_HTTP = new HTTPClient(new Template(), $oCI);
            $obj_HTTP->connect();
            $HTTPResponseCode = $obj_HTTP->send($h, $b);
            $response = simpledom_load_string($obj_HTTP->getReplyBody());

            if(intval($HTTPResponseCode) == 200 && count($response->{'get-profile'}->{'profile'}) > 0)
            {

                if ($this->_obj_ClientConfig->getAdditionalProperties(Constants::iInternalProperty, "ENABLE_PROFILE_ANONYMIZATION") == "true" && $this->_obj_CustomerInfo->getProfileID() > 0)
                {
                    $obj_Customer = simplexml_load_string($this->_obj_CustomerInfo->toXML());
                    if (empty($obj_Customer["customer-ref"]) === true) {
                        $obj_Customer["customer-ref"] = (string) $response->{'get-profile'}->{'profile'}["external-id"];
                    }
                    if (empty($obj_Customer["full-name"]) === true && isset($response->{'get-profile'}->{'profile'}->{'first-name'}) === true) {
                        $obj_Customer["full-name"] = (string) $response->{'get-profile'}->{'profile'}->{'first-name'}.' '.$response->{'get-profile'}->{'profile'}->{'last-name'};
                    }
                    if (empty($obj_Customer["email"]) === true && isset($response->{'get-profile'}->{'profile'}->{'contacts'}->{'contact'}->{'email'}) === true) {
                        $obj_Customer["email"] = (string) $response->{'get-profile'}->{'profile'}->{'contacts'}->{'contact'}->{'email'};
                    }
                    if (empty($obj_Customer["mobile"]) === true && isset($response->{'get-profile'}->{'profile'}->{'contacts'}->{'contact'}->{'mobile'}) === true) {
                        $obj_Customer["mobile"] = (string) $response->{'get-profile'}->{'profile'}->{'contacts'}->{'contact'}->{'mobile'};
                        $obj_Customer["country-id"] = (string) $response->{'get-profile'}->{'profile'}->{'contacts'}->{'contact'}->{'mobile'}["country-id"];
                    }
                    $this->setObjCustomerInfo(CustomerInfo::produceInfo($obj_Customer));
                }

                $this->_setIsGuestFlag(General::xml2bool($response->{'get-profile'}->{'profile'}["guest"]) );
                $this->_setDeviceID(($response->{'get-profile'}->{'profile'}->{'device-id'}) );
                $this->_setPushID(($response->{'get-profile'}->{'profile'}->{'push-id'}) );
                $this->_setMProfileID(($response->{'get-profile'}->{'profile'}["id"]) );
                $this->_setPlatformID(intval($response->{'get-profile'}->{'profile'}->{'device-id'}['platform-id']) );
                return 1000;
            }
            else
            {
                throw new mProfileException("mProfile responded with HTTP status code: ". $HTTPResponseCode. " and body: ". $obj_HTTP->getReplyBody(), $HTTPResponseCode );
            }

        }
        catch (mProfileException $e)
        {
            trigger_error("User profile could not be fetched using client: ". $this->_obj_ClientConfig->getID(). " failed with code: ". $e->getCode(). " and message: ". $e->getMessage(), E_USER_ERROR);
            return $e->getCode();
        }
        catch (HTTPConnectionException $e)
        {
            trigger_error("mProfile Service at: ". $oCI->toURL() ." is unreachable due to ". get_class($e), E_USER_WARNING);
            return $e->getCode();
        }
        catch (HTTPSendException $e)
        {
            trigger_error("mProfile Service at: ". $oCI->toURL() ." is unavailable due to ". get_class($e), E_USER_WARNING);
            return $e->getCode();
        }
        catch (HTTPException $e)
        {
            trigger_error("Internal error while communicating with mProfile Service at: ". $oCI->toURL() ." due to ". get_class($e), E_USER_WARNING);
            return $e->getCode();
        }

    }
}