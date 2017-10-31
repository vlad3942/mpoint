<-- Pay Tabs Database Script-->

INSERT INTO System.PSP_Tbl (id, name) VALUES (38, 'PayTabs');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (602,38,'AED');
INSERT INTO System.PspCard_Tbl(cardid, pspid) VALUES (31, 38);
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 38, 'PayTabs', '10004931', 'zoVCrg1wOzCN22cXIZt5YM3TnAKoA5paulNWBOtqo6eq8roRqSWoEZh1A2qb7PlCa9yMX2cm8qMgSb7i34HH3ZID19P9YaL9jkVh');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 38, '-1');
UPDATE Client.CardAccess_Tbl SET pspid = 38, countryid = 602 WHERE clientid = 10007 AND cardid = 31;
