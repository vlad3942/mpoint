<?php

require_once(dirname(__FILE__). '/../../../inc/include.php');

require_once(sAPI_CLASS_PATH ."/simpledom.php");

$obj_XML = simpledom_load_string(file_get_contents('php://input') );

if ($obj_XML->validate(dirname(__FILE__). '/status.xsd') )
{
    header("Content-Type: text/xml; charset=\"UTF-8\"");

    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<root>';
    echo '<transactions>';
    echo '<transaction id="1001001">';

	trigger_error($obj_XML->status->transactions->transaction->orderid);

	if (strrpos($obj_XML->status->transactions->transaction->orderid, '404') !== false) { echo '<status code="404">Transaction Not Found</status>'; }
	else if (strrpos($obj_XML->status->transactions->transaction->orderid, '2003') !== false) { echo '<status code="2003">Refunded</status>'; }
	else { echo '<status code="2000">Success</status>'; }

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