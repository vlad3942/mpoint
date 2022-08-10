/*
------------------------------------------------------------------------------------------------------------------
Date       : 2022-05-29
Purpose : Creates passbook partition model for Southwest & migrates data for mPoint
Author    : Sarvesh Chimkode 
------------------------------------------------------------------------------------------------------------------
*/

create table log.temp_txnpassbook_tbl_10069_default_202206_60000001
as select * from log.txnpassbook_tbl_10069_default
where transactionid>=60000001;

delete from log.txnpassbook_tbl_10069_default
where transactionid>=60000001;

Commit;

BEGIN;
LOCK TABLE ONLY log.txnpassbook_tbl_default IN EXCLUSIVE MODE NOWAIT; --optional 
LOCK TABLE ONLY log.txnpassbook_tbl IN EXCLUSIVE MODE NOWAIT; --optional

LOCK TABLE ONLY log.txnpassbook_tbl_10069 IN EXCLUSIVE MODE NOWAIT;
LOCK TABLE ONLY log.txnpassbook_tbl_10069_default IN EXCLUSIVE MODE NOWAIT;

select * from log.fn_generate_txnpassbook_partitions('log.txnpassbook_tbl',10069,'-1',60000001,70000000,1000000);
select * from log.fn_add_primary_key_nested_partitions('log.txnpassbook_tbl',10069,60000001,70000000,1000000);
select * from log.fn_generate_txnpassbook_indexes_nested_partitions('log.txnpassbook_tbl',10069,60000001,70000000,1000000);
select * from log.fn_add_txnpassbook_triggers('log.txnpassbook_tbl',10069,'Y',60000001,70000000,1000000);
select * from log.fn_generate_txnpassbook_publications('log.txnpassbook_tbl',10069,'Y',60000001,70000000,1000000);
select * from log.fn_add_txnpassbook_permissions('log.txnpassbook_tbl',10069,'Y',60000001,70000000,1000000);
select * from log.fn_add_txnpassbook_permissions_repuser('log.txnpassbook_tbl',10069,'Y',60000001,70000000,1000000);

select * from log.fn_generate_txnpassbook_partitions('log.txnpassbook_tbl',10069,'-1',70000001,80000000,1000000);
select * from log.fn_add_primary_key_nested_partitions('log.txnpassbook_tbl',10069,70000001,80000000,1000000);
select * from log.fn_generate_txnpassbook_indexes_nested_partitions('log.txnpassbook_tbl',10069,70000001,80000000,1000000);
select * from log.fn_add_txnpassbook_triggers('log.txnpassbook_tbl',10069,'Y',70000001,80000000,1000000);
select * from log.fn_generate_txnpassbook_publications('log.txnpassbook_tbl',10069,'Y',70000001,80000000,1000000);
select * from log.fn_add_txnpassbook_permissions('log.txnpassbook_tbl',10069,'Y',70000001,80000000,1000000);
select * from log.fn_add_txnpassbook_permissions_repuser('log.txnpassbook_tbl',10069,'Y',70000001,80000000,1000000);

Commit;

insert into log.txnpassbook_tbl_10069
select * from log.temp_txnpassbook_tbl_10069_default_202206_60000001;

Commit;


SET MAINTENANCE_WORK_MEM='500MB';

VACUUM ANALYZE log.transaction_tbl;
VACUUM ANALYZE log.txnpassbook_tbl_10069_DEFAULT;
VACUUM ANALYZE log.txnpassbook_tbl_10069;
VACUUM ANALYZE log.txnpassbook_tbl_10077_DEFAULT;
VACUUM ANALYZE log.txnpassbook_tbl_10077;
VACUUM ANALYZE log.txnpassbook_tbl_10101_DEFAULT;
VACUUM ANALYZE log.txnpassbook_tbl_10101;

SET MAINTENANCE_WORK_MEM=default;
