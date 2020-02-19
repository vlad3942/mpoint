<?php

$request_xml = '<?xml version="1.0" encoding="UTF-8"?>
                <root>
                  <initialize-payment account="100210" client-id="10021">
                    <transaction order-no="CPM1534916483305" type-id="30">
                      <amount country-id="608" currency-id="682">100</amount>
                    </transaction>
                    <auth-token>profilesuccessvalidation</auth-token>
                    <client-info language="gb" version="1.43" platform="HTML5">
                      <customer-ref>'.$_POST['cust_ref'].'</customer-ref>
                    </client-info>
                  </initialize-payment>
                </root>';

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_PORT => "10080",
    CURLOPT_URL => "http://6s.mesb.dev2.cellpointmobile.com:10080/mpoint/initialize-payment",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => $request_xml,
    CURLOPT_HTTPHEADER => array(
        "authorization: Basic ".base64_encode("SGADemo:DEMOisNO_2"),
        "cache-control: no-cache",
        "content-type: text/xml"
    ),
));
$response = curl_exec($curl);
curl_close($curl);

$oXML = new SimpleXMLElement( $response );
header( 'Content-type: text/xml' );
echo $oXML->asXML();
?>

