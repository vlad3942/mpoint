/***
 * CMP-1030
 */
INSERT INTO System_OWNR.CardChargeType_Tbl(id, name) VALUES
(4, 'CHARGE'),
(5, 'DEFERRED_DEBIT'),
(6, 'NONE');

/***
 * CMP-1041
 */
INSERT INTO System_Ownr.PspCard_Tbl(cardid, pspid) VALUES (21, 9);
INSERT INTO Client_Ownr.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10001, 21, 9);
INSERT INTO Client_Ownr.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 9, '-1');