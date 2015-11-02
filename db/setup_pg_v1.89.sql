/* ========== CONFIGURE UATP START ========== */
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength) VALUES (21, 'UATP', 19, 15, 15, -1);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (21, 1000, 1999);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 21, id FROM System.PricePoint_Tbl WHERE amount = -1;
/* ========== CONFIGURE UATP END ========== */

/* ========== CONFIGURE ADYEN START ========== */
INSERT INTO System.PSP_Tbl (id, name) VALUES (12, 'Adyen');
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (12, 1);	-- American Express
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (12, 2);	-- Dankort
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (12, 3);	-- Diners Club
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (12, 4);	-- EuroCard
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (12, 5);	-- JCB
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (12, 6);	-- Maestro
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (12, 7);	-- MasterCard
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (12, 8);	-- VISA
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (12, 9);	-- VISA Electron
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (12, 15);	-- Apple Pay
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (12, 21);	-- UATP
INSERT INTO System.PSPCurrency_Tbl (pspid, countryid, name) SELECT 12, id, currency FROM System.Country_Tbl;
/* ========== CONFIGURE ADYEN END ========== */

/* ========== CONFIGURE DSB PSP AND VOUCHER PAYMENT ========= */
INSERT INTO System.PSP_Tbl (id, name) VALUES (15, 'DSB');
INSERT INTO System.PSPCurrency_Tbl (pspid, countryid, name) VALUES (15, 100, 'DKK');
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength) VALUES (22, 'Voucher', 22, -1, -1, -1);
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (15, 22);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT C.id * -1 AS pricepointid, 22 FROM System.Country_Tbl C, System.Card_Tbl Card WHERE C.id = 100 GROUP BY pricepointid;

INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) SELECT 10005, 22, 15 FROM System.PSPCard_Tbl CA;
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username) SELECT 10005, 15, '', '' FROM System.PSP_Tbl;
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) SELECT A.id, 15, '-1'  FROM Client.Account_Tbl A, System.PSP_Tbl P WHERE clientid = 10005 GROUP BY A.id;

INSERT INTO Log.State_Tbl (id, name) VALUES (2007, 'Payment with voucher');
/* ========== CONFIGURE DSB PSP AND VOUCHER PAYMENT ========= */
