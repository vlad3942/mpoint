<?php

require_once(dirname(__FILE__). '/../../../inc/include.php');

require_once(sAPI_CLASS_PATH ."/simpledom.php");

$obj_XML = simpledom_load_string(file_get_contents('php://input') );

if ($obj_XML->validate(dirname(__FILE__). '/../xsd/authenticate.xsd') )
{
    header("Content-Type: text/xml; charset=\"UTF-8\"");
    global $aMPOINT_CONN_INFO;
    $aConnInfo = $aMPOINT_CONN_INFO;
    $aConnInfo['path'] = "/mApp/api/authenticate.php";
    $aConnInfo["contenttype"] = "text/xml";

    $obj_ConnInfo = HTTPConnInfo::produceConnInfo($aConnInfo);
    $h = "{METHOD} {PATH} HTTP/1.0" .HTTPClient::CRLF;
    $h .= "host: {HOST}" .HTTPClient::CRLF;
    $h .= "referer: {REFERER}" .HTTPClient::CRLF;
    $h .= "content-length: {CONTENTLENGTH}" .HTTPClient::CRLF;
    $h .= "content-type: {CONTENTTYPE}" .HTTPClient::CRLF;
    $h .= "user-agent: mPoint" .HTTPClient::CRLF;
    $h .= "Authorization: Basic ". base64_encode("Tuser:Tpass") .HTTPClient::CRLF;

    $obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
    $obj_HTTP->connect();
    $code = $obj_HTTP->send($h, file_get_contents('php://input') );
    $obj_HTTP->disconnect();
    if ($code == 200 || $code == 303)
    {
        $obj_XML = simplexml_load_string($obj_HTTP->getReplyBody());
        $code = (int)$obj_XML->status["code"];
    }
    if ($code !== Constants::iPAYMENT_3DS_VERIFICATION_STATE)
    {
        header("HTTP/1.0 500 Internal Server Error");
        header("Content-Type: text/xml; charset=\"UTF-8\"");
    }
    echo  $obj_HTTP->getReplyBody();
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
