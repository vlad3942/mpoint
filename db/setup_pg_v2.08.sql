--ODMBE-5192
update client.client_tbl cl set maxamount=947483647 where maxamount=-1 and id=100018;

--SGAMBE-4207
UPDATE client.client_tbl set smsrcpt=false where id=10021;