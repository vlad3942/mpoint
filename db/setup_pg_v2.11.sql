INSERT INTO Client.Account_Tbl (id, clientid, name, markup) VALUES( 100225, 10022, 'Ethiopian iOS App', 'ios'  );
UPDATE Client.Account_Tbl set markup = 'android',name = 'Ethiopian Android App' where markup = 'app' and clientid = 10022;
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100225, 41, 'dummy');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (<Android Et Account>, 41, 'CNYETHXNAR9U12N6IL0QNT39UNVHC3DM');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (<Web Et Account>, 41, '71D149972DDC436694922B912104C5A5');
UPDATE Client.MerchantSubAccount_Tbl set name=<TOKEN> where pspid = 41 and accountid = <Android Account>;
