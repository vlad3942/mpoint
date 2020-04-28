-- passenger tbl --
ALTER TABLE log.passenger_tbl alter column first_name type varchar(50);
ALTER TABLE log.passenger_tbl alter column last_name type varchar(50);

-- currency improvement --
ALTER TABLE system.currency_tbl ADD COLUMN symbol VARCHAR(5);
UPDATE system.currency_tbl AS cur SET symbol = con.symbol FROM system.country_tbl AS con WHERE cur.id = con.currencyid;
-- Execute below query after completion of first two
ALTER TABLE system.country_tbl DROP COLUMN symbol;