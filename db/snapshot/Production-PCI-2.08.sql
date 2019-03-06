--Payment is currently unavailable due to large amount of IDR currency
ALTER TABLE client.client_tbl ALTER COLUMN maxamount TYPE BIGINT USING maxamount::BIGINT;
update client.client_tbl cl set maxamount=947483647 where maxamount=-1 and id=10018;

--Invalid Currency issue for RGN so checkout is failing
UPDATE System.Country_Tbl set id = 653, currencyid = 104, alpha2code = 'MM', alpha3code = 'MMR', code = 104 WHERE id = 653;

--new fields to be added in callback (OD - PSPs)
alter table log.additional_data_tbl alter column value type varchar(50) using value::varchar(50);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('TIMEZONE', 'Asia/Kuala_Lumpur', true, 10018, 'client');
INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid) VALUES (10018,653,840);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('SHORT-CODE', 'CAV', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10018 and pspid = 25), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('SHORT-CODE', '2C2', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10018 and pspid = 26), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('SHORT-CODE', 'MBB', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10018 and pspid = 27), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('SHORT-CODE', 'PBB', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10018 and pspid = 28), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('SHORT-CODE', 'FPX', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10018 and pspid = 51), 'merchant');

-- Card Prefix for visa and Master
INSERT INTO "system".cardprefix_tbl ( cardid, min, max) VALUES( 7, 5110, 5210);
INSERT INTO "system".cardprefix_tbl ( cardid, min, max) VALUES( 7, 2700, 2730);

--default smsrcpt to false --SGAMBE-4207
ALTER TABLE client.client_tbl ALTER COLUMN smsrcpt SET DEFAULT FALSE ;
UPDATE client.client_tbl set smsrcpt=false where id=10021;
