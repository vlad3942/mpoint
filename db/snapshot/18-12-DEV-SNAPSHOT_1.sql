-- Table: system.retrialtype_tbl

-- DROP TABLE system.retrialtype_tbl;

CREATE TABLE system.retrialtype_tbl
(
  id serial NOT NULL,
  name character varying(255),
  description character varying(255),
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true
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

------- System data for retrial types ------------------

------- Retrial define for SEPG client ------------------
INSERT INTO client.retrial_tbl( typeid, retrialvalue, delay, clientid)    VALUES (2,'OK', 5 , 10007);
------- Retrial define for SEPG client ------------------

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

ALTER table Client.Client_Tbl ADD secretkey VARCHAR(100);

UPDATE Client.Client_tbl SET secretkey='KJHs@iWqha91203e23' WHERE id = 10007 ;
