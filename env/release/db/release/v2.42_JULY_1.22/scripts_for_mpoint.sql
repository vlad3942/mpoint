
/*#Step 1: UPDATE Capture Amount*/
update log.transaction_tbl set captured = (amount+fee) where id in (32710650,32710110,32711323);

/*#Step 2: Update txnpassbook_tbl with status done for performedopt 2000*/
update log.txnpassbook_tbl set status = 'done' where transactionid in (32710650,32710110,32711323) and clientid=10077 and performedopt =2000;

/*#Step 3: insert requestedopt 5011 in txnpassbook_tbl*/
INSERT INTO log.txnpassbook_tbl (clientid, transactionid, amount, currencyid, requestedopt, performedopt, status, extref, extrefidentifier)
SELECT clientid, transactionid, amount, currencyid, 5011, null, 'done', '', 0 FROM log.txnpassbook_tbl where
        transactionid in (32710650,32710110,32711323) and clientid=10077 and performedopt =2000;

/*#Step 4: insert performedopt 2001 in txnpassbook_tbl*/
INSERT INTO log.txnpassbook_tbl (clientid, transactionid, amount, currencyid, requestedopt, performedopt, status, extref, extrefidentifier)
SELECT clientid, transactionid, amount, currencyid, null, 2001, 'done',id, 'log.txnpassbook_tbl'  FROM log.txnpassbook_tbl where
        transactionid in (32710650,32710110,32711323) and clientid=10077 and requestedopt =5011;

/*#Step 5: insert 2001,4030 in message_tbl*/
insert into log.message_tbl (txnid,stateid) select id,2001 from log.transaction_tbl where id in (32710650,32710110,32711323) and clientid=10077;

insert into log.message_tbl (txnid,stateid) select id,4030 from log.transaction_tbl where id in (32710650,32710110,32711323) and clientid=10077;

/*#Step 6: Update stateid in sessionid*/

UPDATE log.session_tbl set stateid=4030 where id in (select sessionid from log.transaction_tbl tt where id in (32710650,32710110,32711323) and clientid=10077);
