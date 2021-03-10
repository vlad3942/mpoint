-- mPoint DB Scripts :

UPDATE client.cardaccess_tbl
SET psp_type = 7
WHERE clientid = 10018 and cardid = 73 and pspid = 51 and id = 1452;


--Prod PPRO Alipay config. change
UPDATE client.cardaccess_tbl
SET psp_type=4
WHERE clientid=10098 and cardid=32 and pspid=46 and id=1589;