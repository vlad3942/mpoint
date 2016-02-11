<?php

	$xml = file_get_contents("php://input");
		
	$data = array("logged-time" => date("Y-m-d H:i:s"), "postedData" => $xml);
	
	file_put_contents("/var/log/cpm/mPoint/pspdata/notification_data.txt", print_r(json_encode($data), true), FILE_APPEND);
	
	// $xml = '<notify><customer id="51818"><stored-cards>1</stored-cards></customer><password mask=""/><auth-token>testnotification</auth-token><client-info app-id="0" platform="iOS" version="1.00" language="da"><mobile country-id="100">28882861</mobile><email>jona@oismail.com</email><device-id>23lkhfgjh24qsdfkjh</device-id><ip></ip></client-info></notify>';
	
	header("Content-Type: text/xml; charset=\"UTF-8\"");
	header("HTTP/1.1 200 OK");

	echo $xml;	
?>