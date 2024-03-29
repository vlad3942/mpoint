-- Card prefix range for master card --
INSERT INTO "system".cardprefix_tbl (cardid, min, max, enabled) VALUES(7, 222100, 272099, true);
CREATE INDEX CONCURRENTLY externalreference_transaction_idx ON Log.Externalreference_Tbl (txnid, externalid);
CREATE INDEX address_tbl_referebceid_type_index ON log.address_tbl USING btree (reference_id, reference_type);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, "type","scope" ) VALUES('IS_STORE_BILLING_ADDRS', 'true',10077, 'client', 0);
