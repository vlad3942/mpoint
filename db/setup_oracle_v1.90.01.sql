/* ========== CONFIGURE IPAD ACCOUNT FOR VISA CHECKOUT START ========== */
-- VISA Checkout
INSERT INTO Client_Ownr.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10004, 13, '2X5JJ0751LFJG3EMYRMS13h-QPSi0pUet0c2zoXupm10tRL28', '2X5JJ0751LFJG3EMYRMS13h-QPSi0pUet0c2zoXupm10tRL28', '5PH9i3cNJ8ZmFK0B-xuSsMzq{8uSkO$o#GZPA+M}');
INSERT INTO Client_Ownr.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100040, 13, '-1');
INSERT INTO Client_Ownr.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10004, 16, 9);
/* ========== CONFIGURE IPAD ACCOUNT FOR VISA CHECKOUT END ====== */
