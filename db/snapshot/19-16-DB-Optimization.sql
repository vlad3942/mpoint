--CMP-3128
CREATE INDEX CONCURRENTLY additional_data_tbl_externalid_type_index on log.additional_data_tbl (externalid, type);

--CMP-3129
CREATE INDEX CONCURRENTLY order_tbl_txnid_index on log.order_tbl (txnid);