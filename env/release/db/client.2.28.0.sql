
-- mPoint DB Scripts :

-- CMP-5664------
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES('IS_STORE_BILLING_ADDRS', 'true', 10101, 'client', true, 0);

------- CMP-5454 ------
select * from log.fn_generate_txnpassbook_partitions('log.txnpassbook_tbl',10101,'Y',1,10000000,1000000);

select * from log.fn_generate_txnpassbook_indexes_nested_partitions('log.txnpassbook_tbl',10101,1,20000000,1000000);

select * from log.fn_add_primary_key_nested_partitions('log.txnpassbook_tbl',10101,1,10000000,1000000);

select * from log.fn_add_txnpassbook_triggers('log.txnpassbook_tbl',10101,'N',1,20000000,1000000);

select * from log.fn_add_txnpassbook_permissions('log.txnpassbook_tbl',10101,'N');