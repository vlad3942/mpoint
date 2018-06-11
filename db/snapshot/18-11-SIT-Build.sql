ALTER TABLE log.transaction_tbl
ADD approval_action_code varchar(40) NULL;
COMMENT ON COLUMN log.transaction_tbl.approval_action_code
IS 'This field contains an action code and approval code
"approval code":"action code"'