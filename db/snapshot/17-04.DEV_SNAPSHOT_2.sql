/* ========== Global Configuration for Publicbank = STARTS ========== */
INSERT INTO System.PSP_Tbl (id, name) VALUES (28, 'PublicBank');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (638,28,'MYR');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (614,28,'HKD');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (648,28,'SGD');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (500,28,'AUD');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (500,28,'AUD');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (507,28,'AUD');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (508,28,'AUD');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (510,28,'AUD');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (511,28,'AUD');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (630,28,'AUD');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (634,28,'LKR');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (609,28,'CNY');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (644,28,'THB');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (646,28,'TWD');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (608,28,'SAR');

/*Amex*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (1, 28);
/*MasterCard*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (7, 28);
/*VISA*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (8, 28);

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10001, 28, 'PUBLICBANK', 'sandbox', 'APPLE001');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 28, '-1');

-- Route VISA Card to Publicbank with country Thailand
UPDATE Client.CardAccess_Tbl SET pspid = 28, countryid = 638 WHERE clientid = 10001 AND cardid = 1;
-- Route VISA Card to Publicbank with country Thailand
UPDATE Client.CardAccess_Tbl SET pspid = 28, countryid = 638 WHERE clientid = 10001 AND cardid = 7;
-- Route VISA Card to Publicbank with country Thailand
UPDATE Client.CardAccess_Tbl SET pspid = 28, countryid = 638 WHERE clientid = 10001 AND cardid = 8;

/* ========== Global Configuration for Publicbank = ENDS ========== */
