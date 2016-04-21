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


  
 
