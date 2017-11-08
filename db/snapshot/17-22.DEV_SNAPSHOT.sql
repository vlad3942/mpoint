
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (2004, 'Payment approved for partial amount', 'Payment', '');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (2005, '3d verification required for Authorization', 'Payment', '');

/*=========================PayTabs===================================== */

INSERT INTO System.PSP_Tbl (id, name) VALUES (38, 'PayTabs');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (602,38,'AED');
INSERT INTO System.PspCard_Tbl(cardid, pspid) VALUES (31, 38);
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 38, 'PayTabs', 'Arun123', 'zoVCrg1wOzCN22cXIZt5YM3TnAKoA5paulNWBOtqo6eq8roRqSWoEZh1A2qb7PlCa9yMX2cm8qMgSb7i34HH3ZID19P9YaL9jkVh');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 38, '-1');
UPDATE Client.CardAccess_Tbl SET pspid = 38, countryid = 602 WHERE clientid = 10007 AND cardid = 31;


INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('MERCHANT_URL', 'test_sadad@paytabs.com', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10007 and pspid = 38), 'merchant');

/*=========================PayTabs===================================== */

<-- 2C2P ALC Database Script-->

INSERT INTO system.psp_tbl (id, name, system_type) VALUES (40, '2c2p-alc', 1);
INSERT INTO system.pspcurrency_tbl (countryid, pspid, name) VALUES (644,40,'THB');
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 40, '2c2p-alc', 'CELLPM', 'TG2009');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 40, '-1');
INSERT INTO client.cardaccess_tbl ( clientid, cardid, enabled, pspid, countryid, stateid, position) VALUES (10007, 8, true, 40, 644, 1, null);
INSERT INTO client.cardaccess_tbl ( clientid, cardid, enabled, pspid, countryid, stateid, position) VALUES (10007, 7, true, 40, 644, 1, null);
INSERT INTO system.cardpricing_tbl ( pricepointid, cardid) VALUES ( -644, 8);
INSERT INTO system.cardpricing_tbl ( pricepointid, cardid) VALUES ( -644, 7);
INSERT INTO system.pspcard_tbl (cardid, pspid) VALUES (8, 40);
INSERT INTO system.pspcard_tbl (cardid, pspid) VALUES (7, 40);

