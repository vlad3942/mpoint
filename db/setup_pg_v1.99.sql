UPDATE Client.Client_Tbl SET salt = 'Fh17_8sFgd' WHERE id = 10005;
UPDATE Client.Client_Tbl SET salt = 'Fh17_8sFgd' WHERE id = 10014;
UPDATE Client.Client_Tbl SET salt = '8sFgd_Fh17' WHERE id = 10019;

/* ========== CONFIGURE Secure Trading AS PSP ================ */
INSERT INTO System.PSP_Tbl (id, name) VALUES (22, 'SecureTrading');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) SELECT countryid, 22, name FROM System.PSPCurrency_Tbl WHERE pspid = 4;
INSERT INTO System.PSPCard_Tbl (cardid, pspid) SELECT cardid, 22 FROM System.PSPCard_Tbl WHERE pspid = 4;
/* ========== CONFIGURE Secure Trading END ========== */

/* ========== CONFIGURE DEMO ACCOUNT FOR Secure Trading START ========== */

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10001, 22, 'test_cellpoint67180', 'webservice@cellpointmobile.com', 'P9Rf3bVL');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 22, '-1');

UPDATE Client.CardAccess_Tbl SET pspid = 22 WHERE clientid = 10001 AND cardid = 8;
