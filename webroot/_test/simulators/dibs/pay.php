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

//Always respond 200 OK for now
if (true)
{
	header("Content-Type: text/html; charset=ISO-8859-15");
	@readfile('payment.html');
}
