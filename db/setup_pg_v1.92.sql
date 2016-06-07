/* ========== CONFIGURE DSB AS PSP ================ */
INSERT INTO System.PSP_Tbl (id, name) VALUES (19, 'DSB');
/* ========== =================== ================ */

/* ========== CONFIGURE DSB INVOICE PAYMENT START ========= */
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength) VALUES (24, 'Invoice', 24, -1, -1, -1);
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (19, 24);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT C.id * -1 AS pricepointid, 24 FROM System.Country_Tbl C, System.Card_Tbl Card WHERE C.id = 100 GROUP BY pricepointid;
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (24, 0, 0);
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, position) VALUES (10005, 24, 19, 1);
/* Let Mobilepay be positioned after Invoice */
UPDATE Client.CardAccess_Tbl  SET position = 2 WHERE cardid = 17 and clientid = 10005;
/* ========== CONFIGURE DSB INVOICE PAYMENT END ========= */

/* ========== CONFIGURE DSB PSP AND VOUCHER PAYMENT ========= */
INSERT INTO System.PSPCurrency_Tbl (pspid, countryid, name) VALUES (19, 100, 'DKK');
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength) VALUES (26, 'Voucher', 22, -1, -1, -1);
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (19, 26);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT C.id * -1 AS pricepointid, 26 FROM System.Country_Tbl C, System.Card_Tbl Card WHERE C.id = 100 GROUP BY pricepointid;

INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10005, 26, 19);
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username) VALUES (10005, 19, '', '');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) SELECT A.id, 19, '-1'  FROM Client.Account_Tbl A, System.PSP_Tbl P WHERE clientid = 10005 GROUP BY A.id;

INSERT INTO Log.State_Tbl (id, name) VALUES (2007, 'Payment with voucher');
/* ========== CONFIGURE DSB PSP AND VOUCHER PAYMENT ========= */

/* ========== CONFIGURE State table for support Card block by PSP ========== */
INSERT INTO Log.State_Tbl (id, name, module) VALUES (2012, 'Card blocked by PSP', 'Payment');
/* ========== END CONFIGURE State table for support Card block by PSP ========= */
