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

/* ========== CONFIGURE ANDROID PAY START ========== */
INSERT INTO System.PSP_Tbl (id, name) VALUES (20, 'Android Pay');
INSERT INTO System.PSPCurrency_Tbl (pspid, countryid, name) VALUES (20, 200, 'USD');

INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength) VALUES (27, 'Android Pay', 19, -1, -1, -1);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (27, -1, -1);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 27, id FROM System.PricePoint_Tbl WHERE amount = -1 AND countryid = 200;
-- Enable Android Pay Wallet for WorldPay
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (4, 27);
-- Enable Android Pay Wallet for Android Pay PSP
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (20, 27);
/* ========== CONFIGURE ANDROID PAY END ========== */