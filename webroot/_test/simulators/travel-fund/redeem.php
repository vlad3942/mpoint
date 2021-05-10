<?php

require_once(dirname(__FILE__). '/../../../inc/include.php');
require_once(sAPI_CLASS_PATH ."/simpledom.php");

$obj_XML = simpledom_load_string(file_get_contents("php://input") );

if (intval($obj_XML->{"redeem-voucher"}->transaction->amount) < 10)
{
	header("Content-Type: text/xml; charset=\"UTF-8\"");

	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo '<root>';
	if (empty($obj_XML->{"redeem-voucher"}["id"]) === false){
		echo '<external-id>"'. $obj_XML->{"redeem-voucher"}["id"] .'"</external-id>';
	}
	echo '<status code="2000">Payment authorized.</status>';	
	echo '</root>';
}
else
{
	header("HTTP/1.1 402 Payment Required");
}