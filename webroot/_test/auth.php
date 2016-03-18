<?php
		
	$data = array("logged-time" => date("Y-m-d H:i:s"), "postedData" => $_REQUEST);
	
	file_put_contents("/var/log/cpm/mPoint/pspdata/auth_data.txt", print_r($data, true), FILE_APPEND);
	
	header("HTTP/1.1 200 OK");
?>