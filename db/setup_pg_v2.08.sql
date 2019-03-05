--ODMBE-5192
update client.client_tbl cl set maxamount=947483647 where maxamount=-1 and id=100018;

--SGAMBE-4207
UPDATE client.client_tbl set smsrcpt=false where id=10021;

--CMP-2836
UPDATE system.cardprefix_tbl SET enabled=false where cardid=3 and min = 60110000 and max = 60110999;
UPDATE system.cardprefix_tbl SET enabled=false where cardid=3 and min = 60112000 and max = 60114999;
UPDATE system.cardprefix_tbl SET enabled=false where cardid=3 and min = 60117400 and max = 60117499;
UPDATE system.cardprefix_tbl SET enabled=false where cardid=3 and min = 60117700 and max = 60117999;
UPDATE system.cardprefix_tbl SET enabled=false where cardid=3 and min = 60118600 and max = 60119999;
UPDATE system.cardprefix_tbl SET enabled=false where cardid=3 and min = 64400000 and max = 65999999;