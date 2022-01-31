<?php

require_once(dirname(__FILE__). '/../../../inc/include.php');

require_once(sAPI_CLASS_PATH ."/simpledom.php");

$obj_XML = simpledom_load_string(file_get_contents('php://input') );

if ($obj_XML->validate(dirname(__FILE__). '/../xsd/authenticate_mpi.xsd') )
{
    header("Content-Type: text/xml; charset=\"UTF-8\"");

    echo '<?xml version="1.0" encoding="UTF-8"?><root><status code="2005" sub-code="2005002">3D Secure Verification Required</status><web-method></web-method><return-url></return-url><card-mask>401636******0010</card-mask><expiry>01/24</expiry><token>81770143dcb3ca014999c13501532a1c0b19229a287520f90499bfbce4eac0cf80869a4fce4adfafd6a5055f4517b63056fa5a8c012e820e7a7a95b621aeaf3a</token></root>';

}
else
{
    header("HTTP/1.0 400 Bad Request");
    header("Content-Type: text/xml; charset=\"UTF-8\"");

    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<root>';

    $aObj_Errs = libxml_get_errors();

    foreach ($aObj_Errs as $err)
    {
        echo '<status code="400">'. htmlspecialchars($err->message, ENT_NOQUOTES) .'</status>';
    }
    echo '</root>';
}
