<?php

require_once(dirname(__FILE__). '/../../../inc/include.php');

require_once(sAPI_CLASS_PATH ."/simpledom.php");

$obj_XML = simpledom_load_string(file_get_contents('php://input') );

if ($obj_XML->validate(dirname(__FILE__). '/../xsd/capture.xsd') )
{
    header("Content-Type: text/xml; charset=\"UTF-8\"");

    echo '<?xml version="1.0" encoding="UTF-8"?><root><transactions><transaction id="1001001"><status code="1000">Success</status></transaction></transactions></root>';
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
        echo '<?xml version="1.0" encoding="UTF-8"?><root><transactions><transaction id="1001001"><status code="99">Unknown Error</status></transaction></transactions></root>';
    }
    echo '</root>';
}