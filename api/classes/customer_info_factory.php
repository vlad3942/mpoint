<?php

/**
 * Created by IntelliJ IDEA.
 * User: rohit
 * Date: 27-09-2017
 * Time: 11:24
 */
class CustomerInfoFactory
{
    public static function getInstance(RDB $oDB, TranslateText $oTxt, ClientURLConfig $obj_URLConfig, $id, $cid, $mob, $email, $cr, $name, $lang, $clientid, $deviceid)
    {
		$url= $obj_URLConfig->getURL();
        if (empty($url)  === true )
        {
            return new CustomerInfo($id, $cid, $mob, $email, $cr, $name, $lang);
        }
        else
        {
            return new mProfileCustomerInfo($oDB, $oTxt, $id, $cid, $mob, $email, $cr, $name, $lang, $clientid, $deviceid);
        }
    }
}