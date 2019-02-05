--Setup this additional property if 3DS is to be requested with every request to Adyen, the rules configured in Adyen will override
--Do not add this if 3DS is not required
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'MANUALTHREED', 'true', id, 'merchant' from client.merchantaccount_tbl where clientid=<clientid> and pspid=12;

--Setup this additional property if 3DS is to be requested to Adyen based on dynamic rules configured.
--Do not add this if 3DS is not required
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'DYNAMICTHREED', 'true', id, 'merchant' from client.merchantaccount_tbl where clientid=<clientid> and pspid=12;