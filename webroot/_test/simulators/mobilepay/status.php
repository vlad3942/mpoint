<?php

require_once(dirname(__FILE__). '/../../../inc/include.php');

require_once(sAPI_CLASS_PATH ."/simpledom.php");

$obj_XML = simpledom_load_string(file_get_contents('php://input') );

if ($_SERVER['PHP_AUTH_USER'] == 'Tusername' && $_SERVER["PHP_AUTH_PW"] == 'Tpassword')
{

	if ($obj_XML->validate(dirname(__FILE__). '../xsd/status.xsd') )
	{
		header("Content-Type: text/xml; charset=\"UTF-8\"");

		echo '<?xml version="1.0" encoding="UTF-8"?>';
		echo '<root>';
		echo '<transactions>';
		echo '<transaction id="'. $obj_XML->status->transactions->transaction['id'] .'">';

		if (strrpos($obj_XML->status->transactions->transaction->orderid, '404') !== false) { echo '<status code="404">Transaction Not Found</status>'; }
		else if (strrpos($obj_XML->status->transactions->transaction->orderid, '2001') !== false) { echo '<status code="2001">Captured</status>'; }
		else if (strrpos($obj_XML->status->transactions->transaction->orderid, '2003') !== false) { echo '<status code="2003">Refunded</status>'; }
		else { echo '<status code="2000">Authorized</status>'; }

		echo '</transaction>';
		echo '</transactions>';
		echo '</root>';
	}
	else
	{
		header("HTTP/1.0 400 Bad Request");
		header("Content-Type: text/xml; charset=\"UTF-8\"");

		echo '<?xml version="1.0" encoding="UTF-8"?>';
		echo '<root>';
		echo '<transactions>';
		echo '<transaction id="'. $obj_XML->status->transactions->transaction['id'] .'">';

		$aObj_Errs = libxml_get_errors();

		foreach ($aObj_Errs as $err)
		{
			echo '<status code="400">'. htmlspecialchars($err->message, ENT_NOQUOTES) .'</status>';
		}

		echo '</transaction>';
		echo '</transactions>';
		echo '</root>';
	}

}
else
{
	header('WWW-Authenticate: Basic realm="MESB Simulator"');
	header('HTTP/1.0 401 Unauthorized');
}
