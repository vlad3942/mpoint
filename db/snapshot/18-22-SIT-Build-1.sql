-- Chase Payment Script --

UPDATE System.PSP_Tbl set capture_method = 6 WHERE id = 52;
UPDATE Client.MerchantAccount_Tbl set username = 'nconline1',passwd = 'nconline1',name = 'Chase Payment' WHERE clientid = <> AND pspid = 52;
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (124,52,'CAD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (36,52,'AUD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (344,52,'HKD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (392,52,'JPY');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (710,52,'ZAR');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (826,52,'GBP');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (978,52,'EUR');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (554,52,'NZD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (752,52,'SEK');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (901,52,'TWD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (643,52,'RUB');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (356,52,'INR');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (360,52,'IDR');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (608,52,'PHP');


INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'USERNAME.PID', '355426', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=<> AND pspid=52;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'PASSWORD.PID', 'CELLAIR1', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=<> AND pspid=52;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'USERNAME.SID', '355426', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=<> AND pspid=52;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'PASSWORD.SID', 'CELLAIR2', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=<> AND pspid=52;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'CHASE_SFTP_USERNAME', 'SV3PX7R4', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=<> AND pspid=52;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'CHASE_SFTP_PASSWORD', 'W9ajOClR', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=<> AND pspid=52;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'CHASE_FILE_PREFIX', 'CPM', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=<> AND pspid=52;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'MERCHANT.CITY', 'USA', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=<> AND pspid=52;

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'MID.USD', '900183', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=<> AND pspid=52;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'MID.CAD', '253997', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=<> AND pspid=52;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'MID.AUD', '322446', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=<> AND pspid=52;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'MID.HKD', '336032', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=<> AND pspid=52;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'MID.JPY', '348045', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=<> AND pspid=52;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'MID.ZAR', '425272', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=<> AND pspid=52;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'MID.GBP', '732214', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=<> AND pspid=52;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'MID.EUR', '925529', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=<> AND pspid=52;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'MID.NZD', '984047', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=<> AND pspid=52;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'MID.SEK', '984351', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=<> AND pspid=52;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'MID.TWD', '097004', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=<> AND pspid=52;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'MID.RUB', '097003', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=<> AND pspid=52;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'MID.INR', '097332', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=<> AND pspid=52;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'MID.IDR', '097333', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=<> AND pspid=52;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'MID.PHP', '097335', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=<> AND pspid=52;
-- END Chase Payment Script --