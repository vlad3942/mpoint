<?php

// Require Global Include File
require_once '../../../webroot/inc/include.php';
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."/simpledom.php");

$obj_XML = simpledom_load_string(file_get_contents('php://input') );

if ($obj_XML->validate(dirname(__FILE__). '/xsd/check-fraud-status.xsd') )
{
    header("Content-Type: text/xml; charset=\"UTF-8\"");
    if(intval($obj_XML->{'check-fraud-status'}->{'transaction'}->{'additional-data'}->param) == 243001) {
        echo '<?xml version="1.0" encoding="UTF-8"?>
<root><status code="200">Accept</status></root>';
    } else {
        echo '<?xml version="1.0" encoding="UTF-8"?>
<root><status code="200">Reject</status></root>';
    }
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