/* ========== CONFIGURE GlobalCollect AS PSP ================ */
INSERT INTO System.PSP_Tbl (id, name) VALUES (21, 'GlobalCollect');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) SELECT countryid, 21, name FROM System.PSPCurrency_Tbl WHERE pspid = 4;
INSERT INTO System.PSPCard_Tbl (cardid, pspid) SELECT cardid, 21 FROM System.PSPCard_Tbl WHERE pspid = 4;
/* ========== CONFIGURE GlobaCollect END ========== */

/* ========== CONFIGURE DEMO ACCOUNT FOR GlobalCollect START ========== */
-- Data Cash
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10001, 21, '337', '35e849953d7a4b5e', 'zGMYB+75ieEbehRxAF89Pnuek3mDp3xAd0/VofCIDIc=');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 21, '-1');
-- Route VISA Card to GlobaCollect
UPDATE Client.CardAccess_Tbl SET pspid = 21 WHERE clientid = 10001 AND cardid = 8;
/* ========== CONFIGURE DEMO ACCOUNT FOR GlobaCollect END ====== */