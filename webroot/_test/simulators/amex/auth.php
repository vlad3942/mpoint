<?php

require_once(dirname(__FILE__). '/../../../inc/include.php');

require_once(sAPI_CLASS_PATH ."/simpledom.php");

$obj_XML = simpledom_load_string(file_get_contents('php://input') );

if ($obj_XML->validate(dirname(__FILE__). '../xsd/auth.xsd') )
{
    header("Content-Type: text/xml; charset=\"UTF-8\"");

    echo '<?xml version="1.0" encoding="UTF-8"?>
<root><status code="2000">000-Approved :: Approval Code - 923572</status><token>30c80fd3ab1401a344c944cee226d65d4495182c3bac117e39fb742a826b687e2d61468eb0085a17830687987f28fce1b524bf3a7689ced3f383fa65eb0ffd6f</token><card-mask>373953*****1004</card-mask><expiry>01/19</expiry><approval-code>923572</approval-code><action-code>000</action-code><auth-original-data>1100908543180713122909\</auth-original-data></root>';
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