/***
 * CMP-1030
 */
INSERT INTO System_OWNR.CardChargeType_Tbl(id, name) VALUES(4, 'CHARGE');
INSERT INTO System_OWNR.CardChargeType_Tbl(id, name) VALUES(5, 'DEFERRED_DEBIT');
INSERT INTO System_OWNR.CardChargeType_Tbl(id, name) VALUES(6, 'NONE');
/***
 * CMP-1041
 */
INSERT INTO System_Ownr.PspCard_Tbl(cardid, pspid) VALUES (21, 9);
INSERT INTO Client_Ownr.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10001, 21, 9);
INSERT INTO Client_Ownr.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 9, '-1');
/**
 * EKW-517
 */
UPDATE System_Ownr.Country_Tbl SET decimals = 3 WHERE currency = 'OMR' and id = 605;
UPDATE System_Ownr.Country_Tbl SET decimals = 3 WHERE currency = 'KWD' and id = 604;
