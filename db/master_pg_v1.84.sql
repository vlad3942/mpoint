-- Table: log.operation_tbl
-- Data table for operations such as saving a card, deleting a card, etc.
CREATE TABLE log.operation_tbl
(
	id SERIAL NOT NULL,
	name VARCHAR(255),
	created TIMESTAMP WITH TIME ZONE DEFAULT now(),
	modified TIMESTAMP WITH TIME ZONE DEFAULT now(),
	enabled BOOL,
	CONSTRAINT operation_pk PRIMARY KEY (id)
) WITHOUT OIDS;

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE log.operation_tbl TO mpoint;


-- Table: log.auditlog_tbl
-- Logs the activities such as saving a card, deleting a card, etc.
CREATE TABLE log.auditlog_tbl
(
	id SERIAL NOT NULL,
	operationid INT4 NOT NULL,
	mobile INT8,
	email VARCHAR(255),
	customer_ref VARCHAR(50),
	code INT4 NOT NULL,
	message VARCHAR(255),
	created TIMESTAMP WITH TIME ZONE DEFAULT now(),
	modified TIMESTAMP WITH TIME ZONE DEFAULT now(),
	enabled BOOL,
  	CONSTRAINT auditlog_pk PRIMARY KEY (id),
  	CONSTRAINT auditlog2operation_fk FOREIGN KEY (operationid)
      REFERENCES log.operation_tbl (id) 
      ON UPDATE CASCADE ON DELETE CASCADE
) WITHOUT OIDS;
  
GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE log.auditlog_tbl TO mpoint;

-- Flag to show all cards, including disabled and expired cards
ALTER TABLE Client.Client_Tbl ADD show_all_cards BOOL DEFAULT false;
