DROP INDEX log.externalreference_transaction_idx;
CREATE INDEX CONCURRENTLY externalreference_transaction_idx ON log.externalreference_tbl (txnid, externalid, pspid, type);
