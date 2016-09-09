UPDATE Client.Client_Tbl SET salt = 'Fh17_8sFgd' WHERE id = 10005;
UPDATE Client.Client_Tbl SET salt = 'Fh17_8sFgd' WHERE id = 10014;
UPDATE Client.Client_Tbl SET salt = '8sFgd_Fh17' WHERE id = 10019;

/**
 * CMP-917
 */
INSERT INTO System.Type_Tbl (id, name) VALUES (10091, 'New Card Purchase');
/**
 * CMP-999
 */
UPDATE system.country_tbl SET symbol='Kr.' WHERE id = 100;

/***
 * CMP-1030
 */
INSERT INTO System.CardChargeType_Tbl(id, name) VALUES
(4, 'CHARGE'),
(5, 'DEFERRED_DEBIT'),
(6, 'NONE');

/***
 * CMP-1041
 */
INSERT INTO System.PspCard_Tbl(cardid, pspid) VALUES (21, 9);
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10001, 21, 9);
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 9, '-1');