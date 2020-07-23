-------Start of the master script ----------------

ALTER TABLE system.country_tbl DROP COLUMN symbol;
----Increase length of additional_data_tbl's name name
ALTER TABLE log.additional_data_tbl ALTER COLUMN name TYPE varchar(30);

ALTER TABLE log.address_tbl add last_name varchar(200) null;
ALTER TABLE log.address_tbl RENAME COLUMN name TO first_name;
ALTER TABLE log.additional_data_tbl ALTER COLUMN value TYPE varchar(50);

CREATE INDEX CONCURRENTLY externalreference_transaction_idx ON Log.Externalreference_Tbl (txnid, externalid);
CREATE INDEX concurrently address_tbl_referenceid_type_index ON log.address_tbl USING btree (reference_id, reference_type);



---- setup_pg_CRS_v2.22.sql

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

-------End of the master script ----------------

