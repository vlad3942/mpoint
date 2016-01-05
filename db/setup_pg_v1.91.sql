/* ========== CONFIGURE DATA CASH START ========== */
INSERT INTO System.PSP_Tbl (id, name) VALUES (17, 'Data Cash');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) SELECT countryid, 17, name FROM System.PSPCurrency_Tbl WHERE pspid = 4;
INSERT INTO System.PSPCard_Tbl (cardid, pspid) SELECT cardid, 17 FROM System.PSPCard_Tbl WHERE pspid = 4;
/* ========== CONFIGURE DATA CASH END ========== */

/* ========== CONFIGURE DEMO ACCOUNT FOR DATA CASH START ========== */
-- Data Cash
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10001, 17, 'TESTCellpoint_02', 'merchant.TESTCellpoint_02', '684d51b8db5870b514404055d988512a');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 17, '-1');
-- Route VISA Card to Data Cash
UPDATE Client.CardAccess_Tbl SET pspid = 17 WHERE clientid = 10001 AND cardid = 8;
/* ========== CONFIGURE DEMO ACCOUNT FOR DATA CASH END ====== */


/* ========== CONFIGURE Wire Card START ========== */
INSERT INTO System.PSP_Tbl (id, name) VALUES (18, 'Wire Card');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) SELECT countryid, 18, name FROM System.PSPCurrency_Tbl WHERE pspid = 4;
INSERT INTO System.PSPCard_Tbl (cardid, pspid) SELECT cardid, 18 FROM System.PSPCard_Tbl WHERE pspid = 4;
/* ========== CONFIGURE Wire Card END ========== */

/* ========== CONFIGURE DEMO ACCOUNT FOR Wire Card START ========== */
-- Wire Card
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10001, 18, '9105bb4f-ae68-4768-9c3b-3eda968f57ea', '70000-APILUHN-CARD', '8mhwavKVb91T');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 18, '-1');
-- Route VISA Card to Wire Card
UPDATE Client.CardAccess_Tbl SET pspid = 18 WHERE clientid = 10001 AND cardid = 8;
/* ========== CONFIGURE DEMO ACCOUNT FOR Wire Card END ====== */


/* ========== CONFIGURE URLTYPE data for NOTIFCATION URL START ====== */
INSERT INTO System.URLType_Tbl (id, name) VALUES (3, 'Notification URL');
/* ========== CONFIGURE URLTYPE data for NOTIFCATION URL START ====== */
