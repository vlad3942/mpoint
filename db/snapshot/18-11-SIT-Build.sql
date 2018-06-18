-- Settlement Improvement
ALTER TABLE system.psp_tbl ADD capture_method int DEFAULT 0;
COMMENT ON COLUMN system.psp_tbl.capture_method IS '0 - manual
2 - bulk capture
3 - bulk refund
6 - bulk capture + bulk  refund';

-- Update AMEX PSP
UPDATE system.psp_tbl SET  capture_method = 6 WHERE id = 45;

ALTER TABLE log.transaction_tbl
ADD approval_action_code varchar(40) NULL;
COMMENT ON COLUMN log.transaction_tbl.approval_action_code
IS 'This field contains an action code and approval code
"approval code":"action code"';

CREATE TABLE log.settlement_tbl
(
  id                     serial PRIMARY KEY,
  record_number          int         NOT NULL,
  file_reference_number  varchar(10) NOT NULL,
  file_sequence_number   int         NOT NULL,
  created                timestamp DEFAULT now(),
  client_id              int         NOT NULL,
  record_tracking_number varchar(20),
  record_type            varchar(20) NULL
);

CREATE TABLE log.settlement_record_tbl
(
  id            serial PRIMARY KEY,
  settlementid  int,
  transactionid int,
  CONSTRAINT settlement_record_tbl_settlement_tbl_id_fk FOREIGN KEY (settlementid) REFERENCES log.settlement_tbl (id),
  CONSTRAINT settlement_record_tbl_transaction_tbl_id_fk FOREIGN KEY (transactionid) REFERENCES log.transaction_tbl (id)
);

ALTER TABLE log.settlement_tbl ADD status varchar(10) DEFAULT 'active' NOT NULL;


INSERT INTO log.state_tbl (id, name, module, func) VALUES (2103, 'Refund Initialized', 'Payment', 'refund');
INSERT INTO log.state_tbl (id, name, module, func) VALUES (2102, 'Cancel Initialized', 'Payment', 'cancel');
INSERT INTO log.state_tbl (id, name, module, func) VALUES (2101, 'Capture Initialized', 'Payment', 'capture');