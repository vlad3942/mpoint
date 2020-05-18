ALTER TABLE log.additional_data_tbl ALTER COLUMN value TYPE varchar(255);

-- Alter Log.flight_tbl to store additional flight data
ALTER TABLE log.flight_tbl
  ADD COLUMN departure_country integer;
  ADD COLUMN departure_countryid integer;

ALTER TABLE log.flight_tbl
  ADD COLUMN arrival_country integer;
  ADD COLUMN arrival_countryid integer;

ALTER TABLE log.flight_tbl
  ADD CONSTRAINT departure_countryid_country_tbl_id_fk
FOREIGN KEY (departure_countryid) REFERENCES system.country_tbl (id);

ALTER TABLE log.flight_tbl
  ADD CONSTRAINT arrival_countryid_country_tbl_id_fk
FOREIGN KEY (arrival_countryid) REFERENCES system.country_tbl (id);