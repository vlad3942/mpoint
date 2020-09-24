-- Change the code column datatype to var_char from integer
ALTER TABLE system.country_tbl  ALTER COLUMN code type character varying(5)

-- Rename column name
ALTER TABLE log.transaction_tbl 
RENAME COLUMN convetredcurrencyid TO convertedcurrencyid;
