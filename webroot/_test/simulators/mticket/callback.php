<?php

require_once(dirname(__FILE__). '/../../../inc/include.php');

switch (@$_REQUEST["status"])
{
case Constants::iPAYMENT_ACCEPTED_STATE:
	$aRequiredArguments = array('status', 'amount', 'mpoint-id', 'pspid', 'card-id', 'language');
	break;
case Constants::iPAYMENT_CAPTURED_STATE:
case Constants::iPAYMENT_REFUNDED_STATE:
	$aRequiredArguments = array('status', 'amount', 'mpoint-id', 'pspid', 'language');
	break;
case Constants::iPAYMENT_CANCELLED_STATE:
case Constants::iPAYMENT_DECLINED_STATE:
	$aRequiredArguments = array('status', 'mpoint-id', 'pspid', 'language');
	break;
default:
	$sMsg = "mTicket callback, Unknown payment state: ". @$_REQUEST["status"];
	trigger_error($sMsg, E_USER_WARNING);
	header('HTTP/1.0 400 Bad Request');
	echo $sMsg;
	exit;
}


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
	echo "OK";
}
else
{
	$sMsg = "mTicket callback, Missing required callback arguments: ". implode(', ', $aMissing);

	trigger_error($sMsg, E_USER_WARNING);
	header('HTTP/1.0 400 Bad Request');
	echo $sMsg;
}
