<?php
/**
 * User: johan
 * Date: 2/5/15
 * Time: 2:10 PM
 *
 * DIBS capture API simulator implemented to behave almost similar to:
 * http://tech.dibspayment.com/D2/FlexWin/API/Payment_functions/capturecgi
 */

require_once(dirname(__FILE__). '/../../../inc/include.php');

$iMerchant = (integer)@$_REQUEST['merchant'];
$iTransact = (integer)@$_REQUEST['transact'];
$iAmount = (integer)@$_REQUEST['amount'];
$sOrderId = @$_REQUEST['orderid'];

//if ($iMerchant > 0 && $iTransact > 0 && $iAmount > 0 && strlen($sOrderId) > 0)
if (true)
{
	trigger_error("CAPTURE APPROVED BY PSP 2");

    $aParams = array_merge($_REQUEST,
        array('result' => 0,
              'status' => "ACCEPTED",
              'cardtype' => "DK",
              'language' => "da"
             ) );

    $response = http_build_query($aParams);
    echo $response;
}
else
{
    echo "result=8&status=DECLINED&reason=Wrong Parameters&message=Wrong Parameters";
}
