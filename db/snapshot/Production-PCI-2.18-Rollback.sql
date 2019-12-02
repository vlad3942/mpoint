ALTER TABLE enduser.account_tbl DROP COLUMN profileid;

ALTER TABLE Log.order_tbl DROP COLUMN fees;

ALTER TABLE Log.order_tbl DROP COLUMN orderref;

DROP TABLE system.businesstype_tbl;

ALTER TABLE client.account_tbl DROP COLUMN businessType;

ALTER TABLE enduser.account_tbl DROP COLUMN profileid;

DROP TRIGGER Update_Settlement ON Log.settlement_tbl;

DROP TRIGGER Update_Settlement_Record ON Log.settlement_record_tbl;

DROP INDEX account_tbl_businessType_index;

DROP INDEX order_tbl_orderref_index;

DROP INDEX eu_account_tbl_profileid_index;





