--ODMBE-5192
ALTER TABLE client.client_tbl ALTER COLUMN maxamount TYPE BIGINT USING maxamount::BIGINT;