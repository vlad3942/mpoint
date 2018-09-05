---- MADA Integration Start ---

INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (71, 'MADA', 23, -1, -1, -1,4);

INSERT INTO client.cardaccess_tbl ( clientid, cardid, enabled, pspid, countryid, stateid, position, psp_type) VALUES (10007, 71, true, 38, 601, 1, null, 1);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (71, 0, 0);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 71, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 48;
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (71, 38, true);

INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 71, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 414;
-- MADA Integration stop --

-- Paytabs SADAD v2 --
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (72, 'SADAD v2', 23, -1, -1, -1,4);

INSERT INTO client.cardaccess_tbl ( clientid, cardid, enabled, pspid, countryid, stateid, position, psp_type) VALUES (10007, 72, true, 38, 608, 1, null, 1);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (72, 0, 0);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 72, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 682;
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (72, 38, true);

-- End --