<?php
/**
 * User: johan
 * Date: 2/5/15
 * Time: 2:10 PM
 *
 * DIBS capture API simulator implemented to behave almost similar to:
 * http://tech.dibspayment.com/D2/FlexWin/API/Payment_functions/capturecgi
 */

$iMerchant = (integer)@$_REQUEST['merchant'];
$iTransact = (integer)@$_REQUEST['transact'];
$iAmount = (integer)@$_REQUEST['amount'];
$sOrderId = @$_REQUEST['orderid'];

//if ($iMerchant > 0 && $iTransact > 0 && $iAmount > 0 && strlen($sOrderId) > 0)
if (true)
{
	echo substr($iTransact, -1, 1);
}
else
{
    echo "result=8&status=DECLINED&reason=Wrong Parameters&message=Wrong Parameters";
}
