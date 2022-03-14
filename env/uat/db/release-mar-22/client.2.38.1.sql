-- Split payment hybrid --
UPDATE client.split_configuration_tbl SET type = 'hybrid' WHERE trim(name) IN ('Card+Voucher','APM+Voucher','Wallet+Voucher');
UPDATE client.split_configuration_tbl SET type = 'conventional' WHERE trim(name) IN ('Card+Card');

SELECT setval('client.route_tbl_id_seq', COALESCE((SELECT MAX(id)+1 FROM client.route_tbl), 1), false);
SELECT setval('client.routeconfig_tbl_id_seq', COALESCE((SELECT MAX(id)+1 FROM client.routeconfig_tbl), 1), false);
SELECT setval('client.routecountry_tbl_id_seq', COALESCE((SELECT MAX(id)+1 FROM client.routecountry_tbl), 1), false);
SELECT setval('client.routecurrency_tbl_id_seq', COALESCE((SELECT MAX(id)+1 FROM client.routecurrency_tbl), 1), false);
SELECT setval('client.routefeature_tbl_id_seq', COALESCE((SELECT MAX(id)+1 FROM client.routefeature_tbl), 1), false);
SELECT setval('client.routepm_tbl_id_seq', COALESCE((SELECT MAX(id)+1 FROM client.routepm_tbl), 1), false);

-- Client propert fingerprint enchancment --
UPDATE client.client_property_tbl SET value = '45ssiuz3' where propertyid = (select id from system.client_property_tbl where name = 'CYBS_DM_ORGID') and clientid = 10101;


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

-- Stabilize --

CREATE OR REPLACE PROCEDURE public.sp_stabilize_db_connections  --stabilize_pg_v1.1.sql
(
p_age numeric,
p_retain numeric
)
LANGUAGE plpgsql
SECURITY DEFINER
AS $procedure$
/*------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		Procedure : public.sp_stabilize_db_connections()
		Version	  : v1.1
		Date		  : 2022-02-22
		Purpose 	  : Terminates the Idle connections based on the connection age & retention
		Author	      : CPD (SWE/Sarvesh)
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
DECLARE

create_query text;
index_query text;
barray boolean[];

v_bool boolean;

  conn_sql text;
  rec record;

BEGIN

  conn_sql = '
WITH idle_connections
AS
(
    SELECT
        pid,
        rank() over (partition by client_addr order by backend_start ASC) as rank
    FROM
        pg_stat_activity
    WHERE
        pid <> pg_backend_pid( )
    AND
        application_name !~ ''(?:psql)|(?:pgAdmin.+)''
    AND
        datname = current_database()
    AND
        state in (''idle'', ''idle in transaction'', ''idle in transaction (aborted)'', ''disabled'')
    AND
        current_timestamp - state_change > interval '''||p_age|| ' minutes'''
||')
SELECT
    pid
FROM
    idle_connections
WHERE     rank > '||p_retain||'
'
;

--    RAISE NOTICE 'conn_sql %', conn_sql;

  FOR r IN EXECUTE conn_sql
  LOOP
--  PERFORM pg_stat_clear_snapshot();

    PERFORM pg_terminate_backend(r.pid);
	  -- RAISE NOTICE 'Removed pid %', r.pid;

  END LOOP;
 -- PERFORM pg_sleep(1);

END;
$procedure$
;