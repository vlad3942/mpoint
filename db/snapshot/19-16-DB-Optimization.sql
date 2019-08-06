--CMP-3128
create index additional_data_tbl_externalid_type_index
    on log.additional_data_tbl (externalid, type);