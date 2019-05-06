INSERT INTO Client.Account_Tbl (id, clientid, name, markup) VALUES( 100225, 10022, 'Ethiopian iOS App', 'ios'  );
UPDATE Client.Account_Tbl set markup = 'android',name = 'Ethiopian Android App' where id = 100222;
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100225, 41, <>);
UPDATE Client.MerchantSubAccount_Tbl set name=<TOKEN> where pspid = 41 and accountid = 100222;