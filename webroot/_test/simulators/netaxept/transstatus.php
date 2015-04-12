<?php
/**
 * User: johan
 * Date: 2/5/15
 * Time: 2:10 PM
 *
 * DIBS transstatus API simulator implemented to behave almost similar to:
 * http://tech.dibspayment.com/D2/FlexWin/API/Status_functions/transstatuspml
 */

$iMerchant = (integer)@$_REQUEST['merchant'];
$iTransact = (integer)@$_REQUEST['transact'];

if (true)
{
    if (defined("DIBS_SIMULATOR_TRANSSTATUS_BEHAVIOR") === true)
    {
        echo DIBS_SIMULATOR_TRANSSTATUS_BEHAVIOR;
    }
    else
    {
        echo "2"; //Authorization approved
    }
}
else
{
    echo "1"; //Declined
}
