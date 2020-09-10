DROP INDEX IF EXISTS log.paymentsecure_txn_idx;
CREATE UNIQUE INDEX paymentsecure_txn_idx ON log.paymentsecureinfo_tbl USING btree (txnid);
