<?php

require_once(dirname(__FILE__). '/../../../inc/include.php');

switch (@$_REQUEST["status"])
{
case Constants::iPAYMENT_ACCEPTED_STATE:
case Constants::iPRE_FRAUD_CHECK_REVIEW_SUCCESS_STATE:
case Constants::iPAYMENT_PENDING_STATE:
	$aRequiredArguments = array('status', 'amount', 'mpoint-id', 'pspid', 'card-id', 'language');
	break;
case Constants::iPAYMENT_CAPTURED_STATE:
	$aRequiredArguments = array('status', 'amount', 'mpoint-id', 'pspid', 'language', 'fee');
	break;
case Constants::iPAYMENT_REFUNDED_STATE:
	$aRequiredArguments = array('status', 'amount', 'mpoint-id', 'pspid', 'language');
	break;
case Constants::iPAYMENT_CANCELLED_STATE:
case Constants::iPAYMENT_CAPTURE_FAILED_STATE:
case Constants::iPAYMENT_CANCEL_FAILED_STATE:
case Constants::iPAYMENT_REFUND_FAILED_STATE:
	$aRequiredArguments = array('status', 'mpoint-id', 'pspid', 'language');
	break;
case Constants::iPAYMENT_REJECTED_STATE:
case Constants::iPAYMENT_REQUEST_CANCELLED_STATE:
case Constants::iPAYMENT_REQUEST_EXPIRED_STATE:
	$aRequiredArguments = array('status', 'mpoint-id', 'language');
	break;
case Constants::iSESSION_COMPLETED:
case Constants::iSESSION_CREATED:
case Constants::iSESSION_EXPIRED:
	$aRequiredArguments = array('status', 'session-id', 'pspid');
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
	$expectedTransact = 0;
	$aLogLines = file(sERROR_LOG, FILE_IGNORE_NEW_LINES);
	foreach ($aLogLines as $line)
	{
		$pos = strpos($line, 'mRetail expect external transaction id: ');
		if ($pos !== false)
		{
			$expectedTransact = intval(substr($line, $pos+strlen("mRetail expect external transaction id: ") ) );
			break;
		}
	}

	if ($expectedTransact < 1 || $expectedTransact == intval(@$_REQUEST["pspid"]) )
	{
		trigger_error("Fee received from notify client: ". $_REQUEST['fee']);
		echo "OK";
	}
	else
	{
		trigger_error("Wrong transaction ID received from mPoint, transact: ". @$_REQUEST["pspid"], E_USER_ERROR);
	}

}
else
{
	$sMsg = "mTicket callback, Missing required callback arguments: ". implode(', ', $aMissing);

	trigger_error($sMsg, E_USER_WARNING);
	header('HTTP/1.0 400 Bad Request');
	echo $sMsg;
}
