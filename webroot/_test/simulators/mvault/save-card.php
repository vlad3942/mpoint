<?php

require_once(dirname(__FILE__). '/../../../inc/include.php');

require_once(sAPI_CLASS_PATH ."/simpledom.php");

$obj_XML = simpledom_load_string(file_get_contents('php://input') );

if ($obj_XML->validate(dirname(__FILE__). '/../xsd/mvault-save-card.xsd') )
{
    header("Content-Type: text/xml; charset=\"UTF-8\"");
    echo '<?xml version="1.0" encoding="UTF-8"?>
<root><save-card-response><client-id>10069</client-id><external-id>1938070</external-id><token>93736e0408d5cd3793615f6e132c89a8f32337483a74739674a5bb2a9c18f6eb91eae4960e5ff9bad1bf62e60282de3c0605ececa6a82f7d14cbe5305fd1983d</token><enabled>true</enabled></save-card-response></root>';
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