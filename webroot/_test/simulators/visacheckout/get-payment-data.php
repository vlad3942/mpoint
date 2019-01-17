<?php

require_once(dirname(__FILE__). '/../../../inc/include.php');

require_once(sAPI_CLASS_PATH ."/simpledom.php");

$obj_XML = simpledom_load_string(file_get_contents('php://input') );

if ($obj_XML->validate(dirname(__FILE__). '../xsd/get-payment-data.xsd') )
{
    header("Content-Type: text/xml; charset=\"UTF-8\"");

    echo '<root>
    <payment-data>
        <card type-id="8" charge-type-id="3">
            <card-number>4012000300006002</card-number>
            <expiry>01/19</expiry>
            <card-holder-name>Test Card</card-holder-name>
            <info-3d-secure />
            <address country-id="200">
                <first-name>Test</first-name>
                <last-name>Card</last-name>
                <street>pune, pune</street>
                <postal-code>41223</postal-code>
                <city>pune</city>
                <state>AL</state>
            </address>
        </card>
    </payment-data>
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