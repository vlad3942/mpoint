<?php

require_once(dirname(__FILE__). '/../../../inc/include.php');

require_once(sAPI_CLASS_PATH ."/simpledom.php");

$obj_XML = simpledom_load_string(file_get_contents('php://input') );
if ($obj_XML->validate(dirname(__FILE__). '/../xsd/initialize.xsd') )
{
    header("Content-Type: text/xml; charset=\"UTF-8\"");

    echo '<root><url method="overlay" /><head>&lt;script type=\'text/javascript\'&gt; var debug = false; var countryCode = "PH"; var currencyCode = "PHP"; var merchantIdentifier = \'EFS100001149\'; var displayName ="CEBU Pacific Air Automation"; var totalAmount = "148.55"; var supportedNetword = [\'MASTERCARD\',\'VISA\']; &lt;/script&gt; &lt;script type="text/javascript" src=""&gt;&lt;/script&gt; &lt;style&gt; #applePay{width:150px;height:50px;display:none;border-radius:5px;background-image:-webkit-named-image(apple-pay-logo-white);background-position:50% 50%;background-color:#000;background-size:60%;background-repeat:no-repeat} &lt;/style&gt;</head><body>&lt;button type="button" id="applePay"&gt;&lt;/button&gt;</body></root>';
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