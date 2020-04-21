<?php

require_once(dirname(__FILE__). '/../../../inc/include.php');

require_once(sAPI_CLASS_PATH ."/simpledom.php");

$obj_XML = simpledom_load_string(file_get_contents('php://input') );
$xml = '';

if ($obj_XML->validate(dirname(__FILE__). '/bulk-settlement.xsd') )
{
	$clientId =  $obj_XML->{'bulk-settlement'}->{'client-config'}['id'];

	$alreadyProcessedSettlementId = $obj_XML->{'bulk-settlement'}->settlements->settlement['id'];
	$alreadyProcessedSettlementDate = trim($obj_XML->{'bulk-settlement'}->settlements->settlement['file-id']);

	$currentProcessingSettlementId = $obj_XML->{'bulk-settlement'}->{'settlement-in-progress'}->file['id'];
	$currentProcessingSettlementDate = trim($obj_XML->{'bulk-settlement'}->{'settlement-in-progress'}->file['file_sequence_number']);

	$status = "OK";
	$description = $currentProcessingSettlementDate;
	$record_tracking_number = $currentProcessingSettlementDate;

	if($currentProcessingSettlementDate === $alreadyProcessedSettlementDate)
	{
		$status = "duplicate";
		$description = "File is already processed as part of settlement id $alreadyProcessedSettlementId";
		$record_tracking_number = $alreadyProcessedSettlementDate;
	}

	$xml = '<?xml version="1.0" encoding="UTF-8"?>';
	$xml .= '<root>';
	$xml .= '<updateSettlementStatus>';
	$xml .= '<clientId>';
	$xml .= "$clientId";
	$xml .= '</clientId>';
	$xml .= '<settlementFile>';
	$xml .= '<id>';
	$xml .= "$currentProcessingSettlementId";
	$xml .= '</id>';
	$xml .= '<status>';
	$xml .= "$status";
	$xml .= '</status>';
	$xml .= '<description>';
	$xml .= "$description";
	$xml .= '</description>';
	$xml .= '<record_tracking_number>';
	$xml .= "$record_tracking_number";
	$xml .= '</record_tracking_number>';
	$xml .= '</settlementFile>';
	$xml .= '</updateSettlementStatus>';
	$xml .= '</root>';

	global $aMPOINT_CONN_INFO;
	$aConnInfo = $aMPOINT_CONN_INFO;
	$aConnInfo['path'] = "/mApp/api/update-settlement-status.php";
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
	$code = $obj_HTTP->send($h, $xml);
	$obj_HTTP->disconnect();

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