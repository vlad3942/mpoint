alter table log.txnpassbook_tbl
rename to txnpassbook_tbl_part;

alter table log.txnpassbook_tbl_backup_20200401
rename to txnpassbook_tbl;

ALTER TABLE log.txnpassbook_tbl OWNER TO mpoint;
ALTER TABLE log.txnpassbook_tbl_part OWNER TO mpoint;

--get last value of existing old sequence (it will get dropped soon)
SELECT last_value FROM log.txnpassbook_tbl_part_id_seq; --96001

--set new sequence (tagged to current table) to the last value +1
ALTER SEQUENCE log.txnpassbook_tbl_id_seq_backup 
RESTART WITH 96002; --max of last_value of existing sequence

--rename existing old sequence to backup
ALTER SEQUENCE log.txnpassbook_tbl_id_seq_backup  
RENAME TO txnpassbook_tbl_id_seq;

--verify index if not present create
CREATE INDEX CONCURRENTLY IF NOT EXISTS transactionid_idx ON log.txnpassbook_tbl (transactionid);

CREATE INDEX CONCURRENTLY IF NOT EXISTS performedopt_idx ON log.txnpassbook_tbl (performedopt);
CREATE INDEX CONCURRENTLY IF NOT EXISTS txn_status ON log.txnpassbook_tbl (performedopt,status);
CREATE INDEX CONCURRENTLY IF NOT EXISTS txnpassbook_tbl_extref_index ON log.txnpassbook_tbl (extref);

-- DROP TRIGGER update_txnpassbook ON txnpassbook_tbl;
create
    trigger update_txnpassbook before update
        on
        log.txnpassbook_tbl for each row execute procedure update_table_proc();
		
---------------------------------------

select * from log.fn_drop_txnpassbook_publications('log.txnpassbook_tbl',10018,'Y',1,20000000,1000000);
select * from log.fn_drop_txnpassbook_publications('log.txnpassbook_tbl',10020,'Y',1,20000000,1000000);
select * from log.fn_drop_txnpassbook_publications('log.txnpassbook_tbl',10021,'Y',1,20000000,1000000);
select * from log.fn_drop_txnpassbook_publications('log.txnpassbook_tbl',10069,'Y',1,20000000,1000000);
select * from log.fn_drop_txnpassbook_publications('log.txnpassbook_tbl',10022,'N',1,20000000,1000000);
select * from log.fn_drop_txnpassbook_publications('log.txnpassbook_tbl',10060,'N',1,20000000,1000000);
select * from log.fn_drop_txnpassbook_publications('log.txnpassbook_tbl',10061,'N',1,20000000,1000000);
select * from log.fn_drop_txnpassbook_publications('log.txnpassbook_tbl',10062,'N',1,20000000,1000000);
select * from log.fn_drop_txnpassbook_publications('log.txnpassbook_tbl',10065,'N',1,20000000,1000000);
select * from log.fn_drop_txnpassbook_publications('log.txnpassbook_tbl',10067,'N',1,20000000,1000000);
select * from log.fn_drop_txnpassbook_publications('log.txnpassbook_tbl',10073,'N',1,20000000,1000000);
select * from log.fn_drop_txnpassbook_publications('log.txnpassbook_tbl',10099,'N',1,20000000,1000000);
select * from log.fn_drop_txnpassbook_publications('log.txnpassbook_tbl',10066,'N',1,20000000,1000000);
select * from log.fn_drop_txnpassbook_publications('log.txnpassbook_tbl',10070,'N',1,20000000,1000000);
select * from log.fn_drop_txnpassbook_publications('log.txnpassbook_tbl',10071,'N',1,20000000,1000000);
select * from log.fn_drop_txnpassbook_publications('log.txnpassbook_tbl',10074,'N',1,20000000,1000000);
select * from log.fn_drop_txnpassbook_publications('log.txnpassbook_tbl',10075,'N',1,20000000,1000000);
select * from log.fn_drop_txnpassbook_publications('log.txnpassbook_tbl',10076,'N',1,20000000,1000000);
select * from log.fn_drop_txnpassbook_publications('log.txnpassbook_tbl',10077,'N',1,20000000,1000000);
select * from log.fn_drop_txnpassbook_publications('log.txnpassbook_tbl',10078,'N',1,20000000,1000000);
select * from log.fn_drop_txnpassbook_publications('log.txnpassbook_tbl',10079,'N',1,20000000,1000000);
select * from log.fn_drop_txnpassbook_publications('log.txnpassbook_tbl',10080,'N',1,20000000,1000000);
select * from log.fn_drop_txnpassbook_publications('log.txnpassbook_tbl',10089,'N',1,20000000,1000000);
select * from log.fn_drop_txnpassbook_publications('log.txnpassbook_tbl',10098,'N',1,20000000,1000000);

ALTER PUBLICATION mpoint_log_pub 
ADD TABLE log.txnpassbook_tbl; 

---------------------------------------




