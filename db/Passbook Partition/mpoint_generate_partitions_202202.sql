-----------------------------------------------------------------------------------------------------
Date       : 2022-02-16
Jira         : CMP-6342
Purpose : Creates passbook partition model & migrates data for mPoint
Author    : Sarvesh Chimkode 
-----------------------------------------------------------------------------------------------------

--Sarvesh (Scripts for OD)

create table log.temp_txnpassbook_tbl_10018_default_202202
as select * from log.txnpassbook_tbl_10018_default;

delete from log.txnpassbook_tbl_10018_default;

Commit;

BEGIN;
LOCK TABLE ONLY log.txnpassbook_tbl IN EXCLUSIVE MODE NOWAIT;
LOCK TABLE ONLY log.txnpassbook_tbl_default IN EXCLUSIVE MODE NOWAIT;

select * from log.fn_generate_txnpassbook_partitions('log.txnpassbook_tbl',10018,'-1',50000001,60000000,1000000);
select * from log.fn_add_primary_key_nested_partitions('log.txnpassbook_tbl',10018,50000001,60000000,1000000);
select * from log.fn_generate_txnpassbook_indexes_nested_partitions('log.txnpassbook_tbl',10018,50000001,60000000,1000000);
select * from log.fn_add_txnpassbook_triggers('log.txnpassbook_tbl',10018,'Y',50000001,60000000,1000000);
select * from log.fn_generate_txnpassbook_publications('log.txnpassbook_tbl',10018,'Y',50000001,60000000,1000000);
select * from log.fn_add_txnpassbook_permissions('log.txnpassbook_tbl',10018,'Y',50000001,60000000,1000000);
select * from log.fn_add_txnpassbook_permissions_repuser('log.txnpassbook_tbl',10018,'Y',50000001,60000000,1000000);

Commit;

insert into log.txnpassbook_tbl_10018
select * from log.temp_txnpassbook_tbl_10018_default_202202;

Commit;

---------------------------------------------------------------------------------------

--Sarvesh (Scripts for PAL)

create table log.temp_txnpassbook_tbl_10020_default_202202
as select * from log.txnpassbook_tbl_10020_default;

delete from log.txnpassbook_tbl_10020_default;

Commit;

BEGIN;
LOCK TABLE ONLY log.txnpassbook_tbl IN EXCLUSIVE MODE NOWAIT;
LOCK TABLE ONLY log.txnpassbook_tbl_default IN EXCLUSIVE MODE NOWAIT;

select * from log.fn_generate_txnpassbook_partitions('log.txnpassbook_tbl',10020,'-1',50000001,60000000,1000000);
select * from log.fn_add_primary_key_nested_partitions('log.txnpassbook_tbl',10020,50000001,60000000,1000000);
select * from log.fn_generate_txnpassbook_indexes_nested_partitions('log.txnpassbook_tbl',10020,50000001,60000000,1000000);
select * from log.fn_add_txnpassbook_triggers('log.txnpassbook_tbl',10020,'Y',50000001,60000000,1000000);
select * from log.fn_generate_txnpassbook_publications('log.txnpassbook_tbl',10020,'Y',50000001,60000000,1000000);
select * from log.fn_add_txnpassbook_permissions('log.txnpassbook_tbl',10020,'Y',50000001,60000000,1000000);
select * from log.fn_add_txnpassbook_permissions_repuser('log.txnpassbook_tbl',10020,'Y',50000001,60000000,1000000);

Commit;

insert into log.txnpassbook_tbl_10020
select * from log.temp_txnpassbook_tbl_10020_default_202202;

Commit;

---------------------------------------------------------------------------------------

--Sarvesh (Scripts for SWA)

create table log.temp_txnpassbook_tbl_10069_default_202202
as select * from log.txnpassbook_tbl_10069_default
where transactionid>=50000001;

delete from log.txnpassbook_tbl_10069_default
where transactionid>=50000001;

Commit;

BEGIN;
LOCK TABLE ONLY log.txnpassbook_tbl IN EXCLUSIVE MODE NOWAIT;
LOCK TABLE ONLY log.txnpassbook_tbl_default IN EXCLUSIVE MODE NOWAIT;

select * from log.fn_generate_txnpassbook_partitions('log.txnpassbook_tbl',10069,'-1',50000001,60000000,1000000);
select * from log.fn_add_primary_key_nested_partitions('log.txnpassbook_tbl',10069,50000001,60000000,1000000);
select * from log.fn_generate_txnpassbook_indexes_nested_partitions('log.txnpassbook_tbl',10069,50000001,60000000,1000000);
select * from log.fn_add_txnpassbook_triggers('log.txnpassbook_tbl',10069,'Y',50000001,60000000,1000000);
select * from log.fn_generate_txnpassbook_publications('log.txnpassbook_tbl',10069,'Y',50000001,60000000,1000000);
select * from log.fn_add_txnpassbook_permissions('log.txnpassbook_tbl',10069,'Y',50000001,60000000,1000000);
select * from log.fn_add_txnpassbook_permissions_repuser('log.txnpassbook_tbl',10069,'Y',50000001,60000000,1000000);

Commit;

insert into log.txnpassbook_tbl_10069
select * from log.temp_txnpassbook_tbl_10069_default_202202;


Commit;

---------------------------------------------------------------------------------------

--Sarvesh (Scripts for CEBU)

create table log.temp_txnpassbook_tbl_10077_default_202202
as select * from log.txnpassbook_tbl_10077_default;

delete from log.txnpassbook_tbl_10077_default;

Commit;

BEGIN;
LOCK TABLE ONLY log.txnpassbook_tbl IN EXCLUSIVE MODE NOWAIT;
LOCK TABLE ONLY log.txnpassbook_tbl_default IN EXCLUSIVE MODE NOWAIT;

select * from log.fn_generate_txnpassbook_partitions('log.txnpassbook_tbl',10077,'-1',50000001,60000000,1000000);
select * from log.fn_add_primary_key_nested_partitions('log.txnpassbook_tbl',10077,50000001,60000000,1000000);
select * from log.fn_generate_txnpassbook_indexes_nested_partitions('log.txnpassbook_tbl',10077,50000001,60000000,1000000);
select * from log.fn_add_txnpassbook_triggers('log.txnpassbook_tbl',10077,'Y',50000001,60000000,1000000);
select * from log.fn_generate_txnpassbook_publications('log.txnpassbook_tbl',10077,'Y',50000001,60000000,1000000);
select * from log.fn_add_txnpassbook_permissions('log.txnpassbook_tbl',10077,'Y',50000001,60000000,1000000);
select * from log.fn_add_txnpassbook_permissions_repuser('log.txnpassbook_tbl',10077,'Y',50000001,60000000,1000000);

Commit;

insert into log.txnpassbook_tbl_10077
select * from log.temp_txnpassbook_tbl_10077_default_202202;

Commit;

---------------------------------------------------------------------------------------

--Sarvesh (Scripts for AV)

create table log.temp_txnpassbook_tbl_10101_default_202202
as select * from log.txnpassbook_tbl_10101_default;

delete from log.txnpassbook_tbl_10101_default;

Commit;

BEGIN;
LOCK TABLE ONLY log.txnpassbook_tbl IN EXCLUSIVE MODE NOWAIT;
LOCK TABLE ONLY log.txnpassbook_tbl_default IN EXCLUSIVE MODE NOWAIT;

select * from log.fn_generate_txnpassbook_partitions('log.txnpassbook_tbl',10101,'-1',50000001,60000000,1000000);
select * from log.fn_add_primary_key_nested_partitions('log.txnpassbook_tbl',10101,50000001,60000000,1000000);
select * from log.fn_generate_txnpassbook_indexes_nested_partitions('log.txnpassbook_tbl',10101,50000001,60000000,1000000);
select * from log.fn_add_txnpassbook_triggers('log.txnpassbook_tbl',10101,'Y',50000001,60000000,1000000);
select * from log.fn_generate_txnpassbook_publications('log.txnpassbook_tbl',10101,'Y',50000001,60000000,1000000);
select * from log.fn_add_txnpassbook_permissions('log.txnpassbook_tbl',10101,'Y',50000001,60000000,1000000);
select * from log.fn_add_txnpassbook_permissions_repuser('log.txnpassbook_tbl',10101,'Y',50000001,60000000,1000000);

Commit;

insert into log.txnpassbook_tbl_10101
select * from log.temp_txnpassbook_tbl_10101_default_202202;

Commit;

---------------------------------------------------------------------------------------------------------------------------