-- Table: system.processortype_tbl

-- DROP TABLE system.processortype_tbl;

CREATE TABLE system.processortype_tbl
(
  id serial NOT NULL,
  name character varying(50),
  CONSTRAINT id_pk PRIMARY KEY (id),
  CONSTRAINT iduk UNIQUE (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE system.processortype_tbl
  OWNER TO mpoint;

-- Column: system_type

-- ALTER TABLE system.psp_tbl DROP COLUMN system_type;

ALTER TABLE system.psp_tbl ADD COLUMN system_type integer;

   -- Foreign Key: system.psptoproccessingtype_fk

-- ALTER TABLE system.psp_tbl DROP CONSTRAINT psptoproccessingtype_fk;

ALTER TABLE system.psp_tbl
  ADD CONSTRAINT psptoproccessingtype_fk FOREIGN KEY (system_type)
      REFERENCES system.processortype_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE;
  
   -- Insert data : system.processortype_tbl;
  
  INSERT INTO system.processortype_tbl(
            id, name)
    VALUES (1, 'PSP');
    INSERT INTO system.processortype_tbl(
            id, name)
    VALUES (2, 'Bank');
    INSERT INTO system.processortype_tbl(
            id, name)
    VALUES (3, 'Wallet');
    INSERT INTO system.processortype_tbl(
            id, name)
    VALUES (4, 'APM');
