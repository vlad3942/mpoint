<?php

	$xml = file_get_contents("php://input");
	

	//$xml = '<root><import><customer id="51819"><social-security-number>3008990017</social-security-number></customer><client-info app-id="0" platform="iOS" version="1.00" language="da"><mobile country-id="610">3138544000</mobile><email>abhishek@cellpointmobile.com</email><device-id>85ce3843c0a068fb5cb1e76156fdd719</device-id><ip></ip></client-info></import></root>';

	$data = array("logged-time" => date("Y-m-d H:i:s"), "postedData" => $xml);
	
	file_put_contents("/var/log/cpm/mPoint/pspdata/customer_import_data.txt", print_r(json_encode($data), true), FILE_APPEND);
		
	header("Content-Type: text/xml; charset=\"UTF-8\"");
	header("HTTP/1.1 200 OK");

	echo $xml;	
?>