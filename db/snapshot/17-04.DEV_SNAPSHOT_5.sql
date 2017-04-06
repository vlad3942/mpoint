

/* ========== Global Configuration for Qiwi - Payment Method : START========== */
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength) VALUES (33, 'Qiwi', 23, -1, -1, -1);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (33, 0, 0);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 33, id FROM System.PricePoint_Tbl WHERE amount = -1 AND countryid = 607;
/* ========== Global Configuration for Qiwi - Payment Method : END========== */
/* ========== Global Configuration for Qiwi = STARTS ========== */
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (31, 'Qiwi',4);
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (607,31,'RUB');

/*Qiwi*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (33, 31);

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 31, '506982', '55762053', 'kS9UKBSYDzVV8HHejopx');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 31, '-1');

-- Route Qiwi Card to Qiwi with country Russia
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, countryid) VALUES (10007, 33, 31, true, 607);
/* ========== Global Configuration for Qiwi = ENDS ========== */