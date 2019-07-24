-- Diasabling Non 3D authentication for JCB MID
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
SELECT 'SUPRESS_3DS_FLOW', 'JPY.5', id, 'merchant', 2 FROM client.merchantaccount_tbl WHERE pspid=26 AND clientid = 10018;
