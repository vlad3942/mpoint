
---CMP-5454,CMP-5795 ---

--Sarvesh (Scripts for OD)
create table log.temp_txnpassbook_tbl_10018_default
as select * from log.txnpassbook_tbl_10018_default;

delete from log.txnpassbook_tbl_10018_default;

Commit;

select * from log.fn_generate_txnpassbook_partitions('log.txnpassbook_tbl',10018,'-1',30000001,50000000,1000000);
select * from log.fn_add_primary_key_nested_partitions('log.txnpassbook_tbl',10018,30000001,50000000,1000000);
select * from log.fn_generate_txnpassbook_indexes_nested_partitions('log.txnpassbook_tbl',10018,30000001,50000000,1000000);
select * from log.fn_add_txnpassbook_triggers('log.txnpassbook_tbl',10018,'Y',30000001,50000000,1000000);
select * from log.fn_generate_txnpassbook_publications('log.txnpassbook_tbl',10018,'Y',30000001,50000000,1000000);
select * from log.fn_add_txnpassbook_permissions('log.txnpassbook_tbl',10018,'Y',30000001,50000000,1000000);
select * from log.fn_add_txnpassbook_permissions_repuser('log.txnpassbook_tbl',10018,'Y',30000001,50000000,1000000);

Commit;

insert into log.txnpassbook_tbl_10018
select * from log.temp_txnpassbook_tbl_10018_default;

Commit;


----Sarvesh (Scripts for PAL)
create table log.temp_txnpassbook_tbl_10020_default
as select * from log.txnpassbook_tbl_10020_default;

delete from log.txnpassbook_tbl_10020_default;

COMMIT;

select * from log.fn_generate_txnpassbook_partitions('log.txnpassbook_tbl',10020,'-1',30000001,50000000,1000000);
select * from log.fn_add_primary_key_nested_partitions('log.txnpassbook_tbl',10020,30000001,50000000,1000000);
select * from log.fn_generate_txnpassbook_indexes_nested_partitions('log.txnpassbook_tbl',10020,30000001,50000000,1000000);
select * from log.fn_add_txnpassbook_triggers('log.txnpassbook_tbl',10020,'Y',30000001,50000000,1000000);
select * from log.fn_generate_txnpassbook_publications('log.txnpassbook_tbl',10020,'Y',30000001,50000000,1000000);
select * from log.fn_add_txnpassbook_permissions('log.txnpassbook_tbl',10020,'Y',30000001,50000000,1000000);
select * from log.fn_add_txnpassbook_permissions_repuser('log.txnpassbook_tbl',10020,'Y',30000001,50000000,1000000);

COMMIT;

insert into log.txnpassbook_tbl_10020
select * from log.temp_txnpassbook_tbl_10020_default;

COMMIT;


-------CMP-5454 ------
--Sarvesh (Scripts for AV)
select * from log.fn_generate_txnpassbook_partitions('log.txnpassbook_tbl',10101,'Y',1,50000000,1000000);
select * from log.fn_add_primary_key_nested_partitions('log.txnpassbook_tbl',10101,1,50000000,1000000);
select * from log.fn_generate_txnpassbook_indexes_nested_partitions('log.txnpassbook_tbl',10101,1,50000000,1000000);
select * from log.fn_add_txnpassbook_triggers('log.txnpassbook_tbl',10101,'Y',1,50000000,1000000);
select * from log.fn_add_txnpassbook_permissions('log.txnpassbook_tbl',10101,'Y',1,50000000,1000000);
select * from log.fn_add_txnpassbook_permissions_repuser('log.txnpassbook_tbl',10101,'Y',1,50000000,1000000);
select * from log.fn_generate_txnpassbook_publications('log.txnpassbook_tbl',10101,'Y',1,50000000,1000000);

Commit;

CREATE TABLE Log.TxnPassbook_Tbl_10101_Default PARTITION OF Log.TxnPassbook_Tbl_10101 DEFAULT;
ALTER TABLE log.txnpassbook_tbl_10101_default OWNER TO mpoint;
GRANT ALL ON TABLE log.txnpassbook_tbl_10101_default TO mpoint;
ALTER TABLE log.txnpassbook_tbl_10101_default ADD PRIMARY KEY (id);
CREATE INDEX idx_txnpassbook_tbl_10101_default ON log.txnpassbook_tbl_10101_default USING btree (clientid, transactionid);
CREATE INDEX txnpassbook_tbl_10101_default_clientid_created_idx ON log.txnpassbook_tbl_10101_default USING btree (clientid, created);
CREATE TRIGGER update_txnpassbook_tbl_10101_default
    BEFORE UPDATE
    ON log.txnpassbook_tbl_10101_default
    FOR EACH ROW
    EXECUTE PROCEDURE public.update_table_proc();
ALTER PUBLICATION mpoint_log_pub ADD TABLE log.txnpassbook_tbl_10101_default;
GRANT SELECT ON log.txnpassbook_tbl_10101_default TO repuser;


----Sarvesh (Scripts for CEBU)
create table log.temp_txnpassbook_tbl_10077_default
as select * from log.txnpassbook_tbl_default where clientid=10077;

delete from log.txnpassbook_tbl_default where clientid=10077;

Commit;

select * from log.fn_generate_txnpassbook_partitions('log.txnpassbook_tbl',10077,'Y',1,50000000,1000000);
select * from log.fn_add_primary_key_nested_partitions('log.txnpassbook_tbl',10077,1,50000000,1000000);
select * from log.fn_generate_txnpassbook_indexes_nested_partitions('log.txnpassbook_tbl',10077,1,50000000,1000000);
select * from log.fn_add_txnpassbook_triggers('log.txnpassbook_tbl',10077,'Y',1,50000000,1000000);
select * from log.fn_add_txnpassbook_permissions('log.txnpassbook_tbl',10077,'Y',1,50000000,1000000);
select * from log.fn_add_txnpassbook_permissions_repuser('log.txnpassbook_tbl',10077,'Y',1,50000000,1000000);
select * from log.fn_generate_txnpassbook_publications('log.txnpassbook_tbl',10077,'Y',1,50000000,1000000);

Commit;

CREATE TABLE Log.TxnPassbook_Tbl_10077_Default PARTITION OF Log.TxnPassbook_Tbl_10077 DEFAULT;
ALTER TABLE log.txnpassbook_tbl_10077_default OWNER TO mpoint;
GRANT ALL ON TABLE log.txnpassbook_tbl_10077_default TO mpoint;
ALTER TABLE log.txnpassbook_tbl_10077_default ADD PRIMARY KEY (id);
CREATE INDEX idx_txnpassbook_tbl_10077_default ON log.txnpassbook_tbl_10077_default USING btree (clientid, transactionid);
CREATE INDEX txnpassbook_tbl_10077_default_clientid_created_idx ON log.txnpassbook_tbl_10077_default USING btree (clientid, created);
CREATE TRIGGER update_txnpassbook_tbl_10077_default
    BEFORE UPDATE
    ON log.txnpassbook_tbl_10077_default
    FOR EACH ROW
    EXECUTE PROCEDURE public.update_table_proc();
ALTER PUBLICATION mpoint_log_pub ADD TABLE log.txnpassbook_tbl_10077_default;
GRANT SELECT ON log.txnpassbook_tbl_10077_default TO repuser;

Insert into Log.TxnPassbook_Tbl_10077
select * from log.temp_txnpassbook_tbl_10077_default;

Commit;

-----------