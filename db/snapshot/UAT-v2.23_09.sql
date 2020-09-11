INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('invoiceidrule_PAYPAL_CEBU', 'invoiceid ::= (psp-config/@id)=="24"=(transaction.@id)', true, 10077, 'client', 0);

DROP INDEX log.externalreference_transaction_idx;
CREATE INDEX CONCURRENTLY externalreference_transaction_idx ON log.externalreference_tbl (txnid, externalid, pspid, type);
CREATE INDEX CONCURRENTLY passeneger_tbl_orderid_index ON log.passenger_tbl USING btree (order_id);