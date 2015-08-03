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