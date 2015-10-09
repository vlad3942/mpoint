<?php

$aRequiredArguments = array('merchant', 'amount', 'currency', 'orderid', 'mpointid', 'textreply');

/*
 *     [merchant] =>
    [mpointid] => 1001001
    [ticket] => 1767989
    [amount] => 5000
    [currency] =>
    [orderid] => 1001001
    [textreply] => true
 *
 */


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
	$aParams = array_merge($_REQUEST,
		array('result' => 0,
			'status' => "ACCEPTED",
			'cardtype' => "DK",
			'language' => "da",
			'transact' => "". rand(100000, 1000000). "2"
		) );

	$response = http_build_query($aParams);
	echo $response;
}
else
{
	$sMsg = "DIBS payment.pml, Missing required arguments: ". implode(', ', $aMissing);

	trigger_error($sMsg, E_USER_WARNING);
	header('HTTP/1.0 400 Bad Request');
	echo $sMsg;
}
