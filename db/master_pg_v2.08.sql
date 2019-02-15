--ODMBE-5192
ALTER TABLE client.client_tbl ALTER COLUMN maxamount TYPE BIGINT USING maxamount::BIGINT;

--default smsrcpt to false --SGAMBE-4207
ALTER TABLE client.client_tbl ALTER COLUMN smsrcpt SET DEFAULT FALSE ;
