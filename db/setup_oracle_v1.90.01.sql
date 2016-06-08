/* ========== CONFIGURE IPAD ACCOUNT FOR VISA CHECKOUT START ========== */
-- VISA Checkout
INSERT INTO Client_Ownr.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10004, 13, '787FBIEK3RH8SOW8B2LF13CWSBdjufOH_2iyyw3YnO1HKitDc', '787FBIEK3RH8SOW8B2LF13CWSBdjufOH_2iyyw3YnO1HKitDc', 'ItZw9/c/Yu$cpAJ1hD/vaJkNKW2TJW6P-@Kjr2Tq');
INSERT INTO Client_Ownr.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100040, 13, '-1');
INSERT INTO Client_Ownr.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10004, 16, 9);
/* ========== CONFIGURE IPAD ACCOUNT FOR VISA CHECKOUT END ====== */
