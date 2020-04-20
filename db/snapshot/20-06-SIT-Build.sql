-- FileExpiry for UATP and Chase
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) VALUES ('FILE_EXPIRY', '1', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10069 and pspid = 50), 'merchant',1);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) VALUES ('FILE_EXPIRY', '4', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10069 and pspid = 52), 'merchant',1);

ALTER TABLE Log.Transaction_Tbl ALTER COLUMN attempt SET DEFAULT 1;

ALTER TABLE CLIENT.SUREPAY_TBL ADD MAX INT4 DEFAULT 1;

INSERT INTO CLIENT.SUREPAY_TBL (CLIENTID, RESEND, MAX)
SELECT CLIENTID, DELAY, RETRIALVALUE::INTEGER
FROM CLIENT.RETRIAL_TBL;

DROP TABLE IF EXISTS CLIENT.RETRIAL_TBL;

DROP TABLE IF EXISTS SYSTEM.RETRIALTYPE_TBL;