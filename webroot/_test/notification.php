<?php

	$xml = file_get_contents("php://input");
		
	$xml = str_replace(array("\n", "\r", "\t"), '', $xml);
	
	$xml = trim(str_replace('"', "'", $xml));	
	
	$data = array("logged-time" => date("Y-m-d H:i:s"), "postedData" => $xml);
	
	file_put_contents("/var/log/cpm/mPoint/pspdata/notification_data.txt", print_r(json_encode($data), true), FILE_APPEND);
		
	header("Content-Type: text/xml; charset=\"UTF-8\"");
	header("HTTP/1.1 200 OK");

	echo $xml;	
?>