<?php

$request_xml = '<?xml version="1.0" encoding="UTF-8"?>
                <root>
                	<login client-id="10021">
                		<password></password>
                		<client-info language="us" version="1.28" platform="iOS/10.3.1">
                			<customer-ref>'.$_POST['cust_ref'].'</customer-ref>
                		</client-info>
                	</login>
                </root>';

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => "http://mpoint.dev2.cellpointmobile.com/mApp/api/login.php",
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
        "content-type: text/xml",
    ),
));

$response = curl_exec($curl);
curl_close($curl);

$oXML = new SimpleXMLElement( $response );
header( 'Content-type: text/xml' );
echo $oXML->asXML();
?>

