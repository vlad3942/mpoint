/*-------------------------------------------------------------------------------------------
		Script : cutover_commands.sql
		Version	  : v1.1
		Date		  : 2020-03-09
		Purpose 	  : Commands to be run during the cutover,application Downtime.
		Author	      : CPM (SWE/Sarvesh)
--------------------------------------------------------------------------------------------*/
--get last value
SELECT last_value FROM log.txnpassbook_tbl_id_seq; --96001

--set new sequence
ALTER SEQUENCE log.txnpassbook_tbl_part_id_seq 
RESTART WITH 96002; --max of last_value of existing sequence

--rename existing
ALTER SEQUENCE log.txnpassbook_tbl_id_seq  
RENAME TO txnpassbook_tbl_id_seq_backup;

--rename new sequence
ALTER SEQUENCE log.txnpassbook_tbl_part_id_seq 
RENAME TO txnpassbook_tbl_id_seq;

---DROP base TABLE PUBLICATION
ALTER PUBLICATION mpoint_log_pub 
DROP TABLE log.txnpassbook_tbl;

--Backup & Rename
alter table log.txnpassbook_tbl
rename to txnpassbook_tbl_backup_20200401;

alter table log.txnpassbook_tbl_part
rename to txnpassbook_tbl;

ALTER TABLE log.txnpassbook_tbl OWNER TO mpoint;
