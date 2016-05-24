/* ========== CONFIGURE GlobalCollect AS PSP ================ */
INSERT INTO System_Ownr.PSP_Tbl (id, name) VALUES (21, 'GlobalCollect');
INSERT INTO System_Ownr.PSPCurrency_Tbl (countryid, pspid, name) SELECT countryid, 21, name FROM System_Ownr.PSPCurrency_Tbl WHERE pspid = 4;
INSERT INTO System_Ownr.PSPCard_Tbl (cardid, pspid) SELECT cardid, 21 FROM System_Ownr.PSPCard_Tbl WHERE pspid = 4;
/* ========== CONFIGURE DATA CASH END ========== */

/* ========== CONFIGURE DEMO ACCOUNT FOR GlobalCollect START ========== */
-- Data Cash
INSERT INTO Client_Ownr.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10001, 21, '337', '35e849953d7a4b5e', 'zGMYB+75ieEbehRxAF89Pnuek3mDp3xAd0/VofCIDIc=');
INSERT INTO Client_Ownr.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 21, '-1');
-- Route VISA Card to GlobaCollect
UPDATE Client_Ownr.CardAccess_Tbl SET pspid = 21 WHERE clientid = 10001 AND cardid = 8;
/* ========== CONFIGURE DEMO ACCOUNT FOR GlobaCollect END ====== */

SET DEFINE OFF;
SET SQLBLANKLINES ON;
/* ========== CONFIGURE ANDROID PAY START ========== */
INSERT INTO System_Ownr.PSP_Tbl (id, name) VALUES (20, 'Android Pay');
INSERT INTO System_Ownr.PSPCurrency_Tbl (pspid, countryid, name) VALUES (20, 200, 'USD');

INSERT INTO System_Ownr.Card_Tbl (id, name, position, minlength, maxlength, cvclength) VALUES (27, 'Android Pay', 19, -1, -1, -1);
INSERT INTO System_Ownr.CardPrefix_Tbl (cardid, min, "max") VALUES (27, -1, -1);
INSERT INTO System_Ownr.CardPricing_Tbl (cardid, pricepointid) SELECT 27, id FROM System_Ownr.PricePoint_Tbl WHERE amount = -1 AND countryid = 200;
-- Enable Android Pay Wallet for WorldPay
INSERT INTO System_Ownr.PSPCard_Tbl (pspid, cardid) VALUES (4, 27);
-- Enable Android Pay Wallet for CPG
INSERT INTO System_Ownr.PSPCard_Tbl (pspid, cardid) VALUES (9, 27);
-- Enable Android Pay Wallet for Android Pay PSP
INSERT INTO System_Ownr.PSPCard_Tbl (pspid, cardid) VALUES (20, 27);
/* ========== CONFIGURE ANDROID PAY END ========== */

/* ========== CONFIGURE MOBILE APP ACCOUNT FOR ANDROID PAY START ====== */
-- AndroidPay
INSERT INTO Client_Ownr.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10004, 20, 'BDvZFW+iAbl79oAWKRtIoLRirs+Cm3J+Ml5xxeQJOHVUbakroK6HJ9FW4mS5vrAor52luwF1MvyqkXUYT3OUC/I=', NULL, NULL);
INSERT INTO Client_Ownr.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100040, 20, '-1');
INSERT INTO Client_Ownr.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10004, 27, 9);
/* ========== CONFIGURE MOBILE APP ACCOUNT FOR ANDROID PAY END ====== */

