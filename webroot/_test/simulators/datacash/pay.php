<?php

require_once(dirname(__FILE__). '/../../../inc/include.php');

require_once(sAPI_CLASS_PATH ."/simpledom.php");

$obj_XML = simpledom_load_string(file_get_contents('php://input') );

if ($obj_XML->validate(dirname(__FILE__). '/pay.xsd') )
{
    header("Content-Type: text/xml; charset=\"UTF-8\"");

    echo '
    <root>
        <url content-type="application/x-www-form-urlencoded" method="post">http://test:test@localhost/mpoint/authorize-payment</url>
        <card-number>card_number</card-number>
        <expiry-month>expiration_month</expiry-month>
        <expiry-year>expiration_year</expiry-year>
        <cvc>securitycode</cvc>
        <name>card_holderName</name>
        <clientinfo>client_info</clientinfo>
        <decline-url>http://localhost/mpsp/data-cash/threed-redirect</decline-url>
        <cancel-url>http://localhost/mpsp/data-cash/threed-redirect</cancel-url>
        <accept-url>http://localhost/mpsp/data-cash/threed-redirect</accept-url>
        <hidden-fields>
        <order.amount />
        <order.currency />
        <session.id />
        <transaction.id />
        <sourceOfFunds.type>CARD</sourceOfFunds.type>
        <mpoint-id />
        <store-card>false</store-card>
        <transactionId>'.$obj_XML->initialize->transaction["id"].'</transactionId>
        <transaction_type>10091</transaction_type>
        <card-type-id>'.$obj_XML->initialize->card["type-id"].'</card-type-id>
        <client-id>'.$obj_XML->initialize["client-id"].'</client-id>
        <account-id>'.$obj_XML->initialize["account"].'</account-id>
        <requested_amount>'.$obj_XML->initialize->transaction->{"authorized-amount"}.'</requested_amount>
        <requested_amount_currency>'.$obj_XML->initialize->transaction->{"authorized-amount"}["currency"].'</requested_amount_currency>
        <requested_amount_country>'.$obj_XML->initialize->transaction->{"authorized-amount"}["country-id"].'</requested_amount_country>    
        </hidden-fields>
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