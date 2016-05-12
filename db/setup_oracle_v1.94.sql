/* ========== CONFIGURE GlobalCollect AS PSP ================ */
INSERT INTO System_Ownr.PSP_Tbl (id, name) VALUES (20, 'GlobalCollect');
INSERT INTO System_Ownr.PSPCurrency_Tbl (countryid, pspid, name) SELECT countryid, 20, name FROM System_Ownr.PSPCurrency_Tbl WHERE pspid = 4;
INSERT INTO System_Ownr.PSPCard_Tbl (cardid, pspid) SELECT cardid, 20 FROM System_Ownr.PSPCard_Tbl WHERE pspid = 4;
/* ========== CONFIGURE DATA CASH END ========== */

/* ========== CONFIGURE DEMO ACCOUNT FOR GlobalCollect START ========== */
-- Data Cash
INSERT INTO Client_Ownr.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10001, 20, '337', '35e849953d7a4b5e', 'zGMYB+75ieEbehRxAF89Pnuek3mDp3xAd0/VofCIDIc=');
INSERT INTO Client_Ownr.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 20, '-1');
-- Route VISA Card to GlobaCollect
UPDATE Client_Ownr.CardAccess_Tbl SET pspid = 20 WHERE clientid = 10001 AND cardid = 8;
/* ========== CONFIGURE DEMO ACCOUNT FOR GlobaCollect END ====== */