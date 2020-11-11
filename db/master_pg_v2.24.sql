-- Change the code column datatype to var_char from integer
ALTER TABLE system.country_tbl  ALTER COLUMN code type character varying(5)

-- Rename column name
ALTER TABLE log.transaction_tbl 
RENAME COLUMN convetredcurrencyid TO convertedcurrencyid;

-- Change the amount column datatype to bigint from integer
ALTER TABLE log.txnpassbook_tbl ALTER COLUMN amount TYPE BIGINT USING amount::BIGINT;

ALTER TABLE log.passenger_tbl ADD amount int8 default 0;