alter table log.settlement_tbl
	add modified TIMESTAMP default now();

CREATE TRIGGER Update_Settlement
    BEFORE UPDATE
    ON Log.settlement_tbl
    FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();

alter table log.settlement_record_tbl
	add created TIMESTAMP default now();

alter table log.settlement_record_tbl
	add modified TIMESTAMP default now();

CREATE TRIGGER Update_Settlement_Record
    BEFORE UPDATE
    ON Log.settlement_record_tbl
    FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();