INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('SHORT-CODE', 'CAV', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10018 and pspid = 25), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('SHORT-CODE', '2C2', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10018 and pspid = 26), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('SHORT-CODE', 'MBB', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10018 and pspid = 27), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('SHORT-CODE', 'PBB', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10018 and pspid = 28), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('SHORT-CODE', 'FPX', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10018 and pspid = 51), 'merchant');


alter table log.additional_data_tbl alter column value type varchar(50) using value::varchar(50);