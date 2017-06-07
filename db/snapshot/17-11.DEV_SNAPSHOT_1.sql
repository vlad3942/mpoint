--Add coloumn device-id in log.Transaction_tbl
ALTER TABLE log.transaction_tbl ADD COLUMN deviceid character varying(50);