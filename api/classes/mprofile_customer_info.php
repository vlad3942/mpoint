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

    public function __construct(RDB $oDB, TranslateText $oTxt, $id, $cid, $mob, $email, $cr, $name, $lang, $clientid, $deviceid, $profileid)
    {
        parent::__construct($id, $cid, $mob, $email, $cr, $name, $lang, $profileid);
        $this->_obj_Profile = mProfile::produceConfiguration($oDB, $oTxt, $clientid, $this, $deviceid);
        $this->_obj_Profile->getProfile();
    }

    public function toXML()
    {
        $xml = '<customer';
        if ($this->_obj_Profile->getObjCustomerInfo()->getID() > 0) {
            $xml .= ' id="' . $this->_obj_Profile->getObjCustomerInfo()->getID() . '"';
        }
        if (strlen($this->_obj_Profile->getObjCustomerInfo()->getCustomerRef()) > 0) {
            $xml .= ' customer-ref="' . htmlspecialchars($this->_obj_Profile->getObjCustomerInfo()->getCustomerRef(), ENT_NOQUOTES) . '"';
        }
        $xml .= '>';
        if (strlen($this->_obj_Profile->getObjCustomerInfo()->getFullName()) > 0) {
            $xml .= '<full-name>' . htmlspecialchars($this->_obj_Profile->getObjCustomerInfo()->getFullName(), ENT_NOQUOTES) . '</full-name>';
        }
        if ($this->_obj_Profile->getObjCustomerInfo()->getMobile() > 0) {
            $xml .= '<mobile country-id="' . $this->_obj_Profile->getObjCustomerInfo()->getCountryID() . '">' . $this->_obj_Profile->getObjCustomerInfo()->getMobile() . '</mobile>';
        }
        if (strlen($this->_obj_Profile->getObjCustomerInfo()->getEMail()) > 0) {
            $xml .= '<email>' . htmlspecialchars($this->_obj_Profile->getObjCustomerInfo()->getEMail(), ENT_NOQUOTES) . '</email>';
        }
        $xml .= '<device-id platform-id = "'.$this->_obj_Profile->getPlatformID().'">'.$this->_obj_Profile->getDeviceID().'</device-id>';
        $xml .= '<push-id>'.$this->_obj_Profile->getPushID().'</push-id>';
        $xml .= '<profile-id>'.$this->_obj_Profile->getMProfileID().'</profile-id>';
        $xml .= '<guest-user>'.General::bool2xml($this->_obj_Profile->getGuestFlag()).'</guest-user>';
        $xml .= '</customer>';
        return $xml;
    }
}