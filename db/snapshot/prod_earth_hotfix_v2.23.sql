--CMP-4471 [Chase Payment] Limit process file additional property	CMP-4471[Chase Payment] Limit process file additional property--
INSERT INTO client.additionalproperty_tbl (id,key, value, externalid, type, scope) select (SELECT max(id)+1 FROM client.additionalproperty_tbl),'MAX_DOWNLOAD_FILE_LIMIT', '1', id, 'merchant',2 from client.merchantaccount_tbl WHERE clientid=10069 AND pspid=52;
--END CMP-4471 [Chase Payment] Limit process file additional property	CMP-4471[Chase Payment] Limit process file additional property--

UPDATE client.additionalproperty_tbl SET value='4' WHERE id=239 and externalid=428;