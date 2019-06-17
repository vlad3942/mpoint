--ROLLBACK Scripts

ALTER TABLE system.psp_tbl ADD capture_method int DEFAULT 0;
COMMENT ON COLUMN system.psp_tbl.capture_method IS '0 - manual
2 - bulk capture
3 - bulk refund
6 - bulk capture + bulk  refund';


alter table client.cardaccess_tbl drop column capture_method;

alter table log.transaction_tbl drop column issuing_bank;

ALTER TABLE client.client_tbl drop column enable_cvv;