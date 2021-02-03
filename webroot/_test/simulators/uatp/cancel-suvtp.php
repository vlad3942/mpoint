<?php

require_once(__DIR__ . '/../../../inc/include.php');

require_once(sAPI_CLASS_PATH . "/simpledom.php");

$obj_XML = simpledom_load_string(file_get_contents('php://input'));

if ($obj_XML->validate(__DIR__ . '/../xsd/cancel.xsd')) {
    header("Content-Type: text/xml; charset=\"UTF-8\"");

    if ((string)$obj_XML->xpath("//transactions/transaction/external_refs/external_ref[pspid = '50']/reference") === '165404603632840') {
        echo '<?xml version="1.0" encoding="UTF-8"?>
    <status code="100">Card deleted Successfully</status>';
    } else {
        header("HTTP/1.0 400 Bad Request");
        header("Content-Type: text/xml; charset=\"UTF-8\"");
        echo '<?xml version="1.0" encoding="UTF-8"?>
    <status code="99">Error</status>';
    }
} else {
    header("HTTP/1.0 400 Bad Request");
    header("Content-Type: text/xml; charset=\"UTF-8\"");

    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<root>';

    $aObj_Errs = libxml_get_errors();

    foreach ($aObj_Errs as $err) {
        echo '<status code="400">' . htmlspecialchars($err->message, ENT_NOQUOTES) . '</status>';
    }
    echo '</root>';
}