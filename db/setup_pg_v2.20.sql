
/* ========== retry and batch-size for the chase connector:: CMP-3457 ========== */

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) VALUES ('MVAULT_RETRY_COUNT', '1', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = <client id> and pspid = <pspid>), 'merchant',1);

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) VALUES ('MVAULT_BATCH_SIZE', '1', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = <client id> and pspid =  <pspid>), 'merchant',1);
