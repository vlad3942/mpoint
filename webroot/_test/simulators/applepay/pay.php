<?php
require_once(dirname(__FILE__). '/../../../inc/include.php');

require_once(sAPI_CLASS_PATH ."/simpledom.php");

$obj_XML = simpledom_load_string(file_get_contents('php://input') );

$sXSDValidateFile = '/../xsd/pay.xsd';
$sOrderId = $obj_XML->initialize->transaction->orderid ?? -1;
$sPaymentMethods = '';

if(strpos(strtolower($sOrderId) , 'wallet') !== false) {
    $sXSDValidateFile = '/../xsd/initialize.xsd';
    $sPaymentMethods = '<supported_cards>';
    foreach($obj_XML->initialize->{'client-config'}->{'payment-methods'}->{'payment-method'} as $payment){
        if((int)$payment->attributes()->walletid === 14) {
            $sPaymentMethods .= '<supported_card>'.(string)$payment[0][0].'</supported_card>';
        }
    }
    $sPaymentMethods .= '</supported_cards>';
}

if ($obj_XML->validate(dirname(__FILE__). $sXSDValidateFile) )
{
    header("Content-Type: text/xml; charset=\"UTF-8\"");

    echo '
    <root>
        <url method="overlay"/>
        <head>'.$sPaymentMethods.'</head>
        <body></body>
        <name>card_holderName</name>
        <message language="gb"></message>
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