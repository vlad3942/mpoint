<?php

require_once(dirname(__FILE__). '/../../../inc/include.php');

require_once(sAPI_CLASS_PATH ."/simpledom.php");

$obj_XML = simpledom_load_string(file_get_contents('php://input') );
if (true )
{
    header("Content-Type: text/xml; charset=\"UTF-8\"");
        echo '<?xml version="1.0" encoding="UTF-8"?>
            <root>
                <active-payment-menthods>
                    <payment-method>
                        <logoName>1022_CASH</logoName>
                        <logoURL>1022_CASH</logoURL>
                        <displayName>Efecty</displayName>
                        <issuingBank>1022</issuingBank>
                    </payment-method>
                </active-payment-menthods>
            </root>';
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