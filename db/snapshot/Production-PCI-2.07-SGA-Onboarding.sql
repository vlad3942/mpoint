
UPDATE Client.Client_tbl SET store_card = 3 WHERE id = 10021;

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10021, 36, 'mVault', 'blank', 'blank');

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10021, 1, 'mVault', 'blank', 'blank');

INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100220, 1, '-1');

INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100220, 36, '-1');

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('mvault', 'true', 10021, 'client');

INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, stateid) values (10021,11, 1, 1);
