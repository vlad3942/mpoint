ALTER TABLE log.additional_data_tbl ALTER COLUMN value TYPE varchar(255);


-- Table: log.txnroute_tbl
CREATE TABLE log.txnroute_tbl
(
  id serial NOT NULL,
  sessionid integer NOT NULL,
  pspid integer NOT NULL,
  preference integer NOT NULL,
  enabled boolean DEFAULT true,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  CONSTRAINT txnroute_pk PRIMARY KEY (id),
  CONSTRAINT pspid FOREIGN KEY (pspid)
      REFERENCES system.psp_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);
ALTER TABLE log.txnroute_tbl OWNER TO postgres;

