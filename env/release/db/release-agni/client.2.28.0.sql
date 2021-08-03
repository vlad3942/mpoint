-- mPoint DB Scripts:  


--- script for legacy callback flow ----

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'IS_LEGACY_CALLBACK_FLOW', 'true',  true, id , 'client', 2 from client.client_tbl;


-- CMP-5546
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'post_fraud_rule', 'isPostFraudAttemp::=<isPostFraudAttemp1>OR<isPostFraudAttemp2>OR<isPostFraudAttemp3>OR<isPostFraudAttemp4>
isPostFraudAttemp1::=<eci>=="02"AND<isCryptogrm>!==""
isPostFraudAttemp2::=<eci>=="05"AND<isCryptogrm>!==""
isPostFraudAttemp3::=<eci>=="01"AND<isCryptogrm>!==""
isPostFraudAttemp4::=<eci>=="07"AND<isCryptogrm>!==""
eci::=(card.info-3d-secure.cryptogram.@eci)
isCryptogrm::={trim.(card,info-3d-secure,cryptogram)}', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=10077 AND pspid=72;



-- mPoint DB Scripts :

-- CMP-5546
DELETE FROM client.additionalproperty_tbl WHERE key = 'post_fraud_rule' AND externalid = (SELECT id from client.merchantaccount_tbl WHERE clientid=10077 AND pspid=72);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'post_fraud_rule', 'isPostFraudAttemp::=<isPostFraudAttemp1>OR<isPostFraudAttemp2>OR<isPostFraudAttemp3>OR<isPostFraudAttemp4>
isPostFraudAttemp1::=<eci>=="02"AND<isCryptogrm>!==""
isPostFraudAttemp2::=<eci>=="05"AND<isCryptogrm>!==""
isPostFraudAttemp3::=<eci>=="01"AND<isCryptogrm>!==""
isPostFraudAttemp4::=<eci>=="06"AND<isCryptogrm>!==""
eci::=(card.info-3d-secure.cryptogram.@eci)
isCryptogrm::={trim.(card,info-3d-secure,cryptogram)}', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=10077 AND pspid=72;


-- mPoint DB Scripts :


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

-- mPoint Sctipts

-- CMP-5799

INSERT INTO client.additionalproperty_tbl("key", value, modified, created, enabled, externalid, "type", "scope")VALUES('minPollingInterval', '5', now(), now(), true, 10077, 'client', 2);

INSERT INTO client.additionalproperty_tbl("key", value, modified, created, enabled, externalid, "type", "scope")VALUES('maxPollingInterval', '15', now(), now(), true, 10077, 'client', 2);