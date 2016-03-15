/* ========== CONFIGURE ANDROID PAY START ========== */
INSERT INTO System.PSP_Tbl (id, name) VALUES (20, 'Android Pay');
INSERT INTO System.PSPCurrency_Tbl (pspid, countryid, name) VALUES (20, 200, 'USD');

INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength) VALUES (27, 'Android Pay', 19, -1, -1, -1);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (27, -1, -1);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 27, id FROM System.PricePoint_Tbl WHERE amount = -1 AND countryid = 200;
-- Enable Android Pay Wallet for WorldPay
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (4, 27);
-- Enable Android Pay Wallet for Android Pay PSP
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (20, 27);
/* ========== CONFIGURE ANDROID PAY END ========== */
