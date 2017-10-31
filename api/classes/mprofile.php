<?php

class mProfileException extends mPointException{}

class mProfile extends General
{
    const sHTTP_METHOD = 'POST';
    const sHTTP_TIMEOUT = 120;
    const sHTTP_CONTENT_TYPE = 'text/xml';
    const sX_CPM_TOKEN = 'eyJhbGciOiJIUzI1NiJ9.eyJleHAiOjQ2NTg5OTIyOTYsImlhdCI6MTUwMzMxODY5NiwiaXNzIjoiQ1BNIiwicHJvZmlsZUlkIjoxLCJzZXNzaW9uSWQiOjF9.IeLj6HLMd5tLbIpr_70homfUNHi6cqgaN_iUj-omLow';
    const sGET_PROFILE_SERVICE_URL = '/mprofile/get-profile';

    private $_obj_ClientConfig;

    private $_obj_CustomerInfo;

    private $_sDeviceID;

    private $_sPushID;

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

    public function getPushID()     { return $this->_sPushID; }
    public function getDeviceID()   { return $this->_sDeviceID; }
    public function getGuestFlag()  { return $this->_bIsGuest; }
    public function getMProfileID() { return $this->_iMProfileID; }


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
        $b = '<?xml version="1.0" encoding="UTF-8"?>
                <root>
                    <get-profile />
                </root>';

        try
        {
            $code = -1;
            $oCI = $this->_constConnInfo(self::sGET_PROFILE_SERVICE_URL);
            $h = trim($this->constHTTPHeaders() ) .HTTPClient::CRLF;
            $h .= "x-cpm-Token: ". self::sX_CPM_TOKEN .HTTPClient::CRLF;
            $h .= "X-CPM-client-id: 10018". HTTPClient::CRLF;
            $h .= "x-cpm-mobile: ". $this->_obj_CustomerInfo->getMobile() .HTTPClient::CRLF;
            $h .= "x-cpm-country-id: ". $this->_obj_CustomerInfo->getCountryID() .HTTPClient::CRLF;
            $h .= "x-cpm-device-id: ". $this->_sDeviceID .HTTPClient::CRLF;
            $obj_HTTP = new HTTPClient(new Template(), $oCI);
            $obj_HTTP->connect();
            $HTTPResponseCode = $obj_HTTP->send($h, $b);
            $response = simpledom_load_string($obj_HTTP->getReplyBody());

            if(intval($HTTPResponseCode) == 200 && count($response->{'get-profile'}->{'profile'}) > 0)
            {
                $this->_setIsGuestFlag(General::xml2bool($response->{'get-profile'}->{'profile'}["guest"]) );
                $this->_setDeviceID(($response->{'get-profile'}->{'profile'}->{'device-id'}) );
                $this->_setPushID(($response->{'get-profile'}->{'profile'}->{'push-id'}) );
                $this->_setMProfileID(($response->{'get-profile'}->{'profile'}["id"]) );
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