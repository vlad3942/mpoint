--CMP-3128
CREATE INDEX CONCURRENTLY additional_data_tbl_externalid_type_index on log.additional_data_tbl (externalid, type);

--CMP-3129
CREATE INDEX CONCURRENTLY order_tbl_txnid_index on log.order_tbl (txnid);

--CMP-3130
CREATE INDEX CONCURRENTLY flight_tbl_orderid_index on log.flight_tbl (order_id);

--CMP-3131
CREATE INDEX CONCURRENTLY transaction_tbl_clientid_pspid_index on log.transaction_tbl (clientid,pspid);