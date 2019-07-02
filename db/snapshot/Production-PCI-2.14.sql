

-- Hpp Iframe flag
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('isEmbeddedHpp', 'true', <clientid>, 'client', true);


INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
    SELECT 'PAYPAL_BILLING_AGREEMENT', 'This is billing agreement with CellPoint Mobile', id, 'merchant', 2 FROM client.merchantaccount_tbl WHERE pspid=24 AND clientid = 10020;

--CMP-3015 default value set to empty
alter table client.merchantaccount_tbl alter column username set default 'empty';
alter table client.merchantaccount_tbl alter column passwd set default 'empty';

-- UATP
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('SETTLEMENT_BATCH_LIMIT', '20', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10069 and pspid = 52), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('SETTLEMENT_BATCH_LIMIT', '20', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10069 and pspid = 50), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('SETTLEMENT_BATCH_RETRY', '2', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10069 and pspid = 52), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('SETTLEMENT_BATCH_RETRY', '2', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10069 and pspid = 50), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'SFTP_HOST', 'https://sitaftp.sita.aero', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=10069 AND pspid=50;


