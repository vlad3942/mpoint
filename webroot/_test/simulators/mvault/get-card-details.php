<?php

require_once(dirname(__FILE__). '/../../../inc/include.php');

require_once(sAPI_CLASS_PATH ."/simpledom.php");

$obj_XML = simpledom_load_string(file_get_contents('php://input') );

if ($obj_XML->validate(dirname(__FILE__). '/../xsd/mvault-get-card-details.xsd') )
{
    header("Content-Type: text/xml; charset=\"UTF-8\"");
    echo '<?xml version="1.0" encoding="UTF-8"?>
<root><get-card-details-response><client-id>10069</client-id><external-id>1938070</external-id><masked-pan>519463******0017</masked-pan><card-pan>5194630000000017</card-pan><expiry>11/24</expiry><card-holder-name>CellPointMobie</card-holder-name></get-card-details-response></root>';
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