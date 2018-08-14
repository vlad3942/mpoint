<?php

require_once(dirname(__FILE__). '/../../../inc/include.php');

require_once(sAPI_CLASS_PATH ."/simpledom.php");

$obj_XML = simpledom_load_string(file_get_contents('php://input') );
trigger_error($obj_XML->validate(dirname(__FILE__). '/auth.xsd'), E_USER_NOTICE);
if ($obj_XML->validate(dirname(__FILE__). '/auth.xsd') )
{
    header("Content-Type: text/xml; charset=\"UTF-8\"");

    echo '<?xml version="1.0" encoding="UTF-8"?><root><status code="2000">Payment authorized</status></root>';
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