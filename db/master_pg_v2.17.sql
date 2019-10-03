--CMP-3128
ALTER TABLE log.transaction_tbl ADD profileid int8 NULL;

comment on column log.transaction_tbl.profileid is 'mProfile id associated with the txn';

