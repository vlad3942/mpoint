<?php

require_once(dirname(__FILE__). '/../../../inc/include.php');

require_once(sAPI_CLASS_PATH ."/simpledom.php");

$obj_XML = simpledom_load_string(file_get_contents('php://input') );
if ($obj_XML->validate(dirname(__FILE__). '/../xsd/pay.xsd') )
{
    header("Content-Type: text/xml; charset=\"UTF-8\"");

    echo '<?xml version="1.0" encoding="utf-8"?>
        <root>
        <url content-type="application/x-www-form-urlencoded" method="POST">https://openapi.alipaydev.com/gateway.do</url>
        <hidden-fields>
            <service>create_forex_trade</service>
            <partner>2088621877192410</partner>
            <_input_charset>utf-8</_input_charset>
            <sign_type>RSA</sign_type>
            <sign>agpSRTMe45kuKJBd2Lm0C7LKVx+HouC1euOXPdHlhTmUfPeX0idBe//t5EXSLGHq1Osv4lcJRpCPsn/BchT2A8aSIXt4NtLABaiQOK9H7W6dVlPr16Vt+Gf/EaQU490g3oZXoTbwKPsXDPNlNYoDQe6/V3opKs2S7lR3UrcZ6ts=</sign>
            <notify_url>http://localhost/mpoint/alipay/callback</notify_url>
            <return_url>http://localhost/mpoint/alipay/redirect</return_url>
            <subject>AirTicket</subject>
            <out_trade_no>1867247</out_trade_no>
            <total_fee>1.00</total_fee>
            <currency>USD</currency>
            <body>AirTicket</body>
        </hidden-fields>
        <accept-url>http://localhost/mpoint/alipay/process-response</accept-url>
        <input-fields>
            <optimized>false</optimized>
        </input-fields>
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