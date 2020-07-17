ALTER TABLE log.additional_data_tbl ALTER COLUMN value TYPE varchar(255);

-- Alter Log.flight_tbl to store additional flight data
ALTER TABLE log.flight_tbl
  ADD COLUMN departure_countryid integer;

ALTER TABLE log.flight_tbl
  ADD COLUMN arrival_countryid integer;

ALTER TABLE log.flight_tbl
  ADD CONSTRAINT departure_countryid_country_tbl_id_fk
FOREIGN KEY (departure_countryid) REFERENCES system.country_tbl (id);

ALTER TABLE log.flight_tbl
  ADD CONSTRAINT arrival_countryid_country_tbl_id_fk
FOREIGN KEY (arrival_countryid) REFERENCES system.country_tbl (id);

-- Table: log.txnroute_tbl
CREATE TABLE log.paymentroute_tbl
(
  id serial NOT NULL,
  sessionid integer NOT NULL,
  pspid integer NOT NULL,
  preference integer NOT NULL,
  enabled boolean DEFAULT true,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  CONSTRAINT paymentroute_pk PRIMARY KEY (id),
  CONSTRAINT pspid FOREIGN KEY (pspid)
      REFERENCES system.psp_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);
ALTER TABLE log.paymentroute_tbl OWNER TO mpoint;



--  Revert Changes
ALTER TABLE log.additional_data_tbl ALTER COLUMN value TYPE varchar(50);

-- Alter Log.flight_tbl to store additional flight data
ALTER TABLE log.flight_tbl
  ADD COLUMN time_zone character varying(10);

-- Add new transaction types
INSERT INTO System.Type_Tbl (id, name) VALUES (1, 'Shopping Online');
INSERT INTO System.Type_Tbl (id, name) VALUES (2, 'Shopping Offline');
INSERT INTO System.Type_Tbl (id, name) VALUES (3, 'Self Service Online');
INSERT INTO System.Type_Tbl (id, name) VALUES (4, 'Self Service Offline	');

