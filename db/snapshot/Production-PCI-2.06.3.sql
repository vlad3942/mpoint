-- Drop orderId unique constraint --
ALTER TABLE log.session_tbl DROP CONSTRAINT constraint_name;

--- SET THIS FOR ALL VELOCITY CLIENTS --
INSERT INTO client.additionalproperty_tbl(key, value,externalid, type)
VALUES ('webSessionTimeout', 2000, 10007, 'client');

/*START: Fixing incorrect values for USD in PSPCurrency_tbl in system schema */

UPDATE system.pspcurrency_tbl sp
SET name = sc.code
FROM system.currency_tbl sc
WHERE sc.id = sp.currencyid;

/*END: Fixing incorrect values for USD in PSPCurrency_tbl in system schema*/

-- Malindo Vietnam Static route

INSERT INTO client.cardaccess_tbl
( clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type)
VALUES( 10018, 7, true, 28, 649, 1, NULL, false, 1);
INSERT INTO client.cardaccess_tbl
( clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type)
VALUES( 10018, 8, true, 28, 649, 1, NULL, false, 1);

-- End Malindo Vietnam Static route


ALTER TABLE log.transaction_tbl
  ADD approval_action_code varchar(40) NULL;
COMMENT ON COLUMN log.transaction_tbl.approval_action_code
IS 'This field contains an action code and approval code
"approval code":"action code"';

-- Settlement Improvement
ALTER TABLE system.psp_tbl ADD capture_method int DEFAULT 0;
COMMENT ON COLUMN system.psp_tbl.capture_method IS '0 - manual
2 - bulk capture
3 - bulk refund
6 - bulk capture + bulk  refund';

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
)
WITH (
  OIDS=FALSE
);
ALTER TABLE log.settlement_tbl
  OWNER TO mpoint;
  

CREATE TABLE log.settlement_record_tbl
(
  id            serial PRIMARY KEY,
  settlementid  int,
  transactionid int,
  description varchar(100),
  CONSTRAINT settlement_record_tbl_settlement_tbl_id_fk FOREIGN KEY (settlementid) REFERENCES log.settlement_tbl (id),
  CONSTRAINT settlement_record_tbl_transaction_tbl_id_fk FOREIGN KEY (transactionid) REFERENCES log.transaction_tbl (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE log.settlement_record_tbl
  OWNER TO mpoint;
  


-- Table: system.retrialtype_tbl

-- DROP TABLE system.retrialtype_tbl;

CREATE TABLE system.retrialtype_tbl
(
  id serial NOT NULL,
  name character varying(255),
  description character varying(255),
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true,
  CONSTRAINT retrialtype_pk PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE system.retrialtype_tbl
  OWNER TO mpoint;
  
  
  -- Table: client.retrial_tbl

-- DROP TABLE client.retrial_tbl;

CREATE TABLE client.retrial_tbl
(
  id serial NOT NULL,  
  typeid integer  NOT NULL,
  retrialvalue character varying(255),
  delay integer,
  clientid integer NOT NULL,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true,
  CONSTRAINT retrial_pk PRIMARY KEY (id),
  CONSTRAINT retrialtype_fk FOREIGN KEY (typeid)
      REFERENCES system.retrialtype_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT client_fk FOREIGN KEY (clientid)
      REFERENCES client.client_tbl (id) MATCH SIMPLE    
)
WITH (
  OIDS=FALSE
);
ALTER TABLE client.retrial_tbl
  OWNER TO mpoint;

  
------- System data for retrial types ------------------

INSERT INTO system.retrialtype_tbl( id, name, description) VALUES (1,'Time Based','Retry till the defined time period');
INSERT INTO system.retrialtype_tbl( id, name, description) VALUES (2,'Response Based','Retry until specified response received');
INSERT INTO system.retrialtype_tbl( id, name, description) VALUES (3,'Max Attempt Based','Retry until Max attempts are over');


-- Adding Virtual Token for Saving SUVTP in mPoint schema
ALTER TABLE Log.Transaction_Tbl ADD COLUMN virtualtoken character varying(512);

ALTER TABLE log.settlement_tbl ALTER COLUMN status TYPE varchar(100) USING status::varchar(100);


ALTER TABLE log.transaction_tbl ALTER COLUMN attempt SET DEFAULT 0;
/*======= ADD NEW PROCESSOR TYPE FOR TOKENIZATION SYSTEM ======== */
INSERT INTO system.processortype_tbl (id, name) VALUES (8, 'Tokenize');
/*======= END NEW PROCESSOR TYPE FOR TOKENIZATION SYSTEM ======== */



