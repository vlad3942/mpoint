-------- TXN Time Out -------------

UPDATE log.state_tbl SET name = 'Issuer timed out' WHERE id = 20109;
INSERT INTO log.state_tbl (id, name, module) VALUES (20108, 'PSP timed out', 'Payment');

-------- TXN Time Out -------------

CREATE TABLE log.settlement_tbl
(
    id serial PRIMARY KEY,
    record_number int NOT NULL,
    file_reference_number varchar(10) NOT NULL,
    file_sequence_number int NOT NULL,
    created timestamp DEFAULT now(),
    client_id int NOT NULL,
    psp_id int NOT NULL,
    record_tracking_number varchar(20),
    record_type varchar(20),
    description varchar(100),
    status varchar(10) DEFAULT 'active' NOT NULL,
    CONSTRAINT settlement_tbl_client_tbl_id_fk FOREIGN KEY (client_id) REFERENCES client.client_tbl (id),
    CONSTRAINT settlement_tbl_psp_tbl_id_fk FOREIGN KEY (psp_id) REFERENCES system.psp_tbl (id)
);

CREATE TABLE log.settlement_record_tbl
(
  id            serial PRIMARY KEY,
  settlementid  int,
  transactionid int,
  description varchar(100),
  CONSTRAINT settlement_record_tbl_settlement_tbl_id_fk FOREIGN KEY (settlementid) REFERENCES log.settlement_tbl (id),
  CONSTRAINT settlement_record_tbl_transaction_tbl_id_fk FOREIGN KEY (transactionid) REFERENCES log.transaction_tbl (id)
);
