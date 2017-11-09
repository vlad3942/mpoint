<?php

/**
 * Created by IntelliJ IDEA.
 * User: rohit
 * Date: 26-09-2017
 * Time: 13:00
 */
class mProfileCustomerInfo extends CustomerInfo
{
    private $_obj_Profile;

    public function __construct(RDB $oDB, TranslateText $oTxt, $id, $cid, $mob, $email, $cr, $name, $lang, $clientid, $deviceid)
    {
        parent::__construct($id, $cid, $mob, $email, $cr, $name, $lang);
        $this->_obj_Profile = mProfile::produceConfiguration($oDB, $oTxt, $clientid, $this, $deviceid);
        $this->_obj_Profile->getProfile();

    }

    public function toXML()
    {
        $xml = parent::toXML();
        $xml = str_replace('</customer>', '', $xml);
        $xml .= '<device-id platform-id = "'.$this->_obj_Profile->getPlatformID().'">'.$this->_obj_Profile->getDeviceID().'</device-id>';
        $xml .= '<push-id>'.$this->_obj_Profile->getPushID().'</push-id>';
        $xml .= '<profile-id>'.$this->_obj_Profile->getMProfileID().'</profile-id>';
        $xml .= '<guest-user>'.General::bool2xml($this->_obj_Profile->getGuestFlag()).'</guest-user>';
        $xml .= '</customer>';
        return $xml;
    }
}