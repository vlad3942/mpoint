<?php

require_once(dirname(__FILE__). '/../../../inc/include.php');

require_once(sAPI_CLASS_PATH ."/simpledom.php");

$obj_XML = simpledom_load_string(file_get_contents('php://input') );

if ($obj_XML->validate(dirname(__FILE__). '/../xsd/pay.xsd') )
{
    header("Content-Type: text/xml; charset=\"UTF-8\"");

    echo '<?xml version="1.0" encoding="utf-8"?>
<root>
  <url content-type="application/x-www-form-urlencoded" method="post">http://test:test@localhost/mpoint/authorize-payment</url>
  <card-number>card_number</card-number>
  <expiry-month>expiration_month</expiry-month>
  <expiry-year>expiration_year</expiry-year>
  <valid-from-month>valid_from_month</valid-from-month>
  <valid-from-year>valid_from_year</valid-from-year>
  <cvc>securitycode</cvc>
  <name>card_holderName</name>
  <decline-url>http://localhost/mpsp/amex/threed-redirect</decline-url>
  <cancel-url>http://localhost/mpsp/amex/threed-redirect</cancel-url>
  <accept-url>http://localhost/mpsp/amex/threed-redirect</accept-url>
  <clientinfo>client_info</clientinfo>
  <addressinfo>addressinfo</addressinfo>
  <hidden-fields>
    <client-id>'.$obj_XML->initialize["client-id"].'</client-id>
    <account-id>'.$obj_XML->initialize["account"].'</account-id>
    <card-type-id>'.$obj_XML->initialize->card["type-id"].'</card-type-id>
    <merchant_account_id />
    <merchantAccount />
    <amount.value>'.$obj_XML->initialize->transaction->{"authorized-amount"}.'</amount.value>
    <amount.currency>'.$obj_XML->initialize->transaction->{"authorized-amount"}["currency"].'</amount.currency>
    <transaction_type>10091</transaction_type>
    <requested_amount>'.$obj_XML->initialize->transaction->{"authorized-amount"}.'</requested_amount>
    <requested_amount_currency>'.$obj_XML->initialize->transaction->{"authorized-amount"}["currency"].'</requested_amount_currency>
    <requested_amount_country>'.$obj_XML->initialize->transaction->{"authorized-amount"}["country-id"].'</requested_amount_country>
    <transactionId>'.$obj_XML->initialize->transaction["id"].'</transactionId>
    <reference>'.$obj_XML->initialize->transaction->orderid.'</reference>
    <platform />
    <language />
    <version />
    <mobile-country-id />
    <mobile-operator-id />
    <mobile-no />
    <email />
    <device-id />
    <first_name />
    <last_name />
    <street />
    <city />
    <state />
    <country />
    <postal-code />
  </hidden-fields>
</root>
';
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