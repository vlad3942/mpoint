

DROP TABLE log.txnpassbook_tbl;

ALTER TABLE system.psp_tbl DROP COLUMN SupportedPartialOperations ;

ALTER TABLE client.merchantaccount_tbl DROP COLUMN SupportedPartialOperations ;	


--index on table: log.TxnPassbook_tbl --column:transactionid
DROP INDEX transactionid_idx ;

--index on table: log.TxnPassbook_tbl --column:performedopt
DROP INDEX performedopt_idx ;

--index on table: log.settlement_record_tbl --column:transactionid
DROP INDEX SRtransactionid_idx ;

--index on table: log.settlement_record_tbl --column:settlementid
DROP INDEX settlementid_idx ;

--index on table: log.settlement_tbl --column:client_id
DROP INDEX client_id_idx ;

--index on table: log.settlement_tbl --column:psp_id
DROP INDEX psp_id_idx ;
