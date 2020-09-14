DROP INDEX IF EXISTS log.paymentsecure_txn_idx;
CREATE UNIQUE INDEX paymentsecure_txn_idx ON log.paymentsecureinfo_tbl USING btree (txnid);

-- Enable Payment Retry With Alternate Route
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('PAYMENT_RETRY_WITH_ALTERNATE_ROUTE', 'true', true, <client_id>, 'client');
