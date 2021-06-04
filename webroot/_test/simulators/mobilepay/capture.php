<?php

require_once(dirname(__FILE__). '/../../../inc/include.php');

require_once(sAPI_CLASS_PATH ."/simpledom.php");

$obj_XML = simpledom_load_string(file_get_contents('php://input') );
$validate = $obj_XML->validate(dirname(__FILE__). '/../xsd/capture.xsd');
$orderref = (string)$obj_XML->capture->transactions->transaction->orders[0]->{"line-item"}[0]->product["order-ref"];
if ( $validate && $obj_XML->capture->transactions->transaction->orderid == 'ticketlevel' &&  ($orderref === "SOCGN6" || $orderref === "SOCGN7") )
{
    header("Content-Type: text/xml; charset=\"UTF-8\"");

    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<root>';
    echo '<transactions>';
    echo '<transaction id="1001001">';
    echo '<status code="1000">Success</status>';
    echo '</transaction>';
    echo '</transactions>';
    echo '</root>';
}
elseif ($validate && $obj_XML->capture->transactions->transaction->orderid != 'ticketlevel')
{
    header("Content-Type: text/xml; charset=\"UTF-8\"");

    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<root>';
    echo '<transactions>';
    echo '<transaction id="1001001">';
    echo '<status code="1000">Success</status>';
    echo '</transaction>';
    echo '</transactions>';
    echo '</root>';
}
else
{
    header("HTTP/1.0 400 Bad Request");
    header("Content-Type: text/xml; charset=\"UTF-8\"");

    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<root>';
    echo '<transactions>';
    echo '<transaction id="1001001">';

    $aObj_Errs = libxml_get_errors();

    foreach ($aObj_Errs as $err)
    {
        echo '<status code="400">'. htmlspecialchars($err->message, ENT_NOQUOTES) .'</status>';
    }

    echo '</transaction>';
    echo '</transactions>';
    echo '</root>';
}