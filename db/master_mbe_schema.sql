CREATE DATABASE mbe
  WITH OWNER = postgres
       ENCODING = 'UTF8'
       TABLESPACE = pg_default
       LC_COLLATE = 'English_India.1252'
       LC_CTYPE = 'English_India.1252'
       CONNECTION LIMIT = -1;
	   
	   
CREATE SCHEMA enduser
  AUTHORIZATION mpoint;
  
CREATE SEQUENCE enduser.account_tbl_id_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 17
  CACHE 1;
ALTER TABLE enduser.account_tbl_id_seq
  OWNER TO mpoint;


CREATE TABLE enduser.account_tbl
(
  id serial NOT NULL,
  chatname character varying(50),
  pushid character varying(150),
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true,
  CONSTRAINT message_pk PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE enduser.account_tbl
  OWNER TO mpoint;
  
CREATE SCHEMA log
  AUTHORIZATION mpoint;

CREATE SEQUENCE log.message_tbl_id_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 51
  CACHE 1;
ALTER TABLE log.message_tbl_id_seq
  OWNER TO mpoint;
  
  
CREATE TABLE log.message_tbl
(
  id serial NOT NULL,
  fromid integer,
  toid integer,
  data text,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true,
  read boolean DEFAULT false,
  CONSTRAINT message_pk PRIMARY KEY (id),
  CONSTRAINT message2from_fk FOREIGN KEY (fromid)
      REFERENCES enduser.account_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT message2to_fk FOREIGN KEY (toid)
      REFERENCES enduser.account_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT
)
WITH (
  OIDS=FALSE
);
ALTER TABLE log.message_tbl
  OWNER TO mpoint;

  
INSERT INTO Enduser.Account_Tbl (id,chatname) VALUES (1,'System');

-- Sequence: log.transaction_tbl_id_seq

-- DROP SEQUENCE log.transaction_tbl_id_seq;

CREATE SEQUENCE log.transaction_tbl_id_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 34
  CACHE 1;
ALTER TABLE log.transaction_tbl_id_seq
  OWNER TO mpoint;

 CREATE TABLE log.transaction_tbl
(
  id serial NOT NULL,
  source character varying(20),
  destination character varying(20),
  journeytype integer,
  departure character varying(20),
  arrival character varying(20),
  class character varying(20),
  passengers integer,
  sessionid character varying(20),
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true,
  CONSTRAINT transaction_pk PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE log.transaction_tbl
  OWNER TO mpoint;

CREATE SEQUENCE log.txnstate_tbl_id_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 158
  CACHE 1;
ALTER TABLE log.txnstate_tbl_id_seq
  OWNER TO mpoint;

-- Table: log.txnstate_tbl

-- DROP TABLE log.txnstate_tbl;

CREATE TABLE log.txnstate_tbl
(
  id serial NOT NULL,
  txnid integer NOT NULL,
  stateid integer NOT NULL,
  data text,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true,
  CONSTRAINT txnstate_pk PRIMARY KEY (id),
  CONSTRAINT txnstate2txn_fk FOREIGN KEY (txnid)
      REFERENCES log.transaction_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);
ALTER TABLE log.txnstate_tbl
  OWNER TO mpoint;


  
 
