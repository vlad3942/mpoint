/* ========== Travel Fund on-board sqls start ========== */
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, countryid,capture_type,psp_type) VALUES (10077, 26, 71, true, 640,2,11); 
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username) VALUES (10077, 71, '', '');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) SELECT A.id, 71, '-1'  FROM Client.Account_Tbl A, System.PSP_Tbl P WHERE clientid = 10077 GROUP BY A.id;
/* ========== Travel Fund on-board sqls end ========== */