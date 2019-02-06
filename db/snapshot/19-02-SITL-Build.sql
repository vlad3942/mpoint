--ODMBE-5192
ALTER TABLE client.client_tbl ALTER COLUMN maxamount TYPE BIGINT USING maxamount::BIGINT;

update client.client_tbl cl set maxamount=947483647 where maxamount=-1 and id=100018;