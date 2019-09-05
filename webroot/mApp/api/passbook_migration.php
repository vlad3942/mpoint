<?php

// Require Global Include File
require_once("../../inc/include.php");

$insertRecords = array();

$sql = 'SELECT t.id, t.amount, t.currencyid, m.stateid
        FROM log.' . sSCHEMA_POSTFIX . 'transaction_tbl t
                 INNER JOIN log.' . sSCHEMA_POSTFIX . 'message_tbl m ON t.id = m.txnid
                 INNER JOIN log.' . sSCHEMA_POSTFIX . "txnpassbook_tbl p ON t.id <> p.transactionid
        WHERE m.created > (now() - INTERVAL '2 WEEKS')
        AND m.stateid in (1001, 2000, 2001, 2002, 2003)
        AND EXISTS(SELECT mt.txnid FROM log." . sSCHEMA_POSTFIX . 'message_tbl mt WHERE mt.stateid = 2000 and mt.txnid=t.id)
        AND t.clientid= 10069
        GROUP BY t.id, t.amount, t.currencyid, m.stateid, m.created
        ORDER BY m.created ASC';

$aRS = $_OBJ_DB->getAllNames($sql);
if (is_array($aRS) === TRUE && count($aRS) > 0) {
    foreach ($aRS as $rs) {
        $transactionId = (int)$rs["ID"];
        $amount = (int)$rs["AMOUNT"];
        $currency = (int)$rs["CURRENCYID"];
        $state2 = (int)$rs["STATEID"];
        $state1 = 0;

        if($state2 === 1001)
        {
            $state1 = 5014;
        }
        elseif($state2 === 2000)
        {
            $state1 = 5010;
        }
        elseif($state2 === 2001)
        {
            $state1 = 5011;
        }
        elseif($state2 === 2002)
        {
            $state1 = 5012;
        }
        elseif($state2 === 2003)
        {
            $state1 = 5013;
        }

        if($state1 !== 0) {
            $request1 = array('transactionId' => $transactionId, 'amount' => $amount, 'currency' => $currency, 'state1' => $state1, 'state2' => 'null');
            array_push($insertRecords, $request1);
            $request2 = array('transactionId' => $transactionId, 'amount' => $amount, 'currency' => $currency, 'state1' => 'null', 'state2' => $state2);
            array_push($insertRecords, $request2);
        }
    }
}

$insertSql = '';
foreach ($insertRecords as $insertRecort)
{
    $transactionId = (int)$insertRecort["transactionId"];
    $amount = (int)$insertRecort["amount"];
    $currency = (int)$insertRecort["currency"];
    $state1 = $insertRecort["state1"];
    $state2 = $insertRecort["state2"];
    $insertSql = 'INSERT INTO log.txnpassbook_tbl (transactionid, amount, currencyid, requestedopt, performedopt, status) VALUES (' . $transactionId . ', '.$amount.', '.$currency.', '.$state1.', '.$state2.', \'done\');';

    $_OBJ_DB->query($insertSql);

}

//echo $insertSql;

