INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength) VALUES (21, 'UATP', 19, 15, 15, -1);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (21, 1000, 1999);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 21, id FROM System.PricePoint_Tbl WHERE amount = -1;
