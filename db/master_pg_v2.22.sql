ALTER TABLE log.address_tbl add last_name varchar(200) null;
ALTER TABLE log.address_tbl RENAME COLUMN name TO first_name;
CREATE INDEX CONCURRENTLY externalreference_transaction_idx ON Log.Externalreference_Tbl (txnid, externalid);
CREATE INDEX address_tbl_referebceid_type_index ON log.address_tbl USING btree (reference_id, reference_type);