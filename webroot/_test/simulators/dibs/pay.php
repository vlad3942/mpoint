<?php

$aRequiredArguments = array('merchant', 'callbackurl', 'amount', 'currency', 'fullreply',
							'orderid', 'language', 'cardid', 'mpointid', 'markup', 'eauid',
							'clientid', 'accountid', 'store_card', 'auto_store_card');

$aMissing = array();
foreach ($aRequiredArguments as $arg)
{
	if (isset($_REQUEST[$arg]) === false || strlen($_REQUEST[$arg]) < 1)
	{
		$aMissing[] = $arg;
	}
}

if (count($aMissing) < 1)
{
	header("Content-Type: text/xml; charset=UTF-8");
	@readfile('payment.xml');
}
else
{
	$sMsg = "DIBS payment.pml, Missing required arguments: ". implode(', ', $aMissing);

	trigger_error($sMsg, E_USER_WARNING);
	header('HTTP/1.0 400 Bad Request');
	echo $sMsg;
}
