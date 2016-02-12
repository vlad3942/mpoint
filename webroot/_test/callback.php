<?php
	
/* 	$callbackData = array(
			'mpoint-id'=> 1818498,
			'orderid'=> '523423-65760',
			'status'=> 2000,
			'amount'=> 210000,
			'fee'=> 0,
			'currency'=> 'DKK',
			'mobile'=> 1234567890,
			'operator'=> 20000,
			'language'=> 'gb',
			'card-id'=> 8,
			'card-number'=> '471110******0000',
			'pspid'=> 1279646112,
			'mac'=> 'a5c74b60597af04f7fd1cf25d00eaac6d2728071'
	); */

	

	$callbackData = $_REQUEST;
	
	if(count($callbackData) < 2)
	{
		$callbackData = array();
		$callbackData["empty"] = "Empty Data";
	}
	
	$xml = new SimpleXMLElement('<root/>');
	array_walk_recursive($callbackData, array ($xml, 'addChild'));
	$xml = $xml->asXML();

	$data = array("logged-time" => date("Y-m-d H:i:s"), "postedData" => $xml);
	
	file_put_contents("./data/callback_data.txt", print_r($data, true), FILE_APPEND);
	
	header("HTTP/1.1 200 OK");
?>