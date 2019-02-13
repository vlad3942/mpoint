/** post Jan 2019 release **/
--Setup this additional property if 3DS is to be requested with every request to Adyen, the rules configured in Adyen will override
--Do not add this if 3DS is not required
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'MANUALTHREED', 'true', id, 'merchant' from client.merchantaccount_tbl where clientid=<clientid> and pspid=12;

--Setup this additional property if 3DS is to be requested to Adyen based on dynamic rules configured.
--Do not add this if 3DS is not required
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'DYNAMICTHREED', 'true', id, 'merchant' from client.merchantaccount_tbl where clientid=<clientid> and pspid=12;

--  CMP-2810 Add Paypal STC related credentials to additional properties table linked to merchant config --
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type )
    SELECT 'PAYPAL_STC', 'true', id, 'merchant' FROM client.merchantaccount_tbl WHERE pspid=24;
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type )
    SELECT 'PAYPAL_REST_ACC_ID', '897383MMQSC9W', id, 'merchant' FROM client.merchantaccount_tbl WHERE pspid=24;
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type )
    SELECT 'PAYPAL_REST_CLIENT_ID', 'AejFqzw9vADty0xlc9oAgI0Rz0LQXYaoZyGPo0rlNiMx7taGI5C1VxqrGpT9zVjg1LMiPwfzkftO0W3U', id, 'merchant' FROM client.merchantaccount_tbl WHERE pspid=24;
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type )
    SELECT 'PAYPAL_REST_SECRET', 'EEmWU-1Bcmfuhe0xheaAlrArpEx2uzrBcB-HVkm125max3hgtVJc4d26bWe0TuDmks-kOl7WlqoRn4-G', id, 'merchant' FROM client.merchantaccount_tbl WHERE pspid=24;
-- CMP-2810 --
