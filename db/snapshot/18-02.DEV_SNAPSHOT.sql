/* ========== Global Configuration for AliPay Chinese - Payment Method : START========== */
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (40, 'AliPay Chinese', 23, -1, -1, -1,4);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (40, 0, 0);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 156;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 344;


INSERT INTO System.PSP_Tbl (id, name, system_type) VALUES (43, 'AliPay - Chinese',4);
INSERT INTO System.PSPCurrency_Tbl (pspid, name,currencyid) VALUES (43,'CNY',156);
INSERT INTO System.PSPCurrency_Tbl (pspid, name,currencyid) VALUES (43,'HKD',344);

/*AliPay*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (40, 43);

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10001, 43, 'AliPay Chinese', '2088621891660447', '');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 43, '-1');

-- Route AliPay Card to AliPay with country CHN / HK
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, countryid) VALUES (10001, 40, 43, true, 609);
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, countryid) VALUES (10001, 40, 43, true, 614);

/* ========== Global Configuration for AliPay Chinese ENDS ========== */