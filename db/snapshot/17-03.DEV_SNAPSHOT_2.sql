
/* ========== Global Configuration for MayBank = STARTS ========== */
INSERT INTO System.PSP_Tbl (id, name) VALUES (27, 'MayBank');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (638,1,'MYR');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (638,27,'MYR');

/*Amex*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (1, 27);
/*MasterCard*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (7, 27);
/*VISA*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (8, 27);

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10001, 27, 'MayBank', '02700770202075001284', '4GkR2Hkk');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 27, '-1');

-- Route VISA Card to MayBank with country Malaysia
UPDATE Client.CardAccess_Tbl SET pspid = 27, countryid = 638 WHERE clientid = 10001 AND cardid = 8;
-- Route Master Card to MayBank with country Malaysia
UPDATE Client.CardAccess_Tbl SET pspid = 27, countryid = 638 WHERE clientid = 10001 AND cardid = 7;
-- Route AMEX Card to MayBank with country Malaysia
UPDATE Client.CardAccess_Tbl SET pspid = 27, countryid = 638 WHERE clientid = 10001 AND cardid = 1;

/* ========== Global Configuration for MayBank = ENDS ========== */