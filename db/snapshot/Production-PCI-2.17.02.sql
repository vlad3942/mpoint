--index on table: log.TxnPassbook_tbl --column:performedopt,status
CREATE INDEX txn_status ON log.txnpassbook_tbl (performedopt, status);
