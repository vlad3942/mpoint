<?php

require_once(dirname(__FILE__). '/../../../inc/include.php');

require_once(sAPI_CLASS_PATH ."/simpledom.php");

$obj_XML = simpledom_load_string(file_get_contents('php://input') );
if ($obj_XML->validate(dirname(__FILE__). '/../xsd/pay.xsd') )
{

    $isPaymentPending  = 'false';
     if((string)$obj_XML->initialize->transaction->amount_info->{"currency-id"} === '986')
     {
        $isPaymentPending  = 'true';
     }


    header("Content-Type: text/xml; charset=\"UTF-8\"");

    echo '<?xml version="1.0" encoding="utf-8"?>
        <root>
            <url content-type="application/x-www-form-urlencoded" method="POST">https://sandbox.api.payulatam.com/payments-api/pse-caller</url>
            <hidden-fields>
            </hidden-fields>
            <accept-url>http://localhost/mpoint/alipay/process-response</accept-url>
            <input-fields>
                <optimized>false</optimized>
            </input-fields>
            <name>card_holderName</name>
            <message language="gb"></message>
            <payment-pending>'.$isPaymentPending.'</payment-pending>
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